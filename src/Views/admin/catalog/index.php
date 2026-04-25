<?php
$scripts ??= [];
$scripts[] = '/assets/js/admin/filters.js';

$editingBrandId = (int) ($editingBrand['id'] ?? 0);
$editingFragranceTypeId = (int) ($editingFragranceType['id'] ?? 0);
$brandForm = is_array($brandForm ?? null) ? $brandForm : [];
$fragranceTypeForm = is_array($fragranceTypeForm ?? null) ? $fragranceTypeForm : [];

$brandFilters = is_array($brandFilters ?? null) ? $brandFilters : ['q' => '', 'page' => 1];
$fragranceTypeFilters = is_array($fragranceTypeFilters ?? null) ? $fragranceTypeFilters : ['q' => '', 'page' => 1];
$brandPagination = is_array($brandPagination ?? null) ? $brandPagination : ['currentPage' => 1, 'totalPages' => 1, 'total' => count($brands)];
$fragranceTypePagination = is_array($fragranceTypePagination ?? null) ? $fragranceTypePagination : ['currentPage' => 1, 'totalPages' => 1, 'total' => count($fragranceTypes)];

$brandSearchOpen = ($brandFilters['q'] ?? '') !== '';
$typeSearchOpen = ($fragranceTypeFilters['q'] ?? '') !== '';

$summary = [
    'brands' => (int) ($brandPagination['total'] ?? count($brands)),
    'fragrance_types' => (int) ($fragranceTypePagination['total'] ?? count($fragranceTypes)),
    'brands_in_use' => 0,
    'types_in_use' => 0,
    'gender_products' => 0,
];

foreach ($brands as $brand) {
    if ((int) ($brand['product_count'] ?? 0) > 0) {
        $summary['brands_in_use']++;
    }
}

foreach ($fragranceTypes as $fragranceType) {
    if ((int) ($fragranceType['product_count'] ?? 0) > 0) {
        $summary['types_in_use']++;
    }
}

foreach ($genders as $gender) {
    $summary['gender_products'] += (int) ($gender['product_count'] ?? 0);
}

$adminHeaderTitle = 'Catalog Data';
$adminHeaderLead = 'Manage the reusable brand, fragrance type, and gender metadata used by the product catalog.';
$adminHeaderSection = 'catalog';
$adminHeaderClass = 'admin-catalog-header';
$adminHeaderActions = [];

$buildBrandPageUrl = static function (int $pageNumber) use ($brandFilters, $fragranceTypeFilters): string {
    return '/admin/catalog?' . http_build_query(array_filter([
        'brand_q' => $brandFilters['q'] ?? '',
        'brand_page' => $pageNumber,
        'type_q' => $fragranceTypeFilters['q'] ?? '',
        'type_page' => $fragranceTypeFilters['page'] ?? 1,
    ], static fn($value) => $value !== '' && $value !== null));
};

$buildTypePageUrl = static function (int $pageNumber) use ($brandFilters, $fragranceTypeFilters): string {
    return '/admin/catalog?' . http_build_query(array_filter([
        'brand_q' => $brandFilters['q'] ?? '',
        'brand_page' => $brandFilters['page'] ?? 1,
        'type_q' => $fragranceTypeFilters['q'] ?? '',
        'type_page' => $pageNumber,
    ], static fn($value) => $value !== '' && $value !== null));
};
?>

