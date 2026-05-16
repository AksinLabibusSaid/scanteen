<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWriteRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$venueId = (int) StaffAuth::venueId();
$orderListRepo = new OrderListRepository();
$warungRepo = new WarungRepository();

function scanteen_admin_order_status_label(string $status): string
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

function scanteen_admin_order_status_class(string $status): string
{
    return match ($status) {
        'pending_payment' => 'bg-gray-100 text-gray-500',
        'cancelled' => 'bg-red-50 text-[var(--error-red)]',
        'completed' => 'bg-[var(--success-bg)] text-[var(--success-green)]',
        'paid' => 'bg-blue-50 text-blue-600',
        default => 'bg-[var(--brand-muted)] text-[var(--brand)]',
    };
}

function scanteen_admin_payment_label(string $pm): string
{
    return match ($pm) {
        'qris' => 'QRIS',
        'cashier' => 'Kasir',
        'midtrans' => 'Midtrans',
        default => ucfirst($pm),
    };
}

$filters = [
    'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : '',
    'status' => isset($_GET['status']) ? trim((string) $_GET['status']) : '',
    'date' => isset($_GET['date']) ? trim((string) $_GET['date']) : '',
    'payment' => isset($_GET['payment']) ? trim((string) $_GET['payment']) : '',
    'warung' => isset($_GET['warung']) ? (int) $_GET['warung'] : 0,
];

$orders = $orderListRepo->listForVenueFiltered(
    $venueId,
    100,
    $filters['status'] !== '' ? $filters['status'] : null,
    $filters['date'] !== '' ? $filters['date'] : null,
    null, // dateTo
    $filters['payment'] !== '' ? $filters['payment'] : null,
    $filters['warung'] > 0 ? $filters['warung'] : null,
    $filters['q'] !== '' ? $filters['q'] : null
);

$warungs = $warungRepo->listByVenueId($venueId);
$stats = (new \App\Repositories\VenueStatsRepository())->dashboardKpis($venueId);
$apiBase = PublicUrl::basePath();
?>

