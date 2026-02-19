<?php
declare(strict_types=1);

final class AdminAuthService
{
    public static function login(array $config, string $username, string $password): bool
    {
        $admin = $config['admin'] ?? null;
        if (!is_array($admin)) {
            return false;
        }

        $okUser = isset($admin['username']) && $admin['username'] === $username;
        $okPass = isset($admin['password_hash']) && password_verify($password, (string)$admin['password_hash']);

        if ($okUser && $okPass) {
            Session::set('is_admin', true);
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        Session::remove('is_admin');
    }

    public static function isAdmin(): bool
    {
        return Session::get('is_admin', false) === true;
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            header('Location: ' . base_url('/?r=admin/login'));
            exit;
        }
    }
}
