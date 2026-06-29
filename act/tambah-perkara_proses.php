<?php
// 1. Inisialisasi Session sesuai standar
session_save_path('../login/session');
session_start();

// 2. Pastikan user sudah login
if (!isset($_SESSION['kode_user']) || !isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$id_mpd = $_SESSION['kode_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    include "../config/koneksi.php";
    
    if (file_exists('../models/models.php')) {
        include '../models/models.php';
    }

    if (!function_exists('RemoveSpecialChar')) {
        function RemoveSpecialChar($data) {
            return htmlspecialchars(strip_tags(trim($data)));
        }
    }

    // Helper singkatan wilayah kerja
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
            'BANDUNG'         => 'BDG', 'BANDUNG BARAT'   => 'BB', 'BEKASI'          => 'BKS', 'BOGOR'           => 'BGR',
            'CIAMIS'          => 'CMS', 'CIANJUR'         => 'CJR', 'CIREBON'         => 'CRB', 'GARUT'           => 'GRT',
            'INDRAMAYU'       => 'IMY', 'KARAWANG'        => 'KRW', 'KUNINGAN'        => 'KNG', 'MAJALENGKA'      => 'MJL',
            'PANGANDARAN'     => 'PND', 'PURWAKARTA'      => 'PWK', 'SUBANG'          => 'SBG', 'SUKABUMI'        => 'SKB',
            'SUMEDANG'        => 'SMD', 'TASIKMALAYA'     => 'TSM', 'BANJAR'          => 'BJR', 'CIMAHI'          => 'CMH', 'DEPOK'           => 'DPK'
        ];

        $singkatan = isset($daftar_singkatan[$sisa]) ? $daftar_singkatan[$sisa] : $sisa;
        return $prefix . $singkatan;
    }

    $id_user_login = $_SESSION['kode_user'];

    // Sanitasi input data string text (Termasuk kolom judul baru)
    $judulPerkara        = RemoveSpecialChar($_POST['judul'] ?? '');
    $uraian_pengaduan    = RemoveSpecialChar($_POST['uraian_pengaduan'] ?? '');
    $jenisTerlapor       = RemoveSpecialChar($_POST['jenis_terlapor'] ?? '');
    $namaPelapor         = RemoveSpecialChar($_POST['nama_pelapor'] ?? '');
    $noHpPelapor         = RemoveSpecialChar($_POST['no_hp_pelapor'] ?? '');
    $noHpTerlapor        = RemoveSpecialChar($_POST['no_hp_terlapor'] ?? '');
    $alamatPelapor       = RemoveSpecialChar($_POST['alamat_pelapor'] ?? '');
    $alamatTerlapor      = RemoveSpecialChar($_POST['alamat_terlapor'] ?? '');
    $dataDukungLink      = RemoveSpecialChar($_POST['data_dukung_link'] ?? '');

    $idNotaris           = null;
    $namaTerlaporManual  = null;
    $idKedudukan         = null;

    if ($jenisTerlapor == 'database') {
        $idNotaris = RemoveSpecialChar($_POST['id_notaris'] ?? '');
        $queryCariKedudukan = $koneksi->prepare("SELECT id_kedudukan FROM notaris WHERE id_notaris = ? LIMIT 1");
        $queryCariKedudukan->execute([$idNotaris]);
        $resNotaris = $queryCariKedudukan->fetch(PDO::FETCH_ASSOC);
        $idKedudukan = $resNotaris ? $resNotaris['id_kedudukan'] : 0;
    } else {
        $namaTerlaporManual = RemoveSpecialChar($_POST['nama_terlapor_manual'] ?? '');
        $idKedudukan        = RemoveSpecialChar($_POST['id_kedudukan'] ?? 0); 
    }

    try {
        $koneksi->beginTransaction();

        $queryInsert = $koneksi->prepare("
            INSERT INTO perkara_mpw (
                nomor_register,
                judul,
                uraian_pengaduan,
                jenis_terlapor,
                id_notaris,
                nama_terlapor_manual,
                id_kedudukan,
                nama_pelapor,
                no_hp_pelapor,
                no_hp_terlapor,
                alamat_pelapor,
                alamat_terlapor,
                data_dukung_link,
                created_by,
                created_at,
                status
            ) VALUES (
                NULL,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?
            )
        ");

        $queryInsert->execute([
            $judulPerkara,
            $uraian_pengaduan,
            $jenisTerlapor,
            $idNotaris,
            $namaTerlaporManual,
            $idKedudukan,
            $namaPelapor,
            $noHpPelapor,
            $noHpTerlapor,
            $alamatPelapor,
            $alamatTerlapor,
            $dataDukungLink,
            $id_user_login,
            'Pending'
        ]);

        $id_perkara = $koneksi->lastInsertId();

        // Alur Struktur Folder Upload: upload/{id_user_mpd}/{id_perkara}/
        $targetDir = "upload/" . $id_mpd . "/" . $id_perkara . "/";

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $db_surat_pengaduan = "";
        $db_sk_majelis      = "";

        // Hanya memproses berkas tersisa untuk Tahap Registrasi
        $filesToUpload = [
            'surat_pengaduan'      => 'SURAT_PENGADUAN',
            'sk_majelis_pemeriksa' => 'SK_MAJELIS'
        ];

        foreach ($filesToUpload as $inputName => $prefixName) {
            if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                
                $fileTmpPath   = $_FILES[$inputName]['tmp_name'];
                $fileName      = $_FILES[$inputName]['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if ($fileExtension === 'pdf') {
                    $newFileName = $prefixName . "_" . $id_perkara . "_" . time() . "_" . rand(100, 999) . ".pdf";
                    $targetFilePath = $targetDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                        $savedPathInDb = "act/" . $targetFilePath;

                        if ($inputName == 'surat_pengaduan') $db_surat_pengaduan = $savedPathInDb;
                        if ($inputName == 'sk_majelis_pemeriksa') $db_sk_majelis = $db_sk_majelis = $savedPathInDb;
                    }
                }
            }
        }

        // Jalankan Update path dokumen ke row perkara
        $queryUpdateFile = $koneksi->prepare("
            UPDATE perkara_mpw SET 
                surat_pengaduan = ?,
                sk_majelis_pemeriksa = ?
            WHERE id_perkara = ?
        ");
        
        $queryUpdateFile->execute([
            $db_surat_pengaduan,
            $db_sk_majelis,
            $id_perkara
        ]);

        $koneksi->commit();

        echo "<script>
            alert('Data Perkara Berhasil Disimpan Dengan Status Pending!');
            window.location.href = '/laponot/admin/perkara_index.php';
        </script>";
        exit();

    } catch (Exception $e) {
        $koneksi->rollBack();
        echo "Gagal Menyimpan Data Registrasi: " . $e->getMessage();
    }
} else {
    echo "WRONG ACCESS";
    exit();
}
?>