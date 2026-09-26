<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // get old file to delete
    $q = $conn->query("SELECT dokumentasi FROM jadwal_zoom WHERE id = $id");
    if($r = $q->fetch_assoc()) {
        if(!empty($r['dokumentasi']) && file_exists('../uploads/dokumentasi/' . $r['dokumentasi'])) {
            unlink('../uploads/dokumentasi/' . $r['dokumentasi']);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM jadwal_zoom WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("Location: index.php?msg=deleted");
}
