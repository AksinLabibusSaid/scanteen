<?php // Order Management - Admin ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Order Management</h2>
        <p class="text-sm text-gray-500 mt-1">Monitor dan kelola semua pesanan yang masuk.</p>
    </div>
    <div class="flex gap-3">
        <select class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-600 outline-none focus:ring-2 focus:ring-red-200">
            <option>Semua Status</option>
            <option>Menunggu</option>
            <option>Diproses</option>
            <option>Selesai</option>
            <option>Dibatalkan</option>
        </select>
        <input type="date" class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-600 outline-none focus:ring-2 focus:ring-red-200">
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tenant</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Meja</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php
                $orders = [
                    ['#ORD-2841','Andi Pratama',  'Warung Pak Budi',    'Meja 5', 'Rp 45.000','Selesai',   '12:34'],
                    ['#ORD-2840','Siti Rahayu',   'Nasi Goreng Spesial','Meja 2', 'Rp 32.000','Diproses',  '12:31'],
                    ['#ORD-2839','Budi Santoso',  'Mie Ayam Barokah',   'Meja 8', 'Rp 27.500','Menunggu',  '12:29'],
                    ['#ORD-2838','Dewi Lestari',  'Warung Pak Budi',    'Meja 1', 'Rp 58.000','Selesai',   '12:20'],
                    ['#ORD-2837','Fajar Nugroho', 'Soto Betawi Asli',   'Meja 4', 'Rp 35.000','Dibatalkan','12:15'],
                    ['#ORD-2836','Rina Wati',     'Mie Ayam Barokah',   'Meja 3', 'Rp 22.000','Selesai',   '12:10'],
                ];
                $statusColor = [
                    'Selesai'    => 'bg-green-100 text-green-700',
                    'Diproses'   => 'bg-yellow-100 text-yellow-700',
                    'Menunggu'   => 'bg-blue-100 text-blue-700',
                    'Dibatalkan' => 'bg-red-100 text-red-600',
                ];
                foreach ($orders as $o): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-800"><?= $o[0] ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= $o[1] ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= $o[2] ?></td>
                    <td class="px-6 py-4 text-gray-500"><?= $o[3] ?></td>
                    <td class="px-6 py-4 font-semibold text-gray-800"><?= $o[4] ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $statusColor[$o[5]] ?? '' ?>"><?= $o[5] ?></span>
                    </td>
                    <td class="px-6 py-4 text-gray-400"><?= $o[6] ?></td>
                    <td class="px-6 py-4">
                        <button class="text-xs font-semibold text-[#991B1B] hover:underline">Detail</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
