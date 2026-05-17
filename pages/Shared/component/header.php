<?php
declare(strict_types=1);

use App\Core\Database;
use App\Staff\StaffAuth;

$userName = StaffAuth::check() ? StaffAuth::userName() : 'User';
$role = StaffAuth::check() ? (string) StaffAuth::role() : '';
$subLabel = match($role) {
    'admin' => 'Administrator',
    'kasir' => 'Kasir',
    'warung' => 'Pemilik Warung',
    default => $role
};

$avatar = $_SESSION['foto'] ?? 'https://api.builder.io/api/v1/image/assets/TEMP/fadf3b369dc031a0c33b9f7d9de993750210b555?width=72';

if ($role === 'warung') {
    $wid = StaffAuth::warungId();
    if ($wid !== null) {
        $stmt = Database::mysqli()->prepare('SELECT name FROM warungs WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $wid);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row !== null) {
            $subLabel = (string) $row['name'];
        }
    }
}
?>
<!-- Header -->
<header class="w-full h-16 px-6 flex items-center justify-between border-b border-gray-200 bg-white shadow-sm flex-shrink-0">

    <!-- Left Section - Search Bar -->
    <div class="flex-1 max-w-md">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.8333 15L8.58333 9.75C8.16667 10.0833 7.6875 10.3472 7.14583 10.5417C6.60417 10.7361 6.02778 10.8333 5.41667 10.8333C3.90278 10.8333 2.62153 10.309 1.57292 9.26042C0.524305 8.21181 0 6.93056 0 5.41667C0 3.90278 0.524305 2.62153 1.57292 1.57292C2.62153 0.524305 3.90278 0 5.41667 0C6.93056 0 8.21181 0.524305 9.26042 1.57292C10.309 2.62153 10.8333 3.90278 10.8333 5.41667C10.8333 6.02778 10.7361 6.60417 10.5417 7.14583C10.3472 7.6875 10.0833 8.16667 9.75 8.58333L15 13.8333L13.8333 15ZM5.41667 9.16667C6.45833 9.16667 7.34375 8.80208 8.07292 8.07292C8.80208 7.34375 9.16667 6.45833 9.16667 5.41667C9.16667 4.375 8.80208 3.48958 8.07292 2.76042C7.34375 2.03125 6.45833 1.66667 5.41667 1.66667C4.375 1.66667 3.48958 2.03125 2.76042 2.76042C2.03125 3.48958 1.66667 4.375 1.66667 5.41667C1.66667 6.45833 2.03125 7.34375 2.76042 8.07292C3.48958 8.80208 4.375 9.16667 5.41667 9.16667Z" fill="#9CA3AF"/>
            </svg>
            <input
                type="text"
                placeholder="Cari..."
                class="w-full pl-10 pr-4 py-2 text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[var(--brand-soft)] placeholder:text-gray-400 transition"
            />
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-6 ml-6">

        <!-- Vertical Divider -->
        <div class="w-px h-8 bg-gray-200"></div>

        <!-- User Info -->
        <div class="flex items-center gap-3">
            <a href="?page=profile" class="flex items-center gap-3 hover:opacity-80 transition-all">
                <div class="flex flex-col items-end">
                    <span class="text-sm font-bold text-[var(--text-dark)] leading-[14px]">
                        <?= htmlspecialchars($userName) ?>
                    </span>
                    <span class="text-[10px] font-medium text-[var(--text-muted)] leading-[15px]">
                        <?= htmlspecialchars($subLabel) ?>
                    </span>
                </div>
                <div class="w-10 h-10 rounded-full border-2 border-[var(--brand-soft)] overflow-hidden flex-shrink-0">
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Profile" class="w-full h-full object-cover">
                </div>
            </a>
        </div>

    </div>
</header>
