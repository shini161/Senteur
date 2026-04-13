<?php
// Admin order listing focused on quick operational scanning and recent order
// health rather than raw tabular output.

$statusClasses = [
    'pending' => 'status-pending',
    'processing' => 'status-processing',
    'shipped' => 'status-shipped',
    'delivered' => 'status-delivered',
    'cancelled' => 'status-cancelled',
];

$formatDateTime = static function (?string $value): string {
    if ($value === null || trim($value) === '') {
        return 'Not recorded';
    }

    $timestamp = strtotime($value);

    return $timestamp === false
        ? $value
        : date('d M Y, H:i', $timestamp);
};

$formatDayName = static function (?string $value): string {
    if ($value === null || trim($value) === '') {
        return '';
    }

    $timestamp = strtotime($value);

    return $timestamp === false
        ? ''
        : date('l', $timestamp);
};

$statusLabel = static function (string $value): string {
    return ucfirst($value);
};

$summary = [
    'orders' => count($orders),
    'pending' => 0,
    'processing' => 0,
    'shipped' => 0,
    'delivered' => 0,
    'cancelled' => 0,
    'units' => 0,
    'revenue' => 0.0,
];

foreach ($orders as $adminOrder) {
    $status = (string) ($adminOrder['status'] ?? 'pending');

    if (array_key_exists($status, $summary)) {
        $summary[$status]++;
    }

    $summary['units'] += (int) ($adminOrder['items_count'] ?? 0);

    if ($status !== 'cancelled') {
        $summary['revenue'] += (float) ($adminOrder['total_amount'] ?? 0);
    }
}

$needsAttention = $summary['pending'] + $summary['processing'];
?>
<section class="admin-orders-page">
    <div class="admin-orders-shell">
        <div class="card admin-orders-hero">
            <div class="admin-orders-hero-copy">
                <p class="section-kicker">Admin</p>
                <h1>Orders</h1>
                <p class="lead admin-orders-lead">
                    Monitor customer purchases, fulfillment progress, and order value without digging through raw rows.
                </p>
            </div>

            <div class="admin-orders-actions">
                <a href="/admin/products" class="button-secondary">Manage products</a>

                <form method="POST" action="/admin/logout" class="admin-orders-logout-form">
                    <?= \App\Core\Csrf::input() ?>
                    <button type="submit" class="button-secondary">Logout</button>
                </form>
            </div>
        </div>

        <div class="admin-orders-stats">
            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Total orders</span>
                <strong><?= number_format($summary['orders']) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['units']) ?> units across the full queue
                </span>
            </div>

            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Needs attention</span>
                <strong><?= number_format($needsAttention) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['pending']) ?> pending, <?= number_format($summary['processing']) ?> processing
                </span>
            </div>

            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Delivered</span>
                <strong><?= number_format($summary['delivered']) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['shipped']) ?> still in transit
                </span>
            </div>

            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Order value</span>
                <strong>€<?= number_format($summary['revenue'], 2) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['cancelled']) ?> cancelled orders excluded
                </span>
            </div>
        </div>

        <?php if ($orders === []): ?>
            <div class="empty-state admin-orders-empty">
                <h2>No orders yet</h2>
                <p>New customer purchases will appear here once checkout activity starts flowing in.</p>
            </div>
        <?php else: ?>
            <div class="card admin-orders-table-card">
                <div class="admin-orders-table-header">
                    <div>
                        <h2>Latest orders</h2>
                        <p class="muted">Newest purchases across the storefront, sorted by creation time.</p>
                    </div>

                    <span class="badge">
                        <?= number_format($summary['orders']) ?> total
                    </span>
                </div>

                <div class="admin-table-wrap admin-orders-table-wrap">
                    <table class="admin-table admin-orders-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $adminOrder): ?>
                                <?php
                                $status = (string) ($adminOrder['status'] ?? 'pending');
                                $statusClass = $statusClasses[$status] ?? 'status-pending';
                                $itemsCount = (int) ($adminOrder['items_count'] ?? 0);
                                ?>
                                <tr class="admin-orders-row">
                                    <td data-label="Order">
                                        <a
                                            href="/admin/orders/<?= urlencode((string) $adminOrder['public_id']) ?>"
                                            class="admin-order-id">
                                            <?= htmlspecialchars((string) $adminOrder['public_id']) ?>
                                        </a>
                                        <span class="admin-order-submeta">
                                            Reference for fulfillment and support follow-up
                                        </span>
                                    </td>
                                    <td data-label="Customer">
                                        <div class="admin-customer-name">
                                            <?= htmlspecialchars((string) $adminOrder['username']) ?>
                                        </div>
                                        <a
                                            href="mailto:<?= htmlspecialchars((string) $adminOrder['user_email']) ?>"
                                            class="admin-customer-email">
                                            <?= htmlspecialchars((string) $adminOrder['user_email']) ?>
                                        </a>
                                    </td>
                                    <td data-label="Status">
                                        <span class="order-status <?= $statusClass ?>">
                                            <?= htmlspecialchars($statusLabel($status)) ?>
                                        </span>
                                    </td>
                                    <td data-label="Items">
                                        <div class="admin-orders-cell-strong"><?= $itemsCount ?></div>
                                        <span class="admin-order-submeta">
                                            <?= $itemsCount === 1 ? 'unit' : 'units' ?>
                                        </span>
                                    </td>
                                    <td data-label="Total">
                                        <div class="admin-orders-cell-strong">
                                            €<?= number_format((float) $adminOrder['total_amount'], 2) ?>
                                        </div>
                                        <span class="admin-order-submeta">
                                            <?= $status === 'cancelled' ? 'Excluded from value totals' : 'Snapshot total' ?>
                                        </span>
                                    </td>
                                    <td data-label="Created">
                                        <div class="admin-orders-cell-strong">
                                            <?= htmlspecialchars($formatDateTime((string) $adminOrder['created_at'])) ?>
                                        </div>
                                        <?php $createdDay = $formatDayName((string) $adminOrder['created_at']); ?>
                                        <?php if ($createdDay !== ''): ?>
                                            <span class="admin-order-submeta">
                                                <?= htmlspecialchars($createdDay) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Action">
                                        <a
                                            href="/admin/orders/<?= urlencode((string) $adminOrder['public_id']) ?>"
                                            class="button-link admin-orders-open-link">
                                            Open order
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
