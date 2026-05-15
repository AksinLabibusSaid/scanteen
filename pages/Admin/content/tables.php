<?php
declare(strict_types=1);

use App\Repositories\DiningTableRepository;
use App\Repositories\DiningTableWriteRepository;
use App\Staff\StaffAuth;
use App\Support\PublicUrl;
use App\Core\Database;

$venueId = (int) StaffAuth::venueId();
$dtr = new DiningTableRepository();
$tables = $dtr->listByVenueId($venueId);

// summary counts
$totalTables = count($tables);
$activeTables = 0;
foreach ($tables as $tt) {
    if ((int) ($tt['is_active'] ?? 0) === 1) {
        $activeTables++;
    }
}
$inactiveTables = max(0, $totalTables - $activeTables);

// occupied tables: distinct dining_table_id from active orders
$mysqli = Database::mysqli();
$sql = 'SELECT COUNT(DISTINCT dining_table_id) AS c FROM orders WHERE venue_id = ? AND dining_table_id IS NOT NULL AND status IN (\'accepted\', \'processing\', \'ready\', \'paid\')';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $venueId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$occupiedTables = (int) ($row['c'] ?? 0);

// approximate: scans today = number of orders created today with a dining_table_id
$sql2 = 'SELECT COUNT(*) AS c FROM orders WHERE venue_id = ? AND dining_table_id IS NOT NULL AND DATE(created_at) = CURDATE()';
$stmt2 = $mysqli->prepare($sql2);
$stmt2->bind_param('i', $venueId);
$stmt2->execute();
$row2 = $stmt2->get_result()->fetch_assoc();
$stmt2->close();
$scansToday = (int) ($row2['c'] ?? 0);

// get set of currently occupied table ids for per-card badge
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

