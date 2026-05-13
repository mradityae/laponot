<?php
require '../vendor/autoload.php'; // Sesuaikan path vendor autoload Anda

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

include("../config/koneksi.php");

// 1. Ambil Parameter
$bulan_awal  = $_GET['bulan_awal'] ?? '01';
$bulan_akhir = $_GET['bulan_akhir'] ?? date('m');
$tahun       = $_GET['tahun'] ?? date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// 2. Query Data
$sql = "SELECT 
            k.nama_kedudukan AS mpd,
            (SELECT COUNT(*) FROM notaris WHERE id_kedudukan = k.id_kedudukan AND level = '2') AS jml_notaris_aktif,
            COUNT(DISTINCT la.id_notaris) AS jumlah_notaris_kirim,
            SUM(la.jml_buku_daftar) AS jml_buku_daftar,
            SUM(la.jml_tangan_dibukukan) AS jml_tangan_dibukukan,
            SUM(la.jml_tangan_disahkan) AS jml_tangan_disahkan,
            SUM(la.jml_buku_protes) AS jml_buku_protes,
            SUM(la.jml_buku_daftar + la.jml_tangan_dibukukan + la.jml_tangan_disahkan + la.jml_buku_protes) AS total_akta
        FROM kedudukan k
        LEFT JOIN notaris n ON n.id_kedudukan = k.id_kedudukan AND n.level = '2'
        LEFT JOIN laporan la ON la.id_notaris = n.id_notaris 
            AND YEAR(la.tanggal) = :tahun 
            AND MONTH(la.tanggal) BETWEEN :bulan_awal AND :bulan_akhir
        GROUP BY k.id_kedudukan
        ORDER BY k.nama_kedudukan ASC";

$stmt = $koneksi->prepare($sql);
$stmt->execute([':tahun' => $tahun, ':bulan_awal' => $bulan_awal, ':bulan_akhir' => $bulan_akhir]);

// 3. Inisialisasi Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Judul
$sheet->setCellValue('A1', 'REKAP KEPATUHAN LAPORAN BULANAN NOTARIS');
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A2', 'Periode: ' . $nama_bulan[$bulan_awal] . ' s/d ' . $nama_bulan[$bulan_akhir] . ' ' . $tahun);
$sheet->mergeCells('A2:J2');

// Header Tabel
$sheet->setCellValue('A4', 'No');
$sheet->setCellValue('B4', 'MPD (Kedudukan)');
$sheet->setCellValue('C4', 'Jml Notaris Aktif');
$sheet->setCellValue('D4', 'Jml Notaris Kirim');
$sheet->setCellValue('E4', 'Kepatuhan (%)');
$sheet->setCellValue('F4', 'Rincian Akta');
$sheet->mergeCells('F4:I4');
$sheet->setCellValue('J4', 'Total Akta');

$sheet->setCellValue('F5', 'Buku Daftar');
$sheet->setCellValue('G5', 'Waarmerking');
$sheet->setCellValue('H5', 'Legalisasi');
$sheet->setCellValue('I5', 'Buku Protes');

// Merge Header yang memiliki rowspan
foreach (['A', 'B', 'C', 'D', 'E', 'J'] as $col) {
    $sheet->mergeCells($col . '4:' . $col . '5');
}

// Styling Header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A4:J5')->applyFromArray($headerStyle);

// 4. Isi Data
$rowNum = 6;
$no = 1;
$g_notaris = 0; $g_kirim = 0; $g_akta = 0;

while ($row = $stmt->fetch()) {
    $notaris_aktif = (int)$row['jml_notaris_aktif'];
    $notaris_kirim = (int)$row['jumlah_notaris_kirim'];
    $persen = ($notaris_aktif > 0) ? ($notaris_kirim / $notaris_aktif) : 0;
    
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $row['mpd']);
    $sheet->setCellValue('C' . $rowNum, $notaris_aktif);
    $sheet->setCellValue('D' . $rowNum, $notaris_kirim);
    $sheet->setCellValue('E' . $rowNum, $persen);
    $sheet->setCellValue('F' . $rowNum, (int)$row['jml_buku_daftar']);
    $sheet->setCellValue('G' . $rowNum, (int)$row['jml_tangan_dibukukan']);
    $sheet->setCellValue('H' . $rowNum, (int)$row['jml_tangan_disahkan']);
    $sheet->setCellValue('I' . $rowNum, (int)$row['jml_buku_protes']);
    $sheet->setCellValue('J' . $rowNum, (int)$row['total_akta']);
    
    // Format Persentase Excel
    $sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('0.00%');
    
    $g_notaris += $notaris_aktif;
    $g_kirim   += $notaris_kirim;
    $g_akta    += $row['total_akta'];
    $rowNum++;
}

// 5. Total Keseluruhan
$sheet->setCellValue('A' . $rowNum, 'TOTAL KESELURUHAN');
$sheet->mergeCells('A' . $rowNum . ':B' . $rowNum);
$sheet->setCellValue('C' . $rowNum, $g_notaris);
$sheet->setCellValue('D' . $rowNum, $g_kirim);
$p_total = ($g_notaris > 0) ? ($g_kirim / $g_notaris) : 0;
$sheet->setCellValue('E' . $rowNum, $p_total);
$sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('0.00%');
$sheet->setCellValue('J' . $rowNum, $g_akta);

$sheet->getStyle('A' . $rowNum . ':J' . $rowNum)->getFont()->setBold(true);
$sheet->getStyle('A4:J' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Autosize kolom
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// 6. Output ke Browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Rekap_Laporan_Notaris_' . $tahun . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;