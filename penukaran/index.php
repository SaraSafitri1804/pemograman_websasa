<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

$page_title = 'Penukaran Reward';

// Pagination setup
$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_param = '';

if (!empty($search)) {
    $search_condition = " WHERE c.nama LIKE ? OR c.id_customer LIKE ? OR r.nama_reward LIKE ?";
    $search_param = "%$search%";
}

// Get total records
$count_query = "SELECT COUNT(*) as total FROM penukaran_reward pr 
                JOIN customer c ON pr.customer_id = c.id 
                JOIN reward r ON pr.reward_id = r.id" . $search_condition;

if (!empty($search)) {
    $stmt_count = $conn->prepare($count_query);
    $stmt_count->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt_count->execute();
    $total_records = $stmt_count->get_result()->fetch_assoc()['total'];
    $stmt_count->close();
} else {
    $total_records = $conn->query($count_query)->fetch_assoc()['total'];
}

$total_pages = ceil($total_records / $per_page);

// Get data with pagination
$query = "SELECT pr.*, c.nama, c.id_customer, r.nama_reward 
          FROM penukaran_reward pr 
          JOIN customer c ON pr.customer_id = c.id 
          JOIN reward r ON pr.reward_id = r.id" 
          . $search_condition . 
          " ORDER BY pr.tanggal_tukar DESC LIMIT ? OFFSET ?";

if (!empty($search)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $search_param, $search_param, $search_param, $per_page, $offset);
    $stmt->execute();
    $rows = $stmt->get_result();
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $per_page, $offset);
    $stmt->execute();
    $rows = $stmt->get_result();
}

include '../includes/header.php';
?>

