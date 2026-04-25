<section class="panel admin-product-form-panel">
    <div class="admin-product-panel-heading">
        <div>
            <h2>Identity</h2>
            <p class="muted">Core catalog information used across search, product listings, and the detail page.</p>
        </div>

        <?php if ($isEditing): ?>
            <div class="admin-product-panel-actions">
                <span class="badge">Slug: <?= htmlspecialchars((string) ($formData['slug'] ?? '')) ?></span>
            </div>
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
