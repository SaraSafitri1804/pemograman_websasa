<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

// Menentukan variabel halaman untuk header.php
$page_title = 'Customer Management';

// Logika Pencarian (Search)
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Logika Filter Tier
$tier_filter = '';
if (isset($_GET['tier']) && in_array($_GET['tier'], ['Bronze', 'Silver', 'Gold'])) {
    $tier_filter = $_GET['tier'];
}

// Logika Pagination
$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Build WHERE clause
$where_conditions = [];
$params = [];
$types = '';

if ($search !== '') {
    $where_conditions[] = "(c.nama LIKE ? OR c.id_customer LIKE ? OR c.email LIKE ? OR c.no_hp LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'ssss';
}

if ($tier_filter !== '') {
    $where_conditions[] = "c.tier = ?";
    $params[] = $tier_filter;
    $types .= 's';
}

$where_clause = count($where_conditions) > 0 ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Hitung Total Data (untuk navigasi halaman)
$count_query = "SELECT COUNT(*) as total FROM customer c " . $where_clause;
if (count($params) > 0) {
    $stmt_count = $conn->prepare($count_query);
    $stmt_count->bind_param($types, ...$params);
    $stmt_count->execute();
    $total = $stmt_count->get_result()->fetch_assoc()['total'];
    $stmt_count->close();
} else {
    $total = $conn->query($count_query)->fetch_assoc()['total'];
}

$total_pages = max(1, ceil($total / $per_page));

// Mengambil Data Customer Berdasarkan Halaman, Pencarian & Filter
$select_query = "SELECT c.*, (SELECT MAX(t.tanggal) FROM transaksi t WHERE t.customer_id = c.id) as last_trx FROM customer c " . $where_clause . " ORDER BY c.id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($select_query);

// Add limit and offset to params
$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';

$stmt->bind_param($types, ...$params);
$stmt->execute();
$customers = $stmt->get_result();

// Append query string untuk pagination agar search dan tier parameter tetap terjaga
$query_params = [];
if ($search !== '') {
    $query_params[] = 'search=' . urlencode($search);
}
if ($tier_filter !== '') {
    $query_params[] = 'tier=' . urlencode($tier_filter);
}
$query_str = count($query_params) > 0 ? '&' . implode('&', $query_params) : '';

// Mengambil file pembuka template HTML & Head
require_once '../includes/header.php'; 
?>

