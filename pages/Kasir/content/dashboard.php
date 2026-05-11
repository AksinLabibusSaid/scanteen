<div class="flex flex-col gap-5">

    <!-- Incoming Order Notification -->
    <div class="bg-red-50 border border-red-200 rounded-2xl flex items-center gap-4 px-5 py-4">
        <!-- Bell Icon -->
        <div class="w-12 h-12 rounded-full bg-white border border-red-100 flex items-center justify-center flex-shrink-0">
            <svg width="22" height="24" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 19V17H3.5V10C3.5 8.31667 4.02083 6.8125 5.0625 5.4875C6.10417 4.1625 7.45 3.31667 9.1 2.95V2C9.1 1.58333 9.24583 1.22917 9.5375 0.9375C9.82917 0.645833 10.1833 0.5 10.6 0.5C11.0167 0.5 11.3708 0.645833 11.6625 0.9375C11.9542 1.22917 12.1 1.58333 12.1 2V2.95C13.75 3.31667 15.0958 4.1625 16.1375 5.4875C17.1792 6.8125 17.7 8.31667 17.7 10V17H20.2V19H1ZM10.6 24C9.93333 24 9.36458 23.7625 8.89375 23.2875C8.42292 22.8125 8.1875 22.2333 8.1875 21.55H13.0125C13.0125 22.2333 12.7771 22.8125 12.3063 23.2875C11.8354 23.7625 11.2667 24 10.6 24ZM5.5 17H15.7V10C15.7 8.61667 15.2208 7.4375 14.2625 6.4625C13.3042 5.4875 12.15 5 10.8 5C9.45 5 8.29583 5.4875 7.3375 6.4625C6.37917 7.4375 5.9 8.61667 5.9 10V17H5.5Z" fill="#991B1B"/>
                <circle cx="17" cy="4" r="4" fill="#DC2626" stroke="white" stroke-width="2"/>
            </svg>
        </div>
        <!-- Text -->
        <div class="flex-1">
            <p class="text-[#991B1B] font-bold text-base leading-tight">Pesanan Masuk</p>
            <p class="text-gray-500 text-sm mt-0.5">Meja 5 baru saja memesan 4 item.</p>
        </div>
        <!-- Action Buttons -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <button class="bg-[#7F1D1D] hover:bg-[#991B1B] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                Lihat Pesanan
            </button>
            <button class="bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl border border-gray-200 transition-colors">
                Tutup
            </button>
        </div>
    </div>

    <!-- Session Management Card -->
    <div class="bg-white rounded-2xl border border-gray-100 flex items-center gap-4 px-6 py-5">
        <!-- Timer Icon -->
        <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 20C9.75 20 8.57917 19.7625 7.4875 19.2875C6.39583 18.8125 5.44583 18.1708 4.6375 17.3625C3.82917 16.5542 3.1875 15.6042 2.7125 14.5125C2.2375 13.4208 2 12.25 2 11C2 9.75 2.2375 8.57917 2.7125 7.4875C3.1875 6.39583 3.82917 5.44583 4.6375 4.6375C5.44583 3.82917 6.39583 3.1875 7.4875 2.7125C8.57917 2.2375 9.75 2 11 2C12.25 2 13.4208 2.2375 14.5125 2.7125C15.6042 3.1875 16.5542 3.82917 17.3625 4.6375C18.1708 5.44583 18.8125 6.39583 19.2875 7.4875C19.7625 8.57917 20 9.75 20 11C20 12.25 19.7625 13.4208 19.2875 14.5125C18.8125 15.6042 18.1708 16.5542 17.3625 17.3625C16.5542 18.1708 15.6042 18.8125 14.5125 19.2875C13.4208 19.7625 12.25 20 11 20ZM11 18C13 18 14.6875 17.3125 16.1125 15.9375C17.5375 14.5625 18.25 12.9167 18.25 11C18.25 9.08333 17.5375 7.4375 16.1125 6.0625C14.6875 4.6875 13 4 11 4C9 4 7.3125 4.6875 5.8875 6.0625C4.4625 7.4375 3.75 9.08333 3.75 11C3.75 12.9167 4.4625 14.5625 5.8875 15.9375C7.3125 17.3125 9 18 11 18ZM13.8 14.85L15.1 13.55L11.875 10.325V6.25H10.125V11.025L13.8 14.85Z" fill="#F97316"/>
            </svg>
        </div>
        <!-- Text -->
        <div class="flex-1">
            <p class="text-gray-900 font-bold text-base">Manajemen Sesi</p>
            <p class="text-gray-500 text-sm mt-0.5">Kelola sesi kasir aktif dan pemrosesan pesanan.</p>
        </div>
        <!-- Session Buttons -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <button class="flex items-center gap-2 bg-[#7F1D1D] hover:bg-[#991B1B] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" fill="white"/>
                </svg>
                Buka Sesi Pesanan
            </button>
            <button class="flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl border border-gray-200 transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" fill="#374151"/>
                </svg>
                Tutup Sesi Pesanan
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Orders Today -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 20C5.45 20 4.97917 19.8042 4.5875 19.4125C4.19583 19.0208 4 18.55 4 18C4 17.45 4.19583 16.9792 4.5875 16.5875C4.97917 16.1958 5.45 16 6 16C6.55 16 7.02083 16.1958 7.4125 16.5875C7.80417 16.9792 8 17.45 8 18C8 18.55 7.80417 19.0208 7.4125 19.4125C7.02083 19.8042 6.55 20 6 20ZM16 20C15.45 20 14.9792 19.8042 14.5875 19.4125C14.1958 19.0208 14 18.55 14 18C14 17.45 14.1958 16.9792 14.5875 16.5875C14.9792 16.1958 15.45 16 16 16C16.55 16 17.0208 16.1958 17.4125 16.5875C17.8042 16.9792 18 17.45 18 18C18 18.55 17.8042 19.0208 17.4125 19.4125C17.0208 19.8042 16.55 20 16 20ZM5.15 4L7.55 9H14.55L17.3 4H5.15ZM4.2 2H18.95C19.3333 2 19.625 2.17083 19.825 2.5125C20.025 2.85417 20.0333 3.2 19.85 3.55L16.3 9.95C16.1167 10.2833 15.8708 10.5417 15.5625 10.725C15.2542 10.9083 14.9167 11 14.55 11H7.1L6 13H18V15H6C5.25 15 4.68333 14.6708 4.3 14.0125C3.91667 13.3542 3.9 12.7 4.25 12.05L5.6 9.6L2 2H0V0H3.25L4.2 2ZM7.55 9H14.55H7.55Z" fill="#991B1B"/>
                    </svg>
                </div>
                <span class="text-green-600 text-xs font-semibold flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 2L10 6H7V10H5V6H2L6 2Z" fill="#16A34A"/></svg>
                    +12.5%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Total Pesanan Hari Ini</p>
            <p class="text-gray-900 text-3xl font-bold mt-1">156</p>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 9C12.1667 9 11.4583 8.70833 10.875 8.125C10.2917 7.54167 10 6.83333 10 6C10 5.16667 10.2917 4.45833 10.875 3.875C11.4583 3.29167 12.1667 3 13 3C13.8333 3 14.5417 3.29167 15.125 3.875C15.7083 4.45833 16 5.16667 16 6C16 6.83333 15.7083 7.54167 15.125 8.125C14.5417 8.70833 13.8333 9 13 9ZM6 12C5.45 12 4.97917 11.8042 4.5875 11.4125C4.19583 11.0208 4 10.55 4 10V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H20C20.55 0 21.0208 0.195833 21.4125 0.5875C21.8042 0.979167 22 1.45 22 2V10C22 10.55 21.8042 11.0208 21.4125 11.4125C21.0208 11.8042 20.55 12 20 12H6ZM8 10H18C18 9.45 18.1958 8.97917 18.5875 8.5875C18.9792 8.19583 19.45 8 20 8V4C19.45 4 18.9792 3.80417 18.5875 3.4125C18.1958 3.02083 18 2.55 18 2H8C8 2.55 7.80417 3.02083 7.4125 3.4125C7.02083 3.80417 6.55 4 6 4V8C6.55 8 7.02083 8.19583 7.4125 8.5875C7.80417 8.97917 8 9.45 8 10ZM19 16H2C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V3H2V14H19V16ZM6 10V2V10Z" fill="#991B1B"/>
                    </svg>
                </div>
                <span class="text-green-600 text-xs font-semibold flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 2L10 6H7V10H5V6H2L6 2Z" fill="#16A34A"/></svg>
                    +8.2%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Total Pendapatan</p>
            <p class="text-gray-900 text-2xl font-bold mt-1">Rp 4.250.000</p>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                    <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 12C8.75 12 7.70833 11.5625 6.875 10.6875C6.04167 9.8125 5.625 8.75 5.625 7.5C5.625 6.25 6.04167 5.1875 6.875 4.3125C7.70833 3.4375 8.75 3 10 3C11.25 3 12.2917 3.4375 13.125 4.3125C13.9583 5.1875 14.375 6.25 14.375 7.5C14.375 8.75 13.9583 9.8125 13.125 10.6875C12.2917 11.5625 11.25 12 10 12ZM2 19V17C2 16.3542 2.17708 15.7604 2.53125 15.2188C2.88542 14.6771 3.35417 14.2708 3.9375 14C4.85417 13.5833 5.79167 13.2708 6.75 13.0625C7.70833 12.8542 8.83333 12.75 10 12.75C11.1667 12.75 12.2917 12.8542 13.25 13.0625C14.2083 13.2708 15.1458 13.5833 16.0625 14C16.6458 14.2708 17.1146 14.6771 17.4688 15.2188C17.8229 15.7604 18 16.3542 18 17V19H2Z" fill="#F97316"/>
                    </svg>
                </div>
                <span class="text-orange-500 text-xs font-semibold bg-orange-50 px-2 py-0.5 rounded-full">Aktif</span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Pesanan Pending</p>
            <p class="text-gray-900 text-3xl font-bold mt-1">12</p>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.55 16.15L16.6 9.1L15.175 7.675L9.55 13.3L6.825 10.575L5.4 12L9.55 16.15ZM11 21C9.75 21 8.57917 20.7625 7.4875 20.2875C6.39583 19.8125 5.44583 19.1708 4.6375 18.3625C3.82917 17.5542 3.1875 16.6042 2.7125 15.5125C2.2375 14.4208 2 13.25 2 12C2 10.75 2.2375 9.57917 2.7125 8.4875C3.1875 7.39583 3.82917 6.44583 4.6375 5.6375C5.44583 4.82917 6.39583 4.1875 7.4875 3.7125C8.57917 3.2375 9.75 3 11 3C12.25 3 13.4208 3.2375 14.5125 3.7125C15.6042 4.1875 16.5542 4.82917 17.3625 5.6375C18.1708 6.44583 18.8125 7.39583 19.2875 8.4875C19.7625 9.57917 20 10.75 20 12C20 13.25 19.7625 14.4208 19.2875 15.5125C18.8125 16.6042 18.1708 17.5542 17.3625 18.3625C16.5542 19.1708 15.6042 19.8125 14.5125 20.2875C13.4208 20.7625 12.25 21 11 21Z" fill="#16A34A"/>
                    </svg>
                </div>
                <span class="text-gray-400 text-xs font-semibold">Total</span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Pesanan Selesai</p>
            <p class="text-gray-900 text-3xl font-bold mt-1">144</p>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="flex items-center justify-between px-6 py-5">
            <h2 class="text-gray-900 font-bold text-lg">Pesanan Terbaru</h2>
            <a href="?page=orders" class="text-[#991B1B] text-sm font-semibold hover:underline">Lihat Semua Transaksi</a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-t border-gray-100 bg-gray-50">
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-6 py-3">ID Pesanan</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-4 py-3">Meja</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-4 py-3">Waktu</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-4 py-3">Total</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Row 1 - Pending -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#ORD-2024-8842</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Meja 5</td>
                        <td class="px-4 py-4 text-sm text-gray-600">14:22</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">Rp 145.000</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Pending</span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Detail</a>
                        </td>
                    </tr>
                    <!-- Row 2 - Paid -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#ORD-2024-8841</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Meja 2</td>
                        <td class="px-4 py-4 text-sm text-gray-600">14:15</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">Rp 82.000</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Sudah Bayar</span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Detail</a>
                        </td>
                    </tr>
                    <!-- Row 3 - Paid -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#ORD-2024-8840</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Bungkus</td>
                        <td class="px-4 py-4 text-sm text-gray-600">14:10</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">Rp 210.000</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Sudah Bayar</span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Detail</a>
                        </td>
                    </tr>
                    <!-- Row 4 - Cancelled -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#ORD-2024-8839</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Meja 12</td>
                        <td class="px-4 py-4 text-sm text-gray-600">14:05</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">Rp 55.000</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Dibatalkan</span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Detail</a>
                        </td>
                    </tr>
                    <!-- Row 5 - Paid -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#ORD-2024-8838</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Meja 8</td>
                        <td class="px-4 py-4 text-sm text-gray-600">13:58</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">Rp 320.500</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Sudah Bayar</span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Details</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
