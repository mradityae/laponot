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

$kedudukan      = $_GET['id_kedudukan'] ?? ($_SESSION["kedudukan"] ?? null);
$bulan_berjalan = $_GET['bulan_berjalan'] ?? date('m', strtotime('-1 month'));
$tahun          = $_GET['tahun'] ?? date('Y');
$status_filter  = $_GET['status'] ?? 'semua'; // sudah, belum, semua

if (empty($kedudukan)) {
    exit("Akses ditolak: ID Kedudukan tidak ditemukan.");
}

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$label_status = "Semua Notaris";
if ($status_filter == 'sudah') $label_status = "Sudah Melapor";
if ($status_filter == 'belum') $label_status = "Belum Melapor";

// ========================================
// QUERY DATA BERDASARKAN FILTER
// ========================================
if ($status_filter == 'sudah') {
    $sql = "
        SELECT DISTINCT n.*, l.tanggal as tgl_lapor
        FROM notaris n
        INNER JOIN laporan l ON n.id_notaris = l.id_notaris
        WHERE n.id_kedudukan = ? AND n.level = '2' AND n.aktif = '1'
        AND YEAR(l.tanggal) = ? AND MONTH(l.tanggal) = ?
        ORDER BY n.nama ASC
    ";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute([$kedudukan, $tahun, $bulan_berjalan]);
} 
elseif ($status_filter == 'belum') {
    $sql = "
        SELECT n.* FROM notaris n
        WHERE n.id_kedudukan = ? AND n.level = '2' AND n.aktif = '1'
        AND n.id_notaris NOT IN (
            SELECT DISTINCT la.id_notaris FROM laporan la
            WHERE YEAR(la.tanggal) = ? AND MONTH(la.tanggal) = ?
        )
        ORDER BY n.nama ASC
    ";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute([$kedudukan, $tahun, $bulan_berjalan]);
} 
else {
    $sql = "
        SELECT n.*, 
               (SELECT MAX(l.tanggal) FROM laporan l WHERE l.id_notaris = n.id_notaris AND YEAR(l.tanggal) = ? AND MONTH(l.tanggal) = ?) as tgl_lapor
        FROM notaris n
        WHERE n.id_kedudukan = ? AND n.level = '2' AND n.aktif = '1'
        ORDER BY n.nama ASC
    ";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute([$tahun, $bulan_berjalan, $kedudukan]);
}

$notaris_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ========================================
// MEMBUAT OBJEK PHPSPREADSHEET
// ========================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Kepatuhan Notaris');

// Tampilkan gridlines di Excel
$sheet->setShowGridLines(true);

// 1. HEADER JUDUL DI TENGAH (Merge A s.d G)
$sheet->mergeCells('A1:G1');
$sheet->mergeCells('A2:G2');
$sheet->mergeCells('A3:G3');

$sheet->setCellValue('A1', 'REKAPITULASI KEPATUHAN LAPORAN NOTARIS');
$sheet->setCellValue('A2', 'WILAYAH: ' . strtoupper(getWilayah($koneksi, $kedudukan)));
$sheet->setCellValue('A3', 'PERIODE: ' . ($nama_bulan[$bulan_berjalan] ?? '') . ' ' . $tahun . ' (Status: ' . $label_status . ')');

// Styling Judul (Centered)
$titleStyle = [
    'font' => ['bold' => true, 'size' => 13],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:G3')->applyFromArray($titleStyle);
$sheet->getStyle('A1')->getFont()->setSize(14);
$sheet->getRowDimension(1)->setRowHeight(25);
$sheet->getRowDimension(2)->setRowHeight(20);
$sheet->getRowDimension(3)->setRowHeight(20);

// 2. HEADER TABEL (Baris 5)
$headers = ['No', 'Nama Notaris', 'Email', 'Telepon', 'Alamat', 'Status Laporan', 'Tanggal Lapor Terakhir'];
$col = 'A';
foreach ($headers as $headerTitle) {
    $sheet->setCellValue($col . '5', $headerTitle);
    $col++;
}

// Styling Header Tabel
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => '0B1D51'], // Biru gelap
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
$sheet->getStyle('A5:G5')->applyFromArray($headerStyle);
$sheet->getRowDimension(5)->setRowHeight(24);

// 3. MASUKKAN DATA (Mulai Baris 6) dengan perbaikan increment nomor ($no++)
$rowNum = 6;
$no = 1;

foreach ($notaris_data as $row) {
    if ($status_filter == 'semua') {
        $status_lapor = (!empty($row['tgl_lapor'])) ? "Sudah Melapor" : "Belum Melapor";
    } else {
        $status_lapor = ($status_filter == 'sudah') ? "Sudah Melapor" : "Belum Melapor";
    }
    $tgl_lapor = !empty($row['tgl_lapor']) ? $row['tgl_lapor'] : "-";

    // Set nilai sel secara eksplisit
    $sheet->setCellValue('A' . $rowNum, (int)$no); // Pastikan integer agar increment jalan sempurna
    $sheet->setCellValue('B' . $rowNum, $row['nama'] ?? '');
    $sheet->setCellValue('C' . $rowNum, $row['email'] ?? '');
    
    // Telepon diset explicit string agar angka '0' di depan tidak hilang & lebar kolom pas
    $sheet->setCellValueExplicit('D' . $rowNum, $row['telepon'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    
    $sheet->setCellValue('E' . $rowNum, $row['alamat'] ?? '');
    $sheet->setCellValue('F' . $rowNum, $status_lapor);
    $sheet->setCellValue('G' . $rowNum, $tgl_lapor);

    // Styling baris data
    $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('F' . $rowNum . ':G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $sheet->getRowDimension($rowNum)->setRowHeight(20);
    
    $rowNum++;
    $no++; // Increment nomor urut agar tidak angka 1 terus
}

// Atur lebar kolom agar proporsional (Nomor dibuat pas/tidak terlalu panjang, kolom lain disesuaikan)
$sheet->getColumnDimension('A')->setWidth(6);   // No
$sheet->getColumnDimension('B')->setWidth(28);  // Nama Notaris
$sheet->getColumnDimension('C')->setWidth(25);  // Email
$sheet->getColumnDimension('D')->setWidth(16);  // Telepon (tidak terlalu panjang)
$sheet->getColumnDimension('E')->setWidth(35);  // Alamat
$sheet->getColumnDimension('F')->setWidth(18);  // Status Laporan
$sheet->getColumnDimension('G')->setWidth(22);  // Tanggal Lapor Terakhir

// Bersihkan output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Header HTTP untuk download file .xlsx
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Laporan_Kepatuhan_Notaris_' . $label_status . '_' . ($nama_bulan[$bulan_berjalan] ?? '') . '_' . $tahun . '.xlsx"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;