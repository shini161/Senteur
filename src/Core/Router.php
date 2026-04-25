<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\CartController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\AddressController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Controllers\ReviewController;
use App\Controllers\StripeWebhookController;
use App\Controllers\AdminAuthController;
use App\Controllers\AdminCatalogController;
use App\Controllers\AdminNoteController;
use App\Controllers\AdminOrderController;
use App\Controllers\AdminProductController;
use App\Models\CartRepository;
use App\Models\NoteRepository;
use App\Models\ProductRepository;
use App\Models\UserRepository;
use App\Models\AddressRepository;
use App\Models\OrderRepository;
use App\Models\PaymentRepository;
use App\Models\ReviewRepository;
use App\Services\CartService;
use App\Services\ProductService;
use App\Services\AuthService;
use App\Services\AddressService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\AdminOrderService;
use App\Services\AdminCatalogService;
use App\Services\AdminProductService;
use App\Services\NoteService;
use App\Services\ReviewService;

/**
 * Matches incoming requests against the route table and manually wires
 * controller dependencies for this lightweight MVC application.
 */
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

	/**
	 * Finds the first matching route, extracts named parameters, and invokes
	 * the configured controller action.
	 */
	public function dispatch(Request $request): void
	{
		$requestPath = $this->normalizePath($request->path);

		foreach ($this->routes as [$method, $path, $handler]) {
			if ($request->method !== $method) {
				continue;
			}

			// Route placeholders like `/products/{slug}` are converted into
			// named capture groups so controller method arguments stay readable.
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

	/**
	 * Translates route placeholders into regular expressions the dispatcher can match.
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
	 * Normalizes route paths so matching stays consistent across definitions and requests.
	 */
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
	 * This keeps construction simple without introducing a full DI container.
	 */
	private function resolve(string $class): object
	{
		return match ($class) {
			HomeController::class => new HomeController(
				new ProductService(
					new ProductRepository()
				)
			),

			ProductController::class => new ProductController(
				new ProductService(
					new ProductRepository()
				),
				new ReviewService(
					new ReviewRepository(),
					new OrderRepository(),
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

			CheckoutController::class => new CheckoutController(
				new CheckoutService(
					new CartService(new CartRepository()),
					new AddressRepository(),
					new OrderRepository(null, new CartRepository())
				),
				new PaymentService(
					new PaymentRepository(),
					new OrderRepository()
				),
				new CartService(new CartRepository())
			),

			OrderController::class => new OrderController(
				new OrderService(
					new OrderRepository()
				)
			),

			StripeWebhookController::class => new StripeWebhookController(
				new PaymentService(
					new PaymentRepository(),
					new OrderRepository()
				)
			),

			AdminAuthController::class => new AdminAuthController(
				new AuthService(
					new UserRepository()
				)
			),

			AdminOrderController::class => new AdminOrderController(
				new AdminOrderService(
					new OrderRepository()
				)
			),

			AdminProductController::class => new AdminProductController(
				new AdminProductService(
					new ProductRepository()
				)
			),

			AdminNoteController::class => new AdminNoteController(
				new NoteService(
					new NoteRepository()
				)
			),

			AdminCatalogController::class => new AdminCatalogController(
				new AdminCatalogService(
					new ProductRepository()
				)
			),

			ReviewController::class => new ReviewController(
				new ReviewService(
					new ReviewRepository(),
					new OrderRepository(),
					new ProductRepository()
				)
			),
		};
	}
}
