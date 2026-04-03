<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

class OrderRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    public function createOrderWithItems(array $orderData, array $items): string
    {
        $this->pdo->beginTransaction();

        try {
            $orderStmt = $this->pdo->prepare("
                INSERT INTO orders (
                    public_id,
                    user_id,
                    shipping_address_id,
                    status,
                    subtotal_amount,
                    shipping_cost,
                    total_amount
                ) VALUES (
                    :public_id,
                    :user_id,
                    :shipping_address_id,
                    :status,
                    :subtotal_amount,
                    :shipping_cost,
                    :total_amount
                )
            ");

            $orderStmt->execute([
                'public_id' => $orderData['public_id'],
                'user_id' => $orderData['user_id'],
                'shipping_address_id' => $orderData['shipping_address_id'],
                'status' => $orderData['status'],
                'subtotal_amount' => $orderData['subtotal_amount'],
                'shipping_cost' => $orderData['shipping_cost'],
                'total_amount' => $orderData['total_amount'],
            ]);

            $orderId = (int) $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare("
                INSERT INTO order_items (
                    order_id,
                    product_variant_id,
                    product_name_snapshot,
                    size_ml_snapshot,
                    quantity,
                    price_at_purchase
                ) VALUES (
                    :order_id,
                    :product_variant_id,
                    :product_name_snapshot,
                    :size_ml_snapshot,
                    :quantity,
                    :price_at_purchase
                )
            ");

            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_variant_id' => $item['variant_id'],
                    'product_name_snapshot' => $item['product_name'],
                    'size_ml_snapshot' => $item['size_ml'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);
            }

            $this->pdo->commit();

            return $orderData['public_id'];
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                o.id,
                o.public_id,
                o.status,
                o.subtotal_amount,
                o.shipping_cost,
                o.total_amount,
                o.created_at,
                COALESCE(SUM(oi.quantity), 0) AS items_count
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.user_id = :user_id
            GROUP BY
                o.id,
                o.public_id,
                o.status,
                o.subtotal_amount,
                o.shipping_cost,
                o.total_amount,
                o.created_at
            ORDER BY o.created_at DESC
        ");

        $stmt->execute([
            'user_id' => $userId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByPublicIdForUser(string $publicId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
        SELECT
            o.id,
            o.public_id,
            o.status,
            o.subtotal_amount,
            o.shipping_cost,
            o.total_amount,
            o.paid_at,
            o.shipped_at,
            o.delivered_at,
            o.created_at,
            ua.full_name,
            ua.address_line,
            ua.city,
            ua.postal_code,
            ua.country
        FROM orders o
        LEFT JOIN user_addresses ua ON ua.id = o.shipping_address_id
        WHERE o.public_id = :public_id
          AND o.user_id = :user_id
        LIMIT 1
    ");

        $stmt->execute([
            'public_id' => $publicId,
            'user_id' => $userId,
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    public function findItemsByOrderId(int $orderId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                order_id,
                product_variant_id,
                product_name_snapshot,
                size_ml_snapshot,
                quantity,
                price_at_purchase,
                created_at
            FROM order_items
            WHERE order_id = :order_id
            ORDER BY id ASC
        ");

        $stmt->execute([
            'order_id' => $orderId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
