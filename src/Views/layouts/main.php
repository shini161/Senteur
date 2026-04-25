<?php
$publicDir = dirname(__DIR__, 3) . '/public';
$assetUrl = static function (string $path) use ($publicDir): string {
    $normalizedPath = '/' . ltrim($path, '/');
    $filePath = $publicDir . $normalizedPath;

    if (! is_file($filePath)) {
        return $normalizedPath;
    }

    return $normalizedPath . '?v=' . filemtime($filePath);
};

$layoutStyles = [
    '/assets/css/app.css',
    '/assets/css/pages/home.css',
    '/assets/css/pages/account.css',
    '/assets/css/pages/products.css',
    '/assets/css/pages/cart.css',
    '/assets/css/pages/orders.css',
    '/assets/css/responsive.css',
];
$allStyles = array_values(array_unique(array_merge($layoutStyles, $styles ?? [])));
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Senteur', ENT_QUOTES, 'UTF-8') ?> · Senteur</title>
    <?php foreach ($allStyles as $style): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl($style), ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <link rel="icon" href="/assets/images/logo-favicon.svg" type="image/svg+xml">
</head>

<body>
    <?php
    $layoutScripts = ['/assets/js/layout/navbar.js'];
    $allScripts = array_values(array_unique(array_merge($layoutScripts, $scripts ?? [])));
    ?>
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
                <form method="GET" action="/products" class="search-form nav-search">
                    <input
                        type="text"
                        name="search"
                        aria-label="Search perfumes"
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

                <div class="mobile-nav-controls">
                    <a
                        href="/products?show=search"
                        class="mobile-search-link"
                        aria-label="Search perfumes">
                        <span class="mobile-search-icon" aria-hidden="true"></span>
                    </a>

                    <button
                        type="button"
                        class="mobile-nav-toggle"
                        data-mobile-nav-toggle
                        aria-expanded="false"
                        aria-controls="mobile-nav-panel">
                        <span class="mobile-nav-toggle-icon" aria-hidden="true"></span>
                        <span class="sr-only">Open navigation menu</span>
                    </button>
                </div>

                <div
                    class="mobile-nav-panel"
                    id="mobile-nav-panel"
                    data-mobile-nav-panel
                    hidden>
                    <div class="mobile-nav-links">
                        <a href="/products" class="mobile-nav-link">Shop</a>

                        <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
                            <a href="/admin/orders" class="mobile-nav-link">Admin</a>
                        <?php endif; ?>

                        <a href="/cart" class="mobile-nav-link">Cart</a>

                        <?php if ($user): ?>
                            <a href="/profile" class="mobile-nav-link mobile-nav-profile-link">Profile</a>
                        <?php else: ?>
                            <a href="/login" class="mobile-nav-link mobile-nav-profile-link">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="site-main">
        <?= $content ?? '' ?>
    </main>

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

    <?php foreach ($allScripts as $script): ?>
        <script src="<?= htmlspecialchars($assetUrl($script), ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <?php endforeach; ?>
</body>

</html>
