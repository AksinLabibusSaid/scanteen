<?php
// Tentukan halaman aktif berdasarkan parameter atau default
$activePage = $activePage ?? 'dashboard';
?>
<!-- Sidebar Container -->
<aside class="w-64 min-h-screen bg-white border-r border-gray-100 flex flex-col justify-between flex-shrink-0">
    <!-- Top Section -->
    <div class="flex flex-col gap-10 p-6">
        <!-- Logo Section -->
        <div class="flex items-center gap-3">
            <!-- Logo Badge -->
            <div class="w-10 h-10 rounded-xl bg-white overflow-hidden flex items-center justify-center flex-shrink-0 border border-gray-100">
                <img src="https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80" alt="Scanteen Logo" class="w-full h-full object-contain">
            </div>
            <!-- Logo Text -->
            <div class="flex flex-col justify-center" style="gap: 0.5px;">
                <h1 class="text-[var(--brand)] text-lg font-black leading-[22.5px]">
                    Scanteen
                </h1>
                <p class="text-[var(--brand)] text-[10px] font-bold leading-[15px] tracking-[1px] uppercase">
                    Panel Warung
                </p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex flex-col gap-1">
            <!-- Overview -->
            <a href="?page=dashboard"
               class="<?= $activePage === 'dashboard' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 6V0H18V6H10ZM0 10V0H8V10H0ZM10 18V8H18V18H10ZM0 18V12H8V18H0ZM2 8H6V2H2V8ZM12 16H16V10H12V16ZM12 4H16V2H12V4ZM2 16H6V14H2V16Z"
                          fill="<?= $activePage === 'dashboard' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Ringkasan</span>
            </a>

            <!-- Orders -->
            <a href="?page=orders"
               class="<?= $activePage === 'orders' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zm0 2h12l2 4H4l2-4zm14 16H4V8h16v12zm-8-9a3 3 0 0 1-3-3V7h2v1a1 1 0 0 0 2 0V7h2v1a3 3 0 0 1-3 3z"
                          fill="<?= $activePage === 'orders' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Pesanan</span>
            </a>

            <!-- Menu Manager -->
            <a href="?page=menu"
               class="<?= $activePage === 'menu' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                          fill="<?= $activePage === 'menu' ? 'var(--brand)' : '#6B7280' ?>"/>
                    <circle cx="18" cy="18" r="3" fill="<?= $activePage === 'menu' ? 'var(--brand)' : '#6B7280' ?>" opacity="0.4"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Manajemen Menu</span>
            </a>

            <!-- Riwayat -->
            <a href="?page=history"
               class="<?= $activePage === 'history' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"
                          fill="<?= $activePage === 'history' ? 'var(--brand)' : '#6B7280' ?>"/>
                    <rect x="7" y="13" width="2" height="5" fill="<?= $activePage === 'history' ? 'var(--brand)' : '#6B7280' ?>" opacity="0.3"/>
                    <rect x="10" y="11" width="2" height="7" fill="<?= $activePage === 'history' ? 'var(--brand)' : '#6B7280' ?>" opacity="0.3"/>
                    <rect x="13" y="15" width="2" height="3" fill="<?= $activePage === 'history' ? 'var(--brand)' : '#6B7280' ?>" opacity="0.3"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Riwayat</span>
            </a>

            </a>

        </nav>
    </div>

    <!-- Bottom Section - Logout -->
    <div class="border-t border-gray-100 p-6">
        <a href="/scanteen/pages/Auth/logout.php" class="flex items-center gap-3 px-5 py-3.5 w-full bg-gray-50 text-[var(--error-red)] hover:bg-red-50 rounded-2xl transition-all font-bold group">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="group-hover:translate-x-1 transition-transform">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span class="text-xs uppercase tracking-widest">Keluar</span>
        </a>
    </div>
</aside>
