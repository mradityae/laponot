<?php
require '../vendor/autoload.php';
include("../config/koneksi.php");
include("../log_activity.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Ambil dan validasi parameter
$id = $_GET["id"] ?? null;
$tanggal_awal = $_GET["tanggal_awal"] ?? '';
$tanggal_akhir = $_GET["tanggal_akhir"] ?? '';
$jenis_transaksi = $_GET["jenis_transaksi"] ?? '';

if (!$id) die("ID Notaris tidak ditemukan!");

// Validasi jenis transaksi
$valid_jenis = ['Pendaftaran', 'Perubahan', 'Pembatalan', 'Penghapusan'];
if (!in_array($jenis_transaksi, $valid_jenis)) {
    $jenis_transaksi = '';
}

// Ambil info notaris
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

// Format teks periode
if ($tanggal_awal && $tanggal_akhir) {
    $periodeText = "PERIODE " . date('d-m-Y', strtotime($tanggal_awal)) . " s.d. " . date('d-m-Y', strtotime($tanggal_akhir));
} else {
    $periodeText = "PERIODE: SEMUA";
}

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Judul
$sheet->setCellValue('A1', 'FORMAT LAPORAN AKTA JAMINAN FIDUSIA YANG DIBUAT NOTARIS');
$sheet->mergeCells('A1:F1');

$sheet->setCellValue('A2', $periodeText);
$sheet->mergeCells('A2:F2');

$sheet->setCellValue('A3', 'NAMA NOTARIS : ' . strtoupper($namaNotaris));
$sheet->mergeCells('A3:F3');

$sheet->setCellValue('A4', 'KEDUDUKAN NOTARIS : ' . strtoupper($namaKedudukan));
$sheet->mergeCells('A4:F4');

$sheet->setCellValue('A5', 'JENIS TRANSAKSI : ' . ($jenis_transaksi ?: 'SEMUA'));
$sheet->mergeCells('A5:F5');

// Header Kolom
$sheet->setCellValue('A7', 'No');
$sheet->setCellValue('B7', 'Nomor Akta');
$sheet->setCellValue('C7', 'Tanggal Akta');
$sheet->setCellValue('D7', 'Nama Pemberi Fidusia');
$sheet->setCellValue('E7', 'Nama Penerima Fidusia');
$sheet->setCellValue('F7', 'Nomor Sertifikat Jaminan Fidusia');

// Query data
$sql = "SELECT * FROM laporan_entitas WHERE id_notaris = :id";
$params = [":id" => $id];

if ($tanggal_awal && $tanggal_akhir) {
    $sql .= " AND tanggal BETWEEN :awal AND :akhir";
    $params[":awal"] = $tanggal_awal;
    $params[":akhir"] = $tanggal_akhir;
}
if (!empty($jenis_transaksi)) {
    $sql .= " AND jenis_transaksi = :jenis_transaksi";
    $params[":jenis_transaksi"] = $jenis_transaksi;
}

$sql .= " ORDER BY tanggal ASC";

$query = $koneksi->prepare($sql);
foreach ($params as $key => $val) {
    $query->bindValue($key, $val);
}
$query->execute();

// Isi Data
$i = 8;
$no = 1;
while ($row = $query->fetch()) {
    $sheet->setCellValue("A$i", $no++);
    $sheet->setCellValue("B$i", $row['nomor']);
    $sheet->setCellValue("C$i", date('d-m-Y', strtotime($row['tanggal'])));
    $sheet->setCellValue("D$i", strtoupper($row['pemberi']));
    $sheet->setCellValue("E$i", strtoupper($row['penerima']));
    $sheet->setCellValue("F$i", $row['no_sertifikat']);
    $i++;
}

// Auto size kolom
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Styling
$sheet->getStyle('A1:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A7:F7')->getFont()->setBold(true);
$sheet->getStyle('A7:F7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');

$lastRow = $i - 1;
if ($lastRow >= 8) {
    $sheet->getStyle("A8:F$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle("A7:F$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
}

// File output
$cleanNama = preg_replace('/[^A-Za-z0-9 ]/', '', $namaNotaris);
$cleanKedudukan = preg_replace('/[^A-Za-z0-9 ]/', '', $namaKedudukan);
$filename = "Laporan Akta Fidusia - " . $cleanNama . ", " . $cleanKedudukan . ".xlsx";
write_log("Export Excel : " . $filename);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
