<section class="products-hero">
    <p class="section-kicker">Catalogue</p>
    <h1>Discover perfumes worth wearing</h1>
    <p class="lead">
        Explore curated fragrances with clean variant pricing and availability.
    </p>
</section>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <p>No products available right now.</p>
    </div>
<?php else: ?>
    <section class="products-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <a href="/product/<?= htmlspecialchars((string) $product['id']) ?>" class="product-card-link">
                    <div class="product-card-media">
                        <?php if (!empty($product['image_url'])): ?>
                            <img
                                src="/<?= htmlspecialchars($product['image_url']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div class="product-placeholder">SENTEUR</div>
                        <?php endif; ?>
                    </div>

                    <div class="product-card-body">
                        <h2 class="product-card-title"><?= htmlspecialchars($product['name']) ?></h2>

                        <div class="product-card-meta">
                            <span class="product-card-price">
                                €<?= number_format((float) $product['price'], 2) ?>
                            </span>

                            <span class="product-card-status">
                                <?= !empty($product['is_sellable']) ? 'In stock' : 'Unavailable' ?>
                            </span>
                        </div>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>