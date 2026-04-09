<section class="order-page">
    <div class="order-container">

        <div class="order-header">
            <div>
                <h1>Order #<?= htmlspecialchars($order['public_id']) ?></h1>
                <p class="order-page-date">
                    Placed on <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                </p>
            </div>

            <span class="order-status status-<?= htmlspecialchars($order['status']) ?>">
                <?= ucfirst($order['status']) ?>
            </span>
        </div>

        <div class="order-actions">
            <button type="button" class="button-secondary">Download receipt</button>
            <button type="button" class="button-secondary">Track order</button>
        </div>

        <div class="order-grid">
            <div class="order-main">
                <div class="order-card">
                    <h2>Items</h2>

                    <?php if (empty($order['items'])): ?>
                        <p class="muted">No items found.</p>
                    <?php else: ?>
                        <div class="order-items-list">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item-row">
                                    <div class="order-item-left">
                                        <div class="order-item-image-wrap">
                                            <?php if (!empty($item['image_url_snapshot'])): ?>
                                                <img
                                                    src="/<?= htmlspecialchars((string) $item['image_url_snapshot']) ?>"
                                                    alt="<?= htmlspecialchars((string) $item['product_name_snapshot']) ?>"
                                                    class="order-item-image">
                                            <?php else: ?>
                                                <div class="order-item-image-placeholder">SENTEUR</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="order-item-info">
                                            <?php if (!empty($item['brand_name_snapshot'])): ?>
                                                <div class="order-item-brand">
                                                    <?= htmlspecialchars((string) $item['brand_name_snapshot']) ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="order-item-name">
                                                <?= htmlspecialchars((string) $item['product_name_snapshot']) ?>
                                            </div>

                                            <div class="order-item-meta muted">
                                                <?php if (!empty($item['concentration_label_snapshot'])): ?>
                                                    <span><?= htmlspecialchars((string) $item['concentration_label_snapshot']) ?></span>
                                                    <span class="cart-meta-separator">·</span>
                                                <?php endif; ?>

                                                <span><?= (int) $item['size_ml_snapshot'] ?> ml</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-item-right">
                                        <div class="order-item-qty muted">
                                            Qty <?= (int) $item['quantity'] ?>
                                        </div>
                                        <div class="order-item-unit muted">
                                            €<?= number_format((float) $item['price_at_purchase'], 2) ?> each
                                        </div>
                                        <div class="order-item-total">
                                            €<?= number_format((float) $item['line_total'], 2) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="order-sidebar">
                <div class="order-card">
                    <h2>Summary</h2>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>€<?= number_format((float) $order['subtotal_amount'], 2) ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>€<?= number_format((float) $order['shipping_cost'], 2) ?></span>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span>€<?= number_format((float) $order['total_amount'], 2) ?></span>
                    </div>
                </div>

                <div class="order-card">
                    <h2>Shipping</h2>

                    <?php if (!empty($order['full_name']) || !empty($order['address_line'])): ?>
                        <p class="order-shipping-address">
                            <?= htmlspecialchars($order['full_name'] ?? '') ?><br>
                            <?= htmlspecialchars($order['address_line'] ?? '') ?><br>
                            <?= htmlspecialchars($order['postal_code'] ?? '') ?>
                            <?= htmlspecialchars($order['city'] ?? '') ?><br>
                            <?= htmlspecialchars($order['country'] ?? '') ?>
                        </p>
                    <?php else: ?>
                        <p class="muted">Shipping address unavailable.</p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <a href="/orders" class="back-link">← Back to orders</a>
    </div>
</section>