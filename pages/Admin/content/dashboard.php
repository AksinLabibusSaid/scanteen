<?php
// Dashboard Admin — High-fidelity analytics based on design image
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)] uppercase">Dashboard</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Real-time performance analytics for Scanteen Global Network.</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Export Button -->
        <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#FDE8E4] text-[var(--brand)] text-sm font-bold shadow-sm hover:bg-[#F5D5CE] transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Export
        </button>
        <!-- Date Filter Button -->
        <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[var(--text-dark)] text-white text-sm font-bold shadow-md hover:opacity-90 transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Last 24h
        </button>
    </div>
</div>

<!-- Summary Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Orders -->
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col justify-between h-32">
        <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Total Orders</p>
        <p class="poppins text-3xl font-bold text-[var(--text-dark)]">8,901</p>
    </div>

    <!-- Warung Terlaris -->
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col justify-between h-32">
        <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Warung Terlaris</p>
        <div class="flex items-center gap-3 mt-2">
            <div class="w-10 h-10 bg-[var(--brand)] rounded-lg flex items-center justify-center text-white">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3h18v18H3zM9 9v6M15 9v6"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-[var(--text-dark)]">Warung 1</p>
                <p class="text-[10px] font-medium text-[var(--text-muted)]">412 orders</p>
            </div>
        </div>
    </div>

    <!-- Revenue Today (Dark Card) -->
    <div class="bg-[var(--brand)] p-6 rounded-[24px] shadow-lg flex flex-col justify-between h-32">
        <p class="text-[10px] font-extrabold text-[#F5E3DF] uppercase tracking-widest opacity-80">Revenue Today</p>
        <p class="poppins text-3xl font-bold text-white">Rp 42.1M</p>
    </div>

    <!-- Growth Weekly -->
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col justify-between h-32">
        <div class="flex justify-between items-start">
            <p class="text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-widest">Growth Weekly</p>
            <div class="flex items-end gap-0.5 h-6">
                <div class="w-1 bg-[#E9ECEF] h-2 rounded-full"></div>
                <div class="w-1 bg-[#E9ECEF] h-4 rounded-full"></div>
                <div class="w-1 bg-[var(--brand)] h-6 rounded-full"></div>
                <div class="w-1 bg-[#E9ECEF] h-3 rounded-full"></div>
            </div>
        </div>
        <p class="poppins text-xl font-bold text-[var(--text-dark)] leading-tight">
            Rp 288M
        </p>
    </div>
</div>

