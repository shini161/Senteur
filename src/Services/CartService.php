<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CartRepository;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository
    ) {
        if (! isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addItem(int $variantId, int $quantity): void
    {
        $stock = $this->cartRepository->findVariantStock($variantId);

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

        $stock = $this->cartRepository->findVariantStock($variantId);

        if ($stock === null) {
            return;
        }

        $_SESSION['cart'][$variantId] = min($quantity, $stock);
    }

    public function removeItem(int $variantId): void
    {
        unset($_SESSION['cart'][$variantId]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function getItems(): array
    {
        $cart = $_SESSION['cart'] ?? [];

        if ($cart === []) {
            return [];
        }

        $items = $this->cartRepository->findItemsByVariantIds(array_keys($cart));

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
}
