<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

// 1. FILTER LOGIC
$bulan_awal  = $_GET['bulan_awal'] ?? '01';
$bulan_akhir = $_GET['bulan_akhir'] ?? date('m');
$tahun       = $_GET['tahun'] ?? date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// 2. QUERY UTAMA (Data Rekap per Kedudukan)
$sql = "SELECT 
            k.id_kedudukan,
            k.nama_kedudukan AS mpd,
            (SELECT COUNT(*) FROM notaris WHERE id_kedudukan = k.id_kedudukan AND level = '2' AND aktif='1') AS jml_notaris_aktif,
            COUNT(DISTINCT la.id_notaris) AS jumlah_notaris_kirim,
            SUM(la.jml_buku_daftar) AS jml_buku_daftar,
            SUM(la.jml_tangan_dibukukan) AS jml_tangan_dibukukan,
            SUM(la.jml_tangan_disahkan) AS jml_tangan_disahkan,
            SUM(la.jml_buku_protes) AS jml_buku_protes,
            SUM(la.jml_buku_daftar + la.jml_tangan_dibukukan + la.jml_tangan_disahkan + la.jml_buku_protes) AS total_akta
        FROM kedudukan k
        LEFT JOIN notaris n ON n.id_kedudukan = k.id_kedudukan AND n.level = '2' AND n.aktif='1'
        LEFT JOIN laporan la ON la.id_notaris = n.id_notaris 
            AND YEAR(la.tanggal) = :tahun 
            AND MONTH(la.tanggal) BETWEEN :bulan_awal AND :bulan_akhir
        GROUP BY k.id_kedudukan
        ORDER BY k.nama_kedudukan ASC";

$stmt = $koneksi->prepare($sql);
$stmt->execute([':tahun' => $tahun, ':bulan_awal' => $bulan_awal, ':bulan_akhir' => $bulan_akhir]);
$results = $stmt->fetchAll();

// 3. HITUNG TOTAL KESELURUHAN UNTUK SUMMARY
$g_total_notaris = 0; 
$g_total_kirim   = 0; 
$g_total_akta    = 0;

