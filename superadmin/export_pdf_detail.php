<?php

require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include("../config/koneksi.php");

// =====================================================
// 1. PARAMETER
// =====================================================

$id = isset($_GET['id_kedudukan'])
    ? (int)$_GET['id_kedudukan']
    : 0;

$b1 = isset($_GET['bulan_awal'])
    ? (int)$_GET['bulan_awal']
    : 1;

$b2 = isset($_GET['bulan_akhir'])
    ? (int)$_GET['bulan_akhir']
    : date('m');

$th = isset($_GET['tahun'])
    ? (int)$_GET['tahun']
    : date('Y');


// Validasi bulan

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
// 3. NAMA KEDUDUKAN
// =====================================================

$stmt_k = $koneksi->prepare("
    SELECT nama_kedudukan
    FROM kedudukan
    WHERE id_kedudukan = ?
");

$stmt_k->execute([$id]);

$nama_kedudukan = $stmt_k->fetchColumn();

if (!$nama_kedudukan) {
    $nama_kedudukan = 'Tidak Diketahui';
}


// =====================================================
// 4. PERIODE
// =====================================================

$jumlah_bulan = ($b2 - $b1) + 1;

if ($b1 == $b2) {

    $periode_text =
        $nama_bulan[$b1] . ' ' . $th;

} else {

    $periode_text =
        $nama_bulan[$b1] .
        ' s/d ' .
        $nama_bulan[$b2] .
        ' ' .
        $th;
}


// =====================================================
// 5. AMBIL DATA NOTARIS
// =====================================================

$sql = "
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

$stmt = $koneksi->prepare($sql);

$stmt->execute([
    ':id' => $id
]);

$notaris = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// 6. AMBIL LAPORAN PER NOTARIS + BULAN
//
// Hanya satu kombinasi Notaris-Bulan yang dihitung.
// Kalau ada beberapa laporan di bulan yang sama,
// tetap dihitung sebagai 1 kewajiban.
// =====================================================

$sql_laporan = "
    SELECT
        id_notaris,
        YEAR(tanggal) AS tahun,
        MONTH(tanggal) AS bulan

    FROM laporan

    WHERE YEAR(tanggal) = :tahun
      AND MONTH(tanggal) BETWEEN :b1 AND :b2

    GROUP BY
        id_notaris,
        YEAR(tanggal),
        MONTH(tanggal)
";

$stmt_laporan = $koneksi->prepare($sql_laporan);

$stmt_laporan->execute([
    ':tahun' => $th,
    ':b1'    => $b1,
    ':b2'    => $b2
]);

$laporan_rows = $stmt_laporan->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// 7. INDEX LAPORAN
//
// $laporan_map[id_notaris][bulan] = true
// =====================================================

$laporan_map = [];

foreach ($laporan_rows as $laporan) {

    $id_notaris = (int)$laporan['id_notaris'];
    $bulan      = (int)$laporan['bulan'];

    if (!isset($laporan_map[$id_notaris])) {
        $laporan_map[$id_notaris] = [];
    }

    $laporan_map[$id_notaris][$bulan] = true;
}


// =====================================================
// 8. SUMMARY
// =====================================================

$total_notaris = count($notaris);

$total_kewajiban = $total_notaris * $jumlah_bulan;

$total_laporan_masuk = 0;

$total_belum = 0;

$jumlah_sudah_lengkap = 0;

$jumlah_belum_lengkap = 0;


// =====================================================
// 9. HTML PDF
// =====================================================

$html = '

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<style>

@page {
    margin: 25px 25px 30px 25px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 10px;
    color: #222;
}

.header {
    text-align: center;
    margin-bottom: 15px;
}

.header h2 {
    margin: 0 0 5px 0;
    font-size: 16px;
}

.header h3 {
    margin: 0 0 5px 0;
    font-size: 13px;
}

.header p {
    margin: 3px 0;
    font-size: 10px;
}

.summary {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}

.summary td {
    border: 1px solid #999;
    padding: 7px;
    text-align: center;
}

.summary-title {
    font-size: 8px;
    color: #555;
}

.summary-number {
    font-size: 14px;
    font-weight: bold;
}

table.data {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table.data th {
    background-color: #2c3e50;
    color: white;
    border: 1px solid #222;
    padding: 6px;
    text-align: center;
    font-size: 9px;
}

table.data td {
    border: 1px solid #999;
    padding: 5px;
    vertical-align: middle;
    font-size: 9px;
}

.text-center {
    text-align: center;
}

.text-left {
    text-align: left;
}

.status-lengkap {
    color: #198754;
    font-weight: bold;
}

.status-belum {
    color: #dc3545;
    font-weight: bold;
}

.bulan-sudah {
    color: #198754;
    font-size: 8px;
}

.bulan-belum {
    color: #dc3545;
    font-weight: bold;
    font-size: 8px;
}

.footer {
    margin-top: 15px;
    font-size: 8px;
    color: #666;
}

</style>

</head>

<body>


<div class="header">

    <h2>
        DAFTAR KEPATUHAN LAPORAN NOTARIS
    </h2>

    <h3>
        KEDUDUKAN: ' .
        htmlspecialchars(
            strtoupper($nama_kedudukan),
            ENT_QUOTES,
            'UTF-8'
        ) .
    '</h3>

    <p>
        Periode: <strong>' .
        htmlspecialchars(
            $periode_text,
            ENT_QUOTES,
            'UTF-8'
        ) .
    '</strong>
    </p>

    <p>
        Kewajiban setiap Notaris:
        <strong>' .
        $jumlah_bulan .
        ' laporan</strong>
    </p>

</div>


<table class="summary">

<tr>

    <td width="20%">
        <div class="summary-title">
            NOTARIS AKTIF
        </div>
        <div class="summary-number">
            ' . number_format($total_notaris) . '
        </div>
    </td>

    <td width="20%">
        <div class="summary-title">
            TOTAL KEWAJIBAN
        </div>
        <div class="summary-number">
            ' . number_format($total_kewajiban) . '
        </div>
    </td>

    <td width="20%">
        <div class="summary-title">
            LAPORAN MASUK
        </div>
        <div class="summary-number">
            ' . number_format($total_laporan_masuk) . '
        </div>
    </td>

    <td width="20%">
        <div class="summary-title">
            BELUM LAPOR
        </div>
        <div class="summary-number">
            ' . number_format($total_belum) . '
        </div>
    </td>

    <td width="20%">
        <div class="summary-title">
            KEPATUHAN
        </div>
        <div class="summary-number">
            -
        </div>
    </td>

</tr>

</table>


<table class="data">

<thead>

<tr>

    <th width="4%">
        No
    </th>

    <th width="22%">
        Nama Notaris
    </th>

    <th width="14%">
        No. HP
    </th>

    <th width="10%">
        Sudah
    </th>

    <th width="24%">
        Bulan Sudah Lapor
    </th>

    <th width="20%">
        Bulan Belum Lapor
    </th>

    <th width="10%">
        Status
    </th>

</tr>

</thead>

<tbody>
';


// =====================================================
// 10. DATA NOTARIS
// =====================================================

$no = 1;

foreach ($notaris as $d) {

    $id_notaris = (int)$d['id_notaris'];

    $bulan_sudah = [];

    $bulan_belum = [];

    $jumlah_sudah = 0;


    // ==============================================
    // CEK SETIAP BULAN
    // ==============================================

    for ($m = $b1; $m <= $b2; $m++) {

        if (
            isset($laporan_map[$id_notaris][$m]) &&
            $laporan_map[$id_notaris][$m] === true
        ) {

            $jumlah_sudah++;

            $bulan_sudah[] =
                $nama_bulan[$m];

        } else {

            $bulan_belum[] =
                $nama_bulan[$m];

        }
    }


    // ==============================================
    // HITUNG GLOBAL
    // ==============================================

    $jumlah_belum =
        $jumlah_bulan - $jumlah_sudah;

    $total_laporan_masuk +=
        $jumlah_sudah;

    $total_belum +=
        $jumlah_belum;


    // ==============================================
    // STATUS
    // ==============================================

    if ($jumlah_belum == 0) {

        $status =
            'SUDAH LENGKAP';

        $status_class =
            'status-lengkap';

        $jumlah_sudah_lengkap++;

    } else {

        $status =
            'BELUM LENGKAP';

        $status_class =
            'status-belum';

        $jumlah_belum_lengkap++;
    }


    // ==============================================
    // FORMAT BULAN SUDAH
    // ==============================================

    if (count($bulan_sudah) > 0) {

        $bulan_sudah_html = '';

        foreach ($bulan_sudah as $bulan) {

            $bulan_sudah_html .=
                '<span class="bulan-sudah">' .
                htmlspecialchars(
                    $bulan,
                    ENT_QUOTES,
                    'UTF-8'
                ) .
                '</span><br>';

        }

    } else {

        $bulan_sudah_html = '-';
    }


    // ==============================================
    // FORMAT BULAN BELUM
    // ==============================================

    if (count($bulan_belum) > 0) {

        $bulan_belum_html = '';

        foreach ($bulan_belum as $bulan) {

            $bulan_belum_html .=
                '<span class="bulan-belum">' .
                htmlspecialchars(
                    $bulan,
                    ENT_QUOTES,
                    'UTF-8'
                ) .
                '</span><br>';

        }

    } else {

        $bulan_belum_html =
            '<span class="status-lengkap">-</span>';
    }


    // ==============================================
    // NAMA & TELEPON
    // ==============================================

    $nama =
        htmlspecialchars(
            $d['nama'],
            ENT_QUOTES,
            'UTF-8'
        );

    $telepon =
        htmlspecialchars(
            $d['telepon'] ?: '-',
            ENT_QUOTES,
            'UTF-8'
        );


    // ==============================================
    // ROW
    // ==============================================

    $html .= '

    <tr>

        <td class="text-center">
            ' . $no++ . '
        </td>

        <td class="text-left">
            <strong>' . $nama . '</strong>
        </td>

        <td class="text-center">
            ' . $telepon . '
        </td>

        <td class="text-center">
            ' .
            $jumlah_sudah .
            ' / ' .
            $jumlah_bulan .
            '
        </td>

        <td class="text-left">
            ' .
            $bulan_sudah_html .
            '
        </td>

        <td class="text-left">
            ' .
            $bulan_belum_html .
            '
        </td>

        <td class="text-center ' .
            $status_class .
            '">
            ' .
            $status .
            '
        </td>

    </tr>

    ';
}


// =====================================================
// 11. HITUNG PERSENTASE
//
// SETELAH seluruh data diproses.
// =====================================================

$persentase =
    ($total_kewajiban > 0)
    ? ($total_laporan_masuk / $total_kewajiban) * 100
    : 0;


// =====================================================
// 12. SELESAIKAN SUMMARY
//
// Karena summary berada di atas tabel,
// kita replace tanda "-" dengan persentase.
// =====================================================

$html = str_replace(
    '<div class="summary-number">
            -
        </div>',
    '<div class="summary-number">
            ' . number_format($persentase, 2) . '%
        </div>',
    $html
);


// =====================================================
// 13. TUTUP TABLE
// =====================================================

$html .= '

</tbody>

</table>


<div class="footer">

    <strong>Keterangan:</strong>

    Persentase kepatuhan dihitung berdasarkan:

    <strong>
        jumlah laporan masuk ÷ total kewajiban laporan × 100%
    </strong>

    <br><br>

    Total kewajiban laporan =
    jumlah Notaris aktif × jumlah bulan dalam periode.

    <br><br>

    Dicetak pada:
    ' . date('d-m-Y H:i:s') . '

</div>


</body>

</html>
';


// =====================================================
// 14. DOMPDF
// =====================================================

$options = new Options();

$options->set(
    'isRemoteEnabled',
    true
);

$options->set(
    'defaultFont',
    'DejaVu Sans'
);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

$dompdf->setPaper(
    'A4',
    'landscape'
);

$dompdf->render();


// =====================================================
// 15. NAMA FILE
// =====================================================

$nama_file =
    'Detail_Laporan_' .
    preg_replace(
        '/[^A-Za-z0-9_-]/',
        '_',
        $nama_kedudukan
    ) .
    '_' .
    $th .
    '_' .
    $b1 .
    '-' .
    $b2 .
    '.pdf';


// =====================================================
// 16. OUTPUT PDF
// =====================================================

$dompdf->stream(
    $nama_file,
    [
        "Attachment" => false
    ]
);

exit;
?>