<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$jurusan_list = $conn->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode        = trim($_POST['kode_prodi'] ?? '');
    $nama        = trim($_POST['nama_prodi'] ?? '');
    $jurusan_id  = (int)($_POST['jurusan_id'] ?? 0);
    $jenjang     = trim($_POST['jenjang'] ?? 'S1');
    $ket         = trim($_POST['keterangan'] ?? '');

    if (empty($kode) || empty($nama) || !$jurusan_id) {
        $error = 'Kode, Nama Prodi, dan Jurusan wajib diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO prodi (kode_prodi, nama_prodi, jurusan_id, jenjang, keterangan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $kode, $nama, $jurusan_id, $jenjang, $ket);
        if ($stmt->execute()) {
            header("Location: index.php?msg=Program studi berhasil ditambahkan");
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
        <h3 class="mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>Tambah Program Studi</h3>
        <small class="text-muted">Tambahkan data program studi baru</small>
    </div>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($jurusan_list->num_rows === 0): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Belum ada data jurusan. <a href="<?= base_url('jurusan/tambah.php') ?>">Tambah Jurusan</a> terlebih dahulu.
</div>
<?php else: ?>
<div class="card shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Jurusan <span class="text-danger">*</span></label>
                <select name="jurusan_id" class="form-select" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php while ($j = $jurusan_list->fetch_assoc()): ?>
                    <option value="<?= $j['id'] ?>" <?= (($_POST['jurusan_id'] ?? '') == $j['id']) ? 'selected' : '' ?>>
                        [<?= htmlspecialchars($j['kode_jurusan']) ?>] <?= htmlspecialchars($j['nama_jurusan']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Prodi <span class="text-danger">*</span></label>
                <input type="text" name="kode_prodi" class="form-control" placeholder="Contoh: TI-S1, SI-S2" required value="<?= htmlspecialchars($_POST['kode_prodi'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Program Studi <span class="text-danger">*</span></label>
                <input type="text" name="nama_prodi" class="form-control" placeholder="Contoh: Teknik Informatika" required value="<?= htmlspecialchars($_POST['nama_prodi'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jenjang</label>
                <select name="jenjang" class="form-select">
                    <?php foreach (['D3','D4','S1','S2','S3','Profesi'] as $j): ?>
                    <option value="<?= $j ?>" <?= (($_POST['jenjang'] ?? 'S1') === $j) ? 'selected' : '' ?>><?= $j ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="index.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php require_once '../layouts/footer.php'; ?>
