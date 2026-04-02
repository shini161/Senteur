<?php

use App\Controllers\HomeController;
use App\Controllers\ProductController;

return [
    // [HTTP METHOD, URL PATH, [Controller, Method]]

    // Home page
    ['GET', '/', [HomeController::class, 'index']],

    // Products page
    ['GET', '/products', [ProductController::class, 'index']],

    // Product details page
    ['GET', '/product/{id}', [ProductController::class, 'show']],
];
