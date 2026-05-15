<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\OrderRepository;
use App\Repositories\VenueStatsRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$venueId = (int) StaffAuth::venueId();
$orderListRepo = new OrderListRepository();
$orderRepo = new OrderRepository();
$statsRepo = new VenueStatsRepository();
$warungRepo = new WarungRepository();

// Filters
$filters = [
    'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : '',
    'status' => isset($_GET['status']) ? trim((string) $_GET['status']) : '',
    'date' => isset($_GET['date']) ? trim((string) $_GET['date']) : '',
    'payment' => isset($_GET['payment']) ? trim((string) $_GET['payment']) : '',
    'warung' => isset($_GET['warung']) ? (int) $_GET['warung'] : 0,
];

// Pagination settings
$limit = 10; // Set a small limit for testing pagination
$page = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
$offset = ($page - 1) * $limit;

$totalOrders = $orderListRepo->countForVenueFiltered(
    $venueId,
    $filters['status'] !== '' && $filters['status'] !== 'all' ? $filters['status'] : null,
    $filters['date'] !== '' ? $filters['date'] : null,
    null, // dateTo
    $filters['payment'] !== '' ? $filters['payment'] : null,
    $filters['warung'] > 0 ? $filters['warung'] : null,
    $filters['q'] !== '' ? $filters['q'] : null
);

$totalPages = ceil($totalOrders / $limit);

$orders = $orderListRepo->listForVenueFiltered(
    $venueId, 
    $limit, 
    $filters['status'] !== '' && $filters['status'] !== 'all' ? $filters['status'] : null,
    $filters['date'] !== '' ? $filters['date'] : null,
    null, // dateTo
    $filters['payment'] !== '' ? $filters['payment'] : null,
    $filters['warung'] > 0 ? $filters['warung'] : null,
    $filters['q'] !== '' ? $filters['q'] : null,
    $offset
);
$warungs = $warungRepo->listByVenueId($venueId);
$apiOrder = PublicUrl::basePath() . '/api/staff/order.php';

$kpis = $statsRepo->dashboardKpis($venueId);

function scanteen_kasir_status_badge(string $s): string
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

