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
                o.public_token,
                o.customer_name,
                o.status,
                o.payment_method,
                o.total,
                o.created_at,
                dt.table_number
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
                ) AS warung_fulfillment_status
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
        $stmt->bind_param('iii', $warungId, $venueId, $warungId);
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
                dt.table_number
            FROM orders o
            INNER JOIN dining_tables dt ON dt.id = o.dining_table_id
            INNER JOIN order_items oi ON oi.order_id = o.id AND oi.warung_id = ?
            WHERE o.venue_id = ?
              AND o.status = 'completed'
            ORDER BY o.id DESC
            LIMIT {$limit}
            SQL;
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $warungId, $venueId);
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
        int $limit = 150,
        ?string $status = null,
        ?string $dateYmd = null,
        ?string $paymentMethod = null,
        ?int $warungId = null,
        ?string $orderSearch = null,
    ): array {
        $limit = max(1, min(500, $limit));
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
        if ($dateYmd !== null && $dateYmd !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateYmd)) {
            $where[] = 'DATE(o.created_at) = ?';
            $types .= 's';
            $params[] = $dateYmd;
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

        $sql = 'SELECT o.id, o.order_number, o.public_token, o.customer_name, o.customer_email, o.status, '
            . 'o.payment_method, o.total, o.subtotal, o.service_tax, o.created_at, dt.table_number '
            . 'FROM orders o INNER JOIN dining_tables dt ON dt.id = o.dining_table_id WHERE '
            . implode(' AND ', $where)
            . ' ORDER BY o.id DESC LIMIT ' . $limit;

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
}
