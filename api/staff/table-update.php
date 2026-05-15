<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\DiningTableWriteRepository;
use App\Staff\StaffAuth;

scanteen_staff_require_roles(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_staff_json(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_staff_json(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$id = (int) ($data['id'] ?? 0);
$tableNumber = trim((string) ($data['table_number'] ?? ''));
if ($id <= 0 || $tableNumber === '') {
    scanteen_staff_json(['ok' => false, 'error' => 'Data tidak lengkap'], 422);
}

$venueId = StaffAuth::venueId();
$write = new DiningTableWriteRepository();
$ok = $write->updateTableNumber($id, $venueId, $tableNumber);

scanteen_staff_json(['ok' => $ok]);
