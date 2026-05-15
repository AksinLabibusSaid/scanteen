<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$statsRepo = new VenueStatsRepository();
$orderListRepo = new OrderListRepository();
$warungRepo = new WarungRepository();

// Filter parameters
$mode = $_GET['mode'] ?? 'daily'; // daily, monthly, yearly, custom
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedMonth = (int)($_GET['month'] ?? date('m'));
$selectedYear = (int)($_GET['year'] ?? date('Y'));
$customFrom = $_GET['from'] ?? date('Y-m-01');
$customTo = $_GET['to'] ?? date('Y-m-d');
$search = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$selectedWarung = isset($_GET['warung_id']) ? (int) $_GET['warung_id'] : 0;

// Resolve $from and $to based on mode
$from = '';
$to = '';

switch ($mode) {
    case 'daily':
        $from = $selectedDate;
        $to = $selectedDate;
        break;
    case 'monthly':
        $from = sprintf('%04d-%02d-01', $selectedYear, $selectedMonth);
        $to = date('Y-m-t', strtotime($from));
        break;
    case 'yearly':
        $from = sprintf('%04d-01-01', $selectedYear);
        $to = sprintf('%04d-12-31', $selectedYear);
        break;
    case 'custom':
    default:
        $from = $customFrom;
        $to = $customTo;
        break;
}

if ($from > $to) {
    $t = $from;
    $from = $to;
    $to = $t;
}

// Fetch summaries
$sum = $statsRepo->summaryBetween($venueId, $from, $to, $selectedWarung > 0 ? $selectedWarung : null);
$topWarung = $statsRepo->topWarungBetween($venueId, $from, $to, $selectedWarung > 0 ? $selectedWarung : null);
$breakdown = $statsRepo->warungRevenueBreakdown($venueId, $from, $to, $selectedWarung > 0 ? $selectedWarung : null);

// Fetch orders (Recent 15 in this period)
$orders = $orderListRepo->listForVenueFiltered(
    $venueId,
    15, 
    null,
    $from,
    $to,
    null,
    $selectedWarung > 0 ? $selectedWarung : null,
    $search !== '' ? $search : null
);

$allWarungs = $warungRepo->listByVenueId($venueId);

function scanteen_report_status_badge(string $s): string
{
    return match ($s) {
        'completed' => 'bg-emerald-50 text-emerald-600',
        'cancelled' => 'bg-red-50 text-red-600',
        default => 'bg-blue-50 text-blue-600',
    };
}
?>

