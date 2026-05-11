<?php
/**
 * Layout Kasir - layout.php
 * 
 * Cara penggunaan:
 *   $activePage  = 'dashboard'; // (opsional) nama halaman aktif untuk highlight sidebar
 *   $pageTitle   = 'Dashboard'; // (opsional) judul tab browser
 *   ob_start();
 *   // ... konten halaman ...
 *   $pageContent = ob_get_clean();
 *   require __DIR__ . '/layout.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$activePage  = $activePage  ?? ($_GET['page'] ?? 'dashboard');
$pageTitle   = $pageTitle   ?? 'Cashier Portal';
$pageContent = $pageContent ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Cashier Portal</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, Roboto, Helvetica, sans-serif;
        }

        /* Sidebar active state */
        .active-nav {
            border-left: 4px solid #991B1B;
            background-color: rgba(254, 242, 242, 0.5);
        }
        .active-nav svg path { fill: #991B1B; }
        .active-nav span    { color: #991B1B; }

        /* Smooth scrollbar untuk area konten */
        .content-area::-webkit-scrollbar        { width: 6px; }
        .content-area::-webkit-scrollbar-track  { background: #f1f5f9; }
        .content-area::-webkit-scrollbar-thumb  { background: #cbd5e1; border-radius: 3px; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- ============================================================
         WRAPPER UTAMA  (sidebar + kolom kanan)
    ============================================================ -->
    <div class="flex h-screen overflow-hidden">

        <!-- ===== SIDEBAR ===== -->
        <?php require __DIR__ . '/sidebar.php'; ?>

        <!-- ===== KOLOM KANAN (header + konten) ===== -->
        <div class="flex flex-col flex-1 min-w-0">

            <!-- ===== HEADER ===== -->
            <?php require __DIR__ . '/header.php'; ?>

            <!-- ===== AREA KONTEN ===== -->
            <main class="content-area flex-1 overflow-y-auto p-6">
                <?= $pageContent ?>
            </main>

        </div><!-- /kolom kanan -->

    </div><!-- /wrapper utama -->

</body>
</html>