?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Meja & QR</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Pantau ketersediaan meja dan kelola kode QR pemesanan pelanggan.</p>
    </div>
    <div class="flex items-center gap-3">
        <button id="btnBulkDownload" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-white border border-[var(--brand)] text-[var(--brand)] text-xs font-black uppercase tracking-widest shadow-sm hover:bg-[var(--brand-muted)] transition-all">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Bulk Download QR
        </button>
        <form id="formAddTable" class="inline" onsubmit="return false;">
            <div class="flex items-center gap-2">
                <input id="inputTableNumber" name="table_number" type="text" placeholder="Nomor meja (Contoh: T-01)" class="px-4 py-3 rounded-xl border border-gray-100 text-xs outline-none" />
                <button id="btnAddTable" type="submit" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/>
                    </svg>
                    Tambah Meja Baru
                </button>
                <span id="addTableMsg" class="text-xs ml-2 hidden"></span>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Total Meja</p>
            <p class="poppins text-xl font-black text-[var(--text-dark)]"><?= $totalTables ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-[#F0FDF4] flex items-center justify-center text-[#16A34A]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Meja Tersedia</p>
            <p class="poppins text-xl font-black text-[#16A34A]"><?= $activeTables ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Meja Terisi</p>
            <p class="poppins text-xl font-black text-[var(--brand)]"><?= $occupiedTables ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h7v7h-7z"/><path d="M7 7h1"/><path d="M18 7h1"/><path d="M7 18h1"/>
            </svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-0.5">Total Scan Hari Ini</p>
            <p class="poppins text-xl font-black text-blue-600"><?= $scansToday ?></p>
        </div>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8">
    <!-- Left Column: Table Grid -->
    <div class="flex-1">
        <div class="flex items-center justify-between mb-8 px-2">
            <h2 class="poppins text-lg font-black text-[var(--text-dark)] uppercase tracking-widest">Daftar Meja</h2>
            <div class="flex items-center gap-2">
                <button class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-[var(--brand)] transition-all shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="2" y1="14" x2="6" y2="14"/><line x1="10" y1="8" x2="14" y2="8"/><line x1="18" y1="16" x2="22" y2="16"/>
                    </svg>
                </button>
                <button class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-[var(--brand)] transition-all shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-12">
            <?php foreach ($tables as $t):
                $tid = (int) $t['id'];
                $tableNumber = htmlspecialchars((string) $t['table_number'], ENT_QUOTES, 'UTF-8');
                $isActive = (int) ($t['is_active'] ?? 0) === 1;
                $token = htmlspecialchars((string) $t['barcode_token'], ENT_QUOTES, 'UTF-8');
                $scanUrl = PublicUrl::customerScanUrl((string) $t['barcode_token']);
                $isOccupied = in_array($tid, $activeIds, true);
                $seats = isset($t['seats']) ? (int) $t['seats'] : 4;
            ?>
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden hover:shadow-md transition-all">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="poppins text-lg font-black text-[var(--text-dark)]"><?= $tableNumber ?></h3>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider <?= $isOccupied ? 'bg-[#FDE8E4] text-[var(--brand)]' : 'bg-[#F0FDF4] text-[#16A34A]' ?>">
                            <?= $isOccupied ? 'Occupied' : 'Available' ?>
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mb-8">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span class="text-[11px] font-bold text-gray-400"><?= $seats ?> Kursi</span>
                    </div>

                    <div class="h-px bg-gray-50 mb-6"></div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <button class="btn-show-qr flex items-center gap-1.5 text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest hover:text-[var(--brand)] transition-all" data-token="<?= $token ?>" data-url="<?= htmlspecialchars($scanUrl, ENT_QUOTES, 'UTF-8') ?>" data-table="<?= $tableNumber ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h7v7h-7z"/>
                                </svg>
                                QR
                            </button>
                            <button class="btn-edit-table flex items-center gap-1.5 text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest hover:text-[var(--brand)] transition-all" data-id="<?= $tid ?>" data-table="<?= $tableNumber ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </button>
                        </div>
                        <button class="btn-delete-table text-gray-300 hover:text-[#BA1A1A] transition-all" data-id="<?= $tid ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($tables)): ?>
            <div class="flex flex-col items-center justify-center py-16">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-300 mb-4">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                <p class="text-center text-gray-400 text-sm">Belum ada meja. Tambahkan meja pertama di atas.</p>
            </div>
        <?php else: ?>
            <div class="flex items-center justify-center">
                <button class="flex items-center gap-2 px-6 py-2 rounded-full text-[10px] font-black text-[var(--brand)] uppercase tracking-widest hover:bg-[var(--brand-muted)] transition-all group">
                    Lihat Semua <?= $totalTables ?> Meja
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-y-0.5 transition-transform">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar: Analytics & Bulk -->
    <div class="w-full lg:w-72 space-y-8">
        <!-- Scan Analytics Card -->
        <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
            <h3 class="poppins text-base font-black text-[var(--text-dark)] mb-1">Scan Analytics</h3>
            <p class="text-[9px] font-bold text-gray-400 mb-8">Aktivitas scan QR per jam</p>
            
            <!-- Simulated Bar Chart -->
            <div class="flex items-end justify-between h-32 gap-1 mb-6">
                <div class="w-2 bg-[#FDE8E4] h-[20%] rounded-full"></div>
                <div class="w-2 bg-[#FDE8E4] h-[35%] rounded-full"></div>
                <div class="w-2 bg-[#FDE8E4] h-[50%] rounded-full"></div>
                <div class="w-2 bg-[var(--brand)] h-[80%] rounded-full shadow-lg shadow-red-200"></div>
                <div class="w-2 bg-[#FDE8E4] h-[60%] rounded-full"></div>
                <div class="w-2 bg-[#FDE8E4] h-[75%] rounded-full"></div>
                <div class="w-2 bg-[var(--brand)] h-[90%] rounded-full shadow-lg shadow-red-200"></div>
                <div class="w-2 bg-[#FDE8E4] h-[40%] rounded-full"></div>
                <div class="w-2 bg-[#FDE8E4] h-[30%] rounded-full"></div>
            </div>

            <div class="flex justify-between items-center text-[8px] font-black text-gray-400 uppercase tracking-widest px-1">
                <span>10:00</span>
                <span class="text-[var(--brand)]">Peak (13:00)</span>
                <span>20:00</span>
            </div>

            <div class="mt-10 space-y-4">
                <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest border-b border-gray-50 pb-2">Top Performance</p>
                <div class="p-4 bg-[#FDE8E4]/50 rounded-[24px] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="poppins font-black text-[var(--brand)]">T-04</div>
                        <div class="flex flex-col">
                            <span class="text-[8px] font-bold text-[var(--text-muted)] leading-none">Paling sering</span>
                            <span class="text-[8px] font-bold text-[var(--text-muted)] leading-none mt-0.5">di-scan</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="poppins text-base font-black text-[var(--brand)] leading-none">32</span>
                        <span class="text-[8px] font-black text-[var(--brand)] block">scans</span>
                    </div>
                </div>
                <div class="p-4 bg-[#FDE8E4]/50 rounded-[24px] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="poppins font-black text-[var(--brand)]">T-12</div>
                        <div class="flex flex-col">
                            <span class="text-[8px] font-bold text-[var(--text-muted)] leading-none">Durasi</span>
                            <span class="text-[8px] font-bold text-[var(--text-muted)] leading-none mt-0.5">terlama</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="poppins text-base font-black text-[var(--brand)] leading-none">1h</span>
                        <span class="text-[8px] font-black text-[var(--brand)] block">20m</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Generate QR Card -->
        <div class="bg-[var(--brand)] p-8 rounded-[32px] shadow-lg relative overflow-hidden">
            <!-- Decorative Icon Overlay -->
            <div class="absolute -right-4 -top-4 opacity-10">
                <svg width="120" height="120" viewBox="0 0 24 24" fill="white">
                    <path d="M3 3h7v7H3V3zm11 0h7v7h-7V3zm-11 11h7v7H3v-7zm11 0h7v7h-7v-7zM6 6v1h1V6H6zm11 0v1h1V6h-1zM6 17v1h1v-1H6zm11 0v1h1v-1h-1z"/>
                </svg>
            </div>
            
            <div class="flex items-center gap-3 mb-4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                    <path d="M12 3l1.91 5.84H20l-4.91 3.57 1.87 5.76L12 14.6l-4.96 3.57 1.87-5.76L4 8.84h6.09L12 3z"/>
                </svg>
                <h3 class="poppins text-sm font-black text-white">Generate QR Masal</h3>
            </div>
            <p class="text-[10px] font-medium text-white/70 leading-relaxed mb-8">
                Cetak semua QR sekaligus untuk outlet ini.
            </p>
            <button class="w-full py-4 bg-white rounded-2xl text-[10px] font-black text-[var(--brand)] uppercase tracking-widest shadow-xl hover:bg-gray-50 transition-all">
                Mulai Bulk Generation
            </button>
        </div>
    </div>
</div>

<?php if ($tables === []): ?>
    <p class="text-center text-gray-500 py-12">Belum ada meja. Tambahkan meja pertama di atas.</p>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
(function () {
    const apiTable = <?= json_encode(\App\Support\PublicUrl::basePath() . '/api/staff/table.php', JSON_THROW_ON_ERROR) ?>;

    const form = document.getElementById('formAddTable');
    const input = document.getElementById('inputTableNumber');
    const msg = document.getElementById('addTableMsg');

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const num = input.value.trim();
        if (!num) return;
        msg.classList.remove('hidden'); msg.textContent = 'Menyimpan…'; msg.classList.remove('text-red-600');
        try {
            const res = await fetch(apiTable, {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin',
                body: JSON.stringify({ action: 'create', table_number: num }),
            });
            const data = await res.json();
            if (!data.ok) {
                msg.classList.add('text-red-600'); msg.textContent = data.error || 'Gagal menyimpan';
                return;
            }
            window.location.reload();
        } catch (err) {
            msg.classList.add('text-red-600'); msg.textContent = 'Jaringan error';
        }
    });

    // Edit, toggle, delete handlers
    document.querySelectorAll('.btn-edit-table').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.getAttribute('data-id');
            const table = btn.getAttribute('data-table');
            const newVal = prompt('Ubah nomor meja', table || '');
            if (!newVal) return;
            fetch(apiTable, {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin',
                body: JSON.stringify({ action: 'update', id: parseInt(id, 10), table_number: newVal }),
            }).then(r => r.json()).then(d => { if (d.ok) window.location.reload(); else alert(d.error || 'Gagal'); }).catch(() => alert('Jaringan error'));
        });
    });

    document.querySelectorAll('.btn-toggle-table').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = parseInt(btn.getAttribute('data-id'), 10);
            const cur = btn.getAttribute('data-active') === '1' ? 1 : 0;
            const next = cur === 1 ? 0 : 1;
            fetch(apiTable, {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin',
                body: JSON.stringify({ action: 'toggle', id: id, is_active: next }),
            }).then(r => r.json()).then(d => { if (d.ok) window.location.reload(); else alert(d.error || 'Gagal'); }).catch(() => alert('Jaringan error'));
        });
    });

    document.querySelectorAll('.btn-delete-table').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Nonaktifkan meja ini?')) return;
            const id = parseInt(btn.getAttribute('data-id'), 10);
            fetch(apiTable, {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin',
                body: JSON.stringify({ action: 'delete', id: id }),
            }).then(r => r.json()).then(d => { if (d.ok) window.location.reload(); else alert(d.error || 'Gagal'); }).catch(() => alert('Jaringan error'));
        });
    });

    // QR preview / print
    document.querySelectorAll('.btn-show-qr').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url = btn.getAttribute('data-url');
            const token = btn.getAttribute('data-token');
            const table = btn.getAttribute('data-table');
            const w = window.open('', '_blank');
            if (!w) return;
            w.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Label QR - ${token}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { background: white; padding: 40px; border-radius: 12px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: 800; color: #1f2937; margin-bottom: 5px; letter-spacing: 1px; }
        .subtitle { font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .qr-container { text-align: center; margin: 30px 0; }
        .qr-container canvas, .qr-container img { max-width: 100%; height: auto; display: inline-block; }
        .info { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .info-label { font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 8px; }
        .info-value { font-size: 14px; color: #1f2937; font-weight: 600; word-break: break-all; font-family: monospace; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #d1d5db; }
        @media print {
            body { background: white; }
            .footer { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">${table}</div>
            <div class="subtitle">Table QR Code</div>
        </div>
        <div class="qr-container" id="qr"></div>
        <div class="info">
            <div class="info-label">Token / ID</div>
            <div class="info-value">${token}</div>
        </div>
        <div class="info">
            <div class="info-label">Scan URL</div>
            <div class="info-value">${url}</div>
        </div>
        <div class="footer">
            Scanteen • Table Management System
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"><\/script>
    <script>
        QRCode.toCanvas(document.getElementById('qr'), '${url}', { width: 250, margin: 2 }, function(err) {
            if (err) console.log('Error:', err);
            else setTimeout(function() { window.print(); }, 500);
        });
    <\/script>
</body>
</html>`);
            w.document.close();
        });
    });

})();
</script>
