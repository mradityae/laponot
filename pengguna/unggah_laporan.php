<?php
include "header.php";
date_default_timezone_set('Asia/Jakarta');
?>

<div id="page-wrapper">
    <div id="page-inner">
        <h1 class="page-head-line text-center">Unggah Laporan</h1>

        <div class="form-group">
            <label for="jenis_laporan">Pilih Jenis Laporan</label>
            <select class="form-control form-control-lg" id="jenis_laporan" onchange="tampilkanForm()" style="height: 50px;">
                <option value="">-- Pilih Jenis Laporan --</option>
                <option value="bulanan" selected>Laporan Bulanan Notaris</option>
                <option value="fidusia">Laporan Fidusia / Jaminan</option>
            </select>
        </div>


        <!-- FORM BULANAN -->
        <div id="form_bulanan" style="display:none;">
            <form action="<?=$url;?>act/unggah-laporan_proses.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $_SESSION['kode_user'] ?>" readonly/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Periode Laporan</label>
                            <input type="date" name="tanggal_laporan" class="form-control" required/>
                            <small class="form-text text-muted text-danger">Untuk mengisi periode laporan, jika menggunakan browser Google Chrome, klik icon kalender pada bagian pojok kanan tempat mengisi periode laporan lalu pilih bulan dan tanggal dari kalender yang muncul. Jika menggunakan browser Firefox, klik pada tempat mengisi periode, lalu pilih tanggal dan bulan dari kalender yang muncul.</small>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Akta Daftar Akta</label>
                            <input type="number" name="jml_buku_daftar" class="form-control" required/>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Akta Surat Di Bawah Tangan Yang Dibukukan</label>
                            <input type="number" name="jml_tangan_dibukukan" class="form-control" required/>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah Akta Surat Di Bawah Tangan Yang Disahkan</label>
                            <input type="number" name="jml_tangan_disahkan" class="form-control" required/>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Akta Protes</label>
                            <input type="number" name="jml_buku_protes" class="form-control" required/>
                        </div>

                        <div class="form-group">
                            <label>Unggah File (PDF, max 5MB)</label>
                            <input type="file" name="file" class="form-control" accept="application/pdf" required/>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <input type="checkbox" id="terms_bulanan" onclick="document.getElementById('submit_bulanan').disabled = !this.checked;">
                    <label style="color:red;">Pastikan data Anda benar dan dapat dipertanggungjawabkan sesuai dengan perundang - undangan yang berlaku</label>
                </div>

                <input type="submit" name="submit" id="submit_bulanan" class="btn btn-success" value="Simpan" disabled>
            </form>
        </div>

        <!-- FORM FIDUSIA -->
        <div id="form_fidusia" style="display:none;">
            <form action="<?=$url;?>act/unggah-laporan-entitas_proses.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <input type="hidden" name="id" value="<?=$_SESSION['kode_user']?>" />
                <input type="hidden" name="tipe" value="fidusia" />

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Judul Akta</label>
                            <input type="text" name="judul_akta" class="form-control" required placeholder="Contoh: Perjanjian Fidusia" />
                        </div>

                        <div class="form-group">
                            <label>Nomor Akta</label>
                            <input type="text" name="nomor" class="form-control" required />
                        </div>

                        <div class="form-group">
                            <label>Tanggal Akta</label>
                            <input type="date" name="tanggal" class="form-control" required/>
                            <small class="form-text text-muted text-danger">Untuk Chrome: klik ikon kalender. Untuk Firefox: klik kolom lalu pilih tanggal.</small>
                        </div>

                        <div class="form-group">
                            <label>Jenis Transaksi</label>
                            <select style="width: 100%;" name="jenis_transaksi" class="form-control form-control-lg" id="jenis_transaksi" required onchange="toggleKeterangan()">
                                <option value="">-- Pilih Jenis Transaksi --</option>
                                <option value="Pendaftaran">Pendaftaran</option>
                                <option value="Perubahan">Perubahan</option>
                                <option value="Pembatalan">Pembatalan</option>
                                <option value="Penghapusan">Penghapusan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pemberi Fidusia</label>
                            <input type="text" name="pemberi" class="form-control" required />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Penerima Fidusia</label>
                            <input type="text" name="penerima" class="form-control" required />
                        </div>

                        <div class="form-group">
                            <label>No Sertifikat</label>
                            <input type="text" name="no_sertifikat" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Kategori Nilai Penjaminan</label>
                            <select style="width: 100%;" name="nilai_jaminan" class="form-control form-control-lg" required>
                                <option value="">-- Pilih --</option>
                                <option value="<=50 juta">s.d. 50 juta</option>
                                <option value="50-100 juta">50 juta – 100 juta</option>
                                <option value="100-200 juta">100 juta – 200 juta</option>
                                <option value="200-500 juta">200 juta – 500 juta</option>
                                <option value="500 juta – 1 M">500 juta – 1 Miliar</option>
                                <option value="1 – 100 M">1 Miliar – 100 Miliar</option>
                                <option value="100 – 500 M">100 M – 500 Miliar</option>
                                <option value="500 M – 1 T">500 M – 1 Triliun</option>
                                <option value=">1 T">> 1 Triliun</option>
                                <option value="Tidak Relevan">Tidak relevan</option>
                            </select>
                        </div>

                        <div class="form-group" id="keterangan_box" style="display:none;">
                            <label>Keterangan Perubahan / Perbaikan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Diisi jika ini merupakan perubahan data sebelumnya"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <input type="checkbox" onclick="document.getElementById('submit_fidusia').disabled = !this.checked;">
                    <label style="color:red;">Saya bertanggung jawab atas keabsahan data ini</label>
                </div>

                <input type="submit" id="submit_fidusia" name="submit" value="Simpan" class="btn btn-success" disabled>
            </form>
        </div>

    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function() {
    tampilkanForm(); // Otomatis panggil fungsi saat halaman selesai dimuat
});

function tampilkanForm() {
    var pilihan = document.getElementById('jenis_laporan').value;
    document.getElementById('form_bulanan').style.display = 'none';
    document.getElementById('form_fidusia').style.display = 'none';

    if (pilihan === 'bulanan') {
        document.getElementById('form_bulanan').style.display = 'block';
    } else if (pilihan === 'fidusia') {
        document.getElementById('form_fidusia').style.display = 'block';
    }
}

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
        ket.value = "";
    }
}

function validateForm() {
    var jenis = document.getElementById('jenis_transaksi').value;
    var ket = document.getElementById('keterangan').value;
    if (jenis === 'Perubahan' && ket.trim() === "") {
        alert("Keterangan perubahan wajib diisi jika jenis transaksi adalah Perubahan.");
        return false;
    }
    return true;
}
</script>

<?php include "footer.php"; ?>
