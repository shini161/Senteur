<section class="checkout-page">
    <div class="checkout-grid">

        <div class="checkout-card">
            <h1>Checkout</h1>
            <p class="checkout-subtitle">Review your order and choose a shipping address.</p>

            <?php if (! empty($error)): ?>
                <p class="auth-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (empty($items)): ?>
                <p>Your cart is empty.</p>
                <p><a href="/cart">Go back to cart</a></p>
            <?php else: ?>
                <div class="checkout-items">
                    <?php foreach ($items as $item): ?>
                        <article class="checkout-item">
                            <div class="checkout-item-left">
                                <div class="checkout-item-media">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img
                                            src="/<?= htmlspecialchars((string) $item['image_url']) ?>"
                                            alt="<?= htmlspecialchars((string) $item['product_name']) ?>">
                                    <?php else: ?>
                                        <div class="product-placeholder">SENTEUR</div>
                                    <?php endif; ?>
                                </div>

                                <div class="checkout-item-main">
                                    <?php if (!empty($item['brand_name'])): ?>
                                        <div class="checkout-item-brand">
                                            <?= htmlspecialchars((string) $item['brand_name']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="checkout-item-name">
                                        <?= htmlspecialchars((string) $item['product_name']) ?>
                                    </div>

                                    <div class="checkout-item-meta muted">
                                        <?php if (!empty($item['concentration_label'])): ?>
                                            <span><?= htmlspecialchars((string) $item['concentration_label']) ?></span>
                                            <span class="cart-meta-separator">·</span>
                                        <?php endif; ?>

                                        <span><?= htmlspecialchars((string) $item['size_ml']) ?>ml</span>
                                    </div>
                                </div>
                            </div>

                            <div class="checkout-item-side">
                                <div class="checkout-item-price">
                                    €<?= number_format((float) $item['subtotal'], 2) ?>
                                </div>

                                <div class="checkout-item-qty muted">
                                    Qty <?= htmlspecialchars((string) $item['quantity']) ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="checkout-total">
                    <span>Total</span>
                    <strong><?= number_format($total, 2) ?>€</strong>
                </div>
            <?php endif; ?>
        </div>

        <div class="checkout-card">
            <h2>Shipping address</h2>

            <?php if (! empty($items) && ! empty($addresses)): ?>
                <form method="POST" action="/checkout" class="auth-form">
                    <?= \App\Core\Csrf::input() ?>

                    <div class="form-group">
                        <div class="form-group-header">
                            <label for="shipping_address_id">Choose address</label>
                            <a href="/addresses" class="button-link">Add new address</a>
                        </div>

                        <select id="shipping_address_id" name="shipping_address_id" class="checkout-select" required>
                            <option value="">Select an address</option>

                            <?php foreach ($addresses as $address): ?>
                                <option
                                    value="<?= htmlspecialchars((string) $address['id']) ?>"
                                    <?= ((string) ($old['shipping_address_id'] ?? '') === (string) $address['id']) || (empty($old['shipping_address_id']) && (int) $address['is_default'] === 1) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($address['full_name']) ?> —
                                    <?= htmlspecialchars($address['address_line']) ?>,
                                    <?= htmlspecialchars($address['city']) ?>,
                                    <?= htmlspecialchars($address['postal_code']) ?>,
                                    <?= htmlspecialchars($address['country']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="checkout-actions">
                        <button type="submit" class="auth-button">Place order</button>
                    </div>
                </form>
            <?php elseif (empty($addresses)): ?>
                <p>You need a saved address before checkout.</p>
                <p><a href="/addresses">Add an address</a></p>
            <?php endif; ?>
        </div>

    </div>
</section>