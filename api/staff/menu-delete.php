<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Repositories\MenuRepository;
use App\Staff\StaffAuth;

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$menuId = (int) ($data['menu_id'] ?? 0);
$venueId = (int) StaffAuth::venueId();
$repo = new MenuRepository();

// Basic security check: ensure menu belongs to this venue
$mysqli = App\Core\Database::mysqli();
$sql = 'SELECT m.warung_id FROM menus m JOIN warungs w ON w.id = m.warung_id WHERE m.id = ? AND w.venue_id = ?';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $menuId, $venueId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['ok' => false, 'error' => 'Menu tidak ditemukan atau akses ditolak']);
    exit;
}

$warungId = (int) $row['warung_id'];
$ok = $repo->deleteMenu($menuId, $warungId);

echo json_encode(['ok' => $ok]);
