<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Daftar halaman yang diizinkan
$allowedPages = [
    'dashboard' => __DIR__ . '/content/dashboard.php',
    'orders'    => __DIR__ . '/content/orders.php',
    'history'   => __DIR__ . '/content/history.php',
    'reports'   => __DIR__ . '/content/reports.php',
];

$pageKey = isset($_GET['page']) ? (string) $_GET['page'] : 'dashboard';
if (!array_key_exists($pageKey, $allowedPages)) {
    $pageKey = 'dashboard';
}

$activePage  = $pageKey;
$contentFile = $allowedPages[$pageKey];

$pageTitle = match($pageKey) {
    'orders'   => 'Orders',
    'history'  => 'History',
    'reports'  => 'Reports',
    default    => 'Dashboard',
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> — Scanteen</title>
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
                <?php include $contentFile; ?>
            </main>

        </div>

    </div>

</body>
</html>
