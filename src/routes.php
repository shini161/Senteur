<?php

use App\Controllers\HomeController;

return [
    // [HTTP METHOD, URL PATH, [Controller, Method]]

    // Home page
    ['GET', '/', [HomeController::class, 'index']],
];
