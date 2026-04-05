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

    public function show(string $id): void
    {
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
