<section class="cart-page">
    <div class="cart-header">
        <h1>Cart</h1>
        <p class="cart-subtitle">Review your selected perfumes before checkout.</p>
    </div>

    <?php if (empty($items)): ?>
        <div class="panel cart-empty">
            <h2>Your cart is empty</h2>
            <p class="muted">Add a fragrance to continue to checkout.</p>
            <a href="/products" class="auth-button">Continue shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
                <?php foreach ($items as $item): ?>
                    <article class="panel cart-item">
                        <div class="cart-item-media">
                            <?php if (!empty($item['image_url'])): ?>
                                <img
                                    src="/<?= htmlspecialchars((string) $item['image_url']) ?>"
                                    alt="<?= htmlspecialchars((string) $item['product_name']) ?>">
                            <?php else: ?>
                                <div class="product-placeholder">SENTEUR</div>
                            <?php endif; ?>
                        </div>

                        <div class="cart-item-body">
                            <div class="cart-item-top">
                                <div>
                                    <?php if (!empty($item['brand_name'])): ?>
                                        <div class="cart-item-brand">
                                            <?= htmlspecialchars((string) $item['brand_name']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <h2 class="cart-item-title">
                                        <a href="/products/<?= htmlspecialchars((string) $item['product_slug']) ?>" class="cart-item-title-link">
                                            <?= htmlspecialchars((string) $item['product_name']) ?>
                                        </a>
                                    </h2>

                                    <p class="cart-item-meta muted">
                                        <?php if (!empty($item['concentration_label'])): ?>
                                            <span><?= htmlspecialchars((string) $item['concentration_label']) ?></span>
                                            <span class="cart-meta-separator">·</span>
                                        <?php endif; ?>

                                        <span><?= htmlspecialchars((string) $item['size_ml']) ?>ml</span>
                                    </p>
                                </div>

                                <div class="cart-item-price">
                                    €<?= number_format((float) $item['price'], 2) ?>
                                </div>
                            </div>
                            <div class="cart-item-bottom">
                                <div class="cart-item-bottom-left">
                                    <?php if ((int) $item['quantity'] > 1): ?>
                                        <div class="cart-item-subtotal">
                                            <span class="muted">Subtotal</span>
                                            <strong>€<?= number_format((float) $item['subtotal'], 2) ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="cart-item-actions">
                                    <form action="/cart/update" method="POST" class="cart-qty-form">
                                        <?= \App\Core\Csrf::input() ?>
                                        <input type="hidden" name="variant_id" value="<?= htmlspecialchars((string) $item['variant_id']) ?>">

                                        <div class="qty-stepper">
                                            <button type="button" class="qty-btn" data-qty-action="decrease" aria-label="Decrease quantity">−</button>
                                            <input
                                                type="number"
                                                name="quantity"
                                                min="0"
                                                max="<?= htmlspecialchars((string) $item['max_quantity']) ?>"
                                                value="<?= htmlspecialchars((string) $item['quantity']) ?>"
                                                class="qty-input"
                                                inputmode="numeric">
                                            <button type="button" class="qty-btn" data-qty-action="increase" aria-label="Increase quantity">+</button>
                                        </div>

                                        <button type="submit" class="button-secondary">Update</button>
                                    </form>

                                    <form action="/cart/remove" method="POST" class="cart-remove-form">
                                        <?= \App\Core\Csrf::input() ?>
                                        <input type="hidden" name="variant_id" value="<?= htmlspecialchars((string) $item['variant_id']) ?>">
                                        <button type="submit" class="button-link">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <aside class="panel cart-summary">
                <h2>Order summary</h2>

                <div class="cart-summary-row">
                    <span class="muted">Items</span>
                    <span><?= array_sum(array_map(static fn(array $item): int => (int) $item['quantity'], $items)) ?></span>
                </div>

                <div class="cart-summary-row">
                    <span class="muted">Shipping</span>
                    <span>Calculated at checkout</span>
                </div>

                <div class="cart-summary-total">
                    <span>Total</span>
                    <strong>€<?= number_format((float) $total, 2) ?></strong>
                </div>

                <a href="/checkout" class="auth-button cart-checkout-button">Proceed to checkout</a>
                <a href="/products" class="button-link">Continue shopping</a>
            </aside>
        </div>
    <?php endif; ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.cart-qty-form').forEach((form) => {
            const input = form.querySelector('.qty-input');
            const decreaseBtn = form.querySelector('[data-qty-action="decrease"]');
            const increaseBtn = form.querySelector('[data-qty-action="increase"]');

            if (!input || !decreaseBtn || !increaseBtn) {
                return;
            }

            const clampQuantity = () => {
                const min = Number(input.min || 0);
                const max = Number(input.max || 0);
                let value = Number(input.value || min);

                if (Number.isNaN(value)) {
                    value = min;
                }

                value = Math.max(min, Math.min(max, value));
                input.value = String(value);

                decreaseBtn.disabled = value <= min;
                increaseBtn.disabled = value >= max;
            };

            decreaseBtn.addEventListener('click', () => {
                input.stepDown();
                clampQuantity();
            });

            increaseBtn.addEventListener('click', () => {
                input.stepUp();
                clampQuantity();
            });

            input.addEventListener('input', clampQuantity);
            input.addEventListener('change', clampQuantity);

            clampQuantity();
        });
    });
</script>