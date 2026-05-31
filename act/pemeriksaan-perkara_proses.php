<?php
session_save_path('../login/session');
session_start();

if (!isset($_SESSION['kode_user']) || !isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$id_mpd = $_SESSION['kode_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "../config/koneksi.php";

    $id_perkara = isset($_POST['id_perkara']) ? (int)$_POST['id_perkara'] : 0;
    $db_data_tambahan = isset($_POST['data_dukung_tambahan']) ? trim($_POST['data_dukung_tambahan']) : "";

    if ($id_perkara <= 0) {
        echo "ID Perkara Tidak Valid.";
        exit();
    }

    try {
        $koneksi->beginTransaction();
        $targetDir = "upload/" . $id_mpd . "/" . $id_perkara . "/";

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Mengambil berkas PDF yang ada saat ini di database
        $stmtCek = $koneksi->prepare("SELECT ba_pemeriksaan, laporan_hasil_pemeriksaan, rekomendasi, surat_pemanggilan FROM perkara_mpw WHERE id_perkara = ? LIMIT 1");
        $stmtCek->execute([$id_perkara]);
        $currentFiles = $stmtCek->fetch(PDO::FETCH_ASSOC);

        $db_ba          = $currentFiles['ba_pemeriksaan'] ?? "";
        $db_lhp         = $currentFiles['laporan_hasil_pemeriksaan'] ?? "";
        $db_rekomendasi = $currentFiles['rekomendasi'] ?? "";
        $db_pemanggilan = $currentFiles['surat_pemanggilan'] ?? "";

        // Daftar file PDF yang diproses upload (Proses upload PDF data_dukung_tambahan sudah ditiadakan)
        $filesToUpload = [
            'ba_pemeriksaan'            => 'BA_PEMERIKSAAN',
            'laporan_hasil_pemeriksaan' => 'LHP',
            'surat_pemanggilan'         => 'SURAT_PEMANGGILAN',
            'rekomendasi'               => 'REKOMENDASI'
        ];

        foreach ($filesToUpload as $inputName => $prefixName) {
            if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES[$inputName]['tmp_name'];
                $fileExtension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));

                if ($fileExtension === 'pdf') {
                    $newFileName = $prefixName . "_" . $id_perkara . "_" . time() . "_" . rand(100, 999) . ".pdf";
                    $targetFilePath = $targetDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                        $savedPathInDb = "act/" . $targetFilePath;

                        if ($inputName == 'ba_pemeriksaan') $db_ba = $savedPathInDb;
                        if ($inputName == 'laporan_hasil_pemeriksaan') $db_lhp = $savedPathInDb;
                        if ($inputName == 'surat_pemanggilan') $db_pemanggilan = $savedPathInDb;
                        if ($inputName == 'rekomendasi') $db_rekomendasi = $savedPathInDb;
                    }
                }
            }
        }

        // Simpan semua ke kolom database yang valid (menggunakan data_dukung_tambahan)
        $queryUpdatePemeriksaan = $koneksi->prepare("
            UPDATE perkara_mpw SET
                ba_pemeriksaan = ?,
                laporan_hasil_pemeriksaan = ?,
                surat_pemanggilan = ?,
                rekomendasi = ?,
                data_dukung_tambahan = ?,
                status = 'Perkara Pemeriksaan'
            WHERE id_perkara = ?
        ");
        
        $queryUpdatePemeriksaan->execute([
            $db_ba, 
            $db_lhp, 
            $db_pemanggilan, 
            $db_rekomendasi, 
            $db_data_tambahan, 
            $id_perkara
        ]);

        $koneksi->commit();
        echo "<script>alert('Berkas Tahap Pemeriksaan Perkara Berhasil Diperbarui!'); window.location.href = '/laponot/admin/perkara_index.php';</script>";
        exit();

    } catch (Exception $e) {
        $koneksi->rollBack();
        echo "Gagal Memperbarui Dokumen Pemeriksaan: " . $e->getMessage();
    }
}
?>