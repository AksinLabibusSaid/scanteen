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
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap");

      * {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      }

      :root {
        --maroon: #800000;
        --salmon: #ff8371;
      }

      .bg-maroon { background-color: var(--maroon); }
      .text-salmon { color: var(--salmon); }
      .text-maroon { color: var(--maroon); }

      input:focus {
        border-color: var(--maroon);
        outline: none;
        box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
      }

      .btn-login {
        background-color: var(--maroon);
        box-shadow: 0 10px 15px -3px rgba(128, 0, 0, 0.2), 0 4px 6px -4px rgba(128, 0, 0, 0.2);
        transition: all 0.2s ease;
      }
      .btn-login:hover { background-color: rgba(128, 0, 0, 0.9); }
      .btn-login:active { background-color: rgba(128, 0, 0, 0.8); }

      .img-overlay {
        opacity: 0.3;
        mix-blend-mode: overlay;
        background-color: #000;
      }

      .pwd-toggle { transition: color 0.2s ease; }
      .pwd-toggle:hover { color: var(--maroon); }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 lg:p-20">
        <div class="w-full max-w-5xl rounded-3xl shadow-lg overflow-hidden grid grid-cols-1 lg:grid-cols-2 bg-white">
            <!-- Left: Branding -->
            <div class="relative bg-maroon flex flex-col justify-between p-10 sm:p-12 min-h-64 lg:min-h-0 overflow-hidden">
                <div class="absolute inset-0">
                    <img
                        src="https://api.builder.io/api/v1/image/assets/TEMP/ea6c8305bdda7f6383c0fdab51bd4516a59dbd55?width=1024"
                        alt=""
                        class="w-full h-full object-cover img-overlay"
                    />
                </div>
                <div class="relative z-10 flex-1 lg:flex-none"></div>
                <div class="relative z-10 flex flex-col gap-8 mt-auto">
                    <div class="flex flex-col gap-3">
                        <p class="text-white text-base font-normal leading-6">Modern Culinary Efficiency</p>
                        <p class="text-salmon text-base font-normal leading-6 max-w-xs opacity-90">
                            Elevate your canteen management with our precise, automated, and elegant ecosystem designed for the modern vendor.
                        </p>
                    </div>
                    <div class="flex items-start gap-6">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-white text-base font-normal leading-6">2k+</span>
                            <span class="text-salmon text-xs font-medium leading-tight">Active Vendors</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-white text-base font-normal leading-6">99%</span>
                            <span class="text-salmon text-xs font-medium leading-tight">Satisfaction</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Login -->
            <div class="flex flex-col p-10 sm:p-12">
                <div class="flex flex-col gap-6 w-full max-w-md mx-auto">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-3xl font-semibold text-[#1A1C1A] leading-tight tracking-tight">Selamat Datang</h1>
                        <p class="text-base font-normal text-[#5F5E5B] leading-6">Silakan masuk ke akun Anda</p>
                    </div>

                    <?php if ($error !== ''): ?>
                        <div class="rounded-xl bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-100"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>

                    <form method="post" class="flex flex-col gap-5">
                        <?php if ($next !== ''): ?>
                            <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-medium text-[#5A413D]" for="login-email">Email atau Username</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.66667 6.66667C5.75 6.66667 4.96528 6.34028 4.3125 5.6875C3.65972 5.03472 3.33333 4.25 3.33333 3.33333C3.33333 2.41667 3.65972 1.63194 4.3125 0.979167C4.96528 0.326389 5.75 0 6.66667 0C7.58333 0 8.36806 0.326389 9.02083 0.979167C9.67361 1.63194 10 2.41667 10 3.33333C10 4.25 9.67361 5.03472 9.02083 5.6875C8.36806 6.34028 7.58333 6.66667 6.66667 6.66667ZM0 13.3333V11C0 10.5278 0.121528 10.0938 0.364583 9.69792C0.607639 9.30208 0.930556 9 1.33333 8.79167C2.19444 8.36111 3.06944 8.03819 3.95833 7.82292C4.84722 7.60764 5.75 7.5 6.66667 7.5C7.58333 7.5 8.48611 7.60764 9.375 7.82292C10.2639 8.03819 11.1389 8.36111 12 8.79167C12.4028 9 12.7257 9.30208 12.9688 9.69792C13.2118 10.0938 13.3333 10.5278 13.3333 11V13.3333H0ZM1.66667 11.6667H11.6667V11C11.6667 10.8472 11.6285 10.7083 11.5521 10.5833C11.4757 10.4583 11.375 10.3611 11.25 10.2917C10.5 9.91667 9.74306 9.63542 8.97917 9.44792C8.21528 9.26042 7.44444 9.16667 6.66667 9.16667C5.88889 9.16667 5.11806 9.26042 4.35417 9.44792C3.59028 9.63542 2.83333 9.91667 2.08333 10.2917C1.95833 10.3611 1.85764 10.4583 1.78125 10.5833C1.70486 10.7083 1.66667 10.8472 1.66667 11V11.6667ZM6.66667 5C7.125 5 7.51736 4.83681 7.84375 4.51042C8.17014 4.18403 8.33333 3.79167 8.33333 3.33333C8.33333 2.875 8.17014 2.48264 7.84375 2.15625C7.51736 1.82986 7.125 1.66667 6.66667 1.66667C6.20833 1.66667 5.81597 1.82986 5.48958 2.15625C5.16319 2.48264 5 2.875 5 3.33333C5 3.79167 5.16319 4.18403 5.48958 4.51042C5.81597 4.83681 6.20833 5 6.66667 5Z" fill="#8E706C"/>
                                    </svg>
                                </span>
                                <input
                                    id="login-email"
                                    name="email"
                                    type="email"
                                    required
                                    autocomplete="username"
                                    placeholder="nama@email.com"
                                    class="w-full pl-10 pr-4 py-3.5 rounded-lg border border-[#E2BFB9] bg-white text-base text-gray-900 placeholder-gray-400 outline-none transition-colors"
                                />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-medium text-[#5A413D]" for="password-input">Kata Sandi</label>
                                <button type="button" class="text-xs font-medium text-maroon hover:underline" title="Hubungi administrator">
                                    Lupa Password?
                                </button>
                            </div>
                            <div class="relative mt-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.66667 17.5C1.20833 17.5 0.815972 17.3368 0.489583 17.0104C0.163194 16.684 0 16.2917 0 15.8333V7.5C0 7.04167 0.163194 6.64931 0.489583 6.32292C0.815972 5.99653 1.20833 5.83333 1.66667 5.83333H2.5V4.16667C2.5 3.01389 2.90625 2.03125 3.71875 1.21875C4.53125 0.40625 5.51389 0 6.66667 0C7.81944 0 8.80208 0.40625 9.61458 1.21875C10.4271 2.03125 10.8333 3.01389 10.8333 4.16667V5.83333H11.6667C12.125 5.83333 12.5174 5.99653 12.8438 6.32292C13.1701 6.64931 13.3333 7.04167 13.3333 7.5V15.8333C13.3333 16.2917 13.1701 16.684 12.8438 17.0104C12.5174 17.3368 12.125 17.5 11.6667 17.5H1.66667ZM1.66667 15.8333H11.6667V7.5H1.66667V15.8333ZM6.66667 13.3333C7.125 13.3333 7.51736 13.1701 7.84375 12.8438C8.17014 12.5174 8.33333 12.125 8.33333 11.6667C8.33333 11.2083 8.17014 10.816 7.84375 10.4896C7.51736 10.1632 7.125 10 6.66667 10C6.20833 10 5.81597 10.1632 5.48958 10.4896C5.16319 10.816 5 11.2083 5 11.6667C5 12.125 5.16319 12.5174 5.48958 12.8438C5.81597 13.1701 6.20833 13.3333 6.66667 13.3333ZM4.16667 5.83333H9.16667V4.16667C9.16667 3.47222 8.92361 2.88194 8.4375 2.39583C7.95139 1.90972 7.36111 1.66667 6.66667 1.66667C5.97222 1.66667 5.38194 1.90972 4.89583 2.39583C4.40972 2.88194 4.16667 3.47222 4.16667 4.16667V5.83333ZM1.66667 15.8333V7.5V15.8333Z" fill="#8E706C"/>
                                    </svg>
                                </span>
                                <input
                                    id="password-input"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-12 py-3.5 rounded-lg border border-[#E2BFB9] bg-white text-base text-gray-900 placeholder-gray-400 outline-none transition-colors"
                                />
                                <button type="button" class="pwd-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#8E706C]" id="pwd-toggle-btn" aria-label="Tampilkan sandi">
                                    <svg width="19" height="13" viewBox="0 0 19 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.16667 10C10.2083 10 11.0938 9.63542 11.8229 8.90625C12.5521 8.17708 12.9167 7.29167 12.9167 6.25C12.9167 5.20833 12.5521 4.32292 11.8229 3.59375C11.0938 2.86458 10.2083 2.5 9.16667 2.5C8.125 2.5 7.23958 2.86458 6.51042 3.59375C5.78125 4.32292 5.41667 5.20833 5.41667 6.25C5.41667 7.29167 5.78125 8.17708 6.51042 8.90625C7.23958 9.63542 8.125 10 9.16667 10ZM9.16667 8.5C8.54167 8.5 8.01042 8.28125 7.57292 7.84375C7.13542 7.40625 6.91667 6.875 6.91667 6.25C6.91667 5.625 7.13542 5.09375 7.57292 4.65625C8.01042 4.21875 8.54167 4 9.16667 4C9.79167 4 10.3229 4.21875 10.7604 4.65625C11.1979 5.09375 11.4167 5.625 11.4167 6.25C11.4167 6.875 11.1979 7.40625 10.7604 7.84375C10.3229 8.28125 9.79167 8.5 9.16667 8.5ZM9.16667 12.5C7.13889 12.5 5.29167 11.934 3.625 10.8021C1.95833 9.67014 0.75 8.15278 0 6.25C0.75 4.34722 1.95833 2.82986 3.625 1.69792C5.29167 0.565972 7.13889 0 9.16667 0C11.1944 0 13.0417 0.565972 14.7083 1.69792C16.375 2.82986 17.5833 4.34722 18.3333 6.25C17.5833 8.15278 16.375 9.67014 14.7083 10.8021C13.0417 11.934 11.1944 12.5 9.16667 12.5ZM9.16667 10.8333C10.7361 10.8333 12.1771 10.4201 13.4896 9.59375C14.8021 8.76736 15.8056 7.65278 16.5 6.25C15.8056 4.84722 14.8021 3.73264 13.4896 2.90625C12.1771 2.07986 10.7361 1.66667 9.16667 1.66667C7.59722 1.66667 6.15625 2.07986 4.84375 2.90625C3.53125 3.73264 2.52778 4.84722 1.83333 6.25C2.52778 7.65278 3.53125 8.76736 4.84375 9.59375C6.15625 10.4201 7.59722 10.8333 9.16667 10.8333Z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-login w-full py-3 rounded-lg text-white text-sm font-semibold tracking-wider text-center">
                            Masuk
                        </button>
                    </form>

                    <div class="border-t border-[#E3E2E0] pt-6">
                        <p class="text-center text-xs text-[#8E706C] mt-3 max-w-sm mx-auto leading-relaxed">
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
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
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
