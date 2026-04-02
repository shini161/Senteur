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
	) {}

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
		$requestPath = $this->normalizePath($request->path);

		// Iterate over all defined routes
		foreach ($this->routes as [$method, $path, $handler]) {
			// Skip if HTTP method does not match
			if ($request->method !== $method) {
				continue;
			}

			// Convert route path into regex pattern
			$pattern = $this->convertRouteToRegex($path);

			// Skip if request path does not match route pattern
			if (! preg_match($pattern, $requestPath, $matches)) {
				continue;
			}

			// Extract named params from regex matches
			$params = [];
			foreach ($matches as $key => $value) {
				if (! is_int($key)) {
					$params[] = $value;
				}
			}

			// Extract controller class and action method
			[$class, $action] = $handler;

			// Instantiate controller and call the action
			(new $class())->{$action}(...$params);

			return; // Stop after first match
		}

		// No route matched: 404 response
		http_response_code(404);
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Not found';
	}

	/**
	 * Converts a route path like /product/{id}
	 * into a regex pattern like #^/product/(?P<id>[^/]+)$#
	 */
	private function convertRouteToRegex(string $path): string
	{
		$path = $this->normalizePath($path);

		$pattern = preg_replace(
			'#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
			'(?P<$1>[^/]+)',
			$path
		);

		return '#^' . $pattern . '$#';
	}

	/**
	 * Normalizes paths so "/product/1/" becomes "/product/1"
	 * while keeping "/" unchanged.
	 */
	private function normalizePath(string $path): string
	{
		$path = parse_url($path, PHP_URL_PATH) ?? '/';

		if ($path !== '/' && str_ends_with($path, '/')) {
			$path = rtrim($path, '/');
		}

		return $path;
	}
};