<style>
  /* Status badges */
  .status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.625rem;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 500;
    border: 1px solid;
  }
  .status-pending {
    background: #FFF7ED;
    color: #9A3412;
    border-color: #FED7AA;
  }
  .status-completed, .status-berhasil {
    background: #F0FDF4;
    color: #166534;
    border-color: #BBF7D0;
  }
  .status-cancelled, .status-batal {
    background: #FEF2F2;
    color: #991B1B;
    border-color: #FECACA;
  }
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
  <?php include '../includes/topbar.php'; ?>

  <!-- Main Content Canvas -->
  <main class="mt-16 min-h-screen">
    <div class="p-margin-mobile lg:p-margin-desktop max-w-container-max mx-auto space-y-stack-lg pb-8">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <h2 class="font-headline-lg text-headline-lg text-on-surface mb-2">Penukaran Reward</h2>
          <p class="font-body-md text-body-md text-on-surface-variant">Kelola permintaan penukaran poin hadiah dari pelanggan.</p>
        </div>
        <a href="tambah.php" class="bg-primary text-on-primary font-label-md text-label-md px-6 py-2.5 rounded hover:bg-primary-container transition-colors inline-flex items-center justify-center gap-2 w-full md:w-auto shadow-sm">
          <span class="material-symbols-outlined text-[18px]">add</span>
          Tukar Reward
        </a>
      </div>

      <!-- Alert Messages -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-[#F0FDF4] text-[#166534] border border-[#BBF7D0] px-4 py-3 rounded-lg font-body-md flex items-center gap-3">
          <span class="material-symbols-outlined">check_circle</span>
          <span><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-error-container text-error border border-error px-4 py-3 rounded-lg font-body-md flex items-center gap-3">
          <span class="material-symbols-outlined">error</span>
          <span><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
        </div>
      <?php endif; ?>

      <!-- Table Card -->
      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-sm overflow-hidden flex flex-col">
        
        <div class="p-4 border-b border-outline-variant/30 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-surface-container-lowest gap-4">
          <form method="GET" action="index.php" class="relative w-full sm:w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input 
              name="search"
              value="<?= htmlspecialchars($search) ?>"
              class="w-full pl-9 pr-4 py-2 bg-surface-container-low border border-outline-variant/50 rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" 
              placeholder="Cari penukaran..." 
              type="text"
            />
          </form>
          <div class="flex items-center gap-2">
            <?php if (!empty($search)): ?>
              <a href="index.php" class="text-on-surface-variant hover:text-primary flex items-center gap-2 font-label-md text-label-md px-3 py-1.5 border border-outline-variant/50 rounded-lg hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
                Clear
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container/30 border-b border-outline-variant/30">
                <th class="py-4 px-6 font-label-md text-label-md text-secondary font-semibold uppercase tracking-wider">Tanggal</th>
                <th class="py-4 px-6 font-label-md text-label-md text-secondary font-semibold uppercase tracking-wider">Customer</th>
                <th class="py-4 px-6 font-label-md text-label-md text-secondary font-semibold uppercase tracking-wider">Reward</th>
                <th class="py-4 px-6 font-label-md text-label-md text-secondary font-semibold uppercase tracking-wider text-right">Poin Digunakan</th>
                <th class="py-4 px-6 font-label-md text-label-md text-secondary font-semibold uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20 font-body-md text-body-md text-on-surface">
              <?php if ($rows->num_rows === 0): ?>
                <tr>
                  <td colspan="5" class="py-12 text-center text-on-surface-variant">
                    <div class="flex flex-col items-center gap-3">
                      <span class="material-symbols-outlined text-[48px] text-outline">inbox</span>
                      <p class="font-body-md">
                        <?= !empty($search) ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data penukaran.' ?>
                      </p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php while ($r = $rows->fetch_assoc()): ?>
                  <tr class="hover:bg-surface-container-low transition-colors group">
                    <td class="py-4 px-6 whitespace-nowrap text-on-surface-variant">
                      <?= date('d M Y', strtotime($r['tanggal_tukar'])) ?>
                    </td>
                    <td class="py-4 px-6 font-medium text-on-surface">
                      <?= htmlspecialchars($r['nama']) ?>
                      <span class="block text-on-surface-variant text-label-sm"><?= htmlspecialchars($r['id_customer']) ?></span>
                    </td>
                    <td class="py-4 px-6">
                      <?= htmlspecialchars($r['nama_reward']) ?>
                    </td>
                    <td class="py-4 px-6 text-right font-medium text-error">
                      -<?= number_format($r['poin_digunakan'], 0, ',', '.') ?> Pts
                    </td>
                    <td class="py-4 px-6">
                      <?php
                        $status = strtolower($r['status']);
                        $status_class = 'status-completed';
                        if (strpos($status, 'pending') !== false || strpos($status, 'menunggu') !== false) {
                          $status_class = 'status-pending';
                        } elseif (strpos($status, 'batal') !== false || strpos($status, 'cancel') !== false || strpos($status, 'gagal') !== false) {
                          $status_class = 'status-cancelled';
                        }
                      ?>
                      <span class="status-badge <?= $status_class ?>">
                        <?= htmlspecialchars($r['status']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
          <div class="px-4 lg:px-6 py-4 border-t border-outline-variant/30 flex flex-col sm:flex-row items-center justify-between bg-surface-container-lowest gap-3">
            <span class="font-label-sm text-label-sm text-on-surface-variant text-center sm:text-left">
              Menampilkan <?= $offset + 1 ?>-<?= min($offset + $per_page, $total_records) ?> dari <?= $total_records ?> data
            </span>
            <div class="flex items-center justify-center gap-1">
              <!-- Previous Button -->
              <a href="?page=<?= max(1, $page - 1) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                 class="p-1 rounded hover:bg-surface-container text-on-surface-variant <?= $page <= 1 ? 'opacity-50 pointer-events-none' : '' ?> transition-colors">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
              </a>
              
              <!-- Page Numbers -->
              <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
              ?>
                <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="w-8 h-8 rounded flex items-center justify-center font-label-sm text-label-sm transition-colors <?= $i == $page ? 'bg-primary text-on-primary' : 'hover:bg-surface-container text-on-surface-variant' ?>">
                  <?= $i ?>
                </a>
              <?php endfor; ?>
              
              <!-- Next Button -->
              <a href="?page=<?= min($total_pages, $page + 1) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                 class="p-1 rounded hover:bg-surface-container text-on-surface-variant <?= $page >= $total_pages ? 'opacity-50 pointer-events-none' : '' ?> transition-colors">
                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

</body>
</html>
