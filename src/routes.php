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
use App\Controllers\AdminAuthController;
use App\Controllers\AdminOrderController;
use App\Controllers\AdminProductController;
use App\Controllers\ReviewController;

// The router consumes a flat route table of `[method, path, [controller, action]]`
// entries so the request flow stays explicit and easy to scan.
return [
    // [HTTP METHOD, URL PATH, [Controller, Method]]

    // Home page
    ['GET', '/', [HomeController::class, 'index']],

    // Products
    ['GET', '/products', [ProductController::class, 'index']],
    ['GET', '/products/{slug}', [ProductController::class, 'show']],
    ['POST', '/products/{slug}/reviews', [ReviewController::class, 'store']],

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

    // Admin auth
    ['GET', '/admin/login', [AdminAuthController::class, 'showLogin']],
    ['POST', '/admin/login', [AdminAuthController::class, 'login']],
    ['POST', '/admin/logout', [AdminAuthController::class, 'logout']],

    // Admin orders
    ['GET', '/admin/orders', [AdminOrderController::class, 'index']],
    ['GET', '/admin/orders/{publicId}', [AdminOrderController::class, 'show']],
    ['POST', '/admin/orders/{publicId}/status', [AdminOrderController::class, 'updateStatus']],

    // Admin products
    ['GET', '/admin/products', [AdminProductController::class, 'index']],
    ['GET', '/admin/products/create', [AdminProductController::class, 'create']],
    ['POST', '/admin/products', [AdminProductController::class, 'store']],
    ['GET', '/admin/products/{id}/edit', [AdminProductController::class, 'edit']],
    ['POST', '/admin/products/{id}', [AdminProductController::class, 'update']],
    ['POST', '/admin/products/{id}/image', [AdminProductController::class, 'uploadImage']],

    ['POST', '/admin/variants/{id}/image', [AdminProductController::class, 'uploadVariantImage']]
];
