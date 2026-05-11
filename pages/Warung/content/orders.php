<?php // Konten: Manajemen Pesanan Warung ?>

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
        letter-spacing: 0.05em;
    }

    .status-new {
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

    .status-done {
        background-color: #ECFDF5;
        color: #059669;
        border: 1px solid #D1FAE5;
    }

    .active-tab {
        background-color: white;
        color: #7B0009;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
</style>

<div class="flex flex-col gap-5">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">Manajemen Pesanan</h1>
            <p class="mt-1 text-sm text-gray-500 font-normal">Pemantauan transaksi dan pemenuhan pesanan stan secara real-time.</p>
        </div>
        <button class="flex items-center gap-2 self-start shrink-0 rounded-xl bg-[#7B0009] px-5 py-3 text-sm font-bold text-white hover:bg-[#991B1B] transition-all shadow-sm active:scale-95">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 3.33301V12.6663M3.33333 7.99967H12.6667" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Buat Pesanan Baru
        </button>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Incoming Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-[#7B0009]">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                </div>
                <span class="flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-700">
                    <svg width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M6 2L10 6H7V10H5V6H2L6 2Z" fill="currentColor"/></svg>
                    +12%
                </span>
            </div>
            <p class="text-[10px] font-bold tracking-[1.1px] uppercase text-gray-400 mb-1">Pesanan Masuk</p>
            <p class="text-3xl font-bold text-gray-900">24</p>
        </div>

        <!-- Active Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                </div>
                <span class="rounded-full bg-gray-50 px-2.5 py-1 text-[11px] font-bold text-gray-400">Stabil</span>
            </div>
            <p class="text-[10px] font-bold tracking-[1.1px] uppercase text-gray-400 mb-1">Pesanan Aktif</p>
            <p class="text-3xl font-bold text-gray-900">08</p>
        </div>

        <!-- Completed Today -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <span class="rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-700">Rekor</span>
            </div>
            <p class="text-[10px] font-bold tracking-[1.1px] uppercase text-gray-400 mb-1">Selesai Hari Ini</p>
            <p class="text-3xl font-bold text-gray-900">142</p>
        </div>
    </div>

    <!-- Live Order Queue -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Panel Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-5 border-b border-gray-50">
            <h2 class="text-xl font-bold text-gray-900">Antrean Pesanan Langsung</h2>
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Tab Filters -->
                <div class="flex items-center gap-1 rounded-xl bg-gray-100/50 p-1">
                    <button class="active-tab rounded-lg px-4 py-1.5 text-xs font-bold transition-all">Semua</button>
                    <button class="rounded-lg px-4 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 transition-all">Baru</button>
                    <button class="rounded-lg px-4 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 transition-all">Diproses</button>
                </div>
                <!-- Divider -->
                <div class="hidden sm:block h-8 w-px bg-gray-200 mx-1"></div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button class="flex items-center gap-2 rounded-xl border border-gray-100 bg-white px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filter
                    </button>
                    <button class="flex items-center gap-2 rounded-xl border border-gray-100 bg-white px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4M7 10l5 5 5-5M12 15V3"></path></svg>
                        Ekspor
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50 bg-[#FAF9F9]">
                        <th class="px-6 py-4 text-left text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">ID Pesanan</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Pelanggan</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Item</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Total</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold tracking-[1.5px] uppercase text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 text-sm font-bold text-gray-900">#ORD-9021</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-100 text-[11px] font-bold text-[#7B0009]">JD</div>
                                <span class="text-sm font-semibold text-gray-700">Jane Doe</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-400 font-medium leading-relaxed">2x Grilled Chicken, 1x Fresh Juice</td>
                        <td class="px-6 py-5 text-right text-sm font-black text-gray-900">Rp 285.000</td>
                        <td class="px-6 py-5 text-center">
                            <span class="status-badge status-new">BARU</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <button class="text-xs font-bold text-[#7B0009] hover:underline">Detail</button>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 text-sm font-bold text-gray-900">#ORD-9020</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[11px] font-bold text-blue-800">MS</div>
                                <span class="text-sm font-semibold text-gray-700">Michael Smith</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-400 font-medium leading-relaxed">1x Premium Steak, 1x Lemon Tea</td>
                        <td class="px-6 py-5 text-right text-sm font-black text-gray-900">Rp 640.000</td>
                        <td class="px-6 py-5 text-center">
                            <span class="status-badge status-preparing">DIPROSES</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <button class="text-xs font-bold text-[#7B0009] hover:underline">Detail</button>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 text-sm font-bold text-gray-900">#ORD-9019</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 text-[11px] font-bold text-purple-800">RW</div>
                                <span class="text-sm font-semibold text-gray-700">Robert White</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-400 font-medium leading-relaxed">3x Club Sandwiches</td>
                        <td class="px-6 py-5 text-right text-sm font-black text-gray-900">Rp 322.500</td>
                        <td class="px-6 py-5 text-center">
                            <span class="status-badge status-ready">SIAP</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <button class="text-xs font-bold text-[#7B0009] hover:underline">Detail</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-t border-gray-50 bg-white">
            <span class="text-xs font-bold text-gray-400">Menampilkan <span class="text-gray-900">5</span> dari <span class="text-gray-900">142</span> total pesanan</span>
            <div class="flex items-center gap-2">
                <button class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#7B0009] text-xs font-bold text-white shadow-sm">1</button>
                <button class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 hover:bg-gray-50">2</button>
                <button class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>

</div>
