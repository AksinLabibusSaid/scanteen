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

$tableNumber = trim((string) ($data['table_number'] ?? ''));
if ($tableNumber === '') {
    scanteen_staff_json(['ok' => false, 'error' => 'Nomor meja wajib diisi'], 422);
}

$venueId = StaffAuth::venueId();
$token = TokenGenerator::tableScanToken();
$write = new DiningTableWriteRepository();
$id = $write->insert($venueId, $tableNumber, $token);

scanteen_staff_json([
    'ok' => true,
    'table' => [
        'id' => $id,
        'table_number' => $tableNumber,
        'barcode_token' => $token,
    ],
]);
