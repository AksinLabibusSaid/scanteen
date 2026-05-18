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

$statusMapping = [
    'pending_payment' => 1,
    'paid'            => 2,
    'accepted'        => 2,
    'processing'      => 3,
    'ready'           => 4,
    'completed'       => 5,
];

$currentStep = $statusMapping[$ord['status'] ?? 'pending_payment'] ?? 1;

$steps = [
    1 => ['title' => 'Menunggu', 'desc' => (($ord['status'] ?? 'pending_payment') === 'pending_payment' ? 'Pesanan belum dibayar' : 'Pesanan telah dibayar')],
    2 => ['title' => 'Diterima', 'desc' => 'Pesanan terkirim'],
    3 => ['title' => 'Diproses', 'desc' => 'Pesanan sedang disiapkan'],
    4 => ['title' => 'Siap', 'desc' => 'Pesanan sudah siap diantarkan'],
    5 => ['title' => 'Selesai', 'desc' => 'Pesanan telah sampai'],
];

$warungStatusMapping = [
    'new' => ['label' => 'Menunggu', 'class' => 'bg-gray-50 text-gray-500 border-gray-200'],
    'accepted' => ['label' => 'Diterima', 'class' => 'bg-blue-50 text-blue-600 border-blue-100'],
    'processing' => ['label' => 'Diproses', 'class' => 'bg-yellow-50 text-yellow-600 border-yellow-100'],
    'ready' => ['label' => 'Siap', 'class' => 'bg-green-50 text-green-600 border-green-100'],
    'completed' => ['label' => 'Selesai', 'class' => 'bg-teal-50 text-teal-600 border-teal-100'],
];
?>

<!-- Scrollable Content -->
<main class="flex-1 flex flex-col gap-6 px-4 pt-5 pb-32">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl border border-[#F3F4F6] shadow-[0_4px_20px_rgba(0,0,0,0.03)] px-6 py-5 flex items-center justify-between">
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

    <!-- Stepper Section -->
    <div class="bg-white rounded-3xl border border-[#F3F4F6] shadow-[0_4px_20px_rgba(0,0,0,0.03)] px-6 pt-5 pb-6">
        <div class="flex items-center gap-2 mb-6">
            <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4 text-left">
                Status Pesanan
            </span>
        </div>

        <div class="flex flex-col">
            <?php foreach ($steps as $num => $step) {
                $isCompleted = $num < $currentStep;
                $isCurrent = $num === $currentStep;
                $isLast = $num === count($steps);
                
                $circleColor = $isCompleted || $isCurrent ? 'border-[#7B0009] text-[#7B0009]' : 'border-gray-300 text-gray-400';
                $titleColor = $isCompleted || $isCurrent ? 'text-[#7B0009]' : 'text-gray-400';
                $descColor = $isCompleted || $isCurrent ? 'text-gray-500' : 'text-gray-300';
                $lineColor = $isCompleted ? 'bg-[#7B0009]' : 'bg-gray-200';
            ?>
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center font-bold text-xs bg-white <?php echo $circleColor; ?> z-10">
                        <?php if ($isCompleted) { ?>
                            <svg class="w-3 h-3 text-[#7B0009]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        <?php } else { ?>
                            <?php echo $num; ?>
                        <?php } ?>
                    </div>
                    <?php if (!$isLast) { ?>
                    <div class="w-0.5 flex-1 <?php echo $lineColor; ?> my-1"></div>
                    <?php } ?>
                </div>
                <div class="pb-6">
                    <p class="font-bold text-sm leading-none <?php echo $titleColor; ?>"><?php echo $step['title']; ?></p>
                    <p class="text-xs mt-1 <?php echo $descColor; ?>"><?php echo $step['desc']; ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Detail Menu Section -->
    <div class="flex flex-col gap-3">
        <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4 px-2 text-left">
            Detail Menu
        </span>

        <?php foreach ($groups as $g) { 
            $wStatus = $g['status'] ?? 'new';
            $sMap = $warungStatusMapping[$wStatus] ?? ['label' => strtoupper($wStatus), 'class' => 'bg-gray-50 text-gray-500 border-gray-200'];
        ?>
        <div class="bg-white rounded-3xl border border-[#F3F4F6] shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#F3F4F6] bg-[#FAFAF9]">
                <span class="font-inter text-[#261817] text-sm font-bold leading-6"><?php echo htmlspecialchars((string) $g['warung_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="text-[10px] font-bold tracking-wide rounded-lg px-2 py-1 border <?php echo $sMap['class']; ?>"><?php echo $sMap['label']; ?></span>
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
                    <?php if (($it['note'] ?? '-') !== '-') { ?>
                    <div class="pl-6">
                        <span class="inline-block text-[#59413E] text-xs font-normal leading-4 border border-[#E5E7EB] rounded-lg px-2 py-0.5 bg-white">
                            <?php echo htmlspecialchars((string) $it['note'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div class="bg-white rounded-3xl border border-[#F3F4F6] shadow-[0_4px_20px_rgba(0,0,0,0.03)] flex items-center justify-between px-6 py-5 mt-2">
            <span class="text-[#261817] text-sm font-bold leading-6">Total Pembayaran</span>
            <span class="font-inter text-[#7B0009] text-lg font-black leading-7"><?php echo htmlspecialchars($totalFmt, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    </div>

</main>

<!-- Bottom Button -->
<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="bg-transparent rounded-3xl px-5 py-4">
        <button class="w-full py-4 rounded-2xl bg-[#7B0009] flex items-center justify-center gap-2 shadow-[0_4px_12px_rgba(123,0,9,0.2)] hover:bg-[#6a0007] transition-all active:scale-[0.98]" onclick="window.location.href='./index.php?page=home'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            <span class="font-inter text-white text-sm font-bold leading-6">
                Tambah Pesanan Baru
            </span>
        </button>
    </div>
</div>

<?php if (($ord['status'] ?? '') === 'completed'): ?>
<script>
    setTimeout(function() {
        window.location.href = './index.php?page=home';
    }, 60000); // 1 menit (60 detik)
</script>
<?php endif; ?>
