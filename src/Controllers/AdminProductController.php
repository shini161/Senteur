<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AdminProductService;
use RuntimeException;

/**
 * Provides CRUD-style product management for administrators.
 */
class AdminProductController extends Controller
{
    public function __construct(
        private AdminProductService $adminProductService
    ) {}

    // ---------------------------------------------------------------------
    // Listing and form pages
    // ---------------------------------------------------------------------

    /**
     * Lists products in the admin catalogue table.
     */
    public function index(): void
    {
        Auth::requireAdmin();

        $this->render('admin/products/index', [
            'title' => 'Admin Products',
            'products' => $this->adminProductService->getProducts(),
        ]);
    }

    /**
     * Shows the product creation form with metadata needed by the partial.
     */
    public function create(): void
    {
        Auth::requireAdmin();

        $meta = $this->adminProductService->getFormMeta();

        $this->render('admin/products/create', [
            'title' => 'Create Product',
            'error' => null,
            'old' => [
                'variants' => [
                    ['size_ml' => '', 'price' => '', 'stock' => ''],
                    ['size_ml' => '', 'price' => '', 'stock' => ''],
                ],
            ],
            'brands' => $meta['brands'],
            'fragranceTypes' => $meta['fragranceTypes'],
            'genders' => $meta['genders'],
        ]);
    }

    // ---------------------------------------------------------------------
    // Product write actions
    // ---------------------------------------------------------------------

    /**
     * Validates and persists a newly created product.
     */
    public function store(): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        try {
            $productId = $this->adminProductService->create($_POST);

            header('Location: /admin/products/' . $productId . '/edit');
            exit;
        } catch (RuntimeException $e) {
            $meta = $this->adminProductService->getFormMeta();

            $this->render('admin/products/create', [
                'title' => 'Create Product',
                'error' => $e->getMessage(),
                'old' => $_POST,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'genders' => $meta['genders'],
            ]);
        }
    }

    /**
     * Loads a product and renders the edit screen.
     */
    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $product = $this->adminProductService->getProductById((int) $id);

        if ($product === null) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        $meta = $this->adminProductService->getFormMeta();

        $this->render('admin/products/edit', [
            'title' => 'Edit Product',
            'error' => null,
            'imageError' => null,
            'product' => $product,
            'brands' => $meta['brands'],
            'fragranceTypes' => $meta['fragranceTypes'],
            'genders' => $meta['genders'],
        ]);
    }

    /**
     * Saves admin changes to an existing product and its variants.
     */
    public function update(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $productId = (int) $id;

        try {
            $this->adminProductService->update($productId, $_POST);

            header('Location: /admin/products/' . $productId . '/edit');
            exit;
        } catch (RuntimeException $e) {
            $product = $this->adminProductService->getProductById($productId);
            $meta = $this->adminProductService->getFormMeta();

            $fallbackProduct = $product ?? [
                'id' => $productId,
                'brand_id' => $_POST['brand_id'] ?? '',
                'fragrance_type_id' => $_POST['fragrance_type_id'] ?? '',
                'family_name' => $_POST['family_name'] ?? '',
                'name' => $_POST['name'] ?? '',
                'concentration_label' => $_POST['concentration_label'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'description' => $_POST['description'] ?? '',
                'gender' => $_POST['gender'] ?? '',
                'image_url' => $product['image_url'] ?? null,
                'variants' => $_POST['variants'] ?? [],
            ];

            $this->render('admin/products/edit', [
                'title' => 'Edit Product',
                'error' => $e->getMessage(),
                'imageError' => null,
                'product' => $fallbackProduct,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'genders' => $meta['genders'],
            ]);
        }
    }

    // ---------------------------------------------------------------------
    // Media uploads
    // ---------------------------------------------------------------------

    /**
     * Uploads the product-level primary image.
     */
    public function uploadImage(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $productId = (int) $id;

        try {
            $this->adminProductService->uploadPrimaryImage($productId, $_FILES['image'] ?? []);

            header('Location: /admin/products/' . $productId . '/edit');
            exit;
        } catch (RuntimeException $e) {
            $product = $this->adminProductService->getProductById($productId);
            $meta = $this->adminProductService->getFormMeta();

            if ($product === null) {
                http_response_code(404);
                echo 'Product not found';
                return;
            }

            $this->render('admin/products/edit', [
                'title' => 'Edit Product',
                'error' => null,
                'imageError' => $e->getMessage(),
                'product' => $product,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'genders' => $meta['genders'],
            ]);
        }
    }

    /**
     * Uploads the primary image for an individual variant.
     */
    public function uploadVariantImage(string $variantId): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $variantIdInt = (int) $variantId;

        try {
            $this->adminProductService->uploadVariantImage($variantIdInt, $_FILES['image'] ?? []);

            header('Location: /admin/products/' . (int) ($_POST['product_id'] ?? 0) . '/edit');
            exit;
        } catch (RuntimeException $e) {
            $productId = (int) ($_POST['product_id'] ?? 0);
            $product = $this->adminProductService->getProductById($productId);
            $meta = $this->adminProductService->getFormMeta();

            if ($product === null) {
                http_response_code(404);
                echo 'Product not found';
                return;
            }

            $this->render('admin/products/edit', [
                'title' => 'Edit Product',
                'error' => null,
                'imageError' => null,
                'variantImageError' => $e->getMessage(),
                'product' => $product,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'genders' => $meta['genders'],
            ]);
        }
    }
}
