<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductRepository;
use App\Support\ProductNotes;
use RuntimeException;

/**
 * Encapsulates admin catalogue creation, editing, and media upload rules.
 */
class AdminProductService
{
    private const PER_PAGE = 12;

    private const SORT_OPTIONS = [
        'newest' => 'Newest',
        'name_asc' => 'Name A-Z',
        'price_asc' => 'Price low-high',
        'price_desc' => 'Price high-low',
        'stock_asc' => 'Stock low-high',
        'stock_desc' => 'Stock high-low',
    ];

    public function __construct(
        private ProductRepository $productRepository
    ) {}

    // ---------------------------------------------------------------------
    // Read operations
    // ---------------------------------------------------------------------

    /**
     * Returns all products visible in the admin catalogue table.
     */
    public function getProductListData(array $rawFilters): array
    {
        $filters = $this->normalizeListFilters($rawFilters);
        $totalProducts = $this->productRepository->countForAdmin($filters);
        $totalPages = max(1, (int) ceil($totalProducts / self::PER_PAGE));
        $currentPage = min($filters['page'], $totalPages);

        return [
            'products' => $this->productRepository->findPageForAdmin(
                $filters,
                self::PER_PAGE,
                ($currentPage - 1) * self::PER_PAGE
            ),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'sortOptions' => self::SORT_OPTIONS,
        ];
    }

    /**
     * Loads one product and its variants for the admin edit page.
     */
    public function getProductById(int $id): ?array
    {
        return $this->productRepository->findByIdForAdmin($id);
    }

    /**
     * Returns reusable select options for admin product forms.
     */
    public function getFormMeta(): array
    {
        return [
            'brands' => $this->productRepository->getBrands(),
            'fragranceTypes' => $this->productRepository->getFragranceTypes(),
            'notes' => $this->productRepository->getNotes(),
            'genders' => ['male', 'female', 'unisex'],
        ];
    }

    // ---------------------------------------------------------------------
    // Write operations
    // ---------------------------------------------------------------------

    /**
     * Validates and creates a new product together with its variants.
     */
    public function create(array $data): int
    {
        $normalized = $this->normalizeAndValidate($data);

        if ($this->productRepository->slugExists($normalized['product']['slug'])) {
            throw new RuntimeException('Slug already exists.');
        }

        return $this->productRepository->createForAdmin(
            $normalized['product'],
            $normalized['variants'],
            $normalized['noteAssignments']
        );
    }

    /**
     * Validates and updates an existing product together with its variants.
     */
    public function update(int $id, array $data): void
    {
        $normalized = $this->normalizeAndValidate($data);

        if ($this->productRepository->slugExists($normalized['product']['slug'], $id)) {
            throw new RuntimeException('Slug already exists.');
        }

        $product = $this->productRepository->findByIdForAdmin($id);

        if ($product === null) {
            throw new RuntimeException('Product not found.');
        }

        $this->productRepository->updateForAdmin(
            $id,
            $normalized['product'],
            $normalized['variants'],
            $normalized['noteAssignments']
        );
    }

    // ---------------------------------------------------------------------
    // Media operations
    // ---------------------------------------------------------------------

    /**
     * Stores the primary image for a product after validating upload constraints.
     */
    public function uploadPrimaryImage(int $productId, array $file): void
    {
        $product = $this->productRepository->findByIdForAdmin($productId);

        if ($product === null) {
            throw new RuntimeException('Product not found.');
        }

        $imageUrl = $this->storeUploadedImage('product', $productId, $file);
        $this->productRepository->replacePrimaryImage($productId, $imageUrl);
    }

    /**
     * Stores the primary image for a specific product variant.
     */
    public function uploadVariantImage(int $variantId, array $file): void
    {
        $variant = $this->productRepository->findVariantById($variantId);

        if ($variant === null) {
            throw new RuntimeException('Variant not found.');
        }

        $imageUrl = $this->storeUploadedImage('variant', $variantId, $file);
        $this->productRepository->replaceVariantPrimaryImage($variantId, $imageUrl);
    }

    // ---------------------------------------------------------------------
    // Internal helpers
    // ---------------------------------------------------------------------

