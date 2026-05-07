<!-- Scrollable Content -->
<main class="flex-1 flex flex-col gap-5 px-4 pt-5 pb-32">

    <!-- Order ID Card -->
    <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] px-5 py-5 flex items-center justify-between">
        <div class="flex flex-col gap-1 text-left">
            <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4">
                ID PESANAN
            </span>
            <span class="font-inter text-[#261817] text-xl font-bold leading-7">
                #ORD-1012-0004
            </span>
        </div>
        <div class="flex flex-col items-center justify-center bg-[#7B0009] rounded-xl px-3 py-2 min-w-[52px]">
            <span class="text-white text-[10px] font-semibold tracking-wider uppercase leading-none">MEJA</span>
            <span class="font-inter text-white text-xl font-black leading-tight">12</span>
        </div>
    </div>

    <!-- Payment Alert Card -->
    <div class="bg-[#7B0009] rounded-2xl p-6 flex flex-col items-center text-center gap-3 shadow-[0_4px_12px_rgba(123,0,9,0.2)]">
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8V12L15 15" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="9" stroke="white" stroke-width="2"/>
            </svg>
        </div>
        <div class="flex flex-col gap-1">
            <h3 class="text-white text-base font-bold">Menunggu Pembayaran</h3>
            <p class="text-white/80 text-sm">Selesaikan pembayaran sebelum waktu habis</p>
        </div>
        <div class="mt-2 px-6 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm">
            <span id="timer-waiting" class="text-white font-mono text-xl font-bold tracking-widest" data-countdown-seconds="1785">29:45</span>
        </div>
    </div>

    <!-- Order Status Section -->
    <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] px-5 pt-5 pb-6">
        <div class="flex items-center gap-2 mb-5">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 4H15M1 8H10M1 12H7" stroke="#675C5C" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4 text-left">
                Status Pesanan
            </span>
        </div>

        <div class="flex flex-col">
            <!-- Step 1: Menunggu (Active) -->
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-[#7B0009] border-[#7B0009]">
                        <span class="text-white text-xs font-bold">1</span>
                    </div>
                    <div class="w-px flex-1 bg-[#E5D5D5] my-1 min-h-[20px]"></div>
                </div>
                <div class="pb-5 text-left">
                    <p class="text-[#7B0009] text-sm font-bold leading-5">Menunggu</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan belum dibayar</p>
                </div>
            </div>

            <!-- Step 2: Diterima -->
            <div class="flex gap-4">
                <div class="flex flex-col items-center opacity-50">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-white border-[#E5D5D5]"></div>
                    <div class="w-px flex-1 bg-[#E5D5D5] my-1 min-h-[20px]"></div>
                </div>
                <div class="pb-5 text-left opacity-50">
                    <p class="text-[#59413E] text-sm font-bold leading-5">Diterima</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan terkirim</p>
                </div>
            </div>

            <!-- Step 3+ combined for space -->
            <div class="text-[#675C5C] text-[10px] font-medium italic ml-11">... langkah selanjutnya ...</div>
        </div>
    </div>
</main>

<!-- Bottom Action Bar -->
<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="flex flex-col gap-3 bg-[#FAF9F6] rounded-3xl px-5 py-4 shadow-[0_-4px_30px_rgba(0,0,0,0.08)] border border-[#EFEEEB]">
        <button type="button" onclick="window.location.href='./index.php?page=status-sudah-bayar'"
            class="w-full py-4 rounded-2xl bg-[#7B0009] text-white font-bold text-base transition-all hover:bg-[#6a0000] active:scale-[0.98]">
            Bayar Sekarang
        </button>
        <button type="button" onclick="window.location.href='./index.php?page=pilih-pembayaran'"
            class="w-full py-3 rounded-2xl border-2 border-[#7B0009] text-[#7B0009] font-bold text-base transition-all hover:bg-[#7B0009]/5 active:scale-[0.98]">
            Ubah Metode Pembayaran
        </button>
    </div>
</div>

<script>
    function startTimerWaiting() {
        const timerEl = document.getElementById('timer-waiting');
        if (!timerEl) return;
        
        let seconds = parseInt(timerEl.getAttribute('data-countdown-seconds') || '0');
        
        const tick = () => {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            timerEl.textContent = `${m}:${s}`;
            if (seconds > 0) seconds--;
        };

        tick();
        const interval = setInterval(() => {
            if (seconds <= 0) {
                clearInterval(interval);
                timerEl.textContent = '00:00';
                return;
            }
            tick();
        }, 1000);
    }
    
    document.addEventListener('DOMContentLoaded', startTimerWaiting);
</script>
