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

$bulan_awal  = isset($_GET['bulan_awal'])
    ? (int)$_GET['bulan_awal']
    : 1;

$bulan_akhir = isset($_GET['bulan_akhir'])
    ? (int)$_GET['bulan_akhir']
    : date('m');

$tahun = isset($_GET['tahun'])
    ? (int)$_GET['tahun']
    : date('Y');


// Validasi

if ($bulan_awal < 1 || $bulan_awal > 12) {
    $bulan_awal = 1;
}

if ($bulan_akhir < 1 || $bulan_akhir > 12) {
    $bulan_akhir = 12;
}

if ($bulan_awal > $bulan_akhir) {
    $tmp = $bulan_awal;
    $bulan_awal = $bulan_akhir;
    $bulan_akhir = $tmp;
}


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
// 3. JUMLAH BULAN
// =====================================================

$jumlah_bulan =
    ($bulan_akhir - $bulan_awal) + 1;


// =====================================================
// 4. QUERY REKAP
//
// Hitung Notaris-Bulan, bukan DISTINCT Notaris.
// =====================================================

$sql = "
    SELECT

        k.id_kedudukan,

        k.nama_kedudukan AS mpd,

        COUNT(DISTINCT n.id_notaris)
            AS jml_notaris_aktif,

        COUNT(
            DISTINCT CASE
                WHEN la.tanggal IS NOT NULL
                THEN CONCAT(
                    n.id_notaris,
                    '-',
                    YEAR(la.tanggal),
                    '-',
                    MONTH(la.tanggal)
                )
            END
        ) AS jumlah_laporan_masuk,

        COALESCE(
            SUM(la.jml_buku_daftar),
            0
        ) AS jml_buku_daftar,

        COALESCE(
            SUM(la.jml_tangan_dibukukan),
            0
        ) AS jml_tangan_dibukukan,

        COALESCE(
            SUM(la.jml_tangan_disahkan),
            0
        ) AS jml_tangan_disahkan,

        COALESCE(
            SUM(la.jml_buku_protes),
            0
        ) AS jml_buku_protes,

        COALESCE(
            SUM(
                COALESCE(la.jml_buku_daftar, 0)
                +
                COALESCE(la.jml_tangan_dibukukan, 0)
                +
                COALESCE(la.jml_tangan_disahkan, 0)
                +
                COALESCE(la.jml_buku_protes, 0)
            ),
            0
        ) AS total_akta

    FROM kedudukan k

    LEFT JOIN notaris n
        ON n.id_kedudukan = k.id_kedudukan
        AND n.level = '2'
        AND n.aktif = '1'

    LEFT JOIN laporan la
        ON la.id_notaris = n.id_notaris
        AND YEAR(la.tanggal) = :tahun
        AND MONTH(la.tanggal)
            BETWEEN :bulan_awal AND :bulan_akhir

    GROUP BY
        k.id_kedudukan,
        k.nama_kedudukan

    ORDER BY
        k.nama_kedudukan ASC
";

$stmt = $koneksi->prepare($sql);

$stmt->execute([
    ':tahun'       => $tahun,
    ':bulan_awal'  => $bulan_awal,
    ':bulan_akhir' => $bulan_akhir
]);


// =====================================================
// 5. SPREADSHEET
// =====================================================

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();


// =====================================================
// 6. JUDUL
// =====================================================

$sheet->setCellValue(
    'A1',
    'REKAP KEPATUHAN LAPORAN BULANAN NOTARIS'
);

$sheet->mergeCells('A1:L1');

$sheet->setCellValue(
    'A2',
    'Periode: ' .
    $nama_bulan[$bulan_awal] .
    ' s/d ' .
    $nama_bulan[$bulan_akhir] .
    ' ' .
    $tahun .
    ' | Kewajiban: ' .
    $jumlah_bulan .
    ' laporan/notaris'
);

$sheet->mergeCells('A2:L2');


// Style judul

$sheet->getStyle('A1')
    ->getFont()
    ->setBold(true)
    ->setSize(16);

$sheet->getStyle('A1:L2')
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getStyle('A1:L2')
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_CENTER);


// =====================================================
// 7. HEADER
// =====================================================

$sheet->setCellValue('A4', 'No');
$sheet->setCellValue('B4', 'MPD (Kedudukan)');
$sheet->setCellValue('C4', 'Notaris Aktif');
$sheet->setCellValue('D4', 'Laporan Masuk');
$sheet->setCellValue('E4', 'Kewajiban');
$sheet->setCellValue('F4', 'Belum');
$sheet->setCellValue('G4', 'Kepatuhan (%)');

$sheet->setCellValue('H4', 'Rincian Akta');
$sheet->mergeCells('H4:K4');

$sheet->setCellValue('L4', 'Total Akta');

$sheet->setCellValue('H5', 'Buku Daftar');
$sheet->setCellValue('I5', 'Waarmerking');
$sheet->setCellValue('J5', 'Legalisasi');
$sheet->setCellValue('K5', 'Buku Protes');


// rowspan

foreach (
    ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'L']
    as $col
) {

    $sheet->mergeCells(
        $col . '4:' .
        $col . '5'
    );
}


// =====================================================
// 8. STYLE HEADER
// =====================================================

$headerStyle = [

    'font' => [
        'bold' => true,
        'color' => [
            'rgb' => 'FFFFFF'
        ]
    ],

    'alignment' => [
        'horizontal' =>
            Alignment::HORIZONTAL_CENTER,

        'vertical' =>
            Alignment::VERTICAL_CENTER,

        'wrapText' => true
    ],

    'fill' => [
        'fillType' =>
            Fill::FILL_SOLID,

        'startColor' => [
            'rgb' => '2C3E50'
        ]
    ],

    'borders' => [
        'allBorders' => [
            'borderStyle' =>
                Border::BORDER_THIN
        ]
    ]
];

