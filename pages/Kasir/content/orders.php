<?php // Konten: Orders ?>

<style>
    .filter-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .filter-btn.active {
        background-color: #7B0009;
        color: white;
        box-shadow: 0 4px 12px rgba(123, 0, 9, 0.2);
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .status-pending { background-color: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
    .status-paid { background-color: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; }
    .status-done { background-color: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; }
    .status-cancel { background-color: #FEF2F2; color: #DC2626; border: 1px solid #FEE2E2; }
    
    /* Custom Scrollbar for Table */
    .table-container::-webkit-scrollbar { height: 6px; }
    .table-container::-webkit-scrollbar-track { background: #f1f5f9; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
</style>

<div class="flex flex-col gap-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-[#261817] tracking-tight">Manajemen Pesanan</h1>
            <p class="text-[#675C5C] text-sm mt-1 font-medium">Memantau dan memproses transaksi pelanggan yang aktif.</p>
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <!-- Scan Button -->
            <button class="flex items-center gap-3 px-6 py-2.5 rounded-xl border-2 border-[#7B0009] bg-white text-[#7B0009] font-bold text-sm hover:bg-red-50 transition-all shadow-sm active:scale-95">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Input Kode Pesanan
            </button>

            <!-- Filter Navigation -->
            <div class="flex items-center p-1.5 bg-white border border-gray-200 rounded-2xl shadow-sm">
                <button class="filter-btn active px-5 py-2 rounded-xl text-xs font-bold" data-filter="All">Semua</button>
                <button class="filter-btn px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800" data-filter="Pending">Pending</button>
                <button class="filter-btn px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800" data-filter="Paid">Sudah Bayar</button>
                <button class="filter-btn px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800" data-filter="Done">Selesai</button>
                <button class="filter-btn px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800" data-filter="Cancel">Dibatalkan</button>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden md:block table-container overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[#FAF9F9] border-b border-gray-100">
                        <th class="px-6 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">ID Pesanan</th>
                        <th class="px-4 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Meja</th>
                        <th class="px-6 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Detail Pesanan</th>
                        <th class="px-6 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Total Harga</th>
                        <th class="px-6 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-5 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-50">
                    <!-- Data rendered via JS -->
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div id="mobileCards" class="md:hidden divide-y divide-gray-50">
            <!-- Data rendered via JS -->
        </div>

        <!-- Table Footer -->
        <div class="px-8 py-5 bg-[#FAF9F9] border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-500 font-medium">
                Menampilkan <span class="text-[#261817] font-bold">5</span> dari <span class="text-[#261817] font-bold">128</span> pesanan
            </p>
            
            <div class="flex items-center gap-2">
                <button class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-gray-600 hover:border-gray-300 transition-all">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <div class="flex items-center gap-1.5">
                    <button class="w-9 h-9 rounded-xl bg-[#7B0009] text-white text-xs font-bold shadow-md shadow-red-900/20">1</button>
                    <button class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-gray-500 text-xs font-bold hover:bg-gray-50">2</button>
                    <button class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-gray-500 text-xs font-bold hover:bg-gray-50">3</button>
                    <span class="px-1 text-gray-300">•••</span>
                    <button class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-gray-500 text-xs font-bold hover:bg-gray-50">26</button>
                </div>
                <button class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-gray-600 hover:border-gray-300 transition-all">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
        <!-- Card: Success -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-[2px]">Pesanan Berhasil</p>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
            </div>
            <div class="flex items-end gap-3">
                <span class="text-3xl font-black text-[#261817]">84</span>
                <span class="mb-1 text-emerald-500 text-[10px] font-bold px-2 py-0.5 bg-emerald-50 rounded-lg flex items-center gap-1">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    +12%
                </span>
            </div>
        </div>

        <!-- Card: Queue -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-[2px]">Antrean Aktif</p>
                <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-black text-[#261817]">12</span>
                <span class="mb-1 text-gray-400 text-[10px] font-bold uppercase tracking-wider">pesanan</span>
            </div>
        </div>

        <!-- Card: Revenue -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-[2px]">Total Pendapatan</p>
                <div class="w-8 h-8 rounded-xl bg-red-50 text-[#7B0009] flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-black text-[#261817]">Rp 4.2M</span>
                <span class="mb-1 text-gray-400 text-[10px] font-bold uppercase tracking-wider">hari ini</span>
            </div>
        </div>

        <!-- Card: Shift -->
        <div class="bg-[#7B0009] rounded-3xl p-6 shadow-xl shadow-red-900/20 flex flex-col justify-between min-h-[120px] relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <p class="text-[10px] font-extrabold text-white/50 uppercase tracking-[2px]">Info Shift</p>
                <span class="px-2 py-0.5 bg-green-500/20 text-green-400 text-[9px] font-black rounded-lg border border-green-500/20 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                    AKTIF
                </span>
            </div>
            <div class="relative z-10">
                <h3 class="text-xl font-bold text-white leading-tight">Shift Pagi</h3>
                <p class="text-white/60 text-[10px] font-medium mt-0.5">07:00 — 15:00</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Data mockup
    const orders = [
        {
            id: "#ORD-9021",
            tableNumber: "T-14",
            items: [
                { stall: "Stall A", items: "Soto Ayam (2), Nasi Putih (2)", color: "red" },
                { stall: "Stall B", items: "Es Teh Manis (2)", color: "blue" }
            ],
            totalPrice: "Rp 54,000",
            status: "Pending"
        },
        {
            id: "#ORD-9022",
            tableNumber: "T-05",
            items: [
                { stall: "Stall C", items: "Mie Goreng Spesial (1)", color: "red" },
                { stall: "Stall B", items: "Jus Jeruk (1)", color: "blue" }
            ],
            totalPrice: "Rp 32,500",
            status: "Paid"
        },
        {
            id: "#ORD-9019",
            tableNumber: "T-22",
            items: [
                { stall: "Stall A", items: "Nasi Goreng (3)", color: "red" }
            ],
            totalPrice: "Rp 75,000",
            status: "Done"
        },
        {
            id: "#ORD-9018",
            tableNumber: "T-01",
            items: [
                { stall: "Stall D", items: "Ayam Bakar (1)", color: "red" },
                { stall: "Stall B", items: "Es Jeruk (1)", color: "blue" }
            ],
            totalPrice: "Rp 45,000",
            status: "Cancel"
        },
        {
            id: "#ORD-9025",
            tableNumber: "T-10",
            items: [
                { stall: "Stall A", items: "Bubur Ayam (2)", color: "red" },
                { stall: "Stall B", items: "Teh Hangat (2)", color: "blue" }
            ],
            totalPrice: "Rp 38,000",
            status: "Pending"
        }
    ];

    function getActionButtons(status) {
        const baseBtn = "px-4 py-2 rounded-xl text-[11px] font-bold transition-all active:scale-95 whitespace-nowrap";
        
        if (status === "Pending") {
            return `
                <div class="flex items-center justify-end gap-2">
                    <button class="${baseBtn} border border-gray-200 text-gray-600 hover:bg-gray-50">Lihat</button>
                    <button class="${baseBtn} bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">Konfirmasi</button>
                </div>
            `;
        } else if (status === "Paid") {
            return `
                <div class="flex items-center justify-end gap-2">
                    <button class="${baseBtn} border border-gray-200 text-gray-600 hover:bg-gray-50">Lihat</button>
                    <span class="text-[10px] text-orange-400 font-bold italic pr-2 flex items-center gap-1.5">
                        <span class="w-1 h-1 bg-orange-400 rounded-full animate-ping"></span>
                        Sedang Diproses
                    </span>
                </div>
            `;
        } else if (status === "Done") {
            return `
                <div class="flex items-center justify-end gap-2">
                    <button class="${baseBtn} border border-gray-200 text-gray-600 hover:bg-gray-50">Lihat</button>
                </div>
            `;
        }
        return `<span class="text-[10px] text-gray-300 font-bold pr-4">Pesanan Dibatalkan</span>`;
    }

    function getStatusLabel(status) {
        const labels = {
            "Pending": "Pending",
            "Paid": "Sudah Bayar",
            "Done": "Selesai",
            "Cancel": "Dibatalkan"
        };
        return labels[status] || status;
    }

    function renderTable(filteredOrders) {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = filteredOrders.map((order) => `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <p class="text-xs font-bold text-[#261817]">${order.id}</p>
                </td>
                <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-[#FAF9F9] border border-gray-100 text-[#261817] text-[11px] font-black">
                        ${order.tableNumber}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-0.5">
                        ${order.items.map(item => `
                            <p class="text-[11px]">
                                <span class="font-bold ${item.color === 'red' ? 'text-[#7B0009]' : 'text-blue-700'}">${item.stall.replace('Stall', 'Stan')}:</span>
                                <span class="text-gray-500 font-medium">${item.items}</span>
                            </p>
                        `).join('')}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-xs font-black text-[#261817]">${order.totalPrice}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="status-badge status-${order.status.toLowerCase()}">
                        ${getStatusLabel(order.status)}
                    </span>
                </td>
                <td class="px-6 py-4">
                    ${getActionButtons(order.status)}
                </td>
            </tr>
        `).join('');
    }

    function renderMobileCards(filteredOrders) {
        const mobileCards = document.getElementById('mobileCards');
        mobileCards.innerHTML = filteredOrders.map((order) => `
            <div class="p-5 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-1">
                        <p class="text-sm font-black text-[#261817]">${order.id}</p>
                        <span class="w-fit inline-flex items-center px-3 py-0.5 rounded-lg bg-gray-100 text-gray-800 text-[10px] font-bold">
                            ${order.tableNumber}
                        </span>
                    </div>
                    <span class="status-badge status-${order.status.toLowerCase()}">
                        ${getStatusLabel(order.status)}
                    </span>
                </div>
                <div class="space-y-1">
                    ${order.items.map(item => `
                        <p class="text-xs">
                            <span class="font-bold ${item.color === 'red' ? 'text-[#7B0009]' : 'text-blue-700'}">${item.stall.replace('Stall', 'Stan')}:</span>
                            <span class="text-gray-500">${item.items}</span>
                        </p>
                    `).join('')}
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-sm font-black text-[#261817]">${order.totalPrice}</span>
                    ${getActionButtons(order.status)}
                </div>
            </div>
        `).join('');
    }

    // Init
    renderTable(orders);
    renderMobileCards(orders);

    // Filter Logic
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const filter = btn.dataset.filter;
            const filtered = filter === 'All' ? orders : orders.filter(o => o.status === filter);
            
            renderTable(filtered);
            renderMobileCards(filtered);
        });
    });
</script>
