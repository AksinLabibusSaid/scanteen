<?php // Table Management - Admin ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Table Management</h2>
        <p class="text-sm text-gray-500 mt-1">Monitor dan kelola status meja di kantin.</p>
    </div>
    <button id="btn-add-table"
            class="flex items-center gap-2 bg-[#991B1B] hover:bg-[#7f1d1d] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/></svg>
        Tambah Meja
    </button>
</div>

<!-- Legend -->
<div class="flex items-center gap-5 mb-6 text-xs font-semibold">
    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span> Tersedia</div>
    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Terisi</div>
    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> Nonaktif</div>
</div>

<!-- Table Grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-4 mb-8">
    <?php
    $tables = [
        ['Meja 1',  'Tersedia', 4],
        ['Meja 2',  'Terisi',   4],
        ['Meja 3',  'Terisi',   6],
        ['Meja 4',  'Tersedia', 4],
        ['Meja 5',  'Terisi',   4],
        ['Meja 6',  'Tersedia', 6],
        ['Meja 7',  'Nonaktif', 4],
        ['Meja 8',  'Terisi',   2],
        ['Meja 9',  'Tersedia', 4],
        ['Meja 10', 'Tersedia', 4],
        ['Meja 11', 'Terisi',   6],
        ['Meja 12', 'Nonaktif', 4],
    ];
    $styles = [
        'Tersedia' => ['border-green-200 bg-green-50',  'text-green-600', 'bg-green-100 text-green-700'],
        'Terisi'   => ['border-yellow-200 bg-yellow-50','text-yellow-600','bg-yellow-100 text-yellow-700'],
        'Nonaktif' => ['border-gray-200 bg-gray-50',   'text-gray-400',  'bg-gray-100 text-gray-500'],
    ];
    foreach ($tables as $t):
        [$card, $icon, $badge] = $styles[$t[1]] ?? $styles['Tersedia']; ?>
    <div class="bg-white rounded-2xl border <?= $card ?> p-4 text-center hover:shadow-md transition-shadow cursor-pointer">
        <svg class="mx-auto mb-2 <?= $icon ?>" width="32" height="28" viewBox="0 0 32 28" fill="none">
            <path d="M1 28V26H3V21H1V19H3V2C3 1.45 3.196 0.979 3.588 0.588C3.979 0.196 4.45 0 5 0H27C27.55 0 28.021 0.196 28.413 0.588C28.804 0.979 29 1.45 29 2V19H31V21H29V26H31V28H1ZM5 19H13V2H5V19ZM15 19H27V2H15V19ZM5 26H27V21H5V26Z" fill="currentColor"/>
        </svg>
        <p class="font-bold text-gray-800 text-sm"><?= $t[0] ?></p>
        <p class="text-xs text-gray-400 mt-0.5"><?= $t[2] ?> kursi</p>
        <span class="mt-2 inline-block px-2 py-0.5 rounded-full text-[10px] font-bold <?= $badge ?>"><?= $t[1] ?></span>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table Stats -->
<div class="grid grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <p class="text-3xl font-black text-green-600">5</p>
        <p class="text-xs font-semibold text-gray-400 mt-1">Meja Tersedia</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <p class="text-3xl font-black text-yellow-500">5</p>
        <p class="text-xs font-semibold text-gray-400 mt-1">Meja Terisi</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <p class="text-3xl font-black text-gray-400">2</p>
        <p class="text-xs font-semibold text-gray-400 mt-1">Meja Nonaktif</p>
    </div>
</div>
