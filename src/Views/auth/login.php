<section class="auth-page">
    <div class="auth-card">
        <h1>Login</h1>
        <p class="auth-subtitle">Access your account.</p>

        <?php if (! empty($error)): ?>
            <p class="auth-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="/login" class="auth-form">
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
                <input id="password" name="password" type="password" required>
            </div>

            <button type="submit" class="auth-button">Login</button>
        </form>

        <p class="auth-switch">
            Don’t have an account?
            <a href="/register">Create one</a>
        </p>
    </div>
</section>