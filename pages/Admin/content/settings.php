<?php // System Settings - Admin ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">System Settings</h2>
    <p class="text-sm text-gray-500 mt-1">Konfigurasi umum sistem Scanteen.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- Left Column: Settings Panels -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Informasi Sistem -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Informasi Sistem</h3>
                <p class="text-xs text-gray-400 mt-0.5">Identitas dan branding aplikasi.</p>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nama Aplikasi</label>
                    <input type="text" value="Scanteen"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-red-200 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nama Kantin</label>
                    <input type="text" value="Kantin Utama Gedung A"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-red-200 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Alamat</label>
                    <textarea rows="2"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-red-200 transition resize-none">Jl. Raya Pendidikan No. 1, Jakarta Selatan</textarea>
                </div>
            </div>
        </div>

        <!-- Pengaturan Operasional -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Operasional</h3>
                <p class="text-xs text-gray-400 mt-0.5">Jam buka, pajak, dan metode pembayaran.</p>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Jam Buka</label>
                        <input type="time" value="07:00"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-red-200 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Jam Tutup</label>
                        <input type="time" value="17:00"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-red-200 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Pajak (%)</label>
                    <input type="number" value="11" min="0" max="100"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-red-200 transition">
                </div>

                <!-- Toggles -->
                <div class="space-y-3">
                    <?php
                    $toggles = [
                        ['Pembayaran Tunai',  true],
                        ['Pembayaran QRIS',   true],
                        ['Mode Maintenance',  false],
                    ];
                    foreach ($toggles as $tg): ?>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <span class="text-sm font-semibold text-gray-700"><?= $tg[0] ?></span>
                        <button class="relative inline-flex h-6 w-11 rounded-full transition-colors focus:outline-none
                            <?= $tg[1] ? 'bg-[#991B1B]' : 'bg-gray-200' ?>">
                            <span class="inline-block h-5 w-5 rounded-full bg-white shadow-sm transform transition-transform mt-0.5
                                <?= $tg[1] ? 'translate-x-5' : 'translate-x-0.5' ?>"></span>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button class="bg-[#991B1B] hover:bg-[#7f1d1d] text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Right Column: System Info -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Info Versi</h3>
            <div class="space-y-3 text-sm">
                <?php
                $info = [
                    ['Versi Aplikasi', 'v1.0.0'],
                    ['PHP',            phpversion()],
                    ['Server',         $_SERVER['SERVER_SOFTWARE'] ?? 'Laragon'],
                    ['Terakhir Update','13 Mei 2026'],
                ];
                foreach ($info as $inf): ?>
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-gray-400 font-medium"><?= $inf[0] ?></span>
                    <span class="font-semibold text-gray-700"><?= htmlspecialchars((string)$inf[1]) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="space-y-2">
                <button class="w-full text-left text-sm font-semibold text-gray-600 hover:text-[#991B1B] px-4 py-3 rounded-xl hover:bg-red-50 transition-colors flex items-center gap-3">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M7 11H9V7H7V11ZM8 5C8.28333 5 8.52083 4.90417 8.7125 4.7125C8.90417 4.52083 9 4.28333 9 4C9 3.71667 8.90417 3.47917 8.7125 3.2875C8.52083 3.09583 8.28333 3 8 3C7.71667 3 7.47917 3.09583 7.2875 3.2875C7.09583 3.47917 7 3.71667 7 4C7 4.28333 7.09583 4.52083 7.2875 4.7125C7.47917 4.90417 7.71667 5 8 5ZM8 16C6.90 16 5.87083 15.7917 4.9125 15.375C3.95417 14.9583 3.12083 14.3875 2.4125 13.6625C1.70417 12.9375 1.14583 12.0958 0.7375 11.1375C0.329167 10.1792 0.125 9.15 0.125 8.05C0.125 6.95 0.329167 5.92083 0.7375 4.9625C1.14583 4.00417 1.70417 3.17083 2.4125 2.4625C3.12083 1.75417 3.95417 1.20417 4.9125 0.8125C5.87083 0.420833 6.9 0.225 8 0.225C9.1 0.225 10.1292 0.420833 11.0875 0.8125C12.0458 1.20417 12.8792 1.75417 13.5875 2.4625C14.2958 3.17083 14.8542 4.00417 15.2625 4.9625C15.6708 5.92083 15.875 6.95 15.875 8.05C15.875 9.15 15.6708 10.1792 15.2625 11.1375C14.8542 12.0958 14.2958 12.9375 13.5875 13.6625C12.8792 14.3875 12.0458 14.9583 11.0875 15.375C10.1292 15.7917 9.1 16 8 16Z" fill="currentColor"/></svg>
                    Bersihkan Cache
                </button>
                <button class="w-full text-left text-sm font-semibold text-gray-600 hover:text-[#991B1B] px-4 py-3 rounded-xl hover:bg-red-50 transition-colors flex items-center gap-3">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 16C5.79 16 3.90417 15.2208 2.3375 13.6625C0.770833 12.1042 -0.00833333 10.2167 2.22045e-05 8.05C0.00836776 5.88333 0.7875 4.00417 2.3375 2.4125C3.8875 0.820833 5.77083 0.025 8 0.025C9.11667 0.025 10.1667 0.254167 11.15 0.7125C12.1333 1.17083 12.9833 1.8 13.7 2.6V0.025H15.7V6.525H9.2V4.525H13.125C12.5917 3.69167 11.8958 3.03333 11.0375 2.55C10.1792 2.06667 9.13333 1.825 8 1.825C6.31667 1.825 4.88333 2.41667 3.7 3.6C2.51667 4.78333 1.925 6.2 1.925 7.85C1.925 9.5 2.51667 10.9208 3.7 12.1125C4.88333 13.3042 6.31667 13.9 8 13.9C9.36667 13.9 10.5458 13.5042 11.5375 12.7125C12.5292 11.9208 13.1917 10.9167 13.525 9.7H15.6C15.25 11.5 14.3792 12.9625 12.9875 14.0875C11.5958 15.2125 9.93333 15.975 8 16Z" fill="currentColor"/></svg>
                    Reset Data Demo
                </button>
                <button class="w-full text-left text-sm font-semibold text-red-500 hover:text-red-700 px-4 py-3 rounded-xl hover:bg-red-50 transition-colors flex items-center gap-3">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none"><path d="M3 18C2.45 18 1.97917 17.8042 1.5875 17.4125C1.19583 17.0208 1 16.55 1 16V3H0V1H5V0H11V1H16V3H15V16C15 16.55 14.8042 17.0208 14.4125 17.4125C14.0208 17.8042 13.55 18 13 18H3ZM13 3H3V16H13V3ZM5 14H7V5H5V14ZM9 14H11V5H9V14Z" fill="currentColor"/></svg>
                    Hapus Semua Log
                </button>
            </div>
        </div>
    </div>

</div>
