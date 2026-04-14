<?php
// Admin catalogue overview focused on stock, merchandising data, and quick
// access to product editing.

$formatPriceRange = static function ($minPrice, $maxPrice): string {
    if ($minPrice === null || $maxPrice === null) {
        return 'No variants yet';
    }

    $min = (float) $minPrice;
    $max = (float) $maxPrice;

    if (abs($min - $max) < 0.001) {
        return '€' . number_format($min, 2);
    }

    return sprintf('€%s - €%s', number_format($min, 2), number_format($max, 2));
};

$summary = [
    'products' => count($products),
    'variants' => 0,
    'stocked' => 0,
    'missing_images' => 0,
    'total_stock' => 0,
];

foreach ($products as $product) {
    $summary['variants'] += (int) ($product['variant_count'] ?? 0);
    $summary['total_stock'] += (int) ($product['total_stock'] ?? 0);

    if ((int) ($product['total_stock'] ?? 0) > 0) {
        $summary['stocked']++;
    }

    if (empty($product['image_url'])) {
        $summary['missing_images']++;
    }
}

$buildPageUrl = static function (int $pageNumber) use ($filters): string {
    $params = $filters;
    $params['page'] = $pageNumber;

    return '/admin/products?' . http_build_query(array_filter(
        $params,
        static fn ($value) => $value !== '' && $value !== null
    ));
};
$hasActiveFilters = ($filters['q'] ?? '') !== ''
    || ($filters['gender'] ?? '') !== ''
    || ($filters['inventory'] ?? '') !== '';
