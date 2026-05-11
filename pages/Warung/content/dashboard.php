<?php // Konten: Overview Warung ?>

<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.625rem;
        font-weight: 700;
        line-height: 1;
        text-transform: uppercase;
    }

    .status-incoming {
        background-color: #FFF7ED;
        color: #C2410C;
        border: 1px solid #FFEDD5;
    }

    .status-preparing {
        background-color: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #DBEAFE;
    }

    .status-ready {
        background-color: #F5F3FF;
        color: #7C3AED;
        border: 1px solid #EDE9FE;
    }

    .status-picked-up {
        background-color: #ECFDF5;
        color: #059669;
        border: 1px solid #D1FAE5;
    }

    .card-stat {
        transition: all 0.2s ease;
    }
    .card-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="flex flex-col gap-5">

    <!-- Page Title & Greeting -->
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900">Overview</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau performa stan dan kelola antrean pesanan Anda.</p>
    </div>

    <!-- Incoming Order Alert -->
    <div class="bg-red-50 border border-red-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-5 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white border border-red-100 flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg width="22" height="24" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 19V17H3.5V10C3.5 8.31667 4.02083 6.8125 5.0625 5.4875C6.10417 4.1625 7.45 3.31667 9.1 2.95V2C9.1 1.58333 9.24583 1.22917 9.5375 0.9375C9.82917 0.645833 10.1833 0.5 10.6 0.5C11.0167 0.5 11.3708 0.645833 11.6625 0.9375C11.9542 1.22917 12.1 1.58333 12.1 2V2.95C13.75 3.31667 15.0958 4.1625 16.1375 5.4875C17.1792 6.8125 17.7 8.31667 17.7 10V17H20.2V19H1ZM10.6 24C9.93333 24 9.36458 23.7625 8.89375 23.2875C8.42292 22.8125 8.1875 22.2333 8.1875 21.55H13.0125C13.0125 22.2333 12.7771 22.8125 12.3063 23.2875C11.8354 23.7625 11.2667 24 10.6 24ZM5.5 17H15.7V10C15.7 8.61667 15.2208 7.4375 14.2625 6.4625C13.3042 5.4875 12.15 5 10.8 5C9.45 5 8.29583 5.4875 7.3375 6.4625C6.37917 7.4375 5.9 8.61667 5.9 10V17H5.5Z" fill="#991B1B"/>
                    <circle cx="17" cy="4" r="4" fill="#DC2626" stroke="white" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <p class="text-[#991B1B] font-bold text-base leading-tight">Pesanan Masuk</p>
                <p class="text-gray-500 text-sm mt-0.5">Order #8842 dari Sarah J. baru saja tiba.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <button class="bg-[#7F1D1D] hover:bg-[#991B1B] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm active:scale-95">
                Lihat Pesanan
            </button>
            <button class="bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl border border-gray-200 transition-colors active:scale-95">
                Detail
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Incoming Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                </div>
                <span class="text-emerald-600 text-xs font-semibold flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 2L10 6H7V10H5V6H2L6 2Z" fill="#16A34A"/></svg>
                    +8%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Pesanan Masuk</p>
            <p class="text-gray-900 text-3xl font-bold mt-1">12</p>
        </div>

        <!-- Active Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <span class="text-emerald-600 text-xs font-semibold flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 2L10 6H7V10H5V6H2L6 2Z" fill="#16A34A"/></svg>
                    +12%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Pesanan Aktif</p>
            <p class="text-gray-900 text-3xl font-bold mt-1">45</p>
        </div>

        <!-- Completed Today -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <span class="text-red-500 text-xs font-semibold flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" class="rotate-180"><path d="M6 2L10 6H7V10H5V6H2L6 2Z" fill="#EF4444"/></svg>
                    -2%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Selesai Hari Ini</p>
            <p class="text-gray-900 text-3xl font-bold mt-1">28</p>
        </div>

        <!-- Daily Revenue -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-[#7B0009]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <span class="text-gray-400 text-xs font-semibold">Total</span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Pendapatan Hari Ini</p>
            <p class="text-gray-900 text-2xl font-bold mt-1">Rp 1.240.000</p>
        </div>
    </div>

    <!-- Live Order Queue -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-2">
        <!-- Table Header -->
        <div class="flex items-center justify-between px-6 py-5">
            <h2 class="text-gray-900 font-bold text-lg">Antrean Pesanan Langsung</h2>
            <div class="flex items-center gap-2">
                <button class="bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-bold px-4 py-2 rounded-xl border border-gray-100 transition-colors">
                    Filter
                </button>
                <button class="bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-bold px-4 py-2 rounded-xl border border-gray-100 transition-colors">
                    Ekspor
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-t border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">ID Pesanan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Item</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wide">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#8842</td>
                        <td class="px-4 py-4 text-sm text-gray-600 font-semibold">Sarah Johnson</td>
                        <td class="px-4 py-4 text-sm text-gray-500">2x Carbonara, 1x Coke</td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-gray-900">Rp 325.000</td>
                        <td class="px-4 py-4 text-center">
                            <span class="status-badge status-incoming">MASUK</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Detail</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#8839</td>
                        <td class="px-4 py-4 text-sm text-gray-600 font-semibold">Michael Chen</td>
                        <td class="px-4 py-4 text-sm text-gray-500">1x Bolognese (L)</td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-gray-900">Rp 189.000</td>
                        <td class="px-4 py-4 text-center">
                            <span class="status-badge status-preparing">DIPROSES</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Detail</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#8835</td>
                        <td class="px-4 py-4 text-sm text-gray-600 font-semibold">Elena Rodriguez</td>
                        <td class="px-4 py-4 text-sm text-gray-500">3x Garlic Bread, 2x Lasagna</td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-gray-900">Rp 450.000</td>
                        <td class="px-4 py-4 text-center">
                            <span class="status-badge status-ready">SIAP</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="#" class="text-sm font-bold text-[#991B1B] hover:underline">Detail</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400 font-medium">
                Menampilkan <span class="text-gray-900 font-bold">3</span> dari <span class="text-gray-900 font-bold">28</span> pesanan aktif
            </p>
            <div class="flex items-center gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
        <!-- Revenue Chart Placeholder -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Tren Pendapatan</h3>
                <span class="text-xs font-bold text-[#7B0009] bg-red-50 px-2 py-1 rounded-lg">+$1,240.00</span>
            </div>
            <div class="h-40 bg-gray-50 rounded-xl flex items-center justify-center border border-dashed border-gray-200">
                <p class="text-xs font-bold text-gray-300 uppercase tracking-widest italic">Grafik Pendapatan</p>
            </div>
        </div>

        <!-- Heatmap Placeholder -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Volume Pesanan</h3>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Puncak: 12:00 - 14:00</span>
            </div>
            <div class="h-40 bg-gray-50 rounded-xl flex items-center justify-center border border-dashed border-gray-200">
                <p class="text-xs font-bold text-gray-300 uppercase tracking-widest italic">Heatmap Aktivitas</p>
            </div>
        </div>
    </div>

</div>
