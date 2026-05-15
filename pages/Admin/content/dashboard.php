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
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function scanteen_admin_status_class(string $status): string
{
    return match ($status) {
        'pending_payment' => 'bg-[#F1F3F5] text-gray-500',
        'cancelled' => 'bg-red-50 text-red-600',
        default => 'bg-[#FDE8E4] text-[var(--brand)]',
    };
}

function scanteen_admin_pm(string $pm): string
{
    return match ($pm) {
        'qris' => 'QRIS',
        'cashier' => 'Kasir',
        default => ucfirst($pm),
    };
}

function scanteen_admin_payment_icon(string $pm): string
{
    if ($pm === 'qris') {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[var(--brand)]"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="7" y="7" width="1" height="1"/><rect x="18" y="7" width="1" height="1"/><rect x="7" y="18" width="1" height="1"/><rect x="18" y="18" width="1" height="1"/></svg>';
    }

    return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="12" cy="12" r="3"/></svg>';
}

?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)] uppercase">Dashboard</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Real-time performance analytics untuk venue aktif.</p>
        <p class="text-[10px] font-bold text-[var(--text-muted)] mt-1">Format order: ORD-MMDD-SEQ, contoh ORD-0515-001.</p>
    </div>
    <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#FDE8E4] text-[var(--brand)] text-sm font-bold shadow-sm hover:bg-[#F5D5CE] transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Export
        </button>
        <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[var(--text-dark)] text-white text-sm font-bold shadow-md hover:opacity-90 transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Last 24h
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col justify-between h-32">
        <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Total Orders</p>
        <p class="poppins text-3xl font-bold text-[var(--text-dark)]"><?= (int) $stats['today_orders'] ?></p>
    </div>

    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col justify-between h-32">
        <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Warung Terlaris</p>
        <div class="flex items-center gap-3 mt-2">
            <div class="w-10 h-10 bg-[var(--brand)] rounded-lg flex items-center justify-center text-white">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3h18v18H3zM9 9v6M15 9v6"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-[var(--text-dark)]"><?= htmlspecialchars($topWarung['name'] ?? 'Belum ada data', ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-[10px] font-medium text-[var(--text-muted)]"><?= isset($topWarung['orders']) ? (int) $topWarung['orders'] . ' orders' : '0 orders' ?></p>
            </div>
        </div>
    </div>

    <div class="bg-[var(--brand)] p-6 rounded-[24px] shadow-lg flex flex-col justify-between h-32">
        <p class="text-[10px] font-extrabold text-[#F5E3DF] uppercase tracking-widest opacity-80">Revenue Today</p>
        <p class="poppins text-3xl font-bold text-white"><?= htmlspecialchars(Money::formatIdr($stats['today_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col justify-between h-32">
        <div class="flex justify-between items-start">
            <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Growth Weekly</p>
            <div class="flex items-end gap-0.5 h-6">
                <?php foreach ($sparkValues as $value): ?>
                    <div class="w-1 bg-<?= $value === max($sparkValues) ? '[var(--brand)]' : '#E9ECEF' ?> rounded-full" style="height: <?= max(4, (int) round(($value / $sparkMax) * 24)) ?>px;"></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <p class="poppins text-xl font-bold text-[var(--text-dark)] leading-tight"><?= htmlspecialchars(Money::formatIdr($currentWeekRevenue), ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-[10px] font-bold mt-1 <?= $weeklyGrowthPercent === null ? 'text-[var(--text-muted)]' : ($weeklyGrowthPercent >= 0 ? 'text-emerald-600' : 'text-red-600') ?>">
                <?= $weeklyGrowthPercent === null ? 'Belum ada pembanding minggu lalu' : sprintf('%+.1f%% vs minggu lalu', $weeklyGrowthPercent) ?>
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 relative overflow-hidden">
        <div class="flex items-start justify-between mb-8">
            <div>
                <h3 class="poppins text-lg font-bold text-[var(--brand)]">Revenue Analytics</h3>
                <p class="text-xs text-[var(--text-muted)] font-medium mt-1">Perbandingan omzet 7 hari terakhir dan minggu sebelumnya.</p>
            </div>
            <div class="flex gap-2 flex-wrap justify-end">
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-[#FAF7F6] border border-gray-100 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--brand)]"></span>
                    <span class="text-[10px] font-bold text-[var(--text-dark)]">This Week</span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-[#FAF7F6] border border-gray-100 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#F5D5CE]"></span>
                    <span class="text-[10px] font-bold text-[var(--text-dark)]">Last Week</span>
                </div>
            </div>
        </div>

        <div class="flex items-end justify-between h-48 px-2 border-b border-gray-100 pb-2 relative">
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                <div class="w-full border-t border-gray-50 h-px"></div>
                <div class="w-full border-t border-gray-50 h-px"></div>
                <div class="w-full border-t border-gray-50 h-px"></div>
                <div class="w-full border-t border-gray-50 h-px"></div>
            </div>

            <?php for ($i = 0; $i < count($chartLabels); $i++): ?>
                <?php
                $currentHeight = max(4, (int) round(($currentWeekValues[$i] / $chartMax) * 100));
                $previousHeight = max(4, (int) round(($previousWeekValues[$i] / $chartMax) * 100));
                ?>
                <div class="flex flex-col items-center flex-1 max-w-[60px] relative z-10">
                    <div class="flex items-end gap-1.5 w-full justify-center">
                        <div class="w-4 bg-[var(--brand-soft)] rounded-t-lg transition-all duration-500 hover:opacity-80 cursor-pointer" style="height: <?= $previousHeight ?>%;"></div>
                        <div class="w-4 <?= $currentWeekValues[$i] > 0 && $currentWeekValues[$i] === max($currentWeekValues) ? 'bg-[var(--brand)]' : 'bg-[var(--brand-soft)]' ?> rounded-t-lg transition-all duration-500 hover:opacity-80 cursor-pointer" style="height: <?= $currentHeight ?>%;"></div>
                    </div>
                    <span class="text-[9px] font-bold text-gray-400 mt-3"><?= htmlspecialchars($chartLabels[$i], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <h3 class="poppins text-lg font-bold text-[var(--brand)] mb-8">Payment Distribution</h3>

        <div class="flex flex-col items-center">
            <div class="relative w-40 h-40 flex items-center justify-center">
                <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                    <circle cx="18" cy="18" r="16" fill="none" stroke="#FDE8E4" stroke-width="3" stroke-dasharray="100, 100" />
                    <circle cx="18" cy="18" r="16" fill="none" stroke="var(--brand)" stroke-width="4" stroke-dasharray="<?= $digitalShare ?>, 100" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="poppins text-2xl font-black text-[var(--text-dark)] leading-none"><?= $digitalShare ?>%</span>
                    <span class="text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-1">Digital</span>
                </div>
                <div class="absolute left-0 top-1/2 -translate-x-1/2 w-6 h-4 bg-[#F5D5CE] rounded shadow-sm"></div>
            </div>

            <div class="w-full mt-10 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[var(--brand)]"></span>
                        <span class="text-xs font-bold text-[var(--text-dark)]">QRIS / Digital</span>
                    </div>
                    <span class="text-xs font-black text-[var(--text-dark)]"><?= htmlspecialchars(Money::formatIdr($digitalRevenue), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[#FDE8E4]"></span>
                        <span class="text-xs font-bold text-[var(--text-dark)]">Cash Payment</span>
                    </div>
                    <span class="text-xs font-black text-[var(--text-dark)]"><?= htmlspecialchars(Money::formatIdr($cashRevenue), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
    <div class="px-10 py-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="poppins text-lg font-bold text-[var(--brand)]">Recent Transactions</h3>

        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">Filter:</span>
                <div class="relative">
                    <select class="appearance-none bg-[#FDE8E4] text-[var(--brand)] text-[11px] font-bold px-4 py-2 pr-10 rounded-full border-none outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all cursor-pointer">
                        <option>Semua Warung</option>
                        <option>Warung Barokah</option>
                        <option>Artisan Bakery</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[var(--brand)]" width="10" height="6" viewBox="0 0 10 6" fill="none">
                        <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <a href="?page=orders" class="text-[11px] font-black text-[var(--brand)] uppercase tracking-widest hover:opacity-70 flex items-center gap-1">
                View All Activity
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#FAF7F6]">
                <tr>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Order ID</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Tenant</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Customer</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Amount</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Payment</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                    <th class="px-10 py-5 text-center text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if ($recent === []): ?>
                    <tr>
                        <td colspan="7" class="px-10 py-8 text-center text-gray-400 text-sm">Belum ada pesanan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent as $tx): ?>
                        <?php
                        $status = (string) ($tx['status'] ?? '');
                        $paymentMethod = (string) ($tx['payment_method'] ?? '');
                        ?>
                        <tr class="hover:bg-[#FAF7F6] transition-colors group">
                            <td class="px-10 py-6 text-sm font-black text-[var(--brand)]"><?= htmlspecialchars((string) $tx['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= htmlspecialchars((string) ($tx['tenant_names'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= htmlspecialchars((string) ($tx['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6 text-sm font-black text-[var(--text-dark)]"><?= htmlspecialchars(Money::formatIdr((float) $tx['total']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-2">
                                    <?= scanteen_admin_payment_icon($paymentMethod) ?>
                                    <span class="text-[11px] font-extrabold text-[var(--text-dark)] opacity-70"><?= htmlspecialchars(scanteen_admin_pm($paymentMethod), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black <?= scanteen_admin_status_class($status) ?>">
                                    <?= htmlspecialchars(scanteen_admin_status_badge($status), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <button class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white hover:shadow-sm text-gray-400 hover:text-[var(--brand)] transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="px-10 py-8 text-center bg-[#FAF7F6]/50">
        <button class="text-[11px] font-black text-[var(--text-muted)] uppercase tracking-[1px] hover:text-[var(--brand)] transition-colors">
            Load More Transactions
        </button>
    </div>
</div>
