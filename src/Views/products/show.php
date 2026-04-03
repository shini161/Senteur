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

                <form action="/cart/add" method="POST" style="display:inline-block; margin-left: 12px;">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="variant_id" value="<?= htmlspecialchars((string) $variant['id']) ?>">
                    <input type="number" name="quantity" value="1" min="1" style="width: 60px;">
                    <button type="submit">Add to cart</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>