<?php
declare(strict_types=1);

use App\Customer\OrderUi;
use App\Support\Money;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=bayar-qris');
    exit;
}

$ord = $customerOrder ?? null;
$tok = $ord !== null ? (string) $ord['public_token'] : '';
$secs = $ord !== null ? OrderUi::countdownSeconds($ord['payment_deadline_at'] !== null ? (string) $ord['payment_deadline_at'] : null) : 900;
$totalFmt = Money::formatIdr((float) ($ord['total'] ?? 0));
$orderNum = htmlspecialchars((string) ($ord['order_number'] ?? ''), ENT_QUOTES, 'UTF-8');
$statusHref = './index.php?page=status-belum-bayar&o=' . rawurlencode($tok);
$strukHref = './index.php?page=struk&o=' . rawurlencode($tok);
?>

<!-- Scrollable content -->
<main class="flex-1 flex flex-col gap-6 px-4 pt-5 pb-32">
    <!-- Countdown banner -->
    <div class="flex items-center justify-center gap-2 px-4 py-2 bg-[#800000] rounded-lg">
        <svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 1.66667V0H10V1.66667H5ZM6.66667 10.8333H8.33333V5.83333H6.66667V10.8333ZM7.5 17.5C6.47222 17.5 5.50347 17.3021 4.59375 16.9062C3.68403 16.5104 2.88889 15.9722 2.20833 15.2917C1.52778 14.6111 0.989583 13.816 0.59375 12.9062C0.197917 11.9965 0 11.0278 0 10C0 8.97222 0.197917 8.00347 0.59375 7.09375C0.989583 6.18403 1.52778 5.38889 2.20833 4.70833C2.88889 4.02778 3.68403 3.48958 4.59375 3.09375C5.50347 2.69792 6.47222 2.5 7.5 2.5C8.36111 2.5 9.1875 2.63889 9.97917 2.91667C10.7708 3.19444 11.5139 3.59722 12.2083 4.125L13.375 2.95833L14.5417 4.125L13.375 5.29167C13.9028 5.98611 14.3056 6.72917 14.5833 7.52083C14.8611 8.3125 15 9.13889 15 10C15 11.0278 14.8021 11.9965 14.4062 12.9062C14.0104 13.816 13.4722 14.6111 12.7917 15.2917C12.1111 15.9722 11.316 16.5104 10.4062 16.9062C9.49653 17.3021 8.52778 17.5 7.5 17.5ZM7.5 15.8333C9.11111 15.8333 10.4861 15.2639 11.625 14.125C12.7639 12.9861 13.3333 11.6111 13.3333 10C13.3333 8.38889 12.7639 7.01389 11.625 5.875C10.4861 4.73611 9.11111 4.16667 7.5 4.16667C5.88889 4.16667 4.51389 4.73611 3.375 5.875C2.23611 7.01389 1.66667 8.38889 1.66667 10C1.66667 11.6111 2.23611 12.9861 3.375 14.125C4.51389 15.2639 5.88889 15.8333 7.5 15.8333Z" fill="white"/>
        </svg>
        <span class="text-sm text-white font-semibold">
            Selesaikan pembayaran dalam
            <span id="countdown-qris" class="font-bold underline ml-1" data-countdown-seconds="<?php echo (int) $secs; ?>">00:00</span>
        </span>
    </div>

    <!-- Payment Code Card -->
    <div id="payment-card" class="bg-white rounded-2xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col gap-6 p-6">
        <!-- Order Metadata -->
        <div class="flex justify-between items-start pt-2">
            <div class="flex flex-col gap-1">
                <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4">
                    ORDER ID
                </span>
                <span class="font-inter text-[#261817] text-base font-bold leading-6">
                    <?php echo $orderNum !== '' ? $orderNum : '-'; ?>
                </span>
            </div>
            <div class="flex flex-col gap-1 items-end">
                <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4">
                    TOTAL BAYAR
                </span>
                <span class="font-inter text-[#7B0009] text-base font-semibold leading-6">
                    <?php echo htmlspecialchars($totalFmt, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
        </div>

        <!-- QR Code -->
        <div class="flex justify-center">
            <div class="p-2 rounded-2xl border-2 border-[#7B0009] bg-white">
                <img
                    src="https://api.builder.io/api/v1/image/assets/TEMP/a52bb22c7583c816363d4ce1630f1ce1e49ff9f3?width=512"
                    alt="QRIS QR Code"
                    class="w-64 h-64 rounded-lg object-cover"
                />
            </div>
        </div>

        <!-- Scan instruction -->
        <div class="flex items-center justify-center gap-2">
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 4.16667V0H4.16667V1.66667H1.66667V4.16667H0ZM0 16.6667V12.5H1.66667V15H4.16667V16.6667H0ZM12.5 16.6667V15H15V12.5H16.6667V16.6667H12.5ZM15 4.16667V1.66667H12.5V0H16.6667V4.16667H15ZM12.9167 12.9167H14.1667V14.1667H12.9167V12.9167ZM12.9167 10.4167H14.1667V11.6667H12.9167V10.4167ZM11.6667 11.6667H12.9167V12.9167H11.6667V11.6667ZM10.4167 12.9167H11.6667V14.1667H10.4167V12.9167ZM9.16667 11.6667H10.4167V12.9167H9.16667V11.6667ZM11.6667 9.16667H12.9167V10.4167H11.6667V9.16667ZM10.4167 10.4167H11.6667V11.6667H10.4167V10.4167ZM9.16667 9.16667H10.4167V10.4167H9.16667V9.16667ZM14.1667 2.5V7.5H9.16667V2.5H14.1667ZM7.5 9.16667V14.1667H2.5V9.16667H7.5ZM7.5 2.5V7.5H2.5V2.5H7.5ZM6.25 12.9167V10.4167H3.75V12.9167H6.25ZM6.25 6.25V3.75H3.75V6.25H6.25ZM12.9167 6.25V3.75H10.4167V6.25H12.9167Z" fill="#675C5C"/>
            </svg>
            <p class="text-[#59413E] text-sm font-medium leading-5 text-center">
                Scan QR menggunakan aplikasi pembayaran
            </p>
        </div>

        <!-- Red divider -->
        <div class="h-1 bg-[#7B0009] -mx-6" />
    </div>

    <!-- Instructions Section -->
    <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] flex flex-col gap-6 px-6 pt-8 pb-6">
        <div class="flex items-center gap-2">
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.5 12.5H9.16667V7.5H7.5V12.5ZM8.33333 5.83333C8.56944 5.83333 8.76736 5.75347 8.92708 5.59375C9.08681 5.43403 9.16667 5.23611 9.16667 5C9.16667 4.76389 9.08681 4.56597 8.92708 4.40625C8.76736 4.24653 8.56944 4.16667 8.33333 4.16667C8.09722 4.16667 7.89931 4.24653 7.73958 4.40625C7.57986 4.56597 7.5 4.76389 7.5 5C7.5 5.23611 7.57986 5.43403 7.73958 5.59375C7.89931 5.75347 8.09722 5.83333 8.33333 5.83333ZM8.33333 16.6667C7.18056 16.6667 6.09722 16.4479 5.08333 16.0104C4.06944 15.5729 3.1875 14.9792 2.4375 14.2292C1.6875 13.4792 1.09375 12.5972 0.65625 11.5833C0.21875 10.5694 0 9.48611 0 8.33333C0 7.18056 0.21875 6.09722 0.65625 5.08333C1.09375 4.06944 1.6875 3.1875 2.4375 2.4375C3.1875 1.6875 4.06944 1.09375 5.08333 0.65625C6.09722 0.21875 7.18056 0 8.33333 0C9.48611 0 10.5694 0.21875 11.5833 0.65625C12.5972 1.09375 13.4792 1.6875 14.2292 2.4375C14.9792 3.1875 15.5729 4.06944 16.0104 5.08333C16.4479 6.09722 16.6667 7.18056 16.6667 8.33333C16.6667 9.48611 16.4479 10.5694 16.0104 11.5833C15.5729 12.5972 14.9792 13.4792 14.2292 14.2292C13.4792 14.9792 12.5972 15.5729 11.5833 16.0104C10.5694 16.4479 9.48611 16.6667 8.33333 16.6667ZM8.33333 15C10.1944 15 11.7708 14.3542 13.0625 13.0625C14.3542 11.7708 15 10.1944 15 8.33333C15 6.47222 14.3542 4.89583 13.0625 3.60417C11.7708 2.3125 10.1944 1.66667 8.33333 1.66667C6.47222 1.66667 4.89583 2.3125 3.60417 3.60417C2.3125 4.89583 1.66667 6.47222 1.66667 8.33333C1.66667 10.1944 2.3125 11.7708 3.60417 13.0625C4.89583 14.3542 6.47222 15 8.33333 15Z" fill="#7B0009"/>
            </svg>
            <span class="text-[#261817] text-sm font-semibold leading-5">
                Langkah Pembayaran
            </span>
        </div>

        <ul class="flex flex-col gap-6">
            <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-6 h-6 rounded-full border border-[#FEE2E2] bg-[#FEF2F2] flex items-center justify-center">
                    <span class="text-[#7B0009] text-xs font-bold leading-[18px]">1</span>
                </div>
                <p class="text-[#59413E] text-sm font-normal leading-5 flex-1">
                    Buka aplikasi pembayaran pilihan Anda (Gopay, OVO, Dana, dll).
                </p>
            </li>
            <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-6 h-6 rounded-full border border-[#FEE2E2] bg-[#FEF2F2] flex items-center justify-center">
                    <span class="text-[#7B0009] text-xs font-bold leading-[18px]">2</span>
                </div>
                <p class="text-[#59413E] text-sm font-normal leading-5 flex-1">
                    Pilih menu <strong class="font-semibold text-[#261817]">'Bayar'</strong> atau <strong class="font-semibold text-[#261817]">'Scan QR'</strong>.
                </p>
            </li>
            <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-6 h-6 rounded-full border border-[#FEE2E2] bg-[#FEF2F2] flex items-center justify-center">
                    <span class="text-[#7B0009] text-xs font-bold leading-[18px]">3</span>
                </div>
                <p class="text-[#59413E] text-sm font-normal leading-5 flex-1">
                    Arahkan kamera ponsel Anda ke Kode QR di atas.
                </p>
            </li>
            <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-6 h-6 rounded-full border border-[#FEE2E2] bg-[#FEF2F2] flex items-center justify-center">
                    <span class="text-[#7B0009] text-xs font-bold leading-[18px]">4</span>
                </div>
                <p class="text-[#59413E] text-sm font-normal leading-5 flex-1">
                    Periksa jumlah pembayaran dan detail pesanan Anda.
                </p>
            </li>
            <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-6 h-6 rounded-full border border-[#FEE2E2] bg-[#FEF2F2] flex items-center justify-center">
                    <span class="text-[#7B0009] text-xs font-bold leading-[18px]">5</span>
                </div>
                <p class="text-[#59413E] text-sm font-normal leading-5 flex-1">
                    Masukkan PIN Anda untuk menyelesaikan transaksi.
                </p>
            </li>
        </ul>
    </div>
</main>

<!-- Bottom Action Bar -->
<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="flex items-center gap-3 bg-[#FAF9F6] rounded-3xl px-5 py-4 shadow-[0_-4px_30px_rgba(0,0,0,0.08)] border border-[#EFEEEB]">
        <button class="flex-1 py-4 rounded-2xl bg-[#7B0009] flex items-center justify-center shadow-[0_4px_6px_-1px_rgba(0,0,0,0.10)] hover:bg-[#6a0007] transition-all active:scale-[0.98]" onclick="window.location.href='<?php echo htmlspecialchars($statusHref, ENT_QUOTES, 'UTF-8'); ?>'">
            <span class="font-inter text-white text-base font-bold leading-6">
                Status pesanan
            </span>
        </button>
        <button id="btn-download" class="w-14 h-14 flex-shrink-0 rounded-2xl border-2 border-[#9E1C1C] flex items-center justify-center hover:bg-gray-50 transition-all active:scale-[0.95]" title="Download">
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.48046 12.3109L2.80216 6.63259L4.66849 4.74452L7.15544 7.24561V0H9.80548V7.24561L12.2924 4.74452L14.1588 6.63259L8.48046 12.3109ZM2.65003 16.9609C1.91162 16.9609 1.28534 16.7039 0.771205 16.1897C0.257068 15.6756 0 15.0493 0 14.3109V11.3109H2.65003V14.3109H14.3109V11.3109H16.9609V14.3109C16.9609 15.0493 16.7039 15.6756 16.1897 16.1897C15.6756 16.7039 15.0493 16.9609 14.3109 16.9609H2.65003Z" fill="#9E1C1C"/>
            </svg>
        </button>
    </div>
</div>

<!-- html2canvas for downloading card -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.getElementById('btn-download').addEventListener('click', function() {
    const card = document.getElementById('payment-card');
    html2canvas(card, {
        scale: 2, // Higher quality
        useCORS: true // Allow loading cross-origin images
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'Pembayaran-<?php echo $orderNum; ?>.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
});
</script>

