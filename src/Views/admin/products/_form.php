<?php
$formData = $product ?? $old ?? [];
$variants = $formData['variants'] ?? [
    ['size_ml' => '', 'price' => '', 'stock' => ''],
    ['size_ml' => '', 'price' => '', 'stock' => ''],
];
?>

<?php if (! empty($error)): ?>
    <p class="auth-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="form-group">
    <label for="brand_id">Brand</label>
    <select id="brand_id" name="brand_id" required>
        <option value="">Select brand</option>
        <?php foreach ($brands as $brand): ?>
            <option
                value="<?= (int) $brand['id'] ?>"
                <?= (string) ($formData['brand_id'] ?? '') === (string) $brand['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($brand['name']) ?>
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
                <?= htmlspecialchars($type['name']) ?>
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
        placeholder="e.g. Baccarat Rouge 540">
</div>

<div class="form-group">
    <label for="name">Name</label>
    <input
        id="name"
        name="name"
        type="text"
        value="<?= htmlspecialchars((string) ($formData['name'] ?? '')) ?>"
        required>
</div>

<div class="form-group">
    <label for="slug">Slug</label>
    <input
        id="slug"
        name="slug"
        type="text"
        value="<?= htmlspecialchars((string) ($formData['slug'] ?? '')) ?>"
        required>
</div>

<div class="form-group">
    <label for="gender">Gender</label>
    <select id="gender" name="gender" required>
        <option value="">Select gender</option>
        <?php foreach ($genders as $gender): ?>
            <option
                value="<?= htmlspecialchars($gender) ?>"
                <?= (string) ($formData['gender'] ?? '') === $gender ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($gender)) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea
        id="description"
        name="description"
        rows="5"><?= htmlspecialchars((string) ($formData['description'] ?? '')) ?></textarea>
</div>

<div class="form-group">
    <label>Variants</label>

    <?php foreach ($variants as $index => $variant): ?>
        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 0.75rem;">
            <input
                type="number"
                min="1"
                name="variants[<?= $index ?>][size_ml]"
                placeholder="Size (ml)"
                value="<?= htmlspecialchars((string) ($variant['size_ml'] ?? '')) ?>"
                required>

            <input
                type="number"
                min="0"
                step="0.01"
                name="variants[<?= $index ?>][price]"
                placeholder="Price"
                value="<?= htmlspecialchars((string) ($variant['price'] ?? '')) ?>"
                required>

            <input
                type="number"
                min="0"
                name="variants[<?= $index ?>][stock]"
                placeholder="Stock"
                value="<?= htmlspecialchars((string) ($variant['stock'] ?? '')) ?>"
                required>
        </div>
    <?php endforeach; ?>
</div>