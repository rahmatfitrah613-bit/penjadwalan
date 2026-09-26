<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

$sql = "SELECT * FROM jadwal_zoom WHERE dokumentasi != '' OR file_video != '' OR file_materi != '' ORDER BY tanggal_rapat DESC";
$q = $conn->query($sql);

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Arsip Dokumentasi Zoom</h3>
</div>

<div class="row">
    <?php if($q->num_rows > 0): while($r = $q->fetch_assoc()): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <?php if(!empty($r['dokumentasi'])): ?>
                <img src="<?= base_url('uploads/dokumentasi/'.$r['dokumentasi']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
            <?php else: ?>
                <div class="bg-secondary text-white d-flex justify-content-center align-items-center" style="height: 200px;">
                    <span>Tanpa Foto</span>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($r['nama_rapat']) ?></h5>
                <p class="card-text text-muted small">
                    <i class="bi bi-calendar"></i> <?= date('d-m-Y', strtotime($r['tanggal_rapat'])) ?> <br>
                    <i class="bi bi-tag"></i> <?= htmlspecialchars($r['jurusan'] . ' - ' . $r['program_magister']) ?>
                </p>
                <hr>
                <div class="d-grid gap-2">
                    <?php if(!empty($r['file_video'])): ?>
                        <a href="<?= base_url('uploads/dokumentasi/'.$r['file_video']) ?>" class="btn btn-sm btn-outline-danger" target="_blank"><i class="bi bi-play-circle"></i> Lihat Video</a>
                    <?php endif; ?>
                    <?php if(!empty($r['file_materi'])): ?>
                        <a href="<?= base_url('uploads/dokumentasi/'.$r['file_materi']) ?>" class="btn btn-sm btn-outline-info" target="_blank"><i class="bi bi-file-earmark-text"></i> Buka Materi</a>
                    <?php endif; ?>
                    <a href="<?= base_url('jadwal/detail.php?id='.$r['id']) ?>" class="btn btn-sm btn-primary">Lihat Detail Rapat</a>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; else: ?>
        <div class="col-12">
            <div class="alert alert-info">Belum ada dokumentasi rapat (Foto, Video, atau Materi) yang diunggah.</div>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../layouts/footer.php'; ?>
