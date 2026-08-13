<?php

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

include("../config/koneksi.php");


// =====================================================
// 1. PARAMETER
// =====================================================

$b1 = isset($_GET['bulan_awal'])
    ? (int)$_GET['bulan_awal']
    : 1;

$b2 = isset($_GET['bulan_akhir'])
    ? (int)$_GET['bulan_akhir']
    : date('m');

$th = isset($_GET['tahun'])
    ? (int)$_GET['tahun']
    : date('Y');


if ($b1 < 1 || $b1 > 12) {
    $b1 = 1;
}

if ($b2 < 1 || $b2 > 12) {
    $b2 = 12;
}

if ($b1 > $b2) {
    $tmp = $b1;
    $b1 = $b2;
    $b2 = $tmp;
}


$jumlah_bulan =
    ($b2 - $b1) + 1;


// =====================================================
// 2. NAMA BULAN
// =====================================================

$nama_bulan = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];


// =====================================================
// 3. KEDUDUKAN
// =====================================================

$stmt_kedudukan = $koneksi->prepare("
    SELECT
        id_kedudukan,
        nama_kedudukan
    FROM kedudukan
    ORDER BY nama_kedudukan ASC
");

$stmt_kedudukan->execute();


$spreadsheet = new Spreadsheet();

$sheetIndex = 0;


// =====================================================
// 4. LOOP KEDUDUKAN
// =====================================================

while (
    $kedudukan =
        $stmt_kedudukan->fetch(PDO::FETCH_ASSOC)
) {

    $id_kedudukan =
        (int)$kedudukan['id_kedudukan'];

    $nama_kedudukan =
        $kedudukan['nama_kedudukan'];


    // ==============================================
    // CREATE SHEET
    // ==============================================

    if ($sheetIndex == 0) {

        $sheet =
            $spreadsheet->getActiveSheet();

    } else {

        $sheet =
            $spreadsheet->createSheet();
    }


    $sheetTitle =
        substr(
            preg_replace(
                '/[\\\\\\/\\?\\*\\[\\]:]/',
                '',
                $nama_kedudukan
            ),
            0,
            31
        );

    if ($sheetTitle == '') {
        $sheetTitle =
            'Kedudukan_' .
            $id_kedudukan;
    }

    $sheet->setTitle($sheetTitle);


    // ==============================================
    // JUDUL
    // ==============================================

    $row = 1;

    $sheet->mergeCells(
        "A{$row}:G{$row}"
    );

    $sheet->setCellValue(
        "A{$row}",
        "DAFTAR KEPATUHAN LAPORAN NOTARIS - SUDAH LENGKAP"
    );

    $sheet
        ->getStyle("A{$row}")
        ->getFont()
        ->setBold(true)
        ->setSize(14);

    $sheet
        ->getStyle("A{$row}:G{$row}")
        ->getAlignment()
        ->setHorizontal(
            Alignment::HORIZONTAL_CENTER
        );

    $row++;


    // ==============================================
    // KEDUDUKAN
    // ==============================================

    $sheet->mergeCells(
        "A{$row}:G{$row}"
    );

    $sheet->setCellValue(
        "A{$row}",
        "KEDUDUKAN : " .
        strtoupper($nama_kedudukan)
    );

    $sheet
        ->getStyle("A{$row}")
        ->getFont()
        ->setBold(true);

    $row++;


    // ==============================================
    // PERIODE
    // ==============================================

    $sheet->mergeCells(
        "A{$row}:G{$row}"
    );

    $sheet->setCellValue(
        "A{$row}",
        "PERIODE : " .
        $nama_bulan[$b1] .
        " S/D " .
        $nama_bulan[$b2] .
        " " .
        $th .
        " | KEWAJIBAN : " .
        $jumlah_bulan .
        " LAPORAN / NOTARIS"
    );

    $row += 2;


    // ==============================================
    // HEADER
    // ==============================================

    $sheet->setCellValue(
        "A{$row}",
        "No"
    );

    $sheet->setCellValue(
        "B{$row}",
        "ID Notaris"
    );

    $sheet->setCellValue(
        "C{$row}",
        "Nama Notaris"
    );

    $sheet->setCellValue(
        "D{$row}",
        "Telepon"
    );

    $sheet->setCellValue(
        "E{$row}",
        "Jumlah Laporan"
    );

    $sheet->setCellValue(
        "F{$row}",
        "Bulan Dilaporkan"
    );

    $sheet->setCellValue(
        "G{$row}",
        "Status"
    );


    $headerRow = $row;


    $sheet
        ->getStyle(
            "A{$row}:G{$row}"
        )
        ->getFont()
        ->setBold(true);

    $sheet
        ->getStyle(
            "A{$row}:G{$row}"
        )
        ->getFill()
        ->setFillType(
            Fill::FILL_SOLID
        )
        ->getStartColor()
        ->setARGB('D9EAD3');

    $sheet
        ->getStyle(
            "A{$row}:G{$row}"
        )
        ->getAlignment()
        ->setHorizontal(
            Alignment::HORIZONTAL_CENTER
        );


    $row++;


    // ==============================================
    // NOTARIS
    // ==============================================

    $sql_notaris = "
        SELECT
            n.id_notaris,
            n.nama,
            n.telepon
        FROM notaris n
        WHERE n.id_kedudukan = :id
          AND n.level = '2'
          AND n.aktif = '1'
        ORDER BY n.nama ASC
    ";

    $stmt_notaris =
        $koneksi->prepare($sql_notaris);

    $stmt_notaris->execute([
        ':id' => $id_kedudukan
    ]);


    // ==============================================
    // LAPORAN
    // ==============================================

    $sql_laporan = "
        SELECT
            id_notaris,
            MONTH(tanggal) AS bulan
        FROM laporan
        WHERE YEAR(tanggal) = :tahun
          AND MONTH(tanggal)
              BETWEEN :b1 AND :b2
        GROUP BY
            id_notaris,
            MONTH(tanggal)
    ";

    $stmt_laporan =
        $koneksi->prepare($sql_laporan);

    $stmt_laporan->execute([
        ':tahun' => $th,
        ':b1'    => $b1,
        ':b2'    => $b2
    ]);


    // ==============================================
    // MAP
    // ==============================================

    $laporan_map = [];

    while (
        $lap =
            $stmt_laporan->fetch(PDO::FETCH_ASSOC)
    ) {

        $id_notaris =
            (int)$lap['id_notaris'];

        $bulan =
            (int)$lap['bulan'];

        if (!isset($laporan_map[$id_notaris])) {
            $laporan_map[$id_notaris] = [];
        }

        $laporan_map[$id_notaris][$bulan] = true;
    }


    // ==============================================
    // DATA
    // ==============================================

    $no = 1;

    $total_sudah = 0;


    while (
        $data =
            $stmt_notaris->fetch(PDO::FETCH_ASSOC)
    ) {

        $id_notaris =
            (int)$data['id_notaris'];

        $bulan_sudah = [];

        $bulan_belum = [];


        // ------------------------------------------
        // CEK SEMUA BULAN
        // ------------------------------------------

        for ($m = $b1; $m <= $b2; $m++) {

            if (
                isset(
                    $laporan_map[$id_notaris][$m]
                )
            ) {

                $bulan_sudah[] =
                    $nama_bulan[$m];

            } else {

                $bulan_belum[] =
                    $nama_bulan[$m];
            }
        }


        // ------------------------------------------
        // HANYA LENGKAP
        // ------------------------------------------

        if (count($bulan_belum) > 0) {
            continue;
        }


        // ------------------------------------------
        // ISI
        // ------------------------------------------

        $sheet->setCellValue(
            "A{$row}",
            $no++
        );

        $sheet->setCellValue(
            "B{$row}",
            $id_notaris
        );

        $sheet->setCellValue(
            "C{$row}",
            $data['nama']
        );

        $sheet->setCellValue(
            "D{$row}",
            $data['telepon']
        );

        $sheet->setCellValue(
            "E{$row}",
            $jumlah_bulan
        );

        $sheet->setCellValue(
            "F{$row}",
            implode(
                ', ',
                $bulan_sudah
            )
        );

        $sheet->setCellValue(
            "G{$row}",
            "SUDAH LENGKAP"
        );


        $sheet
            ->getStyle("G{$row}")
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB('FF008000');


        $total_sudah++;

        $row++;
    }


    // ==============================================
    // TOTAL
    // ==============================================

    $sheet->setCellValue(
        "B{$row}",
        "TOTAL SUDAH LENGKAP"
    );

    $sheet->setCellValue(
        "C{$row}",
        $total_sudah
    );

    $sheet
        ->getStyle(
            "B{$row}:C{$row}"
        )
        ->getFont()
        ->setBold(true);


    $lastRow = $row;


    // ==============================================
    // BORDER
    // ==============================================

    $sheet
        ->getStyle(
            "A{$headerRow}:G{$lastRow}"
        )
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(
            Border::BORDER_THIN
        );


    // ==============================================
    // AUTOSIZE
    // ==============================================

    foreach (
        range('A', 'G')
        as $col
    ) {

        $sheet
            ->getColumnDimension($col)
            ->setAutoSize(true);
    }


    $sheet->freezePane(
        "A" . ($headerRow + 1)
    );


    $sheetIndex++;
}


// =====================================================
// 5. OUTPUT
// =====================================================

$spreadsheet->setActiveSheetIndex(0);

$filename =
    'Detail_Kepatuhan_Notaris_Sudah_Lengkap_' .
    $th .
    '_' .
    $b1 .
    '-' .
    $b2 .
    '.xlsx';

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="' .
    $filename .
    '"'
);

header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);

$writer->save('php://output');

exit;