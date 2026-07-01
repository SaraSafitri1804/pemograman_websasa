<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

// Set page title untuk topbar
$page_title = 'Tambah Reward';

// Include header
require_once '../includes/header.php';
?>

<body class="bg-background text-on-surface antialiased min-h-screen flex">

  <?php require_once '../includes/sidebar.php'; ?>

  <div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
    
    <?php require_once '../includes/topbar.php'; ?>

    <main class="flex-1 pt-24 px-margin-mobile lg:px-margin-desktop pb-8 max-w-[1000px] mx-auto w-full">
      
      <!-- Breadcrumbs -->
      <nav aria-label="Breadcrumb" class="flex items-center gap-2 mb-8">
        <a class="text-on-surface-variant font-label-md text-label-md hover:text-primary transition-colors" href="index.php">Reward</a>
        <span class="material-symbols-outlined text-[16px] text-outline">chevron_right</span>
        <span class="text-on-surface font-label-md text-label-md font-bold">Tambah Reward</span>
      </nav>

      <!-- Page Header -->
      <div class="mb-8">
        <h2 class="font-display-md text-display-md text-on-surface mb-2">Buat Reward Baru</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Konfigurasi item penukaran poin baru untuk pelanggan Anda.</p>
      </div>

      <!-- Form Card -->
      <div class="glass-card rounded-xl p-8">
        <form action="simpan.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-stack-lg">
          
          <!-- Nama Reward -->
          <div class="md:col-span-2 space-y-2">
            <label class="font-label-md text-label-md text-on-surface-variant flex items-center gap-1" for="nama_reward">
              Nama Reward <span class="text-error">*</span>
            </label>
            <input 
              class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary font-body-md text-body-md bg-white" 
              id="nama_reward" 
              name="nama_reward" 
              placeholder="Contoh: Voucher Belanja Rp 50.000" 
              type="text"
              required
            />
            <p class="font-label-sm text-label-sm text-outline">Gunakan nama yang jelas dan menarik untuk pengguna.</p>
          </div>

          <!-- Poin -->
          <div class="space-y-2">
            <label class="font-label-md text-label-md text-on-surface-variant flex items-center gap-1" for="jumlah_poin">
              Jumlah Poin yang Dibutuhkan <span class="text-error">*</span>
            </label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">stars</span>
              <input 
                class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary font-body-md text-body-md bg-white" 
                id="jumlah_poin" 
                name="jumlah_poin" 
                placeholder="0" 
                type="number"
                min="1"
                required
              />
            </div>
          </div>

          <!-- Stok -->
          <div class="space-y-2">
            <label class="font-label-md text-label-md text-on-surface-variant flex items-center gap-1" for="stok">
              Stok <span class="text-error">*</span>
            </label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">inventory</span>
              <input 
                class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary font-body-md text-body-md bg-white" 
                id="stok" 
                name="stok" 
                placeholder="0" 
                type="number"
                min="0"
                required
              />
            </div>
          </div>

          <!-- Upload Foto -->
          <div class="md:col-span-2 space-y-2">
            <label class="font-label-md text-label-md text-on-surface-variant" for="foto">
              Gambar Reward
            </label>
            <div class="border-2 border-dashed border-outline-variant rounded-xl p-8 flex flex-col items-center justify-center bg-surface-container-low hover:bg-surface-container-high transition-colors cursor-pointer relative">
              <input type="file" name="foto" id="foto" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(event)"/>
              <div id="preview-container" class="flex flex-col items-center">
                <span class="material-symbols-outlined text-outline text-4xl mb-2">cloud_upload</span>
                <p class="font-body-md text-on-surface">Klik atau seret gambar ke sini</p>
                <p class="font-label-sm text-outline mt-1">PNG, JPG up to 2MB</p>
              </div>
              <img id="preview-image" class="hidden max-h-48 rounded-lg" alt="Preview"/>
            </div>
          </div>

          <!-- Buttons -->
          <div class="md:col-span-2 flex items-center justify-end gap-stack-md pt-8 border-t border-outline-variant mt-4">
            <a href="index.php" class="px-6 py-2.5 rounded-lg border border-outline text-on-surface-variant font-label-md text-label-md hover:bg-surface-container-high transition-colors duration-200">
              Batal
            </a>
            <button class="px-8 py-2.5 rounded-lg bg-primary text-white font-label-md text-label-md hover:bg-primary-container active:scale-95 transition-all shadow-md" type="submit">
              Simpan Reward
            </button>
          </div>
        </form>
      </div>

      <!-- Quick Tips -->
      <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-secondary-container/30 border border-secondary-container rounded-xl">
          <span class="material-symbols-outlined text-secondary text-[32px] mb-3">lightbulb</span>
          <h4 class="font-headline-md text-headline-md text-on-secondary-container mb-2 text-[16px]">Tips Konfigurasi</h4>
          <p class="font-body-md text-body-md text-on-secondary-container/80">Pastikan poin yang dibutuhkan sebanding dengan nilai pasar reward agar program tetap menarik.</p>
        </div>
        <div class="p-6 bg-tertiary-fixed/30 border border-tertiary-fixed rounded-xl">
          <span class="material-symbols-outlined text-tertiary text-[32px] mb-3">auto_graph</span>
          <h4 class="font-headline-md text-headline-md text-on-tertiary-fixed mb-2 text-[16px]">Analitik Otomatis</h4>
          <p class="font-body-md text-body-md text-on-tertiary-fixed/80">Sistem akan memantau kecepatan penukaran stok dan memberikan notifikasi jika stok hampir habis.</p>
        </div>
        <div class="p-6 bg-surface-container-high border border-outline-variant rounded-xl">
          <span class="material-symbols-outlined text-on-surface-variant text-[32px] mb-3">security</span>
          <h4 class="font-headline-md text-headline-md text-on-surface mb-2 text-[16px]">Keamanan</h4>
          <p class="font-body-md text-body-md text-on-surface-variant">Setiap penukaran reward akan diverifikasi melalui OTP atau kode unik untuk mencegah fraud.</p>
        </div>
      </div>

    </main>

    <footer class="mt-auto py-6 px-margin-desktop border-t border-outline-variant bg-surface-container-lowest flex justify-between items-center w-full">
      <p class="font-label-sm text-label-sm text-secondary">© 2024 RewardSystem Enterprise. All rights reserved.</p>
      <div class="flex gap-4">
        <a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="#">Privacy Policy</a>
        <a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="#">Terms of Service</a>
        <a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="#">Help Center</a>
      </div>
    </footer>
  </div>

  <script>
    function previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const previewContainer = document.getElementById('preview-container');
          const previewImage = document.getElementById('preview-image');
          
          previewContainer.classList.add('hidden');
          previewImage.src = e.target.result;
          previewImage.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
      }
    }
  </script>
</body>
</html>
