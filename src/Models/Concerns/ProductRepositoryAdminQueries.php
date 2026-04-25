<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Support\ProductNotes;
use PDO;
use RuntimeException;
use Throwable;

trait ProductRepositoryAdminQueries
{
    /**
     * Returns the admin catalogue listing including stock and price ranges.
     */
    public function findAllForAdmin(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                p.deleted_at,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                COUNT(v.id) AS variant_count,
                COALESCE(SUM(v.stock), 0) AS total_stock,
                MIN(v.price) AS min_price,
                MAX(v.price) AS max_price,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_variants v ON v.product_id = p.id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                p.deleted_at,
                b.name,
                ft.name,
                pi.image_url
            ORDER BY p.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns one admin catalogue page with optional search and filters.
     *
     * @param array{q?: string, gender?: string, inventory?: string} $filters
     */
    public function findPageForAdmin(array $filters, int $limit, int $offset): array
    {
        $params = [];
        ['where' => $whereSql, 'having' => $havingSql] = $this->buildAdminProductFilterSql($filters, $params);

        $orderBy = $this->resolveAdminProductSort((string) ($filters['sort'] ?? 'newest'));

        $sql = "
            SELECT
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                p.deleted_at,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                COUNT(v.id) AS variant_count,
                COALESCE(SUM(v.stock), 0) AS total_stock,
                MIN(v.price) AS min_price,
                MAX(v.price) AS max_price,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_variants v ON v.product_id = p.id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            {$whereSql}
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                p.deleted_at,
                b.name,
                ft.name,
                pi.image_url
            {$havingSql}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $name => $value) {
            $stmt->bindValue(':' . $name, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Counts filtered admin products for pagination.
     *
     * @param array{q?: string, gender?: string, inventory?: string} $filters
     */
    public function countForAdmin(array $filters): int
    {
        $params = [];
        ['where' => $whereSql, 'having' => $havingSql] = $this->buildAdminProductFilterSql($filters, $params);

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM (
                SELECT p.id
                FROM products p
                INNER JOIN brands b ON b.id = p.brand_id
                LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
                LEFT JOIN product_variants v ON v.product_id = p.id
                {$whereSql}
                GROUP BY p.id
                {$havingSql}
            ) filtered_products
        ");

        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Returns one product and its variants for the admin edit form.
     */
    public function findByIdForAdmin(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.brand_id,
                p.fragrance_type_id,
                p.family_name,
                p.name,
                p.concentration_label,
                p.slug,
                p.description,
                p.gender,
                p.deleted_at,
                pi.image_url
            FROM products p
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE p.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $product) {
            return null;
        }

        $variantStmt = $this->pdo->prepare("
            SELECT
                id,
                size_ml,
                price,
                stock
            FROM product_variants
            WHERE product_id = :product_id
            ORDER BY size_ml ASC
        ");

        $variantStmt->execute([
            'product_id' => $id,
        ]);

        $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);
        $product['notes'] = ProductNotes::emptyBuckets();
        $product['note_ids'] = ProductNotes::emptyBuckets();

        $noteStmt = $this->pdo->prepare("
            SELECT
                n.id,
                n.name,
                n.slug,
                n.image_url,
                pn.note_type
            FROM product_notes pn
            INNER JOIN notes n ON n.id = pn.note_id
            WHERE pn.product_id = :product_id
            ORDER BY
                FIELD(pn.note_type, 'general', 'top', 'heart', 'middle', 'base'),
                n.name ASC
        ");

        $noteStmt->execute([
            'product_id' => $id,
        ]);

        foreach ($noteStmt->fetchAll(PDO::FETCH_ASSOC) as $note) {
            $type = ProductNotes::normalizeType((string) ($note['note_type'] ?? ''));

            if (! isset($product['notes'][$type], $product['note_ids'][$type])) {
                continue;
            }

            $product['notes'][$type][] = $note;
            $product['note_ids'][$type][] = (int) $note['id'];
        }

        foreach ($product['variants'] as &$variant) {
            $imageStmt = $this->pdo->prepare("
                SELECT
                    id,
                    image_url,
                    position
                FROM product_variant_images
                WHERE product_variant_id = :variant_id
                ORDER BY position ASC, id ASC
            ");

            $imageStmt->execute([
                'variant_id' => $variant['id'],
            ]);

            $variant['images'] = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($variant);

        return $product;
    }

    /**
     * Creates a product and its variants in a single transaction.
     */
    public function createForAdmin(array $data, array $variants, array $noteAssignments = []): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO products (
                    brand_id,
                    fragrance_type_id,
                    family_name,
                    name,
                    concentration_label,
                    slug,
                    description,
                    gender
                ) VALUES (
                    :brand_id,
                    :fragrance_type_id,
                    :family_name,
                    :name,
                    :concentration_label,
                    :slug,
                    :description,
                    :gender
                )
            ");

            $stmt->execute([
                'brand_id' => $data['brand_id'],
                'fragrance_type_id' => $data['fragrance_type_id'] ?: null,
                'family_name' => $data['family_name'] ?: null,
                'name' => $data['name'],
                'concentration_label' => $data['concentration_label'] ?: null,
                'slug' => $data['slug'],
                'description' => $data['description'],
                'gender' => $data['gender'],
            ]);

            $productId = (int) $this->pdo->lastInsertId();

            $this->replaceVariants($productId, $variants);
            $this->syncProductNotes($productId, $noteAssignments);

            $this->pdo->commit();

            return $productId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Updates a product and fully replaces its variants inside one transaction.
     */
    public function updateForAdmin(int $id, array $data, array $variants, array $noteAssignments = []): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                UPDATE products
                SET
                    brand_id = :brand_id,
                    fragrance_type_id = :fragrance_type_id,
                    family_name = :family_name,
                    name = :name,
                    concentration_label = :concentration_label,
                    slug = :slug,
                    description = :description,
                    gender = :gender
                WHERE id = :id
            ");

            $stmt->execute([
                'id' => $id,
                'brand_id' => $data['brand_id'],
                'fragrance_type_id' => $data['fragrance_type_id'] ?: null,
                'family_name' => $data['family_name'] ?: null,
                'name' => $data['name'],
                'concentration_label' => $data['concentration_label'] ?: null,
                'slug' => $data['slug'],
                'description' => $data['description'],
                'gender' => $data['gender'],
            ]);

            $this->syncVariants($id, $variants);
            $this->syncProductNotes($id, $noteAssignments);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Returns whether the slug already belongs to another product.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "
            SELECT 1
            FROM products
            WHERE slug = :slug
        ";

        $params = ['slug' => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Replaces the product's primary image pointer.
     */
    public function replacePrimaryImage(int $productId, string $imageUrl): void
    {
        $deleteStmt = $this->pdo->prepare("
            DELETE FROM product_images
            WHERE product_id = :product_id
              AND position = 0
        ");

        $deleteStmt->execute([
            'product_id' => $productId,
        ]);

        $insertStmt = $this->pdo->prepare("
            INSERT INTO product_images (
                product_id,
                image_url,
                position
            ) VALUES (
                :product_id,
                :image_url,
                0
            )
        ");

        $insertStmt->execute([
            'product_id' => $productId,
            'image_url' => $imageUrl,
        ]);
    }

    /**
     * Replaces the primary image pointer for a product variant.
     */
    public function replaceVariantPrimaryImage(int $variantId, string $imageUrl): void
    {
        $deleteStmt = $this->pdo->prepare("
            DELETE FROM product_variant_images
            WHERE product_variant_id = :variant_id
              AND position = 0
        ");

        $deleteStmt->execute([
            'variant_id' => $variantId,
        ]);

        $insertStmt = $this->pdo->prepare("
            INSERT INTO product_variant_images (
                product_variant_id,
                image_url,
                position
            ) VALUES (
                :variant_id,
                :image_url,
                0
            )
        ");

        $insertStmt->execute([
            'variant_id' => $variantId,
            'image_url' => $imageUrl,
        ]);
    }

    /**
     * Returns one variant with enough product context for the admin upload flow.
     */
    public function findVariantById(int $variantId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                v.id,
                v.product_id,
                v.size_ml,
                v.price,
                v.stock,
                p.name AS product_name,
                p.concentration_label
            FROM product_variants v
            INNER JOIN products p ON p.id = v.product_id
            WHERE v.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $variantId,
        ]);

        $variant = $stmt->fetch(PDO::FETCH_ASSOC);

        return $variant ?: null;
    }

    /**
     * Rebuilds the variant rows for a product while enforcing basic constraints.
     */
    private function replaceVariants(int $productId, array $variants): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_variants (
                product_id,
                size_ml,
                price,
                stock
            ) VALUES (
                :product_id,
                :size_ml,
                :price,
                :stock
            )
        ");

        foreach ($this->normalizeVariantsForPersistence($variants) as $variant) {
            $stmt->execute([
                'product_id' => $productId,
                'size_ml' => $variant['size_ml'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
            ]);
        }
    }

    /**
     * Updates existing variants in place, inserts new ones, and removes any
     * omitted variants so existing image associations survive normal edits.
     */
    private function syncVariants(int $productId, array $variants): void
    {
        $normalizedVariants = $this->normalizeVariantsForPersistence($variants);
        $existingVariantIds = $this->findVariantIdsForProduct($productId);
        $submittedExistingIds = [];

        foreach ($normalizedVariants as $variant) {
            if ($variant['id'] !== null) {
                $submittedExistingIds[] = (int) $variant['id'];
            }
        }

        $invalidIds = array_diff($submittedExistingIds, $existingVariantIds);

        if ($invalidIds !== []) {
            throw new RuntimeException('One or more variants no longer exist for this product.');
        }

        $this->deleteVariantsNotInList($productId, $submittedExistingIds);

        if ($submittedExistingIds !== []) {
            $placeholders = implode(',', array_fill(0, count($submittedExistingIds), '?'));
            $shiftStmt = $this->pdo->prepare("
                UPDATE product_variants
                SET size_ml = size_ml + 1000000
                WHERE product_id = ?
                  AND id IN ($placeholders)
            ");

            $shiftStmt->execute([$productId, ...$submittedExistingIds]);
        }

        $updateStmt = $this->pdo->prepare("
            UPDATE product_variants
            SET
                size_ml = :size_ml,
                price = :price,
                stock = :stock
            WHERE id = :id
              AND product_id = :product_id
        ");

        $insertStmt = $this->pdo->prepare("
            INSERT INTO product_variants (
                product_id,
                size_ml,
                price,
                stock
            ) VALUES (
                :product_id,
                :size_ml,
                :price,
                :stock
            )
        ");

        foreach ($normalizedVariants as $variant) {
            if ($variant['id'] === null) {
                $insertStmt->execute([
                    'product_id' => $productId,
                    'size_ml' => $variant['size_ml'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                ]);

                continue;
            }

            $updateStmt->execute([
                'id' => $variant['id'],
                'product_id' => $productId,
                'size_ml' => $variant['size_ml'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
            ]);
        }
    }

    /**
     * Validates variant payloads and normalizes them into persistence-ready
     * arrays with optional identifiers for existing rows.
     *
     * @return array<int, array{id: int|null, size_ml: int, price: float, stock: int}>
     */
    private function normalizeVariantsForPersistence(array $variants): array
    {
        if ($variants === []) {
            throw new RuntimeException('At least one variant is required.');
        }

        $seenIds = [];
        $seenSizes = [];
        $normalized = [];

        foreach ($variants as $variant) {
            $variantId = isset($variant['id']) && $variant['id'] !== null
                ? (int) $variant['id']
                : null;
            $sizeMl = (int) $variant['size_ml'];
            $price = (float) $variant['price'];
            $stock = (int) $variant['stock'];

            if ($variantId !== null && in_array($variantId, $seenIds, true)) {
                throw new RuntimeException('Duplicate variant rows were submitted.');
            }

            if ($sizeMl <= 0) {
                throw new RuntimeException('Variant size must be greater than 0.');
            }

            if ($price < 0) {
                throw new RuntimeException('Variant price must be 0 or greater.');
            }

            if ($stock < 0) {
                throw new RuntimeException('Variant stock must be 0 or greater.');
            }

            if (in_array($sizeMl, $seenSizes, true)) {
                throw new RuntimeException('Variant sizes must be unique.');
            }

            if ($variantId !== null) {
                $seenIds[] = $variantId;
            }

            $seenSizes[] = $sizeMl;
            $normalized[] = [
                'id' => $variantId,
                'size_ml' => $sizeMl,
                'price' => $price,
                'stock' => $stock,
            ];
        }

        return $normalized;
    }

    /**
     * Returns the variant ids that currently belong to the given product.
     *
     * @return int[]
     */
    private function findVariantIdsForProduct(int $productId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM product_variants
            WHERE product_id = :product_id
        ");

        $stmt->execute([
            'product_id' => $productId,
        ]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Removes variants omitted from the edit form before updates and inserts
     * are applied.
     *
     * @param int[] $submittedExistingIds
     */
    private function deleteVariantsNotInList(int $productId, array $submittedExistingIds): void
    {
        if ($submittedExistingIds === []) {
            $deleteStmt = $this->pdo->prepare("
                DELETE FROM product_variants
                WHERE product_id = :product_id
            ");

            $deleteStmt->execute([
                'product_id' => $productId,
            ]);

            return;
        }

        $placeholders = implode(',', array_fill(0, count($submittedExistingIds), '?'));
        $deleteStmt = $this->pdo->prepare("
            DELETE FROM product_variants
            WHERE product_id = ?
              AND id NOT IN ($placeholders)
        ");

        $deleteStmt->execute([$productId, ...$submittedExistingIds]);
    }

    /**
     * Replaces the note assignments for a product in one pass.
     *
     * @param array{general?: int[], top?: int[], heart?: int[], base?: int[]} $noteAssignments
     */
    private function syncProductNotes(int $productId, array $noteAssignments): void
    {
        $deleteStmt = $this->pdo->prepare("
            DELETE FROM product_notes
            WHERE product_id = :product_id
        ");

        $deleteStmt->execute([
            'product_id' => $productId,
        ]);

        $insertStmt = $this->pdo->prepare("
            INSERT INTO product_notes (
                product_id,
                note_id,
                note_type
            ) VALUES (
                :product_id,
                :note_id,
                :note_type
            )
        ");

        foreach (ProductNotes::ORDER as $type) {
            foreach (($noteAssignments[$type] ?? []) as $noteId) {
                $insertStmt->execute([
                    'product_id' => $productId,
                    'note_id' => $noteId,
                    'note_type' => $type,
                ]);
            }
        }
    }

    /**
     * Builds reusable WHERE and HAVING SQL for admin product list filters.
     *
     * @param array{q?: string, gender?: string, inventory?: string} $filters
     * @param array<string, string> $params
     * @return array{where: string, having: string}
     */
    private function buildAdminProductFilterSql(array $filters, array &$params): array
    {
        $where = [];
        $having = [];

        $query = trim((string) ($filters['q'] ?? ''));
        $brandId = (int) ($filters['brand_id'] ?? 0);
        $fragranceTypeId = (int) ($filters['fragrance_type_id'] ?? 0);
        $gender = trim((string) ($filters['gender'] ?? ''));
        $inventory = trim((string) ($filters['inventory'] ?? ''));

        if ($query !== '') {
            $tokens = preg_split('/\s+/', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($tokens as $index => $token) {
                $paramKey = 'search_' . $index;
                $params[$paramKey] = '%' . $token . '%';

                $where[] = "
                    (
                        LOWER(p.name) LIKE :{$paramKey}
                        OR LOWER(p.slug) LIKE :{$paramKey}
                        OR LOWER(COALESCE(p.family_name, '')) LIKE :{$paramKey}
                        OR LOWER(COALESCE(p.concentration_label, '')) LIKE :{$paramKey}
                        OR LOWER(COALESCE(p.description, '')) LIKE :{$paramKey}
                        OR LOWER(b.name) LIKE :{$paramKey}
                        OR LOWER(COALESCE(ft.name, '')) LIKE :{$paramKey}
                        OR LOWER(p.gender) LIKE :{$paramKey}
                        OR CAST(p.id AS CHAR) LIKE :{$paramKey}
                        OR EXISTS (
                            SELECT 1
                            FROM product_notes pn_search
                            INNER JOIN notes n_search ON n_search.id = pn_search.note_id
                            WHERE pn_search.product_id = p.id
                              AND LOWER(n_search.name) LIKE :{$paramKey}
                        )
                    )
                ";
            }
        }

        if ($brandId > 0) {
            $params['brand_id'] = $brandId;
            $where[] = 'p.brand_id = :brand_id';
        }

        if ($fragranceTypeId > 0) {
            $params['fragrance_type_id'] = $fragranceTypeId;
            $where[] = 'p.fragrance_type_id = :fragrance_type_id';
        }

        if ($gender !== '') {
            $params['gender'] = $gender;
            $where[] = 'p.gender = :gender';
        }

        if ($inventory === 'in_stock') {
            $having[] = 'COALESCE(SUM(v.stock), 0) > 0';
        } elseif ($inventory === 'low_stock') {
            $having[] = 'COALESCE(SUM(v.stock), 0) BETWEEN 1 AND 10';
        } elseif ($inventory === 'out_of_stock') {
            $having[] = 'COALESCE(SUM(v.stock), 0) <= 0';
        }

        return [
            'where' => $where === [] ? '' : 'WHERE ' . implode(' AND ', $where),
            'having' => $having === [] ? '' : 'HAVING ' . implode(' AND ', $having),
        ];
    }

    private function resolveAdminProductSort(string $sort): string
    {
        return match ($sort) {
            'name_asc' => 'p.name ASC',
            'price_asc' => 'min_price ASC, p.id DESC',
            'price_desc' => 'min_price DESC, p.id DESC',
            'stock_asc' => 'total_stock ASC, p.id DESC',
            'stock_desc' => 'total_stock DESC, p.id DESC',
            default => 'p.id DESC',
        };
    }
}
