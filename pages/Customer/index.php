<?php

declare(strict_types=1);

session_start();

require_once dirname(__DIR__, 2) . '/config/db.php';

use App\Customer\CustomerAccess;
use App\Customer\CustomerSessionKeys;
use App\Repositories\MenuRepository;
use App\Repositories\OrderRepository;
use App\Services\CartService;
use App\Services\CartViewBuilder;
use App\Services\CheckoutDraftService;
use App\Services\VenueOperatingService;

CustomerAccess::syncTableTokenFromRequest();

$customerContext = CustomerAccess::contextFromSession();
$customerHasAccess = $customerContext !== null;

$orderRepo = new OrderRepository();
$activeOrderForBanner = null;
$customerOrder = null;

$customerCartSummary = null;
$checkoutDraft = null;

if ($customerHasAccess && $customerContext !== null) {
    // Check 5 minute inactivity (300 seconds)
    $lastAct = $_SESSION['last_activity'] ?? null;
    if ($lastAct !== null && (time() - $lastAct) > 300) {
        // Clear session and set last_cleared_at to make it available
        try {
            $mysqli = \App\Core\Database::mysqli();
            $now = date('Y-m-d H:i:s');
            $stmtClear = $mysqli->prepare("UPDATE dining_tables SET last_cleared_at = ? WHERE id = ?");
            $stmtClear->bind_param('si', $now, $customerContext->diningTableId);
            $stmtClear->execute();
            $stmtClear->close();
        } catch (\Throwable $e) {}

        \App\Customer\CustomerAccess::clear();
        $_SESSION['scan_error_type'] = 'inactivity';
        $_SESSION['scan_error'] = "Sesi Anda telah berakhir karena tidak ada aktivitas selama 5 menit.";
        header('Location: ./index.php?page=need-scan');
        exit;
    }
    $_SESSION['last_activity'] = time();

    // Update last_activity_at in database
    try {
        $mysqli = \App\Core\Database::mysqli();
        $resAct = $mysqli->query("SHOW COLUMNS FROM dining_tables LIKE 'last_activity_at'");
        if ($resAct->num_rows === 0) {
            $mysqli->query("ALTER TABLE dining_tables ADD COLUMN last_activity_at DATETIME NULL");
        }
        $stmtAct = $mysqli->prepare("UPDATE dining_tables SET last_activity_at = NOW() WHERE id = ?");
        $stmtAct->bind_param('i', $customerContext->diningTableId);
        $stmtAct->execute();
        $stmtAct->close();
    } catch (\Throwable $e) {}

    // Check if table was cleared by admin
    $lastCleared = null;
    try {
        $mysqli = \App\Core\Database::mysqli();
        $stmt = $mysqli->prepare("SELECT last_cleared_at FROM dining_tables WHERE id = ?");
        $stmt->bind_param('i', $customerContext->diningTableId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $lastCleared = $row['last_cleared_at'] ?? null;
    } catch (\Throwable $e) {
        // Kolom mungkin belum ada (akan dibuat otomatis saat admin klik Clear pertama kali)
    }
    
    $sessionCleared = $_SESSION['customer_last_seen_cleared_at'] ?? null;
    
    if ($lastCleared !== null && $sessionCleared !== $lastCleared) {
        // Table was cleared (timestamp changed)!
        (new \App\Services\CheckoutDraftService())->clear();
        (new \App\Services\CartService())->clear();
        
        // Update session to match the new clear timestamp
        $_SESSION['customer_last_seen_cleared_at'] = $lastCleared;
        
        // Clear active order token so customer returns to home page
        unset($_SESSION[CustomerSessionKeys::LAST_ORDER_TOKEN]);
        
        header('Location: ./index.php?page=home');
        exit;
    }

    $cartSvc = new CartService();
    $menuRepo = new MenuRepository();
    $customerCartSummary = (new CartViewBuilder($menuRepo, $cartSvc))->summarize($customerContext);
    $checkoutDraft = (new CheckoutDraftService())->get();

    $activeOrdersForBanner = $orderRepo->findAllTrackableForTable($customerContext->diningTableId);

    // Track order statuses for auto-refresh
    $currentStatusStr = '';
    foreach ($activeOrdersForBanner as $o) {
        $currentStatusStr .= $o['id'] . ':' . $o['status'] . ',';
    }
    $_SESSION['customer_last_seen_order_statuses'] = $currentStatusStr;

    // Track venue settings for auto-refresh
    $venueIdForTrack = $_SESSION[CustomerSessionKeys::VENUE_ID] ?? 0;
    if ($venueIdForTrack > 0) {
        $stmtv = $mysqli->prepare("SELECT maintenance_mode, operating_hours, allow_qris, allow_cash, allow_debit FROM venues WHERE id = ?");
        $stmtv->bind_param('i', $venueIdForTrack);
        $stmtv->execute();
        $v = $stmtv->get_result()->fetch_assoc();
        $stmtv->close();
        
        $_SESSION['customer_last_seen_venue_state'] = md5(json_encode($v));
    }

    $customerOrder = null;
    $tok = '';
    if (isset($_GET['o']) && is_string($_GET['o']) && strlen(trim($_GET['o'])) === 32) {
        $tok = trim($_GET['o']);
        $o = $orderRepo->findByPublicToken($tok);
        if ($o !== null && (int) $o['dining_table_id'] === $customerContext->diningTableId) {
            $_SESSION[CustomerSessionKeys::LAST_ORDER_TOKEN] = $tok;
            $customerOrder = $o;
        }
    }
    if ($customerOrder === null) {
        $tok = $_SESSION[CustomerSessionKeys::LAST_ORDER_TOKEN] ?? '';
        if (is_string($tok) && strlen($tok) === 32) {
            $o = $orderRepo->findByPublicToken($tok);
            if ($o !== null && (int) $o['dining_table_id'] === $customerContext->diningTableId) {
                $customerOrder = $o;
            }
        }
    }
}

$customerOrderGroups = null;
if ($customerOrder !== null) {
    $customerOrderGroups = $orderRepo->groupItemsByWarung((int) $customerOrder['id']);
}

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
    'pembayaran' => __DIR__ . '/content/bayar-kasir.php',
    'status' => __DIR__ . '/content/status-belum-bayar.php',
    'pesanan' => __DIR__ . '/content/checkout.php',
];

