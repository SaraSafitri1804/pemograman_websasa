<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

$page_title = 'Tambah Transaksi';
$customers = $conn->query("SELECT id, id_customer, nama FROM customer ORDER BY nama ASC");

include '../includes/header.php';
?>

<style>
  /* Custom scrollbar */
  ::-webkit-scrollbar { width: 6px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: #e1e2ed; border-radius: 10px; }
  ::-webkit-scrollbar-thumb:hover { background: #c3c6d7; }
  
  .transition-scale {
    transition: transform 0.1s ease-out;
  }
  .scale-110 {
    transform: scale(1.1);
  }
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
  <?php include '../includes/topbar.php'; ?>

  <!-- Main Content Canvas -->
  <main class="mt-16 p-margin-mobile lg:p-margin-desktop max-w-[1200px] mx-auto">
    <!-- Breadcrumbs -->
    <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-on-surface-variant mb-6">
      <a class="text-body-md hover:text-primary transition-colors" href="../dashboard/dashboard.php">Dashboard</a>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <a class="text-body-md hover:text-primary transition-colors" href="index.php">Manajemen Transaksi</a>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-body-md font-semibold text-primary">Tambah Transaksi</span>
    </nav>

    <!-- Header -->
    <div class="mb-10">
      <h2 class="font-display-md text-display-md text-on-background mb-2">Tambah Transaksi Baru</h2>
      <p class="font-body-lg text-on-surface-variant">Catat transaksi belanja customer untuk menerbitkan poin loyalitas.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Form Card -->
      <div class="lg:col-span-2">
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-8">
          <form class="space-y-6" id="transaction-form" action="simpan.php" method="POST">
            <!-- Invoice Number -->
            <div class="space-y-2">
              <label class="font-label-md text-on-surface flex items-center gap-1" for="invoice">
                Nomor Invoice <span class="text-error">*</span>
              </label>
              <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-semibold text-on-surface-variant">#INV-</span>
                <input 
                  class="w-full pl-16 pr-4 py-3 bg-surface border border-outline-variant rounded-lg font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" 
                  id="invoice" 
                  name="invoice" 
                  placeholder="20231015001" 
                  required 
                  type="text"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Customer Selection -->
              <div class="space-y-2">
                <label class="font-label-md text-on-surface flex items-center gap-1" for="customer_id">
                  Customer <span class="text-error">*</span>
                </label>
                <div class="relative">
                  <select 
                    class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-lg font-body-md appearance-none focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" 
                    id="customer_id" 
                    name="customer_id" 
                    required
                  >
                    <option value="" disabled selected>Pilih Customer</option>
                    <?php while ($cust = $customers->fetch_assoc()): ?>
                      <option value="<?= $cust['id'] ?>">
                        <?= htmlspecialchars($cust['nama']) ?> (<?= htmlspecialchars($cust['id_customer']) ?>)
                      </option>
                    <?php endwhile; ?>
                  </select>
                  <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                </div>
              </div>

              <!-- Date Picker -->
              <div class="space-y-2">
                <label class="font-label-md text-on-surface flex items-center gap-1" for="tanggal">
                  Tanggal Transaksi <span class="text-error">*</span>
                </label>
                <div class="relative">
                  <input 
                    class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-lg font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" 
                    id="tanggal" 
                    name="tanggal" 
                    value="<?= date('Y-m-d') ?>"
                    required 
                    type="date"
                  />
                  <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant bg-surface px-1">calendar_today</span>
                </div>
              </div>
            </div>

            <!-- Total Purchase -->
            <div class="space-y-2">
              <label class="font-label-md text-on-surface flex items-center gap-1" for="total_belanja">
                Total Belanja <span class="text-error">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-semibold text-on-surface-variant">Rp</span>
                <input 
                  class="w-full pl-12 pr-4 py-3 bg-surface border border-outline-variant rounded-lg font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" 
                  id="total_belanja" 
                  name="total_belanja" 
                  placeholder="0" 
                  required 
                  type="number"
                  min="1"
                />
              </div>
              <p class="text-label-sm text-on-surface-variant">Masukkan total nominal sesuai yang tertera pada struk fisik.</p>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-outline-variant flex flex-col sm:flex-row items-center justify-end gap-4">
              <a href="index.php" class="px-6 py-2.5 font-label-md text-secondary hover:bg-surface-container-high rounded-lg transition-colors active:scale-95 w-full sm:w-auto text-center">
                Batal
              </a>
              <button 
                class="px-8 py-2.5 font-label-md bg-primary text-on-primary rounded-lg shadow-sm hover:shadow-md hover:bg-primary-container transition-all active:scale-95 flex items-center justify-center gap-2 w-full sm:w-auto" 
                type="submit"
              >
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Transaksi
              </button>
            </div>
          </form>
        </section>
      </div>

      <!-- Sidebar Information / Tips -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Info Card -->
        <div class="bg-secondary-container/30 rounded-xl border border-secondary-container p-6">
          <div class="flex items-center gap-3 mb-4 text-primary">
            <span class="material-symbols-outlined">info</span>
            <h3 class="font-label-md">Informasi Poin</h3>
          </div>
          <p class="font-body-md text-on-secondary-container mb-4">
            Poin loyalitas akan dihitung secara otomatis berdasarkan total belanja yang Anda masukkan.
          </p>
          <ul class="space-y-3">
            <li class="flex items-start gap-2 text-body-md text-on-secondary-container">
              <span class="material-symbols-outlined text-[16px] mt-0.5">check_circle</span>
              <span>Ratio: Rp 1.000 = 1 Poin</span>
            </li>
            <li class="flex items-start gap-2 text-body-md text-on-secondary-container">
              <span class="material-symbols-outlined text-[16px] mt-0.5">check_circle</span>
              <span>Poin akan langsung masuk ke saldo customer.</span>
            </li>
            <li class="flex items-start gap-2 text-body-md text-on-secondary-container">
              <span class="material-symbols-outlined text-[16px] mt-0.5">check_circle</span>
              <span>Invoice yang sudah disimpan tidak dapat dihapus.</span>
            </li>
          </ul>
        </div>

        <!-- Preview Card (Dynamic Micro-interaction) -->
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
          <h3 class="font-label-md text-on-surface mb-4">Estimasi Poin</h3>
          <div class="flex flex-col items-center justify-center py-6 border-2 border-dashed border-outline-variant rounded-lg">
            <span class="font-display-lg text-display-lg text-primary transition-scale" id="points-preview">0</span>
            <p class="font-label-sm text-on-surface-variant uppercase tracking-wider">Loyalty Points</p>
          </div>
        </div>

        <!-- Helper Illustration -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-outline-variant bg-surface-container-high h-48 relative">
          <div 
            class="w-full h-full bg-cover bg-center" 
            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuA8KsdsqdEfmE2my8YopsKdFvOAQcpOFmhGi59kjBWsKUFNHC9BxYxLbbOfH46Le8HNj-DCNKe4rHK7GEmK-B3kGQTsLdcEMMDSEBuM7oEavvyyKVzBR1I93wJ3t4gSnZ5dfCEW5Jg6-GWh8PCrVZnsmeO98mdfKdH0BIPydwFB2O88Lq96FAlVfjgfp6ug89IR0Yg40X5cE9qDBg_zMrVi3LcqmYF_wtys59sTQG_hr5eBqrrWPlMMsHwOGXcuJ4Tm_vyfAZ82eHU')"
          ></div>
          <div class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent"></div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Success Toast (Hidden by default) -->
<div 
  class="fixed bottom-8 right-8 bg-inverse-surface text-inverse-on-surface px-6 py-4 rounded-xl shadow-lg flex items-center gap-4 translate-y-20 opacity-0 transition-all duration-300 z-[100]" 
  id="success-toast"
>
  <span class="material-symbols-outlined text-primary-fixed">check_circle</span>
  <div>
    <p class="font-label-md">Transaksi Berhasil Disimpan</p>
    <p class="text-body-md text-surface-variant">Poin telah diterbitkan ke customer.</p>
  </div>
</div>

<script>
  // Micro-interaction: Calculate points in real-time
  const totalInput = document.getElementById('total_belanja');
  const pointsPreview = document.getElementById('points-preview');
  const form = document.getElementById('transaction-form');

  totalInput.addEventListener('input', (e) => {
    const value = parseFloat(e.target.value) || 0;
    const points = Math.floor(value / 1000); // Rp 1.000 = 1 Poin
    
    // Animate number change
    pointsPreview.classList.add('scale-110');
    setTimeout(() => {
      pointsPreview.textContent = points.toLocaleString('id-ID');
      pointsPreview.classList.remove('scale-110');
    }, 100);
  });

  // Set default date to today
  document.getElementById('tanggal').valueAsDate = new Date();
</script>

</body>
</html>
