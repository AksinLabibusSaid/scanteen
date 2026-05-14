<?php
declare(strict_types=1);

use App\Repositories\MenuRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$warungId = StaffAuth::warungId();
$venueId = (int) StaffAuth::venueId();
$menus = [];
$api = PublicUrl::basePath() . '/api/staff/menu-availability.php';
if ($warungId !== null) {
    $menus = (new MenuRepository())->listAdminByVenue($venueId, $warungId);
}
?>

<div class="flex flex-col gap-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Menu stan</h1>
        <p class="text-sm text-gray-500 mt-1">Aktifkan / nonaktifkan menu yang dijual di stan Anda.</p>
    </div>

    <?php if ($warungId === null): ?>
        <p class="text-red-600 text-sm">Akun tidak terhubung ke stan.</p>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-gray-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Menu</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold"><?= htmlspecialchars((string) $m['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-xs text-gray-600"><?= htmlspecialchars((string) $m['category_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right"><?= htmlspecialchars(Money::formatIdr((float) $m['price']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3">
                                <button type="button" class="text-xs font-bold text-[#7B0009] btn-toggle" data-id="<?= (int) $m['id'] ?>" data-next="<?= (int) $m['is_available'] === 1 ? '0' : '1' ?>">
                                    <?= (int) $m['is_available'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($menus === []): ?>
                <p class="p-8 text-center text-gray-400">Belum ada menu untuk stan ini.</p>
            <?php endif; ?>
        </div>
        <p class="text-xs text-gray-400">Untuk menambah atau mengedit nama/harga menu, gunakan portal Admin.</p>

        <script>
        (function () {
          const api = <?= json_encode($api, JSON_THROW_ON_ERROR) ?>;
          document.querySelectorAll('.btn-toggle').forEach(btn => btn.addEventListener('click', async () => {
            const menu_id = parseInt(btn.getAttribute('data-id'), 10);
            const is_available = parseInt(btn.getAttribute('data-next'), 10);
            const res = await fetch(api, { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ menu_id, is_available }) });
            const data = await res.json();
            if (!data.ok) { alert(data.error || 'Gagal'); return; }
            location.reload();
          }));
        })();
        </script>
    <?php endif; ?>
</div>
