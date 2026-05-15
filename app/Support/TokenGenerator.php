<?php

declare(strict_types=1);

namespace App\Support;

final class TokenGenerator
{
    public static function publicToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function orderNumberForDate(string|\DateTimeInterface $date, int $sequence): string
    {
        $dt = $date instanceof \DateTimeInterface
            ? $date
            : new \DateTimeImmutable($date, new \DateTimeZone(date_default_timezone_get()));

        return 'ORD-' . $dt->format('md') . '-' . sprintf('%03d', max(1, $sequence));
    }

    public static function orderNumber(): string
    {
        $suffix = strtoupper(bin2hex(random_bytes(2)));

        return 'ORD-' . date('Ymd') . '-' . $suffix;
    }

    /** Token unik untuk URL scan meja (query ?t=). */
    public static function tableScanToken(): string
    {
        return 'scan_' . bin2hex(random_bytes(12));
    }
}
