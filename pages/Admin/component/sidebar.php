<?php
$activePage = $activePage ?? 'dashboard';

$db = \App\Core\Database::mysqli();
$venueId = (int) \App\Staff\StaffAuth::venueId();

// Count all active orders (paid, accepted, processing, ready)
$activeOrdersCount = 0;
$q = $db->prepare("SELECT COUNT(*) FROM orders WHERE venue_id = ? AND status IN ('paid', 'accepted', 'processing', 'ready')");
if ($q) {
    $q->bind_param('i', $venueId);
    $q->execute();
    $q->bind_result($activeOrdersCount);
    $q->fetch();
    $q->close();
}

// Count inactive warungs (is_active = 0)
$inactiveWarungsCount = 0;
$q = $db->prepare("SELECT COUNT(*) FROM warungs WHERE venue_id = ? AND is_active = 0");
if ($q) {
    $q->bind_param('i', $venueId);
    $q->execute();
    $q->bind_result($inactiveWarungsCount);
    $q->fetch();
    $q->close();
}

// Count menus that are out of stock (stock_quantity = 0) or unavailable (is_available = 0) across all warungs of this venue
$warningMenusCount = 0;
$q = $db->prepare("
    SELECT COUNT(*) 
    FROM menus m
    INNER JOIN warungs w ON w.id = m.warung_id
    WHERE w.venue_id = ? 
      AND (m.is_available = 0 OR m.stock_quantity = 0)
");
if ($q) {
    $q->bind_param('i', $venueId);
    $q->execute();
    $q->bind_result($warningMenusCount);
    $q->fetch();
    $q->close();
}

// Count inactive tables (is_active = 0)
$inactiveTablesCount = 0;
$q = $db->prepare("SELECT COUNT(*) FROM dining_tables WHERE venue_id = ? AND is_active = 0");
if ($q) {
    $q->bind_param('i', $venueId);
    $q->execute();
    $q->bind_result($inactiveTablesCount);
    $q->fetch();
    $q->close();
}
?>
<!-- Admin Sidebar -->
<aside class="w-64 min-h-screen bg-white border-r border-gray-100 flex flex-col justify-between flex-shrink-0">

    <!-- Top Section -->
    <div class="flex flex-col gap-8 p-6">

        <!-- Logo -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white overflow-hidden flex items-center justify-center flex-shrink-0 border border-gray-100">
                <img src="https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80"
                     alt="Scanteen Logo" class="w-full h-full object-contain">
            </div>
            <div class="flex flex-col justify-center" style="gap: 0.5px;">
                <h1 class="text-[var(--brand)] text-lg font-black leading-[22.5px]">Scanteen</h1>
                <p class="text-[var(--brand)] text-[10px] font-bold leading-[15px] tracking-[1px] uppercase">Panel Admin</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col gap-1">

            <!-- Dashboard -->
            <a href="?page=dashboard"
               class="<?= $activePage === 'dashboard' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 6V0H18V6H10ZM0 10V0H8V10H0ZM10 18V8H18V18H10ZM0 18V12H8V18H0ZM2 8H6V2H2V8ZM12 16H16V10H12V16ZM12 4H16V2H12V4ZM2 16H6V14H2V16Z"
                          fill="<?= $activePage === 'dashboard' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Dashboard</span>
            </a>

            <!-- Order Management -->
            <a href="?page=orders"
               class="<?= $activePage === 'orders' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center justify-between px-4 py-3 rounded-r-lg transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                        <path d="M6 20C5.45 20 4.97917 19.8042 4.5875 19.4125C4.19583 19.0208 4 18.55 4 18C4 17.45 4.19583 16.9792 4.5875 16.5875C4.97917 16.1958 5.45 16 6 16C6.55 16 7.02083 16.1958 7.4125 16.5875C7.80417 16.9792 8 17.45 8 18C8 18.55 7.80417 19.0208 7.4125 19.4125C7.02083 19.8042 6.55 20 6 20ZM16 20C15.45 20 14.9792 19.8042 14.5875 19.4125C14.1958 19.0208 14 18.55 14 18C14 17.45 14.1958 16.9792 14.5875 16.5875C14.9792 16.1958 15.45 16 16 16C16.55 16 17.0208 16.1958 17.4125 16.5875C17.8042 16.9792 18 17.45 18 18C18 18.55 17.8042 19.0208 17.4125 19.4125C17.0208 19.8042 16.55 20 16 20ZM5.15 4L7.55 9H14.55L17.3 4H5.15ZM4.2 2H18.95C19.3333 2 19.625 2.17083 19.825 2.5125C20.025 2.85417 20.0333 3.2 19.85 3.55L16.3 9.95C16.1167 10.2833 15.8708 10.5417 15.5625 10.725C15.2542 10.9083 14.9167 11 14.55 11H7.1L6 13H18V15H6C5.25 15 4.68333 14.6708 4.3 14.0125C3.91667 13.3542 3.9 12.7 4.25 12.05L5.6 9.6L2 2H0V0H3.25L4.2 2ZM7.55 9H14.55H7.55Z"
                              fill="<?= $activePage === 'orders' ? 'var(--brand)' : '#6B7280' ?>"/>
                    </svg>
                    <span class="font-semibold text-sm leading-5 whitespace-nowrap overflow-hidden text-ellipsis min-w-0">Manajemen Pesanan</span>
                </div>
                <?php if ($activeOrdersCount > 0): ?>
                    <div class="flex items-center justify-center min-w-[22px] h-[22px] bg-[var(--brand)] text-white px-1.5 rounded-full shadow-sm flex-shrink-0 ml-2" style="color: #ffffff !important; font-size: 11px !important; font-weight: 700 !important; line-height: 1 !important;" title="<?= $activeOrdersCount ?> pesanan aktif sedang berjalan">
                        <?= $activeOrdersCount ?>
                    </div>
                <?php endif; ?>
            </a>

            <!-- Tenant Management -->
            <a href="?page=tenants"
               class="<?= $activePage === 'tenants' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center justify-between px-4 py-3 rounded-r-lg transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                        <path d="M1 20V17H3V7L0 5V2H8V5L5 7V9H15V7L12 5V2H20V5L17 7V17H19V20H12V17H14V11H6V17H8V20H1ZM14 5H18V3.5L17 3H15L14 3.5V5ZM2 5H6V3.5L5 3H3L2 3.5V5ZM6 17H10V11H6V17ZM5 17V11V17ZM15 17V11V17Z"
                              fill="<?= $activePage === 'tenants' ? 'var(--brand)' : '#6B7280' ?>"/>
                    </svg>
                    <span class="font-semibold text-sm leading-5 whitespace-nowrap overflow-hidden text-ellipsis min-w-0">Manajemen Stan</span>
                </div>
                <?php if ($inactiveWarungsCount > 0): ?>
                    <div class="flex items-center justify-center min-w-[22px] h-[22px] bg-amber-500 text-white px-1.5 rounded-full shadow-sm flex-shrink-0 ml-2" style="color: #ffffff !important; font-size: 11px !important; font-weight: 700 !important; line-height: 1 !important;" title="<?= $inactiveWarungsCount ?> stan dinonaktifkan">
                        <?= $inactiveWarungsCount ?>
                    </div>
                <?php endif; ?>
            </a>

            <!-- Menu Management -->
            <a href="?page=menus"
               class="<?= $activePage === 'menus' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center justify-between px-4 py-3 rounded-r-lg transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                        <path d="M9 20V13H5V3C5 2.45 5.19583 1.97917 5.5875 1.5875C5.97917 1.19583 6.45 1 7 1H13C13.55 1 14.0208 1.19583 14.4125 1.5875C14.8042 1.97917 15 2.45 15 3V13H11V20H9ZM3 20V10C2.16667 9.71667 1.5 9.225 1 8.525C0.5 7.825 0.25 7.03333 0.25 6.15V1H2.25V6H3V1H5V6H5.75V1H7.75V6.15C7.75 7.03333 7.5 7.825 7 8.525C6.5 9.225 5.83333 9.71667 5 10V20H3ZM7 13H13V3H7V13Z"
                              fill="<?= $activePage === 'menus' ? 'var(--brand)' : '#6B7280' ?>"/>
                    </svg>
                    <span class="font-semibold text-sm leading-5 whitespace-nowrap overflow-hidden text-ellipsis min-w-0">Manajemen Menu</span>
                </div>
                <?php if ($warningMenusCount > 0): ?>
                    <div class="flex items-center justify-center min-w-[22px] h-[22px] bg-amber-500 text-white px-1.5 rounded-full shadow-sm flex-shrink-0 ml-2" style="color: #ffffff !important; font-size: 11px !important; font-weight: 700 !important; line-height: 1 !important;" title="<?= $warningMenusCount ?> menu habis atau dinonaktifkan">
                        <?= $warningMenusCount ?>
                    </div>
                <?php endif; ?>
            </a>

            <!-- Table Management -->
            <a href="?page=tables"
               class="<?= $activePage === 'tables' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center justify-between px-4 py-3 rounded-r-lg transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                        <path d="M1 20V18H3V15H1V13H3V2C3 1.45 3.19583 0.979167 3.5875 0.5875C3.97917 0.195833 4.45 0 5 0H19C19.55 0 20.0208 0.195833 20.4125 0.5875C20.8042 0.979167 21 1.45 21 2V18H23V20H1ZM5 13H9V2H5V13ZM11 13H19V2H11V13ZM5 18H19V15H5V18Z"
                              fill="<?= $activePage === 'tables' ? 'var(--brand)' : '#6B7280' ?>"/>
                    </svg>
                    <span class="font-semibold text-sm leading-5 whitespace-nowrap overflow-hidden text-ellipsis min-w-0">Manajemen Meja</span>
                </div>
                <?php if ($inactiveTablesCount > 0): ?>
                    <div class="flex items-center justify-center min-w-[22px] h-[22px] bg-amber-500 text-white px-1.5 rounded-full shadow-sm flex-shrink-0 ml-2" style="color: #ffffff !important; font-size: 11px !important; font-weight: 700 !important; line-height: 1 !important;" title="<?= $inactiveTablesCount ?> meja makan dinonaktifkan">
                        <?= $inactiveTablesCount ?>
                    </div>
                <?php endif; ?>
            </a>

            <!-- Reports -->
            <a href="?page=reports"
               class="<?= $activePage === 'reports' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 14H6V7H4V14ZM8 14H10V4H8V14ZM12 14H14V10H12V14ZM2 18C1.45 18 0.979167 17.8042 0.5875 17.4125C0.195833 17.0208 0 16.55 0 16V2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0H16C16.55 0 17.0208 0.195833 17.4125 0.5875C17.8042 0.979167 18 1.45 18 2V16C18 16.55 17.8042 17.0208 17.4125 17.4125C17.0208 17.8042 16.55 18 16 18H2ZM2 16H16V2H2V16ZM2 2V16V2Z"
                          fill="<?= $activePage === 'reports' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">Reports</span>
            </a>

            <!-- System Settings -->
            <a href="?page=settings"
               class="<?= $activePage === 'settings' ? 'active-nav' : 'text-gray-500 hover:bg-gray-50' ?> flex items-center gap-3 px-4 py-3 rounded-r-lg transition-colors">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.3 20L6.9 16.8C6.68333 16.7167 6.47917 16.6167 6.2875 16.5C6.09583 16.3833 5.90833 16.2583 5.725 16.125L2.75 17.375L0 12.625L2.575 10.675C2.55833 10.5583 2.55 10.4458 2.55 10.3375V9.6625C2.55 9.55417 2.55833 9.44167 2.575 9.325L0 7.375L2.75 2.625L5.725 3.875C5.90833 3.74167 6.1 3.61667 6.3 3.5C6.5 3.38333 6.7 3.28333 6.9 3.2L7.3 0H12.8L13.2 3.2C13.4167 3.28333 13.6208 3.38333 13.8125 3.5C14.0042 3.61667 14.1917 3.74167 14.375 3.875L17.35 2.625L20.1 7.375L17.525 9.325C17.5417 9.44167 17.55 9.55417 17.55 9.6625V10.3375C17.55 10.4458 17.5333 10.5583 17.5 10.675L20.075 12.625L17.325 17.375L14.375 16.125C14.1917 16.2583 14 16.3833 13.8 16.5C13.6 16.6167 13.4 16.7167 13.2 16.8L12.8 20H7.3ZM10.1 13.5C11.0667 13.5 11.8917 13.1583 12.575 12.475C13.2583 11.7917 13.6 10.9667 13.6 10C13.6 9.03333 13.2583 8.20833 12.575 7.525C11.8917 6.84167 11.0667 6.5 10.1 6.5C9.11667 6.5 8.2875 6.84167 7.6125 7.525C6.9375 8.20833 6.6 9.03333 6.6 10C6.6 10.9667 6.9375 11.7917 7.6125 12.475C8.2875 13.1583 9.11667 13.5 10.1 13.5Z"
                          fill="<?= $activePage === 'settings' ? 'var(--brand)' : '#6B7280' ?>"/>
                </svg>
                <span class="font-semibold text-sm leading-5">System Settings</span>
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
