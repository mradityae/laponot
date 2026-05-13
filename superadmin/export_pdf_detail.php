<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include("../config/koneksi.php");

$id = $_GET['id_kedudukan'];
$b1 = $_GET['bulan_awal'];
$b2 = $_GET['bulan_akhir'];
$th = $_GET['tahun'];

// Query Data yang sama
$stmt_k = $koneksi->prepare("SELECT nama_kedudukan FROM kedudukan WHERE id_kedudukan = ?");
$stmt_k->execute([$id]);
$nama_kedudukan = $stmt_k->fetchColumn();

$sql = "SELECT n.nama, n.telepon,
        (SELECT COUNT(*) FROM laporan l WHERE l.id_notaris = n.id_notaris 
         AND YEAR(l.tanggal) = :th AND MONTH(l.tanggal) BETWEEN :b1 AND :b2) as cek
        FROM notaris n WHERE n.id_kedudukan = :id AND n.level = '2' ORDER BY n.nama ASC";
$stmt = $koneksi->prepare($sql);
$stmt->execute([':id'=>$id, ':th'=>$th, ':b1'=>$b1, ':b2'=>$b2]);

// Desain HTML untuk PDF
$html = '
<style>
    body { font-family: sans-serif; font-size: 12px; }
    .header { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .label { padding: 2px 5px; border-radius: 3px; color: white; font-size: 10px; font-weight: bold; }
    .bg-success { background-color: #28a745; }
    .bg-danger { background-color: #dc3545; }
</style>

<div class="header">
    <h3>DAFTAR KEPATUHAN LAPORAN NOTARIS</h3>
    <h4>KEDUDUKAN: '.strtoupper($nama_kedudukan).'</h4>
    <p>Periode Bulan: '.$b1.' s/d '.$b2.' Tahun '.$th.'</p>
</div>

<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th>Nama Notaris</th>
            <th>Telepon</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while($d = $stmt->fetch()){
    $status = ($d['cek'] > 0) ? 'SUDAH LAPOR' : 'BELUM LAPOR';
    $color = ($d['cek'] > 0) ? '#28a745' : '#dc3545';
    
    $html .= '<tr>
                <td>'.$no++.'</td>
                <td>'.$d['nama'].'</td>
                <td>'.$d['telepon'].'</td>
                <td style="color: '.$color.'; font-weight:bold;">'.$status.'</td>
              </tr>';
}

$html .= '</tbody></table>';

// Eksekusi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Detail_Laporan_".$nama_kedudukan.".pdf", ["Attachment" => false]);