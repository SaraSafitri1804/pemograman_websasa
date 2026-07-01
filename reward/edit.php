<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: index.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM reward WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$r) { header("Location: index.php"); exit; }

// Set page title untuk topbar
$page_title = 'Edit Reward';

// Include header
require_once '../includes/header.php';
?>

<body class="bg-background text-on-surface antialiased min-h-screen flex">

  <?php require_once '../includes/sidebar.php'; ?>

  <div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
    
    <?php require_once '../includes/topbar.php'; ?>

    <main class="flex-1 pt-24 px-margin-mobile lg:px-margin-desktop pb-8 max-w-[1200px] mx-auto w-full">
      
      <!-- Breadcrumbs & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
        <div>
          <nav aria-label="Breadcrumb" class="flex items-center gap-2 mb-2">
            <a class="text-on-surface-variant font-label-md text-label-md hover:text-primary transition-colors" href="index.php">Reward</a>
            <span class="material-symbols-outlined text-[16px] text-outline">chevron_right</span>
            <span class="text-on-surface font-label-md text-label-md font-bold">Edit Reward</span>
          </nav>
          <h2 class="font-display-md text-display-md text-on-surface mb-2">Edit Reward</h2>
          <p class="font-body-md text-body-md text-on-surface-variant mt-1">Perbarui detail hadiah, stok, dan status untuk katalog penukaran poin pelanggan.</p>
        </div>
        <div class="flex gap-stack-md w-full sm:w-auto mt-4 sm:mt-0">
          <a href="index.php" class="flex-1 sm:flex-none px-6 py-2 border border-outline text-on-surface-variant rounded-lg font-label-md text-label-md hover:bg-surface-container-high transition-all active:scale-95 text-center">
            Batal
          </a>
          <button type="submit" form="form-edit" class="flex-1 sm:flex-none px-6 py-2 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95 text-center">
            Simpan Perubahan
          </button>
        </div>
      </div>

      <form id="form-edit" action="update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $r['id'] ?>" />
        <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($r['foto'] ?? '') ?>" />
        
        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-12 gap-gutter">
          
          <!-- Left Column: Primary Info -->
          <div class="col-span-12 lg:col-span-8 flex flex-col gap-gutter">
            
            <!-- General Information -->
            <div class="glass-card p-stack-lg rounded-xl shadow-sm">
              <div class="flex items-center gap-2 mb-6 text-primary">
                <span class="material-symbols-outlined">info</span>
                <h3 class="font-headline-md text-headline-md font-bold">Informasi Umum</h3>
              </div>
              <div class="space-y-6">
                <div class="grid grid-cols-1 gap-2">
                  <label class="font-label-md text-label-md text-on-surface-variant">Nama Reward</label>
                  <input 
                    class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-body-md text-body-md" 
                    name="nama_reward"
                    placeholder="Masukkan nama reward..." 
                    type="text" 
                    value="<?= htmlspecialchars($r['nama_reward']) ?>"
                    required
                  />
                </div>
              </div>
            </div>

            <!-- Inventory & Pricing -->
            <div class="glass-card p-stack-lg rounded-xl shadow-sm">
              <div class="flex items-center gap-2 mb-6 text-primary">
                <span class="material-symbols-outlined">payments</span>
                <h3 class="font-headline-md text-headline-md font-bold">Inventaris & Harga</h3>
              </div>
              <div class="grid grid-cols-2 gap-stack-lg">
                <div class="grid grid-cols-1 gap-2">
                  <label class="font-label-md text-label-md text-on-surface-variant">Poin Penukaran</label>
                  <div class="relative">
                    <input 
                      class="w-full pl-4 pr-12 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-body-md text-body-md" 
                      name="jumlah_poin"
                      type="number" 
                      value="<?= $r['jumlah_poin'] ?>"
                      min="1"
                      required
                    />
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-label-md text-label-md text-on-surface-variant">PTS</span>
                  </div>
                </div>
                <div class="grid grid-cols-1 gap-2">
                  <label class="font-label-md text-label-md text-on-surface-variant">Stok Tersedia</label>
                  <div class="relative">
                    <input 
                      class="w-full pl-4 pr-12 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-body-md text-body-md" 
                      name="stok"
                      type="number" 
                      value="<?= $r['stok'] ?>"
                      min="0"
                      required
                    />
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-label-md text-label-md text-on-surface-variant">PCS</span>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Right Column: Media & Actions -->
          <div class="col-span-12 lg:col-span-4 flex flex-col gap-gutter">
            
            <!-- Image Upload Preview -->
            <div class="glass-card p-stack-lg rounded-xl shadow-sm">
              <div class="flex items-center gap-2 mb-4 text-primary">
                <span class="material-symbols-outlined">image</span>
                <h3 class="font-headline-md text-headline-md font-bold">Media</h3>
              </div>
              <div class="relative group rounded-lg overflow-hidden border-2 border-dashed border-outline-variant aspect-square mb-4">
                <?php if (!empty($r['foto']) && file_exists('../uploads/reward/' . $r['foto'])): ?>
                  <img id="preview-image" class="w-full h-full object-cover" src="../uploads/reward/<?= htmlspecialchars($r['foto']) ?>" alt="Reward Image"/>
                <?php else: ?>
                  <img id="preview-image" class="hidden w-full h-full object-cover" alt="Preview"/>
                  <div id="preview-placeholder" class="w-full h-full flex flex-col items-center justify-center bg-surface-container">
                    <span class="material-symbols-outlined text-outline text-4xl">image</span>
                    <span class="text-outline font-label-sm mt-2">No Image</span>
                  </div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-on-surface/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 cursor-pointer">
                  <input type="file" name="foto" id="foto" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(event)"/>
                  <span class="material-symbols-outlined text-white text-4xl">cloud_upload</span>
                  <span class="text-white font-label-md text-label-md">Ganti Gambar</span>
                </div>
              </div>
              <p class="font-label-sm text-label-sm text-on-surface-variant text-center">Format: JPG, PNG, WEBP. Maks 2MB. Rekomendasi: 1000x1000px.</p>
            </div>

            <!-- Danger Zone -->
            <div class="bg-error-container/30 border border-error/20 p-stack-lg rounded-xl">
              <div class="flex items-center gap-2 mb-4 text-error">
                <span class="material-symbols-outlined">report</span>
                <h3 class="font-label-md text-label-md font-bold uppercase tracking-wider">Danger Zone</h3>
              </div>
              <p class="font-body-md text-body-md text-on-error-container mb-4">Menghapus reward ini akan menghilangkan datanya dari katalog secara permanen. Pengguna tidak akan bisa melihat atau menukar poin untuk item ini lagi.</p>
              <button type="button" onclick="confirmDelete(<?= $r['id'] ?>)" class="w-full py-3 bg-white border border-error text-error rounded-lg font-label-md text-label-md hover:bg-error hover:text-white transition-all active:scale-95 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                Hapus Reward
              </button>
            </div>

          </div>
        </div>
      </form>

    </main>

      <footer class="mt-auto py-4 lg:py-6 px-margin-mobile lg:px-margin-desktop border-t border-outline-variant bg-surface-container-lowest flex flex-col sm:flex-row justify-between items-center w-full gap-3">
        <p class="font-label-sm text-label-sm text-secondary text-center sm:text-left">© 2024 RewardSystem Enterprise. All rights reserved.</p>
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
          const previewImage = document.getElementById('preview-image');
          const placeholder = document.getElementById('preview-placeholder');
          
          if (placeholder) {
            placeholder.classList.add('hidden');
          }
          
          previewImage.src = e.target.result;
          previewImage.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
      }
    }

    function confirmDelete(id) {
      if (confirm('Apakah Anda yakin ingin menghapus reward ini? Tindakan ini tidak dapat dibatalkan.')) {
        window.location.href = 'hapus.php?id=' + id;
      }
    }
  </script>
</body>
</html>
