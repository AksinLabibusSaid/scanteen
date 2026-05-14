<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$venueId = (int) StaffAuth::venueId();
$warungId = StaffAuth::warungId();
$orders = [];
if ($warungId !== null) {
    $orders = (new OrderListRepository())->listForWarung($venueId, $warungId, 150);
}
$apiFulfillment = PublicUrl::basePath() . '/api/staff/warung-fulfillment.php';

function scanteen_warung_fulfillment_badge(?string $s): string
{
    $s = $s ?? 'new';

    return match ($s) {
        'new' => 'Baru',
        'preparing' => 'Diproses',
        'ready' => 'Siap',
        default => $s,
    };
}
?>

<div class="flex flex-col gap-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pesanan stan Anda</h1>
        <p class="mt-1 text-sm text-gray-500">Hanya menampilkan pesanan yang mengandung menu dari warung ini.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-[#FAF9F9] text-[10px] font-bold uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4 text-left">Pesanan</th>
                        <th class="px-6 py-4 text-left">Meja</th>
                        <th class="px-6 py-4 text-left">Pelanggan</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-left">Status pesanan</th>
                        <th class="px-6 py-4 text-left">Dapur</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($orders as $o): ?>
                        <?php
                        $oid = (int) $o['id'];
                        $ful = (string) ($o['warung_fulfillment_status'] ?? 'new');
                        $st = (string) $o['status'];
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars((string) $o['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars((string) ($o['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-right font-black text-gray-900"><?= htmlspecialchars(Money::formatIdr((float) $o['total']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-xs text-gray-500"><?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-red-50 text-[#7B0009] border border-red-100">
                                    <?= htmlspecialchars(scanteen_warung_fulfillment_badge($ful), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                <?php if ($st !== 'pending_payment' && $ful !== 'ready'): ?>
                                    <?php if ($ful === 'new'): ?>
                                        <button type="button" class="text-xs font-bold text-[#7B0009] hover:underline btn-ful"
                                                data-order="<?= $oid ?>" data-status="preparing">Mulai</button>
                                    <?php endif; ?>
                                    <?php if ($ful === 'preparing'): ?>
                                        <button type="button" class="text-xs font-bold text-emerald-700 hover:underline btn-ful"
                                                data-order="<?= $oid ?>" data-status="ready">Siap</button>
                                    <?php endif; ?>
                                <?php elseif ($ful === 'ready'): ?>
                                    <span class="text-[10px] text-emerald-600 font-bold">Siap</span>
                                <?php else: ?>
                                    <span class="text-[10px] text-gray-400">Menunggu bayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($orders === []): ?>
            <p class="p-8 text-center text-gray-400 text-sm">Tidak ada pesanan aktif untuk stan ini.</p>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
  document.querySelectorAll('.btn-ful').forEach(function (btn) {
    btn.addEventListener('click', async function () {
      const orderId = parseInt(btn.getAttribute('data-order'), 10);
      const status = btn.getAttribute('data-status');
      const res = await fetch(<?= json_encode($apiFulfillment, JSON_THROW_ON_ERROR) ?>, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ order_id: orderId, status: status }),
      });
      const data = await res.json();
      if (!data.ok) {
        alert(data.error || 'Gagal');
        return;
      }
      location.reload();
    });
  });
})();
</script>
