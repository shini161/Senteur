<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProductService;

/**
 * Renders the storefront landing page.
 */
class HomeController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Shows featured products and prebuilt discovery collections.
     */
    public function index(): void
    {
        $featuredProducts = $this->productService->getFeatured(4);
        $collections = $this->productService->getHomeCollections();

        $this->render('home/index', [
            'title' => 'Home',
            'featuredProducts' => $featuredProducts,
            'collections' => $collections,
        ]);
    }
}
