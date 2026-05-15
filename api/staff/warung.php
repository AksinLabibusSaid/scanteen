<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\OrderListRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarungFulfillmentRepository;
use App\Repositories\OrderWriteRepository;
use App\Repositories\WarungRepository;
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

$action = trim((string) ($data['action'] ?? ''));
$venueId = (int) StaffAuth::venueId();
$role = StaffAuth::role();

switch ($action) {
    case 'fulfillment':
        $orderId = (int) ($data['order_id'] ?? 0);
        $status = trim((string) ($data['status'] ?? ''));
        if ($orderId < 1 || !in_array($status, ['new', 'preparing', 'ready'], true)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }
        $orderRepo = new OrderRepository();
        $order = $orderRepo->findById($orderId);
        if ($order === null || (int) $order['venue_id'] !== $venueId) {
            scanteen_staff_json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }
        if ($role === 'warung') {
            $warungId = StaffAuth::warungId();
            if ($warungId === null) {
                scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak terpasang'], 403);
            }
        } else {
            $warungId = (int) ($data['warung_id'] ?? 0);
        }
        if ($warungId < 1) {
            scanteen_staff_json(['ok' => false, 'error' => 'warung_id wajib'], 422);
        }
        $list = new OrderListRepository();
        if (!$list->warungOwnsOrderItem($orderId, $warungId)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak punya item di pesanan ini'], 422);
        }
        $ful = new OrderWarungFulfillmentRepository();
        $ok = $ful->updateStatus($orderId, $warungId, $status);
        if (!$ok) {
            scanteen_staff_json(['ok' => false, 'error' => 'Gagal update status'], 409);
        }
        if ($status === 'ready' && $ful->allReadyForOrder($orderId)) {
            (new OrderWriteRepository())->markReadyIfEligible($orderId);
        }
        scanteen_staff_json(['ok' => true]);
        break;

    case 'create':
        scanteen_staff_require_admin();
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            scanteen_staff_json(['ok' => false, 'error' => 'Nama warung wajib'], 422);
        }
        try {
            $id = (new WarungRepository())->insert($venueId, $name);
            scanteen_staff_json(['ok' => true, 'id' => $id]);
        } catch (\Throwable $e) {
            scanteen_staff_json(['ok' => false, 'error' => 'Gagal menyimpan'], 409);
        }
        break;

    case 'toggle':
        scanteen_staff_require_admin();
        $id = (int) ($data['id'] ?? 0);
        $isActive = (int) ($data['is_active'] ?? -1);
        if ($id <= 0 || ($isActive !== 0 && $isActive !== 1)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }
        if (!(new WarungRepository())->setActive($id, $venueId, $isActive)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak ditemukan'], 404);
        }
        scanteen_staff_json(['ok' => true]);
        break;

    case 'rename':
        scanteen_staff_require_admin();
        $id = (int) ($data['id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        if ($id <= 0 || $name === '') {
            scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }
        if (!(new WarungRepository())->updateName($id, $venueId, $name)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak ditemukan'], 404);
        }
        scanteen_staff_json(['ok' => true]);
        break;

    case 'delete':
        scanteen_staff_require_admin();
        $id = (int) ($data['id'] ?? 0);
        if ($id <= 0) {
            scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }
        if (!(new WarungRepository())->softDelete($id, $venueId)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak ditemukan'], 404);
        }
        scanteen_staff_json(['ok' => true]);
        break;

    default:
        scanteen_staff_json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        break;
}
