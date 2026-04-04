<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\AddressController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Controllers\StripeWebhookController;

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
    ['POST', '/logout', [AuthController::class, 'logout']],

    // User profile
    ['GET', '/profile', [ProfileController::class, 'index']],

    // Addresses
    ['GET', '/addresses', [AddressController::class, 'index']],
    ['POST', '/addresses', [AddressController::class, 'store']],
    ['POST', '/addresses/delete', [AddressController::class, 'delete']],
    ['POST', '/addresses/default', [AddressController::class, 'setDefault']],

    // Checkout
    ['GET', '/checkout', [CheckoutController::class, 'index']],
    ['POST', '/checkout', [CheckoutController::class, 'store']],
    ['GET', '/checkout/success', [CheckoutController::class, 'success']],
    ['GET', '/checkout/cancel', [CheckoutController::class, 'cancel']],

    // Orders
    ['GET', '/orders', [OrderController::class, 'index']],
    ['GET', '/orders/{publicId}', [OrderController::class, 'show']],

    // Stripe
    ['POST', '/webhooks/stripe', [StripeWebhookController::class, 'handle']],
];
