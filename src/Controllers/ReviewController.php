<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Services\ReviewService;
use RuntimeException;

class ReviewController
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function store(string $slug): void
    {
        Auth::requireAuth();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $userId = Auth::id();

        if ($userId === null) {
            header('Location: /login');
            exit;
        }

        try {
            $this->reviewService->saveByProductSlug($userId, $slug, $_POST);
            header('Location: /products/' . urlencode($slug) . '#reviews');
            exit;
        } catch (RuntimeException $e) {
            http_response_code(400);
            echo $e->getMessage();
        }
    }
}
