<?php
include "header.php";
date_default_timezone_set('Asia/Jakarta');
?>

<div id="page-wrapper" style="background-color:#EAF5F8;">
    <div id="page-inner">
        <h1 class="page-head-line text-center" style="color:#0B2447;border-bottom:2px solid #007BFF;padding-bottom:10px;">Unggah Laporan</h1>

        <div class="form-group">
            <label for="jenis_laporan" style="font-weight:bold;">Pilih Jenis Laporan</label>
            <select class="form-control input-lg" id="jenis_laporan" onchange="tampilkanForm()" style="height:50px;">
                <option value="">-- Pilih Jenis Laporan --</option>
                <option value="bulanan" selected>Laporan Bulanan Notaris</option>
                <option value="fidusia">Laporan Fidusia</option>
            </select>
        </div>

        <!-- ================= FORM BULANAN ================= -->
        <div id="form_bulanan" style="display:none;">
            <form action="<?=$url;?>act/unggah-laporan_proses.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?=$_SESSION['kode_user']?>" readonly/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Periode Laporan</label>
                            <input type="date" name="tanggal_laporan" class="form-control" required/>
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
                    <label style="color:red;">Pastikan data Anda benar dan dapat dipertanggungjawabkan.</label>
                </div>

                <input type="submit" name="submit" id="submit_bulanan" class="btn btn-success" value="Simpan" disabled>
            </form>
        </div>

        <!-- ================= FORM FIDUSIA ================= -->
        <div id="form_fidusia" style="display:none;">

            <!-- ✅ NAV TABS (Bootstrap 3) -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#fidusia_normal" role="tab" data-toggle="tab">Isi Laporan Fidusia</a></li>
                <li><a href="#fidusia_nihil" role="tab" data-toggle="tab">Kirim Laporan Nihil</a></li>
            </ul>

            <!-- ✅ TAB CONTENT -->
            <div class="tab-content" style="background:#fff;padding:20px;border:1px solid #ddd;border-top:none;">
                
                <!-- ================= FORM NORMAL ================= -->
                <div class="tab-pane fade in active" id="fidusia_normal">
                    <form action="<?=$url;?>act/unggah-laporan-entitas_proses.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?=$_SESSION['kode_user']?>" />
                        <input type="hidden" name="tipe" value="fidusia" />

                        <div class="form-group">
                            <label>Jenis Transaksi</label>
                            <select style="width: 100%;" name="jenis_transaksi" class="form-control" id="jenis_transaksi" required onchange="toggleKeterangan()">
                                <option value="">-- Pilih Jenis Transaksi --</option>
                                <option value="Pendaftaran">Pendaftaran</option>
                                <option value="Perubahan">Perubahan</option>
                                <option value="Perbaikan">Perbaikan</option>
                                <option value="Penghapusan">Penghapusan</option>
                            </select>
                        </div>

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

                                <div class="form-group" id="no_sertifikat_lama" style="display:none;">
                                    <label>No Sertifikat Lama</label>
                                    <input type="text" name="no_sertifikat_lama" id="input_no_sertifikat_lama" class="form-control" />
                                </div>

                                <div class="form-group">
                                    <label>No Sertifikat</label>
                                    <input type="text" name="no_sertifikat" class="form-control" required/>
                                </div>

                                <div class="form-group">
                                    <label>Kategori Nilai Penjaminan</label>
                                    <select style="width: 100%;" name="nilai_jaminan" class="form-control" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="<=50 juta">s.d. 50 juta</option>
                                        <option value="50-100 juta">50 juta – 100 juta</option>
                                        <option value="100-250 juta">100 juta – 250 juta</option>
                                        <option value="250-500 juta">250 juta – 500 juta</option>
                                        <option value="500 juta – 1 M">500 juta – 1 Miliar</option>
                                        <option value="1 – 100 M">1 Miliar – 100 Miliar</option>
                                        <option value="100 – 500 M">100 M – 500 Miliar</option>
                                        <option value="500 M – 1 T">500 M – 1 Triliun</option>
                                        <option value=">1 T">> 1 Triliun</option>
                                        <option value="Tidak Relevan">Tidak Relevan</option>
                                    </select>
                                </div>

                                <div class="form-group" id="keterangan_box" style="display:none;">
                                    <label>Keterangan Perubahan / Perbaikan</label>
                                    <textarea name="ket" id="input_keterangan" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <input type="checkbox" onclick="document.getElementById('submit_fidusia_normal').disabled = !this.checked;">
                            <label style="color:red;">Saya bertanggung jawab atas keabsahan data ini</label>
                        </div>

                        <input type="submit" id="submit_fidusia_normal" name="submit" value="Simpan" class="btn btn-success" disabled>
                    </form>
                </div>

                <!-- ================= FORM NIHIL ================= -->
                <div class="tab-pane fade" id="fidusia_nihil">

                    <form action="<?=$url;?>act/unggah-laporan-entitas_proses.php"
                        method="POST"
                        onsubmit="return confirm('Kirim laporan NIHIL untuk periode ini?')">

                        <!-- HIDDEN PARAM -->
                        <input type="hidden" name="id" value="<?=$_SESSION['kode_user']?>" />
                        <input type="hidden" name="tipe" value="fidusia" />
                        <input type="hidden" name="laporan_nihil" value="1" />

                        <!-- INFO -->
                        <div class="alert alert-warning text-center"
                            style="background-color:#FFF3CD;border:1px solid #FFEEBA;color:#664D03;">
                            <strong>Anda akan mengirim Laporan Fidusia NIHIL.</strong><br>
                            Tidak ada data akta yang diunggah untuk periode ini.
                        </div>

                        <!-- PERIODE NIHIL -->
                        <div class="form-group mt-3">
                            <label class="font-weight-bold">
                                Periode Laporan <span class="text-danger">*</span>
                            </label>

                            <input type="month"
                                name="tanggal_nihil"
                                class="form-control"
                                value="<?=date('Y-m')?>"
                                required>

                            <small class="form-text text-muted">
                                Pilih bulan dan tahun laporan fidusia nihil.
                            </small>
                        </div>

                        <!-- PERNYATAAN -->
                        <div class="form-group mt-3 text-center">
                            <input type="checkbox"
                                id="confirm_nihil"
                                onclick="document.getElementById('submit_fidusia_nihil').disabled = !this.checked;">

                            <label for="confirm_nihil" style="color:red;">
                                Saya menyatakan tidak ada laporan fidusia untuk periode tersebut.
                            </label>
                        </div>

                        <!-- SUBMIT -->
                        <div class="text-center mt-3">
                            <input type="submit"
                                id="submit_fidusia_nihil"
                                name="submit"
                                value="Kirim Laporan Nihil"
                                class="btn btn-warning"
                                disabled>
                        </div>

                    </form>

                </div>


            </div>
        </div>
    </div>
