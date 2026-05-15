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

$action = trim((string) ($data['action'] ?? ''));
$venueId = (int) StaffAuth::venueId();
$write = new OrderWriteRepository();

switch ($action) {
    case 'mark-paid':
        $publicToken = trim((string) ($data['public_token'] ?? ''));
        if (strlen($publicToken) !== 32) {
            scanteen_staff_json(['ok' => false, 'error' => 'Token tidak valid'], 422);
        }
        $repo = new OrderRepository();
        $order = $repo->findByPublicToken($publicToken);
        if ($order === null || (int) $order['venue_id'] !== $venueId) {
            scanteen_staff_json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }
        $ok = $write->markPaidByPublicToken($publicToken);
        if (!$ok) {
            scanteen_staff_json(['ok' => false, 'error' => 'Status tidak diubah'], 409);
        }
        scanteen_staff_json(['ok' => true]);
        break;

    case 'cancel':
        scanteen_staff_require_admin();
        $orderId = (int) ($data['order_id'] ?? 0);
        if ($orderId <= 0) {
            scanteen_staff_json(['ok' => false, 'error' => 'ID tidak valid'], 422);
        }
        if (!$write->cancelPendingPaymentOrder($orderId, $venueId)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Pesanan tidak bisa dibatalkan'], 409);
        }
        scanteen_staff_json(['ok' => true]);
        break;

    default:
        scanteen_staff_json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        break;
}