function scanteen_kasir_status_label(string $s): string
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
            <h1 class="text-4xl font-bold text-[#261817] tracking-tight">Manajemen Pesanan</h1>
            <p class="text-gray-500 text-lg mt-2 font-medium">Pantau semua transaksi kantin secara real-time.</p>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <button id="btn-input-code" class="flex items-center gap-2 px-6 py-3 border-2 border-[#7B0009] rounded-xl text-[#7B0009] font-bold hover:bg-[#7B0009] hover:text-white transition-all duration-300 group">
                <svg class="w-5 h-5 text-[#7B0009] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Input Kode Pesanan
            </button>
            
            <div class="bg-white p-1 rounded-xl border border-gray-100 shadow-sm flex gap-1">
                <?php
                $tabs = [
                    'all' => 'Semua',
                    'pending_payment' => 'Menunggu',
                    'paid' => 'Dibayar',
                    'completed' => 'Selesai',
                    'cancelled' => 'Batal'
                ];
                $activeTab = $_GET['status'] ?? 'all';
                foreach ($tabs as $key => $label):
                    $active = $activeTab === $key;
                    $queryParams = $_GET;
                    if ($key === 'all') {
                        unset($queryParams['status']);
                    } else {
                        $queryParams['status'] = $key;
                    }
                    $queryParams['p'] = 1; // Reset page on tab change
                    $url = '?' . http_build_query($queryParams);
                ?>
                <a href="<?= $url ?>" 
                   class="px-5 py-2 rounded-lg text-sm font-bold transition-all <?= $active ? 'bg-[#7B0009] text-white shadow-md' : 'text-gray-400 hover:text-gray-600' ?>">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[#FFF5F5] border-b border-gray-50 text-left text-[13px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-8 py-6">ID Pesanan</th>
                        <th class="px-6 py-6 text-center">No. Meja</th>
                        <th class="px-6 py-6">Menu Pesanan</th>
                        <th class="px-6 py-6">Total Harga</th>
                        <th class="px-6 py-6">Status</th>
                        <th class="px-8 py-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    </div>
                                    <p class="text-gray-400 font-medium">Belum ada pesanan.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($orders as $o): ?>
                        <?php
                        $st = (string) $o['status'];
                        $pm = (string) ($o['payment_method'] ?? '');
                        $itemGroups = $orderRepo->groupItemsByWarung((int) $o['id']);
                        ?>
                        <tr class="group hover:bg-[#FFFAFA] transition-colors">
                            <td class="px-8 py-8">
                                <span class="text-lg font-bold text-[#261817] group-hover:text-[#7B0009] transition-colors">#<?= htmlspecialchars($o['display_order_number'], ENT_QUOTES, 'UTF-8') ?></span>
                                <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase"><?= htmlspecialchars((string) ($o['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                            </td>
                            <td class="px-6 py-8 text-center">
                                <span class="inline-flex px-4 py-1.5 rounded-full bg-gray-100 text-gray-500 text-xs font-black">
                                    T-<?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-6 py-8 max-w-xs">
                                <div class="flex flex-col gap-1.5">
                                    <?php foreach ($itemGroups as $group): ?>
                                        <div class="text-[13px] leading-relaxed">
                                            <span class="font-bold text-[#7B0009]"><?= htmlspecialchars($group['warung_name'], ENT_QUOTES, 'UTF-8') ?>:</span>
                                            <span class="text-gray-600">
                                                <?php
                                                $itemStrings = array_map(function($item) {
                                                    return $item['menu_name_snapshot'] . ' (' . $item['quantity'] . ')';
                                                }, $group['items']);
                                                echo htmlspecialchars(implode(', ', $itemStrings), ENT_QUOTES, 'UTF-8');
                                                ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-8">
                                <div class="text-lg font-black text-[#261817]">
                                    <span class="text-sm font-bold text-gray-400 mr-1">Rp</span>
                                    <?= number_format((float)$o['total'], 0, ',', '.') ?>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest"><?= htmlspecialchars(strtoupper($pm), ENT_QUOTES, 'UTF-8') ?></p>
                            </td>
                            <td class="px-6 py-8">
                                <span class="px-4 py-1.5 rounded-full text-[11px] font-black border tracking-wider <?= scanteen_kasir_status_badge($st) ?>">
                                    <?= scanteen_kasir_status_label($st) ?>
                                </span>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div class="flex justify-end items-center gap-3">
                                    <button type="button" 
                                            class="px-5 py-2.5 rounded-xl bg-white border border-[#7B0009]/20 text-[#7B0009] text-xs font-bold hover:bg-[#7B0009]/5 transition-all btn-view-detail"
                                            data-id="<?= (int) $o['id'] ?>">
                                        Lihat Detail
                                    </button>

                                    <?php if ($st === 'pending_payment'): ?>
                                        <button type="button"
                                                class="px-5 py-2.5 rounded-xl bg-[#00A86B] text-white text-xs font-bold shadow-lg shadow-emerald-100 hover:bg-[#008F5B] transition-all btn-mark-paid"
                                                data-token="<?= htmlspecialchars((string) $o['public_token'], ENT_QUOTES, 'UTF-8') ?>">
                                            Konfirmasi Bayar
                                        </button>
                                    <?php elseif ($st === 'ready'): ?>
                                        <span class="text-emerald-500 text-xs font-bold italic pr-4 uppercase tracking-widest">Siap Diantarkan</span>
                                    <?php elseif (in_array($st, ['paid', 'accepted', 'processing'])): ?>
                                        <span class="text-gray-400 text-xs font-bold italic pr-4">Diproses...</span>
                                    <?php elseif ($st === 'completed'): ?>
                                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center border border-emerald-100">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    <?php elseif ($st === 'cancelled'): ?>
                                        <span class="text-gray-300 text-xs italic font-bold pr-4">Dibatalkan</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination UI -->
        <div class="px-8 py-6 border-t border-gray-50 flex justify-between items-center bg-[#FAF9F9]">
            <?php 
            $from = ($totalOrders > 0) ? $offset + 1 : 0;
            $to = min($offset + $limit, $totalOrders);
            ?>
            <p class="text-sm text-gray-400 font-medium">Menampilkan <span class="text-gray-900 font-bold"><?= $from ?> - <?= $to ?></span> dari <span class="text-gray-900 font-bold"><?= $totalOrders ?></span> pesanan</p>
            
            <div class="flex items-center gap-2">
                <!-- Prev Button -->
                <?php if ($page > 1): ?>
                    <?php 
                    $prevParams = $_GET;
                    $prevParams['p'] = $page - 1;
                    ?>
                    <a href="?<?= http_build_query($prevParams) ?>" class="p-2 text-gray-500 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                <?php else: ?>
                    <span class="p-2 text-gray-200 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                <?php endif; ?>

                <!-- Page numbers -->
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                if ($startPage > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['p' => 1])) ?>" class="w-10 h-10 rounded-xl text-gray-500 font-bold hover:bg-gray-100 flex items-center justify-center transition-all">1</a>
                    <?php if ($startPage > 2): ?><span class="text-gray-300 px-1">...</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($i = (int)$startPage; $i <= (int)$endPage; $i++): ?>
                    <?php 
                    $pParams = $_GET;
                    $pParams['p'] = $i;
                    $isActive = ($i === $page);
                    ?>
                    <a href="?<?= http_build_query($pParams) ?>" 
                       class="w-10 h-10 rounded-xl flex items-center justify-center font-bold transition-all <?= $isActive ? 'bg-[#7B0009] text-white shadow-lg shadow-red-100' : 'text-gray-500 hover:bg-gray-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?><span class="text-gray-300 px-1">...</span><?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['p' => $totalPages])) ?>" class="w-10 h-10 rounded-xl text-gray-500 font-bold hover:bg-gray-100 flex items-center justify-center transition-all"><?= $totalPages ?></a>
                <?php endif; ?>

                <!-- Next Button -->
                <?php if ($page < $totalPages): ?>
                    <?php 
                    $nextParams = $_GET;
                    $nextParams['p'] = $page + 1;
                    ?>
                    <a href="?<?= http_build_query($nextParams) ?>" class="p-2 text-gray-500 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                <?php else: ?>
                    <span class="p-2 text-gray-200 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/30 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#00C853] group-hover:w-3 transition-all"></div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Pesanan Berhasil</p>
            <div class="flex items-baseline gap-3 mt-2">
                <h3 class="text-4xl font-black text-[#261817]"><?= $kpis['today_orders'] ?></h3>
                <span class="text-emerald-500 text-sm font-bold flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    +12%
                </span>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/30 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#FF9100] group-hover:w-3 transition-all"></div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Antrean Aktif</p>
            <div class="flex items-baseline gap-3 mt-2">
                <h3 class="text-4xl font-black text-[#261817]"><?= $kpis['pending_payment'] ?></h3>
                <span class="text-gray-400 text-sm font-bold uppercase tracking-wide">pesanan</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/30 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#7B0009] group-hover:w-3 transition-all"></div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Pendapatan</p>
            <div class="flex items-baseline gap-2 mt-2">
                <h3 class="text-4xl font-black text-[#261817]">Rp <?= number_format($kpis['today_revenue'] / 1000000, 1) ?>Jt</h3>
                <span class="text-gray-400 text-sm font-bold uppercase tracking-wide">hari ini</span>
            </div>
        </div>

        <div class="bg-[#7B0009] p-8 rounded-[2rem] shadow-2xl shadow-red-900/20 text-white relative overflow-hidden">
            <div class="absolute right-4 top-4 bg-red-400/20 border border-white/20 px-3 py-1 rounded-full flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Live</span>
            </div>
            <p class="text-red-300/80 text-[10px] font-bold uppercase tracking-[0.2em]">Status Shift</p>
            <h3 class="text-2xl font-black mt-2 leading-tight">Shift Pagi</h3>
            <p class="text-red-200/60 text-xs font-bold mt-1 tracking-widest">07:00 - 15:00</p>
        </div>
    </div>
