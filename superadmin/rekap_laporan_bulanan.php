<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

// 1. FILTER LOGIC
$bulan_awal  = $_GET['bulan_awal'] ?? '01';
$bulan_akhir = $_GET['bulan_akhir'] ?? date('m');
$tahun       = $_GET['tahun'] ?? date('Y');

// 2. DATA HARDCODE NOTARIS AKTIF
$data_notaris_aktif = [
    'Kabupaten Bandung' => 360, 'Kabupaten Bandung Barat' => 178, 'Kabupaten Bekasi' => 218,
    'Kabupaten Bogor' => 350, 'Kabupaten Ciamis' => 56, 'Kabupaten Cianjur' => 118,
    'Kabupaten Cirebon' => 383, 'Kabupaten Garut' => 210, 'Kabupaten Indramayu' => 201,
    'Kabupaten Karawang' => 247, 'Kabupaten Kuningan' => 110, 'Kabupaten Majalengka' => 104,
    'Kabupaten Pangandaran' => 33, 'Kabupaten Purwakarta' => 132, 'Kabupaten Subang' => 198,
    'Kabupaten Sukabumi' => 168, 'Kabupaten Sumedang' => 136, 'Kabupaten Tasikmalaya' => 94,
    'Kota Bandung' => 164, 'Kota Banjar' => 20, 'Kota Bekasi' => 237, 'Kota Bogor' => 207,
    'Kota Cimahi' => 102, 'Kota Cirebon' => 126, 'Kota Depok' => 185, 'Kota Sukabumi' => 71,
    'Kota Tasikmalaya' => 91
];

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<style>
    .page-head-line { font-weight:600; color:#2c3e50; margin-bottom:25px; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; }
    .filter-form { background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; padding:15px; margin-bottom:20px; }
    .table th { background:#2c3e50 !important; color:#fff; text-align:center; font-weight:600; vertical-align: middle !important; font-size: 12px; }
    .table td { vertical-align:middle !important; text-align:center; font-size: 13px; }
    .bg-total { background: #eee !important; font-weight: bold; }
    .text-success { color: #27ae60; font-weight: bold; }
    .text-danger { color: #e74c3c; font-weight: bold; }
    .dataTables_wrapper { margin-top: 20px; }
</style>

<div id="page-wrapper">
    <div id="page-inner">
        <h1 class="page-head-line">📊 Rekap Kepatuhan Laporan Bulanan Notaris</h1>

        <form method="GET" class="form-inline filter-form">
            <label><b>Dari:</b></label>
            <select name="bulan_awal" class="form-control" style="width:140px; margin:0 5px;">
                <?php foreach ($nama_bulan as $m => $nm): ?>
                    <option value="<?= $m ?>" <?= ($bulan_awal == $m ? 'selected' : '') ?>><?= $nm ?></option>
                <?php endforeach; ?>
            </select>

            <label><b>Sampai:</b></label>
            <select name="bulan_akhir" class="form-control" style="width:140px; margin:0 5px;">
                <?php foreach ($nama_bulan as $m => $nm): ?>
                    <option value="<?= $m ?>" <?= ($bulan_akhir == $m ? 'selected' : '') ?>><?= $nm ?></option>
                <?php endforeach; ?>
            </select>

            <label><b>Tahun:</b></label>
            <input type="number" name="tahun" value="<?= $tahun ?>" class="form-control" style="width:100px; margin:0 5px;">

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-search"></i> Tampilkan
            </button>

            <a href="export_rekap_bulanan.php?bulan_awal=<?=$bulan_awal?>&bulan_akhir=<?=$bulan_akhir?>&tahun=<?=$tahun?>" 
               class="btn btn-success btn-sm" style="margin-left:5px;">
                <i class="fa fa-file-excel-o"></i> Export Excel
            </a>
        </form>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="tableRekap" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">MPD (Kedudukan)</th>
                                <th rowspan="2">Jml Notaris Aktif</th>
                                <th rowspan="2">Jml Notaris Kirim</th>
                                <th rowspan="2">Kepatuhan (%)</th>
                                <th colspan="4">Rincian Akta</th>
                                <th rowspan="2">Total Akta</th>
                            </tr>
                            <tr>
                                <th>Buku Daftar</th>
                                <th>Waarmerking</th>
                                <th>Legalisasi</th>
                                <th>Buku Protes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "
                            SELECT 
                                k.nama_kedudukan AS mpd,
                                COUNT(DISTINCT la.id_notaris) AS jumlah_notaris_kirim,
                                SUM(la.jml_buku_daftar) AS jml_buku_daftar,
                                SUM(la.jml_tangan_dibukukan) AS jml_tangan_dibukukan,
                                SUM(la.jml_tangan_disahkan) AS jml_tangan_disahkan,
                                SUM(la.jml_buku_protes) AS jml_buku_protes,
                                SUM(la.jml_buku_daftar + la.jml_tangan_dibukukan + la.jml_tangan_disahkan + la.jml_buku_protes) AS total_akta
                            FROM laporan la
                            JOIN notaris n ON n.id_notaris = la.id_notaris
                            JOIN kedudukan k ON k.id_kedudukan = n.id_kedudukan
                            WHERE YEAR(la.tanggal) = :tahun
                              AND MONTH(la.tanggal) BETWEEN :bulan_awal AND :bulan_akhir
                            GROUP BY k.id_kedudukan
                            ORDER BY k.nama_kedudukan";

                            $stmt = $koneksi->prepare($sql);
                            $stmt->execute([':tahun' => $tahun, ':bulan_awal' => $bulan_awal, ':bulan_akhir' => $bulan_akhir]);

                            $no = 1;
                            $g_total_notaris = 0; $g_total_kirim = 0; $g_total_akta = 0;

                            while ($row = $stmt->fetch()) {
                                $notaris_aktif = $data_notaris_aktif[$row['mpd']] ?? 0;
                                $persentase = ($notaris_aktif > 0) ? ($row['jumlah_notaris_kirim'] / $notaris_aktif) * 100 : 0;
                                $class_persen = ($persentase >= 100) ? 'text-success' : (($persentase < 50) ? 'text-danger' : '');

                                $g_total_notaris += $notaris_aktif;
                                $g_total_kirim += $row['jumlah_notaris_kirim'];
                                $g_total_akta += $row['total_akta'];
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td style="text-align:left"><?= $row['mpd'] ?></td>
                                    <td><b><?= number_format($notaris_aktif) ?></b></td>
                                    <td><?= number_format($row['jumlah_notaris_kirim']) ?></td>
                                    <td class="<?= $class_persen ?>"><?= number_format($persentase, 2) ?>%</td>
                                    <td><?= number_format($row['jml_buku_daftar']) ?></td>
                                    <td><?= number_format($row['jml_tangan_dibukukan']) ?></td>
                                    <td><?= number_format($row['jml_tangan_disahkan']) ?></td>
                                    <td><?= number_format($row['jml_buku_protes']) ?></td>
                                    <td class="info"><b><?= number_format($row['total_akta']) ?></b></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-total">
                                <td colspan="2">TOTAL KESELURUHAN</td>
                                <td><?= number_format($g_total_notaris) ?></td>
                                <td><?= number_format($g_total_kirim) ?></td>
                                <td>
                                    <?php 
                                    $p_total = ($g_total_notaris > 0) ? ($g_total_kirim / $g_total_notaris) * 100 : 0;
                                    echo number_format($p_total, 2) . "%";
                                    ?>
                                </td>
                                <td colspan="4"></td>
                                <td><?= number_format($g_total_akta) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
$(document).ready(function() {
    $('#tableRekap').DataTable({
        "pageLength": 50,
        "order": [[ 1, "asc" ]], // Urutkan berdasarkan Nama Kedudukan
        "language": {
            "search": "Cari Daerah:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ daerah"
        }
    });
});
</script>

<?php include "footer.php"; ?>