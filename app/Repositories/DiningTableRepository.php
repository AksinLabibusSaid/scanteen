<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use mysqli;

final class DiningTableRepository
{
    public function findActiveByToken(string $token): ?array
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        $sql = <<<SQL
            SELECT
                dt.id AS dining_table_id,
                dt.table_number,
                dt.barcode_token,
                v.id AS venue_id,
                v.name AS venue_name
            FROM dining_tables dt
            INNER JOIN venues v ON v.id = dt.venue_id
            WHERE dt.barcode_token = ?
              AND dt.is_active = 1
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

    public function findById(int $id): ?array
    {
        $sql = <<<SQL
            SELECT
                dt.id AS dining_table_id,
                dt.table_number,
                dt.barcode_token,
                v.id AS venue_id,
                v.name AS venue_name
            FROM dining_tables dt
            INNER JOIN venues v ON v.id = dt.venue_id
            WHERE dt.id = ?
              AND dt.is_active = 1
            LIMIT 1
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listByVenueId(int $venueId): array
    {
        $sql = <<<SQL
            SELECT id, venue_id, table_number, barcode_token, is_active, created_at
            FROM dining_tables
            WHERE venue_id = ?
            ORDER BY CAST(table_number AS UNSIGNED) ASC, table_number ASC, id ASC
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

    public function countByVenueId(int $venueId): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM dining_tables WHERE venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row !== null ? (int) $row['c'] : 0;
    }
}
