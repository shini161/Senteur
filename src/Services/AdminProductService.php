<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductRepository;
use RuntimeException;

class AdminProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function getProducts(): array
    {
        return $this->productRepository->findAllForAdmin();
    }

    public function getProductById(int $id): ?array
    {
        return $this->productRepository->findByIdForAdmin($id);
    }

    public function getFormMeta(): array
    {
        return [
            'brands' => $this->productRepository->getBrands(),
            'fragranceTypes' => $this->productRepository->getFragranceTypes(),
            'genders' => ['male', 'female', 'unisex'],
        ];
    }

    public function create(array $data): int
    {
        $normalized = $this->normalizeAndValidate($data);

        if ($this->productRepository->slugExists($normalized['product']['slug'])) {
            throw new RuntimeException('Slug already exists.');
        }

        return $this->productRepository->createForAdmin(
            $normalized['product'],
            $normalized['variants']
        );
    }

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
            $normalized['variants']
        );
    }

    public function uploadPrimaryImage(int $productId, array $file): void
    {
        $product = $this->productRepository->findByIdForAdmin($productId);

        if ($product === null) {
            throw new RuntimeException('Product not found.');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Image upload failed.');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $originalName = (string) ($file['name'] ?? '');

        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            throw new RuntimeException('Invalid uploaded image.');
        }

        $mimeType = mime_content_type($tmpPath) ?: '';

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };

        if ($extension === null) {
            throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new RuntimeException('Image must be 5MB or smaller.');
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/products';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Failed to create upload directory.');
        }

        $safeBaseName = bin2hex(random_bytes(12));
        $filename = 'product-' . $productId . '-' . $safeBaseName . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException('Failed to save uploaded image.');
        }

        $imageUrl = 'uploads/products/' . $filename;
        $this->productRepository->replacePrimaryImage($productId, $imageUrl);
    }

    private function normalizeAndValidate(array $data): array
    {
        $product = [
            'brand_id' => (int) ($data['brand_id'] ?? 0),
            'fragrance_type_id' => ($data['fragrance_type_id'] ?? '') !== '' ? (int) $data['fragrance_type_id'] : null,
            'name' => trim((string) ($data['name'] ?? '')),
            'slug' => trim((string) ($data['slug'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'gender' => trim((string) ($data['gender'] ?? '')),
        ];

        if ($product['brand_id'] <= 0) {
            throw new RuntimeException('Brand is required.');
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

        $variants = [];

        foreach (($data['variants'] ?? []) as $variant) {
            $size = trim((string) ($variant['size_ml'] ?? ''));
            $price = trim((string) ($variant['price'] ?? ''));
            $stock = trim((string) ($variant['stock'] ?? ''));

            if ($size === '' && $price === '' && $stock === '') {
                continue;
            }

            $variants[] = [
                'size_ml' => (int) $size,
                'price' => (float) $price,
                'stock' => (int) $stock,
            ];
        }

        if ($variants === []) {
            throw new RuntimeException('At least one variant is required.');
        }

        return [
            'product' => $product,
            'variants' => $variants,
        ];
    }
}
