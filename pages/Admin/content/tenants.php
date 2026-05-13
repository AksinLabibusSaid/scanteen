<?php
// Tenant Management — Admin High-Fidelity Design
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Warung</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Kelola operasional, pantau kinerja, dan atur ketersediaan tenant kantin.</p>
    </div>
    <button class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/>
        </svg>
        Tambah Warung Baru
    </button>
</div>

<!-- Summary Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Total Tenant -->
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Total Tenant</p>
        <p class="poppins text-3xl font-black text-[var(--brand)]">24</p>
    </div>
    <!-- Tenant Aktif -->
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Tenant Aktif</p>
        <p class="poppins text-3xl font-black text-[#16A34A]">18</p>
    </div>
    <!-- Dalam Pemeliharaan -->
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Dalam Pemeliharaan</p>
        <p class="poppins text-3xl font-black text-[#D97706]">4</p>
    </div>
    <!-- Rata-rata Kinerja -->
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Rata-rata Kinerja</p>
        <p class="poppins text-3xl font-black text-[var(--brand)]">94.2%</p>
    </div>
</div>

<!-- Tenant Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    <?php
    $tenants = [
        [
            'name' => 'Warung Barokah',
            'owner' => 'H. Slamet Riyadi',
            'initials' => 'WB',
            'status' => 'OPEN NOW',
            'operational' => 'BUKA',
            'peak' => '12:00 - 13:30',
            'type' => 'maroon'
        ],
        [
            'name' => 'The Artisan Bakery',
            'owner' => 'Amanda Veranda',
            'initials' => 'AB',
            'status' => 'CLOSED TEMPORARILY',
            'operational' => 'TUTUP',
            'peak' => '08:00 - 10:00',
            'type' => 'grey'
        ],
        [
            'name' => 'Spice Fusion',
            'owner' => 'Chef Chandra',
            'initials' => 'SF',
            'status' => 'OPEN NOW',
            'operational' => 'BUKA',
            'peak' => '13:00 - 15:00',
            'type' => 'maroon'
        ]
    ];

    foreach ($tenants as $t):
        $isOpen = $t['status'] === 'OPEN NOW';
        $isMaintenance = $t['status'] === 'MAINTENANCE';
    ?>
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden group">
        <!-- Top Section -->
        <div class="p-8 pb-6 border-b border-gray-50">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <?php if ($t['name'] === 'Warung Barokah'): ?>
                        <div class="w-14 h-14 rounded-full bg-[#FDE8E4] flex items-center justify-center text-lg font-black text-[var(--brand)]">
                            WB
                        </div>
                    <?php else: ?>
                        <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-gray-50">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= urlencode($t['name']) ?>" class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="poppins text-lg font-black text-[var(--text-dark)] leading-tight"><?= $t['name'] ?></h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span class="text-[10px] font-bold text-gray-400"><?= $t['owner'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-1.5">
                    <button class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[var(--brand)] hover:bg-[#FDE8E4] transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[#BA1A1A] hover:bg-red-50 transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mt-6 flex">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full <?= $isOpen ? 'bg-[#F0FDF4]' : 'bg-[#FFFBEB]' ?>">
                    <span class="w-1.5 h-1.5 rounded-full <?= $isOpen ? 'bg-[#16A34A]' : 'bg-[#D97706]' ?> shadow-sm"></span>
                    <span class="text-[9px] font-black uppercase tracking-wider <?= $isOpen ? 'text-[#16A34A]' : 'text-[#D97706]' ?>">
                        <?= $t['status'] ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="p-8 pt-6">
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-4">Kendalikan Operasional</p>
            <div class="flex gap-3">
                <!-- Buka Button -->
                <button class="flex-1 px-4 py-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-0.5 <?= $t['operational'] === 'BUKA' ? 'bg-[var(--brand)] border-[var(--brand)] text-white' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200' ?>">
                    <span class="text-[10px] font-black uppercase tracking-widest">Buka</span>
                    <span class="text-[9px] font-bold opacity-80">Pesanan</span>
                </button>
                <!-- Tutup Button -->
                <button class="flex-1 px-4 py-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-0.5 <?= $t['operational'] === 'TUTUP' ? 'bg-[#5A6472] border-[#5A6472] text-white' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200' ?>">
                    <span class="text-[10px] font-black uppercase tracking-widest">Tutup</span>
                    <span class="text-[9px] font-bold opacity-80">Pesanan</span>
                </button>
                <!-- Maintenance Button -->
                <button class="w-14 rounded-2xl border-2 border-gray-100 bg-white flex items-center justify-center text-gray-400 hover:border-[#D97706] hover:text-[#D97706] transition-all">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </button>
            </div>

            <!-- Footer Peak Hours -->
            <div class="mt-8 px-6 py-3 bg-[#FAF7F6] rounded-2xl flex items-center justify-between border border-gray-50">
                <div class="flex flex-col">
                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Peak Hours:</span>
                    <span class="text-[10px] font-black text-[var(--text-dark)] leading-none"><?= $t['peak'] ?></span>
                </div>
                <div class="flex items-end gap-0.5 h-6 opacity-40">
                    <div class="w-0.5 bg-[var(--brand)] h-2"></div>
                    <div class="w-0.5 bg-[var(--brand)] h-4"></div>
                    <div class="w-0.5 bg-[var(--brand)] h-6"></div>
                    <div class="w-0.5 bg-[var(--brand)] h-3"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Status History Table -->
<div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
    <div class="px-10 py-8 flex items-center justify-between">
        <h3 class="poppins text-lg font-bold text-[var(--brand)]">Riwayat Perubahan Status</h3>
        <a href="#" class="text-[11px] font-black text-[var(--brand)] uppercase tracking-widest hover:opacity-70 flex items-center gap-2">
            Lihat Semua Log
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#FAF7F6]">
                <tr>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Waktu</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Warung</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Aksi</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Oleh</th>
                    <th class="px-10 py-5 text-center text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-[#FAF7F6] transition-colors">
                    <td class="px-10 py-6 text-sm font-bold text-gray-400">Today, 10:45</td>
                    <td class="px-10 py-6 text-sm font-black text-[var(--text-dark)]">Warung Barokah</td>
                    <td class="px-10 py-6 text-sm font-bold text-gray-500">Membuka Pesanan</td>
                    <td class="px-10 py-6 text-sm font-bold text-gray-500">Slamet (Owner)</td>
                    <td class="px-10 py-6 text-center">
                        <span class="px-3 py-1 bg-[#F0FDF4] text-[#16A34A] rounded-full text-[9px] font-black uppercase tracking-wider">Success</span>
                    </td>
                </tr>
                <tr class="hover:bg-[#FAF7F6] transition-colors">
                    <td class="px-10 py-6 text-sm font-bold text-gray-400">Today, 08:30</td>
                    <td class="px-10 py-6 text-sm font-black text-[var(--text-dark)]">The Artisan Bakery</td>
                    <td class="px-10 py-6 text-sm font-bold text-gray-500">Mode Pemeliharaan</td>
                    <td class="px-10 py-6 text-sm font-bold text-gray-500">Admin System</td>
                    <td class="px-10 py-6 text-center">
                        <span class="px-3 py-1 bg-[#FFFBEB] text-[#D97706] rounded-full text-[9px] font-black uppercase tracking-wider">Scheduled</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
