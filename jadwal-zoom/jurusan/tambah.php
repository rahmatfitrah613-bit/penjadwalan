<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode   = trim($_POST['kode_jurusan'] ?? '');
    $nama   = trim($_POST['nama_jurusan'] ?? '');
    $ket    = trim($_POST['keterangan'] ?? '');

    if (empty($kode) || empty($nama)) {
        $error = 'Kode dan Nama Jurusan wajib diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO jurusan (kode_jurusan, nama_jurusan, keterangan) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kode, $nama, $ket);
        if ($stmt->execute()) {
            header("Location: index.php?msg=Jurusan berhasil ditambahkan");
            exit;
        } else {
            $error = 'Gagal menyimpan: ' . $conn->error;
        }
    }
}

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Jurusan</h3>
        <small class="text-muted">Tambahkan data jurusan baru</small>
    </div>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Jurusan <span class="text-danger">*</span></label>
                <input type="text" name="kode_jurusan" class="form-control" placeholder="Contoh: TI, SI, AK" required value="<?= htmlspecialchars($_POST['kode_jurusan'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Jurusan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jurusan" class="form-control" placeholder="Contoh: Teknik Informatika" required value="<?= htmlspecialchars($_POST['nama_jurusan'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="index.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
