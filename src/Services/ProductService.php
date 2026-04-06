<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductRepository;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function getAll(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        return $this->productRepository->findAllActive($filters, $limit, $offset);
    }

    public function getFeatured(int $limit = 4): array
    {
        return $this->productRepository->findFeaturedActive($limit);
    }

    public function getHomeCollections(): array
    {
        return $this->productRepository->findCategoryHighlights();
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->productRepository->findActiveBySlug($slug);
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
            'top_note_ids' => array_values(array_filter(
                array_map('intval', (array) ($input['top_note_ids'] ?? [])),
                static fn(int $id): bool => $id > 0
            )),
            'middle_note_ids' => array_values(array_filter(
                array_map('intval', (array) ($input['middle_note_ids'] ?? [])),
                static fn(int $id): bool => $id > 0
            )),
            'base_note_ids' => array_values(array_filter(
                array_map('intval', (array) ($input['base_note_ids'] ?? [])),
                static fn(int $id): bool => $id > 0
            )),
        ];
    }

    public function getIdBySlug(string $slug): ?int
    {
        return $this->productRepository->findProductIdBySlug($slug);
    }

    public function countAll(array $filters = []): int
    {
        return $this->productRepository->countAllActive($filters);
    }
}
