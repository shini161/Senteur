<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductRepository;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function getAll(array $filters = []): array
    {
        return $this->productRepository->findAllActive($filters);
    }

    public function getById(int $id): ?array
    {
        return $this->productRepository->findActiveById($id);
    }

    public function getPublicFilterMeta(): array
    {
        return $this->productRepository->getPublicFilterMeta();
    }

    public function normalizePublicFilters(array $input): array
    {
        return [
            'search' => trim((string) ($input['search'] ?? '')),
            'brand_id' => (int) ($input['brand_id'] ?? 0),
            'fragrance_type_id' => (int) ($input['fragrance_type_id'] ?? 0),
            'gender' => trim((string) ($input['gender'] ?? '')),
            'sort' => trim((string) ($input['sort'] ?? 'newest')),
        ];
    }
}
