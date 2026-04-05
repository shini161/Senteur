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

    public function findAllActive(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                p.id,
                p.name,
                p.slug,
                MIN(v.price) AS price,
                COUNT(v.id) AS variant_count,
                COALESCE(SUM(CASE WHEN v.stock > 0 THEN 1 ELSE 0 END), 0) AS in_stock_variant_count,
                pi.image_url
            FROM products p
            LEFT JOIN product_variants v ON v.product_id = p.id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE p.deleted_at IS NULL
            GROUP BY p.id, p.name, p.slug, pi.image_url
            ORDER BY p.id DESC
        ");

        $products = $stmt->fetchAll();

        foreach ($products as &$product) {
            $product['is_sellable'] = (int) $product['in_stock_variant_count'] > 0;
        }

        unset($product);

        return $products;
    }

    public function findActiveById(int $id): ?array
    {
        $productStmt = $this->pdo->prepare("
            SELECT 
                p.id,
                p.name,
                p.description,
                pi.image_url
            FROM products p
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE p.id = :id
              AND p.deleted_at IS NULL
            LIMIT 1
        ");

        $productStmt->execute(['id' => $id]);
        $product = $productStmt->fetch();

        if (! $product) {
            return null;
        }

        $variantStmt = $this->pdo->prepare("
            SELECT
                v.id,
                v.size_ml,
                v.price,
                v.stock
            FROM product_variants v
            WHERE v.product_id = :id
            ORDER BY v.price ASC
        ");

        $variantStmt->execute(['id' => $id]);
        $product['variants'] = $variantStmt->fetchAll();

        return $product;
    }

    public function findAllForAdmin(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                p.id,
                p.name,
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
                p.name,
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
                    name,
                    slug,
                    description,
                    gender
                ) VALUES (
                    :brand_id,
                    :fragrance_type_id,
                    :name,
                    :slug,
                    :description,
                    :gender
                )
            ");

            $stmt->execute([
                'brand_id' => $data['brand_id'],
                'fragrance_type_id' => $data['fragrance_type_id'] ?: null,
                'name' => $data['name'],
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
                    name = :name,
                    slug = :slug,
                    description = :description,
                    gender = :gender
                WHERE id = :id
            ");

            $stmt->execute([
                'id' => $id,
                'brand_id' => $data['brand_id'],
                'fragrance_type_id' => $data['fragrance_type_id'] ?: null,
                'name' => $data['name'],
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
}
