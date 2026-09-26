<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    // Hapus prodi terkait terlebih dahulu
    $conn->query("DELETE FROM prodi WHERE jurusan_id=$id");
    $conn->query("DELETE FROM jurusan WHERE id=$id");
}

header("Location: index.php?msg=Jurusan dan prodi terkait berhasil dihapus");
exit;
