<?php

declare(strict_types=1);

namespace App\Controllers\Api\Staff;

use App\Repositories\StaffUserRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;

final class StaffUserApiController extends StaffApiController
{
    public function handle(): void
    {
        $this->requireAdmin();
        $data = $this->getJsonData();
        $action = trim((string) ($data['action'] ?? ''));

        switch ($action) {
            case 'create':
                $this->create($data);
                break;
            case 'toggle':
                $this->toggle($data);
                break;
            case 'password':
                $this->updatePassword($data);
                break;
            default:
                $this->json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        }
    }

    private function create(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $name = trim((string) ($data['name'] ?? ''));
        $role = trim((string) ($data['role'] ?? ''));
        $warungId = isset($data['warung_id']) && $data['warung_id'] !== '' && $data['warung_id'] !== null
            ? (int) $data['warung_id']
            : null;

        if ($email === '' || strlen($password) < 6 || $name === '') {
            $this->json(['ok' => false, 'error' => 'Email, nama, dan sandi (min. 6 karakter) wajib'], 422);
        }

        if (!in_array($role, ['admin', 'kasir', 'warung'], true)) {
            $this->json(['ok' => false, 'error' => 'Peran tidak valid'], 422);
        }

        if ($role === 'warung') {
            if ($warungId === null || $warungId <= 0) {
                $this->json(['ok' => false, 'error' => 'warung_id wajib untuk peran warung'], 422);
            }
            $warungRepo = new WarungRepository();
            if ($warungRepo->findByIdForVenue($warungId, $venueId) === null) {
                $this->json(['ok' => false, 'error' => 'Warung tidak valid'], 422);
            }
        } else {
            $warungId = null;
        }

        try {
            $repo = new StaffUserRepository();
            $id = $repo->insert($venueId, $email, $password, $name, $role, $warungId);
            $this->json(['ok' => true, 'id' => $id]);
        } catch (\Throwable $e) {
            $this->json(['ok' => false, 'error' => 'Gagal simpan (email duplikat?)'], 409);
        }
    }

    private function toggle(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);
        $isActive = (int) ($data['is_active'] ?? -1);

        if ($id <= 0 || ($isActive !== 0 && $isActive !== 1)) {
            $this->json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }

        $self = StaffAuth::userId();
        if ($self !== null && $id === $self) {
            $this->json(['ok' => false, 'error' => 'Tidak dapat menonaktifkan akun sendiri'], 422);
        }

        $repo = new StaffUserRepository();
        if (!$repo->setActive($id, $venueId, $isActive)) {
            $this->json(['ok' => false, 'error' => 'Pengguna tidak ditemukan'], 404);
        }

        $this->json(['ok' => true]);
    }

    private function updatePassword(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $id = (int) ($data['id'] ?? 0);
        $password = (string) ($data['password'] ?? '');

        if ($id <= 0 || strlen($password) < 6) {
            $this->json(['ok' => false, 'error' => 'ID dan sandi baru (min. 6) wajib'], 422);
        }

        $repo = new StaffUserRepository();
        if (!$repo->updatePassword($id, $venueId, $password)) {
            $this->json(['ok' => false, 'error' => 'Pengguna tidak ditemukan'], 404);
        }

        $this->json(['ok' => true]);
    }
}
