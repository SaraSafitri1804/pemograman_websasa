<?php
require_once '../config/cek_login.php';
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id            = (int)($_POST['id'] ?? 0);
$id_customer   = trim($_POST['id_customer'] ?? '');
$nama          = trim($_POST['nama'] ?? '');
$email         = trim($_POST['email'] ?? '');
$no_hp         = trim($_POST['no_hp'] ?? '');
$alamat        = trim($_POST['alamat'] ?? '');
$tier          = $_POST['tier'] ?? '';
$tanggal_daftar = $_POST['tanggal_daftar'] ?? '';

if ($id <= 0 || empty($id_customer) || empty($nama) || empty($email) || empty($no_hp) || empty($alamat) || empty($tier) || empty($tanggal_daftar)) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header("Location: edit.php?id=$id");
    exit;
}

$stmt = $conn->prepare("UPDATE customer SET id_customer=?, nama=?, email=?, no_hp=?, alamat=?, tier=?, tanggal_daftar=? WHERE id=?");
$stmt->bind_param("sssssssi", $id_customer, $nama, $email, $no_hp, $alamat, $tier, $tanggal_daftar, $id);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Customer berhasil diupdate.';
} else {
    $_SESSION['error'] = 'Gagal mengupdate customer: ' . $stmt->error;
}

$stmt->close();
header("Location: index.php");
exit;
