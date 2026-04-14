<?php
// Admin order detail page with fulfillment controls, customer context, and
// item snapshots presented in an operational layout.

$statusClasses = [
    'pending' => 'status-pending',
    'processing' => 'status-processing',
    'shipped' => 'status-shipped',
    'delivered' => 'status-delivered',
    'cancelled' => 'status-cancelled',
];

$formatDateTime = static function (?string $value): string {
    if ($value === null || trim($value) === '') {
        return 'Not recorded yet';
    }

    $timestamp = strtotime($value);

    return $timestamp === false
        ? $value
        : date('d M Y, H:i', $timestamp);
};

$statusLabel = static function (string $value): string {
    return ucfirst($value);
};

$currentStatus = (string) ($order['status'] ?? 'pending');
$statusClass = $statusClasses[$currentStatus] ?? 'status-pending';
$lineItemCount = count($order['items']);
$unitCount = 0;

foreach ($order['items'] as $item) {
    $unitCount += (int) ($item['quantity'] ?? 0);
}

$shippingLines = array_values(array_filter([
    (string) ($order['full_name'] ?? ''),
    (string) ($order['address_line'] ?? ''),
    trim(sprintf(
        '%s %s',
        (string) ($order['postal_code'] ?? ''),
        (string) ($order['city'] ?? '')
    )),
    (string) ($order['country'] ?? ''),
], static function (string $line): bool {
    return trim($line) !== '';
}));

$destinationSummary = trim(implode(', ', array_filter([
    (string) ($order['city'] ?? ''),
    (string) ($order['country'] ?? ''),
], static function (string $value): bool {
    return trim($value) !== '';
})));

if ($destinationSummary === '') {
    $destinationSummary = 'Address pending';
}

$shippingContact = trim((string) ($order['full_name'] ?? ''));

if ($shippingContact === '') {
    $shippingContact = 'Shipping contact unavailable';
}

