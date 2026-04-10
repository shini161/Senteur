<?php

declare(strict_types=1);

use App\Core\Request;
use App\Core\Router;

// Register a lightweight PSR-4-style autoloader for the local `App\` namespace.
// Composer is still loaded in `public/index.php` for third-party packages.

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = __DIR__ . '/' . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

// Global helper functions are intentionally kept framework-agnostic.
require_once __DIR__ . '/helpers.php';

// Load environment variables once per request and expose them through `$_ENV`.
$env = parse_ini_file(__DIR__ . '/../.env');

if ($env === false) {
    throw new RuntimeException('Unable to load .env file');
}

foreach ($env as $key => $value) {
    $_ENV[$key] = (string) $value;
}

// Keep error output developer-friendly in debug mode while avoiding leaking
// internals in production-like environments.
set_exception_handler(function (Throwable $e): void {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');

    $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

    error_log((string) $e);
    error_log($e->getMessage());

    echo $debug
        ? 'Internal Server Error: ' . $e->getMessage()
        : 'Internal Server Error';
});

// Boot the request lifecycle: session, route table, request object, dispatch.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$routes = require __DIR__ . '/routes.php';
$request = Request::fromGlobals();
$router = new Router($routes);
$router->dispatch($request);
