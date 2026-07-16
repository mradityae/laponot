<?php
include "header.php";
include("../config/koneksi.php");
date_default_timezone_set('Asia/Jakarta');
?>

<!-- Sisipkan CSS Flatpickr Theme Material Blue agar serasi dengan Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<div id="page-wrapper" style="background-color:#EAF5F8; min-height: 600px;">
    <div id="page-inner">
        
        <h1 class="page-head-line text-center" style="color:#0B2447; border-bottom:2px solid #007BFF; padding-bottom:10px;">Tambah Register MPW</h1>

        <!-- Tombol Kembali di sudut kiri atas -->
        <div style="margin-top: 15px; margin-bottom: 15px;">
            <a href="register_mpw_tabel.php" class="btn btn-default btn-lg"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>

        <!-- Nav Tabs Bootstrap -->
        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
            <li role="presentation" class="active">
                <a href="#input-manual" aria-controls="input-manual" role="tab" data-toggle="tab" style="font-weight: bold; font-size: 16px;">
                    <i class="fa fa-edit"></i> Input Manual
                </a>
            </li>
            <li role="presentation">
                <a href="#upload-excel" aria-controls="upload-excel" role="tab" data-toggle="tab" style="font-weight: bold; font-size: 16px;">
                    <i class="fa fa-file-excel-o"></i> Upload Kolektif Excel
                </a>
            </li>
        </ul>

        <!-- Tab Panes -->
        <div class="tab-content">
            
            <!-- TAB 1: INPUT MANUAL -->
            <div role="tabpanel" class="tab-pane active" id="input-manual">
                <div class="panel panel-default" style="border: 1px solid #ddd;">
                    <div class="panel-body" style="background: #fff; padding: 30px;">
                        <form action="../act/mpw-action_proses.php" method="POST">
                            <input type="hidden" name="aksi" value="tambah">
                            
                            <div class="row">
                                <!-- KOLOM KIRI: DATA UTAMA & PELAPOR -->
                                <div class="col-md-6" style="border-right: 1px solid #eee; padding-right: 25px;">
                                    <h4 style="font-weight: bold; color: #0B2447; margin-bottom: 20px;"><i class="fa fa-file-text"></i> DATA SURAT & PERKARA</h4>
                                    
                                    <div class="form-group">
                                        <label>No Surat</label>
                                        <input type="text" name="no_surat" class="form-control input-lg" required placeholder="Masukkan nomor surat...">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>No Register Perkara</label>
                                        <input type="text" name="no_register_perkara" class="form-control input-lg" required placeholder="Masukkan nomor register perkara...">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Tanggal Surat</label>
                                        <!-- Menggunakan type="text" dengan class .datepicker-mod -->
                                        <div class="input-group">
                                            <input type="text" name="tanggal_surat" class="form-control input-lg datepicker-mod" required placeholder="Pilih tanggal surat..." style="background-color: #fff;">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Tanggal Pemanggilan</label>
                                        <!-- Menggunakan type="text" dengan class .datepicker-mod -->
                                        <div class="input-group">
                                            <input type="text" name="tanggal_pemanggilan" class="form-control input-lg datepicker-mod" required placeholder="Pilih tanggal pemanggilan..." style="background-color: #fff;">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                    
                                    <hr style="border-top: 2px dashed #eee;">
                                    
                                    <h4 style="font-weight: bold; color: #337ab7; margin-bottom: 20px;"><i class="fa fa-user"></i> DATA PELAPOR</h4>
                                    
                                    <div class="form-group">
                                        <label>Nama Pelapor</label>
                                        <input type="text" name="pelapor_nama" class="form-control input-lg" required placeholder="Masukkan nama pelapor...">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Telepon Pelapor</label>
                                        <input type="number" name="pelapor_telepon" class="form-control input-lg" placeholder="Masukkan nomor telepon pelapor...">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Alamat Pelapor</label>
                                        <textarea name="pelapor_alamat" class="form-control input-lg" rows="3" placeholder="Masukkan alamat lengkap pelapor..."></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Status Hadir Pelapor</label>
                                        <select name="pelapor_status_hadir" class="form-control input-lg" required style="border-radius: 4px; width: 100%;">
                                            <option value="">-- Pilih Status Hadir --</option>
                                            <option value="Hadir">Hadir</option>
                                            <option value="Tidak Hadir">Tidak Hadir</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- KOLOM KANAN: DATA TERLAPOR & PANGGILAN -->
                                <div class="col-md-6" style="padding-left: 25px;">
                                    <h4 style="font-weight: bold; color: #d9534f; margin-bottom: 20px;"><i class="fa fa-gavel"></i> DATA TERLAPOR (NOTARIS)</h4>
                                    
                                    <div class="form-group">
                                        <label>Nama Terlapor</label>
                                        <input type="text" name="terlapor_nama" class="form-control input-lg" required placeholder="Masukkan nama terlapor...">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Kedudukan Notaris</label>
                                        <select name="kedudukan_notaris" class="form-control input-lg" required style="border-radius: 4px; width: 100%;">
                                            <option value="">-- Pilih Wilayah Kedudukan Kab/Kota --</option>
                                            <option value="Kabupaten Bandung">Kabupaten Bandung</option>
                                            <option value="Kabupaten Bandung Barat">Kabupaten Bandung Barat</option>
                                            <option value="Kabupaten Bekasi">Kabupaten Bekasi</option>
                                            <option value="Kabupaten Bogor">Kabupaten Bogor</option>
                                            <option value="Kabupaten Ciamis">Kabupaten Ciamis</option>
                                            <option value="Kabupaten Cianjur">Kabupaten Cianjur</option>
                                            <option value="Kabupaten Cirebon">Kabupaten Cirebon</option>
                                            <option value="Kabupaten Garut">Kabupaten Garut</option>
                                            <option value="Kabupaten Indramayu">Kabupaten Indramayu</option>
                                            <option value="Kabupaten Karawang">Kabupaten Karawang</option>
                                            <option value="Kabupaten Kuningan">Kabupaten Kuningan</option>
                                            <option value="Kabupaten Majalengka">Kabupaten Majalengka</option>
                                            <option value="Kabupaten Pangandaran">Kabupaten Pangandaran</option>
                                            <option value="Kabupaten Purwakarta">Kabupaten Purwakarta</option>
                                            <option value="Kabupaten Subang">Kabupaten Subang</option>
                                            <option value="Kabupaten Sukabumi">Kabupaten Sukabumi</option>
                                            <option value="Kabupaten Sumedang">Kabupaten Sumedang</option>
                                            <option value="Kabupaten Tasikmalaya">Kabupaten Tasikmalaya</option>
                                            <option value="Kota Bandung">Kota Bandung</option>
                                            <option value="Kota Banjar">Kota Banjar</option>
                                            <option value="Kota Bekasi">Kota Bekasi</option>
                                            <option value="Kota Bogor">Kota Bogor</option>
                                            <option value="Kota Cimahi">Kota Cimahi</option>
                                            <option value="Kota Cirebon">Kota Cirebon</option>
                                            <option value="Kota Depok">Kota Depok</option>
                                            <option value="Kota Sukabumi">Kota Sukabumi</option>
                                            <option value="Kota Tasikmalaya">Kota Tasikmalaya</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Telepon Terlapor</label>
                                        <input type="number" name="terlapor_telepon" class="form-control input-lg" placeholder="Masukkan nomor telepon terlapor...">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Status Hadir Terlapor</label>
                                        <select name="terlapor_status_hadir" class="form-control input-lg" required style="border-radius: 4px; width: 100%;">
                                            <option value="">-- Pilih Status Hadir --</option>
                                            <option value="Hadir">Hadir</option>
                                            <option value="Tidak Hadir">Tidak Hadir</option>
                                        </select>
                                    </div>
                                    
                                    <hr style="border-top: 2px dashed #eee;">
                                    
                                    <h4 style="font-weight: bold; color: #5cb85c; margin-bottom: 20px;"><i class="fa fa-envelope"></i> ADMINISTRASI TAMBAHAN</h4>
                                    
                                    <div class="form-group">
                                        <label>Nomor Surat Panggilan</label>
                                        <input type="text" name="nomor_surat_panggilan" class="form-control input-lg" placeholder="Masukkan nomor surat panggilan...">
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12 text-right" style="border-top: 1px solid #eee; padding-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Simpan Data</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- TAB 2: UPLOAD EXCEL -->
            <div role="tabpanel" class="tab-pane" id="upload-excel">
                <div class="alert alert-info">
                    <h4><i class="fa fa-file-excel-o"></i> Petunjuk Impor Data Kolektif Register MPW</h4>
                    <p>Fitur ini digunakan untuk mengunggah berkas spreadsheet secara masal sekaligus.</p>
                    
                    <div style="background:#fff; padding:15px; border-left:4px solid #31708f; margin:10px 0; border-radius:4px;">
                        <p style="margin-bottom:10px; font-weight:bold; color:#31708f;"><i class="fa fa-info-circle"></i> FITUR ANTI-DUPLIKAT</p>
                        <ul style="padding-left:18px; font-size:13px; line-height:1.6; color:#333;">
                            <li>Sistem otomatis memvalidasi data berdasarkan label header kolom: <strong>No Surat</strong> dan <strong>No Register Perkara</strong>.</li>
                            <li>Jika kombinasi data tersebut sudah ada di database, baris data tersebut <strong>otomatis dilewati</strong> agar tidak terjadi duplikasi data.</li>
                            <li>Pastikan baris pertama (header kolom) mencantumkan label yang benar agar pembacaan kolom data tidak tertukar.</li>
                        </ul>
                    </div>
                </div>

                <div class="panel panel-default" style="border: 1px solid #ddd;">
                    <div class="panel-body" style="background: #fff; padding: 30px;">
                        <form action="../act/mpw-action_proses.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="aksi" value="upload_excel">
                            
                            <div class="form-group">
                                <label style="font-weight:bold; font-size: 16px;">Pilih File Excel Berisi Rekapan (.xlsx / .xls)</label>
                                <input type="file" name="file_excel_mpw" class="form-control input-lg" accept=".xlsx, .xls" required style="height: auto; padding: 10px;" />
                                <small class="text-muted">Pastikan file berformat Excel (.xlsx atau .xls) berserta baris data pelapor dan terlapor.</small>
                            </div>

                            <div class="form-group" style="margin-top:25px;">
                                <input type="checkbox" id="confirm_mpw" onclick="document.getElementById('submit_mpw').disabled = !this.checked;">
                                <label for="confirm_mpw" style="color:red; font-weight:bold; cursor: pointer; user-select: none;">
                                    Saya bertanggung jawab atas keabsahan data register MPW yang diunggah dan siap menyinkronkannya ke dalam database.
                                </label>
                            </div>

                            <div class="form-group" style="margin-top:20px;">
                                <button type="submit" id="submit_mpw" class="btn btn-primary btn-lg" disabled><i class="fa fa-upload"></i> Mulai Impor Data Kolektif</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php include "footer.php"; ?>

<!-- Sisipkan JavaScript Flatpickr dan Localization Bahasa Indonesia -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Inisialisasi Flatpickr pada class .datepicker-mod
    flatpickr(".datepicker-mod", {
        locale: "id",             // Format bahasa Indonesia untuk nama bulan/hari
        dateFormat: "Y-m-d",      // Format data yang dikirim ke backend database (YYYY-MM-DD)
        altInput: true,           // Mengaktifkan masking tampilan alternatif
        altFormat: "d F Y",       // Format tampilan ke user (Contoh: 14 Juli 2026)
        allowInput: false,        // Mencegah user mengetik manual via keyboard
        disableMobile: "true"     // Memaksa tampilan desktop tetap aktif di perangkat mobile
    });
});
</script>