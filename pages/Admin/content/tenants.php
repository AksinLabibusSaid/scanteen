<?php
declare(strict_types=1);

use App\Repositories\VenueStatsRepository;
use App\Repositories\WarungRepository;
use App\Repositories\StaffUserRepository;
use App\Staff\StaffAuth;

$venueId = (int) StaffAuth::venueId();
$repo = new WarungRepository();
$statsRepo = new VenueStatsRepository();
$userRepo = new StaffUserRepository();

// Handle POST actions (for non-AJAX fallback or direct calls)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? ''));
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $error = null;

    if ($action === 'create') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $ownerName = trim((string) ($_POST['owner_name'] ?? ''));
        $ownerPhone = trim((string) ($_POST['owner_phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = trim((string) ($_POST['password'] ?? ''));

        if ($name !== '' && $email !== '' && $password !== '') {
            try {
                \App\Core\Database::transaction(function() use ($venueId, $name, $ownerName, $ownerPhone, $email, $password, $repo, $userRepo) {
                    $wid = $repo->insert($venueId, $name);
                    if ($wid > 0) {
                        $userRepo->insert($venueId, $email, $password, $ownerName ?: $name, 'warung', $wid, $ownerPhone);
                    }
                });
            } catch (\Throwable $e) {
                if (str_contains(strtolower($e->getMessage()), 'duplicate entry') && str_contains(strtolower($e->getMessage()), 'email')) {
                    $error = 'Email ini sudah terdaftar. Silakan gunakan email lain.';
                } else {
                    $error = 'Gagal menambahkan warung: ' . $e->getMessage();
                }
            }
        } else {
            $error = 'Semua field wajib harus diisi.';
        }
    }

    if ($action === 'rename') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $ownerName = trim((string) ($_POST['owner_name'] ?? ''));
        $ownerPhone = trim((string) ($_POST['owner_phone'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($id > 0 && $name !== '') {
            try {
                $repo->updateInfo($id, $venueId, $name);
                
                $user = $userRepo->findByWarungId($id);
                if ($user !== null) {
                    $userRepo->updateInfo((int)$user['id'], $ownerName ?: $name, $ownerPhone);
                    
                    if ($password !== '') {
                        $userRepo->updatePassword((int)$user['id'], $venueId, $password);
                    }
                }
            } catch (\Throwable $e) {
                $error = 'Gagal mengubah data warung: ' . $e->getMessage();
            }
        } else {
            $error = 'Data tidak valid.';
        }
    }

    if ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        $isActive = (int) ($_POST['is_active'] ?? 1);
        if ($id > 0) {
            try {
                $repo->setActive($id, $venueId, $isActive === 1 ? 1 : 0);
            } catch (\Throwable $e) {
                $error = 'Gagal mengubah status warung: ' . $e->getMessage();
            }
        } else {
            $error = 'Data tidak valid.';
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                \App\Core\Database::transaction(function() use ($id, $venueId, $repo, $userRepo) {
                    // 1. Delete associated staff users first
                    $userRepo->deleteByWarungId($id, $venueId);
                    
                    // 2. Delete menus (this will fail if they have orders, triggering the fallback)
                    $mysqli = \App\Core\Database::mysqli();
                    $stmtMenus = $mysqli->prepare("DELETE FROM menus WHERE warung_id = ?");
                    $stmtMenus->bind_param('i', $id);
                    $stmtMenus->execute();
                    $stmtMenus->close();
                    
                    // 3. Delete the warung itself
                    $repo->delete($id, $venueId);
                });
            } catch (\Throwable $e) {
                try {
                    // FALLBACK: Soft delete if there are orders/foreign key constraints
                    $repo->softDelete($id, $venueId);
                    
                    // Deactivate the associated staff users as well
                    $u = $userRepo->findByWarungId($id);
                    if ($u !== null) {
                        $userRepo->setActive((int)$u['id'], $venueId, 0);
                    }
                } catch (\Throwable $ex) {
                    $error = 'Gagal menghapus warung: ' . $ex->getMessage();
                }
            }
        } else {
            $error = 'Data tidak valid.';
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        if ($error !== null) {
            echo json_encode(['ok' => false, 'error' => $error]);
        } else {
            echo json_encode(['ok' => true]);
        }
        exit;
    }
    
    if ($error !== null) {
        $_SESSION['error'] = $error;
    }
    header('Location: ?page=tenants');
    exit;
}

$warungs = $repo->listByVenueId($venueId);
$totalWarungs = $repo->countByVenue($venueId);
$activeWarungs = $repo->countActiveByVenue($venueId);
$inactiveWarungs = max(0, $totalWarungs - $activeWarungs);
$activeRatio = $totalWarungs > 0 ? round(($activeWarungs / $totalWarungs) * 100, 1) : 0.0;

function scanteen_admin_tenant_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $initials = '';
    foreach ($parts as $part) {
        if ($part === '') continue;
        $initials .= strtoupper(substr($part, 0, 1));
        if (strlen($initials) >= 2) break;
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
        <button id="btnTambahWarung" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/></svg>
            Tambah Warung Baru
        </button>
    </div>

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
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Non-Aktif</p>
            <p class="poppins text-3xl font-black text-[#D97706]"><?= $inactiveWarungs ?></p>
        </div>
        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-50">
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-2">Rata-rata Kinerja</p>
            <p class="poppins text-3xl font-black text-[var(--brand)]"><?= $activeRatio ?>%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php foreach ($warungs as $warung): ?>
            <?php
            $id = (int) $warung['id'];
            $isActive = (int) $warung['is_active'] === 1;
            $name = (string) $warung['name'];
            $ownerName = (string) ($warung['owner_name'] ?? '-');
            $ownerPhone = (string) ($warung['owner_phone'] ?? '-');
            $rawOwnerName = (string) ($warung['owner_name'] ?? '');
            $rawOwnerPhone = (string) ($warung['owner_phone'] ?? '');
            $initials = scanteen_admin_tenant_initials($name);
            $createdAt = !empty($warung['created_at']) ? date('d M Y', strtotime((string) $warung['created_at'])) : '-';
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
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <span class="text-[10px] font-bold text-gray-400 truncate"><?= htmlspecialchars($ownerName, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1.5">
                            <button class="btn-rename-warung p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[var(--brand)] hover:bg-[#FDE8E4] transition-all" title="Edit Warung"
                                data-id="<?= $id ?>" 
                                data-name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                                data-owner="<?= htmlspecialchars($rawOwnerName, ENT_QUOTES, 'UTF-8') ?>"
                                data-phone="<?= htmlspecialchars($rawOwnerPhone, ENT_QUOTES, 'UTF-8') ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button class="btn-delete-warung p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[#BA1A1A] hover:bg-red-50 transition-all" 
                                data-id="<?= $id ?>" data-name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" title="Hapus Warung">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 flex">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full <?= scanteen_admin_tenant_status_class($isActive) ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= scanteen_admin_tenant_dot_class($isActive) ?> shadow-sm"></span>
                            <span class="text-[9px] font-black uppercase tracking-wider"><?= scanteen_admin_tenant_status_label($isActive) ?></span>
                        </div>
                    </div>
                </div>

                <div class="p-8 pt-6">
                    <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-4">Kontrol Operasional</p>
                    <div class="flex gap-3">
                        <button class="btn-toggle-warung flex-1 px-4 py-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-0.5 <?= $isActive ? 'bg-[var(--brand)] border-[var(--brand)] text-white' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200' ?>"
                            data-id="<?= $id ?>" data-active="1">
                            <span class="text-[10px] font-black uppercase tracking-widest">Buka</span>
                        </button>
                        <button class="btn-toggle-warung flex-1 px-4 py-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-0.5 <?= !$isActive ? 'bg-[#5A6472] border-[#5A6472] text-white' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200' ?>"
                            data-id="<?= $id ?>" data-active="0">
                            <span class="text-[10px] font-black uppercase tracking-widest">Tutup</span>
                        </button>
                    </div>

                    <div class="mt-8 px-6 py-3 bg-[#FAF7F6] rounded-2xl flex items-center justify-between border border-gray-50">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Terdaftar Sejak:</span>
                            <span class="text-[10px] font-black text-[var(--text-dark)] leading-none"><?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="text-right">
                             <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Telp:</span>
                             <span class="text-[10px] font-black text-[var(--text-dark)] block"><?= htmlspecialchars($ownerPhone, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($warungs === []): ?>
        <div class="bg-white rounded-[24px] shadow-sm border border-gray-50 p-10 text-center text-gray-400 text-sm">Belum ada warung. Tambahkan warung baru untuk memulai.</div>
    <?php endif; ?>
</div>

<!-- ===================== MODAL TAMBAH WARUNG ===================== -->
<div id="modalWarung" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="modalWarungOverlay"></div>
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="poppins text-lg font-black text-[var(--text-dark)]">Tambah Warung Baru</h2>
            <button id="btnCloseWarung" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-all">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="formTambahWarung" class="px-8 py-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <input type="hidden" name="action" value="create">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Nama Warung *</label>
                    <input type="text" name="name" placeholder="Warung Barokah" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Nama Pemilik</label>
                    <input type="text" name="owner_name" placeholder="Budi Santoso" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Nomor Telepon Pemilik</label>
                <input type="text" name="owner_phone" placeholder="081234567890" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
            </div>

            <div class="p-4 bg-[#FAF7F6] rounded-2xl border border-gray-50 space-y-4">
                <p class="text-[9px] font-black text-[var(--brand)] uppercase tracking-widest">Akun Login Tenant</p>
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Email *</label>
                    <input type="email" name="email" placeholder="owner@warung.com" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Password *</label>
                    <input type="password" name="password" placeholder="********" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all" required>
                </div>
            </div>
        </form>
        <div class="px-8 py-5 border-t border-gray-100 flex gap-3">
            <button id="btnCancelWarung" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
            <button id="btnSubmitTambah" class="flex-1 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest hover:opacity-90 transition-all">Simpan Warung</button>
        </div>
    </div>
</div>

<!-- ===================== MODAL EDIT WARUNG ===================== -->
<div id="modalRename" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="modalRenameOverlay"></div>
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="poppins text-lg font-black text-[var(--text-dark)]">Edit Data Warung</h2>
            <button id="btnCloseRename" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-all">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="formRenameWarung" class="px-8 py-6 space-y-4">
            <input type="hidden" name="action" value="rename">
            <input type="hidden" name="id" id="renameWarungId" value="">
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Nama Warung *</label>
                <input type="text" name="name" id="renameWarungName" placeholder="Nama warung baru" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all" required>
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Nama Pemilik</label>
                <input type="text" name="owner_name" id="renameOwnerName" placeholder="Nama pemilik" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Nomor Telepon</label>
                <input type="text" name="owner_phone" id="renameOwnerPhone" placeholder="081xxx" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block ml-1">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="renamePassword" placeholder="********" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
            </div>
        </form>
        <div class="px-8 py-5 border-t border-gray-100 flex gap-3">
            <button id="btnCancelRename" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
            <button id="btnSubmitRename" class="flex-1 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest hover:opacity-90 transition-all">Update Data</button>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #FDE8E4; border-radius: 10px; }
</style>

<script>
(function () {
    const modalW = document.getElementById('modalWarung');
    function openW()  { modalW.classList.remove('hidden'); modalW.classList.add('flex'); }
    function closeW() { modalW.classList.add('hidden'); modalW.classList.remove('flex'); }
    document.getElementById('btnTambahWarung').addEventListener('click', openW);
    document.getElementById('btnCloseWarung').addEventListener('click', closeW);
    document.getElementById('btnCancelWarung').addEventListener('click', closeW);
    document.getElementById('modalWarungOverlay').addEventListener('click', closeW);

    const modalR = document.getElementById('modalRename');
    function closeR() { modalR.classList.add('hidden'); modalR.classList.remove('flex'); }
    document.getElementById('btnCloseRename').addEventListener('click', closeR);
    document.getElementById('btnCancelRename').addEventListener('click', closeR);
    document.getElementById('modalRenameOverlay').addEventListener('click', closeR);

    document.querySelectorAll('.btn-rename-warung').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('renameWarungId').value   = btn.dataset.id;
            document.getElementById('renameWarungName').value = btn.dataset.name;
            document.getElementById('renameOwnerName').value = btn.dataset.owner;
            document.getElementById('renameOwnerPhone').value = btn.dataset.phone;
            const pwdInput = document.getElementById('renamePassword');
            if (pwdInput) pwdInput.value = '';
            modalR.classList.remove('hidden');
            modalR.classList.add('flex');
        });
    });

    // SPA POST logic
    async function postAction(formData) {
        try {
            const res = await fetch(window.location.href, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json().catch(() => ({ ok: true }));
            if (!data.ok) {
                alert(data.error || 'Terjadi kesalahan saat memproses data.');
                return;
            }
            if (typeof window.scanteenLoadPage === 'function') {
                window.scanteenLoadPage(window.location.search || '?page=tenants');
            } else {
                window.location.reload();
            }
        } catch (err) {
            console.error('Action failed:', err);
            alert('Terjadi kesalahan saat memproses data.');
        }
    }

    document.querySelectorAll('.btn-toggle-warung').forEach(btn => {
        btn.addEventListener('click', () => {
            const fd = new FormData();
            fd.append('action', 'toggle');
            fd.append('id', btn.dataset.id);
            fd.append('is_active', btn.dataset.active);
            postAction(fd);
        });
    });

    document.querySelectorAll('.btn-delete-warung').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!confirm('Hapus warung "' + btn.dataset.name + '" secara permanen?')) return;
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', btn.dataset.id);
            postAction(fd);
        });
    });

    document.getElementById('btnSubmitTambah').addEventListener('click', () => {
        const form = document.getElementById('formTambahWarung');
        if (!form.checkValidity()) { form.reportValidity(); return; }
        const fd = new FormData(form);
        closeW();
        postAction(fd);
    });

    document.getElementById('btnSubmitRename').addEventListener('click', () => {
        const form = document.getElementById('formRenameWarung');
        if (!form.checkValidity()) { form.reportValidity(); return; }
        const fd = new FormData(form);
        closeR();
        postAction(fd);
    });
})();
</script>