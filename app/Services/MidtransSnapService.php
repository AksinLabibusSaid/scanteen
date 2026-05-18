<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Midtrans Snap API (server key). Tanpa kunci server, Snap tidak dapat dibuat.
 */
final class MidtransSnapService
{
    /** @return array{token: string, redirect_url?: string} */
    public function createSnapToken(array $orderRow, array $itemLinesForDisplay): array
    {
        $cfg = require dirname(__DIR__, 2) . '/config/payment.php';
        $serverKey = trim((string) ($cfg['midtrans_server_key'] ?? ''));
        if ($serverKey === '') {
            throw new \RuntimeException(
                'Midtrans belum dikonfigurasi. Set environment MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY, atau gunakan simulasi pembayaran di mode demo.'
            );
        }

        $sandbox = (bool) ($cfg['midtrans_sandbox'] ?? true);
        $base = $sandbox
            ? 'https://app.sandbox.midtrans.com/snap/v1/transactions'
            : 'https://app.midtrans.com/snap/v1/transactions';

        $orderNumber = (string) $orderRow['order_number'];
        $gross = (int) round((float) $orderRow['total']);
        if ($gross < 1) {
            throw new \InvalidArgumentException('Total pembayaran tidak valid.');
        }

        $itemDetails = [];
        $sum = 0;
        foreach ($itemLinesForDisplay as $line) {
            $name = (string) ($line['name'] ?? 'Item');
            $qty = max(1, (int) ($line['qty'] ?? 1));
            $price = (int) round((float) ($line['price'] ?? 0));
            $sum += $price * $qty;
            $itemDetails[] = [
                'id' => (string) ($line['id'] ?? 'MENU'),
                'price' => $price,
                'quantity' => $qty,
                'name' => mb_substr($name, 0, 50),
            ];
        }

        if ($itemDetails === [] || $sum !== $gross) {
            $itemDetails = [[
                'id' => 'ORDER',
                'price' => $gross,
                'quantity' => 1,
                'name' => 'Pesanan Scanteen',
            ]];
        }

        $customerName = trim((string) ($orderRow['customer_name'] ?? 'Guest'));
        $customerEmail = trim((string) ($orderRow['customer_email'] ?? ''));

        $customerDetails = [
            'first_name' => mb_substr($customerName !== '' ? $customerName : 'Guest', 0, 40),
        ];
        if ($customerEmail !== '') {
            $customerDetails['email'] = $customerEmail;
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => $gross,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        $body = json_encode($payload, JSON_THROW_ON_ERROR);
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($serverKey . ':'),
        ];

        if (function_exists('curl_init')) {
            $ch = curl_init($base);
            if ($ch === false) {
                throw new \RuntimeException('Gagal inisialisasi HTTP.');
            }

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
            ]);

            $raw = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            if (!is_string($raw)) {
                throw new \RuntimeException('Respons Midtrans kosong: ' . $err);
            }
        } else {
            // Fallback for environments where PHP cURL extension is not enabled (e.g. fresh Laragon / Windows PHP)
            $context = stream_context_create([
                'http' => [
                    'method'        => 'POST',
                    'header'        => implode("\r\n", $headers),
                    'content'       => $body,
                    'ignore_errors' => true, // Retrieve response body even on error codes
                    'timeout'       => 30,
                ],
            ]);

            $raw = @file_get_contents($base, false, $context);
            if ($raw === false) {
                $err = error_get_last();
                throw new \RuntimeException('Gagal menghubungi Midtrans: ' . ($err['message'] ?? 'Unknown error'));
            }

            // Extract HTTP status code from $http_response_header
            $code = 200;
            if (isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (preg_match('#HTTP/[0-9.]+\s+([0-9]+)#', $header, $matches)) {
                        $code = (int) $matches[1];
                        break;
                    }
                }
            }
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Respons Midtrans bukan JSON (HTTP ' . $code . ').');
        }

        if ($code >= 400) {
            $msg = (string) ($decoded['error_messages'][0] ?? $decoded['message'] ?? $raw);
            throw new \RuntimeException('Midtrans error (' . $code . '): ' . $msg);
        }

        $token = (string) ($decoded['token'] ?? '');
        if ($token === '') {
            throw new \RuntimeException('Midtrans tidak mengembalikan token Snap.');
        }

        $out = ['token' => $token];
        if (isset($decoded['redirect_url'])) {
            $out['redirect_url'] = (string) $decoded['redirect_url'];
        }

        return $out;
    }

    public static function verifyNotificationSignature(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey,
        string $serverKey
    ): bool {
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signatureKey);
    }
}
