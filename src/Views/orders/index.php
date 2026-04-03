<h1>My Orders</h1>

<?php if (empty($orders)): ?>
    <p>You have no orders yet.</p>
    <p><a href="/products">Continue shopping</a></p>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div>
            <h2>
                <a href="/orders/<?= htmlspecialchars($order['public_id']) ?>">
                    Order #<?= htmlspecialchars($order['public_id']) ?>
                </a>
            </h2>

            <p>Status: <?= htmlspecialchars(ucfirst($order['status'])) ?></p>
            <p>Date: <?= htmlspecialchars($order['created_at']) ?></p>
            <p>Items: <?= (int) $order['items_count'] ?></p>
            <p>Total: €<?= number_format((float) $order['total_amount'], 2) ?></p>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>