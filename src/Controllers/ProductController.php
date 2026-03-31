<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService = new ProductService())
    {}

    public function index(): void {
        $products = $this->productService->getAll();

        $this->render('products/index', [
            'title' => 'Products',
            'products' => $products
        ]);

    }
}