$timeline = [
    [
        'label' => 'Placed',
        'value' => (string) ($order['created_at'] ?? ''),
        'complete' => ! empty($order['created_at']),
    ],
    [
        'label' => 'Paid',
        'value' => (string) ($order['paid_at'] ?? ''),
        'complete' => ! empty($order['paid_at']),
    ],
    [
        'label' => 'Shipped',
        'value' => (string) ($order['shipped_at'] ?? ''),
        'complete' => ! empty($order['shipped_at']),
    ],
    [
        'label' => 'Delivered',
        'value' => (string) ($order['delivered_at'] ?? ''),
        'complete' => ! empty($order['delivered_at']),
    ],
];
?>
<section class="admin-order-page">
    <div class="admin-order-shell">
        <?php
        $adminHeaderTitle = 'Order ' . (string) $order['public_id'];
        $adminHeaderLead = (string) $order['username']
            . ' · '
            . (string) $order['user_email']
            . ' · placed '
            . $formatDateTime((string) ($order['created_at'] ?? ''));
        $adminHeaderKicker = 'Admin / Orders';
        $adminHeaderSection = 'orders';
        $adminHeaderClass = 'admin-order-header';
        $adminHeaderActions = [
            [
                'type' => 'link',
                'href' => '/admin/orders',
                'label' => 'Back to orders',
                'class' => 'button-secondary',
            ],
            [
                'type' => 'badge',
                'label' => $statusLabel($currentStatus),
                'class' => 'order-status ' . $statusClass . ' admin-order-header-status',
            ],
        ];

        require __DIR__ . '/../_header.php';
        ?>

        <?php if (! empty($error)): ?>
            <div class="message message-error">
                <?= htmlspecialchars((string) $error) ?>
            </div>
        <?php endif; ?>

        <div class="admin-order-snapshot-grid">
            <div class="card admin-orders-stat admin-order-snapshot-card">
                <span class="admin-orders-stat-label">Order total</span>
                <strong>€<?= number_format((float) $order['total_amount'], 2) ?></strong>
                <span class="admin-orders-stat-note">
                    Subtotal €<?= number_format((float) $order['subtotal_amount'], 2) ?>
                    + shipping €<?= number_format((float) $order['shipping_cost'], 2) ?>
                </span>
            </div>

            <div class="card admin-orders-stat admin-order-snapshot-card">
                <span class="admin-orders-stat-label">Items</span>
                <strong><?= number_format($unitCount) ?> <?= $unitCount === 1 ? 'unit' : 'units' ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($lineItemCount) ?> <?= $lineItemCount === 1 ? 'line item' : 'line items' ?>
                </span>
            </div>

            <div class="card admin-orders-stat admin-order-snapshot-card">
                <span class="admin-orders-stat-label">Customer</span>
                <strong><?= htmlspecialchars((string) $order['username']) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= htmlspecialchars((string) $order['user_email']) ?>
                </span>
            </div>

            <div class="card admin-orders-stat admin-order-snapshot-card">
                <span class="admin-orders-stat-label">Destination</span>
                <strong><?= htmlspecialchars($destinationSummary) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= htmlspecialchars($shippingContact) ?>
                </span>
            </div>
        </div>

        <div class="admin-order-grid">
            <div class="admin-order-main">
                <section class="panel admin-order-panel">
                    <div class="admin-order-panel-header">
                        <div>
                            <h2>Items</h2>
                            <p class="muted">Snapshot of exactly what the customer purchased at checkout time.</p>
                        </div>

                        <span class="badge">
                            <?= number_format($lineItemCount) ?> <?= $lineItemCount === 1 ? 'line' : 'lines' ?>
                        </span>
                    </div>

                    <?php if ($order['items'] === []): ?>
                        <div class="empty-state">
                            <p>No item snapshots are available for this order.</p>
                        </div>
                    <?php else: ?>
                        <div class="admin-order-item-list">
                            <?php foreach ($order['items'] as $item): ?>
                                <article class="admin-order-item">
                                    <div class="admin-order-item-media">
                                        <?php if (! empty($item['image_url_snapshot'])): ?>
                                            <img
                                                src="/<?= htmlspecialchars((string) $item['image_url_snapshot']) ?>"
                                                alt="<?= htmlspecialchars((string) $item['product_name_snapshot']) ?>"
                                                class="admin-order-item-image">
                                        <?php else: ?>
                                            <div class="admin-order-item-placeholder">SENTEUR</div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="admin-order-item-content">
                                        <div class="admin-order-item-top">
                                            <div class="admin-order-item-copy">
                                                <?php if (! empty($item['brand_name_snapshot'])): ?>
                                                    <div class="admin-order-item-brand">
                                                        <?= htmlspecialchars((string) $item['brand_name_snapshot']) ?>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="admin-order-item-name">
                                                    <?= htmlspecialchars((string) $item['product_name_snapshot']) ?>
                                                </div>

                                                <div class="admin-order-item-meta">
                                                    <?php if (! empty($item['concentration_label_snapshot'])): ?>
                                                        <span><?= htmlspecialchars((string) $item['concentration_label_snapshot']) ?></span>
                                                        <span class="cart-meta-separator">·</span>
                                                    <?php endif; ?>

                                                    <span><?= (int) $item['size_ml_snapshot'] ?> ml</span>
                                                </div>
                                            </div>

                                            <div class="admin-order-item-pricing">
                                                <span class="admin-order-item-qty">
                                                    Qty <?= (int) $item['quantity'] ?>
                                                </span>
                                                <strong>€<?= number_format((float) $item['line_total'], 2) ?></strong>
                                                <span class="muted">
                                                    €<?= number_format((float) $item['price_at_purchase'], 2) ?> each
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <aside class="admin-order-sidebar">
                <section class="panel admin-order-panel">
                    <div class="admin-order-panel-header">
                        <div>
                            <h2>Fulfillment</h2>
                            <p class="muted">Update the order stage and keep lifecycle milestones visible.</p>
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="/admin/orders/<?= urlencode((string) $order['public_id']) ?>/status"
                        class="admin-order-status-form">
                        <?= \App\Core\Csrf::input() ?>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" required>
                                <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $currentStatus === $status ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="auth-button">Update status</button>
                    </form>

                    <p class="muted admin-order-status-help">
                        Shipping and delivery timestamps are updated automatically when the order advances.
                    </p>

                    <ul class="admin-order-timeline">
                        <?php foreach ($timeline as $event): ?>
                            <li class="admin-order-timeline-item<?= $event['complete'] ? ' is-complete' : '' ?>">
                                <span class="admin-order-timeline-marker" aria-hidden="true"></span>
                                <div>
                                    <span class="admin-order-timeline-label">
                                        <?= htmlspecialchars((string) $event['label']) ?>
                                    </span>
                                    <span class="admin-order-timeline-time">
                                        <?= htmlspecialchars($formatDateTime((string) $event['value'])) ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($currentStatus === 'cancelled'): ?>
                        <div class="message message-error admin-order-status-note">
                            This order is cancelled and should be excluded from the active fulfillment queue.
                        </div>
                    <?php endif; ?>
                </section>

                <section class="panel admin-order-panel">
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
                </section>

                <section class="panel admin-order-panel">
                    <h2>Customer</h2>

                    <div class="admin-order-contact">
                        <strong><?= htmlspecialchars((string) $order['username']) ?></strong>
                        <a href="mailto:<?= htmlspecialchars((string) $order['user_email']) ?>">
                            <?= htmlspecialchars((string) $order['user_email']) ?>
                        </a>
                    </div>

                    <div class="admin-order-detail-list">
                        <div class="admin-order-detail-row">
                            <span class="admin-order-detail-label">Order ID</span>
                            <span class="admin-order-detail-value"><?= htmlspecialchars((string) $order['public_id']) ?></span>
                        </div>

                        <div class="admin-order-detail-row">
                            <span class="admin-order-detail-label">Created</span>
                            <span class="admin-order-detail-value">
                                <?= htmlspecialchars($formatDateTime((string) ($order['created_at'] ?? ''))) ?>
                            </span>
                        </div>

                        <div class="admin-order-detail-row">
                            <span class="admin-order-detail-label">Current status</span>
                            <span class="admin-order-detail-value">
                                <?= htmlspecialchars($statusLabel($currentStatus)) ?>
                            </span>
                        </div>
                    </div>
                </section>

                <section class="panel admin-order-panel">
                    <h2>Shipping</h2>

                    <?php if ($shippingLines !== []): ?>
                        <p class="admin-order-shipping-address">
                            <?= implode('<br>', array_map(
                                static fn(string $line): string => htmlspecialchars($line, ENT_QUOTES, 'UTF-8'),
                                $shippingLines
                            )) ?>
                        </p>
                    <?php else: ?>
                        <p class="muted">Shipping address unavailable.</p>
                    <?php endif; ?>
                </section>
            </aside>
        </div>
    </div>
</section>
