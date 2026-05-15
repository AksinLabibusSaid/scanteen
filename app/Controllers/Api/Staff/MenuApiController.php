<?php

declare(strict_types=1);

namespace App\Controllers\Api\Staff;

use App\Repositories\MenuRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;

final class MenuApiController extends StaffApiController
{
    public function handle(): void
    {
        $this->requireRoles(['admin', 'warung']);
        $data = $this->getJsonData();
        $action = trim((string) ($data['action'] ?? ''));

        switch ($action) {
            case 'availability':
                $this->updateAvailability($data);
                break;
            case 'create':
                $this->create($data);
                break;
            case 'update':
                $this->update($data);
                break;
            default:
                $this->json(['ok' => false, 'error' => 'Aksi tidak dikenal'], 400);
        }
    }

    private function updateAvailability(array $data): void
    {
        $menuId = (int) ($data['menu_id'] ?? 0);
        $isAvailable = (int) ($data['is_available'] ?? -1);
        $venueId = (int) StaffAuth::venueId();
        $role = StaffAuth::role();

        if ($menuId <= 0 || ($isAvailable !== 0 && $isAvailable !== 1)) {
            $this->json(['ok' => false, 'error' => 'Data tidak valid'], 422);
        }

        $menuRepo = new MenuRepository();
        if (!$menuRepo->menuBelongsToVenue($menuId, $venueId)) {
            $this->json(['ok' => false, 'error' => 'Menu tidak ditemukan'], 404);
        }

        if ($role === 'warung') {
            $wid = StaffAuth::warungId();
            if ($wid === null || !$menuRepo->menuBelongsToWarung($menuId, $wid)) {
                $this->json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
        }

        $menuRepo->setAvailability($menuId, $isAvailable);
        $this->json(['ok' => true]);
    }

    private function create(array $data): void
    {
        $this->requireAdmin();
        $venueId = (int) StaffAuth::venueId();
        
        $wid = (int) ($data['warung_id'] ?? 0);
        $cat = (int) ($data['category_id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $price = (float) ($data['price'] ?? 0);
        $desc = isset($data['description']) ? (string) $data['description'] : '';
        $img = isset($data['image_url']) ? (string) $data['image_url'] : '';
        $avail = (int) ($data['is_available'] ?? 1);

        if ($wid <= 0 || $cat <= 0 || $name === '' || $price <= 0) {
            $this->json(['ok' => false, 'error' => 'Data menu tidak lengkap'], 422);
        }

        $warungRepo = new WarungRepository();
        if ($warungRepo->findByIdForVenue($wid, $venueId) === null) {
            $this->json(['ok' => false, 'error' => 'Warung tidak valid'], 422);
        }

        $menuRepo = new MenuRepository();
        $id = $menuRepo->insertMenu($wid, $cat, $name, $desc, $price, $img, $avail ? 1 : 0);
        $this->json(['ok' => true, 'id' => $id]);
    }

    private function update(array $data): void
    {
        $venueId = (int) StaffAuth::venueId();
        $role = StaffAuth::role();

        $menuId = (int) ($data['menu_id'] ?? 0);
        $wid = (int) ($data['warung_id'] ?? 0);
        $cat = (int) ($data['category_id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $price = (float) ($data['price'] ?? 0);
        $desc = isset($data['description']) ? (string) $data['description'] : '';
        $img = isset($data['image_url']) ? (string) $data['image_url'] : '';
        $avail = (int) ($data['is_available'] ?? 1);

        if ($menuId <= 0 || $wid <= 0 || $cat <= 0 || $name === '' || $price <= 0) {
            $this->json(['ok' => false, 'error' => 'Data menu tidak lengkap'], 422);
        }

        $menuRepo = new MenuRepository();
        if (!$menuRepo->menuBelongsToVenue($menuId, $venueId)) {
            $this->json(['ok' => false, 'error' => 'Menu tidak ditemukan'], 404);
        }

        $warungRepo = new WarungRepository();
        if ($warungRepo->findByIdForVenue($wid, $venueId) === null) {
            $this->json(['ok' => false, 'error' => 'Warung tidak valid'], 422);
        }

        if ($role === 'warung') {
            $myWid = StaffAuth::warungId();
            if ($myWid === null || $myWid !== $wid || !$menuRepo->menuBelongsToWarung($menuId, $myWid)) {
                $this->json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
        }

        $menuRepo->updateMenu($menuId, $wid, $cat, $name, $desc, $price, $img, $avail ? 1 : 0);
        $this->json(['ok' => true]);
    }
}
