<?php

use App\Support\ProductNotes;

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
    static fn(array $note): array => [
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
    <?php require __DIR__ . '/_form_identity.php'; ?>
    <?php require __DIR__ . '/_form_notes.php'; ?>
    <?php require __DIR__ . '/_form_variants.php'; ?>
</div>
