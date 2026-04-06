<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use RuntimeException;
use Throwable;

class ProductRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    public function findAllActive(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $brandId = (int) ($filters['brand_id'] ?? 0);
        $fragranceTypeId = (int) ($filters['fragrance_type_id'] ?? 0);
        $gender = trim((string) ($filters['gender'] ?? ''));
        $sort = trim((string) ($filters['sort'] ?? 'newest'));

        $topNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['top_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $middleNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['middle_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $baseNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['base_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $allowedSorts = [
            'newest' => 'p.id DESC',
            'name_asc' => 'p.name ASC',
            'price_asc' => 'price ASC',
            'price_desc' => 'price DESC',
        ];

        $orderBy = $allowedSorts[$sort] ?? $allowedSorts['newest'];

        $conditions = ['p.deleted_at IS NULL'];
        $params = [];

        if ($search !== '') {
            $tokens = preg_split('/\s+/', mb_strtolower($search), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($tokens as $index => $token) {
                $paramKey = 'search_' . $index;

                $conditions[] = "(
                    LOWER(p.name) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.family_name, '')) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.concentration_label, '')) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.description, '')) LIKE :{$paramKey}
                    OR LOWER(b.name) LIKE :{$paramKey}
                    OR LOWER(COALESCE(ft.name, '')) LIKE :{$paramKey}
                )";

                $params[$paramKey] = '%' . $token . '%';
            }
        }

        if ($brandId > 0) {
            $conditions[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = $brandId;
        }

        if ($fragranceTypeId > 0) {
            $conditions[] = 'p.fragrance_type_id = :fragrance_type_id';
            $params['fragrance_type_id'] = $fragranceTypeId;
        }

        if (in_array($gender, ['male', 'female', 'unisex'], true)) {
            $conditions[] = 'p.gender = :gender';
            $params['gender'] = $gender;
        }

        if ($topNoteIds !== []) {
            $topPlaceholders = [];

            foreach ($topNoteIds as $index => $noteId) {
                $paramKey = 'top_note_id_' . $index;
                $topPlaceholders[] = ':' . $paramKey;
                $params[$paramKey] = $noteId;
            }

            $conditions[] = "
                EXISTS (
                    SELECT 1
                    FROM product_notes pn_top
                    WHERE pn_top.product_id = p.id
                      AND pn_top.note_type = 'top'
                      AND pn_top.note_id IN (" . implode(', ', $topPlaceholders) . ")
                )
            ";
        }

        if ($middleNoteIds !== []) {
            $middlePlaceholders = [];

            foreach ($middleNoteIds as $index => $noteId) {
                $paramKey = 'middle_note_id_' . $index;
                $middlePlaceholders[] = ':' . $paramKey;
                $params[$paramKey] = $noteId;
            }

            $conditions[] = "
                EXISTS (
                    SELECT 1
                    FROM product_notes pn_middle
                    WHERE pn_middle.product_id = p.id
                      AND pn_middle.note_type = 'middle'
                      AND pn_middle.note_id IN (" . implode(', ', $middlePlaceholders) . ")
                )
            ";
        }

        if ($baseNoteIds !== []) {
            $basePlaceholders = [];

            foreach ($baseNoteIds as $index => $noteId) {
                $paramKey = 'base_note_id_' . $index;
                $basePlaceholders[] = ':' . $paramKey;
                $params[$paramKey] = $noteId;
            }

            $conditions[] = "
                EXISTS (
                    SELECT 1
                    FROM product_notes pn_base
                    WHERE pn_base.product_id = p.id
                      AND pn_base.note_type = 'base'
                      AND pn_base.note_id IN (" . implode(', ', $basePlaceholders) . ")
                )
            ";
        }

        $whereSql = implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare("
            SELECT 
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                MIN(v.price) AS price,
                COUNT(v.id) AS variant_count,
                COALESCE(SUM(CASE WHEN v.stock > 0 THEN 1 ELSE 0 END), 0) AS in_stock_variant_count,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_variants v ON v.product_id = p.id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE {$whereSql}
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name,
                ft.name,
                pi.image_url
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product['is_sellable'] = (int) $product['in_stock_variant_count'] > 0;
        }

        unset($product);

        return $products;
    }

    public function findActiveBySlug(string $slug): ?array
    {
        $productStmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.family_name,
                p.name,
                p.concentration_label,
                p.slug,
                p.description,
                p.gender,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE p.slug = :slug
              AND p.deleted_at IS NULL
            LIMIT 1
        ");

        $productStmt->execute([
            'slug' => $slug,
        ]);

        $product = $productStmt->fetch(PDO::FETCH_ASSOC);

        if (! $product) {
            return null;
        }

        $variantStmt = $this->pdo->prepare("
            SELECT
                v.id,
                v.size_ml,
                v.price,
                v.stock,
                (
                    SELECT pvi.image_url
                    FROM product_variant_images pvi
                    WHERE pvi.product_variant_id = v.id
                    ORDER BY pvi.position ASC
                    LIMIT 1
                ) AS image_url
            FROM product_variants v
            WHERE v.product_id = :product_id
            ORDER BY v.price ASC
        ");

        $variantStmt->execute([
            'product_id' => $product['id'],
        ]);

        $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

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

            if (empty($variant['image_url']) && !empty($product['image_url'])) {
                $variant['image_url'] = $product['image_url'];
            }
        }
        unset($variant);

        $categoryStmt = $this->pdo->prepare("
            SELECT
                c.id,
                c.name
            FROM product_categories pc
            INNER JOIN categories c ON c.id = pc.category_id
            WHERE pc.product_id = :product_id
            ORDER BY c.name ASC
        ");

        $categoryStmt->execute([
            'product_id' => $product['id'],
        ]);

        $product['categories'] = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

        $noteStmt = $this->pdo->prepare("
            SELECT
                n.id,
                n.name,
                n.image_url,
                pn.note_type
            FROM product_notes pn
            INNER JOIN notes n ON n.id = pn.note_id
            WHERE pn.product_id = :product_id
            ORDER BY
                FIELD(pn.note_type, 'top', 'middle', 'base'),
                n.name ASC
        ");

        $noteStmt->execute([
            'product_id' => $product['id'],
        ]);

        $notes = $noteStmt->fetchAll(PDO::FETCH_ASSOC);

        $product['notes'] = [
            'top' => [],
            'middle' => [],
            'base' => [],
        ];

        foreach ($notes as $note) {
            $type = $note['note_type'];

            if (isset($product['notes'][$type])) {
                $product['notes'][$type][] = $note;
            }
        }

        $product['related_family_products'] = [];

        if (!empty($product['family_name'])) {
            $relatedStmt = $this->pdo->prepare("
                SELECT
                    p.id,
                    p.name,
                    p.concentration_label,
                    p.slug,
                    b.name AS brand_name,
                    ft.name AS fragrance_type_name,
                    pi.image_url,
                    MIN(v.price) AS price
                FROM products p
                INNER JOIN brands b ON b.id = p.brand_id
                LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
                LEFT JOIN product_images pi
                    ON pi.product_id = p.id
                    AND pi.position = 0
                LEFT JOIN product_variants v ON v.product_id = p.id
                WHERE p.family_name = :family_name
                  AND p.id != :product_id
                  AND p.deleted_at IS NULL
                GROUP BY
                    p.id,
                    p.name,
                    p.concentration_label,
                    p.slug,
                    b.name,
                    ft.name,
                    pi.image_url
                ORDER BY p.name ASC
            ");

            $relatedStmt->execute([
                'family_name' => $product['family_name'],
                'product_id' => $product['id'],
            ]);

            $product['related_family_products'] = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $relatedProductsStmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                pi.image_url,
                MIN(v.price) AS price,
                (
                    CASE WHEN p.brand_id = (
                        SELECT brand_id FROM products WHERE id = :product_id
                    ) THEN 100 ELSE 0 END
                ) +
                (
                    CASE WHEN COALESCE(p.fragrance_type_id, 0) = COALESCE((
                        SELECT fragrance_type_id FROM products WHERE id = :product_id
                    ), 0) THEN 10 ELSE 0 END
                ) +
                (
                    CASE WHEN p.gender = (
                        SELECT gender FROM products WHERE id = :product_id
                    ) THEN 1 ELSE 0 END
                ) AS relevance_score
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            LEFT JOIN product_variants v ON v.product_id = p.id
            WHERE p.id != :product_id
              AND p.deleted_at IS NULL
              AND (
                p.brand_id = (SELECT brand_id FROM products WHERE id = :product_id)
                OR COALESCE(p.fragrance_type_id, 0) = COALESCE((SELECT fragrance_type_id FROM products WHERE id = :product_id), 0)
                OR p.gender = (SELECT gender FROM products WHERE id = :product_id)
              )
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                b.name,
                ft.name,
                pi.image_url,
                p.brand_id,
                p.fragrance_type_id,
                p.gender
            ORDER BY relevance_score DESC, p.name ASC
            LIMIT 4
        ");

        $relatedProductsStmt->execute([
            'product_id' => $product['id'],
        ]);

        $product['related_products'] = $relatedProductsStmt->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

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

    public function getBrands(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, name
            FROM brands
            ORDER BY name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotes(): array
    {
        $stmt = $this->pdo->query("
        SELECT id, name
        FROM notes
        ORDER BY name ASC
    ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFragranceTypes(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, name
            FROM fragrance_types
            ORDER BY name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createForAdmin(array $data, array $variants): int
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

            $this->pdo->commit();

            return $productId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateForAdmin(int $id, array $data, array $variants): void
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

            $deleteStmt = $this->pdo->prepare("
                DELETE FROM product_variants
                WHERE product_id = :product_id
            ");

            $deleteStmt->execute([
                'product_id' => $id,
            ]);

            $this->replaceVariants($id, $variants);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

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

    private function replaceVariants(int $productId, array $variants): void
    {
        if ($variants === []) {
            throw new RuntimeException('At least one variant is required.');
        }

        $seenSizes = [];

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

        foreach ($variants as $variant) {
            $sizeMl = (int) $variant['size_ml'];
            $price = (float) $variant['price'];
            $stock = (int) $variant['stock'];

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

            $seenSizes[] = $sizeMl;

            $stmt->execute([
                'product_id' => $productId,
                'size_ml' => $sizeMl,
                'price' => $price,
                'stock' => $stock,
            ]);
        }
    }

    public function getPublicFilterMeta(): array
    {
        return [
            'brands' => $this->getBrands(),
            'fragranceTypes' => $this->getFragranceTypes(),
            'notes' => $this->getNotes(),
            'genders' => ['male', 'female', 'unisex'],
            'sortOptions' => [
                'newest' => 'Newest',
                'name_asc' => 'Name (A-Z)',
                'price_asc' => 'Price (low to high)',
                'price_desc' => 'Price (high to low)',
            ],
        ];
    }

    public function findFeaturedActive(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                MIN(v.price) AS price,
                COUNT(v.id) AS variant_count,
                COALESCE(SUM(CASE WHEN v.stock > 0 THEN 1 ELSE 0 END), 0) AS in_stock_variant_count,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_variants v ON v.product_id = p.id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE p.deleted_at IS NULL
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name,
                ft.name,
                pi.image_url
            ORDER BY p.id DESC
            LIMIT :limit
        ");

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product['is_sellable'] = (int) $product['in_stock_variant_count'] > 0;
        }

        unset($product);

        return $products;
    }

    public function findCategoryHighlights(): array
    {
        return [
            [
                'title' => 'Fresh',
                'description' => 'Clean, bright, and easy to wear daily.',
                'query' => '?search=&sort=newest',
            ],
            [
                'title' => 'Woody',
                'description' => 'Elegant, warm profiles with depth and structure.',
                'query' => '?search=&sort=price_desc',
            ],
            [
                'title' => 'Floral',
                'description' => 'Soft to radiant compositions with signature bloom.',
                'query' => '?gender=female&sort=newest',
            ],
            [
                'title' => 'Luxury',
                'description' => 'Statement bottles and richer concentrations.',
                'query' => '?sort=price_desc',
            ],
        ];
    }

    public function findProductIdBySlug(string $slug): ?int
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM products
            WHERE slug = :slug
              AND deleted_at IS NULL
            LIMIT 1
        ");

        $stmt->execute([
            'slug' => $slug,
        ]);

        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

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

    public function countAllActive(array $filters = []): int
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $brandId = (int) ($filters['brand_id'] ?? 0);
        $fragranceTypeId = (int) ($filters['fragrance_type_id'] ?? 0);
        $gender = trim((string) ($filters['gender'] ?? ''));

        $topNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['top_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $middleNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['middle_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $baseNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['base_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $conditions = ['p.deleted_at IS NULL'];
        $params = [];

        if ($search !== '') {
            $tokens = preg_split('/\s+/', mb_strtolower($search), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($tokens as $index => $token) {
                $paramKey = 'search_' . $index;

                $conditions[] = "(
                    LOWER(p.name) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.family_name, '')) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.concentration_label, '')) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.description, '')) LIKE :{$paramKey}
                    OR LOWER(b.name) LIKE :{$paramKey}
                    OR LOWER(COALESCE(ft.name, '')) LIKE :{$paramKey}
                )";

                $params[$paramKey] = '%' . $token . '%';
            }
        }

        if ($brandId > 0) {
            $conditions[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = $brandId;
        }

        if ($fragranceTypeId > 0) {
            $conditions[] = 'p.fragrance_type_id = :fragrance_type_id';
            $params['fragrance_type_id'] = $fragranceTypeId;
        }

        if (in_array($gender, ['male', 'female', 'unisex'], true)) {
            $conditions[] = 'p.gender = :gender';
            $params['gender'] = $gender;
        }

        if ($topNoteIds !== []) {
            $topPlaceholders = [];

            foreach ($topNoteIds as $index => $noteId) {
                $paramKey = 'top_note_id_' . $index;
                $topPlaceholders[] = ':' . $paramKey;
                $params[$paramKey] = $noteId;
            }

            $conditions[] = "
                EXISTS (
                    SELECT 1
                    FROM product_notes pn_top
                    WHERE pn_top.product_id = p.id
                      AND pn_top.note_type = 'top'
                      AND pn_top.note_id IN (" . implode(', ', $topPlaceholders) . ")
                )
            ";
        }

        if ($middleNoteIds !== []) {
            $middlePlaceholders = [];

            foreach ($middleNoteIds as $index => $noteId) {
                $paramKey = 'middle_note_id_' . $index;
                $middlePlaceholders[] = ':' . $paramKey;
                $params[$paramKey] = $noteId;
            }

            $conditions[] = "
                EXISTS (
                    SELECT 1
                    FROM product_notes pn_middle
                    WHERE pn_middle.product_id = p.id
                      AND pn_middle.note_type = 'middle'
                      AND pn_middle.note_id IN (" . implode(', ', $middlePlaceholders) . ")
                )
            ";
        }

        if ($baseNoteIds !== []) {
            $basePlaceholders = [];

            foreach ($baseNoteIds as $index => $noteId) {
                $paramKey = 'base_note_id_' . $index;
                $basePlaceholders[] = ':' . $paramKey;
                $params[$paramKey] = $noteId;
            }

            $conditions[] = "
                EXISTS (
                    SELECT 1
                    FROM product_notes pn_base
                    WHERE pn_base.product_id = p.id
                      AND pn_base.note_type = 'base'
                      AND pn_base.note_id IN (" . implode(', ', $basePlaceholders) . ")
                )
            ";
        }

        $whereSql = implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            WHERE {$whereSql}
        ");

        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
