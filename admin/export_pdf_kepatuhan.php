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
$bulan_awal  = $_GET['bulan_awal'] ?? ($_GET['bulan_berjalan'] ?? date('m'));
$bulan_akhir = $_GET['bulan_akhir'] ?? ($_GET['bulan_berjalan'] ?? date('m'));
$tahun       = $_GET['tahun'] ?? date('Y');

// Ambil nama kedudukan
$stmt_k = $koneksi->prepare("
    SELECT nama_kedudukan 
    FROM kedudukan 
    WHERE id_kedudukan = ?
");
$stmt_k->execute([$kedudukan]);
$nama_kedudukan = $stmt_k->fetchColumn();

// Query digabung menggunakan LEFT JOIN untuk menarik semua data sekaligus
$sql = "
    SELECT 
        n.nama,
        n.email,
        n.telepon,
        CASE 
            WHEN la.id_laporan IS NOT NULL THEN 'sudah'
            ELSE 'belum'
        END AS status_lapor
    FROM notaris n
    LEFT JOIN laporan la ON n.id_notaris = la.id_notaris 
        AND YEAR(la.tanggal) = ? 
        AND MONTH(la.tanggal) BETWEEN ? AND ?
    WHERE n.id_kedudukan = ?
    AND n.level = '2'
    AND n.aktif = '1'
    GROUP BY n.id_notaris
    ORDER BY n.nama ASC
";

$stmt = $koneksi->prepare($sql);

// Eksekusi parameter sesuai urutan tanda tanya di query baru
$stmt->execute([
    $tahun,
    $bulan_awal,
    $bulan_akhir,
    $kedudukan
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

.status-sudah{
    font-weight:bold;
    color:#28a745;
}

.status-belum{
    font-weight:bold;
    color:#dc3545;
}

.total{
    margin-top:15px;
    font-size:13px;
    font-weight:bold;
}
</style>

<div class="header">
    <h2>DAFTAR KEPATUHAN LAPORAN NOTARIS</h2>
    <h3>KEDUDUKAN : '.strtoupper($nama_kedudukan).'</h3>
    <p>
        Periode Bulan '.$bulan_awal.' s/d '.$bulan_akhir.' Tahun '.$tahun.'
    </p>
</div>

<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="35%">Nama Notaris</th>
            <th width="30%">Email</th>
            <th width="15%">No HP</th>
            <th width="15%">Status</th>
        </tr>
    </thead>
    <tbody>
';

$no = 1;

foreach($data as $d){
    // Penentuan teks dan class CSS warna status secara dinamis per baris data
    if ($d['status_lapor'] == 'sudah') {
        $text_status = "SUDAH LAPOR";
        $class_status = "status-sudah";
    } else {
        $text_status = "BELUM LAPOR";
        $class_status = "status-belum";
    }

    $html .= '
    <tr>
        <td class="text-center">'.$no++.'</td>
        <td>'.htmlspecialchars($d['nama']).'</td>
        <td>'.htmlspecialchars($d['email']).'</td>
        <td>'.htmlspecialchars($d['telepon']).'</td>
        <td class="'.$class_status.' text-center">
            '.$text_status.'
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
    Total Data Notaris : '.count($data).'
</div>
';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = "Kepatuhan_Notaris_Semua_".$tahun.".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
?>