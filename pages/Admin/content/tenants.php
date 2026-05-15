<?php
declare(strict_types=1);

use App\Repositories\VenueStatsRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;

$venueId = (int) StaffAuth::venueId();
$repo = new WarungRepository();
$statsRepo = new VenueStatsRepository();

function scanteen_admin_redirect(string $url): void
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }

    echo '<script>window.location.href=' . json_encode($url, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';</script>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? ''));
    $redirect = '?page=tenants';

    if ($action === 'create') {
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name !== '') {
            $repo->insert($venueId, $name);
        }
        scanteen_admin_redirect($redirect . '&flash=created');
    }

    if ($action === 'rename') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($id > 0 && $name !== '') {
            $repo->updateName($id, $venueId, $name);
        }
        scanteen_admin_redirect($redirect . '&flash=updated');
    }

    if ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        $isActive = (int) ($_POST['is_active'] ?? 1);
        if ($id > 0) {
            $repo->setActive($id, $venueId, $isActive === 1 ? 1 : 0);
        }
        scanteen_admin_redirect($redirect . '&flash=toggled');
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $repo->softDelete($id, $venueId);
        }
        scanteen_admin_redirect($redirect . '&flash=deleted');
    }
}

$warungs = $repo->listByVenueId($venueId);
$totalWarungs = $repo->countByVenue($venueId);
$activeWarungs = $repo->countActiveByVenue($venueId);
$inactiveWarungs = max(0, $totalWarungs - $activeWarungs);
$activeRatio = $totalWarungs > 0 ? round(($activeWarungs / $totalWarungs) * 100, 1) : 0.0;
$flash = (string) ($_GET['flash'] ?? '');

function scanteen_admin_tenant_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $initials = '';

    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }

        $initials .= strtoupper(substr($part, 0, 1));
        if (strlen($initials) >= 2) {
            break;
        }
    }

    return $initials !== '' ? substr($initials, 0, 2) : 'W';
}

function scanteen_admin_tenant_status_label(bool $isActive): string
{
    return $isActive ? 'OPEN NOW' : 'CLOSED TEMPORARILY';
}

function scanteen_admin_tenant_status_class(bool $isActive): string
{
    return $isActive ? 'bg-[#F0FDF4] text-[#16A34A]' : 'bg-[#FFFBEB] text-[#D97706]';
}

