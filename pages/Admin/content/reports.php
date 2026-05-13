<?php
// Reports & Analytics — Admin High-Fidelity Design
?>

<!-- Page Header & Controls -->
<div class="flex flex-col lg:flex-row items-start justify-between gap-6 mb-10">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Laporan & Analitik</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Pantau performa operasional kantin secara real-time.</p>
    </div>
    
    <!-- Controls Card -->
    <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50 flex flex-col md:flex-row items-center gap-6">
        <div class="flex flex-col gap-4 flex-1">
            <div class="flex items-center gap-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span class="text-xs font-black text-[var(--text-dark)] uppercase tracking-wider">1 Okt 2023 - 31 Okt 2023</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-5 rounded-full bg-[var(--brand)] relative transition-colors cursor-pointer">
                    <div class="absolute top-1 right-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
                </div>
                <span class="text-[10px] font-bold text-gray-400">Bandingkan dengan Periode Sebelumnya</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button class="flex items-center gap-2 px-5 py-3 rounded-xl bg-[#FDE8E4] text-[var(--brand)] text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-[#F5D5CE] transition-all">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export PDF
            </button>
            <button class="flex items-center gap-2 px-5 py-3 rounded-xl bg-[var(--brand)] text-white text-[10px] font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
                Export Excel
            </button>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M16 5V3a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Pendapatan Total</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]">Rp 124.900.000</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Total Pesanan</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]">8.432</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Customer Growth</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]">+1,240</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col gap-3">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><line x1="12" y1="18" x2="12" y2="6"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Avg. Order Value</p>
            <p class="poppins text-lg font-black text-[var(--text-dark)]">Rp 14.700</p>
        </div>
    </div>
</div>

<!-- Main Trend Chart -->
<div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 mb-8 overflow-hidden relative">
    <div class="flex items-start justify-between mb-12">
        <div>
            <h3 class="poppins text-lg font-black text-[var(--brand)]">Revenue & Order Trend</h3>
            <p class="text-xs text-gray-400 font-medium mt-1">Analisis harian fluktuasi pendapatan</p>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[var(--brand)]"></span>
                <span class="text-[10px] font-black text-[var(--text-dark)] uppercase tracking-widest">Revenue</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Last Month</span>
            </div>
        </div>
    </div>

    <!-- Simulated Area Chart -->
    <div class="relative h-64 w-full">
        <svg viewBox="0 0 1000 300" class="w-full h-full preserve-3d" preserveAspectRatio="none">
            <!-- Last Month (Dashed) -->
            <path d="M0,250 C100,240 200,260 300,220 C400,180 500,230 600,200 C700,170 800,210 900,180 L1000,170" fill="none" stroke="#E2E8F0" stroke-width="3" stroke-dasharray="10, 10" />
            
            <!-- This Month (Solid Gradient) -->
            <defs>
                <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" style="stop-color:var(--brand);stop-opacity:0.15" />
                    <stop offset="100%" style="stop-color:var(--brand);stop-opacity:0" />
                </linearGradient>
            </defs>
            <path d="M0,280 C100,275 200,260 300,200 C400,140 500,180 600,140 C700,100 800,130 900,80 L1000,60 V300 H0 Z" fill="url(#areaGradient)" />
            <path d="M0,280 C100,275 200,260 300,200 C400,140 500,180 600,140 C700,100 800,130 900,80 L1000,60" fill="none" stroke="var(--brand)" stroke-width="4" stroke-linecap="round" />
            
            <!-- Grid Lines (Labels) -->
            <text x="50" y="295" font-size="12" fill="#CBD5E0" font-weight="800">01 OKT</text>
            <text x="300" y="295" font-size="12" fill="#CBD5E0" font-weight="800">07 OKT</text>
            <text x="550" y="295" font-size="12" fill="#CBD5E0" font-weight="800">14 OKT</text>
            <text x="800" y="295" font-size="12" fill="#CBD5E0" font-weight="800">21 OKT</text>
            <text x="950" y="295" font-size="12" fill="#CBD5E0" font-weight="800">31 OKT</text>
        </svg>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
    <!-- Performa Tenant (Left Column) -->
    <div class="lg:col-span-3 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center justify-between mb-8">
            <h3 class="poppins text-lg font-black text-[var(--brand)]">Performa Tenant</h3>
            <button class="text-gray-300 hover:text-[var(--brand)]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                </svg>
            </button>
        </div>

        <div class="space-y-8">
            <?php
            $tenants = [
                ['Warung Mas Eko (Nasi Goreng)', '42.1M', 100],
                ['Kedai Kopi Bahagia', '35.8M', 80],
                ['Ayam Penyet Mbak Lastri', '26.4M', 60],
                ['Soto Seger Pak Slamet', '12.2M', 30],
            ];
            foreach ($tenants as $idx => $t):
            ?>
            <div class="space-y-3">
                <div class="flex justify-between items-end">
                    <p class="text-xs font-black text-[var(--text-dark)] opacity-80"><?= $t[0] ?></p>
                    <p class="text-xs font-black text-[var(--brand)]">Rp <?= $t[1] ?></p>
                </div>
                <div class="w-full h-3 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000" 
                         style="width: <?= $t[2] ?>%; background-color: var(--brand); opacity: <?= 1 - ($idx * 0.2) ?>;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Metode Pembayaran (Right Column) -->
    <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <h3 class="poppins text-lg font-black text-[var(--brand)] mb-10">Metode Pembayaran</h3>
        
        <div class="flex items-center gap-10">
            <!-- Simulated Donut Chart -->
            <div class="relative w-36 h-36 flex items-center justify-center flex-shrink-0">
                <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                    <circle cx="18" cy="18" r="16" fill="none" stroke="#F1F3F5" stroke-width="3" stroke-dasharray="100, 100" />
                    <circle cx="18" cy="18" r="16" fill="none" stroke="var(--brand)" stroke-width="4" stroke-dasharray="65, 100" />
                    <circle cx="18" cy="18" r="16" fill="none" stroke="#5A413D" stroke-width="3.5" stroke-dasharray="28, 100" stroke-dashoffset="-65" />
                    <circle cx="18" cy="18" r="16" fill="none" stroke="#A8928F" stroke-width="3" stroke-dasharray="7, 100" stroke-dashoffset="-93" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="poppins text-2xl font-black text-[var(--text-dark)] leading-none">100%</span>
                </div>
            </div>

            <div class="flex-1 space-y-6">
                <div class="flex items-start gap-3">
                    <span class="w-2 h-2 rounded-full bg-[var(--brand)] mt-1.5"></span>
                    <div>
                        <p class="text-[10px] font-black text-[var(--text-dark)] leading-none mb-1">QRIS</p>
                        <p class="text-[9px] font-bold text-gray-400 leading-none">65% — Rp 81.1M</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-2 h-2 rounded-full bg-[#5A413D] mt-1.5"></span>
                    <div>
                        <p class="text-[10px] font-black text-[var(--text-dark)] leading-none mb-1">Tunai</p>
                        <p class="text-[9px] font-bold text-gray-400 leading-none">28% — Rp 34.9M</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-2 h-2 rounded-full bg-[#A8928F] mt-1.5"></span>
                    <div>
                        <p class="text-[10px] font-black text-[var(--text-dark)] leading-none mb-1">Debit</p>
                        <p class="text-[9px] font-bold text-gray-400 leading-none">7% — Rp 8.8M</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
