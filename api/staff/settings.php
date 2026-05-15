<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Controllers\Api\Staff\SettingsApiController;

(new SettingsApiController())->handle();
