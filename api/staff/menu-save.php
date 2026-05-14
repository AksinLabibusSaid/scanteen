<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\MenuRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;

if (!StaffAuth::check()) {
    scanteen_staff_json(['ok' => false, 'error' => 'Unauthorized'], 401);
}

$role = StaffAuth::role();
if ($role !== 'admin' && $role !== 'warung') {
    scanteen_staff_json(['ok' => false, 'error' => 'Forbidden'], 403);
}

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
$menuRepo = new MenuRepository();
$warungRepo = new WarungRepository();

if ($action === 'create') {
    if ($role !== 'admin') {
        scanteen_staff_json(['ok' => false, 'error' => 'Hanya admin'], 403);
    }
    $wid = (int) ($data['warung_id'] ?? 0);
    $cat = (int) ($data['category_id'] ?? 0);
    $name = trim((string) ($data['name'] ?? ''));
    $price = (float) ($data['price'] ?? 0);
    $desc = isset($data['description']) ? (string) $data['description'] : '';
    $img = isset($data['image_url']) ? (string) $data['image_url'] : '';
    $avail = (int) ($data['is_available'] ?? 1);
    if ($wid <= 0 || $cat <= 0 || $name === '' || $price <= 0) {
        scanteen_staff_json(['ok' => false, 'error' => 'Data menu tidak lengkap'], 422);
    }
    if ($warungRepo->findByIdForVenue($wid, $venueId) === null) {
        scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak valid'], 422);
    }
    $id = $menuRepo->insertMenu($wid, $cat, $name, $desc, $price, $img, $avail ? 1 : 0);
    scanteen_staff_json(['ok' => true, 'id' => $id]);
}

if ($action === 'update') {
    $menuId = (int) ($data['menu_id'] ?? 0);
    $wid = (int) ($data['warung_id'] ?? 0);
    $cat = (int) ($data['category_id'] ?? 0);
    $name = trim((string) ($data['name'] ?? ''));
    $price = (float) ($data['price'] ?? 0);
    $desc = isset($data['description']) ? (string) $data['description'] : '';
    $img = isset($data['image_url']) ? (string) $data['image_url'] : '';
    $avail = (int) ($data['is_available'] ?? 1);
    if ($menuId <= 0 || $wid <= 0 || $cat <= 0 || $name === '' || $price <= 0) {
        scanteen_staff_json(['ok' => false, 'error' => 'Data menu tidak lengkap'], 422);
    }
    if (!$menuRepo->menuBelongsToVenue($menuId, $venueId)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Menu tidak ditemukan'], 404);
    }
    if ($warungRepo->findByIdForVenue($wid, $venueId) === null) {
        scanteen_staff_json(['ok' => false, 'error' => 'Warung tidak valid'], 422);
    }
    if ($role === 'warung') {
        $myWid = StaffAuth::warungId();
        if ($myWid === null || $myWid !== $wid || !$menuRepo->menuBelongsToWarung($menuId, $myWid)) {
            scanteen_staff_json(['ok' => false, 'error' => 'Forbidden'], 403);
        }
    }
    $menuRepo->updateMenu($menuId, $wid, $cat, $name, $desc, $price, $img, $avail ? 1 : 0);
    scanteen_staff_json(['ok' => true]);
}

scanteen_staff_json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
