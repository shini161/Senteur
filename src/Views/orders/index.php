<?php
// Paginated order history for the signed-in user, with optional status filters.
?>
<section class="orders-page">
    <div class="orders-container">
        <h1>My Orders</h1>

        <div class="orders-filters">
            <a href="/orders" class="<?= $status === null ? 'active' : '' ?>">All</a>
            <a href="/orders?status=pending" class="<?= $status === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="/orders?status=processing" class="<?= $status === 'processing' ? 'active' : '' ?>">Processing</a>
            <a href="/orders?status=shipped" class="<?= $status === 'shipped' ? 'active' : '' ?>">Shipped</a>
            <a href="/orders?status=delivered" class="<?= $status === 'delivered' ? 'active' : '' ?>">Delivered</a>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <p>No orders found.</p>
                <a href="/products" class="auth-button">Continue shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <a href="/orders/<?= htmlspecialchars($order['public_id']) ?>" class="order-card">
                        <div class="order-top">
                            <div class="order-meta">
                                <strong>Order #<?= htmlspecialchars($order['public_id']) ?></strong>
                                <p class="order-date">
                                    <?= date('d M Y', strtotime($order['created_at'])) ?>
                                </p>
                            </div>

                            <span class="order-status status-<?= htmlspecialchars($order['status']) ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>

                        <div class="order-bottom">
                            <span><?= (int) $order['items_count'] ?> item<?= (int) $order['items_count'] === 1 ? '' : 's' ?></span>
                            <strong>€<?= number_format((float) $order['total_amount'], 2) ?></strong>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (($totalPages ?? 1) > 1): ?>
                <?php // Preserve the selected status filter while paging. ?>
                <div class="orders-pagination">
                    <?php
                    $baseQuery = [];
                    if ($status !== null) {
                        $baseQuery['status'] = $status;
                    }
                    ?>

                    <?php if ($page > 1): ?>
                        <?php $prevQuery = http_build_query(array_merge($baseQuery, ['page' => $page - 1])); ?>
                        <a href="/orders?<?= htmlspecialchars($prevQuery) ?>" class="button-secondary">Previous</a>
                    <?php else: ?>
                        <span class="pagination-disabled button-secondary">Previous</span>
                    <?php endif; ?>

                    <span class="orders-pagination-info">
                        Page <?= (int) $page ?> of <?= (int) $totalPages ?>
                    </span>

                    <?php if ($page < $totalPages): ?>
                        <?php $nextQuery = http_build_query(array_merge($baseQuery, ['page' => $page + 1])); ?>
                        <a href="/orders?<?= htmlspecialchars($nextQuery) ?>" class="button-secondary">Next</a>
                    <?php else: ?>
                        <span class="pagination-disabled button-secondary">Next</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
