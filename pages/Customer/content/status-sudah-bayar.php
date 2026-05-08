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

            <!-- Step 1: Menunggu (Done) -->
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-[#7B0009] border-[#7B0009]">
                        <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 5L4.5 8.5L11 1.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="w-px flex-1 bg-[#7B0009] my-1 min-h-[20px]"></div>
                </div>
                <div class="pb-5 text-left">
                    <p class="text-[#7B0009] text-sm font-bold leading-5">Menunggu</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan berhasil dibayar</p>
                </div>
            </div>

            <!-- Step 2: Diterima (Active) -->
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-[#7B0009] border-[#7B0009]">
                        <span class="text-white text-xs font-bold">2</span>
                    </div>
                    <div class="w-px flex-1 bg-[#E5D5D5] my-1 min-h-[20px]"></div>
                </div>
                <div class="pb-5 text-left">
                    <p class="text-[#7B0009] text-sm font-bold leading-5">Diterima</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan sedang dikonfirmasi oleh warung</p>
                </div>
            </div>

            <!-- Step 3: Diproses -->
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-white border-[#E5D5D5]"></div>
                    <div class="w-px flex-1 bg-[#E5D5D5] my-1 min-h-[20px]"></div>
                </div>
                <div class="pb-5 text-left opacity-50">
                    <p class="text-[#59413E] text-sm font-bold leading-5">Diproses</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan sedang disiapkan</p>
                </div>
            </div>

            <!-- Step 4: Siap -->
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-white border-[#E5D5D5]"></div>
                    <div class="w-px flex-1 bg-[#E5D5D5] my-1 min-h-[20px]"></div>
                </div>
                <div class="pb-5 text-left opacity-50">
                    <p class="text-[#59413E] text-sm font-bold leading-5">Siap</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan sudah siap diantarkan</p>
                </div>
            </div>

            <!-- Step 5: Selesai -->
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-white border-[#E5D5D5]"></div>
                </div>
                <div class="text-left opacity-50">
                    <p class="text-[#59413E] text-sm font-bold leading-5">Selesai</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan telah sampai</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Detail Menu Section -->
    <div class="flex flex-col gap-3">
        <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4 px-1 text-left">
            Detail Menu
        </span>

        <!-- Warung 1 Card -->
        <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#F3F4F6]">
                <div class="flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 7.5L3.5 2H16.5L18 7.5M2 7.5H18M2 7.5C2 8.88 3.12 10 4.5 10C5.88 10 7 8.88 7 7.5M18 7.5C18 8.88 16.88 10 15.5 10C14.12 10 13 8.88 13 7.5M7 7.5C7 8.88 8.12 10 9.5 10C10.88 10 12 8.88 12 7.5M13 7.5C13 8.88 11.88 10 10.5 10M4 10.5V18H16V10.5" stroke="#7B0009" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="font-inter text-[#261817] text-base font-bold leading-6">Warung 1</span>
                </div>
                <span class="text-[#7B0009] text-xs font-semibold tracking-wide bg-[#FEF2F2] border border-[#FEE2E2] rounded px-2 py-0.5">
                    CONFIRMED
                </span>
            </div>
            <div class="flex flex-col gap-4 px-5 py-4">
                <!-- Item 1 -->
                <div class="flex flex-col gap-1.5 text-left">
                    <div class="flex items-baseline gap-2">
                        <span class="text-[#7B0009] text-sm font-bold leading-5 flex-shrink-0">1x</span>
                        <span class="text-[#261817] text-sm font-semibold leading-5">Soto Babat</span>
                    </div>
                    <p class="text-[#675C5C] text-xs font-normal leading-4 pl-6">Rp 12.000 / porsi</p>
                    <div class="pl-6">
                        <span class="inline-block text-[#59413E] text-xs font-normal leading-4 border border-[#E5E7EB] rounded px-2 py-0.5 bg-white">
                            Tambah sambal extra
                        </span>
                    </div>
                </div>
                <!-- Item 2 -->
                <div class="flex flex-col gap-1.5 text-left">
                    <div class="flex items-baseline gap-2">
                        <span class="text-[#7B0009] text-sm font-bold leading-5 flex-shrink-0">2x</span>
                        <span class="text-[#261817] text-sm font-semibold leading-5">Wader Goreng</span>
                    </div>
                    <p class="text-[#675C5C] text-xs font-normal leading-4 pl-6">Rp 12.000 / porsi</p>
                    <div class="pl-6">
                        <span class="inline-block text-[#59413E] text-xs font-normal leading-4 border border-[#E5E7EB] rounded px-2 py-0.5 bg-white">
                            -
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warung 2 Card -->
        <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#F3F4F6]">
                <div class="flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 7.5L3.5 2H16.5L18 7.5M2 7.5H18M2 7.5C2 8.88 3.12 10 4.5 10C5.88 10 7 8.88 7 7.5M18 7.5C18 8.88 16.88 10 15.5 10C14.12 10 13 8.88 13 7.5M7 7.5C7 8.88 8.12 10 9.5 10C10.88 10 12 8.88 12 7.5M13 7.5C13 8.88 11.88 10 10.5 10M4 10.5V18H16V10.5" stroke="#7B0009" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="font-inter text-[#261817] text-base font-bold leading-6">Warung 2</span>
                </div>
                <span class="text-[#7B0009] text-xs font-semibold tracking-wide bg-[#FEF2F2] border border-[#FEE2E2] rounded px-2 py-0.5">
                    CONFIRMED
                </span>
            </div>
            <div class="flex flex-col gap-4 px-5 py-4">
                <!-- Item 1 -->
                <div class="flex flex-col gap-1.5 text-left">
                    <div class="flex items-baseline gap-2">
                        <span class="text-[#7B0009] text-sm font-bold leading-5 flex-shrink-0">1x</span>
                        <span class="text-[#261817] text-sm font-semibold leading-5">Rawon Jumbo</span>
                    </div>
                    <p class="text-[#675C5C] text-xs font-normal leading-4 pl-6">Rp 12.000 / porsi</p>
                    <div class="pl-6">
                        <span class="inline-block text-[#59413E] text-xs font-normal leading-4 border border-[#E5E7EB] rounded px-2 py-0.5 bg-white">
                            Kuah pisah
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total -->
        <div class="flex items-center justify-between px-1 pt-2 pb-1">
            <span class="text-[#261817] text-base font-bold leading-6">Total Pembayaran</span>
            <span class="font-inter text-[#7B0009] text-lg font-black leading-7">Rp 100.000</span>
        </div>
    </div>

</main>

<!-- Bottom CTA -->
<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="bg-[#FAF9F6] rounded-3xl px-5 py-4 shadow-[0_-4px_30px_rgba(0,0,0,0.08)] border border-[#EFEEEB]">
        <button class="w-full py-4 rounded-2xl bg-[#7B0009] flex items-center justify-center gap-2 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.10)] hover:bg-[#6a0007] transition-all active:scale-[0.98]" onclick="window.location.href='./index.php?page=home'">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 1V17M1 9H17" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span class="font-inter text-white text-base font-bold leading-6">
                Tambah Pesanan Baru
            </span>
        </button>
    </div>
</div>
