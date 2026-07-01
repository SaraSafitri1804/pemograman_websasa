<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: index.php"); exit; }

$id          = (int)($_POST['id'] ?? 0);
$nama_reward = trim($_POST['nama_reward'] ?? '');
$jumlah_poin = (int)($_POST['jumlah_poin'] ?? 0);
$stok        = (int)($_POST['stok'] ?? 0);
$foto_lama   = trim($_POST['foto_lama'] ?? '');

if ($id <= 0 || empty($nama_reward) || $jumlah_poin <= 0 || $stok < 0) {
    $_SESSION['error'] = 'Semua field wajib diisi dengan benar.';
    header("Location: edit.php?id=$id");
    exit;
}

// Handle upload foto baru
$foto = $foto_lama;
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
            // Hapus foto lama jika ada
            if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
                unlink($upload_dir . $foto_lama);
            }
            $foto = $filename;
        }
    }
}

$stmt = $conn->prepare("UPDATE reward SET nama_reward=?, jumlah_poin=?, stok=?, foto=? WHERE id=?");
$stmt->bind_param("siisi", $nama_reward, $jumlah_poin, $stok, $foto, $id);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Reward berhasil diupdate.';
} else {
    $_SESSION['error'] = 'Gagal mengupdate reward: ' . $stmt->error;
}

$stmt->close();
header("Location: index.php");
exit;
