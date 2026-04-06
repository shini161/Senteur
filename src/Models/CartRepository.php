<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use RuntimeException;

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

        $stmt->execute([
            'id' => $variantId,
        ]);

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
                p.slug AS product_slug,
                p.name AS product_name,
                b.name AS brand_name,
                p.concentration_label,
                pi.image_url
            FROM product_variants v
            INNER JOIN products p ON p.id = v.product_id
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE v.id IN ($placeholders)
            ORDER BY b.name ASC, p.name ASC, v.size_ml ASC
        ");

        $stmt->execute($variantIds);

        return $stmt->fetchAll();
    }

    public function findVariantStockForUpdate(int $variantId): ?int
    {
        $stmt = $this->pdo->prepare("
            SELECT stock
            FROM product_variants
            WHERE id = :id
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([
            'id' => $variantId,
        ]);

        $stock = $stmt->fetchColumn();

        return $stock === false ? null : (int) $stock;
    }

    public function decrementVariantStock(int $variantId, int $quantity): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE product_variants
            SET stock = stock - :quantity
            WHERE id = :id
              AND stock >= :quantity
        ");

        $stmt->execute([
            'id' => $variantId,
            'quantity' => $quantity,
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Failed to decrement stock for variant ' . $variantId);
        }
    }
}
