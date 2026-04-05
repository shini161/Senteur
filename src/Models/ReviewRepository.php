<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class ReviewRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    public function findSummaryByProductId(int $productId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                COUNT(*) AS review_count,
                AVG(rating) AS average_rating
            FROM reviews
            WHERE product_id = :product_id
        ");

        $stmt->execute([
            'product_id' => $productId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'review_count' => 0,
            'average_rating' => null,
        ];

        return [
            'review_count' => (int) ($row['review_count'] ?? 0),
            'average_rating' => $row['average_rating'] !== null
                ? round((float) $row['average_rating'], 1)
                : null,
        ];
    }

    public function findByProductId(int $productId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                r.id,
                r.rating,
                r.title,
                r.comment,
                r.created_at,
                u.username
            FROM reviews r
            INNER JOIN users u ON u.id = r.user_id
            WHERE r.product_id = :product_id
            ORDER BY r.created_at DESC
        ");

        $stmt->execute([
            'product_id' => $productId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserAndProduct(int $userId, int $productId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                user_id,
                product_id,
                rating,
                title,
                comment,
                created_at
            FROM reviews
            WHERE user_id = :user_id
              AND product_id = :product_id
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        return $review ?: null;
    }

    public function upsert(int $userId, int $productId, int $rating, ?string $title, ?string $comment): void
    {
        $existing = $this->findByUserAndProduct($userId, $productId);

        if ($existing === null) {
            $stmt = $this->pdo->prepare("
                INSERT INTO reviews (
                    user_id,
                    product_id,
                    rating,
                    title,
                    comment
                ) VALUES (
                    :user_id,
                    :product_id,
                    :rating,
                    :title,
                    :comment
                )
            ");

            $stmt->execute([
                'user_id' => $userId,
                'product_id' => $productId,
                'rating' => $rating,
                'title' => $title,
                'comment' => $comment,
            ]);

            return;
        }

        $stmt = $this->pdo->prepare("
            UPDATE reviews
            SET
                rating = :rating,
                title = :title,
                comment = :comment
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $existing['id'],
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
        ]);
    }
}
