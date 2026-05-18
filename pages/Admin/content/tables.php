<?php
declare(strict_types=1);

use App\Repositories\DiningTableRepository;
use App\Staff\StaffAuth;
use App\Support\PublicUrl;
use App\Core\Database;
use chillerlan\QRCode\QRCode;

$venueId = (int) StaffAuth::venueId();
$mysqli = Database::mysqli();

// Ensure columns exist on dining_tables
try {
    $resScan = $mysqli->query("SHOW COLUMNS FROM dining_tables LIKE 'last_scanned_at'");
    if ($resScan->num_rows === 0) {
        $mysqli->query("ALTER TABLE dining_tables ADD COLUMN last_scanned_at DATETIME NULL");
    }
    $resClear = $mysqli->query("SHOW COLUMNS FROM dining_tables LIKE 'last_cleared_at'");
    if ($resClear->num_rows === 0) {
        $mysqli->query("ALTER TABLE dining_tables ADD COLUMN last_cleared_at DATETIME NULL");
    }
} catch (\Throwable $e) {}

$dtr = new DiningTableRepository();
$allTables = $dtr->listByVenueId($venueId);

$filterStatus = $_GET['status'] ?? null;

// summary counts (always calculate from all tables)
$totalTablesCount = count($allTables);

// occupied ids from active orders
$activeIds = [];
$sql3 = 'SELECT DISTINCT dining_table_id FROM orders WHERE venue_id = ? AND dining_table_id IS NOT NULL AND status IN (\'accepted\', \'processing\', \'ready\', \'paid\')';
$stmt3 = $mysqli->prepare($sql3);
$stmt3->bind_param('i', $venueId);
$stmt3->execute();
$res3 = $stmt3->get_result();
while ($r = $res3->fetch_assoc()) {
    $activeIds[] = (int) ($r['dining_table_id'] ?? 0);
}
$stmt3->close();

// determine occupied tables list using both orders and scan timestamps
$occupiedTableIds = [];
foreach ($allTables as $t) {
    $tid = (int) $t['id'];
    $hasActiveOrder = in_array($tid, $activeIds, true);
    
    $lastScan = $t['last_scanned_at'] ?? null;
    $lastClear = $t['last_cleared_at'] ?? null;
    $lastActivity = $t['last_activity_at'] ?? null;
    
    $isScannedActive = false;
    if ($lastScan !== null) {
        if ($lastClear === null || strtotime($lastScan) > strtotime($lastClear)) {
            // Check if activity is still fresh (within 5 minutes / 300 seconds)
            $activityFresh = true;
            if ($lastActivity !== null) {
                if ((time() - strtotime($lastActivity)) > 300) {
                    $activityFresh = false;
                }
            } else {
                if ((time() - strtotime($lastScan)) > 300) {
                    $activityFresh = false;
                }
            }
            
            if ($activityFresh) {
                $isScannedActive = true;
            }
        }
    }
    
    if ($hasActiveOrder || $isScannedActive) {
        $occupiedTableIds[] = $tid;
    }
}

$occupiedTablesCount = count($occupiedTableIds);
$activeTablesCount = $totalTablesCount - $occupiedTablesCount; // "Meja Tersedia" count

// scans today
$sql2 = 'SELECT COUNT(*) AS c FROM orders WHERE venue_id = ? AND dining_table_id IS NOT NULL AND DATE(created_at) = CURDATE()';
$stmt2 = $mysqli->prepare($sql2);
$stmt2->bind_param('i', $venueId);
$stmt2->execute();
$row2 = $stmt2->get_result()->fetch_assoc();
$stmt2->close();
$scansToday = (int) ($row2['c'] ?? 0);

