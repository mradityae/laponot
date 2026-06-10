<?php

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

include("../config/koneksi.php");

$b1 = $_GET['bulan_awal'];
$b2 = $_GET['bulan_akhir'];
$th = $_GET['tahun'];

$sql_kedudukan = "
SELECT id_kedudukan, nama_kedudukan
FROM kedudukan
ORDER BY nama_kedudukan ASC
";

$stmt_kedudukan = $koneksi->prepare($sql_kedudukan);
$stmt_kedudukan->execute();

$spreadsheet = new Spreadsheet();
$sheetIndex = 0;

while ($kedudukan = $stmt_kedudukan->fetch(PDO::FETCH_ASSOC)) {
    $id_kedudukan   = $kedudukan['id_kedudukan'];
    $nama_kedudukan = $kedudukan['nama_kedudukan'];

    if ($sheetIndex == 0) {
        $sheet = $spreadsheet->getActiveSheet();
    } else {
        $sheet = $spreadsheet->createSheet();
    }

    $sheet->setTitle(substr($nama_kedudukan, 0, 31));

    $row = 1;
    $sheet->mergeCells("A{$row}:E{$row}");
    $sheet->setCellValue("A{$row}", "DAFTAR KEPATUHAN LAPORAN NOTARIS (SUDAH LAPOR)");
    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
    $row++;

    $sheet->mergeCells("A{$row}:E{$row}");
    $sheet->setCellValue("A{$row}", "KEDUDUKAN : " . strtoupper($nama_kedudukan));
    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
    $row++;

    $sheet->mergeCells("A{$row}:E{$row}");
    $sheet->setCellValue("A{$row}", "PERIODE BULAN {$b1} S/D {$b2} TAHUN {$th}");
    $row += 2;

    $sheet->setCellValue("A{$row}", "No");
    $sheet->setCellValue("B{$row}", "ID Notaris");
    $sheet->setCellValue("C{$row}", "Nama Notaris");
    $sheet->setCellValue("D{$row}", "Telepon");
    $sheet->setCellValue("E{$row}", "Status");

    $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
    $sheet->getStyle("A{$row}:E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9EAD3');

    $headerRow = $row;
    $row++;

    $sql_notaris = "
        SELECT *
        FROM (
            SELECT
                n.id_notaris,
                n.nama,
                n.telepon,
                (
                    SELECT COUNT(*)
                    FROM laporan l
                    WHERE l.id_notaris = n.id_notaris
                    AND YEAR(l.tanggal) = :tahun
                    AND MONTH(l.tanggal) BETWEEN :bulan_awal AND :bulan_akhir
                ) AS cek
            FROM notaris n
            WHERE n.id_kedudukan = :id_kedudukan
            AND n.level = '2'
            AND n.aktif = '1'
        ) x
        WHERE cek > 0
        ORDER BY nama ASC
    ";

    $stmt_notaris = $koneksi->prepare($sql_notaris);
    $stmt_notaris->execute([
        ':id_kedudukan' => $id_kedudukan,
        ':tahun'        => $th,
        ':bulan_awal'   => $b1,
        ':bulan_akhir'  => $b2
    ]);

    $no = 1;
    $total_sudah = 0;

    while ($data = $stmt_notaris->fetch(PDO::FETCH_ASSOC)) {
        $sheet->setCellValue("A{$row}", $no++);
        $sheet->setCellValue("B{$row}", $data['id_notaris']);
        $sheet->setCellValue("C{$row}", $data['nama']);
        $sheet->setCellValue("D{$row}", $data['telepon']);
        $sheet->setCellValue("E{$row}", "SUDAH LAPOR");

        $sheet->getStyle("E{$row}")->getFont()->getColor()->setARGB('008000');
        $row++;
        $total_sudah++;
    }

    $row++;
    $sheet->setCellValue("B{$row}", "TOTAL SUDAH LAPOR");
    $sheet->setCellValue("C{$row}", $total_sudah);
    $sheet->getStyle("B{$row}:C{$row}")->getFont()->setBold(true);

    $lastDataRow = $row;
    $sheet->getStyle("A{$headerRow}:E{$lastDataRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $sheet->freezePane('A6');
    $sheetIndex++;
}

$spreadsheet->setActiveSheetIndex(0);
$filename = 'Detail_Kepatuhan_Notaris_Sudah_Lapor_'.$th.'_'.$b1.'-'.$b2.'.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;