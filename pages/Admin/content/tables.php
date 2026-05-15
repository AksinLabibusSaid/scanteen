<?php
declare(strict_types=1);

use App\Repositories\DiningTableRepository;
use App\Staff\StaffAuth;
use App\Support\PublicUrl;
use App\Core\Database;

$venueId = (int) StaffAuth::venueId();
$dtr = new DiningTableRepository();
$allTables = $dtr->listByVenueId($venueId);

$filterStatus = $_GET['status'] ?? null;

// summary counts (always calculate from all tables)
$totalTablesCount = count($allTables);
$activeTablesCount = 0;
foreach ($allTables as $tt) {
    if ((int) ($tt['is_active'] ?? 0) === 1) {
        $activeTablesCount++;
    }
}

// occupied tables
$mysqli = Database::mysqli();
$sql = 'SELECT COUNT(DISTINCT dining_table_id) AS c FROM orders WHERE venue_id = ? AND dining_table_id IS NOT NULL AND status IN (\'accepted\', \'processing\', \'ready\', \'paid\')';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $venueId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$occupiedTablesCount = (int) ($row['c'] ?? 0);

// scans today
$sql2 = 'SELECT COUNT(*) AS c FROM orders WHERE venue_id = ? AND dining_table_id IS NOT NULL AND DATE(created_at) = CURDATE()';
$stmt2 = $mysqli->prepare($sql2);
$stmt2->bind_param('i', $venueId);
$stmt2->execute();
$row2 = $stmt2->get_result()->fetch_assoc();
$stmt2->close();
$scansToday = (int) ($row2['c'] ?? 0);

// occupied ids
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

// Filter the tables for display
$tables = $allTables;
if ($filterStatus === 'available') {
    $tables = array_filter($allTables, fn($t) => !in_array((int)$t['id'], $activeIds, true));
} elseif ($filterStatus === 'occupied') {
    $tables = array_filter($allTables, fn($t) => in_array((int)$t['id'], $activeIds, true));
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
        $isOccupied = in_array($tid, $activeIds, true);
    ?>
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden hover:shadow-md transition-all">
        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="poppins text-lg font-black text-[var(--text-dark)]">Meja <?= $tableNumber ?></h3>
                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider <?= $isOccupied ? 'bg-[#FDE8E4] text-[var(--brand)]' : 'bg-[#F0FDF4] text-[#16A34A]' ?>">
                    <?= $isOccupied ? 'Occupied' : 'Available' ?>
                </span>
            </div>

            <div class="flex items-center justify-center py-6 bg-gray-50 rounded-2xl mb-8 group cursor-pointer" onclick="showQR('<?= $tableNumber ?>', '<?= $token ?>', '<?= $scanUrl ?>')">
                <div id="qr-mini-<?= $token ?>" class="opacity-80 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <div class="flex items-center justify-between">
                <button class="flex items-center gap-1.5 text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest hover:text-[var(--brand)] transition-all" onclick="showQR('<?= $tableNumber ?>', '<?= $token ?>', '<?= $scanUrl ?>')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h7v7h-7z"/></svg>
                    Lihat QR
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

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
function showQR(table, token, url) {
    const w = window.open('', '_blank');
    if (!w) return;
    w.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>QR Meja ${table}</title><style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f5f5f5; }
        .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; }
        .table-num { font-size: 32px; font-weight: 900; margin-bottom: 5px; color: #1f2937; }
        .label { font-size: 14px; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 30px; }
        #qr { margin-bottom: 30px; }
        .footer { font-size: 12px; color: #d1d5db; }
        @media print { body { background: white; } .card { box-shadow: none; } }
    </style></head><body><div class="card">
        <div class="table-num">MEJA ${table}</div>
        <div class="label">SmartCanteen QR Code</div>
        <div id="qr"></div>
        <div class="footer">Scan untuk memesan</div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"><\/script>
    <script>
        QRCode.toCanvas(document.getElementById('qr'), '${url}', { width: 300, margin: 2 }, function(err) {
            if (err) console.error(err);
            else setTimeout(() => window.print(), 500);
        });
    <\/script></body></html>`);
    w.document.close();
}

(function () {
    const apiTable = <?= json_encode(PublicUrl::basePath() . '/api/staff/table.php') ?>;
    
    // Render mini QRs
    <?php foreach ($tables as $t): ?>
    QRCode.toCanvas(document.createElement('canvas'), <?= json_encode(PublicUrl::customerScanUrl($t['barcode_token'])) ?>, { width: 80, margin: 1 }, function(err, canvas) {
        if (!err) {
            const container = document.getElementById('qr-mini-<?= $t['barcode_token'] ?>');
            if (container) container.appendChild(canvas);
        }
    });
    <?php endforeach; ?>

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
