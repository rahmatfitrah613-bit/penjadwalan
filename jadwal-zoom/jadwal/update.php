<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
    $id = $_POST['id'];
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
    
    // Check if new file uploaded
    $update_file_query = "";
    $params = [];
    $types = "";
    
    $upload_dir = '../uploads/dokumentasi/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['dokumentasi']['size'] <= 5000000) {
            $new_filename = 'foto_' . uniqid() . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $upload_dir . $new_filename)) {
                $q = $conn->query("SELECT dokumentasi FROM jadwal_zoom WHERE id = $id");
                if($r = $q->fetch_assoc()) {
                    if(!empty($r['dokumentasi']) && file_exists($upload_dir . $r['dokumentasi'])) unlink($upload_dir . $r['dokumentasi']);
                }
                $update_file_query .= ", dokumentasi = ?";
                $params[] = $new_filename;
                $types .= "s";
            }
        }
    }
    
    if (isset($_FILES['file_video']) && $_FILES['file_video']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_video']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'avi']) && $_FILES['file_video']['size'] <= 50000000) {
            $new_filename = 'video_' . uniqid() . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['file_video']['tmp_name'], $upload_dir . $new_filename)) {
                $q = $conn->query("SELECT file_video FROM jadwal_zoom WHERE id = $id");
                if($r = $q->fetch_assoc()) {
                    if(!empty($r['file_video']) && file_exists($upload_dir . $r['file_video'])) unlink($upload_dir . $r['file_video']);
                }
                $update_file_query .= ", file_video = ?";
                $params[] = $new_filename;
                $types .= "s";
            }
        }
    }

    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_materi']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx']) && $_FILES['file_materi']['size'] <= 20000000) {
            $new_filename = 'materi_' . uniqid() . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $upload_dir . $new_filename)) {
                $q = $conn->query("SELECT file_materi FROM jadwal_zoom WHERE id = $id");
                if($r = $q->fetch_assoc()) {
                    if(!empty($r['file_materi']) && file_exists($upload_dir . $r['file_materi'])) unlink($upload_dir . $r['file_materi']);
                }
                $update_file_query .= ", file_materi = ?";
                $params[] = $new_filename;
                $types .= "s";
            }
        }
    }
    
    $sql = "UPDATE jadwal_zoom SET tanggal_rapat=?, waktu_mulai=?, waktu_selesai=?, nama_rapat=?, jenis_rapat=?, jurusan=?, program_magister=?, link_zoom=?, link_zoom_admin=?, token_zoom=?, password_zoom=?, hasil_rapat=?, keterangan=? $update_file_query WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    
    $base_params = [$tanggal_rapat, $waktu_mulai, $waktu_selesai, $nama_rapat, $jenis_rapat, $jurusan, $program_magister, $link_zoom, $link_zoom_admin, $token_zoom, $password_zoom, $hasil_rapat, $keterangan];
    $base_types = "sssssssssssss";
    
    $all_params = array_merge($base_params, $params, [$id]);
    $all_types = $base_types . $types . "i";
    
    $stmt->bind_param($all_types, ...$all_params);
    
    $stmt->execute();
    header("Location: index.php?msg=updated");
}
