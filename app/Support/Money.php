<?php

declare(strict_types=1);

namespace App\Support;

final class Money
{
    public static function formatIdr(float|string $amount): string
    {
        $n = is_string($amount) ? (float) $amount : $amount;

        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    public static function roundTwo(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
