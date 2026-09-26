<?php
require_once '../config/database.php';

// Endpoint AJAX: kembalikan daftar prodi berdasarkan jurusan_id
header('Content-Type: application/json');

$jurusan_id = (int)($_GET['jurusan_id'] ?? 0);

if (!$jurusan_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, kode_prodi, nama_prodi, jenjang FROM prodi WHERE jurusan_id = ? ORDER BY nama_prodi ASC");
$stmt->bind_param("i", $jurusan_id);
$stmt->execute();
$result = $stmt->get_result();

$prodi = [];
while ($row = $result->fetch_assoc()) {
    $prodi[] = $row;
}

echo json_encode($prodi);