$sheet
    ->getStyle('A4:L5')
    ->applyFromArray($headerStyle);


// =====================================================
// 9. ISI DATA
// =====================================================

$rowNum = 6;

$no = 1;

$g_notaris   = 0;
$g_masuk     = 0;
$g_kewajiban = 0;
$g_belum     = 0;
$g_akta      = 0;


while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $notaris_aktif =
        (int)$row['jml_notaris_aktif'];

    $laporan_masuk =
        (int)$row['jumlah_laporan_masuk'];

    $kewajiban =
        $notaris_aktif *
        $jumlah_bulan;

    $belum =
        max(
            0,
            $kewajiban -
            $laporan_masuk
        );

    $persen =
        ($kewajiban > 0)
        ? ($laporan_masuk / $kewajiban)
        : 0;


    // ---------------------------------------------
    // DATA
    // ---------------------------------------------

    $sheet->setCellValue(
        'A' . $rowNum,
        $no++
    );

    $sheet->setCellValue(
        'B' . $rowNum,
        $row['mpd']
    );

    $sheet->setCellValue(
        'C' . $rowNum,
        $notaris_aktif
    );

    $sheet->setCellValue(
        'D' . $rowNum,
        $laporan_masuk
    );

    $sheet->setCellValue(
        'E' . $rowNum,
        $kewajiban
    );

    $sheet->setCellValue(
        'F' . $rowNum,
        $belum
    );

    $sheet->setCellValue(
        'G' . $rowNum,
        $persen
    );

    $sheet->setCellValue(
        'H' . $rowNum,
        (int)$row['jml_buku_daftar']
    );

    $sheet->setCellValue(
        'I' . $rowNum,
        (int)$row['jml_tangan_dibukukan']
    );

    $sheet->setCellValue(
        'J' . $rowNum,
        (int)$row['jml_tangan_disahkan']
    );

    $sheet->setCellValue(
        'K' . $rowNum,
        (int)$row['jml_buku_protes']
    );

    $sheet->setCellValue(
        'L' . $rowNum,
        (int)$row['total_akta']
    );


    // ---------------------------------------------
    // FORMAT %
    // ---------------------------------------------

    $sheet
        ->getStyle('G' . $rowNum)
        ->getNumberFormat()
        ->setFormatCode('0.00%');


    // ---------------------------------------------
    // WARNA BELUM
    // ---------------------------------------------

    if ($belum > 0) {

        $sheet
            ->getStyle('F' . $rowNum)
            ->getFont()
            ->getColor()
            ->setARGB('FFFF0000');

    }


    // ---------------------------------------------
    // TOTAL
    // ---------------------------------------------

    $g_notaris += $notaris_aktif;
    $g_masuk += $laporan_masuk;
    $g_kewajiban += $kewajiban;
    $g_belum += $belum;
    $g_akta += (int)$row['total_akta'];

    $rowNum++;
}


// =====================================================
// 10. TOTAL KESELURUHAN
// =====================================================

$sheet->setCellValue(
    'A' . $rowNum,
    'TOTAL KESELURUHAN'
);

$sheet->mergeCells(
    'A' . $rowNum .
    ':B' . $rowNum
);

$sheet->setCellValue(
    'C' . $rowNum,
    $g_notaris
);

$sheet->setCellValue(
    'D' . $rowNum,
    $g_masuk
);

$sheet->setCellValue(
    'E' . $rowNum,
    $g_kewajiban
);

$sheet->setCellValue(
    'F' . $rowNum,
    $g_belum
);

$p_total =
    ($g_kewajiban > 0)
    ? ($g_masuk / $g_kewajiban)
    : 0;

$sheet->setCellValue(
    'G' . $rowNum,
    $p_total
);

$sheet
    ->getStyle('G' . $rowNum)
    ->getNumberFormat()
    ->setFormatCode('0.00%');


$sheet->setCellValue(
    'L' . $rowNum,
    $g_akta
);


// =====================================================
// 11. STYLE TOTAL
// =====================================================

$sheet
    ->getStyle(
        'A' . $rowNum .
        ':L' . $rowNum
    )
    ->getFont()
    ->setBold(true);


// =====================================================
// 12. BORDER
// =====================================================

$sheet
    ->getStyle(
        'A4:L' . $rowNum
    )
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(
        Border::BORDER_THIN
    );


// =====================================================
// 13. ALIGNMENT
// =====================================================

$sheet
    ->getStyle(
        'A4:L' . $rowNum
    )
    ->getAlignment()
    ->setVertical(
        Alignment::VERTICAL_CENTER
    );

$sheet
    ->getStyle(
        'A4:A' . $rowNum
    )
    ->getAlignment()
    ->setHorizontal(
        Alignment::HORIZONTAL_CENTER
    );

$sheet
    ->getStyle(
        'C4:L' . $rowNum
    )
    ->getAlignment()
    ->setHorizontal(
        Alignment::HORIZONTAL_CENTER
    );


// =====================================================
// 14. AUTOSIZE
// =====================================================

foreach (range('A', 'L') as $col) {

    $sheet
        ->getColumnDimension($col)
        ->setAutoSize(true);
}


$sheet->freezePane('A6');


// =====================================================
// 15. OUTPUT
// =====================================================

$filename =
    'Rekap_Laporan_Notaris_' .
    $tahun .
    '_' .
    str_pad($bulan_awal, 2, '0', STR_PAD_LEFT) .
    '-' .
    str_pad($bulan_akhir, 2, '0', STR_PAD_LEFT) .
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