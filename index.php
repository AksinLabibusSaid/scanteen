<?php

declare(strict_types=1);

/**
 * Entry aplikasi: arahkan ke login staff (admin / kasir / warung) — satu form, role dari akun.
 * Halaman pelanggan: /pages/Customer/index.php?t=TOKEN_BARCODE (URL cetak QR per meja).
 */
require_once __DIR__ . '/app/bootstrap.php';

use App\Support\PublicUrl;

header('Location: ' . PublicUrl::staffLoginPath(), true, 302);
exit;
