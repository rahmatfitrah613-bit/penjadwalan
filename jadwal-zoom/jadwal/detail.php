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
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <tr class="table-danger">
                <th><i class="bi bi-shield-lock-fill text-danger"></i> Link Zoom Admin</th>
                <td>
                    <?php if (!empty($data['link_zoom_admin'])): ?>
                        <a href="<?= htmlspecialchars($data['link_zoom_admin']) ?>" target="_blank" class="btn btn-danger"><i class="bi bi-camera-video-fill"></i> Bergabung sebagai Host/Admin</a>
                        <br><small class="text-muted"><?= htmlspecialchars($data['link_zoom_admin']) ?></small>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Belum diisi</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Kode Token Zoom</th>
                <td><?= htmlspecialchars($data['token_zoom'] ?? '-') ?></td>
            </tr>
            <?php endif; ?>
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
                <th>Dokumentasi (Foto)</th>
                <td>
                    <?php if(!empty($data['dokumentasi'])): ?>
                        <?php 
                        $path = base_url('uploads/dokumentasi/' . $data['dokumentasi']);
                        ?>
                        <img src="<?= $path ?>" class="img-fluid rounded border mb-2" style="max-height: 200px; display:block;">
                        <a href="<?= $path ?>" download class="btn btn-sm btn-success">Download Foto</a>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada foto</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Video Hasil Rapat</th>
                <td>
                    <?php if(!empty($data['file_video'])): ?>
                        <?php 
                        $path = base_url('uploads/dokumentasi/' . $data['file_video']);
                        ?>
                        <video width="320" height="240" controls class="mb-2 border">
                            <source src="<?= $path ?>" type="video/mp4">
                            Browser Anda tidak mendukung tag video.
                        </video><br>
                        <a href="<?= $path ?>" download class="btn btn-sm btn-success">Download Video</a>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada video</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Materi Rapat</th>
                <td>
                    <?php if(!empty($data['file_materi'])): ?>
                        <?php 
                        $path = base_url('uploads/dokumentasi/' . $data['file_materi']);
                        ?>
                        <a href="<?= $path ?>" target="_blank" class="btn btn-sm btn-info">Lihat Materi</a>
                        <a href="<?= $path ?>" download class="btn btn-sm btn-success">Download Materi</a>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada materi</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
