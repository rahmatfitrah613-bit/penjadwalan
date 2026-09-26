<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: index.php"); exit; }

$q = $conn->query("SELECT * FROM jurusan WHERE id=$id");
$data = $q->fetch_assoc();
if (!$data) { header("Location: index.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_jurusan'] ?? '');
    $nama = trim($_POST['nama_jurusan'] ?? '');
    $ket  = trim($_POST['keterangan'] ?? '');

    if (empty($kode) || empty($nama)) {
        $error = 'Kode dan Nama Jurusan wajib diisi!';
    } else {
        $stmt = $conn->prepare("UPDATE jurusan SET kode_jurusan=?, nama_jurusan=?, keterangan=? WHERE id=?");
        $stmt->bind_param("sssi", $kode, $nama, $ket, $id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=Jurusan berhasil diperbarui");
            exit;
        } else {
            $error = 'Gagal memperbarui: ' . $conn->error;
        }
    }
    $data['kode_jurusan'] = $kode;
    $data['nama_jurusan'] = $nama;
    $data['keterangan']   = $ket;
}

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Jurusan</h3>
        <small class="text-muted">Perbarui data jurusan</small>
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
                <input type="text" name="kode_jurusan" class="form-control" required value="<?= htmlspecialchars($data['kode_jurusan']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Jurusan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jurusan" class="form-control" required value="<?= htmlspecialchars($data['nama_jurusan']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Perbarui</button>
                <a href="index.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
