<?php
// Admin note library with creation/editing on the same workspace.
$editingId = (int) ($editingNote['id'] ?? 0);
$formNote = is_array($formNote ?? null) ? $formNote : [];

$summary = [
    'notes' => count($notes),
    'linked' => 0,
    'unused' => 0,
    'assignments' => 0,
];

foreach ($notes as $note) {
    $productCount = (int) ($note['product_count'] ?? 0);
    $summary['assignments'] += $productCount;

    if ($productCount > 0) {
        $summary['linked']++;
    } else {
        $summary['unused']++;
    }
}

$adminHeaderTitle = 'Fragrance Notes';
$adminHeaderLead = 'Build the note library once, attach imagery, then assign Fragrance Notes as either a flat list or a full pyramid inside each perfume.';
$adminHeaderSection = 'notes';
$adminHeaderClass = 'admin-notes-header';
$adminHeaderActions = [];

$buildPageUrl = static function (int $pageNumber) use ($filters): string {
    $params = $filters;
    $params['page'] = $pageNumber;

    return '/admin/notes?' . http_build_query(array_filter(
        $params,
        static fn ($value) => $value !== '' && $value !== null
    ));
};
$hasActiveFilters = ($filters['q'] ?? '') !== '' || ($filters['usage'] ?? '') !== '';
?>
<section class="admin-notes-page">
    <div class="admin-notes-shell">
        <?php require __DIR__ . '/../_header.php'; ?>

        <div class="admin-notes-stats">
            <div class="card admin-notes-stat">
                <span class="admin-products-stat-label">Matching notes</span>
                <strong><?= number_format((int) $totalNotes) ?></strong>
                <span class="admin-products-stat-note"><?= number_format($summary['notes']) ?> shown on this page</span>
            </div>

            <div class="card admin-notes-stat">
                <span class="admin-products-stat-label">In use</span>
                <strong><?= number_format($summary['linked']) ?></strong>
                <span class="admin-products-stat-note">Notes linked to perfumes on this page</span>
            </div>

            <div class="card admin-notes-stat">
                <span class="admin-products-stat-label">Unused</span>
                <strong><?= number_format($summary['unused']) ?></strong>
                <span class="admin-products-stat-note">Ready to be attached from the product form</span>
            </div>

            <div class="card admin-notes-stat">
                <span class="admin-products-stat-label">Product links</span>
                <strong><?= number_format($summary['assignments']) ?></strong>
                <span class="admin-products-stat-note">Visible note-to-product links on this page</span>
            </div>
        </div>

        <div class="admin-notes-workspace">
            <div class="admin-notes-main">
                <section class="panel admin-notes-list-panel">
                    <div class="admin-product-panel-heading">
                        <div>
                            <h2>Fragrance note library</h2>
                            <p class="muted">Use this as the source of truth for the notes you want available inside perfume editing.</p>
                        </div>

                        <span class="badge">Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?></span>
                    </div>

                    <form method="GET" action="/admin/notes" class="auth-form admin-filter-form admin-note-list-toolbar">
                        <div class="admin-filter-grid">
                            <div class="form-group admin-filter-search">
                                <label for="note-q">Search</label>
                                <input
                                    id="note-q"
                                    type="text"
                                    name="q"
                                    placeholder="Note name, slug, or ID"
                                    value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>">
                            </div>

                            <div class="form-group">
                                <label for="note-usage">Usage</label>
                                <select id="note-usage" name="usage">
                                    <option value="">All notes</option>
                                    <option value="used" <?= ($filters['usage'] ?? '') === 'used' ? 'selected' : '' ?>>In use</option>
                                    <option value="unused" <?= ($filters['usage'] ?? '') === 'unused' ? 'selected' : '' ?>>Unused</option>
                                </select>
                            </div>
                        </div>

                        <div class="admin-filter-actions">
                            <button type="submit" class="auth-button">Apply filters</button>
                            <a href="/admin/notes" class="button-secondary">Reset</a>
                            <span class="muted admin-results-count"><?= number_format((int) $totalNotes) ?> matching notes</span>
                        </div>
                    </form>

                    <?php if ($notes === []): ?>
                        <div class="empty-state">
                            <h3><?= $hasActiveFilters ? 'No matching notes' : 'No notes yet' ?></h3>
                            <p>
                                <?= $hasActiveFilters
                                    ? 'Try a broader search, switch the usage filter, or create a new note on the right.'
                                    : 'Create the first note on the right, then return to product editing to assign it to perfumes.' ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="admin-note-card-list">
                            <?php foreach ($notes as $note): ?>
                                <?php
                                $noteId = (int) $note['id'];
                                $productCount = (int) ($note['product_count'] ?? 0);
                                $isActive = $editingId === $noteId;
                                $linkedProducts = $linkedProductsByNote[$noteId] ?? [];
                                ?>
                                <article class="admin-note-card <?= $isActive ? 'is-active' : '' ?>">
                                    <div class="admin-note-card-media">
                                        <img
                                            src="/<?= htmlspecialchars((string) $note['image_url']) ?>"
                                            alt="<?= htmlspecialchars((string) $note['name']) ?>"
                                            class="admin-note-card-image">
                                    </div>

                                    <div class="admin-note-card-content">
                                        <div class="admin-note-card-top">
                                            <div>
                                                <strong class="admin-note-card-name">
                                                    <?= htmlspecialchars((string) $note['name']) ?>
                                                </strong>
                                                <div class="admin-note-card-slug">
                                                    /<?= htmlspecialchars((string) $note['slug']) ?>
                                                </div>
                                            </div>

                                            <span class="badge">
                                                <?= $productCount === 1 ? '1 perfume' : number_format($productCount) . ' perfumes' ?>
                                            </span>
                                        </div>

                                        <p class="muted admin-note-card-copy">
                                            <?= $productCount > 0
                                                ? 'Available now inside product editing and already linked to one or more perfume note lists.'
                                                : 'Created and ready to be assigned from the product form.' ?>
                                        </p>

                                        <?php if ($linkedProducts !== []): ?>
                                            <div class="admin-note-linked-products">
                                                <span class="admin-note-linked-label">Used by</span>
                                                <div class="admin-note-linked-list">
                                                    <?php foreach ($linkedProducts as $linkedProduct): ?>
                                                        <a
                                                            href="/admin/products/<?= (int) $linkedProduct['id'] ?>/edit"
                                                            class="admin-note-linked-link">
                                                            <?= htmlspecialchars((string) $linkedProduct['name']) ?>
                                                        </a>
                                                    <?php endforeach; ?>

                                                    <?php if ($productCount > count($linkedProducts)): ?>
                                                        <span class="admin-note-linked-more">
                                                            +<?= number_format($productCount - count($linkedProducts)) ?> more
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="admin-note-card-actions">
                                            <a href="/admin/notes?edit=<?= $noteId ?>" class="button-secondary">Edit</a>

                                            <form
                                                method="POST"
                                                action="/admin/notes/<?= $noteId ?>/delete"
                                                class="admin-note-delete-form">
                                                <?= \App\Core\Csrf::input() ?>
                                                <button
                                                    type="submit"
                                                    class="button-danger"
                                                    <?= $productCount > 0 ? 'disabled' : '' ?>>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <?php if (($totalPages ?? 1) > 1): ?>
                            <nav class="admin-pagination admin-note-pagination">
                                <?php if (($currentPage ?? 1) > 1): ?>
                                    <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" class="button-secondary">Previous</a>
                                <?php else: ?>
                                    <span class="button-secondary pagination-disabled">Previous</span>
                                <?php endif; ?>

                                <span class="muted admin-results-count">
                                    Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?>
                                </span>

                                <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
                                    <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" class="button-secondary">Next</a>
                                <?php else: ?>
                                    <span class="button-secondary pagination-disabled">Next</span>
                                <?php endif; ?>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>
            </div>

            <aside class="admin-notes-sidebar">
                <section class="panel admin-notes-form-panel" id="note-editor">
                    <?php if (! empty($error)): ?>
                        <div class="message message-error">
                            <?= htmlspecialchars((string) $error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="admin-product-panel-heading">
                        <div>
                            <h2><?= $editingId > 0 ? 'Edit note' : 'Create note' ?></h2>
                            <p class="muted">
                                <?= $editingId > 0
                                    ? 'Update the note label or swap in a better icon without breaking perfume assignments.'
                                    : 'Add a new note and upload the visual used anywhere this note appears.' ?>
                            </p>
                        </div>

                        <?php if ($editingId > 0): ?>
                            <a href="/admin/notes" class="button-secondary">New note</a>
                        <?php endif; ?>
                    </div>

                    <?php if (! empty($formNote['image_url'])): ?>
                        <img
                            src="/<?= htmlspecialchars((string) $formNote['image_url']) ?>"
                            alt="<?= htmlspecialchars((string) ($formNote['name'] ?? 'Note preview')) ?>"
                            class="admin-note-form-image">
                    <?php else: ?>
                        <div class="admin-note-form-image admin-note-form-image-placeholder">Note image preview</div>
                    <?php endif; ?>

                    <form
                        method="POST"
                        action="<?= $editingId > 0 ? '/admin/notes/' . $editingId : '/admin/notes' ?>"
                        enctype="multipart/form-data"
                        class="admin-note-form">
                        <?= \App\Core\Csrf::input() ?>

                        <div class="form-group">
                            <label for="note-name">Name</label>
                            <input
                                id="note-name"
                                name="name"
                                type="text"
                                maxlength="100"
                                value="<?= htmlspecialchars((string) ($formNote['name'] ?? '')) ?>"
                                placeholder="e.g. Bergamot"
                                required>
                        </div>

                        <?php if (! empty($formNote['slug'])): ?>
                            <div class="form-group">
                                <label>Slug</label>
                                <div class="admin-note-slug-preview">/<?= htmlspecialchars((string) $formNote['slug']) ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="note-image">
                                <?= $editingId > 0 ? 'Replace image' : 'Note image' ?>
                            </label>
                            <input
                                id="note-image"
                                name="image"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp"
                                <?= $editingId > 0 ? '' : 'required' ?>>
                            <p class="admin-field-help">PNG, JPG, or WEBP up to 2MB.</p>
                        </div>

                        <div class="admin-note-form-actions">
                            <button type="submit" class="auth-button">
                                <?= $editingId > 0 ? 'Save note' : 'Create note' ?>
                            </button>
                        </div>
                    </form>
                </section>

                <section class="panel admin-notes-sidebar-panel">
                    <h2>Using notes in perfumes</h2>
                    <p class="muted">
                        After a note exists here, open any product and assign it to Fragrance Notes or into top, heart, and base pyramid stages.
                    </p>
                </section>
            </aside>
        </div>
    </div>
</section>
