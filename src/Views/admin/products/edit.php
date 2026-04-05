<section class="auth-page">
    <div class="auth-card" style="max-width: 900px;">
        <p class="section-kicker">Admin</p>
        <h1>Edit Product</h1>
        <p class="auth-subtitle">Update product data, variants, and image.</p>

        <p>
            <a href="/admin/products">← Back to products</a>
        </p>

        <div style="margin: 1.25rem 0; padding: 1rem; border: 1px solid #e5e8e2; border-radius: 16px; background: #fcfbf8;">
            <h2 style="margin-top: 0;">Primary image</h2>

            <?php if (!empty($imageError)): ?>
                <p class="auth-error"><?= htmlspecialchars($imageError) ?></p>
            <?php endif; ?>

            <?php if (!empty($product['image_url'])): ?>
                <img
                    src="/<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    style="width: 180px; height: 180px; object-fit: cover; border-radius: 16px; border: 1px solid #e5e8e2; margin-bottom: 1rem;">
            <?php else: ?>
                <div style="width: 180px; height: 180px; display:grid; place-items:center; border: 1px solid #e5e8e2; border-radius: 16px; margin-bottom: 1rem; color:#667067;">
                    No image uploaded
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/products/<?= (int) $product['id'] ?>/image" enctype="multipart/form-data" class="auth-form">
                <?= \App\Core\Csrf::input() ?>

                <div class="form-group">
                    <label for="image">Upload image</label>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" required>
                </div>

                <button type="submit" class="button-secondary">Upload image</button>
            </form>
        </div>

        <form method="POST" action="/admin/products/<?= (int) $product['id'] ?>" class="auth-form">
            <?= \App\Core\Csrf::input() ?>
            <?php require __DIR__ . '/_form.php'; ?>
            <button type="submit" class="auth-button">Save changes</button>
        </form>
    </div>
</section>