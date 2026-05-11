<?php // Konten: Manajemen Menu Warung ?>

<style>
    .card-menu {
        background: white;
        border: 1px solid #F1F3F5;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-menu:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .tab-btn {
        border-bottom: 2.5px solid transparent;
        color: #64748b;
        padding-bottom: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .tab-btn.active {
        border-bottom-color: #7B0009;
        color: #7B0009;
    }

    /* Toggle Switch Custom */
    .toggle-switch {
        width: 42px;
        height: 22px;
        border-radius: 9999px;
        background: #e2e8f0;
        cursor: pointer;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        padding: 0;
    }

    .toggle-switch.active {
        background: #7B0009;
    }

    .toggle-circle {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .toggle-switch.active .toggle-circle {
        transform: translateX(20px);
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        background: #FEF2F2;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        color: #991B1B;
    }

    .item-image-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #F8FAFC;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #F1F5F9;
    }

    .item-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .stock-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
</style>

<div class="flex flex-col gap-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Menu</h1>
            <p class="text-sm text-gray-500 mt-1">Atur menu digital Anda dan pantau stok harian dengan mudah.</p>
        </div>
        <button class="flex items-center gap-2 self-start shrink-0 rounded-xl bg-[#7B0009] px-6 py-3 text-sm font-bold text-white hover:bg-[#991B1B] transition-all shadow-sm active:scale-95">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/>
            </svg>
            Tambah Menu Baru
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Total Items Card -->
        <div class="card-menu p-6 flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-[#7B0009]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                </div>
                <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-bold">+4 minggu ini</span>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Menu</p>
                <p class="text-3xl font-black text-gray-900 mt-1">124</p>
            </div>
        </div>

        <!-- Active Menu Card -->
        <div class="card-menu p-6 flex flex-col gap-4 border-l-4 border-emerald-500">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <span class="bg-gray-50 text-gray-400 px-3 py-1 rounded-full text-[10px] font-bold">Aktif</span>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menu Aktif</p>
                <p class="text-3xl font-black text-gray-900 mt-1">14</p>
            </div>
        </div>

        <!-- Out of Stock Card -->
        <div class="card-menu p-6 flex flex-col gap-4 border-l-4 border-red-500">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                </div>
                <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-[10px] font-bold italic">Tindakan Diperlukan</span>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Stok Habis</p>
                <p class="text-3xl font-black text-gray-900 mt-1">12</p>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Tabs & Filter -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50 flex-wrap gap-4">
            <div class="flex items-center gap-6 overflow-x-auto no-scrollbar">
                <button class="tab-btn active whitespace-nowrap">Semua Menu</button>
                <button class="tab-btn whitespace-nowrap">Makanan Utama</button>
                <button class="tab-btn whitespace-nowrap">Minuman</button>
                <button class="tab-btn whitespace-nowrap">Cemilan</button>
            </div>
            <button class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-bold px-4 py-2 rounded-xl border border-gray-100 transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Filter
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#FAF9F9] border-b border-gray-50">
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold tracking-[1.5px] uppercase text-gray-400">Menu</th>
                        <th class="px-4 py-4 text-left text-[10px] font-extrabold tracking-[1.5px] uppercase text-gray-400">Kategori</th>
                        <th class="px-4 py-4 text-left text-[10px] font-extrabold tracking-[1.5px] uppercase text-gray-400">Harga</th>
                        <th class="px-4 py-4 text-left text-[10px] font-extrabold tracking-[1.5px] uppercase text-gray-400">Stok Harian</th>
                        <th class="px-4 py-4 text-left text-[10px] font-extrabold tracking-[1.5px] uppercase text-gray-400">Ketersediaan</th>
                        <th class="px-6 py-4 text-center text-[10px] font-extrabold tracking-[1.5px] uppercase text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="menuTableBody">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="item-image-box">
                                    <img src="https://api.builder.io/api/v1/image/assets/TEMP/92fc22870690cae61ec89834fc7e9fb514cc84e9?width=69" alt="Menu">
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Nasi Goreng Spesial</h4>
                                    <p class="text-[11px] text-gray-400 font-medium">SKU: NASG-001</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="category-badge">Makanan Utama</span>
                        </td>
                        <td class="px-4 py-4">
                            <strong class="text-sm font-black text-gray-900">Rp 25.000</strong>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-900">45</span>
                                <span class="stock-label text-gray-400">/ 50 Tersisa</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <button class="toggle-switch active" onclick="this.classList.toggle('active')">
                                    <span class="toggle-circle"></span>
                                </button>
                                <span class="text-xs font-bold text-gray-900">Tersedia</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="p-2 text-gray-400 hover:text-[#7B0009] hover:bg-red-50 rounded-lg transition-all">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50/50 transition-colors opacity-70 bg-gray-50/30">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="item-image-box">
                                    <img src="https://api.builder.io/api/v1/image/assets/TEMP/9061fdc906b6957469b3c3f0e21f400c0d395d74?width=84" alt="Menu">
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Es Teh Manis</h4>
                                    <p class="text-[11px] text-gray-400 font-medium">SKU: BEV-009</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="category-badge">Minuman</span>
                        </td>
                        <td class="px-4 py-4">
                            <strong class="text-sm font-black text-gray-900">Rp 5.000</strong>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-red-600">0</span>
                                <span class="stock-label text-red-600">Habis Terjual</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <button class="toggle-switch" onclick="this.classList.toggle('active')">
                                    <span class="toggle-circle"></span>
                                </button>
                                <span class="text-xs font-bold text-red-600">Tidak Tersedia</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="p-2 text-gray-400 hover:text-[#7B0009] hover:bg-red-50 rounded-lg transition-all">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-5 bg-[#FAF9F9] border-t border-gray-50 flex items-center justify-between flex-wrap gap-4">
            <p class="text-xs font-bold text-gray-400">Menampilkan <span class="text-gray-900">2</span> dari <span class="text-gray-900">124</span> item menu</p>
            <div class="flex items-center gap-1.5">
                <button class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-xl bg-[#7B0009] text-xs font-bold text-white shadow-sm">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-600 hover:bg-gray-100">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>
</div>
