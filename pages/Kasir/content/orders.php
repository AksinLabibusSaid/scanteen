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
$limit = 10;
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
            <h1 class="poppins text-4xl font-bold text-[var(--text-dark)] tracking-tight">Manajemen Pesanan</h1>
            <p class="text-[var(--text-muted)] text-lg mt-2 font-medium">Pantau dan verifikasi transaksi secara real-time.</p>
        </div>
        <div class="flex items-center gap-4">
            <button id="btn-input-code" class="flex items-center gap-3 px-6 py-3.5 bg-[var(--brand-muted)] text-[var(--brand)] rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-[var(--brand)] hover:text-white transition-all shadow-sm group">
                <svg class="w-5 h-5 text-[var(--brand)] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Input Kode
            </button>
        </div>
    </div>

    <!-- Unified Filter Bar -->
    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <form method="get" class="flex flex-col lg:flex-row items-center gap-8">
            <input type="hidden" name="page" value="orders">
            
            <!-- Search -->
            <div class="w-full lg:w-1/3 relative">
                <input type="text" name="q" value="<?= htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari Kode atau Pelanggan..." 
                       class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent focus:border-[var(--brand-soft)] focus:bg-white rounded-2xl outline-none transition-all text-sm font-bold placeholder:text-gray-300">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Tabs -->
            <div class="flex-1 bg-gray-50 p-1.5 rounded-2xl flex gap-1">
                <?php
                $tabs = [
                    'all' => 'Semua',
                    'pending_payment' => 'Menunggu',
                    'paid' => 'Dibayar',
                    'completed' => 'Selesai'
                ];
                $activeTab = $filters['status'] ?: 'all';
                foreach ($tabs as $key => $label):
                    $active = $activeTab === $key;
                ?>
                <button type="button" 
                        onclick="typeof scanteenLoadPage === 'function' ? scanteenLoadPage('?page=orders&status=<?= $key === 'all' ? '' : $key ?>&q=<?= urlencode($filters['q']) ?>') : window.location.href='?page=orders&status=<?= $key === 'all' ? '' : $key ?>&q=<?= urlencode($filters['q']) ?>'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?= $active ? 'bg-white text-[var(--brand)] shadow-sm' : 'text-gray-400 hover:text-gray-600' ?>">
                    <?= $label ?>
                </button>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[#FAF7F6] text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">
                        <th class="px-10 py-6">ID & Pelanggan</th>
                        <th class="px-6 py-6 text-center">Meja</th>
                        <th class="px-6 py-6">Ringkasan Menu</th>
                        <th class="px-6 py-6">Total Harga</th>
                        <th class="px-6 py-6">Status</th>
                        <th class="px-10 py-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-10 py-24 text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    </div>
                                    <p class="text-[var(--text-muted)] font-black text-[11px] uppercase tracking-widest opacity-50">Tidak ada pesanan ditemukan</p>
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
                        <tr class="group hover:bg-[#FAF7F6]/50 transition-colors">
                            <td class="px-10 py-8">
                                <span class="poppins text-lg font-bold text-[var(--brand)] group-hover:tracking-tight transition-all tracking-tighter">#<?= htmlspecialchars($o['display_order_number'], ENT_QUOTES, 'UTF-8') ?></span>
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                                    <p class="text-xs font-bold text-[var(--text-dark)] opacity-70"><?= htmlspecialchars((string) ($o['customer_name'] ?? 'Umum'), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </td>
                            <td class="px-6 py-8 text-center">
                                <span class="inline-flex px-4 py-2 rounded-xl bg-gray-50 text-[var(--text-dark)] text-[11px] font-black uppercase border border-gray-100 shadow-sm">
                                    T-<?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-6 py-8 max-w-sm">
                                <div class="flex flex-col gap-3">
                                    <?php foreach ($itemGroups as $group): ?>
                                        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm group/item">
                                            <p class="text-[9px] font-black text-[var(--brand)] uppercase tracking-widest border-b border-gray-50 pb-1.5 mb-2 opacity-60"><?= htmlspecialchars($group['warung_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-[11px] font-bold text-[var(--text-muted)] leading-relaxed">
                                                <?php
                                                $itemStrings = array_map(function($item) {
                                                    return '<span class="text-[var(--text-dark)]">' . $item['quantity'] . 'x</span> ' . $item['menu_name_snapshot'];
                                                }, $group['items']);
                                                echo implode('<span class="mx-1 opacity-20">·</span>', $itemStrings);
                                                ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-8">
                                <div class="poppins text-xl font-bold text-[var(--text-dark)] tracking-tighter">
                                    <?= number_format((float)$o['total'], 0, ',', '.') ?>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest opacity-40"><?= htmlspecialchars(strtoupper($pm), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                    <span class="text-[9px] font-bold text-[var(--text-muted)] opacity-40"><?= date('H:i', strtotime((string) $o['created_at'])) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-8">
                                <span class="px-4 py-1.5 rounded-full text-[9px] font-black border tracking-widest <?= scanteen_kasir_status_badge($st) ?> uppercase">
                                    <?= scanteen_kasir_status_label($st) ?>
                                </span>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <div class="flex justify-end items-center gap-3">
                                    <button type="button" 
                                            class="px-5 py-3 rounded-xl bg-white border border-gray-100 text-[var(--text-dark)] text-[10px] font-black uppercase tracking-widest hover:border-[var(--brand)] hover:text-[var(--brand)] hover:shadow-sm transition-all btn-view-detail"
                                            data-id="<?= (int) $o['id'] ?>">
                                        Detail
                                    </button>

                                    <?php if ($st === 'pending_payment'): ?>
                                        <button type="button"
                                                class="px-6 py-3 rounded-xl bg-[var(--success-green)] text-white text-[10px] font-black uppercase tracking-widest shadow-xl shadow-emerald-900/10 hover:opacity-90 transition-all btn-mark-paid"
                                                data-token="<?= htmlspecialchars((string) $o['public_token'], ENT_QUOTES, 'UTF-8') ?>">
                                            LUNASKAN
                                        </button>
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 opacity-40">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination UI -->
        <div class="px-10 py-8 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center bg-[#FAF7F6] gap-6">
            <?php 
            $from = ($totalOrders > 0) ? $offset + 1 : 0;
            $to = min($offset + $limit, $totalOrders);
            ?>
            <div>
                <p class="text-[10px] text-[var(--text-muted)] font-black uppercase tracking-widest opacity-60">
                    Menampilkan <span class="text-[var(--text-dark)]"><?= $from ?> - <?= $to ?></span> 
                    <span class="mx-1 opacity-20">/</span> 
                    Total <span class="text-[var(--text-dark)]"><?= $totalOrders ?></span> pesanan
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Prev Button -->
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-[var(--text-muted)] hover:text-[var(--brand)] hover:border-[var(--brand-soft)] transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                <?php else: ?>
                    <span class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-50 text-gray-200 cursor-not-allowed opacity-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                <?php endif; ?>

                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-2xl border border-gray-50 shadow-sm">
                    <?php 
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    for ($i = (int)$startPage; $i <= (int)$endPage; $i++): 
                        $isActive = ($i === $page);
                    ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" 
                           class="w-9 h-9 rounded-xl flex items-center justify-center text-[10px] font-black transition-all <?= $isActive ? 'bg-[var(--brand)] text-white shadow-md' : 'text-[var(--text-muted)] hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Next Button -->
                <?php if ($page < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-[var(--text-muted)] hover:text-[var(--brand)] hover:border-[var(--brand-soft)] transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                    </a>
                <?php else: ?>
                    <span class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-50 text-gray-200 cursor-not-allowed opacity-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div id="modal-order-detail" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#261817]/60 backdrop-blur-md transition-opacity opacity-0" id="modal-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-6xl max-h-[90vh] rounded-[40px] shadow-2xl overflow-hidden flex flex-col md:flex-row transform scale-95 opacity-0 transition-all duration-500" id="modal-container">
            
            <!-- Left Side: Order Details (Scrollable) -->
            <div class="flex-1 p-12 overflow-y-auto custom-scrollbar border-r border-gray-50 bg-[#FAF7F6]">
                <div class="flex justify-between items-start mb-12">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-[var(--brand)] rounded-2xl flex items-center justify-center shadow-xl shadow-red-900/10">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h2 class="poppins text-2xl font-bold text-[var(--text-dark)] leading-tight">Detail Pesanan</h2>
                            <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mt-2 opacity-50" id="detail-order-number">#ORD-...</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm group">
                        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-6 opacity-40">Informasi Pelanggan</p>
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 bg-[var(--brand-muted)] rounded-2xl flex items-center justify-center text-[var(--brand)] font-black text-lg border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h4 class="poppins text-xl font-bold text-[var(--text-dark)]" id="detail-customer-name">...</h4>
                                <p class="text-[11px] text-[var(--text-muted)] font-bold mt-1.5 opacity-60" id="detail-customer-email">...</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm">
                        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-6 opacity-40">Lokasi & Tipe Makan</p>
                        <div class="flex gap-4">
                            <span class="inline-flex items-center gap-2 px-5 py-3 bg-[var(--success-bg)] text-[var(--success-green)] rounded-2xl text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                                <span class="w-2 h-2 rounded-full bg-[var(--success-green)] animate-pulse"></span>
                                Dine In
                            </span>
                            <span class="inline-flex items-center gap-2 px-5 py-3 bg-gray-50 text-[var(--text-dark)] rounded-2xl text-[10px] font-black uppercase tracking-widest border border-gray-100">
                                <span id="detail-table-number">Table #...</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-4 mb-10">
                        <h5 class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] opacity-40">Rincian Item Pesanan</h5>
                        <div class="h-px flex-1 bg-gray-100 opacity-50"></div>
                    </div>

                    <div id="detail-items-container" class="space-y-8">
                        <!-- Items populated by JS -->
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary & Actions -->
            <div class="w-full md:w-[480px] p-12 bg-white flex flex-col relative shadow-[-40px_0_80px_rgba(0,0,0,0.03)]">
                <button id="btn-close-modal" class="absolute top-10 right-10 w-12 h-12 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center transition-all group border border-gray-100">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="mb-12 pt-8">
                    <div class="flex items-center gap-4 mb-12">
                        <div class="w-1.5 h-6 bg-[var(--brand)] rounded-full"></div>
                        <h4 class="poppins text-xl font-bold text-[var(--brand)]">Rincian Pembayaran</h4>
                    </div>

                    <div class="space-y-8">
                        <div class="flex justify-between items-center pb-8 border-b border-gray-50">
                            <span class="text-[11px] font-black text-[var(--text-muted)] uppercase tracking-widest opacity-60">Jumlah Subtotal</span>
                            <span class="text-lg font-bold text-[var(--text-dark)] poppins" id="detail-subtotal">Rp 0</span>
                        </div>
                        
                        <div class="pt-2">
                            <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-4 opacity-40">Total Akhir yang Harus Dibayar</p>
                            <div class="flex flex-col gap-6">
                                <h3 class="poppins text-5xl font-bold text-[var(--text-dark)] tracking-tighter" id="detail-total-amount">Rp 0</h3>
                                <div class="inline-flex">
                                    <span class="px-6 py-2 bg-[var(--brand-muted)] text-[var(--brand)] text-[11px] font-black rounded-full uppercase tracking-[0.2em] border border-[var(--brand-soft)]" id="detail-payment-status">PENDING</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 mt-auto">
                    <button id="modal-btn-confirm" class="w-full py-8 bg-[var(--success-green)] text-white rounded-[24px] font-black shadow-2xl shadow-emerald-900/20 hover:scale-[1.02] active:scale-95 transition-all flex flex-col items-center justify-center leading-tight">
                        <div class="flex items-center gap-3 mb-2 text-sm tracking-[0.2em]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            KONFIRMASI LUNAS
                        </div>
                        <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest">Verifikasi Penerimaan Dana Kasir</span>
                    </button>
                    <button id="modal-btn-print" class="w-full py-6 bg-white border-2 border-gray-50 text-[var(--text-dark)] rounded-[24px] font-black text-[11px] uppercase tracking-[0.2em] hover:border-[var(--brand)] hover:text-[var(--brand)] hover:bg-[#FAF7F6] transition-all flex items-center justify-center gap-4">
                        <svg class="w-6 h-6 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak Struk Transaksi
                    </button>
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

  btnClose.onclick = hideModal;
  modalBackdrop.onclick = hideModal;

  // View Detail
  document.querySelectorAll('.btn-view-detail').forEach(btn => {
    btn.onclick = async () => {
      const id = btn.getAttribute('data-id');
      if (!id) return;

      const res = await fetch(apiOrder, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'detail', order_id: parseInt(id) }),
      });
      const data = await res.json();
      if (!data.ok) return alert(data.error || 'Gagal');

      const o = data.order;
      document.getElementById('detail-order-number').textContent = '#' + o.display_number;
      document.getElementById('detail-customer-name').textContent = o.customer_name || 'Pelanggan Umum';
      document.getElementById('detail-customer-email').textContent = o.customer_email || '-';
      document.getElementById('detail-table-number').textContent = 'Meja #' + o.table_number;
      document.getElementById('detail-subtotal').textContent = formatMoney(o.subtotal);
      document.getElementById('detail-total-amount').textContent = formatMoney(o.total);
      
      const statusLabel = document.getElementById('detail-payment-status');
      statusLabel.textContent = o.status.toUpperCase().replace('_', ' ');
      
      const btnConfirm = document.getElementById('modal-btn-confirm');
      const btnPrint = document.getElementById('modal-btn-print');
      
      btnPrint.onclick = () => window.open('?page=struk&id=' + o.id, '_blank');

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
        groupEl.className = 'bg-white rounded-[32px] border border-gray-50 overflow-hidden shadow-sm hover:shadow-md transition-all group/card';
        
        let itemsHtml = '';
        group.items.forEach(item => {
          itemsHtml += `
            <div class="flex items-center gap-6 p-8 border-t border-gray-50 group/item hover:bg-[#FAF7F6]/30 transition-colors">
              <div class="w-20 h-20 bg-gray-50 rounded-2xl overflow-hidden flex-shrink-0 border-2 border-white shadow-sm group-hover/item:scale-105 transition-transform">
                ${item.image_url ? `<img src="${item.image_url}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center text-gray-200 bg-gray-50"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>`}
              </div>
              <div class="flex-1">
                <h6 class="text-base font-bold text-[var(--text-dark)] leading-tight"><span class="text-[var(--brand)] font-black mr-2">${item.quantity}x</span> ${item.menu_name_snapshot}</h6>
                ${item.note ? `<p class="text-[11px] text-[var(--brand)] font-bold mt-2 italic flex items-center gap-2 bg-[var(--brand-muted)] px-3 py-1 rounded-lg w-fit"><span class="w-1 h-1 bg-[var(--brand)] rounded-full animate-pulse"></span>${item.note}</p>` : ''}
              </div>
              <div class="text-right">
                <p class="text-base font-black text-[var(--text-dark)] poppins tracking-tighter">${formatMoney(item.line_subtotal)}</p>
                <p class="text-[9px] text-[var(--text-muted)] font-black mt-1 opacity-30 uppercase tracking-[0.2em]">${formatMoney(item.unit_price)} / PORSI</p>
              </div>
            </div>
          `;
        });

        groupEl.innerHTML = `
          <div class="bg-[#FAF7F6]/50 px-10 py-6 flex justify-between items-center border-b border-gray-50">
            <div class="flex items-center gap-4">
              <div class="w-2 h-6 bg-[var(--brand)] rounded-full opacity-20 group-hover/card:opacity-100 transition-opacity"></div>
              <span class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-[0.2em] opacity-80">${group.warung_name}</span>
            </div>
            <span class="px-4 py-1.5 bg-white border border-gray-100 rounded-full text-[9px] font-black text-[var(--text-muted)] tracking-[0.2em] uppercase opacity-50">SIAP</span>
          </div>
          <div class="divide-y divide-gray-50 bg-white">
            ${itemsHtml}
          </div>
        `;
        container.appendChild(groupEl);
      });

      showModal();
    };
  });

  async function confirmPayment(token) {
    if (!confirm('Lanjutkan untuk melunasi pembayaran pesanan ini?')) return;
    const res = await fetch(apiOrder, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'mark-paid', public_token: token }),
    });
    const data = await res.json();
    if (data.ok) location.reload();
    else alert(data.error || 'Gagal');
  }

  document.querySelectorAll('.btn-mark-paid').forEach(btn => {
    btn.onclick = () => confirmPayment(btn.getAttribute('data-token'));
  });
})();
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: var(--brand-soft); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--brand); }
</style>

