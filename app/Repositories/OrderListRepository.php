<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class OrderListRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function listForVenue(int $venueId, int $limit = 200): array
    {
        $limit = max(1, min(500, $limit));
        $sql = <<<SQL
            SELECT
                o.id,
                o.order_number,
                CONCAT(
                    'ORD-',
                    DATE_FORMAT(o.created_at, '%m%d'),
                    '-',
                    LPAD(
                        (
                            SELECT COUNT(*)
                            FROM orders o2
                            WHERE o2.venue_id = o.venue_id
                              AND DATE(o2.created_at) = DATE(o.created_at)
                              AND (
                                  o2.created_at < o.created_at
                                  OR (o2.created_at = o.created_at AND o2.id <= o.id)
                              )
                        ),
                        3,
                        '0'
                    )
                ) AS display_order_number,
                o.public_token,
                o.customer_name,
                o.status,
                o.payment_method,
                o.total,
                o.created_at,
                dt.table_number,
                (
                    SELECT GROUP_CONCAT(DISTINCT w.name ORDER BY w.sort_order SEPARATOR ', ')
                    FROM order_items oi
                    INNER JOIN warungs w ON w.id = oi.warung_id
                    WHERE oi.order_id = o.id
                ) AS tenant_names
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            WHERE o.venue_id = ?
            ORDER BY o.id DESC
            LIMIT {$limit}
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Pesanan yang mengandung item dari warung tertentu.
     *
     * @return list<array<string, mixed>>
     */
    public function listForWarung(int $venueId, int $warungId, int $limit = 200): array
    {
        $limit = max(1, min(500, $limit));
        $sql = <<<SQL
            SELECT
                o.id,
                o.order_number,
                o.public_token,
                o.customer_name,
                o.status,
                o.total,
                o.created_at,
                dt.table_number,
                (
                    SELECT f.status
                    FROM order_warung_fulfillment f
                    WHERE f.order_id = o.id AND f.warung_id = ?
                    LIMIT 1
                ) AS warung_fulfillment_status,
                (
                    SELECT GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.menu_name_snapshot) SEPARATOR ', ')
                    FROM order_items oi
                    WHERE oi.order_id = o.id AND oi.warung_id = ?
                ) AS warung_items_summary
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            WHERE o.venue_id = ?
              AND EXISTS (
                SELECT 1 FROM order_items oi
                WHERE oi.order_id = o.id AND oi.warung_id = ?
              )
              AND o.status NOT IN ('cancelled','completed','pending_payment')
            ORDER BY o.id DESC
            LIMIT {$limit}
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iiii', $warungId, $warungId, $venueId, $warungId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    public function warungOwnsOrderItem(int $orderId, int $warungId): bool
    {
        $sql = 'SELECT 1 FROM order_items WHERE order_id = ? AND warung_id = ? LIMIT 1';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $orderId, $warungId);
        $stmt->execute();
        $ok = $stmt->get_result()->fetch_row() !== null;
        $stmt->close();

        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listHistoryForWarung(int $venueId, int $warungId, int $limit = 100): array
    {
        $limit = max(1, min(300, $limit));
        $sql = <<<SQL
            SELECT DISTINCT
                o.id,
                o.order_number,
                o.public_token,
                o.customer_name,
                o.status,
                o.total,
                o.created_at,
                o.updated_at,
                dt.table_number,
                (
                    SELECT GROUP_CONCAT(CONCAT(oi2.quantity, 'x ', oi2.menu_name_snapshot) SEPARATOR ', ')
                    FROM order_items oi2
                    WHERE oi2.order_id = o.id AND oi2.warung_id = ?
                ) AS warung_items_summary
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            INNER JOIN order_items oi ON oi.order_id = o.id AND oi.warung_id = ?
            WHERE o.venue_id = ?
              AND o.status IN ('completed', 'cancelled')
            ORDER BY o.id DESC
            LIMIT {$limit}
            SQL;
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iii', $warungId, $warungId, $venueId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listCompletedForWarung(int $venueId, int $warungId, int $limit = 100): array
    {
        $limit = max(1, min(300, $limit));
        $sql = <<<SQL
            SELECT DISTINCT
                o.id,
                o.order_number,
                o.public_token,
                o.customer_name,
                o.status,
                o.total,
                o.created_at,
                dt.table_number,
                (
                    SELECT GROUP_CONCAT(CONCAT(oi2.quantity, 'x ', oi2.menu_name_snapshot) SEPARATOR ', ')
                    FROM order_items oi2
                    WHERE oi2.order_id = o.id AND oi2.warung_id = ?
                ) AS warung_items_summary
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            INNER JOIN order_items oi ON oi.order_id = o.id AND oi.warung_id = ?
            WHERE o.venue_id = ?
              AND o.status = 'completed'
            ORDER BY o.id DESC
            LIMIT {$limit}
            SQL;
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iii', $warungId, $warungId, $venueId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Daftar pesanan untuk admin/kasir dengan filter opsional.
     *
     * @return list<array<string, mixed>>
     */
    public function listForVenueFiltered(
        int $venueId,
        int $limit = 20,
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $paymentMethod = null,
        ?int $warungId = null,
        ?string $orderSearch = null,
        int $offset = 0
    ): array {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);
        $statuses = [
            'pending_payment', 'paid', 'accepted', 'processing', 'ready', 'completed', 'cancelled',
        ];
        $payments = ['qris', 'cashier'];

        $where = ['o.venue_id = ?'];
        $types = 'i';
        $params = [$venueId];

        if ($status !== null && $status !== '' && in_array($status, $statuses, true)) {
            $where[] = 'o.status = ?';
            $types .= 's';
            $params[] = $status;
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            if ($dateTo !== null && $dateTo !== '' && $dateFrom !== $dateTo) {
                $where[] = 'DATE(o.created_at) BETWEEN ? AND ?';
                $types .= 'ss';
                $params[] = $dateFrom;
                $params[] = $dateTo;
            } else {
                $where[] = 'DATE(o.created_at) = ?';
                $types .= 's';
                $params[] = $dateFrom;
            }
        }
        if ($paymentMethod !== null && $paymentMethod !== '' && in_array($paymentMethod, $payments, true)) {
            $where[] = 'o.payment_method = ?';
            $types .= 's';
            $params[] = $paymentMethod;
        }
        if ($warungId !== null && $warungId > 0) {
            $where[] = 'EXISTS (SELECT 1 FROM order_items oi2 WHERE oi2.order_id = o.id AND oi2.warung_id = ?)';
            $types .= 'i';
            $params[] = $warungId;
        }
        if ($orderSearch !== null && trim($orderSearch) !== '') {
            $like = '%' . trim($orderSearch) . '%';
            $where[] = '(o.order_number LIKE ? OR o.public_token LIKE ? OR o.customer_name LIKE ?)';
            $types .= 'sss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql = 'SELECT o.id, o.order_number, '
            . "CONCAT('ORD-', DATE_FORMAT(o.created_at, '%m%d'), '-', LPAD((SELECT COUNT(*) FROM orders o2 WHERE o2.venue_id = o.venue_id AND DATE(o2.created_at) = DATE(o.created_at) AND (o2.created_at < o.created_at OR (o2.created_at = o.created_at AND o2.id <= o.id))), 3, '0')) AS display_order_number, "
            . 'o.public_token, o.customer_name, o.customer_email, o.status, '
            . 'o.payment_method, o.total, o.subtotal, o.service_tax, o.created_at, dt.table_number, '
            . '('
            . "SELECT GROUP_CONCAT(DISTINCT w.name ORDER BY w.sort_order SEPARATOR ', ') "
            . 'FROM order_items oi3 INNER JOIN warungs w ON w.id = oi3.warung_id '
            . 'WHERE oi3.order_id = o.id'
            . ') AS tenant_names '
            . 'FROM orders o INNER JOIN dining_tables dt ON dt.id = o.dining_table_id WHERE '
            . implode(' AND ', $where)
            . ' ORDER BY o.id DESC LIMIT ? OFFSET ?';
        
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    public function countForVenueFiltered(
        int $venueId,
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $paymentMethod = null,
        ?int $warungId = null,
        ?string $orderSearch = null
    ): int {
        $statuses = ['pending_payment', 'paid', 'accepted', 'processing', 'ready', 'completed', 'cancelled'];
        $payments = ['qris', 'cashier'];

        $where = ['o.venue_id = ?'];
        $types = 'i';
        $params = [$venueId];

        if ($status !== null && $status !== '' && in_array($status, $statuses, true)) {
            $where[] = 'o.status = ?';
            $types .= 's';
            $params[] = $status;
        }
        
        if ($dateFrom !== null && $dateFrom !== '') {
            if ($dateTo !== null && $dateTo !== '' && $dateFrom !== $dateTo) {
                $where[] = 'DATE(o.created_at) BETWEEN ? AND ?';
                $types .= 'ss';
                $params[] = $dateFrom;
                $params[] = $dateTo;
            } else {
                $where[] = 'DATE(o.created_at) = ?';
                $types .= 's';
                $params[] = $dateFrom;
            }
        }

        if ($paymentMethod !== null && $paymentMethod !== '' && in_array($paymentMethod, $payments, true)) {
            $where[] = 'o.payment_method = ?';
            $types .= 's';
            $params[] = $paymentMethod;
        }
        if ($warungId !== null && $warungId > 0) {
            $where[] = 'EXISTS (SELECT 1 FROM order_items oi2 WHERE oi2.order_id = o.id AND oi2.warung_id = ?)';
            $types .= 'i';
            $params[] = $warungId;
        }
        if ($orderSearch !== null && trim($orderSearch) !== '') {
            $like = '%' . trim($orderSearch) . '%';
            $where[] = '(o.order_number LIKE ? OR o.public_token LIKE ? OR o.customer_name LIKE ?)';
            $types .= 'sss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql = 'SELECT COUNT(*) FROM orders o WHERE ' . implode(' AND ', $where);
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $count = (int) $stmt->get_result()->fetch_row()[0];
        $stmt->close();

        return $count;
    }

    /**
     * @return array{incoming: int, preparing: int, completed_today: int}
     */
    public function getWarungFulfillmentStats(int $venueId, int $warungId): array
    {
        $today = date('Y-m-d');
        $sql = <<<SQL
            SELECT
                SUM(CASE WHEN f.status = 'new' AND o.status IN ('paid','accepted','processing') THEN 1 ELSE 0 END) as incoming,
                SUM(CASE WHEN f.status = 'preparing' THEN 1 ELSE 0 END) as preparing,
                SUM(CASE WHEN (f.status = 'ready' OR o.status = 'completed') AND DATE(o.created_at) = ? THEN 1 ELSE 0 END) as completed_today
            FROM orders o
            INNER JOIN order_warung_fulfillment f ON f.order_id = o.id AND f.warung_id = ?
            WHERE o.venue_id = ?
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('sii', $today, $warungId, $venueId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return [
            'incoming' => (int) ($res['incoming'] ?? 0),
            'preparing' => (int) ($res['preparing'] ?? 0),
            'completed_today' => (int) ($res['completed_today'] ?? 0),
        ];
    }
}
