<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class VenueStatsRepository
{
    /**
     * @return array{
     *   today_orders:int,
     *   today_revenue:float,
     *   pending_payment:int,
     *   today_qris:int,
     *   today_cashier:int,
     *   week_revenue:float
     * }
     */
    public function dashboardKpis(int $venueId): array
    {
        $mysqli = Database::mysqli();
        $sql = <<<SQL
            SELECT
                (SELECT COUNT(*) FROM orders o WHERE o.venue_id = ? AND DATE(o.created_at) = CURDATE()) AS today_orders,
                (SELECT COALESCE(SUM(o.total), 0) FROM orders o
                 WHERE o.venue_id = ? AND DATE(o.created_at) = CURDATE()
                   AND o.status IN ('paid','accepted','processing','ready','completed')) AS today_revenue,
                (SELECT COUNT(*) FROM orders o WHERE o.venue_id = ? AND o.status = 'pending_payment') AS pending_payment,
                (SELECT COUNT(*) FROM orders o WHERE o.venue_id = ? AND DATE(o.created_at) = CURDATE() AND o.payment_method = 'qris') AS today_qris,
                (SELECT COUNT(*) FROM orders o WHERE o.venue_id = ? AND DATE(o.created_at) = CURDATE() AND o.payment_method = 'cashier') AS today_cashier,
                (SELECT COALESCE(SUM(o.total), 0) FROM orders o
                 WHERE o.venue_id = ? AND o.created_at >= (CURDATE() - INTERVAL 6 DAY)
                   AND o.status IN ('paid','accepted','processing','ready','completed')) AS week_revenue
            SQL;
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('iiiiii', $venueId, $venueId, $venueId, $venueId, $venueId, $venueId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return [
            'today_orders' => (int) ($row['today_orders'] ?? 0),
            'today_revenue' => (float) ($row['today_revenue'] ?? 0),
            'pending_payment' => (int) ($row['pending_payment'] ?? 0),
            'today_qris' => (int) ($row['today_qris'] ?? 0),
            'today_cashier' => (int) ($row['today_cashier'] ?? 0),
            'week_revenue' => (float) ($row['week_revenue'] ?? 0),
        ];
    }

    /**
     * @return array{orders:int, revenue:float}
     */
    public function summaryBetween(int $venueId, string $dateFrom, string $dateTo): array
    {
        $mysqli = Database::mysqli();
        $sql = <<<SQL
            SELECT
                COUNT(*) AS c,
                COALESCE(SUM(o.total), 0) AS rev
            FROM orders o
            WHERE o.venue_id = ?
              AND DATE(o.created_at) BETWEEN ? AND ?
              AND o.status IN ('paid','accepted','processing','ready','completed')
            SQL;
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('iss', $venueId, $dateFrom, $dateTo);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return [
            'orders' => (int) ($row['c'] ?? 0),
            'revenue' => (float) ($row['rev'] ?? 0),
        ];
    }

    /**
     * Warung dengan omzet tertinggi dalam rentang tanggal (satu baris atau null).
     *
     * @return array{name:string, revenue:float, orders:int}|null
     */
    public function topWarungBetween(int $venueId, string $dateFrom, string $dateTo): ?array
    {
        $mysqli = Database::mysqli();
        $sql = <<<SQL
            SELECT w.name AS warung_name,
                   COALESCE(SUM(oi.line_subtotal), 0) AS revenue,
                   COUNT(DISTINCT o.id) AS order_count
            FROM order_items oi
            INNER JOIN orders o ON o.id = oi.order_id
            INNER JOIN warungs w ON w.id = oi.warung_id
            WHERE o.venue_id = ?
              AND DATE(o.created_at) BETWEEN ? AND ?
              AND o.status IN ('paid','accepted','processing','ready','completed')
            GROUP BY oi.warung_id, w.name
            ORDER BY revenue DESC
            LIMIT 1
            SQL;
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('iss', $venueId, $dateFrom, $dateTo);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row === null || $row['warung_name'] === null) {
            return null;
        }

        return [
            'name' => (string) $row['warung_name'],
            'revenue' => (float) $row['revenue'],
            'orders' => (int) $row['order_count'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function warungRevenueBreakdown(int $venueId, string $dateFrom, string $dateTo): array
    {
        $mysqli = Database::mysqli();
        $sql = <<<SQL
            SELECT w.id AS warung_id, w.name AS warung_name,
                   COALESCE(SUM(oi.line_subtotal), 0) AS revenue
            FROM warungs w
            LEFT JOIN order_items oi ON oi.warung_id = w.id
            LEFT JOIN orders o ON o.id = oi.order_id
              AND o.venue_id = w.venue_id
              AND DATE(o.created_at) BETWEEN ? AND ?
              AND o.status IN ('paid','accepted','processing','ready','completed')
            WHERE w.venue_id = ?
            GROUP BY w.id, w.name
            ORDER BY revenue DESC, w.sort_order ASC
            SQL;
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssi', $dateFrom, $dateTo, $venueId);
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
     * Ringkasan stan: pesanan aktif (belum selesai/batal) yang punya item stan ini.
     *
     * @return array{incoming:int, active:int, completed_today:int, revenue_today:float}
     */
    public function warungDashboard(int $venueId, int $warungId): array
    {
        $mysqli = Database::mysqli();
        $sql = <<<SQL
            SELECT
                (SELECT COUNT(DISTINCT o.id) FROM orders o
                 INNER JOIN order_items oi ON oi.order_id = o.id AND oi.warung_id = ?
                 WHERE o.venue_id = ? AND o.status = 'pending_payment') AS incoming,
                (SELECT COUNT(DISTINCT o.id) FROM orders o
                 INNER JOIN order_items oi ON oi.order_id = o.id AND oi.warung_id = ?
                 INNER JOIN order_warung_fulfillment f ON f.order_id = o.id AND f.warung_id = ?
                 WHERE o.venue_id = ?
                   AND o.status NOT IN ('completed','cancelled','pending_payment')
                   AND f.status != 'ready') AS active,
                (SELECT COUNT(DISTINCT o.id) FROM orders o
                 INNER JOIN order_items oi ON oi.order_id = o.id AND oi.warung_id = ?
                 WHERE o.venue_id = ? AND o.status = 'completed' AND DATE(o.updated_at) = CURDATE()) AS completed_today,
                (SELECT COALESCE(SUM(oi.line_subtotal), 0) FROM order_items oi
                 INNER JOIN orders o ON o.id = oi.order_id
                 WHERE oi.warung_id = ? AND o.venue_id = ?
                   AND DATE(o.created_at) = CURDATE()
                   AND o.status IN ('paid','accepted','processing','ready','completed')) AS revenue_today
            SQL;
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param(
            'iiiiiiiii',
            $warungId,
            $venueId,
            $warungId,
            $warungId,
            $venueId,
            $warungId,
            $venueId,
            $warungId,
            $venueId
        );
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return [
            'incoming' => (int) ($row['incoming'] ?? 0),
            'active' => (int) ($row['active'] ?? 0),
            'completed_today' => (int) ($row['completed_today'] ?? 0),
            'revenue_today' => (float) ($row['revenue_today'] ?? 0),
        ];
    }
}
