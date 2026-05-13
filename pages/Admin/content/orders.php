<?php
// Order Management — Admin High-Fidelity Design
?>

<div class="flex flex-col lg:flex-row gap-6 h-full">
    <!-- Left Column: Order List -->
    <div class="flex-1 min-w-0">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Order Management</h1>
            <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Manage and track all customer transactions across the network.</p>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 mb-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Order ID -->
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Order ID</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">#</span>
                        <input type="text" placeholder="SC-" class="w-full pl-6 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
                    </div>
                </div>
                <!-- Status -->
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Status</label>
                    <select class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                        <option>All</option>
                        <option>Completed</option>
                        <option>Pending</option>
                        <option>Cancelled</option>
                    </select>
                </div>
                <!-- Date -->
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Date</label>
                    <input type="date" class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none">
                </div>
                <!-- Warung -->
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Warung</label>
                    <select class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                        <option>All</option>
                        <option>Warung Barokah</option>
                        <option>Bakso Pak De</option>
                    </select>
                </div>
                <!-- Payment -->
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Payment</label>
                    <select class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                        <option>All</option>
                        <option>QRIS</option>
                        <option>Cash</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-[24px] shadow-sm border border-gray-50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Order ID</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Tenant</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Customer</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Amount</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php
                        $orders = [
                            ['#SC-8291', 'Warung Barokah', 'Adi Santoso', 'Rp 42,000', 'QRIS', 'AS', true],
                            ['#SC-8290', 'Bakso Pak De', 'Maya Lestari', 'Rp 28,500', 'Cash', 'ML', false],
                            ['#SC-8289', 'Soto Seger', 'Budi Nugraha', 'Rp 15,000', 'QRIS', 'BN', false],
                            ['#SC-8288', 'Warung Barokah', 'Eka Putri', 'Rp 58,000', 'QRIS', 'EP', false],
                        ];
                        foreach ($orders as $o):
                        ?>
                        <tr class="hover:bg-[#FAF7F6] transition-all cursor-pointer <?= $o[6] ? 'bg-[#FAF7F6] border-l-4 border-[var(--brand)]' : '' ?>">
                            <td class="px-8 py-6 text-sm font-black text-[var(--brand)]"><?= $o[0] ?></td>
                            <td class="px-8 py-6 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= $o[1] ?></td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-full bg-[#F5D5CE] flex items-center justify-center text-[10px] font-black text-[var(--brand)]">
                                        <?= $o[5] ?>
                                    </div>
                                    <span class="text-sm font-bold text-[var(--text-dark)] opacity-70"><?= $o[2] ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm font-black text-[var(--text-dark)]"><?= $o[3] ?></td>
                            <td class="px-8 py-6 text-xs font-bold text-[var(--text-muted)] uppercase tracking-widest"><?= $o[4] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Order Details Sidebar -->
    <div class="w-full lg:w-[400px] bg-white rounded-[32px] shadow-lg border border-gray-100 flex flex-col h-[calc(100vh-120px)] sticky top-6 overflow-hidden">
        <!-- Sidebar Header -->
        <div class="p-8 pb-6 bg-[#FAF7F6]/50 relative">
            <button class="absolute right-6 top-6 text-gray-400 hover:text-[var(--brand)] transition-colors">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-1">Order ID</p>
            <h2 class="poppins text-2xl font-black text-[var(--brand)]">#SC-8291</h2>
            
            <div class="flex items-center justify-between mt-6">
                <span class="px-4 py-1 bg-[#D1FAE5] text-[#065F46] rounded-full text-[10px] font-black uppercase tracking-wider">Completed</span>
                <button class="px-5 py-2 bg-[var(--brand)] text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-sm hover:opacity-90 transition-all">Force Complete</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-8 pt-6 custom-scrollbar">
            <!-- Customer Details -->
            <div class="bg-[#FAF7F6] p-6 rounded-[24px] mb-8 border border-gray-50">
                <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-4">Customer Details</p>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-[var(--brand)] flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-[var(--text-dark)] leading-none">Adi Santoso</p>
                            <p class="text-[11px] font-bold text-[var(--text-muted)] mt-1.5">+62 812-3456-7890</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-[var(--brand)] flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h2"/>
                                <circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M9 17h6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-[var(--text-dark)] leading-none">Table #12</p>
                            <p class="text-[11px] font-bold text-[var(--text-muted)] mt-1.5">Dine In — Main Hall</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4 px-1">
                    <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px]">Order Items</p>
                    <span class="px-2 py-0.5 bg-[var(--brand)] text-white rounded text-[8px] font-black uppercase">Warung Barokah</span>
                </div>
                <div class="space-y-4">
                    <!-- Item 1 -->
                    <div class="flex items-center gap-4 group">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1512058560366-cd2429555614?auto=format&fit=crop&q=80&w=100" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-xs font-black text-[var(--text-dark)] truncate">Nasi Goreng Spesial</p>
                                <p class="text-xs font-black text-[var(--text-dark)]">Rp 25.000</p>
                            </div>
                            <p class="text-[9px] font-bold text-[var(--text-muted)] mt-0.5 italic truncate">Note: Extra pedas, telor matang</p>
                            <p class="text-[10px] font-black text-[var(--brand)] mt-1.5">Qty: 1</p>
                        </div>
                    </div>
                    <!-- Item 2 -->
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1544145945-f904253d0c7b?auto=format&fit=crop&q=80&w=100" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-xs font-black text-[var(--text-dark)] truncate">Es Teh Manis</p>
                                <p class="text-xs font-black text-[var(--text-dark)]">Rp 5.000</p>
                            </div>
                            <p class="text-[10px] font-black text-[var(--brand)] mt-2">Qty: 2</p>
                        </div>
                    </div>
                    <!-- Item 3 -->
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1606471191009-63994c53433b?auto=format&fit=crop&q=80&w=100" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-xs font-black text-[var(--text-dark)] truncate">Tempe Mendoan (3pcs)</p>
                                <p class="text-xs font-black text-[var(--text-dark)]">Rp 7.000</p>
                            </div>
                            <p class="text-[10px] font-black text-[var(--brand)] mt-2">Qty: 1</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="mb-8 px-1">
                <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-6">Order Timeline</p>
                <div class="space-y-6 relative ml-2">
                    <!-- Vertical Line -->
                    <div class="absolute left-[3px] top-2 bottom-2 w-0.5 bg-gray-100"></div>
                    
                    <div class="relative flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-2 rounded-full bg-[var(--brand)] relative z-10"></div>
                            <p class="text-[11px] font-black text-[var(--text-dark)]">Order Placed</p>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400">12:30 PM</p>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-2 rounded-full bg-[var(--brand)] relative z-10"></div>
                            <p class="text-[11px] font-black text-[var(--text-dark)]">Confirmed</p>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400">12:32 PM</p>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-2 rounded-full bg-[var(--brand)] relative z-10"></div>
                            <p class="text-[11px] font-black text-[var(--text-dark)]">Preparing</p>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400">12:35 PM</p>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-2 rounded-full bg-[var(--brand)] relative z-10"></div>
                            <p class="text-[11px] font-black text-[var(--text-dark)]">Ready for Pickup</p>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400">12:50 PM</p>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-2 rounded-full bg-[var(--brand)] relative z-10"></div>
                            <p class="text-[11px] font-black text-[var(--text-dark)]">Completed</p>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400">12:55 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Total Summary Card -->
        <div class="p-8 bg-[var(--brand)] rounded-t-[32px] shadow-2xl">
            <div class="space-y-3 mb-6">
                <div class="flex justify-between text-xs font-bold text-white/70">
                    <span>Subtotal</span>
                    <span>Rp 37.000</span>
                </div>
                <div class="flex justify-between text-xs font-bold text-white/70 border-b border-white/10 pb-3">
                    <span>Service Tax (10%)</span>
                    <span>Rp 5.000</span>
                </div>
                <div class="flex justify-between items-center pt-1">
                    <span class="poppins text-xs font-bold text-white uppercase tracking-widest">Total Amount</span>
                    <span class="poppins text-xl font-black text-white">Rp 42.000</span>
                </div>
            </div>
            <div class="flex items-center gap-2 px-3 py-2 bg-white/10 rounded-xl">
                <div class="w-4 h-4 rounded-full bg-green-500 flex items-center justify-center">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="4">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span class="text-[9px] font-black text-white uppercase tracking-[1px]">Paid via QRIS</span>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #F1F3F5;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #E9ECEF;
    }
</style>
