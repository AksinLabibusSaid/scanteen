<?php
declare(strict_types=1);

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php');
    exit;
}

$scanError = $_SESSION['scan_error'] ?? null;
$scanErrorType = $_SESSION['scan_error_type'] ?? null;
$scanErrorTable = $_SESSION['scan_error_table'] ?? '';

unset($_SESSION['scan_error'], $_SESSION['scan_error_type'], $_SESSION['scan_error_table']); // Clear after read
?>

<?php if ($scanErrorType === 'inactivity'): ?>
<!-- INACTIVITY TIMEOUT SCREEN -->
<main class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center gap-6 animate-fade-in">
    <div class="relative w-24 h-24 rounded-full bg-amber-50 flex items-center justify-center shadow-inner">
        <!-- Animated hourglass SVG -->
        <svg class="w-12 h-12 text-[#B45309]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="absolute -top-1 -right-1 flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-amber-500"></span>
        </span>
    </div>
    <div class="space-y-3">
        <h1 class="text-2xl font-extrabold text-[#7B0009] tracking-tight poppins">Sesi Aktivitas Berakhir</h1>
        <p class="text-stone-600 text-sm max-w-sm mx-auto leading-relaxed">
            Sesi belanja Anda telah ditutup otomatis karena **tidak ada aktivitas selama 5 menit**.
        </p>
        <div class="bg-stone-50 border border-stone-100 rounded-2xl p-4 max-w-sm mx-auto text-left mt-2">
            <p class="text-xs text-stone-500 leading-relaxed">
                <strong class="text-stone-700">Kenapa ini terjadi?</strong> Untuk kenyamanan bersama, sistem otomatis melepaskan status meja agar pengunjung lain dapat memesan jika tidak ada interaksi di browser Anda.
            </p>
        </div>
    </div>
    <div class="w-full max-w-xs mt-4">
        <a href="./index.php" class="inline-block w-full py-4 bg-[#7B0009] text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-red-900/20 hover:bg-[#6a0007] transition-all active:scale-[0.98]">
            Scan Ulang Meja
        </a>
    </div>
</main>

<?php elseif ($scanErrorType === 'occupied'): ?>
<!-- SIMULTANEOUS SCAN / OCCUPIED BLOCK SCREEN -->
<main class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center gap-6 animate-fade-in">
    <div class="relative w-24 h-24 rounded-full bg-red-50 flex items-center justify-center shadow-inner">
        <!-- Premium lock SVG -->
        <svg class="w-12 h-12 text-[#BA1A1A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <span class="absolute -top-1 -right-1 flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
        </span>
    </div>
    <div class="space-y-3">
        <h1 class="text-2xl font-extrabold text-[#7B0009] tracking-tight poppins">Meja Sedang Digunakan</h1>
        <p class="text-stone-600 text-sm max-w-sm mx-auto leading-relaxed">
            Meja <strong class="text-[#7B0009]">Meja <?= htmlspecialchars($scanErrorTable) ?></strong> telah di-scan dan sedang aktif digunakan oleh perangkat/pelanggan lain.
        </p>
        <div class="bg-red-50/50 border border-red-100 rounded-2xl p-4 max-w-sm mx-auto text-left mt-2">
            <p class="text-xs text-stone-600 leading-relaxed">
                <strong class="text-red-700">Penting:</strong> Demi keamanan transaksi dan ketepatan pemesanan, akses meja dibatasi **hanya untuk 1 perangkat** yang aktif.
            </p>
            <p class="text-[10px] text-stone-400 leading-relaxed mt-2 pt-2 border-t border-red-100">
                * Jika meja ini kosong secara fisik tetapi keterangannya terisi, meja akan **otomatis kosong kembali setelah 5 menit** tidak ada aktivitas. Atau hubungi staf kantin untuk melakukan **Clear Meja**.
            </p>
        </div>
    </div>
    <div class="w-full max-w-xs mt-4 flex flex-col gap-2">
        <a href="./index.php" class="inline-block w-full py-4 bg-[#7B0009] text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-red-900/20 hover:bg-[#6a0007] transition-all active:scale-[0.98]">
            Scan Ulang Meja Lain
        </a>
    </div>
</main>

<?php else: ?>
<!-- NORMAL SCAN INSTRUCTIONS SCREEN -->
<main class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center gap-6 animate-fade-in">
    <div class="w-20 h-20 rounded-full bg-[#800000]/10 flex items-center justify-center shadow-inner">
        <svg class="w-10 h-10 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
    </div>
    <div class="space-y-2">
        <h1 class="text-2xl font-bold text-[#570000] poppins">Scan QR Meja</h1>
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
<?php endif; ?>
