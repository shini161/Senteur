<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Services\ProductService;
use App\Services\ReviewService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private ReviewService $reviewService
    ) {}

    public function index(): void
    {
        $filters = $this->productService->normalizePublicFilters($_GET);
        $products = $this->productService->getAll($filters);
        $meta = $this->productService->getPublicFilterMeta();

        $this->render('products/index', [
            'title' => 'Products',
            'products' => $products,
            'filters' => $filters,
            'brands' => $meta['brands'],
            'fragranceTypes' => $meta['fragranceTypes'],
            'genders' => $meta['genders'],
            'sortOptions' => $meta['sortOptions'],
        ]);
    }

    public function show(string $slug): void
    {
        $slug = trim($slug);

        if ($slug === '') {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        $product = $this->productService->getBySlug($slug);

        if ($product === null) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        $reviewData = $this->reviewService->getProductReviewData(
            (int) $product['id'],
            Auth::id()
        );

        $this->render('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'reviewSummary' => $reviewData['summary'],
            'reviews' => $reviewData['reviews'],
            'userReview' => $reviewData['userReview'],
            'canReview' => $reviewData['canReview'],
        ]);
    }
}
