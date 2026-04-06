<section class="products-hero">
    <p class="section-kicker">Catalogue</p>
    <h1>Discover perfumes worth wearing</h1>
    <p class="lead">
        Explore curated fragrances with clean variant pricing and availability.
    </p>
</section>

<section class="panel" style="margin-bottom: 1.5rem;">
    <form method="GET" action="/products" class="auth-form">
        <div style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 0.9rem;">
            <div class="form-group">
                <label for="search">Search</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Name or description"
                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="brand_id">Brand</label>
                <select id="brand_id" name="brand_id">
                    <option value="0">All brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option
                            value="<?= (int) $brand['id'] ?>"
                            <?= (int) ($filters['brand_id'] ?? 0) === (int) $brand['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fragrance_type_id">Type</label>
                <select id="fragrance_type_id" name="fragrance_type_id">
                    <option value="0">All types</option>
                    <?php foreach ($fragranceTypes as $type): ?>
                        <option
                            value="<?= (int) $type['id'] ?>"
                            <?= (int) ($filters['fragrance_type_id'] ?? 0) === (int) $type['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">All</option>
                    <?php foreach ($genders as $gender): ?>
                        <option
                            value="<?= htmlspecialchars($gender) ?>"
                            <?= ($filters['gender'] ?? '') === $gender ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($gender)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="sort">Sort</label>
                <select id="sort" name="sort">
                    <?php foreach ($sortOptions as $value => $label): ?>
                        <option
                            value="<?= htmlspecialchars($value) ?>"
                            <?= ($filters['sort'] ?? 'newest') === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display:flex; gap: 0.75rem; margin-top: 1rem; align-items:center;">
            <button type="submit" class="auth-button">Apply filters</button>
            <a href="/products" class="button-secondary">Reset</a>
            <span class="muted"><?= (int) $totalProducts ?> result(s)</span>
        </div>
    </form>
</section>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <p>No products matched your filters.</p>
    </div>
<?php else: ?>
    <section class="products-grid">
        <?php foreach ($products as $product): ?>
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

<?php if (($totalPages ?? 1) > 1): ?>
    <?php
    $queryParams = $filters;
    unset($queryParams['page']);

    $buildPageUrl = static function (int $pageNumber) use ($queryParams): string {
        $params = array_merge($queryParams, ['page' => $pageNumber]);

        return '/products?' . http_build_query(array_filter(
            $params,
            static fn($value) => $value !== '' && $value !== 0 && $value !== '0'
        ));
    };
    ?>

    <nav class="catalog-pagination">
        <?php if (($currentPage ?? 1) > 1): ?>
            <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" class="button-secondary">Previous</a>
        <?php else: ?>
            <span class="button-secondary pagination-disabled">Previous</span>
        <?php endif; ?>

        <span class="muted">
            Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?>
        </span>

        <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
            <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" class="button-secondary">Next</a>
        <?php else: ?>
            <span class="button-secondary pagination-disabled">Next</span>
        <?php endif; ?>
    </nav>
<?php endif; ?>