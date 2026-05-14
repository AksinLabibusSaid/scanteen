<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\OrderRepository;
use App\Repositories\OrderWriteRepository;
use App\Staff\StaffAuth;

scanteen_staff_require_roles(['admin', 'kasir']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_staff_json(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_staff_json(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$publicToken = trim((string) ($data['public_token'] ?? ''));
if (strlen($publicToken) !== 32) {
    scanteen_staff_json(['ok' => false, 'error' => 'Token tidak valid'], 422);
}

$venueId = StaffAuth::venueId();
$repo = new OrderRepository();
$order = $repo->findByPublicToken($publicToken);
if ($order === null || (int) $order['venue_id'] !== $venueId) {
    scanteen_staff_json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
}

$write = new OrderWriteRepository();
$ok = $write->markPaidByPublicToken($publicToken);
if (!$ok) {
    scanteen_staff_json(['ok' => false, 'error' => 'Status tidak diubah (bukan menunggu bayar).'], 409);
}

scanteen_staff_json(['ok' => true]);
