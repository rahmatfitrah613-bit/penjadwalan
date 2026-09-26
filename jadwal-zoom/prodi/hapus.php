<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $conn->query("DELETE FROM prodi WHERE id=$id");
}

header("Location: index.php?msg=Program studi berhasil dihapus");
exit;
