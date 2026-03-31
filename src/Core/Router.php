<?php

namespace App\Core;

class Router
{
    /**
     * @param list<array{
     *   0: string, // HTTP method (GET, POST, ...)
     *   1: string, // path (e.g. "/products")
     *   2: array{0: class-string, 1: string} // [Controller, method]
     * }> $routes
     */
    public function __construct(
        private array $routes,
    ) {
    }

    /**
     * Dispatches the incoming request to the matching route.
     *
     * Flow:
     * Request → match route → call controller action
     *
     * If no route matches, returns a 404 response.
     */
    public function dispatch(Request $request): void
    {
        // Iterate over all defined routes
        foreach ($this->routes as [$method, $path, $handler]) {
            // Skip if method or path does not match
            if ($request->method !== $method || $request->path !== $path) {
                continue;
            }

            // Extract controller class and action method
            [$class, $action] = $handler;

            // Instantiate controller and call the action
            (new $class())->{$action}();

            return; // Stop after first match
        }


        // No route matched: 404 response
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Not found';
    }
}
