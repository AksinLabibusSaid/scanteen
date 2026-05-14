<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

use App\Staff\StaffAuth;
use App\Support\PublicUrl;

StaffAuth::logout();

header('Location: ' . PublicUrl::staffLoginPath());
exit;
