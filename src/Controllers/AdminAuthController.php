<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AuthService;
use RuntimeException;

/**
 * Handles admin-only authentication flows.
 */
class AdminAuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Renders the admin login form.
     */
    public function showLogin(): void
    {
        Auth::requireAdminGuest();

        $this->render('admin/auth/login', [
            'title' => 'Admin Login',
            'error' => null,
            'old' => [],
        ]);
    }

    /**
     * Attempts to authenticate an admin user and redirect to the admin area.
     */
    public function login(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        Auth::requireAdminGuest();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->render('admin/auth/login', [
                'title' => 'Admin Login',
                'error' => 'Invalid credentials',
                'old' => [
                    'email' => $email,
                ],
            ]);
            return;
        }

        try {
            $user = $this->authService->loginAdmin($email, $password);

            Auth::login($user);

            header('Location: /admin/orders');
            exit;
        } catch (RuntimeException) {
            $this->render('admin/auth/login', [
                'title' => 'Admin Login',
                'error' => 'Invalid credentials',
                'old' => [
                    'email' => $email,
                ],
            ]);
        }
    }

    /**
     * Logs the current admin out.
     */
    public function logout(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        Auth::requireAdmin();

        Auth::logout();

        header('Location: /admin/login');
        exit;
    }
}
