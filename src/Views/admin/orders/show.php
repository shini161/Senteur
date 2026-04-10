<?php
// Admin order detail page with lifecycle timestamps, shipping info, and status
// controls used to manage the fulfillment flow.
?>
<section class="auth-page">
    <div class="auth-card" style="max-width: 1100px;">
        <h1>Order <?= htmlspecialchars($order['public_id']) ?></h1>
        <p class="auth-subtitle">Admin order detail.</p>

        <p>
            <a href="/admin/orders">← Back to orders</a>
        </p>

        <?php if (! empty($error)): ?>
            <p class="auth-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div style="margin: 1rem 0;">
            <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($order['username']) ?> (<?= htmlspecialchars($order['user_email']) ?>)</p>
            <p><strong>Total:</strong> €<?= number_format((float) $order['total_amount'], 2) ?></p>
            <p><strong>Created:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
            <p><strong>Paid at:</strong> <?= htmlspecialchars((string) ($order['paid_at'] ?? '')) ?></p>
            <p><strong>Shipped at:</strong> <?= htmlspecialchars((string) ($order['shipped_at'] ?? '')) ?></p>
            <p><strong>Delivered at:</strong> <?= htmlspecialchars((string) ($order['delivered_at'] ?? '')) ?></p>
        </div>

        <div style="margin: 1rem 0;">
            <h2>Shipping address</h2>
            <p><?= htmlspecialchars($order['full_name']) ?></p>
            <p><?= htmlspecialchars($order['address_line']) ?></p>
            <p><?= htmlspecialchars($order['postal_code']) ?> <?= htmlspecialchars($order['city']) ?></p>
            <p><?= htmlspecialchars($order['country']) ?></p>
        </div>

        <div style="margin: 1rem 0;">
            <?php // Status changes are intentionally handled inline on the detail page. ?>
            <h2>Update status</h2>
            <form method="POST" action="/admin/orders/<?= urlencode($order['public_id']) ?>/status">
                <?= \App\Core\Csrf::input() ?>

                <select name="status" required>
                    <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                        <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                            <?= ucfirst($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="auth-button">Update</button>
            </form>
        </div>

        <div style="margin: 1rem 0;">
            <h2>Items</h2>

            <?php foreach ($order['items'] as $item): ?>
                <div style="padding: 0.75rem 0; border-bottom: 1px solid #ddd;">
                    <p><strong><?= htmlspecialchars((string) $item['product_name_snapshot']) ?></strong></p>

                    <?php if (!empty($item['brand_name_snapshot'])): ?>
                        <p><?= htmlspecialchars((string) $item['brand_name_snapshot']) ?></p>
                    <?php endif; ?>

                    <p>
                        <?php if (!empty($item['concentration_label_snapshot'])): ?>
                            <?= htmlspecialchars((string) $item['concentration_label_snapshot']) ?> ·
                        <?php endif; ?>
                        <?= (int) $item['size_ml_snapshot'] ?>ml
                    </p>

                    <?php if (!empty($item['image_url_snapshot'])): ?>
                        <p><?= htmlspecialchars((string) $item['image_url_snapshot']) ?></p>
                    <?php endif; ?>

                    <p>Qty: <?= (int) $item['quantity'] ?></p>
                    <p>Unit: €<?= number_format((float) $item['price_at_purchase'], 2) ?></p>
                    <p>Line total: €<?= number_format((float) $item['line_total'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
