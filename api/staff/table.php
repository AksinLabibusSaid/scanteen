<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\DiningTableWriteRepository;
use App\Staff\StaffAuth;
use App\Support\TokenGenerator;

scanteen_staff_require_roles(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_staff_json(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_staff_json(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$action = (string) ($data['action'] ?? '');
$venueId = StaffAuth::venueId();
$write = new DiningTableWriteRepository();

switch ($action) {
    case 'create':
        $tableNumber = trim((string) ($data['table_number'] ?? ''));
        if ($tableNumber === '') {
            scanteen_staff_json(['ok' => false, 'error' => 'Nomor meja wajib diisi'], 422);
        }
        $token = TokenGenerator::tableScanToken();
        $id = $write->insert($venueId, $tableNumber, $token);
        scanteen_staff_json([
            'ok' => true,
            'table' => [
                'id' => $id,
                'table_number' => $tableNumber,
                'barcode_token' => $token,
            ],
        ]);
        break;

    case 'update':
        $id = (int) ($data['id'] ?? 0);
        $tableNumber = trim((string) ($data['table_number'] ?? ''));
        if ($id <= 0 || $tableNumber === '') {
            scanteen_staff_json(['ok' => false, 'error' => 'Data tidak lengkap'], 422);
        }
        $ok = $write->updateTableNumber($id, $venueId, $tableNumber);
        scanteen_staff_json(['ok' => $ok]);
        break;

    case 'toggle':
        $id = (int) ($data['id'] ?? 0);
        $isActive = isset($data['is_active']) ? (int) $data['is_active'] : null;
        if ($id <= 0 || ($isActive !== 0 && $isActive !== 1)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Data tidak lengkap'], 422);
        }
        $ok = $write->setActive($id, $venueId, $isActive);
        scanteen_staff_json(['ok' => $ok]);
        break;

    case 'delete':
        $id = (int) ($data['id'] ?? 0);
        if ($id <= 0) {
            scanteen_staff_json(['ok' => false, 'error' => 'Data tidak lengkap'], 422);
        }
        $ok = $write->softDelete($id, $venueId);
        scanteen_staff_json(['ok' => $ok]);
        break;

    default:
        scanteen_staff_json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        break;
}
