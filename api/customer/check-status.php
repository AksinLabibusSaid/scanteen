<?php
declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
use App\Customer\CustomerSessionKeys;
use App\Repositories\OrderRepository;

date_default_timezone_set('Asia/Jakarta');

session_start();

$cid = $_SESSION[CustomerSessionKeys::TABLE_ID] ?? 0;
if ($cid <= 0) {
    echo json_encode(['cleared' => false, 'order_changed' => false, 'venue_changed' => false]);
    exit;
}

try {
    $mysqli = \App\Core\Database::mysqli();
    
    // 1. Check table cleared
    $stmt = $mysqli->prepare("SELECT last_cleared_at FROM dining_tables WHERE id = ?");
    $stmt->bind_param('i', $cid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $dbCleared = $row['last_cleared_at'] ?? null;
    $sessionCleared = $_SESSION['customer_last_seen_cleared_at'] ?? null;
    
    $cleared = ($dbCleared !== null && $sessionCleared !== $dbCleared);

    // 2. Check order status
    $orderRepo = new OrderRepository();
    $activeOrders = $orderRepo->findAllTrackableForTable($cid);
    
    $currentStatusStr = '';
    foreach ($activeOrders as $o) {
        $currentStatusStr .= $o['id'] . ':' . $o['status'] . ',';
    }
    
    $sessionStatusStr = $_SESSION['customer_last_seen_order_statuses'] ?? '';
    
    $orderChanged = ($sessionStatusStr !== $currentStatusStr);

    // 3. Check venue settings (Disabled temporarily due to missing columns in venues table)
    $venueChanged = false;
    /*
    $venueId = $_SESSION[CustomerSessionKeys::VENUE_ID] ?? 0;
    if ($venueId > 0) {
        $stmt = $mysqli->prepare("SELECT maintenance_mode, operating_hours, allow_qris, allow_cash, allow_debit FROM venues WHERE id = ?");
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $v = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $currentVenueState = md5(json_encode($v));
        $sessionVenueState = $_SESSION['customer_last_seen_venue_state'] ?? '';
        
        $venueChanged = ($sessionVenueState !== $currentVenueState);
    }
    */

    // 4. Auto-clear table after 5 minutes of completion
    $stmt = $mysqli->prepare("SELECT id, status, updated_at FROM orders WHERE dining_table_id = ?");
    $stmt->bind_param('i', $cid);
    $stmt->execute();
    $ordersResult = $stmt->get_result();
    $orders = $ordersResult->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (!empty($orders)) {
        $allCompletedOrCancelled = true;
        $maxUpdatedAt = null;

        foreach ($orders as $o) {
            if (!in_array($o['status'], ['completed', 'cancelled'], true)) {
                $allCompletedOrCancelled = false;
                break;
            }
            if ($maxUpdatedAt === null || $o['updated_at'] > $maxUpdatedAt) {
                $maxUpdatedAt = $o['updated_at'];
            }
        }

        if ($allCompletedOrCancelled && $maxUpdatedAt !== null) {
            $stmt = $mysqli->prepare("SELECT last_cleared_at FROM dining_tables WHERE id = ?");
            $stmt->bind_param('i', $cid);
            $stmt->execute();
            $tableRow = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $lastClearedAt = $tableRow['last_cleared_at'] ?? null;

            if ($lastClearedAt === null || $lastClearedAt < $maxUpdatedAt) {
                $maxTime = strtotime($maxUpdatedAt);
                $nowTime = time();
                
                // 5 minutes = 300 seconds
                if (($nowTime - $maxTime) >= 300) {
                    // Perform clear
                    $sql1 = "UPDATE orders SET status = 'cancelled' WHERE dining_table_id = ? AND status = 'pending_payment'";
                    $stmt1 = $mysqli->prepare($sql1);
                    $stmt1->bind_param('i', $cid);
                    $stmt1->execute();
                    $stmt1->close();
                    
                    $sql2 = "UPDATE orders SET status = 'completed' WHERE dining_table_id = ? AND status IN ('paid', 'accepted', 'processing', 'ready')";
                    $stmt2 = $mysqli->prepare($sql2);
                    $stmt2->bind_param('i', $cid);
                    $stmt2->execute();
                    $stmt2->close();
                    
                    $now = date('Y-m-d H:i:s');
                    $sql3 = "UPDATE dining_tables SET last_cleared_at = ? WHERE id = ?";
                    $stmt3 = $mysqli->prepare($sql3);
                    $stmt3->bind_param('si', $now, $cid);
                    $stmt3->execute();
                    $stmt3->close();

                    $cleared = true;
                }
            }
        }
    }

    echo json_encode([
        'cleared' => $cleared,
        'order_changed' => $orderChanged,
        'venue_changed' => $venueChanged
    ]);
} catch (\Throwable $e) {
    echo json_encode(['cleared' => false, 'order_changed' => false, 'venue_changed' => false]);
}
