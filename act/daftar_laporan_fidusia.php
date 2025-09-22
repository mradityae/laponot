<?php
include "../config/koneksi.php";

$columns = ['nama','pemberi','penerima','jenis_transaksi','nilai_penjaminan','tanggal'];

// STATIC DEFAULT
$start = 0;
$length = 10;
$draw = 1;
$order_column_index = 6; // kolom tanggal
$order_dir = 'desc';
$search = '';

// ambil filter dari form
$tgl_a = $_POST['tgl_a'] ?? '2025-01-01';
$tgl_b = $_POST['tgl_b'] ?? date('Y-m-d');
$id_kedudukan = $_POST['id_kedudukan'] ?? '';
$id_notaris = $_POST['id_notaris'] ?? '';
$jenis_transaksi = $_POST['jenis_transaksi'] ?? '';

// query total
$totalQuery = "SELECT COUNT(*) as total FROM laporan_entitas l
               JOIN notaris n ON l.id_notaris=n.id_notaris
               WHERE DATE(l.tanggal) BETWEEN :tgl_a AND :tgl_b";
$stmt = $koneksi->prepare($totalQuery);
$stmt->bindParam(':tgl_a',$tgl_a);
$stmt->bindParam(':tgl_b',$tgl_b);
$stmt->execute();
$totalData = $stmt->fetch()['total'];

// filter tambahan
$filterSql = "";
if($id_kedudukan) $filterSql .= " AND n.id_kedudukan = ".(int)$id_kedudukan;
if($id_notaris) $filterSql .= " AND n.id_notaris = ".(int)$id_notaris;
if($jenis_transaksi) $filterSql .= " AND l.jenis_transaksi = '".$jenis_transaksi."'";

// total filtered
$totalFilteredQuery = "SELECT COUNT(*) as total FROM laporan_entitas l
                       JOIN notaris n ON l.id_notaris=n.id_notaris
                       WHERE DATE(l.tanggal) BETWEEN :tgl_a AND :tgl_b $filterSql";
$stmt = $koneksi->prepare($totalFilteredQuery);
$stmt->bindParam(':tgl_a',$tgl_a);
$stmt->bindParam(':tgl_b',$tgl_b);
$stmt->execute();
$totalFiltered = $stmt->fetch()['total'];

// order column mapping
$order_column = $columns[$order_column_index-1] ?? 'tanggal';

// fetch data
$dataQuery = "SELECT n.nama, l.pemberi, l.penerima, l.jenis_transaksi, l.nilai_penjaminan, l.tanggal
              FROM laporan_entitas l
              JOIN notaris n ON l.id_notaris=n.id_notaris
              WHERE DATE(l.tanggal) BETWEEN :tgl_a AND :tgl_b $filterSql
              ORDER BY $order_column $order_dir
              LIMIT :start, :length";
$stmt = $koneksi->prepare($dataQuery);
$stmt->bindParam(':tgl_a',$tgl_a);
$stmt->bindParam(':tgl_b',$tgl_b);
$stmt->bindParam(':start',$start,PDO::PARAM_INT);
$stmt->bindParam(':length',$length,PDO::PARAM_INT);
$stmt->execute();

// format data
$data = [];
$no = $start + 1;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $nested = [];
    $nested[] = $no++;
    $nested[] = htmlspecialchars($row['nama']);
    $nested[] = htmlspecialchars($row['pemberi']);
    $nested[] = htmlspecialchars($row['penerima']);
    $nested[] = htmlspecialchars($row['jenis_transaksi']);
    $nested[] = htmlspecialchars($row['nilai_penjaminan']); // string apa adanya
    $nested[] = date('d-m-Y', strtotime($row['tanggal']));
    $data[] = $nested;
}

// output JSON
header('Content-Type: application/json');
echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
exit;
