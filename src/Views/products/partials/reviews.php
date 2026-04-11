<div class="panel" id="reviews">
    <?php // Review access is gated by purchase history in the review service. ?>
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
                <?php // The current user's review can be toggled back into edit mode inline. ?>
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
