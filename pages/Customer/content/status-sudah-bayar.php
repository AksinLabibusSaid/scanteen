<?php

declare(strict_types=1);

use App\Support\Money;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=status-sudah-bayar');
    exit;
}

$ord = $customerOrder ?? null;
$groups = $customerOrderGroups ?? [];
$orderNum = htmlspecialchars((string) ($ord['order_number'] ?? '-'), ENT_QUOTES, 'UTF-8');
$tableNum = htmlspecialchars((string) ($customerContext->tableNumber ?? '?'), ENT_QUOTES, 'UTF-8');
$totalFmt = Money::formatIdr((float) ($ord['total'] ?? 0));
?>

<!-- Scrollable Content -->
<main class="flex-1 flex flex-col gap-5 px-4 pt-5 pb-32">

    <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] px-5 py-5 flex items-center justify-between">
        <div class="flex flex-col gap-1 text-left">
            <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4">
                ID PESANAN
            </span>
            <span class="font-inter text-[#261817] text-xl font-bold leading-7">
                <?php echo $orderNum; ?>
            </span>
        </div>
        <div class="flex flex-col items-center justify-center bg-[#7B0009] rounded-xl px-3 py-2 min-w-[52px]">
            <span class="text-white text-[10px] font-semibold tracking-wider uppercase leading-none">MEJA</span>
            <span class="font-inter text-white text-xl font-black leading-tight"><?php echo $tableNum; ?></span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] px-5 pt-5 pb-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4 text-left">
                Status Pesanan
            </span>
        </div>
        <p class="text-sm text-[#59413E]">Pembayaran tercatat. Warung akan mengonfirmasi pesanan Anda.</p>
    </div>

    <div class="flex flex-col gap-3">
        <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4 px-1 text-left">
            Detail Menu
        </span>

        <?php foreach ($groups as $g) { ?>
        <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#F3F4F6]">
                <span class="font-inter text-[#261817] text-base font-bold leading-6"><?php echo htmlspecialchars((string) $g['warung_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="text-[#7B0009] text-xs font-semibold tracking-wide bg-[#FEF2F2] border border-[#FEE2E2] rounded px-2 py-0.5">PAID</span>
            </div>
            <div class="flex flex-col gap-4 px-5 py-4">
                <?php foreach ($g['items'] as $it) {
                    $unit = Money::formatIdr((float) $it['unit_price']);
                    ?>
                <div class="flex flex-col gap-1.5 text-left">
                    <div class="flex items-baseline gap-2">
                        <span class="text-[#7B0009] text-sm font-bold leading-5 flex-shrink-0"><?php echo (int) $it['quantity']; ?>x</span>
                        <span class="text-[#261817] text-sm font-semibold leading-5"><?php echo htmlspecialchars((string) $it['menu_name_snapshot'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <p class="text-[#675C5C] text-xs font-normal leading-4 pl-6"><?php echo htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?> / porsi</p>
                    <div class="pl-6">
                        <span class="inline-block text-[#59413E] text-xs font-normal leading-4 border border-[#E5E7EB] rounded px-2 py-0.5 bg-white">
                            <?php echo htmlspecialchars((string) ($it['note'] ?? '-') ?: '-', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div class="flex items-center justify-between px-1 pt-2 pb-1">
            <span class="text-[#261817] text-base font-bold leading-6">Total Pembayaran</span>
            <span class="font-inter text-[#7B0009] text-lg font-black leading-7"><?php echo htmlspecialchars($totalFmt, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    </div>

</main>

<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="bg-[#FAF9F6] rounded-3xl px-5 py-4 shadow-[0_-4px_30px_rgba(0,0,0,0.08)] border border-[#EFEEEB]">
        <button class="w-full py-4 rounded-2xl bg-[#7B0009] flex items-center justify-center gap-2 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.10)] hover:bg-[#6a0007] transition-all active:scale-[0.98]" onclick="window.location.href='./index.php?page=home'">
            <span class="font-inter text-white text-base font-bold leading-6">
                Tambah Pesanan Baru
            </span>
        </button>
    </div>
</div>
