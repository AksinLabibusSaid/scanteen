<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$venueId = (int) StaffAuth::venueId();
$orders = (new OrderListRepository())->listForVenue($venueId, 150);
$apiMarkPaid = PublicUrl::basePath() . '/api/staff/order-mark-paid.php';

function scanteen_kasir_status_label(string $s): string
{
    return match ($s) {
        'pending_payment' => 'Menunggu bayar',
        'paid' => 'Sudah bayar',
        'accepted' => 'Diterima',
        'processing' => 'Diproses',
        'ready' => 'Siap diambil',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        default => $s,
    };
}
?>

<div class="flex flex-col gap-6">
    <div>
        <h1 class="text-3xl font-extrabold text-[#261817] tracking-tight">Pesanan venue</h1>
        <p class="text-[#675C5C] text-sm mt-1 font-medium">Data dari database — konfirmasi pembayaran tunai/QR di kasir.</p>
    </div>

    <div class="bg-white rounded-3xl shadow border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-[#FAF9F9] border-b border-gray-100 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-4">No. pesanan</th>
                        <th class="px-4 py-4">Meja</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($orders as $o): ?>
                        <?php
                        $st = (string) $o['status'];
                        $pm = (string) ($o['payment_method'] ?? '');
                        $pmLabel = match ($pm) {
                            'qris' => 'QRIS',
                            'cashier' => 'Kasir',
                            default => $pm,
                        };
                        ?>
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-[#261817]"><?= htmlspecialchars((string) $o['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex px-3 py-1 rounded-lg bg-gray-50 border border-gray-100 text-[11px] font-black"><?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars((string) ($o['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-xs text-gray-600"><?= htmlspecialchars($pmLabel, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 font-black text-[#261817]"><?= htmlspecialchars(Money::formatIdr((float) $o['total']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide <?= $st === 'pending_payment' ? 'bg-orange-50 text-orange-700 border border-orange-100' : 'bg-slate-50 text-slate-600 border border-slate-100' ?>">
                                    <?= htmlspecialchars(scanteen_kasir_status_label($st), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($st === 'pending_payment' && in_array($pm, ['cashier', 'qris'], true)): ?>
                                    <button type="button"
                                            class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-[11px] font-bold shadow-sm hover:bg-emerald-700 btn-mark-paid"
                                            data-token="<?= htmlspecialchars((string) $o['public_token'], ENT_QUOTES, 'UTF-8') ?>">
                                        Konfirmasi bayar
                                    </button>
                                <?php else: ?>
                                    <span class="text-[10px] text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($orders === []): ?>
            <p class="p-10 text-center text-gray-400 text-sm">Belum ada pesanan.</p>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
  document.querySelectorAll('.btn-mark-paid').forEach(function (btn) {
    btn.addEventListener('click', async function () {
      const token = btn.getAttribute('data-token');
      if (!token || btn.disabled) return;
      btn.disabled = true;
      const res = await fetch(<?= json_encode($apiMarkPaid, JSON_THROW_ON_ERROR) ?>, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ public_token: token }),
      });
      const data = await res.json();
      if (!data.ok) {
        alert(data.error || 'Gagal');
        btn.disabled = false;
        return;
      }
      location.reload();
    });
  });
})();
</script>
