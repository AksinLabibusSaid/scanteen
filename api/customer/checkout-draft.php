<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

/** @var \App\Customer\CustomerContext $ctx */
[$ctx, $_cart, $_menus, $_builder] = scanteen_customer_api_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$name = trim((string) ($_POST['customer_name'] ?? ''));
$email = trim((string) ($_POST['customer_email'] ?? ''));
$dining = ($_POST['dining_type'] ?? 'dine_in') === 'take_away' ? 'take_away' : 'dine_in';

if ($name === '') {
    scanteen_json_response(['ok' => false, 'error' => 'Nama wajib diisi.'], 422);
}

(new \App\Services\CheckoutDraftService())->save([
    'name' => $name,
    'email' => $email,
    'dining_type' => $dining,
]);

scanteen_json_response(['ok' => true]);
