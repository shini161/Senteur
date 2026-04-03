<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\AuthController;

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

    // Auth routes: register
    ['GET', '/register', [AuthController::class, 'showRegister']],
    ['POST', '/register', [AuthController::class, 'register']],

    // Auth routes: login
    ['GET', '/login', [AuthController::class, 'showLogin']],
    ['POST', '/login', [AuthController::class, 'login']],

    // Auth routes: logout
    ['POST', '/logout', [AuthController::class, 'logout']]
];
