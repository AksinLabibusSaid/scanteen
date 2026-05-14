<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/db.php';

use App\Repositories\OrderRepository;
use App\Repositories\OrderWriteRepository;
use App\Repositories\PaymentGatewayRepository;
use App\Services\MidtransSnapService;

header('Content-Type: text/plain; charset=utf-8');

$cfg = require dirname(__DIR__, 2) . '/config/payment.php';
$serverKey = trim((string) ($cfg['midtrans_server_key'] ?? ''));
if ($serverKey === '') {
    http_response_code(503);
    echo 'SKIP';
    exit;
}

/** @var array<string, string> $post */
$post = $_POST;
$orderId = trim((string) ($post['order_id'] ?? ''));
$statusCode = trim((string) ($post['status_code'] ?? ''));
$grossAmount = trim((string) ($post['gross_amount'] ?? ''));
$signatureKey = trim((string) ($post['signature_key'] ?? ''));
$transactionStatus = trim((string) ($post['transaction_status'] ?? ''));

if ($orderId === '' || $signatureKey === '') {
    http_response_code(400);
    echo 'BAD';
    exit;
}

if (!MidtransSnapService::verifyNotificationSignature($orderId, $statusCode, $grossAmount, $signatureKey, $serverKey)) {
    http_response_code(403);
    echo 'INVALID_SIG';
    exit;
}

$orderRepo = new OrderRepository();
$order = $orderRepo->findByOrderNumber($orderId);
if ($order === null) {
    http_response_code(404);
    echo 'NO_ORDER';
    exit;
}

$payLog = new PaymentGatewayRepository();
$payLog->appendNotificationLog((int) $order['id'], "\n" . json_encode($post, JSON_UNESCAPED_UNICODE));

$write = new OrderWriteRepository();

if (in_array($transactionStatus, ['settlement', 'capture'], true)) {
    $write->markPaidIfPendingByOrderId((int) $order['id']);
    $payLog->markStatusByOrderId((int) $order['id'], 'settlement');
} elseif ($transactionStatus === 'pending') {
    $payLog->markStatusByOrderId((int) $order['id'], 'pending');
} elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
    $payLog->markStatusByOrderId((int) $order['id'], $transactionStatus);
}

echo 'OK';
exit;
