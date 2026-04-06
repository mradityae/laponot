<?php
    include "header.php";
    include("../config/koneksi.php");

    $idUser = $_SESSION['kode_user'];
    $idLaporan = $_GET['idLaporan'] ?? null;

    if (!$idLaporan) {
        echo "<script>alert('ID laporan tidak valid');window.location='daftar_laporan.php';</script>";
        exit;
    }

    $ambil = $koneksi->prepare("
        SELECT * FROM laporan 
        WHERE id_laporan = :id_laporan 
        AND id_notaris = :id_notaris
    ");

    $ambil->bindParam(":id_laporan", $idLaporan);
    $ambil->bindParam(":id_notaris", $idUser);
    $ambil->execute();

    if ($ambil->rowCount() == 0) {
        echo "<script>alert('Data tidak ditemukan');window.location='daftar_laporan.php';</script>";
        exit;
    }

    $d = $ambil->fetch(PDO::FETCH_ASSOC);
    $displayDate = date('Y-m-d', strtotime($d['tanggal']));
    ?>

    <div id="page-wrapper">
        <div id="page-inner">
            <h1 class="page-head-line">Edit Laporan Bulanan</h1>

            <a href="daftar_laporan.php" class="btn btn-primary">KEMBALI</a>
            <br><br>

            <form action="<?=$url;?>act/edit_laporan_bulanan_proses.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_laporan" value="<?= $d['id_laporan']; ?>">
                <input type="hidden" name="id_notaris" value="<?= $d['id_notaris']; ?>">
                <input type="hidden" name="file_lama" value="<?= $d['file_upload']; ?>">

                <div class="form-group">
                    <label>Periode Laporan</label>
                    <?php 
                        // Ambil data tanggal dari DB (misal 2026-03-01) 
                        // Lalu potong hanya ambil Tahun dan Bulannya saja (2026-03)
                        $formattedMonth = date('Y-m', strtotime($displayDate)); 
                    ?>
                    <input type="month" 
                        name="tanggal_laporan" 
                        class="form-control" 
                        value="<?= $formattedMonth; ?>" 
                        required>
                    <small class="text-muted">Format: Bulan dan Tahun</small>
                </div>

                <div class="form-group">
                    <label>Jumlah Buku Daftar Akta</label>
                    <input type="number" name="jml_buku_daftar" class="form-control"
                        value="<?= $d['jml_buku_daftar']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Jumlah Surat Dibukukan</label>
                    <input type="number" name="jml_tangan_dibukukan" class="form-control"
                        value="<?= $d['jml_tangan_dibukukan']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Jumlah Surat Disahkan</label>
                    <input type="number" name="jml_tangan_disahkan" class="form-control"
                        value="<?= $d['jml_tangan_disahkan']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Jumlah Buku Protes</label>
                    <input type="number" name="jml_buku_protes" class="form-control"
                        value="<?= $d['jml_buku_protes']; ?>" required>
                </div>

                <div class="form-group">
                    <label>File Lama</label><br>
                    <a href="<?= $d['file_upload']; ?>" target="_blank">Lihat File Lama</a>
                </div>

                <div class="form-group">
                    <label>Upload File Baru (PDF, Max 5MB)</label>
                    <input type="file" name="file" class="form-control" accept="application/pdf">
                    <small><i>Kosongkan jika tidak mengganti file</i></small>
                </div>

                <div class="checkbox">
                    <label style="color:red">
                        <input type="checkbox" onclick="document.getElementById('submit').disabled = !this.checked;">
                        Data yang saya kirim benar dan dapat dipertanggungjawabkan
                    </label>
                </div>

                <input type="submit" name="submit" id="submit"
                    class="btn btn-success" value="SIMPAN" disabled>
            </form>

        </div>
    </div>

    <?php include "footer.php"; ?>
