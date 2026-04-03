<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class CartRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    public function findVariantStock(int $variantId): ?int
    {
        $stmt = $this->pdo->prepare("
            SELECT stock
            FROM product_variants
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $variantId]);
        $stock = $stmt->fetchColumn();

        return $stock === false ? null : (int) $stock;
    }

    public function findItemsByVariantIds(array $variantIds): array
    {
        if ($variantIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($variantIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT
                v.id AS variant_id,
                v.size_ml,
                v.price,
                v.stock,
                p.id AS product_id,
                p.name AS product_name,
                pi.image_url
            FROM product_variants v
            INNER JOIN products p ON p.id = v.product_id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE v.id IN ($placeholders)
            ORDER BY p.name ASC, v.size_ml ASC
        ");

        $stmt->execute($variantIds);

        return $stmt->fetchAll();
    }
}
