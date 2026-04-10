<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AddressService;
use RuntimeException;

/**
 * Lets authenticated users manage their saved shipping addresses.
 */
class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService
    ) {}

    /**
     * Shows the address book and address creation form.
     */
    public function index(): void
    {
        Auth::requireAuth();

        $userId = Auth::id();
        $addresses = $this->addressService->getAllForUser((int) $userId);

        $this->render('user/addresses', [
            'title' => 'Addresses',
            'addresses' => $addresses,
            'can_add_address' => count($addresses) < 10,
            'error' => null,
            'old' => [],
        ]);
    }

    /**
     * Validates and stores a new address for the signed-in user.
     */
    public function store(): void
    {
        Auth::requireAuth();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $userId = (int) Auth::id();

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'address_line' => trim($_POST['address_line'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'is_default' => isset($_POST['is_default']),
        ];

        try {
            $this->addressService->createForUser($userId, $data);

            header('Location: /addresses');
            exit;
        } catch (RuntimeException $e) {
            $this->render('user/addresses', [
                'title' => 'Addresses',
                'addresses' => $this->addressService->getAllForUser($userId),
                'can_add_address' => count($this->addressService->getAllForUser($userId)) < 10,
                'error' => $e->getMessage(),
                'old' => $data,
            ]);
        }
    }

    /**
     * Deletes an address owned by the current user.
     */
    public function delete(): void
    {
        Auth::requireAuth();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $userId = (int) Auth::id();

        if ($id > 0) {
            $this->addressService->deleteForUser($id, $userId);
        }

        header('Location: /addresses');
        exit;
    }

    /**
     * Promotes one address to the default shipping address.
     */
    public function setDefault(): void
    {
        Auth::requireAuth();

        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $userId = (int) Auth::id();

        if ($id <= 0) {
            header('Location: /addresses');
            exit;
        }

        try {
            $this->addressService->setDefaultForUser($id, $userId);
        } catch (RuntimeException) {
            // Keep redirect-only flow for now
        }

        header('Location: /addresses');
        exit;
    }
}
