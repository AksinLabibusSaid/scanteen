<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\MenuRepository;
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
$menuId = (int) ($data['menu_id'] ?? 0);
$isAvailable = (int) ($data['is_available'] ?? -1);
if ($menuId <= 0 || ($isAvailable !== 0 && $isAvailable !== 1)) {
    scanteen_staff_json(['ok' => false, 'error' => 'Data tidak valid'], 422);
}

$menuRepo = new MenuRepository();
if (!$menuRepo->menuBelongsToVenue($menuId, $venueId)) {
    scanteen_staff_json(['ok' => false, 'error' => 'Menu tidak ditemukan'], 404);
}

if ($role === 'warung') {
    $wid = StaffAuth::warungId();
    if ($wid === null || !$menuRepo->menuBelongsToWarung($menuId, $wid)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Forbidden'], 403);
    }
}

$menuRepo->setAvailability($menuId, $isAvailable);
scanteen_staff_json(['ok' => true]);