function scanteen_admin_tenant_dot_class(bool $isActive): string
{
    return $isActive ? 'bg-[#16A34A]' : 'bg-[#D97706]';
}
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Warung</h1>
            <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Kelola operasional, pantau kinerja, dan atur ketersediaan tenant kantin.</p>
        </div>
        <button form="tenant-create-form" type="submit" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/>
            </svg>
            Tambah Warung Baru
        </button>
    </div>

    <?php if ($flash === 'created'): ?>
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-3 rounded-xl text-sm font-medium">Warung berhasil ditambahkan.</div>
    <?php elseif ($flash === 'updated'): ?>
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-3 rounded-xl text-sm font-medium">Nama warung berhasil diperbarui.</div>
    <?php elseif ($flash === 'toggled'): ?>
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-3 rounded-xl text-sm font-medium">Status warung berhasil diubah.</div>
    <?php elseif ($flash === 'deleted'): ?>
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-3 rounded-xl text-sm font-medium">Warung berhasil dinonaktifkan.</div>
    <?php endif; ?>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Total Tenant</p>
            <p class="poppins text-3xl font-black text-[var(--brand)]"><?= $totalWarungs ?></p>
        </div>
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Tenant Aktif</p>
            <p class="poppins text-3xl font-black text-[#16A34A]"><?= $activeWarungs ?></p>
        </div>
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Dalam Pemeliharaan</p>
            <p class="poppins text-3xl font-black text-[#D97706]"><?= $inactiveWarungs ?></p>
        </div>
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Rata-rata Kinerja</p>
            <p class="poppins text-3xl font-black text-[var(--brand)]"><?= $activeRatio ?>%</p>
        </div>
    </div>

    <form id="tenant-create-form" method="post" class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50 flex flex-col sm:flex-row gap-4 items-end">
        <input type="hidden" name="action" value="create">
        <div class="flex-1 w-full">
            <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] ml-1 mb-2 block">Nama Warung Baru</label>
            <input type="text" name="name" placeholder="Contoh: Warung Barokah" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
        </div>
        <button class="px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">Tambah Warung</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php foreach ($warungs as $warung): ?>
            <?php
            $id = (int) $warung['id'];
            $isActive = (int) $warung['is_active'] === 1;
            $wd = $statsRepo->warungDashboard($venueId, $id);
            $name = (string) $warung['name'];
            $slug = (string) $warung['slug'];
            $initials = scanteen_admin_tenant_initials($name);
            $createdAt = !empty($warung['created_at']) ? date('d M Y, H:i', strtotime((string) $warung['created_at'])) : '-';
            $statusLabel = scanteen_admin_tenant_status_label($isActive);
            $statusClass = scanteen_admin_tenant_status_class($isActive);
            $dotClass = scanteen_admin_tenant_dot_class($isActive);
            ?>
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden group">
                <div class="p-8 pb-6 border-b border-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-14 h-14 rounded-full bg-[#FDE8E4] flex items-center justify-center text-lg font-black text-[var(--brand)] flex-shrink-0">
                                <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="min-w-0">
                                <h3 class="poppins text-lg font-black text-[var(--text-dark)] leading-tight truncate"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h3>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span class="text-[10px] font-bold text-gray-400 truncate"><?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1.5">
                            <form method="post">
                                <input type="hidden" name="action" value="rename">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                                <button class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[var(--brand)] hover:bg-[#FDE8E4] transition-all" title="Ubah Nama">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                            </form>
                            <form method="post" onsubmit="return confirm('Nonaktifkan warung ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[#BA1A1A] hover:bg-red-50 transition-all" title="Nonaktifkan">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-6 flex">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full <?= $statusClass ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $dotClass ?> shadow-sm"></span>
                            <span class="text-[9px] font-black uppercase tracking-wider"><?= $statusLabel ?></span>
                        </div>
                    </div>
                </div>

                <div class="p-8 pt-6">
                    <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-4">Kendalikan Operasional</p>
                    <div class="flex gap-3">
                        <form method="post" class="flex-1">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="is_active" value="1">
                            <button class="w-full px-4 py-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-0.5 <?= $isActive ? 'bg-[var(--brand)] border-[var(--brand)] text-white' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200' ?>">
                                <span class="text-[10px] font-black uppercase tracking-widest">Buka</span>
                                <span class="text-[9px] font-bold opacity-80">Pesanan</span>
                            </button>
                        </form>
                        <form method="post" class="flex-1">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="is_active" value="0">
                            <button class="w-full px-4 py-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-0.5 <?= !$isActive ? 'bg-[#5A6472] border-[#5A6472] text-white' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200' ?>">
                                <span class="text-[10px] font-black uppercase tracking-widest">Tutup</span>
                                <span class="text-[9px] font-bold opacity-80">Pesanan</span>
                            </button>
                        </form>
                        <form method="post" class="w-14" onsubmit="return confirm('Nonaktifkan warung ini?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button class="w-full h-full rounded-2xl border-2 border-gray-100 bg-white flex items-center justify-center text-gray-400 hover:border-[#D97706] hover:text-[#D97706] transition-all" title="Maintenance / Nonaktifkan">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div class="mt-8 px-6 py-3 bg-[#FAF7F6] rounded-2xl flex items-center justify-between border border-gray-50">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Peak Hours:</span>
                            <span class="text-[10px] font-black text-[var(--text-dark)] leading-none"><?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex items-end gap-0.5 h-6 opacity-40">
                            <div class="w-0.5 bg-[var(--brand)] h-2"></div>
                            <div class="w-0.5 bg-[var(--brand)] h-4"></div>
                            <div class="w-0.5 bg-[var(--brand)] h-6"></div>
                            <div class="w-0.5 bg-[var(--brand)] h-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($warungs === []): ?>
        <div class="bg-white rounded-[24px] shadow-sm border border-gray-50 p-10 text-center text-gray-400 text-sm">Belum ada warung. Tambahkan warung baru untuk memulai.</div>
    <?php endif; ?>

    <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
        <div class="px-10 py-8 flex items-center justify-between">
            <h3 class="poppins text-lg font-bold text-[var(--brand)]">Riwayat Perubahan Status</h3>
            <a href="#" class="text-[11px] font-black text-[var(--brand)] uppercase tracking-widest hover:opacity-70 flex items-center gap-2">
                Lihat Semua Log
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#FAF7F6]">
                    <tr>
                        <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Waktu</th>
                        <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Warung</th>
                        <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Aksi</th>
                        <th class="px-10 py-5 text-left text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Oleh</th>
                        <th class="px-10 py-5 text-center text-[10px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach (array_slice($warungs, 0, 8) as $row): ?>
                        <?php
                        $rowIsActive = (int) $row['is_active'] === 1;
                        $rowTime = !empty($row['created_at']) ? date('d M Y, H:i', strtotime((string) $row['created_at'])) : 'Today';
                        ?>
                        <tr class="hover:bg-[#FAF7F6] transition-colors">
                            <td class="px-10 py-6 text-sm font-bold text-gray-400"><?= htmlspecialchars($rowTime, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6 text-sm font-black text-[var(--text-dark)]"><?= htmlspecialchars((string) $row['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-10 py-6 text-sm font-bold text-gray-500"><?= $rowIsActive ? 'Membuka Pesanan' : 'Mode Pemeliharaan' ?></td>
                            <td class="px-10 py-6 text-sm font-bold text-gray-500">Admin System</td>
                            <td class="px-10 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider <?= $rowIsActive ? 'bg-[#F0FDF4] text-[#16A34A]' : 'bg-[#FFFBEB] text-[#D97706]' ?>"><?= $rowIsActive ? 'Success' : 'Scheduled' ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($warungs === []): ?>
                        <tr>
                            <td colspan="5" class="px-10 py-10 text-center text-sm text-gray-400">Belum ada data perubahan status.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>