<div class="flex flex-col gap-10 pb-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 no-print">
        <div>
            <h1 class="text-4xl font-bold text-[#261817] tracking-tight">Laporan Harian & Stan</h1>
            <p class="text-gray-500 text-lg mt-2 font-medium">Pantau performa pendapatan per stan secara detail.</p>
        </div>
        <div class="flex flex-col md:flex-row items-end gap-4">
            <button onclick="window.print()" class="flex items-center gap-3 px-6 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm hover:bg-gray-50 transition-all group">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7B0009" stroke-width="2.5" class="group-hover:scale-110 transition-transform">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span class="text-xs font-black text-[#261817] uppercase tracking-widest">Export PDF</span>
            </button>
            <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl border border-gray-100 shadow-sm">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Periode: <span class="text-[#7B0009]"><?= date('d M Y', strtotime($from)) ?> <?= $from !== $to ? '- ' . date('d M Y', strtotime($to)) : '' ?></span></span>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100/30 border-t-4 border-[#00C853] flex flex-col group">
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest">Pendapatan</p>
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-3xl font-black text-[#261817]"><?= htmlspecialchars(Money::formatIdr($sum['revenue']), ENT_QUOTES, 'UTF-8') ?></h3>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100/30 border-t-4 border-[#2979FF] flex flex-col group">
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest">Total Pesanan</p>
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <h3 class="text-3xl font-black text-[#261817]"><?= number_format($sum['orders']) ?> <span class="text-sm font-bold text-gray-300">Transaksi</span></h3>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100/30 border-t-4 border-[#7B0009] flex flex-col group">
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest">Stan Terlaris</p>
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-[#7B0009]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-[#261817] truncate"><?= $topWarung ? htmlspecialchars($topWarung['name'], ENT_QUOTES, 'UTF-8') : '-' ?></h3>
            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest"><?= $topWarung ? Money::formatIdr($topWarung['revenue']) : 'Belum ada data' ?></p>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="get" id="filter-form" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-50 no-print">
        <input type="hidden" name="page" value="reports">
        
        <div class="flex flex-col lg:flex-row gap-8 items-end">
            <!-- Mode Selector -->
            <div class="w-full lg:w-48 space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Mode</label>
                <select name="mode" id="filter-mode" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold outline-none cursor-pointer focus:ring-2 focus:ring-[#7B0009]/20 transition-all">
                    <option value="daily" <?= $mode === 'daily' ? 'selected' : '' ?>>Harian</option>
                    <option value="monthly" <?= $mode === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                    <option value="yearly" <?= $mode === 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                    <option value="custom" <?= $mode === 'custom' ? 'selected' : '' ?>>Custom</option>
                </select>
            </div>

            <!-- Dynamic Inputs Container -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                <!-- Daily -->
                <div class="space-y-2 mode-input" id="mode-daily" style="<?= $mode === 'daily' ? '' : 'display:none' ?>">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Tanggal</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($selectedDate, ENT_QUOTES, 'UTF-8') ?>" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[#7B0009]/20 transition-all">
                </div>

                <!-- Monthly -->
                <div class="space-y-2 mode-input" id="mode-monthly-month" style="<?= $mode === 'monthly' ? '' : 'display:none' ?>">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Bulan</label>
                    <select name="month" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[#7B0009]/20 transition-all">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?= $m ?>" <?= $selectedMonth === $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Custom -->
                <div class="space-y-2 mode-input" id="mode-custom-from" style="<?= $mode === 'custom' ? '' : 'display:none' ?>">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Dari</label>
                    <input type="date" name="from" value="<?= htmlspecialchars($customFrom, ENT_QUOTES, 'UTF-8') ?>" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[#7B0009]/20 transition-all">
                </div>
                <div class="space-y-2 mode-input" id="mode-custom-to" style="<?= $mode === 'custom' ? '' : 'display:none' ?>">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai</label>
                    <input type="date" name="to" value="<?= htmlspecialchars($customTo, ENT_QUOTES, 'UTF-8') ?>" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[#7B0009]/20 transition-all">
                </div>

                <!-- Filter Stan -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Filter Stan</label>
                    <select name="warung_id" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold outline-none cursor-pointer focus:ring-2 focus:ring-[#7B0009]/20 transition-all">
                        <option value="0">Semua Stan</option>
                        <?php foreach ($allWarungs as $w): ?>
                            <option value="<?= (int)$w['id'] ?>" <?= $selectedWarung === (int)$w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full lg:w-auto px-10 py-3.5 bg-[#7B0009] text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-red-900/20 hover:opacity-90 transition-all">Terapkan Filter</button>
        </div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Warung Breakdown List -->
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-[3rem] shadow-xl shadow-gray-100/30 border border-gray-100 h-full">
                <h2 class="text-xl font-black text-[#261817] mb-8">Pendapatan Per Stan</h2>
                <div class="space-y-6">
                    <?php if (empty($breakdown)): ?>
                        <p class="text-gray-400 text-sm italic">Data tidak tersedia.</p>
                    <?php endif; ?>
                    <?php foreach ($breakdown as $b): ?>
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#7B0009] group-hover:scale-150 transition-transform"></div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-700"><?= htmlspecialchars($b['warung_name'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">ID: #STN-<?= (int)$b['warung_id'] ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-[#261817]"><?= htmlspecialchars(Money::formatIdr((float)$b['revenue']), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-100/50 border border-gray-100 overflow-hidden h-full">
                <div class="px-10 py-8 flex flex-col md:flex-row justify-between items-center gap-6 border-b border-gray-50">
                    <h2 class="text-xl font-black text-[#261817]">Log Transaksi</h2>
                    <div class="relative w-full md:w-64">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <form method="get">
                            <input type="hidden" name="page" value="reports">
                            <input type="hidden" name="mode" value="<?= $mode ?>">
                            <input type="hidden" name="date" value="<?= $selectedDate ?>">
                            <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                            <input type="hidden" name="year" value="<?= $selectedYear ?>">
                            <input type="hidden" name="from" value="<?= $from ?>">
                            <input type="hidden" name="to" value="<?= $to ?>">
                            <input type="hidden" name="warung_id" value="<?= $selectedWarung ?>">
                            <input type="text" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold outline-none focus:bg-white focus:ring-2 focus:ring-[#7B0009]/10 transition-all">
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-left text-[10px] font-black text-gray-300 uppercase tracking-widest">
                            <tr>
                                <th class="px-10 py-4">Pesanan</th>
                                <th class="px-6 py-4">Stan</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-10 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($orders as $o): ?>
                                <tr class="hover:bg-[#FAF9F9] transition-colors">
                                    <td class="px-10 py-5">
                                        <p class="font-black text-[#261817] text-sm">#<?= htmlspecialchars($o['display_order_number'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="text-[10px] text-gray-400 font-bold"><?= date('H:i', strtotime((string)$o['created_at'])) ?></p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-xs font-bold text-gray-500"><?= htmlspecialchars((string)($o['tenant_names'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="px-6 py-5 font-black text-[#261817] text-sm">
                                        <?= htmlspecialchars(Money::formatIdr((float)$o['total']), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-10 py-5 text-right">
                                        <a href="?page=orders&q=<?= urlencode($o['display_order_number']) ?>" class="text-[#7B0009] text-xs font-black uppercase tracking-widest hover:underline">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('filter-mode').addEventListener('change', function() {
    const mode = this.value;
    document.querySelectorAll('.mode-input').forEach(el => el.style.display = 'none');
    
    if (mode === 'daily') {
        document.getElementById('mode-daily').style.display = 'block';
    } else if (mode === 'monthly') {
        document.getElementById('mode-monthly-month').style.display = 'block';
    } else if (mode === 'yearly') {
        document.getElementById('mode-monthly-year').style.display = 'block';
    } else if (mode === 'custom') {
        document.getElementById('mode-custom-from').style.display = 'block';
        document.getElementById('mode-custom-to').style.display = 'block';
    }
});
</script>

<style>
@media print {
    /* Hide navigation, sidebar, and filters */
    .no-print, 
    nav, 
    aside, 
    #filter-form,
    .sidebar-container,
    header {
        display: none !important;
    }

    /* Reset background and layout for printing */
    body {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    /* Ensure colors and cards look good */
    .bg-white {
        border: 1px solid #eee !important;
        box-shadow: none !important;
    }

    /* Grid adjustment for print (ensure they stack or stay as needed) */
    .grid {
        display: block !important;
    }
    
    .grid-cols-1, .grid-cols-2, .grid-cols-3, .lg:grid-cols-3 {
        grid-template-columns: none !important;
    }

    .lg:col-span-1, .lg:col-span-2 {
        width: 100% !important;
        margin-bottom: 2rem;
    }
    
    .gap-10, .gap-8 {
        gap: 0 !important;
    }

    /* Force background colors in PDF */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
