<?php
// System Settings — Admin High-Fidelity Design
?>

<!-- Page Header & Global Actions -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Pengaturan Sistem</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Configure your digital canteen ecosystem and payment gateways.</p>
    </div>
    <div class="flex items-center gap-3">
        <button class="px-6 py-2.5 rounded-xl bg-white border border-[var(--brand)] text-[var(--brand)] text-[10px] font-black uppercase tracking-widest hover:bg-[var(--brand-muted)] transition-all">
            Reset to Default
        </button>
        <button class="px-6 py-2.5 rounded-xl bg-[var(--brand)] text-white text-[10px] font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
            Save All Changes
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Payment Gateway Config (Large Panel) -->
    <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <h3 class="poppins text-base font-black text-[var(--text-dark)]">Payment Gateway Config</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="space-y-2">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Payment Provider</label>
                <select class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                    <option>Midtrans (Recommended)</option>
                    <option>Xendit</option>
                    <option>Stripe</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Environment Mode</label>
                <div class="flex items-center gap-3 mt-3">
                    <span class="text-[10px] font-bold text-gray-400">Sandbox</span>
                    <div class="w-10 h-5 rounded-full bg-[var(--brand)] relative cursor-pointer">
                        <div class="absolute top-1 right-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
                    </div>
                    <span class="text-[10px] font-black text-[var(--brand)]">Production</span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Client Key / API Key</label>
                <input type="password" value="****************************************" class="w-full px-5 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--brand)] outline-none tracking-widest">
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Server Key</label>
                <input type="password" value="****************************************" class="w-full px-5 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--brand)] outline-none tracking-widest">
            </div>
        </div>
    </div>

    <!-- Payment Expiry (Dark Panel) -->
    <div class="bg-[var(--brand)] p-8 rounded-[32px] shadow-lg text-white">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-3">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <h3 class="poppins text-sm font-black uppercase tracking-wider">Payment Expiry</h3>
            </div>
            <button class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all shadow-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>
        </div>

        <div class="space-y-6">
            <div>
                <p class="text-[9px] font-black text-white/50 uppercase tracking-widest mb-4">Payment Countdown Timer</p>
                <div class="flex items-center gap-4">
                    <input type="text" value="05:00" class="w-32 px-4 py-3 bg-white/10 border-none rounded-xl text-lg font-black text-white text-center outline-none">
                    <span class="text-[10px] font-bold text-white/50">MM:SS</span>
                </div>
            </div>
            
            <div class="p-6 bg-white/5 rounded-2xl border border-white/5 mt-10">
                <p class="text-[10px] font-medium italic text-white/60 leading-relaxed text-center">
                    Preview: "Selesaikan pembayaran dalam 05:00"
                </p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Fees & Taxes -->
    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center gap-3 mb-8">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2.5">
                <line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>
            </svg>
            <h3 class="poppins text-sm font-black text-[var(--text-dark)] uppercase">Fees & Taxes</h3>
        </div>

        <div class="p-5 bg-[#FAF7F6] rounded-2xl flex items-center justify-between mb-8">
            <span class="text-[10px] font-black text-[var(--text-dark)] uppercase tracking-widest">Apply Tax Globally</span>
            <div class="w-10 h-5 rounded-full bg-[var(--brand)] relative cursor-pointer">
                <div class="absolute top-1 right-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tax (%)</p>
                <input type="text" value="11" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none">
            </div>
            <div class="space-y-2">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Service Fee (%)</p>
                <input type="text" value="2" class="w-full px-4 py-3 bg-[#FAF7F6] border-none rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none">
            </div>
        </div>
    </div>

    <!-- Maintenance Mode -->
    <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2.5">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
                <h3 class="poppins text-sm font-black text-[var(--text-dark)] uppercase">Maintenance Mode</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[9px] font-black text-[var(--brand)] uppercase tracking-widest">Enable Mode</span>
                <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer">
                    <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Custom Message for Customers</p>
            <textarea rows="3" placeholder="e.g. Mohon maaf, SmartCanteen sedang dalam pemeliharaan sistem rutin. Kami akan segera kembali!" class="w-full px-5 py-4 bg-[#FAF7F6] border-none rounded-2xl text-[11px] font-medium text-[var(--text-muted)] outline-none resize-none leading-relaxed"></textarea>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
    <!-- Operating Hours -->
    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <h3 class="poppins text-sm font-black text-[var(--text-dark)] uppercase">Operating Hours</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest">Close on Holidays</span>
                <div class="w-10 h-5 rounded-full bg-[var(--brand)] relative cursor-pointer">
                    <div class="absolute top-1 right-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Mon-Fri -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-black text-[var(--text-dark)] w-24">Mon - Fri</span>
                <div class="flex items-center gap-3">
                    <input type="text" value="08:00 AM" class="w-24 px-3 py-2 bg-[#FAF7F6] rounded-xl text-[10px] font-black text-[var(--brand)] text-center outline-none">
                    <span class="text-[9px] font-bold text-gray-300">to</span>
                    <input type="text" value="05:00 PM" class="w-24 px-3 py-2 bg-[#FAF7F6] rounded-xl text-[10px] font-black text-[var(--brand)] text-center outline-none">
                </div>
            </div>
            <!-- Sat -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-black text-[var(--text-dark)] w-24">Saturday</span>
                <div class="flex items-center gap-3">
                    <input type="text" value="09:00 AM" class="w-24 px-3 py-2 bg-[#FAF7F6] rounded-xl text-[10px] font-black text-[var(--brand)] text-center outline-none">
                    <span class="text-[9px] font-bold text-gray-300">to</span>
                    <input type="text" value="03:00 PM" class="w-24 px-3 py-2 bg-[#FAF7F6] rounded-xl text-[10px] font-black text-[var(--brand)] text-center outline-none">
                </div>
            </div>
            <!-- Sun -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-black text-[var(--text-dark)] w-24">Sunday</span>
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black text-[var(--brand)] opacity-50 uppercase tracking-widest">Closed</span>
                    <button class="text-[9px] font-black text-[var(--brand)] underline uppercase tracking-widest">Enable</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
        <div class="flex items-center gap-3 mb-8">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2.5">
                <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
            <h3 class="poppins text-sm font-black text-[var(--text-dark)] uppercase">Payment Methods</h3>
        </div>

        <div class="space-y-3">
            <div class="p-4 bg-[#FAF7F6] rounded-2xl flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-[var(--brand)]">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-widest">QRIS</span>
                </div>
                <div class="w-10 h-5 rounded-full bg-[var(--brand)] relative cursor-pointer">
                    <div class="absolute top-1 right-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
                </div>
            </div>
            <div class="p-4 bg-[#FAF7F6] rounded-2xl flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-[var(--brand)]">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/><line x1="6" y1="12" x2="6.01" y2="12"/><line x1="18" y1="12" x2="18.01" y2="12"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-widest">Cash</span>
                </div>
                <div class="w-10 h-5 rounded-full bg-[var(--brand)] relative cursor-pointer">
                    <div class="absolute top-1 right-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
                </div>
            </div>
            <div class="p-4 bg-[#FAF7F6] rounded-2xl flex items-center justify-between opacity-50">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Debit</span>
                </div>
                <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer">
                    <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Notification Rules -->
