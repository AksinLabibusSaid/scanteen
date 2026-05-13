<?php // Tenant Management - Admin ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Tenant Management</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola data warung/tenant yang terdaftar di sistem.</p>
    </div>
    <button id="btn-add-tenant"
            class="flex items-center gap-2 bg-[#991B1B] hover:bg-[#7f1d1d] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/></svg>
        Tambah Tenant
    </button>
</div>

<!-- Tenant Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php
    $tenants = [
        ['Warung Pak Budi',    'Masakan Tradisional', 42, 'Aktif'],
        ['Nasi Goreng Spesial','Nasi & Mie',          38, 'Aktif'],
        ['Mie Ayam Barokah',  'Mie & Bakso',         29, 'Aktif'],
        ['Soto Betawi Asli',  'Sup & Soto',          17, 'Aktif'],
        ['Bakso Malang Joss', 'Bakso',                12, 'Nonaktif'],
        ['Es Campur Segar',   'Minuman & Jus',        8,  'Aktif'],
    ];
    foreach ($tenants as $t): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center text-[#991B1B] font-black text-lg">
                    <?= strtoupper(substr($t[0], 0, 1)) ?>
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($t[0]) ?></p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($t[1]) ?></p>
                </div>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-bold <?= $t[3] === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                <?= $t[3] ?>
            </span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="text-gray-400">Total Menu: <span class="font-bold text-gray-700"><?= $t[2] ?></span></span>
            <div class="flex gap-2">
                <button class="text-xs font-semibold text-[#991B1B] hover:underline">Edit</button>
                <span class="text-gray-200">|</span>
                <button class="text-xs font-semibold text-gray-400 hover:text-gray-600">Hapus</button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
