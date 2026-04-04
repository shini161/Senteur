<section class="auth-page">
    <div class="auth-card" style="max-width: 900px;">
        <h1>Edit Product</h1>
        <p class="auth-subtitle">Update product data and variants.</p>

        <p>
            <a href="/admin/products">← Back to products</a>
        </p>

        <form method="POST" action="/admin/products/<?= (int) $product['id'] ?>" class="auth-form">
            <?= \App\Core\Csrf::input() ?>
            <?php require __DIR__ . '/_form.php'; ?>
            <button type="submit" class="auth-button">Save changes</button>
        </form>
    </div>
</section>