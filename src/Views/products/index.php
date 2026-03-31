<h1>Products</h1>

<?php if (empty($products)): ?>
    <p>No products available.</p>
<?php else: ?>
    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <strong><?= htmlspecialchars($product['name']) ?></strong>
                - <?= number_format($product['price'], 2) ?>€
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>