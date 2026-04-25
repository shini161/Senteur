<section class="home-hero panel">
    <p class="section-kicker">Senteur</p>
    <h1>Discover your next fragrance</h1>
    <p class="lead home-hero-lead">
        Explore a curated perfume catalogue with elegant product pages,
        structured scent profiles, secure checkout, and a clean buying flow.
    </p>

    <div class="home-hero-actions">
        <a href="/products" class="auth-button">Shop now</a>
        <a href="/products?sort=price_desc" class="button-secondary">Premium picks</a>
    </div>
</section>

<section class="home-section">
    <div class="section-heading">
        <p class="section-kicker">Featured</p>
        <h2 class="home-section-title">Featured products</h2>
    </div>

    <?php if (empty($featuredProducts)): ?>
        <div class="empty-state">
            <p>No featured products available yet.</p>
        </div>
    <?php else: ?>
        <section class="home-featured-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <article class="product-card">
                    <a href="/products/<?= htmlspecialchars($product['slug']) ?>" class="product-card-link">
                        <div class="product-card-media">
                            <?php if (!empty($product['image_url'])): ?>
                                <img
                                    src="/<?= htmlspecialchars($product['image_url']) ?>"
                                    alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                <div class="product-placeholder">SENTEUR</div>
                            <?php endif; ?>
                        </div>

                        <div class="product-card-body product-card-body-featured">
                            <div class="product-card-brand">
                                <?= htmlspecialchars($product['brand_name']) ?>
                            </div>

                            <h2 class="product-card-title product-card-title-featured">
                                <?= htmlspecialchars($product['name']) ?>
                            </h2>

                            <?php if (!empty($product['concentration_label'])): ?>
                                <div class="product-card-subtitle product-card-subtitle-featured">
                                    <?= htmlspecialchars($product['concentration_label']) ?>
                                </div>
                            <?php endif; ?>

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
</section>

<section class="home-section">
    <div class="section-heading">
        <p class="section-kicker">Explore</p>
        <h2 class="home-section-title">Shop by style</h2>
    </div>

    <div class="home-collections-grid">
        <?php foreach ($collections as $collection): ?>
            <article class="panel">
                <h2 class="home-section-title"><?= htmlspecialchars($collection['title']) ?></h2>
                <p class="muted"><?= htmlspecialchars($collection['description']) ?></p>
                <a href="/products<?= htmlspecialchars($collection['query']) ?>" class="button-link">Browse selection</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="panel home-value-panel">
    <div class="section-heading home-value-heading">
        <p class="section-kicker">Why Senteur</p>
        <h2 class="home-section-title">Fragrance shopping, made more intuitive</h2>
    </div>

    <div class="home-why-grid">
        <div>
            <h2 class="home-section-title">Clear product discovery</h2>
            <p class="muted">Browse by brand, concentration, notes, and style without getting lost in clutter.</p>
        </div>

        <div>
            <h2 class="home-section-title">Meaningful perfume details</h2>
            <p class="muted">See concentrations, variants, scent profiles, and related fragrances in a cleaner format.</p>
        </div>

        <div>
            <h2 class="home-section-title">Smooth buying flow</h2>
            <p class="muted">From discovery to checkout, everything is designed to stay simple, fast, and elegant.</p>
        </div>
    </div>
</section>
