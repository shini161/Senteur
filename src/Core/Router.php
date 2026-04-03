<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\CartController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\AddressController;
use App\Models\CartRepository;
use App\Models\ProductRepository;
use App\Models\UserRepository;
use App\Models\AddressRepository;
use App\Services\CartService;
use App\Services\ProductService;
use App\Services\AuthService;
use App\Services\AddressService;

class Router
{
	/**
	 * @param list<array{
	 *   0: string,
	 *   1: string,
	 *   2: array{0: class-string, 1: string}
	 * }> $routes
	 */
	public function __construct(
		private array $routes,
	) {}

	public function dispatch(Request $request): void
	{
		$requestPath = $this->normalizePath($request->path);

		foreach ($this->routes as [$method, $path, $handler]) {
			if ($request->method !== $method) {
				continue;
			}

			$pattern = $this->convertRouteToRegex($path);

			if (! preg_match($pattern, $requestPath, $matches)) {
				continue;
			}

			$params = [];
			foreach ($matches as $key => $value) {
				if (! is_int($key)) {
					$params[$key] = $value;
				}
			}

			[$class, $action] = $handler;

			if (! class_exists($class)) {
				http_response_code(500);
				header('Content-Type: text/plain; charset=UTF-8');
				echo 'Route controller not found';
				return;
			}

			$controller = $this->resolve($class);

			if (! method_exists($controller, $action)) {
				http_response_code(500);
				header('Content-Type: text/plain; charset=UTF-8');
				echo 'Route action not found';
				return;
			}

			$controller->{$action}(...$params);
			return;
		}

		http_response_code(404);
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Not found';
	}

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

	private function normalizePath(string $path): string
	{
		$path = parse_url($path, PHP_URL_PATH) ?? '/';

		if ($path !== '/' && str_ends_with($path, '/')) {
			$path = rtrim($path, '/');
		}

		return $path;
	}

	/**
	 * Resolves current controllers and their dependencies explicitly.
	 */
	private function resolve(string $class): object
	{
		return match ($class) {
			HomeController::class => new HomeController(),

			ProductController::class => new ProductController(
				new ProductService(
					new ProductRepository()
				)
			),

			CartController::class => new CartController(
				new CartService(
					new CartRepository()
				)
			),

			AuthController::class => new AuthController(
				new AuthService(
					new UserRepository()
				)
			),

			ProfileController::class => new ProfileController(),

			AddressController::class => new AddressController(
				new AddressService(
					new AddressRepository()
				)
			),
		};
	}
}
