<?php
declare(strict_types=1);

$allowedPages = [
    'home' => __DIR__ . '/content/home.php',
    'keranjang' => __DIR__ . '/content/keranjang.php',
    'checkout' => __DIR__ . '/content/checkout.php',
    'pilih-pembayaran' => __DIR__ . '/content/pilih-pembayaran.php',
    'bayar-kasir' => __DIR__ . '/content/bayar-kasir.php',
    'bayar-qris' => __DIR__ . '/content/bayar-qris.php',
    'status-belum-bayar' => __DIR__ . '/content/status-belum-bayar.php',
    'status-sudah-bayar' => __DIR__ . '/content/status-sudah-bayar.php',
    'struk' => __DIR__ . '/content/struk.php',
    
    /* Alias untuk compatibility */
    'pembayaran' => __DIR__ . '/content/bayar-kasir.php',
    'status' => __DIR__ . '/content/status-belum-bayar.php',
    'pesanan' => __DIR__ . '/content/checkout.php',
];

$pageKey = isset($_GET['page']) ? (string) $_GET['page'] : 'home';
if (!array_key_exists($pageKey, $allowedPages)) {
    $pageKey = 'home';
}

$contentFile = $allowedPages[$pageKey];

$bodyClass = '';
$outerClass = 'min-h-screen flex justify-center bg-[#FAF9F6]';
$innerClass = 'w-full max-w-[430px] relative';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanteen - Customer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kanit:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/customer.css">
</head>
<body class="<?php echo htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8'); ?>" data-page="<?php echo htmlspecialchars($pageKey, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="<?php echo htmlspecialchars($outerClass, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="<?php echo htmlspecialchars($innerClass, ENT_QUOTES, 'UTF-8'); ?>">

            <?php include __DIR__ . '/component/header.php'; ?>

            <?php include $contentFile; ?>

        </div>
    </div>

    <script src="../../assets/js/customer.js"></script>
</body>
</html>