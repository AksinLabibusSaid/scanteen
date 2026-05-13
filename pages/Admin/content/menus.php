<?php // Menu Management - Admin ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Menu Management</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola seluruh item menu dari semua tenant.</p>
    </div>
    <button id="btn-add-menu"
            class="flex items-center gap-2 bg-[#991B1B] hover:bg-[#7f1d1d] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/></svg>
        Tambah Menu
    </button>
</div>

<!-- Filter Bar -->
<div class="flex gap-3 mb-6">
    <select class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-600 outline-none focus:ring-2 focus:ring-red-200">
        <option>Semua Tenant</option>
        <option>Warung Pak Budi</option>
        <option>Nasi Goreng Spesial</option>
        <option>Mie Ayam Barokah</option>
    </select>
    <select class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-600 outline-none focus:ring-2 focus:ring-red-200">
        <option>Semua Kategori</option>
        <option>Makanan</option>
        <option>Minuman</option>
        <option>Snack</option>
    </select>
</div>

<!-- Menu Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Menu</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tenant</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php
                $menus = [
                    ['Nasi Rendang',      'Warung Pak Budi',    'Makanan', 'Rp 18.000', 'Tersedia'],
                    ['Ayam Bakar',        'Warung Pak Budi',    'Makanan', 'Rp 20.000', 'Tersedia'],
                    ['Nasi Goreng Spesial','Nasi Goreng Spesial','Makanan', 'Rp 15.000', 'Tersedia'],
                    ['Mie Ayam Biasa',    'Mie Ayam Barokah',  'Makanan', 'Rp 13.000', 'Tersedia'],
                    ['Bakso Campur',      'Mie Ayam Barokah',  'Makanan', 'Rp 16.000', 'Habis'],
                    ['Es Teh Manis',      'Warung Pak Budi',   'Minuman', 'Rp  5.000', 'Tersedia'],
                    ['Soto Betawi',       'Soto Betawi Asli',  'Makanan', 'Rp 17.000', 'Tersedia'],
                    ['Jus Alpukat',       'Es Campur Segar',   'Minuman', 'Rp 12.000', 'Habis'],
                ];
                foreach ($menus as $m): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-gray-800"><?= $m[0] ?></td>
                    <td class="px-6 py-4 text-gray-500"><?= $m[1] ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700"><?= $m[2] ?></span>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-800"><?= $m[3] ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $m[4] === 'Tersedia' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?>"><?= $m[4] ?></span>
                    </td>
                    <td class="px-6 py-4 flex gap-3">
                        <button class="text-xs font-semibold text-[#991B1B] hover:underline">Edit</button>
                        <button class="text-xs font-semibold text-gray-400 hover:text-gray-600">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
