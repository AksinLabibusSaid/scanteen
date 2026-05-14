<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Services\OrderCreationService;

/** @var \App\Customer\CustomerContext $ctx */
[$ctx, $cart, $menus, $_builder] = scanteen_customer_api_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_json_response(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$method = (string) ($data['payment_method'] ?? '');
if ($method === 'kasir') {
    $method = 'cashier';
}
if (!in_array($method, ['qris', 'cashier'], true)) {
    scanteen_json_response(['ok' => false, 'error' => 'Metode pembayaran tidak valid.'], 422);
}

$service = new OrderCreationService($menus, $cart);

try {
    $result = $service->placeOrder($ctx, $method);
} catch (\InvalidArgumentException $e) {
    scanteen_json_response(['ok' => false, 'error' => $e->getMessage()], 422);
} catch (\Throwable $e) {
    $payload = ['ok' => false, 'error' => 'Gagal membuat pesanan.'];
    if (defined('SCANTEEN_CUSTOMER_SIMULATE_PAYMENT') && SCANTEEN_CUSTOMER_SIMULATE_PAYMENT === true) {
        $payload['detail'] = $e->getMessage();
    }
    scanteen_json_response($payload, 500);
}

scanteen_json_response([
    'ok' => true,
    'order' => [
        'public_token' => $result['public_token'],
        'order_number' => $result['order_number'],
    ],
]);
