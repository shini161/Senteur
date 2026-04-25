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
