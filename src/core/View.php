<?php
declare(strict_types=1);

final class View
{
    public static function render(string $template, array $data = []): void
    {
        $path = __DIR__ . '/../views/' . ltrim($template, '/');

        if (!file_exists($path)) {
            http_response_code(500);
            echo "View niet gevonden: " . htmlspecialchars($template);
            exit;
        }

        extract($data, EXTR_SKIP);
        require $path;
    }
}
