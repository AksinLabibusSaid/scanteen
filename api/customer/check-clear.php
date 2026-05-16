<?php

declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
use App\Customer\CustomerSessionKeys;

session_start();

$cid = $_SESSION[CustomerSessionKeys::TABLE_ID] ?? 0;
if ($cid <= 0) {
    echo json_encode(['cleared' => false]);
    exit;
}

try {
    $mysqli = \App\Core\Database::mysqli();
    $stmt = $mysqli->prepare("SELECT last_cleared_at FROM dining_tables WHERE id = ?");
    $stmt->bind_param('i', $cid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $dbCleared = $row['last_cleared_at'] ?? null;
    $sessionCleared = $_SESSION['customer_last_seen_cleared_at'] ?? null;
    
    if ($dbCleared !== null && $sessionCleared !== $dbCleared) {
        echo json_encode(['cleared' => true]);
    } else {
        echo json_encode(['cleared' => false]);
    }
} catch (\Throwable $e) {
    echo json_encode(['cleared' => false]);
}
