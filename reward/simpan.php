<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: index.php"); exit; }

$nama_reward = trim($_POST['nama_reward'] ?? '');
$jumlah_poin = (int)($_POST['jumlah_poin'] ?? 0);
$stok        = (int)($_POST['stok'] ?? 0);

if (empty($nama_reward) || $jumlah_poin <= 0 || $stok < 0) {
    $_SESSION['error'] = 'Semua field wajib diisi dengan benar.';
    header("Location: tambah.php");
    exit;
}

// Handle upload foto
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/reward/';
    
    // Buat folder jika belum ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['foto']['type'];
    
    if (in_array($file_type, $allowed_types)) {
        $file_ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = 'reward_' . time() . '_' . uniqid() . '.' . $file_ext;
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
            $foto = $filename;
        }
    }
}

$stmt = $conn->prepare("INSERT INTO reward (nama_reward, jumlah_poin, stok, foto) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siis", $nama_reward, $jumlah_poin, $stok, $foto);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Reward berhasil ditambahkan.';
} else {
    $_SESSION['error'] = 'Gagal menambahkan reward: ' . $stmt->error;
}

$stmt->close();
header("Location: index.php");
exit;
