<?php
session_save_path('../login/session');
session_start();

if (!isset($_SESSION['kode_user'])) {
    header("Location: ../login.php");
    exit();
}

include "../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_perkara = (int)($_POST['id_perkara'] ?? 0);

    $verifikasi = trim($_POST['verifikasi'] ?? '');
    $catatan = trim($_POST['catatan_verifikasi'] ?? '');

    if($id_perkara <= 0){
        exit("ID perkara tidak valid");
    }

    if(
        $verifikasi != 'Terverifikasi' &&
        $verifikasi != 'Tidak Terverifikasi'
    ){
        exit("Status verifikasi tidak valid");
    }

    try {

        $query = $koneksi->prepare("
            UPDATE perkara_mpw
            SET
                verifikasi = ?,
                catatan_verifikasi = ?
            WHERE id_perkara = ?
        ");

        $query->execute([
            $verifikasi,
            $catatan,
            $id_perkara
        ]);

        echo "
        <script>
            alert('Verifikasi perkara berhasil disimpan');
        </script>
        ";
        $link = $url."superadmin/perkara_index.php";
        header("refresh:0.1; url=$link");

    } catch(Exception $e){

        echo "Error: " . $e->getMessage();

    }

} else {

    echo "Invalid Request";

}
?>