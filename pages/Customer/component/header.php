<?php
declare(strict_types=1);

/**
 * Shared header for Customer pages (changes by $pageKey).
 *
 * Expected variables from parent scope:
 * - $pageKey: string
 * - $customerContext: ?\App\Customer\CustomerContext
 * - $customerHasAccess: bool (optional, inferred from context)
 */

use App\Customer\CustomerContext;

$pageKeyLocal = isset($pageKey) ? (string) $pageKey : 'home';
$ctx = isset($customerContext) && $customerContext instanceof CustomerContext ? $customerContext : null;
?>

<?php
$titles = [
    'keranjang' => 'KERANJANG',
    'checkout' => 'RINGKASAN ORDER',
    'pilih-pembayaran' => 'METODE PEMBAYARAN',
    'bayar-kasir' => 'PEMBAYARAN DI KASIR',
    'bayar-qris' => 'PEMBAYARAN QRIS',
    'status-belum-bayar' => 'STATUS PESANAN',
    'status-sudah-bayar' => 'STATUS PESANAN',
    'struk' => 'STRUK PEMBAYARAN',
];

$backLinks = [
    'keranjang' => './index.php?page=home',
    'checkout' => './index.php?page=keranjang',
    'pilih-pembayaran' => './index.php?page=checkout',
    'bayar-kasir' => './index.php?page=pilih-pembayaran',
    'bayar-qris' => './index.php?page=pilih-pembayaran',
    'status-belum-bayar' => './index.php?page=home',
    'status-sudah-bayar' => './index.php?page=home',
    'struk' => './index.php?page=status-belum-bayar',
];

$title = $titles[$pageKeyLocal] ?? null;
$backHref = $backLinks[$pageKeyLocal] ?? './index.php?page=home';
?>

<?php if ($pageKeyLocal === 'need-scan') { ?>
    <header class="sticky top-0 z-30 flex items-center justify-center px-4 py-4 bg-white border-b border-gray-200 shadow-sm">
        <img src="https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80" alt="Logo" class="w-10 h-[46px] object-contain">
    </header>
<?php } elseif ($title !== null) { ?>
    <header class="sticky top-0 z-30 flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200">
        <?php if ($pageKeyLocal === 'bayar-qris') { ?>
            <div class="inline-flex items-center gap-3">
                <span class="font-plus-jakarta text-maroon font-extrabold tracking-[0.18em] text-sm"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php } else { ?>
            <a href="<?php echo htmlspecialchars($backHref, ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center gap-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="#800000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-plus-jakarta text-maroon font-extrabold tracking-[0.18em] text-sm"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        <?php } ?>

        <img src="https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80" alt="Logo" class="w-10 h-[46px] object-contain">
    </header>
<?php } else { ?>
    <header class="sticky top-0 z-30 flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center gap-1 px-3 py-1 rounded-full" style="background-color: rgba(128, 0, 0, 0.1);">
                <svg width="12" height="15" viewBox="0 0 12 15" fill="none" aria-hidden="true">
                    <path d="M6 7.5C6.4125 7.5 6.76562 7.35312 7.05937 7.05937C7.35312 6.76562 7.5 6.4125 7.5 6C7.5 5.5875 7.35312 5.23438 7.05937 4.94063C6.76562 4.64688 6.4125 4.5 6 4.5C5.5875 4.5 5.23438 4.64688 4.94063 4.94063C4.64688 5.23438 4.5 5.5875 4.5 6C4.5 6.4125 4.64688 6.76562 4.94063 7.05937C5.23438 7.35312 5.5875 7.5 6 7.5ZM6 13.0125C7.525 11.6125 8.65625 10.3406 9.39375 9.19687C10.1313 8.05312 10.5 7.0375 10.5 6.15C10.5 4.7875 10.0656 3.67188 9.19687 2.80312C8.32812 1.93437 7.2625 1.5 6 1.5C4.7375 1.5 3.67188 1.93437 2.80312 2.80312C1.93437 3.67188 1.5 4.7875 1.5 6.15C1.5 7.0375 1.86875 8.05312 2.60625 9.19687C3.34375 10.3406 4.475 11.6125 6 13.0125ZM6 15C3.9875 13.2875 2.48438 11.6969 1.49063 10.2281C0.496875 8.75937 0 7.4 0 6.15C0 4.275 0.603125 2.78125 1.80938 1.66875C3.01562 0.55625 4.4125 0 6 0C7.5875 0 8.98438 0.55625 10.1906 1.66875C11.3969 2.78125 12 4.275 12 6.15C12 7.4 11.5031 8.75937 10.5094 10.2281C9.51562 11.6969 8.0125 13.2875 6 15Z" fill="#800000" />
                </svg>
                <span class="text-maroon text-base leading-6">Meja <?php echo htmlspecialchars($ctx !== null ? $ctx->tableNumber : '?', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
        <img src="https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80" alt="Logo" class="w-10 h-[46px] object-contain">
    </header>
<?php } ?>
