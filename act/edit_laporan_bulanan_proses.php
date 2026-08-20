<?php
session_save_path('../login/session');
session_start();

if (
    !isset($_POST['submit']) ||
    !isset($_SESSION['email']) ||
    $_SESSION['user_role'] != 2
) {
    header("Location: https://kabayanpasti.kemenkum.go.id");
    exit;
}

require_once '../config/koneksi.php';
require_once '../models/models.php';

function redirectWithAlert($msg, $url)
{
    echo "<script>
        alert(" . json_encode($msg) . ");
        window.location.href = " . json_encode($url) . ";
    </script>";
    exit;
}

try {

    // =======================
    // AMBIL DATA POST
    // =======================
    $id_laporan            = $_POST['id_laporan'];
    $id_notaris            = $_POST['id_notaris'];
    $tanggal_input         = $_POST['tanggal_laporan'];
    $tanggal               = $tanggal_input . "-10";

    $jml_buku_daftar       = $_POST['jml_buku_daftar'];
    $jml_tangan_dibukukan  = $_POST['jml_tangan_dibukukan'];
    $jml_tangan_disahkan   = $_POST['jml_tangan_disahkan'];
    $jml_buku_protes       = $_POST['jml_buku_protes'];

    $jml_akta_fidusia      = $_POST['jml_akta_fidusia'];
    $jml_akta_badan_usaha  = $_POST['jml_akta_badan_usaha'];
    $jml_akta_wasiat       = $_POST['jml_akta_wasiat'];

    $redirectUrl           = $url . "pengguna/daftar_laporan";

    // =======================
    // AMBIL DATA LAMA
    // =======================
    $stmtOld = $koneksi->prepare("
        SELECT tanggal, file_upload
        FROM laporan
        WHERE id_laporan = ?
          AND id_notaris = ?
    ");

    $stmtOld->execute([$id_laporan, $id_notaris]);

    if ($stmtOld->rowCount() == 0) {
        redirectWithAlert('Data laporan tidak ditemukan', $redirectUrl);
    }

    $dataOld       = $stmtOld->fetch(PDO::FETCH_ASSOC);
    $tanggal_lama  = $dataOld['tanggal'];
    $file_lama_db  = $dataOld['file_upload'];

    $file_upload_final = $file_lama_db;

    // =======================
    // VALIDASI DUPLIKASI BULAN
    // =======================
    if (!cekUpdated($koneksi, $id_notaris, $tanggal, $id_laporan)) {
        redirectWithAlert(
            'Error Edit: Anda telah mengirim file pada bulan yang bersangkutan',
            $redirectUrl
        );
    }

    // =======================
    // FOLDER UPLOAD
    // =======================
    $directory = "upload/$id_notaris";

    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $monthYearBaru = date('F_Y', strtotime($tanggal));
    $newRelativePath = "$directory/Laporan-$monthYearBaru.pdf";
    $newUrlPath = $url . 'act/' . $newRelativePath;

    // =======================
    // UPLOAD FILE BARU
    // =======================
    if (
        isset($_FILES['file']) &&
        $_FILES['file']['error'] == 0
    ) {

        $file = $_FILES['file'];

        $ekstensi = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        if ($file['size'] > 5242880) {
            redirectWithAlert(
                'Ukuran file maksimal 5 MB',
                $redirectUrl
            );
        }

        if ($ekstensi !== 'pdf') {
            redirectWithAlert(
                'File harus PDF',
                $redirectUrl
            );
        }

        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $newRelativePath
            )
        ) {
            redirectWithAlert(
                'Gagal upload file',
                $redirectUrl
            );
        }

        $file_upload_final = $newUrlPath;
    }

    // =======================
    // RENAME FILE LAMA
    // JIKA BULAN BERUBAH &
    // TIDAK ADA FILE BARU
    // =======================
    else {

        $bulanLama = date('Y-m', strtotime($tanggal_lama));
        $bulanBaru = date('Y-m', strtotime($tanggal));

        if ($bulanLama != $bulanBaru && !empty($file_lama_db)) {

            $oldRelativePath = str_replace(
                $url . 'act/',
                '',
                $file_lama_db
            );

            $oldFullPath = __DIR__ . '/' . $oldRelativePath;
            $newFullPath = __DIR__ . '/' . $newRelativePath;

            if (file_exists($oldFullPath)) {

                if (
                    $oldFullPath != $newFullPath &&
                    file_exists($newFullPath)
                ) {
                    unlink($newFullPath);
                }

                if (
                    $oldFullPath != $newFullPath &&
                    rename($oldFullPath, $newFullPath)
                ) {
                    $file_upload_final = $newUrlPath;
                }
            }
        }
    }

    // =======================
    // UPDATE DATABASE
    // =======================
    $updated = editLaporanBulanan(
        $koneksi,
        $id_laporan,
        $tanggal,
        $jml_buku_daftar,
        $jml_tangan_dibukukan,
        $jml_tangan_disahkan,
        $jml_buku_protes,
        $jml_akta_fidusia,
        $jml_akta_badan_usaha,
        $jml_akta_wasiat,
        $file_upload_final
    );

    if ($updated) {
        redirectWithAlert(
            'Laporan berhasil diperbarui',
            $redirectUrl
        );
    }

    redirectWithAlert(
        'Gagal memperbarui laporan',
        $redirectUrl
    );

} catch (Throwable $e) {

    echo "System Error : " . $e->getMessage();

}