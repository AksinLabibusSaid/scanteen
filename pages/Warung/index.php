<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/app/Staff/staff_portal_guard.php';
scanteen_staff_require_portal('warung');

// Daftar halaman yang diizinkan untuk Warung
$allowedPages = [
    'dashboard' => __DIR__ . '/content/dashboard.php',
    'orders'    => __DIR__ . '/content/orders.php',
    'menu'      => __DIR__ . '/content/menu.php',
    'history'   => __DIR__ . '/content/history.php',
    'profile'   => dirname(__DIR__) . '/Shared/content/profile.php',
];

$pageKey = isset($_GET['page']) ? (string) $_GET['page'] : 'dashboard';
if (!array_key_exists($pageKey, $allowedPages)) {
    $pageKey = 'dashboard';
}

$activePage  = $pageKey;
$contentFile = $allowedPages[$pageKey];

$pageTitle = match($pageKey) {
    'orders'   => 'Pesanan',
    'menu'     => 'Manajemen Menu',
    'history'  => 'Riwayat',
    'profile'  => 'Profil Saya',
    default    => 'Overview',
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> — Scanteen Warung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #570000;
            --brand-muted: #F7E9E6;
            --brand-soft: #F5E3DF;
            --text-dark: #261816;
            --text-muted: #5A413D;
            --error-red: #BA1A1A;
            --category-badge: #FDE2DE;
            --card-border: #E9ECEF;
            --row-divider: #F1F3F5;
            --success-green: #16A34A;
            --success-bg: #F0FDF4;
        }

        * { font-family: 'Inter', -apple-system, sans-serif; }
        .poppins { font-family: 'Poppins', sans-serif; }

        .active-nav {
            border-left: 4px solid var(--brand);
            background-color: var(--brand-muted);
        }
        .active-nav span { color: var(--brand); font-weight: 600; }
        .active-nav svg path { fill: var(--brand) !important; }

        .content-area {
            background: #FAF7F6;
        }

        .content-area::-webkit-scrollbar        { width: 6px; }
        .content-area::-webkit-scrollbar-track  { background: #f1f5f9; }
        .content-area::-webkit-scrollbar-thumb  { background: #cbd5e1; border-radius: 3px; }

        .card {
            background: white;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .brand-btn {
            background: var(--brand);
            color: white;
            transition: all 0.2s;
        }
        .brand-btn:hover {
            background: #6b0000;
            transform: translateY(-1px);
        }
    </style>

</head>
<body class="bg-gray-50" data-page="<?= htmlspecialchars($pageKey, ENT_QUOTES, 'UTF-8') ?>">

    <div class="flex h-screen overflow-hidden">

        <!-- ===== SIDEBAR ===== -->
        <?php include __DIR__ . '/component/sidebar.php'; ?>

        <!-- ===== KOLOM KANAN ===== -->
        <div class="flex flex-col flex-1 min-w-0">

            <!-- ===== HEADER ===== -->
            <?php include dirname(__DIR__) . '/Shared/component/header.php'; ?>

            <!-- ===== KONTEN ===== -->
            <main class="content-area flex-1 overflow-y-auto p-6">
                <?php 
                if (file_exists($contentFile)) {
                    include $contentFile; 
                } else {
                    echo '<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-400">
                            <svg class="mx-auto mb-4 opacity-20" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M2 12h20"/></svg>
                            <p class="text-lg font-semibold">Halaman Sedang Dikembangkan</p>
                            <p class="text-sm mt-1">Konten untuk ' . htmlspecialchars($pageTitle) . ' akan segera hadir.</p>
                          </div>';
                }
                ?>
            </main>

        </div>

    </div>

</body>
</html>
