<?php
declare(strict_types=1);

use App\Repositories\MenuRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId   = (int) StaffAuth::venueId();
$statsRepo = new VenueStatsRepository();
$menuRepo  = new MenuRepository();

// Pilih offset minggu (0 = minggu ini, 1 = minggu lalu, dst.)
$weekOffset = max(0, (int) ($_GET['week'] ?? 0));

// Hitung rentang tanggal minggu yang dipilih
$weekStart = date('Y-m-d', strtotime('-' . (($weekOffset * 7) + 6) . ' days'));
$weekEnd   = date('Y-m-d', strtotime('-' . ($weekOffset * 7) . ' days'));

// Weekly summary (7 hari dalam rentang terpilih)
$chartLabels      = [];
$chartValues      = [];
$chartOrders      = [];
$totalRevenue     = 0.0;
$totalOrders      = 0;
$peakRevenue      = 0.0;

for ($i = 6; $i >= 0; $i--) {
    $d    = date('Y-m-d', strtotime('-' . (($weekOffset * 7) + $i) . ' days'));
    $sum  = $statsRepo->summaryBetween($venueId, $d, $d);
    $chartLabels[] = date('D, d/m', strtotime($d));
    $chartValues[] = (float) $sum['revenue'];
    $chartOrders[] = (int)  $sum['orders'];
    $totalRevenue  += (float) $sum['revenue'];
    $totalOrders   += (int)  $sum['orders'];
    if ((float)$sum['revenue'] > $peakRevenue) {
        $peakRevenue = (float)$sum['revenue'];
    }
}

$avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0.0;
$chartMax      = max(1.0, $peakRevenue);

// Tenant performance
$tenantBreakdown = $statsRepo->warungRevenueBreakdown($venueId, $weekStart, $weekEnd);
$maxTenantRev    = max(1.0, (float) ($tenantBreakdown[0]['revenue'] ?? 0));

