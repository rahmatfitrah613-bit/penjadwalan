import os

project_dir = "d:/Penjadwalan/jadwal-zoom"

files = {
    "database.sql": """
CREATE DATABASE IF NOT EXISTS db_jadwal_zoom;
USE db_jadwal_zoom;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS jadwal_zoom (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal_rapat DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    nama_rapat VARCHAR(200) NOT NULL,
    jenis_rapat VARCHAR(100),
    jurusan VARCHAR(100),
    program_magister VARCHAR(100),
    link_zoom TEXT NOT NULL,
    password_zoom VARCHAR(100),
    dokumentasi VARCHAR(255),
    hasil_rapat TEXT,
    keterangan TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Password for admin is 'admin123'
INSERT INTO users (nama, username, password, role) VALUES ('Administrator', 'admin', '$2y$10$HRMkCJcz6rF4t.T4pvAJbeb.cWpTw7ZZ9sRPk4RV1V3cviqKbAGlW', 'admin');
""",
    "config/database.php": """
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
$username = "root";
$password = "";
$database = "db_jadwal_zoom";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function base_url($path = '') {
    $doc_root = str_replace('\\\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $dir = str_replace('\\\\', '/', __DIR__);
    $project_root = str_replace('/config', '', $dir);
    $base_path = str_replace($doc_root, '', $project_root);
    return $base_path . '/' . ltrim($path, '/');
}
?>
""",
    "auth/auth_check.php": """
<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: " . base_url('login.php'));
    exit();
}
?>
""",
    "layouts/header.php": """
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Jadwal Rapat Zoom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="bg-light">
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar bg-dark text-white p-3 vh-100" style="width: 250px; position: fixed;">
        <h4><i class="bi bi-camera-video"></i> ZoomApp</h4>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="<?= base_url('dashboard.php') ?>" class="nav-link text-white"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="nav-item mb-2">
                <a href="<?= base_url('jadwal/index.php') ?>" class="nav-link text-white"><i class="bi bi-calendar-event"></i> Data Jadwal</a>
            </li>
            <li class="nav-item mb-2">
                <a href="<?= base_url('jadwal/tambah.php') ?>" class="nav-link text-white"><i class="bi bi-plus-circle"></i> Tambah Jadwal</a>
            </li>
            <li class="nav-item mb-2">
                <a href="<?= base_url('kalender/index.php') ?>" class="nav-link text-white"><i class="bi bi-calendar3"></i> Kalender</a>
            </li>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item mb-2">
                <a href="<?= base_url('users/index.php') ?>" class="nav-link text-white"><i class="bi bi-people"></i> Manajemen User</a>
            </li>
            <?php endif; ?>
            <li class="nav-item mt-4">
                <a href="<?= base_url('logout.php') ?>" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="content flex-grow-1" style="margin-left: 250px; min-height: 100vh;">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Sistem Informasi Jadwal Rapat Zoom</span>
                <div class="d-flex align-items-center">
                    <span class="navbar-text me-3 text-dark">
                        Halo, <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></strong> (<?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'user')) ?>)
                    </span>
                    <a href="<?= base_url('logout.php') ?>" class="btn btn-sm btn-outline-danger">Logout</a>
                </div>
            </div>
        </nav>
        <div class="p-4">
""",
    "layouts/footer.php": """
        </div> <!-- End of p-4 -->
    </div> <!-- End of content -->
</div> <!-- End of d-flex -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>
""",
    "assets/css/style.css": """
.sidebar {
    z-index: 1000;
}
.sidebar .nav-link {
    border-radius: 5px;
}
.sidebar .nav-link:hover {
    background-color: rgba(255,255,255,0.1);
}
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
""",
    "assets/js/script.js": """
// Script custom tambahan jika diperlukan
""",
    "login.php": """
<?php
require_once 'config/database.php';
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, nama, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Jadwal Zoom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-box { width: 100%; max-width: 400px; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="login-box">
    <h3 class="text-center mb-4">Login SI Rapat</h3>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
</body>
</html>
""",
    "logout.php": """
<?php
require_once 'config/database.php';
session_destroy();
header("Location: login.php");
exit();
""",
    "index.php": """
<?php
header("Location: login.php");
exit();
""",
    "dashboard.php": """
<?php
require_once 'config/database.php';
require_once 'auth/auth_check.php';

// Menghitung statistik
$today = date('Y-m-d');
$week_end = date('Y-m-d', strtotime('+7 days'));

$total = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom")->fetch_assoc()['c'];
$hari_ini = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom WHERE tanggal_rapat = '$today'")->fetch_assoc()['c'];
$minggu_ini = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom WHERE tanggal_rapat >= '$today' AND tanggal_rapat <= '$week_end'")->fetch_assoc()['c'];
$akan_datang = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom WHERE tanggal_rapat > '$today'")->fetch_assoc()['c'];

require_once 'layouts/header.php';
?>
<h2 class="mb-4">Dashboard</h2>
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Rapat</h5>
                <h2><?= $total ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Rapat Hari Ini</h5>
                <h2><?= $hari_ini ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Rapat 7 Hari Kedepan</h5>
                <h2><?= $minggu_ini ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Akan Datang</h5>
                <h2><?= $akan_datang ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header bg-white">
        <strong>Jadwal Rapat Terdekat</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Nama Rapat</th>
                        <th>Jurusan</th>
                        <th>Program</th>
                        <th>Link Zoom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = $conn->query("SELECT * FROM jadwal_zoom WHERE tanggal_rapat >= '$today' ORDER BY tanggal_rapat ASC, waktu_mulai ASC LIMIT 5");
                    while($r = $q->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($r['tanggal_rapat'])) ?></td>
                        <td><?= substr($r['waktu_mulai'],0,5) ?> - <?= substr($r['waktu_selesai'],0,5) ?></td>
                        <td><?= htmlspecialchars($r['nama_rapat']) ?></td>
                        <td><?= htmlspecialchars($r['jurusan']) ?></td>
                        <td><?= htmlspecialchars($r['program_magister']) ?></td>
                        <td><a href="<?= htmlspecialchars($r['link_zoom']) ?>" target="_blank" class="btn btn-sm btn-primary">Join</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'layouts/footer.php'; ?>
""",
    "jadwal/index.php": """
<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

$where = [];
$params = [];
$types = "";

if (isset($_GET['cari'])) {
    if (!empty($_GET['tanggal'])) {
        $where[] = "tanggal_rapat = ?";
        $params[] = $_GET['tanggal'];
        $types .= "s";
    }
    if (!empty($_GET['bulan'])) {
        $where[] = "MONTH(tanggal_rapat) = ?";
        $params[] = $_GET['bulan'];
        $types .= "s";
    }
    if (!empty($_GET['tahun'])) {
        $where[] = "YEAR(tanggal_rapat) = ?";
        $params[] = $_GET['tahun'];
        $types .= "s";
    }
    if (!empty($_GET['jurusan'])) {
        $where[] = "jurusan LIKE ?";
        $params[] = "%".$_GET['jurusan']."%";
        $types .= "s";
    }
    if (!empty($_GET['program'])) {
        $where[] = "program_magister LIKE ?";
        $params[] = "%".$_GET['program']."%";
        $types .= "s";
    }
}

$sql = "SELECT * FROM jadwal_zoom";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY tanggal_rapat DESC, waktu_mulai DESC";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$hari_indonesia = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Jadwal Zoom</h2>
    <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Jadwal</a>
</div>

<!-- Filter Box -->
<div class="card mb-4">
    <div class="card-body bg-light">
        <form method="GET" action="">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $_GET['tanggal'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Bulan</label>
                    <select name="bulan" class="form-control">
                        <option value="">Semua</option>
                        <?php for($i=1;$i<=12;$i++): ?>
                        <option value="<?= str_pad($i,2,'0',STR_PAD_LEFT) ?>" <?= (isset($_GET['bulan']) && $_GET['bulan'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="<?= $_GET['tahun'] ?? '' ?>" placeholder="2026">
                </div>
                <div class="col-md-2">
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" class="form-control" value="<?= $_GET['jurusan'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Program</label>
                    <input type="text" name="program" class="form-control" value="<?= $_GET['program'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="cari" value="1" class="btn btn-info w-100 mb-1">Cari</button>
                    <a href="index.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Nama Rapat</th>
                        <th>Jurusan</th>
                        <th>Program Magister</th>
                        <th>Link Zoom</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): $no=1; while($row = $result->fetch_assoc()): 
                        $hari_en = date('l', strtotime($row['tanggal_rapat']));
                        $hari_id = $hari_indonesia[$hari_en];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $hari_id ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal_rapat'])) ?></td>
                        <td><?= substr($row['waktu_mulai'],0,5) ?> - <?= substr($row['waktu_selesai'],0,5) ?></td>
                        <td><?= htmlspecialchars($row['nama_rapat']) ?></td>
                        <td><?= htmlspecialchars($row['jurusan']) ?></td>
                        <td><?= htmlspecialchars($row['program_magister']) ?></td>
                        <td><a href="<?= htmlspecialchars($row['link_zoom']) ?>" target="_blank" class="btn btn-sm btn-primary">Join</a></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i> Detail</a>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus data ini?');" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
""",
    "jadwal/tambah.php": """
<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';
require_once '../layouts/header.php';
?>
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Tambah Jadwal Rapat Zoom</h4>
    </div>
    <div class="card-body">
        <form action="simpan.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Tanggal Rapat *</label>
                    <input type="date" name="tanggal_rapat" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Jam Mulai *</label>
                    <input type="time" name="waktu_mulai" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Jam Selesai *</label>
                    <input type="time" name="waktu_selesai" class="form-control" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nama Rapat *</label>
                    <input type="text" name="nama_rapat" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Jenis Rapat</label>
                    <input type="text" name="jenis_rapat" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Jurusan</label>
                    <select name="jurusan" class="form-control">
                        <option value="">Pilih Jurusan</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Ilmu Ekonomi">Ilmu Ekonomi</option>
                        <option value="Administrasi">Administrasi</option>
                        <option value="Hukum">Hukum</option>
                        <option value="Teknik">Teknik</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Program Magister</label>
                    <select name="program_magister" class="form-control">
                        <option value="">Pilih Program</option>
                        <option value="Magister Manajemen">Magister Manajemen</option>
                        <option value="Magister Akuntansi">Magister Akuntansi</option>
                        <option value="Magister Ilmu Ekonomi">Magister Ilmu Ekonomi</option>
                        <option value="Magister Administrasi">Magister Administrasi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Link Zoom *</label>
                    <input type="url" name="link_zoom" class="form-control" required placeholder="https://zoom.us/j/...">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Password Zoom</label>
                    <input type="text" name="password_zoom" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label>Dokumentasi (PDF/JPG/PNG)</label>
                <input type="file" name="dokumentasi" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
            </div>

            <div class="mb-3">
                <label>Hasil Rapat</label>
                <textarea name="hasil_rapat" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
""",
    "jadwal/simpan.php": """
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
    $password_zoom = $_POST['password_zoom'];
    $hasil_rapat = $_POST['hasil_rapat'];
    $keterangan = $_POST['keterangan'];
    $created_by = $_SESSION['user_id'];
    
    $file_dokumentasi = "";
    
    if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['dokumentasi']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['dokumentasi']['size'] <= 5000000) { // max 5MB
            $new_filename = uniqid() . '_' . time() . '.' . $ext;
            $upload_dir = '../uploads/dokumentasi/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $upload_dir . $new_filename)) {
                $file_dokumentasi = $new_filename;
            }
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO jadwal_zoom (tanggal_rapat, waktu_mulai, waktu_selesai, nama_rapat, jenis_rapat, jurusan, program_magister, link_zoom, password_zoom, hasil_rapat, keterangan, dokumentasi, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $tanggal_rapat, $waktu_mulai, $waktu_selesai, $nama_rapat, $jenis_rapat, $jurusan, $program_magister, $link_zoom, $password_zoom, $hasil_rapat, $keterangan, $file_dokumentasi, $created_by);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . $stmt->error;
    }
}
""",
    "jadwal/hapus.php": """
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
""",
    "jadwal/detail.php": """
<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if (!isset($_GET['id'])) die('ID tidak ditemukan');
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT j.*, u.nama as pembuat FROM jadwal_zoom j LEFT JOIN users u ON j.created_by = u.id WHERE j.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) die('Data tidak ditemukan');

$hari_indonesia = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];
$hari_en = date('l', strtotime($data['tanggal_rapat']));
$hari_id = $hari_indonesia[$hari_en];
$tanggal_indo = date('d F Y', strtotime($data['tanggal_rapat']));

require_once '../layouts/header.php';
?>
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between">
        <h5 class="mb-0">Detail Rapat Zoom</h5>
        <a href="index.php" class="btn btn-sm btn-light">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <tr>
                <th width="200">Tanggal</th>
                <td><?= $hari_id ?>, <?= $tanggal_indo ?></td>
            </tr>
            <tr>
                <th>Waktu</th>
                <td><?= substr($data['waktu_mulai'],0,5) ?> - <?= substr($data['waktu_selesai'],0,5) ?> WIB</td>
            </tr>
            <tr>
                <th>Nama Rapat</th>
                <td><?= htmlspecialchars($data['nama_rapat']) ?></td>
            </tr>
            <tr>
                <th>Jenis Rapat</th>
                <td><?= htmlspecialchars($data['jenis_rapat']) ?></td>
            </tr>
            <tr>
                <th>Jurusan</th>
                <td><?= htmlspecialchars($data['jurusan']) ?></td>
            </tr>
            <tr>
                <th>Program Magister</th>
                <td><?= htmlspecialchars($data['program_magister']) ?></td>
            </tr>
            <tr>
                <th>Link Zoom</th>
                <td>
                    <a href="<?= htmlspecialchars($data['link_zoom']) ?>" target="_blank" class="btn btn-primary"><i class="bi bi-camera-video"></i> Bergabung ke Zoom</a>
                    <br><small><?= htmlspecialchars($data['link_zoom']) ?></small>
                </td>
            </tr>
            <tr>
                <th>Password Zoom</th>
                <td><?= htmlspecialchars($data['password_zoom']) ?></td>
            </tr>
            <tr>
                <th>Hasil Rapat</th>
                <td><?= nl2br(htmlspecialchars($data['hasil_rapat'])) ?></td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td><?= nl2br(htmlspecialchars($data['keterangan'])) ?></td>
            </tr>
            <tr>
                <th>Dokumentasi</th>
                <td>
                    <?php if(!empty($data['dokumentasi'])): ?>
                        <?php 
                        $ext = strtolower(pathinfo($data['dokumentasi'], PATHINFO_EXTENSION));
                        $path = base_url('uploads/dokumentasi/' . $data['dokumentasi']);
                        if(in_array($ext, ['jpg','jpeg','png'])):
                        ?>
                            <img src="<?= $path ?>" class="img-fluid rounded border mb-2" style="max-height: 200px; display:block;">
                            <a href="<?= $path ?>" download class="btn btn-sm btn-success">Download Dokumentasi</a>
                        <?php else: ?>
                            <a href="<?= $path ?>" target="_blank" class="btn btn-sm btn-info">Lihat Dokumentasi (PDF)</a>
                            <a href="<?= $path ?>" download class="btn btn-sm btn-success">Download PDF</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada dokumentasi</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
""",
    "kalender/index.php": """
<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hari_pertama = date('w', strtotime("$tahun-$bulan-01"));
// Adjust so Monday is 1, Sunday is 7 (or 0 depending on loop)
$hari_pertama = ($hari_pertama == 0) ? 7 : $hari_pertama;

// Ambil data jadwal bulan ini
$q = $conn->query("SELECT id, tanggal_rapat, nama_rapat, waktu_mulai FROM jadwal_zoom WHERE MONTH(tanggal_rapat) = '$bulan' AND YEAR(tanggal_rapat) = '$tahun'");
$jadwal_array = [];
while($r = $q->fetch_assoc()){
    $tgl = (int)date('d', strtotime($r['tanggal_rapat']));
    $jadwal_array[$tgl][] = $r;
}

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Kalender Jadwal Zoom</h3>
    <form class="d-flex" method="GET">
        <select name="bulan" class="form-select me-2">
            <?php for($i=1;$i<=12;$i++): ?>
            <option value="<?= str_pad($i,2,'0',STR_PAD_LEFT) ?>" <?= ($bulan==$i)?'selected':'' ?>><?= date("F", mktime(0,0,0,$i,10)) ?></option>
            <?php endfor; ?>
        </select>
        <input type="number" name="tahun" class="form-control me-2" value="<?= $tahun ?>" style="width: 100px;">
        <button type="submit" class="btn btn-primary">Lihat</button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered mb-0" style="table-layout: fixed; min-height: 500px;">
            <thead class="bg-light text-center">
                <tr>
                    <th width="14%">Senin</th>
                    <th width="14%">Selasa</th>
                    <th width="14%">Rabu</th>
                    <th width="14%">Kamis</th>
                    <th width="14%">Jumat</th>
                    <th width="14%">Sabtu</th>
                    <th width="14%">Minggu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php 
                $hari_ini = 1;
                // Kosong sebelum tanggal 1
                for($i=1; $i < $hari_pertama; $i++){
                    echo "<td></td>";
                    $hari_ini++;
                }
                
                for($tgl=1; $tgl<=$jumlah_hari; $tgl++){
                    $is_today = ($tgl == date('j') && $bulan == date('m') && $tahun == date('Y')) ? 'bg-warning bg-opacity-25' : '';
                    echo "<td class='p-2 align-top $is_today' style='height: 100px;'>";
                    echo "<strong>$tgl</strong><br>";
                    
                    if(isset($jadwal_array[$tgl])){
                        foreach($jadwal_array[$tgl] as $j){
                            echo "<a href='../jadwal/detail.php?id={$j['id']}' class='badge bg-primary text-wrap text-start d-block mb-1 text-decoration-none p-2' style='font-size:0.75rem;' title='{$j['nama_rapat']}'>";
                            echo substr($j['waktu_mulai'],0,5) . " - " . htmlspecialchars($j['nama_rapat']);
                            echo "</a>";
                        }
                    }
                    echo "</td>";
                    
                    if($hari_ini % 7 == 0 && $tgl != $jumlah_hari){
                        echo "</tr><tr>";
                    }
                    $hari_ini++;
                }
                
                // Kosong setelah akhir bulan
                while(($hari_ini-1) % 7 != 0){
                    echo "<td></td>";
                    $hari_ini++;
                }
                ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>
"""
}

for path, content in files.items():
    full_path = os.path.join(project_dir, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(content.strip() + "\n")

print("Project files generated successfully!")
