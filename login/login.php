<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: ../dashboard/dashboard.php");
    exit;
}
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>RewardAdmin - Login</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "on-surface": "#191b23",
            "on-tertiary-container": "#ffede6",
            "on-primary-fixed": "#00174b",
            "inverse-primary": "#b4c5ff",
            "secondary-fixed-dim": "#b7c8e1",
            "on-secondary-fixed-variant": "#38485d",
            "on-secondary": "#ffffff",
            "surface-bright": "#faf8ff",
            "on-secondary-fixed": "#0b1c30",
            "outline": "#737686",
            "primary-container": "#2563eb",
            "on-error-container": "#93000a",
            "inverse-surface": "#2e3039",
            "surface-container-highest": "#e1e2ed",
            "surface-container-high": "#e7e7f3",
            "secondary-fixed": "#d3e4fe",
            "error": "#ba1a1a",
            "primary": "#004ac6",
            "on-primary-container": "#eeefff",
            "surface-container": "#ededf9",
            "surface-container-low": "#f3f3fe",
            "outline-variant": "#c3c6d7",
            "inverse-on-surface": "#f0f0fb",
            "tertiary-container": "#bc4800",
            "primary-fixed": "#dbe1ff",
            "tertiary-fixed": "#ffdbcd",
            "on-surface-variant": "#434655",
            "surface": "#faf8ff",
            "primary-fixed-dim": "#b4c5ff",
            "tertiary-fixed-dim": "#ffb596",
            "on-tertiary": "#ffffff",
            "surface-container-lowest": "#ffffff",
            "error-container": "#ffdad6",
            "surface-variant": "#e1e2ed",
            "surface-dim": "#d9d9e5",
            "on-primary": "#ffffff",
            "on-error": "#ffffff",
            "on-tertiary-fixed": "#360f00",
            "on-secondary-container": "#54647a",
            "secondary": "#505f76",
            "secondary-container": "#d0e1fb",
            "on-background": "#191b23",
            "surface-tint": "#0053db",
            "on-tertiary-fixed-variant": "#7d2d00",
            "tertiary": "#943700",
            "on-primary-fixed-variant": "#003ea8",
            "background": "#faf8ff"
          },
          "borderRadius": {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          "spacing": {
            "gutter": "24px",
            "stack-sm": "8px",
            "sidebar-width": "280px",
            "container-max": "1440px",
            "stack-md": "16px",
            "stack-lg": "24px",
            "margin-mobile": "16px",
            "margin-desktop": "32px"
          },
          "fontFamily": {
            "body-lg": ["Inter"],
            "headline-md": ["Inter"],
            "body-md": ["Inter"],
            "display-lg": ["Inter"],
            "display-md": ["Inter"],
            "headline-lg": ["Inter"],
            "label-md": ["Inter"],
            "label-sm": ["Inter"]
          },
          "fontSize": {
            "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
            "headline-md": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
            "body-md": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
            "display-lg": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
            "display-md": ["30px", { "lineHeight": "38px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
            "headline-lg": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
            "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.01em", "fontWeight": "600" }],
            "label-sm": ["11px", { "lineHeight": "14px", "fontWeight": "500" }]
          }
        },
      },
    }
  </script>
  <style>
    .glass-panel {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.5);
    }
  </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex antialiased">
  <div class="flex flex-col md:flex-row w-full min-h-screen">
    
    <div class="hidden md:flex md:w-1/2 lg:w-5/12 bg-[#F8FAFC] flex-col justify-between p-margin-desktop border-r border-outline-variant relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-fixed rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-secondary-fixed rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-1/3 translate-y-1/3"></div>
      </div>
      <div class="relative z-10 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-3xl font-variation-settings: 'FILL' 1;">stars</span>
        <span class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">LoyaltyPro</span>
      </div>
      <div class="relative z-10 flex flex-col items-center justify-center flex-grow py-stack-lg max-w-md mx-auto text-center">
        <img alt="Reward System 3D Illustration" class="w-full max-w-sm object-contain mb-stack-lg drop-shadow-xl animate-[pulse_4s_ease-in-out_infinite]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDQW99rTaNpgd1UOIHCYWcAB151KPSC9Xoqkqe_kAE23CN2ZGzsFkykE0Z2OvZRcdBywfw3KJ82Pmnz231PK0jyHXDFZiQOzwc_7H97eH70d8skX0bhJKUXXFGExZtbLSGWJ-kIclfZt7ayikCFQUNHmqHGQzur0UrrrpPG0pzKh5alQx4levH8iLc-c3LOFeDeb3YBtPUyIvYYYCm6gBdAj0C3Qk2wMAwYMzG3bOeoZKMBzo2hnR6G2wZdZRG50AyMg-d8c-xTeCM" style="filter: drop-shadow(0 20px 30px rgba(37, 99, 235, 0.15));"/>
        <h1 class="font-display-md text-display-md text-on-surface mb-stack-sm font-bold">Optimalkan Loyalitas</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-sm mx-auto">
          Kelola program loyalitas pelanggan Anda dengan lebih cerdas, aman, dan efisien dalam satu platform terpusat.
        </p>
      </div>
      <div class="relative z-10 font-label-sm text-label-sm text-outline flex justify-between items-center w-full">
        <span>© 2026 LoyaltyPro Enterprise</span>
        <div class="flex gap-4">
          <a class="hover:text-primary transition-colors" href="#">Bantuan</a>
          <a class="hover:text-primary transition-colors" href="#">Privasi</a>
        </div>
      </div>
    </div>

    <div class="w-full md:w-1/2 lg:w-7/12 bg-surface-container-lowest flex flex-col p-margin-mobile md:p-margin-desktop relative min-h-screen">
      <!-- Mobile Logo (Non-absolute) -->
      <div class="flex md:hidden items-center gap-2 mb-auto pt-4">
        <span class="material-symbols-outlined text-primary text-2xl font-variation-settings: 'FILL' 1;">stars</span>
        <span class="font-headline-md text-headline-md font-bold text-on-surface tracking-tight">LoyaltyPro</span>
      </div>
      
      <div class="flex-1 flex items-center justify-center w-full my-8 md:my-0">
        <div class="w-full max-w-[420px] mx-auto">
          <div class="glass-panel p-8 md:p-10 rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05),0_10px_15px_-3px_rgba(0,0,0,0.03)] border border-surface-variant relative z-10">
          <div class="mb-8 text-center md:text-left">
            <h2 class="font-headline-lg text-headline-lg font-semibold text-on-surface mb-2">Selamat Datang Kembali</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Masuk ke akun admin Anda untuk melanjutkan.</p>
          </div>

          <?php if ($error): ?>
          <div class="flex items-center gap-3 bg-error-container text-error p-4 rounded-lg border border-error/20 mb-5">
            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">error</span>
            <span class="font-label-md text-label-md"><?= htmlspecialchars($error) ?></span>
          </div>
          <?php endif; ?>

          <form action="proses_login.php" method="POST" class="space-y-5" id="loginForm">
            <div class="space-y-1">
              <label class="block font-label-md text-label-md text-on-surface font-semibold" for="username">Username</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors text-[20px]">person</span>
                </div>
                <input class="block w-full pl-10 pr-3 py-2.5 border border-outline-variant rounded-lg text-on-surface bg-surface-container-lowest focus:ring-2 focus:ring-primary-container focus:border-primary-container transition-all duration-200 placeholder-outline-variant font-body-md text-body-md" id="username" name="username" placeholder="Masukkan username Anda" required type="text" autocomplete="off"/>
              </div>
            </div>
            
            <div class="space-y-1">
              <label class="block font-label-md text-label-md text-on-surface font-semibold" for="password">Password</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors text-[20px]">lock</span>
                </div>
                <input class="block w-full pl-10 pr-10 py-2.5 border border-outline-variant rounded-lg text-on-surface bg-surface-container-lowest focus:ring-2 focus:ring-primary-container focus:border-primary-container transition-all duration-200 placeholder-outline-variant font-body-md text-body-md" id="password" name="password" placeholder="••••••••" required type="password"/>
                <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-outline hover:text-on-surface transition-colors focus:outline-none" id="togglePassword" type="button">
                  <span class="material-symbols-outlined text-[20px] pointer-events-none" id="toggleIcon">visibility_off</span>
                </button>
              </div>
            </div>
            
            <div class="flex items-center justify-between pt-1">
              <div class="flex items-center">
                <input class="h-4 w-4 text-primary focus:ring-primary-container border-outline-variant rounded transition-colors bg-surface-container-lowest cursor-pointer" id="remember-me" name="remember-me" type="checkbox"/>
                <label class="ml-2 block font-body-md text-body-md text-on-surface-variant cursor-pointer" for="remember-me">
                  Ingat saya
                </label>
              </div>
              <div class="text-sm">
                <a class="font-label-md text-label-md font-semibold text-primary hover:text-primary-fixed-dim transition-colors" href="#">
                  Lupa Password?
                </a>
              </div>
            </div>
            
            <div class="pt-2">
              <button class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm font-label-md text-label-md font-semibold text-on-primary bg-[#2563EB] hover:bg-primary hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-container transition-all duration-200 ease-in-out relative overflow-hidden group" id="submitBtn" type="submit">
                <span class="relative z-10 flex items-center gap-2">
                  Masuk ke Dashboard
                  <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform duration-200">arrow_forward</span>
                </span>
                <div class="absolute inset-0 w-full h-full bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
              </button>
            </div>
          </form>
          
          <div class="mt-8 flex items-center justify-center gap-1.5 text-outline">
            <span class="material-symbols-outlined text-[16px]">verified_user</span>
            <span class="font-label-sm text-label-sm">Koneksi aman & terenkripsi</span>
          </div>
        </div>
      </div>
    </div>
    <!-- End Flex-1 Wrapper -->
    </div>

  </div>

  <script>
    // Functional Password Visibility Toggle
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.textContent = 'visibility';
        } else {
            passwordInput.type = 'password';
            icon.textContent = 'visibility_off';
        }
    });
  </script>
</body>
</html>