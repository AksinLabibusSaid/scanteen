<?php

declare(strict_types=1);

/**
 * Midtrans / kartu / e-wallet tidak lagi dipakai. Pembayaran: QRIS atau bayar di kasir.
 */
require __DIR__ . '/_init.php';

scanteen_json_response(['ok' => false, 'error' => 'Metode pembayaran gateway tidak tersedia. Gunakan QRIS atau bayar di kasir.'], 410);
