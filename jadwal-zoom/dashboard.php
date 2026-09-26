<?php
require_once 'config/database.php';
require_once 'auth/auth_check.php';

// Menghitung statistik
$today = date('Y-m-d');
$week_end = date('Y-m-d', strtotime('+7 days'));

$total = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom")->fetch_assoc()['c'];
$hari_ini = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom WHERE tanggal_rapat = '$today'")->fetch_assoc()['c'];
$minggu_ini = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom WHERE tanggal_rapat >= '$today' AND tanggal_rapat <= '$week_end'")->fetch_assoc()['c'];
$akan_datang = $conn->query("SELECT COUNT(id) as c FROM jadwal_zoom WHERE tanggal_rapat > '$today'")->fetch_assoc()['c'];

require_once 'layouts/header.php';
?>
<h2 class="mb-4">Dashboard</h2>
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Rapat</h5>
                <h2><?= $total ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Rapat Hari Ini</h5>
                <h2><?= $hari_ini ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Rapat 7 Hari Kedepan</h5>
                <h2><?= $minggu_ini ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Akan Datang</h5>
                <h2><?= $akan_datang ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header bg-white">
        <strong>Jadwal Rapat Terdekat</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Nama Rapat</th>
                        <th>Jurusan</th>
                        <th>Program</th>
                        <th>Link Zoom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = $conn->query("SELECT * FROM jadwal_zoom WHERE tanggal_rapat >= '$today' ORDER BY tanggal_rapat ASC, waktu_mulai ASC LIMIT 5");
                    while($r = $q->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($r['tanggal_rapat'])) ?></td>
                        <td><?= substr($r['waktu_mulai'],0,5) ?> - <?= substr($r['waktu_selesai'],0,5) ?></td>
                        <td><?= htmlspecialchars($r['nama_rapat']) ?></td>
                        <td><?= htmlspecialchars($r['jurusan']) ?></td>
                        <td><?= htmlspecialchars($r['program_magister']) ?></td>
                        <td><a href="<?= htmlspecialchars($r['link_zoom']) ?>" target="_blank" class="btn btn-sm btn-primary">Join</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'layouts/footer.php'; ?>
