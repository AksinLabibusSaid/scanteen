<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/config/db.php';

use App\Staff\StaffAuth;
use App\Support\PublicUrl;

$base = PublicUrl::basePath();

$next = isset($_GET['next']) && is_string($_GET['next']) ? trim($_GET['next']) : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['next']) && is_string($_POST['next'])) {
        $fromPost = trim($_POST['next']);
        if ($fromPost !== '') {
            $next = $fromPost;
        }
    }

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (StaffAuth::attemptLogin($email, $password)) {
        $role = (string) StaffAuth::role();
        $home = PublicUrl::staffPortalPathForRole($role);
        if ($home === null) {
            StaffAuth::logout();
            $error = 'Peran akun tidak dikenal.';
        } elseif ($role === 'warung' && StaffAuth::warungId() === null) {
            StaffAuth::logout();
            $error = 'Akun warung belum dihubungkan ke stan (warung_id).';
        } else {
            $target = $home;
            if ($next !== '' && str_starts_with($next, $base . '/')) {
                $target = $next;
            }
            header('Location: ' . $target);
            exit;
        }
    } else {
        $error = 'Email atau sandi salah.';
    }
}

if (StaffAuth::check()) {
    $r = StaffAuth::role();
    $home = is_string($r) ? PublicUrl::staffPortalPathForRole($r) : null;
    if ($home !== null) {
        header('Location: ' . $home);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff — Scanteen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#FAF7F6] flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg border border-stone-100 p-8">
        <h1 class="text-2xl font-extrabold text-[#570000] mb-1">Scanteen</h1>
        <p class="text-sm text-stone-600 mb-6">
            Masuk untuk <strong>Admin</strong>, <strong>Kasir</strong>, atau <strong>Warung</strong>.
            Portal ditentukan otomatis dari <strong>role</strong> akun Anda.
        </p>
        <?php if ($error !== ''): ?>
            <div class="mb-4 rounded-xl bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-100"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post" class="space-y-4">
            <?php if ($next !== ''): ?>
                <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wide mb-1">Email</label>
                <input name="email" type="email" required autocomplete="username"
                       class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm focus:ring-2 focus:ring-[#800000]/30 focus:border-[#800000] outline-none"
                       placeholder="nama@kantin.local">
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wide mb-1">Sandi</label>
                <input name="password" type="password" required autocomplete="current-password"
                       class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm focus:ring-2 focus:ring-[#800000]/30 focus:border-[#800000] outline-none"
                       placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-[#7B0009] text-white font-bold text-sm shadow-md hover:bg-[#6A0008] transition-colors">
                Masuk
            </button>
        </form>
        <p class="text-xs text-stone-500 mt-6 leading-relaxed border-t border-stone-100 pt-4">
            <strong>Pelanggan</strong> tidak login di sini. Scan QR meja untuk membuka halaman pesanan
            (setiap meja punya barcode/token berbeda).
        </p>
        <p class="text-xs text-stone-500 mt-3 leading-relaxed">
            Demo: <code class="bg-stone-100 px-1 rounded">admin@scanteen.local</code> /
            <code class="bg-stone-100 px-1 rounded">kasir@scanteen.local</code> /
            <code class="bg-stone-100 px-1 rounded">warung1@scanteen.local</code> — sandi: <strong>scanteen123</strong>
        </p>
    </div>
</body>
</html>
