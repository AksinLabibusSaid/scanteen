<?php
declare(strict_types=1);

use App\Repositories\MenuRepository;
use App\Staff\StaffAuth;
use App\Support\Money;
use App\Support\PublicUrl;

$warungId = StaffAuth::warungId();
$venueId = (int) StaffAuth::venueId();
$menus = [];
$api = PublicUrl::basePath() . '/api/staff/menu.php';

if ($warungId !== null) {
    $menuRepo = new MenuRepository();
    $menus = $menuRepo->listAdminByVenue($venueId, $warungId);
}

// Calculate Stats
$totalItems = count($menus);
$activeItems = count(array_filter($menus, fn($m) => (int)$m['is_available'] === 1));
$outOfStock = count(array_filter($menus, fn($m) => (int)$m['stock_quantity'] === 0));

// Extract Categories for Tabs
$categories = [];
foreach ($menus as $m) {
    $catId = (int)$m['category_id'];
    if (!isset($categories[$catId])) {
        $categories[$catId] = $m['category_name'];
    }
}
asort($categories);

$currentTab = $_GET['tab'] ?? 'all';
$search = trim((string)($_GET['q'] ?? ''));

// Filter Menus
$filteredMenus = array_filter($menus, function($m) use ($currentTab, $search) {
    $matchTab = ($currentTab === 'all' || (int)$m['category_id'] === (int)$currentTab);
    $matchSearch = ($search === '' || stripos($m['name'], $search) !== false);
    return $matchTab && $matchSearch;
});

?>

