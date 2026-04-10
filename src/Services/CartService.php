<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CartRepository;

/**
 * Manages the cart stored in the PHP session and enriches it with product data.
 */
class CartService
{
    public function __construct(
        private CartRepository $cartRepository
    ) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['cart'] ??= [];
    }

    /**
     * Returns cart lines enriched with variant details, capped quantities, and subtotals.
     */
    public function getItems(): array
    {
        $cart = $this->getCart();

        if ($cart === []) {
            return [];
        }

        $variantIds = array_map('intval', array_keys($cart));
        $variants = $this->cartRepository->findItemsByVariantIds($variantIds);

        $items = [];

        foreach ($variants as $variant) {
            $variantId = (int) $variant['variant_id'];
            $quantity = (int) ($cart[$variantId] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $price = (float) $variant['price'];
            $stock = (int) $variant['stock'];
            // The UI and business rules cap cart quantities at 5 per variant
            // even if stock is higher to keep the demo storefront manageable.
            $maxQuantity = max(0, min($stock, 5));
            $finalQuantity = min($quantity, $maxQuantity > 0 ? $maxQuantity : $quantity);

            $items[] = [
                'variant_id' => $variantId,
                'product_id' => (int) $variant['product_id'],
                'product_slug' => $variant['product_slug'],
                'product_name' => $variant['product_name'],
                'brand_name' => $variant['brand_name'] ?? null,
                'concentration_label' => $variant['concentration_label'] ?? null,
                'size_ml' => (int) $variant['size_ml'],
                'price' => $price,
                'stock' => $stock,
                'image_url' => $variant['image_url'] ?? null,
                'quantity' => $finalQuantity,
                'max_quantity' => $maxQuantity,
                'subtotal' => $price * $finalQuantity,
            ];
        }

        return $items;
    }

    /**
     * Calculates the cart total from the normalized cart lines.
     */
    public function getTotal(): float
    {
        $total = 0.0;

        foreach ($this->getItems() as $item) {
            $total += (float) $item['subtotal'];
        }

        return $total;
    }

    /**
     * Adds quantity to an existing cart line while respecting stock and hard caps.
     */
    public function addItem(int $variantId, int $quantity): void
    {
        if ($variantId <= 0 || $quantity <= 0) {
            return;
        }

        $stock = $this->cartRepository->findVariantStock($variantId);

        if ($stock === null || $stock <= 0) {
            return;
        }

        $cart = $this->getCart();
        $currentQuantity = (int) ($cart[$variantId] ?? 0);
        $newQuantity = min($currentQuantity + $quantity, min($stock, 5));

        $cart[$variantId] = $newQuantity;
        $this->storeCart($cart);
    }

    /**
     * Replaces the quantity for one cart line or removes it when quantity drops to zero.
     */
    public function updateItem(int $variantId, int $quantity): void
    {
        if ($variantId <= 0) {
            return;
        }

        if ($quantity <= 0) {
            $this->removeItem($variantId);
            return;
        }

        $stock = $this->cartRepository->findVariantStock($variantId);

        if ($stock === null || $stock <= 0) {
            $this->removeItem($variantId);
            return;
        }

        $cart = $this->getCart();
        $cart[$variantId] = min($quantity, min($stock, 5));

        $this->storeCart($cart);
    }

    /**
     * Removes a variant from the session cart.
     */
    public function removeItem(int $variantId): void
    {
        $cart = $this->getCart();
        unset($cart[$variantId]);

        $this->storeCart($cart);
    }

    /**
     * Empties the cart, typically after a confirmed payment.
     */
    public function clear(): void
    {
        $this->storeCart([]);
    }

    /**
     * Returns a sanitized cart map of `variantId => quantity`.
     */
    private function getCart(): array
    {
        $cart = $_SESSION['cart'] ?? [];

        if (! is_array($cart)) {
            return [];
        }

        $normalized = [];

        foreach ($cart as $variantId => $quantity) {
            $variantId = (int) $variantId;
            $quantity = (int) $quantity;

            if ($variantId > 0 && $quantity > 0) {
                $normalized[$variantId] = $quantity;
            }
        }

        return $normalized;
    }

    /**
     * Persists the normalized cart back into the active session.
     */
    private function storeCart(array $cart): void
    {
        $_SESSION['cart'] = $cart;
    }
}
