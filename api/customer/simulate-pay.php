<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Customer\CustomerSessionKeys;
use App\Repositories\OrderWriteRepository;

/** @var \App\Customer\CustomerContext $ctx */
[$ctx, $_c, $_m, $_b] = scanteen_customer_api_bootstrap();

if (!defined('SCANTEEN_CUSTOMER_SIMULATE_PAYMENT') || SCANTEEN_CUSTOMER_SIMULATE_PAYMENT !== true) {
    scanteen_json_response(['ok' => false, 'error' => 'Simulasi nonaktif.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$token = isset($_SESSION[CustomerSessionKeys::LAST_ORDER_TOKEN])
    ? (string) $_SESSION[CustomerSessionKeys::LAST_ORDER_TOKEN]
    : '';

$raw = file_get_contents('php://input');
if (is_string($raw) && trim($raw) !== '') {
    $data = json_decode($raw, true);
    if (is_array($data) && isset($data['public_token'])) {
        $token = trim((string) $data['public_token']);
    }
}

if (strlen($token) !== 32) {
    scanteen_json_response(['ok' => false, 'error' => 'Token pesanan tidak ada.'], 422);
}

$orderRepo = new \App\Repositories\OrderRepository();
$order = $orderRepo->findByPublicToken($token);
if ($order === null || (int) $order['dining_table_id'] !== $ctx->diningTableId) {
    scanteen_json_response(['ok' => false, 'error' => 'Pesanan tidak ditemukan.'], 404);
}

$write = new OrderWriteRepository();
$ok = $write->markPaidByPublicToken($token);
if (!$ok) {
    scanteen_json_response(['ok' => false, 'error' => 'Status pembayaran tidak diubah (sudah dibayar atau tidak valid).'], 409);
}

// Kirim email rincian pembayaran
try {
    $items = $orderRepo->itemsByOrderId((int)$order['id']);
    $mailSvc = new \App\Services\MailService();
    $sent = $mailSvc->sendReceipt(
        (string)$order['customer_email'], 
        (string)$order['customer_name'], 
        $order, 
        $items
    );
    if (!$sent) {
        throw new \Exception('MailService returned false. Periksa kredensial atau log.');
    }
} catch (\Throwable $e) {
    scanteen_json_response([
        'ok' => false, 
        'error' => 'Gagal mengirim email: ' . $e->getMessage()
    ], 500);
}

scanteen_json_response(['ok' => true]);
