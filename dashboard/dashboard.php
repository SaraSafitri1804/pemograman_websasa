<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

// Set page variables
$page_title = 'Dashboard';
$include_chartjs = true;

// Query Data (Sesuai kode awal Anda)
$total_customer = $conn->query("SELECT COUNT(*) as total FROM customer")->fetch_assoc()['total'];
$total_poin = $conn->query("SELECT SUM(total_poin) as total FROM customer")->fetch_assoc()['total'] ?? 0;
$gold = $conn->query("SELECT COUNT(*) as total FROM customer WHERE tier = 'Gold'")->fetch_assoc()['total'];
$silver = $conn->query("SELECT COUNT(*) as total FROM customer WHERE tier = 'Silver'")->fetch_assoc()['total'];
$bronze = $conn->query("SELECT COUNT(*) as total FROM customer WHERE tier = 'Bronze'")->fetch_assoc()['total'];

$q_top_customers = $conn->query("SELECT nama, total_poin, tier FROM customer ORDER BY total_poin DESC LIMIT 5");
$q_top_rewards = $conn->query("
  SELECT r.nama_reward, r.jumlah_poin, COUNT(p.id) as total_klaim 
  FROM penukaran_reward p
  JOIN reward r ON p.reward_id = r.id
  GROUP BY p.reward_id
  ORDER BY total_klaim DESC
  LIMIT 3
");
$q_recent_trans = $conn->query("
  SELECT t.id, t.tanggal, t.poin_didapat, c.nama 
  FROM transaksi t 
  JOIN customer c ON t.customer_id = c.id 
  ORDER BY t.tanggal DESC 
  LIMIT 5
");

// Data untuk grafik statistik sistem (7 hari terakhir)
$stat_labels = [];
$stat_transaksi = [];
$stat_penukaran = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_label = date('d M', strtotime("-$i days"));
    $stat_labels[] = $date_label;
    
    // Count transaksi per hari
    $transaksi_count = $conn->query("SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = '$date'")->fetch_assoc()['total'];
    $stat_transaksi[] = $transaksi_count;
    
    // Count penukaran per hari
    $penukaran_count = $conn->query("SELECT COUNT(*) as total FROM penukaran_reward WHERE DATE(tanggal_tukar) = '$date'")->fetch_assoc()['total'];
    $stat_penukaran[] = $penukaran_count;
}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<!-- Main Content Wrapper -->
<div class="w-full lg:ml-[280px] lg:w-[calc(100%-280px)] min-h-screen flex flex-col relative transition-all duration-300">
  <?php require_once '../includes/topbar.php'; ?>

  <!-- Canvas (Main Content) -->
  <main class="flex-1 mt-16 p-margin-mobile lg:p-margin-desktop max-w-container-max mx-auto w-full pb-24">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-stack-lg gap-4">
      <div>
        <h2 class="font-headline-lg text-headline-lg text-on-background">Halo, Admin 👋</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Selamat datang kembali. Berikut ringkasan performa sistem hari ini.</p>
      </div>
      <div class="flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-sm">
        <span class="material-symbols-outlined text-on-surface-variant text-[20px]">calendar_today</span>
        <span class="font-label-md text-label-md text-on-background"><?= date('d M Y') ?></span>
      </div>
    </div>

    <!-- Bento Grid: Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 lg:gap-gutter mb-stack-lg">
      <!-- Stat 1 -->
      <div class="bg-surface-container-lowest rounded-xl p-stack-lg border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 hover:border-primary/30 transition-all duration-300 flex flex-col justify-between h-full min-h-[140px] group cursor-pointer relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="flex justify-between items-start mb-2 relative z-10">
          <span class="font-label-md text-label-md text-on-surface-variant group-hover:text-primary transition-colors">Total Customer</span>
          <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors text-primary">
            <span class="material-symbols-outlined text-[20px]">group</span>
          </div>
        </div>
        <div class="relative z-10">
          <div class="font-display-md text-display-md text-on-background mb-2">
            <?= number_format($total_customer) ?>
          </div>
          <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px] text-emerald-500">trending_up</span>
            registered users
          </div>
        </div>
      </div>

      <!-- Stat 2 -->
      <div class="bg-surface-container-lowest rounded-xl p-stack-lg border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 hover:border-error/30 transition-all duration-300 flex flex-col justify-between h-full min-h-[140px] group cursor-pointer relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-error/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="flex justify-between items-start mb-2 relative z-10">
          <span class="font-label-md text-label-md text-on-surface-variant group-hover:text-error transition-colors">Poin Beredar</span>
          <div class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center group-hover:bg-error group-hover:text-white transition-colors text-error">
            <span class="material-symbols-outlined text-[20px]">stars</span>
          </div>
        </div>
        <div class="relative z-10">
          <div class="font-display-md text-display-md text-on-background mb-2">
            <?= number_format($total_poin) ?>
          </div>
          <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px] text-error">analytics</span>
            active points
          </div>
        </div>
      </div>

      <!-- Stat 3 (Gold) -->
      <div class="bg-surface-container-lowest rounded-xl p-stack-lg border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 hover:border-[#facc15]/30 transition-all duration-300 flex flex-col justify-between h-full min-h-[140px] group cursor-pointer relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-[#facc15]/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="flex justify-between items-start mb-2 relative z-10">
          <span class="font-label-md text-label-md text-on-surface-variant group-hover:text-[#ca8a04] transition-colors">Gold Member</span>
          <div class="w-10 h-10 rounded-full bg-[#facc15]/10 flex items-center justify-center group-hover:bg-[#facc15] group-hover:text-white transition-colors text-[#facc15]">
            <span class="material-symbols-outlined text-[20px]">military_tech</span>
          </div>
        </div>
        <div class="relative z-10">
          <div class="font-display-md text-display-md text-on-background mb-2">
            <?= number_format($gold) ?>
          </div>
          <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px] text-[#facc15]">workspace_premium</span>
            highest tier
          </div>
        </div>
      </div>

      <!-- Stat 4 (Silver) -->
      <div class="bg-surface-container-lowest rounded-xl p-stack-lg border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 hover:border-[#94a3b8]/30 transition-all duration-300 flex flex-col justify-between h-full min-h-[140px] group cursor-pointer relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-[#94a3b8]/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="flex justify-between items-start mb-2 relative z-10">
          <span class="font-label-md text-label-md text-on-surface-variant group-hover:text-[#64748b] transition-colors">Silver Member</span>
          <div class="w-10 h-10 rounded-full bg-[#94a3b8]/10 flex items-center justify-center group-hover:bg-[#94a3b8] group-hover:text-white transition-colors text-[#94a3b8]">
            <span class="material-symbols-outlined text-[20px]">military_tech</span>
          </div>
        </div>
        <div class="relative z-10">
          <div class="font-display-md text-display-md text-on-background mb-2">
            <?= number_format($silver) ?>
          </div>
          <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px] text-[#94a3b8]">workspace_premium</span>
            middle tier
          </div>
        </div>
      </div>

      <!-- Stat 5 (Bronze) -->
      <div class="bg-surface-container-lowest rounded-xl p-stack-lg border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 hover:border-[#fb923c]/30 transition-all duration-300 flex flex-col justify-between h-full min-h-[140px] group cursor-pointer relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-[#fb923c]/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="flex justify-between items-start mb-2 relative z-10">
          <span class="font-label-md text-label-md text-on-surface-variant group-hover:text-[#ea580c] transition-colors">Bronze Member</span>
          <div class="w-10 h-10 rounded-full bg-[#fb923c]/10 flex items-center justify-center group-hover:bg-[#fb923c] group-hover:text-white transition-colors text-[#fb923c]">
            <span class="material-symbols-outlined text-[20px]">military_tech</span>
          </div>
        </div>
        <div class="relative z-10">
          <div class="font-display-md text-display-md text-on-background mb-2">
            <?= number_format($bronze) ?>
          </div>
          <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px] text-[#fb923c]">workspace_premium</span>
            entry tier
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mb-stack-lg">
      <!-- Area Chart Placeholder -->
      <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-stack-lg border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)]">
        <div class="flex justify-between items-center mb-6">
          <h3 class="font-headline-md text-headline-md text-on-background">Statistik Sistem</h3>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-primary"></span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Transaksi</span>
            <span class="w-3 h-3 rounded-full bg-tertiary ml-3"></span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Penukaran</span>
          </div>
        </div>
        <div class="w-full h-64 relative">
          <canvas id="systemStatsChart"></canvas>
        </div>
      </div>

      <!-- Donut Chart: Level Member -->
      <div class="lg:col-span-1 bg-surface-container-lowest rounded-xl p-stack-lg border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)]">
        <div class="flex justify-between items-center mb-6">
          <h3 class="font-headline-md text-headline-md text-on-background">Level Member</h3>
        </div>
        <div class="flex flex-col items-center justify-center h-64">
          <div class="relative w-full h-48 flex items-center justify-center">
            <canvas id="memberLevelChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter mb-stack-lg">
      <!-- Customer Teraktif -->
      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
        <div class="p-stack-lg border-b border-outline-variant/50 bg-surface-container-lowest">
          <h3 class="font-headline-md text-headline-md text-on-background">Customer Paling Aktif</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-lowest border-b border-outline-variant/50 text-on-surface-variant font-semibold">
                <th class="py-4 px-6 font-medium">Customer</th>
                <th class="py-4 px-6 font-medium text-right">Poin</th>
                <th class="py-4 px-6 font-medium text-right">Level</th>
              </tr>
            </thead>
            <tbody class="font-body-md text-body-md">
              <?php while ($row = $q_top_customers->fetch_assoc()): 
                $tier = $row['tier'];
                $tier_bg = $tier == 'Gold' ? 'bg-[#fef9c3] text-[#a16207]' : ($tier == 'Silver' ? 'bg-[#f1f5f9] text-[#475569]' : 'bg-[#ffedd5] text-[#c2410c]');
              ?>
              <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low/50 transition-colors">
                <td class="py-4 px-6 font-medium text-on-background"><?= htmlspecialchars($row['nama']) ?></td>
                <td class="py-4 px-6 text-right font-medium text-primary"><?= number_format($row['total_poin']) ?></td>
                <td class="py-4 px-6 text-right">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full font-label-sm text-label-sm <?= $tier_bg ?>"><?= htmlspecialchars($tier) ?></span>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Reward Populer -->
      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
        <div class="p-stack-lg border-b border-outline-variant/50 bg-surface-container-lowest">
          <h3 class="font-headline-md text-headline-md text-on-background">Reward Populer</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-lowest border-b border-outline-variant/50 text-on-surface-variant font-semibold">
                <th class="py-4 px-6 font-medium">Nama Reward</th>
                <th class="py-4 px-6 font-medium text-right">Poin</th>
                <th class="py-4 px-6 font-medium text-right">Total Klaim</th>
              </tr>
            </thead>
            <tbody class="font-body-md text-body-md">
              <?php while ($row = $q_top_rewards->fetch_assoc()): ?>
              <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low/50 transition-colors">
                <td class="py-4 px-6 font-medium text-on-background"><?= htmlspecialchars($row['nama_reward']) ?></td>
                <td class="py-4 px-6 text-right text-on-surface-variant"><?= number_format($row['jumlah_poin']) ?> Poin</td>
                <td class="py-4 px-6 text-right font-medium text-[#16a34a]"><?= number_format($row['total_klaim']) ?> Klaim</td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
      <div class="p-stack-lg border-b border-outline-variant/50 flex justify-between items-center bg-surface-container-lowest">
        <h3 class="font-headline-md text-headline-md text-on-background">Transaksi Terbaru</h3>
        <a href="../transaksi/index.php" class="font-label-md text-label-md text-primary hover:text-primary-container transition-colors">Lihat Semua</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-lowest border-b border-outline-variant/50 text-on-surface-variant font-semibold">
              <th class="py-4 px-6 font-medium">ID Transaksi</th>
              <th class="py-4 px-6 font-medium">Customer</th>
              <th class="py-4 px-6 font-medium">Tanggal</th>
              <th class="py-4 px-6 font-medium text-right">Poin Diperoleh</th>
            </tr>
          </thead>
          <tbody class="font-body-md text-body-md">
            <?php while ($row = $q_recent_trans->fetch_assoc()): ?>
            <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low/50 transition-colors">
              <td class="py-4 px-6 font-mono text-sm text-on-background">#TRX-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
              <td class="py-4 px-6 font-medium text-on-background"><?= htmlspecialchars($row['nama']) ?></td>
              <td class="py-4 px-6 text-on-surface-variant"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
              <td class="py-4 px-6 text-right text-[#16a34a] font-medium">+<?= number_format($row['poin_didapat']) ?> Poin</td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="absolute bottom-0 right-0 w-full min-h-[64px] bg-surface-container-lowest border-t border-outline-variant flex flex-col sm:flex-row justify-between items-center px-4 lg:px-margin-desktop py-4 z-40 gap-2">
    <p class="font-label-sm text-label-sm text-secondary text-center sm:text-left">© <?= date('Y') ?> Reward System Enterprise. All rights reserved.</p>
  </footer>