// Total menu aktif
$allCategories   = $menuRepo->listCategories();
$allMenus        = $menuRepo->listAdminByVenue($venueId);
$totalMenuAktif  = count(array_filter($allMenus, fn($m) => (int)$m['is_available'] === 1));
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Laporan &amp; Analitik</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Pantau performa operasional kantin secara real-time.</p>
    </div>
    <!-- Week Filter -->
    <div class="flex items-center gap-3">
        <?php if ($weekOffset > 0): ?>
        <a href="?page=reports&week=<?= $weekOffset - 1 ?>" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FDE8E4] text-[var(--brand)] text-xs font-black uppercase tracking-widest hover:bg-[#F5D5CE] transition-all">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"/></svg>
            Lebih Baru
        </a>
        <?php endif; ?>
        <div class="px-4 py-2.5 rounded-xl bg-white border border-gray-100 text-xs font-black text-[var(--text-dark)]">
            <?php if ($weekOffset === 0): ?>
            Minggu Ini
            <?php elseif ($weekOffset === 1): ?>
            Minggu Lalu
            <?php else: ?>
            <?= $weekOffset ?> Minggu Lalu
            <?php endif; ?>
            <span class="text-gray-400 font-bold ml-1">(<?= date('d/m', strtotime($weekStart)) ?> – <?= date('d/m', strtotime($weekEnd)) ?>)</span>
        </div>
        <a href="?page=reports&week=<?= $weekOffset + 1 ?>" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FDE8E4] text-[var(--brand)] text-xs font-black uppercase tracking-widest hover:bg-[#F5D5CE] transition-all">
            Lebih Lama
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="12" cy="12" r="3"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Pendapatan Total</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]"><?= Money::formatIdr($totalRevenue) ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Total Pesanan</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]"><?= number_format($totalOrders, 0, ',', '.') ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Menu Aktif</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]"><?= $totalMenuAktif ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><line x1="12" y1="18" x2="12" y2="6"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Avg. Order Value</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]"><?= Money::formatIdr($avgOrderValue) ?></p>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 mb-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="poppins text-lg font-black text-[var(--brand)]">Revenue &amp; Order Trend</h3>
            <p class="text-xs text-gray-400 font-medium mt-1">Pendapatan harian dalam periode yang dipilih</p>
        </div>
    </div>

    <?php if ($totalRevenue <= 0): ?>
    <div class="h-48 flex items-center justify-center text-gray-400 text-sm">
        Belum ada data transaksi di periode ini.
    </div>
    <?php else: ?>
    <!-- Bar Chart -->
    <div class="flex items-end justify-between h-48 gap-2 border-b border-gray-100 pb-2 mb-4">
        <?php foreach ($chartValues as $i => $val):
            $h = max(4, (int) round(($val / $chartMax) * 100));
            $isPeak = $val === $peakRevenue && $peakRevenue > 0;
        ?>
        <div class="flex flex-col items-center flex-1 gap-1">
            <span class="text-[8px] font-black text-[var(--brand)] <?= $isPeak ? '' : 'opacity-0' ?>">▲</span>
            <div class="w-full <?= $isPeak ? 'bg-[var(--brand)]' : 'bg-[var(--brand-soft)]' ?> rounded-t-lg transition-all duration-500 hover:opacity-80 cursor-pointer"
                 style="height:<?= $h ?>%"
                 title="<?= htmlspecialchars($chartLabels[$i], ENT_QUOTES, 'UTF-8') ?>: <?= Money::formatIdr($val) ?> (<?= $chartOrders[$i] ?> pesanan)">
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="flex justify-between gap-2">
        <?php foreach ($chartLabels as $lbl): ?>
        <div class="flex-1 text-center text-[8px] font-bold text-gray-400"><?= htmlspecialchars($lbl, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Tenant Performance + Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
    <div class="lg:col-span-3 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center justify-between mb-8">
            <h3 class="poppins text-lg font-black text-[var(--brand)]">Performa Tenant</h3>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest"><?= date('d/m', strtotime($weekStart)) ?> – <?= date('d/m', strtotime($weekEnd)) ?></span>
        </div>

        <?php if ($tenantBreakdown === []): ?>
        <p class="text-sm text-gray-400 text-center py-8">Belum ada data transaksi tenant di periode ini.</p>
        <?php else: ?>
        <div class="space-y-7">
            <?php foreach ($tenantBreakdown as $idx => $t):
                $rev = (float) $t['revenue'];
                $pct = $maxTenantRev > 0 ? (int) round(($rev / $maxTenantRev) * 100) : 0;
                $opacity = max(0.3, 1 - ($idx * 0.18));
            ?>
            <div class="space-y-2">
                <div class="flex justify-between items-end">
                    <p class="text-xs font-black text-[var(--text-dark)] opacity-80"><?= htmlspecialchars($t['warung_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-xs font-black text-[var(--brand)]"><?= Money::formatIdr($rev) ?></p>
                </div>
                <div class="w-full h-3 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700"
                         style="width:<?= $pct ?>%; background-color:var(--brand); opacity:<?= $opacity ?>"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Daily Orders -->
    <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <h3 class="poppins text-lg font-black text-[var(--brand)] mb-8">Pesanan Harian</h3>
        <?php if ($totalOrders === 0): ?>
        <p class="text-sm text-gray-400 text-center py-8">Tidak ada pesanan di periode ini.</p>
        <?php else: ?>
        <div class="space-y-4">
            <?php
            $maxOrd = max(1, max($chartOrders));
            foreach ($chartOrders as $i => $ord):
                $pct = (int) round(($ord / $maxOrd) * 100);
                $parts = explode(', ', $chartLabels[$i]);
                $dayLabel = $parts[0];
                $dateLabel = $parts[1] ?? '';
            ?>
            <div class="flex items-center gap-3">
                <div class="w-16 text-right">
                    <span class="text-[9px] font-black text-gray-400 uppercase"><?= $dayLabel ?></span>
                    <span class="text-[8px] font-bold text-gray-300 block"><?= $dateLabel ?></span>
                </div>
                <div class="flex-1 h-2.5 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--brand)] rounded-full transition-all duration-700" style="width:<?= $pct ?>%"></div>
                </div>
                <span class="text-xs font-black text-[var(--text-dark)] w-8 text-right"><?= $ord ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
