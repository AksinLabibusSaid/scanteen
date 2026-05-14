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
}
