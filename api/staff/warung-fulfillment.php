<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\OrderListRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarungFulfillmentRepository;
use App\Repositories\OrderWriteRepository;
use App\Staff\StaffAuth;

scanteen_staff_require_roles(['admin', 'warung']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_staff_json(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_staff_json(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$orderId = (int) ($data['order_id'] ?? 0);
$status = trim((string) ($data['status'] ?? ''));
if ($orderId < 1 || !in_array($status, ['new', 'preparing', 'ready'], true)) {
    scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
}

$venueId = StaffAuth::venueId();
$orderRepo = new OrderRepository();
$order = $orderRepo->findById($orderId);
if ($order === null || (int) $order['venue_id'] !== $venueId) {
    scanteen_staff_json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
}

$role = StaffAuth::role();
if ($role === 'warung') {
    $warungId = StaffAuth::warungId();
    if ($warungId === null) {
        scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak terpasang'], 403);
    }
} else {
    $warungId = (int) ($data['warung_id'] ?? 0);
}

if ($warungId < 1) {
    scanteen_staff_json(['ok' => false, 'error' => 'warung_id wajib untuk admin'], 422);
}

$list = new OrderListRepository();
if (!$list->warungOwnsOrderItem($orderId, $warungId)) {
    scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak punya item di pesanan ini'], 422);
}

$ful = new OrderWarungFulfillmentRepository();
$ok = $ful->updateStatus($orderId, $warungId, $status);
if (!$ok) {
    scanteen_staff_json(['ok' => false, 'error' => 'Baris fulfillment tidak ditemukan'], 409);
}

if ($status === 'ready' && $ful->allReadyForOrder($orderId)) {
    (new OrderWriteRepository())->markReadyIfEligible($orderId);
}

scanteen_staff_json(['ok' => true]);
