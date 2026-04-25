<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductRepository;
use RuntimeException;

/**
 * Handles admin management of reusable catalogue lookup data.
 */
class AdminCatalogService
{
    private const PER_PAGE = 10;

    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Returns the data needed by the catalog settings workspace.
     *
     * @return array{
     *   brands: array<int, array<string, mixed>>,
     *   fragranceTypes: array<int, array<string, mixed>>,
     *   genders: array<int, array{value: string, label: string, product_count: int}>
     * }
     */
    public function getIndexData(array $query = []): array
    {
        $brandFilters = [
            'q' => trim((string) ($query['brand_q'] ?? '')),
            'page' => max(1, (int) ($query['brand_page'] ?? 1)),
        ];

        $typeFilters = [
            'q' => trim((string) ($query['type_q'] ?? '')),
            'page' => max(1, (int) ($query['type_page'] ?? 1)),
        ];

        $totalBrands = $this->productRepository->countBrandsForAdmin($brandFilters);
        $totalBrandPages = max(1, (int) ceil($totalBrands / self::PER_PAGE));
        $brandPage = min($brandFilters['page'], $totalBrandPages);

        $totalTypes = $this->productRepository->countFragranceTypesForAdmin($typeFilters);
        $totalTypePages = max(1, (int) ceil($totalTypes / self::PER_PAGE));
        $typePage = min($typeFilters['page'], $totalTypePages);

        return [
            'brands' => $this->productRepository->findPageBrandsForAdmin(
                $brandFilters,
                self::PER_PAGE,
                ($brandPage - 1) * self::PER_PAGE
            ),
            'fragranceTypes' => $this->productRepository->findPageFragranceTypesForAdmin(
                $typeFilters,
                self::PER_PAGE,
                ($typePage - 1) * self::PER_PAGE
            ),
            'genders' => $this->productRepository->getAdminGenderStats(),
            'brandFilters' => $brandFilters,
            'fragranceTypeFilters' => $typeFilters,
            'brandPagination' => [
                'currentPage' => $brandPage,
                'totalPages' => $totalBrandPages,
                'total' => $totalBrands,
            ],
            'fragranceTypePagination' => [
                'currentPage' => $typePage,
                'totalPages' => $totalTypePages,
                'total' => $totalTypes,
            ],
        ];
    }

    /**
     * Returns one brand for editing.
     */
    public function getBrandById(int $id): ?array
    {
        return $this->productRepository->findBrandById($id);
    }

    /**
     * Returns one fragrance type for editing.
     */
    public function getFragranceTypeById(int $id): ?array
    {
        return $this->productRepository->findFragranceTypeById($id);
    }

    /**
     * Validates and creates a brand.
     */
    public function createBrand(array $data): int
    {
        $name = $this->normalizeBrandName($data);

        if ($this->productRepository->brandNameExists($name)) {
            throw new RuntimeException('A brand with this name already exists.');
        }

        return $this->productRepository->createBrand($name);
    }

    /**
     * Validates and updates a brand.
     */
    public function updateBrand(int $id, array $data): void
    {
        if (! $this->productRepository->brandExists($id)) {
            throw new RuntimeException('Brand not found.');
        }

        $name = $this->normalizeBrandName($data);

        if ($this->productRepository->brandNameExists($name, $id)) {
            throw new RuntimeException('A brand with this name already exists.');
        }

        $this->productRepository->updateBrand($id, $name);
    }

    /**
     * Deletes a brand when it is no longer referenced by products.
     */
    public function deleteBrand(int $id): void
    {
        if (! $this->productRepository->brandExists($id)) {
            throw new RuntimeException('Brand not found.');
        }

        if ($this->productRepository->countProductsUsingBrand($id) > 0) {
            throw new RuntimeException('This brand is still attached to one or more products.');
        }

        $this->productRepository->deleteBrand($id);
    }

    /**
     * Validates and creates a fragrance type.
     */
    public function createFragranceType(array $data): int
    {
        $name = $this->normalizeFragranceTypeName($data);

        if ($this->productRepository->fragranceTypeNameExists($name)) {
            throw new RuntimeException('A fragrance type with this name already exists.');
        }

        return $this->productRepository->createFragranceType($name);
    }

    /**
     * Validates and updates a fragrance type.
     */
    public function updateFragranceType(int $id, array $data): void
    {
        if (! $this->productRepository->fragranceTypeExists($id)) {
            throw new RuntimeException('Fragrance type not found.');
        }

        $name = $this->normalizeFragranceTypeName($data);

        if ($this->productRepository->fragranceTypeNameExists($name, $id)) {
            throw new RuntimeException('A fragrance type with this name already exists.');
        }

        $this->productRepository->updateFragranceType($id, $name);
    }

    /**
     * Deletes a fragrance type when it is no longer referenced by products.
     */
    public function deleteFragranceType(int $id): void
    {
        if (! $this->productRepository->fragranceTypeExists($id)) {
            throw new RuntimeException('Fragrance type not found.');
        }

        if ($this->productRepository->countProductsUsingFragranceType($id) > 0) {
            throw new RuntimeException('This fragrance type is still attached to one or more products.');
        }

        $this->productRepository->deleteFragranceType($id);
    }

    /**
     * Normalizes a brand payload.
     */
    private function normalizeBrandName(array $data): string
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new RuntimeException('Brand name is required.');
        }

        if (mb_strlen($name) > 100) {
            throw new RuntimeException('Brand name must be 100 characters or fewer.');
        }

        return $name;
    }

    /**
     * Normalizes a fragrance type payload.
     */
    private function normalizeFragranceTypeName(array $data): string
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new RuntimeException('Fragrance type name is required.');
        }

        if (mb_strlen($name) > 50) {
            throw new RuntimeException('Fragrance type name must be 50 characters or fewer.');
        }

        return $name;
    }
}
