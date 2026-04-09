<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(): void
    {
        $userId = Auth::id();

        if ($userId === null) {
            header('Location: /login');
            exit;
        }

        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered'];
        $status = $_GET['status'] ?? null;

        if (!is_string($status) || !in_array($status, $allowedStatuses, true)) {
            $status = null;
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 5;

        $result = $this->orderService->getUserOrdersPaginated($userId, $status, $page, $perPage);

        $this->render('orders/index', [
            'title' => 'My Orders',
            'orders' => $result['orders'],
            'status' => $status,
            'page' => $page,
            'perPage' => $perPage,
            'totalOrders' => $result['total'],
            'totalPages' => $result['pages'],
        ]);
    }

    public function show(string $publicId): void
    {
        $userId = Auth::id();

        if ($userId === null) {
            header('Location: /login');
            exit;
        }

        $order = $this->orderService->getUserOrderByPublicId($userId, $publicId);

        if ($order === null) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }

        $this->render('orders/show', [
            'title' => 'Order ' . $order['public_id'],
            'order' => $order,
        ]);
    }
}
