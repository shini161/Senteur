<?php
// Shared admin product form partial. The controller passes either `$product`
// or `$old` so create and edit flows can reuse the same fields.
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
    <label for="name">Base name</label>
    <input
        id="name"
        name="name"
        type="text"
        value="<?= htmlspecialchars((string) ($formData['name'] ?? '')) ?>"
        placeholder="e.g. Bleu de Chanel"
        required>
</div>

<div class="form-group">
    <label for="concentration_label">Concentration / subtitle</label>
    <input
        id="concentration_label"
        name="concentration_label"
        type="text"
        value="<?= htmlspecialchars((string) ($formData['concentration_label'] ?? '')) ?>"
        placeholder="e.g. Eau de Parfum or Parfum Cologne">
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
        <?php // Variant rows stay simple because the repository fully replaces them on save. ?>
        <div class="admin-variant-row">
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

        <?php if (!empty($product['variants'][$index]['id'])): ?>
            <?php // Existing variants can receive dedicated image uploads after creation. ?>
            <div class="admin-variant-image-block">
                <div class="admin-variant-image-meta">
                    <strong><?= htmlspecialchars((string) ($variant['size_ml'] ?? '')) ?>ml image</strong>
                </div>

                <?php
                $variantImage = $product['variants'][$index]['images'][0]['image_url'] ?? null;
                ?>

                <?php if ($variantImage): ?>
                    <img
                        src="/<?= htmlspecialchars($variantImage) ?>"
                        alt="Variant image"
                        class="admin-variant-image-preview">
                <?php else: ?>
                    <div class="admin-variant-image-empty">No variant image uploaded</div>
                <?php endif; ?>

                <form
                    method="POST"
                    action="/admin/variants/<?= (int) $product['variants'][$index]['id'] ?>/image"
                    enctype="multipart/form-data"
                    class="admin-variant-image-form">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                    <button type="submit" class="button-secondary">Upload variant image</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
