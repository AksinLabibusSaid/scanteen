<?php
declare(strict_types=1);

use App\Repositories\MenuRepository;
use App\Repositories\WarungRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$venueId   = (int) StaffAuth::venueId();
$menuRepo  = new MenuRepository();
$warungRepo = new WarungRepository();

$filterWarungId  = isset($_GET['warung_id']) && $_GET['warung_id'] !== '' ? (int) $_GET['warung_id'] : null;
$filterCategoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int) $_GET['category_id'] : null;

$allMenus    = $menuRepo->listAdminByVenue($venueId, $filterWarungId, $filterCategoryId);
$warungs     = $warungRepo->listByVenueId($venueId);
$categories  = $menuRepo->listCategories();

// Category filter is now handled client-side for smoother transition

// Count per category
$catCounts = [];
$allForCount = $menuRepo->listAdminByVenue($venueId, $filterWarungId);
foreach ($allForCount as $m) {
    $cid = (int)$m['category_id'];
    $catCounts[$cid] = ($catCounts[$cid] ?? 0) + 1;
}

$apiBase = PublicUrl::basePath();
?>

<div id="menusContentWrapper">
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Manajemen Menu &amp; Kategori</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Kelola daftar menu kantin, kategori hidangan, dan ketersediaan stok tenant.</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Warung Filter -->
        <div class="relative">
            <select id="filterWarungSelect"
                class="appearance-none bg-[var(--brand-muted)] border border-[var(--brand)] text-[var(--brand)] text-xs font-black uppercase tracking-widest pl-10 pr-10 py-3 rounded-xl shadow-sm outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all cursor-pointer">
                <option value="">Semua Warung</option>
                <?php foreach ($warungs as $w): ?>
                <option value="<?= $w['id'] ?>" <?= $filterWarungId === (int)$w['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
                <?php endforeach; ?>
            </select>
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--brand)]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[var(--brand)]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>

        <button id="btnTambahMenu" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-md hover:opacity-90 transition-all">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/></svg>
            Tambah Menu Baru
        </button>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8">
    <!-- Left Sidebar: Categories -->
    <div class="w-full lg:w-64 space-y-6 flex-shrink-0">
        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50">
            <h3 class="poppins text-base font-bold text-[var(--text-dark)] mb-6 ml-2">Kategori</h3>
            <nav class="space-y-2">
                <a href="?page=menus<?= $filterWarungId ? '&warung_id='.$filterWarungId : '' ?>"
                   data-category-id="all"
                   class="category-filter-link flex items-center justify-between px-4 py-3 rounded-2xl transition-all <?= $filterCategoryId === null ? 'bg-[var(--brand-muted)] border-l-4 border-[var(--brand)]' : 'hover:bg-gray-50' ?>">
                    <div class="flex items-center gap-3">
                        <?php if ($filterCategoryId === null): ?>
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--brand)]"></div>
                        <?php endif; ?>
                        <span class="text-xs font-black <?= $filterCategoryId === null ? 'text-[var(--brand)] uppercase tracking-wider' : 'text-[var(--text-muted)] ml-4' ?>">Semua</span>
                    </div>
                    <span class="<?= $filterCategoryId === null ? 'bg-[var(--brand-soft)] text-[var(--brand)]' : 'bg-gray-100 text-gray-400' ?> px-2 py-0.5 rounded text-[9px] font-black"><?= count($allForCount) ?></span>
                </a>
                <?php foreach ($categories as $cat): ?>
                <?php if ((int)$cat['id'] === 1) continue; ?>
                <?php $cnt = $catCounts[(int)$cat['id']] ?? 0; $isActive = $filterCategoryId === (int)$cat['id']; ?>
                <a href="?page=menus&category_id=<?= $cat['id'] ?><?= $filterWarungId ? '&warung_id='.$filterWarungId : '' ?>"
                   data-category-id="<?= $cat['id'] ?>"
                   class="category-filter-link flex items-center justify-between px-4 py-3 rounded-2xl transition-all <?= $isActive ? 'bg-[var(--brand-muted)] border-l-4 border-[var(--brand)]' : 'hover:bg-gray-50' ?>">
                    <div class="flex items-center gap-3">
                        <?php if ($isActive): ?>
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--brand)]"></div>
                        <?php endif; ?>
                        <span class="text-xs font-<?= $isActive ? 'black text-[var(--brand)] uppercase tracking-wider' : 'bold text-[var(--text-muted)] ml-4' ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="<?= $isActive ? 'bg-[var(--brand-soft)] text-[var(--brand)]' : 'bg-gray-100 text-gray-400' ?> px-2 py-0.5 rounded text-[9px] font-black"><?= $cnt ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Summary Card -->
        <div class="bg-[var(--brand)] p-6 rounded-[32px] shadow-lg text-white">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                </div>
                <h3 class="poppins text-sm font-bold mt-1">Info Menu</h3>
            </div>
            <?php
            $totalMenus    = count($allForCount);
            $availableMenus = count(array_filter($allForCount, fn($m) => (int)$m['is_available'] === 1));
            ?>
            <p class="text-[10px] font-medium leading-relaxed opacity-80 mb-2">
                Total: <strong><?= $totalMenus ?></strong> menu<br>
                Tersedia: <strong><?= $availableMenus ?></strong> menu aktif
            </p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between mb-8 px-2">
            <div class="flex items-end gap-2">
                <h2 class="poppins text-lg font-black text-[var(--text-dark)]">
                    <?php if ($filterCategoryId !== null):
                        $activeCat = array_filter($categories, fn($c) => (int)$c['id'] === $filterCategoryId);
                        echo htmlspecialchars(array_values($activeCat)[0]['name'] ?? 'Kategori', ENT_QUOTES, 'UTF-8');
                    else: ?>Semua Menu<?php endif; ?>
                </h2>
                <span class="text-xs font-bold text-gray-400 mb-0.5">(<?= count($allMenus) ?> Menu)</span>
            </div>
        </div>

        <?php if ($allMenus === []): ?>
        <div class="bg-white rounded-[24px] shadow-sm border border-gray-50 p-12 text-center text-gray-400">
            <svg class="mx-auto mb-4 opacity-30" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <p class="text-sm font-bold">Belum ada menu. Tambahkan menu baru.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($allMenus as $m):
                $isAvailable = (int)$m['is_available'] === 1;
                $imgUrl = !empty($m['image_url']) ? $m['image_url'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400';
            ?>
            <div class="menu-item bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden group hover:shadow-md transition-all" data-category-id="<?= $m['category_id'] ?>">
                <div class="h-44 overflow-hidden relative">
                    <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400'">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-[8px] font-black text-[var(--brand)] uppercase tracking-wider shadow-sm">
                            <?= htmlspecialchars($m['category_name'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <div class="absolute top-4 right-4">
                        <button class="btn-menu-toggle w-10 h-5 rounded-full relative transition-colors cursor-pointer <?= $isAvailable ? 'bg-[#16A34A]' : 'bg-gray-300' ?>"
                            data-id="<?= $m['id'] ?>" data-next="<?= $isAvailable ? 0 : 1 ?>" title="Toggle Ketersediaan">
                            <div class="absolute top-1 transition-all <?= $isAvailable ? 'right-1' : 'left-1' ?> w-3 h-3 rounded-full bg-white shadow-sm"></div>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="poppins text-base font-black text-[var(--text-dark)] leading-tight truncate"><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <div class="flex items-center gap-1.5 mt-1">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-gray-400"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                <span class="text-[10px] font-bold text-gray-400"><?= htmlspecialchars($m['warung_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <span class="text-[10px] font-black text-[var(--brand)] uppercase leading-none">Rp</span>
                            <p class="poppins text-base font-black text-[var(--text-dark)] leading-none"><?= number_format((float)$m['price'], 0, ',', '.') ?></p>
                        </div>
                    </div>

                    <?php if (!empty($m['description'])): ?>
                    <p class="text-[10px] text-gray-400 leading-relaxed mb-3 line-clamp-2"><?= htmlspecialchars($m['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>

                    <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-xl mb-4 border border-gray-100">
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Status Menu:</span>
                        <span class="text-[9px] font-black <?= $isAvailable ? 'text-[#16A34A]' : 'text-[#BA1A1A]' ?>">
                            <?= $isAvailable ? 'AKTIF' : 'NONAKTIF' ?>
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <button class="btn-menu-edit flex-1 py-2.5 bg-[#FDE8E4] text-[var(--brand)] rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-[#F5D5CE] transition-all"
                            data-id="<?= $m['id'] ?>"
                            data-name="<?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>"
                            data-warung_id="<?= $m['warung_id'] ?>"
                            data-category_id="<?= $m['category_id'] ?>"
                            data-price="<?= $m['price'] ?>"
                            data-description="<?= htmlspecialchars($m['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            data-image_url="<?= htmlspecialchars($m['image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            data-is_available="<?= (int)$m['is_available'] ?>">
                            Edit Menu
                        </button>
                        <button class="btn-menu-delete w-12 py-2.5 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-[#BA1A1A] transition-all"
                            data-id="<?= $m['id'] ?>" data-name="<?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===================== MODAL TAMBAH / EDIT MENU ===================== -->
<div id="modalMenu" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="modalMenuOverlay"></div>
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <h2 id="modalMenuTitle" class="poppins text-lg font-black text-[var(--text-dark)]">Tambah Menu Baru</h2>
            <button id="btnCloseModal" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-all">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="formMenu" class="px-8 py-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <input type="hidden" id="menuId" name="menu_id" value="">
            <div id="menuErrorAlert" class="hidden bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-xl">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p id="menuErrorText" class="text-xs font-bold text-red-700"></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Warung *</label>
                    <select id="menuWarungId" name="warung_id" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required>
                        <option value="">Pilih Warung</option>
                        <?php foreach ($warungs as $w): ?>
                        <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Kategori *</label>
                    <select id="menuCategoryId" name="category_id" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                        <?php if ((int)$cat['id'] === 1) continue; ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Nama Menu *</label>
                <input type="text" id="menuName" name="name" placeholder="Nasi Goreng Spesial" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required>
            </div>

            <div>
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">Harga (Rp) *</label>
                <input type="number" id="menuPrice" name="price" placeholder="25000" min="0" step="500" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]" required>
            </div>

            <div>
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest block mb-1">URL Gambar</label>
                <input type="url" id="menuImageUrl" name="image_url" placeholder="https://..." class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-[var(--brand-soft)]">
            </div>

            <div class="hidden">
                <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest">Tersedia</label>
                <input type="checkbox" id="menuIsAvailable" name="is_available" class="w-4 h-4 rounded accent-[var(--brand)]">
            </div>
        </form>
        <div class="px-8 py-5 border-t border-gray-100 flex gap-3">
            <button id="btnCancelModal" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
            <button id="btnSaveMenu" class="flex-1 py-3 rounded-xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest hover:opacity-90 transition-all">Simpan</button>
        </div>
    </div>
</div>

<!-- ===================== MODAL KONFIRMASI HAPUS ===================== -->
<div id="modalDelete" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="modalDeleteOverlay"></div>
    <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-8 py-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="poppins text-lg font-black text-[var(--text-dark)] mb-2">Hapus Menu?</h2>
            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus menu <span id="deleteMenuName" class="font-bold text-[var(--text-dark)]"></span>? Tindakan ini tidak dapat dibatalkan.</p>
            
            <div class="flex gap-3">
                <button id="btnCancelDelete" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
                <button id="btnConfirmDelete" class="flex-1 py-3 rounded-xl bg-red-500 text-white text-xs font-black uppercase tracking-widest hover:bg-red-600 transition-all">Hapus</button>
            </div>
        </div>
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
    const apiMenu   = <?= json_encode($apiBase . '/api/staff/menu.php') ?>;
    const apiDelete = <?= json_encode($apiBase . '/api/staff/menu-delete.php') ?>;
    const modal     = document.getElementById('modalMenu');
    const modalTitle = document.getElementById('modalMenuTitle');
    const menuIdInput = document.getElementById('menuId');
    const menuErrorAlert = document.getElementById('menuErrorAlert');
    const menuErrorText = document.getElementById('menuErrorText');

    // SPA Logic for Warung Filter
    const filterWarungSelect = document.getElementById('filterWarungSelect');
    filterWarungSelect.addEventListener('change', () => {
        const val = filterWarungSelect.value;
        const url = new URL(window.location.href);
        if (val) url.searchParams.set('warung_id', val);
        else url.searchParams.delete('warung_id');
        // If we are on a category, keep it
        
        if (typeof window.scanteenLoadPage === 'function') {
            window.scanteenLoadPage(url.search);
        } else {
            window.location.href = url.search;
        }
    });

    function openModal(isEdit = false) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modalTitle.textContent = isEdit ? 'Edit Menu' : 'Tambah Menu Baru';
        menuErrorAlert.classList.add('hidden');
    }
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('formMenu').reset();
        menuIdInput.value = '';
    }

    document.getElementById('btnTambahMenu').addEventListener('click', () => { menuIdInput.value = ''; openModal(false); });
    document.getElementById('btnCloseModal').addEventListener('click', closeModal);
    document.getElementById('btnCancelModal').addEventListener('click', closeModal);
    document.getElementById('modalMenuOverlay').addEventListener('click', closeModal);

    // Event Delegation for Edit, Delete, Toggle
    if (!document._scanteenMenusListenerAttached) {
        document.addEventListener('click', async (e) => {
            // Category Filter Link
            const filterLink = e.target.closest('.category-filter-link');
            if (filterLink) {
                e.preventDefault();
                const url = new URL(filterLink.href);
                if (typeof window.scanteenLoadPage === 'function') {
                    window.scanteenLoadPage(url.search);
                } else {
                    window.location.href = url.search;
                }
                return;
            }

            // Edit Button
            const editBtn = e.target.closest('.btn-menu-edit');
            if (editBtn) {
                document.getElementById('menuId').value = editBtn.dataset.id;
                document.getElementById('menuWarungId').value    = editBtn.dataset.warung_id;
                document.getElementById('menuCategoryId').value  = editBtn.dataset.category_id;
                document.getElementById('menuName').value        = editBtn.dataset.name;
                document.getElementById('menuPrice').value       = editBtn.dataset.price;
                document.getElementById('menuImageUrl').value    = editBtn.dataset.image_url;
                document.getElementById('menuIsAvailable').checked = editBtn.dataset.is_available === '1';
                
                // openModal logic
                const modal = document.getElementById('modalMenu');
                const modalTitle = document.getElementById('modalMenuTitle');
                const menuErrorAlert = document.getElementById('menuErrorAlert');
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modalTitle.textContent = 'Edit Menu';
                menuErrorAlert.classList.add('hidden');
                return;
            }

            // Delete Button
            const deleteBtn = e.target.closest('.btn-menu-delete');
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                const name = deleteBtn.dataset.name;
                
                const modalDelete = document.getElementById('modalDelete');
                const deleteMenuName = document.getElementById('deleteMenuName');
                
                modalDelete.dataset.targetId = id;
                deleteMenuName.textContent = name;
                modalDelete.classList.remove('hidden');
                modalDelete.classList.add('flex');
                return;
            }

            // Toggle Button
            const toggleBtn = e.target.closest('.btn-menu-toggle');
            if (toggleBtn) {
                const menuId = parseInt(toggleBtn.dataset.id, 10);
                const next = parseInt(toggleBtn.dataset.next, 10);
                
                const res  = await fetch(apiMenu, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ action: 'availability', menu_id: menuId, is_available: next })
                });
                const data = await res.json();
                if (data.ok) {
                    if (typeof window.scanteenLoadPage === 'function') {
                        window.scanteenLoadPage(window.location.search);
                    } else {
                        location.reload();
                    }
                } else {
                    alert(data.error || 'Gagal mengubah status');
                }
                return;
            }

            // Cancel Delete Button
            const cancelDeleteBtn = e.target.closest('#btnCancelDelete') || e.target.closest('#modalDeleteOverlay');
            if (cancelDeleteBtn) {
                const modalDelete = document.getElementById('modalDelete');
                modalDelete.classList.add('hidden');
                modalDelete.classList.remove('flex');
                return;
            }

            // Confirm Delete Button
            const confirmDeleteBtn = e.target.closest('#btnConfirmDelete');
            if (confirmDeleteBtn) {
                const modalDelete = document.getElementById('modalDelete');
                const deleteTargetId = modalDelete.dataset.targetId;
                if (!deleteTargetId) return;
                
                const res  = await fetch(apiDelete, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ menu_id: parseInt(deleteTargetId, 10) })
                });
                const data = await res.json();
                if (data.ok) {
                    modalDelete.classList.add('hidden');
                    modalDelete.classList.remove('flex');
                    if (typeof window.scanteenLoadPage === 'function') {
                        window.scanteenLoadPage(window.location.search);
                    } else {
                        location.reload();
                    }
                }
                return;
            }
        });
        document._scanteenMenusListenerAttached = true;
    }

    document.getElementById('btnSaveMenu').addEventListener('click', async () => {
        const id = menuIdInput.value;
        const body = {
            action:      id ? 'update' : 'create',
            warung_id:   parseInt(document.getElementById('menuWarungId').value, 10),
            category_id: parseInt(document.getElementById('menuCategoryId').value, 10),
            name:        document.getElementById('menuName').value,
            price:       parseFloat(document.getElementById('menuPrice').value),
            description: '',
            image_url:   document.getElementById('menuImageUrl').value,
            is_available: document.getElementById('menuIsAvailable').checked ? 1 : 0,
        };
        if (id) body.menu_id = parseInt(id, 10);

        const res  = await fetch(apiMenu, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(body) });
        const data = await res.json();
        if (!data.ok) {
            menuErrorText.textContent = data.error || 'Gagal menyimpan';
            menuErrorAlert.classList.remove('hidden');
            return;
        }
        if (typeof window.scanteenLoadPage === 'function') {
            closeModal();
            window.scanteenLoadPage(window.location.search);
        } else {
            location.reload();
        }
    });

    // Event listeners removed - handled by delegation

    // Event listeners removed - handled by delegation
})();
</script>
