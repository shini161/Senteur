<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Controller;
use App\Services\CartService;

/**
 * Manages the session-backed shopping cart.
 */
class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    /**
     * Displays cart contents and the calculated total.
     */
    public function index(): void
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();

        $this->render('cart/index', [
            'title' => 'Cart',
            'items' => $items,
            'total' => $total,
        ]);
    }

    /**
     * Adds a product variant to the cart after validating user input and CSRF state.
     */
    public function add(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $variantId = (int) ($_POST['variant_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($variantId <= 0 || $quantity <= 0) {
            http_response_code(400);
            echo 'Invalid cart input';
            return;
        }

        $this->cartService->addItem($variantId, $quantity);

        header('Location: /cart');
        exit;
    }

    /**
     * Updates the quantity of a cart line item.
     */
    public function update(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $variantId = (int) ($_POST['variant_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if ($variantId <= 0) {
            http_response_code(400);
            echo 'Invalid cart input';
            return;
        }

        $this->cartService->updateItem($variantId, $quantity);

        header('Location: /cart');
        exit;
    }

    /**
     * Removes a cart line item entirely.
     */
    public function remove(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $variantId = (int) ($_POST['variant_id'] ?? 0);

        if ($variantId <= 0) {
            http_response_code(400);
            echo 'Invalid cart input';
            return;
        }

        $this->cartService->removeItem($variantId);

        header('Location: /cart');
        exit;
    }
}
