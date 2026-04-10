<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderRepository;

/**
 * Prepares order data for the customer account area.
 */
class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    /**
     * Returns all orders belonging to one user, optionally filtered by status.
     */
    public function getUserOrders(int $userId, ?string $status = null): array
    {
        return $this->orderRepository->findByUserId($userId, $status);
    }

    /**
     * Loads a single order and computes line totals from stored purchase snapshots.
     */
    public function getUserOrderByPublicId(int $userId, string $publicId): ?array
    {
        $order = $this->orderRepository->findByPublicIdForUser($publicId, $userId);

        if ($order === null) {
            return null;
        }

        $items = $this->orderRepository->findItemsByOrderId((int) $order['id']);

        // The repository stores unit price snapshots; the service derives
        // presentation-friendly totals for each order line.
        foreach ($items as &$item) {
            $item['line_total'] = (float) $item['price_at_purchase'] * (int) $item['quantity'];
        }

        unset($item);

        $order['items'] = $items;

        return $order;
    }

    /**
     * Returns paginated order history for the "My Orders" page.
     */
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
