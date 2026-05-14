<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class WarungRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function listByVenueId(int $venueId): array
    {
        $sql = <<<SQL
            SELECT id, venue_id, name, slug, sort_order, is_active, created_at
            FROM warungs
            WHERE venue_id = ?
            ORDER BY sort_order ASC, id ASC
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

    public function findByIdForVenue(int $warungId, int $venueId): ?array
    {
        $sql = 'SELECT * FROM warungs WHERE id = ? AND venue_id = ? LIMIT 1';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $warungId, $venueId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    public function countByVenue(int $venueId): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM warungs WHERE venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $c = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
        $stmt->close();

        return $c;
    }

    public function countActiveByVenue(int $venueId): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM warungs WHERE venue_id = ? AND is_active = 1';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $c = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
        $stmt->close();

        return $c;
    }

    private static function slugify(string $name): string
    {
        $s = strtolower(trim($name));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? 'warung';
        $s = trim($s, '-');

        return $s !== '' ? $s : 'warung';
    }

    public function insert(int $venueId, string $name, ?string $slug = null): int
    {
        $name = trim($name);
        $sort = $this->nextSortOrder($venueId);
        $base = self::slugify($name);
        $slug = $slug !== null && trim($slug) !== '' ? trim($slug) : $base . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
        $sql = 'INSERT INTO warungs (venue_id, name, slug, sort_order, is_active) VALUES (?,?,?,?,1)';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('issi', $venueId, $name, $slug, $sort);
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    private function nextSortOrder(int $venueId): int
    {
        $sql = 'SELECT COALESCE(MAX(sort_order), 0) + 1 AS n FROM warungs WHERE venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $n = (int) ($stmt->get_result()->fetch_assoc()['n'] ?? 1);
        $stmt->close();

        return $n;
    }

    public function setActive(int $warungId, int $venueId, int $isActive): bool
    {
        $sql = 'UPDATE warungs SET is_active = ? WHERE id = ? AND venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iii', $isActive, $warungId, $venueId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    }

    public function updateName(int $warungId, int $venueId, string $name): bool
    {
        $name = trim($name);
        $sql = 'UPDATE warungs SET name = ? WHERE id = ? AND venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('sii', $name, $warungId, $venueId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    }
}
