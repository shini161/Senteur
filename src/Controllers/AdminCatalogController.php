<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AdminCatalogService;
use RuntimeException;

/**
 * Provides admin CRUD actions for reusable catalogue metadata.
 */
class AdminCatalogController extends Controller
{
    public function __construct(
        private AdminCatalogService $adminCatalogService
    ) {}

    /**
     * Shows the catalog settings workspace.
     */
    public function index(): void
    {
        Auth::requireAdmin();

        $brandEditId = isset($_GET['brand_edit']) ? (int) $_GET['brand_edit'] : 0;
        $fragranceEditId = isset($_GET['fragrance_edit']) ? (int) $_GET['fragrance_edit'] : 0;
        $editingBrand = $brandEditId > 0 ? $this->adminCatalogService->getBrandById($brandEditId) : null;
        $editingFragranceType = $fragranceEditId > 0 ? $this->adminCatalogService->getFragranceTypeById($fragranceEditId) : null;

        $this->renderIndex([
            'editingBrand' => $editingBrand,
            'editingFragranceType' => $editingFragranceType,
            'brandForm' => $editingBrand,
            'fragranceTypeForm' => $editingFragranceType,
            'brandError' => $brandEditId > 0 && $editingBrand === null ? 'Brand not found.' : null,
            'fragranceTypeError' => $fragranceEditId > 0 && $editingFragranceType === null ? 'Fragrance type not found.' : null,
        ]);
    }

    /**
     * Creates a brand.
     */
    public function storeBrand(): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        try {
            $brandId = $this->adminCatalogService->createBrand($_POST);

            header('Location: /admin/catalog?brand_edit=' . $brandId . '#brand-editor');
            exit;
        } catch (RuntimeException $e) {
            $this->renderIndex([
                'brandForm' => [
                    'name' => $_POST['name'] ?? '',
                ],
                'brandError' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Updates an existing brand.
     */
    public function updateBrand(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $brandId = (int) $id;

        try {
            $this->adminCatalogService->updateBrand($brandId, $_POST);

            header('Location: /admin/catalog?brand_edit=' . $brandId . '#brand-editor');
            exit;
        } catch (RuntimeException $e) {
            $existingBrand = $this->adminCatalogService->getBrandById($brandId);

            $this->renderIndex([
                'editingBrand' => $existingBrand,
                'brandForm' => [
                    'id' => $brandId,
                    'name' => $_POST['name'] ?? ($existingBrand['name'] ?? ''),
                    'product_count' => $existingBrand['product_count'] ?? 0,
                ],
                'brandError' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Deletes a brand.
     */
    public function deleteBrand(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        try {
            $this->adminCatalogService->deleteBrand((int) $id);

            header('Location: /admin/catalog#brands');
            exit;
        } catch (RuntimeException $e) {
            $existingBrand = $this->adminCatalogService->getBrandById((int) $id);

            $this->renderIndex([
                'editingBrand' => $existingBrand,
                'brandForm' => $existingBrand,
                'brandError' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Creates a fragrance type.
     */
    public function storeFragranceType(): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        try {
            $typeId = $this->adminCatalogService->createFragranceType($_POST);

            header('Location: /admin/catalog?fragrance_edit=' . $typeId . '#fragrance-type-editor');
            exit;
        } catch (RuntimeException $e) {
            $this->renderIndex([
                'fragranceTypeForm' => [
                    'name' => $_POST['name'] ?? '',
                ],
                'fragranceTypeError' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Updates an existing fragrance type.
     */
    public function updateFragranceType(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $typeId = (int) $id;

        try {
            $this->adminCatalogService->updateFragranceType($typeId, $_POST);

            header('Location: /admin/catalog?fragrance_edit=' . $typeId . '#fragrance-type-editor');
            exit;
        } catch (RuntimeException $e) {
            $existingType = $this->adminCatalogService->getFragranceTypeById($typeId);

            $this->renderIndex([
                'editingFragranceType' => $existingType,
                'fragranceTypeForm' => [
                    'id' => $typeId,
                    'name' => $_POST['name'] ?? ($existingType['name'] ?? ''),
                    'product_count' => $existingType['product_count'] ?? 0,
                ],
                'fragranceTypeError' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Deletes a fragrance type.
     */
    public function deleteFragranceType(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        try {
            $this->adminCatalogService->deleteFragranceType((int) $id);

            header('Location: /admin/catalog#fragrance-types');
            exit;
        } catch (RuntimeException $e) {
            $existingType = $this->adminCatalogService->getFragranceTypeById((int) $id);

            $this->renderIndex([
                'editingFragranceType' => $existingType,
                'fragranceTypeForm' => $existingType,
                'fragranceTypeError' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Renders the shared catalog settings workspace.
     *
     * @param array<string, mixed> $data
     */
    private function renderIndex(array $data = []): void
    {
        $indexData = $this->adminCatalogService->getIndexData($_GET);

        $this->render('admin/catalog/index', $data + [
            'title' => 'Admin Catalog Data',
            'brands' => $indexData['brands'],
            'fragranceTypes' => $indexData['fragranceTypes'],
            'genders' => $indexData['genders'],
            'brandFilters' => $indexData['brandFilters'],
            'fragranceTypeFilters' => $indexData['fragranceTypeFilters'],
            'brandPagination' => $indexData['brandPagination'],
            'fragranceTypePagination' => $indexData['fragranceTypePagination'],
            'editingBrand' => null,
            'editingFragranceType' => null,
            'brandForm' => ['name' => ''],
            'fragranceTypeForm' => ['name' => ''],
            'brandError' => null,
            'fragranceTypeError' => null,
        ]);
    }
}