<div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 mb-10">
    <div class="flex items-center gap-3 mb-8">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2.5">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <h3 class="poppins text-sm font-black text-[var(--text-dark)] uppercase tracking-widest">Global Notification Rules</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-2xl transition-all cursor-pointer group">
            <div class="w-10 h-10 rounded-xl bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)] flex-shrink-0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-wider">Email Notifications</p>
                    <input type="checkbox" checked class="accent-[var(--brand)]">
                </div>
                <p class="text-[9px] font-medium text-gray-400 leading-relaxed">Send daily transaction summaries to admin email.</p>
            </div>
        </div>
        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-2xl transition-all cursor-pointer group">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-wider">Push Notifications</p>
                    <input type="checkbox" checked class="accent-blue-600">
                </div>
                <p class="text-[9px] font-medium text-gray-400 leading-relaxed">Real-time alerts for new orders and payments.</p>
            </div>
        </div>
        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-2xl transition-all cursor-pointer group">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600 flex-shrink-0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] font-black text-[var(--text-dark)] uppercase tracking-wider">Low Stock Alerts</p>
                    <input type="checkbox" checked class="accent-red-600">
                </div>
                <p class="text-[9px] font-medium text-gray-400 leading-relaxed">Alert when menu items fall below threshold.</p>
            </div>
        </div>
    </div>
</div>

<!-- Footer Actions -->
<div class="flex justify-center">
    <div class="inline-flex items-center bg-white border border-gray-100 rounded-full p-2 shadow-sm gap-2">
        <button class="flex items-center gap-2 px-6 py-2.5 rounded-full hover:bg-[var(--brand-muted)] text-gray-400 hover:text-[var(--brand)] transition-all">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span class="text-[10px] font-black uppercase tracking-widest">Audit Log</span>
        </button>
        <div class="w-px h-6 bg-gray-100"></div>
        <button class="flex items-center gap-2 px-6 py-2.5 rounded-full hover:bg-[var(--brand-muted)] text-gray-400 hover:text-[var(--brand)] transition-all">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            <span class="text-[10px] font-black uppercase tracking-widest">Backup Database</span>
        </button>
    </div>
</div>
