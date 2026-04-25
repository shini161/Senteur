<section class="panel admin-product-form-panel">
    <div class="admin-product-panel-heading">
        <div>
            <h2>Fragrance notes</h2>
            <p class="muted">Assign Fragrance Notes, a full top/heart/base pyramid, or both.</p>
        </div>
    </div>

    <?php if ($notes === []): ?>
        <div class="empty-state">
            <h3>No notes in the library yet</h3>
            <p>Create notes first, then come back here to attach them as Fragrance Notes or as staged top, heart, and base notes.</p>
        </div>
    <?php else: ?>
        <div class="admin-product-note-stage-grid">
            <?php foreach ($noteStages as $stageKey => $stage): ?>
                <?php $selectedCount = count($selectedNoteIds[$stageKey]); ?>
                <article class="admin-product-note-stage" data-note-stage>
                    <div class="admin-product-note-stage-header">
                        <div>
                            <h3><?= htmlspecialchars($stage['label']) ?></h3>
                            <p class="muted"><?= htmlspecialchars($stage['description']) ?></p>
                        </div>

                        <span class="badge" data-note-stage-count>
                            <?= $selectedCount === 0 ? 'None selected' : number_format($selectedCount) . ' selected' ?>
                        </span>
                    </div>

                    <div
                        class="catalog-note-filter-group admin-note-picker"
                        data-note-picker
                        data-input-name="note_ids[<?= htmlspecialchars($stageKey) ?>][]"
                        data-notes="<?= $allNotesJson ?>">
                        <div class="catalog-note-selected" data-note-selected>
                            <?php if ($selectedNoteIds[$stageKey] === []): ?>
                                <span class="catalog-note-empty">No notes selected</span>
                            <?php else: ?>
                                <?php foreach ($selectedNoteIds[$stageKey] as $selectedId): ?>
                                    <?php $selectedNote = $findNoteById((int) $selectedId); ?>
                                    <?php if ($selectedNote !== null): ?>
                                        <button
                                            type="button"
                                            class="catalog-note-selected-chip"
                                            data-remove-note="<?= (int) $selectedNote['id'] ?>">
                                            <span><?= htmlspecialchars((string) $selectedNote['name']) ?></span>
                                            <span aria-hidden="true">x</span>
                                        </button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="catalog-note-input-wrap">
                            <input
                                type="text"
                                class="catalog-note-search"
                                data-note-search
                                placeholder="Search notes...">
                            <div class="catalog-note-dropdown" data-note-dropdown></div>
                        </div>

                        <div data-note-inputs>
                            <?php foreach ($selectedNoteIds[$stageKey] as $selectedId): ?>
                                <input
                                    type="hidden"
                                    name="note_ids[<?= htmlspecialchars($stageKey) ?>][]"
                                    value="<?= (int) $selectedId ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
