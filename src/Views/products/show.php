<?php
// Product detail page with variant switching, gallery updates, purchase actions,
// scent breakdown, reviews, and related-product recommendations.

// Review dates use relative wording for recent entries and fall back to an
// absolute date for older reviews.
$formatReviewDate = static function (string $date): string {
    $reviewDate = new DateTime($date);
    $now = new DateTime();

    $reviewDate->setTime(0, 0, 0);
    $now->setTime(0, 0, 0);

    $days = (int) $reviewDate->diff($now)->format('%r%a');

    if ($days === 0) {
        return 'Today';
    }

    if ($days === 1) {
        return 'Yesterday';
    }

    if ($days > 1 && $days <= 14) {
        return $days . ' days ago';
    }

    return (new DateTime($date))->format('d M Y');
};

// Filled and empty star markup are generated server-side so review lists render
// correctly before any JavaScript enhancements run.
$renderStars = static function (int $rating): string {
    $rating = max(0, min(5, $rating));

    $html = '';

    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $rating ? '★' : '<span class="empty">★</span>';
    }

    return $html;
};

// Average ratings support half-star display for a slightly richer summary UI.
$renderAverageStars = static function (float $rating): string {
    $rating = max(0, min(5, $rating));
    $html = '';

    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $html .= '<span class="star-full">★</span>';
        } elseif ($rating >= $i - 0.5) {
            $html .= '<span class="star-half">★</span>';
        } else {
            $html .= '<span class="star-empty">★</span>';
        }
    }

    return $html;
};

$variants = $product['variants'] ?? [];
$selectedVariant = $variants[0] ?? null;
$selectedVariantImages = $selectedVariant['images'] ?? [];
$selectedImage = $selectedVariantImages[0]['image_url'] ?? $selectedVariant['image_url'] ?? $product['image_url'] ?? null;

$scripts ??= [];
$scripts[] = '/assets/js/products/show.js';
?>

