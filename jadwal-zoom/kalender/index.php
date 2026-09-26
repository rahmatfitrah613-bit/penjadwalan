<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hari_pertama = date('w', strtotime("$tahun-$bulan-01"));
// Adjust so Monday is 1, Sunday is 7 (or 0 depending on loop)
$hari_pertama = ($hari_pertama == 0) ? 7 : $hari_pertama;

// Ambil data jadwal bulan ini
$q = $conn->query("SELECT id, tanggal_rapat, nama_rapat, waktu_mulai FROM jadwal_zoom WHERE MONTH(tanggal_rapat) = '$bulan' AND YEAR(tanggal_rapat) = '$tahun'");
$jadwal_array = [];
while($r = $q->fetch_assoc()){
    $tgl = (int)date('d', strtotime($r['tanggal_rapat']));
    $jadwal_array[$tgl][] = $r;
}

require_once '../layouts/header.php';
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Kalender Jadwal Zoom</h3>
    <form class="d-flex" method="GET">
        <select name="bulan" class="form-select me-2">
            <?php for($i=1;$i<=12;$i++): ?>
            <option value="<?= str_pad($i,2,'0',STR_PAD_LEFT) ?>" <?= ($bulan==$i)?'selected':'' ?>><?= date("F", mktime(0,0,0,$i,10)) ?></option>
            <?php endfor; ?>
        </select>
        <input type="number" name="tahun" class="form-control me-2" value="<?= $tahun ?>" style="width: 100px;">
        <button type="submit" class="btn btn-primary">Lihat</button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered mb-0" style="table-layout: fixed; min-height: 500px;">
            <thead class="bg-light text-center">
                <tr>
                    <th width="14%">Senin</th>
                    <th width="14%">Selasa</th>
                    <th width="14%">Rabu</th>
                    <th width="14%">Kamis</th>
                    <th width="14%">Jumat</th>
                    <th width="14%">Sabtu</th>
                    <th width="14%">Minggu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php 
                $hari_ini = 1;
                // Kosong sebelum tanggal 1
                for($i=1; $i < $hari_pertama; $i++){
                    echo "<td></td>";
                    $hari_ini++;
                }
                
                for($tgl=1; $tgl<=$jumlah_hari; $tgl++){
                    $is_today = ($tgl == date('j') && $bulan == date('m') && $tahun == date('Y')) ? 'bg-warning bg-opacity-25' : '';
                    echo "<td class='p-2 align-top $is_today' style='height: 100px;'>";
                    echo "<strong>$tgl</strong><br>";
                    
                    if(isset($jadwal_array[$tgl])){
                        foreach($jadwal_array[$tgl] as $j){
                            echo "<a href='../jadwal/detail.php?id={$j['id']}' class='badge bg-primary text-wrap text-start d-block mb-1 text-decoration-none p-2' style='font-size:0.75rem;' title='{$j['nama_rapat']}'>";
                            echo substr($j['waktu_mulai'],0,5) . " - " . htmlspecialchars($j['nama_rapat']);
                            echo "</a>";
                        }
                    }
                    echo "</td>";
                    
                    if($hari_ini % 7 == 0 && $tgl != $jumlah_hari){
                        echo "</tr><tr>";
                    }
                    $hari_ini++;
                }
                
                // Kosong setelah akhir bulan
                while(($hari_ini-1) % 7 != 0){
                    echo "<td></td>";
                    $hari_ini++;
                }
                ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>
