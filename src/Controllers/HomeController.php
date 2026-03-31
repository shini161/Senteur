<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    /**
     * Handles the home page request (/)
     *
     * Calls the render method to load the view inside the main layout.
     */
    public function index(): void
    {
        $this->render('home/index', [
            'title' => 'Home', // Data passed to the view
        ]);
    }
}
