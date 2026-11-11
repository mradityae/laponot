<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Path autoload vendor Composer
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include("../config/koneksi.php");
include("../models/models.php");

$tahun     = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$aktif     = isset($_GET['aktif']) ? $_GET['aktif'] : "Semua";

// Ambil kedudukan dari session atau fallback dari parameter GET id_kedudukan
$kedudukan = $_SESSION["kedudukan"] ?? ($_GET['id_kedudukan'] ?? null);

if (empty($kedudukan)) {
    exit("Akses ditolak: ID Kedudukan tidak ditemukan.");
}

$level = 2;

// Query rekapitulasi akta
$sql = "
    SELECT n.*, 
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 1 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS jan,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 2 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS feb,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 3 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS mar,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 4 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS apr,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 5 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS mei,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 6 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS jun,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 7 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS jul,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 8 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS agu,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 9 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS sep,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 10 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS okt,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 11 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS nov,
        COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 12 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS des
    FROM notaris n 
    LEFT JOIN laporan l ON n.id_notaris = l.id_notaris AND YEAR(l.tanggal) = :tahun
    WHERE n.level = :level AND n.id_kedudukan = :id_kedudukan
";

if($aktif == "0"){
    $sql .= " AND n.aktif = :aktif ";
}

$sql .= " GROUP BY n.id_notaris ORDER BY n.nama ASC";

$ambil = $koneksi->prepare($sql);
$ambil->bindParam(":tahun", $tahun);
$ambil->bindParam(":level", $level);
$ambil->bindParam(":id_kedudukan", $kedudukan);

if($aktif == "0"){
    $ambil->bindParam(":aktif", $aktif);
}

$ambil->execute();
$data_notaris = $ambil->fetchAll(PDO::FETCH_ASSOC);

// ========================================
// MEMBUAT OBJEK PHPSPREADSHEET
// ========================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Rekap Akta Notaris');

// Tampilkan gridlines Excel
$sheet->setShowGridLines(true);

// 1. JUDUL DI TENGAH (Merge dari Kolom A s.d R)
$sheet->mergeCells('A1:R1');
$sheet->mergeCells('A2:R2');
$sheet->mergeCells('A3:R3');

$sheet->setCellValue('A1', 'REKAPITULASI JUMLAH AKTA NOTARIS TAHUN ' . $tahun);
$sheet->setCellValue('A2', 'WILAYAH: ' . strtoupper(getWilayah($koneksi, $kedudukan)));
$sheet->setCellValue('A3', 'STATUS NOTARIS: ' . ($aktif == "0" ? "Belum Aktif" : "Semua Aktif"));

$titleStyle = [
    'font' => ['bold' => true, 'size' => 12],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:R3')->applyFromArray($titleStyle);
$sheet->getStyle('A1')->getFont()->setSize(14);
$sheet->getRowDimension(1)->setRowHeight(25);
$sheet->getRowDimension(2)->setRowHeight(20);
$sheet->getRowDimension(3)->setRowHeight(20);

// 2. HEADER TABEL MULTI-ROW (Baris 5 & 6)
$sheet->mergeCells('A5:A6');
$sheet->mergeCells('B5:B6');
$sheet->mergeCells('C5:C6');
$sheet->mergeCells('D5:D6');
$sheet->mergeCells('E5:E6');
$sheet->mergeCells('F5:Q5'); // Kolom Bulan Jan - Des
$sheet->mergeCells('R5:R6'); // Total Tahun

$sheet->setCellValue('A5', 'No');
$sheet->setCellValue('B5', 'Nama Notaris');
$sheet->setCellValue('C5', 'Email');
$sheet->setCellValue('D5', 'Telepon');
$sheet->setCellValue('E5', 'Status');
$sheet->setCellValue('F5', 'Jumlah Akta (Dibukukan + Disahkan + Protes) Tahun ' . $tahun);
$sheet->setCellValue('R5', 'Total Tahun');

// Nama Bulan (Baris 6 untuk kolom F sampai Q)
$bulan_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$col_idx = ord('F'); 
foreach ($bulan_labels as $bln) {
    $sheet->setCellValue(chr($col_idx) . '6', $bln);
    $col_idx++;
}

// Styling Header Tabel
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => '0B1D51'], 
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A5:R6')->applyFromArray($headerStyle);
$sheet->getRowDimension(5)->setRowHeight(22);
$sheet->getRowDimension(6)->setRowHeight(20);

// 3. MASUKKAN DATA (Mulai Baris 7)
$rowNum = 7;
$no = 1;

foreach ($data_notaris as $row) {
    $total_tahun = $row['jan'] + $row['feb'] + $row['mar'] + $row['apr'] + $row['mei'] + $row['jun'] + 
                   $row['jul'] + $row['agu'] + $row['sep'] + $row['okt'] + $row['nov'] + $row['des'];

    $status_teks = ($row['aktif'] == 1 ? "Aktif" : "Belum Aktif");

    $sheet->setCellValue('A' . $rowNum, (int)$no);
    $sheet->setCellValue('B' . $rowNum, $row['nama'] ?? '');
    $sheet->setCellValue('C' . $rowNum, $row['email'] ?? '');
    
    // Telepon diset explicit string agar nol di depan tidak hilang
    $sheet->setCellValueExplicit('D' . $rowNum, $row['telepon'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    
    $sheet->setCellValue('E' . $rowNum, $status_teks);
    
    // Bulan Jan - Des
    $sheet->setCellValue('F' . $rowNum, (int)$row['jan']);
    $sheet->setCellValue('G' . $rowNum, (int)$row['feb']);
    $sheet->setCellValue('H' . $rowNum, (int)$row['mar']);
    $sheet->setCellValue('I' . $rowNum, (int)$row['apr']);
    $sheet->setCellValue('J' . $rowNum, (int)$row['mei']);
    $sheet->setCellValue('K' . $rowNum, (int)$row['jun']);
    $sheet->setCellValue('L' . $rowNum, (int)$row['jul']);
    $sheet->setCellValue('M' . $rowNum, (int)$row['agu']);
    $sheet->setCellValue('N' . $rowNum, (int)$row['sep']);
    $sheet->setCellValue('O' . $rowNum, (int)$row['okt']);
    $sheet->setCellValue('P' . $rowNum, (int)$row['nov']);
    $sheet->setCellValue('Q' . $rowNum, (int)$row['des']);
    
    // Total Tahun
    $sheet->setCellValue('R' . $rowNum, (int)$total_tahun);

    // Styling Baris Data
    $sheet->getStyle('A' . $rowNum . ':R' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // Center alignment untuk angka-angka bulan & total
    $sheet->getStyle('F' . $rowNum . ':R' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $sheet->getRowDimension($rowNum)->setRowHeight(20);

    $rowNum++;
    $no++;
}

// 4. ATUR LEBAR KOLOM AGAR PROPORSIONAL
$sheet->getColumnDimension('A')->setWidth(6);   // No
$sheet->getColumnDimension('B')->setWidth(28);  // Nama Notaris
$sheet->getColumnDimension('C')->setWidth(25);  // Email
$sheet->getColumnDimension('D')->setWidth(16);  // Telepon
$sheet->getColumnDimension('E')->setWidth(14);  // Status

// Kolom Bulan Jan - Des (F sampai Q) dan Total (R) dibuat pas untuk angka
foreach (range('F', 'R') as $colID) {
    $sheet->getColumnDimension($colID)->setWidth(9);
}

// Bersihkan output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Header HTTP untuk download file .xlsx
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Rekap_Akta_Notaris_Tahun_' . $tahun . '.xlsx"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;