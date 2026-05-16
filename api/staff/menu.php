<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\MenuRepository;
use App\Staff\StaffAuth;

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $data['action'] ?? '';
$venueId = (int) StaffAuth::venueId();
$repo = new MenuRepository();

if ($action === 'create' || $action === 'update') {
    $warungId = (int) ($data['warung_id'] ?? 0);
    $categoryId = (int) ($data['category_id'] ?? 0);
    $name = trim((string) ($data['name'] ?? ''));
    $price = (float) ($data['price'] ?? 0);
    $desc = (string) ($data['description'] ?? '');
    $imgUrl = (string) ($data['image_url'] ?? '');
    $isAvailable = (int) ($data['is_available'] ?? 1);

    if ($warungId <= 0 || $categoryId <= 0 || $name === '') {
        echo json_encode(['ok' => false, 'error' => 'Data tidak lengkap']);
        exit;
    }

    if ($action === 'create') {
        $id = $repo->insertMenu($warungId, $categoryId, $name, $desc, $price, $imgUrl, $isAvailable);
        echo json_encode(['ok' => $id > 0, 'id' => $id]);
    } else {
        $menuId = (int) ($data['menu_id'] ?? 0);
        $ok = $repo->updateMenu($menuId, $warungId, $categoryId, $name, $desc, $price, $imgUrl, $isAvailable);
        echo json_encode(['ok' => $ok]);
    }
    exit;
}

if ($action === 'availability') {
    $menuId = (int) ($data['menu_id'] ?? 0);
    $next = (int) ($data['is_available'] ?? 0);
    // basic check
    if ($repo->menuBelongsToVenue($menuId, $venueId)) {
        $ok = $repo->setAvailability($menuId, $next);
        echo json_encode(['ok' => $ok]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Akses ditolak']);
    }
    exit;
}

if ($action === 'stock') {
    $menuId = (int) ($data['menu_id'] ?? 0);
    $newStock = (int) ($data['stock'] ?? 0);
    $warungId = (int) StaffAuth::warungId();
    
    if ($warungId > 0 && $repo->menuBelongsToWarung($menuId, $warungId)) {
        $ok = $repo->updateStock($menuId, $warungId, $newStock);
        echo json_encode(['ok' => $ok]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Akses ditolak']);
    }
    exit;
}

echo json_encode(['ok' => false, 'error' => 'Aksi tidak dikenal']);
