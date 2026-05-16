<?php

declare(strict_types=1);

namespace App\Controllers\Api\Staff;

use App\Repositories\OrderRepository;
use App\Repositories\OrderWriteRepository;
use App\Staff\StaffAuth;

final class OrderApiController extends StaffApiController
{
    public function handle(): void
    {
        $this->requireRoles(['admin', 'kasir']);
        $data = $this->getJsonData();
        $action = trim((string) ($data['action'] ?? ''));

        switch ($action) {
            case 'mark-paid':
                $this->markPaid($data);
                break;
            case 'detail':
                $this->detail($data);
                break;
            case 'cancel':
                $this->cancel($data);
                break;
            default:
                $this->json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        }
    }

    private function detail(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $orderId = (int) ($data['order_id'] ?? 0);

        if ($orderId <= 0) {
            $this->json(['ok' => false, 'error' => 'ID tidak valid'], 422);
        }

        $repo = new OrderRepository();
        $detail = $repo->getDetailWithGroups($orderId);

        if ($detail === null || (int) $detail['order']['venue_id'] !== $venueId) {
            $this->json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        $this->json(['ok' => true, 'order' => $detail['order'], 'groups' => $detail['groups']]);
    }

    private function markPaid(array $data): void
    {
        $publicToken = trim((string) ($data['public_token'] ?? ''));
        $venueId = (int) StaffAuth::venueId();

        if (strlen($publicToken) !== 32) {
            $this->json(['ok' => false, 'error' => 'Token tidak valid'], 422);
        }

        $repo = new OrderRepository();
        $order = $repo->findByPublicToken($publicToken);
        if ($order === null || (int) $order['venue_id'] !== $venueId) {
            $this->json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        $write = new OrderWriteRepository();
        $ok = $write->markPaidByPublicToken($publicToken);
        if (!$ok) {
            $this->json(['ok' => false, 'error' => 'Status tidak diubah'], 409);
        }

        $this->json(['ok' => true]);
    }

    private function cancel(array $data): void
    {
        $this->requireAdmin();
        $venueId = (int) StaffAuth::venueId();
        $orderId = (int) ($data['order_id'] ?? 0);

        if ($orderId <= 0) {
            $this->json(['ok' => false, 'error' => 'ID tidak valid'], 422);
        }

        $write = new OrderWriteRepository();
        if (!$write->cancelPendingPaymentOrder($orderId, $venueId)) {
            $this->json(['ok' => false, 'error' => 'Pesanan tidak bisa dibatalkan'], 409);
        }

        $this->json(['ok' => true]);
    }
}
