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
SELECT id_kedudukan,nama_kedudukan
FROM kedudukan
ORDER BY nama_kedudukan ASC
";

$stmt_kedudukan = $koneksi->prepare($sql_kedudukan);
$stmt_kedudukan->execute();

$spreadsheet = new Spreadsheet();

$sheetIndex = 0;

while ($kedudukan = $stmt_kedudukan->fetch(PDO::FETCH_ASSOC))
{
    $id_kedudukan   = $kedudukan['id_kedudukan'];
    $nama_kedudukan = $kedudukan['nama_kedudukan'];

    if ($sheetIndex == 0) {
        $sheet = $spreadsheet->getActiveSheet();
    } else {
        $sheet = $spreadsheet->createSheet();
    }

    $sheet->setTitle(substr($nama_kedudukan, 0, 31));

    $row = 1;

    $sheet->mergeCells("A{$row}:D{$row}");
    $sheet->setCellValue(
        "A{$row}",
        "DAFTAR KEPATUHAN LAPORAN NOTARIS"
    );

    $sheet->getStyle("A{$row}")
        ->getFont()
        ->setBold(true)
        ->setSize(14);

    $row++;

    $sheet->mergeCells("A{$row}:D{$row}");
    $sheet->setCellValue(
        "A{$row}",
        "KEDUDUKAN : " . strtoupper($nama_kedudukan)
    );

    $sheet->getStyle("A{$row}")
        ->getFont()
        ->setBold(true);

    $row++;

    $sheet->mergeCells("A{$row}:D{$row}");
    $sheet->setCellValue(
        "A{$row}",
        "PERIODE BULAN {$b1} S/D {$b2} TAHUN {$th}"
    );

    $row += 2;

    $sheet->setCellValue("A{$row}", "No");
    $sheet->setCellValue("B{$row}", "Nama Notaris");
    $sheet->setCellValue("C{$row}", "Telepon");
    $sheet->setCellValue("D{$row}", "Status");

    $sheet->getStyle("A{$row}:D{$row}")
        ->getFont()
        ->setBold(true);

    $sheet->getStyle("A{$row}:D{$row}")
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('D9EAD3');

    $headerRow = $row;
    $row++;

    $sql_notaris = "
        SELECT *
        FROM
        (
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
                ) AS cek,

                ROW_NUMBER() OVER(
                    PARTITION BY TRIM(n.telepon)
                    ORDER BY
                        CASE
                            WHEN (
                                SELECT COUNT(*)
                                FROM laporan l
                                WHERE l.id_notaris = n.id_notaris
                                AND YEAR(l.tanggal) = :tahun2
                                AND MONTH(l.tanggal) BETWEEN :bulan_awal2 AND :bulan_akhir2
                            ) > 0
                            THEN 1
                            ELSE 0
                        END DESC,
                        n.nama ASC
                ) AS rn

            FROM notaris n
            WHERE
                n.id_kedudukan = :id_kedudukan
                AND n.level = '2'
                AND n.aktif = '1'
        ) x
        WHERE rn = 1
        ORDER BY nama ASC
        ";

    $stmt_notaris = $koneksi->prepare($sql_notaris);

    $stmt_notaris->execute([
        ':id_kedudukan' => $id_kedudukan,
        ':tahun'        => $th,
        ':bulan_awal'   => $b1,
        ':bulan_akhir'  => $b2,
        ':tahun2'       => $th,
        ':bulan_awal2'  => $b1,
        ':bulan_akhir2' => $b2
    ]);

    $no = 1;
    $sudah = 0;
    $belum = 0;

    while ($data = $stmt_notaris->fetch(PDO::FETCH_ASSOC))
    {
        $status = ($data['cek'] > 0)
            ? 'SUDAH LAPOR'
            : 'BELUM LAPOR';

        if ($data['cek'] > 0) {
            $sudah++;
        } else {
            $belum++;
        }

        $sheet->setCellValue("A{$row}", $no++);
        $sheet->setCellValue("B{$row}", $data['nama']);
        $sheet->setCellValue("C{$row}", $data['telepon']);
        $sheet->setCellValue("D{$row}", $status);

        if ($status == 'SUDAH LAPOR')
        {
            $sheet->getStyle("D{$row}")
                ->getFont()
                ->getColor()
                ->setARGB('008000');
        }
        else
        {
            $sheet->getStyle("D{$row}")
                ->getFont()
                ->getColor()
                ->setARGB('FF0000');
        }

        $row++;
    }

    $sheet->setCellValue("A{$row}", "");
    $sheet->setCellValue("B{$row}", "TOTAL SUDAH LAPOR");
    $sheet->setCellValue("C{$row}", $sudah);

    $sheet->getStyle("B{$row}:C{$row}")
        ->getFont()
        ->setBold(true);

    $row++;

    $sheet->setCellValue("B{$row}", "TOTAL BELUM LAPOR");
    $sheet->setCellValue("C{$row}", $belum);

    $sheet->getStyle("B{$row}:C{$row}")
        ->getFont()
        ->setBold(true);

    $lastDataRow = $row;

    $sheet->getStyle("A{$headerRow}:D{$lastDataRow}")
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    foreach (range('A', 'D') as $col)
    {
        $sheet->getColumnDimension($col)
            ->setAutoSize(true);
    }

    $sheet->freezePane('A6');

    $sheetIndex++;
}

$spreadsheet->setActiveSheetIndex(0);

$filename = 'Detail_Kepatuhan_Notaris_'.$th.'_'.$b1.'-'.$b2.'.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;