<section class="admin-catalog-page<?= ($editingBrandId > 0 || $editingFragranceTypeId > 0) ? ' is-editing-catalog' : '' ?>">
    <div class="admin-catalog-shell">
        <?php require __DIR__ . '/../_header.php'; ?>

        <div class="admin-products-stats">
            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Brands</span>
                <strong><?= number_format($summary['brands']) ?></strong>
                <span class="admin-products-stat-note"><?= number_format($summary['brands_in_use']) ?> linked on this page</span>
            </div>

            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Fragrance types</span>
                <strong><?= number_format($summary['fragrance_types']) ?></strong>
                <span class="admin-products-stat-note"><?= number_format($summary['types_in_use']) ?> linked on this page</span>
            </div>

            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Gender buckets</span>
                <strong><?= number_format(count($genders)) ?></strong>
                <span class="admin-products-stat-note">Catalog filter options</span>
            </div>

            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Tagged products</span>
                <strong><?= number_format($summary['gender_products']) ?></strong>
                <span class="admin-products-stat-note">Products currently mapped into gender filters</span>
            </div>
        </div>

        <div class="admin-catalog-grid">
            <div class="admin-catalog-main">
                <section class="panel admin-catalog-panel" id="brands">
                    <div class="admin-product-panel-heading">
                        <div>
                            <h2>Brands</h2>
                            <p class="muted">Add new houses, rename existing ones, and clean up unused entries before they appear in the product form.</p>
                        </div>

                        <span class="badge">
                            <?= number_format((int) ($brandPagination['total'] ?? count($brands))) ?> total
                        </span>
                    </div>

                    <section class="panel admin-filter-panel" data-filter-panel>
                        <div class="admin-filter-header">
                            <div>
                                <h2>Search brands</h2>
                                <p class="muted admin-filter-summary">
                                    <?= number_format((int) ($brandPagination['total'] ?? count($brands))) ?> matching brands
                                </p>
                            </div>

                            <button
                                type="button"
                                class="button-secondary admin-filter-toggle filter-toggle-button filter-toggle-button-icon-only"
                                data-filter-toggle
                                aria-expanded="<?= $brandSearchOpen ? 'true' : 'false' ?>"
                                aria-label="Toggle brand search"
                                title="Toggle brand search">
                                <span class="filter-toggle-icon" aria-hidden="true"></span>
                                <span class="sr-only">Toggle brand search</span>
                            </button>
                        </div>

                        <form
                            method="GET"
                            action="/admin/catalog#brands"
                            class="auth-form admin-filter-form admin-filter-body <?= $brandSearchOpen ? 'is-open' : '' ?>"
                            data-filter-body>
                            <div class="admin-filter-grid">
                                <div class="form-group admin-filter-search">
                                    <label for="brand-q">Search</label>
                                    <input
                                        id="brand-q"
                                        type="text"
                                        name="brand_q"
                                        value="<?= htmlspecialchars((string) ($brandFilters['q'] ?? '')) ?>"
                                        placeholder="Search brands...">
                                </div>
                            </div>

                            <input type="hidden" name="type_q" value="<?= htmlspecialchars((string) ($fragranceTypeFilters['q'] ?? '')) ?>">
                            <input type="hidden" name="type_page" value="<?= (int) ($fragranceTypeFilters['page'] ?? 1) ?>">

                            <div class="admin-filter-actions">
                                <button type="submit" class="auth-button">Apply filters</button>
                                <a href="/admin/catalog#brands" class="button-secondary">Reset</a>
                            </div>
                        </form>
                    </section>

                    <div class="admin-catalog-section-grid">
                        <div class="admin-catalog-list">
                            <?php if ($brands === []): ?>
                                <div class="empty-state">
                                    <h3>No matching brands</h3>
                                    <p>Try a broader search or create a new brand on the right.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($brands as $brand): ?>
                                    <?php $productCount = (int) ($brand['product_count'] ?? 0); ?>
                                    <article class="admin-catalog-item">
                                        <div class="admin-catalog-item-copy">
                                            <strong><?= htmlspecialchars((string) $brand['name']) ?></strong>
                                            <a
                                                href="/admin/products?q=<?= urlencode((string) $brand['name']) ?>"
                                                class="badge admin-catalog-product-count">
                                                <?= $productCount > 0
                                                    ? number_format($productCount) . ' ' . ($productCount === 1 ? 'product' : 'products')
                                                    : 'Unused' ?>
                                            </a>
                                        </div>

                                        <div class="admin-catalog-item-actions">
                                            <a href="/admin/catalog?brand_edit=<?= (int) $brand['id'] ?>#brand-editor" class="button-secondary">Edit</a>

                                            <form method="POST" action="/admin/catalog/brands/<?= (int) $brand['id'] ?>/delete">
                                                <?= \App\Core\Csrf::input() ?>
                                                <button
                                                    type="submit"
                                                    class="button-danger"
                                                    <?= $productCount > 0 ? 'disabled' : '' ?>>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </article>
                                <?php endforeach; ?>

                                <?php if ((int) ($brandPagination['totalPages'] ?? 1) > 1): ?>
                                    <nav class="admin-pagination">
                                        <?php if ((int) $brandPagination['currentPage'] > 1): ?>
                                            <a href="<?= htmlspecialchars($buildBrandPageUrl((int) $brandPagination['currentPage'] - 1)) ?>#brands" class="button-secondary">Previous</a>
                                        <?php else: ?>
                                            <span class="button-secondary pagination-disabled">Previous</span>
                                        <?php endif; ?>

                                        <span class="muted admin-results-count">
                                            Page <?= (int) $brandPagination['currentPage'] ?> of <?= (int) $brandPagination['totalPages'] ?>
                                        </span>

                                        <?php if ((int) $brandPagination['currentPage'] < (int) $brandPagination['totalPages']): ?>
                                            <a href="<?= htmlspecialchars($buildBrandPageUrl((int) $brandPagination['currentPage'] + 1)) ?>#brands" class="button-secondary">Next</a>
                                        <?php else: ?>
                                            <span class="button-secondary pagination-disabled">Next</span>
                                        <?php endif; ?>
                                    </nav>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <section class="admin-catalog-editor" id="brand-editor">
                            <div class="admin-product-panel-heading">
                                <div>
                                    <h3><?= $editingBrandId > 0 ? 'Edit brand' : 'Create brand' ?></h3>
                                    <p class="muted">Brand names must stay unique and can only be deleted when no products still reference them.</p>
                                </div>

                                <?php if ($editingBrandId > 0): ?>
                                    <a href="/admin/catalog#brand-editor" class="button-secondary">New brand</a>
                                <?php endif; ?>
                            </div>

                            <?php if (! empty($brandError)): ?>
                                <div class="message message-error">
                                    <?= htmlspecialchars((string) $brandError) ?>
                                </div>
                            <?php endif; ?>

                            <form
                                method="POST"
                                action="<?= $editingBrandId > 0 ? '/admin/catalog/brands/' . $editingBrandId : '/admin/catalog/brands' ?>"
                                class="admin-catalog-form">
                                <?= \App\Core\Csrf::input() ?>

                                <div class="form-group">
                                    <label for="brand-name">Brand name</label>
                                    <input
                                        id="brand-name"
                                        name="name"
                                        type="text"
                                        maxlength="100"
                                        value="<?= htmlspecialchars((string) ($brandForm['name'] ?? '')) ?>"
                                        placeholder="e.g. Maison Francis Kurkdjian"
                                        required>
                                </div>

                                <div class="admin-note-form-actions">
                                    <button type="submit" class="auth-button">
                                        <?= $editingBrandId > 0 ? 'Save brand' : 'Create brand' ?>
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </section>

                <section class="panel admin-catalog-panel" id="fragrance-types">
                    <div class="admin-product-panel-heading">
                        <div>
                            <h2>Fragrance Types</h2>
                            <p class="muted">Keep the concentration and perfume-style options tidy so merchandising and filtering stay consistent.</p>
                        </div>

                        <span class="badge">
                            <?= number_format((int) ($fragranceTypePagination['total'] ?? count($fragranceTypes))) ?> total
                        </span>
                    </div>

                    <section class="panel admin-filter-panel" data-filter-panel>
                        <div class="admin-filter-header">
                            <div>
                                <h2>Search</h2>
                                <p class="muted admin-filter-summary">
                                    <?= number_format((int) ($fragranceTypePagination['total'] ?? count($fragranceTypes))) ?> matching types
                                    <?php if ($typeSearchOpen): ?>
                                        · 1 active
                                    <?php endif; ?>
                                </p>
                            </div>

                            <button
                                type="button"
                                class="button-secondary admin-filter-toggle filter-toggle-button filter-toggle-button-icon-only"
                                data-filter-toggle
                                aria-expanded="<?= $typeSearchOpen ? 'true' : 'false' ?>"
                                aria-label="Toggle fragrance type search"
                                title="Toggle fragrance type search">
                                <span class="filter-toggle-icon" aria-hidden="true"></span>
                                <span class="sr-only">Toggle fragrance type search</span>
                            </button>
                        </div>

                        <form
                            method="GET"
                            action="/admin/catalog#fragrance-types"
                            class="auth-form admin-filter-form admin-filter-body <?= $typeSearchOpen ? 'is-open' : '' ?>"
                            data-filter-body>
                            <div class="admin-filter-grid">
                                <div class="form-group admin-filter-search">
                                    <label for="type-q">Search fragrance types</label>
                                    <input
                                        id="type-q"
                                        type="text"
                                        name="type_q"
                                        value="<?= htmlspecialchars((string) ($fragranceTypeFilters['q'] ?? '')) ?>"
                                        placeholder="Type name">
                                </div>
                            </div>

                            <input type="hidden" name="brand_q" value="<?= htmlspecialchars((string) ($brandFilters['q'] ?? '')) ?>">
                            <input type="hidden" name="brand_page" value="<?= (int) ($brandFilters['page'] ?? 1) ?>">

                            <div class="admin-filter-actions">
                                <button type="submit" class="auth-button">Apply filters</button>
                                <a href="/admin/catalog#fragrance-types" class="button-secondary">Reset</a>
                            </div>
                        </form>
                    </section>

                    <div class="admin-catalog-section-grid">
                        <div class="admin-catalog-list">
                            <?php if ($fragranceTypes === []): ?>
                                <div class="empty-state">
                                    <h3>No matching fragrance types</h3>
                                    <p>Try a broader search or create a new type on the right.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($fragranceTypes as $fragranceType): ?>
                                    <?php $productCount = (int) ($fragranceType['product_count'] ?? 0); ?>
                                    <article class="admin-catalog-item">
                                        <div class="admin-catalog-item-copy">
                                            <strong><?= htmlspecialchars((string) $fragranceType['name']) ?></strong>
                                            <a
                                                href="/admin/products?q=<?= urlencode((string) $fragranceType['name']) ?>"
                                                class="badge admin-catalog-product-count">
                                                <?= $productCount > 0
                                                    ? number_format($productCount) . ' ' . ($productCount === 1 ? 'product' : 'products')
                                                    : 'Unused' ?>
                                            </a>
                                        </div>

                                        <div class="admin-catalog-item-actions">
                                            <a href="/admin/catalog?fragrance_edit=<?= (int) $fragranceType['id'] ?>#fragrance-type-editor" class="button-secondary">Edit</a>

                                            <form method="POST" action="/admin/catalog/fragrance-types/<?= (int) $fragranceType['id'] ?>/delete">
                                                <?= \App\Core\Csrf::input() ?>
                                                <button
                                                    type="submit"
                                                    class="button-danger"
                                                    <?= $productCount > 0 ? 'disabled' : '' ?>>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </article>
                                <?php endforeach; ?>

                                <?php if ((int) ($fragranceTypePagination['totalPages'] ?? 1) > 1): ?>
                                    <nav class="admin-pagination">
                                        <?php if ((int) $fragranceTypePagination['currentPage'] > 1): ?>
                                            <a href="<?= htmlspecialchars($buildTypePageUrl((int) $fragranceTypePagination['currentPage'] - 1)) ?>#fragrance-types" class="button-secondary">Previous</a>
                                        <?php else: ?>
                                            <span class="button-secondary pagination-disabled">Previous</span>
                                        <?php endif; ?>

                                        <span class="muted admin-results-count">
                                            Page <?= (int) $fragranceTypePagination['currentPage'] ?> of <?= (int) $fragranceTypePagination['totalPages'] ?>
                                        </span>

                                        <?php if ((int) $fragranceTypePagination['currentPage'] < (int) $fragranceTypePagination['totalPages']): ?>
                                            <a href="<?= htmlspecialchars($buildTypePageUrl((int) $fragranceTypePagination['currentPage'] + 1)) ?>#fragrance-types" class="button-secondary">Next</a>
                                        <?php else: ?>
                                            <span class="button-secondary pagination-disabled">Next</span>
                                        <?php endif; ?>
                                    </nav>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <section class="admin-catalog-editor" id="fragrance-type-editor">
                            <div class="admin-product-panel-heading">
                                <div>
                                    <h3><?= $editingFragranceTypeId > 0 ? 'Edit fragrance type' : 'Create fragrance type' ?></h3>
                                    <p class="muted">These values are optional on products, but each entry still needs to stay unique and descriptive.</p>
                                </div>

                                <?php if ($editingFragranceTypeId > 0): ?>
                                    <a href="/admin/catalog#fragrance-type-editor" class="button-secondary">New type</a>
                                <?php endif; ?>
                            </div>

                            <?php if (! empty($fragranceTypeError)): ?>
                                <div class="message message-error">
                                    <?= htmlspecialchars((string) $fragranceTypeError) ?>
                                </div>
                            <?php endif; ?>

                            <form
                                method="POST"
                                action="<?= $editingFragranceTypeId > 0 ? '/admin/catalog/fragrance-types/' . $editingFragranceTypeId : '/admin/catalog/fragrance-types' ?>"
                                class="admin-catalog-form">
                                <?= \App\Core\Csrf::input() ?>

                                <div class="form-group">
                                    <label for="fragrance-type-name">Fragrance type name</label>
                                    <input
                                        id="fragrance-type-name"
                                        name="name"
                                        type="text"
                                        maxlength="50"
                                        value="<?= htmlspecialchars((string) ($fragranceTypeForm['name'] ?? '')) ?>"
                                        placeholder="e.g. Eau de Parfum"
                                        required>
                                </div>

                                <div class="admin-note-form-actions">
                                    <button type="submit" class="auth-button">
                                        <?= $editingFragranceTypeId > 0 ? 'Save type' : 'Create type' ?>
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </section>
            </div>

            <aside class="admin-catalog-sidebar">
                <section class="panel admin-catalog-sidebar-panel">
                    <div class="admin-product-panel-heading">
                        <div>
                            <h2>Gender</h2>
                            <p class="muted">Product-facing gender filters used across the catalog.</p>
                        </div>
                    </div>

                    <div class="admin-catalog-gender-list">
                        <?php foreach ($genders as $gender): ?>
                            <article class="admin-catalog-gender-card">
                                <div class="admin-catalog-gender-top">
                                    <strong><?= htmlspecialchars((string) $gender['label']) ?></strong>
                                    <a
                                        href="/admin/products?gender=<?= urlencode((string) $gender['value']) ?>"
                                        class="badge admin-catalog-product-count">
                                        <?= number_format((int) $gender['product_count']) ?> products
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</section>
