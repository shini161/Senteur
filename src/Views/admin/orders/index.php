<?php
$scripts ??= [];
$scripts[] = '/assets/js/admin/filters.js';

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
$activeFilterCount = (($filters['q'] ?? '') !== '' ? 1 : 0) + (($filters['status'] ?? '') !== '' ? 1 : 0);
$filtersOpen = $activeFilterCount > 0;

$buildPageUrl = static function (int $pageNumber) use ($filters): string {
    $params = $filters;
    $params['page'] = $pageNumber;

    return '/admin/orders?' . http_build_query(array_filter(
        $params,
        static fn ($value) => $value !== '' && $value !== null
    ));
};
$hasActiveFilters = ($filters['q'] ?? '') !== '' || ($filters['status'] ?? '') !== '';
?>
<section class="admin-orders-page">
    <div class="admin-orders-shell">
        <?php
        $adminHeaderTitle = 'Orders';
        $adminHeaderLead = 'Monitor customer purchases, fulfillment progress, and order value without digging through raw rows.';
        $adminHeaderSection = 'orders';
        $adminHeaderClass = 'admin-orders-hero';
        $adminHeaderActions = [];

        require __DIR__ . '/../_header.php';
        ?>

        <section class="panel admin-filter-panel" data-filter-panel>
            <div class="admin-filter-header">
                <div>
                    <h2>Search</h2>
                    <p class="muted admin-filter-summary">
                        <?= number_format((int) $totalOrders) ?> matching orders
                        <?php if ($activeFilterCount > 0): ?>
                            · <?= number_format($activeFilterCount) ?> active
                        <?php endif; ?>
                    </p>
                </div>

                <button
                    type="button"
                    class="button-secondary admin-filter-toggle filter-toggle-button filter-toggle-button-icon-only"
                    data-filter-toggle
                    aria-expanded="<?= $filtersOpen ? 'true' : 'false' ?>"
                    aria-label="Toggle order filters"
                    title="Toggle order filters">
                    <span class="filter-toggle-icon" aria-hidden="true"></span>
                    <span class="sr-only">Toggle order filters</span>
                </button>
            </div>

            <form
                method="GET"
                action="/admin/orders"
                class="auth-form admin-filter-form admin-filter-body <?= $filtersOpen ? 'is-open' : '' ?>"
                data-filter-body>
                <div class="admin-filter-grid">
                    <div class="form-group admin-filter-search">
                        <label for="order-q">Search</label>
                        <input
                            id="order-q"
                            type="text"
                            name="q"
                            placeholder="Order ID, customer name, email, or reference"
                            value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>">
                    </div>

                    <div class="form-group">
                        <label for="order-status">Status</label>
                        <select id="order-status" name="status">
                            <option value="">All statuses</option>
                            <?php foreach (array_keys($statusClasses) as $statusOption): ?>
                                <option
                                    value="<?= htmlspecialchars($statusOption) ?>"
                                    <?= (string) ($filters['status'] ?? '') === $statusOption ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($statusLabel($statusOption)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="admin-filter-actions">
                    <button type="submit" class="auth-button">Apply filters</button>
                    <a href="/admin/orders" class="button-secondary">Reset</a>
                    <span class="muted admin-results-count"><?= number_format((int) $totalOrders) ?> matching orders</span>
                </div>
            </form>
        </section>

        <div class="admin-orders-stats">
            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Matching orders</span>
                <strong><?= number_format((int) $totalOrders) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['orders']) ?> shown on this page
                </span>
            </div>

            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Needs attention</span>
                <strong><?= number_format($needsAttention) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['pending']) ?> pending, <?= number_format($summary['processing']) ?> processing on this page
                </span>
            </div>

            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Delivered</span>
                <strong><?= number_format($summary['delivered']) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['shipped']) ?> still in transit on this page
                </span>
            </div>

            <div class="card admin-orders-stat">
                <span class="admin-orders-stat-label">Order value</span>
                <strong>€<?= number_format($summary['revenue'], 2) ?></strong>
                <span class="admin-orders-stat-note">
                    <?= number_format($summary['cancelled']) ?> cancelled orders excluded on this page
                </span>
            </div>
        </div>

        <?php if ($orders === []): ?>
            <div class="empty-state admin-orders-empty">
                <h2><?= $hasActiveFilters ? 'No matching orders' : 'No orders yet' ?></h2>
                <p>
                    <?= $hasActiveFilters
                        ? 'Try a broader search or clear the current filters to bring more orders back into view.'
                        : 'New customer purchases will appear here once checkout activity starts flowing in.' ?>
                </p>
            </div>
        <?php else: ?>
            <div class="card admin-orders-table-card">
                <div class="admin-orders-table-header">
                    <div>
                        <h2>Latest orders</h2>
                        <p class="muted">Newest purchases across the storefront, sorted by creation time.</p>
                    </div>

                    <span class="badge">
                        Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?>
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

            <?php if (($totalPages ?? 1) > 1): ?>
                <nav class="admin-pagination">
                    <?php if (($currentPage ?? 1) > 1): ?>
                        <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" class="button-secondary">Previous</a>
                    <?php else: ?>
                        <span class="button-secondary pagination-disabled">Previous</span>
                    <?php endif; ?>

                    <span class="muted admin-results-count">
                        Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?>
                    </span>

                    <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
                        <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" class="button-secondary">Next</a>
                    <?php else: ?>
                        <span class="button-secondary pagination-disabled">Next</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
