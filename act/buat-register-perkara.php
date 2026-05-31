<?php
session_save_path('../login/session');
session_start();

if (!isset($_SESSION['kode_user'])) {
    header("Location: ../login.php");
    exit();
}

include "../config/koneksi.php";

$id_perkara = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_perkara <= 0) {
    die("ID perkara tidak valid");
}

function singkatKedudukan($nama_kedudukan) {
    $nama = strtoupper(trim($nama_kedudukan));

    if (strpos($nama, 'KABUPATEN') !== false) {
        $prefix = "MPDN-KAB.";
        $sisa = trim(str_replace('KABUPATEN', '', $nama));
    } elseif (strpos($nama, 'KOTA') !== false) {
        $prefix = "MPDN-KOTA.";
        $sisa = trim(str_replace('KOTA', '', $nama));
    } else {
        $prefix = "MPDN-";
        $sisa = $nama;
    }

    $daftar_singkatan = [
        'BANDUNG' => 'BDG',
        'BANDUNG BARAT' => 'BB',
        'BEKASI' => 'BKS',
        'BOGOR' => 'BGR',
        'CIAMIS' => 'CMS',
        'CIANJUR' => 'CJR',
        'CIREBON' => 'CRB',
        'GARUT' => 'GRT',
        'INDRAMAYU' => 'IMY',
        'KARAWANG' => 'KRW',
        'KUNINGAN' => 'KNG',
        'MAJALENGKA' => 'MJL',
        'PANGANDARAN' => 'PND',
        'PURWAKARTA' => 'PWK',
        'SUBANG' => 'SBG',
        'SUKABUMI' => 'SKB',
        'SUMEDANG' => 'SMD',
        'TASIKMALAYA' => 'TSM',
        'BANJAR' => 'BJR',
        'CIMAHI' => 'CMH',
        'DEPOK' => 'DPK'
    ];

    $singkatan = isset($daftar_singkatan[$sisa]) ? $daftar_singkatan[$sisa] : $sisa;

    return $prefix . $singkatan;
}

$query = $koneksi->prepare("
    SELECT *
    FROM perkara_mpw
    WHERE id_perkara = ?
    LIMIT 1
");
$query->execute([$id_perkara]);

$perkara = $query->fetch(PDO::FETCH_ASSOC);

if (!$perkara) {
    die("Data perkara tidak ditemukan");
}

if (!empty($perkara['nomor_register'])) {
    echo "<script>
            alert('Perkara ini sudah memiliki nomor register!');
            window.location.href='../admin/perkara_index.php';
          </script>";
    exit();
}

$tahunSekarang = date('Y');
$bulanSekarang = date('n');

$array_romawi = [
    1 => 'I',
    2 => 'II',
    3 => 'III',
    4 => 'IV',
    5 => 'V',
    6 => 'VI',
    7 => 'VII',
    8 => 'VIII',
    9 => 'IX',
    10 => 'X',
    11 => 'XI',
    12 => 'XII'
];

$bulanRomawi = $array_romawi[$bulanSekarang];

$stmtKedudukan = $koneksi->prepare("
    SELECT nama_kedudukan
    FROM kedudukan
    WHERE id_kedudukan = ?
    LIMIT 1
");
$stmtKedudukan->execute([$perkara['id_kedudukan']]);

$dataKedudukan = $stmtKedudukan->fetch(PDO::FETCH_ASSOC);

$namaKedudukanAsli = $dataKedudukan['nama_kedudukan'];

$identitasMpd = singkatKedudukan($namaKedudukanAsli);

$stmtUrut = $koneksi->prepare("
    SELECT COUNT(*) as total
    FROM perkara_mpw
    WHERE id_kedudukan = ?
    AND nomor_register IS NOT NULL
    AND YEAR(created_at) = ?
");

$stmtUrut->execute([
    $perkara['id_kedudukan'],
    $tahunSekarang
]);

$rowUrut = $stmtUrut->fetch(PDO::FETCH_ASSOC);

$nomorUrutBaru = $rowUrut['total'] + 1;

$nomorUrutFmt = str_pad($nomorUrutBaru, 2, "0", STR_PAD_LEFT);

$nomorRegister = $nomorUrutFmt .
    "/REG-PENG/" .
    $identitasMpd .
    "/" .
    $bulanRomawi .
    "/" .
    $tahunSekarang;

$update = $koneksi->prepare("
    UPDATE perkara_mpw
    SET
        nomor_register = ?,
        status = 'Register Perkara'
    WHERE id_perkara = ?
");

$update->execute([
    $nomorRegister,
    $id_perkara
]);

echo "<script>
        alert('Nomor Register Berhasil Dibuat!\\n\\nNomor Register: ".$nomorRegister."\\n\\nSetelah mendapatkan nomor register perkara, perkara wajib diperiksa selama 30 hari kerja.');
        window.location.href='../admin/perkara_index.php';
      </script>";
?>