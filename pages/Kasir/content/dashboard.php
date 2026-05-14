<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$kpis = (new VenueStatsRepository())->dashboardKpis($venueId);
$recent = (new OrderListRepository())->listForVenue($venueId, 8);

function scanteen_kdash_status(string $s): string
{
    return match ($s) {
        'pending_payment' => 'Menunggu bayar',
        'paid' => 'Sudah bayar',
        'accepted' => 'Diterima',
        'processing' => 'Diproses',
        'ready' => 'Siap',
        'completed' => 'Selesai',
        'cancelled' => 'Batal',
        default => $s,
    };
}
?>

<div class="flex flex-col gap-5">
    <div>
        <h1 class="text-2xl font-extrabold text-[#261817] tracking-tight">Dashboard kasir</h1>
        <p class="text-[#675C5C] text-sm mt-1">Data venue: <?= htmlspecialchars((string) StaffAuth::userName(), ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-gray-500 text-xs font-semibold uppercase">Pesanan hari ini</p>
            <p class="text-gray-900 text-3xl font-bold mt-1"><?= (int) $kpis['today_orders'] ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-gray-500 text-xs font-semibold uppercase">Omzet hari ini</p>
            <p class="text-gray-900 text-2xl font-bold mt-1"><?= htmlspecialchars(Money::formatIdr($kpis['today_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-gray-500 text-xs font-semibold uppercase">Menunggu bayar</p>
            <p class="text-orange-600 text-3xl font-bold mt-1"><?= (int) $kpis['pending_payment'] ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-gray-500 text-xs font-semibold uppercase">Omzet 7 hari</p>
            <p class="text-gray-900 text-xl font-bold mt-1"><?= htmlspecialchars(Money::formatIdr($kpis['week_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-gray-900 font-bold text-lg">Pesanan terbaru</h2>
            <a href="?page=orders" class="text-[#991B1B] text-sm font-semibold hover:underline">Semua pesanan</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Meja</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($recent as $o): ?>
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-4 py-3 font-mono text-xs font-bold"><?= htmlspecialchars((string) $o['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars((string) ($o['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right font-semibold"><?= htmlspecialchars(Money::formatIdr((float) $o['total']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-xs"><?= htmlspecialchars(scanteen_kdash_status((string) $o['status']), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($recent === []): ?>
            <p class="p-6 text-center text-gray-400 text-sm">Belum ada pesanan.</p>
        <?php endif; ?>
    </div>
</div>
