<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$q = $conn->query("
    SELECT p.*, j.nama_jurusan, j.kode_jurusan 
    FROM prodi p 
    LEFT JOIN jurusan j ON p.jurusan_id = j.id 
    ORDER BY j.nama_jurusan ASC, p.nama_prodi ASC
");

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="bi bi-diagram-3 me-2 text-success"></i>Data Program Studi</h3>
        <small class="text-muted">Kelola daftar program studi yang tersedia</small>
    </div>
    <a href="tambah.php" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i> Tambah Prodi
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
                    <th>Kode Prodi</th>
                    <th>Nama Program Studi</th>
                    <th>Jurusan</th>
                    <th>Jenjang</th>
                    <th>Keterangan</th>
                    <th style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($r = $q->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($r['kode_prodi']) ?></span></td>
                    <td><strong><?= htmlspecialchars($r['nama_prodi']) ?></strong></td>
                    <td>
                        <span class="badge bg-primary"><?= htmlspecialchars($r['kode_jurusan'] ?? '-') ?></span>
                        <?= htmlspecialchars($r['nama_jurusan'] ?? '-') ?>
                    </td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r['jenjang'] ?? 'S1') ?></span></td>
                    <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                    <td>
                        <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="hapus.php?id=<?= $r['id'] ?>" onclick="return confirm('Hapus prodi ini?')" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($q->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data program studi</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
