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
    <div class="section-heading">
        <p class="section-kicker">Featured</p>
        <h2 style="margin-top: 0;">Featured products</h2>
    </div>

    <?php if (empty($featuredProducts)): ?>
        <div class="empty-state">
            <p>No featured products available yet.</p>
        </div>
    <?php else: ?>
        <section class="products-grid">
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

                        <div class="product-card-body">
                            <div class="product-meta-line" style="margin-bottom: 0.7rem;">
                                <span class="badge"><?= htmlspecialchars($product['brand_name']) ?></span>
                                <?php if (!empty($product['fragrance_type_name'])): ?>
                                    <span class="badge"><?= htmlspecialchars($product['fragrance_type_name']) ?></span>
                                <?php endif; ?>
                            </div>

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
</section>

<section style="margin-bottom: 2rem;">
    <div class="section-heading">
        <p class="section-kicker">Explore</p>
        <h2 style="margin-top: 0;">Shop by style</h2>
    </div>

    <div class="products-grid" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
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
    <div class="section-heading" style="margin-bottom: 1rem;">
        <p class="section-kicker">Why Senteur</p>
        <h2 style="margin-top: 0;">A cleaner fragrance shopping experience</h2>
    </div>

    <div style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">
        <div>
            <h2 style="margin-top: 0;">Curated catalogue</h2>
            <p class="muted">Structured product pages with variants, notes, and richer perfume details.</p>
        </div>

        <div>
            <h2 style="margin-top: 0;">Secure checkout</h2>
            <p class="muted">Stripe checkout flow with payment confirmation and order tracking.</p>
        </div>

        <div>
            <h2 style="margin-top: 0;">Admin operations</h2>
            <p class="muted">Internal order and product management built into the same platform.</p>
        </div>
    </div>
</section>