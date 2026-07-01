<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

// Set page title untuk topbar
$page_title = 'Katalog Reward';

// Logika Pencarian (Search)
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Logika Pagination
$per_page = 10;
$page_num = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page_num - 1) * $per_page;

// Build WHERE clause
$where_conditions = [];
$params = [];
$types = '';

if ($search !== '') {
    $where_conditions[] = "(nama_reward LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $types .= 's';
}

$where_clause = count($where_conditions) > 0 ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Hitung total data
$count_query = "SELECT COUNT(*) as total FROM reward " . $where_clause;
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

// Ambil data reward dengan pagination dan filter
$select_query = "SELECT * FROM reward " . $where_clause . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($select_query);

// Add limit and offset to params
$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';

$stmt->bind_param($types, ...$params);
$stmt->execute();
$rows = $stmt->get_result();
$stmt->close();

// Append query string untuk pagination
$query_params = [];
if ($search !== '') {
    $query_params[] = 'search=' . urlencode($search);
}
$query_str = count($query_params) > 0 ? '&' . implode('&', $query_params) : '';

// Include header
require_once '../includes/header.php';
?>

<body class="bg-background text-on-surface antialiased min-h-screen flex">

  <?php require_once '../includes/sidebar.php'; ?>

  <div class="flex-1 w-full lg:ml-[280px] lg:w-auto flex flex-col min-h-screen transition-all duration-300">
    
    <?php require_once '../includes/topbar.php'; ?>

    <main class="flex-1 pt-24 px-margin-mobile lg:px-margin-desktop pb-8 max-w-container-max mx-auto w-full">
      
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
          <nav aria-label="Breadcrumb" class="flex text-outline font-label-sm mb-1">
            <ol class="flex items-center space-x-2">
              <li><a class="hover:text-primary" href="../dashboard/dashboard.php">Dashboard</a></li>
              <li><span class="material-symbols-outlined text-[14px]">chevron_right</span></li>
              <li class="text-on-surface font-semibold">Katalog Reward</li>
            </ol>
          </nav>
          <h2 class="font-headline-lg text-headline-lg text-on-surface">Katalog Reward</h2>
          <p class="font-body-md text-body-md text-outline">Kelola item reward yang dapat ditukarkan oleh pelanggan menggunakan poin mereka.</p>
        </div>
        <a href="tambah.php" class="flex items-center gap-2 px-6 py-2.5 bg-primary-container text-on-primary-container hover:bg-primary-container/90 rounded-xl font-label-md transition-all active:scale-95 shadow-sm w-full sm:w-auto justify-center">
          <span class="material-symbols-outlined">add</span>
          Tambah Reward
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

      <!-- Filters Section -->
      <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-6 flex flex-col sm:flex-row flex-wrap gap-4 items-start sm:items-end">
        
        <div class="flex flex-col gap-1 flex-1 min-w-[250px]">
          <label class="font-label-sm text-outline">Cari Reward</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input type="text" id="search-input" value="<?= htmlspecialchars($search) ?>" class="w-full pl-10 pr-4 py-2 rounded-lg border border-outline-variant bg-surface-container-low font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" placeholder="Cari nama reward..." onkeypress="handleSearchKeypress(event)"/>
          </div>
        </div>
        
        <div class="flex gap-2">
          <button onclick="applyFilters()" class="flex items-center gap-2 px-4 py-2 bg-primary-container text-on-primary-container hover:bg-primary-container/90 rounded-lg font-label-md transition-all">
            <span class="material-symbols-outlined">search</span>
            Cari
          </button>
          <a href="index.php" class="flex items-center gap-2 px-4 py-2 border border-outline-variant text-on-surface hover:bg-surface-container-low rounded-lg font-label-md transition-all">
            <span class="material-symbols-outlined">filter_list_off</span>
            Reset
          </a>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-low border-b border-outline-variant">
                <th class="px-6 py-4 font-label-md text-outline uppercase tracking-wider">Reward</th>
                <th class="px-6 py-4 font-label-md text-outline uppercase tracking-wider">Poin Dibutuhkan</th>
                <th class="px-6 py-4 font-label-md text-outline uppercase tracking-wider">Stok</th>
                <th class="px-6 py-4 font-label-md text-outline uppercase tracking-wider text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30">
              <?php if ($rows->num_rows === 0): ?>
                <tr>
                  <td colspan="4" class="px-6 py-10 text-center font-body-md text-on-surface-variant">Belum ada data reward.</td>
                </tr>
              <?php else: ?>
                <?php while ($r = $rows->fetch_assoc()): ?>
                  <tr class="hover:bg-surface-container-low transition-colors group">
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-4">
                        <?php if (!empty($r['foto']) && file_exists('../uploads/reward/' . $r['foto'])): ?>
                          <img class="w-12 h-12 rounded-lg object-cover bg-surface-container" src="../uploads/reward/<?= htmlspecialchars($r['foto']) ?>" alt="<?= htmlspecialchars($r['nama_reward']) ?>"/>
                        <?php else: ?>
                          <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-3xl">military_tech</span>
                          </div>
                        <?php endif; ?>
                        <div>
                          <p class="font-body-md font-semibold text-on-surface"><?= htmlspecialchars($r['nama_reward']) ?></p>
                          <p class="font-label-sm text-outline">Reward Item</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="font-body-md font-bold text-primary"><?= number_format($r['jumlah_poin']) ?> pts</span>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant font-body-md">
                      <?= $r['stok'] ?> pcs
                      <?php if ($r['stok'] == 0): ?>
                        <span class="ml-2 px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-label-sm font-label-sm">Habis</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex justify-end gap-2">
                        <a href="edit.php?id=<?= $r['id'] ?>" class="p-2 text-outline hover:text-primary hover:bg-primary/10 rounded-lg transition-all" title="Edit">
                          <span class="material-symbols-outlined">edit</span>
                        </a>
                        <button type="button" class="p-2 text-outline hover:text-error hover:bg-error/10 rounded-lg transition-all" onclick="bukaModalHapus(<?= $r['id'] ?>)" title="Hapus">
                          <span class="material-symbols-outlined">delete</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 lg:px-6 py-4 bg-surface-container-low border-t border-outline-variant flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="font-label-md text-outline">
            Menampilkan <?= $offset + 1 ?> - <?= min($offset + $per_page, $total) ?> dari <?= $total ?> reward
          </p>
          <div class="flex items-center gap-2">
            <a href="?page=<?= max(1, $page_num - 1) . $query_str ?>" class="p-2 border border-outline-variant rounded-lg text-outline hover:bg-surface-container-high transition-colors <?= $page_num <= 1 ? 'opacity-50 pointer-events-none' : '' ?>">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
            
            <?php
            $start_page = max(1, $page_num - 2);
            $end_page = min($total_pages, $page_num + 2);
            
            for ($p = $start_page; $p <= $end_page; $p++):
            ?>
              <a href="?page=<?= $p . $query_str ?>" class="px-4 py-2 <?= $p === $page_num ? 'bg-primary text-white' : 'border border-outline-variant text-on-surface-variant hover:bg-surface-container-high' ?> rounded-lg font-label-md"><?= $p ?></a>
            <?php endfor; ?>
            
            <a href="?page=<?= min($total_pages, $page_num + 1) . $query_str ?>" class="p-2 border border-outline-variant rounded-lg text-outline hover:bg-surface-container-high transition-colors <?= $page_num >= $total_pages ? 'opacity-50 pointer-events-none' : '' ?>">
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
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

  <!-- Delete Confirmation Modal -->
  <div class="fixed inset-0 z-[110] flex items-center justify-center hidden" id="modal-delete">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm" onclick="tutupModalHapus()"></div>
    <div class="relative bg-surface-container-lowest rounded-xl shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1)] w-full max-w-md border border-outline-variant p-6 text-center mx-4 sm:mx-auto">
      <div class="w-12 h-12 rounded-full bg-error-container text-error flex items-center justify-center mx-auto mb-4">
        <span class="material-symbols-outlined text-[24px]">warning</span>
      </div>
      <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Hapus Reward?</h3>
      <p class="font-body-md text-body-md text-on-surface-variant mb-6">Apakah Anda yakin ingin menghapus reward ini? Tindakan ini tidak dapat dibatalkan dan akan berpengaruh pada riwayat penukaran pelanggan.</p>
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

    function applyFilters() {
        const search = document.getElementById('search-input').value.trim();
        
        let url = 'index.php?';
        const params = [];
        
        if (search !== '') {
            params.push('search=' + encodeURIComponent(search));
        }
        
        url += params.join('&');
        window.location.href = url;
    }

    function handleSearchKeypress(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyFilters();
        }
    }
  </script>
</body>
</html>
