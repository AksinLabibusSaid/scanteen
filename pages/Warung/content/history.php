<?php // Konten: Riwayat Pesanan Warung ?>

<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-delivered {
        background-color: #ECFDF5;
        color: #059669;
    }

    .status-preparing {
        background-color: #EFF6FF;
        color: #1D4ED8;
    }

    .status-refunded {
        background-color: #FFF7ED;
        color: #C2410C;
    }

    .history-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        border: 1px solid #F1F3F5;
    }

    .table-header-text {
        font-size: 11px;
        font-weight: 800;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .row-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #F8FAFC;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7B0009;
        border: 1px solid #F1F5F9;
    }
</style>

<div class="flex flex-col gap-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Riwayat Pesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar lengkap transaksi yang telah diproses oleh stan Anda.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="#" class="text-sm font-bold text-[#7B0009] hover:underline">Lihat Log Lengkap</a>
            <button class="flex items-center gap-2 rounded-xl bg-[#7B0009] px-6 py-3 text-sm font-bold text-white hover:bg-[#991B1B] transition-all shadow-sm active:scale-95">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"></path></svg>
                Ekspor Laporan
            </button>
        </div>
    </div>

    <!-- History Table Card -->
    <div class="history-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-8 py-6 text-left table-header-text">ID PESANAN</th>
                        <th class="px-4 py-6 text-left table-header-text">PELANGGAN</th>
                        <th class="px-4 py-6 text-left table-header-text">ITEM</th>
                        <th class="px-4 py-6 text-left table-header-text">MEJA/OPSI</th>
                        <th class="px-4 py-6 text-left table-header-text">WAKTU</th>
                        <th class="px-4 py-6 text-right table-header-text">TOTAL</th>
                        <th class="px-4 py-6 text-left table-header-text">PEMBAYARAN</th>
                        <th class="px-8 py-6 text-center table-header-text">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="row-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zm0 2h12l2 4H4l2-4zm14 16H4V8h16v12zm-8-9a3 3 0 0 1-3-3V7h2v1a1 1 0 0 0 2 0V7h2v1a3 3 0 0 1-3 3z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-900">#ORD-9405</span>
                            </div>
                        </td>
                        <td class="px-4 py-6 text-sm font-semibold text-gray-600">Sarah Jenkins</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">3 Item (Mains, Drink)</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">Meja 12</td>
                        <td class="px-4 py-6 text-sm text-gray-400 font-medium leading-tight">24 Okt,<br>14:15:30</td>
                        <td class="px-4 py-6 text-right text-sm font-black text-gray-900">Rp 345.000</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-semibold">Dompet Digital</td>
                        <td class="px-8 py-6 text-center">
                            <span class="status-pill status-delivered">TERKIRIM</span>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="row-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zm0 2h12l2 4H4l2-4zm14 16H4V8h16v12zm-8-9a3 3 0 0 1-3-3V7h2v1a1 1 0 0 0 2 0V7h2v1a3 3 0 0 1-3 3z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-900">#ORD-9404</span>
                            </div>
                        </td>
                        <td class="px-4 py-6 text-sm font-semibold text-gray-600">Mark Thompson</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">1 Item (Salad)</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">Bungkus</td>
                        <td class="px-4 py-6 text-sm text-gray-400 font-medium leading-tight">24 Okt,<br>14:02:10</td>
                        <td class="px-4 py-6 text-right text-sm font-black text-gray-900">Rp 140.000</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-semibold">Kartu Kredit</td>
                        <td class="px-8 py-6 text-center">
                            <span class="status-pill status-preparing">DIPROSES</span>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="row-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zm0 2h12l2 4H4l2-4zm14 16H4V8h16v12zm-8-9a3 3 0 0 1-3-3V7h2v1a1 1 0 0 0 2 0V7h2v1a3 3 0 0 1-3 3z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-900">#ORD-9403</span>
                            </div>
                        </td>
                        <td class="px-4 py-6 text-sm font-semibold text-gray-600">Dr. Emily Chen</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">5 Item (Party Pack)</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">Ruang VIP</td>
                        <td class="px-4 py-6 text-sm text-gray-400 font-medium leading-tight">24 Okt,<br>13:45:00</td>
                        <td class="px-4 py-6 text-right text-sm font-black text-gray-900">Rp 822.000</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-semibold">Kredit Akun</td>
                        <td class="px-8 py-6 text-center">
                            <span class="status-pill status-delivered">TERKIRIM</span>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="row-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zm0 2h12l2 4H4l2-4zm14 16H4V8h16v12zm-8-9a3 3 0 0 1-3-3V7h2v1a1 1 0 0 0 2 0V7h2v1a3 3 0 0 1-3 3z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-900">#ORD-9402</span>
                            </div>
                        </td>
                        <td class="px-4 py-6 text-sm font-semibold text-gray-600">Robert Wilson</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">2 Item (Burgers)</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium">Meja 04</td>
                        <td class="px-4 py-6 text-sm text-gray-400 font-medium leading-tight">24 Okt,<br>13:10:45</td>
                        <td class="px-4 py-6 text-right text-sm font-black text-gray-900">Rp 280.000</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-semibold">Kartu Debit</td>
                        <td class="px-8 py-6 text-center">
                            <span class="status-pill status-refunded">DIKEMBALIKAN</span>
                        </td>
                    </tr>

                    <!-- Row 5 -->
                    <tr class="hover:bg-gray-50/50 transition-colors border-b-0">
                        <td class="px-8 py-6 border-b-0">
                            <div class="flex items-center gap-4">
                                <div class="row-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zm0 2h12l2 4H4l2-4zm14 16H4V8h16v12zm-8-9a3 3 0 0 1-3-3V7h2v1a1 1 0 0 0 2 0V7h2v1a3 3 0 0 1-3 3z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-900">#ORD-9401</span>
                            </div>
                        </td>
                        <td class="px-4 py-6 text-sm font-semibold text-gray-600 border-b-0">Lisa Garcia</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium border-b-0">1 Item (Combo)</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-medium border-b-0">Meja 08</td>
                        <td class="px-4 py-6 text-sm text-gray-400 font-medium leading-tight border-b-0">24 Okt,<br>12:55:20</td>
                        <td class="px-4 py-6 text-right text-sm font-black text-gray-900 border-b-0">Rp 185.000</td>
                        <td class="px-4 py-6 text-sm text-gray-500 font-semibold border-b-0">Tunai</td>
                        <td class="px-8 py-6 text-center border-b-0">
                            <span class="status-pill status-delivered">TERKIRIM</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="px-8 py-5 bg-[#FAF9F9] border-t border-gray-50 flex items-center justify-between">
            <p class="text-xs font-bold text-gray-400">Menampilkan <span class="text-gray-900">5</span> transaksi terbaru</p>
            <div class="flex items-center gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-xl bg-[#7B0009] text-xs font-bold text-white shadow-sm">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>
</div>