$normalizeTagValue = static function (?string $value): string {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/i', ' ', (string) $value) ?? ''));
};
?>
<section class="admin-products-page">
    <div class="admin-products-shell">
        <?php
        $adminHeaderTitle = 'Products';
        $adminHeaderLead = 'Manage merchandising details, note profiles, stock coverage, and catalog presentation from one cleaner workspace.';
        $adminHeaderSection = 'products';
        $adminHeaderClass = 'admin-products-hero';
        $adminHeaderActions = [
            [
                'type' => 'link',
                'href' => '/admin/products/create',
                'label' => 'Create product',
                'class' => 'auth-button',
            ],
        ];

        require __DIR__ . '/../_header.php';
        ?>

        <section class="panel admin-filter-panel">
            <form method="GET" action="/admin/products" class="auth-form admin-filter-form">
                <div class="admin-filter-grid">
                    <div class="form-group admin-filter-search">
                        <label for="product-q">Search</label>
                        <input
                            id="product-q"
                            type="text"
                            name="q"
                            placeholder="Product name, slug, brand, or ID"
                            value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>">
                    </div>

                    <div class="form-group">
                        <label for="product-gender">Gender</label>
                        <select id="product-gender" name="gender">
                            <option value="">All</option>
                            <?php foreach ($genders as $gender): ?>
                                <option
                                    value="<?= htmlspecialchars((string) $gender) ?>"
                                    <?= (string) ($filters['gender'] ?? '') === (string) $gender ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(ucfirst((string) $gender)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product-inventory">Inventory</label>
                        <select id="product-inventory" name="inventory">
                            <option value="">All stock levels</option>
                            <option value="in_stock" <?= ($filters['inventory'] ?? '') === 'in_stock' ? 'selected' : '' ?>>In stock</option>
                            <option value="low_stock" <?= ($filters['inventory'] ?? '') === 'low_stock' ? 'selected' : '' ?>>Low stock</option>
                            <option value="out_of_stock" <?= ($filters['inventory'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of stock</option>
                        </select>
                    </div>
                </div>

                <div class="admin-filter-actions">
                    <button type="submit" class="auth-button">Apply filters</button>
                    <a href="/admin/products" class="button-secondary">Reset</a>
                    <span class="muted admin-results-count"><?= number_format((int) $totalProducts) ?> matching products</span>
                </div>
            </form>
        </section>

        <div class="admin-products-stats">
            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Matching products</span>
                <strong><?= number_format((int) $totalProducts) ?></strong>
                <span class="admin-products-stat-note">
                    <?= number_format($summary['products']) ?> shown on this page
                </span>
            </div>

            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Stocked on page</span>
                <strong><?= number_format($summary['stocked']) ?></strong>
                <span class="admin-products-stat-note">
                    <?= number_format($summary['total_stock']) ?> units total on hand
                </span>
            </div>

            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Missing image</span>
                <strong><?= number_format($summary['missing_images']) ?></strong>
                <span class="admin-products-stat-note">
                    Primary visuals missing on this page
                </span>
            </div>

            <div class="card admin-products-stat">
                <span class="admin-products-stat-label">Avg variants</span>
                <strong>
                    <?= $summary['products'] > 0 ? number_format($summary['variants'] / $summary['products'], 1) : '0.0' ?>
                </strong>
                <span class="admin-products-stat-note">
                    Visible size coverage per product
                </span>
            </div>
        </div>

        <?php if ($products === []): ?>
            <div class="empty-state admin-products-empty">
                <h2><?= $hasActiveFilters ? 'No matching products' : 'No products yet' ?></h2>
                <p>
                    <?= $hasActiveFilters
                        ? 'Try widening the search or clearing filters to see more of the catalogue.'
                        : 'Create the first catalog entry to start building the storefront assortment.' ?>
                </p>
            </div>
        <?php else: ?>
            <div class="card admin-products-table-card">
                <div class="admin-products-table-header">
                    <div>
                        <h2>Catalog</h2>
                        <p class="muted">Snapshot of the current assortment, pricing spread, and inventory health.</p>
                    </div>

                    <span class="badge">
                        Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?>
                    </span>
                </div>

                <div class="admin-table-wrap admin-products-table-wrap">
                    <table class="admin-table admin-products-table">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Product</th>
                                <th>Positioning</th>
                                <th>Inventory</th>
                                <th>Pricing</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <?php
                                $totalStock = (int) ($product['total_stock'] ?? 0);
                                $variantCount = (int) ($product['variant_count'] ?? 0);
                                $inventoryClass = $totalStock <= 0
                                    ? 'is-empty'
                                    : ($totalStock <= 10 ? 'is-low' : 'is-healthy');
                                $inventoryLabel = $totalStock <= 0
                                    ? 'Out of stock'
                                    : ($totalStock <= 10 ? 'Low stock' : 'In stock');
                                $positioningTags = [];
                                $concentrationLabel = trim((string) ($product['concentration_label'] ?? ''));
                                $fragranceTypeName = trim((string) ($product['fragrance_type_name'] ?? ''));
                                $genderLabel = ucfirst((string) ($product['gender'] ?? ''));

                                if ($concentrationLabel !== '') {
                                    $positioningTags[] = $concentrationLabel;
                                }

                                if (
                                    $fragranceTypeName !== ''
                                    && $normalizeTagValue($fragranceTypeName) !== $normalizeTagValue($concentrationLabel)
                                ) {
                                    $positioningTags[] = $fragranceTypeName;
                                }

                                if ($genderLabel !== '') {
                                    $positioningTags[] = $genderLabel;
                                }
                                ?>
                                <tr>
                                    <td data-label="Preview">
                                        <?php if (! empty($product['image_url'])): ?>
                                            <img
                                                src="/<?= htmlspecialchars((string) $product['image_url']) ?>"
                                                alt="<?= htmlspecialchars((string) $product['name']) ?>"
                                                class="admin-product-table-image">
                                        <?php else: ?>
                                            <div class="admin-product-table-image-placeholder">No image</div>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Product">
                                        <div class="admin-product-table-name-row">
                                            <strong class="admin-product-table-name">
                                                <?= htmlspecialchars((string) $product['name']) ?>
                                            </strong>

                                            <?php if (! empty($product['deleted_at'])): ?>
                                                <span class="admin-product-table-state">Archived</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="admin-product-table-brand">
                                            <?= htmlspecialchars((string) $product['brand_name']) ?>
                                        </div>

                                        <div class="admin-product-table-meta">
                                            <span>#<?= (int) $product['id'] ?></span>
                                            <span class="cart-meta-separator">·</span>
                                            <span><?= htmlspecialchars((string) $product['slug']) ?></span>
                                        </div>
                                    </td>
                                    <td data-label="Positioning">
                                        <div class="admin-product-tag-list">
                                            <?php foreach ($positioningTags as $tag): ?>
                                                <span class="admin-product-tag">
                                                    <?= htmlspecialchars((string) $tag) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td data-label="Inventory">
                                        <span class="admin-product-stock-pill <?= $inventoryClass ?>">
                                            <?= htmlspecialchars($inventoryLabel) ?>
                                        </span>
                                        <div class="admin-product-table-metric">
                                            <?= number_format($variantCount) ?> <?= $variantCount === 1 ? 'variant' : 'variants' ?>
                                        </div>
                                        <div class="admin-product-table-submeta">
                                            <?= number_format($totalStock) ?> units available
                                        </div>
                                    </td>
                                    <td data-label="Pricing">
                                        <div class="admin-product-table-price">
                                            <?= htmlspecialchars($formatPriceRange($product['min_price'], $product['max_price'])) ?>
                                        </div>
                                        <div class="admin-product-table-submeta">
                                            Variant price range
                                        </div>
                                    </td>
                                    <td data-label="Action">
                                        <a
                                            href="/admin/products/<?= (int) $product['id'] ?>/edit"
                                            class="button-link admin-products-edit-link">
                                            Edit product
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (($totalPages ?? 1) > 1): ?>
                <nav class="admin-pagination">
                    <?php if (($currentPage ?? 1) > 1): ?>
                        <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" class="button-secondary">Previous</a>
                    <?php else: ?>
                        <span class="button-secondary pagination-disabled">Previous</span>
                    <?php endif; ?>

                    <span class="muted admin-results-count">
                        Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?>
                    </span>

                    <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
                        <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" class="button-secondary">Next</a>
                    <?php else: ?>
                        <span class="button-secondary pagination-disabled">Next</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
