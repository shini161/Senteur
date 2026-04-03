<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductRepository;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function getAll(): array
    {
        return $this->productRepository->findAllActive();
    }

    public function getById(int $id): ?array
    {
        return $this->productRepository->findActiveById($id);
    }
}
