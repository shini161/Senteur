<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AdminOrderService;
use RuntimeException;

class AdminOrderController extends Controller
{
    public function __construct(
        private AdminOrderService $adminOrderService
    ) {}

    public function index(): void
    {
        Auth::requireAdmin();

        $orders = $this->adminOrderService->getOrders();

        $this->render('admin/orders/index', [
            'title' => 'Admin Orders',
            'orders' => $orders,
            'error' => null,
        ]);
    }

    public function show(string $publicId): void
    {
        Auth::requireAdmin();

        $order = $this->adminOrderService->getOrderByPublicId($publicId);

        if ($order === null) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }

        $this->render('admin/orders/show', [
            'title' => 'Admin Order ' . $order['public_id'],
            'order' => $order,
            'error' => null,
        ]);
    }

    public function updateStatus(string $publicId): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $status = trim($_POST['status'] ?? '');

        try {
            $this->adminOrderService->updateStatus($publicId, $status);

            header('Location: /admin/orders/' . urlencode($publicId));
            exit;
        } catch (RuntimeException $e) {
            $order = $this->adminOrderService->getOrderByPublicId($publicId);

            if ($order === null) {
                http_response_code(404);
                echo 'Order not found';
                return;
            }

            $this->render('admin/orders/show', [
                'title' => 'Admin Order ' . $order['public_id'],
                'order' => $order,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
