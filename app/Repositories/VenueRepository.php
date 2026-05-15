<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class VenueRepository
{
    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM venues WHERE id = ? LIMIT 1';
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row !== null ? $row : null;
    }

    public function updateSettings(
        int $id,
        float $taxPercent,
        float $serviceFeePercent,
        int $paymentExpiryMinutes,
        bool $maintenanceMode,
        ?string $maintenanceMessage,
        ?string $midtransClientKey,
        ?string $midtransServerKey,
        bool $isProduction,
        bool $allowQris,
        bool $allowCash,
        bool $allowDebit,
        ?string $operatingHours
    ): bool {
        $sql = <<<SQL
            UPDATE venues
            SET tax_percent = ?,
                service_fee_percent = ?,
                payment_expiry_minutes = ?,
                maintenance_mode = ?,
                maintenance_message = ?,
                midtrans_client_key = ?,
                midtrans_server_key = ?,
                is_production = ?,
                allow_qris = ?,
                allow_cash = ?,
                allow_debit = ?,
                operating_hours = ?
            WHERE id = ?
            SQL;

        $mMode = $maintenanceMode ? 1 : 0;
        $iProd = $isProduction ? 1 : 0;
        $qris = $allowQris ? 1 : 0;
        $cash = $allowCash ? 1 : 0;
        $debit = $allowDebit ? 1 : 0;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param(
            'ddiisssiiiisi',
            $taxPercent,
            $serviceFeePercent,
            $paymentExpiryMinutes,
            $mMode,
            $maintenanceMessage,
            $midtransClientKey,
            $midtransServerKey,
            $iProd,
            $qris,
            $cash,
            $debit,
            $operatingHours,
            $id
        );
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
