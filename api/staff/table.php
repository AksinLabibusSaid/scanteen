<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\DiningTableWriteRepository;
use App\Staff\StaffAuth;

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $data['action'] ?? '';
$venueId = (int) StaffAuth::venueId();
$repo = new DiningTableWriteRepository();

if ($action === 'create') {
    $num = trim((string) ($data['table_number'] ?? ''));
    if ($num === '') {
        echo json_encode(['ok' => false, 'error' => 'Nomor meja harus diisi']);
        exit;
    }
    // Simple unique token
    $token = 'TBL-' . bin2hex(random_bytes(4));
    $id = $repo->insert($venueId, $num, $token);
    echo json_encode(['ok' => $id > 0, 'id' => $id]);
    exit;
}

if ($action === 'update') {
    $id = (int) ($data['id'] ?? 0);
    $num = trim((string) ($data['table_number'] ?? ''));
    if ($id <= 0 || $num === '') {
        echo json_encode(['ok' => false, 'error' => 'Data tidak valid']);
        exit;
    }
    $ok = $repo->updateTableNumber($id, $venueId, $num);
    echo json_encode(['ok' => $ok]);
    exit;
}

if ($action === 'toggle') {
    $id = (int) ($data['id'] ?? 0);
    $active = (int) ($data['is_active'] ?? 0);
    $ok = $repo->setActive($id, $venueId, $active);
    echo json_encode(['ok' => $ok]);
    exit;
}

if ($action === 'delete') {
    $id = (int) ($data['id'] ?? 0);
    $ok = $repo->delete($id, $venueId);
    echo json_encode(['ok' => $ok]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'Aksi tidak dikenal']);
