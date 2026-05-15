<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class DiningTableWriteRepository
{
    public function insert(int $venueId, string $tableNumber, string $barcodeToken): int
    {
        $sql = <<<SQL
            INSERT INTO dining_tables (venue_id, table_number, barcode_token, is_active)
            VALUES (?, ?, ?, 1)
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iss', $venueId, $tableNumber, $barcodeToken);
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function updateTableNumber(int $id, int $venueId, string $tableNumber): bool
    {
        $sql = 'UPDATE dining_tables SET table_number = ? WHERE id = ? AND venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('sii', $tableNumber, $id, $venueId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    }

    public function setActive(int $id, int $venueId, int $isActive): bool
    {
        $sql = 'UPDATE dining_tables SET is_active = ? WHERE id = ? AND venue_id = ?';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('iii', $isActive, $id, $venueId);
        $stmt->execute();
        $ok = $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    }

    public function softDelete(int $id, int $venueId): bool
    {
        return $this->setActive($id, $venueId, 0);
    }
}
