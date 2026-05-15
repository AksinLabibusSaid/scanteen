<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\StaffUserRepository;
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
$repo = new StaffUserRepository();
$warungRepo = new WarungRepository();

if ($action === 'create') {
    $email = trim((string) ($data['email'] ?? ''));
    $password = (string) ($data['password'] ?? '');
    $name = trim((string) ($data['name'] ?? ''));
    $role = trim((string) ($data['role'] ?? ''));
    $warungId = isset($data['warung_id']) && $data['warung_id'] !== '' && $data['warung_id'] !== null
        ? (int) $data['warung_id']
        : null;
    if ($email === '' || strlen($password) < 6 || $name === '') {
        scanteen_staff_json(['ok' => false, 'error' => 'Email, nama, dan sandi (min. 6 karakter) wajib'], 422);
    }
    if (!in_array($role, ['admin', 'kasir', 'warung'], true)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Peran tidak valid'], 422);
    }
    if ($role === 'warung') {
        if ($warungId === null || $warungId <= 0) {
            scanteen_staff_json(['ok' => false, 'error' => 'warung_id wajib untuk peran warung'], 422);
        }
        if ($warungRepo->findByIdForVenue($warungId, $venueId) === null) {
            scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak valid'], 422);
        }
    } else {
        $warungId = null;
    }
    try {
        $id = $repo->insert($venueId, $email, $password, $name, $role, $warungId);
    } catch (\Throwable $e) {
        scanteen_staff_json(['ok' => false, 'error' => 'Gagal simpan (email duplikat?)'], 409);
    }
    scanteen_staff_json(['ok' => true, 'id' => $id]);
}

if ($action === 'toggle') {
    $id = (int) ($data['id'] ?? 0);
    $isActive = (int) ($data['is_active'] ?? -1);
    if ($id <= 0 || ($isActive !== 0 && $isActive !== 1)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
    }
    $self = StaffAuth::userId();
    if ($self !== null && $id === $self) {
        scanteen_staff_json(['ok' => false, 'error' => 'Tidak dapat menonaktifkan akun sendiri'], 422);
    }
    if (!$repo->setActive($id, $venueId, $isActive)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Pengguna tidak ditemukan'], 404);
    }
    scanteen_staff_json(['ok' => true]);
}

if ($action === 'password') {
    $id = (int) ($data['id'] ?? 0);
    $password = (string) ($data['password'] ?? '');
    if ($id <= 0 || strlen($password) < 6) {
        scanteen_staff_json(['ok' => false, 'error' => 'ID dan sandi baru (min. 6) wajib'], 422);
    }
    if (!$repo->updatePassword($id, $venueId, $password)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Pengguna tidak ditemukan'], 404);
    }
    scanteen_staff_json(['ok' => true]);
}

scanteen_staff_json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
