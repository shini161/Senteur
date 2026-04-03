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
}
