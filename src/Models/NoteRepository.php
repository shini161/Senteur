<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Repository for admin note management and note usage lookups.
 */
class NoteRepository
{
    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }

    /**
     * Returns all notes together with how many products currently reference them.
     */
    public function findAllForAdmin(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                n.id,
                n.name,
                n.slug,
                n.image_url,
                COUNT(DISTINCT pn.product_id) AS product_count
            FROM notes n
            LEFT JOIN product_notes pn ON pn.note_id = n.id
            GROUP BY
                n.id,
                n.name,
                n.slug,
                n.image_url
            ORDER BY n.name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns a filtered admin notes page.
     *
     * @param array{q?: string, usage?: string} $filters
     */
    public function findPageForAdmin(array $filters, int $limit, int $offset): array
    {
        $params = [];
        ['where' => $whereSql, 'having' => $havingSql] = $this->buildAdminNoteFilterSql($filters, $params);

        $sql = "
            SELECT
                n.id,
                n.name,
                n.slug,
                n.image_url,
                COUNT(DISTINCT pn.product_id) AS product_count
            FROM notes n
            LEFT JOIN product_notes pn ON pn.note_id = n.id
            {$whereSql}
            GROUP BY
                n.id,
                n.name,
                n.slug,
                n.image_url
            {$havingSql}
            ORDER BY n.name ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $name => $value) {
            $stmt->bindValue(':' . $name, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Counts filtered admin notes for pagination.
     *
     * @param array{q?: string, usage?: string} $filters
     */
    public function countForAdmin(array $filters): int
    {
        $params = [];
        ['where' => $whereSql, 'having' => $havingSql] = $this->buildAdminNoteFilterSql($filters, $params);

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM (
                SELECT n.id
                FROM notes n
                LEFT JOIN product_notes pn ON pn.note_id = n.id
                {$whereSql}
                GROUP BY n.id
                {$havingSql}
            ) filtered_notes
        ");

        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Returns linked product previews keyed by note id for the current admin page.
     *
     * @param int[] $noteIds
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function findProductsForNotes(array $noteIds, int $limitPerNote = 4): array
    {
        $noteIds = array_values(array_unique(array_filter(array_map('intval', $noteIds), static fn (int $id): bool => $id > 0)));

        if ($noteIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($noteIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT
                pn.note_id,
                p.id,
                p.name,
                p.slug,
                p.deleted_at
            FROM product_notes pn
            INNER JOIN products p ON p.id = pn.product_id
            WHERE pn.note_id IN ($placeholders)
            GROUP BY
                pn.note_id,
                p.id,
                p.name,
                p.slug,
                p.deleted_at
            ORDER BY pn.note_id ASC, p.name ASC
        ");

        $stmt->execute($noteIds);

        $mapped = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $noteId = (int) ($row['note_id'] ?? 0);

            if ($noteId <= 0) {
                continue;
            }

            $mapped[$noteId] ??= [];

            if (count($mapped[$noteId]) >= $limitPerNote) {
                continue;
            }

            $mapped[$noteId][] = $row;
        }

        return $mapped;
    }

    /**
     * Returns one note by id for editing.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                n.id,
                n.name,
                n.slug,
                n.image_url,
                COUNT(DISTINCT pn.product_id) AS product_count
            FROM notes n
            LEFT JOIN product_notes pn ON pn.note_id = n.id
            WHERE n.id = :id
            GROUP BY
                n.id,
                n.name,
                n.slug,
                n.image_url
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        return $note ?: null;
    }

    /**
     * Returns whether a note name already exists for another record.
     */
    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "
            SELECT 1
            FROM notes
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
     * Returns whether a note slug already exists for another record.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "
            SELECT 1
            FROM notes
            WHERE slug = :slug
        ";

        $params = ['slug' => $slug];

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
     * Creates a note and returns its identifier.
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO notes (
                name,
                slug,
                image_url
            ) VALUES (
                :name,
                :slug,
                :image_url
            )
        ");

        $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'image_url' => $data['image_url'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Updates a note in place.
     */
    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE notes
            SET
                name = :name,
                slug = :slug,
                image_url = :image_url
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'image_url' => $data['image_url'],
        ]);
    }

    /**
     * Returns how many products currently use the note.
     */
    public function countProductsUsing(int $id): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT product_id)
            FROM product_notes
            WHERE note_id = :id
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Deletes a note by id.
     */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM notes
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
        ]);
    }

    /**
     * Builds reusable WHERE and HAVING SQL for admin note list filters.
     *
     * @param array{q?: string, usage?: string} $filters
     * @param array<string, string> $params
     * @return array{where: string, having: string}
     */
    private function buildAdminNoteFilterSql(array $filters, array &$params): array
    {
        $where = [];
        $having = [];
        $query = trim((string) ($filters['q'] ?? ''));
        $usage = trim((string) ($filters['usage'] ?? ''));

        if ($query !== '') {
            $tokens = preg_split('/\s+/', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($tokens as $index => $token) {
                $paramKey = 'search_' . $index;
                $params[$paramKey] = '%' . $token . '%';
                $where[] = "
                    (
                        LOWER(n.name) LIKE :{$paramKey}
                        OR LOWER(n.slug) LIKE :{$paramKey}
                        OR CAST(n.id AS CHAR) LIKE :{$paramKey}
                    )
                ";
            }
        }

        if ($usage === 'used') {
            $having[] = 'COUNT(DISTINCT pn.product_id) > 0';
        } elseif ($usage === 'unused') {
            $having[] = 'COUNT(DISTINCT pn.product_id) = 0';
        }

        return [
            'where' => $where === [] ? '' : 'WHERE ' . implode(' AND ', $where),
            'having' => $having === [] ? '' : 'HAVING ' . implode(' AND ', $having),
        ];
    }
}
