<?php
// Payment cancellation page with quick routes back to checkout or the cart.
?>
<section class="auth-page">
    <div class="auth-card">
        <h1>Payment cancelled</h1>
        <p class="auth-subtitle">Your payment was not completed.</p>

        <?php if ($orderPublicId !== ''): ?>
            <p><strong>Order ID:</strong> <?= htmlspecialchars($orderPublicId) ?></p>
        <?php endif; ?>

        <p>
            <a href="/checkout">Return to checkout</a>
        </p>

        <p>
            <a href="/cart">Back to cart</a>
        </p>
    </div>
</section>
