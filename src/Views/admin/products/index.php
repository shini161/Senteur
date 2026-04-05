<section class="auth-page">
    <div class="auth-card" style="max-width: 1200px;">
        <p class="section-kicker">Admin</p>
        <h1>Products</h1>
        <p class="auth-subtitle">Manage catalog products.</p>

        <p style="margin-bottom: 1rem;">
            <a href="/admin/products/create">Create new product</a>
        </p>

        <?php if ($products === []): ?>
            <div class="empty-state">
                <p>No products found.</p>
            </div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Preview</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Gender</th>
                            <th>Variants</th>
                            <th>Stock</th>
                            <th>Price range</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= (int) $product['id'] ?></td>
                                <td>
                                    <?php if (!empty($product['image_url'])): ?>
                                        <img
                                            src="/<?= htmlspecialchars($product['image_url']) ?>"
                                            alt="<?= htmlspecialchars($product['name']) ?>"
                                            style="width: 56px; height: 56px; object-fit: cover; border-radius: 10px; border: 1px solid #e5e8e2;">
                                    <?php else: ?>
                                        <div style="width: 56px; height: 56px; border-radius: 10px; border: 1px solid #e5e8e2; display:grid; place-items:center; color:#667067; font-size:0.7rem;">
                                            No image
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($product['name']) ?><br>
                                    <small><?= htmlspecialchars($product['slug']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($product['brand_name']) ?></td>
                                <td><?= htmlspecialchars((string) ($product['fragrance_type_name'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($product['gender']) ?></td>
                                <td><?= (int) $product['variant_count'] ?></td>
                                <td><?= (int) $product['total_stock'] ?></td>
                                <td>
                                    €<?= number_format((float) $product['min_price'], 2) ?>
                                    —
                                    €<?= number_format((float) $product['max_price'], 2) ?>
                                </td>
                                <td>
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