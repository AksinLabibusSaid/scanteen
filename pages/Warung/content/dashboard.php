<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$warungId = StaffAuth::warungId();
$kpis = ['incoming' => 0, 'active' => 0, 'completed_today' => 0, 'revenue_today' => 0.0];
$queue = [];
$staffName = StaffAuth::userName();
$warungName = 'Stan';

if ($warungId !== null) {
    $kpis = (new VenueStatsRepository())->warungDashboard($venueId, $warungId);
    $queue = (new OrderListRepository())->listForWarung($venueId, $warungId, 8);
    
    // Fetch warung name
    $w = (new \App\Repositories\WarungRepository())->findByIdForVenue($warungId, $venueId);
    if ($w) {
        $warungName = $w['name'];
    }
}
?>

<div class="flex flex-col gap-10 pb-10">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--text-dark)]">Selamat Datang, <?= htmlspecialchars($staffName) ?>! 👋</h1>
            <p class="text-sm font-medium text-[var(--text-muted)] mt-1 uppercase tracking-[0.15em]">Overview <?= htmlspecialchars($warungName) ?> • <?= date('d M Y') ?></p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white px-6 py-3 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-[var(--success-green)] animate-pulse"></div>
                <span class="text-xs font-black text-[var(--text-dark)] uppercase tracking-widest">Sistem Online</span>
            </div>
        </div>
    </div>

    <?php if ($warungId === null): ?>
        <div class="bg-red-50 p-12 rounded-[32px] border border-red-100 text-center shadow-sm">
            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-[var(--error-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h2 class="poppins text-xl font-bold text-red-900 mb-2">Akses Terbatas</h2>
            <p class="text-sm font-bold text-red-600/70 uppercase tracking-widest leading-relaxed max-w-md mx-auto">Akun Anda belum terhubung ke stan manapun. Silakan hubungi administrator untuk penugasan stan.</p>
        </div>
    <?php else: ?>
        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Incoming (Pending Payment) -->
            <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-20 h-20 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                </div>
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-4">Menunggu Bayar</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="poppins text-4xl font-bold text-[var(--text-dark)] tracking-tighter"><?= (int) $kpis['incoming'] ?></h3>
                    <span class="text-xs font-bold text-orange-500 uppercase tracking-widest">Pesanan</span>
                </div>
                <div class="mt-4 w-12 h-1.5 bg-orange-100 rounded-full">
                    <div class="h-full bg-orange-500 rounded-full" style="width: 40%"></div>
                </div>
            </div>

            <!-- Active (Processing) -->
            <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-20 h-20 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
                </div>
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-4">Sedang Proses</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="poppins text-4xl font-bold text-[var(--text-dark)] tracking-tighter"><?= (int) $kpis['active'] ?></h3>
                    <span class="text-xs font-bold text-blue-500 uppercase tracking-widest">Antrean</span>
                </div>
                <div class="mt-4 w-12 h-1.5 bg-blue-100 rounded-full">
                    <div class="h-full bg-blue-500 rounded-full" style="width: 70%"></div>
                </div>
            </div>

            <!-- Completed Today -->
            <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-20 h-20 text-[var(--success-green)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] mb-4">Selesai Hari Ini</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="poppins text-4xl font-bold text-[var(--text-dark)] tracking-tighter"><?= (int) $kpis['completed_today'] ?></h3>
                    <span class="text-xs font-bold text-[var(--success-green)] uppercase tracking-widest">Selesai</span>
                </div>
                <div class="mt-4 w-12 h-1.5 bg-emerald-100 rounded-full">
                    <div class="h-full bg-[var(--success-green)] rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="bg-[var(--brand)] p-8 rounded-[24px] shadow-lg shadow-red-900/10 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
                <p class="text-[10px] font-black text-white/50 uppercase tracking-[0.2em] mb-4">Omzet Hari Ini</p>
                <h3 class="poppins text-2xl font-bold text-white tracking-tight"><?= htmlspecialchars(Money::formatIdr($kpis['revenue_today'])) ?></h3>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-4 italic">Berdasarkan pesanan dibayar</p>
            </div>
        </div>

        <!-- Active Queue Section -->
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden mt-4">
            <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-[var(--brand)] rounded-full"></div>
                    <h2 class="poppins text-lg font-bold text-[var(--brand)]">Antrean Dapur Terkini</h2>
                </div>
                <a href="?page=orders" class="px-5 py-2.5 bg-[#FAF7F6] text-[var(--brand)] rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[var(--brand)] hover:text-white transition-all">Buka Semua Pesanan</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#FAF7F6]">
                        <tr>
                            <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">ID Pesanan</th>
                            <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Pelanggan & Meja</th>
                            <th class="px-10 py-5 text-center text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Item Pesanan</th>
                            <th class="px-10 py-5 text-center text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status Dapur</th>
                            <th class="px-10 py-5 text-right text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($queue as $o): ?>
                            <tr class="hover:bg-[#FAF7F6] transition-colors group">
                                <td class="px-10 py-6">
                                    <span class="text-sm font-black text-[var(--brand)] tracking-tight">#<?= htmlspecialchars((string) $o['order_number']) ?></span>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <p class="text-sm font-bold text-[var(--text-dark)]"><?= htmlspecialchars((string) ($o['customer_name'] ?? 'Pelanggan')) ?></p>
                                        <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-0.5">Meja #<?= htmlspecialchars((string) $o['table_number']) ?></p>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs font-bold text-[var(--text-muted)] max-w-[200px] truncate opacity-70">
                                            <?= htmlspecialchars((string) ($o['warung_items_summary'] ?? '-')) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <?php 
                                    $fs = $o['warung_fulfillment_status'] ?? 'new';
                                    $badgeClass = $fs === 'preparing' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-orange-50 text-orange-600 border-orange-100';
                                    $badgeText = $fs === 'preparing' ? 'MEMASAK' : 'ANTREAN BARU';
                                    ?>
                                    <span class="px-3 py-1 <?= $badgeClass ?> text-[9px] font-black rounded-lg uppercase tracking-widest border">
                                        <?= $badgeText ?>
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button class="btn-view-detail px-5 py-2 bg-white border border-gray-100 text-[var(--text-dark)] rounded-xl font-black text-[10px] uppercase tracking-widest hover:border-[var(--brand)] hover:text-[var(--brand)] transition-all"
                                            data-id="<?= (int) $o['id'] ?>">
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($queue === []): ?>
                <div class="py-20 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-5">
                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 4-8-4"/></svg>
                    </div>
                    <p class="text-sm font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">Tidak Ada Antrean Aktif Saat Ini</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Dashboard Detail Logic
(function () {
  document.querySelectorAll('.btn-view-detail').forEach(btn => {
    btn.onclick = () => {
      const id = btn.getAttribute('data-id');
      window.location.href = '?page=orders&focus=' + id;
    };
  });
})();
</script>
