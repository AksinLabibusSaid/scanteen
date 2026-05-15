<?php
declare(strict_types=1);

use App\Staff\StaffAuth;

$user = [
    'name' => StaffAuth::userName(),
    'email' => StaffAuth::userEmail(),
    'role' => (string) StaffAuth::role(),
    'avatar' => $_SESSION['foto'] ?? 'https://api.builder.io/api/v1/image/assets/TEMP/fadf3b369dc031a0c33b9f7d9de993750210b555?width=72',
];

$roleLabel = match($user['role']) {
    'admin' => 'Administrator',
    'kasir' => 'Kasir',
    'warung' => 'Pemilik Warung',
    default => $user['role']
};
?>

<div class="max-w-4xl mx-auto py-8">
    <div class="mb-8">
        <h1 class="poppins text-3xl font-bold text-[var(--brand)]">Profil Saya</h1>
        <p class="text-sm text-[var(--text-muted)] mt-1 font-medium">Kelola informasi akun dan preferensi Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Profile -->
        <div class="md:col-span-1">
            <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 text-center">
                <div class="relative w-32 h-32 mx-auto mb-6">
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="w-full h-full rounded-full object-cover border-4 border-[var(--brand-soft)] shadow-sm">
                    <button class="absolute bottom-0 right-0 w-10 h-10 bg-[var(--brand)] text-white rounded-full flex items-center justify-center border-4 border-white shadow-md hover:scale-110 transition-transform">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                        </svg>
                    </button>
                </div>
                <h3 class="poppins text-xl font-bold text-[var(--text-dark)] leading-tight"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-xs font-black text-[var(--brand)] uppercase tracking-widest mt-2 px-3 py-1 bg-[var(--brand-muted)] inline-block rounded-full">
                    <?= htmlspecialchars($roleLabel) ?>
                </p>
                
                <div class="mt-8 pt-8 border-t border-gray-100 flex flex-col gap-3">
                    <button class="w-full py-3 rounded-2xl bg-[#FAF7F6] text-xs font-bold text-[var(--text-dark)] hover:bg-[var(--brand-muted)] transition-all">Ubah Foto Profil</button>
                    <button class="w-full py-3 rounded-2xl bg-[#FAF7F6] text-xs font-bold text-[var(--text-dark)] hover:bg-[var(--brand-muted)] transition-all">Reset Password</button>
                </div>
            </div>
        </div>

        <!-- Form Profile -->
        <div class="md:col-span-2">
            <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50">
                <h3 class="poppins text-lg font-bold text-[var(--text-dark)] mb-6">Informasi Personal</h3>
                
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                            <input type="text" value="<?= htmlspecialchars($user['name']) ?>" class="w-full px-5 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--text-dark)] outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email</label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full px-5 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--text-dark)] outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor Telepon</label>
                        <input type="text" placeholder="+62 8xx xxxx xxxx" class="w-full px-5 py-3.5 bg-[#FAF7F6] border-none rounded-2xl text-xs font-bold text-[var(--text-dark)] outline-none focus:ring-2 focus:ring-[var(--brand-soft)] transition-all">
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-3 rounded-2xl bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-[#57000033] hover:translate-y-[-2px] active:translate-y-[0] transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Section -->
            <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 mt-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)]">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h3 class="poppins text-lg font-bold text-[var(--text-dark)]">Keamanan Akun</h3>
                </div>
                
                <div class="flex items-center justify-between p-5 bg-[#FAF7F6] rounded-2xl">
                    <div>
                        <p class="text-xs font-bold text-[var(--text-dark)]">Password Terakhir Diubah</p>
                        <p class="text-[10px] text-gray-400 font-medium">3 bulan yang lalu</p>
                    </div>
                    <button class="px-4 py-2 rounded-xl border border-[var(--brand)] text-[10px] font-black text-[var(--brand)] uppercase tracking-widest hover:bg-[var(--brand-muted)] transition-all">
                        Ubah Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
