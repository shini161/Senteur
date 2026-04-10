<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderRepository;
use App\Models\ProductRepository;
use App\Models\ReviewRepository;
use RuntimeException;

/**
 * Handles review visibility and purchase-gated review submission rules.
 */
class ReviewService
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository
    ) {}

    /**
     * Returns everything the product page needs to render reviews and review permissions.
     */
    public function getProductReviewData(int $productId, ?int $userId): array
    {
        return [
            'summary' => $this->reviewRepository->findSummaryByProductId($productId),
            'reviews' => $this->reviewRepository->findByProductId($productId),
            'userReview' => $userId !== null
                ? $this->reviewRepository->findByUserAndProduct($userId, $productId)
                : null,
            'canReview' => $userId !== null
                ? $this->orderRepository->userHasPurchasedProduct($userId, $productId)
                : false,
        ];
    }

    /**
     * Creates or updates the current user's review for a product looked up by slug.
     */
    public function saveByProductSlug(int $userId, string $slug, array $data): void
    {
        $productId = $this->productRepository->findProductIdBySlug($slug);

        if ($productId === null) {
            throw new RuntimeException('Product not found.');
        }

        if (! $this->orderRepository->userHasPurchasedProduct($userId, $productId)) {
            throw new RuntimeException('You can only review products you have purchased.');
        }

        $rating = (int) ($data['rating'] ?? 0);
        $title = trim((string) ($data['title'] ?? ''));
        $comment = trim((string) ($data['comment'] ?? ''));

        if ($rating < 1 || $rating > 5) {
            throw new RuntimeException('Rating must be between 1 and 5.');
        }

        $title = $title !== '' ? $title : null;
        $comment = $comment !== '' ? $comment : null;

        $this->reviewRepository->upsert(
            $userId,
            $productId,
            $rating,
            $title,
            $comment
        );
    }
}
