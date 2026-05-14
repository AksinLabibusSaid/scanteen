<?php
declare(strict_types=1);

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=pilih-pembayaran');
    exit;
}
?>
<main class="flex-1 flex flex-col px-4 pt-6 pb-[105px] gap-6">
    <div class="flex flex-col gap-3">
        <h2 class="text-[18px] font-semibold text-[#800000] leading-[33.6px]">Metode Pembayaran</h2>
        <p class="text-xs text-[#5F5E5B]">Hanya <strong>QRIS</strong> atau <strong>Bayar di Kasir</strong>.</p>

        <div class="flex flex-col gap-3">
            <button type="button" class="payment-option group flex items-center justify-between w-full p-6 rounded-3xl bg-white transition-all border-2 border-[#800000] shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] cursor-pointer hover:shadow-[0_6px_24px_0_rgba(0,0,0,0.08)]" data-method="qris">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-[#800000] transition-colors">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M10 18V16H12V18H10ZM8 16V11H10V16H8ZM16 13V9H18V13H16ZM14 9V7H16V9H14ZM2 11V9H4V11H2ZM0 9V7H2V9H0ZM9 2V0H11V2H9ZM1.5 4.5H4.5V1.5H1.5V4.5ZM0 6V0H6V6H0ZM1.5 16.5H4.5V13.5H1.5V16.5ZM0 18V12H6V18H0ZM13.5 4.5H16.5V1.5H13.5V4.5ZM12 6V0H18V6H12ZM14 18V15H12V13H16V16H18V18H14ZM10 11V9H14V11H10ZM6 11V9H4V7H10V9H8V11H6ZM7 6V2H9V4H11V6H7ZM2.25 3.75V2.25H3.75V3.75H2.25ZM2.25 15.75V14.25H3.75V15.75H2.25ZM14.25 3.75V2.25H15.75V3.75H14.25Z" fill="white"/>
                        </svg>
                    </div>
                    <div class="flex flex-col items-start text-left">
                        <span class="text-sm font-semibold text-[#1A1C1A] leading-[16.8px] tracking-[0.7px]">QRIS</span>
                        <span class="text-xs text-[#5F5E5B] leading-[19.2px] mt-0.5">Tunjukkan QR di kasir / konfirmasi setelah transfer</span>
                    </div>
                </div>
                <div class="w-6 h-6 rounded-full border-2 border-[#800000] flex items-center justify-center flex-shrink-0 radio-button">
                    <div class="w-3 h-3 rounded-full bg-[#800000]"></div>
                </div>
            </button>

            <button type="button" class="payment-option group flex items-center justify-between w-full p-6 rounded-3xl bg-white transition-all border border-[#F5F5F4] opacity-70 shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] cursor-pointer hover:shadow-[0_6px_24px_0_rgba(0,0,0,0.08)]" data-method="kasir">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-[#E5E2DD] transition-colors">
                        <svg width="22" height="16" viewBox="0 0 22 16" fill="none">
                            <path d="M13 9C12.1667 9 11.4583 8.70833 10.875 8.125C10.2917 7.54167 10 6.83333 10 6C10 5.16667 10.2917 4.45833 10.875 3.875C11.4583 3.29167 12.1667 3 13 3C13.8333 3 14.5417 3.29167 15.125 3.875C15.7083 4.45833 16 5.16667 16 6C16 6.83333 15.7083 7.54167 15.125 8.125C14.5417 8.70833 13.8333 9 13 9ZM6 12C5.45 12 4.97917 11.8042 4.5875 11.4125C4.19583 11.0208 4 10.55 4 10V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H20C20.55 0 21.0208 0.195833 21.4125 0.5875C21.8042 0.979167 22 1.45 22 2V10C22 10.55 21.8042 11.0208 21.4125 11.4125C21.0208 11.8042 20.55 12 20 12H6ZM8 10H18C18 9.45 18.1958 8.97917 18.5875 8.5875C18.9792 8.19583 19.45 8 20 8V4C19.45 4 18.9792 3.80417 18.5875 3.4125C18.1958 3.02083 18 2.55 18 2H8C8 2.55 7.80417 3.02083 7.4125 3.4125C7.02083 3.80417 6.55 4 6 4V8C6.55 8 7.02083 8.19583 7.4125 8.5875C7.80417 8.97917 8 9.45 8 10ZM19 16H2C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V3H2V14H19V16ZM6 10V2V10Z" fill="#5F5E5B"/>
                        </svg>
                    </div>
                    <div class="flex flex-col items-start text-left">
                        <span class="text-sm font-semibold text-[#1A1C1A] leading-[16.8px] tracking-[0.7px]">Bayar di Kasir</span>
                        <span class="text-xs text-[#5F5E5B] leading-[19.2px] mt-0.5">Bayar tunai atau non-QR di kasir</span>
                    </div>
                </div>
                <div class="w-6 h-6 rounded-full border-2 border-[#E7E5E4] flex items-center justify-center flex-shrink-0 radio-button"></div>
            </button>
        </div>
    </div>
</main>

<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] px-4 pt-4 pb-8 bg-white/95 backdrop-blur-md border-t border-gray-100 shadow-[0_-4px_12px_0_rgba(0,0,0,0.05)] rounded-t-[20px] z-40">
    <button id="btnContinuePayment" type="button" class="w-full py-4 rounded-xl bg-[#7B0009] text-white text-base font-bold leading-6 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1),0_2px_4px_-2px_rgba(0,0,0,0.1)] active:opacity-90 transition-opacity hover:bg-[#6A0008]">
        Lanjutkan
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const paymentOptions = document.querySelectorAll('.payment-option');
        const btnContinue = document.getElementById('btnContinuePayment');
        let selectedMethod = 'qris';

        paymentOptions.forEach(option => {
            option.addEventListener('click', function (e) {
                e.preventDefault();
                selectedMethod = this.dataset.method || 'qris';

                paymentOptions.forEach(opt => {
                    opt.classList.add('opacity-70');
                    opt.classList.remove('border-[#800000]', 'border-2');
                    opt.classList.add('border-[#F5F5F4]', 'border');

                    const bgIcon = opt.querySelector('.w-10.h-10.rounded-full');
                    if (bgIcon) {
                        bgIcon.classList.remove('bg-[#800000]');
                        bgIcon.classList.add('bg-[#E5E2DD]');
                    }
                    const svgPath = opt.querySelector('svg path');
                    if (svgPath) svgPath.setAttribute('fill', '#5F5E5B');

                    const radioButton = opt.querySelector('.radio-button');
                    radioButton.classList.remove('border-[#800000]');
                    radioButton.classList.add('border-[#E7E5E4]');
                    radioButton.innerHTML = '';
                });

                this.classList.remove('opacity-70');
                this.classList.remove('border-[#F5F5F4]', 'border');
                this.classList.add('border-[#800000]', 'border-2');

                const bgIcon = this.querySelector('.w-10.h-10.rounded-full');
                if (bgIcon) {
                    bgIcon.classList.remove('bg-[#E5E2DD]');
                    bgIcon.classList.add('bg-[#800000]');
                }
                const svgPath = this.querySelector('svg path');
                if (svgPath) svgPath.setAttribute('fill', 'white');

                const radioButton = this.querySelector('.radio-button');
                radioButton.classList.remove('border-[#E7E5E4]');
                radioButton.classList.add('border-[#800000]');
                radioButton.innerHTML = '<div class="w-3 h-3 rounded-full bg-[#800000]"></div>';
            });
        });

        btnContinue?.addEventListener('click', async () => {
            const apiRoot = document.body.getAttribute('data-api-root');
            if (!apiRoot) return;
            const payment_method = selectedMethod === 'qris' ? 'qris' : 'kasir';
            btnContinue.disabled = true;
            try {
                const res = await fetch(apiRoot + '/order-create.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({ payment_method }),
                });
                const text = await res.text();
                let data = {};
                try { data = JSON.parse(text); } catch (_) {
                    data = { ok: false, error: 'Respons server bukan JSON (' + res.status + ')' };
                }
                if (!data.ok) {
                    const show = window.ScanteenUi && typeof window.ScanteenUi.showError === 'function'
                        ? window.ScanteenUi.showError.bind(window.ScanteenUi)
                        : function (o) { alert([o.message, o.detail].filter(Boolean).join('\n\n')); };
                    show({
                        title: 'Pesanan tidak dapat dibuat',
                        message: data.error || 'Gagal membuat pesanan.',
                        detail: data.detail || '',
                    });
                    btnContinue.disabled = false;
                    return;
                }
                window.location.href = selectedMethod === 'qris'
                    ? './index.php?page=bayar-qris'
                    : './index.php?page=bayar-kasir';
            } catch (e) {
                const show = window.ScanteenUi && typeof window.ScanteenUi.showError === 'function'
                    ? window.ScanteenUi.showError.bind(window.ScanteenUi)
                    : function (o) { alert(o.message); };
                show({
                    title: 'Koneksi bermasalah',
                    message: 'Tidak dapat menghubungi server. Periksa jaringan lalu coba lagi.',
                    detail: e && e.message ? String(e.message) : '',
                });
                btnContinue.disabled = false;
            }
        });
    });
</script>
