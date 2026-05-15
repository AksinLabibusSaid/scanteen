<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;
use App\Controllers\Admin\AdminController;

$router = new Router();

// Dashboard & Login Default
$router->add('GET', '/', function() {
    header('Location: /scanteen/admin');
});

// Admin Routes
$router->add('GET', '/admin', [AdminController::class, 'dashboard']);
$router->add('GET', '/admin/orders', [AdminController::class, 'orders']);
$router->add('GET', '/admin/tenants', [AdminController::class, 'tenants']);
$router->add('GET', '/admin/menus', [AdminController::class, 'menus']);
$router->add('GET', '/admin/users', [AdminController::class, 'users']);

$router->run();
