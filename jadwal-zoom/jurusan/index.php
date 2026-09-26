<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$q = $conn->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="bi bi-building me-2 text-primary"></i>Data Jurusan</h3>
        <small class="text-muted">Kelola daftar jurusan yang tersedia</small>
    </div>
    <a href="tambah.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Jurusan
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_GET['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px">No</th>
                    <th>Kode Jurusan</th>
                    <th>Nama Jurusan</th>
                    <th>Keterangan</th>
                    <th style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($r = $q->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($r['kode_jurusan']) ?></span></td>
                    <td><strong><?= htmlspecialchars($r['nama_jurusan']) ?></strong></td>
                    <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                    <td>
                        <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="hapus.php?id=<?= $r['id'] ?>" onclick="return confirm('Hapus jurusan ini? Semua prodi terkait juga akan terhapus!')" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($q->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data jurusan</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
