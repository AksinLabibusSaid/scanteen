<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Daftar halaman yang diizinkan untuk Warung
$allowedPages = [
    'dashboard' => __DIR__ . '/content/dashboard.php',
    'orders'    => __DIR__ . '/content/orders.php',
    'menu'      => __DIR__ . '/content/menu.php',
    'history'   => __DIR__ . '/content/history.php',
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', -apple-system, Roboto, Helvetica, sans-serif; }

        .active-nav {
            border-left: 4px solid #991B1B;
            background-color: rgba(254, 242, 242, 0.5);
        }
        .active-nav span { color: #991B1B; }

        .content-area::-webkit-scrollbar        { width: 6px; }
        .content-area::-webkit-scrollbar-track  { background: #f1f5f9; }
        .content-area::-webkit-scrollbar-thumb  { background: #cbd5e1; border-radius: 3px; }
    </style>
</head>
<body class="bg-gray-50" data-page="<?= htmlspecialchars($pageKey, ENT_QUOTES, 'UTF-8') ?>">

    <div class="flex h-screen overflow-hidden">

        <!-- ===== SIDEBAR ===== -->
        <?php include __DIR__ . '/component/sidebar.php'; ?>

        <!-- ===== KOLOM KANAN ===== -->
        <div class="flex flex-col flex-1 min-w-0">

            <!-- ===== HEADER ===== -->
            <?php include __DIR__ . '/component/header.php'; ?>

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
