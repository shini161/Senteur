<h1><?= htmlspecialchars($product['name']) ?></h1>

<?php if (! empty($product['description'])): ?>
    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
<?php endif; ?>

<h2>Variants</h2>

<?php if (empty($product['variants'])): ?>
    <p>No variants available.</p>
<?php else: ?>
    <ul>
        <?php foreach ($product['variants'] as $variant): ?>
            <li>
                <?= htmlspecialchars((string) $variant['size_ml']) ?>ml -
                <?= number_format((float) $variant['price'], 2) ?>€ -
                Stock: <?= htmlspecialchars((string) $variant['stock']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>