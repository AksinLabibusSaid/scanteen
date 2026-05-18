<?php

declare(strict_types=1);

namespace App\Controllers\Api\Staff;

use App\Repositories\OrderListRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarungFulfillmentRepository;
use App\Repositories\OrderWriteRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;

final class WarungApiController extends StaffApiController
{
    public function handle(): void
    {
        $this->requireRoles(['admin', 'warung']);
        $data = $this->getJsonData();
        $action = trim((string) ($data['action'] ?? ''));

        switch ($action) {
            case 'fulfillment':
                $this->fulfillment($data);
                break;
            case 'detail':
                $this->detail($data);
                break;
            case 'create':
                $this->create($data);
                break;
            case 'toggle':
                $this->toggle($data);
                break;
            case 'rename':
                $this->rename($data);
                break;
            case 'delete':
                $this->delete($data);
                break;
            default:
                $this->json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        }
    }

    private function fulfillment(array $data): void
    {
        $orderId = (int) ($data['order_id'] ?? 0);
        $status = trim((string) ($data['status'] ?? ''));
        $venueId = (int) StaffAuth::venueId();
        $role = StaffAuth::role();

        if ($orderId < 1 || !in_array($status, ['new', 'preparing', 'ready', 'completed'], true)) {
            $this->json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }

        $orderRepo = new OrderRepository();
        $order = $orderRepo->findById($orderId);
        if ($order === null || (int) $order['venue_id'] !== $venueId) {
            $this->json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($role === 'warung') {
            $warungId = StaffAuth::warungId();
            if ($warungId === null) {
                $this->json(['ok' => false, 'error' => 'Warung tidak terpasang'], 403);
            }
        } else {
            $warungId = (int) ($data['warung_id'] ?? 0);
        }

        if ($warungId < 1) {
            $this->json(['ok' => false, 'error' => 'warung_id wajib'], 422);
        }

        $list = new OrderListRepository();
        if (!$list->warungOwnsOrderItem($orderId, $warungId)) {
            $this->json(['ok' => false, 'error' => 'Warung tidak punya item di pesanan ini'], 422);
        }

        $ful = new OrderWarungFulfillmentRepository();
        $ok = $ful->updateStatus($orderId, $warungId, $status);
        if (!$ok) {
            $this->json(['ok' => false, 'error' => 'Gagal update status'], 409);
        }

        if ($status === 'preparing') {
            (new OrderWriteRepository())->markProcessingIfEligible($orderId);
        }

        if ($status === 'ready' && $ful->allReadyOrCompletedForOrder($orderId)) {
            (new OrderWriteRepository())->markReadyIfEligible($orderId);
        }

        if ($status === 'completed' && $ful->allCompletedForOrder($orderId)) {
            (new OrderWriteRepository())->markCompletedIfEligible($orderId);
        }

        $this->json(['ok' => true]);
    }

    private function create(array $data): void
    {
        $this->requireAdmin();
        $venueId = (int) StaffAuth::venueId();
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            $this->json(['ok' => false, 'error' => 'Nama warung wajib'], 422);
        }

        try {
            $id = (new WarungRepository())->insert($venueId, $name);
            $this->json(['ok' => true, 'id' => $id]);
        } catch (\Throwable $e) {
            $this->json(['ok' => false, 'error' => 'Gagal menyimpan'], 409);
        }
    }

    private function toggle(array $data): void
    {
        $this->requireAdmin();
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);
        $isActive = (int) ($data['is_active'] ?? -1);

        if ($id <= 0 || ($isActive !== 0 && $isActive !== 1)) {
            $this->json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }

        if (!(new WarungRepository())->setActive($id, $venueId, $isActive)) {
            $this->json(['ok' => false, 'error' => 'Warung tidak ditemukan'], 404);
        }

        $this->json(['ok' => true]);
    }

    private function rename(array $data): void
    {
        $this->requireAdmin();
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));

        if ($id <= 0 || $name === '') {
            $this->json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }

        if (!(new WarungRepository())->updateName($id, $venueId, $name)) {
            $this->json(['ok' => false, 'error' => 'Warung tidak ditemukan'], 404);
        }

        $this->json(['ok' => true]);
    }

    private function delete(array $data): void
    {
        $this->requireAdmin();
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0) {
            $this->json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }

        $repo = new WarungRepository();
        $userRepo = new \App\Repositories\StaffUserRepository();

        try {
            \App\Core\Database::transaction(function() use ($id, $venueId, $repo, $userRepo) {
                // 1. Delete associated staff users first
                $userRepo->deleteByWarungId($id, $venueId);
                
                // 2. Delete menus
                $mysqli = \App\Core\Database::mysqli();
                $stmtMenus = $mysqli->prepare("DELETE FROM menus WHERE warung_id = ?");
                $stmtMenus->bind_param('i', $id);
                $stmtMenus->execute();
                $stmtMenus->close();
                
                // 3. Delete the warung itself
                $repo->delete($id, $venueId);
            });
        } catch (\Throwable $e) {
            // FALLBACK: Soft delete if there are orders/foreign key constraints
            $repo->softDelete($id, $venueId);
            
            // Deactivate the associated staff users as well
            $u = $userRepo->findByWarungId($id);
            if ($u !== null) {
                $userRepo->setActive((int)$u['id'], $venueId, 0);
            }
        }

        $this->json(['ok' => true]);
    }

    private function detail(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $orderId = (int) ($data['order_id'] ?? 0);
        $warungId = StaffAuth::warungId();

        if ($orderId <= 0 || $warungId === null) {
            $this->json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }

        $repo = new OrderRepository();
        $detail = $repo->getWarungOrderDetail($orderId, $warungId);

        if ($detail === null || (int) $detail['order']['venue_id'] !== $venueId) {
            $this->json(['ok' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        $this->json([
            'ok' => true, 
            'order' => $detail['order'], 
            'items' => $detail['items'], 
            'fulfillment_status' => $detail['fulfillment_status'],
            'fulfillment_updated_at' => $detail['fulfillment_updated_at']
        ]);
    }
}
