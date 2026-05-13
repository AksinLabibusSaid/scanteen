<?php // Reports - Admin ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Reports</h2>
        <p class="text-sm text-gray-500 mt-1">Laporan penjualan dan performa sistem secara keseluruhan.</p>
    </div>
    <div class="flex gap-3">
        <select class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-600 outline-none focus:ring-2 focus:ring-red-200">
            <option>Bulan Ini</option>
            <option>Minggu Ini</option>
            <option>Hari Ini</option>
            <option>Custom</option>
        </select>
        <button class="flex items-center gap-2 border border-[#991B1B] text-[#991B1B] hover:bg-red-50 text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1 11H13V13H1V11ZM7.5 9.5L3 5H6V1H8V5H11L7.5 9.5Z" fill="#991B1B"/></svg>
            Export PDF
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <?php
    $summaries = [
        ['Total Pendapatan','Rp 84,2 Jt',  '+15% vs bulan lalu', 'text-green-600'],
        ['Total Pesanan',   '8.241',        '+9% vs bulan lalu',  'text-green-600'],
        ['Rata-rata/Hari',  'Rp 2,7 Jt',   '±3% stabil',         'text-gray-400'],
        ['Pesanan Batal',   '127',          '-5% membaik',        'text-green-600'],
    ];
    foreach ($summaries as $s): ?>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3"><?= $s[0] ?></p>
        <p class="text-2xl font-black text-gray-900"><?= $s[1] ?></p>
        <p class="text-xs font-semibold <?= $s[3] ?> mt-1"><?= $s[2] ?></p>
    </div>
    <?php endforeach; ?>
</div>

<!-- Top Tenants -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Top Tenant Bulan Ini</h3>
        </div>
        <div class="p-6 space-y-4">
            <?php
            $topTenants = [
                ['Warung Pak Budi',    'Rp 21,4 Jt', 82],
                ['Nasi Goreng Spesial','Rp 18,7 Jt', 72],
                ['Mie Ayam Barokah',  'Rp 15,2 Jt', 58],
                ['Soto Betawi Asli',  'Rp 12,1 Jt', 46],
                ['Es Campur Segar',   'Rp  9,8 Jt', 37],
            ];
            foreach ($topTenants as $i => $tt): ?>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-gray-400 w-4"><?= $i + 1 ?>.</span>
                        <span class="text-sm font-semibold text-gray-700"><?= $tt[0] ?></span>
                    </div>
                    <span class="text-sm font-bold text-gray-800"><?= $tt[1] ?></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-[#991B1B] h-1.5 rounded-full transition-all" style="width: <?= $tt[2] ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Menu Items -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Menu Terlaris Bulan Ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Terjual</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $topMenus = [
                        ['Nasi Rendang',       '1.241', 'Rp 22,3 Jt'],
                        ['Mie Ayam Biasa',     '1.085', 'Rp 14,1 Jt'],
                        ['Nasi Goreng Spesial','  987', 'Rp 14,8 Jt'],
                        ['Ayam Bakar',         '  854', 'Rp 17,1 Jt'],
                        ['Soto Betawi',        '  731', 'Rp 12,4 Jt'],
                    ];
                    foreach ($topMenus as $i => $tm): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-black text-gray-300 text-xs"><?= $i + 1 ?></td>
                        <td class="px-6 py-3 font-semibold text-gray-700"><?= $tm[0] ?></td>
                        <td class="px-6 py-3 text-gray-500"><?= $tm[1] ?></td>
                        <td class="px-6 py-3 font-semibold text-gray-800"><?= $tm[2] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
