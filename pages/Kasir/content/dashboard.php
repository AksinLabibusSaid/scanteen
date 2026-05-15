<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$kpis = (new VenueStatsRepository())->dashboardKpis($venueId);
$recent = (new OrderListRepository())->listForVenue($venueId, 8);

function scanteen_kdash_status_badge(string $s): string
{
    return match ($s) {
        'pending_payment' => 'bg-orange-50 text-orange-600 border-orange-100',
        'paid' => 'bg-blue-50 text-blue-600 border-blue-100',
        'accepted', 'processing' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        'ready', 'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'cancelled' => 'bg-red-50 text-red-600 border-red-100',
        default => 'bg-gray-50 text-gray-600 border-gray-100',
    };
}

function scanteen_kdash_status_label(string $s): string
{
    return match ($s) {
        'pending_payment' => 'MENUNGGU',
        'paid' => 'DIBAYAR',
        'accepted' => 'DITERIMA',
        'processing' => 'DIPROSES',
        'ready' => 'SIAP',
        'completed' => 'SELESAI',
        'cancelled' => 'BATAL',
        default => strtoupper($s),
    };
}
?>

<div class="flex flex-col gap-10 pb-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-4xl font-bold text-[#261817] tracking-tight">Ringkasan Venue</h1>
            <p class="text-gray-500 text-lg mt-2 font-medium">Selamat datang kembali, <span class="text-[#7B0009] font-bold"><?= htmlspecialchars((string) StaffAuth::userName(), ENT_QUOTES, 'UTF-8') ?></span>.</p>
        </div>
        <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl border border-gray-100 shadow-sm">
            <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Sistem Aktif</span>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/30 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#00C853] group-hover:w-3 transition-all"></div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Pesanan Hari Ini</p>
            <div class="flex items-baseline gap-3 mt-2">
                <h3 class="text-4xl font-black text-[#261817]"><?= (int) $kpis['today_orders'] ?></h3>
                <span class="text-gray-400 text-sm font-bold uppercase tracking-wide">transaksi</span>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/30 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#FF9100] group-hover:w-3 transition-all"></div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Menunggu Bayar</p>
            <div class="flex items-baseline gap-3 mt-2">
                <h3 class="text-4xl font-black text-[#FF9100]"><?= (int) $kpis['pending_payment'] ?></h3>
                <span class="text-gray-400 text-sm font-bold uppercase tracking-wide">antrean</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/30 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#7B0009] group-hover:w-3 transition-all"></div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Omzet Hari Ini</p>
            <div class="flex items-baseline gap-2 mt-2">
                <h3 class="text-3xl font-black text-[#261817]"><?= htmlspecialchars(Money::formatIdr($kpis['today_revenue']), ENT_QUOTES, 'UTF-8') ?></h3>
            </div>
        </div>

        <div class="bg-[#7B0009] p-8 rounded-[2rem] shadow-2xl shadow-red-900/20 text-white relative overflow-hidden">
            <p class="text-red-300/80 text-[10px] font-black uppercase tracking-[0.2em]">Omzet 7 Hari</p>
            <h3 class="text-2xl font-black mt-2 leading-tight"><?= htmlspecialchars(Money::formatIdr($kpis['week_revenue']), ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="text-red-200/60 text-xs font-bold mt-1 tracking-widest">Data Real-time</p>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-10 py-8 border-b border-gray-50">
            <div>
                <h2 class="text-xl font-black text-[#261817]">Pesanan Terbaru</h2>
                <p class="text-gray-400 text-xs font-medium mt-1">Menampilkan 8 transaksi terakhir yang masuk.</p>
            </div>
            <a href="?page=orders" class="px-6 py-2.5 bg-[#7B0009]/5 text-[#7B0009] rounded-xl text-xs font-bold hover:bg-[#7B0009] hover:text-white transition-all duration-300">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[#FFF5F5] border-b border-gray-50 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">
                        <th class="px-10 py-5">No. Pesanan</th>
                        <th class="px-6 py-5">Meja</th>
                        <th class="px-6 py-5">Pelanggan</th>
                        <th class="px-6 py-5">Total Harga</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-10 py-5 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($recent)): ?>
                        <tr>
                            <td colspan="6" class="px-10 py-20 text-center">
                                <p class="text-gray-400 font-medium">Belum ada pesanan terbaru.</p>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($recent as $o): ?>
                        <tr class="group hover:bg-[#FFFAFA] transition-colors">
                            <td class="px-10 py-6">
                                <span class="font-bold text-[#261817]">#<?= htmlspecialchars((string) $o['order_number'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-6">
                                <span class="px-3 py-1 bg-gray-50 rounded-lg text-xs font-bold text-gray-500">T-<?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-6 text-sm font-bold text-gray-600">
                                <?= htmlspecialchars((string) ($o['customer_name'] ?? 'Umum'), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-6 font-black text-[#261817]">
                                <?= htmlspecialchars(Money::formatIdr((float) $o['total']), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black border tracking-wider <?= scanteen_kdash_status_badge((string) $o['status']) ?>">
                                    <?= scanteen_kdash_status_label((string) $o['status']) ?>
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right text-xs font-bold text-gray-400">
                                <?= date('H:i', strtotime((string) $o['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
