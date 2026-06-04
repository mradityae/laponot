<?php
session_save_path('../login/session');
session_start();

if (
    !isset($_POST['submit']) ||
    !isset($_SESSION['email']) ||
    $_SESSION['user_role'] != 2
) {
    echo "WRONG ACCESS";
    header("refresh:0.1; url=https://kabayanpasti.kemenkum.go.id");
    exit;
}

include '../config/koneksi.php';
include '../models/models.php';

function redirectAlert($pesan, $url)
{
    echo "<script>alert('".$pesan."')</script>";
    header("refresh:0.1; url=".$url);
    exit;
}

try {

    $id_notaris             = trim($_POST['id']);
    $tanggal_input          = $_POST['tanggal_laporan'];
    $tanggal                = $tanggal_input . "-10";

    $jml_buku_daftar        = $_POST['jml_buku_daftar'];
    $jml_tangan_dibukukan   = $_POST['jml_tangan_dibukukan'];
    $jml_tangan_disahkan    = $_POST['jml_tangan_disahkan'];
    $jml_buku_protes        = $_POST['jml_buku_protes'];

    if (!isset($_FILES['file'])) {
        redirectAlert(
            "File tidak ditemukan",
            $url . "pengguna/unggah_laporan"
        );
    }

    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        redirectAlert(
            "Upload file gagal. Error Code: ".$_FILES['file']['error'],
            $url . "pengguna/unggah_laporan"
        );
    }

    $monthYear = date("F_Y", strtotime($tanggal));

    $namaFile  = $_FILES['file']['name'];
    $ekstensi  = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ukuran    = $_FILES['file']['size'];
    $fileTemp  = $_FILES['file']['tmp_name'];

    $direktory   = "upload/" . $id_notaris;
    $namaSimpan  = "Laporan-" . $monthYear . "." . $ekstensi;
    $dirSaveFile = $direktory . "/" . $namaSimpan;
    $fullDirBaru = $url . "act/" . $dirSaveFile;

    if (!cekUploaded($koneksi, $id_notaris, $tanggal)) {
        redirectAlert(
            "Anda telah mengirim file pada bulan yang bersangkutan",
            $url . "pengguna/index"
        );
    }

    if ($ukuran > 5242880) {
        redirectAlert(
            "Ukuran File Terlalu Besar. Maksimal 5 MB",
            $url . "pengguna/unggah_laporan"
        );
    }

    if ($ekstensi !== 'pdf') {
        redirectAlert(
            "File tidak valid. Harus PDF",
            $url . "pengguna/unggah_laporan"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Buat folder jika belum ada
    |--------------------------------------------------------------------------
    */

    if (!is_dir($direktory)) {

        if (!mkdir($direktory, 0777, true)) {
            redirectAlert(
                "Gagal membuat folder upload",
                $url . "pengguna/unggah_laporan"
            );
        }

        if (!chmod($direktory, 0777)) {
            redirectAlert(
                "Folder berhasil dibuat tetapi gagal set permission 777",
                $url . "pengguna/unggah_laporan"
            );
        }
    } else {

        @chmod($direktory, 0777);

        if (!is_writable($direktory)) {
            redirectAlert(
                "Folder upload tidak writable",
                $url . "pengguna/unggah_laporan"
            );
        }
    }

    clearstatcache();

    /*
    |--------------------------------------------------------------------------
    | Simpan file
    |--------------------------------------------------------------------------
    */

    if (!is_uploaded_file($fileTemp)) {
        redirectAlert(
            "Temporary upload file tidak valid",
            $url . "pengguna/unggah_laporan"
        );
    }

    if (!move_uploaded_file($fileTemp, $dirSaveFile)) {

        $debug =
            "TMP=".$fileTemp.
            " | DEST=".$dirSaveFile.
            " | ERROR=".$_FILES['file']['error'];

        error_log($debug);

        redirectAlert(
            "Gagal menyimpan file ke server",
            $url . "pengguna/unggah_laporan"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan database
    |--------------------------------------------------------------------------
    */

    $simpan = unggahLaporan(
        $koneksi,
        $id_notaris,
        $tanggal,
        $jml_buku_daftar,
        $jml_tangan_dibukukan,
        $jml_tangan_disahkan,
        $jml_buku_protes,
        $fullDirBaru
    );

    if (!$simpan) {

        if (file_exists($dirSaveFile)) {
            unlink($dirSaveFile);
        }

        redirectAlert(
            "Gagal menyimpan data laporan",
            $url . "pengguna/unggah_laporan"
        );
    }

    redirectAlert(
        "Laporan Berhasil Dikirim",
        $url . "pengguna/index"
    );

} catch (Exception $e) {

    error_log($e->getMessage());

    echo "Something Wrong : " . $e->getMessage();
}