<body class="bg-background text-on-surface antialiased min-h-screen flex">

  <?php require_once '../includes/sidebar.php'; ?>

  <div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
    
    <?php require_once '../includes/topbar.php'; ?>

    <main class="flex-1 pt-24 px-margin-mobile lg:px-margin-desktop pb-8 max-w-container-max mx-auto w-full">
      
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 lg:mb-8 gap-4">
        <div>
          <h1 class="font-display-md text-display-md text-on-surface">Daftar Customer</h1>
          <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola data pelanggan dan pantau performa loyalitas mereka.</p>
        </div>
        <a href="tambah.php" class="bg-primary hover:bg-on-primary-fixed-variant text-on-primary font-label-md text-label-md px-6 py-3 rounded-lg flex items-center gap-2 transition-colors shadow-sm w-full sm:w-auto justify-center">
          <span class="material-symbols-outlined text-[20px]">add</span>
          Tambah Customer
        </a>
      </div>

      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 mb-6 shadow-[0px_1px_3px_rgba(0,0,0,0.05)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex gap-2 flex-wrap">
          <a href="index.php" class="px-4 py-2 rounded-full font-label-md text-label-md <?= $tier_filter === '' ? 'bg-secondary-container text-primary font-semibold border border-primary/20' : 'bg-surface text-on-surface-variant border border-outline-variant hover:bg-surface-container-high' ?> transition-colors">All</a>
          <a href="index.php?tier=Bronze<?= $search !== '' ? '&search=' . urlencode($search) : '' ?>" class="px-4 py-2 rounded-full font-label-md text-label-md <?= $tier_filter === 'Bronze' ? 'bg-secondary-container text-primary font-semibold border border-primary/20' : 'bg-surface text-on-surface-variant border border-outline-variant hover:bg-surface-container-high' ?> transition-colors">Bronze</a>
          <a href="index.php?tier=Silver<?= $search !== '' ? '&search=' . urlencode($search) : '' ?>" class="px-4 py-2 rounded-full font-label-md text-label-md <?= $tier_filter === 'Silver' ? 'bg-secondary-container text-primary font-semibold border border-primary/20' : 'bg-surface text-on-surface-variant border border-outline-variant hover:bg-surface-container-high' ?> transition-colors">Silver</a>
          <a href="index.php?tier=Gold<?= $search !== '' ? '&search=' . urlencode($search) : '' ?>" class="px-4 py-2 rounded-full font-label-md text-label-md <?= $tier_filter === 'Gold' ? 'bg-secondary-container text-primary font-semibold border border-primary/20' : 'bg-surface text-on-surface-variant border border-outline-variant hover:bg-surface-container-high' ?> transition-colors">Gold</a>
        </div>
        
        <form method="GET" action="index.php" class="relative w-full sm:w-72">
          <?php if ($tier_filter !== ''): ?>
            <input type="hidden" name="tier" value="<?= htmlspecialchars($tier_filter) ?>" />
          <?php endif; ?>
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="w-full pl-10 pr-4 py-2 rounded-lg border border-outline-variant bg-surface font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" placeholder="Cari nama, email, atau no HP..."/>
        </form>
      </div>

      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0px_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider">ID Customer</th>
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Nama</th>
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Nomor HP</th>
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider text-right">Total Poin</th>
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Level</th>
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Transaksi Terakhir</th>
              <th class="px-6 py-4 font-label-md text-label-md text-secondary uppercase tracking-wider text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-container-highest">
            <?php if ($customers->num_rows > 0): ?>
              <?php while ($row = $customers->fetch_assoc()): ?>
                <tr class="hover:bg-surface-container transition-colors group">
                  <td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">#<?= htmlspecialchars($row['id_customer']) ?></td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-primary-fixed text-primary font-bold flex items-center justify-center font-label-md">
                        <?= strtoupper(substr(htmlspecialchars($row['nama']), 0, 2)) ?>
                      </div>
                      <span class="font-body-md text-body-md font-medium text-on-surface"><?= htmlspecialchars($row['nama']) ?></span>
                    </div>
                  </td>
                  <td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant"><?= htmlspecialchars($row['email']) ?></td>
                  <td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant"><?= htmlspecialchars($row['no_hp']) ?></td>
                  <td class="px-6 py-4 font-body-md text-body-md text-on-surface font-semibold text-right"><?= number_format($row['total_poin']) ?></td>
                  <td class="px-6 py-4">
                    <?php 
                      // Styling badge dinamis berdasarkan tier tingkatan pelanggan
                      $tier = htmlspecialchars($row['tier']);
                      if ($tier === 'Gold') {
                          echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#FFF8E1] text-[#F57F17] border border-[#FBC02D]/30">Gold</span>';
                      } elseif ($tier === 'Silver') {
                          echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-surface-variant text-on-surface-variant border border-outline-variant/30">Silver</span>';
                      } else {
                          echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#EFEBE9] text-[#5D4037] border border-[#D7CCC8]/50">Bronze</span>';
                      }
                    ?>
                  </td>
                  <td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">
                    <?php 
                      if ($row['last_trx']) {
                          echo date('d M Y', strtotime($row['last_trx']));
                      } else {
                          echo '<span class="text-on-surface-variant/50">Belum ada</span>';
                      }
                    ?>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                      <a href="detail.php?id=<?= $row['id'] ?>" class="p-1.5 text-on-surface-variant hover:text-tertiary hover:bg-tertiary-container rounded-md transition-colors" title="Detail">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                      </a>
                      <a href="edit.php?id=<?= $row['id'] ?>" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-md transition-colors" title="Edit">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                      </a>
                      <button type="button" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-md transition-colors" 
                              onclick="bukaModalHapus(<?= $row['id'] ?>)" title="Delete">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="px-6 py-10 text-center font-body-md text-on-surface-variant">Belum ada data customer.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        </div>

        <div class="px-4 lg:px-6 py-4 border-t border-outline-variant bg-surface-container-lowest flex flex-col sm:flex-row items-center justify-between gap-3">
          <span class="font-body-md text-body-md text-on-surface-variant text-center sm:text-left">
            Showing <?= $offset + 1 ?> to <?= min($offset + $per_page, $total) ?> of <?= $total ?> entries
          </span>
          <div class="flex items-center gap-1">
            <a href="?page=<?= max(1, $page - 1) . $query_str ?>" class="px-3 py-1.5 border border-outline-variant rounded-md text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md <?= $page <= 1 ? 'pointer-events-none opacity-50' : '' ?>">Previous</a>
            
            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            if ($start_page > 1): ?>
              <a href="?page=1<?= $query_str ?>" class="w-8 h-8 flex items-center justify-center border border-outline-variant hover:bg-surface-container-high rounded-md text-on-surface-variant font-label-md text-label-md">1</a>
              <?php if ($start_page > 2): ?>
                <span class="px-1 text-on-surface-variant">...</span>
              <?php endif; ?>
            <?php endif; ?>

            <?php for ($p = $start_page; $p <= $end_page; $p++): ?>
              <a href="?page=<?= $p . $query_str ?>" class="w-8 h-8 flex items-center justify-center border <?= $p === $page ? 'border-primary bg-primary-fixed text-primary' : 'border-outline-variant hover:bg-surface-container-high text-on-surface-variant' ?> rounded-md font-label-md text-label-md"><?= $p ?></a>
            <?php endfor; ?>

            <?php if ($end_page < $total_pages): ?>
              <?php if ($end_page < $total_pages - 1): ?>
                <span class="px-1 text-on-surface-variant">...</span>
              <?php endif; ?>
              <a href="?page=<?= $total_pages . $query_str ?>" class="w-8 h-8 flex items-center justify-center border border-outline-variant hover:bg-surface-container-high rounded-md text-on-surface-variant font-label-md text-label-md"><?= $total_pages ?></a>
            <?php endif; ?>

            <a href="?page=<?= min($total_pages, $page + 1) . $query_str ?>" class="px-3 py-1.5 border border-outline-variant rounded-md text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md <?= $page >= $total_pages ? 'pointer-events-none opacity-50' : '' ?>">Next</a>
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

  <div class="fixed inset-0 z-[110] flex items-center justify-center hidden" id="modal-delete">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm" onclick="tutupModalHapus()"></div>
    <div class="relative bg-surface-container-lowest rounded-xl shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1)] w-full max-w-sm border border-outline-variant p-6 text-center mx-4 sm:mx-auto">
      <div class="w-12 h-12 rounded-full bg-error-container text-error flex items-center justify-center mx-auto mb-4">
        <span class="material-symbols-outlined text-[24px]">warning</span>
      </div>
      <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Hapus Customer?</h3>
      <p class="font-body-md text-body-md text-on-surface-variant mb-6">Tindakan ini tidak dapat dibatalkan. Semua data poin dan riwayat transaksi pelanggan ini akan dihapus secara permanen.</p>
      <div class="flex gap-3 justify-center">
        <button type="button" class="flex-1 px-4 py-2 rounded-lg border border-outline-variant text-on-surface font-label-md text-label-md hover:bg-surface-container-highest transition-colors" onclick="tutupModalHapus()">Batal</button>
        <a id="link-konfirmasi-hapus" href="#" class="flex-1 px-4 py-2 rounded-lg bg-error text-on-error font-label-md text-label-md hover:bg-[#93000a] text-center transition-colors shadow-sm">Ya, Hapus</a>
      </div>
    </div>
  </div>

  <script>
    function bukaModalHapus(id) {
        document.getElementById('link-konfirmasi-hapus').href = 'hapus.php?id=' + id;
        document.getElementById('modal-delete').classList.remove('hidden');
    }

    function tutupModalHapus() {
        document.getElementById('modal-delete').classList.add('hidden');
    }
  </script>
</body>
</html>