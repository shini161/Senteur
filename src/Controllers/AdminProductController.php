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

        $listData = $this->adminProductService->getProductListData($_GET);

        $this->render('admin/products/index', [
            'title' => 'Admin Products',
            'products' => $listData['products'],
            'filters' => $listData['filters'],
            'currentPage' => $listData['currentPage'],
            'totalPages' => $listData['totalPages'],
            'totalProducts' => $listData['totalProducts'],
            'genders' => ['male', 'female', 'unisex'],
        ]);
    }

    /**
     * Shows the product creation form with metadata needed by the partial.
     */
    public function create(): void
    {
        Auth::requireAdmin();

        $meta = $this->adminProductService->getFormMeta();

        $this->render('admin/products/create', $this->withProductFormEnhancements([
            'title' => 'Create Product',
            'old' => [
                'note_ids' => [
                    'top' => [],
                    'middle' => [],
                    'base' => [],
                ],
                'variants' => [
                    ['id' => '', 'size_ml' => '', 'price' => '', 'stock' => ''],
                ],
            ],
            'brands' => $meta['brands'],
            'fragranceTypes' => $meta['fragranceTypes'],
            'notes' => $meta['notes'],
            'genders' => $meta['genders'],
        ]));
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

            $this->render('admin/products/create', $this->withProductFormEnhancements([
                'title' => 'Create Product',
                'error' => $e->getMessage(),
                'old' => $_POST,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'notes' => $meta['notes'],
                'genders' => $meta['genders'],
            ]));
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

        $this->render('admin/products/edit', $this->withProductFormEnhancements([
            'title' => 'Edit Product',
            'product' => $product,
            'brands' => $meta['brands'],
            'fragranceTypes' => $meta['fragranceTypes'],
            'notes' => $meta['notes'],
            'genders' => $meta['genders'],
        ]));
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
            $fallbackVariants = $this->mergePostedVariantsWithExistingImages(
                (array) ($_POST['variants'] ?? []),
                $product
            );

            $fallbackProduct = [
                'id' => $productId,
                'brand_id' => $_POST['brand_id'] ?? ($product['brand_id'] ?? ''),
                'fragrance_type_id' => $_POST['fragrance_type_id'] ?? ($product['fragrance_type_id'] ?? ''),
                'family_name' => $_POST['family_name'] ?? ($product['family_name'] ?? ''),
                'name' => $_POST['name'] ?? ($product['name'] ?? ''),
                'concentration_label' => $_POST['concentration_label'] ?? ($product['concentration_label'] ?? ''),
                'slug' => $_POST['slug'] ?? ($product['slug'] ?? ''),
                'description' => $_POST['description'] ?? ($product['description'] ?? ''),
                'gender' => $_POST['gender'] ?? ($product['gender'] ?? ''),
                'image_url' => $product['image_url'] ?? null,
                'note_ids' => [
                    'top' => (array) ($_POST['note_ids']['top'] ?? ($product['note_ids']['top'] ?? [])),
                    'middle' => (array) ($_POST['note_ids']['middle'] ?? ($product['note_ids']['middle'] ?? [])),
                    'base' => (array) ($_POST['note_ids']['base'] ?? ($product['note_ids']['base'] ?? [])),
                ],
                'notes' => $product['notes'] ?? ['top' => [], 'middle' => [], 'base' => []],
                'variants' => $fallbackVariants,
            ];

            $this->render('admin/products/edit', $this->withProductFormEnhancements([
                'title' => 'Edit Product',
                'error' => $e->getMessage(),
                'product' => $fallbackProduct,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'notes' => $meta['notes'],
                'genders' => $meta['genders'],
            ]));
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

            $this->render('admin/products/edit', $this->withProductFormEnhancements([
                'title' => 'Edit Product',
                'imageError' => $e->getMessage(),
                'product' => $product,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'notes' => $meta['notes'],
                'genders' => $meta['genders'],
            ]));
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

            $this->render('admin/products/edit', $this->withProductFormEnhancements([
                'title' => 'Edit Product',
                'variantImageError' => $e->getMessage(),
                'product' => $product,
                'brands' => $meta['brands'],
                'fragranceTypes' => $meta['fragranceTypes'],
                'notes' => $meta['notes'],
                'genders' => $meta['genders'],
            ]));
        }
    }

    /**
     * Provides common defaults and enhancement assets for product form pages.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function withProductFormEnhancements(array $data): array
    {
        return $data + [
            'error' => null,
            'imageError' => null,
            'variantImageError' => null,
            'scripts' => ['/assets/js/admin/products-form.js'],
        ];
    }

    /**
     * Keeps existing variant images attached when validation fails and the edit
     * form needs to re-render user-submitted variant rows.
     *
     * @param array<int, array<string, mixed>> $submittedVariants
     * @param array<string, mixed>|null $product
     * @return array<int, array<string, mixed>>
     */
    private function mergePostedVariantsWithExistingImages(array $submittedVariants, ?array $product): array
    {
        if ($product === null || empty($product['variants'])) {
            return $submittedVariants;
        }

        $existingById = [];

        foreach ($product['variants'] as $variant) {
            $variantId = (int) ($variant['id'] ?? 0);

            if ($variantId > 0) {
                $existingById[$variantId] = $variant;
            }
        }

        foreach ($submittedVariants as $index => $variant) {
            $variantId = (int) ($variant['id'] ?? 0);

            if ($variantId > 0 && isset($existingById[$variantId])) {
                $submittedVariants[$index]['images'] = $existingById[$variantId]['images'] ?? [];
            }
        }

        return $submittedVariants;
    }
}
