<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use PDO;

trait ProductRepositoryTaxonomyQueries
{
    /**
     * Returns admin-facing brand rows with product usage counts.
     */
    public function findAllBrandsForAdmin(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                b.id,
                b.name,
                COUNT(DISTINCT p.id) AS product_count
            FROM brands b
            LEFT JOIN products p ON p.brand_id = b.id
            GROUP BY b.id, b.name
            ORDER BY b.name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns one brand row for editing.
     */
    public function findBrandById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                b.id,
                b.name,
                COUNT(DISTINCT p.id) AS product_count
            FROM brands b
            LEFT JOIN products p ON p.brand_id = b.id
            WHERE b.id = :id
            GROUP BY b.id, b.name
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        $brand = $stmt->fetch(PDO::FETCH_ASSOC);

        return $brand ?: null;
    }

    /**
     * Returns whether a brand currently exists.
     */
    public function brandExists(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM brands
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Returns whether a brand name already exists for another row.
     */
    public function brandNameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "
            SELECT 1
            FROM brands
            WHERE LOWER(name) = LOWER(:name)
        ";

        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Creates a brand and returns its identifier.
     */
    public function createBrand(string $name): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO brands (
                name
            ) VALUES (
                :name
            )
        ");

        $stmt->execute([
            'name' => $name,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Updates an existing brand name.
     */
    public function updateBrand(int $id, string $name): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE brands
            SET name = :name
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'name' => $name,
        ]);
    }

    /**
     * Deletes a brand.
     */
    public function deleteBrand(int $id): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM brands
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
        ]);
    }

    /**
     * Counts products linked to a given brand.
     */
    public function countProductsUsingBrand(int $id): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM products
            WHERE brand_id = :id
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Returns admin-facing fragrance type rows with product usage counts.
     */
    public function findAllFragranceTypesForAdmin(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                ft.id,
                ft.name,
                COUNT(DISTINCT p.id) AS product_count
            FROM fragrance_types ft
            LEFT JOIN products p ON p.fragrance_type_id = ft.id
            GROUP BY ft.id, ft.name
            ORDER BY ft.name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns one fragrance type row for editing.
     */
    public function findFragranceTypeById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                ft.id,
                ft.name,
                COUNT(DISTINCT p.id) AS product_count
            FROM fragrance_types ft
            LEFT JOIN products p ON p.fragrance_type_id = ft.id
            WHERE ft.id = :id
            GROUP BY ft.id, ft.name
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        $type = $stmt->fetch(PDO::FETCH_ASSOC);

        return $type ?: null;
    }

    /**
     * Returns whether a fragrance type exists.
     */
    public function fragranceTypeExists(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM fragrance_types
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Returns whether a fragrance type name already exists for another row.
     */
    public function fragranceTypeNameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "
            SELECT 1
            FROM fragrance_types
            WHERE LOWER(name) = LOWER(:name)
        ";

        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Creates a fragrance type and returns its identifier.
     */
    public function createFragranceType(string $name): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO fragrance_types (
                name
            ) VALUES (
                :name
            )
        ");

        $stmt->execute([
            'name' => $name,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Updates an existing fragrance type name.
     */
    public function updateFragranceType(int $id, string $name): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE fragrance_types
            SET name = :name
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'name' => $name,
        ]);
    }

    /**
     * Deletes a fragrance type.
     */
    public function deleteFragranceType(int $id): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM fragrance_types
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
        ]);
    }

    /**
     * Counts products linked to a given fragrance type.
     */
    public function countProductsUsingFragranceType(int $id): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM products
            WHERE fragrance_type_id = :id
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Returns fixed product gender buckets with usage counts.
     *
     * @return array<int, array{value: string, label: string, product_count: int}>
     */
    public function getAdminGenderStats(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                gender,
                COUNT(*) AS product_count
            FROM products
            GROUP BY gender
        ");

        $counts = [
            'male' => 0,
            'female' => 0,
            'unisex' => 0,
        ];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $gender = (string) ($row['gender'] ?? '');

            if (! array_key_exists($gender, $counts)) {
                continue;
            }

            $counts[$gender] = (int) ($row['product_count'] ?? 0);
        }

        $labels = [
            'male' => 'Male',
            'female' => 'Female',
            'unisex' => 'Unisex',
        ];

        return array_map(
            static fn(string $value) => [
                'value' => $value,
                'label' => $labels[$value],
                'product_count' => $counts[$value],
            ],
            array_keys($counts)
        );
    }

    public function findPageBrandsForAdmin(array $filters, int $limit, int $offset): array
    {
        $params = [];
        $where = $this->buildBrandAdminWhere($filters, $params);

        $stmt = $this->pdo->prepare("
        SELECT
            b.id,
            b.name,
            COUNT(DISTINCT p.id) AS product_count
        FROM brands b
        LEFT JOIN products p ON p.brand_id = b.id
        {$where}
        GROUP BY b.id, b.name
        ORDER BY b.name ASC
        LIMIT :limit OFFSET :offset
    ");

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countBrandsForAdmin(array $filters): int
    {
        $params = [];
        $where = $this->buildBrandAdminWhere($filters, $params);

        $stmt = $this->pdo->prepare("
        SELECT COUNT(*)
        FROM brands b
        {$where}
    ");

        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findPageFragranceTypesForAdmin(array $filters, int $limit, int $offset): array
    {
        $params = [];
        $where = $this->buildFragranceTypeAdminWhere($filters, $params);

        $stmt = $this->pdo->prepare("
        SELECT
            ft.id,
            ft.name,
            COUNT(DISTINCT p.id) AS product_count
        FROM fragrance_types ft
        LEFT JOIN products p ON p.fragrance_type_id = ft.id
        {$where}
        GROUP BY ft.id, ft.name
        ORDER BY ft.name ASC
        LIMIT :limit OFFSET :offset
    ");

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFragranceTypesForAdmin(array $filters): int
    {
        $params = [];
        $where = $this->buildFragranceTypeAdminWhere($filters, $params);

        $stmt = $this->pdo->prepare("
        SELECT COUNT(*)
        FROM fragrance_types ft
        {$where}
    ");

        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private function buildBrandAdminWhere(array $filters, array &$params): string
    {
        $q = trim((string) ($filters['q'] ?? ''));

        if ($q === '') {
            return '';
        }

        $params['brand_q'] = '%' . $q . '%';

        return 'WHERE b.name LIKE :brand_q';
    }

    private function buildFragranceTypeAdminWhere(array $filters, array &$params): string
    {
        $q = trim((string) ($filters['q'] ?? ''));

        if ($q === '') {
            return '';
        }

        $params['type_q'] = '%' . $q . '%';

        return 'WHERE ft.name LIKE :type_q';
    }
}
