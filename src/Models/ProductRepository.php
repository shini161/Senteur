<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

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
            COALESCE(SUM(CASE WHEN v.stock > 0 THEN 1 ELSE 0 END), 0) AS in_stock_variant_count
        FROM products p
        LEFT JOIN product_variants v ON v.product_id = p.id
        WHERE p.deleted_at IS NULL
        GROUP BY p.id, p.name, p.slug
        ORDER BY p.id DESC
    ");

        $products = $stmt->fetchAll();

        foreach ($products as &$product) {
            $product['is_sellable'] = (int) $product['in_stock_variant_count'] > 0;
        }

        return $products;
    }

    public function findActiveById(int $id): ?array
    {
        $productStmt = $this->pdo->prepare("
            SELECT 
                p.id,
                p.name,
                p.description
            FROM products p
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
}
