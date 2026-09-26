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
            <li class="nav-item mb-2">
                <a href="<?= base_url('dokumentasi/index.php') ?>" class="nav-link text-white"><i class="bi bi-folder2-open"></i> Dokumentasi</a>
            </li>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item mb-2">
                <a href="<?= base_url('users/index.php') ?>" class="nav-link text-white"><i class="bi bi-people"></i> Manajemen User</a>
            </li>
            <!-- Master Data Dropdown -->
            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex justify-content-between align-items-center"
                   href="#masterDataMenu"
                   data-bs-toggle="collapse"
                   role="button"
                   aria-expanded="false"
                   aria-controls="masterDataMenu">
                    <span><i class="bi bi-database me-1"></i> Master Data</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse" id="masterDataMenu">
                    <ul class="nav flex-column ms-3 mt-1">
                        <li class="nav-item mb-1">
                            <a href="<?= base_url('jurusan/index.php') ?>" class="nav-link text-white-50 py-1">
                                <i class="bi bi-building me-1"></i> Jurusan
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="<?= base_url('prodi/index.php') ?>" class="nav-link text-white-50 py-1">
                                <i class="bi bi-diagram-3 me-1"></i> Program Studi
                            </a>
                        </li>
                    </ul>
                </div>
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
