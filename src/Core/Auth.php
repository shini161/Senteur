<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\UserRepository;

class Auth
{
    private static ?array $user = null;
    private static bool $userLoaded = false;

    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        self::$user = $user;
        self::$userLoaded = true;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        self::$user = null;
        self::$userLoaded = false;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function user(): ?array
    {
        if (self::$userLoaded) {
            return self::$user;
        }

        $userId = self::id();

        if ($userId === null) {
            self::$userLoaded = true;
            self::$user = null;
            return null;
        }

        $users = new UserRepository();
        self::$user = $users->findById($userId);
        self::$userLoaded = true;

        return self::$user;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user !== null && ($user['role'] ?? null) === 'admin';
    }

    public static function requireGuest(): void
    {
        if (self::check()) {
            header('Location: /');
            exit;
        }
    }

    public static function requireAuth(): void
    {
        if (! self::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireAdminGuest(): void
    {
        if (self::isAdmin()) {
            header('Location: /admin/orders');
            exit;
        }

        if (self::check()) {
            header('Location: /');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (! self::check()) {
            header('Location: /admin/login');
            exit;
        }

        if (! self::isAdmin()) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}
