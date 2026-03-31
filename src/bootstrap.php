<?php

declare(strict_types=1);

// --------------------------------------------------
// Autoloader
// --------------------------------------------------

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    // Only load classes from our App namespace
    if (! str_starts_with($class, $prefix)) {
        return;
    }

    // Convert namespace to file path
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = __DIR__ . '/' . $relative . '.php'; // full path to file
    
    // Include file if it exists
    if (is_file($file)) {
        require $file;
    }
});

// --------------------------------------------------
// Application bootstrap
// --------------------------------------------------

// Load all route definitions (array of routes)
$routes = require __DIR__ . '/routes.php';

// Create Request object from PHP globals
$request = App\Core\Request::fromGlobals();

// Initialize router with route definitions
$router = new App\Core\Router($routes);

// Dispatch request -> matches route -> calls controller
$router->dispatch($request);