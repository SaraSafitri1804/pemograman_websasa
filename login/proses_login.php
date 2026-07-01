<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, username, password, nama_lengkap FROM admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_nama'] = $admin['nama_lengkap'];
        header("Location: ../dashboard/dashboard.php");
        exit;
    }
}

$_SESSION['login_error'] = 'Username atau password salah.';
header("Location: login.php");
exit;
