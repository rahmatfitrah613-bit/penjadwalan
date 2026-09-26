<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_rapat = $_POST['tanggal_rapat'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $nama_rapat = $_POST['nama_rapat'];
    $jenis_rapat = $_POST['jenis_rapat'];
    $jurusan = $_POST['jurusan'];
    $program_magister = $_POST['program_magister'];
    $link_zoom = $_POST['link_zoom'];
    $link_zoom_admin = $_POST['link_zoom_admin'] ?? '';
    $token_zoom = $_POST['token_zoom'] ?? '';
    $password_zoom = $_POST['password_zoom'];
    $hasil_rapat = $_POST['hasil_rapat'];
    $keterangan = $_POST['keterangan'];
    $created_by = $_SESSION['user_id'];
    
    $file_dokumentasi = "";
    $file_video = "";
    $file_materi = "";
    $upload_dir = '../uploads/dokumentasi/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    // Upload Foto
    if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['dokumentasi']['size'] <= 5000000) {
            $new_filename = 'foto_' . uniqid() . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $upload_dir . $new_filename)) {
                $file_dokumentasi = $new_filename;
            }
        }
    }
    
    // Upload Video
    if (isset($_FILES['file_video']) && $_FILES['file_video']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_video']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'avi']) && $_FILES['file_video']['size'] <= 50000000) { // max 50mb
            $new_filename = 'video_' . uniqid() . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['file_video']['tmp_name'], $upload_dir . $new_filename)) {
                $file_video = $new_filename;
            }
        }
    }

    // Upload Materi
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_materi']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx']) && $_FILES['file_materi']['size'] <= 20000000) { // max 20mb
            $new_filename = 'materi_' . uniqid() . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $upload_dir . $new_filename)) {
                $file_materi = $new_filename;
            }
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO jadwal_zoom (tanggal_rapat, waktu_mulai, waktu_selesai, nama_rapat, jenis_rapat, jurusan, program_magister, link_zoom, link_zoom_admin, token_zoom, password_zoom, hasil_rapat, keterangan, dokumentasi, file_video, file_materi, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssssssss", $tanggal_rapat, $waktu_mulai, $waktu_selesai, $nama_rapat, $jenis_rapat, $jurusan, $program_magister, $link_zoom, $link_zoom_admin, $token_zoom, $password_zoom, $hasil_rapat, $keterangan, $file_dokumentasi, $file_video, $file_materi, $created_by);
    

    if ($stmt->execute()) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . $stmt->error;
    }
}