$pageKey = isset($_GET['page']) ? (string) $_GET['page'] : 'home';
if ($pageKey === 'bayar-midtrans') {
    header('Location: ./index.php?page=pilih-pembayaran');
    exit;
}
if (!array_key_exists($pageKey, $allowedPages)) {
    $pageKey = 'home';
}

if (!$customerHasAccess) {
    $pageKey = 'need-scan';
    $contentFile = __DIR__ . '/content/need-scan.php';
} else {
    $orderLockedPages = [
        'bayar-kasir',
        'bayar-qris',
        'status-belum-bayar',
        'status-sudah-bayar',
        'struk',
        'pembayaran',
        'status',
    ];
    if (in_array($pageKey, $orderLockedPages, true) && $customerOrder === null) {
        header('Location: ./index.php?page=home');
        exit;
    }

    if ($customerOrder !== null) {
        $st = (string) $customerOrder['status'];
        $o = rawurlencode((string) $customerOrder['public_token']);
        if ($pageKey === 'status-sudah-bayar' && $st === 'pending_payment') {
            header('Location: ./index.php?page=status-belum-bayar&o=' . $o);
            exit;
        }
        if ($pageKey === 'status-belum-bayar' && $st !== 'pending_payment') {
            header('Location: ./index.php?page=status-sudah-bayar&o=' . $o);
            exit;
        }
        if (in_array($pageKey, ['bayar-kasir', 'bayar-qris', 'bayar-midtrans', 'pembayaran'], true) && $st !== 'pending_payment') {
            header('Location: ./index.php?page=status-sudah-bayar&o=' . $o);
            exit;
        }
    }

    $contentFile = $allowedPages[$pageKey];

    // Global Venue Status Check (except for critical pages like status/struk if order exists)
    $exemptPages = ['status-belum-bayar', 'status-sudah-bayar', 'struk', 'status'];
    if (!in_array($pageKey, $exemptPages, true) && isset($_SESSION[CustomerSessionKeys::VENUE_ID])) {
        $venueId = (int) $_SESSION[CustomerSessionKeys::VENUE_ID];
        $status = (new VenueOperatingService())->getStatus($venueId);
        if (!$status['isOpen']) {
            $pageKey = 'closed';
            $contentFile = __DIR__ . '/content/closed.php';
            // Pass status to the closed page
            $closedStatus = $status;
        }
    }
}

