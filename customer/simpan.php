<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$nama          = trim($_POST['nama'] ?? '');
$email         = trim($_POST['email'] ?? '');
$no_hp         = trim($_POST['no_hp'] ?? '');
$alamat        = trim($_POST['alamat'] ?? '');
$tier          = $_POST['tier'] ?? '';
$tanggal_daftar = $_POST['tanggal_daftar'] ?? '';

// Validasi
if (empty($nama) || empty($email) || empty($no_hp) || empty($alamat) || empty($tier) || empty($tanggal_daftar)) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header("Location: tambah.php");
    exit;
}

// Auto-generate ID Customer
$result = $conn->query("SELECT id_customer FROM customer ORDER BY id DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $last = $result->fetch_assoc();
    $last_number = (int)substr($last['id_customer'], 5); // Ambil angka setelah "CUST-"
    $new_number = $last_number + 1;
} else {
    $new_number = 89210; // Nomor awal jika belum ada data
}
$id_customer = 'CUST-' . $new_number;

$stmt = $conn->prepare("INSERT INTO customer (id_customer, nama, email, no_hp, alamat, tier, total_poin, tanggal_daftar) VALUES (?, ?, ?, ?, ?, ?, 0, ?)");
$stmt->bind_param("sssssss", $id_customer, $nama, $email, $no_hp, $alamat, $tier, $tanggal_daftar);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Customer berhasil ditambahkan.';
} else {
    $_SESSION['error'] = 'Gagal menambahkan customer: ' . $stmt->error;
}

$stmt->close();
header("Location: index.php");
exit;
