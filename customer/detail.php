<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: index.php"); exit; }

// Judul halaman untuk TopNavBar
$page_title = "Detail Customer";

// Customer data
$stmt = $conn->prepare("SELECT * FROM customer WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$c) { header("Location: index.php"); exit; }

// Riwayat transaksi (perolehan poin)
$stmt2 = $conn->prepare("SELECT * FROM transaksi WHERE customer_id = ? ORDER BY tanggal DESC LIMIT 10");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$transaksi = $stmt2->get_result();
$stmt2->close();

// Riwayat penukaran reward
$stmt3 = $conn->prepare("SELECT pr.*, r.nama_reward FROM penukaran_reward pr JOIN reward r ON pr.reward_id = r.id WHERE pr.customer_id = ? ORDER BY pr.tanggal_tukar DESC LIMIT 10");
$stmt3->bind_param("i", $id);
$stmt3->execute();
$penukaran = $stmt3->get_result();
$stmt3->close();

// First letter for avatar fallback
$initial = strtoupper(substr($c['nama'], 0, 1));

// Hitung statistik
$total_transaksi = 0;
$total_belanja = 0;
$total_poin_masuk = 0;
$stmt_stats = $conn->prepare("SELECT COUNT(*) as jumlah, SUM(total_belanja) as total, SUM(poin_didapat) as poin FROM transaksi WHERE customer_id = ?");
$stmt_stats->bind_param("i", $id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();
$stmt_stats->close();

$total_transaksi = $stats['jumlah'] ?? 0;
$total_belanja = $stats['total'] ?? 0;
$total_poin_masuk = $stats['poin'] ?? 0;

// Hitung reward ditukar
$stmt_reward = $conn->prepare("SELECT COUNT(*) as jumlah, SUM(poin_digunakan) as poin FROM penukaran_reward WHERE customer_id = ?");
$stmt_reward->bind_param("i", $id);
$stmt_reward->execute();
$reward_stats = $stmt_reward->get_result()->fetch_assoc();
$stmt_reward->close();

$total_reward = $reward_stats['jumlah'] ?? 0;
$total_poin_keluar = $reward_stats['poin'] ?? 0;

// Tentukan tier berdasarkan poin
$tier = 'Silver';
$tier_bg = 'bg-gray-200';
$tier_text = 'text-gray-700';
if ($c['total_poin'] >= 5000) {
    $tier = 'Platinum';
    $tier_bg = 'bg-purple-100';
    $tier_text = 'text-purple-700';
} elseif ($c['total_poin'] >= 2000) {
    $tier = 'Gold';
    $tier_bg = 'bg-[#FEF08A]';
    $tier_text = 'text-[#854D0E]';
}

// Include header
require_once '../includes/header.php';
?>

<!-- Memanggil SideNavBar Terpisah -->
<?php require_once '../includes/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
  
  <!-- Memanggil TopNavBar Terpisah -->
  <?php require_once '../includes/topbar.php'; ?>

  <!-- Main Canvas -->
  <main class="flex-1 p-margin-mobile lg:p-margin-desktop overflow-y-auto mt-16">
    
    <!-- Flash Alert Notification -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 text-green-800 p-4 rounded-xl border border-green-200 font-medium flex items-center gap-2 shadow-sm mb-4">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Header Section Profile -->
    <div class="glass-card rounded-xl p-6 mb-gutter flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
      <div class="flex items-center">
        <div>
          <h2 class="text-display-md font-display-md text-on-surface flex items-center gap-3">
            <?= htmlspecialchars($c['nama']) ?>
            <span class="<?= $tier_bg ?> <?= $tier_text ?> text-label-sm font-label-sm px-3 py-1 rounded-full flex items-center gap-1">
              <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">workspace_premium</span> <?= $tier ?> Member
            </span>
          </h2>
          <div class="flex flex-wrap items-center gap-4 mt-2 text-on-surface-variant text-body-md font-body-md">
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">account_balance_wallet</span> <?= number_format($c['total_poin']) ?> pts</span>
            <span class="w-1 h-1 bg-outline rounded-full"></span>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">calendar_today</span> Since <?= date('M d, Y', strtotime($c['tanggal_daftar'])) ?></span>
          </div>
        </div>
      </div>
      <div class="flex gap-3 w-full md:w-auto">
        <a href="edit.php?id=<?= $c['id'] ?>" class="flex-1 md:flex-none px-4 py-2 border border-outline text-on-surface-variant rounded-lg text-label-md font-label-md hover:bg-surface-container transition-colors flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-[18px]">edit</span> Edit
        </a>
        <a href="index.php" class="flex-1 md:flex-none px-4 py-2 bg-primary-container text-on-primary rounded-lg text-label-md font-label-md hover:bg-primary transition-colors flex items-center justify-center gap-2 shadow-sm">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-gutter">
      <div class="glass-card rounded-xl p-5 flex flex-col gap-2">
        <span class="text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Total Belanja</span>
        <div class="flex items-end justify-between">
          <span class="text-headline-lg font-headline-lg text-on-surface">Rp <?= number_format($total_belanja / 1000000, 1) ?>M</span>
          <span class="bg-[#DCFCE7] text-[#166534] text-label-sm px-2 py-0.5 rounded flex items-center gap-1">
            <span class="material-symbols-outlined text-[12px]">receipt</span> <?= $total_transaksi ?> Transaksi
          </span>
        </div>
      </div>
      <div class="glass-card rounded-xl p-5 flex flex-col gap-2">
        <span class="text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Reward Ditukar</span>
        <div class="flex items-end justify-between">
          <span class="text-headline-lg font-headline-lg text-on-surface"><?= $total_reward ?> items</span>
          <span class="text-body-md font-body-md text-outline"><?= $total_reward > 0 ? 'Aktif menukar' : 'Belum ada' ?></span>
        </div>
      </div>
      <div class="glass-card rounded-xl p-5 flex flex-col gap-3">
        <span class="text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Total Poin Masuk</span>
        <span class="text-headline-lg font-headline-lg text-on-surface"><?= number_format($total_poin_masuk) ?> pts</span>
        <div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
          <?php $poin_progress = min(100, ($total_poin_masuk / 10000) * 100); ?>
          <div class="bg-primary-container h-full rounded-full" style="width: <?= $poin_progress ?>%"></div>
        </div>
      </div>
      <div class="glass-card rounded-xl p-5 flex flex-col gap-3">
        <span class="text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Total Poin Keluar</span>
        <div class="flex items-center justify-between">
          <span class="text-headline-lg font-headline-lg text-on-surface"><?= number_format($total_poin_keluar) ?> pts</span>
          <div class="w-16 h-8 flex items-center justify-end">
            <svg class="stroke-[#943700] fill-none w-full h-full stroke-2" preserveAspectRatio="none" viewBox="0 0 100 30">
              <path d="M0,25 C20,25 30,10 50,15 C70,20 80,5 100,10"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-gutter">
      <!-- Left Column (Main Content) -->
      <div class="xl:col-span-2 flex flex-col gap-gutter">
        
        <!-- Transaction Table -->
        <div class="glass-card rounded-xl overflow-hidden">
          <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center">
            <h3 class="text-headline-md font-headline-md text-on-surface">Riwayat Transaksi</h3>
            <a class="text-primary-container text-label-md font-label-md hover:underline" href="#">View All</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-surface-container-lowest text-outline text-body-md font-body-md font-semibold">
                  <th class="px-6 py-3 font-medium">ID Transaksi</th>
                  <th class="px-6 py-3 font-medium">Tanggal</th>
                  <th class="px-6 py-3 font-medium">Produk</th>
                  <th class="px-6 py-3 font-medium text-right">Total (Rp)</th>
                  <th class="px-6 py-3 font-medium text-right">Poin</th>
                </tr>
              </thead>
              <tbody class="text-body-md font-body-md text-on-surface">
                <?php if ($transaksi->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant">Belum ada riwayat transaksi.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($t = $transaksi->fetch_assoc()): ?>
                        <tr class="border-b border-surface-container-high hover:bg-surface-container-low transition-colors group">
                            <td class="px-6 py-4 font-medium">#<?= htmlspecialchars($t['invoice']) ?></td>
                            <td class="px-6 py-4 text-on-surface-variant"><?= date('d M Y', strtotime($t['tanggal'])) ?></td>
                            <td class="px-6 py-4">Pembelian Produk</td>
                            <td class="px-6 py-4 text-right"><?= number_format($t['total_belanja'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4 text-right text-[#166534] font-medium">+<?= number_format($t['poin_didapat']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Reward Redemption Table -->
        <div class="glass-card rounded-xl overflow-hidden mb-gutter">
          <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center">
            <h3 class="text-headline-md font-headline-md text-on-surface">Riwayat Penukaran Reward</h3>
            <a class="text-primary-container text-label-md font-label-md hover:underline" href="#">View All</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-surface-container-lowest text-outline text-body-md font-body-md font-semibold">
                  <th class="px-6 py-3 font-medium">ID Penukaran</th>
                  <th class="px-6 py-3 font-medium">Tanggal</th>
                  <th class="px-6 py-3 font-medium">Item Reward</th>
                  <th class="px-6 py-3 font-medium">Status</th>
                </tr>
              </thead>
              <tbody class="text-body-md font-body-md text-on-surface">
                <?php if ($penukaran->num_rows === 0): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">Belum ada riwayat penukaran.</td>
                    </tr>
                <?php else: ?>
                    <?php $redemption_counter = 1; ?>
                    <?php while ($pr = $penukaran->fetch_assoc()): ?>
                        <tr class="border-b border-surface-container-high hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-4 font-medium">#RDW-<?= str_pad($pr['id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td class="px-6 py-4 text-on-surface-variant"><?= date('d M Y', strtotime($pr['tanggal_tukar'])) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($pr['nama_reward']) ?></td>
                            <td class="px-6 py-4">
                                <?php 
                                $status = strtolower($pr['status']);
                                $statusText = ucfirst($pr['status']);
                                $badgeClass = "bg-[#FEF08A] text-[#854D0E]";
                                if ($status === 'completed' || $status === 'sukses' || $status === 'selesai') {
                                    $badgeClass = "bg-[#DCFCE7] text-[#166534]";
                                    $statusText = "Completed";
                                } elseif ($status === 'pending' || $status === 'diproses') {
                                    $badgeClass = "bg-[#FEF08A] text-[#854D0E]";
                                    $statusText = "Pending";
                                }
                                ?>
                                <span class="<?= $badgeClass ?> px-2.5 py-1 rounded-full text-label-sm font-label-sm"><?= $statusText ?></span>
                            </td>
                        </tr>
                        <?php $redemption_counter++; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Right Column (Sidebar info) -->
      <div class="flex flex-col gap-gutter">
        <!-- Customer Info Card -->
        <div class="glass-card rounded-xl p-6">
          <h3 class="text-headline-md font-headline-md text-on-surface mb-4">Customer Info</h3>
          <div class="flex flex-col gap-4 text-body-md font-body-md">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-outline mt-0.5">mail</span>
              <div>
                <p class="text-outline text-label-sm font-label-sm mb-0.5">Email</p>
                <p class="text-on-surface"><?= htmlspecialchars($c['email'] ?: '-') ?></p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-outline mt-0.5">call</span>
              <div>
                <p class="text-outline text-label-sm font-label-sm mb-0.5">Phone</p>
                <p class="text-on-surface"><?= htmlspecialchars($c['no_hp'] ?: '-') ?></p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-outline mt-0.5">location_on</span>
              <div>
                <p class="text-outline text-label-sm font-label-sm mb-0.5">Address</p>
                <p class="text-on-surface"><?= htmlspecialchars($c['alamat'] ?: 'Alamat belum diisi') ?></p>
              </div>
            </div>
            <div class="flex items-start gap-3 pt-2 border-t border-surface-container-high">
              <span class="material-symbols-outlined text-outline mt-0.5">verified_user</span>
              <div class="flex items-center gap-2">
                <p class="text-on-surface">Account Status</p>
                <span class="bg-[#DCFCE7] text-[#166534] px-2 py-0.5 rounded text-label-sm font-label-sm">Active</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Point Timeline -->
        <div class="glass-card rounded-xl p-6">
          <h3 class="text-headline-md font-headline-md text-on-surface mb-6">Riwayat Poin</h3>
          <div class="relative border-l border-surface-variant ml-3 space-y-6">
            <?php 
            // Gabungkan transaksi dan penukaran untuk timeline
            $timeline = [];
            
            // Reset pointer transaksi
            $transaksi->data_seek(0);
            while ($t = $transaksi->fetch_assoc()) {
                $timeline[] = [
                    'type' => 'gain',
                    'date' => $t['tanggal'],
                    'title' => 'Belanja ' . $t['invoice'],
                    'points' => $t['poin_didapat']
                ];
            }
            
            // Reset pointer penukaran
            $penukaran->data_seek(0);
            while ($pr = $penukaran->fetch_assoc()) {
                $timeline[] = [
                    'type' => 'redeem',
                    'date' => $pr['tanggal_tukar'],
                    'title' => 'Tukar ' . $pr['nama_reward'],
                    'points' => $pr['poin_digunakan']
                ];
            }
            
            // Sort by date descending
            usort($timeline, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            // Show max 4 recent activities
            $timeline = array_slice($timeline, 0, 4);
            ?>
            
            <?php if (empty($timeline)): ?>
                <p class="text-on-surface-variant text-body-md pl-6">Belum ada aktivitas poin.</p>
            <?php else: ?>
                <?php foreach ($timeline as $idx => $item): ?>
                    <div class="relative pl-6">
                      <div class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full <?= $idx === 0 ? 'bg-primary-container' : 'bg-surface-variant' ?> border-2 border-white"></div>
                      <div class="flex justify-between items-start">
                        <div>
                          <p class="text-body-md font-body-md font-medium text-on-surface"><?= htmlspecialchars($item['title']) ?></p>
                          <p class="text-label-sm font-label-sm text-outline mt-1"><?= date('d M Y, H:i', strtotime($item['date'])) ?></p>
                        </div>
                        <span class="text-label-md font-label-md <?= $item['type'] === 'gain' ? 'text-[#166534]' : 'text-[#991B1B]' ?>">
                          <?= $item['type'] === 'gain' ? '+' : '-' ?><?= number_format($item['points']) ?>
                        </span>
                      </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <?php if (count($timeline) > 0): ?>
              <button class="w-full mt-6 py-2 border border-outline text-on-surface-variant rounded-lg text-label-md font-label-md hover:bg-surface-container transition-colors">Load More History</button>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </main>

  <!-- Footer -->
  <footer class="w-full py-4 lg:py-6 mt-auto bg-surface-container-lowest border-t border-outline-variant flex flex-col md:flex-row justify-between items-center px-margin-mobile lg:px-8 gap-4">
    <span class="text-label-md font-label-md text-on-surface-variant text-center md:text-left">© 2024 Enterprise Reward System. All rights reserved.</span>
    <div class="flex gap-4">
      <a class="text-body-md font-body-md text-secondary hover:text-primary transition-colors" href="#">Privacy Policy</a>
      <a class="text-body-md font-body-md text-secondary hover:text-primary transition-colors" href="#">Terms of Service</a>
      <a class="text-body-md font-body-md text-secondary hover:text-primary transition-colors" href="#">Help Center</a>
    </div>
  </footer>
</div>

</body>
</html>