<!-- Modal Input Kode -->
<div id="modal-input-code" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#261817]/40 backdrop-blur-sm transition-opacity opacity-0" id="input-modal-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modal-input-content">
            <div class="p-12">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="poppins text-3xl font-bold text-[var(--text-dark)] tracking-tighter">Verifikasi Kode</h3>
                    <button class="w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 transition-colors btn-close-input">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <p class="text-[var(--text-muted)] mb-10 font-medium text-base leading-relaxed opacity-70">Masukkan kode pesanan unik untuk melacak status pembayaran dan rincian item pelanggan.</p>
                
                <div class="relative mb-12">
                    <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                        <span class="text-xl font-black text-[var(--brand)] opacity-20 poppins">#</span>
                    </div>
                    <input type="text" id="input-order-code" placeholder="ORD-XXXX-XXX" 
                           class="w-full pl-14 pr-8 py-6 bg-gray-50 border-2 border-transparent focus:border-[var(--brand)] focus:bg-white rounded-[24px] outline-none transition-all font-bold text-2xl tracking-[0.2em] uppercase poppins text-[var(--brand)] shadow-inner">
                </div>

                <div class="flex gap-5">
                    <button class="flex-1 py-5 bg-gray-50 text-[var(--text-muted)] rounded-[20px] font-black text-[11px] uppercase tracking-widest hover:bg-gray-100 transition-all btn-close-input">Batal</button>
                    <button id="btn-submit-code" class="flex-1 py-5 bg-[var(--brand)] text-white rounded-[20px] font-black text-[11px] uppercase tracking-widest shadow-2xl shadow-red-900/20 hover:scale-[1.02] transition-all">Lacak Pesanan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const btnInput = document.getElementById('btn-input-code');
    const modal = document.getElementById('modal-input-code');
    const backdrop = document.getElementById('input-modal-backdrop');
    const content = document.getElementById('modal-input-content');
    const input = document.getElementById('input-order-code');
    const btnSubmit = document.getElementById('btn-submit-code');
    const closeBtns = document.querySelectorAll('.btn-close-input');

    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.replace('opacity-0', 'opacity-100');
            content.classList.replace('scale-95', 'scale-100');
            content.classList.replace('opacity-0', 'opacity-100');
            input.focus();
        }, 10);
    }

    function closeModal() {
        backdrop.classList.replace('opacity-100', 'opacity-0');
        content.classList.replace('scale-100', 'scale-95');
        content.classList.replace('opacity-100', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            input.value = '';
        }, 300);
    }

    btnInput.onclick = openModal;
    closeBtns.forEach(btn => btn.onclick = closeModal);
    backdrop.onclick = closeModal;

    function handleSubmit() {
        const code = input.value.trim();
        if (code) window.location.href = '?page=orders&q=' + encodeURIComponent(code);
    }

    input.oninput = (e) => {
        let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (val.startsWith('ORD')) val = val.substring(3);
        let formatted = 'ORD-';
        if (val.length > 0) formatted += val.substring(0, 4);
        if (val.length > 4) formatted += '-' + val.substring(4, 7);
        e.target.value = formatted;
    };

    btnSubmit.onclick = handleSubmit;
    input.onkeypress = (e) => { if (e.key === 'Enter') handleSubmit(); };
})();
</script>
