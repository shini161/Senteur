<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base controller with shared rendering helpers for PHP views.
 */
class Controller
{
    /**
     * Renders a view inside the main layout.
     *
     * @param string $view Path to the view (e.g. 'home/index')
     * @param array<string, mixed> $data Data passed to the view
     */
    protected function render(string $view, array $data = []): void
    {
        // Every view gets the authenticated user automatically so layout and
        // feature templates do not need to fetch auth state on their own.
        $data['user'] = Auth::user();

        // Convert `['title' => 'Home']` into `$title = 'Home'`.
        extract($data, EXTR_SKIP);

        $viewsDir = dirname(__DIR__) . '/Views';
        ob_start();

        // Capture the feature template first so the layout can place it.
        require $viewsDir . '/' . $view . '.php';

        $content = ob_get_clean();

        // The layout receives `$content` plus the extracted view variables.
        require $viewsDir . '/layouts/main.php';
    }
}
