<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

$page_title = 'Tambah Penukaran Reward';

$customers = $conn->query("SELECT id, id_customer, nama, total_poin FROM customer ORDER BY nama ASC");
$rewards = $conn->query("SELECT id, nama_reward, jumlah_poin, stok FROM reward WHERE stok > 0 ORDER BY nama_reward ASC");

// Build JSON for JS
$reward_data = [];
$rewards_copy = $conn->query("SELECT id, nama_reward, jumlah_poin, stok FROM reward WHERE stok > 0 ORDER BY nama_reward ASC");
while ($rw = $rewards_copy->fetch_assoc()) {
    $reward_data[] = $rw;
}

$customer_data = [];
$customers_copy = $conn->query("SELECT id, id_customer, nama, total_poin FROM customer ORDER BY nama ASC");
while ($cw = $customers_copy->fetch_assoc()) {
    $customer_data[] = $cw;
}

include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
  <?php include '../includes/topbar.php'; ?>

  <!-- Page Content -->
  <main class="flex-1 mt-16 p-margin-mobile lg:p-margin-desktop max-w-container-max mx-auto w-full pb-8">
    <div class="mb-8">
      <h2 class="font-headline-lg text-headline-lg font-bold text-on-surface">Tambah Penukaran Reward</h2>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">Proses penukaran poin reward untuk customer.</p>
    </div>

    <form action="simpan.php" method="POST" id="formTukar">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        <!-- Form Section -->
        <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-sm p-6">
          <h3 class="font-headline-md text-headline-md font-semibold text-on-surface mb-6 border-b border-outline-variant pb-4">
            Detail Penukaran
          </h3>
          
          <div class="space-y-6">
            <!-- Customer Selection -->
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-2">Pilih Customer</label>
              <div class="relative">
                <select 
                  name="customer_id"
                  id="customerSelect"
                  required
                  class="w-full appearance-none bg-surface border border-outline-variant rounded-md py-3 px-4 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-shadow"
                >
                  <option value="" disabled selected>Cari dan pilih customer...</option>
                  <?php while ($cust = $customers->fetch_assoc()): ?>
                    <option value="<?= $cust['id'] ?>" data-poin="<?= $cust['total_poin'] ?>">
                      <?= htmlspecialchars($cust['nama']) ?> - <?= htmlspecialchars($cust['id_customer']) ?> (<?= number_format($cust['total_poin'], 0, ',', '.') ?> Pts)
                    </option>
                  <?php endwhile; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-on-surface-variant">
                  <span class="material-symbols-outlined">expand_more</span>
                </div>
              </div>
            </div>

            <!-- Reward Selection -->
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-2">Pilih Reward</label>
              <div class="relative">
                <select 
                  name="reward_id"
                  id="rewardSelect"
                  required
                  class="w-full appearance-none bg-surface border border-outline-variant rounded-md py-3 px-4 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-shadow"
                >
                  <option value="" disabled selected>Pilih reward yang tersedia...</option>
                  <?php while ($rw = $rewards->fetch_assoc()): ?>
                    <option value="<?= $rw['id'] ?>" data-poin="<?= $rw['jumlah_poin'] ?>" data-stok="<?= $rw['stok'] ?>">
                      <?= htmlspecialchars($rw['nama_reward']) ?> (<?= number_format($rw['jumlah_poin'], 0, ',', '.') ?> Pts, Stok: <?= $rw['stok'] ?>)
                    </option>
                  <?php endwhile; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-on-surface-variant">
                  <span class="material-symbols-outlined">expand_more</span>
                </div>
              </div>
            </div>

            <!-- Date Picker -->
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-2">Tanggal Penukaran</label>
              <div class="relative">
                <input 
                  type="date"
                  name="tanggal_tukar"
                  value="<?= date('Y-m-d') ?>"
                  required
                  class="w-full bg-surface border border-outline-variant rounded-md py-3 px-4 font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-shadow"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Side Panel -->
        <div class="space-y-6">
          <!-- Points Summary -->
          <div class="bg-surface-container-lowest border border-outline-variant rounded-lg shadow-sm p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-5">
              <span class="material-symbols-outlined text-[120px]">stars</span>
            </div>
            
            <h3 class="font-headline-md text-headline-md font-semibold text-on-surface mb-6">Ringkasan Poin</h3>
            
            <div class="space-y-4 relative z-10">
              <div class="flex justify-between items-center pb-4 border-b border-outline-variant">
                <span class="font-body-md text-body-md text-on-surface-variant">Saldo Poin Saat Ini</span>
                <span class="font-label-md text-label-md text-on-surface font-bold" id="saldoPoinAwal">0 Pts</span>
              </div>
              
              <div class="flex justify-between items-center pb-4 border-b border-outline-variant">
                <span class="font-body-md text-body-md text-on-surface-variant">Biaya Penukaran</span>
                <span class="font-label-md text-label-md text-error font-bold" id="biayaPenukaran">- 0 Pts</span>
              </div>
              
              <div class="flex justify-between items-center pt-2">
                <span class="font-body-md text-body-md text-on-surface font-semibold">Sisa Poin Estimasi</span>
                <span class="font-headline-md text-headline-md text-primary font-bold" id="sisaPoin">0 Pts</span>
              </div>
            </div>

            <div id="statusPoin" class="mt-6 hidden">
              <!-- Status will be injected by JS -->
            </div>
          </div>

          <!-- Information Box -->
          <div class="bg-surface-container border border-outline-variant rounded-lg p-5">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-secondary mt-0.5">info</span>
              <div>
                <h4 class="font-label-md text-label-md font-bold text-on-surface mb-1">Informasi Penukaran</h4>
                <p class="font-body-md text-body-md text-on-surface-variant text-sm">
                  Pastikan data customer dan reward sudah benar. Proses penukaran yang sudah dikonfirmasi tidak dapat dibatalkan atau dikembalikan poinnya secara otomatis.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-8 flex flex-col sm:flex-row justify-end gap-4 border-t border-outline-variant pt-6">
        <a href="index.php" class="px-6 py-2.5 border border-outline font-label-md text-label-md font-semibold text-secondary rounded-md hover:bg-surface-container-low hover:text-on-surface transition-colors w-full sm:w-auto text-center">
          Batal
        </a>
        <button 
          type="submit"
          id="btnSubmit"
          class="px-6 py-2.5 bg-primary font-label-md text-label-md font-semibold text-on-primary rounded-md hover:bg-surface-tint shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 w-full sm:w-auto"
        >
          <span class="material-symbols-outlined text-[18px]">save</span>
          Simpan Penukaran
        </button>
      </div>
    </form>
  </main>
