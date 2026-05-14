<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\OrderWriteRepository;
use App\Staff\StaffAuth;

scanteen_staff_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_staff_json(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_staff_json(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$orderId = (int) ($data['order_id'] ?? 0);
if ($orderId <= 0) {
    scanteen_staff_json(['ok' => false, 'error' => 'order_id tidak valid'], 422);
}

$venueId = (int) StaffAuth::venueId();
$write = new OrderWriteRepository();
if (!$write->cancelPendingPaymentOrder($orderId, $venueId)) {
    scanteen_staff_json(['ok' => false, 'error' => 'Pesanan tidak bisa dibatalkan (bukan menunggu bayar / tidak ada).'], 409);
}

scanteen_staff_json(['ok' => true]);
