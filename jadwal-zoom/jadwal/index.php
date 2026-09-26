<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

$where = [];
$params = [];
$types = "";

if (isset($_GET['cari'])) {
    if (!empty($_GET['tanggal'])) {
        $where[] = "tanggal_rapat = ?";
        $params[] = $_GET['tanggal'];
        $types .= "s";
    }
    if (!empty($_GET['bulan'])) {
        $where[] = "MONTH(tanggal_rapat) = ?";
        $params[] = $_GET['bulan'];
        $types .= "s";
    }
    if (!empty($_GET['tahun'])) {
        $where[] = "YEAR(tanggal_rapat) = ?";
        $params[] = $_GET['tahun'];
        $types .= "s";
    }
    if (!empty($_GET['jurusan'])) {
        $where[] = "jurusan LIKE ?";
        $params[] = "%".$_GET['jurusan']."%";
        $types .= "s";
    }
    if (!empty($_GET['program'])) {
        $where[] = "program_magister LIKE ?";
        $params[] = "%".$_GET['program']."%";
        $types .= "s";
    }
}

$sql = "SELECT * FROM jadwal_zoom";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY tanggal_rapat DESC, waktu_mulai DESC";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$hari_indonesia = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Jadwal Zoom</h2>
    <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Jadwal</a>
</div>

<!-- Filter Box -->
<div class="card mb-4">
    <div class="card-body bg-light">
        <form method="GET" action="">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $_GET['tanggal'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Bulan</label>
                    <select name="bulan" class="form-control">
                        <option value="">Semua</option>
                        <?php for($i=1;$i<=12;$i++): ?>
                        <option value="<?= str_pad($i,2,'0',STR_PAD_LEFT) ?>" <?= (isset($_GET['bulan']) && $_GET['bulan'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="<?= $_GET['tahun'] ?? '' ?>" placeholder="2026">
                </div>
                <div class="col-md-2">
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" class="form-control" value="<?= $_GET['jurusan'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Program</label>
                    <input type="text" name="program" class="form-control" value="<?= $_GET['program'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="cari" value="1" class="btn btn-info w-100 mb-1">Cari</button>
                    <a href="index.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Nama Rapat</th>
                        <th>Jurusan</th>
                        <th>Program Magister</th>
                        <th>Link Zoom</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): $no=1; while($row = $result->fetch_assoc()): 
                        $hari_en = date('l', strtotime($row['tanggal_rapat']));
                        $hari_id = $hari_indonesia[$hari_en];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $hari_id ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal_rapat'])) ?></td>
                        <td><?= substr($row['waktu_mulai'],0,5) ?> - <?= substr($row['waktu_selesai'],0,5) ?></td>
                        <td><?= htmlspecialchars($row['nama_rapat']) ?></td>
                        <td><?= htmlspecialchars($row['jurusan']) ?></td>
                        <td><?= htmlspecialchars($row['program_magister']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($row['link_zoom']) ?>" target="_blank" class="btn btn-sm btn-primary">Join</a>
                            <?php if($_SESSION['role'] === 'admin' && !empty($row['link_zoom_admin'])): ?>
                            <a href="<?= htmlspecialchars($row['link_zoom_admin']) ?>" target="_blank" class="btn btn-sm btn-danger" title="Link Admin/Host"><i class="bi bi-shield-lock-fill"></i> Host</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i> Detail</a>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus data ini?');" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../layouts/footer.php'; ?>
