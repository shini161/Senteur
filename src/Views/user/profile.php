<?php
// Profile dashboard for the authenticated user. The shared layout injects the
// `$user` variable so the template can stay read-only.
?>
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
                <span><?= htmlspecialchars(!empty($user['phone']) ? formatPhone((string) $user['phone']) : '—') ?></span>
            </div>

            <div class="profile-field">
                <span class="profile-label">Member since</span>
                <span><?= htmlspecialchars(date('F j, Y', strtotime((string) $user['created_at']))) ?></span>
            </div>

            <div class="profile-links">
                <a href="/addresses" class="profile-link">
                    Manage addresses →
                </a>

                <a href="/orders" class="profile-link">
                    My orders →
                </a>

                <form method="POST" action="/logout" class="profile-logout-form">
                    <?= \App\Core\Csrf::input() ?>
                    <button type="submit" class="button-danger profile-logout-button">
                        Logout
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
