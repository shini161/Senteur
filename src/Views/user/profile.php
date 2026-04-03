<section class="profile-page">
    <div class="profile-card">
        <h1>Profile</h1>
        <p class="profile-subtitle">Your account details.</p>

        <?php if (! $user): ?>
            <p>User not found.</p>
        <?php else: ?>
            <div class="profile-field">
                <span class="profile-label">Username</span>
                <span><?= htmlspecialchars($user['username']) ?></span>
            </div>

            <div class="profile-field">
                <span class="profile-label">Email</span>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>

            <div class="profile-field">
                <span class="profile-label">Phone</span>
                <span><?= htmlspecialchars($user['phone'] ?? '—') ?></span>
            </div>

            <div class="profile-field">
                <span class="profile-label">Role</span>
                <span><?= htmlspecialchars($user['role']) ?></span>
            </div>

            <div class="profile-field">
                <span class="profile-label">Member since</span>
                <span><?= htmlspecialchars($user['created_at']) ?></span>
            </div>

            <div class="profile-links">
                <a href="/addresses" class="profile-link">
                    Manage addresses →
                </a>

                <a href="/orders" class="profile-link">
                    My orders →
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>