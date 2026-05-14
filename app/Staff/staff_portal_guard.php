<?php

declare(strict_types=1);

use App\Staff\StaffAuth;
use App\Support\PublicUrl;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * @param 'admin'|'kasir'|'warung' $requiredRole
 */
function scanteen_staff_require_portal(string $requiredRole): void
{
    if (!StaffAuth::check()) {
        $next = $_SERVER['REQUEST_URI'] ?? '';
        $q = rawurlencode($next);
        header('Location: ' . PublicUrl::staffLoginPath() . '?next=' . $q);
        exit;
    }

    $role = StaffAuth::role();
    if ($role !== $requiredRole) {
        $theirHome = PublicUrl::staffPortalPathForRole((string) $role);
        if ($theirHome !== null) {
            header('Location: ' . $theirHome);
            exit;
        }
        http_response_code(403);
        echo 'Akses ditolak untuk peran ini.';
        exit;
    }

    if ($requiredRole === 'warung' && StaffAuth::warungId() === null) {
        http_response_code(403);
        echo 'Akun warung tidak terhubung ke stan.';
        exit;
    }
}
