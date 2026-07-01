<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /pemograman_websasa-main/pemograman_websasa-main/login/login.php");
    exit;
}
