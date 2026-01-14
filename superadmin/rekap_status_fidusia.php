<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

// filter
$kedudukan = $_GET['kedudukan'] ?? 'Semua';
$status    = $_GET['status'] ?? 'Semua';
$tahun     = $_GET['tahun'] ?? date('Y');

// fungsi WA
function make_whatsapp_link($raw_phone) {
    $digits = preg_replace('/\D+/', '', $raw_phone);
    if ($digits === '') return false;

    if (preg_match('/^0/', $digits)) {
        $digits = preg_replace('/^0+/', '62', $digits);
    }
    if (preg_match('/^8\d{6,}$/', $digits)) {
        $digits = '62' . $digits;
    }

    $len = strlen($digits);
    if ($len < 10 || $len > 16) return false;

    return "https://api.whatsapp.com/send?phone=" . $digits;
}
?>

<style>
.page-head-line { font-weight:600; color:#2c3e50; margin-bottom:25px; }
.filter-form { background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; padding:15px; margin-bottom:20px; }
.table th { background:#2c3e50; color:#fff; text-align:center; font-weight:600; }
.table td { vertical-align:middle !important; text-align:center; }
.wa-link { color:#25d366; font-weight:600; }
</style>

<div id="page-wrapper">
<div id="page-inner">

<h1 class="page-head-line">📊 Rekap Status Penyampaian Laporan Fidusia</h1>

<!-- FILTER -->
<form method="GET" class="form-inline filter-form">

    <label><b>Tahun:</b></label>
    <input type="number" name="tahun" value="<?=$tahun?>" class="form-control" style="width:100px; margin:0 10px;">

    <label><b>Kedudukan:</b></label>
    <select name="kedudukan" class="form-control" style="width:220px; margin:0 10px;">
        <option value="Semua">Semua Daerah</option>
        <?php
        $stmt = $koneksi->query("SELECT id_kedudukan,nama_kedudukan FROM kedudukan ORDER BY nama_kedudukan");
        while ($k = $stmt->fetch()) {
            $sel = ($kedudukan==$k['id_kedudukan'])?'selected':'';
            echo "<option value='{$k['id_kedudukan']}' $sel>{$k['nama_kedudukan']}</option>";
        }
        ?>
    </select>

    <label><b>Status:</b></label>
    <select name="status" class="form-control" style="width:240px; margin:0 10px;">
        <option value="Semua">Semua Status</option>
        <option <?=($status=='BELUM MENYAMPAIKAN'?'selected':'')?> value="BELUM MENYAMPAIKAN">BELUM MENYAMPAIKAN</option>
        <option <?=($status=='HANYA NIHIL'?'selected':'')?> value="HANYA NIHIL">HANYA NIHIL</option>
        <option <?=($status=='ADA ISI'?'selected':'')?> value="ADA ISI">ADA ISI</option>
    </select>

    <button class="btn btn-primary btn-sm">
        <i class="fa fa-search"></i> Tampilkan
    </button>

    <!-- <a href="export_fidusia_status.php?tahun=<?=$tahun?>&kedudukan=<?=$kedudukan?>&status=<?=$status?>"
       class="btn btn-success btn-sm" style="margin-left:10px;">
        <i class="fa fa-file-excel-o"></i> Export Excel
    </a> -->
</form>

<?php
// ======================
// REKAP JUMLAH PER STATUS (QUERY)
// ======================
$sqlRekap = "
SELECT status_input_fidusia, COUNT(*) jumlah FROM (
    SELECT n.id_notaris,
        CASE
            WHEN COUNT(l.id_laporan)=0 THEN 'BELUM MENYAMPAIKAN'
            WHEN COUNT(l.id_laporan)>0 AND SUM(l.jenis_transaksi<>'NIHIL')=0 THEN 'HANYA NIHIL'
            ELSE 'ADA ISI'
        END status_input_fidusia
    FROM notaris n
    LEFT JOIN laporan_entitas l 
        ON l.id_notaris=n.id_notaris 
        AND l.tipe='fidusia'
        AND YEAR(l.tanggal)=:tahun
    WHERE n.aktif='1' AND n.level='2'
";

if ($kedudukan!='Semua') $sqlRekap.=" AND n.id_kedudukan=:kedudukan";
$sqlRekap.=" GROUP BY n.id_notaris) x GROUP BY status_input_fidusia";

$rekap = ['BELUM MENYAMPAIKAN'=>0,'HANYA NIHIL'=>0,'ADA ISI'=>0];
$q = $koneksi->prepare($sqlRekap);
$q->bindParam(':tahun',$tahun);
if ($kedudukan!='Semua') $q->bindParam(':kedudukan',$kedudukan);
$q->execute();
while($r=$q->fetch()) $rekap[$r['status_input_fidusia']]=$r['jumlah'];
?>

<div class="alert alert-info">
    <b>Rekap Tahun <?=$tahun?></b><br>
    🟥 Belum Menyampaikan: <b><?=$rekap['BELUM MENYAMPAIKAN']?></b> |
    🟨 Hanya Nihil: <b><?=$rekap['HANYA NIHIL']?></b> |
    🟩 Ada Isi: <b><?=$rekap['ADA ISI']?></b>
</div>

<!-- ====================== -->
<!-- TABEL DATA -->
<!-- ====================== -->
<div class="panel panel-default">
<div class="panel-body">
<table class="table table-bordered table-striped table-hover data" style="width:100%">
<thead>
<tr>
    <th>No</th>
    <th>Nama Notaris</th>
    <th>Email</th>
    <th>Telepon</th>
    <th>Kedudukan</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php
$sql = "
SELECT n.nama,n.email,n.telepon,k.nama_kedudukan,
CASE
    WHEN COUNT(l.id_laporan)=0 THEN 'BELUM MENYAMPAIKAN'
    WHEN COUNT(l.id_laporan)>0 AND SUM(l.jenis_transaksi<>'NIHIL')=0 THEN 'HANYA NIHIL'
    ELSE 'ADA ISI'
END status
FROM notaris n
JOIN kedudukan k ON k.id_kedudukan=n.id_kedudukan
LEFT JOIN laporan_entitas l 
    ON l.id_notaris=n.id_notaris
    AND l.tipe='fidusia'
    AND YEAR(l.tanggal)=:tahun
WHERE n.aktif='1' AND n.level='2'
";

if ($kedudukan!='Semua') $sql.=" AND n.id_kedudukan=:kedudukan";
$sql.=" GROUP BY n.id_notaris ORDER BY k.nama_kedudukan,n.nama";

$stmt=$koneksi->prepare($sql);
$stmt->bindParam(':tahun',$tahun);
if ($kedudukan!='Semua') $stmt->bindParam(':kedudukan',$kedudukan);
$stmt->execute();

$no=1;
while($d=$stmt->fetch()){
    if($status!='Semua' && $d['status']!=$status) continue;
    $wa=make_whatsapp_link($d['telepon']);
    echo "<tr>
        <td>$no</td>
        <td style='text-align:left'>{$d['nama']}</td>
        <td>{$d['email']}</td>
        <td>".($wa?"<a href='#' onclick=\"window.open('$wa')\">{$d['telepon']}</a>":"-")."</td>
        <td>{$d['nama_kedudukan']}</td>
        <td><b>{$d['status']}</b></td>
    </tr>";
    $no++;
}
?>
</tbody>
</table>
</div>
</div>

</div>
</div>

<?php include "footer.php"; ?>
