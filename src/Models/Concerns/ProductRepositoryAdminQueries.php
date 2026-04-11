<?php

declare(strict_types=1);

namespace App\Models\Concerns;

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

    /**
     * Updates a product and fully replaces its variants inside one transaction.
     */
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

            // Variants are replaced wholesale so the admin form can stay simple
            // without managing deleted/created/updated lines separately.
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
}
