<?php
// User Management — Admin High-Fidelity Design
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Pengguna</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Kelola akun kasir, pemilik warung, dan hak akses sistem.</p>
    </div>
    <button class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        Tambah Pengguna Baru
    </button>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <!-- Total Pengguna -->
    <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-1">Total Pengguna</p>
            <p class="poppins text-2xl font-black text-[var(--text-dark)]">128</p>
        </div>
    </div>
    <!-- Pemilik Warung Aktif -->
    <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-1">Pemilik Warung Aktif</p>
            <p class="poppins text-2xl font-black text-[var(--text-dark)]">42</p>
        </div>
    </div>
    <!-- Kasir Terdaftar -->
    <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-1">Kasir Terdaftar</p>
            <p class="poppins text-2xl font-black text-[var(--text-dark)]">86</p>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50 mb-8 flex flex-wrap items-center gap-6">
    <div class="flex-1 min-w-[300px] relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" placeholder="Cari Nama, Email atau No. Telepon" class="w-full pl-12 pr-6 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--text-dark)] outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
    </div>
    
    <div class="flex items-center gap-4">
        <div class="space-y-1">
            <p class="text-[8px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Peran</p>
            <select class="bg-[#FAF7F6] px-4 py-2.5 rounded-xl text-xs font-bold text-[var(--text-dark)] border-none outline-none cursor-pointer min-w-[140px]">
                <option>Semua Peran</option>
                <option>Pemilik Warung</option>
                <option>Kasir</option>
            </select>
        </div>
        <div class="space-y-1">
            <p class="text-[8px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Status</p>
            <select class="bg-[#FAF7F6] px-4 py-2.5 rounded-xl text-xs font-bold text-[var(--text-dark)] border-none outline-none cursor-pointer min-w-[140px]">
                <option>Semua Status</option>
                <option>Aktif</option>
                <option>Nonaktif</option>
            </select>
        </div>
        <button class="w-12 h-12 rounded-2xl bg-[#FAF7F6] flex items-center justify-center text-[var(--text-muted)] hover:bg-[var(--brand-muted)] hover:text-[var(--brand)] transition-all mt-4">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="2" y1="14" x2="6" y2="14"/><line x1="10" y1="8" x2="14" y2="8"/><line x1="18" y1="16" x2="22" y2="16"/>
            </svg>
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#FDE8E4]/50">
                <tr>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Nama Pengguna</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Peran</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Tenant / Unit</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Email / No. Telp</th>
                    <th class="px-10 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                    <th class="px-10 py-5 text-center text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php
                $users = [
                    ['Andi Ardiansyah', 'USR-0012', 'Pemilik Warung', 'Warung Barokah', 'andi.ard@email.com', '+62 812-3456-7890', 'Aktif', 'AA'],
                    ['Siti Khadijah', 'USR-0045', 'Kasir', 'Kantin Utama A', 'siti.kh@email.com', '+62 813-9876-5432', 'Aktif', 'SK'],
                    ['Bambang Pamungkas', 'USR-0089', 'Kasir', 'Pojok Rasa', 'bambang.p@email.com', '+62 877-1122-3344', 'Nonaktif', 'BP'],
                    ['Rina Melati', 'USR-0102', 'Pemilik Warung', 'Kedai Hijau', 'rina.melati@email.com', '+62 852-4455-6677', 'Aktif', 'RM'],
                ];

                foreach ($users as $u):
                    $isWarung = $u[2] === 'Pemilik Warung';
                    $isActive = $u[6] === 'Aktif';
                ?>
                <tr class="hover:bg-[#FAF7F6] transition-colors">
                    <td class="px-10 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-[#F5D5CE] flex items-center justify-center text-[11px] font-black text-[var(--brand)]">
                                <?= $u[7] ?>
                            </div>
                            <div>
                                <p class="text-sm font-black text-[var(--text-dark)] leading-tight"><?= $u[0] ?></p>
                                <p class="text-[10px] font-bold text-gray-400 mt-0.5">ID: <?= $u[1] ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-10 py-6">
                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider <?= $isWarung ? 'bg-[#FDE8E4] text-[var(--brand)]' : 'bg-gray-100 text-gray-500' ?>">
                            <?= $u[2] ?>
                        </span>
                    </td>
                    <td class="px-10 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= $u[3] ?></td>
                    <td class="px-10 py-6">
                        <p class="text-xs font-bold text-[var(--text-dark)]"><?= $u[4] ?></p>
                        <p class="text-[10px] font-bold text-gray-400 mt-1"><?= $u[5] ?></p>
                    </td>
                    <td class="px-10 py-6">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full <?= $isActive ? 'bg-[#16A34A]' : 'bg-gray-400' ?>"></span>
                            <span class="text-xs font-bold <?= $isActive ? 'text-[#16A34A]' : 'text-gray-400' ?>"><?= $u[6] ?></span>
                        </div>
                    </td>
                    <td class="px-10 py-6">
                        <div class="flex items-center justify-center gap-2">
                            <button class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[var(--brand)] hover:bg-[#FDE8E4] transition-all" title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[var(--brand)] hover:bg-[#FDE8E4] transition-all" title="Reset Password">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/>
                                </svg>
                            </button>
                            <button class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[#BA1A1A] hover:bg-red-50 transition-all" title="Delete">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between bg-[#FAF7F6]/30">
        <p class="text-xs font-bold text-gray-400">Menampilkan 1-10 dari 128 pengguna</p>
        <div class="flex items-center gap-2">
            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-white transition-all">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
            <button class="w-8 h-8 rounded-lg bg-[var(--brand)] text-white text-xs font-black shadow-sm">1</button>
            <button class="w-8 h-8 rounded-lg text-xs font-bold text-gray-500 hover:bg-white transition-all">2</button>
            <button class="w-8 h-8 rounded-lg text-xs font-bold text-gray-500 hover:bg-white transition-all">3</button>
            <span class="text-gray-300 mx-1">...</span>
            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-white transition-all">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
        </div>
    </div>
</div>
