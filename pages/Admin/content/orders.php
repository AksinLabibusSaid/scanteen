<?php
declare(strict_types=1);

use App\Repositories\OrderListRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWriteRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;
use App\Support\Money;

$venueId = (int) StaffAuth::venueId();
$orderListRepo = new OrderListRepository();
$orderRepo = new OrderRepository();
$orderWriteRepo = new OrderWriteRepository();
$warungRepo = new WarungRepository();

function scanteen_admin_order_status_label(string $status): string
{
    return match ($status) {
        'pending_payment' => 'Menunggu Bayar',
        'paid' => 'Dibayar',
        'accepted' => 'Diterima',
        'processing' => 'Diproses',
        'ready' => 'Siap',
        'completed' => 'Selesai',
        'cancelled' => 'Batal',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function scanteen_admin_order_status_class(string $status): string
{
    return match ($status) {
        'pending_payment' => 'bg-[#F1F3F5] text-gray-500',
        'cancelled' => 'bg-red-50 text-red-600',
        'completed' => 'bg-emerald-50 text-emerald-700',
        default => 'bg-[#FDE8E4] text-[var(--brand)]',
    };
}

function scanteen_admin_payment_label(string $pm): string
{
    return match ($pm) {
        'qris' => 'QRIS',
        'cashier' => 'Kasir',
        'midtrans' => 'Midtrans',
        default => ucfirst($pm),
    };
}

function scanteen_admin_dining_label(string $type): string
{
    return match ($type) {
        'take_away' => 'Take Away',
        'dine_in' => 'Dine In',
        default => ucfirst(str_replace('_', ' ', $type)),
    };
}

function scanteen_admin_build_query(array $base, array $params = []): string
{
    return http_build_query(array_filter($base + $params, static fn ($value) => $value !== null && $value !== ''));
}

function scanteen_admin_redirect(string $url): void
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }

    echo '<script>window.location.href=' . json_encode($url, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';</script>';
    exit;
}

$filters = [
    'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : '',
    'status' => isset($_GET['status']) ? trim((string) $_GET['status']) : '',
    'date' => isset($_GET['date']) ? trim((string) $_GET['date']) : '',
    'payment' => isset($_GET['payment']) ? trim((string) $_GET['payment']) : '',
    'warung' => isset($_GET['warung']) ? (int) $_GET['warung'] : 0,
];
$selectedOrderId = isset($_GET['selected']) ? (int) $_GET['selected'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? ''));
    $returnQuery = trim((string) ($_POST['return_query'] ?? 'page=orders'));

    if ($action === 'mark_paid') {
        $publicToken = trim((string) ($_POST['public_token'] ?? ''));
        if ($publicToken !== '' && $orderWriteRepo->markPaidByPublicToken($publicToken)) {
            scanteen_admin_redirect('?' . $returnQuery . '&flash=mark_paid');
        }
        scanteen_admin_redirect('?' . $returnQuery . '&flash=mark_paid_failed');
    }

    if ($action === 'cancel') {
        $orderId = (int) ($_POST['order_id'] ?? 0);
        if ($orderId > 0 && $orderWriteRepo->cancelPendingPaymentOrder($orderId, $venueId)) {
            scanteen_admin_redirect('?' . $returnQuery . '&flash=cancelled');
        }
        scanteen_admin_redirect('?' . $returnQuery . '&flash=cancel_failed');
    }
}

$orders = $orderListRepo->listForVenueFiltered(
    $venueId,
    100,
    $filters['status'] !== '' ? $filters['status'] : null,
    $filters['date'] !== '' ? $filters['date'] : null,
    $filters['payment'] !== '' ? $filters['payment'] : null,
    $filters['warung'] > 0 ? $filters['warung'] : null,
    $filters['q'] !== '' ? $filters['q'] : null,
);

if ($selectedOrderId <= 0 && $orders !== []) {
    $selectedOrderId = (int) $orders[0]['id'];
}

