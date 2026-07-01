<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

// Menentukan variabel halaman untuk header.php
$page_title = 'Tambah Customer Baru';

// Mengambil file pembuka template HTML & Head
require_once '../includes/header.php'; 
?>

<body class="bg-background text-on-background antialiased min-h-screen flex">

  <?php require_once '../includes/sidebar.php'; ?>

  <div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
    
    <?php require_once '../includes/topbar.php'; ?>

    <main class="flex-1 pt-24 px-margin-mobile lg:px-margin-desktop pb-8 max-w-container-max mx-auto w-full">
      <div class="max-w-4xl mx-auto">
        
        <div class="mb-stack-lg">
          <a class="inline-flex items-center text-secondary hover:text-primary transition-colors font-body-md text-body-md mb-2" href="index.php">
            <span class="material-symbols-outlined text-sm mr-1">arrow_back</span>
            Kembali ke Daftar Customer
          </a>
          <h1 class="font-headline-lg text-headline-lg text-on-surface">Tambah Customer Baru</h1>
          <p class="font-body-md text-body-md text-secondary mt-1">Masukkan informasi detail pelanggan untuk mendaftarkan mereka ke sistem reward.</p>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] p-stack-lg md:p-8">
          <form action="simpan.php" method="POST" class="space-y-stack-lg">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
              <div class="space-y-2">
                <label class="block font-label-md text-label-md text-on-surface" id="customerIdLabel">ID Customer</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                    <span class="material-symbols-outlined text-[18px]">fingerprint</span>
                  </div>
                  <input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded-lg bg-surface-container text-secondary font-body-md text-body-md cursor-not-allowed" disabled id="customerIdDummy" placeholder="CUST-AUTO-GEN" type="text" value="CUST-AUTO-GEN"/>
                  <input type="hidden" name="id_customer" value="CUST-AUTO-GEN" />
                </div>
                <p class="font-label-sm text-label-sm text-secondary">ID akan dibuat otomatis oleh sistem.</p>
              </div>

              <div class="space-y-2">
                <label class="block font-label-md text-label-md text-on-surface" for="tanggal_daftar">Tanggal Pendaftaran</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                  </div>
                  <input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-primary focus:border-primary transition-shadow" id="tanggal_daftar" name="tanggal_daftar" type="date" value="<?= date('Y-m-d') ?>" required />
                </div>
              </div>
            </div>

            <div class="border-t border-outline-variant pt-6">
              <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Informasi Pribadi</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                
                <div class="space-y-2 md:col-span-2">
                  <label class="block font-label-md text-label-md text-on-surface" for="nama">Nama Lengkap *</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                      <span class="material-symbols-outlined text-[18px]">person</span>
                    </div>
                    <input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-shadow" id="nama" name="nama" placeholder="Masukkan nama lengkap pelanggan" required type="text"/>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block font-label-md text-label-md text-on-surface" for="email">Alamat Email *</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                      <span class="material-symbols-outlined text-[18px]">mail</span>
                    </div>
                    <input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-shadow" id="email" name="email" placeholder="email@contoh.com" required type="email"/>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block font-label-md text-label-md text-on-surface" for="no_hp">No HP *</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                      <span class="material-symbols-outlined text-[18px]">phone_iphone</span>
                    </div>
                    <input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-shadow" id="no_hp" name="no_hp" placeholder="081234567890" required type="text"/>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block font-label-md text-label-md text-on-surface" for="tier">Tier/Tingkatan Pelanggan</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                      <span class="material-symbols-outlined text-[18px]">stars</span>
                    </div>
                    <select class="block w-full pl-10 pr-10 py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-shadow appearance-none" id="tier" name="tier" required>
                      <option value="">-- Pilih Tier --</option>
                      <option value="Bronze">Bronze</option>
                      <option value="Silver">Silver</option>
                      <option value="Gold">Gold</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-secondary">
                      <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="border-t border-outline-variant pt-6">
              <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Informasi Tambahan</h3>
              <div class="space-y-2">
                <label class="block font-label-md text-label-md text-on-surface" for="alamat">Alamat Lengkap</label>
                <textarea class="block w-full p-3 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-shadow resize-y" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap pengiriman/domisili" rows="3" required></textarea>
              </div>
            </div>

            <div class="pt-6 flex items-center justify-end gap-4 border-t border-outline-variant">
              <a href="index.php" class="px-6 py-2.5 rounded-lg border border-outline-variant text-secondary bg-transparent hover:bg-surface-container-low font-label-md text-label-md transition-colors text-center">
                Batal
              </a>
              <button class="px-6 py-2.5 rounded-lg bg-primary text-white font-label-md text-label-md hover:opacity-90 shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center gap-2" type="submit">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Customer
              </button>
            </div>

          </form>
        </div>

      </div>
    </main>

  </div>

</body>
</html>