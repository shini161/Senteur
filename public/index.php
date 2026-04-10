<?php

declare(strict_types=1);

// Front controller for every web request. Composer loads third-party packages
// and the app bootstrap wires the custom autoloader, routes, and request flow.
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';
