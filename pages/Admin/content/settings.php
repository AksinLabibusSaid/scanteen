<?php
declare(strict_types=1);

use App\Repositories\VenueRepository;
use App\Staff\StaffAuth;
use App\Support\PublicUrl;

$venueId = (int) StaffAuth::venueId();
$venueRepo = new VenueRepository();
$venue = $venueRepo->findById($venueId);

$oh = json_decode($venue['operating_hours'] ?? '{}', true);
// Default values if empty
if (empty($oh)) {
    $oh = [
        'mon_fri' => ['open' => '08:00', 'close' => '17:00', 'active' => true],
        'sat'     => ['open' => '09:00', 'close' => '15:00', 'active' => true],
        'sun'     => ['open' => '00:00', 'close' => '00:00', 'active' => false],
        'close_on_holidays' => true
    ];
}

$apiSettings = PublicUrl::basePath() . '/api/staff/settings.php';
?>

<!-- Page Header & Global Actions -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="poppins text-3xl font-bold tracking-tight text-[var(--brand)]">Pengaturan Sistem</h1>
        <p class="text-sm text-[var(--text-muted)] font-medium mt-1">Konfigurasi ekosistem kantin digital, jam operasional, dan gerbang pembayaran.</p>
    </div>
    <div class="flex items-center gap-3">
        <button id="btnSaveSettings" class="px-8 py-3 rounded-xl bg-[var(--brand)] text-white text-[11px] font-black uppercase tracking-widest shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan Semua Perubahan
        </button>
    </div>
</div>

