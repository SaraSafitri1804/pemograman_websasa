<!-- TopNavBar -->
<header class="fixed top-0 right-0 w-full lg:w-[calc(100%-280px)] h-16 bg-surface-container-lowest border-b border-outline-variant shadow-sm z-30 flex justify-between items-center px-4 lg:px-margin-desktop transition-all duration-200">
  <!-- Left: Hamburger + Breadcrumbs -->
  <div class="flex items-center gap-3">
    <!-- Hamburger (mobile only) -->
    <button onclick="openSidebar()" class="lg:hidden w-10 h-10 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container-low transition-colors">
      <span class="material-symbols-outlined">menu</span>
    </button>
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant">
      <a href="../dashboard/dashboard.php" class="hidden sm:inline hover:text-primary transition-colors hover:underline">Enterprise</a>
      <span class="material-symbols-outlined text-[16px] hidden sm:inline">chevron_right</span>
      <span class="text-primary font-bold"><?= isset($page_title) ? $page_title : 'Dashboard' ?></span>
    </div>
  </div>

  <!-- Actions -->
  <div class="flex items-center gap-2 sm:gap-4 relative">
    
    <!-- Notification Button & Dropdown -->
    <div class="relative">
      <button onclick="toggleDropdown('notifDropdown')" class="w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-surface-container-low hover:text-primary transition-all duration-200 relative">
        <span class="material-symbols-outlined">notifications</span>
        <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-surface-container-lowest"></span>
      </button>

      <!-- Notifications Dropdown -->
      <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg z-50 overflow-hidden transform origin-top-right transition-all">
        <div class="px-4 py-3 border-b border-outline-variant/50 bg-surface-container-low flex justify-between items-center">
          <span class="font-label-md text-label-md font-bold text-on-surface">Notifikasi Baru</span>
          <button class="font-label-sm text-label-sm text-primary hover:underline">Tandai dibaca</button>
        </div>
        <div class="max-h-80 overflow-y-auto">
          <a href="#" class="flex gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors border-b border-outline-variant/30">
            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="material-symbols-outlined text-[16px]">redeem</span>
            </div>
            <div>
              <p class="font-body-md text-body-md text-on-surface"><span class="font-semibold">Budi Santoso</span> menukarkan 5.000 poin.</p>
              <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">10 menit yang lalu</p>
            </div>
          </a>
          <a href="#" class="flex gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors border-b border-outline-variant/30">
            <div class="w-8 h-8 rounded-full bg-error-container text-error flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="material-symbols-outlined text-[16px]">warning</span>
            </div>
            <div>
              <p class="font-body-md text-body-md text-on-surface">Stok <span class="font-semibold">Voucher Makan</span> hampir habis (sisa 2).</p>
              <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">1 jam yang lalu</p>
            </div>
          </a>
        </div>
        <a href="javascript:void(0)" onclick="alert('Fitur Halaman Semua Notifikasi sedang dalam tahap pengembangan.'); toggleDropdown('notifDropdown');" class="block px-4 py-3 text-center font-label-md text-label-md text-primary hover:bg-surface-container-low transition-colors border-t border-outline-variant/50">
          Lihat Semua Notifikasi
        </a>
      </div>
    </div>

    <div class="h-8 w-px bg-outline-variant hidden sm:block"></div>

    <!-- User Profile Button & Dropdown -->
    <div class="relative">
      <div onclick="toggleDropdown('profileDropdown')" class="flex items-center gap-3 cursor-pointer hover:bg-surface-container-low p-1.5 pr-3 rounded-full transition-colors">
        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold text-sm shadow-sm border border-outline-variant">
          <?= isset($_SESSION['admin_nama']) ? strtoupper(substr($_SESSION['admin_nama'], 0, 1)) : 'A' ?>
        </div>
        <div class="flex-col hidden sm:flex">
          <span class="font-label-md text-label-md text-on-background"><?= isset($_SESSION['admin_nama']) ? htmlspecialchars($_SESSION['admin_nama']) : 'Admin User' ?></span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">System Administrator</span>
        </div>
        <span class="material-symbols-outlined text-[18px] text-on-surface-variant hidden sm:block">expand_more</span>
      </div>

      <!-- Profile Dropdown -->
      <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg z-50 overflow-hidden transform origin-top-right transition-all">
        <div class="px-4 py-3 border-b border-outline-variant/50">
          <p class="font-label-md text-label-md font-bold text-on-surface"><?= isset($_SESSION['admin_nama']) ? htmlspecialchars($_SESSION['admin_nama']) : 'Admin User' ?></p>
          <p class="font-label-sm text-label-sm text-on-surface-variant truncate">admin@loyaltypro.com</p>
        </div>
        <div class="py-1">
          <a href="javascript:void(0)" onclick="alert('Halaman Profil sedang dalam tahap pengembangan.'); toggleDropdown('profileDropdown');" class="flex items-center gap-3 px-4 py-2 hover:bg-surface-container-low text-on-surface-variant hover:text-primary transition-colors font-body-md text-body-md">
            <span class="material-symbols-outlined text-[18px]">person</span> Profil Saya
          </a>
          <a href="javascript:void(0)" onclick="alert('Halaman Pengaturan sedang dalam tahap pengembangan.'); toggleDropdown('profileDropdown');" class="flex items-center gap-3 px-4 py-2 hover:bg-surface-container-low text-on-surface-variant hover:text-primary transition-colors font-body-md text-body-md">
            <span class="material-symbols-outlined text-[18px]">settings</span> Pengaturan
          </a>
        </div>
        <div class="border-t border-outline-variant/50 py-1">
          <!-- Changed logout path from static ../login/logout.php to an absolute/relative path based on a safe assumption -->
          <a href="../login/logout.php" class="flex items-center gap-3 px-4 py-2 hover:bg-error-container text-error transition-colors font-body-md text-body-md font-medium">
            <span class="material-symbols-outlined text-[18px]">logout</span> Keluar
          </a>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Dropdown Scripts -->
<script>
  function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    const allDropdowns = ['notifDropdown', 'profileDropdown'];
    
    // Close other dropdowns
    allDropdowns.forEach(did => {
      if(did !== id) {
        document.getElementById(did).classList.add('hidden');
      }
    });

    // Toggle current
    dropdown.classList.toggle('hidden');
  }

  // Close dropdowns when clicking outside
  window.addEventListener('click', function(e) {
    if (!e.target.closest('.relative')) {
      const notifDropdown = document.getElementById('notifDropdown');
      const profileDropdown = document.getElementById('profileDropdown');
      if (notifDropdown) notifDropdown.classList.add('hidden');
      if (profileDropdown) profileDropdown.classList.add('hidden');
    }
  });
</script>