    /**
     * Normalizes raw form input and enforces product/variant validation rules.
     *
     * @return array{
     *   product: array<string, mixed>,
     *   variants: array<int, array<string, int|float|null>>,
     *   noteAssignments: array{general: int[], top: int[], heart: int[], base: int[]}
     * }
     */
    private function normalizeAndValidate(array $data): array
    {
        $product = [
            'brand_id' => (int) ($data['brand_id'] ?? 0),
            'fragrance_type_id' => ($data['fragrance_type_id'] ?? '') !== '' ? (int) $data['fragrance_type_id'] : null,
            'family_name' => trim((string) ($data['family_name'] ?? '')),
            'name' => trim((string) ($data['name'] ?? '')),
            'concentration_label' => trim((string) ($data['concentration_label'] ?? '')),
            'slug' => trim((string) ($data['slug'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'gender' => trim((string) ($data['gender'] ?? '')),
        ];

        if ($product['brand_id'] <= 0) {
            throw new RuntimeException('Brand is required.');
        }

        if (! $this->productRepository->brandExists($product['brand_id'])) {
            throw new RuntimeException('Selected brand is no longer available.');
        }

        if ($product['name'] === '') {
            throw new RuntimeException('Product name is required.');
        }

        if ($product['slug'] === '') {
            throw new RuntimeException('Slug is required.');
        }

        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $product['slug'])) {
            throw new RuntimeException('Slug must contain only lowercase letters, numbers, and hyphens.');
        }

        if (! in_array($product['gender'], ['male', 'female', 'unisex'], true)) {
            throw new RuntimeException('Invalid gender.');
        }

        if (
            $product['fragrance_type_id'] !== null
            && ! $this->productRepository->fragranceTypeExists((int) $product['fragrance_type_id'])
        ) {
            throw new RuntimeException('Selected fragrance type is no longer available.');
        }

        $product['family_name'] = $product['family_name'] !== '' ? $product['family_name'] : null;
        $product['concentration_label'] = $product['concentration_label'] !== '' ? $product['concentration_label'] : null;

        $variants = [];

        foreach (($data['variants'] ?? []) as $variant) {
            $variantId = trim((string) ($variant['id'] ?? ''));
            $size = trim((string) ($variant['size_ml'] ?? ''));
            $price = trim((string) ($variant['price'] ?? ''));
            $stock = trim((string) ($variant['stock'] ?? ''));

            if ($size === '' && $price === '' && $stock === '') {
                continue;
            }

            $variants[] = [
                'id' => $variantId !== '' ? (int) $variantId : null,
                'size_ml' => (int) $size,
                'price' => (float) $price,
                'stock' => (int) $stock,
            ];
        }

        if ($variants === []) {
            throw new RuntimeException('At least one variant is required.');
        }

        $noteAssignments = [];

        foreach (ProductNotes::ORDER as $type) {
            $rawNoteIds = (array) ($data['note_ids'][$type] ?? []);

            if ($type === ProductNotes::HEART && $rawNoteIds === []) {
                $rawNoteIds = (array) ($data['note_ids'][ProductNotes::LEGACY_MIDDLE] ?? []);
            }

            $noteAssignments[$type] = $this->normalizeNoteIds($rawNoteIds);
        }

        $submittedNoteIds = [];

        foreach (ProductNotes::ORDER as $type) {
            $submittedNoteIds = array_merge($submittedNoteIds, $noteAssignments[$type]);
        }

        $submittedNoteIds = array_values(array_unique($submittedNoteIds));

        $existingNoteIds = $this->productRepository->findExistingNoteIds($submittedNoteIds);

        if (count($existingNoteIds) !== count($submittedNoteIds)) {
            throw new RuntimeException('One or more selected notes are no longer available.');
        }

        return [
            'product' => $product,
            'variants' => $variants,
            'noteAssignments' => $noteAssignments,
        ];
    }

    /**
     * Returns unique positive note ids from raw checkbox input.
     *
     * @param mixed[] $noteIds
     * @return int[]
     */
    private function normalizeNoteIds(array $noteIds): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $noteIds),
            static fn(int $id): bool => $id > 0
        )));
    }

    /**
     * Normalizes admin product list filters from the query string.
     *
     * @param array<string, mixed> $rawFilters
     * @return array{q: string, gender: string, inventory: string, page: int}
     */
    private function normalizeListFilters(array $rawFilters): array
    {
        $gender = trim((string) ($rawFilters['gender'] ?? ''));
        $inventory = trim((string) ($rawFilters['inventory'] ?? ''));
        $sort = trim((string) ($rawFilters['sort'] ?? 'newest'));

        return [
            'q' => trim((string) ($rawFilters['q'] ?? '')),
            'brand_id' => max(0, (int) ($rawFilters['brand_id'] ?? 0)),
            'fragrance_type_id' => max(0, (int) ($rawFilters['fragrance_type_id'] ?? 0)),
            'gender' => in_array($gender, ['male', 'female', 'unisex'], true) ? $gender : '',
            'inventory' => in_array($inventory, ['in_stock', 'low_stock', 'out_of_stock'], true) ? $inventory : '',
            'sort' => array_key_exists($sort, self::SORT_OPTIONS) ? $sort : 'newest',
            'page' => max(1, (int) ($rawFilters['page'] ?? 1)),
        ];
    }

    /**
     * Validates and moves an uploaded image into the shared product upload directory.
     */
    private function storeUploadedImage(string $prefix, int $entityId, array $file): string
    {
        $tmpPath = $this->validateUploadedImage($file);
        $extension = $this->resolveImageExtension($tmpPath);
        $uploadDir = $this->getProductUploadDirectory();

        // Randomized file names avoid collisions and avoid trusting user-supplied names.
        $safeBaseName = bin2hex(random_bytes(12));
        $filename = $prefix . '-' . $entityId . '-' . $safeBaseName . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException('Failed to save uploaded image.');
        }

        return 'uploads/products/' . $filename;
    }

    /**
     * Validates the shape and size of an uploaded image and returns its temp path.
     */
    private function validateUploadedImage(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Image upload failed.');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');

        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            throw new RuntimeException('Invalid uploaded image.');
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new RuntimeException('Image must be 5MB or smaller.');
        }

        return $tmpPath;
    }

    /**
     * Resolves the file extension from the uploaded file MIME type.
     */
    private function resolveImageExtension(string $tmpPath): string
    {
        $mimeType = mime_content_type($tmpPath) ?: '';

        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.'),
        };
    }

    /**
     * Ensures the shared product upload directory exists and returns its path.
     */
    private function getProductUploadDirectory(): string
    {
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/products';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Failed to create upload directory.');
        }

        return $uploadDir;
    }
}
