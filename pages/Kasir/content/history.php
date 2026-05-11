<?php // Konten: History ?>

<style>
    .shadow-dashboard {
        box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.05);
    }
</style>

<div class="flex flex-col gap-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <h1 class="text-[24px] font-bold leading-8 text-[#111827]">Riwayat</h1>
        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#F0FDF4]">
            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
            <span class="text-xs font-semibold text-[#16A34A]">Pembaruan Otomatis Aktif</span>
        </div>
    </div>

    <!-- Stats Bento Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Today's Revenue -->
        <div class="flex justify-between items-start bg-white rounded-2xl shadow-dashboard p-6 min-h-[176px]">
            <div class="flex flex-col gap-1">
                <p class="text-[#675C5C] text-sm font-bold tracking-[0.8px] uppercase leading-6">
                    Pendapatan Hari Ini
                </p>
                <p class="text-[#991B1B] text-[30px] font-bold leading-[38px] tracking-[-0.6px]">
                    Rp 4.250.000
                </p>
                <div class="flex items-center gap-1 pt-0.5">
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.7 6L0 5.3L3.7 1.575L5.7 3.575L8.3 1H7V0H10V3H9V1.7L5.7 5L3.7 3L0.7 6Z" fill="#16A34A"/>
                    </svg>
                    <span class="text-[10px] font-bold text-[#16A34A]">+12% dari kemarin</span>
                </div>
            </div>
            <div class="flex w-12 h-12 items-center justify-center rounded-xl bg-[#FEF2F2] flex-shrink-0">
                <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V3H2V14H19V16H2ZM6 12C5.45 12 4.97917 11.8042 4.5875 11.4125C4.19583 11.0208 4 10.55 4 10V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H20C20.55 0 21.0208 0.195833 21.4125 0.5875C21.8042 0.979167 22 1.45 22 2V10C22 10.55 21.8042 11.0208 21.4125 11.4125C21.0208 11.8042 20.55 12 20 12H6ZM8 10C8 9.45 7.80417 8.97917 7.4125 8.5875C7.02083 8.19583 6.55 8 6 8V10H8ZM18 10H20V8C19.45 8 18.9792 8.19583 18.5875 8.5875C18.1958 8.97917 18 9.45 18 10ZM13 9C13.8333 9 14.5417 8.70833 15.125 8.125C15.7083 7.54167 16 6.83333 16 6C16 5.16667 15.7083 4.45833 15.125 3.875C14.5417 3.29167 13.8333 3 13 3C12.1667 3 11.4583 3.29167 10.875 3.875C10.2917 4.45833 10 5.16667 10 6C10 6.83333 10.2917 7.54167 10.875 8.125C11.4583 8.70833 12.1667 9 13 9ZM6 4C6.55 4 7.02083 3.80417 7.4125 3.4125C7.80417 3.02083 8 2.55 8 2H6V4ZM20 4V2H18C18 2.55 18.1958 3.02083 18.5875 3.4125C18.9792 3.80417 19.45 4 20 4Z" fill="#991B1B"/>
                </svg>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="flex justify-between items-start bg-white rounded-2xl shadow-dashboard p-6 min-h-[176px]">
            <div class="flex flex-col gap-1">
                <p class="text-[#675C5C] text-sm font-bold tracking-[0.8px] uppercase leading-6">
                    Pesanan<br />Selesai
                </p>
                <p class="text-[#261817] text-[30px] font-bold leading-[38px] tracking-[-0.6px]">142</p>
                <p class="text-[#675C5C] text-[10px] font-medium leading-4 pt-1 italic">Rata-rata Proses: 4.2m</p>
            </div>
            <div class="flex w-12 h-12 items-center justify-center rounded-xl bg-[#FFF7ED] flex-shrink-0">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.6 14.6L15.65 7.55L14.25 6.15L8.6 11.8L5.75 8.95L4.35 10.35L8.6 14.6ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20Z" fill="#EA580C"/>
                </svg>
            </div>
        </div>

        <!-- QRIS Usage -->
        <div class="flex justify-between items-start bg-white rounded-2xl shadow-dashboard p-6 min-h-[176px]">
            <div class="flex flex-col gap-1">
                <p class="text-[#675C5C] text-sm font-bold tracking-[0.8px] uppercase leading-6">Penggunaan QRIS</p>
                <p class="text-[#261817] text-[30px] font-bold leading-[38px] tracking-[-0.6px]">68%</p>
                <p class="text-[#675C5C] text-[10px] font-medium leading-4 pt-1 italic">
                    Metode Pembayaran<br />Utama
                </p>
            </div>
            <div class="flex w-12 h-12 items-center justify-center rounded-xl bg-[#EFF6FF] flex-shrink-0">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 18V16H12V18H10ZM8 16V11H10V16H8ZM16 13V9H18V13H16ZM14 9V7H16V9H14ZM2 11V9H4V11H2ZM0 9V7H2V9H0ZM9 2V0H11V2H9ZM1.5 4.5H4.5V1.5H1.5V4.5ZM0 6V0H6V6H0ZM1.5 16.5H4.5V13.5H1.5V16.5ZM0 18V12H6V18H0ZM13.5 4.5H16.5V1.5H13.5V4.5ZM12 6V0H18V6H12ZM14 18V15H12V13H16V16H18V18H14ZM10 11V9H14V11H10ZM6 11V9H4V7H10V9H8V11H6ZM7 6V2H9V4H11V6H7ZM2.25 3.75V2.25H3.75V3.75H2.25ZM2.25 15.75V14.25H3.75V15.75H2.25ZM14.25 3.75V2.25H15.75V3.75H14.25Z" fill="#2563EB"/>
                </svg>
            </div>
        </div>

        <!-- System Status -->
        <div class="relative flex justify-between items-start bg-white rounded-2xl shadow-dashboard p-6 min-h-[176px] overflow-hidden">
            <div class="flex flex-col gap-1 relative z-10">
                <p class="text-[#675C5C] text-sm font-bold tracking-[0.8px] uppercase leading-6">Status Sistem</p>
                <p class="text-[#15803D] text-[30px] font-bold leading-[38px] tracking-[-0.6px]">Sehat</p>
                <p class="text-[#675C5C] text-[10px] font-medium leading-4 pt-1 italic">Latensi: 42ms</p>
            </div>
            <div class="flex w-12 h-12 items-center justify-center rounded-xl bg-[#F0FDF4] flex-shrink-0 relative z-10">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 12V6H12V12H6ZM8 10H10V8H8V10ZM6 18V16H4C3.45 16 2.97917 15.8042 2.5875 15.4125C2.19583 15.0208 2 14.55 2 14V12H0V10H2V8H0V6H2V4C2 3.45 2.19583 2.97917 2.5875 2.5875C2.97917 2.19583 3.45 2 4 2H6V0H8V2H10V0H12V2H14C14.55 2 15.0208 2.19583 15.4125 2.5875C15.8042 2.97917 16 3.45 16 4V6H18V8H16V10H18V12H16V14C16 14.55 15.8042 15.0208 15.4125 15.4125C15.0208 15.8042 14.55 16 14 16H12V18H10V16H8V18H6ZM14 14V4H4V14H14Z" fill="#16A34A"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Live Transaction Feed -->
    <div class="bg-white rounded-3xl shadow-dashboard overflow-hidden border border-gray-50">
        <!-- Table Header Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-8 py-7 border-b border-gray-50">
            <div>
                <h2 class="text-[#111827] text-xl font-bold leading-7">Feed Transaksi Langsung</h2>
                <p class="text-[#6B7280] text-sm font-medium leading-5 mt-0.5">Pembaruan real-time untuk semua transaksi</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- All Methods dropdown -->
                <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-100 bg-[#F9FAFB] text-[#4B5563] text-sm font-bold hover:bg-gray-100 transition-colors">
                    Semua Metode
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.6668 6L8.00016 10.6667L3.3335 6" stroke="#4B5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <!-- Cetak Semua Struk -->
                <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-[#4B5563] text-sm font-bold hover:bg-gray-50 transition-colors shadow-sm">
                    <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.5 3.75V1.5H4.5V3.75H3V0H12V3.75H10.5ZM1.5 5.25C1.5 5.25 1.57188 5.25 1.71563 5.25C1.85938 5.25 2.0375 5.25 2.25 5.25H12.75C12.9625 5.25 13.1406 5.25 13.2844 5.25C13.4281 5.25 13.5 5.25 13.5 5.25H12H3H1.5ZM12 7.125C12.2125 7.125 12.3906 7.05313 12.5344 6.90938C12.6781 6.76562 12.75 6.5875 12.75 6.375C12.75 6.1625 12.6781 5.98438 12.5344 5.84062C12.3906 5.69687 12.2125 5.625 12 5.625C11.7875 5.625 11.6094 5.69687 11.4656 5.84062C11.3219 5.98438 11.25 6.1625 11.25 6.375C11.25 6.5875 11.3219 6.76562 11.4656 6.90938C11.6094 7.05313 11.7875 7.125 12 7.125ZM10.5 12V9H4.5V12H10.5ZM12 13.5H3V10.5H0V6C0 5.3625 0.21875 4.82812 0.65625 4.39687C1.09375 3.96562 1.625 3.75 2.25 3.75H12.75C13.3875 3.75 13.9219 3.96562 14.3531 4.39687C14.7844 4.82812 15 5.3625 15 6V10.5H12V13.5ZM13.5 9V6C13.5 5.7875 13.4281 5.60938 13.2844 5.46562C13.1406 5.32187 12.9625 5.25 12.75 5.25H2.25C2.0375 5.25 1.85938 5.32187 1.71563 5.46562C1.57188 5.60938 1.5 5.7875 1.5 6V9H3V7.5H12V9H13.5Z" fill="#4B5563"/>
                    </svg>
                    Cetak Semua Struk
                </button>
                <!-- Export Data -->
                <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#7B0009] text-white text-sm font-bold hover:bg-[#991B1B] transition-all shadow-md shadow-red-900/10 active:scale-95">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.6665 10.667V11.3337C2.6665 12.4375 3.56267 13.3337 4.6665 13.3337H11.3332C12.437 13.3337 13.3332 12.4375 13.3332 11.3337V10.667M10.6665 8.00033L7.99984 10.667M7.99984 10.667L5.33317 8.00033M7.99984 10.667V2.66699" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Export Data
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-5 text-left text-[11px] font-extrabold text-[#675C5C] tracking-[1px] uppercase">ID Pesanan</th>
                        <th class="px-8 py-5 text-left text-[11px] font-extrabold text-[#675C5C] tracking-[1px] uppercase">Metode Pembayaran</th>
                        <th class="px-8 py-5 text-left text-[11px] font-extrabold text-[#675C5C] tracking-[1px] uppercase">Waktu</th>
                        <th class="px-8 py-5 text-right text-[11px] font-extrabold text-[#675C5C] tracking-[1px] uppercase">Jumlah</th>
                        <th class="px-8 py-5 text-left text-[11px] font-extrabold text-[#675C5C] tracking-[1px] uppercase">Status</th>
                        <th class="px-8 py-5 text-left text-[11px] font-extrabold text-[#675C5C] tracking-[1px] uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!-- Row 1: Success QRIS -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <span class="text-[#111827] text-sm font-bold">#SC-9042</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="flex w-9 h-9 items-center justify-center rounded-xl bg-blue-50 flex-shrink-0">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 18V16H12V18H10ZM8 16V11H10V16H8ZM16 13V9H18V13H16ZM14 9V7H16V9H14ZM2 11V9H4V11H2ZM0 9V7H2V9H0ZM9 2V0H11V2H9ZM1.5 4.5H4.5V1.5H1.5V4.5ZM0 6V0H6V6H0ZM1.5 16.5H4.5V13.5H1.5V16.5ZM0 18V12H6V18H0ZM13.5 4.5H16.5V1.5H13.5V4.5ZM12 6V0H18V6H12ZM14 18V15H12V13H16V16H18V18H14ZM10 11V9H14V11H10ZM6 11V9H4V7H10V9H8V11H6ZM7 6V2H9V4H11V6H7ZM2.25 3.75V2.25H3.75V3.75H2.25ZM2.25 15.75V14.25H3.75V15.75H2.25ZM14.25 3.75V2.25H15.75V3.75H14.25Z" fill="#2563EB"/>
                                    </svg>
                                </div>
                                <span class="text-[#261817] text-sm font-semibold">QRIS</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[#675C5C] text-sm font-medium">Hari ini, 14:45:22</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="text-[#261817] text-sm font-black">Rp 45.000</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider">Berhasil</span>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all active:scale-90">
                                <svg width="18" height="20" viewBox="0 0 18 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 6 2 12 2 12 9"></polyline>
                                    <path d="M6 18H3C2.44772 18 2 17.5523 2 17V9H16V17C16 17.5523 15.5523 18 15 18H12"></path>
                                    <rect x="6" y="14" width="6" height="4"></rect>
                                </svg>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 2: Pending E-Wallet -->
                    <tr class="hover:bg-gray-50/50 transition-colors text-gray-400">
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold">#SC-9041</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="flex w-9 h-9 items-center justify-center rounded-xl bg-purple-50 flex-shrink-0 opacity-50">
                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.33333 11.6667C7.875 11.6667 7.48264 11.5035 7.15625 11.1771C6.82986 10.8507 6.66667 10.4583 6.66667 10V5C6.66667 4.54167 6.82986 4.14931 7.15625 3.82292C7.48264 3.49653 7.875 3.33333 8.33333 3.33333H14.1667C14.625 3.33333 15.0174 3.49653 15.3438 3.82292C15.6701 4.14931 15.8333 4.54167 15.8333 5V10C15.8333 10.4583 15.6701 10.8507 15.3438 11.1771C15.0174 11.5035 14.625 11.6667 14.1667 11.6667H8.33333Z" fill="#9333EA"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-500">E-Wallet (OVO)</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-medium">Hari ini, 14:42:10</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="text-sm font-black">Rp 112.500</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 border border-orange-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                <span class="text-[10px] font-black text-orange-700 uppercase tracking-wider">Pending</span>
                            </span>
                        </td>
                        <td class="px-8 py-6 text-gray-300 italic text-[11px] font-bold tracking-wider">
                            Sedang Diproses...
                        </td>
                    </tr>

                    <!-- Row 3: Success Cash -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <span class="text-[#111827] text-sm font-bold">#SC-9040</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="flex w-9 h-9 items-center justify-center rounded-xl bg-emerald-50 flex-shrink-0">
                                    <svg width="19" height="14" viewBox="0 0 19 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.8333 7.5C10.1389 7.5 9.54861 7.25694 9.0625 6.77083C8.57639 6.28472 8.33333 5.69444 8.33333 5C8.33333 4.30556 8.57639 3.71528 9.0625 3.22917C9.54861 2.74306 10.1389 2.5 10.8333 2.5C11.5278 2.5 12.1181 2.74306 12.6042 3.22917C13.0903 3.71528 13.3333 4.30556 13.3333 5C13.3333 5.69444 13.0903 6.28472 12.6042 6.77083C12.1181 7.25694 11.5278 7.5 10.8333 7.5Z" fill="#059669"/>
                                    </svg>
                                </div>
                                <span class="text-[#261817] text-sm font-semibold">Tunai</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[#675C5C] text-sm font-medium">Hari ini, 14:38:55</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="text-[#261817] text-sm font-black">Rp 28.000</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider">Berhasil</span>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all active:scale-90">
                                <svg width="18" height="20" viewBox="0 0 18 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 6 2 12 2 12 9"></polyline>
                                    <path d="M6 18H3C2.44772 18 2 17.5523 2 17V9H16V17C16 17.5523 15.5523 18 15 18H12"></path>
                                    <rect x="6" y="14" width="6" height="4"></rect>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-[#675C5C] text-xs font-bold tracking-tight">
                Menampilkan 1-10 dari 2.450 transaksi
            </span>
            <div class="flex items-center gap-2">
                <button class="px-4 py-2 rounded-xl border border-gray-200 text-gray-500 text-xs font-black hover:bg-white hover:border-gray-300 transition-all">
                    SEBELUMNYA
                </button>
                <div class="flex items-center gap-1.5">
                    <button class="w-9 h-9 rounded-xl bg-[#7B0009] text-white text-xs font-black shadow-md shadow-red-900/20">1</button>
                    <button class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-gray-400 text-xs font-black hover:bg-gray-50">2</button>
                    <button class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-gray-400 text-xs font-black hover:bg-gray-50">3</button>
                </div>
                <button class="px-4 py-2 rounded-xl border border-gray-200 text-gray-500 text-xs font-black hover:bg-white hover:border-gray-300 transition-all">
                    SELANJUTNYA
                </button>
            </div>
        </div>
    </div>
</div>
