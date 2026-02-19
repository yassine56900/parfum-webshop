<?php
declare(strict_types=1);

final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (!isset($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION[self::KEY];
    }

    public static function validate(?string $token): bool
    {
        $sessionToken = $_SESSION[self::KEY] ?? '';
        return is_string($token) && is_string($sessionToken) && hash_equals($sessionToken, $token);
    }
}
