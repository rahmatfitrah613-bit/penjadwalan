<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM jadwal_zoom WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("Data tidak ditemukan");

// Ambil data jurusan dari master data
$jurusan_list = $conn->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
$jurusan_data = [];
if ($jurusan_list) {
    while ($row = $jurusan_list->fetch_assoc()) {
        $jurusan_data[] = $row;
    }
}

// Cari jurusan_id yang cocok dengan nama jurusan tersimpan (untuk pre-select prodi)
$saved_jurusan_id = 0;
$saved_jurusan_nama = $data['jurusan'] ?? '';
foreach ($jurusan_data as $jd) {
    if ($jd['nama_jurusan'] === $saved_jurusan_nama) {
        $saved_jurusan_id = $jd['id'];
        break;
    }
}

// Ambil prodi sesuai jurusan tersimpan untuk pre-select
$prodi_list = [];
if ($saved_jurusan_id) {
    $pq = $conn->query("SELECT * FROM prodi WHERE jurusan_id=$saved_jurusan_id ORDER BY nama_prodi ASC");
    if ($pq) {
        while ($row = $pq->fetch_assoc()) {
            $prodi_list[] = $row;
        }
    }
}

require_once '../layouts/header.php';
?>
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Edit Jadwal Rapat</h4>
    </div>
    <div class="card-body">
        <form action="update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Tanggal Rapat *</label>
                    <input type="date" name="tanggal_rapat" class="form-control" value="<?= $data['tanggal_rapat'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Jam Mulai *</label>
                    <input type="time" name="waktu_mulai" class="form-control" value="<?= $data['waktu_mulai'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Jam Selesai *</label>
                    <input type="time" name="waktu_selesai" class="form-control" value="<?= $data['waktu_selesai'] ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nama Rapat *</label>
                    <input type="text" name="nama_rapat" class="form-control" value="<?= htmlspecialchars($data['nama_rapat']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Jenis Rapat</label>
                    <input type="text" name="jenis_rapat" class="form-control" value="<?= htmlspecialchars($data['jenis_rapat']) ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Jurusan</label>
                    <select name="jurusan" id="select_jurusan" class="form-control">
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($jurusan_data as $j): ?>
                        <option value="<?= htmlspecialchars($j['nama_jurusan']) ?>"
                                data-id="<?= $j['id'] ?>"
                                <?= ($saved_jurusan_id == $j['id']) ? 'selected' : '' ?>>
                            [<?= htmlspecialchars($j['kode_jurusan']) ?>] <?= htmlspecialchars($j['nama_jurusan']) ?>
                        </option>
                        <?php endforeach; ?>
                        <?php if (empty($jurusan_data)): ?>
                        <option disabled>Belum ada jurusan — tambahkan di Master Data</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Program Studi</label>
                    <select name="program_magister" id="select_prodi" class="form-control">
                        <?php if (empty($prodi_list)): ?>
                        <option value="">-- Pilih Jurusan dulu --</option>
                        <?php else: ?>
                        <option value="">-- Pilih Program Studi --</option>
                        <?php foreach ($prodi_list as $p): ?>
                        <option value="<?= htmlspecialchars($p['nama_prodi']) ?>"
                                <?= ($data['program_magister'] === $p['nama_prodi']) ? 'selected' : '' ?>>
                            [<?= htmlspecialchars($p['kode_prodi']) ?>] <?= htmlspecialchars($p['nama_prodi']) ?> (<?= $p['jenjang'] ?>)
                        </option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="text-muted" id="prodi_hint"></small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Link Zoom *</label>
                    <input type="url" name="link_zoom" class="form-control" value="<?= htmlspecialchars($data['link_zoom'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Kode Token Zoom</label>
                    <input type="text" name="token_zoom" class="form-control" value="<?= htmlspecialchars($data['token_zoom'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Password Zoom</label>
                    <input type="text" name="password_zoom" class="form-control" value="<?= htmlspecialchars($data['password_zoom'] ?? '') ?>">
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="fw-semibold text-danger"><i class="bi bi-shield-lock-fill"></i> Link Zoom Admin <small class="text-muted fw-normal">(Hanya terlihat oleh Admin)</small></label>
                    <div class="input-group">
                        <span class="input-group-text bg-danger text-white"><i class="bi bi-camera-video-fill"></i></span>
                        <input type="url" name="link_zoom_admin" class="form-control border-danger" value="<?= htmlspecialchars($data['link_zoom_admin'] ?? '') ?>" placeholder="https://zoom.us/j/... (link khusus admin/host)">
                    </div>
                    <small class="text-muted">Link ini hanya ditampilkan kepada pengguna dengan role Admin.</small>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Ganti Foto (JPG/PNG)</label>
                    <input type="file" name="dokumentasi" class="form-control" accept=".jpg,.jpeg,.png">
                    <?php if(!empty($data['dokumentasi'])): ?>
                        <small class="text-muted">Ada file: <?= htmlspecialchars($data['dokumentasi']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ganti Video (MP4)</label>
                    <input type="file" name="file_video" class="form-control" accept=".mp4,.avi">
                    <?php if(!empty($data['file_video'])): ?>
                        <small class="text-muted">Ada file: <?= htmlspecialchars($data['file_video']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ganti Materi (PDF/DOC/PPT)</label>
                    <input type="file" name="file_materi" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx">
                    <?php if(!empty($data['file_materi'])): ?>
                        <small class="text-muted">Ada file: <?= htmlspecialchars($data['file_materi']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <label>Hasil Rapat</label>
                <textarea name="hasil_rapat" class="form-control" rows="3"><?= htmlspecialchars($data['hasil_rapat']) ?></textarea>
            </div>

            <div class="mb-3">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($data['keterangan']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Data</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
// AJAX: Load prodi saat jurusan berubah
document.getElementById('select_jurusan').addEventListener('change', function () {
    const jurusanId = this.options[this.selectedIndex]?.dataset?.id ?? '';
    const prodiSelect = document.getElementById('select_prodi');
    const hint = document.getElementById('prodi_hint');

    prodiSelect.innerHTML = '<option value="">Memuat...</option>';

    if (!jurusanId) {
        prodiSelect.innerHTML = '<option value="">-- Pilih Jurusan dulu --</option>';
        hint.textContent = '';
        return;
    }

    fetch(`<?= base_url('prodi/get_by_jurusan.php') ?>?jurusan_id=` + jurusanId)
        .then(r => r.json())
        .then(data => {
            prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
            if (data.length === 0) {
                prodiSelect.innerHTML += '<option disabled>Belum ada prodi untuk jurusan ini</option>';
                hint.innerHTML = '<a href="<?= base_url('prodi/tambah.php') ?>" target="_blank">Tambah prodi</a> untuk jurusan ini.';
            } else {
                data.forEach(p => {
                    prodiSelect.innerHTML += `<option value="${p.nama_prodi}">[${p.kode_prodi}] ${p.nama_prodi} (${p.jenjang})</option>`;
                });
                hint.textContent = '';
            }
        })
        .catch(() => {
            prodiSelect.innerHTML = '<option value="">Gagal memuat prodi</option>';
        });
});
</script>
<?php require_once '../layouts/footer.php'; ?>