<!-- Middle Section: Charts & Distribution -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Revenue Analytics Chart -->
    <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 relative overflow-hidden">
        <div class="flex items-start justify-between mb-8">
            <div>
                <h3 class="poppins text-lg font-bold text-[var(--brand)]">Revenue Analytics</h3>
                <p class="text-xs text-[var(--text-muted)] font-medium mt-1">Hourly revenue fluctuations across all active nodes.</p>
            </div>
            <div class="flex gap-2">
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-[#FAF7F6] border border-gray-100 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--brand)]"></span>
                    <span class="text-[10px] font-bold text-[var(--text-dark)]">This Week</span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-[#FAF7F6] border border-gray-100 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#F5D5CE]"></span>
                    <span class="text-[10px] font-bold text-[var(--text-dark)]">Last Week</span>
                </div>
            </div>
        </div>
        
        <!-- Simulated Bar Chart -->
        <div class="flex items-end justify-between h-48 px-2 border-b border-gray-100 pb-2 relative">
            <!-- Grid Lines (Horizontal) -->
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                <div class="w-full border-t border-gray-50 h-px"></div>
                <div class="w-full border-t border-gray-50 h-px"></div>
                <div class="w-full border-t border-gray-50 h-px"></div>
                <div class="w-full border-t border-gray-50 h-px"></div>
            </div>

            <!-- Bars -->
            <?php
            $chartData = [
                ['08:00', 40, 60],
                ['10:00', 65, 55],
                ['12:00', 95, 30],
                ['14:00', 80, 45],
                ['16:00', 50, 70],
                ['18:00', 35, 60],
                ['20:00', 20, 40],
            ];
            foreach ($chartData as $data):
            ?>
            <div class="flex flex-col items-center flex-1 max-w-[60px] relative z-10">
                <div class="flex items-end gap-1.5 w-full justify-center">
                    <!-- This Week Bar -->
                    <div class="w-4 bg-<?= $data[0] == '12:00' ? '[var(--brand)]' : '[var(--brand-soft)]' ?> rounded-t-lg transition-all duration-500 hover:opacity-80 cursor-pointer" style="height: <?= $data[1] ?>%;"></div>
                </div>
                <span class="text-[9px] font-bold text-gray-400 mt-3"><?= $data[0] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Payment Distribution -->
    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <h3 class="poppins text-lg font-bold text-[var(--brand)] mb-8">Payment Distribution</h3>
        
        <div class="flex flex-col items-center">
            <!-- Simulated Donut Chart -->
            <div class="relative w-40 h-40 flex items-center justify-center">
                <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                    <circle cx="18" cy="18" r="16" fill="none" stroke="#FDE8E4" stroke-width="3" stroke-dasharray="100, 100" />
                    <circle cx="18" cy="18" r="16" fill="none" stroke="var(--brand)" stroke-width="4" stroke-dasharray="75, 100" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="poppins text-2xl font-black text-[var(--text-dark)] leading-none">75%</span>
                    <span class="text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest mt-1">Digital</span>
                </div>
                <!-- Mini icon box on the side -->
                <div class="absolute left-0 top-1/2 -translate-x-1/2 w-6 h-4 bg-[#F5D5CE] rounded shadow-sm"></div>
            </div>

            <!-- Legend -->
            <div class="w-full mt-10 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[var(--brand)]"></span>
                        <span class="text-xs font-bold text-[var(--text-dark)]">QRIS / Digital</span>
                    </div>
                    <span class="text-xs font-black text-[var(--text-dark)]">Rp 31.5M</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[#FDE8E4]"></span>
                        <span class="text-xs font-bold text-[var(--text-dark)]">Cash Payment</span>
                    </div>
                    <span class="text-xs font-black text-[var(--text-dark)]">Rp 10.6M</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Section -->
<div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
    <div class="px-10 py-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="poppins text-lg font-bold text-[var(--brand)]">Recent Transactions</h3>
        
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">Filter:</span>
                <div class="relative">
                    <select class="appearance-none bg-[#FDE8E4] text-[var(--brand)] text-[11px] font-bold px-4 py-2 pr-10 rounded-full border-none outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all cursor-pointer">
                        <option>Semua Warung</option>
                        <option>Warung Barokah</option>
                        <option>Artisan Bakery</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[var(--brand)]" width="10" height="6" viewBox="0 0 10 6" fill="none">
                        <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <a href="?page=orders" class="text-[11px] font-black text-[var(--brand)] uppercase tracking-widest hover:opacity-70 flex items-center gap-1">
                View All Activity
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#FAF7F6]">
                <tr>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Order ID</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Tenant</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Customer</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Amount</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Payment</th>
                    <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                    <th class="px-10 py-5 text-center text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $transactions = [
                    ['#SC-8291', 'Warung Barokah', 'Adi Santoso', 'Rp 45,000', 'QRIS', 'COMPLETED'],
                    ['#SC-8290', 'Artisan Bakery', 'Linda Wijaya', 'Rp 128,000', 'QRIS', 'COMPLETED'],
                    ['#SC-8289', 'Spice Fusion', 'Budi Hartono', 'Rp 32,500', 'Cash', 'PENDING'],
                    ['#SC-8288', 'Brew Master', 'Siti Aminah', 'Rp 55,000', 'QRIS', 'COMPLETED'],
                ];
                
                foreach ($transactions as $tx):
                    $isPending = $tx[5] === 'PENDING';
                ?>
                <tr class="hover:bg-[#FAF7F6] transition-colors group">
                    <td class="px-10 py-6 text-sm font-black text-[var(--brand)]"><?= $tx[0] ?></td>
                    <td class="px-10 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= $tx[1] ?></td>
                    <td class="px-10 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= $tx[2] ?></td>
                    <td class="px-10 py-6 text-sm font-black text-[var(--text-dark)]"><?= $tx[3] ?></td>
                    <td class="px-10 py-6">
                        <div class="flex items-center gap-2">
                            <?php if ($tx[4] === 'QRIS'): ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[var(--brand)]">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="7" y="7" width="1" height="1"/><rect x="18" y="7" width="1" height="1"/><rect x="7" y="18" width="1" height="1"/><rect x="18" y="18" width="1" height="1"/>
                                </svg>
                            <?php else: ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400">
                                    <rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            <?php endif; ?>
                            <span class="text-[11px] font-extrabold text-[var(--text-dark)] opacity-70"><?= $tx[4] ?></span>
                        </div>
                    </td>
                    <td class="px-10 py-6">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black <?= $isPending ? 'bg-[#F1F3F5] text-gray-500' : 'bg-[#FDE8E4] text-[var(--brand)]' ?>">
                            <?= $tx[5] ?>
                        </span>
                    </td>
                    <td class="px-10 py-6 text-center">
                        <button class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white hover:shadow-sm text-gray-400 hover:text-[var(--brand)] transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="px-10 py-8 text-center bg-[#FAF7F6]/50">
        <button class="text-[11px] font-black text-[var(--text-muted)] uppercase tracking-[1px] hover:text-[var(--brand)] transition-colors">
            Load More Transactions
        </button>
    </div>
</div>
