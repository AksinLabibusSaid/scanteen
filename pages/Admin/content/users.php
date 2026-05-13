<?php // User Management - Admin ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">User Management</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola akun pengguna sistem — Admin, Kasir, dan Warung.</p>
    </div>
    <button id="btn-add-user"
            class="flex items-center gap-2 bg-[#991B1B] hover:bg-[#7f1d1d] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M6 8H0V6H6V0H8V6H14V8H8V14H6V8Z" fill="white"/></svg>
        Tambah Pengguna
    </button>
</div>

<!-- Role Tabs -->
<div class="flex gap-2 mb-6">
    <?php foreach (['Semua','Admin','Kasir','Warung'] as $tab): ?>
    <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors
        <?= $tab === 'Semua' ? 'bg-[#991B1B] text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' ?>">
        <?= $tab ?>
    </button>
    <?php endforeach; ?>
</div>

<!-- Users Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php
                $users = [
                    ['Budi Santoso',    'budi@scanteen.id',   'Admin',  'Aktif', '01 Jan 2024'],
                    ['Siti Rahayu',     'siti@scanteen.id',   'Kasir',  'Aktif', '15 Feb 2024'],
                    ['Andi Pratama',    'andi@scanteen.id',   'Kasir',  'Aktif', '10 Mar 2024'],
                    ['Pak Budi Warung', 'pakbudi@scanteen.id','Warung', 'Aktif', '05 Jan 2024'],
                    ['Dewi Lestari',    'dewi@scanteen.id',   'Warung', 'Aktif', '20 Mar 2024'],
                    ['Fajar Nugroho',   'fajar@scanteen.id',  'Kasir',  'Nonaktif','01 Apr 2024'],
                ];
                $roleColor = [
                    'Admin'  => 'bg-purple-100 text-purple-700',
                    'Kasir'  => 'bg-blue-100 text-blue-700',
                    'Warung' => 'bg-orange-100 text-orange-700',
                ];
                foreach ($users as $u): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-[#991B1B] font-bold text-xs flex-shrink-0">
                                <?= strtoupper(substr($u[0], 0, 2)) ?>
                            </div>
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($u[0]) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500"><?= $u[1] ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $roleColor[$u[2]] ?? '' ?>"><?= $u[2] ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $u[3] === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>"><?= $u[3] ?></span>
                    </td>
                    <td class="px-6 py-4 text-gray-400"><?= $u[4] ?></td>
                    <td class="px-6 py-4 flex gap-3">
                        <button class="text-xs font-semibold text-[#991B1B] hover:underline">Edit</button>
                        <button class="text-xs font-semibold text-gray-400 hover:text-gray-600">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
