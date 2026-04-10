<?php
// Home page view combining hero messaging, featured products, and curated
// collection shortcuts derived from repository-backed note filters.
?>
<section class="home-hero panel" style="padding: 2rem; margin-bottom: 1.5rem;">
    <p class="section-kicker">Senteur</p>
    <h1>Discover your next fragrance</h1>
    <p class="lead" style="max-width: 640px;">
        Explore a curated perfume catalogue with elegant product pages,
        structured scent profiles, secure checkout, and a clean buying flow.
    </p>

    <div style="display:flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem;">
        <a href="/products" class="auth-button">Shop now</a>
        <a href="/products?sort=price_desc" class="button-secondary">Explore premium picks</a>
    </div>
</section>

<section style="margin-bottom: 2rem;">
    <?php // Featured catalogue entries surfaced on the landing page. ?>
    <div class="section-heading">
        <p class="section-kicker">Featured</p>
        <h2 style="margin-top: 0;">Featured products</h2>
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

<section style="margin-bottom: 2rem;">
    <?php // Collection cards link into the catalogue with pre-filled queries. ?>
    <div class="section-heading">
        <p class="section-kicker">Explore</p>
        <h2 style="margin-top: 0;">Shop by style</h2>
    </div>

    <div class="home-collections-grid">
        <?php foreach ($collections as $collection): ?>
            <article class="panel">
                <h2 style="margin-top: 0;"><?= htmlspecialchars($collection['title']) ?></h2>
                <p class="muted"><?= htmlspecialchars($collection['description']) ?></p>
                <a href="/products<?= htmlspecialchars($collection['query']) ?>" class="button-link">Browse selection</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="panel" style="padding: 1.5rem;">
    <?php // Closing value proposition block for the storefront landing page. ?>
    <div class="section-heading" style="margin-bottom: 1rem;">
        <p class="section-kicker">Why Senteur</p>
        <h2 style="margin-top: 0;">Fragrance shopping, made more intuitive</h2>
    </div>

    <div class="home-why-grid">
        <div>
            <h2 style="margin-top: 0;">Clear product discovery</h2>
            <p class="muted">Browse by brand, concentration, notes, and style without getting lost in clutter.</p>
        </div>

        <div>
            <h2 style="margin-top: 0;">Meaningful perfume details</h2>
            <p class="muted">See concentrations, variants, scent profiles, and related fragrances in a cleaner format.</p>
        </div>

        <div>
            <h2 style="margin-top: 0;">Smooth buying flow</h2>
            <p class="muted">From discovery to checkout, everything is designed to stay simple, fast, and elegant.</p>
        </div>
    </div>
</section>
