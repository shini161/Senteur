<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class CartService
{
    public function __construct()
    {
        if (! isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addItem(int $variantId, int $quantity): void
    {
        $stock = $this->getVariantStock($variantId);

        if ($stock === null) {
            return;
        }

        $currentQuantity = $_SESSION['cart'][$variantId] ?? 0;
        $newQuantity = $currentQuantity + $quantity;

        $_SESSION['cart'][$variantId] = min($newQuantity, $stock);
    }

    public function updateItem(int $variantId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($variantId);
            return;
        }

        $stock = $this->getVariantStock($variantId);

        if ($stock === null) {
            return;
        }

        $_SESSION['cart'][$variantId] = min($quantity, $stock);
    }

    public function removeItem(int $variantId): void
    {
        unset($_SESSION['cart'][$variantId]);
    }

    public function getItems(): array
    {
        $cart = $_SESSION['cart'];

        if (empty($cart)) {
            return [];
        }

        $variantIds = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($variantIds), '?'));

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
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
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            $quantity = (int) ($cart[$item['variant_id']] ?? 0);
            $price = (float) $item['price'];

            $item['quantity'] = $quantity;
            $item['subtotal'] = $price * $quantity;
        }

        unset($item);

        return $items;
    }

    public function getTotal(): float
    {
        $total = 0.0;

        foreach ($this->getItems() as $item) {
            $total += (float) $item['subtotal'];
        }

        return $total;
    }

    private function getVariantStock(int $variantId): ?int
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT stock 
            FROM product_variants
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $variantId]);

        $stock = $stmt->fetchColumn();

        if ($stock === false) {
            return null;
        }

        return (int) $stock;
    }
}
