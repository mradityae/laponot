<?php

require '../vendor/autoload.php';
include("../config/koneksi.php");
// include("../log_activity.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache3;

// ====================================================
// OPTIMASI
// ====================================================

ini_set('memory_limit', '1024M');
set_time_limit(0);

Settings::setCache(new SimpleCache3());

// ====================================================
// PARAMETER
// ====================================================

$id = $_GET['id'] ?? null;
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$jenis_transaksi = $_GET['jenis_transaksi'] ?? '';

if (!$id) {
    exit("ID Notaris tidak ditemukan.");
}

$valid_jenis = [
    'Pendaftaran',
    'Perubahan',
    'Perbaikan',
    'Penghapusan'
];

if (!in_array($jenis_transaksi, $valid_jenis)) {
    $jenis_transaksi = '';
}

// ====================================================
// DATA NOTARIS
// ====================================================

$stmt = $koneksi->prepare("
SELECT
    n.nama AS nama_notaris,
    k.nama_kedudukan
FROM notaris n
JOIN kedudukan k
ON n.id_kedudukan=k.id_kedudukan
WHERE n.id_notaris=:id
");

$stmt->execute([
    ':id'=>$id
]);

$dataNotaris = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dataNotaris){
    exit("Data notaris tidak ditemukan.");
}

$namaNotaris   = $dataNotaris['nama_notaris'];
$namaKedudukan = $dataNotaris['nama_kedudukan'];

$periode = "SEMUA";

if($tanggal_awal && $tanggal_akhir){

    $periode =
        date('d-m-Y',strtotime($tanggal_awal)).
        " s.d. ".
        date('d-m-Y',strtotime($tanggal_akhir));

}

// ====================================================
// SPREADSHEET
// ====================================================

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Laporan');

// ====================================================
// HEADER
// ====================================================

$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:F2');
$sheet->mergeCells('A3:F3');
$sheet->mergeCells('A4:F4');
$sheet->mergeCells('A5:F5');

$sheet->setCellValue('A1','FORMAT LAPORAN AKTA JAMINAN FIDUSIA YANG DIBUAT NOTARIS');
$sheet->setCellValue('A2',"PERIODE : ".$periode);
$sheet->setCellValue('A3',"NAMA NOTARIS : ".strtoupper($namaNotaris));
$sheet->setCellValue('A4',"KEDUDUKAN : ".strtoupper($namaKedudukan));
$sheet->setCellValue('A5',"JENIS TRANSAKSI : ".($jenis_transaksi ?: "SEMUA"));

$sheet->fromArray([
    [
        'No',
        'Nomor Akta',
        'Tanggal Akta',
        'Nama Pemberi Fidusia',
        'Nama Penerima Fidusia',
        'Nomor Sertifikat'
    ]
],NULL,'A7');

// ====================================================
// QUERY
// ====================================================

$sql="

SELECT

nomor,
tanggal,
pemberi,
penerima,
no_sertifikat

FROM laporan_entitas

WHERE id_notaris=:id

";

$params=[
    ':id'=>$id
];

if($tanggal_awal && $tanggal_akhir){

    $sql.=" AND tanggal BETWEEN :awal AND :akhir";

    $params[':awal']=$tanggal_awal;
    $params[':akhir']=$tanggal_akhir;

}

if($jenis_transaksi){

    $sql.=" AND jenis_transaksi=:jenis";

    $params[':jenis']=$jenis_transaksi;

}

$sql.=" ORDER BY tanggal ASC";

$stmt=$koneksi->prepare($sql);
$stmt->execute($params);

// ====================================================
// DATA
// ====================================================

$rowExcel=8;
$no=1;

while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

    $sheet->fromArray([[
        $no++,
        $row['nomor'],
        date('d-m-Y',strtotime($row['tanggal'])),
        strtoupper($row['pemberi']),
        strtoupper($row['penerima']),
        $row['no_sertifikat']
    ]],NULL,"A".$rowExcel);

    $rowExcel++;

}

// ====================================================
// STYLE
// ====================================================

$sheet->getStyle('A1:F5')
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getStyle('A7:F7')->getFont()->setBold(true);

$sheet->getStyle('A7:F7')
    ->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setRGB('DDDDDD');

$lastRow=$rowExcel-1;

if($lastRow>=7){

    $sheet->getStyle("A7:F".$lastRow)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

}

// ====================================================
// LEBAR KOLOM (JANGAN AUTOSIZE)
// ====================================================

$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(18);
$sheet->getColumnDimension('D')->setWidth(40);
$sheet->getColumnDimension('E')->setWidth(40);
$sheet->getColumnDimension('F')->setWidth(28);

// ====================================================
// OUTPUT
// ====================================================

$filename="Laporan Akta Fidusia - ".
preg_replace('/[^A-Za-z0-9 ]/','',$namaNotaris).
".xlsx";

// write_log("Export Excel : ".$filename);

while(ob_get_level()){
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer=new Xlsx($spreadsheet);
$writer->setPreCalculateFormulas(false);
$writer->save('php://output');

$spreadsheet->disconnectWorksheets();
unset($spreadsheet);

exit;