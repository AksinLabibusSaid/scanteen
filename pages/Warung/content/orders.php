<?php

declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$venueId = (int) StaffAuth::venueId();
$warungId = StaffAuth::warungId();
$orders = [];
$kpis = ['incoming' => 0, 'active' => 0, 'completed_today' => 0];
if ($warungId !== null) {
    $orders = (new OrderListRepository())->listForWarung($venueId, $warungId, 150);
    $kpis = (new VenueStatsRepository())->warungDashboard($venueId, $warungId);
}
$apiWarung = PublicUrl::basePath() . '/api/staff/warung.php';

function scanteen_warung_orders_badge(?string $s): string
{
    $s = $s ?? 'new';
    return match ($s) {
        'new' => 'status-new',
        'preparing' => 'status-preparing',
        'ready' => 'status-ready',
        default => 'bg-gray-100 text-gray-600',
    };
}

function scanteen_warung_orders_label(?string $s): string
{
    $s = $s ?? 'new';
    return match ($s) {
        'new' => 'BARU',
        'preparing' => 'DIPROSES',
        'ready' => 'SIAP',
        default => strtoupper($s),
    };
}
?>

<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.625rem;
        font-weight: 700;
        line-height: 1;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-new {
        background-color: #FFF7ED;
        color: #C2410C;
        border: 1px solid #FFEDD5;
    }

    .status-preparing {
        background-color: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #DBEAFE;
    }

    .status-ready {
        background-color: #F5F3FF;
        color: #7C3AED;
        border: 1px solid #EDE9FE;
    }

    .status-done {
        background-color: #ECFDF5;
        color: #059669;
        border: 1px solid #D1FAE5;
    }

    .active-tab {
        background-color: white;
        color: #7B0009;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
</style>

<div class="flex flex-col gap-5">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">Manajemen Pesanan</h1>
            <p class="mt-1 text-sm text-gray-500 font-normal">Pemantauan transaksi dan pemenuhan pesanan stan secara real-time.</p>
        </div>
    </div>

    <?php if ($warungId === null): ?>
        <p class="text-red-600 text-sm">Akun tidak terhubung ke stan.</p>
    <?php else: ?>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Incoming Orders -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-[#7B0009]">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                        </svg>
                    </div>
                </div>
                <p class="text-[10px] font-bold tracking-[1.1px] uppercase text-gray-400 mb-1">Pesanan Masuk</p>
                <p class="text-3xl font-bold text-gray-900"><?= (int) $kpis['incoming'] ?></p>
            </div>

            <!-- Active Orders -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-[10px] font-bold tracking-[1.1px] uppercase text-gray-400 mb-1">Pesanan Aktif</p>
                <p class="text-3xl font-bold text-gray-900"><?= (int) $kpis['active'] ?></p>
            </div>

            <!-- Completed Today -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>
                <p class="text-[10px] font-bold tracking-[1.1px] uppercase text-gray-400 mb-1">Selesai Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900"><?= (int) $kpis['completed_today'] ?></p>
            </div>
        </div>

        <!-- Live Order Queue -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <!-- Panel Header -->
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-5 border-b border-gray-50">
                <h2 class="text-xl font-bold text-gray-900">Antrean Pesanan Langsung</h2>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50 bg-[#FAF9F9]">
                            <th class="px-6 py-4 text-left text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">ID Pesanan</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Meja</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Total</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Status</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if ($orders === []): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 font-medium">Tidak ada antrean pesanan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                                <?php
                                $oid = (int) $o['id'];
                                $ful = (string) ($o['warung_fulfillment_status'] ?? 'new');
                                $st = (string) $o['status'];
                                $initial = strtoupper(substr($o['customer_name'] ?? 'U', 0, 1));
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-900">#<?= htmlspecialchars((string) ($o['display_order_number'] ?? $o['order_number']), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-100 text-[11px] font-bold text-[#7B0009]"><?= $initial ?></div>
                                            <span class="text-sm font-semibold text-gray-700"><?= htmlspecialchars((string) ($o['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-gray-400 font-medium leading-relaxed">T-<?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-6 py-5 text-right text-sm font-black text-gray-900"><?= htmlspecialchars(Money::formatIdr((float) $o['total']), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="status-badge <?= scanteen_warung_orders_badge($ful) ?>"><?= scanteen_warung_orders_label($ful) ?></span>
                                    </td>
                                    <td class="px-6 py-5 text-right space-x-2">
                                        <?php if ($st !== 'pending_payment' && $ful !== 'ready'): ?>
                                            <?php if ($ful === 'new'): ?>
                                                <button type="button" class="text-xs font-bold px-4 py-2 bg-[#7B0009] text-white rounded-lg hover:bg-[#991B1B] transition-colors btn-ful"
                                                    data-order="<?= $oid ?>" data-status="preparing">Mulai Masak</button>
                                            <?php endif; ?>
                                            <?php if ($ful === 'preparing'): ?>
                                                <button type="button" class="text-xs font-bold px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors btn-ful"
                                                    data-order="<?= $oid ?>" data-status="ready">Selesai</button>
                                            <?php endif; ?>
                                        <?php elseif ($ful === 'ready'): ?>
                                            <span class="text-[10px] text-emerald-600 font-bold px-2">Sudah Siap</span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-400 px-2">Menunggu Bayar</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    <?php endif; ?>
</div>

<script>
    (function() {
        document.querySelectorAll('.btn-ful').forEach(function(btn) {
            btn.addEventListener('click', async function() {
                const orderId = parseInt(btn.getAttribute('data-order'), 10);
                const status = btn.getAttribute('data-status');
                const res = await fetch(<?= json_encode($apiWarung, JSON_THROW_ON_ERROR) ?>, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        action: 'fulfillment',
                        order_id: orderId,
                        status: status
                    }),
                });
                const data = await res.json();
                if (!data.ok) {
                    alert(data.error || 'Gagal');
                    return;
                }
                if (typeof scanteenLoadPage === 'function') scanteenLoadPage(window.location.href, false);
                else location.reload();
            });
        });
    })();
</script>