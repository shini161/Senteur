<?php
use App\Support\ProductNotes;

// Shared admin product form partial used by both create and edit screens.
$formData = $product ?? $old ?? [];
$variants = $formData['variants'] ?? [
    ['id' => '', 'size_ml' => '', 'price' => '', 'stock' => ''],
];

if ($variants === []) {
    $variants = [
        ['id' => '', 'size_ml' => '', 'price' => '', 'stock' => ''],
    ];
}

$nextVariantIndex = count($variants);
$isEditing = isset($product['id']);
$selectedNoteIds = [];

foreach (ProductNotes::ORDER as $type) {
    $rawSelectedNoteIds = (array) ($formData['note_ids'][$type] ?? []);

    if ($type === ProductNotes::HEART && $rawSelectedNoteIds === []) {
        $rawSelectedNoteIds = (array) ($formData['note_ids'][ProductNotes::LEGACY_MIDDLE] ?? []);
    }

    $selectedNoteIds[$type] = array_map('intval', $rawSelectedNoteIds);
}

$noteStages = ProductNotes::adminStageMeta();
$allNotesJson = htmlspecialchars(json_encode(array_map(
    static fn (array $note): array => [
        'id' => (int) $note['id'],
        'name' => (string) $note['name'],
    ],
    $notes
)), ENT_QUOTES, 'UTF-8');

$findNoteById = static function (int $noteId) use ($notes): ?array {
    foreach ($notes as $note) {
        if ((int) ($note['id'] ?? 0) === $noteId) {
            return $note;
        }
    }

    return null;
};
?>

<?php if (! empty($error)): ?>
    <div class="message message-error">
        <?= htmlspecialchars((string) $error) ?>
    </div>
<?php endif; ?>