$selectedOrder = $selectedOrderId > 0 ? $orderRepo->findById($selectedOrderId) : null;
if ($selectedOrder !== null && (int) $selectedOrder['venue_id'] !== $venueId) {
    $selectedOrder = null;
}
$selectedItems = $selectedOrder !== null ? $orderRepo->itemsByOrderId((int) $selectedOrder['id']) : [];
$selectedGroups = $selectedOrder !== null ? $orderRepo->groupItemsByWarung((int) $selectedOrder['id']) : [];
$warungs = $warungRepo->listByVenueId($venueId);
$stats = (new \App\Repositories\VenueStatsRepository())->dashboardKpis($venueId);
$flash = (string) ($_GET['flash'] ?? '');
$filterQuery = scanteen_admin_build_query(['page' => 'orders'], [
    'q' => $filters['q'],
    'status' => $filters['status'],
    'date' => $filters['date'],
    'payment' => $filters['payment'],
    'warung' => $filters['warung'] > 0 ? (string) $filters['warung'] : '',
]);
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Order Management</h1>
            <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Data pesanan diambil langsung dari database dan dapat diproses dari panel ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white px-5 py-3 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">Hari ini</p>
                <p class="poppins text-lg font-bold text-[var(--text-dark)]"><?= (int) $stats['today_orders'] ?> pesanan</p>
            </div>
            <div class="bg-[var(--brand)] px-5 py-3 rounded-xl shadow-lg text-white">
                <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Omzet</p>
                <p class="poppins text-lg font-bold"><?= htmlspecialchars(Money::formatIdr($stats['today_revenue']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </div>

    <?php if ($flash === 'mark_paid'): ?>
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-3 rounded-xl text-sm font-medium">Pesanan berhasil ditandai sudah dibayar.</div>
    <?php elseif ($flash === 'cancelled'): ?>
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-3 rounded-xl text-sm font-medium">Pesanan berhasil dibatalkan.</div>
    <?php elseif ($flash === 'mark_paid_failed' || $flash === 'cancel_failed'): ?>
        <div class="bg-red-50 text-red-700 border border-red-100 px-4 py-3 rounded-xl text-sm font-medium">Aksi gagal diproses.</div>
    <?php endif; ?>

    <form method="get" class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
        <input type="hidden" name="page" value="orders">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Cari Order</label>
                <input type="text" name="q" value="<?= htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8') ?>" placeholder="ORD-0515-001" class="w-full px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
            </div>
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <?php foreach (['pending_payment' => 'Menunggu Bayar', 'paid' => 'Dibayar', 'accepted' => 'Diterima', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai', 'cancelled' => 'Batal'] as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Tanggal</label>
                <input type="date" name="date" value="<?= htmlspecialchars($filters['date'], ENT_QUOTES, 'UTF-8') ?>" class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Warung</label>
                <select name="warung" class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <?php foreach ($warungs as $warung): ?>
                        <option value="<?= (int) $warung['id'] ?>" <?= $filters['warung'] === (int) $warung['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $warung['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Pembayaran</label>
                <select name="payment" class="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-[var(--text-dark)] outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <option value="qris" <?= $filters['payment'] === 'qris' ? 'selected' : '' ?>>QRIS</option>
                    <option value="cashier" <?= $filters['payment'] === 'cashier' ? 'selected' : '' ?>>Kasir</option>
                </select>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="?page=orders" class="px-4 py-2 rounded-xl bg-gray-50 text-gray-600 text-xs font-bold">Reset</a>
            <button class="px-5 py-2 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest">Terapkan</button>
        </div>
    </form>

    <div class="flex flex-col lg:flex-row gap-6 min-h-[720px]">
        <div class="flex-1 min-w-0 bg-white rounded-[24px] shadow-sm border border-gray-50 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="poppins text-lg font-bold text-[var(--brand)]">Daftar Pesanan</h2>
                <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest"><?= count($orders) ?> hasil</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#FAF7F6]">
                        <tr>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Order ID</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Tenant</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Customer</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Meja</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Amount</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Payment</th>
                            <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if ($orders === []): ?>
                            <tr>
                                <td colspan="7" class="px-8 py-10 text-center text-gray-400 text-sm">Tidak ada data pesanan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $isSelected = $selectedOrderId === (int) $order['id'];
                                $rowQuery = scanteen_admin_build_query(['page' => 'orders'], [
                                    'q' => $filters['q'],
                                    'status' => $filters['status'],
                                    'date' => $filters['date'],
                                    'payment' => $filters['payment'],
                                    'warung' => $filters['warung'] > 0 ? (string) $filters['warung'] : '',
                                    'selected' => (string) $order['id'],
                                ]);
                                ?>
                                <tr class="hover:bg-[#FAF7F6] transition-colors <?= $isSelected ? 'bg-[#FAF7F6] border-l-4 border-[var(--brand)]' : '' ?>">
                                    <td class="px-8 py-5">
                                        <a href="?<?= htmlspecialchars($rowQuery, ENT_QUOTES, 'UTF-8') ?>" class="text-sm font-black text-[var(--brand)] hover:underline"><?= htmlspecialchars((string) ($order['display_order_number'] ?? $order['order_number']), ENT_QUOTES, 'UTF-8') ?></a>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-bold text-[var(--text-dark)] opacity-70">
                                        <?= htmlspecialchars((string) ($order['tenant_names'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-bold text-[var(--text-dark)] opacity-70">
                                        <?= htmlspecialchars((string) ($order['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-bold text-[var(--text-dark)] opacity-70">
                                        <?= htmlspecialchars((string) $order['table_number'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-black text-[var(--text-dark)]">
                                        <?= htmlspecialchars(Money::formatIdr((float) $order['total']), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-8 py-5 text-xs font-bold text-[var(--text-muted)] uppercase tracking-widest">
                                        <?= htmlspecialchars(scanteen_admin_payment_label((string) ($order['payment_method'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black <?= scanteen_admin_order_status_class((string) $order['status']) ?>">
                                            <?= htmlspecialchars(scanteen_admin_order_status_label((string) $order['status']), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="w-full lg:w-[420px] bg-white rounded-[32px] shadow-lg border border-gray-100 flex flex-col sticky top-6 overflow-hidden h-fit">
            <div class="p-8 pb-6 bg-[#FAF7F6]/50 relative">
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-1">Detail Pesanan</p>
                <h2 class="poppins text-2xl font-black text-[var(--brand)]">
                    <?= htmlspecialchars((string) ($selectedOrder['display_order_number'] ?? $selectedOrder['order_number'] ?? 'Pilih order'), ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <?php if ($selectedOrder !== null): ?>
                    <div class="flex items-center justify-between mt-6 gap-3">
                        <span class="px-4 py-1 rounded-full text-[10px] font-black <?= scanteen_admin_order_status_class((string) $selectedOrder['status']) ?>">
                            <?= htmlspecialchars(scanteen_admin_order_status_label((string) $selectedOrder['status']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">
                            <?= htmlspecialchars((string) ($selectedOrder['payment_method'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex-1 overflow-y-auto p-8 pt-6 custom-scrollbar">
                <?php if ($selectedOrder === null): ?>
                    <div class="text-center text-gray-400 text-sm py-10">Pilih pesanan untuk melihat detail.</div>
                <?php else: ?>
                    <div class="bg-[#FAF7F6] p-6 rounded-[24px] mb-6 border border-gray-50">
                        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-4">Customer Details</p>
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-[var(--brand)] flex-shrink-0">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[var(--text-dark)] leading-none"><?= htmlspecialchars((string) ($selectedOrder['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="text-[11px] font-bold text-[var(--text-muted)] mt-1.5"><?= htmlspecialchars((string) ($selectedOrder['customer_email'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-[var(--brand)] flex-shrink-0">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h2"/>
                                        <circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M9 17h6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[var(--text-dark)] leading-none">Meja <?= htmlspecialchars((string) ($selectedOrder['table_number'] ?? '?'), ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="text-[11px] font-bold text-[var(--text-muted)] mt-1.5"><?= htmlspecialchars(scanteen_admin_dining_label((string) ($selectedOrder['dining_type'] ?? 'dine_in')), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px]">Order Items</p>
                            <?php $primaryTenant = $selectedGroups !== [] ? (string) ($selectedGroups[0]['warung_name'] ?? 'Multi Tenant') : 'Multi Tenant'; ?>
                            <span class="px-2 py-0.5 bg-[var(--brand)] text-white rounded text-[8px] font-black uppercase">
                                <?= htmlspecialchars($primaryTenant, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        <div class="space-y-4">
                            <?php foreach ($selectedGroups as $group): ?>
                                <div class="bg-[#FAF7F6] rounded-2xl p-4 border border-gray-50">
                                    <p class="text-xs font-black text-[var(--brand)] mb-3"><?= htmlspecialchars((string) $group['warung_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <div class="space-y-3">
                                        <?php foreach ($group['items'] as $item): ?>
                                            <div class="flex justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-black text-[var(--text-dark)]"><?= htmlspecialchars((string) $item['menu_name_snapshot'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-[10px] text-[var(--text-muted)]">Qty: <?= (int) $item['quantity'] ?> · <?= htmlspecialchars(Money::formatIdr((float) $item['unit_price']), ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-[10px] italic text-[#B22B1D] mt-1"><?= htmlspecialchars((string) (($item['note'] ?? '') !== '' ? $item['note'] : '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                                </div>
                                                <div class="text-sm font-black text-[var(--text-dark)]">
                                                    <?= htmlspecialchars(Money::formatIdr((float) $item['line_subtotal']), ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-6 px-1">
                        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-4">Summary</p>
                        <div class="bg-[var(--brand)] rounded-[24px] p-6 shadow-2xl text-white space-y-2">
                            <div class="flex justify-between text-xs font-bold text-white/70">
                                <span>Subtotal</span>
                                <span><?= htmlspecialchars(Money::formatIdr((float) ($selectedOrder['subtotal'] ?? 0)), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="flex justify-between text-xs font-bold text-white/70 border-b border-white/10 pb-3">
                                <span>Service Tax</span>
                                <span><?= htmlspecialchars(Money::formatIdr((float) ($selectedOrder['service_tax'] ?? 0)), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="flex justify-between items-center pt-1">
                                <span class="poppins text-xs font-bold uppercase tracking-widest">Total</span>
                                <span class="poppins text-xl font-black"><?= htmlspecialchars(Money::formatIdr((float) ($selectedOrder['total'] ?? 0)), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <?php if (($selectedOrder['status'] ?? '') === 'pending_payment'): ?>
                            <form method="post" class="flex-1">
                                <input type="hidden" name="action" value="mark_paid">
                                <input type="hidden" name="public_token" value="<?= htmlspecialchars((string) ($selectedOrder['public_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="return_query" value="<?= htmlspecialchars($filterQuery . '&selected=' . (int) $selectedOrder['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button class="w-full px-4 py-3 rounded-2xl bg-[var(--brand)] text-white text-[10px] font-black uppercase tracking-widest shadow-sm hover:opacity-90 transition-all">Mark Paid</button>
                            </form>
                            <form method="post" class="flex-1" onsubmit="return confirm('Batalkan pesanan ini?')">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="order_id" value="<?= (int) $selectedOrder['id'] ?>">
                                <input type="hidden" name="return_query" value="<?= htmlspecialchars($filterQuery . '&selected=' . (int) $selectedOrder['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 bg-white text-gray-500 text-[10px] font-black uppercase tracking-widest hover:border-red-200 hover:text-red-600 transition-all">Cancel</button>
                            </form>
                        <?php else: ?>
                            <div class="w-full text-center text-[10px] font-bold text-[var(--text-muted)] py-3 bg-gray-50 rounded-2xl">Aksi manual hanya tersedia untuk pesanan menunggu bayar.</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #F1F3F5; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #E9ECEF; }
</style>
