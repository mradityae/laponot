<?php
// Cek apakah path config sudah benar (asumsi folder act sejajar dengan folder pengguna)
// Jika folder act ada di dalam folder lain, sesuaikan jumlah ../ nya
include("../config/koneksi.php");

if (isset($_POST['update_nik'])) {
    $id_notaris = $_POST['id_notaris'];
    $nik = trim($_POST['nik']);

    // Validasi: Harus angka dan tepat 16 digit
    if (strlen($nik) == 16 && is_numeric($nik)) {
        try {
            $stmt = $koneksi->prepare("UPDATE notaris SET nik = :nik WHERE id_notaris = :id");
            $stmt->bindParam(':nik', $nik);
            $stmt->bindParam(':id', $id_notaris);
            $stmt->execute();
            
            // Redirect kembali ke halaman dashboard di folder pengguna
            echo "<script>
                    alert('NIK Berhasil disimpan!'); 
                    window.location.href = '../pengguna/profil';
                  </script>";
        } catch (PDOException $e) {
            echo "<script>
                    alert('Gagal update database!'); 
                    window.history.back();
                  </script>";
        }
    } else {
        echo "<script>
                alert('NIK harus berupa 16 digit angka!'); 
                window.history.back();
              </script>";
    }
} else {
    // Jika diakses ilegal tanpa POST
    header("Location: ../pengguna/index.php");
    exit();
}
?>