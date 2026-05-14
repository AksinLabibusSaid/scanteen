<?php
declare(strict_types=1);

use App\Repositories\VenueStatsRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$statsRepo = new VenueStatsRepository();
$from = isset($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $_GET['from']) ? (string) $_GET['from'] : date('Y-m-01');
$to = isset($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $_GET['to']) ? (string) $_GET['to'] : date('Y-m-d');
if ($from > $to) {
    $t = $from;
    $from = $to;
    $to = $t;
}
$sum = $statsRepo->summaryBetween($venueId, $from, $to);
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan kasir</h2>
    <p class="text-sm text-gray-500 mt-1">Ringkasan omzet venue (pesanan sudah bayar / selesai).</p>
</div>

<form method="get" class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-wrap gap-3 items-end mb-4">
    <input type="hidden" name="page" value="reports">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Dari</label>
        <input type="date" name="from" value="<?= htmlspecialchars($from, ENT_QUOTES, 'UTF-8') ?>" class="rounded-lg border px-2 py-1.5 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai</label>
        <input type="date" name="to" value="<?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?>" class="rounded-lg border px-2 py-1.5 text-sm">
    </div>
    <button type="submit" class="px-4 py-2 rounded-xl bg-[#7F1D1D] text-white text-xs font-bold">Tampilkan</button>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <p class="text-xs font-bold text-gray-500 uppercase">Pendapatan</p>
        <p class="text-2xl font-black text-[#991B1B] mt-2"><?= htmlspecialchars(Money::formatIdr($sum['revenue']), ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <p class="text-xs font-bold text-gray-500 uppercase">Pesanan</p>
        <p class="text-2xl font-black mt-2"><?= (int) $sum['orders'] ?></p>
    </div>
</div>
