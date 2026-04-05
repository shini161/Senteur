<section class="auth-page">
    <div class="auth-card" style="max-width: 900px;">
        <p class="section-kicker">Admin</p>
        <h1>Create Product</h1>
        <p class="auth-subtitle">Add a new catalog product.</p>

        <p>
            <a href="/admin/products">← Back to products</a>
        </p>

        <form method="POST" action="/admin/products" class="auth-form">
            <?= \App\Core\Csrf::input() ?>
            <?php require __DIR__ . '/_form.php'; ?>
            <button type="submit" class="auth-button">Create product</button>
        </form>
    </div>
</section>