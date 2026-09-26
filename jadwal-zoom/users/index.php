<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$q = $conn->query("SELECT * FROM users ORDER BY id ASC");

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Manajemen User</h3>
    <a href="tambah.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah User</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Dibuat Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while($r = $q->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= htmlspecialchars($r['role']) ?></td>
                    <td><?= $r['created_at'] ?></td>
                    <td>
                        <a href="hapus.php?id=<?= $r['id'] ?>" onclick="return confirm('Hapus user ini?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