<form id="formSettings" class="space-y-8 pb-20">
    <!-- Row 1: Payment Gateway & Expiry -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Gateway Config -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-lg bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <h3 class="poppins text-base font-black text-[var(--text-dark)]">Payment Gateway Config (Midtrans)</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Environment Mode</label>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="text-[10px] font-bold text-gray-400">Sandbox</span>
                        <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer transition-colors toggle-switch" data-target="isProduction">
                            <input type="checkbox" name="is_production" id="isProduction" class="hidden" <?= (int)($venue['is_production'] ?? 0) === 1 ? 'checked' : '' ?>>
                            <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm transition-all knob"></div>
                        </div>
                        <span class="text-[10px] font-black text-[var(--brand)]">Production</span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Midtrans Client Key</label>
                    <input type="text" name="midtrans_client_key" value="<?= htmlspecialchars((string)($venue['midtrans_client_key'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full px-5 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--brand)] outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Midtrans Server Key</label>
                    <input type="password" name="midtrans_server_key" value="<?= htmlspecialchars((string)($venue['midtrans_server_key'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full px-5 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--brand)] outline-none tracking-widest">
                </div>
            </div>
        </div>

        <!-- Payment Expiry -->
        <div class="bg-[var(--brand)] p-8 rounded-[32px] shadow-lg text-white">
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <h3 class="poppins text-sm font-black uppercase tracking-wider">Payment Expiry</h3>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <p class="text-[9px] font-black text-white/50 uppercase tracking-widest mb-4">Payment Deadline (Minutes)</p>
                    <div class="flex items-center gap-4">
                        <input type="number" name="payment_expiry_minutes" value="<?= (int)($venue['payment_expiry_minutes'] ?? 15) ?>" class="w-24 px-4 py-3 bg-white/10 border-none rounded-xl text-lg font-black text-white text-center outline-none">
                        <span class="text-[10px] font-bold text-white/50">MENIT</span>
                    </div>
                </div>
                
                <div class="p-6 bg-white/5 rounded-2xl border border-white/5 mt-10">
                    <p class="text-[10px] font-medium italic text-white/60 leading-relaxed text-center">
                        Pesanan akan otomatis dibatalkan jika tidak dibayar dalam waktu tersebut.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Operating Hours & Payment Methods -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Operating Hours -->
        <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <h3 class="poppins text-base font-black text-[var(--text-dark)]">Operating Hours</h3>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-bold text-gray-400">Close on Holidays</span>
                    <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer transition-colors toggle-switch" data-target="closeOnHolidays">
                        <input type="checkbox" name="close_on_holidays" id="closeOnHolidays" class="hidden" <?= ($oh['close_on_holidays'] ?? false) ? 'checked' : '' ?>>
                        <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm transition-all knob"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Mon - Fri -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-[var(--text-dark)]">Mon - Fri</span>
                    <div class="flex items-center gap-3">
                        <input type="time" name="oh_mon_fri_open" value="<?= $oh['mon_fri']['open'] ?>" class="px-3 py-1.5 bg-[#FDE8E4] border-none rounded-lg text-xs font-bold text-[var(--brand)] outline-none">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">to</span>
                        <input type="time" name="oh_mon_fri_close" value="<?= $oh['mon_fri']['close'] ?>" class="px-3 py-1.5 bg-[#FDE8E4] border-none rounded-lg text-xs font-bold text-[var(--brand)] outline-none">
                    </div>
                </div>
                <!-- Saturday -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-[var(--text-dark)]">Saturday</span>
                    <div class="flex items-center gap-3">
                        <input type="time" name="oh_sat_open" value="<?= $oh['sat']['open'] ?>" class="px-3 py-1.5 bg-[#FDE8E4] border-none rounded-lg text-xs font-bold text-[var(--brand)] outline-none">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">to</span>
                        <input type="time" name="oh_sat_close" value="<?= $oh['sat']['close'] ?>" class="px-3 py-1.5 bg-[#FDE8E4] border-none rounded-lg text-xs font-bold text-[var(--brand)] outline-none">
                    </div>
                </div>
                <!-- Sunday -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-gray-300">Sunday</span>
                    <div class="flex items-center gap-4">
                        <span id="sunStatus" class="text-[9px] font-black <?= $oh['sun']['active'] ? 'text-[#16A34A]' : 'text-gray-300' ?> uppercase tracking-widest"><?= $oh['sun']['active'] ? 'OPEN' : 'CLOSED' ?></span>
                        <button type="button" id="btnToggleSun" class="text-[9px] font-black text-[var(--brand)] underline uppercase tracking-widest"><?= $oh['sun']['active'] ? 'Disable' : 'Enable' ?></button>
                        <input type="hidden" name="oh_sun_active" id="ohSunActive" value="<?= $oh['sun']['active'] ? '1' : '0' ?>">
                        <div id="sunTimes" class="<?= $oh['sun']['active'] ? '' : 'hidden' ?> flex items-center gap-3">
                            <input type="time" name="oh_sun_open" value="<?= $oh['sun']['open'] ?>" class="px-3 py-1.5 bg-[#FDE8E4] border-none rounded-lg text-xs font-bold text-[var(--brand)] outline-none">
                            <input type="time" name="oh_sun_close" value="<?= $oh['sun']['close'] ?>" class="px-3 py-1.5 bg-[#FDE8E4] border-none rounded-lg text-xs font-bold text-[var(--brand)] outline-none">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-xl bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                </div>
                <h3 class="poppins text-base font-black text-[var(--text-dark)]">Payment Methods</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 bg-[#FAF7F6] rounded-2xl">
                    <span class="text-xs font-black text-[var(--text-dark)]">QRIS</span>
                    <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer transition-colors toggle-switch" data-target="allowQris">
                        <input type="checkbox" name="allow_qris" id="allowQris" class="hidden" <?= ($venue['allow_qris'] ?? 1) ? 'checked' : '' ?>>
                        <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm transition-all knob"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-[#FAF7F6] rounded-2xl">
                    <span class="text-xs font-black text-[var(--text-dark)]">Cash</span>
                    <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer transition-colors toggle-switch" data-target="allowCash">
                        <input type="checkbox" name="allow_cash" id="allowCash" class="hidden" <?= ($venue['allow_cash'] ?? 1) ? 'checked' : '' ?>>
                        <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm transition-all knob"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-[#FAF7F6] rounded-2xl">
                    <span class="text-xs font-black text-[var(--text-dark)]">Debit Card</span>
                    <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer transition-colors toggle-switch" data-target="allowDebit">
                        <input type="checkbox" name="allow_debit" id="allowDebit" class="hidden" <?= ($venue['allow_debit'] ?? 0) ? 'checked' : '' ?>>
                        <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm transition-all knob"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Mode -->
    <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2.5">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
                <h3 class="poppins text-sm font-black text-[var(--text-dark)] uppercase">Maintenance Mode</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[9px] font-black text-[var(--brand)] uppercase tracking-widest">Enable Mode</span>
                <div class="w-10 h-5 rounded-full bg-gray-200 relative cursor-pointer transition-colors toggle-switch" data-target="maintenanceMode">
                    <input type="checkbox" name="maintenance_mode" id="maintenanceMode" class="hidden" <?= (int)($venue['maintenance_mode'] ?? 0) === 1 ? 'checked' : '' ?>>
                    <div class="absolute top-1 left-1 w-3 h-3 rounded-full bg-white shadow-sm transition-all knob"></div>
                </div>
            </div>
        </div>
        <div class="space-y-3">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Custom Message for Customers</p>
            <textarea name="maintenance_message" rows="3" placeholder="Mohon maaf, SmartCanteen sedang dalam pemeliharaan sistem rutin." class="w-full px-5 py-4 bg-[#FAF7F6] border-none rounded-2xl text-[11px] font-medium text-[var(--text-muted)] outline-none resize-none leading-relaxed"><?= htmlspecialchars((string)($venue['maintenance_message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
    </div>
</form>

<div id="saveStatus" class="fixed bottom-8 right-8 hidden z-50">
    <div class="bg-[var(--brand)] px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-bounce">
        <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-white">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <span class="text-xs font-black text-white uppercase tracking-widest">Pengaturan Tersimpan!</span>
    </div>
</div>

<script>
(function() {
    const api = <?= json_encode($apiSettings) ?>;
    const form = document.getElementById('formSettings');
    const btnSave = document.getElementById('btnSaveSettings');
    const saveStatus = document.getElementById('saveStatus');

    // Sunday toggle logic
    const btnToggleSun = document.getElementById('btnToggleSun');
    const sunStatus = document.getElementById('sunStatus');
    const sunTimes = document.getElementById('sunTimes');
    const ohSunActive = document.getElementById('ohSunActive');

    btnToggleSun.addEventListener('click', () => {
        const isActive = ohSunActive.value === '1';
        if (isActive) {
            ohSunActive.value = '0';
            sunStatus.textContent = 'CLOSED';
            sunStatus.className = 'text-[9px] font-black text-gray-300 uppercase tracking-widest';
            btnToggleSun.textContent = 'Enable';
            sunTimes.classList.add('hidden');
        } else {
            ohSunActive.value = '1';
            sunStatus.textContent = 'OPEN';
            sunStatus.className = 'text-[9px] font-black text-[#16A34A] uppercase tracking-widest';
            btnToggleSun.textContent = 'Disable';
            sunTimes.classList.remove('hidden');
        }
    });

    // Custom Toggles logic
    document.querySelectorAll('.toggle-switch').forEach(container => {
        const checkboxId = container.getAttribute('data-target');
        const checkbox = document.getElementById(checkboxId);
        const knob = container.querySelector('.knob');

        function updateUI() {
            if (checkbox.checked) {
                container.classList.remove('bg-gray-200');
                container.classList.add('bg-[var(--brand)]');
                knob.style.left = 'calc(100% - 16px)';
            } else {
                container.classList.add('bg-gray-200');
                container.classList.remove('bg-[var(--brand)]');
                knob.style.left = '4px';
            }
        }

        container.addEventListener('click', () => {
            checkbox.checked = !checkbox.checked;
            updateUI();
        });

        updateUI();
    });

    btnSave.addEventListener('click', async () => {
        const formData = new FormData(form);
        const body = {
            action: 'update',
            is_production: document.getElementById('isProduction').checked,
            midtrans_client_key: formData.get('midtrans_client_key'),
            midtrans_server_key: formData.get('midtrans_server_key'),
            payment_expiry_minutes: parseInt(formData.get('payment_expiry_minutes')),
            maintenance_mode: document.getElementById('maintenanceMode').checked,
            maintenance_message: formData.get('maintenance_message'),
            allow_qris: document.getElementById('allowQris').checked,
            allow_cash: document.getElementById('allowCash').checked,
            allow_debit: document.getElementById('allowDebit').checked,
            operating_hours: {
                close_on_holidays: document.getElementById('closeOnHolidays').checked,
                mon_fri: { open: formData.get('oh_mon_fri_open'), close: formData.get('oh_mon_fri_close'), active: true },
                sat: { open: formData.get('oh_sat_open'), close: formData.get('oh_sat_close'), active: true },
                sun: { open: formData.get('oh_sun_open'), close: formData.get('oh_sun_close'), active: document.getElementById('ohSunActive').value === '1' }
            }
        };

        btnSave.disabled = true;
        btnSave.textContent = 'Menyimpan...';

        try {
            const res = await fetch(api, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });
            const data = await res.json();
            if (data.ok) {
                saveStatus.classList.remove('hidden');
                setTimeout(() => saveStatus.classList.add('hidden'), 3000);
            } else {
                alert(data.error || 'Gagal menyimpan pengaturan');
            }
        } catch (err) {
            alert('Terjadi kesalahan jaringan');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Simpan Semua Perubahan';
        }
    });
})();
</script>
