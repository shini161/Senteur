<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderRepository;

/**
 * Provides admin-facing order read and update operations.
 */
class AdminOrderService
{
    private const PER_PAGE = 10;

    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    /**
     * Returns the order list used by the admin dashboard.
     */
    public function getOrderListData(array $rawFilters): array
    {
        $filters = $this->normalizeListFilters($rawFilters);
        $totalOrders = $this->orderRepository->countForAdmin($filters);
        $totalPages = max(1, (int) ceil($totalOrders / self::PER_PAGE));
        $currentPage = min($filters['page'], $totalPages);

        return [
            'orders' => $this->orderRepository->findPageForAdmin(
                $filters,
                self::PER_PAGE,
                ($currentPage - 1) * self::PER_PAGE
            ),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
        ];
    }

    /**
     * Loads one order and enriches its items with presentation-ready line totals.
     */
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

    /**
     * Updates the order lifecycle status from the admin panel.
     */
    public function updateStatus(string $publicId, string $status): void
    {
        $this->orderRepository->updateStatusByPublicId($publicId, $status);
    }

    /**
     * Normalizes admin order list filters from the query string.
     *
     * @param array<string, mixed> $rawFilters
     * @return array{q: string, status: string, page: int}
     */
    private function normalizeListFilters(array $rawFilters): array
    {
        $status = trim((string) ($rawFilters['status'] ?? ''));
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        return [
            'q' => trim((string) ($rawFilters['q'] ?? '')),
            'status' => in_array($status, $allowedStatuses, true) ? $status : '',
            'page' => max(1, (int) ($rawFilters['page'] ?? 1)),
        ];
    }
}
