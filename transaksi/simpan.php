<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$invoice       = trim($_POST['invoice'] ?? '');
$customer_id   = (int)($_POST['customer_id'] ?? 0);
$tanggal       = $_POST['tanggal'] ?? '';
$total_belanja = (float)($_POST['total_belanja'] ?? 0);

if (empty($invoice) || $customer_id <= 0 || empty($tanggal) || $total_belanja <= 0) {
    $_SESSION['error'] = 'Semua field wajib diisi dengan benar.';
    header("Location: tambah.php");
    exit;
}

// Hitung poin: Rp1.000 = 1 poin
$poin_didapat = floor($total_belanja / 1000);

$conn->begin_transaction();
try {
    // Simpan transaksi
    $stmt = $conn->prepare("INSERT INTO transaksi (invoice, customer_id, tanggal, total_belanja, poin_didapat) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisdi", $invoice, $customer_id, $tanggal, $total_belanja, $poin_didapat);
    $stmt->execute();
    $stmt->close();

    // Update total poin customer
    $stmt2 = $conn->prepare("UPDATE customer SET total_poin = total_poin + ? WHERE id = ?");
    $stmt2->bind_param("ii", $poin_didapat, $customer_id);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();
    $_SESSION['success'] = "Transaksi berhasil disimpan. Poin didapat: " . number_format($poin_didapat);
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Gagal menyimpan transaksi: ' . $e->getMessage();
}

header("Location: index.php");
exit;
