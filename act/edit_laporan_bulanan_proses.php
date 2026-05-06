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

function redirectWithAlert($msg, $url) {
    echo "<script>
        alert(".json_encode($msg).");
        window.location.href = ".json_encode($url).";
    </script>";
    exit;
}

try {

    // =======================
    // AMBIL DATA
    // =======================
    $id_laporan           = $_POST['id_laporan'];
    $id_notaris           = $_POST['id_notaris'];
    $tanggal_input          = $_POST['tanggal_laporan'];
    $tanggal                = $tanggal_input . "-10";
    $jml_buku_daftar      = $_POST['jml_buku_daftar'];
    $jml_tangan_dibukukan = $_POST['jml_tangan_dibukukan'];
    $jml_tangan_disahkan  = $_POST['jml_tangan_disahkan'];
    $jml_buku_protes      = $_POST['jml_buku_protes'];
    $file_upload_final    = $_POST['file_lama'];

    $redirectUrl = $url . "pengguna/daftar_laporan";

    // =======================
    // VALIDASI DUPLIKASI BULAN
    // =======================
    // if (!cekUpdated($koneksi, $id_notaris, $tanggal, $id_laporan)) {
    //     redirectWithAlert(
    //         'Error Edit: Anda telah mengirim file pada bulan yang bersangkutan',
    //         $redirectUrl
    //     );
    // }

    // =======================
    // UPLOAD FILE (JIKA ADA)
    // =======================
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

        $file       = $_FILES['file'];
        $ekstensi   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['size'] > 5242880) {
            redirectWithAlert('Ukuran file maksimal 5 MB', $redirectUrl);
        }

        if ($ekstensi !== 'pdf') {
            redirectWithAlert('File harus PDF', $redirectUrl);
        }

        $monthYear = date('F_Y', strtotime($tanggal));
        $directory = "upload/$id_notaris";

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filePath = "$directory/Laporan-$monthYear.pdf";

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            redirectWithAlert('Gagal upload file', $redirectUrl);
        }

        $file_upload_final = $url . 'act/' . $filePath;
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
        $file_upload_final
    );

    if ($updated) {
        redirectWithAlert('Laporan berhasil diperbarui', $redirectUrl);
    }

    redirectWithAlert('Gagal memperbarui laporan', $redirectUrl);

} catch (Throwable $e) {
    echo "System Error: " . $e->getMessage();
}