$customerApiRoot = '../../api/customer';

$bodyClass = '';
$outerClass = 'min-h-screen flex justify-center bg-[#FAF9F6]';
$innerClass = 'w-full max-w-[430px] md:max-w-[768px] lg:max-w-[1024px] relative bg-white';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanteen - Customer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kanit:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/scanteen/assets/css/customer.css">
</head>
<body
    class="customer-body <?php echo htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8'); ?>"
    data-page="<?php echo htmlspecialchars($pageKey, ENT_QUOTES, 'UTF-8'); ?>"
    data-api-root="<?php echo htmlspecialchars($customerApiRoot, ENT_QUOTES, 'UTF-8'); ?>"
>
    <div id="customer-device-gate" class="customer-device-gate" role="dialog" aria-modal="true" aria-labelledby="customer-device-gate-title" hidden>
        <div class="customer-device-gate__panel">
            <div class="customer-device-gate__accent" aria-hidden="true"></div>
            <div class="customer-device-gate__icon" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 6h16v12H4V6zm2 2v8h12V8H6z" fill="#800000" opacity=".9"/>
                    <path d="M8 4h8v2H8V4z" fill="#800000"/>
                </svg>
            </div>
            <h1 id="customer-device-gate-title" class="customer-device-gate__title">Gunakan ponsel atau tablet</h1>
            <p class="customer-device-gate__text">
                Tampilan pelanggan Scanteen diperuntukkan untuk <strong>smartphone</strong>, <strong>tablet</strong>, atau <strong>iPad</strong>.
                Silakan buka tautan yang sama dari perangkat tersebut.
            </p>
            <p class="customer-device-gate__hint">
                Jika Anda di laptop atau PC, gunakan mode perangkat (DevTools) hanya untuk pengujian, atau perkecil lebar jendela browser.
            </p>
        </div>
    </div>
    <div class="customer-app-shell" id="customer-app-shell">
    <div class="<?php echo htmlspecialchars($outerClass, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="<?php echo htmlspecialchars($innerClass, ENT_QUOTES, 'UTF-8'); ?>">

            <?php include __DIR__ . '/component/header.php'; ?>

            <?php 
            if (!file_exists($contentFile)) {
                $contentFile = __DIR__ . '/content/home.php';
            }
            include $contentFile; 
            ?>

        </div>
    </div>
    </div>

    <script src="/scanteen/assets/js/customer.js"></script>
    <script>
        // Auto-refresh when state changes (table cleared, order status, or venue settings)
        let isTransitioning = false;
        setInterval(async () => {
            if (isTransitioning) return;
            try {
                const res = await fetch('/scanteen/api/customer/check-status.php');
                const data = await res.json();
                if (data.cleared) {
                    isTransitioning = true;
                    // Show a beautiful premium transition overlay before reloading
                    const overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.inset = '0';
                    overlay.style.backgroundColor = 'rgba(0,0,0,0.6)';
                    overlay.style.backdropFilter = 'blur(12px)';
                    overlay.style.webkitBackdropFilter = 'blur(12px)';
                    overlay.style.zIndex = '999999';
                    overlay.style.display = 'flex';
                    overlay.style.alignItems = 'center';
                    overlay.style.justifyContent = 'center';
                    overlay.style.padding = '24px';
                    overlay.innerHTML = `
                        <div style="animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;" class="bg-white p-8 rounded-[32px] text-center max-w-sm w-full shadow-2xl border border-gray-100 flex flex-col items-center gap-5">
                            <div class="relative w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h2 class="text-lg font-extrabold text-[#7B0009] poppins uppercase tracking-tight">Sesi Anda Berakhir</h2>
                                <p class="text-xs text-stone-500 leading-relaxed">Mengalihkan halaman sesi Anda secara aman...</p>
                            </div>
                        </div>
                        <style>
                            @keyframes scaleUp {
                                from { opacity: 0; transform: scale(0.9) translateY(10px); }
                                to { opacity: 1; transform: scale(1) translateY(0); }
                            }
                        </style>
                    `;
                    document.body.appendChild(overlay);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);
                } else if (data.order_changed || data.venue_changed) {
                    window.location.reload();
                }
            } catch(e) {}
        }, 3000);
    </script>
</body>
</html>
