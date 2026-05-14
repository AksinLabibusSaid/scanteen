<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$warungId = StaffAuth::warungId();
$kpis = ['incoming' => 0, 'active' => 0, 'completed_today' => 0, 'revenue_today' => 0.0];
$queue = [];
if ($warungId !== null) {
    $kpis = (new VenueStatsRepository())->warungDashboard($venueId, $warungId);
    $queue = (new OrderListRepository())->listForWarung($venueId, $warungId, 12);
}
?>

<div class="flex flex-col gap-5">
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900">Overview stan</h1>
        <p class="text-sm text-gray-500 mt-1">Antrean dapur dan ringkasan penjualan item stan Anda.</p>
    </div>

    <?php if ($warungId === null): ?>
        <p class="text-red-600 text-sm">Akun tidak terhubung ke stan.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-gray-500 text-xs font-semibold uppercase">Menunggu bayar (item stan)</p>
                <p class="text-gray-900 text-3xl font-bold mt-1"><?= (int) $kpis['incoming'] ?></p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-gray-500 text-xs font-semibold uppercase">Sedang proses</p>
                <p class="text-gray-900 text-3xl font-bold mt-1"><?= (int) $kpis['active'] ?></p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-gray-500 text-xs font-semibold uppercase">Selesai hari ini</p>
                <p class="text-gray-900 text-3xl font-bold mt-1"><?= (int) $kpis['completed_today'] ?></p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-gray-500 text-xs font-semibold uppercase">Omzet item hari ini</p>
                <p class="text-gray-900 text-2xl font-bold mt-1"><?= htmlspecialchars(Money::formatIdr($kpis['revenue_today']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-2">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-gray-900 font-bold text-lg">Antrean aktif</h2>
                <a href="?page=orders" class="text-sm font-bold text-[#991B1B] hover:underline">Buka semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-400 uppercase">
                        <tr>
                            <th class="px-4 py-3">Pesanan</th>
                            <th class="px-4 py-3">Meja</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3">Dapur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($queue as $o): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold"><?= htmlspecialchars((string) $o['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars((string) ($o['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-right font-semibold"><?= htmlspecialchars(Money::formatIdr((float) $o['total']), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-xs"><?= htmlspecialchars((string) ($o['warung_fulfillment_status'] ?? 'new'), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($queue === []): ?>
                <p class="p-6 text-center text-gray-400 text-sm">Tidak ada antrean aktif.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