</div>

<!-- Chart JS Initialization -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Donut Chart - Member Level
    const ctxPie = document.getElementById('memberLevelChart').getContext('2d');
    new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        labels: ['Bronze', 'Silver', 'Gold'],
        datasets: [{
          data: [<?= $bronze ?>, <?= $silver ?>, <?= $gold ?>],
          backgroundColor: ['#b45309', '#94a3b8', '#facc15'],
          borderWidth: 2,
          borderColor: '#ffffff',
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              font: { family: 'Inter', size: 11 },
              usePointStyle: true,
              padding: 15,
              color: '#434655'
            }
          }
        }
      }
    });

    // Line Chart - Statistik Sistem (7 hari terakhir)
    const ctxLine = document.getElementById('systemStatsChart').getContext('2d');
    new Chart(ctxLine, {
      type: 'line',
      data: {
        labels: <?= json_encode($stat_labels) ?>,
        datasets: [
          {
            label: 'Transaksi',
            data: <?= json_encode($stat_transaksi) ?>,
            borderColor: '#004ac6',
            backgroundColor: 'rgba(0, 74, 198, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#004ac6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2
          },
          {
            label: 'Penukaran Reward',
            data: <?= json_encode($stat_penukaran) ?>,
            borderColor: '#943700',
            backgroundColor: 'rgba(148, 55, 0, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#943700',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: '#ffffff',
            titleColor: '#191b23',
            bodyColor: '#434655',
            borderColor: '#c3c6d7',
            borderWidth: 1,
            padding: 12,
            displayColors: true,
            usePointStyle: true,
            callbacks: {
              title: function(context) {
                return context[0].label;
              },
              label: function(context) {
                return ' ' + context.dataset.label + ': ' + context.parsed.y;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              font: { family: 'Inter', size: 11 },
              color: '#737686'
            },
            grid: {
              color: 'rgba(195, 198, 215, 0.3)',
              drawBorder: false
            }
          },
          x: {
            ticks: {
              font: { family: 'Inter', size: 11 },
              color: '#737686'
            },
            grid: {
              display: false,
              drawBorder: false
            }
          }
        }
      }
    });
  });
</script>
</body>
</html>