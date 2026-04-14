<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\NoteService;
use RuntimeException;

/**
 * Provides admin note listing and CRUD actions.
 */
class AdminNoteController extends Controller
{
    public function __construct(
        private NoteService $noteService
    ) {}

    /**
     * Shows the note management page.
     */
    public function index(): void
    {
        Auth::requireAdmin();

        $editingId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
        $editingNote = $editingId > 0 ? $this->noteService->getById($editingId) : null;

        $this->renderIndex([
            'editingNote' => $editingNote,
            'formNote' => $editingNote,
            'error' => $editingId > 0 && $editingNote === null ? 'Note not found.' : null,
        ]);
    }

    /**
     * Creates a new note.
     */
    public function store(): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        try {
            $noteId = $this->noteService->create($_POST, $_FILES['image'] ?? []);

            header('Location: /admin/notes?edit=' . $noteId);
            exit;
        } catch (RuntimeException $e) {
            $this->renderIndex([
                'formNote' => [
                    'name' => $_POST['name'] ?? '',
                    'slug' => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string) ($_POST['name'] ?? '')) ?? '', '-')),
                    'image_url' => null,
                    'product_count' => 0,
                ],
                'editingNote' => null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Updates an existing note.
     */
    public function update(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $noteId = (int) $id;

        try {
            $this->noteService->update($noteId, $_POST, $_FILES['image'] ?? []);

            header('Location: /admin/notes?edit=' . $noteId);
            exit;
        } catch (RuntimeException $e) {
            $existingNote = $this->noteService->getById($noteId);

            $this->renderIndex([
                'editingNote' => $existingNote,
                'formNote' => [
                    'id' => $noteId,
                    'name' => $_POST['name'] ?? ($existingNote['name'] ?? ''),
                    'slug' => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string) ($_POST['name'] ?? ($existingNote['name'] ?? ''))) ?? '', '-')),
                    'image_url' => $existingNote['image_url'] ?? null,
                    'product_count' => $existingNote['product_count'] ?? 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Deletes an existing note when allowed.
     */
    public function delete(string $id): void
    {
        Auth::requireAdmin();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $noteId = (int) $id;

        try {
            $this->noteService->delete($noteId);

            header('Location: /admin/notes');
            exit;
        } catch (RuntimeException $e) {
            $editingNote = $this->noteService->getById($noteId);

            $this->renderIndex([
                'editingNote' => $editingNote,
                'formNote' => $editingNote,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Renders the shared notes workspace with optional form/error state.
     *
     * @param array<string, mixed> $data
     */
    private function renderIndex(array $data = []): void
    {
        $listData = $this->noteService->getAdminListData($_GET);

        $this->render('admin/notes/index', $data + [
            'title' => 'Admin Notes',
            'notes' => $listData['notes'],
            'filters' => $listData['filters'],
            'currentPage' => $listData['currentPage'],
            'totalPages' => $listData['totalPages'],
            'totalNotes' => $listData['totalNotes'],
            'linkedProductsByNote' => $listData['linkedProductsByNote'],
            'editingNote' => null,
            'formNote' => [
                'name' => '',
                'slug' => '',
                'image_url' => null,
                'product_count' => 0,
            ],
            'error' => null,
        ]);
    }
}
