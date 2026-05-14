<?php

declare(strict_types=1);

namespace App\Customer;

final class OrderUi
{
    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending_payment' => 'Menunggu pembayaran',
            'paid' => 'Dibayar',
            'accepted' => 'Diterima',
            'processing' => 'Diproses',
            'ready' => 'Siap diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }

    /**
     * Detik tersisa untuk countdown (dibatasi 0..86400).
     */
    public static function countdownSeconds(?string $deadlineMysql): int
    {
        if ($deadlineMysql === null || $deadlineMysql === '') {
            return 900;
        }
        try {
            $end = new \DateTimeImmutable($deadlineMysql, new \DateTimeZone(date_default_timezone_get()));
        } catch (\Exception) {
            return 900;
        }
        $now = new \DateTimeImmutable('now', new \DateTimeZone(date_default_timezone_get()));
        $sec = $end->getTimestamp() - $now->getTimestamp();

        return max(0, min(86400, $sec));
    }
}
