<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PaymentService;
use RuntimeException;

class StripeWebhookController
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function handle(): void
    {
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        if (!$payload || !$signature) {
            http_response_code(400);
            echo 'Invalid webhook request';
            return;
        }

        try {
            $this->paymentService->handleStripeWebhook($payload, $signature);

            http_response_code(200);
            echo 'Webhook handled';
        } catch (RuntimeException $e) {
            http_response_code(400);
            echo 'Webhook error: ' . $e->getMessage();
        }
    }
}
