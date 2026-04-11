<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use PDO;

trait ProductRepositoryCatalogueQueries
{
    /**
     * Returns active products for the public catalogue using dynamic filters.
     */
    public function findAllActive(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $sort = trim((string) ($filters['sort'] ?? 'newest'));

        // Sorting is whitelisted so user input never becomes raw SQL.
        $allowedSorts = [
            'newest' => 'p.id DESC',
            'name_asc' => 'p.name ASC',
            'price_asc' => 'price ASC',
            'price_desc' => 'price DESC',
        ];

        $orderBy = $allowedSorts[$sort] ?? $allowedSorts['newest'];
        ['whereSql' => $whereSql, 'params' => $params] = $this->buildPublicCatalogueFilters($filters);

        $stmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                MIN(v.price) AS price,
                COUNT(v.id) AS variant_count,
                COALESCE(SUM(CASE WHEN v.stock > 0 THEN 1 ELSE 0 END), 0) AS in_stock_variant_count,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_variants v ON v.product_id = p.id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE {$whereSql}
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name,
                ft.name,
                pi.image_url
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $this->markProductsSellable($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Returns the full product detail payload used on the product page.
     */
    public function findActiveBySlug(string $slug): ?array
    {
        $productStmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.family_name,
                p.name,
                p.concentration_label,
                p.slug,
                p.description,
                p.gender,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE p.slug = :slug
              AND p.deleted_at IS NULL
            LIMIT 1
        ");

        $productStmt->execute([
            'slug' => $slug,
        ]);

        $product = $productStmt->fetch(PDO::FETCH_ASSOC);

        if (! $product) {
            return null;
        }

        $variantStmt = $this->pdo->prepare("
            SELECT
                v.id,
                v.size_ml,
                v.price,
                v.stock,
                (
                    SELECT pvi.image_url
                    FROM product_variant_images pvi
                    WHERE pvi.product_variant_id = v.id
                    ORDER BY pvi.position ASC
                    LIMIT 1
                ) AS image_url
            FROM product_variants v
            WHERE v.product_id = :product_id
            ORDER BY v.price ASC
        ");

        $variantStmt->execute([
            'product_id' => $product['id'],
        ]);

        $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

        // Variant-specific image galleries are loaded separately so the page can
        // switch images client-side without extra requests.
        foreach ($product['variants'] as &$variant) {
            $imageStmt = $this->pdo->prepare("
                SELECT
                    id,
                    image_url,
                    position
                FROM product_variant_images
                WHERE product_variant_id = :variant_id
                ORDER BY position ASC, id ASC
            ");

            $imageStmt->execute([
                'variant_id' => $variant['id'],
            ]);

            $variant['images'] = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($variant['image_url']) && !empty($product['image_url'])) {
                $variant['image_url'] = $product['image_url'];
            }
        }
        unset($variant);

        $categoryStmt = $this->pdo->prepare("
            SELECT
                c.id,
                c.name
            FROM product_categories pc
            INNER JOIN categories c ON c.id = pc.category_id
            WHERE pc.product_id = :product_id
            ORDER BY c.name ASC
        ");

        $categoryStmt->execute([
            'product_id' => $product['id'],
        ]);

        $product['categories'] = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

        $noteStmt = $this->pdo->prepare("
            SELECT
                n.id,
                n.name,
                n.image_url,
                pn.note_type
            FROM product_notes pn
            INNER JOIN notes n ON n.id = pn.note_id
            WHERE pn.product_id = :product_id
            ORDER BY
                FIELD(pn.note_type, 'top', 'middle', 'base'),
                n.name ASC
        ");

        $noteStmt->execute([
            'product_id' => $product['id'],
        ]);

        $notes = $noteStmt->fetchAll(PDO::FETCH_ASSOC);

        $product['notes'] = [
            'top' => [],
            'middle' => [],
            'base' => [],
        ];

        foreach ($notes as $note) {
            $type = $note['note_type'];

            if (isset($product['notes'][$type])) {
                $product['notes'][$type][] = $note;
            }
        }

        $product['related_family_products'] = [];

        if (! empty($product['family_name'])) {
            // Products in the same family line are shown separately from the
            // broader "you may also like" recommendation block.
            $relatedStmt = $this->pdo->prepare("
                SELECT
                    p.id,
                    p.name,
                    p.concentration_label,
                    p.slug,
                    b.name AS brand_name,
                    ft.name AS fragrance_type_name,
                    pi.image_url,
                    MIN(v.price) AS price
                FROM products p
                INNER JOIN brands b ON b.id = p.brand_id
                LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
                LEFT JOIN product_images pi
                    ON pi.product_id = p.id
                    AND pi.position = 0
                LEFT JOIN product_variants v ON v.product_id = p.id
                WHERE p.family_name = :family_name
                  AND p.id != :product_id
                  AND p.deleted_at IS NULL
                GROUP BY
                    p.id,
                    p.name,
                    p.concentration_label,
                    p.slug,
                    b.name,
                    ft.name,
                    pi.image_url
                ORDER BY p.name ASC
            ");

            $relatedStmt->execute([
                'family_name' => $product['family_name'],
                'product_id' => $product['id'],
            ]);

            $product['related_family_products'] = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $relatedProductsStmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                pi.image_url,
                MIN(v.price) AS price,
                (
                    CASE WHEN p.brand_id = (
                        SELECT brand_id FROM products WHERE id = :product_id
                    ) THEN 100 ELSE 0 END
                ) +
                (
                    CASE WHEN COALESCE(p.fragrance_type_id, 0) = COALESCE((
                        SELECT fragrance_type_id FROM products WHERE id = :product_id
                    ), 0) THEN 10 ELSE 0 END
                ) +
                (
                    CASE WHEN p.gender = (
                        SELECT gender FROM products WHERE id = :product_id
                    ) THEN 1 ELSE 0 END
                ) AS relevance_score
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            LEFT JOIN product_variants v ON v.product_id = p.id
            WHERE p.id != :product_id
              AND p.deleted_at IS NULL
              AND (
                p.brand_id = (SELECT brand_id FROM products WHERE id = :product_id)
                OR COALESCE(p.fragrance_type_id, 0) = COALESCE((SELECT fragrance_type_id FROM products WHERE id = :product_id), 0)
                OR p.gender = (SELECT gender FROM products WHERE id = :product_id)
              )
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                b.name,
                ft.name,
                pi.image_url,
                p.brand_id,
                p.fragrance_type_id,
                p.gender
            ORDER BY relevance_score DESC, p.name ASC
            LIMIT 4
        ");

        $relatedProductsStmt->execute([
            'product_id' => $product['id'],
        ]);

        $product['related_products'] = $relatedProductsStmt->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

    /**
     * Returns all metadata needed by the public catalogue filters.
     */
    public function getPublicFilterMeta(): array
    {
        return [
            'brands' => $this->getBrands(),
            'fragranceTypes' => $this->getFragranceTypes(),
            'notes' => $this->getNotes(),
            'genders' => ['male', 'female', 'unisex'],
            'sortOptions' => [
                'newest' => 'Newest',
                'name_asc' => 'Name (A-Z)',
                'price_asc' => 'Price (low to high)',
                'price_desc' => 'Price (high to low)',
            ],
        ];
    }

    /**
     * Returns the newest active products for the home page featured section.
     */
    public function findFeaturedActive(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name AS brand_name,
                ft.name AS fragrance_type_name,
                MIN(v.price) AS price,
                COUNT(v.id) AS variant_count,
                COALESCE(SUM(CASE WHEN v.stock > 0 THEN 1 ELSE 0 END), 0) AS in_stock_variant_count,
                pi.image_url
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            LEFT JOIN product_variants v ON v.product_id = p.id
            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.position = 0
            WHERE p.deleted_at IS NULL
            GROUP BY
                p.id,
                p.name,
                p.concentration_label,
                p.slug,
                p.gender,
                b.name,
                ft.name,
                pi.image_url
            ORDER BY p.id DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $this->markProductsSellable($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Builds the curated home-page collection shortcuts from known note slugs.
     */
    public function findCategoryHighlights(): array
    {
        $noteIds = $this->findNoteIdsBySlugs([
            'bergamot',
            'vanilla',
            'musk',
            'cedarwood',
            'patchouli',
            'lavender',
            'jasmine',
        ]);

        return [
            [
                'title' => 'Fresh Office Scents',
                'description' => 'Clean bergamot and aromatic openings that stay versatile and refined.',
                'query' => '?top_note_ids[]=' . ($noteIds['bergamot'] ?? 0) . '&sort=price_asc',
            ],
            [
                'title' => 'Dark Seductive',
                'description' => 'Richer vanilla, musk, spice, and amber profiles with more presence.',
                'query' => '?middle_note_ids[]=' . ($noteIds['vanilla'] ?? 0)
                    . '&base_note_ids[]=' . ($noteIds['musk'] ?? 0)
                    . '&sort=price_desc',
            ],
            [
                'title' => 'Woody Luxury',
                'description' => 'Structured woody compositions with cedar, patchouli, and upscale depth.',
                'query' => '?middle_note_ids[]=' . ($noteIds['cedarwood'] ?? 0)
                    . '&base_note_ids[]=' . ($noteIds['patchouli'] ?? 0)
                    . '&sort=price_desc',
            ],
            [
                'title' => 'Soft Floral',
                'description' => 'Lavender and jasmine compositions with a smoother elegant character.',
                'query' => '?top_note_ids[]=' . ($noteIds['lavender'] ?? 0)
                    . '&middle_note_ids[]=' . ($noteIds['jasmine'] ?? 0)
                    . '&sort=newest',
            ],
        ];
    }

    /**
     * Finds a public product id by its slug.
     */
    public function findProductIdBySlug(string $slug): ?int
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM products
            WHERE slug = :slug
              AND deleted_at IS NULL
            LIMIT 1
        ");

        $stmt->execute([
            'slug' => $slug,
        ]);

        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    /**
     * Counts active products matching the public catalogue filters.
     */
    public function countAllActive(array $filters = []): int
    {
        ['whereSql' => $whereSql, 'params' => $params] = $this->buildPublicCatalogueFilters($filters);

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM products p
            INNER JOIN brands b ON b.id = p.brand_id
            LEFT JOIN fragrance_types ft ON ft.id = p.fragrance_type_id
            WHERE {$whereSql}
        ");

        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Builds the shared filter SQL used by listing and pagination counts.
     *
     * @return array{whereSql: string, params: array<string, int|string>}
     */
    private function buildPublicCatalogueFilters(array $filters): array
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $brandId = (int) ($filters['brand_id'] ?? 0);
        $fragranceTypeId = (int) ($filters['fragrance_type_id'] ?? 0);
        $gender = trim((string) ($filters['gender'] ?? ''));

        $topNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['top_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $middleNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['middle_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $baseNoteIds = array_values(array_filter(
            array_map('intval', (array) ($filters['base_note_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        $conditions = ['p.deleted_at IS NULL'];
        $params = [];

        if ($search !== '') {
            // Split free-text searches into tokens so multi-word queries remain flexible.
            $tokens = preg_split('/\s+/', mb_strtolower($search), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($tokens as $index => $token) {
                $paramKey = 'search_' . $index;

                $conditions[] = "(
                    LOWER(p.name) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.family_name, '')) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.concentration_label, '')) LIKE :{$paramKey}
                    OR LOWER(COALESCE(p.description, '')) LIKE :{$paramKey}
                    OR LOWER(b.name) LIKE :{$paramKey}
                    OR LOWER(COALESCE(ft.name, '')) LIKE :{$paramKey}
                )";

                $params[$paramKey] = '%' . $token . '%';
            }
        }

        if ($brandId > 0) {
            $conditions[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = $brandId;
        }

        if ($fragranceTypeId > 0) {
            $conditions[] = 'p.fragrance_type_id = :fragrance_type_id';
            $params['fragrance_type_id'] = $fragranceTypeId;
        }

        if (in_array($gender, ['male', 'female', 'unisex'], true)) {
            $conditions[] = 'p.gender = :gender';
            $params['gender'] = $gender;
        }

        $this->appendNoteFilterCondition($conditions, $params, 'top', $topNoteIds);
        $this->appendNoteFilterCondition($conditions, $params, 'middle', $middleNoteIds);
        $this->appendNoteFilterCondition($conditions, $params, 'base', $baseNoteIds);

        return [
            'whereSql' => implode(' AND ', $conditions),
            'params' => $params,
        ];
    }

    /**
     * Appends an EXISTS note filter clause for a specific accord stage.
     *
     * @param array<int, string> $conditions
     * @param array<string, int|string> $params
     * @param list<int> $noteIds
     */
    private function appendNoteFilterCondition(array &$conditions, array &$params, string $type, array $noteIds): void
    {
        if ($noteIds === []) {
            return;
        }

        $placeholders = [];

        foreach ($noteIds as $index => $noteId) {
            $paramKey = $type . '_note_id_' . $index;
            $placeholders[] = ':' . $paramKey;
            $params[$paramKey] = $noteId;
        }

        $conditions[] = "
            EXISTS (
                SELECT 1
                FROM product_notes pn_{$type}
                WHERE pn_{$type}.product_id = p.id
                  AND pn_{$type}.note_type = '{$type}'
                  AND pn_{$type}.note_id IN (" . implode(', ', $placeholders) . ")
            )
        ";
    }

    /**
     * Annotates catalogue cards with a consistent sellable flag.
     *
     * @param array<int, array<string, mixed>> $products
     * @return array<int, array<string, mixed>>
     */
    private function markProductsSellable(array $products): array
    {
        foreach ($products as &$product) {
            $product['is_sellable'] = (int) $product['in_stock_variant_count'] > 0;
        }

        unset($product);

        return $products;
    }
}
