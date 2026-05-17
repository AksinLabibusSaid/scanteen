<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class OrderWriteRepository
{
    public function markPaidByPublicToken(string $token): bool
    {
        $token = trim($token);
        if (strlen($token) !== 32) {
            return false;
        }

        $sql = <<<SQL
            UPDATE orders
            SET status = 'paid'
            WHERE public_token = ?
              AND status = 'pending_payment'
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected === 1) {
            $sel = Database::mysqli()->prepare("SELECT id FROM orders WHERE public_token = ?");
            $sel->bind_param('s', $token);
            $sel->execute();
            $row = $sel->get_result()->fetch_assoc();
            $sel->close();
            if ($row) {
                (new \App\Services\EmailService())->sendReceipt((int) $row['id']);
            }
        }

        return $affected === 1;
    }

    /**
     * Simulasi konfirmasi warung: paid -> accepted.
     */
    public function markAcceptedByPublicToken(string $token): bool
    {
        $token = trim($token);
        if (strlen($token) !== 32) {
            return false;
        }

        $sql = <<<SQL
            UPDATE orders
            SET status = 'accepted'
            WHERE public_token = ?
              AND status = 'paid'
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected === 1;
    }

    public function markPaidIfPendingByOrderId(int $orderId): bool
    {
        $sql = <<<SQL
            UPDATE orders
            SET status = 'paid'
            WHERE id = ?
              AND status = 'pending_payment'
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $n = $stmt->affected_rows;
        $stmt->close();

        if ($n === 1) {
            (new \App\Services\EmailService())->sendReceipt($orderId);
        }

        return $n === 1;
    }

    public function markProcessingIfEligible(int $orderId): bool
    {
        $sql = <<<SQL
            UPDATE orders
            SET status = 'processing'
            WHERE id = ?
              AND status IN ('paid','accepted')
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $n = $stmt->affected_rows;
        $stmt->close();

        return $n === 1;
    }

    /**
     * Jika semua stan menandai siap, naikkan status pesanan ke ready.
     */
    public function markReadyIfEligible(int $orderId): bool
    {
        $sql = <<<SQL
            UPDATE orders
            SET status = 'ready'
            WHERE id = ?
              AND status IN ('paid','accepted','processing')
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $n = $stmt->affected_rows;
        $stmt->close();

        return $n === 1;
    }

    public function markCompletedIfEligible(int $orderId): bool
    {
        $sql = <<<SQL
            UPDATE orders
            SET status = 'completed'
            WHERE id = ?
              AND status IN ('paid','accepted','processing','ready')
            SQL;

        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $n = $stmt->affected_rows;
        $stmt->close();

        return $n === 1;
    }

    public function cancelPendingPaymentOrder(int $orderId, int $venueId): bool
    {
        $sql = <<<SQL
            UPDATE orders
            SET status = 'cancelled'
            WHERE id = ?
              AND venue_id = ?
              AND status = 'pending_payment'
            SQL;
        $stmt = Database::mysqli()->prepare($sql);
        $stmt->bind_param('ii', $orderId, $venueId);
        $stmt->execute();
        $n = $stmt->affected_rows;
        $stmt->close();

        return $n === 1;
    }
}
