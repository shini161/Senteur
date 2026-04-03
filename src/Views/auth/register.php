<section class="auth-page">
    <div class="auth-card">
        <h1>Register</h1>
        <p class="auth-subtitle">Create your account.</p>

        <?php if (! empty($error)): ?>
            <p class="auth-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="/register" class="auth-form">
            <?= \App\Core\Csrf::input() ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    id="username"
                    name="username"
                    value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                    required>
            </div>

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
                <label for="phone">Phone</label>
                <input
                    id="phone"
                    name="phone"
                    type="text"
                    value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input id="confirm_password" name="confirm_password" type="password" required>
            </div>

            <button type="submit" class="auth-button">Register</button>
        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="/login">Login</a>
        </p>
    </div>
</section>