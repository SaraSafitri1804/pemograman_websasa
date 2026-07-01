<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$customer_id  = (int)($_POST['customer_id'] ?? 0);
$reward_id    = (int)($_POST['reward_id'] ?? 0);
$tanggal_tukar = $_POST['tanggal_tukar'] ?? '';

if ($customer_id <= 0 || $reward_id <= 0 || empty($tanggal_tukar)) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header("Location: tambah.php");
    exit;
}

// Ambil data customer
$stmt_c = $conn->prepare("SELECT id, total_poin FROM customer WHERE id = ?");
$stmt_c->bind_param("i", $customer_id);
$stmt_c->execute();
$customer = $stmt_c->get_result()->fetch_assoc();
$stmt_c->close();

if (!$customer) {
    $_SESSION['error'] = 'Customer tidak ditemukan.';
    header("Location: tambah.php");
    exit;
}

// Ambil data reward
$stmt_r = $conn->prepare("SELECT id, nama_reward, jumlah_poin, stok FROM reward WHERE id = ?");
$stmt_r->bind_param("i", $reward_id);
$stmt_r->execute();
$reward = $stmt_r->get_result()->fetch_assoc();
$stmt_r->close();

if (!$reward) {
    $_SESSION['error'] = 'Reward tidak ditemukan.';
    header("Location: tambah.php");
    exit;
}

// Cek poin mencukupi
if ($customer['total_poin'] < $reward['jumlah_poin']) {
    $_SESSION['error'] = 'Poin customer tidak mencukupi! Dibutuhkan ' . number_format($reward['jumlah_poin']) . ' poin, tersedia ' . number_format($customer['total_poin']) . ' poin.';
    header("Location: tambah.php");
    exit;
}

// Cek stok
if ($reward['stok'] <= 0) {
    $_SESSION['error'] = 'Stok reward habis!';
    header("Location: tambah.php");
    exit;
}

$poin_digunakan = $reward['jumlah_poin'];

$conn->begin_transaction();
try {
    // Simpan penukaran
    $stmt1 = $conn->prepare("INSERT INTO penukaran_reward (customer_id, reward_id, poin_digunakan, tanggal_tukar, status) VALUES (?, ?, ?, ?, 'Berhasil')");
    $stmt1->bind_param("iiis", $customer_id, $reward_id, $poin_digunakan, $tanggal_tukar);
    $stmt1->execute();
    $stmt1->close();

    // Kurangi poin customer
    $stmt2 = $conn->prepare("UPDATE customer SET total_poin = total_poin - ? WHERE id = ?");
    $stmt2->bind_param("ii", $poin_digunakan, $customer_id);
    $stmt2->execute();
    $stmt2->close();

    // Kurangi stok reward
    $stmt3 = $conn->prepare("UPDATE reward SET stok = stok - 1 WHERE id = ?");
    $stmt3->bind_param("i", $reward_id);
    $stmt3->execute();
    $stmt3->close();

    $conn->commit();
    $_SESSION['success'] = 'Penukaran reward berhasil! ' . $reward['nama_reward'] . ' (' . number_format($poin_digunakan) . ' poin)';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Gagal menukar reward: ' . $e->getMessage();
}

header("Location: index.php");
exit;
