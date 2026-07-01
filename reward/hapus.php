<?php
session_start();
require_once '../config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: index.php"); exit; }

// Get foto filename before delete
$stmt_get = $conn->prepare("SELECT foto FROM reward WHERE id = ?");
$stmt_get->bind_param("i", $id);
$stmt_get->execute();
$result = $stmt_get->get_result()->fetch_assoc();
$foto = $result['foto'] ?? null;
$stmt_get->close();

$stmt = $conn->prepare("DELETE FROM reward WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Hapus file foto jika ada
    if (!empty($foto) && file_exists('../uploads/reward/' . $foto)) {
        unlink('../uploads/reward/' . $foto);
    }
    $_SESSION['success'] = 'Reward berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus reward: ' . $stmt->error;
}

$stmt->close();
header("Location: index.php");
exit;
