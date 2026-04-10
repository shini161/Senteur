<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\UserRepository;

/**
 * Session-backed authentication helper shared by controllers and views.
 */
class Auth
{
    private static ?array $user = null;
    private static bool $userLoaded = false;

    /**
     * Stores the authenticated user id in the session and memoizes the user payload.
     */
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

    /**
     * Returns whether the current request has an authenticated session.
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Returns the authenticated user's numeric id when available.
     */
    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /**
     * Loads the authenticated user only once per request to avoid duplicate queries.
     */
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

    /**
     * Admin access is represented by the persisted `role` column.
     */
    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user !== null && ($user['role'] ?? null) === 'admin';
    }

    /**
     * Redirects signed-in users away from guest-only pages.
     */
    public static function requireGuest(): void
    {
        if (self::check()) {
            header('Location: /');
            exit;
        }
    }

    /**
     * Redirects unauthenticated users to the storefront login page.
     */
    public static function requireAuth(): void
    {
        if (! self::check()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Prevents authenticated users from visiting the admin login screen.
     */
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

    /**
     * Ensures the current request belongs to an authenticated admin.
     */
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
