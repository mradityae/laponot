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

// ==============================
// AMBIL DATA FILE
// ==============================
$query = $koneksi->prepare("
    SELECT *
    FROM perkara_mpw
    WHERE id_perkara = ?
    LIMIT 1
");

$query->execute([$id_perkara]);

$data = $query->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data perkara tidak ditemukan");
}

// ==============================
// HAPUS FILE FISIK
// ==============================
$daftar_file = [
    'surat_pengaduan',
    'sk_majelis_pemeriksa',
    'surat_pemanggilan',
    'ba_pemeriksaan',
    'laporan_hasil_pemeriksaan',
    'rekomendasi',
    'data_dukung_tambahan'
];

foreach ($daftar_file as $file) {

    if (!empty($data[$file])) {

        $path_file = "../" . ltrim($data[$file], '/');

        if (file_exists($path_file)) {
            unlink($path_file);
        }
    }
}

// ==============================
// HAPUS DATA DATABASE
// ==============================
$hapus = $koneksi->prepare("
    DELETE FROM perkara_mpw
    WHERE id_perkara = ?
");

$hapus->execute([$id_perkara]);

echo "
<script>
    alert('Data perkara berhasil dihapus!');
    window.location.href='../superadmin/perkara_index.php';
</script>
";
?>