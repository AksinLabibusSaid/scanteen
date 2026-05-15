<?php

declare(strict_types=1);

use App\Support\Money;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=struk');
    exit;
}

$ord = $customerOrder ?? null;
$groups = $customerOrderGroups ?? [];
$pm = (string) ($ord['payment_method'] ?? '');
$payLabel = match ($pm) {
    'qris' => 'QRIS',
    'midtrans' => 'Midtrans',
    default => 'Bayar di kasir',
};
$dineLabel = (($ord['dining_type'] ?? '') === 'take_away') ? 'Take away' : 'Dine-in';
$created = $ord['created_at'] ?? '';
try {
    $dt = new DateTimeImmutable((string) $created, new DateTimeZone(date_default_timezone_get()));
    $dateStr = $dt->format('d M Y, H:i');
} catch (\Exception) {
    $dateStr = (string) $created;
}
?>

<!-- Receipt Container -->
<main class="flex-1 flex flex-col items-center px-6 pt-6 pb-20 bg-[#F9FAFB]">
    <div class="w-full max-w-[380px] bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100 flex flex-col">

        <div class="flex flex-col items-center pt-10 pb-6 px-8 text-center">
            <div class="w-16 h-16 mb-4">
                <img src="https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80" alt="Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-[#800000] text-sm font-bold tracking-[0.1em] uppercase">Scanteen</h1>
            <p class="text-[#6B7280] text-[10px] font-medium tracking-[0.05em] uppercase mt-1"><?php echo htmlspecialchars((string) ($customerContext->venueName ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <div class="px-8">
            <div class="border-t-2 border-dashed border-gray-200"></div>
        </div>

        <div class="px-8 py-6 flex flex-col gap-3">
            <h2 class="text-[#261817] text-[11px] font-bold tracking-wider uppercase">Informasi Pelanggan</h2>
            <div class="flex justify-between items-baseline">
                <span class="text-gray-500 text-[11px] uppercase font-bold">Nama</span>
                <span class="text-[#1A1C1A] text-sm font-bold"><?php echo htmlspecialchars((string) ($ord['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-gray-500 text-[11px] uppercase font-bold">Email</span>
                <span class="text-[#1A1C1A] text-sm font-semibold"><?php echo htmlspecialchars((string) ($ord['customer_email'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="px-8 py-4 flex flex-col gap-3">
            <h2 class="text-[#261817] text-[11px] font-bold tracking-wider uppercase">Detail Pesanan</h2>
            <div class="flex justify-between items-baseline">
                <span class="text-gray-500 text-[11px] uppercase font-bold">ID Pesanan</span>
                <span class="text-[#1A1C1A] text-sm font-black"><?php echo htmlspecialchars((string) ($ord['order_number'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-gray-500 text-[11px] uppercase font-bold">Tanggal</span>
                <span class="text-[#1A1C1A] text-[13px] font-medium"><?php echo htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-gray-500 text-[11px] uppercase font-bold">Lokasi</span>
                <span class="text-[#1A1C1A] text-[13px] font-medium">Meja <?php echo htmlspecialchars((string) ($customerContext->tableNumber ?? '?'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-gray-500 text-[11px] uppercase font-bold">Tipe Pesanan</span>
                <span class="text-[#1A1C1A] text-[10px] font-bold uppercase"><?php echo htmlspecialchars($dineLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-gray-500 text-[11px] uppercase font-bold">Metode Bayar</span>
                <span class="text-[#1A1C1A] text-[13px] font-bold uppercase"><?php echo htmlspecialchars($payLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="px-8 py-2">
            <div class="border-t-2 border-dashed border-gray-200"></div>
        </div>

        <div class="px-8 py-4 flex flex-col gap-5">
            <h2 class="text-[#261817] text-[11px] font-bold tracking-wider uppercase border-b border-gray-100 pb-2">Daftar Pesanan</h2>

            <?php foreach ($groups as $g) { ?>
            <div class="flex flex-col gap-4">
                <h3 class="text-[#261817] text-[13px] font-bold uppercase tracking-tight"><?php echo htmlspecialchars((string) $g['warung_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <?php foreach ($g['items'] as $it) {
                    $lineTotal = Money::formatIdr((float) $it['line_subtotal']);
                    $unit = Money::formatIdr((float) $it['unit_price']);
                    ?>
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between font-bold text-sm text-[#1A1C1A]">
                        <span><?php echo htmlspecialchars((string) $it['menu_name_snapshot'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span><?php echo htmlspecialchars($lineTotal, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="text-[11px] text-gray-500 flex flex-col">
                        <span><?php echo (int) $it['quantity']; ?>x <?php echo htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="italic mt-0.5 text-gray-400"><?php echo htmlspecialchars((string) ($it['note'] ?? '') ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>

        <div class="px-8 py-2">
            <div class="border-t-2 border-dashed border-gray-200"></div>
        </div>

        <div class="px-8 py-6 flex flex-col gap-2">
            <div class="flex justify-between text-base text-[#261817] font-black mt-2 pt-2 border-t border-gray-200">
                <span class="uppercase tracking-wider">Total Akhir</span>
                <span><?php echo htmlspecialchars(Money::formatIdr((float) ($ord['total'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="flex flex-col items-center px-8 py-10 text-center gap-6">
            <p class="text-[11px] text-gray-500 font-medium">Terima kasih atas pesanan Anda!</p>
        </div>
    </div>

    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-30 print-hidden">
        <button onclick="window.print()" class="bg-[#800000] text-white px-6 py-3 rounded-full shadow-lg font-bold flex items-center gap-2 hover:bg-[#6a0000] transition-colors">
            Cetak Struk
        </button>
    </div>
</main>

<script>
window.addEventListener('load', () => {
    // Memberikan waktu sedikit untuk rendering font/gambar
    setTimeout(() => {
        window.print();
    }, 500);
});
</script>

<style>
@media print {
    body * { visibility: hidden; background: white !important; }
    main, main * { visibility: visible; }
    main { position: absolute; left: 0; top: 0; width: 100%; padding: 0 !important; }
    .fixed { display: none !important; }
    header { display: none !important; }
}
</style>
