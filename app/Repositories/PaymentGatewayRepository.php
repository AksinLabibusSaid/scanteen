<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PaymentGatewayRepository
{
    public function insertPendingSnap(
        int $orderId,
        string $provider,
        int $grossAmountIdr,
        ?string $snapToken,
        ?string $rawRequest,
        ?string $rawResponse
    ): int {
        $gross = (float) $grossAmountIdr;
        $status = $snapToken !== null && $snapToken !== '' ? 'snap_created' : 'snap_failed';

        $sql = <<<SQL
            INSERT INTO payment_gateway_transactions (
                order_id, provider, status, snap_token, gross_amount, raw_request, raw_response
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param(
            'isssdss',
            $orderId,
            $provider,
            $status,
            $snapToken,
            $gross,
            $rawRequest,
            $rawResponse
        );
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function appendNotificationLog(int $orderId, string $body): void
    {
        $id = $this->latestIdForOrder($orderId);
        if ($id === null) {
            return;
        }

        $sql2 = 'UPDATE payment_gateway_transactions SET raw_notification = CONCAT(COALESCE(raw_notification, \'\'), ?), updated_at = CURRENT_TIMESTAMP WHERE id = ?';
        $stmt = Database::mysqli()->prepare($sql2);
        $stmt->bind_param('si', $body, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function markStatusByOrderId(int $orderId, string $status): void
    {
        $id = $this->latestIdForOrder($orderId);
        if ($id === null) {
            return;
        }
        $stmt = Database::mysqli()->prepare('UPDATE payment_gateway_transactions SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $stmt->close();
    }

    private function latestIdForOrder(int $orderId): ?int
    {
        $stmt = Database::mysqli()->prepare(
            'SELECT id FROM payment_gateway_transactions WHERE order_id = ? ORDER BY id DESC LIMIT 1'
        );
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row !== null ? (int) $row['id'] : null;
    }
}
