<h1>Products</h1>

<?php if (empty($products)): ?>
    <p>No products available.</p>
<?php else: ?>
    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <a href="/product/<?= htmlspecialchars((string) $product['id']) ?>">
                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                </a>
                - <?= number_format($product['price'], 2) ?>€
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>