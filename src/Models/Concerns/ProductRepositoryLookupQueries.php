<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use PDO;

trait ProductRepositoryLookupQueries
{
    /**
     * Returns all brands used in select inputs.
     */
    public function getBrands(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, name
            FROM brands
            ORDER BY name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all notes used in filters and product details.
     */
    public function getNotes(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, name, slug, image_url
            FROM notes
            ORDER BY name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all fragrance types used by the catalogue.
     */
    public function getFragranceTypes(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, name
            FROM fragrance_types
            ORDER BY name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Maps note names to ids for any internal helper flows that need them.
     */
    public function findNoteIdsByNames(array $names): array
    {
        if ($names === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($names), '?'));

        $stmt = $this->pdo->prepare("
            SELECT id, name
            FROM notes
            WHERE LOWER(name) IN ($placeholders)
        ");

        $names = array_map('mb_strtolower', $names);

        $stmt->execute($names);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[$row['name']] = (int) $row['id'];
        }

        return $mapped;
    }

    /**
     * Maps note slugs to ids for curated query generation.
     */
    public function findNoteIdsBySlugs(array $slugs): array
    {
        if ($slugs === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($slugs), '?'));

        $stmt = $this->pdo->prepare("
            SELECT id, slug
            FROM notes
            WHERE slug IN ($placeholders)
        ");

        $stmt->execute($slugs);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[$row['slug']] = (int) $row['id'];
        }

        return $mapped;
    }

    /**
     * Returns the subset of note ids that currently exist.
     *
     * @param int[] $noteIds
     * @return int[]
     */
    public function findExistingNoteIds(array $noteIds): array
    {
        $noteIds = array_values(array_unique(array_filter(array_map('intval', $noteIds), static fn (int $id): bool => $id > 0)));

        if ($noteIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($noteIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT id
            FROM notes
            WHERE id IN ($placeholders)
        ");

        $stmt->execute($noteIds);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
