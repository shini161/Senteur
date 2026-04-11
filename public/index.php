<?php

declare(strict_types=1);

// Front controller for every web request. Composer loads third-party packages
// and the app bootstrap wires routes, helpers, and the request lifecycle.
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('App\\', __DIR__ . '/../src');

require __DIR__ . '/../src/bootstrap.php';
