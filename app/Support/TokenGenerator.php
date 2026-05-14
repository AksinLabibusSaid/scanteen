<?php

declare(strict_types=1);

namespace App\Support;

final class TokenGenerator
{
    public static function publicToken(): string
    {
        return bin2hex(random_bytes(16));
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
