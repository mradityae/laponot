<?php
    require '../config/koneksi.php';

    $id_kedudukan = (int)($_GET['id_kedudukan'] ?? 0);

    $draw        = (int)($_POST['draw'] ?? 1);
    $start       = (int)($_POST['start'] ?? 0);
    $length      = (int)($_POST['length'] ?? 10);
    $searchValue = $_POST['search']['value'] ?? '';
    $orderColIdx = (int)($_POST['order'][0]['column'] ?? 7);
    $orderDir    = strtolower($_POST['order'][0]['dir'] ?? 'desc');
    $orderDir    = $orderDir === 'asc' ? 'asc' : 'desc';

    $columns = [
        1 => 'n.nama',
        2 => 'le.nomor',
        3 => 'le.tanggal',
        4 => 'le.pemberi',
        5 => 'le.penerima',
        // 6 => 'le.no_sertifikat',
        6 => 'le.jenis_transaksi',
        7 => 'le.created_at'
    ];
    $orderBy = $columns[$orderColIdx] ?? 'le.created_at';

    $where = " WHERE k.id_kedudukan = :id_kedudukan AND n.level='2' AND n.aktif='1' ";
    $params = [":id_kedudukan" => $id_kedudukan];

    if ($searchValue !== '') {
        $where .= " AND (n.nama LIKE :q OR le.nomor LIKE :q OR le.pemberi LIKE :q OR le.penerima LIKE :q OR le.no_sertifikat LIKE :q OR le.jenis_transaksi LIKE :q)";
        $params[":q"] = "%{$searchValue}%";
    }

    $stmt = $koneksi->prepare("
        SELECT COUNT(*) total
        FROM laporan_entitas le
        JOIN notaris n ON le.id_notaris = n.id_notaris
        JOIN kedudukan k ON n.id_kedudukan = k.id_kedudukan
        WHERE k.id_kedudukan = :id_kedudukan
    ");
    $stmt->execute([":id_kedudukan" => $id_kedudukan]);
    $recordsTotal = (int)$stmt->fetchColumn();

    $stmt = $koneksi->prepare("
        SELECT COUNT(*) total
        FROM laporan_entitas le
        JOIN notaris n ON le.id_notaris = n.id_notaris
        JOIN kedudukan k ON n.id_kedudukan = k.id_kedudukan
        $where
    ");
    $stmt->execute($params);
    $recordsFiltered = (int)$stmt->fetchColumn();

    $sql = "
        SELECT UPPER(n.nama) nama, le.nomor, le.tanggal, le.pemberi, le.penerima, le.no_sertifikat, le.jenis_transaksi, le.created_at
        FROM laporan_entitas le
        JOIN notaris n ON le.id_notaris = n.id_notaris
        JOIN kedudukan k ON n.id_kedudukan = k.id_kedudukan
        $where
        ORDER BY $orderBy $orderDir
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $koneksi->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $k === ':id_kedudukan' ? (int)$v : $v, $k === ':id_kedudukan' ? PDO::PARAM_INT : PDO::PARAM_STR);
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
            htmlspecialchars($r['nomor']),
            htmlspecialchars($r['tanggal']),
            htmlspecialchars($r['pemberi']),
            htmlspecialchars($r['penerima']),
            // htmlspecialchars($r['no_sertifikat']),
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