<div class="flex flex-col gap-10 pb-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-4xl font-bold text-[#261817] tracking-tight">Manajemen Menu</h1>
            <p class="text-gray-500 text-lg mt-2 font-medium">Kelola ketersediaan menu stan Anda secara real-time.</p>
        </div>
    </div>

    <?php if ($warungId === null): ?>
        <div class="bg-red-50 p-8 rounded-[2.5rem] border border-red-100 text-center">
            <p class="text-red-600 font-bold">Akun ini tidak terhubung ke stan manapun. Silakan hubungi Administrator.</p>
        </div>
    <?php else: ?>
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100/30 border-l-[6px] border-[#7B0009] flex items-center gap-6 group">
                <div class="w-14 h-14 bg-[#FDE8E4] rounded-2xl flex items-center justify-center text-[#7B0009] group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Total Item</p>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-full">Menu Terdaftar</span>
                    </div>
                    <h3 class="text-3xl font-black text-[#261817]"><?= $totalItems ?></h3>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100/30 border-l-[6px] border-[#00C853] flex items-center gap-6 group">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Menu Aktif</p>
                        <span class="px-2 py-0.5 bg-[#FDE8E4] text-[#7B0009] text-[9px] font-black rounded-full">Siap Jual</span>
                    </div>
                    <h3 class="text-3xl font-black text-[#261817]"><?= $activeItems ?></h3>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-100/30 border-l-[6px] border-[#FF1744] flex items-center gap-6 group">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Stok Kosong</p>
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black rounded-full">Segera Update</span>
                    </div>
                    <h3 class="text-3xl font-black text-[#261817]"><?= $outOfStock ?></h3>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100/50 border border-gray-50 overflow-hidden">
            <div class="flex flex-col lg:flex-row items-center justify-between p-6 gap-6 border-b border-gray-50">
                <!-- Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto w-full lg:w-auto no-scrollbar pb-2 lg:pb-0">
                    <a href="?page=menu&tab=all" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all <?= $currentTab === 'all' ? 'bg-[#7B0009] text-white shadow-lg shadow-red-900/20' : 'text-gray-400 hover:bg-gray-50' ?>">Semua</a>
                    <?php foreach ($categories as $id => $name): ?>
                        <a href="?page=menu&tab=<?= $id ?>" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap <?= (int)$currentTab === $id ? 'bg-[#7B0009] text-white shadow-lg shadow-red-900/20' : 'text-gray-400 hover:bg-gray-50' ?>"><?= htmlspecialchars($name) ?></a>
                    <?php endforeach; ?>
                </div>

                <!-- Search -->
                <div class="relative w-full lg:w-80">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <form method="get" class="w-full">
                        <input type="hidden" name="page" value="menu">
                        <input type="hidden" name="tab" value="<?= $currentTab ?>">
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari menu..." 
                               class="w-full pl-12 pr-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold outline-none focus:bg-white focus:ring-2 focus:ring-[#7B0009]/10 transition-all">
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-[11px] font-black text-gray-300 uppercase tracking-[0.2em] border-b border-gray-50">
                            <th class="px-10 py-6">Item</th>
                            <th class="px-6 py-6">Kategori</th>
                            <th class="px-6 py-6 text-right">Harga</th>
                            <th class="px-10 py-6">Stok Harian</th>
                            <th class="px-6 py-6">Status</th>
                            <th class="px-10 py-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($filteredMenus)): ?>
                            <tr>
                                <td colspan="6" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                                        </div>
                                        <p class="text-gray-400 font-bold">Tidak ada menu ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($filteredMenus as $m): ?>
                            <tr class="hover:bg-[#FAF9F9] transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 rounded-2xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100 shadow-sm">
                                            <img src="<?= htmlspecialchars($m['image_url'] ?: PublicUrl::asset('images/placeholder-menu.png')) ?>" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        </div>
                                        <div>
                                            <h4 class="text-base font-black text-[#261817] leading-tight mb-1"><?= htmlspecialchars((string) $m['name'], ENT_QUOTES, 'UTF-8') ?></h4>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">SKU: <?= sprintf('%s-%03d', strtoupper(substr((string)$m['category_name'], 0, 3)), (int)$m['id']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="px-4 py-1.5 bg-[#FDE8E4] text-[#7B0009] text-[10px] font-black rounded-full uppercase tracking-widest">
                                        <?= htmlspecialchars((string) $m['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <span class="text-sm font-black text-[#261817]"><?= htmlspecialchars(Money::formatIdr((float) $m['price']), ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col gap-1.5">
                                        <?php 
                                        $stock = (int)$m['stock_quantity'];
                                        $isLow = $stock > 0 && $stock <= 5;
                                        ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-black <?= $stock === 0 ? 'text-red-500' : ($isLow ? 'text-orange-500' : 'text-[#261817]') ?>">
                                                <?= $stock ?>
                                            </span>
                                            <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">
                                                <?= $stock === 0 ? 'Habis' : ($isLow ? '<span class="text-orange-500">Menipis</span>' : 'Tersedia') ?>
                                            </span>
                                        </div>
                                        <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full <?= $stock === 0 ? 'bg-red-500' : ($isLow ? 'bg-orange-400' : 'bg-emerald-500') ?> rounded-full transition-all duration-700" 
                                                 style="width: <?= min(100, ($stock / 50) * 100) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-3">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer btn-toggle-switch" 
                                                   data-id="<?= (int) $m['id'] ?>" 
                                                   data-stock="<?= $stock ?>"
                                                   <?= (int) $m['is_available'] === 1 ? 'checked' : '' ?>>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7B0009]"></div>
                                        </label>
                                        <span class="text-[10px] font-black uppercase tracking-widest <?= (int) $m['is_available'] === 1 ? 'text-emerald-600' : 'text-red-500' ?>">
                                            <?= (int) $m['is_available'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button class="p-3 text-[#7B0009] bg-[#FDE8E4] rounded-2xl hover:scale-105 hover:shadow-md transition-all btn-edit-stock" 
                                            data-id="<?= (int)$m['id'] ?>" 
                                            data-name="<?= htmlspecialchars($m['name']) ?>" 
                                            data-stock="<?= $stock ?>">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M12 5v14M5 12h14"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-8 border-t border-gray-50 flex items-center justify-between">
                <p class="text-xs font-bold text-gray-400">Menampilkan <?= count($filteredMenus) ?> dari <?= $totalItems ?> item menu</p>
                <div class="flex items-center gap-2">
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-[#FDE8E4] hover:text-[#7B0009] transition-all">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#7B0009] text-white shadow-lg shadow-red-900/20 font-black text-xs">1</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-[#FDE8E4] hover:text-[#7B0009] transition-all">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Update Stock -->
        <div id="modal-stock" class="fixed inset-0 bg-[#261817]/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-6">
            <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modal-content">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-[#FAF9F9]">
                    <div>
                        <h3 class="text-xl font-black text-[#261817]">Update Stok</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1" id="modal-menu-name">Nama Menu</p>
                    </div>
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-100 text-gray-400 transition-all" onclick="closeStockModal()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                
                <div class="p-8 space-y-6">
                    <div class="flex items-center justify-between p-6 bg-[#FAF9F9] rounded-3xl border border-gray-100">
                        <div class="text-center flex-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Stok Sekarang</p>
                            <p class="text-2xl font-black text-[#261817]" id="modal-current-stock">0</p>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center flex-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
                            <p class="text-sm font-black" id="modal-status">Tersedia</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-xs font-black text-[#261817] uppercase tracking-widest ml-1">Input Stok Baru</label>
                        <div class="flex items-center gap-4">
                            <button class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-[#7B0009] hover:bg-[#FDE8E4] transition-all" onclick="adjustInput(-1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                            <input type="number" id="input-new-stock" 
                                   class="flex-1 h-14 bg-[#FAF9F9] border-none rounded-2xl text-center text-xl font-black text-[#7B0009] outline-none focus:ring-2 focus:ring-[#7B0009]/20" 
                                   value="0" min="0">
                            <button class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-[#7B0009] hover:bg-[#FDE8E4] transition-all" onclick="adjustInput(1)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-[#FAF9F9] flex gap-4">
                    <button class="flex-1 py-4 text-gray-400 text-xs font-black uppercase tracking-widest hover:text-[#261817] transition-all" onclick="closeStockModal()">Batal</button>
                    <button class="flex-[2] py-4 bg-[#7B0009] text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-red-900/20 hover:opacity-90 transition-all" onclick="saveStock()">Simpan Perubahan</button>
                </div>
            </div>
        </div>

        <script>
        let currentMenuId = null;

        function openStockModal(id, name, stock) {
            currentMenuId = id;
            document.getElementById('modal-menu-name').innerText = name;
            document.getElementById('modal-current-stock').innerText = stock;
            document.getElementById('input-new-stock').value = stock;
            
            const statusEl = document.getElementById('modal-status');
            if (stock === 0) {
                statusEl.innerText = 'HABIS';
                statusEl.className = 'text-sm font-black text-red-500';
            } else if (stock <= 5) {
                statusEl.innerText = 'MENIPIS';
                statusEl.className = 'text-sm font-black text-orange-500';
            } else {
                statusEl.innerText = 'TERSEDIA';
                statusEl.className = 'text-sm font-black text-emerald-500';
            }

            const modal = document.getElementById('modal-stock');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeStockModal() {
            const modal = document.getElementById('modal-stock');
            const content = document.getElementById('modal-content');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function adjustInput(val) {
            const input = document.getElementById('input-new-stock');
            let next = parseInt(input.value) + val;
            if (next < 0) next = 0;
            input.value = next;
        }

        async function saveStock() {
            const api = <?= json_encode($api, JSON_THROW_ON_ERROR) ?>;
            const newStock = parseInt(document.getElementById('input-new-stock').value);
            
            const btn = event.currentTarget;
            btn.disabled = true;
            btn.innerHTML = 'Menyimpan...';

            try {
                const res = await fetch(api, { 
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/json' }, 
                    credentials: 'same-origin', 
                    body: JSON.stringify({ action: 'stock', menu_id: currentMenuId, stock: newStock }) 
                });
                const data = await res.json();
                if (data.ok) {
                    location.reload();
                } else {
                    alert(data.error || 'Gagal mengupdate stok');
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Simpan Perubahan';
            }
        }

        (function () {
          const api = <?= json_encode($api, JSON_THROW_ON_ERROR) ?>;

          // Toggle Availability
          document.querySelectorAll('.btn-toggle-switch').forEach(switchInput => switchInput.addEventListener('change', async function() {
            const menu_id = parseInt(this.getAttribute('data-id'), 10);
            const is_available = this.checked ? 1 : 0;
            
            this.disabled = true;
            this.parentElement.style.opacity = '0.5';

            try {
                const res = await fetch(api, { 
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/json' }, 
                    credentials: 'same-origin', 
                    body: JSON.stringify({ action: 'availability', menu_id, is_available }) 
                });
                const data = await res.json();
                if (!data.ok) { 
                    alert(data.error || 'Gagal mengubah status'); 
                    this.checked = !this.checked;
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan');
                this.checked = !this.checked;
            } finally {
                this.disabled = false;
                this.parentElement.style.opacity = '1';
                const labelSpan = this.parentElement.nextElementSibling;
                if (labelSpan) {
                    labelSpan.innerText = is_available ? 'Aktif' : 'Nonaktif';
                    labelSpan.className = `text-[10px] font-black uppercase tracking-widest ${is_available ? 'text-emerald-600' : 'text-red-500'}`;
                }
            }
          }));

          // Open Modal Edit Stock
          document.querySelectorAll('.btn-edit-stock').forEach(btn => btn.addEventListener('click', function() {
              const id = this.getAttribute('data-id');
              const name = this.getAttribute('data-name');
              const stock = parseInt(this.getAttribute('data-stock'));
              openStockModal(id, name, stock);
          }));
        })();
        </script>
    <?php endif; ?>
</div>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
</style>
