<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

/** @var \App\Customer\CustomerContext $ctx */
/** @var \App\Services\CartService $cart */
/** @var \App\Services\CartViewBuilder $builder */
[$ctx, $cart, $_menus, $builder] = scanteen_customer_api_bootstrap();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sum = $builder->summarize($ctx);
    scanteen_json_response(['ok' => true, 'cart' => $sum]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_json_response(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$action = isset($data['action']) ? (string) $data['action'] : '';

try {
    match ($action) {
        'add' => $cart->add(
            (int) ($data['menu_id'] ?? 0),
            max(1, (int) ($data['qty'] ?? 1)),
            (string) ($data['note'] ?? '')
        ),
        'set' => $cart->setQty(
            (int) ($data['menu_id'] ?? 0),
            max(0, (int) ($data['qty'] ?? 0)),
            (string) ($data['note'] ?? '')
        ),
        'remove' => $cart->remove((int) ($data['menu_id'] ?? 0)),
        'clear' => $cart->clear(),
        default => throw new \InvalidArgumentException('Aksi tidak dikenal.'),
    };
} catch (\Throwable $e) {
    scanteen_json_response(['ok' => false, 'error' => $e->getMessage()], 400);
}

$sum = $builder->summarize($ctx);
scanteen_json_response(['ok' => true, 'cart' => $sum]);
