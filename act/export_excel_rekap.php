<?php
require '../config/koneksi.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\BorderStyle;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Ambil filter
$tgl_a = $_GET['tgl_a'] ?? '';
$tgl_b = $_GET['tgl_b'] ?? '';
$id_kedudukan = $_GET['id_kedudukan'] ?? '';

// Fungsi kolom bulan dinamis
function getMonthColumns($tgl_a, $tgl_b) {
    $start = new DateTime($tgl_a);
    $end = new DateTime($tgl_b);
    $end->modify('first day of next month');

    $columns = [];
    while ($start < $end) {
        $ym = $start->format('Y-m');
        $label = $start->format('F Y');
        $columns[] = [
            'ym' => $ym,
            'label' => $label,
            'sql' => "SUM(CASE WHEN DATE_FORMAT(l.tanggal, '%Y-%m') = '$ym' THEN 1 ELSE 0 END) AS `$label`"
        ];
        $start->modify('+1 month');
    }
    return $columns;
}

// Siapkan kolom bulan
$bulan_cols = getMonthColumns($tgl_a, $tgl_b);
$bulan_sql = implode(",\n  ", array_column($bulan_cols, 'sql'));

// Query data
$sql = "
SELECT 
  n.nama AS nama_notaris,
  k.nama_kedudukan,
  $bulan_sql,
  COUNT(l.id_laporan) AS total,
  COUNT(l.id_laporan) AS keterangan
FROM notaris n
LEFT JOIN kedudukan k ON n.id_kedudukan = k.id_kedudukan
LEFT JOIN laporan_entitas l 
  ON l.id_notaris = n.id_notaris 
  AND l.tanggal BETWEEN :tgl_a AND :tgl_b
WHERE n.level = 2";

if (!empty($id_kedudukan)) {
    $sql .= " AND n.id_kedudukan = :id_kedudukan";
}

$sql .= "
GROUP BY n.id_notaris, n.nama, k.nama_kedudukan
ORDER BY n.nama ASC";

$stmt = $koneksi->prepare($sql);
$stmt->bindParam(':tgl_a', $tgl_a);
$stmt->bindParam(':tgl_b', $tgl_b);
if (!empty($id_kedudukan)) {
    $stmt->bindParam(':id_kedudukan', $id_kedudukan);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header judul
$sheet->setCellValue('A1', 'FORMAT LAPORAN AKTA JAMINAN FIDUSIA YANG DIBUAT NOTARIS');
$periodeAwal = strtoupper(date('F Y', strtotime($tgl_a)));
$periodeAkhir = strtoupper(date('F Y', strtotime($tgl_b)));
$sheet->setCellValue('A2', "PERIODE $periodeAwal S.D $periodeAkhir");
$sheet->setCellValue('A3', 'Tanggal Cetak: ' . date('d-m-Y'));

// Merge dan rata tengah
$colCount = 1 + 2 + count($bulan_cols) + 2; // 1 kolom No + Nama, Kedudukan + bulan + total & ket
$lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
$sheet->mergeCells("A1:$lastCol" . '1');
$sheet->mergeCells("A2:$lastCol" . '2');
$sheet->mergeCells("A3:$lastCol" . '3');
$sheet->getStyle("A1:A3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header kolom
$headers = ['No', 'Nama Notaris', 'Kedudukan'];
foreach ($bulan_cols as $b) {
    $headers[] = $b['label'];
}
$headers[] = 'Total';
$headers[] = 'Keterangan';

// Tulis header
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '5', $h);
    $sheet->getStyle($col . '5')->getFont()->setBold(true);
    $sheet->getStyle($col . '5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// Data isi
$rowNum = 6;
$no = 1;
foreach ($data as $row) {
    $col = 'A';
    $sheet->setCellValue($col++ . $rowNum, $no++);
    $sheet->setCellValue($col++ . $rowNum, $row['nama_notaris']);
    $sheet->setCellValue($col++ . $rowNum, $row['nama_kedudukan']);
    foreach ($bulan_cols as $b) {
        $sheet->setCellValue($col++ . $rowNum, $row[$b['label']] ?? 0);
    }
    $sheet->setCellValue($col++ . $rowNum, $row['total']);
    $sheet->setCellValue($col++ . $rowNum, $row['keterangan']);
    $rowNum++;
}

// Styling border & isi rata kiri
$lastRow = $rowNum - 1;
$sheet->getStyle("A6:$lastCol$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle("A5:$lastCol$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Output Excel
$filename = "rekap_laporan_notaris_" . date('Ymd_His') . ".xlsx";
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=\"$filename\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
