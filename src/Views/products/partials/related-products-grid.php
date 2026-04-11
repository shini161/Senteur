<div class="panel">
    <?php // Related product panels reuse the same card grid markup. ?>
    <h2><?= htmlspecialchars($relatedTitle) ?></h2>

    <div class="related-products-grid">
        <?php foreach ($relatedProducts as $related): ?>
            <a href="/products/<?= htmlspecialchars($related['slug']) ?>" class="related-product-card">
                <div class="related-product-media">
                    <?php if (!empty($related['image_url'])): ?>
                        <img src="/<?= htmlspecialchars($related['image_url']) ?>" alt="<?= htmlspecialchars($related['name']) ?>">
                    <?php else: ?>
                        <div class="product-placeholder">SENTEUR</div>
                    <?php endif; ?>
                </div>

                <div class="related-product-body">
                    <div class="related-product-brand">
                        <?= htmlspecialchars($related['brand_name']) ?>
                    </div>

                    <div class="related-product-name">
                        <?= htmlspecialchars($related['name']) ?>
                    </div>

                    <?php if (!empty($related['concentration_label'])): ?>
                        <div class="related-product-subtitle">
                            <?= htmlspecialchars($related['concentration_label']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="related-product-price">
                        From €<?= number_format((float) $related['price'], 2) ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
