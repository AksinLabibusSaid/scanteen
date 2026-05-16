<?php

declare(strict_types=1);

use App\Repositories\MenuRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId   = (int) StaffAuth::venueId();
$statsRepo = new VenueStatsRepository();
$menuRepo  = new MenuRepository();

// Filter per hari
$selectedDate = isset($_GET['date']) ? trim((string)$_GET['date']) : date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
    $selectedDate = date('Y-m-d');
}

// Daily summary
$sum = $statsRepo->summaryBetween($venueId, $selectedDate, $selectedDate);
$totalRevenue = (float) $sum['revenue'];
$totalOrders  = (int)  $sum['orders'];
$avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0.0;

// Tenant performance for the selected date
$tenantBreakdown = $statsRepo->warungRevenueBreakdown($venueId, $selectedDate, $selectedDate);
$maxTenantRev    = 0.0;
$maxTenantOrders = 0;

foreach ($tenantBreakdown as $t) {
    if ((float)$t['revenue'] > $maxTenantRev) {
        $maxTenantRev = (float)$t['revenue'];
    }
}

// Weekly Tenant performance (7 days ending on selected date)
$weekEnd = $selectedDate;
$weekStart = date('Y-m-d', strtotime($selectedDate . ' -6 days'));
$weeklyTenantBreakdown = $statsRepo->warungRevenueBreakdown($venueId, $weekStart, $weekEnd);

// Sort weekly tenant stats by Warung Name (natural order)
usort($weeklyTenantBreakdown, function ($a, $b) {
    return strnatcasecmp($a['warung_name'], $b['warung_name']);
});

$maxWeeklyTenantRev = 0.0;
foreach ($weeklyTenantBreakdown as $t) {
    if ((float)$t['revenue'] > $maxWeeklyTenantRev) {
        $maxWeeklyTenantRev = (float)$t['revenue'];
    }
}

// Get order counts per warung for the selected date (Daily)
$tenantStats = [];
foreach ($tenantBreakdown as $t) {
    $wId = (int)$t['warung_id'];
    $wSum = $statsRepo->summaryBetween($venueId, $selectedDate, $selectedDate, $wId);
    $ordersCount = (int)$wSum['orders'];
    if ($ordersCount > $maxTenantOrders) {
        $maxTenantOrders = $ordersCount;
    }
    $tenantStats[] = [
        'id' => $wId,
        'name' => $t['warung_name'],
        'revenue' => (float)$t['revenue'],
        'orders' => $ordersCount
    ];
}

// Sort tenant stats by Warung Name (natural order: Warung 1, Warung 2, Warung 10...)
usort($tenantStats, function ($a, $b) {
    return strnatcasecmp($a['name'], $b['name']);
});

$chartMax = max(1.0, $maxTenantRev);

