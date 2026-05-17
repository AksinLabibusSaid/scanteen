<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$venueId = (int) StaffAuth::venueId();
$warungId = StaffAuth::warungId();
$orders = [];
$stats = ['incoming' => 0, 'preparing' => 0, 'completed_today' => 0];

if ($warungId !== null) {
    $repo = new OrderListRepository();
    $orders = $repo->listForWarung($venueId, $warungId, 150);
    $stats = $repo->getWarungFulfillmentStats($venueId, $warungId);
}

$apiWarung = PublicUrl::basePath() . '/api/staff/warung.php';

function scanteen_get_initials(?string $name): string
{
    if (!$name) return '??';
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $w) {
        $initials .= strtoupper(substr($w, 0, 1));
        if (strlen($initials) >= 2) break;
    }
    return $initials;
}

function scanteen_ful_badge_styles(string $status): array
{
    return match ($status) {
        'new' => ['bg-orange-50', 'text-orange-600', 'INCOMING'],
        'preparing' => ['bg-blue-50', 'text-blue-600', 'PREPARING'],
        'ready' => ['bg-indigo-50', 'text-indigo-600', 'READY'],
        'picked_up', 'completed' => ['bg-emerald-50', 'text-emerald-600', 'PICKED UP'],
        default => ['bg-gray-50', 'text-gray-600', strtoupper($status)],
    };
}
?>

