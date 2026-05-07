<?php

/**
 * Tenant Page
 * Halaman untuk tenant/pemilik restoran mengelola toko mereka
 */

require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../header.php';
?>

<main class="container mx-auto p-4">
    <h1 class="text-4xl font-bold mb-8">Tenant - Manajemen Toko</h1>

    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-gray-600 text-sm">Penjualan Hari Ini</h2>
            <p class="text-3xl font-bold text-blue-600">Rp 0</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-gray-600 text-sm">Transaksi Hari Ini</h2>
            <p class="text-3xl font-bold text-green-600">0</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-gray-600 text-sm">Rating</h2>
            <p class="text-3xl font-bold text-yellow-600">★ 0</p>
        </div>
    </div>

    <!-- Tenant Menu -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">Kelola Toko Anda</h2>
        <div class="grid grid-cols-3 gap-4">
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <p class="text-2xl mb-2">📦</p>
                <p class="font-bold">Produk</p>
                <p class="text-gray-600 text-sm">Kelola menu produk</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <p class="text-2xl mb-2">📊</p>
                <p class="font-bold">Laporan</p>
                <p class="text-gray-600 text-sm">Lihat laporan penjualan</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <p class="text-2xl mb-2">⚙️</p>
                <p class="font-bold">Pengaturan</p>
                <p class="text-gray-600 text-sm">Konfigurasi toko</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <p class="text-2xl mb-2">👥</p>
                <p class="font-bold">Karyawan</p>
                <p class="text-gray-600 text-sm">Kelola karyawan</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <p class="text-2xl mb-2">⭐</p>
                <p class="font-bold">Review</p>
                <p class="text-gray-600 text-sm">Lihat ulasan pelanggan</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <p class="text-2xl mb-2">💰</p>
                <p class="font-bold">Keuangan</p>
                <p class="text-gray-600 text-sm">Manajemen keuangan</p>
            </a>
        </div>
    </div>
</main>

<?php
require_once '../footer.php';
?>