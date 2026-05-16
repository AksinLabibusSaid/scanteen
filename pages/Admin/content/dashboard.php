<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$statsRepo = new VenueStatsRepository();
$stats = $statsRepo->dashboardKpis($venueId);
$recent = (new OrderListRepository())->listForVenue($venueId, 8);

$today = date('Y-m-d');
$currentWeekRevenue = 0.0;
$previousWeekRevenue = 0.0;
$chartLabels = [];
$currentWeekValues = [];
$previousWeekValues = [];

for ($i = 6; $i >= 0; $i--) {
    $currentDate = date('Y-m-d', strtotime('-' . $i . ' days'));
    $previousDate = date('Y-m-d', strtotime('-' . ($i + 7) . ' days'));

    $currentSummary = $statsRepo->summaryBetween($venueId, $currentDate, $currentDate);
    $previousSummary = $statsRepo->summaryBetween($venueId, $previousDate, $previousDate);

    $chartLabels[] = date('D', strtotime($currentDate));
    $currentWeekValues[] = (float) $currentSummary['revenue'];
    $previousWeekValues[] = (float) $previousSummary['revenue'];
    $currentWeekRevenue += (float) $currentSummary['revenue'];
    $previousWeekRevenue += (float) $previousSummary['revenue'];
}

$chartMax = max(1.0, max(array_merge($currentWeekValues, $previousWeekValues)));
$paymentTotalCount = (int) $stats['today_qris'] + (int) $stats['today_cashier'];
$digitalShare = $paymentTotalCount > 0 ? (int) round(((int) $stats['today_qris'] / $paymentTotalCount) * 100) : 0;
$digitalRevenue = $paymentTotalCount > 0 ? ($stats['today_revenue'] * ((int) $stats['today_qris'] / $paymentTotalCount)) : 0.0;
$cashRevenue = max(0.0, (float) $stats['today_revenue'] - $digitalRevenue);

$topWarung = $statsRepo->topWarungBetween($venueId, date('Y-m-d', strtotime('-6 days')), $today);
$weeklyGrowthPercent = $previousWeekRevenue > 0.0
    ? (($currentWeekRevenue - $previousWeekRevenue) / $previousWeekRevenue) * 100
    : null;

$sparkValues = array_slice($currentWeekValues, -4);
if ($sparkValues === []) {
    $sparkValues = [0, 0, 0, 0];
}
$sparkMax = max(1.0, max($sparkValues));

function scanteen_admin_status_badge(string $status): string
{
    return match ($status) {
        'pending_payment' => 'Menunggu Bayar',
        'paid' => 'Dibayar',
        'accepted' => 'Diterima',
        'processing' => 'Diproses',
        'ready' => 'Siap',
        'completed' => 'Selesai',
        'cancelled' => 'Batal',
        default => strtoupper(str_replace('_', ' ', $status)),
    };
}

function scanteen_admin_status_class(string $status): string
{
    return match ($status) {
        'pending_payment' => 'bg-gray-100 text-gray-500',
        'cancelled' => 'bg-red-50 text-[var(--error-red)]',
        'completed' => 'bg-[var(--success-bg)] text-[var(--success-green)]',
        'paid' => 'bg-blue-50 text-blue-600',
        default => 'bg-[var(--brand-muted)] text-[var(--brand)]',
    };
}

function scanteen_admin_pm(string $pm): string
{
    return match ($pm) {
        'qris' => 'QRIS',
        'cashier' => 'Kasir',
        default => strtoupper($pm),
    };
}

