<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    public static function input(): string
    {
        $token = self::token();

        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verify(?string $token): bool
    {
        if (! isset($_SESSION[self::KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::KEY], (string) $token);
    }
}
