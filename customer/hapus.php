<?php
session_start();
require_once '../config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: index.php"); exit; }

$stmt = $conn->prepare("DELETE FROM customer WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Customer berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus customer: ' . $stmt->error;
}

$stmt->close();
header("Location: index.php");
exit;
