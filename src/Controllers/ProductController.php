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
        $meta = $this->productService->getPublicFilterMeta();

        $perPage = 12;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $totalProducts = $this->productService->countAll($filters);
        $totalPages = max(1, (int) ceil($totalProducts / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }

        $products = $this->productService->getAll($filters, $perPage, $offset);

        $this->render('products/index', [
            'title' => 'Products',
            'products' => $products,
            'filters' => $filters,
            'brands' => $meta['brands'],
            'fragranceTypes' => $meta['fragranceTypes'],
            'notes' => $meta['notes'],
            'genders' => $meta['genders'],
            'sortOptions' => $meta['sortOptions'],
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
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
