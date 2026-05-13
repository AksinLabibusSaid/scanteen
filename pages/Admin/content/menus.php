<?php
// Menu Management — Admin High-Fidelity Design
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Menu & Kategori</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Kelola daftar menu kantin, kategori hidangan, dan ketersediaan stok tenant.</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Warung Filter -->
        <div class="relative">
            <select class="appearance-none bg-[var(--brand-muted)] border border-[var(--brand)] text-[var(--brand)] text-xs font-black uppercase tracking-widest pl-10 pr-10 py-3 rounded-xl shadow-sm outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all cursor-pointer">
                <option>Semua Warung</option>
                <option>Warung Barokah</option>
                <option>Healthy Garden</option>
                <option>Berry Bliss</option>
                <option>Grill & Chill</option>
            </select>
            <!-- Shop Icon -->
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--brand)]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <!-- Chevron Icon -->
            <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[var(--brand)]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>

        <button class="flex items-center gap-2 px-6 py-3 rounded-xl bg-white border border-[var(--brand)] text-[var(--brand)] text-xs font-black uppercase tracking-widest shadow-sm hover:bg-[var(--brand-muted)] transition-all">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Bulk Import
        </button>
        <button class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/>
            </svg>
            Tambah Menu Baru
        </button>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8">
    <!-- Left Sidebar: Categories & Info -->
    <div class="w-full lg:w-64 space-y-6">
        <!-- Categories Card -->
        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50">
            <div class="flex items-center justify-between mb-6">
                <h3 class="poppins text-base font-bold text-[var(--text-dark)]">Kategori</h3>
                <button class="w-6 h-6 rounded-full bg-[var(--brand-muted)] text-[var(--brand)] flex items-center justify-center hover:bg-[var(--brand-soft)] transition-colors">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
            </div>
            
            <nav class="space-y-2">
                <a href="#" class="flex items-center justify-between px-4 py-3 rounded-2xl bg-[var(--brand-muted)] border-l-4 border-[var(--brand)] group transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--brand)]"></div>
                        <span class="text-xs font-black text-[var(--brand)] uppercase tracking-wider">Main Course</span>
                    </div>
                    <span class="bg-[var(--brand-soft)] text-[var(--brand)] px-2 py-0.5 rounded text-[9px] font-black">12</span>
                </a>
                <a href="#" class="flex items-center justify-between px-4 py-3 rounded-2xl hover:bg-gray-50 transition-all">
                    <span class="text-xs font-bold text-[var(--text-muted)] ml-4.5">Drinks</span>
                    <span class="bg-gray-100 text-gray-400 px-2 py-0.5 rounded text-[9px] font-black">8</span>
                </a>
                <a href="#" class="flex items-center justify-between px-4 py-3 rounded-2xl hover:bg-gray-50 transition-all">
                    <span class="text-xs font-bold text-[var(--text-muted)] ml-4.5">Snacks</span>
                    <span class="bg-gray-100 text-gray-400 px-2 py-0.5 rounded text-[9px] font-black">15</span>
                </a>
                <a href="#" class="flex items-center justify-between px-4 py-3 rounded-2xl hover:bg-gray-50 transition-all">
                    <span class="text-xs font-bold text-[var(--text-muted)] ml-4.5">Desserts</span>
                    <span class="bg-gray-100 text-gray-400 px-2 py-0.5 rounded text-[9px] font-black">4</span>
                </a>
            </nav>
        </div>

        <!-- Info Stok Card -->
        <div class="bg-[var(--brand)] p-6 rounded-[32px] shadow-lg text-white">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                </div>
                <h3 class="poppins text-sm font-bold mt-1">Info Stok</h3>
            </div>
            <p class="text-[10px] font-medium leading-relaxed opacity-80 mb-6">
                Terdapat 3 menu yang mendekati batas minimum stok ( < 5 pcs ).
            </p>
            <button class="w-full py-3 bg-white/10 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-white/20 transition-all">
                Lihat Detail
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1">
        <!-- Content Header -->
        <div class="flex items-center justify-between mb-8 px-2">
            <div class="flex items-end gap-2">
                <h2 class="poppins text-lg font-black text-[var(--text-dark)]">Main Course</h2>
                <span class="text-xs font-bold text-gray-400 mb-0.5">(12 Menu)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">Urutkan:</span>
                <select class="bg-transparent text-xs font-black text-[var(--text-dark)] outline-none cursor-pointer">
                    <option>Nama A-Z</option>
                    <option>Harga Terendah</option>
                    <option>Harga Tertinggi</option>
                </select>
            </div>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $menus = [
                [
                    'name' => 'Nasi Goreng Spesial',
                    'price' => '25k',
                    'tenant' => 'Warung Barokah',
                    'stok' => '45',
                    'status' => 'TERSEDIA',
                    'category' => 'MAIN COURSE',
                    'img' => 'https://images.unsplash.com/photo-1512058560366-cd2429555614?auto=format&fit=crop&q=80&w=400'
                ],
                [
                    'name' => 'Salad Ayam Bakar',
                    'price' => '32k',
                    'tenant' => 'Healthy Garden',
                    'stok' => 'Habis',
                    'status' => 'HABIS',
                    'category' => 'HEALTHY BOWL',
                    'img' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400'
                ],
                [
                    'name' => 'Smoothie Berries',
                    'price' => '18k',
                    'tenant' => 'Berry Bliss',
                    'stok' => '12',
                    'status' => 'TERSEDIA',
                    'category' => 'DRINKS',
                    'img' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&q=80&w=400'
                ],
                [
                    'name' => 'Classic Cheese Burger',
                    'price' => '45k',
                    'tenant' => 'Grill & Chill',
                    'stok' => '20',
                    'status' => 'TERSEDIA',
                    'category' => 'WESTERN',
                    'img' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&q=80&w=400'
                ]
            ];

            foreach ($menus as $m):
                $isAvailable = $m['status'] === 'TERSEDIA';
            ?>
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden group hover:shadow-md transition-all">
                <!-- Image Container -->
                <div class="h-48 overflow-hidden relative">
                    <img src="<?= $m['img'] ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-[8px] font-black text-[var(--brand)] uppercase tracking-wider shadow-sm">
                            <?= $m['category'] ?>
                        </span>
                    </div>
                </div>
                
                <!-- Card Content -->
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="min-w-0">
                            <h3 class="poppins text-base font-black text-[var(--text-dark)] leading-tight truncate"><?= $m['name'] ?></h3>
                            <div class="flex items-center gap-1.5 mt-1">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-gray-400">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                </svg>
                                <span class="text-[10px] font-bold text-gray-400"><?= $m['tenant'] ?></span>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="text-[10px] font-black text-[var(--brand)] uppercase tracking-widest leading-none mb-1">Rp</span>
                            <span class="poppins text-lg font-black text-[var(--text-dark)] leading-none"><?= $m['price'] ?></span>
                        </div>
                    </div>

                    <!-- Stock & Toggle -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl mb-6">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Stok:</span>
                            <span class="text-[11px] font-black <?= $isAvailable ? 'text-[var(--text-dark)]' : 'text-[#BA1A1A]' ?> leading-none">
                                <?= $m['stok'] ?> <?= $isAvailable ? 'pcs' : '' ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[8px] font-extrabold text-gray-400 uppercase tracking-widest"><?= $m['status'] ?></span>
                            <div class="w-10 h-5 rounded-full relative transition-colors cursor-pointer <?= $isAvailable ? 'bg-[#16A34A]' : 'bg-gray-200' ?>">
                                <div class="absolute top-1 transition-all <?= $isAvailable ? 'right-1' : 'left-1' ?> w-3 h-3 rounded-full bg-white shadow-sm"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button class="flex-1 py-3 bg-[#FDE8E4] text-[var(--brand)] rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-[#F5D5CE] transition-all">
                            Edit
                        </button>
                        <button class="w-12 py-3 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-[#BA1A1A] transition-all">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
