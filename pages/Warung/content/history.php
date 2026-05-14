<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$warungId = StaffAuth::warungId();
$rows = [];
if ($warungId !== null) {
    $rows = (new OrderListRepository())->listCompletedForWarung($venueId, $warungId, 150);
}
?>

<div class="flex flex-col gap-5">
    <h1 class="text-2xl font-bold text-gray-900">Riwayat selesai</h1>
    <p class="text-sm text-gray-500">Pesanan berstatus selesai yang berisi item dari stan Anda.</p>

    <?php if ($warungId === null): ?>
        <p class="text-red-600 text-sm">Akun tidak terhubung ke stan.</p>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-gray-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Meja</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3 text-right">Total order</th>
                        <th class="px-4 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs font-bold"><?= htmlspecialchars((string) $r['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars((string) $r['table_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars((string) ($r['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right font-semibold"><?= htmlspecialchars(Money::formatIdr((float) $r['total']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-xs text-gray-500"><?= htmlspecialchars((string) $r['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($rows === []): ?>
                <p class="p-8 text-center text-gray-400">Belum ada riwayat.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
