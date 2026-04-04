<section class="auth-page">
    <div class="auth-card" style="max-width: 1100px;">
        <h1>Orders</h1>
        <p class="auth-subtitle">Manage customer orders.</p>

        <form method="POST" action="/admin/logout" style="margin-bottom: 1rem;">
            <?= \App\Core\Csrf::input() ?>
            <button type="submit" class="auth-button">Logout</button>
        </form>

        <?php if ($orders === []): ?>
            <p>No orders found.</p>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding: 0.75rem;">Order</th>
                            <th style="text-align:left; padding: 0.75rem;">Customer</th>
                            <th style="text-align:left; padding: 0.75rem;">Status</th>
                            <th style="text-align:left; padding: 0.75rem;">Items</th>
                            <th style="text-align:left; padding: 0.75rem;">Total</th>
                            <th style="text-align:left; padding: 0.75rem;">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td style="padding: 0.75rem;">
                                    <a href="/admin/orders/<?= urlencode($order['public_id']) ?>">
                                        <?= htmlspecialchars($order['public_id']) ?>
                                    </a>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <?= htmlspecialchars($order['username']) ?><br>
                                    <small><?= htmlspecialchars($order['user_email']) ?></small>
                                </td>
                                <td style="padding: 0.75rem; text-transform: capitalize;">
                                    <?= htmlspecialchars($order['status']) ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <?= (int) $order['items_count'] ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    €<?= number_format((float) $order['total_amount'], 2) ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <?= htmlspecialchars($order['created_at']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>