<div class="admin-product-form-stack">
    <section class="panel admin-product-form-panel">
        <div class="admin-product-panel-heading">
            <div>
                <h2>Identity</h2>
                <p class="muted">Core catalog information used across search, product listings, and the detail page.</p>
            </div>

            <?php if ($isEditing): ?>
                <span class="badge">Slug: <?= htmlspecialchars((string) ($formData['slug'] ?? '')) ?></span>
            <?php endif; ?>
        </div>

        <div class="admin-product-field-grid">
            <div class="form-group">
                <label for="brand_id">Brand</label>
                <select id="brand_id" name="brand_id" required>
                    <option value="">Select brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option
                            value="<?= (int) $brand['id'] ?>"
                            <?= (string) ($formData['brand_id'] ?? '') === (string) $brand['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $brand['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fragrance_type_id">Fragrance type</label>
                <select id="fragrance_type_id" name="fragrance_type_id">
                    <option value="">Select type</option>
                    <?php foreach ($fragranceTypes as $type): ?>
                        <option
                            value="<?= (int) $type['id'] ?>"
                            <?= (string) ($formData['fragrance_type_id'] ?? '') === (string) $type['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="family_name">Product line / family</label>
                <input
                    id="family_name"
                    name="family_name"
                    type="text"
                    value="<?= htmlspecialchars((string) ($formData['family_name'] ?? '')) ?>"
                    placeholder="e.g. Les Exclusifs">
            </div>

            <div class="form-group">
                <label for="name">Base name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="<?= htmlspecialchars((string) ($formData['name'] ?? '')) ?>"
                    placeholder="e.g. Bleu de Chanel"
                    data-slug-name
                    required>
            </div>

            <div class="form-group">
                <label for="concentration_label">Concentration / subtitle</label>
                <input
                    id="concentration_label"
                    name="concentration_label"
                    type="text"
                    value="<?= htmlspecialchars((string) ($formData['concentration_label'] ?? '')) ?>"
                    placeholder="e.g. Eau de Parfum"
                    data-slug-concentration>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select gender</option>
                    <?php foreach ($genders as $gender): ?>
                        <option
                            value="<?= htmlspecialchars((string) $gender) ?>"
                            <?= (string) ($formData['gender'] ?? '') === (string) $gender ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst((string) $gender)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group admin-product-field-span-full">
                <div class="form-group-header">
                    <label for="slug">Slug</label>
                    <button type="button" class="button-link admin-product-inline-action" data-slug-generate>
                        Generate from name
                    </button>
                </div>

                <input
                    id="slug"
                    name="slug"
                    type="text"
                    value="<?= htmlspecialchars((string) ($formData['slug'] ?? '')) ?>"
                    placeholder="e.g. bleu-de-chanel-eau-de-parfum"
                    data-slug-target
                    required>
                <p class="admin-field-help">
                    Lowercase letters, numbers, and hyphens only. This becomes the product URL.
                </p>
            </div>

            <div class="form-group admin-product-field-span-full">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="6"
                    placeholder="Write the merchandising copy shown on the product page."><?= htmlspecialchars((string) ($formData['description'] ?? '')) ?></textarea>
            </div>
        </div>
    </section>

    <section class="panel admin-product-form-panel">
        <div class="admin-product-panel-heading">
            <div>
                <h2>Fragrance notes</h2>
                <p class="muted">Assign Fragrance Notes, a full top/heart/base pyramid, or both. The storefront shows the pyramid when staged notes exist and falls back to Fragrance Notes otherwise.</p>
            </div>

            <a href="/admin/notes" class="button-secondary">Manage notes</a>
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

    <section class="panel admin-product-form-panel">
        <div class="admin-product-panel-heading">
            <div>
                <h2>Variants</h2>
                <p class="muted">Each size needs its own price and stock level. Sizes must stay unique within the same product.</p>
            </div>

            <button type="button" class="button-secondary admin-variant-add-button" data-variant-add>
                Add variant
            </button>
        </div>

        <div class="admin-variant-list" data-variant-list data-next-index="<?= $nextVariantIndex ?>">
            <?php foreach ($variants as $index => $variant): ?>
                <article class="admin-variant-card" data-variant-card>
                    <div class="admin-variant-card-header">
                        <div>
                            <strong data-variant-title>
                                <?php if (! empty($variant['size_ml'])): ?>
                                    <?= htmlspecialchars((string) $variant['size_ml']) ?> ml variant
                                <?php else: ?>
                                    Variant <?= $index + 1 ?>
                                <?php endif; ?>
                            </strong>
                            <span class="admin-product-table-submeta">Pricing, stock, and size-specific setup.</span>
                        </div>

                        <button type="button" class="button-link admin-variant-remove-button" data-variant-remove>
                            Remove
                        </button>
                    </div>

                    <input
                        type="hidden"
                        name="variants[<?= $index ?>][id]"
                        value="<?= htmlspecialchars((string) ($variant['id'] ?? '')) ?>">

                    <div class="admin-product-variant-grid">
                        <div class="form-group">
                            <label for="variant-<?= $index ?>-size">Size (ml)</label>
                            <input
                                id="variant-<?= $index ?>-size"
                                type="number"
                                min="1"
                                name="variants[<?= $index ?>][size_ml]"
                                placeholder="50"
                                value="<?= htmlspecialchars((string) ($variant['size_ml'] ?? '')) ?>"
                                data-variant-size
                                required>
                        </div>

                        <div class="form-group">
                            <label for="variant-<?= $index ?>-price">Price</label>
                            <input
                                id="variant-<?= $index ?>-price"
                                type="number"
                                min="0"
                                step="0.01"
                                name="variants[<?= $index ?>][price]"
                                placeholder="129.00"
                                value="<?= htmlspecialchars((string) ($variant['price'] ?? '')) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="variant-<?= $index ?>-stock">Stock</label>
                            <input
                                id="variant-<?= $index ?>-stock"
                                type="number"
                                min="0"
                                name="variants[<?= $index ?>][stock]"
                                placeholder="12"
                                value="<?= htmlspecialchars((string) ($variant['stock'] ?? '')) ?>"
                                required>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <template data-variant-template>
            <article class="admin-variant-card" data-variant-card>
                <div class="admin-variant-card-header">
                    <div>
                        <strong data-variant-title>New variant</strong>
                        <span class="admin-product-table-submeta">Pricing, stock, and size-specific setup.</span>
                    </div>

                    <button type="button" class="button-link admin-variant-remove-button" data-variant-remove>
                        Remove
                    </button>
                </div>

                <input type="hidden" name="variants[__INDEX__][id]" value="">

                <div class="admin-product-variant-grid">
                    <div class="form-group">
                        <label for="variant-__INDEX__-size">Size (ml)</label>
                        <input
                            id="variant-__INDEX__-size"
                            type="number"
                            min="1"
                            name="variants[__INDEX__][size_ml]"
                            placeholder="50"
                            data-variant-size
                            required>
                    </div>

                    <div class="form-group">
                        <label for="variant-__INDEX__-price">Price</label>
                        <input
                            id="variant-__INDEX__-price"
                            type="number"
                            min="0"
                            step="0.01"
                            name="variants[__INDEX__][price]"
                            placeholder="129.00"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="variant-__INDEX__-stock">Stock</label>
                        <input
                            id="variant-__INDEX__-stock"
                            type="number"
                            min="0"
                            name="variants[__INDEX__][stock]"
                            placeholder="12"
                            required>
                    </div>
                </div>
            </article>
        </template>
    </section>
</div>
