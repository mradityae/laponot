<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include("../config/koneksi.php");

session_save_path('../login/session');
session_start();

if (!isset($_SESSION['kedudukan'])) {
    die("Session login habis.");
}

$kedudukan   = $_SESSION['kedudukan'];
$status      = $_GET['status'] ?? '';
$bulan_awal  = $_GET['bulan_awal'] ?? date('m');
$bulan_akhir = $_GET['bulan_akhir'] ?? date('m');
$tahun       = $_GET['tahun'] ?? date('Y');

$stmt_k = $koneksi->prepare("
    SELECT nama_kedudukan 
    FROM kedudukan 
    WHERE id_kedudukan = ?
");

$stmt_k->execute([$kedudukan]);

$nama_kedudukan = $stmt_k->fetchColumn();

if ($status == 'sudah') {

    $sql = "
        SELECT DISTINCT
            n.nama,
            n.email,
            n.telepon
        FROM laporan la
        INNER JOIN notaris n
            ON la.id_notaris = n.id_notaris
        WHERE n.id_kedudukan = ?
        AND n.level='2'
        AND n.aktif='1'
        AND YEAR(la.tanggal)=?
        AND MONTH(la.tanggal) BETWEEN ? AND ?
        ORDER BY n.nama ASC
    ";

    $judul_status = "SUDAH MELAPOR";
    $warna_status = "#28a745";

} else {

    $sql = "
        SELECT
            n.nama,
            n.email,
            n.telepon
        FROM notaris n
        WHERE n.id_kedudukan = ?
        AND n.level='2'
        AND n.aktif='1'
        AND n.id_notaris NOT IN (
            SELECT DISTINCT la.id_notaris
            FROM laporan la
            WHERE YEAR(la.tanggal)=?
            AND MONTH(la.tanggal) BETWEEN ? AND ?
        )
        ORDER BY n.nama ASC
    ";

    $judul_status = "BELUM MELAPOR";
    $warna_status = "#dc3545";
}

$stmt = $koneksi->prepare($sql);

$stmt->execute([
    $kedudukan,
    $tahun,
    $bulan_awal,
    $bulan_akhir
]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<style>

body{
    font-family: sans-serif;
    font-size: 12px;
    color:#333;
}

.header{
    text-align:center;
    margin-bottom:25px;
}

.header h2{
    margin:0;
    font-size:22px;
}

.header h3{
    margin:5px 0;
    font-size:16px;
}

.header p{
    margin-top:8px;
    font-size:13px;
}

.info-status{
    margin-top:10px;
    font-weight:bold;
    color:'.$warna_status.';
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

table th{
    background:#0B1D51;
    color:#fff;
    padding:10px;
    border:1px solid #ccc;
    font-size:12px;
}

table td{
    padding:9px;
    border:1px solid #ccc;
    font-size:11px;
}

.text-center{
    text-align:center;
}

.status{
    font-weight:bold;
    color:'.$warna_status.';
}

.total{
    margin-top:15px;
    font-size:13px;
    font-weight:bold;
}

</style>

<div class="header">

    <h2>DAFTAR KEPATUHAN NOTARIS</h2>

    <h3>KEDUDUKAN : '.strtoupper($nama_kedudukan).'</h3>

    <p>
        Periode Bulan '.$bulan_awal.' s/d '.$bulan_akhir.' Tahun '.$tahun.'
    </p>

    <div class="info-status">
        STATUS : '.$judul_status.'
    </div>

</div>

<table>

    <thead>

        <tr>

            <th width="5%">No</th>

            <th width="35%">Nama Notaris</th>

            <th width="35%">Email</th>

            <th width="15%">No HP</th>

            <th width="10%">Status</th>

        </tr>

    </thead>

    <tbody>
';

$no = 1;

foreach($data as $d){

    $html .= '

    <tr>

        <td class="text-center">'.$no++.'</td>

        <td>'.$d['nama'].'</td>

        <td>'.$d['email'].'</td>

        <td>'.$d['telepon'].'</td>

        <td class="status text-center">
            '.$judul_status.'
        </td>

    </tr>

    ';
}

if(count($data) == 0){

    $html .= '

    <tr>

        <td colspan="5" class="text-center">
            Tidak ada data
        </td>

    </tr>

    ';
}

$html .= '

    </tbody>

</table>

<div class="total">
    Total Data : '.count($data).'
</div>

';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$filename = "Kepatuhan_Notaris_".$judul_status."_".$tahun.".pdf";

$dompdf->stream($filename, ["Attachment" => false]);
?>