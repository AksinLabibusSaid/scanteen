<?php
declare(strict_types=1);

use App\Repositories\StaffUserRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;
use App\Support\PublicUrl;

$venueId    = (int) StaffAuth::venueId();
$staffRepo  = new StaffUserRepository();
$warungRepo = new WarungRepository();

$staffList  = $staffRepo->listByVenue($venueId);
$warungs    = $warungRepo->listByVenueId($venueId);

$totalStaff   = count($staffList);
$countWarung  = $staffRepo->countByRole($venueId, 'warung');
$countAdmin   = $staffRepo->countByRole($venueId, 'admin');
$countKasir   = $staffRepo->countByRole($venueId, 'kasir');

// Build warung name map
$warungMap = [];
foreach ($warungs as $w) {
    $warungMap[(int)$w['id']] = $w['name'];
}

$apiBase = PublicUrl::basePath();

function scanteen_users_role_label(string $role): string {
    return match($role) {
        'admin'   => 'Admin',
        'kasir'   => 'Kasir',
        'warung'  => 'Pemilik Warung',
        default   => ucfirst($role),
    };
}
function scanteen_users_role_class(string $role): string {
    return match($role) {
        'admin'  => 'bg-purple-50 text-purple-600',
        'kasir'  => 'bg-gray-100 text-gray-500',
        'warung' => 'bg-[#FDE8E4] text-[var(--brand)]',
        default  => 'bg-gray-100 text-gray-500',
    };
}
function scanteen_users_initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $i = '';
    foreach ($parts as $p) {
        if ($p !== '') { $i .= strtoupper(substr($p, 0, 1)); }
        if (strlen($i) >= 2) break;
    }
    return $i !== '' ? substr($i, 0, 2) : '?';
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Pengguna</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Kelola akun kasir, pemilik warung, dan hak akses sistem.</p>
    </div>
    <button id="btnTambahStaff" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        Tambah Pengguna Baru
    </button>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <?php foreach ([
        ['Total Pengguna', $totalStaff, 'text-[var(--brand)]', '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['Admin',          $countAdmin,  'text-purple-600',    '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
        ['Pemilik Warung', $countWarung, 'text-[var(--brand)]', '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
        ['Kasir',          $countKasir,  'text-gray-600',      '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
    ] as [$label, $count, $textClass, $svgPath]): ?>
    <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-[#FDE8E4] flex items-center justify-center <?= $textClass ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><?= $svgPath ?></svg>
        </div>
        <div>
            <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[2px] mb-1"><?= $label ?></p>
            <p class="poppins text-2xl font-black text-[var(--text-dark)]"><?= $count ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Users Table -->
<div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#FDE8E4]/50">
                <tr>
                    <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Nama Pengguna</th>
                    <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Peran</th>
                    <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Tenant / Unit</th>
                    <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Email</th>
                    <th class="px-8 py-5 text-left text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Status</th>
                    <th class="px-8 py-5 text-center text-[9px] font-extrabold text-[var(--text-muted)] uppercase tracking-[2px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if ($staffList === []): ?>
                <tr>
                    <td colspan="6" class="px-8 py-10 text-center text-sm text-gray-400">Belum ada pengguna terdaftar.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($staffList as $u):
                    $uid      = (int) $u['id'];
                    $role     = (string) $u['role'];
                    $isActive = (int) $u['is_active'] === 1;
                    $warungName = isset($u['warung_id']) && $u['warung_id'] !== null ? ($warungMap[(int)$u['warung_id']] ?? '-') : '-';
                    $initials = scanteen_users_initials((string) $u['name']);
                    $isSelf   = StaffAuth::userId() === $uid;
                ?>
                <tr class="hover:bg-[#FAF7F6] transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-[#F5D5CE] flex items-center justify-center text-[11px] font-black text-[var(--brand)] flex-shrink-0">
                                <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div>
                                <p class="text-sm font-black text-[var(--text-dark)] leading-tight"><?= htmlspecialchars((string)$u['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-[10px] font-bold text-gray-400 mt-0.5">ID: <?= str_pad((string)$uid, 4, '0', STR_PAD_LEFT) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider <?= scanteen_users_role_class($role) ?>">
                            <?= scanteen_users_role_label($role) ?>
                        </span>
                    </td>
                    <td class="px-8 py-5 text-sm font-bold text-[var(--text-dark)] opacity-70"><?= htmlspecialchars($warungName, ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-8 py-5 text-xs font-bold text-[var(--text-dark)]"><?= htmlspecialchars((string)$u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full <?= $isActive ? 'bg-[#16A34A]' : 'bg-gray-400' ?>"></span>
                            <span class="text-xs font-bold <?= $isActive ? 'text-[#16A34A]' : 'text-gray-400' ?>"><?= $isActive ? 'Aktif' : 'Nonaktif' ?></span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Reset Password -->
                            <button class="btn-staff-pw p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-[var(--brand)] hover:bg-[#FDE8E4] transition-all"
                                title="Reset Password" data-id="<?= $uid ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/>
                                    <path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/>
                                </svg>
                            </button>
                            <!-- Toggle Active -->
                            <?php if (!$isSelf): ?>
                            <button class="btn-staff-toggle p-2 rounded-lg bg-gray-50 transition-all <?= $isActive ? 'text-gray-400 hover:text-[#D97706] hover:bg-amber-50' : 'text-[#16A34A] hover:bg-green-50' ?>"
                                title="<?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                data-id="<?= $uid ?>" data-next="<?= $isActive ? 0 : 1 ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <?php if ($isActive): ?>
                                    <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                    <?php else: ?>
                                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                    <?php endif; ?>
                                </svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===================== MODAL TAMBAH STAFF ===================== -->
<div id="modalStaff" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="modalStaffOverlay"></div>
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="poppins text-lg font-black text-[var(--text-dark)]">Tambah Pengguna Baru</h2>
            <button id="btnCloseStaff" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-all">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="formStaffAdd" class="px-8 py-6 space-y-4">
            <p id="staffMsg" class="hidden text-xs font-bold text-red-600"></p>

            <div>
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Nama Lengkap *</label>
                <input type="text" name="name" placeholder="Andi Ardiansyah" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required>
            </div>
            <div>
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Email *</label>
                <input type="email" name="email" placeholder="email@contoh.com" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required>
            </div>
            <div>
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Password *</label>
                <input type="password" name="password" placeholder="Min. 6 karakter" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required minlength="6">
            </div>
            <div>
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Peran *</label>
                <select id="staffRole" name="role" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required>
                    <option value="">Pilih Peran</option>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    <option value="warung">Pemilik Warung</option>
                </select>
            </div>
            <div id="wrapWarung" class="hidden">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Warung *</label>
                <select name="warung_id" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]">
                    <option value="">Pilih Warung</option>
                    <?php foreach ($warungs as $w): ?>
                    <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        <div class="px-8 py-5 border-t border-gray-100 flex gap-3">
            <button id="btnCancelStaff" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
            <button id="btnSaveStaff" class="flex-1 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest hover:opacity-90 transition-all">Simpan</button>
        </div>
    </div>
</div>

<script>
(function () {
    const api     = <?= json_encode($apiBase . '/api/staff/staff.php', JSON_THROW_ON_ERROR) ?>;
    const modal   = document.getElementById('modalStaff');
    const staffMsg = document.getElementById('staffMsg');

    const roleSel   = document.getElementById('staffRole');
    const wrapWarung = document.getElementById('wrapWarung');
    function syncWarung() {
        wrapWarung.classList.toggle('hidden', roleSel.value !== 'warung');
    }
    roleSel.addEventListener('change', syncWarung);

    function openModal() { modal.classList.remove('hidden'); modal.classList.add('flex'); staffMsg.classList.add('hidden'); }
    function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); document.getElementById('formStaffAdd').reset(); syncWarung(); }

    document.getElementById('btnTambahStaff').addEventListener('click', openModal);
    document.getElementById('btnCloseStaff').addEventListener('click', closeModal);
    document.getElementById('btnCancelStaff').addEventListener('click', closeModal);
    document.getElementById('modalStaffOverlay').addEventListener('click', closeModal);

    // Save new staff
    document.getElementById('btnSaveStaff').addEventListener('click', async () => {
        const fd = new FormData(document.getElementById('formStaffAdd'));
        const body = {
            action:   'create',
            name:     fd.get('name'),
            email:    fd.get('email'),
            password: fd.get('password'),
            role:     fd.get('role'),
        };
        if (body.role === 'warung') {
            const wid = fd.get('warung_id');
            body.warung_id = wid ? parseInt(wid, 10) : null;
        }
        const res  = await fetch(api, { method: 'POST', headers: {'Content-Type': 'application/json'}, credentials: 'same-origin', body: JSON.stringify(body) });
        const data = await res.json();
        if (!data.ok) { staffMsg.textContent = data.error || 'Gagal menyimpan'; staffMsg.classList.remove('hidden'); return; }
        location.reload();
    });

    // Toggle active
    document.querySelectorAll('.btn-staff-toggle').forEach(btn => {
        btn.addEventListener('click', async () => {
            const action = parseInt(btn.dataset.next, 10) === 1 ? 'Aktifkan' : 'Nonaktifkan';
            if (!confirm(action + ' pengguna ini?')) return;
            const res  = await fetch(api, { method: 'POST', headers: {'Content-Type': 'application/json'}, credentials: 'same-origin', body: JSON.stringify({ action: 'toggle', id: parseInt(btn.dataset.id, 10), is_active: parseInt(btn.dataset.next, 10) }) });
            const data = await res.json();
            if (!data.ok) { alert(data.error || 'Gagal'); return; }
            location.reload();
        });
    });

    // Reset password
    document.querySelectorAll('.btn-staff-pw').forEach(btn => {
        btn.addEventListener('click', async () => {
            const pw = prompt('Masukkan sandi baru (min. 6 karakter):');
            if (!pw || pw.length < 6) return;
            const res  = await fetch(api, { method: 'POST', headers: {'Content-Type': 'application/json'}, credentials: 'same-origin', body: JSON.stringify({ action: 'password', id: parseInt(btn.dataset.id, 10), password: pw }) });
            const data = await res.json();
            alert(data.ok ? '✅ Sandi berhasil diperbarui' : (data.error || 'Gagal'));
        });
    });
})();
</script>
