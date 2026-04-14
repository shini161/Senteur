<?php
// Admin product editing workspace with preserved variant identities, media
// upload controls, and a clearer overview of catalog health.

$variantCount = count($product['variants'] ?? []);
$totalStock = 0;
$prices = [];
$noteCount = count($product['note_ids']['top'] ?? [])
    + count($product['note_ids']['middle'] ?? [])
    + count($product['note_ids']['base'] ?? []);

foreach (($product['variants'] ?? []) as $variant) {
    $totalStock += (int) ($variant['stock'] ?? 0);
    $prices[] = (float) ($variant['price'] ?? 0);
}

$priceSummary = 'No variants yet';

if ($prices !== []) {
    $minPrice = min($prices);
    $maxPrice = max($prices);

    $priceSummary = abs($minPrice - $maxPrice) < 0.001
        ? '€' . number_format($minPrice, 2)
        : sprintf('€%s - €%s', number_format($minPrice, 2), number_format($maxPrice, 2));
}
?>
<section class="admin-product-page">
    <div class="admin-product-shell">
        <?php
        $adminHeaderTitle = 'Edit ' . (string) $product['name'];
        $adminHeaderLead = 'Refine catalog details, note profile, and media without losing variant-level uploads.';
        $adminHeaderKicker = 'Admin / Products';
        $adminHeaderSection = 'products';
        $adminHeaderClass = 'admin-product-header';
        $adminHeaderActions = [
            [
                'type' => 'link',
                'href' => '/admin/products',
                'label' => 'Back to products',
                'class' => 'button-secondary',
            ],
            [
                'type' => 'badge',
                'label' => 'ID #' . (int) $product['id'],
                'class' => 'badge',
            ],
        ];

        require __DIR__ . '/../_header.php';
        ?>

        <div class="admin-product-summary-grid">
            <div class="card admin-product-summary-card">
                <span class="admin-products-stat-label">Variants</span>
                <strong><?= number_format($variantCount) ?></strong>
                <span class="admin-products-stat-note">Tracked sizes for this product</span>
            </div>

            <div class="card admin-product-summary-card">
                <span class="admin-products-stat-label">Stock</span>
                <strong><?= number_format($totalStock) ?></strong>
                <span class="admin-products-stat-note">Units available across all variants</span>
            </div>

            <div class="card admin-product-summary-card">
                <span class="admin-products-stat-label">Pricing</span>
                <strong><?= htmlspecialchars($priceSummary) ?></strong>
                <span class="admin-products-stat-note">Current storefront range</span>
            </div>

            <div class="card admin-product-summary-card">
                <span class="admin-products-stat-label">Primary image</span>
                <strong><?= empty($product['image_url']) ? 'Missing' : 'Uploaded' ?></strong>
                <span class="admin-products-stat-note">Main merchandising visual</span>
            </div>

            <div class="card admin-product-summary-card">
                <span class="admin-products-stat-label">Notes</span>
                <strong><?= number_format($noteCount) ?></strong>
                <span class="admin-products-stat-note">Top, middle, and base profile selections</span>
            </div>
        </div>

        <div class="admin-product-workspace">
            <div class="admin-product-main">
                <form method="POST" action="/admin/products/<?= (int) $product['id'] ?>" class="admin-product-form" data-admin-product-form>
                    <?= \App\Core\Csrf::input() ?>
                    <?php require __DIR__ . '/_form.php'; ?>

                    <div class="panel admin-product-submit-panel">
                        <div>
                            <h2>Save changes</h2>
                            <p class="muted">Variant IDs now stay stable during normal edits, so existing variant media remains attached.</p>
                        </div>

                        <button type="submit" class="auth-button">Save changes</button>
                    </div>
                </form>
            </div>

            <aside class="admin-product-sidebar">
                <section class="panel admin-product-sidebar-panel">
                    <div class="admin-product-panel-heading">
                        <div>
                            <h2>Primary image</h2>
                            <p class="muted">Update the main visual used across catalog cards and detail pages.</p>
                        </div>
                    </div>

                    <?php if (! empty($imageError)): ?>
                        <div class="message message-error">
                            <?= htmlspecialchars((string) $imageError) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($product['image_url'])): ?>
                        <img
                            src="/<?= htmlspecialchars((string) $product['image_url']) ?>"
                            alt="<?= htmlspecialchars((string) $product['name']) ?>"
                            class="admin-product-primary-image">
                    <?php else: ?>
                        <div class="admin-product-primary-image-placeholder">No primary image yet</div>
                    <?php endif; ?>

                    <form
                        method="POST"
                        action="/admin/products/<?= (int) $product['id'] ?>/image"
                        enctype="multipart/form-data"
                        class="admin-product-upload-form">
                        <?= \App\Core\Csrf::input() ?>

                        <div class="form-group">
                            <label for="image">Upload image</label>
                            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" required>
                        </div>

                        <button type="submit" class="button-secondary">Upload primary image</button>
                    </form>
                </section>

                <section class="panel admin-product-sidebar-panel">
                    <h2>Editing notes</h2>
                    <p class="muted">
                        Assign fragrance notes directly in the product form, and use the media section below whenever you need a size-specific image override.
                    </p>
                </section>
            </aside>
        </div>

        <?php if (($product['variants'] ?? []) !== []): ?>
            <section class="panel admin-product-variant-media-panel">
                <div class="admin-product-panel-heading">
                    <div>
                        <h2>Variant images</h2>
                        <p class="muted">Upload a primary image for each size when the bottle or pack shot needs to differ from the default product visual.</p>
                    </div>

                    <span class="badge">
                        <?= number_format($variantCount) ?> <?= $variantCount === 1 ? 'variant' : 'variants' ?>
                    </span>
                </div>

                <?php if (! empty($variantImageError)): ?>
                    <div class="message message-error">
                        <?= htmlspecialchars((string) $variantImageError) ?>
                    </div>
                <?php endif; ?>

                <div class="admin-product-variant-media-grid">
                    <?php foreach ($product['variants'] as $variant): ?>
                        <?php $variantImage = $variant['images'][0]['image_url'] ?? null; ?>
                        <article class="admin-product-variant-media-card">
                            <?php if ($variantImage): ?>
                                <img
                                    src="/<?= htmlspecialchars((string) $variantImage) ?>"
                                    alt="<?= htmlspecialchars((string) $product['name']) ?> <?= (int) $variant['size_ml'] ?> ml"
                                    class="admin-product-variant-media-image">
                            <?php else: ?>
                                <div class="admin-product-variant-media-placeholder">No image</div>
                            <?php endif; ?>

                            <div class="admin-product-variant-media-copy">
                                <strong><?= (int) $variant['size_ml'] ?> ml</strong>
                                <span class="admin-product-table-submeta">
                                    €<?= number_format((float) $variant['price'], 2) ?> · <?= (int) $variant['stock'] ?> in stock
                                </span>
                            </div>

                            <form
                                method="POST"
                                action="/admin/variants/<?= (int) $variant['id'] ?>/image"
                                enctype="multipart/form-data"
                                class="admin-product-upload-form">
                                <?= \App\Core\Csrf::input() ?>
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

                                <div class="form-group">
                                    <label for="variant-image-<?= (int) $variant['id'] ?>">Upload image</label>
                                    <input
                                        id="variant-image-<?= (int) $variant['id'] ?>"
                                        type="file"
                                        name="image"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        required>
                                </div>

                                <button type="submit" class="button-secondary">Upload variant image</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</section>
