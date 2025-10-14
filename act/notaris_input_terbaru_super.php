<?php
require '../config/koneksi.php';

$draw        = (int)($_POST['draw'] ?? 1);
$start       = (int)($_POST['start'] ?? 0);
$length      = (int)($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$orderColIdx = (int)($_POST['order'][0]['column'] ?? 9);
$orderDir    = strtolower($_POST['order'][0]['dir'] ?? 'desc');
$orderDir    = $orderDir === 'asc' ? 'asc' : 'desc';

$columns = [
    1 => 'n.nama',
    2 => 'kd.nama_kedudukan',
    3 => 'le.nomor',
    4 => 'le.tanggal',
    5 => 'le.pemberi',
    6 => 'le.penerima',
    7 => 'le.no_sertifikat',
    8 => 'le.jenis_transaksi',
    9 => 'le.created_at'
];
$orderBy = $columns[$orderColIdx] ?? 'le.created_at';

// Filter dasar (semua notaris aktif)
$where = " WHERE n.level='2' AND n.aktif='1' ";
$params = [];

if ($searchValue !== '') {
    $where .= " AND (
        n.nama LIKE :q 
        OR kd.nama_kedudukan LIKE :q
        OR le.nomor LIKE :q 
        OR le.pemberi LIKE :q 
        OR le.penerima LIKE :q 
        OR le.no_sertifikat LIKE :q 
        OR le.jenis_transaksi LIKE :q
    )";
    $params[":q"] = "%{$searchValue}%";
}

// Hitung total semua data
$stmt = $koneksi->prepare("
    SELECT COUNT(*) total
    FROM laporan_entitas le
    JOIN notaris n ON le.id_notaris = n.id_notaris
    JOIN kedudukan kd ON n.id_kedudukan = kd.id_kedudukan
");
$stmt->execute();
$recordsTotal = (int)$stmt->fetchColumn();

// Hitung total setelah filter
$stmt = $koneksi->prepare("
    SELECT COUNT(*) total
    FROM laporan_entitas le
    JOIN notaris n ON le.id_notaris = n.id_notaris
    JOIN kedudukan kd ON n.id_kedudukan = kd.id_kedudukan
    $where
");
$stmt->execute($params);
$recordsFiltered = (int)$stmt->fetchColumn();

// Ambil data utama
$sql = "
    SELECT 
        UPPER(n.nama) nama,
        kd.nama_kedudukan, 
        le.nomor, 
        le.tanggal, 
        le.pemberi, 
        le.penerima, 
        le.no_sertifikat, 
        le.jenis_transaksi, 
        le.created_at
    FROM laporan_entitas le
    JOIN notaris n ON le.id_notaris = n.id_notaris
    JOIN kedudukan kd ON n.id_kedudukan = kd.id_kedudukan
    $where
    ORDER BY $orderBy $orderDir
    LIMIT :limit OFFSET :offset
";
$stmt = $koneksi->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $length, PDO::PARAM_INT);
$stmt->bindValue(':offset', $start, PDO::PARAM_INT);
$stmt->execute();

$data = [];
$no = $start + 1;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $data[] = [
        $no++,
        htmlspecialchars($r['nama']),
        htmlspecialchars($r['nama_kedudukan']),
        htmlspecialchars($r['nomor']),
        htmlspecialchars($r['tanggal']),
        htmlspecialchars($r['pemberi']),
        htmlspecialchars($r['penerima']),
        htmlspecialchars($r['no_sertifikat']),
        htmlspecialchars($r['jenis_transaksi']),
        htmlspecialchars($r['created_at'])
    ];
}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
], JSON_UNESCAPED_UNICODE);
