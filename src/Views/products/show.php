<?php
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

$renderStars = static function (int $rating): string {
    $rating = max(0, min(5, $rating));

    $html = '';

    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $rating ? '★' : '<span class="empty">★</span>';
    }

    return $html;
};

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
?>

<section class="product-show">
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

    <div class="panel" id="reviews">
        <h2>Reviews</h2>
        <div class="reviews-overview">
            <p class="muted">
                <?= (int) $reviewSummary['review_count'] ?> review<?= (int) $reviewSummary['review_count'] === 1 ? '' : 's' ?>
            </p>

            <?php if ($reviewSummary['average_rating'] !== null): ?>
                <div class="reviews-overview-rating">
                    <span class="product-rating-value"><?= number_format((float) $reviewSummary['average_rating'], 1) ?></span>
                    <span class="review-stars"><?= $renderAverageStars((float) $reviewSummary['average_rating']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($canReview): ?>
            <div class="review-form-wrap is-hidden" id="review-form">
                <h3><?= $userReview ? 'Update your review' : 'Write a review' ?></h3>

                <form method="POST" action="/products/<?= htmlspecialchars($product['slug']) ?>/reviews" class="auth-form">
                    <?= \App\Core\Csrf::input() ?>

                    <div class="form-group">
                        <label for="rating-5">Rating</label>

                        <div class="review-rating-input" data-rating-widget>
                            <div class="star-rating" role="radiogroup" aria-label="Rate this perfume">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input
                                        type="radio"
                                        id="rating-<?= $i ?>"
                                        name="rating"
                                        value="<?= $i ?>"
                                        <?= (string) ($userReview['rating'] ?? '') === (string) $i ? 'checked' : '' ?>
                                        required>
                                    <label
                                        for="rating-<?= $i ?>"
                                        class="star-rating-star"
                                        data-rating-value="<?= $i ?>"
                                        title="<?= $i ?>/5"
                                        aria-label="<?= $i ?> out of 5">
                                        ★
                                    </label>
                                <?php endfor; ?>
                            </div>

                            <div class="star-rating-meta">
                                <span class="star-rating-caption" data-rating-caption>
                                    <?= !empty($userReview['rating'])
                                        ? match ((int) $userReview['rating']) {
                                            1 => 'Poor',
                                            2 => 'Fair',
                                            3 => 'Good',
                                            4 => 'Very good',
                                            5 => 'Excellent',
                                        }
                                        : 'Select your rating' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="<?= htmlspecialchars((string) ($userReview['title'] ?? '')) ?>">
                    </div>

                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea
                            id="comment"
                            name="comment"
                            rows="4"><?= htmlspecialchars((string) ($userReview['comment'] ?? '')) ?></textarea>
                    </div>

                    <button type="submit" class="auth-button">
                        <?= $userReview ? 'Update review' : 'Submit review' ?>
                    </button>
                </form>
            </div>
        <?php elseif (\App\Core\Auth::check()): ?>
            <div class="empty-state">
                <p>You can review this perfume after purchasing it.</p>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p><a href="/login">Log in</a> to review perfumes you have purchased.</p>
            </div>
        <?php endif; ?>

        <?php if (empty($reviews)): ?>
            <div class="empty-state">
                <p>No reviews yet.</p>
            </div>
        <?php else: ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $review): ?>
                    <article class="panel review-card">
                        <div class="review-card-header">
                            <div class="review-card-author">
                                <strong class="review-card-username"><?= htmlspecialchars($review['username']) ?></strong>

                                <div class="review-card-rating-row">
                                    <span class="review-stars review-stars-md"><?= $renderStars((int) $review['rating']) ?></span>
                                    <span class="review-card-rating-value"><?= (int) $review['rating'] ?>.0</span>
                                </div>
                            </div>

                            <div class="review-card-meta">
                                <span class="muted review-card-date"><?= htmlspecialchars($formatReviewDate($review['created_at'])) ?></span>

                                <?php if ($userReview && (int) $review['id'] === (int) $userReview['id']): ?>
                                    <button type="button" class="button-link review-edit-toggle">Edit</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($review['title'])): ?>
                            <p><strong><?= htmlspecialchars($review['title']) ?></strong></p>
                        <?php endif; ?>

                        <?php if (!empty($review['comment'])): ?>
                            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($product['related_family_products'])): ?>
        <div class="panel">
            <h2>Also in this line</h2>

            <div class="related-products-grid">
                <?php foreach ($product['related_family_products'] as $related): ?>
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
    <?php endif; ?>

    <?php if (!empty($product['related_products'])): ?>
        <div class="panel">
            <h2>You may also like</h2>

            <div class="related-products-grid">
                <?php foreach ($product['related_products'] as $related): ?>
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
    <?php endif; ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantButtons = document.querySelectorAll('.variant-option');
        const variantIdInput = document.getElementById('selected-variant-id');
        const priceEl = document.getElementById('selected-variant-price');
        const stockEl = document.getElementById('selected-variant-stock');
        const mainImage = document.getElementById('product-main-image');
        const thumbnailsWrap = document.getElementById('product-thumbnails');
        const quantityInput = document.querySelector('.product-purchase-form input[name="quantity"]');
        const qtyStepper = document.querySelector('.qty-stepper');

        let clampQuantity = null;

        const renderThumbnails = (images) => {
            if (!thumbnailsWrap) {
                return;
            }

            thumbnailsWrap.innerHTML = '';

            if (!images.length) {
                return;
            }

            images.forEach((imageUrl, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'product-thumbnail' + (index === 0 ? ' is-active' : '');
                button.dataset.imageUrl = imageUrl;
                button.innerHTML = `<img src="${imageUrl}" alt="Product image">`;

                button.addEventListener('click', function() {
                    if (mainImage) {
                        mainImage.src = imageUrl;
                    }

                    thumbnailsWrap.querySelectorAll('.product-thumbnail').forEach((thumb) => {
                        thumb.classList.remove('is-active');
                    });

                    button.classList.add('is-active');
                });

                thumbnailsWrap.appendChild(button);
            });
        };

        if (qtyStepper && quantityInput) {
            const decreaseBtn = qtyStepper.querySelector('[data-qty-action="decrease"]');
            const increaseBtn = qtyStepper.querySelector('[data-qty-action="increase"]');

            clampQuantity = () => {
                const min = Number(quantityInput.min || 1);
                const max = Number(quantityInput.max || 1);
                let value = Number(quantityInput.value || min);

                if (Number.isNaN(value)) {
                    value = min;
                }

                value = Math.max(min, Math.min(max, value));
                quantityInput.value = String(value);

                const disabled = quantityInput.disabled;
                decreaseBtn.disabled = disabled || value <= min;
                increaseBtn.disabled = disabled || value >= max;
            };

            decreaseBtn.addEventListener('click', () => {
                if (quantityInput.disabled) {
                    return;
                }

                quantityInput.stepDown();
                clampQuantity();
            });

            increaseBtn.addEventListener('click', () => {
                if (quantityInput.disabled) {
                    return;
                }

                quantityInput.stepUp();
                clampQuantity();
            });

            quantityInput.addEventListener('input', clampQuantity);
            quantityInput.addEventListener('change', clampQuantity);

            clampQuantity();
        }

        variantButtons.forEach((button) => {
            button.addEventListener('click', function() {
                variantButtons.forEach((item) => item.classList.remove('is-selected'));
                button.classList.add('is-selected');

                variantIdInput.value = button.dataset.variantId;
                priceEl.textContent = '€' + Number(button.dataset.price).toFixed(2);

                const stock = Number(button.dataset.stock);
                stockEl.textContent = stock > 0 ? `In stock · ${stock} available` : 'Out of stock';

                if (quantityInput) {
                    const maxQuantity = Math.min(stock, 5);
                    quantityInput.max = String(maxQuantity > 0 ? maxQuantity : 1);

                    if (Number(quantityInput.value) > maxQuantity && maxQuantity > 0) {
                        quantityInput.value = String(maxQuantity);
                    }

                    if (Number(quantityInput.value) < 1) {
                        quantityInput.value = '1';
                    }

                    quantityInput.disabled = stock <= 0;

                    if (typeof clampQuantity === 'function') {
                        clampQuantity();
                    }
                }

                const images = JSON.parse(button.dataset.images || '[]');

                if (images.length > 0) {
                    if (mainImage) {
                        mainImage.src = images[0];
                    }

                    renderThumbnails(images);
                } else if (button.dataset.image && mainImage) {
                    mainImage.src = '/' + button.dataset.image.replace(/^\/+/, '');
                }
            });
        });

        if (thumbnailsWrap) {
            thumbnailsWrap.querySelectorAll('.product-thumbnail').forEach((thumb) => {
                thumb.addEventListener('click', function() {
                    const imageUrl = thumb.dataset.imageUrl;

                    if (mainImage) {
                        mainImage.src = imageUrl;
                    }

                    thumbnailsWrap.querySelectorAll('.product-thumbnail').forEach((item) => {
                        item.classList.remove('is-active');
                    });

                    thumb.classList.add('is-active');
                });
            });
        }

        const ratingWidget = document.querySelector('[data-rating-widget]');

        if (ratingWidget) {
            const stars = Array.from(ratingWidget.querySelectorAll('.star-rating-star'));
            const inputs = Array.from(ratingWidget.querySelectorAll('input[name="rating"]'));
            const caption = ratingWidget.querySelector('[data-rating-caption]');

            const labels = {
                1: 'Poor',
                2: 'Fair',
                3: 'Good',
                4: 'Very good',
                5: 'Excellent'
            };

            const getCheckedValue = () => {
                const checked = inputs.find((input) => input.checked);
                return checked ? Number(checked.value) : 0;
            };

            const paintStars = (value) => {
                stars.forEach((star) => {
                    const starValue = Number(star.dataset.ratingValue);
                    star.classList.toggle('is-active', starValue <= value);
                });

                if (caption) {
                    caption.textContent = value > 0 ? labels[value] : 'Select your rating';
                }
            };

            paintStars(getCheckedValue());

            stars.forEach((star) => {
                const value = Number(star.dataset.ratingValue);

                star.addEventListener('mouseenter', () => paintStars(value));
                star.addEventListener('click', () => paintStars(value));
            });

            ratingWidget.querySelector('.star-rating')?.addEventListener('mouseleave', () => {
                paintStars(getCheckedValue());
            });

            inputs.forEach((input) => {
                input.addEventListener('change', () => {
                    paintStars(Number(input.value));
                });
            });
        }

        const editBtn = document.querySelector('.review-edit-toggle');
        const reviewForm = document.getElementById('review-form');

        if (editBtn && reviewForm) {
            editBtn.addEventListener('click', () => {
                reviewForm.classList.toggle('is-hidden');
                reviewForm.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });
        }
    });
</script>