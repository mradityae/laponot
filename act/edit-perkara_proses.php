<?php
session_save_path('../login/session');
session_start();

if (!isset($_SESSION['kode_user']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../login.php");
    exit();
}

include "../config/koneksi.php";

function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

$id_perkara         = (int)($_POST['id_perkara'] ?? 0);
$judulPerkara       = sanitize($_POST['judul'] ?? '');
$jenisTerlapor      = sanitize($_POST['jenis_terlapor'] ?? '');
$namaPelapor        = sanitize($_POST['nama_pelapor'] ?? '');
$noHpPelapor        = sanitize($_POST['no_hp_pelapor'] ?? '');
$noHpTerlapor       = sanitize($_POST['no_hp_terlapor'] ?? '');
$alamatPelapor      = sanitize($_POST['alamat_pelapor'] ?? '');
$alamatTerlapor     = sanitize($_POST['alamat_terlapor'] ?? '');
$dataDukungLink     = sanitize($_POST['data_dukung_link'] ?? '');

$idNotaris          = null;
$namaTerlaporManual = null;

if ($jenisTerlapor == 'database') {
    $idNotaris = !empty($_POST['id_notaris'])
        ? (int)$_POST['id_notaris']
        : null;
} else {
    $namaTerlaporManual = sanitize($_POST['nama_terlapor_manual'] ?? '');
}

try {

    $koneksi->beginTransaction();

    // =========================================================
    // AMBIL DATA LAMA
    // =========================================================

    $stmtOld = $koneksi->prepare("
        SELECT 
            created_by,
            surat_pengaduan,
            sk_majelis_pemeriksa,
            ba_pemeriksaan,
            laporan_hasil_pemeriksaan,
            rekomendasi,
            data_dukung_tambahan,
            surat_pemanggilan
        FROM perkara_mpw
        WHERE id_perkara = ?
        LIMIT 1
    ");

    $stmtOld->execute([$id_perkara]);

    $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

    if (!$oldData) {
        throw new Exception("Data perkara tidak ditemukan.");
    }

    // =========================================================
    // PATH FOLDER FISIK SERVER
    // =========================================================

    $targetDir = dirname(__DIR__) . "/act/upload/" .
        $oldData['created_by'] . "/" .
        $id_perkara . "/";

    // Buat folder jika belum ada
    if (!is_dir($targetDir)) {

        if (!mkdir($targetDir, 0777, true)) {
            throw new Exception("Gagal membuat folder upload.");
        }
    }

    // Cek writable
    if (!is_writable($targetDir)) {
        throw new Exception("Folder upload tidak writable: " . $targetDir);
    }

    // =========================================================
    // LIST FILE
    // =========================================================

    $filesToUpload = [
        'surat_pengaduan'           => 'SURAT_PENGADUAN',
        'sk_majelis_pemeriksa'      => 'SK_MAJELIS',
        'ba_pemeriksaan'            => 'BA_PEMERIKSAAN',
        'laporan_hasil_pemeriksaan' => 'LHP_MPD',
        'rekomendasi'               => 'REKOMENDASI',
        'data_dukung_tambahan'      => 'DUKUNG_TAMBAHAN',
        'surat_pemanggilan'         => 'SURAT_PANGGILAN'
    ];

    $uploadedPaths = [];

    // =========================================================
    // LOOP UPLOAD FILE
    // =========================================================

    foreach ($filesToUpload as $inputName => $prefixName) {

        // default pakai file lama
        $uploadedPaths[$inputName] = $oldData[$inputName];

        // skip jika tidak upload file
        if (
            !isset($_FILES[$inputName]) ||
            $_FILES[$inputName]['error'] == UPLOAD_ERR_NO_FILE
        ) {
            continue;
        }

        // cek upload error
        if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {

            throw new Exception(
                "Upload gagal pada file: " .
                $inputName .
                " | Error Code: " .
                $_FILES[$inputName]['error']
            );
        }

        $tmpFile   = $_FILES[$inputName]['tmp_name'];
        $fileName  = $_FILES[$inputName]['name'];
        $fileSize  = $_FILES[$inputName]['size'];

        // validasi extension
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($extension !== 'pdf') {
            throw new Exception("File {$inputName} harus PDF.");
        }

        // validasi ukuran 10MB
        if ($fileSize > (10 * 1024 * 1024)) {
            throw new Exception("Ukuran file {$inputName} maksimal 10MB.");
        }

        // validasi mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpFile);
        finfo_close($finfo);

        $allowedMime = [
            'application/pdf',
            'application/x-pdf'
        ];

        if (!in_array($mimeType, $allowedMime)) {
            throw new Exception(
                "Format MIME {$inputName} tidak valid: {$mimeType}"
            );
        }

        // nama file baru
        $newFileName =
            $prefixName . "_" .
            $id_perkara . "_" .
            time() . "_" .
            rand(100, 999) .
            ".pdf";

        // lokasi tujuan fisik server
        $targetFilePath = $targetDir . $newFileName;

        // =========================================================
        // MOVE FILE
        // =========================================================

        if (!move_uploaded_file($tmpFile, $targetFilePath)) {

            $error = error_get_last();

            throw new Exception(
                "move_uploaded_file gagal pada {$inputName}. " .
                ($error['message'] ?? 'Unknown Error')
            );
        }

        // =========================================================
        // PATH UNTUK DATABASE
        // =========================================================

        $savedPathInDb =
            "act/upload/" .
            $oldData['created_by'] . "/" .
            $id_perkara . "/" .
            $newFileName;

        $uploadedPaths[$inputName] = $savedPathInDb;

        // =========================================================
        // HAPUS FILE LAMA
        // =========================================================

        if (!empty($oldData[$inputName])) {

            $oldFilePath =
                dirname(__DIR__) . "/" .
                str_replace($url, '', $oldData[$inputName]);

            if (
                file_exists($oldFilePath) &&
                is_file($oldFilePath)
            ) {
                unlink($oldFilePath);
            }
        }
    }

    // =========================================================
    // UPDATE DATABASE
    // =========================================================

    $queryUpdate = $koneksi->prepare("
        UPDATE perkara_mpw SET 
            judul = ?,
            jenis_terlapor = ?,
            id_notaris = ?,
            nama_terlapor_manual = ?,
            nama_pelapor = ?,
            no_hp_pelapor = ?,
            no_hp_terlapor = ?,
            alamat_pelapor = ?,
            alamat_terlapor = ?,
            data_dukung_link = ?,
            surat_pengaduan = ?,
            sk_majelis_pemeriksa = ?,
            ba_pemeriksaan = ?,
            laporan_hasil_pemeriksaan = ?,
            rekomendasi = ?,
            data_dukung_tambahan = ?,
            surat_pemanggilan = ?
        WHERE id_perkara = ?
    ");

    $queryUpdate->execute([
        $judulPerkara,
        $jenisTerlapor,
        $idNotaris,
        $namaTerlaporManual,
        $namaPelapor,
        $noHpPelapor,
        $noHpTerlapor,
        $alamatPelapor,
        $alamatTerlapor,
        $dataDukungLink,

        $uploadedPaths['surat_pengaduan'],
        $uploadedPaths['sk_majelis_pemeriksa'],
        $uploadedPaths['ba_pemeriksaan'],
        $uploadedPaths['laporan_hasil_pemeriksaan'],
        $uploadedPaths['rekomendasi'],
        $uploadedPaths['data_dukung_tambahan'],
        $uploadedPaths['surat_pemanggilan'],

        $id_perkara
    ]);

    $koneksi->commit();

    echo "
    <script>
        alert('Perubahan Data Perkara Berhasil Disimpan!');
        window.location.href='/laponot/admin/perkara_index.php';
    </script>
    ";

    exit();

} catch (Exception $e) {

    if ($koneksi->inTransaction()) {
        $koneksi->rollBack();
    }

    echo "
    <script>
        alert('Gagal Memperbarui Data:\\n" .
        addslashes($e->getMessage()) .
        "');
        window.history.back();
    </script>
    ";
}
?>