foreach ($results as $res) {
    $g_total_notaris += $res['jml_notaris_aktif'];
    $g_total_kirim   += $res['jumlah_notaris_kirim'];
    $g_total_akta    += $res['total_akta'];
}
$g_belum_lapor = $g_total_notaris - $g_total_kirim;
$g_persentase  = ($g_total_notaris > 0) ? ($g_total_kirim / $g_total_notaris) * 100 : 0;
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<style>
    .page-head-line { font-weight:600; color:#2c3e50; margin-bottom:25px; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; }
    .filter-form { background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; padding:15px; margin-bottom:20px; }
    
    /* Summary Cards Style */
    .summary-card { background: #fff; border-radius: 8px; padding: 20px; text-align: center; border: 1px solid #eee; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .summary-card h4 { margin: 0; color: #7f8c8d; font-size: 14px; text-transform: uppercase; font-weight: 600; }
    .summary-card p { margin: 5px 0 0; font-size: 24px; font-weight: 700; color: #2c3e50; }
    .border-blue { border-left: 5px solid #3498db; }
    .border-green { border-left: 5px solid #27ae60; }
    .border-red { border-left: 5px solid #e74c3c; }
    .border-orange { border-left: 5px solid #f39c12; }

    .table th { background:#2c3e50 !important; color:#fff; text-align:center; font-weight:600; vertical-align: middle !important; font-size: 11px; }
    .table td { vertical-align:middle !important; text-align:center; font-size: 13px; }
    .btn-detail { color: #3498db; cursor: pointer; font-weight: bold; }
    .btn-detail:hover { text-decoration: underline; }
</style>

<div id="page-wrapper">
    <div id="page-inner">
        <h1 class="page-head-line">📊 Rekap Kepatuhan Laporan Bulanan</h1>

        <!-- FILTER FORM -->
        <form method="GET" class="form-inline filter-form">
            <label>Periode:</label>
            <select name="bulan_awal" class="form-control input-sm">
                <?php foreach ($nama_bulan as $m => $nm): ?>
                    <option value="<?= $m ?>" <?= ($bulan_awal == $m ? 'selected' : '') ?>><?= $nm ?></option>
                <?php endforeach; ?>
            </select>
            <select name="bulan_akhir" class="form-control input-sm">
                <?php foreach ($nama_bulan as $m => $nm): ?>
                    <option value="<?= $m ?>" <?= ($bulan_akhir == $m ? 'selected' : '') ?>><?= $nm ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="tahun" value="<?= $tahun ?>" class="form-control input-sm" style="width:80px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Tampilkan</button>
            <a href="export_rekap_bulanan.php?bulan_awal=<?=$bulan_awal?>&bulan_akhir=<?=$bulan_akhir?>&tahun=<?=$tahun?>" 
                class="btn btn-success btn-sm">
                <i class="fa fa-file-excel-o"></i> Export Rekap
            </a>
            <a href="export_detail_excel_all.php?bulan_awal=<?=$bulan_awal?>&bulan_akhir=<?=$bulan_akhir?>&tahun=<?=$tahun?>"
                class="btn btn-success btn-sm">
                <i class="fa fa-file-excel-o"></i> Export Status Belum Lapor 
            </a>
            <a href="export_detail_excel_sudah_lapor.php?bulan_awal=<?=$bulan_awal?>&bulan_akhir=<?=$bulan_akhir?>&tahun=<?=$tahun?>"
                class="btn btn-success btn-sm">
                <i class="fa fa-file-excel-o"></i> Export Status Sudah Lapor 
            </a>
        </form>

        <!-- SUMMARY CARDS -->
        <div class="row" style="margin-bottom: 25px;">
            <div class="col-md-3">
                <div class="summary-card border-blue">
                    <h4>Total Notaris Aktif</h4>
                    <p><?= number_format($g_total_notaris) ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card border-green">
                    <h4>Sudah Lapor</h4>
                    <p><?= number_format($g_total_kirim) ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card border-red">
                    <h4>Belum Lapor</h4>
                    <p><?= number_format($g_belum_lapor) ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card border-orange">
                    <h4>% Kepatuhan</h4>
                    <p><?= number_format($g_persentase, 2) ?>%</p>
                </div>
            </div>
        </div>

        <!-- TABLE REKAP -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="tableRekap" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">MPD (Kedudukan)</th>
                                <th rowspan="2">Jml Notaris</th>
                                <th rowspan="2">Sudah Lapor</th>
                                <th rowspan="2">Kepatuhan (%)</th>
                                <th colspan="4">Rincian Akta</th>
                                <th rowspan="2">Total Akta</th>
                            </tr>
                            <tr>
                                <th>Daftar</th>
                                <th>Waarmerk</th>
                                <th>Legalisasi</th>
                                <th>Protes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($results as $row) { 
                                $persen = ($row['jml_notaris_aktif'] > 0) ? ($row['jumlah_notaris_kirim'] / $row['jml_notaris_aktif']) * 100 : 0;
                                $color  = ($persen >= 100) ? 'text-success' : (($persen < 50) ? 'text-danger' : '');
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td style="text-align:left">
                                    <span class="btn-detail" onclick="showDetail('<?= $row['id_kedudukan'] ?>', '<?= $row['mpd'] ?>')">
                                        <?= $row['mpd'] ?>
                                    </span>
                                </td>
                                <td><b><?= number_format($row['jml_notaris_aktif']) ?></b></td>
                                <td><?= number_format($row['jumlah_notaris_kirim']) ?></td>
                                <td class="<?= $color ?>"><b><?= number_format($persen, 2) ?>%</b></td>
                                <td><?= number_format($row['jml_buku_daftar']) ?></td>
                                <td><?= number_format($row['jml_tangan_dibukukan']) ?></td>
                                <td><?= number_format($row['jml_tangan_disahkan']) ?></td>
                                <td><?= number_format($row['jml_buku_protes']) ?></td>
                                <td class="info"><b><?= number_format($row['total_akta']) ?></b></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="modalTitle"></h4>
            </div>
            <div class="modal-body" id="detailContent"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
$(document).ready(function() {
    $('#tableRekap').DataTable({ "pageLength": 50 });
});

function showDetail(id, nama) {
    $('#modalTitle').html("Detail Notaris: " + nama);
    $('#modalDetail').modal('show');
    $('#detailContent').html('<center>Sedang memuat...</center>');
    
    $.get('get_detail_laporan.php', {
        id_kedudukan: id,
        bulan_awal: '<?= $bulan_awal ?>',
        bulan_akhir: '<?= $bulan_akhir ?>',
        tahun: '<?= $tahun ?>'
    }, function(data) {
        $('#detailContent').html(data);
    });
}
</script>

<?php include "footer.php"; ?>