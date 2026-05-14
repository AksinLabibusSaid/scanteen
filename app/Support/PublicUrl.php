<?php

declare(strict_types=1);

namespace App\Support;

final class PublicUrl
{
    /** Path prefix aplikasi (tanpa trailing slash), mis. /scanteen */
    public static function basePath(): string
    {
        $fromEnv = getenv('SCANTEEN_BASE_PATH');
        if (is_string($fromEnv) && $fromEnv !== '') {
            return rtrim($fromEnv, '/');
        }

        return '/scanteen';
    }

    public static function origin(): string
    {
        $fromEnv = getenv('SCANTEEN_PUBLIC_ORIGIN');
        if (is_string($fromEnv) && $fromEnv !== '') {
            return rtrim($fromEnv, '/');
        }

        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : 'localhost';

        return ($https ? 'https://' : 'http://') . $host;
    }

    public static function customerScanUrl(string $tableToken): string
    {
        $path = self::basePath() . '/pages/Customer/index.php?t=' . rawurlencode($tableToken);

        return self::origin() . $path;
    }

    public static function staffLoginPath(): string
    {
        return self::basePath() . '/pages/staff/login.php';
    }

    /**
     * @return non-empty-string|null
     */
    public static function staffPortalPathForRole(string $role): ?string
    {
        return match ($role) {
            'admin' => self::basePath() . '/pages/Admin/',
            'kasir' => self::basePath() . '/pages/Kasir/',
            'warung' => self::basePath() . '/pages/Warung/',
            default => null,
        };
    }
}
