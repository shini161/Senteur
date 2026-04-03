<section class="auth-page">
    <div class="auth-card">
        <h1>Order placed</h1>
        <p class="auth-subtitle">Your order was created successfully.</p>

        <?php if ($orderPublicId !== ''): ?>
            <p><strong>Order ID:</strong> <?= htmlspecialchars($orderPublicId) ?></p>
        <?php endif; ?>

        <p>
            <a href="/products">Continue shopping</a>
        </p>
    </div>
</section>