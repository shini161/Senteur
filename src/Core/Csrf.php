<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Generates and verifies a single session-scoped CSRF token.
 */
class Csrf
{
    private const KEY = '_csrf_token';

    /**
     * Returns the current token, creating it lazily on first use.
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    /**
     * Convenience helper for embedding the CSRF token in HTML forms.
     */
    public static function input(): string
    {
        $token = self::token();

        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Verifies a submitted token against the session value in constant time.
     */
    public static function verify(?string $token): bool
    {
        if (! isset($_SESSION[self::KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::KEY], (string) $token);
    }
}
