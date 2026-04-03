<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Controller;
use App\Services\AuthService;
use RuntimeException;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function showLogin(): void
    {
        Auth::requireGuest();

        $this->render('auth/login', [
            'title' => 'Login',
            'error' => null,
            'old' => [],
        ]);
    }

    public function login(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        Auth::requireGuest();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->render('auth/login', [
                'title' => 'Login',
                'error' => 'Invalid credentials',
                'old' => [
                    'email' => $email,
                ],
            ]);
            return;
        }

        try {
            $user = $this->authService->login($email, $password);

            Auth::login($user);

            header('Location: /');
            exit;
        } catch (RuntimeException) {
            $this->render('auth/login', [
                'title' => 'Login',
                'error' => 'Invalid credentials',
                'old' => [
                    'email' => $email,
                ],
            ]);
        }
    }

    public function showRegister(): void
    {
        Auth::requireGuest();

        $this->render('auth/register', [
            'title' => 'Register',
            'error' => null,
            'old' => [],
        ]);
    }

    public function register(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        Auth::requireGuest();

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
        ];

        if ($data['password'] !== $data['confirm_password']) {
            $this->render('auth/register', [
                'title' => 'Register',
                'error' => 'Passwords do not match',
                'old' => [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ],
            ]);
            return;
        }

        try {
            $user = $this->authService->register($data);

            Auth::login($user);

            header('Location: /');
            exit;
        } catch (RuntimeException $e) {
            $this->render('auth/register', [
                'title' => 'Register',
                'error' => $e->getMessage(),
                'old' => [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ],
            ]);
        }
    }

    public function logout(): void
    {
        if (! Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            echo 'Invalid CSRF token';
            return;
        }

        Auth::logout();

        header('Location: /');
        exit;
    }
}
