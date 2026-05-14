<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/config/db.php';

use App\Staff\StaffAuth;

function scanteen_staff_json(array $data, int $code = 200): void
{
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** @param list<string> $roles */
function scanteen_staff_require_roles(array $roles): void
{
    if (!StaffAuth::check()) {
        scanteen_staff_json(['ok' => false, 'error' => 'Unauthorized'], 401);
    }
    $r = StaffAuth::role();
    if ($r === null || !in_array($r, $roles, true)) {
        scanteen_staff_json(['ok' => false, 'error' => 'Forbidden'], 403);
    }
}

function scanteen_staff_require_admin(): void
{
    scanteen_staff_require_roles(['admin']);
}
