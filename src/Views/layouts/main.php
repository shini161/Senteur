<?php
// Shared storefront layout. Feature templates render into `$content` while the
// layout owns global navigation, search, and footer chrome.

$publicDir = dirname(__DIR__, 3) . '/public';
$assetUrl = static function (string $path) use ($publicDir): string {
    $normalizedPath = '/' . ltrim($path, '/');
    $filePath = $publicDir . $normalizedPath;

    if (! is_file($filePath)) {
        return $normalizedPath;
    }

    return $normalizedPath . '?v=' . filemtime($filePath);
};
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Senteur', ENT_QUOTES, 'UTF-8') ?> · Senteur</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/assets/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/assets/css/pages/products.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/assets/css/pages/cart.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/assets/css/pages/orders.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/assets/css/responsive.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="icon" href="/assets/images/logo-favicon.svg" type="image/svg+xml">
</head>

<body>
    <!-- Global navigation stays consistent across storefront and admin-auth pages. -->
    <header class="site-header">
        <nav class="navbar">
            <div class="nav-left">
                <a href="/" class="logo">
                    <?php require __DIR__ . '/../../../public/assets/images/logo.svg'; ?>
                    <span>Senteur</span>
                </a>
            </div>

            <div class="nav-center">
                <a href="/products">Shop</a>
                <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
                    <a href="/admin/orders">Admin</a>
                <?php endif; ?>
            </div>

            <div class="nav-right">
                <form method="GET" action="/products" class="search-form">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search perfumes..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </form>

                <div class="nav-actions">
                    <a href="/cart" class="nav-action-link">Cart</a>

                    <?php if ($user): ?>
                        <a href="/profile" class="button-secondary">Profile</a>
                    <?php else: ?>
                        <a href="/login" class="button-secondary">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="site-main">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer links intentionally stay lightweight and static. -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-left">
                <strong>Senteur</strong>
                <p class="muted">
                    Discover refined fragrances, signature scents, and standout perfume lines.
                </p>
            </div>

            <div class="footer-links">
                <a href="/products">Shop</a>
                <a href="https://github.com/shini161/Senteur" target="_blank" rel="noopener noreferrer">GitHub</a>
                <a href="https://www.linkedin.com/in/mirza-osmic" target="_blank" rel="noopener noreferrer">LinkedIn</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© <?= date('Y') ?> Senteur</span>
        </div>
    </footer>

    <?php foreach (($scripts ?? []) as $script): ?>
        <script src="<?= htmlspecialchars($assetUrl($script), ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <?php endforeach; ?>
</body>

</html>
