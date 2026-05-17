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

if ($action === 'clear') {
    $id = (int) ($data['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'error' => 'ID tidak valid']);
        exit;
    }
    
    try {
        $mysqli = \App\Core\Database::mysqli();
        
        // Ensure column exists (MySQL safe way)
        $res = $mysqli->query("SHOW COLUMNS FROM dining_tables LIKE 'last_cleared_at'");
        if ($res->num_rows === 0) {
            $mysqli->query("ALTER TABLE dining_tables ADD COLUMN last_cleared_at DATETIME NULL");
        }
        
        // 1. Batalkan pesanan yang belum dibayar
        $sql1 = "UPDATE orders SET status = 'cancelled' WHERE dining_table_id = ? AND status = 'pending_payment'";
        $stmt1 = $mysqli->prepare($sql1);
        $stmt1->bind_param('i', $id);
        $stmt1->execute();
        $stmt1->close();
        
        // 2. Selesaikan pesanan yang aktif (paid, accepted, processing, ready)
        $sql2 = "UPDATE orders SET status = 'completed' WHERE dining_table_id = ? AND status IN ('paid', 'accepted', 'processing', 'ready')";
        $stmt2 = $mysqli->prepare($sql2);
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $stmt2->close();
        
        // 3. Update last_cleared_at
        $now = date('Y-m-d H:i:s');
        $sql3 = "UPDATE dining_tables SET last_cleared_at = ? WHERE id = ?";
        $stmt3 = $mysqli->prepare($sql3);
        $stmt3->bind_param('si', $now, $id);
        $stmt3->execute();
        $stmt3->close();
        
        echo json_encode(['ok' => true]);
    } catch (\Throwable $e) {
        echo json_encode(['ok' => false, 'error' => 'Gagal membersihkan meja: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete') {
    $id = (int) ($data['id'] ?? 0);
    $ok = $repo->softDelete($id, $venueId);
    echo json_encode(['ok' => $ok]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'Aksi tidak dikenal']);