// Total menu aktif
$allMenus        = $menuRepo->listAdminByVenue($venueId);
$totalMenuAktif  = count(array_filter($allMenus, fn($m) => (int)$m['is_available'] === 1));
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Laporan &amp; Analitik</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Pantau performa operasional kantin berdasarkan tanggal.</p>
    </div>
    <!-- Daily Filter -->
    <form method="get" class="flex items-center gap-3">
        <input type="hidden" name="page" value="reports">
        <label for="dateFilter" class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">Pilih Tanggal</label>
        <input type="date" id="dateFilter" name="date" value="<?= htmlspecialchars($selectedDate, ENT_QUOTES, 'UTF-8') ?>"
            class="px-4 py-2.5 rounded-xl bg-white border border-gray-100 text-xs font-black text-[var(--text-dark)] outline-none cursor-pointer focus:ring-2 focus:ring-[var(--brand-soft)] transition-all"
            onchange="this.form.submit()">
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="2" y="5" width="20" height="14" rx="2" />
                <circle cx="12" cy="12" r="3" />
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
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 0 1-8 0" />
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
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
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
                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1Z" />
                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                <line x1="12" y1="18" x2="12" y2="6" />
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
            <h3 class="poppins text-lg font-black text-[var(--brand)]">Pendapatan Per Warung</h3>
            <p class="text-xs text-gray-400 font-medium mt-1">Distribusi pendapatan antar warung pada <?= date('d/m/Y', strtotime($selectedDate)) ?></p>
        </div>
    </div>

    <?php if ($totalRevenue <= 0): ?>
        <div class="h-48 flex items-center justify-center text-gray-400 text-sm">
            Belum ada data transaksi di periode ini.
        </div>
    <?php else: ?>
        <!-- Bar Chart -->
        <div class="flex items-end justify-around h-48 gap-4 border-b border-gray-100 pb-2 mb-4">
            <?php foreach ($tenantStats as $i => $t):
                $val = $t['revenue'];
                $h = max(4, (int) round(($val / $chartMax) * 100));
                $isPeak = $val === $maxTenantRev && $maxTenantRev > 0;
            ?>
                <div class="flex flex-col items-center flex-1 justify-end h-full">
                    <div class="w-12 sm:w-16 <?= $isPeak ? 'bg-[var(--brand)]' : 'bg-[var(--brand-soft)]' ?> rounded-t-lg transition-all duration-500 hover:opacity-80 cursor-pointer relative group"
                        style="height:<?= $h ?>%">

                        <!-- Peak Indicator -->
                        <span class="text-[8px] font-black text-[var(--brand)] absolute -top-4 left-1/2 -translate-x-1/2 <?= $isPeak ? '' : 'opacity-0' ?>">▲</span>

                        <!-- Hover Tooltip -->
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 opacity-0 group-hover:opacity-100 group-hover:-translate-y-1 transition-all duration-300 pointer-events-none z-10 flex flex-col items-center">
                            <div class="bg-gray-800 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-md shadow-xl whitespace-nowrap border border-gray-700">
                                <?= Money::formatIdr($val) ?>
                            </div>
                            <div class="w-2 h-2 bg-gray-800 border-r border-b border-gray-700 rotate-45 -mt-1.5 rounded-sm"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between gap-2">
            <?php foreach ($tenantStats as $t):
                $name = $t['name'];
                $shortName = strlen($name) > 8 ? substr($name, 0, 6) . '..' : $name;
            ?>
                <div class="flex-1 text-center text-[8px] font-bold text-gray-400" title="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($shortName, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Tenant Performance + Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
    <div class="lg:col-span-3 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center justify-between mb-8">
            <h3 class="poppins text-lg font-black text-[var(--brand)]">Performa Tenant</h3>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest"><?= date('d/m/Y', strtotime($weekStart)) ?> - <?= date('d/m/Y', strtotime($weekEnd)) ?></span>
        </div>

        <?php if ($weeklyTenantBreakdown === []): ?>
            <p class="text-sm text-gray-400 text-center py-8">Belum ada data transaksi tenant di periode ini.</p>
        <?php else: ?>
            <div class="space-y-7">
                <?php foreach ($weeklyTenantBreakdown as $idx => $t):
                    $rev = $t['revenue'];
                    $pct = $maxWeeklyTenantRev > 0 ? (int) round(($rev / $maxWeeklyTenantRev) * 100) : 0;
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
        <h3 class="poppins text-lg font-black text-[var(--brand)] mb-8">Pesanan Per Warung</h3>
        <?php if ($totalOrders === 0): ?>
            <p class="text-sm text-gray-400 text-center py-8">Tidak ada pesanan di periode ini.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php
                $maxOrd = max(1, $maxTenantOrders);
                // Use tenantStats which is already sorted by name naturally
                $orderStats = $tenantStats;

                foreach ($orderStats as $i => $t):
                    $ord = $t['orders'];
                    $pct = (int) round(($ord / $maxOrd) * 100);
                    $name = strlen($t['name']) > 12 ? substr($t['name'], 0, 10) . '..' : $t['name'];
                ?>
                    <div class="flex items-center gap-3">
                        <div class="w-20 text-right">
                            <span class="text-[9px] font-black text-gray-500 uppercase" title="<?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
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
</div>