<section class="product-show">
    <?php // Left column: initial gallery state for the first selectable variant. ?>
    <div class="product-show-gallery">
        <div class="product-show-image-wrap">
            <?php if ($selectedImage): ?>
                <img
                    id="product-main-image"
                    src="/<?= htmlspecialchars($selectedImage) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <div class="product-placeholder">SENTEUR</div>
            <?php endif; ?>
        </div>

        <?php if (!empty($selectedVariantImages)): ?>
            <div class="product-thumbnails" id="product-thumbnails">
                <?php foreach ($selectedVariantImages as $index => $image): ?>
                    <button
                        type="button"
                        class="product-thumbnail <?= $index === 0 ? 'is-active' : '' ?>"
                        data-image-url="/<?= htmlspecialchars($image['image_url']) ?>">
                        <img
                            src="/<?= htmlspecialchars($image['image_url']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-show-panel">
        <?php // Right column: product metadata plus the add-to-cart form. ?>
        <div class="product-show-header">
            <div class="product-show-brand">
                <?= htmlspecialchars($product['brand_name']) ?>
            </div>

            <h1><?= htmlspecialchars($product['name']) ?></h1>

            <?php if (!empty($product['concentration_label'])): ?>
                <p class="product-subtitle"><?= htmlspecialchars($product['concentration_label']) ?></p>
            <?php endif; ?>

            <div class="product-meta-line">
                <span class="badge"><?= htmlspecialchars(ucfirst($product['gender'])) ?></span>
            </div>
        </div>

        <div class="product-rating-summary">
            <?php if ($reviewSummary['average_rating'] !== null): ?>
                <div class="product-rating-pill">
                    <span class="product-rating-value"><?= number_format((float) $reviewSummary['average_rating'], 1) ?></span>
                    <span class="review-stars review-stars-lg"><?= $renderAverageStars((float) $reviewSummary['average_rating']) ?></span>
                    <span class="muted"><?= (int) $reviewSummary['review_count'] ?> review<?= (int) $reviewSummary['review_count'] === 1 ? '' : 's' ?></span>
                </div>
            <?php else: ?>
                <span class="muted">No reviews yet</span>
            <?php endif; ?>
        </div>

        <p class="product-description">
            <?php if (!empty($product['description'])): ?>
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            <?php else: ?>
                No description available yet.
            <?php endif; ?>
        </p>

        <?php if (empty($variants)): ?>
            <div class="empty-state">
                <p>No variants available.</p>
            </div>
        <?php else: ?>
            <div class="product-purchase-card">
                <h2>Select size</h2>

                <div class="variant-selector" id="variant-selector">
                    <?php foreach ($variants as $index => $variant): ?>
                        <?php
                        // Each variant button carries the data needed for client-side
                        // switching so the page does not need round-trips for size changes.
                        $variantImageSet = $variant['images'] ?? [];
                        $variantPrimaryImage = $variantImageSet[0]['image_url'] ?? $variant['image_url'] ?? $product['image_url'] ?? '';
                        ?>
                        <button
                            type="button"
                            class="variant-option <?= $index === 0 ? 'is-selected' : '' ?>"
                            data-variant-id="<?= (int) $variant['id'] ?>"
                            data-price="<?= htmlspecialchars(number_format((float) $variant['price'], 2, '.', '')) ?>"
                            data-stock="<?= (int) $variant['stock'] ?>"
                            data-image="<?= htmlspecialchars($variantPrimaryImage) ?>"
                            data-images='<?= htmlspecialchars(json_encode(array_map(
                                                static fn(array $image): string => '/' . ltrim((string) $image['image_url'], '/'),
                                                $variantImageSet
                                            )), ENT_QUOTES, 'UTF-8') ?>'>
                            <span class="variant-option-size"><?= htmlspecialchars((string) $variant['size_ml']) ?>ml</span>
                            <span class="variant-option-price">€<?= number_format((float) $variant['price'], 2) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <form action="/cart/add" method="POST" class="product-purchase-form">
                    <?= \App\Core\Csrf::input() ?>
                    <input
                        type="hidden"
                        name="variant_id"
                        id="selected-variant-id"
                        value="<?= (int) $selectedVariant['id'] ?>">

                    <div class="product-purchase-summary">
                        <div>
                            <div class="muted">Selected price</div>
                            <div class="product-price" id="selected-variant-price">
                                €<?= number_format((float) $selectedVariant['price'], 2) ?>
                            </div>
                        </div>

                        <div>
                            <div class="muted">Availability</div>
                            <div class="product-stock" id="selected-variant-stock">
                                <?= (int) $selectedVariant['stock'] > 0
                                    ? 'In stock · ' . (int) $selectedVariant['stock'] . ' available'
                                    : 'Out of stock' ?>
                            </div>
                        </div>
                    </div>

                    <div class="product-purchase-actions">
                        <div class="qty-stepper">
                            <button type="button" class="qty-btn" data-qty-action="decrease" aria-label="Decrease quantity">−</button>

                            <input
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                max="<?= min((int) $selectedVariant['stock'], 5) ?>"
                                class="qty-input"
                                inputmode="numeric">

                            <button type="button" class="qty-btn" data-qty-action="increase" aria-label="Increase quantity">+</button>
                        </div>

                        <button type="submit" class="auth-button">Add to cart</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="product-detail-sections">
    <div class="panel">
        <?php // Categories and notes explain the fragrance profile beneath the purchase area. ?>
        <h2>Scent profile</h2>

        <?php if (!empty($product['categories'])): ?>
            <div class="product-meta-line">
                <?php foreach ($product['categories'] as $category): ?>
                    <span class="badge"><?= htmlspecialchars($category['name']) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (
            !empty($product['notes']['top']) ||
            !empty($product['notes']['middle']) ||
            !empty($product['notes']['base'])
        ): ?>
            <div class="scent-profile-grid">
                <?php if (!empty($product['notes']['top'])): ?>
                    <div>
                        <h3>Top notes</h3>
                        <p>
                            <?php foreach ($product['notes']['top'] as $index => $note): ?>
                                <?= $index > 0 ? ', ' : '' ?><?= htmlspecialchars($note['name']) ?>
                            <?php endforeach; ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($product['notes']['middle'])): ?>
                    <div>
                        <h3>Middle notes</h3>
                        <p>
                            <?php foreach ($product['notes']['middle'] as $index => $note): ?>
                                <?= $index > 0 ? ', ' : '' ?><?= htmlspecialchars($note['name']) ?>
                            <?php endforeach; ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($product['notes']['base'])): ?>
                    <div>
                        <h3>Base notes</h3>
                        <p>
                            <?php foreach ($product['notes']['base'] as $index => $note): ?>
                                <?= $index > 0 ? ', ' : '' ?><?= htmlspecialchars($note['name']) ?>
                            <?php endforeach; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No scent profile available yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php require __DIR__ . '/partials/reviews.php'; ?>

    <?php if (!empty($product['related_family_products'])): ?>
        <?php
        $relatedTitle = 'Also in this line';
        $relatedProducts = $product['related_family_products'];
        require __DIR__ . '/partials/related-products-grid.php';
        ?>
    <?php endif; ?>

    <?php if (!empty($product['related_products'])): ?>
        <?php
        $relatedTitle = 'You may also like';
        $relatedProducts = $product['related_products'];
        require __DIR__ . '/partials/related-products-grid.php';
        ?>
    <?php endif; ?>
</section>
