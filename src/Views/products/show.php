<section class="product-show">
    <div class="product-show-gallery">
        <div class="product-show-image-wrap">
            <?php if (!empty($product['image_url'])): ?>
                <img
                    src="/<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <div class="product-placeholder">SENTEUR</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-show-panel">
        <div class="product-meta-line">
            <span class="badge">Perfume</span>
            <span class="badge"><?= count($product['variants'] ?? []) ?> variants</span>
        </div>

        <h1><?= htmlspecialchars($product['name']) ?></h1>

        <?php if (! empty($product['description'])): ?>
            <p class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>
        <?php else: ?>
            <p class="product-description">No description available yet.</p>
        <?php endif; ?>

        <h2>Available variants</h2>

        <?php if (empty($product['variants'])): ?>
            <div class="empty-state">
                <p>No variants available.</p>
            </div>
        <?php else: ?>
            <div class="variant-list">
                <?php foreach ($product['variants'] as $variant): ?>
                    <div class="variant-card">
                        <div class="variant-main">
                            <div class="variant-title">
                                <?= htmlspecialchars((string) $variant['size_ml']) ?>ml
                            </div>
                            <div class="variant-meta">
                                €<?= number_format((float) $variant['price'], 2) ?>
                                ·
                                Stock: <?= htmlspecialchars((string) $variant['stock']) ?>
                            </div>
                        </div>

                        <form action="/cart/add" method="POST" class="variant-form">
                            <?= \App\Core\Csrf::input() ?>
                            <input type="hidden" name="variant_id" value="<?= htmlspecialchars((string) $variant['id']) ?>">

                            <input
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                class="qty-input">

                            <button type="submit" class="auth-button">Add to cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>