</div>

</div>

<!-- Order Detail Modal -->
<div id="modal-order-detail" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#261817]/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 md:p-8">
        <div class="bg-[#FAF9F9] w-full max-w-6xl h-[90vh] rounded-[3rem] shadow-2xl overflow-hidden flex flex-col md:flex-row transform scale-95 opacity-0 transition-all duration-300" id="modal-container">
            
            <!-- Left Side: Order Details (Scrollable) -->
            <div class="flex-1 p-10 overflow-y-auto custom-scrollbar border-r border-gray-100">
                <div class="flex justify-between items-start mb-10">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-[#7B0009] rounded-2xl flex items-center justify-center shadow-lg shadow-red-900/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-[#261817] leading-tight">Smart Kantin</h2>
                            <span class="inline-block mt-1 px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-400 uppercase tracking-widest">Payment Gateway</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kode Pesanan</p>
                        <h3 class="text-2xl font-black text-[#7B0009]" id="detail-order-number">#ORD-...</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Verifikasi Pelanggan</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-[#FFF5F5] rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#7B0009]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-[#261817]" id="detail-customer-name">...</h4>
                                <p class="text-xs text-gray-400 font-medium" id="detail-customer-email">...</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Opsi Makan</p>
                        <div class="flex gap-3">
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-full text-xs font-black">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Dine In
                            </span>
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-full text-xs font-black border border-red-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span id="detail-table-number">Table #...</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-px flex-1 bg-gray-100"></div>
                        <h5 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Rincian Pesanan</h5>
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </div>

                    <div id="detail-items-container" class="space-y-6">
                        <!-- Items populated by JS -->
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary & Actions -->
            <div class="w-full md:w-[400px] p-10 bg-white flex flex-col relative">
                <button id="btn-close-modal" class="absolute top-8 right-8 w-10 h-10 rounded-full hover:bg-gray-50 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-8">
                        <svg class="w-5 h-5 text-[#7B0009]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <h4 class="text-xs font-black text-[#7B0009] uppercase tracking-widest">Ringkasan Pesanan</h4>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-6 border-b border-gray-100">
                            <span class="text-gray-400 text-sm font-medium">Subtotal</span>
                            <span class="text-[#261817] font-bold" id="detail-subtotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Pembayaran</p>
                        <div class="flex justify-between items-end">
                            <h3 class="text-4xl font-black text-[#261817]" id="detail-total-amount">Rp 0</h3>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Bayar</p>
                                <span class="text-[11px] font-black text-[#7B0009] uppercase tracking-wider" id="detail-payment-status">PENDING</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mb-10">
                    <button id="modal-btn-confirm" class="w-full py-5 bg-[#00A86B] text-white rounded-2xl font-black shadow-xl shadow-emerald-900/10 hover:bg-[#008F5B] transition-all flex flex-col items-center justify-center leading-tight">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            KONFIRMASI BAYAR
                        </div>
                        <span class="text-[10px] font-medium opacity-80">Konfirmasi pesanan & kirim nota ke email</span>
                    </button>
                    <button id="modal-btn-print" class="w-full py-5 bg-white border border-gray-100 text-[#261817] rounded-2xl font-black shadow-sm hover:bg-gray-50 transition-all flex items-center justify-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        CETAK NOTA FISIK
                    </button>
                </div>

                <div class="mt-auto">
                    <div class="mt-8 flex justify-center opacity-30">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-[#7B0009] rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <span class="text-[9px] font-black text-gray-900 tracking-widest">Smart Kantin POS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  const apiOrder = <?= json_encode($apiOrder, JSON_THROW_ON_ERROR) ?>;
  const modal = document.getElementById('modal-order-detail');
  const modalBackdrop = document.getElementById('modal-backdrop');
  const modalContainer = document.getElementById('modal-container');
  const btnClose = document.getElementById('btn-close-modal');
  
  function formatMoney(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
  }

  function showModal() {
    modal.classList.remove('hidden');
    setTimeout(() => {
      modalBackdrop.classList.replace('opacity-0', 'opacity-100');
      modalContainer.classList.replace('scale-95', 'scale-100');
      modalContainer.classList.replace('opacity-0', 'opacity-100');
    }, 10);
  }

  function hideModal() {
    modalBackdrop.classList.replace('opacity-100', 'opacity-0');
    modalContainer.classList.replace('scale-100', 'scale-95');
    modalContainer.classList.replace('opacity-100', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 300);
  }

  btnClose.addEventListener('click', hideModal);
  modalBackdrop.addEventListener('click', hideModal);

  // View Detail
  document.querySelectorAll('.btn-view-detail').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      if (!id) return;

      const res = await fetch(apiOrder, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'detail', order_id: parseInt(id) }),
      });
      const data = await res.json();
      if (!data.ok) {
        alert(data.error || 'Gagal mengambil detail');
        return;
      }

      const o = data.order;
      document.getElementById('detail-order-number').textContent = o.display_number;
      document.getElementById('detail-customer-name').textContent = o.customer_name || 'Pelanggan Umum';
      document.getElementById('detail-customer-email').textContent = o.customer_email || 'no-email@scanteen.local';
      document.getElementById('detail-table-number').textContent = 'Meja #' + o.table_number;
      document.getElementById('detail-subtotal').textContent = formatMoney(o.subtotal);
      document.getElementById('detail-total-amount').textContent = formatMoney(o.total);
      
      const statusLabel = document.getElementById('detail-payment-status');
      statusLabel.textContent = o.status.toUpperCase().replace('_', ' ');
      
      const btnConfirm = document.getElementById('modal-btn-confirm');
      const btnPrint = document.getElementById('modal-btn-print');
      
      btnPrint.onclick = () => {
        window.open('?page=struk&id=' + o.id, '_blank');
      };

      if (o.status === 'pending_payment') {
        btnConfirm.classList.remove('hidden');
        btnConfirm.onclick = () => confirmPayment(o.public_token);
      } else {
        btnConfirm.classList.add('hidden');
      }

      // Populate Items
      const container = document.getElementById('detail-items-container');
      container.innerHTML = '';
      data.groups.forEach(group => {
        const groupEl = document.createElement('div');
        groupEl.className = 'bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm';
        
        let itemsHtml = '';
        group.items.forEach(item => {
          itemsHtml += `
            <div class="flex items-center gap-4 p-6 border-t border-gray-50">
              <div class="w-20 h-20 bg-gray-50 rounded-2xl overflow-hidden flex-shrink-0">
                ${item.image_url ? `<img src="${item.image_url}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center text-gray-300"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>`}
              </div>
              <div class="flex-1">
                <h6 class="text-sm font-black text-[#261817]">${item.quantity}x ${item.menu_name_snapshot}</h6>
                <p class="text-[11px] text-gray-400 font-bold mt-1">Catatan: ${item.note || 'Tidak ada catatan'}</p>
              </div>
              <div class="text-right">
                <p class="text-sm font-black text-[#261817]">${formatMoney(item.line_subtotal)}</p>
                <p class="text-[10px] text-gray-400 font-bold mt-0.5">${formatMoney(item.unit_price)} / porsi</p>
              </div>
            </div>
          `;
        });

        groupEl.innerHTML = `
          <div class="bg-[#FAF9F9] px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-[#7B0009]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span class="text-[11px] font-black text-gray-900 uppercase tracking-widest">${group.warung_name}</span>
            </div>
            <span class="px-3 py-1 bg-gray-100 rounded-full text-[9px] font-black text-gray-400 tracking-widest uppercase">${group.fulfillment_status}</span>
          </div>
          <div class="divide-y divide-gray-50">
            ${itemsHtml}
          </div>
        `;
        container.appendChild(groupEl);
      });

      showModal();
    });
  });

  async function confirmPayment(token) {
    if (!confirm('Konfirmasi pembayaran untuk pesanan ini?')) return;
    const res = await fetch(apiOrder, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'mark-paid', public_token: token }),
    });
    const data = await res.json();
    if (data.ok) location.reload();
    else alert(data.error || 'Gagal');
  }

  // Confirm Payment (Table Buttons)
  document.querySelectorAll('.btn-mark-paid').forEach(btn => {
    btn.addEventListener('click', () => confirmPayment(btn.getAttribute('data-token')));
  });


})();
</script>
<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
</style>
<!-- Modal Input Kode -->
<div id="modal-input-code" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#261817]/40 backdrop-blur-sm transition-opacity"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modal-input-content">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-[#261817]">Input Kode Pesanan</h3>
                    <button class="text-gray-400 hover:text-gray-600 transition-colors btn-close-input">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <p class="text-gray-500 mb-6 font-medium">Masukkan kode pesanan (contoh: ORD-0515-001) untuk mencari pesanan.</p>
                
                <div class="relative mb-6">
                    <input type="text" id="input-order-code" placeholder="ORD-XXXX-XXX" 
                           class="w-full pl-6 pr-6 py-4 bg-gray-50 border-2 border-transparent focus:border-[#7B0009] focus:bg-white rounded-2xl outline-none transition-all font-bold text-lg tracking-wider uppercase">
                </div>

                <div class="flex gap-3">
                    <button class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition-all btn-close-input">Batal</button>
                    <button id="btn-submit-code" class="flex-1 py-4 bg-[#7B0009] text-white rounded-2xl font-bold shadow-lg shadow-red-100 hover:opacity-90 transition-all">Cari Pesanan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnInput = document.getElementById('btn-input-code');
    const modal = document.getElementById('modal-input-code');
    const modalContent = document.getElementById('modal-input-content');
    const input = document.getElementById('input-order-code');
    const btnSubmit = document.getElementById('btn-submit-code');
    const closeBtns = document.querySelectorAll('.btn-close-input');

    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
            input.focus();
        }, 10);
    }

    function closeModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            input.value = '';
        }, 300);
    }

    btnInput.addEventListener('click', openModal);
    closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

    modal.addEventListener('click', (e) => {
        if (e.target === modal.children[0] || e.target === modal.children[1]) closeModal();
    });

    function handleSubmit() {
        const code = input.value.trim();
        if (code) {
            window.location.href = '?page=orders&q=' + encodeURIComponent(code);
        }
    }

    // Auto-formatting logic for ORD-XXXX-XXX
    input.addEventListener('input', function(e) {
        let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        
        // Remove 'ORD' if it's at the start to re-process cleanly
        if (val.startsWith('ORD')) {
            val = val.substring(3);
        }
        
        let formatted = 'ORD-';
        
        // Date part (4 digits)
        if (val.length > 0) {
            formatted += val.substring(0, 4);
        }
        
        // Sequence part (3 digits)
        if (val.length > 4) {
            formatted += '-' + val.substring(4, 7);
        }
        
        e.target.value = formatted;
        
        // Maintain cursor position logic can be complex, 
        // but for this fixed prefix mask, simple assignment usually works fine.
    });

    btnSubmit.addEventListener('click', handleSubmit);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') handleSubmit();
    });
});
</script>
