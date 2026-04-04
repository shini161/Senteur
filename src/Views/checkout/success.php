<section class="auth-page">
    <div class="auth-card">
        <h1>Payment confirmation</h1>

        <?php if (!empty($error)): ?>
            <p class="auth-subtitle"><?= htmlspecialchars($error) ?></p>
        <?php else: ?>
            <p class="auth-subtitle">
                <?php if ($paymentStatus === 'paid'): ?>
                    Your payment was confirmed successfully.
                <?php else: ?>
                    Your order was created. Payment confirmation is still pending.
                <?php endif; ?>
            </p>

            <?php if ($orderPublicId !== ''): ?>
                <p><strong>Order ID:</strong> <?= htmlspecialchars($orderPublicId) ?></p>
                <p><strong>Payment status:</strong> <?= htmlspecialchars((string) $paymentStatus) ?></p>
            <?php endif; ?>

            <p>
                <a href="/orders/<?= urlencode($orderPublicId) ?>">View order</a>
            </p>
        <?php endif; ?>

        <p>
            <a href="/products">Continue shopping</a>
        </p>
    </div>
</section>