<?php

namespace App\Services;

use App\Core\Database;

class ProductService
{
    public function getAll(): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->query("
            SELECT 
                p.id, 
                p.name,
                MIN(v.price) AS price
            FROM products p
            JOIN product_variants v ON v.product_id = p.id
            GROUP BY p.id, p.name
            ORDER BY p.id DESC
        ");

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $pdo = Database::getConnection();

        // Fetch the main product data
        $productStmt = $pdo->prepare("
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

        // Fetch all variants for this product
        $variantStmt = $pdo->prepare("
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
