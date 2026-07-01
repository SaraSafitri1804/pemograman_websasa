<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));
?>
<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden opacity-0 pointer-events-none" onclick="closeSidebar()"></div>

<!-- SideNavBar -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-[280px] border-r border-outline-variant bg-surface-container-lowest flex flex-col py-stack-lg z-50 -translate-x-full lg:translate-x-0">
  <!-- Mobile Close Button -->
  <button onclick="closeSidebar()" class="absolute top-4 right-4 lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high transition-colors">
    <span class="material-symbols-outlined text-[20px]">close</span>
  </button>

  <!-- Brand -->
  <a href="../dashboard/dashboard.php" class="px-6 lg:px-margin-desktop mb-stack-lg flex items-center gap-3 hover:opacity-80 transition-opacity">
    <img class="w-8 h-8 object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBXjsvWU63WxPo4Ea4oIDyftCaN5sWWvFxN5YmzjLz8nN-rBicoB801mF3rzekDBsjlUQWN7JH52ysH10C_PfpHVYN8GUViAUk8kWGXQj6iwkUEsSoZGQ5O6RUNcWE9rJ3TQwafb83jem6S_kMjoOetqvl6G0UGZRyt3FmLbaY53ERIerKbLobsaj7vL9mhcFKEmrxYpFQsxYbxLjrX86Uc_6zq6X-nsA_IJHCbin3PTDKGr5neUvIKnTWtENQDa8NRk3NuEiEPOVA" alt="Logo"/>
    <div>
      <h1 class="font-headline-md text-headline-md font-bold text-primary">Reward System</h1>
      <p class="font-label-sm text-label-sm text-on-surface-variant">Enterprise Admin</p>
    </div>
  </a>

  <!-- Navigation -->
  <nav class="flex-1 px-4 flex flex-col gap-1 overflow-y-auto">
    <!-- Dashboard -->
    <a class="flex items-center gap-3 px-4 py-3 <?= ($current_page == 'dashboard.php' || $current_page == 'dashboard.php') ? 'bg-secondary-container text-primary border-l-4 border-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low border-l-4 border-transparent' ?> rounded-lg cursor-pointer transition-all duration-200" href="../dashboard/dashboard.php" onclick="closeSidebar()">
      <span class="material-symbols-outlined <?= ($current_page == 'dashboard.php' || $current_page == 'dashboard.php') ? 'filled' : '' ?>">dashboard</span>
      <span class="font-body-md text-body-md">Dashboard</span>
    </a>

    <!-- Customer -->
    <a class="flex items-center gap-3 px-4 py-3 <?= ($current_folder == 'customer') ? 'bg-secondary-container text-primary border-l-4 border-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low border-l-4 border-transparent hover:border-outline-variant' ?> rounded-lg cursor-pointer transition-all duration-200" href="../customer/index.php" onclick="closeSidebar()">
      <span class="material-symbols-outlined <?= ($current_folder == 'customer') ? 'filled' : '' ?>">group</span>
      <span class="font-body-md text-body-md">Customer</span>
    </a>

    <!-- Reward -->
    <a class="flex items-center gap-3 px-4 py-3 <?= ($current_folder == 'reward') ? 'bg-secondary-container text-primary border-l-4 border-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low border-l-4 border-transparent hover:border-outline-variant' ?> rounded-lg cursor-pointer transition-all duration-200" href="../reward/index.php" onclick="closeSidebar()">
      <span class="material-symbols-outlined <?= ($current_folder == 'reward') ? 'filled' : '' ?>">military_tech</span>
      <span class="font-body-md text-body-md">Reward</span>
    </a>

    <!-- Transaksi -->
    <a class="flex items-center gap-3 px-4 py-3 <?= (strpos($_SERVER['PHP_SELF'], '/transaksi/') !== false) ? 'bg-secondary-container text-primary border-l-4 border-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low border-l-4 border-transparent hover:border-outline-variant' ?> rounded-lg cursor-pointer transition-all duration-200" href="../transaksi/index.php" onclick="closeSidebar()">
      <span class="material-symbols-outlined <?= (strpos($_SERVER['PHP_SELF'], '/transaksi/') !== false) ? 'filled' : '' ?>">receipt_long</span>
      <span class="font-body-md text-body-md">Transaksi</span>
    </a>

    <!-- Penukaran Reward -->
    <a class="flex items-center gap-3 px-4 py-3 <?= (strpos($_SERVER['PHP_SELF'], '/penukaran/') !== false) ? 'bg-secondary-container text-primary border-l-4 border-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low border-l-4 border-transparent hover:border-outline-variant' ?> rounded-lg cursor-pointer transition-all duration-200" href="../penukaran/index.php" onclick="closeSidebar()">
      <span class="material-symbols-outlined <?= (strpos($_SERVER['PHP_SELF'], '/penukaran/') !== false) ? 'filled' : '' ?>">redeem</span>
      <span class="font-body-md text-body-md">Penukaran Reward</span>
    </a>

    <div class="mt-auto pt-4 border-t border-outline-variant/30 flex flex-col gap-1">
      <a class="flex items-center gap-3 px-4 py-3 text-error hover:bg-error-container rounded-lg border-l-4 border-transparent cursor-pointer transition-all duration-200" href="../login/logout.php">
        <span class="material-symbols-outlined">logout</span>
        <span class="font-body-md text-body-md font-medium">Logout</span>
      </a>
    </div>
  </nav>
</aside>

<!-- Sidebar Toggle Script -->
<script>
  function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.remove('opacity-0', 'pointer-events-none');
    document.getElementById('sidebar-overlay').classList.add('opacity-100');
    document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('opacity-0', 'pointer-events-none');
    document.getElementById('sidebar-overlay').classList.remove('opacity-100');
    document.body.classList.remove('overflow-hidden');
  }
</script>