</div>

<script>
  const customerData = <?= json_encode($customer_data) ?>;
  const rewardData = <?= json_encode($reward_data) ?>;

  const customerSelect = document.getElementById('customerSelect');
  const rewardSelect = document.getElementById('rewardSelect');
  const btnSubmit = document.getElementById('btnSubmit');
  
  const saldoPoinAwal = document.getElementById('saldoPoinAwal');
  const biayaPenukaran = document.getElementById('biayaPenukaran');
  const sisaPoin = document.getElementById('sisaPoin');
  const statusPoin = document.getElementById('statusPoin');

  function updateInfo() {
    const custId = customerSelect.value;
    const rewId = rewardSelect.value;

    let customerPoin = 0;
    let rewardPoin = 0;

    // Get customer points
    if (custId) {
      const cust = customerData.find(c => c.id == custId);
      if (cust) {
        customerPoin = parseInt(cust.total_poin) || 0;
        saldoPoinAwal.textContent = customerPoin.toLocaleString('id-ID') + ' Pts';
      }
    }

    // Get reward points
    if (rewId) {
      const rew = rewardData.find(r => r.id == rewId);
      if (rew) {
        rewardPoin = parseInt(rew.jumlah_poin) || 0;
        biayaPenukaran.textContent = '- ' + rewardPoin.toLocaleString('id-ID') + ' Pts';
      }
    }

    // Calculate remaining points
    const remaining = customerPoin - rewardPoin;
    sisaPoin.textContent = remaining.toLocaleString('id-ID') + ' Pts';

    // Show status
    if (custId && rewId) {
      statusPoin.classList.remove('hidden');
      
      if (customerPoin >= rewardPoin && rewardPoin > 0) {
        statusPoin.innerHTML = `
          <div class="flex items-center gap-2 text-on-secondary-container bg-secondary-container/30 px-3 py-2 rounded-md">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span class="font-label-sm text-label-sm">Saldo mencukupi untuk penukaran ini.</span>
          </div>
        `;
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
      } else {
        statusPoin.innerHTML = `
          <div class="flex items-center gap-2 text-error bg-error-container px-3 py-2 rounded-md">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <span class="font-label-sm text-label-sm">Saldo tidak mencukupi!</span>
          </div>
        `;
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
      }
    } else {
      statusPoin.classList.add('hidden');
      btnSubmit.disabled = false;
      btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  }

  customerSelect.addEventListener('change', updateInfo);
  rewardSelect.addEventListener('change', updateInfo);

  // Initial update
  updateInfo();
</script>

</body>
</html>
