<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;

return [
    // [HTTP METHOD, URL PATH, [Controller, Method]]

    // Home page
    ['GET', '/', [HomeController::class, 'index']],

    // Products page
    ['GET', '/products', [ProductController::class, 'index']],

    // Product details page
    ['GET', '/product/{id}', [ProductController::class, 'show']],

    // Cart page
    ['GET', '/cart', [CartController::class, 'index']],
    ['POST', '/cart/add', [CartController::class, 'add']],
    ['POST', '/cart/update', [CartController::class, 'update']],
    ['POST', '/cart/remove', [CartController::class, 'remove']],
];
