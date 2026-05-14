<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$fStatus = isset($_GET['st']) ? trim((string) $_GET['st']) : '';
$orders = (new OrderListRepository())->listForVenueFiltered(
    $venueId,
    250,
    $fStatus !== '' ? $fStatus : null,
    null,
    null,
    null,
    null,
);
?>

<div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat pesanan</h1>
    </div>

    <form method="get" class="flex flex-wrap gap-2 items-end text-sm">
        <input type="hidden" name="page" value="history">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="st" class="rounded-xl border border-gray-200 px-3 py-2" onchange="this.form.submit()">
                <option value="">Semua</option>
                <?php foreach (['pending_payment','paid','completed','cancelled'] as $st): ?>
                    <option value="<?= $st ?>" <?= $fStatus === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-400 uppercase">
                <tr>
                    <th class="px-4 py-3">No.</th>
                    <th class="px-4 py-3">Meja</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($orders as $o): ?>
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3 font-mono text-xs font-bold"><?= htmlspecialchars((string) $o['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) $o['table_number'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) ($o['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right font-semibold"><?= htmlspecialchars(Money::formatIdr((float) $o['total']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-xs"><?= htmlspecialchars((string) $o['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($orders === []): ?>
            <p class="p-8 text-center text-gray-400">Kosong.</p>
        <?php endif; ?>
    </div>
</div>