</div>

<script>
function tampilkanForm() {
    const pilihan = document.getElementById('jenis_laporan').value;
    document.getElementById('form_bulanan').style.display = 'none';
    document.getElementById('form_fidusia').style.display = 'none';

    if (pilihan === 'bulanan') {
        document.getElementById('form_bulanan').style.display = 'block';
    } else if (pilihan === 'fidusia') {
        document.getElementById('form_fidusia').style.display = 'block';
    }
}

function toggleKeterangan() {
    const jenis = document.getElementById('jenis_transaksi').value;
    const box = document.getElementById('keterangan_box');
    const noLama = document.getElementById('no_sertifikat_lama');
    const inputLama = document.getElementById('input_no_sertifikat_lama');
    const inputKet = document.getElementById('input_keterangan');

    if (jenis === 'Perubahan' || jenis === 'Perbaikan') {
        box.style.display = 'block';
        inputKet.required = true;
    } else {
        box.style.display = 'none';
        inputKet.required = false;
    }

    if (jenis === 'Perubahan') {
        noLama.style.display = 'block';
        inputLama.required = true;
    } else {
        noLama.style.display = 'none';
        inputLama.required = false;
    }
}

document.addEventListener("DOMContentLoaded", tampilkanForm);
</script>

<?php include "footer.php"; ?>
