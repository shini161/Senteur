<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AddressRepository;
use App\Models\OrderRepository;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private AddressRepository $addressRepository,
        private OrderRepository $orderRepository
    ) {}

    public function getCheckoutData(int $userId): array
    {
        $items = $this->cartService->getItems();

        if ($items === []) {
            throw new RuntimeException('Your cart is empty.');
        }

        $addresses = $this->addressRepository->findByUserId($userId);

        if ($addresses === []) {
            throw new RuntimeException('You need at least one saved address before checkout.');
        }

        return [
            'items' => $items,
            'total' => $this->cartService->getTotal(),
            'addresses' => $addresses,
        ];
    }

    public function placeOrder(int $userId, int $addressId): string
    {
        $items = $this->cartService->getItems();

        if ($items === []) {
            throw new RuntimeException('Your cart is empty.');
        }

        $address = $this->addressRepository->findByIdForUser($addressId, $userId);

        if (! $address) {
            throw new RuntimeException('Invalid shipping address.');
        }

        foreach ($items as $item) {
            if ((int) $item['quantity'] <= 0) {
                throw new RuntimeException('Invalid cart item quantity.');
            }

            if ((int) $item['stock'] < (int) $item['quantity']) {
                throw new RuntimeException('One or more cart items no longer have enough stock.');
            }
        }

        $subtotal = 0.0;

        foreach ($items as $item) {
            $subtotal += (float) $item['subtotal'];
        }

        $shippingCost = 0.0;
        $total = $subtotal + $shippingCost;

        $publicId = $this->generatePublicId();

        $this->orderRepository->createOrderWithItems([
            'public_id' => $publicId,
            'user_id' => $userId,
            'shipping_address_id' => $addressId,
            'status' => 'pending',
            'subtotal_amount' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_amount' => $total,
        ], $items);

        $this->clearCart();

        return $publicId;
    }

    private function clearCart(): void
    {
        $_SESSION['cart'] = [];
    }

    private function generatePublicId(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(10)), 0, 10));
    }
}
