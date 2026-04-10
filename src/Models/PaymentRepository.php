<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Repository for local payment records linked to orders.
 */
class PaymentRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    /**
     * Creates a local pending payment record before redirecting to a provider.
     */
    public function createPendingPayment(
        int $orderId,
        string $provider,
        float $amount,
        string $currency = 'EUR'
    ): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO payments (
                order_id,
                provider,
                status,
                amount,
                currency
            ) VALUES (
                :order_id,
                :provider,
                'pending',
                :amount,
                :currency
            )
        ");

        $stmt->execute([
            'order_id' => $orderId,
            'provider' => $provider,
            'amount' => $amount,
            'currency' => strtoupper($currency),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Returns the payment associated with one order.
     */
    public function findByOrderId(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM payments
            WHERE order_id = :order_id
            LIMIT 1
        ");

        $stmt->execute([
            'order_id' => $orderId,
        ]);

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $payment ?: null;
    }

    /**
     * Returns the payment associated with one Stripe Checkout session id.
     */
    public function findByStripeSessionId(string $stripeSessionId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM payments
            WHERE stripe_session_id = :stripe_session_id
            LIMIT 1
        ");

        $stmt->execute([
            'stripe_session_id' => $stripeSessionId,
        ]);

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $payment ?: null;
    }

    /**
     * Returns a payment by provider transaction id.
     */
    public function findByTransactionId(string $transactionId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM payments
            WHERE transaction_id = :transaction_id
            LIMIT 1
        ");

        $stmt->execute([
            'transaction_id' => $transactionId,
        ]);

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $payment ?: null;
    }

    /**
     * Persists the Stripe Checkout session id created for a payment.
     */
    public function updateStripeSessionId(int $paymentId, string $stripeSessionId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE payments
            SET stripe_session_id = :stripe_session_id
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $paymentId,
            'stripe_session_id' => $stripeSessionId,
        ]);
    }

    /**
     * Marks a payment as successful and stores provider metadata for auditing.
     */
    public function markPaid(
        int $paymentId,
        string $transactionId,
        ?string $providerPayload = null
    ): void {
        $stmt = $this->pdo->prepare("
            UPDATE payments
            SET
                status = 'paid',
                transaction_id = :transaction_id,
                provider_payload = :provider_payload,
                paid_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $paymentId,
            'transaction_id' => $transactionId,
            'provider_payload' => $providerPayload,
        ]);
    }

    /**
     * Marks a payment as failed while keeping the provider payload for diagnosis.
     */
    public function markFailed(
        int $paymentId,
        ?string $providerPayload = null
    ): void {
        $stmt = $this->pdo->prepare("
            UPDATE payments
            SET
                status = 'failed',
                provider_payload = :provider_payload
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $paymentId,
            'provider_payload' => $providerPayload,
        ]);
    }
}
