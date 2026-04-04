<section class="auth-page">
    <div class="auth-card" style="max-width: 1200px;">
        <h1>Products</h1>
        <p class="auth-subtitle">Manage catalog products.</p>

        <p style="margin-bottom: 1rem;">
            <a href="/admin/products/create">Create new product</a>
        </p>

        <?php if ($products === []): ?>
            <p>No products found.</p>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding: 0.75rem;">ID</th>
                            <th style="text-align:left; padding: 0.75rem;">Name</th>
                            <th style="text-align:left; padding: 0.75rem;">Brand</th>
                            <th style="text-align:left; padding: 0.75rem;">Type</th>
                            <th style="text-align:left; padding: 0.75rem;">Gender</th>
                            <th style="text-align:left; padding: 0.75rem;">Variants</th>
                            <th style="text-align:left; padding: 0.75rem;">Stock</th>
                            <th style="text-align:left; padding: 0.75rem;">Price range</th>
                            <th style="text-align:left; padding: 0.75rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td style="padding: 0.75rem;"><?= (int) $product['id'] ?></td>
                                <td style="padding: 0.75rem;">
                                    <?= htmlspecialchars($product['name']) ?><br>
                                    <small><?= htmlspecialchars($product['slug']) ?></small>
                                </td>
                                <td style="padding: 0.75rem;"><?= htmlspecialchars($product['brand_name']) ?></td>
                                <td style="padding: 0.75rem;"><?= htmlspecialchars((string) ($product['fragrance_type_name'] ?? '')) ?></td>
                                <td style="padding: 0.75rem;"><?= htmlspecialchars($product['gender']) ?></td>
                                <td style="padding: 0.75rem;"><?= (int) $product['variant_count'] ?></td>
                                <td style="padding: 0.75rem;"><?= (int) $product['total_stock'] ?></td>
                                <td style="padding: 0.75rem;">
                                    €<?= number_format((float) $product['min_price'], 2) ?>
                                    —
                                    €<?= number_format((float) $product['max_price'], 2) ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <a href="/admin/products/<?= (int) $product['id'] ?>/edit">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>