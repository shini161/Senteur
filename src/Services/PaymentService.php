<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderRepository;
use App\Models\PaymentRepository;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

class PaymentService
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private OrderRepository $orderRepository
    ) {}

    public function createCheckoutSession(array $order): string
    {
        $secretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;
        $appUrl = $_ENV['APP_URL'] ?? null;

        if (!$secretKey || !$appUrl) {
            throw new RuntimeException('Stripe configuration is missing.');
        }

        Stripe::setApiKey($secretKey);

        $existingPayment = $this->paymentRepository->findByOrderId((int) $order['id']);

        if ($existingPayment === null) {
            $paymentId = $this->paymentRepository->createPendingPayment(
                (int) $order['id'],
                'stripe',
                (float) $order['total_amount'],
                'EUR'
            );

            $existingPayment = $this->paymentRepository->findByOrderId((int) $order['id']);

            if ($existingPayment === null) {
                throw new RuntimeException('Failed to create payment record.');
            }
        }

        $session = Session::create([
            'mode' => 'payment',
            'success_url' => rtrim($appUrl, '/') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => rtrim($appUrl, '/') . '/checkout/cancel?order=' . urlencode($order['public_id']),
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Order #' . $order['public_id'],
                    ],
                    'unit_amount' => (int) round((float) $order['total_amount'] * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'order_id' => (string) $order['id'],
                'order_public_id' => (string) $order['public_id'],
                'user_id' => (string) $order['user_id'],
            ],
        ]);

        $this->paymentRepository->updateStripeSessionId(
            (int) $existingPayment['id'],
            (string) $session->id
        );

        return $session->url;
    }

    public function getCheckoutSuccessData(int $userId, string $stripeSessionId): ?array
    {
        $payment = $this->paymentRepository->findByStripeSessionId($stripeSessionId);

        if ($payment === null) {
            return null;
        }

        $order = $this->orderRepository->findById((int) $payment['order_id']);

        if ($order === null || (int) $order['user_id'] !== $userId) {
            return null;
        }

        return [
            'order' => $order,
            'payment' => $payment,
        ];
    }

    public function handleStripeWebhook(string $payload, string $signature): void
    {
        $secretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;
        $webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;

        if (!$secretKey || !$webhookSecret) {
            throw new RuntimeException('Stripe webhook configuration is missing.');
        }

        Stripe::setApiKey($secretKey);

        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (UnexpectedValueException | SignatureVerificationException $e) {
            throw new RuntimeException('Invalid Stripe webhook payload.', 0, $e);
        }

        if ($event->type !== 'checkout.session.completed') {
            return;
        }

        $session = $event->data->object;

        $stripeSessionId = (string) ($session->id ?? '');
        $orderId = isset($session->metadata->order_id) ? (int) $session->metadata->order_id : 0;
        $transactionId = (string) ($session->payment_intent ?? '');

        if ($stripeSessionId === '' || $orderId <= 0 || $transactionId === '') {
            throw new RuntimeException('Stripe webhook missing required metadata.');
        }

        $payment = $this->paymentRepository->findByStripeSessionId($stripeSessionId);

        if ($payment === null) {
            $payment = $this->paymentRepository->findByOrderId($orderId);
        }

        if ($payment === null) {
            throw new RuntimeException('Payment record not found for order.');
        }

        if ($payment['status'] === 'paid') {
            return;
        }

        $this->paymentRepository->markPaid(
            (int) $payment['id'],
            $transactionId,
            $payload
        );

        $this->orderRepository->markAsPaidAndProcessing($orderId);
    }
}
