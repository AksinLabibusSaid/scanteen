<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$warungId = StaffAuth::warungId();
$rows = [];

if ($warungId !== null) {
    // Show both completed and cancelled in history
    $rows = (new OrderListRepository())->listHistoryForWarung($venueId, $warungId, 150);
}

// API for detail
$apiWarung = '/api/staff/warung.php';
?>

<div class="flex flex-col gap-8 pb-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[32px] border border-gray-50 shadow-sm">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-[var(--brand)] rounded-3xl flex items-center justify-center shadow-lg shadow-red-900/10">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="poppins text-2xl font-bold text-[var(--text-dark)]">Riwayat Pesanan</h1>
                <p class="text-sm font-medium text-[var(--text-muted)] mt-1 uppercase tracking-widest">Pantau semua pesanan yang telah selesai</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-5 py-3 bg-[var(--success-bg)] rounded-2xl border border-emerald-100 flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-[var(--success-green)]"></span>
                <span class="text-xs font-black text-[var(--success-green)] uppercase tracking-widest"><?= count($rows) ?> Transaksi</span>
            </div>
        </div>
    </div>

    <?php if ($warungId === null): ?>
        <div class="bg-red-50 p-10 rounded-[32px] border border-red-100 text-center">
            <p class="text-[var(--error-red)] font-black uppercase tracking-widest text-xs">Akun tidak terhubung ke stan manapun</p>
        </div>
    <?php else: ?>
        <!-- History Table -->
        <div class="bg-white rounded-[32px] border border-gray-50 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[#FAF7F6] text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">
                            <th class="px-8 py-5">ID Pesanan</th>
                            <th class="px-8 py-5">Meja & Pelanggan</th>
                            <th class="px-8 py-5">Menu Stan Anda</th>
                            <th class="px-8 py-5 text-right">Subtotal</th>
                            <th class="px-8 py-5 text-center">Waktu</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($rows as $r): ?>
                            <tr class="hover:bg-[#FAF7F6] transition-colors group">
                                <td class="px-8 py-6">
                                    <span class="text-sm font-black text-[var(--brand)] tracking-tight">#<?= htmlspecialchars((string) $r['order_number']) ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-[var(--brand-muted)] rounded-xl flex items-center justify-center text-[var(--brand)] font-black text-xs border border-white shadow-sm">
                                            <?= htmlspecialchars((string) $r['table_number']) ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-[var(--text-dark)]"><?= htmlspecialchars((string) ($r['customer_name'] ?? 'Pelanggan')) ?></p>
                                            <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-0.5 opacity-50">Dine In</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 max-w-xs">
                                    <p class="text-xs font-bold text-[var(--text-muted)] opacity-60 leading-relaxed truncate">
                                        <?= htmlspecialchars((string) $r['warung_items_summary']) ?>
                                    </p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <p class="text-sm font-black text-[var(--text-dark)] tracking-tight"><?= htmlspecialchars(Money::formatIdr((float) $r['total'])) ?></p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="text-[11px] font-bold text-[var(--text-muted)]"><?= date('H:i', strtotime($r['created_at'])) ?></span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <?php if ($r['status'] === 'completed'): ?>
                                        <span class="px-3 py-1 bg-[var(--success-bg)] text-[var(--success-green)] text-[9px] font-black rounded-lg uppercase tracking-widest border border-emerald-100">Selesai</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-50 text-[var(--error-red)] text-[9px] font-black rounded-lg uppercase tracking-widest border border-red-100">Batal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button class="btn-view-detail px-5 py-2 bg-white border border-gray-100 text-[var(--text-dark)] rounded-xl font-black text-[10px] uppercase tracking-widest hover:border-[var(--brand)] hover:text-[var(--brand)] transition-all"
                                            data-id="<?= (int) $r['id'] ?>">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($rows === []): ?>
                <div class="py-24 flex flex-col items-center justify-center text-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">Belum ada riwayat pesanan</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Order Detail Modal -->
<div id="modal-order-detail" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#261817]/60 backdrop-blur-md transition-opacity opacity-0" id="modal-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-[32px] shadow-2xl overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-500" id="modal-container">
            
            <!-- Header (Fixed) -->
            <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="poppins text-xl font-bold text-[var(--text-dark)]">Riwayat Detail <span id="detail-order-id" class="text-[var(--brand)] ml-1">#ORD-...</span></h2>
                    <span id="detail-fulfillment-badge" class="px-4 py-1.5 bg-[var(--success-bg)] text-[var(--success-green)] text-[10px] font-black rounded-full uppercase tracking-widest border border-emerald-100">COMPLETED</span>
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
                                <h4 class="poppins text-lg font-bold text-[var(--text-dark)] tracking-tight" id="detail-customer-name">Customer</h4>
                                <p class="text-xs text-[var(--text-muted)] font-bold mt-1" id="detail-customer-contact">-</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center min-w-[90px]">
                            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-1 opacity-50">Table</p>
                            <p class="poppins text-2xl font-bold text-[var(--text-dark)]" id="detail-table-number">00</p>
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
                        <div class="absolute left-10 right-10 h-0.5 bg-gray-100 top-3 -z-10"></div>
                        <div class="flex flex-col items-center gap-4 w-1/3">
                            <div class="w-6 h-6 rounded-full bg-[var(--success-green)] flex items-center justify-center text-white ring-8 ring-white shadow-sm shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-[var(--text-dark)] uppercase tracking-tight leading-tight">Order Received</p>
                                <p class="text-[9px] font-bold text-[var(--text-muted)] mt-1.5" id="time-received">--:--</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-4 w-1/3">
                            <div class="w-6 h-6 rounded-full bg-[var(--success-green)] flex items-center justify-center text-white ring-8 ring-white shadow-sm shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-[var(--text-dark)] uppercase tracking-tight leading-tight">Payment Confirmed</p>
                                <p class="text-[9px] font-bold text-[var(--text-muted)] mt-1.5" id="time-payment">--:--</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-4 w-1/3">
                            <div class="w-6 h-6 rounded-full bg-[var(--success-green)] flex items-center justify-center text-white ring-8 ring-white shadow-sm shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-[var(--success-green)] uppercase tracking-tight leading-tight">Completed</p>
                                <p class="text-[9px] font-bold text-[var(--text-muted)] mt-1.5" id="time-completing">--:--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-10 border-t border-gray-50 bg-white flex gap-6 shrink-0">
                <button id="modal-btn-print" class="flex-1 py-5 bg-[var(--brand)] text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-red-900/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Print Receipt
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
      document.getElementById('detail-order-id').textContent = '#' + o.order_number;
      document.getElementById('detail-customer-name').textContent = o.customer_name || 'Customer';
      document.getElementById('detail-initials').textContent = getInitials(o.customer_name);
      document.getElementById('detail-customer-contact').textContent = o.customer_phone || o.customer_email || '-';
      document.getElementById('detail-table-number').textContent = String(o.table_number).padStart(2, '0');
      
      document.getElementById('time-received').textContent = formatTime(o.created_at);
      document.getElementById('time-payment').textContent = formatTime(o.created_at);
      document.getElementById('time-completing').textContent = formatTime(o.updated_at);

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
      document.getElementById('modal-btn-print').onclick = () => window.open('?page=struk&id=' + o.id, '_blank');

      showModal();
    };
  });
})();
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: var(--brand-soft); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--brand); }
</style>
