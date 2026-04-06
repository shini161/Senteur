<h1>Cart</h1>

<?php if (empty($items)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <strong>
                    <a href="/products/<?= htmlspecialchars((string) $item['product_id']) ?>">
                        <?= htmlspecialchars($item['product_name']) ?>
                    </a>
                </strong>
                -
                <?= htmlspecialchars((string) $item['size_ml']) ?>ml
                -
                <?= number_format((float) $item['price'], 2) ?>€
                <br>

                Quantity: <?= htmlspecialchars((string) $item['quantity']) ?>
                <br>

                Subtotal: <?= number_format((float) $item['subtotal'], 2) ?>€
                <br><br>

                <form action="/cart/update" method="POST" style="display:inline-block;">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="variant_id" value="<?= htmlspecialchars((string) $item['variant_id']) ?>">
                    <input
                        type="number"
                        name="quantity"
                        min="0"
                        max="<?= htmlspecialchars((string) $item['max_quantity']) ?>"
                        value="<?= htmlspecialchars((string) $item['quantity']) ?>">
                    <button type="submit">Update</button>
                </form>

                <form action="/cart/remove" method="POST" style="display:inline-block; margin-left: 8px;">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="variant_id" value="<?= htmlspecialchars((string) $item['variant_id']) ?>">
                    <button type="submit">Remove</button>
                </form>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>

    <h2>Total: <?= number_format($total, 2) ?>€</h2>

    <p>
        <a href="/checkout">Proceed to checkout</a>
    </p>
<?php endif; ?>