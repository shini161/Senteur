<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductRepository;

/**
 * Coordinates read-only catalogue queries for public pages.
 */
class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Returns public products using normalized filter input.
     */
    public function getAll(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        return $this->productRepository->findAllActive($filters, $limit, $offset);
    }

    /**
     * Returns the newest active products used on the home page.
     */
    public function getFeatured(int $limit = 4): array
    {
        return $this->productRepository->findFeaturedActive($limit);
    }

    /**
     * Returns curated home-page collections backed by note-based queries.
     */
    public function getHomeCollections(): array
    {
        return $this->productRepository->findCategoryHighlights();
    }

    /**
     * Looks up a single product that is safe to show publicly.
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->productRepository->findActiveBySlug($slug);
    }

    /**
     * Returns metadata needed to render public filter controls.
     */
    public function getPublicFilterMeta(): array
    {
        return $this->productRepository->getPublicFilterMeta();
    }

    /**
     * Converts raw query-string input into a predictable filter payload.
     */
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
            'heart_note_ids' => array_values(array_filter(
                array_map('intval', (array) ($input['heart_note_ids'] ?? ($input['middle_note_ids'] ?? []))),
                static fn(int $id): bool => $id > 0
            )),
            'base_note_ids' => array_values(array_filter(
                array_map('intval', (array) ($input['base_note_ids'] ?? [])),
                static fn(int $id): bool => $id > 0
            )),
        ];
    }

    /**
     * Convenience lookup used by review and product flows.
     */
    public function getIdBySlug(string $slug): ?int
    {
        return $this->productRepository->findProductIdBySlug($slug);
    }

    /**
     * Returns the total number of public products matching the current filters.
     */
    public function countAll(array $filters = []): int
    {
        return $this->productRepository->countAllActive($filters);
    }
}
