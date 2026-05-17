<?php
declare(strict_types=1);

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php');
    exit;
}
?>

<main class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center gap-6">
    <div class="w-20 h-20 rounded-full bg-[#800000]/10 flex items-center justify-center">
        <svg class="w-10 h-10 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
    </div>
    <div class="space-y-2">
        <h1 class="text-2xl font-bold text-[#570000]">Scan QR meja</h1>
        <p class="text-[#5F5E5B] text-base max-w-sm mx-auto leading-relaxed">
            Untuk memesan, arahkan kamera ponsel ke QR code yang terpasang di meja Anda. Setelah terbaca, Anda akan masuk ke menu kantin untuk meja tersebut.
        </p>
    </div>
    <?php if (defined('SCANTEEN_CUSTOMER_SIMULATE_PAYMENT') && SCANTEEN_CUSTOMER_SIMULATE_PAYMENT === true) { ?>
        <p class="text-xs text-gray-400 max-w-xs">
            Mode demo: tautan contoh dengan token seed database —
            <a class="text-[#800000] font-semibold underline" href="./index.php?t=TBL-db5d0835&amp;page=home">buka meja demo (Meja 1)</a>
        </p>
    <?php } ?>
    <p class="text-xs text-stone-400 max-w-xs">
        Admin / kasir / warung:
        <a class="text-[#800000] font-semibold underline" href="<?= htmlspecialchars(\App\Support\PublicUrl::staffLoginPath(), ENT_QUOTES, 'UTF-8') ?>">login staff</a>
    </p>
</main>
