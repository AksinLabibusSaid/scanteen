<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class OrderWarungFulfillmentRepository
{
    public function upsertStatuses(int $orderId, array $distinctWarungIds): void
    {
        $db = Database::mysqli();
        $sql = 'INSERT IGNORE INTO order_warung_fulfillment (order_id, warung_id, status) VALUES (?, ?, \'new\')';
        $stmt = $db->prepare($sql);
        foreach ($distinctWarungIds as $wid) {
            $w = (int) $wid;
            $stmt->bind_param('ii', $orderId, $w);
            $stmt->execute();
        }
        $stmt->close();
    }

    public function updateStatus(int $orderId, int $warungId, string $status): bool
    {
        if (!in_array($status, ['new', 'preparing', 'ready'], true)) {
            return false;
        }

        $sql = <<<SQL
            INSERT INTO order_warung_fulfillment (order_id, warung_id, status)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE status = VALUES(status)
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iis', $orderId, $warungId, $status);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function allReadyForOrder(int $orderId): bool
    {
        $sql = <<<SQL
            SELECT COUNT(*) AS total,
                   SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) AS ready_cnt
            FROM order_warung_fulfillment
            WHERE order_id = ?
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row === null) {
            return false;
        }

        $total = (int) $row['total'];
        $ready = (int) $row['ready_cnt'];

        return $total > 0 && $total === $ready;
    }
}
