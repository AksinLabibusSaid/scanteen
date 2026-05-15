<?php

declare(strict_types=1);

namespace App\Controllers\Api\Staff;

use App\Repositories\VenueRepository;
use App\Staff\StaffAuth;

final class SettingsApiController extends StaffApiController
{
    public function handle(): void
    {
        $this->requireAdmin();
        $data = $this->getJsonData();
        $action = trim((string) ($data['action'] ?? ''));

        switch ($action) {
            case 'update':
                $this->update($data);
                break;
            default:
                $this->json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        }
    }

    private function update(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        
        $tax = (float) ($data['tax_percent'] ?? 11);
        $service = (float) ($data['service_fee_percent'] ?? 2);
        $expiry = (int) ($data['payment_expiry_minutes'] ?? 15);
        $maintenance = (bool) ($data['maintenance_mode'] ?? false);
        $message = isset($data['maintenance_message']) ? (string) $data['maintenance_message'] : null;
        $clientKey = isset($data['midtrans_client_key']) ? (string) $data['midtrans_client_key'] : null;
        $serverKey = isset($data['midtrans_server_key']) ? (string) $data['midtrans_server_key'] : null;
        $isProd = (bool) ($data['is_production'] ?? false);
        
        $allowQris = (bool) ($data['allow_qris'] ?? true);
        $allowCash = (bool) ($data['allow_cash'] ?? true);
        $allowDebit = (bool) ($data['allow_debit'] ?? false);
        
        // operating_hours expects JSON string or null
        $operatingHours = isset($data['operating_hours']) ? json_encode($data['operating_hours']) : null;

        $repo = new VenueRepository();
        $ok = $repo->updateSettings(
            $venueId,
            $tax,
            $service,
            $expiry,
            $maintenance,
            $message,
            $clientKey,
            $serverKey,
            $isProd,
            $allowQris,
            $allowCash,
            $allowDebit,
            $operatingHours
        );

        if (!$ok) {
            $this->json(['ok' => false, 'error' => 'Gagal memperbarui pengaturan'], 500);
        }

        $this->json(['ok' => true]);
    }
}
