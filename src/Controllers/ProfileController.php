<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

/**
 * Displays the authenticated user's profile dashboard.
 */
class ProfileController extends Controller
{
    /**
     * Renders the profile page for logged-in users only.
     */
    public function index(): void
    {
        Auth::requireAuth();

        $this->render('user/profile', [
            'title' => 'Profile',
        ]);
    }
}
