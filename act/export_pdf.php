<?php
require '../vendor/autoload.php';
include("../config/koneksi.php");
include("../log_activity.php");

use Dompdf\Dompdf;

$id = $_GET['id'] ?? null;
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$jenis_transaksi = $_GET['jenis_transaksi'] ?? '';

$valid_jenis = ['Pendaftaran', 'Perubahan', 'Perbaikan', 'Penghapusan'];
if (!in_array($jenis_transaksi, $valid_jenis)) {
    $jenis_transaksi = '';
}

if (!$id) die("ID notaris tidak ditemukan.");

// Ambil nama notaris & kedudukan
$stmt = $koneksi->prepare("
    SELECT notaris.nama AS nama_notaris, kedudukan.nama_kedudukan 
    FROM notaris 
    JOIN kedudukan ON notaris.id_kedudukan = kedudukan.id_kedudukan 
    WHERE notaris.id_notaris = :id
");
$stmt->bindParam(":id", $id);
$stmt->execute();
$dataNotaris = $stmt->fetch();
if (!$dataNotaris) die("Data notaris tidak ditemukan.");

$namaNotaris = $dataNotaris['nama_notaris'];
$namaKedudukan = $dataNotaris['nama_kedudukan'];

// Buat teks periode
$periodeText = 'PERIODE ';
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $periodeText .= date('d/m/Y', strtotime($tanggal_awal)) . " s/d " . date('d/m/Y', strtotime($tanggal_akhir));
} else {
    $periodeText .= 'SEMUA';
}

// Buat SQL
$sql = "SELECT * FROM laporan_entitas WHERE id_notaris = :id";
$params = [":id" => $id];

if (!empty($jenis_transaksi)) {
    $sql .= " AND jenis_transaksi = :jenis_transaksi";
    $params[":jenis_transaksi"] = $jenis_transaksi;
}
if (!empty($tanggal_awal) && !empty($tanggal_akhir) && $tanggal_awal <= $tanggal_akhir) {
    $sql .= " AND tanggal BETWEEN :tanggal_awal AND :tanggal_akhir";
    $params[":tanggal_awal"] = $tanggal_awal;
    $params[":tanggal_akhir"] = $tanggal_akhir;
}

$sql .= " ORDER BY tanggal ASC";
$stmt = $koneksi->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();

// Bangun HTML
$html = '
    <h3 style="text-align:center; margin-bottom: 0;">FORMAT LAPORAN AKTA JAMINAN FIDUSIA YANG DIBUAT NOTARIS</h3>
    <p style="text-align:center; margin:5px 0;">' . $periodeText . '</p>
    <p style="text-align:center; margin:5px 0;">NAMA NOTARIS: <strong>' . htmlspecialchars($namaNotaris) . '</strong></p>
    <p style="text-align:center; margin:5px 0;">KEDUDUKAN NOTARIS: <strong>' . htmlspecialchars($namaKedudukan) . '</strong></p>';
if ($jenis_transaksi) {
    $html .= '<p style="text-align:center; margin:5px 0;">JENIS TRANSAKSI: <strong>' . htmlspecialchars($jenis_transaksi) . '</strong></p>';
}
$html .= '<br>';

$html .= '
    <table border="1" cellspacing="0" cellpadding="6" width="100%" style="border-collapse: collapse; font-size:12px;">
    <thead>
    <tr>
        <th>No</th>
        <th>Nomor Akta</th>
        <th>Tanggal Akta</th>
        <th>Nama Pemberi Fidusia</th>
        <th>Nama Penerima Fidusia</th>
        <th>Nomor Sertifikat Jaminan Fidusia</th>
    </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = $stmt->fetch()) {
    $html .= '<tr>
        <td>' . $no++ . '</td>
        <td>' . htmlspecialchars($row['nomor']) . '</td>
        <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
        <td>' . htmlspecialchars($row['pemberi']) . '</td>
        <td>' . htmlspecialchars($row['penerima']) . '</td>
        <td>' . htmlspecialchars($row['no_sertifikat']) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Nama file aman
$cleanNama = preg_replace('/[^A-Za-z0-9 ]/', '', $namaNotaris);
$cleanKedudukan = preg_replace('/[^A-Za-z0-9 ]/', '', $namaKedudukan);
$filename = "Laporan Fidusia - $cleanNama - $cleanKedudukan.pdf";

write_log("Export PDF: $filename");

// Buat PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($filename, ['Attachment' => true]);
exit;