<div class="flex flex-col gap-6" id="orderManagementRoot">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Order Management</h1>
            <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Data pesanan diambil langsung dari database dan dapat diproses dari panel ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white px-5 py-3 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">Hari ini</p>
                <p class="poppins text-lg font-bold text-[var(--text-dark)]"><?= (int) $stats['today_orders'] ?> pesanan</p>
            </div>
            <div class="bg-[var(--brand)] px-5 py-3 rounded-xl shadow-lg text-white">
                <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Omzet</p>
                <p class="poppins text-lg font-bold"><?= htmlspecialchars(Money::formatIdr($stats['today_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="get" class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-50">
        <input type="hidden" name="page" value="orders">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1 opacity-50">Cari Order</label>
                <input type="text" name="q" value="<?= htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8') ?>" placeholder="ORD-0515-001" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1 opacity-50">Status</label>
                <select name="status" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <?php foreach (['pending_payment' => 'Menunggu Bayar', 'paid' => 'Dibayar', 'accepted' => 'Diterima', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai', 'cancelled' => 'Batal'] as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1 opacity-50">Tanggal</label>
                <input type="date" name="date" value="<?= htmlspecialchars($filters['date'], ENT_QUOTES, 'UTF-8') ?>" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none">
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1 opacity-50">Warung</label>
                <select name="warung" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <?php foreach ($warungs as $warung): ?>
                        <option value="<?= (int) $warung['id'] ?>" <?= $filters['warung'] === (int) $warung['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $warung['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1 opacity-50">Pembayaran</label>
                <select name="payment" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <option value="qris" <?= $filters['payment'] === 'qris' ? 'selected' : '' ?>>QRIS</option>
                    <option value="cashier" <?= $filters['payment'] === 'cashier' ? 'selected' : '' ?>>Kasir</option>
                </select>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="?page=orders" class="px-5 py-2.5 rounded-xl bg-gray-50 text-[var(--text-muted)] text-[10px] font-black uppercase tracking-widest transition-all hover:bg-gray-100">Reset</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[var(--brand)] text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-900/10 hover:opacity-90 transition-all">Terapkan Filter</button>
        </div>
    </form>

    <!-- Content Area: Table + Sidebar Detail -->
    <div class="flex gap-6 relative" id="ordersContentWrapper">
        <!-- Main Table Container -->
        <div class="flex-1 bg-white rounded-[24px] shadow-sm border border-gray-50 overflow-hidden transition-all duration-300" id="ordersTableContainer">
            <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between">
                <h2 class="poppins text-lg font-bold text-[var(--brand)]">Daftar Pesanan</h2>
                <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] opacity-40"><?= count($orders) ?> hasil ditemukan</span>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[800px]" id="ordersTable">
                    <thead class="bg-[#FAF7F6]">
                        <tr>
                            <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">ID Pesanan</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Tenant</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Customer</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Meja</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Amount</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Payment</th>
                            <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if ($orders === []): ?>
                            <tr>
                                <td colspan="7" class="px-10 py-20 text-center text-[var(--text-muted)] font-medium text-sm">Tidak ada data pesanan yang sesuai filter.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr class="order-row group cursor-pointer hover:bg-[#FAF7F6] transition-all relative" 
                                    data-id="<?= (int)$order['id'] ?>"
                                    data-display="<?= htmlspecialchars((string)($order['display_order_number'] ?? $order['order_number']), ENT_QUOTES, 'UTF-8') ?>">
                                    <td class="px-10 py-6">
                                        <span class="text-sm font-black text-[var(--brand)]">#<?= htmlspecialchars((string) ($order['display_order_number'] ?? $order['order_number']), ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70">
                                        <div class="truncate max-w-[150px]"><?= htmlspecialchars((string) ($order['tenant_names'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70">
                                        <?= htmlspecialchars((string) ($order['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70">
                                        <span class="px-3 py-1 bg-gray-50 rounded-lg text-[10px] font-black uppercase tracking-widest">T-<?= htmlspecialchars((string) $order['table_number'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-black text-[var(--text-dark)] poppins">
                                        <?= htmlspecialchars(Money::formatIdr((float) $order['total']), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-8 py-6 text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest opacity-60">
                                        <?= htmlspecialchars(scanteen_admin_payment_label((string) ($order['payment_method'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-10 py-6">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black <?= scanteen_admin_order_status_class((string) $order['status']) ?> uppercase tracking-widest border border-current/10">
                                            <?= htmlspecialchars(scanteen_admin_order_status_label((string) $order['status']), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Sidebar (Hidden by default) -->
        <div class="hidden lg:flex w-0 lg:w-0 overflow-hidden bg-white rounded-[32px] shadow-2xl border border-gray-100 flex-col sticky top-6 transition-all duration-300 opacity-0" id="orderDetailSidebar">
            <div class="p-10 pb-8 bg-[#FAF7F6] relative border-b border-gray-50">
                <button id="btnCloseDetail" class="absolute top-8 right-8 w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-400 hover:text-[var(--brand)] transition-all border border-gray-100 group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-2 opacity-50">Detail Pesanan</p>
                <h2 class="poppins text-3xl font-bold text-[var(--brand)]" id="detailOrderNumber">ORD-XXXX-XXX</h2>
                <div class="flex items-center gap-3 mt-6" id="detailHeaderBadges">
                    <!-- Status & Payment badges will be injected here -->
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-10 pt-8 custom-scrollbar" id="detailBodyContent">
                <!-- Loading or Detail Content -->
                <div class="flex flex-col items-center justify-center py-24 text-gray-400" id="detailLoader">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[var(--brand)] mb-4"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-50">Memuat Data...</p>
                </div>
                <div id="detailActualContent" class="hidden">
                    <!-- Customer Section -->
                    <div class="bg-white p-8 rounded-[24px] mb-8 border border-gray-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[var(--brand)] opacity-20 group-hover:opacity-100 transition-opacity"></div>
                        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-6 opacity-40">Customer Analytics</p>
                        <div class="space-y-5">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-[var(--brand-muted)] flex items-center justify-center text-[var(--brand)] flex-shrink-0 border-2 border-white shadow-sm">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-[var(--text-dark)] leading-none" id="detailCustomerName">-</p>
                                    <p class="text-[11px] font-bold text-[var(--text-muted)] mt-1.5 opacity-60" id="detailCustomerEmail">-</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-[var(--text-dark)] flex-shrink-0 border-2 border-white shadow-sm">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M9 17h6"/></svg>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-[var(--text-dark)] leading-none" id="detailTableName">Meja ?</p>
                                    <p class="text-[11px] font-bold text-[var(--text-muted)] mt-1.5 opacity-60 uppercase tracking-widest" id="detailDiningType">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="mb-10">
                        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-6 opacity-40">Itemized Breakdown</p>
                        <div class="space-y-6" id="detailItemsList">
                            <!-- Items injected here -->
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="mb-10">
                        <div class="bg-[var(--brand)] rounded-[32px] p-8 shadow-2xl text-white relative overflow-hidden group">
                            <div class="absolute right-0 top-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between text-[11px] font-black text-white/40 uppercase tracking-widest">
                                    <span>Subtotal</span>
                                    <span id="detailSubtotal" class="poppins">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-[11px] font-black text-white/40 uppercase tracking-widest border-b border-white/10 pb-5">
                                    <span>Service & Tax</span>
                                    <span id="detailTax" class="poppins">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/60">Grand Total</span>
                                    <span class="poppins text-3xl font-bold" id="detailTotal">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 mb-10" id="detailActions">
                        <!-- Buttons injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--brand-soft); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--brand); }
    
    #ordersTableContainer.compact {
        max-width: calc(100% - 476px);
    }
    
    #orderDetailSidebar.open {
        width: 450px;
        opacity: 1;
        display: flex;
    }
    
    .order-row td:first-child {
        transition: border-left 0.4s ease-in-out;
        border-left: 4px solid transparent;
    }
    
    .order-row.active {
        background-color: #FAF7F6;
    }
    
    .order-row.active td:first-child {
        border-left-color: var(--brand);
    }
</style>

<script>
(function () {
    const apiBase = <?= json_encode($apiBase . '/api/staff/order.php', JSON_THROW_ON_ERROR) ?>;
    const rows = document.querySelectorAll('.order-row');
    const tableContainer = document.getElementById('ordersTableContainer');
    const sidebar = document.getElementById('orderDetailSidebar');
    const btnClose = document.getElementById('btnCloseDetail');
    
    const detailOrderNumber = document.getElementById('detailOrderNumber');
    const detailHeaderBadges = document.getElementById('detailHeaderBadges');
    const detailActualContent = document.getElementById('detailActualContent');
    const detailLoader = document.getElementById('detailLoader');
    
    const detailCustomerName = document.getElementById('detailCustomerName');
    const detailCustomerEmail = document.getElementById('detailCustomerEmail');
    const detailTableName = document.getElementById('detailTableName');
    const detailDiningType = document.getElementById('detailDiningType');
    const detailItemsList = document.getElementById('detailItemsList');
    const detailSubtotal = document.getElementById('detailSubtotal');
    const detailTax = document.getElementById('detailTax');
    const detailTotal = document.getElementById('detailTotal');
    const detailActions = document.getElementById('detailActions');

    function formatIdr(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function getStatusClass(status) {
        switch(status) {
            case 'pending_payment': return 'bg-gray-100 text-gray-500';
            case 'cancelled': return 'bg-red-50 text-[var(--error-red)]';
            case 'completed': return 'bg-[var(--success-bg)] text-[var(--success-green)]';
            case 'paid': return 'bg-blue-50 text-blue-600';
            default: return 'bg-[var(--brand-muted)] text-[var(--brand)]';
        }
    }

    function getStatusLabel(status) {
        const labels = {
            'pending_payment': 'Menunggu Bayar',
            'paid': 'Dibayar',
            'accepted': 'Diterima',
            'processing': 'Diproses',
            'ready': 'Siap',
            'completed': 'Selesai',
            'cancelled': 'Batal'
        };
        return labels[status] || status.toUpperCase().replace('_', ' ');
    }

    async function showDetail(id, displayNumber) {
        rows.forEach(r => r.classList.remove('active'));
        document.querySelector(`.order-row[data-id="${id}"]`).classList.add('active');
        
        tableContainer.classList.add('compact');
        sidebar.classList.remove('hidden');
        setTimeout(() => sidebar.classList.add('open'), 10);
        
        detailOrderNumber.textContent = displayNumber;
        detailActualContent.classList.add('hidden');
        detailLoader.classList.remove('hidden');
        detailHeaderBadges.innerHTML = '';
        
        try {
            const res = await fetch(apiBase, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'detail', order_id: id })
            });
            const data = await res.json();
            if (!data.ok) throw new Error(data.error || 'Gagal');
            
            const order = data.order;
            const groups = data.groups;
            
            detailHeaderBadges.innerHTML = `
                <span class="px-4 py-1.5 rounded-full text-[9px] font-black ${getStatusClass(order.status)} uppercase tracking-widest border border-current/10">
                    ${getStatusLabel(order.status)}
                </span>
                <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] opacity-40 ml-2">
                    ${order.payment_method.toUpperCase()}
                </span>
            `;
            
            detailCustomerName.textContent = order.customer_name || 'Pelanggan Umum';
            detailCustomerEmail.textContent = order.customer_email || '-';
            detailTableName.textContent = 'Meja ' + (order.table_number || '?');
            detailDiningType.textContent = order.dining_type === 'take_away' ? 'Take Away' : 'Dine In';
            
            detailItemsList.innerHTML = '';
            groups.forEach(group => {
                let itemsHtml = '';
                group.items.forEach(item => {
                    itemsHtml += `
                        <div class="flex justify-between gap-4 py-3 first:pt-0">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-[var(--text-dark)] leading-snug">${item.menu_name_snapshot}</p>
                                <p class="text-[10px] font-bold text-[var(--text-muted)] mt-1.5 opacity-60">Qty: ${item.quantity} · ${formatIdr(item.unit_price)}</p>
                                ${item.note ? `<p class="text-[10px] font-bold text-[var(--brand)] mt-2 italic flex items-center gap-1.5"><span class="w-1 h-1 bg-[var(--brand)] rounded-full"></span>${item.note}</p>` : ''}
                            </div>
                            <div class="text-sm font-black text-[var(--text-dark)] poppins">${formatIdr(item.line_subtotal)}</div>
                        </div>
                    `;
                });
                
                detailItemsList.innerHTML += `
                    <div class="bg-white rounded-[24px] p-6 border border-gray-50 shadow-sm group">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-50">
                            <div class="w-1.5 h-4 bg-[var(--brand)] rounded-full opacity-20 group-hover:opacity-100 transition-opacity"></div>
                            <p class="text-[10px] font-black text-[var(--brand)] uppercase tracking-widest">${group.warung_name}</p>
                        </div>
                        <div class="divide-y divide-gray-50">${itemsHtml}</div>
                    </div>
                `;
            });
            
            detailSubtotal.textContent = formatIdr(order.subtotal);
            detailTax.textContent = formatIdr(order.service_tax);
            detailTotal.textContent = formatIdr(order.total);
            
            detailActions.innerHTML = '';
            if (order.status === 'pending_payment') {
                detailActions.innerHTML = `
                    <button id="btnMarkPaid" class="flex-1 px-6 py-4 rounded-2xl bg-[var(--brand)] text-white text-[11px] font-black uppercase tracking-widest shadow-xl shadow-red-900/10 hover:opacity-90 transition-all">Mark Paid</button>
                    <button id="btnCancelOrder" class="flex-1 px-6 py-4 rounded-2xl border border-gray-100 bg-white text-[var(--text-muted)] text-[11px] font-black uppercase tracking-widest hover:bg-red-50 hover:text-[var(--error-red)] hover:border-red-100 transition-all">Cancel</button>
                `;
                
                document.getElementById('btnMarkPaid').onclick = async () => {
                    if (!confirm('Tandai pesanan ini sudah dibayar?')) return;
                    const r = await fetch(apiBase, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ action: 'mark-paid', public_token: order.public_token }) });
                    const d = await r.json();
                    if (d.ok) location.reload(); else alert(d.error || 'Gagal');
                };
                
                document.getElementById('btnCancelOrder').onclick = async () => {
                    if (!confirm('Batalkan pesanan ini?')) return;
                    const r = await fetch(apiBase, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ action: 'cancel', order_id: order.id }) });
                    const d = await r.json();
                    if (d.ok) location.reload(); else alert(d.error || 'Gagal');
                };
            } else {
                detailActions.innerHTML = `
                    <button id="btnPrintDetail" class="w-full py-4 bg-gray-50 text-[var(--text-muted)] rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all">Cetak Ringkasan Pesanan</button>
                `;
                document.getElementById('btnPrintDetail').onclick = () => window.open('?page=struk&id=' + order.id, '_blank');
            }
            
            detailLoader.classList.add('hidden');
            detailActualContent.classList.remove('hidden');
            
        } catch (err) {
            detailHeaderBadges.innerHTML = `<span class="text-[10px] font-black text-[var(--error-red)] uppercase">${err.message}</span>`;
            detailLoader.classList.add('hidden');
        }
    }

    function hideDetail() {
        sidebar.classList.remove('open');
        setTimeout(() => {
            if (!sidebar.classList.contains('open')) {
                sidebar.classList.add('hidden');
                tableContainer.classList.remove('compact');
                rows.forEach(r => r.classList.remove('active'));
            }
        }, 300);
    }

    rows.forEach(row => {
        row.onclick = () => showDetail(row.dataset.id, row.dataset.display);
    });

    btnClose.onclick = hideDetail;
})();
</script>
