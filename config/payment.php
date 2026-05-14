<?php

declare(strict_types=1);

/**
 * Payment gateway (Midtrans Snap). Isi kunci dari environment produksi / sandbox.
 * Dokumentasi: https://docs.midtrans.com/
 */
return [
    'midtrans_server_key' => (string) (getenv('MIDTRANS_SERVER_KEY') ?: ''),
    'midtrans_client_key' => (string) (getenv('MIDTRANS_CLIENT_KEY') ?: ''),
    /** true = sandbox Midtrans */
    'midtrans_sandbox' => getenv('MIDTRANS_PRODUCTION') === '1' || getenv('MIDTRANS_PRODUCTION') === 'true'
        ? false
        : true,
];
