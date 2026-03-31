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
}
