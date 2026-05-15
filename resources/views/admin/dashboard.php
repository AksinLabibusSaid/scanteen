<?php ob_start(); ?>
<!-- Konten Dashboard Admin -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Card Stats Example -->
    <div class="card p-6">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Orders</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">1,234</p>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
include SCANTEEN_ROOT . '/resources/views/layouts/admin_master.php'; 
?>
