<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

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

    public function add(): void
    {
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

    public function update(): void
    {
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

    public function remove(): void
    {
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
