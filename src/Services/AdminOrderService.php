<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderRepository;

class AdminOrderService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function getOrders(): array
    {
        return $this->orderRepository->findAllForAdmin();
    }

    public function getOrderByPublicId(string $publicId): ?array
    {
        $order = $this->orderRepository->findByPublicIdForAdmin($publicId);

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

    public function updateStatus(string $publicId, string $status): void
    {
        $this->orderRepository->updateStatusByPublicId($publicId, $status);
    }
}