<div class="flex flex-col gap-10 pb-10">
    <?php if ($stats['incoming'] > 0): ?>
        <!-- Premium Warning Notification Alert Banner -->
        <div class="flex flex-col md:flex-row items-center justify-between p-6 bg-orange-50 border border-orange-200/80 rounded-[24px] shadow-sm gap-4 transition-all">
            <div class="flex items-center gap-4">
                <!-- Animated Ring Dot -->
                <div class="relative flex h-5 w-5 flex-shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-5 w-5 bg-orange-500 flex items-center justify-center text-white text-[10px] font-black">!</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-orange-950 leading-snug">Ada Pesanan Baru Masuk!</h4>
                    <p class="text-xs text-orange-700 font-medium mt-0.5">Terdapat <span class="font-extrabold text-orange-950"><?= $stats['incoming'] ?> pesanan baru</span> yang siap untuk Anda proses.</p>
                </div>
            </div>
            <button onclick="location.reload()" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-900/10 hover:scale-[1.02] active:scale-95 transition-all">
                Perbarui Daftar
            </button>
        </div>
    <?php endif; ?>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-50 relative overflow-hidden group transition-all hover:shadow-md">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[var(--brand)] group-hover:w-3 transition-all"></div>
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-[var(--brand-muted)] rounded-2xl flex items-center justify-center text-[var(--brand)]">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[var(--text-muted)] text-[10px] font-black uppercase tracking-widest mb-1">Incoming Orders</p>
                    <h3 class="poppins text-3xl font-bold text-[var(--text-dark)]"><?= sprintf('%02d', $stats['incoming']) ?></h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-50 relative overflow-hidden group transition-all hover:shadow-md">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-orange-500 group-hover:w-3 transition-all"></div>
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-[var(--text-muted)] text-[10px] font-black uppercase tracking-widest mb-1">Active Orders</p>
                    <h3 class="poppins text-3xl font-bold text-[var(--text-dark)]"><?= sprintf('%02d', $stats['preparing']) ?></h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-50 relative overflow-hidden group transition-all hover:shadow-md">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[var(--success-green)] group-hover:w-3 transition-all"></div>
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-[var(--success-bg)] rounded-2xl flex items-center justify-center text-[var(--success-green)]">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[var(--text-muted)] text-[10px] font-black uppercase tracking-widest mb-1">Completed Today</p>
                    <h3 class="poppins text-3xl font-bold text-[var(--text-dark)]"><?= sprintf('%03d', $stats['completed_today']) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Order Queue -->
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex flex-col lg:flex-row items-center justify-between gap-6">
            <h2 class="poppins text-2xl font-bold text-[var(--text-dark)]">Live Order Queue</h2>
            <div class="flex items-center gap-4 w-full lg:w-auto">
                <div class="flex bg-[var(--brand-soft)]/50 p-1 rounded-xl items-center gap-1">
                    <button id="filter-btn-all" class="px-5 py-2 bg-white shadow-sm rounded-lg text-[10px] font-black text-[var(--brand)] uppercase tracking-widest flex items-center gap-2 transition-all">
                        Semua
                    </button>
                    <button id="filter-btn-new" class="px-5 py-2 text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest hover:text-[var(--text-dark)] flex items-center gap-2 transition-all">
                        Baru
                        <?php if ($stats['incoming'] > 0): ?>
                            <span class="flex items-center justify-center min-w-[16px] h-4 bg-[var(--brand)] text-white text-[9px] px-1 rounded-full font-bold shadow-sm" style="color: #ffffff !important;">
                                <?= $stats['incoming'] ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <button id="filter-btn-preparing" class="px-5 py-2 text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest hover:text-[var(--text-dark)] flex items-center gap-2 transition-all">
                        Proses
                        <?php if ($stats['preparing'] > 0): ?>
                            <span class="flex items-center justify-center min-w-[16px] h-4 bg-orange-500 text-white text-[9px] px-1 rounded-full font-bold shadow-sm" style="color: #ffffff !important;">
                                <?= $stats['preparing'] ?>
                            </span>
                        <?php endif; ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#FAF7F6] text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">
                        <th class="px-10 py-5">Order ID</th>
                        <th class="px-6 py-5">Customer</th>
                        <th class="px-6 py-5">Items</th>
                        <th class="px-6 py-5">Total</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-10 py-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr id="no-orders-placeholder" style="<?= empty($orders) ? '' : 'display: none;' ?>">
                        <td colspan="6" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-200">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <p class="text-[var(--text-muted)] font-bold uppercase tracking-widest text-[10px]">Tidak ada pesanan aktif</p>
                            </div>
                        </td>
                    </tr>

                    <?php foreach ($orders as $o): ?>
                        <?php
                        $oid = (int) $o['id'];
                        $ful = (string) ($o['warung_fulfillment_status'] ?? 'new');
                        $st = (string) $o['status'];
                        [$bgColor, $textColor, $label] = scanteen_ful_badge_styles($ful);
                        ?>
                        <tr class="hover:bg-[#FAF7F6] transition-colors group" data-fulfillment="<?= $ful ?>">
                            <td class="px-10 py-8">
                                <span class="text-sm font-black text-[var(--brand)]">#<?= htmlspecialchars((string) ($o['display_order_number'] ?? $o['order_number'])) ?></span>
                                <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-1">Meja <?= htmlspecialchars((string) $o['table_number']) ?></p>
                            </td>
                            <td class="px-6 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-[var(--brand-muted)] flex items-center justify-center text-[var(--brand)] text-[10px] font-black border-2 border-white shadow-sm">
                                        <?= scanteen_get_initials($o['customer_name']) ?>
                                    </div>
                                    <span class="text-sm font-bold text-[var(--text-dark)]"><?= htmlspecialchars((string) ($o['customer_name'] ?: 'Pelanggan')) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-8">
                                <p class="text-xs font-bold text-[var(--text-muted)] opacity-60 max-w-xs truncate" title="<?= htmlspecialchars($o['warung_items_summary']) ?>">
                                    <?= htmlspecialchars($o['warung_items_summary']) ?>
                                </p>
                            </td>
                            <td class="px-6 py-8">
                                <span class="text-sm font-black text-[var(--text-dark)]"><?= htmlspecialchars(Money::formatIdr((float) $o['total'])) ?></span>
                            </td>
                            <td class="px-6 py-8 text-center">
                                <span class="px-3 py-1.5 <?= $bgColor ?> <?= $textColor ?> text-[9px] font-black rounded-full uppercase tracking-widest border border-current/20 opacity-90">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <div class="flex flex-col items-end gap-3">
                                    <button class="px-5 py-2 rounded-xl bg-white border border-gray-100 text-[var(--text-dark)] text-[10px] font-black uppercase tracking-widest hover:border-[var(--brand)] hover:text-[var(--brand)] transition-all btn-view-detail"
                                            data-id="<?= $oid ?>">Lihat Detail</button>

                                    <?php if ($st !== 'pending_payment' && $ful !== 'ready'): ?>
                                        <?php if ($ful === 'new'): ?>
                                            <button class="px-6 py-2.5 bg-[var(--brand)] text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-red-900/10 hover:opacity-90 transition-all btn-ful"
                                                    data-order="<?= $oid ?>" data-status="preparing">Mulai Proses</button>
                                        <?php elseif ($ful === 'preparing'): ?>
                                            <button class="px-6 py-2.5 bg-[var(--success-green)] text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-900/10 hover:opacity-90 transition-all btn-ful"
                                                    data-order="<?= $oid ?>" data-status="ready">Pesanan Siap</button>
                                        <?php endif; ?>
                                    <?php elseif ($ful === 'ready'): ?>
                                        <div class="flex items-center justify-end gap-2 text-[var(--success-green)]">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Selesai</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest opacity-40 italic">Unpaid</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div id="modal-order-detail" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#261817]/60 backdrop-blur-md transition-opacity opacity-0" id="modal-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-[32px] shadow-2xl overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-500" id="modal-container">
            
            <!-- Header (Fixed) -->
            <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="poppins text-xl font-bold text-[var(--text-dark)]">Order Details <span id="detail-order-id" class="text-[var(--brand)] ml-1">#ORD-...</span></h2>
                    <span id="detail-fulfillment-badge" class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-blue-100">PREPARING</span>
                </div>
                <button id="btn-close-modal" class="w-12 h-12 rounded-full hover:bg-gray-100 flex items-center justify-center transition-all group">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Content (Scrollable) -->
            <div class="p-10 overflow-y-auto custom-scrollbar flex-1 bg-[#FAF7F6]">
                <!-- Customer Info Card -->
                <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm mb-10 relative">
                    <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-6">Customer Info</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 bg-[var(--brand-muted)] rounded-full flex items-center justify-center text-[var(--brand)] font-black text-lg border-4 border-white shadow-sm" id="detail-initials">??</div>
                            <div>
                                <h4 class="poppins text-lg font-bold text-[var(--text-dark)] tracking-tight" id="detail-customer-name">Siti Aminah</h4>
                                <p class="text-xs text-[var(--text-muted)] font-bold mt-1" id="detail-customer-contact">-</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center min-w-[90px]">
                            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-1 opacity-50">Table</p>
                            <p class="poppins text-2xl font-bold text-[var(--text-dark)]" id="detail-table-number">12</p>
                            <span class="mt-2 px-3 py-1 bg-[var(--brand)] text-white text-[9px] font-black rounded-lg uppercase tracking-widest block">DINE IN</span>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-12 px-2">
                    <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-8">Ordered Items</p>
                    <div id="detail-items-container" class="space-y-8">
                        <!-- Items populated by JS -->
                    </div>
                    
                    <div class="mt-10 pt-8 border-t border-dashed border-gray-100 flex justify-between items-center">
                        <p class="text-[11px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">Total</p>
                        <p class="poppins text-2xl font-bold text-[var(--text-dark)] tracking-tight" id="detail-subtotal">Rp 0</p>
                    </div>
                </div>

                <!-- Timeline Section -->
                <div class="px-2 pb-6">
                    <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-10">Order Timeline</p>
                    <div class="relative flex items-center justify-between px-4">
                        <!-- Timeline Line -->
                        <div class="absolute left-10 right-10 h-0.5 bg-gray-100 top-3 -z-10"></div>
                        
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center gap-4 w-1/3">
                            <div class="w-6 h-6 rounded-full bg-[var(--success-green)] flex items-center justify-center text-white ring-8 ring-white shadow-sm shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-[var(--text-dark)] uppercase tracking-tight leading-tight">Order Received</p>
                                <p class="text-[9px] font-bold text-[var(--text-muted)] mt-1.5" id="time-received">--:--</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center gap-4 w-1/3" id="step-payment">
                            <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-white ring-8 ring-white shadow-sm shrink-0 transition-colors" id="dot-payment">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-tight leading-tight" id="label-payment">Payment Confirmed</p>
                                <p class="text-[9px] font-bold text-[var(--text-muted)] mt-1.5" id="time-payment">--:--</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center gap-4 w-1/3" id="step-preparing">
                            <div class="w-6 h-6 rounded-full bg-gray-100 ring-8 ring-white shadow-sm transition-colors shrink-0" id="dot-preparing"></div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-tight leading-tight" id="label-preparing">Started Preparing</p>
                                <p class="text-[9px] font-bold text-[var(--text-muted)] mt-1.5" id="time-preparing">--:--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer (Fixed) -->
            <div class="p-10 border-t border-gray-50 bg-white flex gap-6 shrink-0">
                <button id="modal-btn-print" class="flex-1 py-5 bg-white border border-gray-100 text-[var(--text-dark)] rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-gray-50 transition-all">
                    Print Receipt
                </button>
                <button id="modal-btn-action" class="flex-1 py-5 bg-[var(--brand)] text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-red-900/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Mark as Ready
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  const apiWarung = <?= json_encode($apiWarung, JSON_THROW_ON_ERROR) ?>;
  const modal = document.getElementById('modal-order-detail');
  const modalBackdrop = document.getElementById('modal-backdrop');
  const modalContainer = document.getElementById('modal-container');
  const btnClose = document.getElementById('btn-close-modal');

  function formatMoney(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
  }

  function getInitials(name) {
    if (!name) return '??';
    const words = name.trim().split(/\s+/);
    if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
    return (words[0][0] + words[words.length - 1][0]).toUpperCase();
  }

  function formatTime(dateStr) {
    if (!dateStr) return '--:--';
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
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
    }, 400);
  }

  btnClose.onclick = hideModal;
  modalBackdrop.onclick = hideModal;

  // View Detail
  document.querySelectorAll('.btn-view-detail').forEach(btn => {
    btn.onclick = async () => {
      const id = btn.getAttribute('data-id');
      const res = await fetch(apiWarung, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'detail', order_id: parseInt(id) }),
      });
      const data = await res.json();
      if (!data.ok) return alert(data.error || 'Gagal');

      const o = data.order;
      const fulStatus = data.fulfillment_status;

      document.getElementById('detail-order-id').textContent = '#' + o.order_number;
      document.getElementById('detail-customer-name').textContent = o.customer_name || 'Customer';
      document.getElementById('detail-initials').textContent = getInitials(o.customer_name);
      document.getElementById('detail-customer-contact').textContent = o.customer_phone || o.customer_email || '-';
      document.getElementById('detail-table-number').textContent = String(o.table_number).padStart(2, '0');
      
      const badge = document.getElementById('detail-fulfillment-badge');
      badge.textContent = fulStatus.toUpperCase();
      badge.className = 'px-4 py-1.5 text-[9px] font-black rounded-full uppercase tracking-widest border ' + 
                       (fulStatus === 'new' ? 'bg-orange-50 text-orange-600 border-orange-100' : 
                        fulStatus === 'preparing' ? 'bg-blue-50 text-blue-600 border-blue-100' : 
                        'bg-indigo-50 text-indigo-600 border-indigo-100');

      // Timeline Logic
      document.getElementById('time-received').textContent = formatTime(o.created_at);
      
      const dotPay = document.getElementById('dot-payment');
      const labelPay = document.getElementById('label-payment');
      if (o.status !== 'pending_payment') {
          dotPay.classList.replace('bg-gray-100', 'bg-[var(--success-green)]');
          labelPay.classList.replace('text-[var(--text-muted)]', 'text-[var(--text-dark)]');
          document.getElementById('time-payment').textContent = formatTime(o.created_at);
      } else {
          dotPay.classList.replace('bg-[var(--success-green)]', 'bg-gray-100');
          labelPay.classList.replace('text-[var(--text-dark)]', 'text-[var(--text-muted)]');
          document.getElementById('time-payment').textContent = '--:--';
      }

      const dotPrep = document.getElementById('dot-preparing');
      const labelPrep = document.getElementById('label-preparing');
      if (fulStatus === 'preparing' || fulStatus === 'ready') {
          dotPrep.classList.replace('bg-gray-100', 'bg-[var(--brand)]');
          labelPrep.classList.replace('text-[var(--text-muted)]', 'text-[var(--brand)]');
          document.getElementById('time-preparing').textContent = formatTime(data.fulfillment_updated_at);
      } else {
          dotPrep.classList.replace('bg-[var(--brand)]', 'bg-gray-100');
          labelPrep.classList.replace('text-[var(--brand)]', 'text-[var(--text-muted)]');
          document.getElementById('time-preparing').textContent = '--:--';
      }

      // Items
      let total = 0;
      const itemsContainer = document.getElementById('detail-items-container');
      itemsContainer.innerHTML = '';
      data.items.forEach(item => {
        total += parseFloat(item.line_subtotal);
        const itemEl = document.createElement('div');
        itemEl.className = 'flex justify-between items-start group';
        itemEl.innerHTML = `
          <div class="flex gap-5">
            <span class="text-sm font-black text-[var(--brand)] min-w-[30px]">${item.quantity}x</span>
            <div>
                <h6 class="text-sm font-bold text-[var(--text-dark)] leading-snug group-hover:text-[var(--brand)] transition-colors">${item.menu_name_snapshot}</h6>
                ${item.note ? `<p class="text-[10px] text-[var(--text-muted)] font-bold mt-1.5 flex items-center gap-1.5 opacity-60"><span class="w-1.5 h-1.5 bg-[var(--brand-soft)] rounded-full"></span>${item.note}</p>` : ''}
            </div>
          </div>
          <p class="text-sm font-black text-[var(--text-dark)]">${formatMoney(item.line_subtotal)}</p>
        `;
        itemsContainer.appendChild(itemEl);
      });

      document.getElementById('detail-subtotal').textContent = formatMoney(total);
      
      const btnAction = document.getElementById('modal-btn-action');
      const btnPrint = document.getElementById('modal-btn-print');
      
      btnPrint.onclick = () => window.open('?page=struk&id=' + o.id, '_blank');

      if (o.status !== 'pending_payment' && fulStatus !== 'ready') {
          btnAction.style.display = 'block';
          if (fulStatus === 'new') {
              btnAction.textContent = 'Mulai Proses';
              btnAction.classList.replace('bg-emerald-500', 'bg-[var(--brand)]');
              btnAction.onclick = () => updateFulfillment(o.id, 'preparing');
          } else {
              btnAction.textContent = 'Mark as Ready';
              btnAction.classList.replace('bg-[var(--brand)]', 'bg-[#00C853]');
              btnAction.onclick = () => updateFulfillment(o.id, 'ready');
          }
      } else {
          btnAction.style.display = 'none';
      }

      showModal();
    };
  });

  async function updateFulfillment(orderId, status) {
    const res = await fetch(apiWarung, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'fulfillment', order_id: orderId, status: status }),
    });
    const data = await res.json();
    if (data.ok) {
        if (typeof scanteenLoadPage === 'function') scanteenLoadPage(window.location.href, false);
        else location.reload();
    } else {
        alert(data.error || 'Gagal');
    }
  }

  document.querySelectorAll('.btn-ful').forEach(btn => {
    btn.onclick = () => updateFulfillment(parseInt(btn.getAttribute('data-order')), btn.getAttribute('data-status'));
  });

  // Dynamic Tab Filtering Logic
  const filterBtnAll = document.getElementById('filter-btn-all');
  const filterBtnNew = document.getElementById('filter-btn-new');
  const filterBtnPrep = document.getElementById('filter-btn-preparing');
  const orderRows = document.querySelectorAll('tbody tr[data-fulfillment]');

  function applyFilter(selectedStatus, activeBtn) {
    // 1. Reset all button classes to inactive
    [filterBtnAll, filterBtnNew, filterBtnPrep].forEach(btn => {
      if (btn) {
        btn.className = btn.className.replace('bg-white shadow-sm text-[var(--brand)]', 'text-[var(--text-muted)] hover:text-[var(--text-dark)]');
        btn.classList.remove('bg-white', 'shadow-sm', 'text-[var(--brand)]');
        if (!btn.className.includes('text-[var(--text-muted)]')) {
          btn.classList.add('text-[var(--text-muted)]', 'hover:text-[var(--text-dark)]');
        }
      }
    });

    // 2. Set active styles for the clicked button
    if (activeBtn) {
      activeBtn.className = activeBtn.className.replace('text-[var(--text-muted)] hover:text-[var(--text-dark)]', 'bg-white shadow-sm text-[var(--brand)]');
      activeBtn.classList.remove('text-[var(--text-muted)]', 'hover:text-[var(--text-dark)]');
      activeBtn.classList.add('bg-white', 'shadow-sm', 'text-[var(--brand)]');
    }

    // 3. Filter rows
    let visibleCount = 0;
    orderRows.forEach(row => {
      const rowStatus = row.getAttribute('data-fulfillment');
      if (selectedStatus === 'all' || rowStatus === selectedStatus) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    // Show or hide placeholder if empty
    const noOrdersRow = document.getElementById('no-orders-placeholder');
    if (noOrdersRow) {
      noOrdersRow.style.display = (visibleCount === 0) ? '' : 'none';
    }
  }

  if (filterBtnAll) filterBtnAll.onclick = () => applyFilter('all', filterBtnAll);
  if (filterBtnNew) filterBtnNew.onclick = () => applyFilter('new', filterBtnNew);
  if (filterBtnPrep) filterBtnPrep.onclick = () => applyFilter('preparing', filterBtnPrep);
})();
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: var(--brand-soft); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--brand); }
</style>
