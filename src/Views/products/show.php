<section class="product-show">
    <div class="product-show-gallery">
        <div class="product-show-image-wrap">
            <?php if (!empty($product['image_url'])): ?>
                <img
                    src="/<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <div class="product-placeholder">SENTEUR</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-show-panel">
        <div class="product-meta-line">
            <span class="badge"><?= htmlspecialchars($product['brand_name']) ?></span>

            <?php if (!empty($product['fragrance_type_name'])): ?>
                <span class="badge"><?= htmlspecialchars($product['fragrance_type_name']) ?></span>
            <?php endif; ?>

            <span class="badge"><?= htmlspecialchars(ucfirst($product['gender'])) ?></span>
            <span class="badge"><?= count($product['variants'] ?? []) ?> variants</span>

            <span class="badge">
                <?= $reviewSummary['average_rating'] !== null
                    ? number_format((float) $reviewSummary['average_rating'], 1) . '/5'
                    : 'No rating' ?>
            </span>
        </div>

        <h1><?= htmlspecialchars($product['name']) ?></h1>

        <p class="product-description">
            <?php if (! empty($product['description'])): ?>
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            <?php else: ?>
                No description available yet.
            <?php endif; ?>
        </p>

        <?php if (!empty($product['categories'])): ?>
            <h2>Categories</h2>
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
            <h2>Scent profile</h2>

            <?php if (!empty($product['notes']['top'])): ?>
                <p><strong>Top notes:</strong>
                    <?php foreach ($product['notes']['top'] as $index => $note): ?>
                        <?= $index > 0 ? ', ' : '' ?><?= htmlspecialchars($note['name']) ?>
                    <?php endforeach; ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($product['notes']['middle'])): ?>
                <p><strong>Middle notes:</strong>
                    <?php foreach ($product['notes']['middle'] as $index => $note): ?>
                        <?= $index > 0 ? ', ' : '' ?><?= htmlspecialchars($note['name']) ?>
                    <?php endforeach; ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($product['notes']['base'])): ?>
                <p><strong>Base notes:</strong>
                    <?php foreach ($product['notes']['base'] as $index => $note): ?>
                        <?= $index > 0 ? ', ' : '' ?><?= htmlspecialchars($note['name']) ?>
                    <?php endforeach; ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <h2>Available variants</h2>

        <?php if (empty($product['variants'])): ?>
            <div class="empty-state">
                <p>No variants available.</p>
            </div>
        <?php else: ?>
            <div class="variant-list">
                <?php foreach ($product['variants'] as $variant): ?>
                    <div class="variant-card">
                        <div class="variant-main">
                            <div class="variant-title">
                                <?= htmlspecialchars((string) $variant['size_ml']) ?>ml
                            </div>
                            <div class="variant-meta">
                                €<?= number_format((float) $variant['price'], 2) ?>
                                ·
                                Stock: <?= htmlspecialchars((string) $variant['stock']) ?>
                            </div>
                        </div>

                        <form action="/cart/add" method="POST" class="variant-form">
                            <?= \App\Core\Csrf::input() ?>
                            <input type="hidden" name="variant_id" value="<?= htmlspecialchars((string) $variant['id']) ?>">

                            <input
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                class="qty-input">

                            <button type="submit" class="auth-button">Add to cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

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
                if ($i <= $rating) {
                    $html .= '★';
                } else {
                    $html .= '<span class="empty">★</span>';
                }
            }

            return $html;
        };
        ?>

        <section id="reviews" style="margin-top: 2rem;">
            <h2>Reviews</h2>
            <p class="muted">
                <?= $reviewSummary['review_count'] ?> review(s)
                <?php if ($reviewSummary['average_rating'] !== null): ?>
                    · Average rating: <?= number_format((float) $reviewSummary['average_rating'], 1) ?>/5
                <?php endif; ?>
            </p>

            <?php if ($canReview): ?>
                <div class="panel" style="margin: 1rem 0;">
                    <h2 style="margin-top: 0;"><?= $userReview ? 'Update your review' : 'Write a review' ?></h2>

                    <form method="POST" action="/products/<?= htmlspecialchars($product['slug']) ?>/reviews" class="auth-form">
                        <?= \App\Core\Csrf::input() ?>

                        <div class="form-group">
                            <label>Rating</label>

                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input
                                        type="radio"
                                        id="rating-<?= $i ?>"
                                        name="rating"
                                        value="<?= $i ?>"
                                        <?= (string) ($userReview['rating'] ?? '') === (string) $i ? 'checked' : '' ?>
                                        required>
                                    <label for="rating-<?= $i ?>" title="<?= $i ?>/5">★</label>
                                <?php endfor; ?>
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
                <div class="empty-state" style="margin: 1rem 0;">
                    <p>You can review this perfume after purchasing it.</p>
                </div>
            <?php else: ?>
                <div class="empty-state" style="margin: 1rem 0;">
                    <p><a href="/login">Log in</a> to review perfumes you have purchased.</p>
                </div>
            <?php endif; ?>

            <?php if (empty($reviews)): ?>
                <div class="empty-state">
                    <p>No reviews yet.</p>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap: 1rem;">
                    <?php foreach ($reviews as $review): ?>
                        <article class="panel">
                            <div style="display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                                <strong><?= htmlspecialchars($review['username']) ?></strong>
                                <span class="muted"><?= htmlspecialchars($formatReviewDate($review['created_at'])) ?></span>
                            </div>

                            <p class="review-stars"><?= $renderStars((int) $review['rating']) ?></p>

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
        </section>
    </div>
</section>