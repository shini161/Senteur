<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal HTTP request object built from PHP superglobals.
 */
class Request
{
    /** HTTP method (GET, POST, etc.) */
    public string $method;

    /** Normalized path, e.g. "/" or "/products" */
    public string $path;

    /**
     * Creates a normalized request instance from PHP's runtime globals.
     */
    public static function fromGlobals(): self
    {
        $request = new self();

        // Default to GET so local CLI or malformed requests still produce
        // a valid object the router can reason about.
        $request->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Ignore the query string because route matching only works on paths.
        $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Treat `/products/` and `/products` as the same endpoint.
        $request->path = rtrim($rawPath, '/') ?: '/';

        return $request;
    }
}
