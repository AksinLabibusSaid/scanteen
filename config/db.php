<?php

declare(strict_types=1);

/**
 * Database configuration + koneksi tunggal (mysqli).
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'scanteen');

/** Set true di lingkungan dev untuk tombol simulasi pembayaran customer. */
define('SCANTEEN_CUSTOMER_SIMULATE_PAYMENT', true);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

date_default_timezone_set('Asia/Jakarta');

try {
    Database::boot();
    /** @var \mysqli $conn Kompatibilitas untuk include lama */
    $conn = Database::mysqli();
} catch (Throwable $e) {
    http_response_code(500);
    exit('Database error: ' . $e->getMessage());
}