// Filter the tables for display
$tables = $allTables;
if ($filterStatus === 'available') {
    $tables = array_filter($allTables, fn($t) => !in_array((int)$t['id'], $occupiedTableIds, true));
} elseif ($filterStatus === 'occupied') {
    $tables = array_filter($allTables, fn($t) => in_array((int)$t['id'], $occupiedTableIds, true));
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Meja & QR</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Pantau ketersediaan meja dan kelola kode QR pemesanan pelanggan.</p>
    </div>
    <div class="flex items-center gap-3">
        <form id="formAddTable" class="inline">
            <div class="flex items-center gap-2">
                <input id="inputTableNumber" name="table_number" type="text" placeholder="Nomor meja (Contoh: T-01)" class="px-4 py-3 rounded-xl border border-gray-100 text-xs outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required />
                <button id="btnAddTable" type="submit" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/></svg>
                    Tambah Meja Baru
                </button>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <a href="?page=tables" class="bg-white p-6 rounded-[24px] shadow-sm border <?= $filterStatus === null ? 'border-[var(--brand)]' : 'border-gray-50' ?> flex items-center gap-4 hover:shadow-md transition-all">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Total Meja</p>
            <p class="poppins text-xl font-black text-[var(--text-dark)]"><?= $totalTablesCount ?></p>
        </div>
    </a>
    <a href="?page=tables&status=available" class="bg-white p-6 rounded-[24px] shadow-sm border <?= $filterStatus === 'available' ? 'border-[#16A34A]' : 'border-gray-50' ?> flex items-center gap-4 hover:shadow-md transition-all">
        <div class="w-10 h-10 rounded-lg bg-[#F0FDF4] flex items-center justify-center text-[#16A34A]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Meja Tersedia</p>
            <p class="poppins text-xl font-black text-[#16A34A]"><?= $activeTablesCount ?></p>
        </div>
    </a>
    <a href="?page=tables&status=occupied" class="bg-white p-6 rounded-[24px] shadow-sm border <?= $filterStatus === 'occupied' ? 'border-[var(--brand)]' : 'border-gray-50' ?> flex items-center gap-4 hover:shadow-md transition-all">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Meja Terisi</p>
            <p class="poppins text-xl font-black text-[var(--brand)]"><?= $occupiedTablesCount ?></p>
        </div>
    </a>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h7v7h-7z"/><path d="M7 7h1"/><path d="M18 7h1"/><path d="M7 18h1"/></svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Scan Hari Ini</p>
            <p class="poppins text-xl font-black text-blue-600"><?= $scansToday ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
    <?php foreach ($tables as $t):
        $tid = (int) $t['id'];
        $tableNumber = htmlspecialchars((string) $t['table_number'], ENT_QUOTES, 'UTF-8');
        $token = (string) $t['barcode_token'];
        $scanUrl = PublicUrl::customerScanUrl($token);
        $qrDataUri = (new QRCode())->render($scanUrl);
        $isOccupied = in_array($tid, $occupiedTableIds, true);
    ?>
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden hover:shadow-md transition-all">
        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="poppins text-lg font-black text-[var(--text-dark)]">Meja <?= $tableNumber ?></h3>
                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider <?= $isOccupied ? 'bg-[#FDE8E4] text-[var(--brand)]' : 'bg-[#F0FDF4] text-[#16A34A]' ?>">
                    <?= $isOccupied ? 'Digunakan' : 'Tersedia' ?>
                </span>
            </div>

            <div class="flex items-center justify-center py-6 bg-gray-50 rounded-2xl mb-8 group cursor-pointer" onclick="showQR('<?= $tableNumber ?>', '<?= $qrDataUri ?>')">
                <div class="w-20 h-20 flex flex-col items-center justify-center text-gray-400 group-hover:text-[var(--brand)] transition-colors">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h7v7h-7z"/><path d="M7 7h1"/><path d="M18 7h1"/><path d="M7 18h1"/></svg>
                    <span class="text-[10px] font-black uppercase tracking-wider mt-2">Klik untuk Scan</span>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button class="flex items-center gap-1.5 text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest hover:text-[var(--brand)] transition-all" onclick="showQR('<?= $tableNumber ?>', '<?= $qrDataUri ?>')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h7v7h-7z"/></svg>
                    Lihat QR
                </button>
                <button class="btn-clear-table flex items-center gap-1.5 text-[9px] font-black text-[#16A34A] uppercase tracking-widest hover:text-[#15803D] transition-all" data-id="<?= $tid ?>" data-num="<?= $tableNumber ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M19 7l-.85 11.4A2 2 0 0 1 16.15 20H7.85a2 2 0 0 1-2-1.6L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Clear
                </button>
                <button class="btn-delete-table text-gray-300 hover:text-[#BA1A1A] transition-all" data-id="<?= $tid ?>" data-num="<?= $tableNumber ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($tables)): ?>
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-50 p-12 text-center text-gray-400">
        <p class="text-sm font-bold">Tidak ada meja yang sesuai filter.</p>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showQR(table, dataUri) {
    const w = window.open('', '_blank');
    if (!w) return;
    w.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>QR Meja ${table}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #556052; }
        .card { background: white; width: 700px; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 40px; border-bottom: 2px solid #E05A4E; }
        .logo-area { display: flex; align-items: center; gap: 10px; background: #F3F4F6; padding: 8px 16px; border-radius: 12px; }
        .logo-text { font-weight: 800; color: #7B0009; letter-spacing: 1px; font-size: 14px; text-transform: uppercase; }
        .version { color: #9CA3AF; font-size: 12px; font-weight: 600; letter-spacing: 0.5px; }
        .content { display: flex; padding: 40px; gap: 40px; align-items: center; }
        .left { flex: 1.2; text-align: left; }
        .reserved { color: #E05A4E; font-size: 12px; font-weight: 800; letter-spacing: 1px; margin-bottom: 5px; text-transform: uppercase; }
        .table-num { font-size: 56px; font-weight: 800; color: #7B0009; margin-bottom: 10px; line-height: 1; }
        .line { width: 40px; height: 3px; background: #E05A4E; opacity: 0.3; margin-bottom: 20px; }
        .desc { color: #6B7280; font-size: 14px; line-height: 1.6; margin-bottom: 30px; }
        .steps { display: flex; flex-direction: column; gap: 12px; }
        .step { display: flex; align-items: center; gap: 12px; background: white; border: 1px solid #F3F4F6; padding: 12px 16px; border-radius: 12px; }
        .step-icon { width: 32px; height: 32px; background: #FEF2F2; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .step-num { font-weight: 800; color: #7B0009; font-size: 14px; }
        .step-text { color: #1F2937; font-size: 13px; font-weight: 700; }
        .right { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 20px; }
        .qr-wrapper { position: relative; padding: 15px; }
        .qr-container { background: white; border-radius: 16px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .qr-img { width: 180px; height: 180px; display: block; }
        .corner { position: absolute; width: 30px; height: 30px; border-color: #7B0009; border-width: 4px; border-style: solid; }
        .corner.tl { top: 0; left: 0; border-right: 0; border-bottom: 0; border-top-left-radius: 20px; }
        .corner.tr { top: 0; right: 0; border-left: 0; border-bottom: 0; border-top-right-radius: 20px; }
        .corner.bl { bottom: 0; left: 0; border-right: 0; border-top: 0; border-bottom-left-radius: 20px; }
        .corner.br { bottom: 0; right: 0; border-left: 0; border-top: 0; border-bottom-right-radius: 20px; }
        .badge { background: #FEE2E2; color: #7B0009; font-size: 11px; font-weight: 800; padding: 8px 16px; border-radius: 20px; display: flex; align-items: center; gap: 6px; letter-spacing: 0.5px; text-transform: uppercase; }
        .footer-bar { background: #7B0009; color: white; text-align: center; padding: 12px; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        @media print { 
            body { background: white; } 
            .card { box-shadow: none; border: none; }
            .header { border-bottom: 2px solid #7B0009; }
        }
    </style></head><body onload="window.print()">
    <div class="card">
        <div class="header">
            <div class="logo-area">
                <span class="logo-text">SmartCanteen</span>
            </div>
            <div class="version">INTEGRATED V1.0</div>
        </div>
        <div class="content">
            <div class="left">
                <div class="reserved">RESERVED FOR</div>
                <div class="table-num">MEJA ${table}</div>
                <div class="line"></div>
                <p class="desc">Pengalaman makan modern dalam satu genggaman</p>
                
                <div class="steps">
                    <div class="step">
                        <div class="step-icon"><span class="step-num">01</span></div>
                        <span class="step-text">Scan Kode</span>
                    </div>
                    <div class="step">
                        <div class="step-icon"><span class="step-num">02</span></div>
                        <span class="step-text">Pesan & Bayar</span>
                    </div>
                    <div class="step">
                        <div class="step-icon"><span class="step-num">03</span></div>
                        <span class="step-text">Nikmati</span>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="qr-wrapper">
                    <div class="corner tl"></div>
                    <div class="corner tr"></div>
                    <div class="corner bl"></div>
                    <div class="corner br"></div>
                    <div class="qr-container">
                        <img src="${dataUri}" class="qr-img" />
                    </div>
                </div>
                <div class="badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    SCAN SEKARANG
                </div>
            </div>
        </div>
        <div class="footer-bar">
            DIGITAL SOLUTION FOR PREMIUM ORDERING
        </div>
    </div>
    </body></html>`);
    w.document.close();
}

(function () {
    const apiTable = <?= json_encode(PublicUrl::basePath() . '/api/staff/table.php') ?>;

    document.getElementById('formAddTable').addEventListener('submit', async function(e) {
        e.preventDefault();
        const num = document.getElementById('inputTableNumber').value.trim();
        if (!num) return;
        const res = await fetch(apiTable, {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'create', table_number: num })
        });
        const data = await res.json();
        if (data.ok) {
            if (typeof scanteenLoadPage === 'function') scanteenLoadPage(window.location.search);
            else location.reload();
        } else alert(data.error || 'Gagal');
    });
    
    document.querySelectorAll('.btn-clear-table').forEach(btn => {
        btn.addEventListener('click', async () => {
            Swal.fire({
                title: 'Clear Meja ' + btn.dataset.num + '?',
                text: 'Ini akan membatalkan pesanan yang belum dibayar dan menyelesaikan pesanan yang aktif.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7B0009',
                cancelButtonColor: '#675C5C',
                confirmButtonText: 'Ya, Clear!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await fetch(apiTable, {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'clear', id: parseInt(btn.dataset.id) })
                    });
                    const data = await res.json();
                    if (data.ok) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Meja ' + btn.dataset.num + ' berhasil diclear!',
                            icon: 'success',
                            confirmButtonColor: '#7B0009'
                        }).then(() => {
                            if (typeof scanteenLoadPage === 'function') scanteenLoadPage(window.location.search);
                            else location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.error || 'Terjadi kesalahan',
                            icon: 'error',
                            confirmButtonColor: '#7B0009'
                        });
                    }
                }
            });
        });
    });

    document.querySelectorAll('.btn-delete-table').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Hapus meja ' + btn.dataset.num + ' secara permanen?')) return;
            const res = await fetch(apiTable, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: parseInt(btn.dataset.id) })
            });
            const data = await res.json();
            if (data.ok) {
                if (typeof scanteenLoadPage === 'function') scanteenLoadPage(window.location.search);
                else location.reload();
            } else alert(data.error || 'Gagal');
        });
    });
})();
</script>
