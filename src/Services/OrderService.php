<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderRepository;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function getUserOrders(int $userId, ?string $status = null): array
    {
        return $this->orderRepository->findByUserId($userId, $status);
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

    public function getUserOrdersPaginated(int $userId, ?string $status, int $page, int $perPage): array
    {
        $total = $this->orderRepository->countByUserId($userId, $status);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);
        $offset = ($page - 1) * $perPage;

        $orders = $this->orderRepository->findByUserIdPaginated($userId, $status, $perPage, $offset);

        return [
            'orders' => $orders,
            'total' => $total,
            'pages' => $pages,
        ];
    }
}
