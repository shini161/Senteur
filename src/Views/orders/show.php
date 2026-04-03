<h1>Order #<?= htmlspecialchars($order['public_id']) ?></h1>

<p>Status: <?= htmlspecialchars(ucfirst($order['status'])) ?></p>
<p>Placed on: <?= htmlspecialchars($order['created_at']) ?></p>

<?php if (!empty($order['paid_at'])): ?>
    <p>Paid at: <?= htmlspecialchars($order['paid_at']) ?></p>
<?php endif; ?>

<?php if (!empty($order['shipped_at'])): ?>
    <p>Shipped at: <?= htmlspecialchars($order['shipped_at']) ?></p>
<?php endif; ?>

<?php if (!empty($order['delivered_at'])): ?>
    <p>Delivered at: <?= htmlspecialchars($order['delivered_at']) ?></p>
<?php endif; ?>

<hr>

<h2>Shipping Address</h2>

<?php if (!empty($order['full_name']) || !empty($order['address_line'])): ?>
    <p>
        <?= htmlspecialchars($order['full_name'] ?? '') ?><br>
        <?= htmlspecialchars($order['address_line'] ?? '') ?><br>
        <?= htmlspecialchars($order['postal_code'] ?? '') ?>
        <?= htmlspecialchars($order['city'] ?? '') ?><br>
        <?= htmlspecialchars($order['country'] ?? '') ?>
    </p>
<?php else: ?>
    <p>Shipping address unavailable.</p>
<?php endif; ?>

<hr>

<h2>Items</h2>

<?php if (empty($order['items'])): ?>
    <p>No items found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name_snapshot']) ?></td>
                    <td><?= (int) $item['size_ml_snapshot'] ?> ml</td>
                    <td>€<?= number_format((float) $item['price_at_purchase'], 2) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td>€<?= number_format((float) $item['line_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr>

<h2>Summary</h2>
<p>Subtotal: €<?= number_format((float) $order['subtotal_amount'], 2) ?></p>
<p>Shipping: €<?= number_format((float) $order['shipping_cost'], 2) ?></p>
<p>Total: €<?= number_format((float) $order['total_amount'], 2) ?></p>

<p><a href="/orders">← Back to orders</a></p>