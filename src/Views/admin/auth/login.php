<section class="auth-page">
    <div class="auth-card">
        <h1>Admin Login</h1>
        <p class="auth-subtitle">Access the admin panel.</p>

        <?php if (! empty($error)): ?>
            <p class="auth-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="/admin/login" class="auth-form">
            <?= \App\Core\Csrf::input() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required>
            </div>

            <button type="submit" class="auth-button">Login as admin</button>
        </form>
    </div>
</section>
