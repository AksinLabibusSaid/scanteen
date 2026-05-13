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
                    Panel Kasir
                </p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex flex-col gap-1">
            <!-- Dashboard -->
            <a href="?page=dashboard"
               class="<?= $activePage === 'dashboard' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 6V0H18V6H10ZM0 10V0H8V10H0ZM10 18V8H18V18H10ZM0 18V12H8V18H0ZM2 8H6V2H2V8ZM12 16H16V10H12V16ZM12 4H16V2H12V4ZM2 16H6V14H2V16Z"
                          fill="<?= $activePage === 'dashboard' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Beranda</span>
            </a>

            <!-- Orders -->
            <a href="?page=orders"
               class="<?= $activePage === 'orders' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 20C5.45 20 4.97917 19.8042 4.5875 19.4125C4.19583 19.0208 4 18.55 4 18C4 17.45 4.19583 16.9792 4.5875 16.5875C4.97917 16.1958 5.45 16 6 16C6.55 16 7.02083 16.1958 7.4125 16.5875C7.80417 16.9792 8 17.45 8 18C8 18.55 7.80417 19.0208 7.4125 19.4125C7.02083 19.8042 6.55 20 6 20ZM16 20C15.45 20 14.9792 19.8042 14.5875 19.4125C14.1958 19.0208 14 18.55 14 18C14 17.45 14.1958 16.9792 14.5875 16.5875C14.9792 16.1958 15.45 16 16 16C16.55 16 17.0208 16.1958 17.4125 16.5875C17.8042 16.9792 18 17.45 18 18C18 18.55 17.8042 19.0208 17.4125 19.4125C17.0208 19.8042 16.55 20 16 20ZM5.15 4L7.55 9H14.55L17.3 4H5.15ZM4.2 2H18.95C19.3333 2 19.625 2.17083 19.825 2.5125C20.025 2.85417 20.0333 3.2 19.85 3.55L16.3 9.95C16.1167 10.2833 15.8708 10.5417 15.5625 10.725C15.2542 10.9083 14.9167 11 14.55 11H7.1L6 13H18V15H6C5.25 15 4.68333 14.6708 4.3 14.0125C3.91667 13.3542 3.9 12.7 4.25 12.05L5.6 9.6L2 2H0V0H3.25L4.2 2ZM7.55 9H14.55H7.55Z"
                          fill="<?= $activePage === 'orders' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Pesanan</span>
            </a>

            <!-- History -->
            <a href="?page=history"
               class="<?= $activePage === 'history' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 9C12.1667 9 11.4583 8.70833 10.875 8.125C10.2917 7.54167 10 6.83333 10 6C10 5.16667 10.2917 4.45833 10.875 3.875C11.4583 3.29167 12.1667 3 13 3C13.8333 3 14.5417 3.29167 15.125 3.875C15.7083 4.45833 16 5.16667 16 6C16 6.83333 15.7083 7.54167 15.125 8.125C14.5417 8.70833 13.8333 9 13 9ZM6 12C5.45 12 4.97917 11.8042 4.5875 11.4125C4.19583 11.0208 4 10.55 4 10V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H20C20.55 0 21.0208 0.195833 21.4125 0.5875C21.8042 0.979167 22 1.45 22 2V10C22 10.55 21.8042 11.0208 21.4125 11.4125C21.0208 11.8042 20.55 12 20 12H6ZM8 10H18C18 9.45 18.1958 8.97917 18.5875 8.5875C18.9792 8.19583 19.45 8 20 8V4C19.45 4 18.9792 3.80417 18.5875 3.4125C18.1958 3.02083 18 2.55 18 2H8C8 2.55 7.80417 3.02083 7.4125 3.4125C7.02083 3.80417 6.55 4 6 4V8C6.55 8 7.02083 8.19583 7.4125 8.5875C7.80417 8.97917 8 9.45 8 10ZM19 16H2C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V3H2V14H19V16ZM6 10V2V10Z"
                          fill="<?= $activePage === 'history' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Riwayat</span>
            </a>

            <!-- Reports -->
            <a href="?page=reports"
               class="<?= $activePage === 'reports' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 14H6V7H4V14ZM8 14H10V4H8V14ZM12 14H14V10H12V14ZM2 18C1.45 18 0.979167 17.8042 0.5875 17.4125C0.195833 17.0208 0 16.55 0 16V2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0H16C16.55 0 17.0208 0.195833 17.4125 0.5875C17.8042 0.979167 18 1.45 18 2V16C18 16.55 17.8042 17.0208 17.4125 17.4125C17.0208 17.8042 16.55 18 16 18H2ZM2 16H16V2H2V16ZM2 2V16V2Z"
                          fill="<?= $activePage === 'reports' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Laporan</span>
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
