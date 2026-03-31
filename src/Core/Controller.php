<?php

namespace App\Core;

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
        // Inject global data available in ALL views
        $data['user'] = $_SESSION['user'] ?? null;

        // Convert array keys into variables for the view
        // ['title' => 'Home'] → $title = 'Home'
        extract($data, EXTR_SKIP);

        $viewsDir = dirname(__DIR__) . '/Views';
        ob_start(); // Start output buffering (capture view output)

        // Load the specific view file (e.g. Views/home/index.php)
        require $viewsDir . '/' . $view . '.php';

        // Store the rendered view content
        $content = ob_get_clean();

        // Load the layout and inject $content inside it
        require $viewsDir . '/layouts/main.php';
    }
}
