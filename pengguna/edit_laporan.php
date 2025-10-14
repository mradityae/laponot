<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

$id_laporan = $_GET['id'] ?? null;
if (!$id_laporan) {
    echo "ID laporan tidak ditemukan.";
    exit;
}

// Ambil data laporan
$stmt = $koneksi->prepare("SELECT * FROM laporan_entitas WHERE id_laporan = :id_laporan");
$stmt->bindParam(':id_laporan', $id_laporan);
$stmt->execute();
$data = $stmt->fetch();

if (!$data) {
    echo "Data laporan tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $pemberi = $_POST['pemberi'];
    $penerima = $_POST['penerima'];
    $nomor = $_POST['nomor'];
    $tipe = $_POST['tipe'];
    $no_sertifikat = $_POST['no_sertifikat'];
    $judul_akta = $_POST['judul_akta'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $nilai_jaminan = $_POST['nilai_jaminan'];
    $keterangan = $_POST['keterangan'];

    // === Perhitungan batas waktu input ===
    $tanggal_input = new DateTime(); // hari ini
    $tanggal_akta  = new DateTime($tanggal);

    // Deadline = tanggal 15 bulan berikutnya dari tanggal akta
    $batas_input = (clone $tanggal_akta)->modify('+1 month')->setDate(
        (clone $tanggal_akta)->modify('+1 month')->format('Y'),
        (clone $tanggal_akta)->modify('+1 month')->format('m'),
        15
    );

    $status_pelanggaran = 0;
    $keterangan_pelanggaran = null;

    if ($tanggal_input > $batas_input) {
        $status_pelanggaran = 1;
        $diff_days = $batas_input->diff($tanggal_input)->days;
        $keterangan_pelanggaran = sprintf(
            "Laporan melebihi batas waktu input. Periode laporan: %s, Maksimal: %s, Diinput: %s, Terlambat: %d hari.",
            $tanggal_akta->format('d-m-Y'),
            $batas_input->format('d-m-Y'),
            $tanggal_input->format('d-m-Y'),
            $diff_days
        );
    }

    // === Update data ===
    $update = $koneksi->prepare("UPDATE laporan_entitas 
        SET tanggal = :tanggal, pemberi = :pemberi, penerima = :penerima, nomor = :nomor, tipe = :tipe, 
            no_sertifikat = :no_sertifikat, judul_akta = :judul_akta, jenis_transaksi = :jenis_transaksi, 
            nilai_penjaminan = :nilai_jaminan, keterangan = :keterangan,
            status_pelanggaran = :status_pelanggaran,
            keterangan_pelanggaran = :keterangan_pelanggaran
        WHERE id_laporan = :id_laporan");

    $update->bindParam(':tanggal', $tanggal);
    $update->bindParam(':pemberi', $pemberi);
    $update->bindParam(':penerima', $penerima);
    $update->bindParam(':nomor', $nomor);
    $update->bindParam(':tipe', $tipe);
    $update->bindParam(':no_sertifikat', $no_sertifikat);
    $update->bindParam(':judul_akta', $judul_akta);
    $update->bindParam(':jenis_transaksi', $jenis_transaksi);
    $update->bindParam(':nilai_jaminan', $nilai_jaminan);
    $update->bindParam(':keterangan', $keterangan);
    $update->bindParam(':status_pelanggaran', $status_pelanggaran);
    $update->bindParam(':keterangan_pelanggaran', $keterangan_pelanggaran);
    $update->bindParam(':id_laporan', $id_laporan);
    $update->execute();

    echo "<script>alert('Data berhasil diperbarui'); window.location.href='daftar_laporan_entitas';</script>";
}

?>

<div id="page-wrapper">
    <div id="page-inner">
        <h2 class="text-center">Edit Laporan Fidusia</h2>
        <form method="post" onsubmit="return validateForm()">
            <input type="hidden" name="tipe" value="fidusia" />

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Judul Akta</label>
                        <input type="text" name="judul_akta" class="form-control" value="<?= $data['judul_akta'] ?>" required />
                    </div>

                    <div class="form-group">
                        <label>Nomor Akta</label>
                        <input type="text" name="nomor" class="form-control" value="<?= $data['nomor'] ?>" required />
                    </div>

                    <div class="form-group">
                        <label>Tanggal Akta</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required />
                    </div>

                    <div class="form-group">
                        <label>Jenis Transaksi</label>
                        <select name="jenis_transaksi" class="form-control" id="jenis_transaksi" required onchange="toggleKeterangan()">
                            <option value="">-- Pilih Jenis Transaksi --</option>
                            <option value="Pendaftaran" <?= ($data['jenis_transaksi'] == 'Pendaftaran') ? 'selected' : '' ?>>Pendaftaran</option>
                            <option value="Perubahan" <?= ($data['jenis_transaksi'] == 'Perubahan') ? 'selected' : '' ?>>Perubahan</option>
                            <option value="Perbaikan" <?= ($data['jenis_transaksi'] == 'Perbaikan') ? 'selected' : '' ?>>Perbaikan</option>
                            <option value="Penghapusan" <?= ($data['jenis_transaksi'] == 'Penghapusan') ? 'selected' : '' ?>>Penghapusan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pemberi Fidusia</label>
                        <input type="text" name="pemberi" class="form-control" value="<?= $data['pemberi'] ?>" required />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Penerima Fidusia</label>
                        <input type="text" name="penerima" class="form-control" value="<?= $data['penerima'] ?>" required />
                    </div>

                    <div class="form-group">
                        <label>No Sertifikat</label>
                        <input type="text" name="no_sertifikat" class="form-control" value="<?= $data['no_sertifikat'] ?>" />
                    </div>

                    <div class="form-group">
                        <label>Kategori Nilai Penjaminan</label>
                        <select name="nilai_jaminan" class="form-control" required>
                            <?php
                            $opsi = [
                                "<=50 juta" => "s.d. 50 juta",
                                "50-100 juta" => "50 juta – 100 juta",
                                "100-200 juta" => "100 juta – 200 juta",
                                "200-500 juta" => "200 juta – 500 juta",
                                "500 juta – 1 M" => "500 juta – 1 Miliar",
                                "1 – 100 M" => "1 Miliar – 100 Miliar",
                                "100 – 500 M" => "100 M – 500 Miliar",
                                "500 M – 1 T" => "500 M – 1 Triliun",
                                ">1 T" => "> 1 Triliun",
                                "Tidak Relevan" => "Tidak Relevan"
                            ];
                            foreach ($opsi as $value => $label) {
                                $selected = ($data['nilai_penjaminan'] == $value) ? 'selected' : '';
                                echo "<option value=\"$value\" $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group" id="keterangan_box" style="<?= ($data['jenis_transaksi'] == 'Perubahan') ? '' : 'display:none;' ?>">
                        <label>Keterangan Perubahan / Perbaikan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Isi jika ini merupakan perubahan data"><?= $data['keterangan'] ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <input type="checkbox" onclick="document.getElementById('submit_btn').disabled = !this.checked;">
                <label style="color:red;">Saya bertanggung jawab atas keabsahan data ini</label>
            </div>

            <button type="submit" id="submit_btn" class="btn btn-success" disabled>Simpan Perubahan</button>
            <a href="daftar_laporan_entitas" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>

<script>
function toggleKeterangan() {
    var jenis = document.getElementById('jenis_transaksi').value;
    var box = document.getElementById('keterangan_box');
    var ket = document.getElementById('keterangan');
    if (jenis === 'Perubahan') {
        box.style.display = 'block';
        ket.setAttribute('required', true);
    } else {
        box.style.display = 'none';
        ket.removeAttribute('required');
        ket.value = '';
    }
}

function validateForm() {
    var jenis = document.getElementById('jenis_transaksi').value;
    var ket = document.getElementById('keterangan').value;
    if (jenis === 'Perubahan' && ket.trim() === "") {
        alert("Keterangan wajib diisi jika jenis transaksi adalah Perubahan.");
        return false;
    }
    return true;
}
</script>

<?php include "footer.php"; ?>