function scanteen_admin_payment_icon(string $pm): string
{
    if ($pm === 'qris') {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[var(--brand)]"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>';
    }
    return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[var(--text-muted)] opacity-50"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="12" cy="12" r="3"/></svg>';
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)] uppercase">Ringkasan Dashboard</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Analisis performa real-time untuk venue aktif di ekosistem Scanteen.</p>
    </div>
    <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-[var(--brand-muted)] text-[var(--brand)] text-[11px] font-black uppercase tracking-widest hover:bg-[var(--brand-soft)] transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4M7 10l5 5 5-5M12 15V3"/></svg>
            Export Data
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-all">
        <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest opacity-50">Total Pesanan Hari Ini</p>
        <div>
            <p class="poppins text-4xl font-bold text-[var(--text-dark)] tracking-tighter"><?= (int) $stats['today_orders'] ?></p>
            <p class="text-[10px] font-bold text-[var(--success-green)] mt-1 uppercase tracking-widest">Transaksi Aktif</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-all">
        <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest opacity-50">Warung Terlaris</p>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-[var(--brand-muted)] rounded-2xl flex items-center justify-center text-[var(--brand)] border-2 border-white shadow-sm">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3zM9 9v6M15 9v6"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-[var(--text-dark)] leading-tight"><?= htmlspecialchars($topWarung['name'] ?? 'Tidak Ada Data', ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-1 opacity-50"><?= isset($topWarung['orders']) ? (int) $topWarung['orders'] . ' pesanan' : '0 pesanan' ?></p>
            </div>
        </div>
    </div>

    <div class="bg-[var(--brand)] p-8 rounded-[24px] shadow-xl shadow-red-900/10 flex flex-col justify-between h-40 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        </div>
        <p class="text-[10px] font-black text-white/50 uppercase tracking-widest">Omzet Hari Ini</p>
        <p class="poppins text-3xl font-bold text-white tracking-tighter"><?= htmlspecialchars(Money::formatIdr($stats['today_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-all">
        <div class="flex justify-between items-start">
            <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest opacity-50">Omzet 7 Hari</p>
            <div class="flex items-end gap-1 h-8">
                <?php foreach ($sparkValues as $value): ?>
                    <div class="w-1.5 bg-<?= $value === max($sparkValues) ? '[var(--brand)]' : 'gray-100' ?> rounded-full transition-all duration-500" style="height: <?= max(4, (int) round(($value / $sparkMax) * 32)) ?>px;"></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <p class="poppins text-lg font-bold text-[var(--text-dark)] tracking-tight leading-none"><?= htmlspecialchars(Money::formatIdr($currentWeekRevenue), ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-[10px] font-black mt-2 uppercase tracking-widest <?= $weeklyGrowthPercent === null ? 'text-[var(--text-muted)] opacity-50' : ($weeklyGrowthPercent >= 0 ? 'text-[var(--success-green)]' : 'text-[var(--error-red)]') ?>">
                <?= $weeklyGrowthPercent === null ? 'Tidak Ada Data' : sprintf('%+.1f%% VS Minggu Lalu', $weeklyGrowthPercent) ?>
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
    <div class="lg:col-span-2 bg-white p-10 rounded-[32px] shadow-sm border border-gray-50 relative overflow-hidden">
        <div class="flex items-start justify-between mb-10">
            <div>
                <h3 class="poppins text-xl font-bold text-[var(--brand)]">Analisis Pendapatan</h3>
                <p class="text-xs text-[var(--text-muted)] font-medium mt-1">Perbandingan tren omzet 7 hari terakhir.</p>
            </div>
            <div class="flex gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-full border border-gray-100">
                    <span class="w-2 h-2 rounded-full bg-[var(--brand)]"></span>
                    <span class="text-[10px] font-black text-[var(--text-dark)] uppercase tracking-widest opacity-60">Minggu Ini</span>
                </div>
            </div>
        </div>

        <div class="flex items-end justify-between h-56 px-4 border-b border-gray-50 pb-4 relative">
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-5">
                <?php for($i=0;$i<5;$i++): ?><div class="w-full border-t border-[var(--text-dark)] h-px"></div><?php endfor; ?>
            </div>

            <?php for ($i = 0; $i < count($chartLabels); $i++): ?>
                <?php
                $currentHeight = max(4, (int) round(($currentWeekValues[$i] / $chartMax) * 100));
                $isMax = $currentWeekValues[$i] > 0 && $currentWeekValues[$i] === max($currentWeekValues);
                ?>
                <div class="flex flex-col items-center flex-1 max-w-[80px] relative z-10 group/bar">
                    <div class="w-full flex justify-center">
                        <div class="w-5 <?= $isMax ? 'bg-[var(--brand)] shadow-lg shadow-red-900/10' : 'bg-[var(--brand-muted)] opacity-60' ?> rounded-t-xl transition-all duration-500 hover:scale-x-110 cursor-pointer relative" style="height: <?= $currentHeight ?>%;">
                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-[var(--text-dark)] text-white text-[9px] font-black px-2 py-1 rounded opacity-0 group-hover/bar:opacity-100 transition-opacity whitespace-nowrap z-20">
                                <?= Money::formatIdr($currentWeekValues[$i]) ?>
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] font-black text-[var(--text-muted)] mt-5 uppercase tracking-widest opacity-40"><?= htmlspecialchars($chartLabels[$i], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="bg-white p-10 rounded-[32px] shadow-sm border border-gray-50 flex flex-col">
        <h3 class="poppins text-xl font-bold text-[var(--brand)] mb-10">Proporsi Pembayaran</h3>

        <div class="flex-1 flex flex-col items-center justify-center">
            <div class="relative w-44 h-44 flex items-center justify-center">
                <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                    <circle cx="18" cy="18" r="16" fill="none" stroke="var(--brand-muted)" stroke-width="4" stroke-dasharray="100, 100" />
                    <circle cx="18" cy="18" r="16" fill="none" stroke="var(--brand)" stroke-width="5" stroke-dasharray="<?= $digitalShare ?>, 100" stroke-linecap="round" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="poppins text-3xl font-bold text-[var(--text-dark)] leading-none"><?= $digitalShare ?>%</span>
                    <span class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mt-2 opacity-50">QRIS</span>
                </div>
            </div>

            <div class="w-full mt-12 space-y-5">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--brand)]"></span>
                        <span class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-widest opacity-70">QRIS / Digital</span>
                    </div>
                    <span class="text-xs font-black text-[var(--text-dark)] poppins"><?= htmlspecialchars(Money::formatIdr($digitalRevenue), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--brand-muted)]"></span>
                        <span class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-widest opacity-40">Tunai / Kasir</span>
                    </div>
                    <span class="text-xs font-black text-[var(--text-dark)] poppins"><?= htmlspecialchars(Money::formatIdr($cashRevenue), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-10 py-8 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <h3 class="poppins text-lg font-bold text-[var(--brand)]">Transaksi Terbaru</h3>
            <p class="text-xs text-[var(--text-muted)] font-medium mt-1">Menampilkan aktivitas penjualan terkini di semua stan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-6">
            <a href="?page=orders" class="px-5 py-2.5 bg-gray-50 text-[var(--brand)] rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[var(--brand)] hover:text-white transition-all flex items-center gap-3 group">
                Kelola Semua Pesanan
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#FAF7F6]">
                <tr>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">ID Pesanan</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Stan / Tenant</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Pelanggan</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Total Harga</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Metode Bayar</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if ($recent === []): ?>
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center text-[var(--text-muted)] text-sm font-medium">Belum ada transaksi terekam hari ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent as $tx): ?>
                        <?php
                        $status = (string) ($tx['status'] ?? '');
                        $paymentMethod = (string) ($tx['payment_method'] ?? '');
                        ?>
                        <tr class="hover:bg-[#FAF7F6] transition-colors group">
                            <td class="px-10 py-6">
                                <span class="text-sm font-black text-[var(--brand)]">#<?= htmlspecialchars((string) $tx['order_number'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-10 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70">
                                <div class="truncate max-w-[150px]"><?= htmlspecialchars((string) ($tx['tenant_names'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td class="px-10 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= htmlspecialchars((string) ($tx['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6 text-sm font-black text-[var(--text-dark)] poppins"><?= htmlspecialchars(Money::formatIdr((float) $tx['total']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 opacity-60">
                                        <?= scanteen_admin_payment_icon($paymentMethod) ?>
                                    </div>
                                    <span class="text-[10px] font-black text-[var(--text-dark)] opacity-40 uppercase tracking-widest"><?= htmlspecialchars(scanteen_admin_pm($paymentMethod), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black <?= scanteen_admin_status_class($status) ?> uppercase tracking-widest border border-current/10">
                                    <?= htmlspecialchars(scanteen_admin_status_badge($status), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
