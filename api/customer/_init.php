<?php

declare(strict_types=1);

session_start();

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_once SCANTEEN_ROOT . '/config/db.php';

use App\Customer\CustomerAccess;
use App\Customer\CustomerContext;
use App\Repositories\MenuRepository;
use App\Services\CartService;
use App\Services\CartViewBuilder;

/**
 * @return array{0:CustomerContext,1:CartService,2:MenuRepository,3:CartViewBuilder}
 */
function scanteen_customer_api_bootstrap(): array
{
    $ctx = CustomerAccess::contextFromSession();
    if (!$ctx instanceof CustomerContext) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'Sesi meja tidak valid. Scan QR meja terlebih dahulu.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cart = new CartService();
    $menus = new MenuRepository();
    $builder = new CartViewBuilder($menus, $cart);

    return [$ctx, $cart, $menus, $builder];
}

function scanteen_json_response(array $payload, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
