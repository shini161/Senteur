<?php

namespace App\Core;

class Request
{
    /** HTTP method (GET, POST, etc.) */
    public string $method;

    /** Normalized path, e.g. "/" or "/products" */
    public string $path;

    /**
     * Creates a Request instance from PHP superglobals.
     *
     * Extracts:
     * - HTTP method from $_SERVER
     * - URL path (without query string)
     *
     * Also normalizes the path:
     * - removes trailing slashes ("/products/" → "/products")
     * - ensures root stays "/"
     */
    public static function fromGlobals(): self
    {
        $request = new self();

        // Get HTTP method (default: GET)
        $request->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Extract path from URL (ignore query string)
        $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Normalize path (remove trailing slash except for root)
        $request->path = rtrim($rawPath, '/') ?: '/';

        return $request;
    }
}
