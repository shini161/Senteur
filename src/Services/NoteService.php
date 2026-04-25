<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NoteRepository;
use RuntimeException;

/**
 * Handles admin note validation, image uploads, and CRUD rules.
 */
class NoteService
{
    private const PER_PAGE = 8;

    public function __construct(
        private NoteRepository $noteRepository
    ) {}

    /**
     * Returns all notes for the admin workspace.
     */
    public function getAll(): array
    {
        return $this->noteRepository->findAllForAdmin();
    }

    /**
     * Returns paginated note management data for the admin workspace.
     *
     * @param array<string, mixed> $rawFilters
     * @return array{
     *   notes: array<int, array<string, mixed>>,
     *   filters: array{q: string, usage: string, page: int},
     *   currentPage: int,
     *   totalPages: int,
     *   totalNotes: int
     * }
     */
    public function getAdminListData(array $rawFilters): array
    {
        $filters = $this->normalizeListFilters($rawFilters);
        $totalNotes = $this->noteRepository->countForAdmin($filters);
        $totalPages = max(1, (int) ceil($totalNotes / self::PER_PAGE));
        $currentPage = min($filters['page'], $totalPages);
        $pageNotes = $this->noteRepository->findPageForAdmin(
            $filters,
            self::PER_PAGE,
            ($currentPage - 1) * self::PER_PAGE
        );

        return [
            'notes' => $pageNotes,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalNotes' => $totalNotes,
        ];
    }

    /**
     * Returns one note for editing.
     */
    public function getById(int $id): ?array
    {
        return $this->noteRepository->findById($id);
    }

    /**
     * Returns every product currently linked to the given note.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLinkedProducts(int $id): array
    {
        return $this->noteRepository->findProductsForNotes([$id], 1000)[$id] ?? [];
    }

    /**
     * Validates and persists a new note.
     */
    public function create(array $data, array $file): int
    {
        $normalized = $this->normalize($data);

        if ($this->noteRepository->nameExists($normalized['name'])) {
            throw new RuntimeException('A note with this name already exists.');
        }

        if ($this->noteRepository->slugExists($normalized['slug'])) {
            throw new RuntimeException('A note with this slug already exists.');
        }

        $normalized['image_url'] = $this->storeUploadedImage($file);

        return $this->noteRepository->create($normalized);
    }

    /**
     * Validates and updates an existing note.
     */
    public function update(int $id, array $data, array $file): void
    {
        $note = $this->noteRepository->findById($id);

        if ($note === null) {
            throw new RuntimeException('Note not found.');
        }

        $normalized = $this->normalize($data);

        if ($this->noteRepository->nameExists($normalized['name'], $id)) {
            throw new RuntimeException('A note with this name already exists.');
        }

        if ($this->noteRepository->slugExists($normalized['slug'], $id)) {
            throw new RuntimeException('A note with this slug already exists.');
        }

        $normalized['image_url'] = $this->shouldReplaceImage($file)
            ? $this->storeUploadedImage($file)
            : (string) $note['image_url'];

        $this->noteRepository->update($id, $normalized);
    }

    /**
     * Deletes a note when it is no longer attached to any products.
     */
    public function delete(int $id): void
    {
        $note = $this->noteRepository->findById($id);

        if ($note === null) {
            throw new RuntimeException('Note not found.');
        }

        $usageCount = $this->noteRepository->countProductsUsing($id);

        if ($usageCount > 0) {
            throw new RuntimeException('This note is still attached to one or more products.');
        }

        $this->noteRepository->delete($id);
    }

    /**
     * Normalizes the note payload and derives the slug from the name.
     *
     * @return array{name: string, slug: string}
     */
    private function normalize(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new RuntimeException('Note name is required.');
        }

        if (mb_strlen($name) > 100) {
            throw new RuntimeException('Note name must be 100 characters or fewer.');
        }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? '', '-'));

        if ($slug === '') {
            throw new RuntimeException('Note name must contain letters or numbers.');
        }

        return [
            'name' => $name,
            'slug' => $slug,
        ];
    }

    /**
     * Returns whether the request intends to replace the note image.
     */
    private function shouldReplaceImage(array $file): bool
    {
        return ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Validates and stores a note image.
     */
    private function storeUploadedImage(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('A note image is required.');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');

        if ($tmpPath === '' || ! is_uploaded_file($tmpPath)) {
            throw new RuntimeException('Invalid uploaded image.');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new RuntimeException('Note images must be 2MB or smaller.');
        }

        $mimeType = mime_content_type($tmpPath) ?: '';

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.'),
        };

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/notes';

        if (! is_dir($uploadDir) && ! mkdir($uploadDir, 0775, true) && ! is_dir($uploadDir)) {
            throw new RuntimeException('Failed to create note upload directory.');
        }

        $filename = 'note-' . bin2hex(random_bytes(12)) . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (! move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException('Failed to save note image.');
        }

        return 'uploads/notes/' . $filename;
    }

    /**
     * Normalizes list filters from the notes admin query string.
     *
     * @param array<string, mixed> $rawFilters
     * @return array{q: string, usage: string, page: int}
     */
    private function normalizeListFilters(array $rawFilters): array
    {
        $usage = trim((string) ($rawFilters['usage'] ?? ''));

        return [
            'q' => trim((string) ($rawFilters['q'] ?? '')),
            'usage' => in_array($usage, ['used', 'unused'], true) ? $usage : '',
            'page' => max(1, (int) ($rawFilters['page'] ?? 1)),
        ];
    }
}
