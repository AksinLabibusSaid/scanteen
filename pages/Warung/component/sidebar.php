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
                <h1 class="text-[#991B1B] text-lg font-black leading-[22.5px]">
                    Scanteen
                </h1>
                <p class="text-[#675C5C] text-[10px] font-bold leading-[15px] tracking-[1px] uppercase">
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
                    <path d="M0 8h8V0H0v8zm2-6h4v4H2V2zm8-2v8h8V0h-8zm6 6h-4V2h4v4zM0 18h8v-8H0v8zm2-6h4v4H2v-4zm8 0v8h8v-8h-8zm6 6h-4v-4h4v4z"
                          fill="<?= $activePage === 'dashboard' ? '#991B1B' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5 <?= $activePage === 'dashboard' ? 'text-[#991B1B]' : '' ?>">Overview</span>
            </a>

            <!-- Orders -->
            <a href="?page=orders"
               class="<?= $activePage === 'orders' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zm0 2h12l2 4H4l2-4zm14 16H4V8h16v12zm-8-9a3 3 0 0 1-3-3V7h2v1a1 1 0 0 0 2 0V7h2v1a3 3 0 0 1-3 3z"
                          fill="<?= $activePage === 'orders' ? '#991B1B' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5 <?= $activePage === 'orders' ? 'text-[#991B1B]' : '' ?>">Orders</span>
            </a>

            <!-- Menu Manager -->
            <a href="?page=menu"
               class="<?= $activePage === 'menu' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                          fill="<?= $activePage === 'menu' ? '#991B1B' : '#6B7280' ?>"/>
                    <circle cx="18" cy="18" r="3" fill="<?= $activePage === 'menu' ? '#991B1B' : '#6B7280' ?>" opacity="0.4"/>
                </svg>
                <span class="font-semibold text-sm leading-5 <?= $activePage === 'menu' ? 'text-[#991B1B]' : '' ?>">Menu Manager</span>
            </a>

            <!-- History & Laporan -->
            <a href="?page=history"
               class="<?= $activePage === 'history' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"
                          fill="<?= $activePage === 'history' ? '#991B1B' : '#6B7280' ?>"/>
                    <rect x="7" y="13" width="2" height="5" fill="<?= $activePage === 'history' ? '#991B1B' : '#6B7280' ?>" opacity="0.3"/>
                    <rect x="10" y="11" width="2" height="7" fill="<?= $activePage === 'history' ? '#991B1B' : '#6B7280' ?>" opacity="0.3"/>
                    <rect x="13" y="15" width="2" height="3" fill="<?= $activePage === 'history' ? '#991B1B' : '#6B7280' ?>" opacity="0.3"/>
                </svg>
                <span class="font-semibold text-sm leading-5 <?= $activePage === 'history' ? 'text-[#991B1B]' : '' ?>">Riwayat</span>
            </a>

        </nav>
    </div>

    <!-- Bottom Section - Logout -->
    <div class="border-t border-gray-100 p-6">
        <a href="../../auth/logout.php" class="flex items-center gap-3 px-4 py-3 w-full text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 18C1.45 18 0.979167 17.8042 0.5875 17.4125C0.195833 17.0208 0 16.55 0 16V2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0H9V2H2V16H9V18H2ZM13 14L11.625 12.55L14.175 10H6V8H14.175L11.625 5.45L13 4L18 9L13 14Z" fill="currentColor"/>
            </svg>
            <span class="font-semibold text-sm leading-5">Keluar</span>
        </a>
    </div>
</aside>
