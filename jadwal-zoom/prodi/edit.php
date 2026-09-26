<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: index.php"); exit; }

$q = $conn->query("SELECT * FROM prodi WHERE id=$id");
$data = $q->fetch_assoc();
if (!$data) { header("Location: index.php"); exit; }

$jurusan_list = $conn->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode       = trim($_POST['kode_prodi'] ?? '');
    $nama       = trim($_POST['nama_prodi'] ?? '');
    $jurusan_id = (int)($_POST['jurusan_id'] ?? 0);
    $jenjang    = trim($_POST['jenjang'] ?? 'S1');
    $ket        = trim($_POST['keterangan'] ?? '');

    if (empty($kode) || empty($nama) || !$jurusan_id) {
        $error = 'Kode, Nama Prodi, dan Jurusan wajib diisi!';
    } else {
        $stmt = $conn->prepare("UPDATE prodi SET kode_prodi=?, nama_prodi=?, jurusan_id=?, jenjang=?, keterangan=? WHERE id=?");
        $stmt->bind_param("ssissi", $kode, $nama, $jurusan_id, $jenjang, $ket, $id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=Program studi berhasil diperbarui");
            exit;
        } else {
            $error = 'Gagal memperbarui: ' . $conn->error;
        }
    }
    $data = array_merge($data, ['kode_prodi'=>$kode,'nama_prodi'=>$nama,'jurusan_id'=>$jurusan_id,'jenjang'=>$jenjang,'keterangan'=>$ket]);
}

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Program Studi</h3>
        <small class="text-muted">Perbarui data program studi</small>
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
                <label class="form-label fw-semibold">Jurusan <span class="text-danger">*</span></label>
                <select name="jurusan_id" class="form-select" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php while ($j = $jurusan_list->fetch_assoc()): ?>
                    <option value="<?= $j['id'] ?>" <?= ($data['jurusan_id'] == $j['id']) ? 'selected' : '' ?>>
                        [<?= htmlspecialchars($j['kode_jurusan']) ?>] <?= htmlspecialchars($j['nama_jurusan']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Prodi <span class="text-danger">*</span></label>
                <input type="text" name="kode_prodi" class="form-control" required value="<?= htmlspecialchars($data['kode_prodi']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Program Studi <span class="text-danger">*</span></label>
                <input type="text" name="nama_prodi" class="form-control" required value="<?= htmlspecialchars($data['nama_prodi']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jenjang</label>
                <select name="jenjang" class="form-select">
                    <?php foreach (['D3','D4','S1','S2','S3','Profesi'] as $j): ?>
                    <option value="<?= $j ?>" <?= ($data['jenjang'] === $j) ? 'selected' : '' ?>><?= $j ?></option>
                    <?php endforeach; ?>
                </select>
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
