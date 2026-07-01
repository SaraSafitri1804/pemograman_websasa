<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

// Set page title untuk topbar
$page_title = 'Manajemen Transaksi';

// Pagination
$per_page = 10;
$page_num = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page_num - 1) * $per_page;

// Hitung total transaksi
$total = $conn->query("SELECT COUNT(*) as t FROM transaksi")->fetch_assoc()['t'];
$total_pages = max(1, ceil($total / $per_page));

// Get transaksi dengan join customer
$stmt = $conn->prepare("SELECT t.*, c.nama, c.id_customer FROM transaksi t JOIN customer c ON t.customer_id = c.id ORDER BY t.tanggal DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$rows = $stmt->get_result();
$stmt->close();

// Statistik bulan ini
$bulan_ini = date('Y-m');
$stats_query = $conn->query("
    SELECT 
        COUNT(*) as total_transaksi,
        COALESCE(SUM(total_belanja), 0) as total_belanja,
        COALESCE(SUM(poin_didapat), 0) as total_poin
    FROM transaksi 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'
");
$stats = $stats_query->fetch_assoc();

// Jika tidak ada data bulan ini, set default
if (!$stats || $stats['total_transaksi'] == 0) {
    $stats = [
        'total_transaksi' => 0,
        'total_belanja' => 0,
        'total_poin' => 0
    ];
}

// Include header
require_once '../includes/header.php';
?>

<body class="bg-background text-on-surface antialiased min-h-screen flex">

  <?php require_once '../includes/sidebar.php'; ?>

  <div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
    
    <?php require_once '../includes/topbar.php'; ?>

    <main class="flex-1 pt-24 px-margin-mobile lg:px-margin-desktop pb-8 max-w-container-max mx-auto w-full">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
        <div>
          <nav class="flex items-center gap-2 text-on-surface-variant mb-1">
            <span class="text-label-md font-label-md">Dashboard</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-label-md font-label-md text-primary">Manajemen Transaksi</span>
          </nav>
          <h2 class="font-display-md text-display-md text-on-surface">Manajemen Transaksi</h2>
        </div>
        <a href="tambah.php" class="bg-primary text-on-primary px-6 py-2.5 rounded-lg flex items-center gap-2 font-body-md hover:shadow-lg transition-all active:scale-[0.98] w-full sm:w-auto justify-center">
          <span class="material-symbols-outlined">add</span>
          Tambah Transaksi
        </a>
      </div>

      <!-- Alert Messages -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 text-green-800 p-4 rounded-xl border border-green-200 font-medium flex items-center gap-2 shadow-sm mb-6">
          <span class="material-symbols-outlined text-green-600">check_circle</span>
          <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 text-red-800 p-4 rounded-xl border border-red-200 font-medium flex items-center gap-2 shadow-sm mb-6">
          <span class="material-symbols-outlined text-red-600">error</span>
          <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-8">
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)]">
          <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Transaksi (Bulan Ini)</p>
          <div class="mt-2 flex items-baseline gap-2">
            <span class="font-display-md text-display-md text-on-surface"><?= number_format($stats['total_transaksi']) ?></span>
            <span class="text-emerald-600 text-label-md font-bold flex items-center">
              <span class="material-symbols-outlined text-[16px]">trending_up</span>
            </span>
          </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)]">
          <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Nilai Transaksi</p>
          <div class="mt-2 flex items-baseline gap-2">
            <?php 
            $total_belanja = $stats['total_belanja'];
            if ($total_belanja >= 1000000) {
                $display = 'Rp ' . number_format($total_belanja / 1000000, 1) . 'M';
            } elseif ($total_belanja >= 1000) {
                $display = 'Rp ' . number_format($total_belanja / 1000, 0) . 'K';
            } else {
                $display = 'Rp ' . number_format($total_belanja, 0);
            }
            ?>
            <span class="font-display-md text-display-md text-on-surface"><?= $display ?></span>
            <span class="text-emerald-600 text-label-md font-bold flex items-center">
              <span class="material-symbols-outlined text-[16px]">trending_up</span>
            </span>
          </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)]">
          <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Poin Diterbitkan</p>
          <div class="mt-2 flex items-baseline gap-2">
            <span class="font-display-md text-display-md text-on-surface"><?= number_format($stats['total_poin']) ?> Pts</span>
            <span class="text-emerald-600 text-label-md font-bold flex items-center">
              <span class="material-symbols-outlined text-[16px]">trending_up</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Transaction Table Section -->
      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
          <h3 class="font-headline-md text-headline-md">Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-lowest text-on-surface-variant">
                <th class="px-6 py-4 font-label-md text-label-md uppercase tracking-wider border-b border-outline-variant">Invoice</th>
                <th class="px-6 py-4 font-label-md text-label-md uppercase tracking-wider border-b border-outline-variant">Customer</th>
                <th class="px-6 py-4 font-label-md text-label-md uppercase tracking-wider border-b border-outline-variant">Tanggal</th>
                <th class="px-6 py-4 font-label-md text-label-md uppercase tracking-wider border-b border-outline-variant">Total Belanja</th>
                <th class="px-6 py-4 font-label-md text-label-md uppercase tracking-wider border-b border-outline-variant">Poin Didapat</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30">
              <?php if ($rows->num_rows === 0): ?>
                <tr>
                  <td colspan="5" class="px-6 py-10 text-center font-body-md text-on-surface-variant">Belum ada data transaksi.</td>
                </tr>
              <?php else: ?>
                <?php while ($r = $rows->fetch_assoc()): ?>
                  <tr class="hover:bg-surface-container-low transition-colors group">
                    <td class="px-6 py-4">
                      <span class="font-body-md font-semibold text-primary">#<?= htmlspecialchars($r['invoice']) ?></span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <span class="font-body-md text-on-surface font-medium"><?= htmlspecialchars($r['nama']) ?></span>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="font-body-md text-on-surface-variant"><?= date('d M Y', strtotime($r['tanggal'])) ?></span>
                    </td>
                    <td class="px-6 py-4 font-body-md text-on-surface font-semibold">
                      Rp <?= number_format($r['total_belanja'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-label-md bg-emerald-100 text-emerald-800">
                        +<?= number_format($r['poin_didapat']) ?> Pts
                      </span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 lg:px-6 py-4 bg-surface border-t border-outline-variant flex flex-col sm:flex-row justify-between items-center gap-3">
          <p class="font-label-md text-label-md text-on-surface-variant text-center sm:text-left">
            Menampilkan <?= $offset + 1 ?> - <?= min($offset + $per_page, $total) ?> dari <?= number_format($total) ?> transaksi
          </p>
          <div class="flex gap-1">
            <a href="?page=<?= max(1, $page_num - 1) ?>" class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-surface text-on-surface-variant <?= $page_num <= 1 ? 'opacity-50 pointer-events-none' : 'hover:bg-surface-container' ?>">
              <span class="material-symbols-outlined text-sm">chevron_left</span>
            </a>
            
            <?php
            $start_page = max(1, $page_num - 1);
            $end_page = min($total_pages, $page_num + 1);
            
            for ($p = $start_page; $p <= $end_page; $p++):
            ?>
              <a href="?page=<?= $p ?>" class="w-8 h-8 flex items-center justify-center rounded border <?= $p === $page_num ? 'border-primary bg-primary text-on-primary' : 'border-outline-variant bg-surface text-on-surface-variant hover:bg-surface-container' ?> font-body-md">
                <?= $p ?>
              </a>
            <?php endfor; ?>
            
            <a href="?page=<?= min($total_pages, $page_num + 1) ?>" class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-surface text-on-surface-variant <?= $page_num >= $total_pages ? 'opacity-50 pointer-events-none' : 'hover:bg-surface-container' ?>">
              <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Informational Card -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mt-8">
        <div class="lg:col-span-2 bg-primary-fixed p-8 rounded-2xl relative overflow-hidden flex flex-col justify-center min-h-[200px]">
          <div class="z-10">
            <h4 class="font-display-md text-display-md text-on-primary-fixed mb-2">Monitor Poin Otomatis</h4>
            <p class="font-body-lg text-on-primary-fixed opacity-80 max-w-md">
              Aktifkan verifikasi transaksi cerdas untuk meminimalisir kesalahan input data dan fraud dalam sistem reward Anda.
            </p>
          </div>
          <div class="absolute right-[-40px] top-[-40px] w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
          <div class="absolute right-4 bottom-4">
            <span class="material-symbols-outlined text-[120px] text-primary/10" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
          </div>
        </div>
      </div>

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

</body>
</html>
