<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class OrderRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function itemsByOrderId(int $orderId): array
    {
        $sql = <<<SQL
            SELECT
                oi.id,
                oi.menu_id,
                oi.warung_id,
                oi.menu_name_snapshot,
                oi.unit_price,
                oi.quantity,
                oi.note,
                oi.line_subtotal,
                w.name AS warung_name
            FROM order_items oi
            INNER JOIN warungs w ON w.id = oi.warung_id
            WHERE oi.order_id = ?
            ORDER BY w.sort_order ASC, oi.id ASC
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    public function findByPublicToken(string $token): ?array
    {
        $token = trim($token);
        if ($token === '' || strlen($token) !== 32) {
            return null;
        }

        $sql = <<<SQL
            SELECT
                o.*,
                dt.table_number,
                dt.barcode_token
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            WHERE o.public_token = ?
            LIMIT 1
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    public function findById(int $orderId): ?array
    {
        $sql = <<<SQL
            SELECT o.*, dt.table_number
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            WHERE o.id = ?
            LIMIT 1
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    public function findByOrderNumber(string $orderNumber): ?array
    {
        $orderNumber = trim($orderNumber);
        if ($orderNumber === '') {
            return null;
        }

        $sql = <<<SQL
            SELECT o.*, dt.table_number
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            WHERE o.order_number = ?
            LIMIT 1
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('s', $orderNumber);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    /**
     * Pesanan aktif terakhir untuk meja (untuk banner home).
     */
    public function findLatestTrackableForTable(int $diningTableId): ?array
    {
        $sql = <<<SQL
            SELECT *
            FROM orders
            WHERE dining_table_id = ?
              AND status NOT IN ('completed','cancelled')
            ORDER BY id DESC
            LIMIT 1
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $diningTableId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    /**
     * Semua pesanan aktif untuk meja (untuk banner home).
     *
     * @return list<array<string, mixed>>
     */
    public function findAllTrackableForTable(int $diningTableId): array
    {
        $sql = <<<SQL
            SELECT *
            FROM orders
            WHERE dining_table_id = ?
              AND status NOT IN ('completed','cancelled')
            ORDER BY id DESC
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $diningTableId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function groupItemsByWarung(int $orderId): array
    {
        $items = $this->itemsByOrderId($orderId);
        $groups = [];
        foreach ($items as $item) {
            $wid = (int) $item['warung_id'];
            if (!isset($groups[$wid])) {
                $groups[$wid] = [
                    'warung_id' => $wid,
                    'warung_name' => (string) $item['warung_name'],
                    'items' => [],
                ];
            }
            $groups[$wid]['items'][] = $item;
        }

        return array_values($groups);
    }

    public function getDetailWithGroups(int $orderId): ?array
    {
        $order = $this->findById($orderId);
        if ($order === null) {
            return null;
        }

        // Add display_number for consistency with UI
        $order['display_number'] = $order['order_number'];

        return [
            'order' => $order,
            'groups' => $this->groupItemsByWarung($orderId),
        ];
    }

    public function getWarungOrderDetail(int $orderId, int $warungId): ?array
    {
        $order = $this->findById($orderId);
        if ($order === null) {
            return null;
        }

        // Fetch fulfillment status for this specific warung
        $sql = 'SELECT status, updated_at FROM order_warung_fulfillment WHERE order_id = ? AND warung_id = ? LIMIT 1';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $orderId, $warungId);
        $stmt->execute();
        $ful = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Fetch only items for this warung
        $sqlItems = <<<SQL
            SELECT oi.menu_name_snapshot, oi.quantity, oi.unit_price, oi.line_subtotal, oi.note
            FROM order_items oi
            WHERE oi.order_id = ? AND oi.warung_id = ?
            SQL;
        $stmtItems = Database::mysqli()->prepare($sqlItems);
        $stmtItems->bind_param('ii', $orderId, $warungId);
        $stmtItems->execute();
        $resItems = $stmtItems->get_result();
        $items = [];
        while ($item = $resItems->fetch_assoc()) {
            $items[] = $item;
        }
        $stmtItems->close();

        return [
            'order' => $order,
            'fulfillment_status' => $ful['status'] ?? 'new',
            'fulfillment_updated_at' => $ful['updated_at'] ?? null,
            'items' => $items,
        ];
    }
}
