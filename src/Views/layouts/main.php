<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Senteur', ENT_QUOTES, 'UTF-8') ?> · Senteur</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="icon" href="/assets/images/logo-favicon.svg" type="image/svg+xml">
</head>

<body>
    <header class="site-header">
        <nav class="navbar">

            <!-- LEFT: LOGO -->
            <div class="nav-left">
                <a href="/" class="logo">
                    <?php require __DIR__ . '/../../../public/assets/images/logo.svg'; ?>
                    <span>Senteur</span>
                </a>
            </div>

            <!-- CENTER: MAIN NAV LINKS -->
            <div class="nav-center">
                <a href="/products">Shop</a>
                <a href="/categories">Categories</a>
            </div>

            <!-- RIGHT: SEARCH + USER ACTIONS -->
            <div class="nav-right">

                <!-- SEARCH -->
                <form method="GET" action="/products" class="search-form">
                    <input type="text" name="search" placeholder="Search...">
                </form>

                <!-- AUTH-DEPENDENT LINKS -->
                <?php if ($user): ?>

                    <!-- LOGGED-IN USER -->
                    <a href="/cart">Cart</a>
                    <a href="/profile">Profile</a>

                    <!-- OPTIONAL: USERNAME -->
                    <span>
                        <?= htmlspecialchars($user['username']) ?>
                    </span>

                    <!-- LOGOUT -->
                    <form method="POST" action="/logout" style="display:inline;">
                        <?= \App\Core\Csrf::input() ?>
                        <button type="submit">Logout</button>
                    </form>

                <?php else: ?>

                    <!-- GUEST -->
                    <a href="/login">Login</a>

                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <?= $content ?? '' ?>
    </main>

</body>

</html>