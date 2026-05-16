<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

/** @var \App\Customer\CustomerContext $ctx */
[$ctx, $cart, $menus, $_builder] = scanteen_customer_api_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scanteen_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    scanteen_json_response(['ok' => false, 'error' => 'JSON tidak valid'], 400);
}

$token = (string) ($data['public_token'] ?? '');
$method = (string) ($data['payment_method'] ?? '');

if ($token === '') {
    scanteen_json_response(['ok' => false, 'error' => 'Token pesanan harus diisi.'], 422);
}

if ($method === 'kasir') {
    $method = 'cashier';
}
if (!in_array($method, ['qris', 'cashier'], true)) {
    scanteen_json_response(['ok' => false, 'error' => 'Metode pembayaran tidak valid.'], 422);
}

$mysqli = \App\Core\Database::mysqli();

// Verify order belongs to this venue and table (security check)
$sql = 'SELECT id FROM orders WHERE public_token = ? AND venue_id = ? AND dining_table_id = ? LIMIT 1';
$stmt = $mysqli->prepare($sql);
$venueId = $ctx->venueId;
$tableId = $ctx->diningTableId;
$stmt->bind_param('sii', $token, $venueId, $tableId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    scanteen_json_response(['ok' => false, 'error' => 'Pesanan tidak ditemukan atau tidak valid.'], 404);
}

// Update payment method
$updateSql = 'UPDATE orders SET payment_method = ? WHERE public_token = ?';
$updateStmt = $mysqli->prepare($updateSql);
$updateStmt->bind_param('ss', $method, $token);
$ok = $updateStmt->execute();
$updateStmt->close();

scanteen_json_response([
    'ok' => $ok,
    'message' => $ok ? 'Metode pembayaran berhasil diperbarui.' : 'Gagal memperbarui metode pembayaran.'
]);
