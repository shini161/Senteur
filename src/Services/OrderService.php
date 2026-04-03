<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderRepository;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function getUserOrders(int $userId): array
    {
        return $this->orderRepository->findByUserId($userId);
    }

    public function getUserOrderByPublicId(int $userId, string $publicId): ?array
    {
        $order = $this->orderRepository->findByPublicIdForUser($publicId, $userId);

        if ($order === null) {
            return null;
        }

        $items = $this->orderRepository->findItemsByOrderId((int) $order['id']);

        foreach ($items as &$item) {
            $item['line_total'] = (float) $item['price_at_purchase'] * (int) $item['quantity'];
        }

        unset($item);

        $order['items'] = $items;

        return $order;
    }
}
