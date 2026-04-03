<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
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
}
