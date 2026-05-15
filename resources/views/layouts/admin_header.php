<?php
declare(strict_types=1);

use App\Staff\StaffAuth;

$adminName = StaffAuth::check() ? StaffAuth::userName() : 'Administrator';
$adminRole = StaffAuth::check() ? (string) StaffAuth::role() : 'Super Admin';
$adminAvatar = 'https://api.builder.io/api/v1/image/assets/TEMP/fadf3b369dc031a0c33b9f7d9de993750210b555?width=72';
$adminAvatar = $_SESSION['foto']  ?? 'https://api.builder.io/api/v1/image/assets/TEMP/fadf3b369dc031a0c33b9f7d9de993750210b555?width=72';
?>
<!-- Header Admin -->
<header class="w-full h-16 px-6 flex items-center justify-between border-b border-gray-200 bg-white shadow-sm flex-shrink-0">

    <!-- Left – Search Bar -->
    <div class="flex-1 max-w-md">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                 width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.8333 15L8.58333 9.75C8.16667 10.0833 7.6875 10.3472 7.14583 10.5417C6.60417 10.7361 6.02778 10.8333 5.41667 10.8333C3.90278 10.8333 2.62153 10.309 1.57292 9.26042C0.524305 8.21181 0 6.93056 0 5.41667C0 3.90278 0.524305 2.62153 1.57292 1.57292C2.62153 0.524305 3.90278 0 5.41667 0C6.93056 0 8.21181 0.524305 9.26042 1.57292C10.309 2.62153 10.8333 3.90278 10.8333 5.41667C10.8333 6.02778 10.7361 6.60417 10.5417 7.14583C10.3472 7.6875 10.0833 8.16667 9.75 8.58333L15 13.8333L13.8333 15ZM5.41667 9.16667C6.45833 9.16667 7.34375 8.80208 8.07292 8.07292C8.80208 7.34375 9.16667 6.45833 9.16667 5.41667C9.16667 4.375 8.80208 3.48958 8.07292 2.76042C7.34375 2.03125 6.45833 1.66667 5.41667 1.66667C4.375 1.66667 3.48958 2.03125 2.76042 2.76042C2.03125 3.48958 1.66667 4.375 1.66667 5.41667C1.66667 6.45833 2.03125 7.34375 2.76042 8.07292C3.48958 8.80208 4.375 9.16667 5.41667 9.16667Z" fill="#9CA3AF"/>
            </svg>
            <input type="text"
                   id="admin-search"
                   placeholder="Cari pengguna, tenant, menu..."
                   class="w-full pl-10 pr-4 py-2 text-sm text-[var(--text-muted)] bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[var(--brand-soft)] placeholder:text-gray-400 transition">
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-6 ml-6">

        <!-- Icon Buttons -->
        <div class="flex items-center gap-3">
            <button id="btn-notif-admin" class="relative p-2 rounded-full hover:bg-gray-100 transition-colors" title="Notifikasi">
                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 17V15H2V8C2 6.61667 2.41667 5.3875 3.25 4.3125C4.08333 3.2375 5.16667 2.53333 6.5 2.2V1.5C6.5 1.08333 6.64583 0.729167 6.9375 0.4375C7.22917 0.145833 7.58333 0 8 0C8.41667 0 8.77083 0.145833 9.0625 0.4375C9.35417 0.729167 9.5 1.08333 9.5 1.5V2.2C10.8333 2.53333 11.9167 3.2375 12.75 4.3125C13.5833 5.3875 14 6.61667 14 8V15H16V17H0ZM8 20C7.45 20 6.97917 19.8042 6.5875 19.4125C6.19583 19.0208 6 18.55 6 18H10C10 18.55 9.80417 19.0208 9.4125 19.4125C9.02083 19.8042 8.55 20 8 20ZM4 15H12V8C12 6.9 11.6083 5.95833 10.825 5.175C10.0417 4.39167 9.1 4 8 4C6.9 4 5.95833 4.39167 5.175 5.175C4.39167 5.95833 4 6.9 4 8V15Z" fill="#6B7280"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[var(--brand)] rounded-full border-2 border-white"></span>
            </button>
        </div>

        <!-- Vertical Divider -->
        <div class="w-px h-8 bg-gray-200"></div>

        <!-- User Info -->
        <div class="flex items-center gap-3">
            <div class="flex flex-col items-end">
                <span class="text-sm font-bold text-[var(--text-dark)] leading-[14px]">
                    <?= htmlspecialchars($adminName) ?>
                </span>
                <span class="text-[10px] font-medium text-[var(--text-muted)] leading-[15px]">
                    <?= htmlspecialchars($adminRole) ?>
                </span>
            </div>
            <div class="w-10 h-10 rounded-full border-2 border-[var(--brand-soft)] overflow-hidden flex-shrink-0">
                <img src="<?= htmlspecialchars($adminAvatar) ?>"
                     alt="Admin Profile"
                     class="w-full h-full object-cover">
            </div>
            <a href="/scanteen/pages/staff/logout.php" class="text-xs font-bold text-[var(--brand)] hover:underline">Keluar</a>
        </div>

    </div>
</header>
