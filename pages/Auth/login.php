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
    <title>Selamat Datang — Scanteen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
      :root {
        --maroon: #7B0009;
        --maroon-dark: #500006;
      }
      * {
        font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif;
        box-sizing: border-box;
      }

      /* Background page */
      body { background: #F5F4F2; }

      /* Left panel gradient */
      .panel-left {
        background: linear-gradient(155deg, #7B0009 0%, #500006 45%, #230002 100%);
        position: relative;
        overflow: hidden;
      }
      .panel-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('https://api.builder.io/api/v1/image/assets/TEMP/ea6c8305bdda7f6383c0fdab51bd4516a59dbd55?width=1024') center/cover no-repeat;
        opacity: 0.3;
        mix-blend-mode: overlay;
      }
      /* Decorative circles on the left panel */
      .panel-left::after {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.06);
        top: -80px;
        right: -80px;
        pointer-events: none;
      }
      .deco-circle {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.05);
        pointer-events: none;
      }



      /* Input */
      .field-input {
        width: 100%;
        padding: 12px 12px 12px 42px;
        border: 1.5px solid #E5D8D6;
        border-radius: 12px;
        background: #FAFAF9;
        font-size: 14px;
        color: #1A1A1A;
        transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
        outline: none;
      }
      .field-input:focus {
        border-color: var(--maroon);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(123,0,9,0.09);
      }
      .field-input::placeholder { color: #B8ACA9; }

      /* Button */
      .btn-login {
        background: linear-gradient(135deg, #7B0009 0%, #500006 100%);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 14px;
        width: 100%;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(123,0,9,0.25), inset 0 1px 0 rgba(255,255,255,0.08);
        transition: opacity 0.18s, transform 0.18s, box-shadow 0.18s;
      }
      .btn-login:hover {
        opacity: 0.92;
        box-shadow: 0 8px 24px rgba(123,0,9,0.35);
        transform: translateY(-1px);
      }
      .btn-login:active { transform: translateY(0); opacity: 1; }

      /* Password toggle */
      .pwd-toggle { transition: color 0.2s; cursor: pointer; background: none; border: none; }
      .pwd-toggle:hover { color: var(--maroon); }

      /* Error banner */
      .error-banner {
        border-radius: 10px;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
        font-size: 13px;
        font-weight: 500;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 lg:p-16">
        <div class="w-full max-w-5xl rounded-[28px] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2 bg-white" style="box-shadow: 0 25px 60px rgba(0,0,0,0.12), 0 8px 20px rgba(0,0,0,0.06);">

            <!-- ========== LEFT: Branding ========== -->
            <div class="panel-left relative flex flex-col justify-between p-10 sm:p-12 min-h-[280px] lg:min-h-0">

                <!-- Decorative circles -->
                <div class="deco-circle" style="width:380px;height:380px;top:-120px;right:-120px;"></div>
                <div class="deco-circle" style="width:200px;height:200px;bottom:60px;left:-60px;"></div>

                <!-- Top badge -->
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);backdrop-filter:blur(6px);">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-300" style="animation:pulse 2s infinite;"></span>
                        <span class="text-white text-[10px] font-bold uppercase tracking-widest">Scanteen</span>
                    </div>
                </div>

                <!-- Bottom content -->
                <div class="relative z-10 flex flex-col gap-8 mt-auto">
                    <div class="flex flex-col gap-3">
                        <span class="text-[10px] font-black uppercase tracking-[3px]" style="color:rgba(255,180,170,0.8);">Ekosistem Digital Kantin</span>
                        <h2 class="text-white font-black leading-tight" style="font-size:clamp(22px,3vw,32px);letter-spacing:-0.02em;">
                            Kelola Kantin Lebih<br>Cerdas &amp; Efisien
                        </h2>
                        <p class="text-sm font-medium leading-relaxed max-w-xs" style="color:rgba(255,220,215,0.75);">
                            Pantau pesanan, kelola warung, dan tingkatkan layanan kantin Anda dalam satu platform terintegrasi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ========== RIGHT: Login Form ========== -->
            <div class="flex flex-col justify-center p-10 sm:p-12 bg-white">
                <div class="flex flex-col gap-7 w-full max-w-sm mx-auto">

                    <!-- Heading -->
                    <div class="flex flex-col gap-1.5">
                        <h1 class="font-black text-[#1A1A1A] leading-tight" style="font-size:26px;letter-spacing:-0.02em;">Selamat Datang 👋</h1>
                        <p class="text-sm font-medium" style="color:#7A7470;">Masuk ke akun staff Anda untuk melanjutkan.</p>
                    </div>

                    <!-- Error banner -->
                    <?php if ($error !== ''): ?>
                        <div class="error-banner">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form method="post" class="flex flex-col gap-4">
                        <?php if ($next !== ''): ?>
                            <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>

                        <!-- Email field -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold" style="color:#5A413D;letter-spacing:0.02em;" for="login-email">Email</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" style="color:#B8ACA9;">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </span>
                                <input
                                    id="login-email"
                                    name="email"
                                    type="email"
                                    required
                                    autocomplete="username"
                                    placeholder="nama@email.com"
                                    class="field-input"
                                />
                            </div>
                        </div>

                        <!-- Password field -->
                        <div class="flex flex-col gap-1.5">
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-bold" style="color:#5A413D;letter-spacing:0.02em;" for="password-input">Kata Sandi</label>
                                <button type="button" class="text-xs font-semibold hover:underline" style="color:var(--maroon);" title="Hubungi administrator">
                                    Lupa Password?
                                </button>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" style="color:#B8ACA9;">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input
                                    id="password-input"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    style="padding-right: 44px;"
                                    class="field-input"
                                />
                                <button type="button" class="pwd-toggle absolute right-3 top-1/2 -translate-y-1/2" id="pwd-toggle-btn" style="color:#B8ACA9;" aria-label="Tampilkan sandi">
                                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn-login" style="margin-top:4px;">
                            Masuk
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="border-t pt-5" style="border-color:#EEE;">
                        <p class="text-center text-xs font-medium" style="color:#B8ACA9;">
                            © 2026 Scanteen. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
      (function () {
        const passwordInput = document.getElementById("password-input");
        const toggleBtn = document.getElementById("pwd-toggle-btn");
        if (passwordInput && toggleBtn) {
          toggleBtn.addEventListener("click", function () {
            const isHidden = passwordInput.type === "password";
            passwordInput.type = isHidden ? "text" : "password";
            toggleBtn.style.color = isHidden ? "#7B0009" : "#B8ACA9";
          });
        }
        const hint = document.getElementById("btn-register-hint");
        if (hint) {
          hint.addEventListener("click", function () {
            alert("Pendaftaran akun staff hanya dapat dilakukan oleh administrator setelah masuk ke panel Admin.");
          });
        }
      })();
    </script>
</body>
</html>

