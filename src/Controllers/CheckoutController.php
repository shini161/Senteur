<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private PaymentService $paymentService
    ) {}

    public function index(): void
    {
        Auth::requireAuth();

        $userId = (int) Auth::id();

        try {
            $checkout = $this->checkoutService->getCheckoutData($userId);

            $this->render('checkout/index', [
                'title' => 'Checkout',
                'items' => $checkout['items'],
                'total' => $checkout['total'],
                'addresses' => $checkout['addresses'],
                'error' => null,
                'old' => [],
            ]);
        } catch (RuntimeException $e) {
            $this->render('checkout/index', [
                'title' => 'Checkout',
                'items' => [],
                'total' => 0.0,
                'addresses' => [],
                'error' => $e->getMessage(),
                'old' => [],
            ]);
        }
    }

    public function store(): void
    {
        Auth::requireAuth();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $userId = (int) Auth::id();
        $addressId = (int) ($_POST['shipping_address_id'] ?? 0);

        try {
            $publicId = $this->checkoutService->placeOrder($userId, $addressId);

            $order = $this->checkoutService->getPlacedOrder($userId, $publicId);

            if ($order === null) {
                throw new RuntimeException('Order not found after creation.');
            }

            $sessionUrl = $this->paymentService->createCheckoutSession($order);

            header('Location: ' . $sessionUrl);
            exit;
        } catch (RuntimeException $e) {
            try {
                $checkout = $this->checkoutService->getCheckoutData($userId);

                $this->render('checkout/index', [
                    'title' => 'Checkout',
                    'items' => $checkout['items'],
                    'total' => $checkout['total'],
                    'addresses' => $checkout['addresses'],
                    'error' => $e->getMessage(),
                    'old' => [
                        'shipping_address_id' => $addressId,
                    ],
                ]);
            } catch (RuntimeException $inner) {
                $this->render('checkout/index', [
                    'title' => 'Checkout',
                    'items' => [],
                    'total' => 0.0,
                    'addresses' => [],
                    'error' => $inner->getMessage(),
                    'old' => [],
                ]);
            }
        }
    }

    public function success(): void
    {
        Auth::requireAuth();

        $userId = (int) Auth::id();
        $sessionId = trim($_GET['session_id'] ?? '');

        if ($sessionId === '') {
            http_response_code(400);

            $this->render('checkout/success', [
                'title' => 'Payment confirmation',
                'orderPublicId' => '',
                'paymentStatus' => null,
                'error' => 'Missing Stripe session id.',
            ]);
            return;
        }

        $result = $this->paymentService->getCheckoutSuccessData($userId, $sessionId);

        if ($result === null) {
            http_response_code(404);

            $this->render('checkout/success', [
                'title' => 'Payment confirmation',
                'orderPublicId' => '',
                'paymentStatus' => null,
                'error' => 'Order payment could not be found.',
            ]);
            return;
        }

        $this->render('checkout/success', [
            'title' => 'Payment confirmation',
            'orderPublicId' => $result['order']['public_id'],
            'paymentStatus' => $result['payment']['status'],
            'error' => null,
        ]);
    }

    public function cancel(): void
    {
        Auth::requireAuth();

        $orderPublicId = trim($_GET['order'] ?? '');

        $this->render('checkout/cancel', [
            'title' => 'Payment cancelled',
            'orderPublicId' => $orderPublicId,
        ]);
    }
}
