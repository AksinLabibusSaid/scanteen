<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\WarungRepository;
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

$venueId = (int) StaffAuth::venueId();
$action = trim((string) ($data['action'] ?? ''));
$repo = new WarungRepository();

if ($action === 'create') {
    $name = trim((string) ($data['name'] ?? ''));
    if ($name === '') {
        scanteen_staff_json(['ok' => false, 'error' => 'Nama warung wajib'], 422);
    }
    try {
        $id = $repo->insert($venueId, $name);
    } catch (\Throwable $e) {
        scanteen_staff_json(['ok' => false, 'error' => 'Gagal menyimpan (slug duplikat?)'], 409);
    }
    scanteen_staff_json(['ok' => true, 'id' => $id]);
}

if ($action === 'toggle') {
    $id = (int) ($data['id'] ?? 0);
    $isActive = (int) ($data['is_active'] ?? -1);
    if ($id <= 0 || ($isActive !== 0 && $isActive !== 1)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
    }
    if (!$repo->setActive($id, $venueId, $isActive)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak ditemukan'], 404);
    }
    scanteen_staff_json(['ok' => true]);
}

if ($action === 'rename') {
    $id = (int) ($data['id'] ?? 0);
    $name = trim((string) ($data['name'] ?? ''));
    if ($id <= 0 || $name === '') {
        scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
    }
    if (!$repo->updateName($id, $venueId, $name)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak ditemukan'], 404);
    }
    scanteen_staff_json(['ok' => true]);
}

scanteen_staff_json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
