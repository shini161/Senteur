<?php

declare(strict_types=1);

use App\Core\Request;
use App\Core\Router;
use RuntimeException;
use Throwable;

// --------------------------------------------------
// Autoloader
// --------------------------------------------------

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

// --------------------------------------------------
// Load environment variables
// --------------------------------------------------

$env = parse_ini_file(__DIR__ . '/../.env');

if ($env === false) {
    throw new RuntimeException('Unable to load .env file');
}

foreach ($env as $key => $value) {
    $_ENV[$key] = (string) $value;
}

// --------------------------------------------------
// Global exception handler
// --------------------------------------------------

set_exception_handler(function (Throwable $e): void {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');

    $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

    echo $debug
        ? 'Internal Server Error: ' . $e->getMessage()
        : 'Internal Server Error';
});

// --------------------------------------------------
// Application bootstrap
// --------------------------------------------------

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$routes = require __DIR__ . '/routes.php';
$request = Request::fromGlobals();
$router = new Router($routes);
$router->dispatch($request);
