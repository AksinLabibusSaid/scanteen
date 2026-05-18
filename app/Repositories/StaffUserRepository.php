<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class StaffUserRepository
{
    public function findActiveByEmail(string $email): ?array
    {
        $email = trim($email);
        $sql = <<<SQL
            SELECT id, venue_id, email, password_hash, name, role, warung_id, is_active
            FROM staff_users
            WHERE email = ?
              AND is_active = 1
            LIMIT 1
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    public function findByWarungId(int $warungId): ?array
    {
        $sql = 'SELECT id, email, role, warung_id, name, phone FROM staff_users WHERE warung_id = ? LIMIT 1';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $warungId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listByVenue(int $venueId): array
    {
        $sql = <<<SQL
            SELECT id, venue_id, email, name, role, warung_id, is_active, created_at
            FROM staff_users
            WHERE venue_id = ?
            ORDER BY role ASC, name ASC
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

    public function countByVenue(int $venueId): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM staff_users WHERE venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $venueId);
        $stmt->execute();
        $c = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
        $stmt->close();

        return $c;
    }

    public function countByRole(int $venueId, string $role): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM staff_users WHERE venue_id = ? AND role = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('is', $venueId, $role);
        $stmt->execute();
        $c = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
        $stmt->close();

        return $c;
    }

    public function insert(
        int $venueId,
        string $email,
        string $passwordPlain,
        string $name,
        string $role,
        ?int $warungId,
        ?string $phone = null
    ): int {
        $email = trim($email);
        $name = trim($name);
        $hash = password_hash($passwordPlain, PASSWORD_DEFAULT);
        $mysqli = Database::mysqli();
        if ($warungId === null) {
            $sql = <<<SQL
                INSERT INTO staff_users (venue_id, email, password_hash, name, phone, role, warung_id, is_active)
                VALUES (?,?,?,?,?,?,NULL,1)
                SQL;
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('isssss', $venueId, $email, $hash, $name, $phone, $role);
        } else {
            $sql = <<<SQL
                INSERT INTO staff_users (venue_id, email, password_hash, name, phone, role, warung_id, is_active)
                VALUES (?,?,?,?,?,?,?,1)
                SQL;
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('isssssi', $venueId, $email, $hash, $name, $phone, $role, $warungId);
        }
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function setActive(int $userId, int $venueId, int $isActive): bool
    {
        $sql = 'UPDATE staff_users SET is_active = ? WHERE id = ? AND venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iii', $isActive, $userId, $venueId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    }

    public function updatePassword(int $userId, int $venueId, string $passwordPlain): bool
    {
        $hash = password_hash($passwordPlain, PASSWORD_DEFAULT);
        $sql = 'UPDATE staff_users SET password_hash = ? WHERE id = ? AND venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('sii', $hash, $userId, $venueId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    }

    public function updateInfo(int $userId, string $name, ?string $phone): bool
    {
        $name = trim($name);
        $sql = 'UPDATE staff_users SET name = ?, phone = ? WHERE id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ssi', $name, $phone, $userId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();
        return $ok;
    }
}
