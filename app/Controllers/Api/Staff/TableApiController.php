<?php

declare(strict_types=1);

namespace App\Controllers\Api\Staff;

use App\Repositories\DiningTableWriteRepository;
use App\Staff\StaffAuth;
use App\Support\TokenGenerator;

final class TableApiController extends StaffApiController
{
    public function handle(): void
    {
        $this->requireRoles(['admin']);
        $data = $this->getJsonData();
        $action = (string) ($data['action'] ?? '');

        switch ($action) {
            case 'create':
                $this->create($data);
                break;
            case 'update':
                $this->update($data);
                break;
            case 'toggle':
                $this->toggle($data);
                break;
            case 'delete':
                $this->delete($data);
                break;
            default:
                $this->json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        }
    }

    private function create(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $tableNumber = trim((string) ($data['table_number'] ?? ''));

        if ($tableNumber === '') {
            $this->json(['ok' => false, 'error' => 'Nomor meja wajib diisi'], 422);
        }

        $token = TokenGenerator::tableScanToken();
        $write = new DiningTableWriteRepository();
        $id = $write->insert($venueId, $tableNumber, $token);

        $this->json([
            'ok' => true,
            'table' => [
                'id' => $id,
                'table_number' => $tableNumber,
                'barcode_token' => $token,
            ],
        ]);
    }

    private function update(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);
        $tableNumber = trim((string) ($data['table_number'] ?? ''));

        if ($id <= 0 || $tableNumber === '') {
            $this->json(['ok' => false, 'error' => 'Data tidak lengkap'], 422);
        }

        $write = new DiningTableWriteRepository();
        $ok = $write->updateTableNumber($id, $venueId, $tableNumber);
        $this->json(['ok' => $ok]);
    }

    private function toggle(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);
        $isActive = isset($data['is_active']) ? (int) $data['is_active'] : null;

        if ($id <= 0 || ($isActive !== 0 && $isActive !== 1)) {
            $this->json(['ok' => false, 'error' => 'Data tidak lengkap'], 422);
        }

        $write = new DiningTableWriteRepository();
        $ok = $write->setActive($id, $venueId, $isActive);
        $this->json(['ok' => $ok]);
    }

    private function delete(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0) {
            $this->json(['ok' => false, 'error' => 'Data tidak lengkap'], 422);
        }

        $write = new DiningTableWriteRepository();
        $ok = $write->softDelete($id, $venueId);
        $this->json(['ok' => $ok]);
    }
}
