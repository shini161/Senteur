<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(): void
    {
        $products = $this->productService->getAll();

        $this->render('products/index', [
            'title' => 'Products',
            'products' => $products
        ]);
    }

    public function show(string $id): void
    {
        // Basic validation: only numeric ids allowed
        if (! ctype_digit($id)) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        $product = $this->productService->getById((int) $id);

        if ($product === null) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        $this->render('products/show', [
            'title' => $product['name'],
            'product' => $product
        ]);
    }
}
