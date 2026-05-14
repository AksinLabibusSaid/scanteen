<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$stats = (new VenueStatsRepository())->dashboardKpis($venueId);
$recent = (new OrderListRepository())->listForVenue($venueId, 12);

function scanteen_admin_status_badge(string $s): string
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

function scanteen_admin_pm(string $pm): string
{
    return match ($pm) {
        'qris' => 'QRIS',
        'cashier' => 'Kasir',
        default => $pm,
    };
}
?>

<div class="flex flex-col gap-6 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Dashboard</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Ringkasan venue Anda dari database.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Pesanan hari ini</p>
            <p class="poppins text-3xl font-bold text-[var(--text-dark)] mt-2"><?= (int) $stats['today_orders'] ?></p>
        </div>
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Omzet hari ini</p>
            <p class="poppins text-2xl font-bold text-[var(--text-dark)] mt-2"><?= htmlspecialchars(Money::formatIdr($stats['today_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Menunggu bayar</p>
            <p class="poppins text-3xl font-bold text-orange-700 mt-2"><?= (int) $stats['pending_payment'] ?></p>
        </div>
        <div class="bg-[var(--brand)] p-6 rounded-[24px] shadow-lg text-white">
            <p class="text-[10px] font-extrabold uppercase tracking-widest opacity-80">Omzet 7 hari</p>
            <p class="poppins text-2xl font-bold mt-2"><?= htmlspecialchars(Money::formatIdr($stats['week_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-[10px] mt-2 opacity-80">QRIS hari ini: <?= (int) $stats['today_qris'] ?> · Kasir: <?= (int) $stats['today_cashier'] ?></p>
        </div>
    </div>

    <div class="bg-white rounded-[24px] shadow-sm border border-gray-50 overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
            <h2 class="poppins text-lg font-bold text-[var(--brand)]">Pesanan terbaru</h2>
            <a href="?page=orders" class="text-xs font-bold text-[var(--brand)] hover:underline">Kelola pesanan</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#FAF7F6]">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">No.</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Meja</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Bayar</th>
                        <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($recent as $r): ?>
                        <tr class="hover:bg-[#FAF7F6]/60">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-[var(--brand)]"><?= htmlspecialchars((string) $r['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars((string) $r['table_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars((string) ($r['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-xs"><?= htmlspecialchars(scanteen_admin_pm((string) ($r['payment_method'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right font-bold"><?= htmlspecialchars(Money::formatIdr((float) $r['total']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-xs"><?= htmlspecialchars(scanteen_admin_status_badge((string) $r['status']), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($recent === []): ?>
            <p class="p-8 text-center text-gray-400 text-sm">Belum ada pesanan.</p>
        <?php endif; ?>
    </div>
</div>
