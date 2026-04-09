<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use RuntimeException;
use Throwable;

class OrderRepository
{
    public function __construct(
        private ?PDO $pdo = null,
        private ?CartRepository $cartRepository = null
    ) {
        $this->pdo ??= Database::getConnection();
        $this->cartRepository ??= new CartRepository($this->pdo);
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
                    brand_name_snapshot,
                    concentration_label_snapshot,
                    image_url_snapshot,
                    size_ml_snapshot,
                    quantity,
                    price_at_purchase
                ) VALUES (
                    :order_id,
                    :product_variant_id,
                    :product_name_snapshot,
                    :brand_name_snapshot,
                    :concentration_label_snapshot,
                    :image_url_snapshot,
                    :size_ml_snapshot,
                    :quantity,
                    :price_at_purchase
                )
            ");

            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_variant_id' => (int) $item['variant_id'],
                    'product_name_snapshot' => (string) $item['product_name'],
                    'brand_name_snapshot' => (string) $item['brand_name'],
                    'concentration_label_snapshot' => $item['concentration_label'] !== null
                        ? (string) $item['concentration_label']
                        : null,
                    'image_url_snapshot' => $item['image_url'] !== null
                        ? (string) $item['image_url']
                        : null,
                    'size_ml_snapshot' => (int) $item['size_ml'],
                    'quantity' => (int) $item['quantity'],
                    'price_at_purchase' => (float) $item['price'],
                ]);
            }

            $this->pdo->commit();

            return $orderData['public_id'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function findByUserId(int $userId, ?string $status = null): array
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
              AND (:status IS NULL OR o.status = :status)
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
            'status' => $status,
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

    public function findOrderByPublicIdForUser(string $publicId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM orders
            WHERE public_id = :public_id
              AND user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            'public_id' => $publicId,
            'user_id' => $userId,
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    public function findById(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM orders
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $orderId,
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
                brand_name_snapshot,
                concentration_label_snapshot,
                image_url_snapshot,
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

    public function markAsPaidAndProcessing(int $orderId): void
    {
        $this->pdo->beginTransaction();

        try {
            $order = $this->findById($orderId);

            if ($order === null) {
                throw new RuntimeException('Order not found.');
            }

            if (
                $order['status'] === 'processing'
                || $order['status'] === 'shipped'
                || $order['status'] === 'delivered'
            ) {
                $this->pdo->commit();
                return;
            }

            $items = $this->findItemsByOrderId($orderId);

            foreach ($items as $item) {
                $variantId = (int) $item['product_variant_id'];
                $quantity = (int) $item['quantity'];

                $stock = $this->cartRepository->findVariantStockForUpdate($variantId);

                if ($stock === null) {
                    throw new RuntimeException('Product variant not found.');
                }

                if ($stock < $quantity) {
                    throw new RuntimeException('Insufficient stock while confirming paid order.');
                }
            }

            foreach ($items as $item) {
                $this->cartRepository->decrementVariantStock(
                    (int) $item['product_variant_id'],
                    (int) $item['quantity']
                );
            }

            $stmt = $this->pdo->prepare("
                UPDATE orders
                SET
                    status = 'processing',
                    paid_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->execute([
                'id' => $orderId,
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function findAllForAdmin(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                o.id,
                o.public_id,
                o.status,
                o.total_amount,
                o.created_at,
                u.email AS user_email,
                u.username,
                COALESCE(SUM(oi.quantity), 0) AS items_count
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            GROUP BY
                o.id,
                o.public_id,
                o.status,
                o.total_amount,
                o.created_at,
                u.email,
                u.username
            ORDER BY o.created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByPublicIdForAdmin(string $publicId): ?array
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
                u.email AS user_email,
                u.username,
                ua.full_name,
                ua.address_line,
                ua.city,
                ua.postal_code,
                ua.country
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            LEFT JOIN user_addresses ua ON ua.id = o.shipping_address_id
            WHERE o.public_id = :public_id
            LIMIT 1
        ");

        $stmt->execute([
            'public_id' => $publicId,
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    public function updateStatusByPublicId(string $publicId, string $status): void
    {
        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (! in_array($status, $allowed, true)) {
            throw new RuntimeException('Invalid order status.');
        }

        $fields = [
            'shipped_at = shipped_at',
            'delivered_at = delivered_at',
        ];

        if ($status === 'shipped') {
            $fields[0] = 'shipped_at = CURRENT_TIMESTAMP';
        }

        if ($status === 'delivered') {
            $fields[0] = 'shipped_at = COALESCE(shipped_at, CURRENT_TIMESTAMP)';
            $fields[1] = 'delivered_at = CURRENT_TIMESTAMP';
        }

        $stmt = $this->pdo->prepare("
            UPDATE orders
            SET
                status = :status,
                {$fields[0]},
                {$fields[1]}
            WHERE public_id = :public_id
        ");

        $stmt->execute([
            'status' => $status,
            'public_id' => $publicId,
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Order not found or unchanged.');
        }
    }

    public function userHasPurchasedProduct(int $userId, int $productId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM orders o
            INNER JOIN order_items oi ON oi.order_id = o.id
            INNER JOIN product_variants pv ON pv.id = oi.product_variant_id
            WHERE o.user_id = :user_id
              AND pv.product_id = :product_id
              AND o.status IN ('processing', 'shipped', 'delivered')
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function countByUserId(int $userId, ?string $status = null): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM orders o
            WHERE o.user_id = :user_id
              AND (:status IS NULL OR o.status = :status)
        ");

        $stmt->execute([
            'user_id' => $userId,
            'status' => $status,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function findByUserIdPaginated(int $userId, ?string $status = null, int $limit = 5, int $offset = 0): array
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
              AND (:status IS NULL OR o.status = :status)
            GROUP BY
                o.id,
                o.public_id,
                o.status,
                o.subtotal_amount,
                o.shipping_cost,
                o.total_amount,
                o.created_at
            ORDER BY o.created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        if ($status === null) {
            $stmt->bindValue(':status', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
