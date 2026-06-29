<?php
include "header.php";
include "../config/koneksi.php";

// Ambil kedudukan dari session user login
$session_kedudukan = isset($_SESSION['kedudukan']) ? $_SESSION['kedudukan'] : 0;

// Query nama kedudukan otomatis berdasarkan session user
$queryNamaKedudukan = $koneksi->prepare("
    SELECT nama_kedudukan 
    FROM kedudukan 
    WHERE id_kedudukan = ? 
    LIMIT 1
");
$queryNamaKedudukan->execute([$session_kedudukan]);
$kedudukanUser = $queryNamaKedudukan->fetch(PDO::FETCH_ASSOC);
$nama_kedudukan_otomatis = $kedudukanUser ? $kedudukanUser['nama_kedudukan'] : 'Tidak Diketahui';

// Query notaris otomatis sesuai kedudukan session user
$queryNotaris = $koneksi->prepare("
    SELECT id_notaris, nama 
    FROM notaris
    WHERE aktif='1' AND id_kedudukan = ? AND level='2'
    ORDER BY nama ASC
");
$queryNotaris->execute([$session_kedudukan]);
$dataNotaris = $queryNotaris->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h2>REGISTER PERKARA</h2>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">FORM INPUT PERKARA (TAHAP REGISTRASI)</div>
                    <div class="panel-body">
                        <form action="<?=$url;?>act/tambah-perkara_proses.php" method="POST" enctype="multipart/form-data">
                            
                            <!-- INPUT BARU: JUDUL PERKARA -->
                            <div class="form-group">
                                <label>Judul Perkara<span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" placeholder="Contoh: Dugaan Pelanggaran Kode Etik Notaris Terkait Akta Jual Beli" required>
                            </div>

                            <div class="form-group">
                                <label>Uraian Singkat Pengaduan<span class="text-danger">*</span></label>
                                <textarea name="uraian_pengaduan" maxlength="50" class="form-control" rows="4"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Jenis Terlapor</label>
                                <select name="jenis_terlapor" id="jenis_terlapor" class="form-control" required>
                                    <option value="database">Notaris Dari Database</option>
                                    <option value="manual">Input Manual</option>
                                </select>
                            </div>

                            <!-- INPUT NOTARIS DARI DATABASE -->
                            <div id="notaris_database">
                                <div class="form-group">
                                    <label>Nama Terlapor (Sesuai Wilayah Kerja Anda)</label>
                                    <select name="id_notaris" id="id_notaris_select" class="form-control" required>
                                        <option value="">-- PILIH NOTARIS --</option>
                                        <?php foreach($dataNotaris as $notaris): ?>
                                            <option value="<?= $notaris['id_notaris']; ?>"><?= $notaris['nama']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- INPUT NOTARIS MANUAL -->
                            <div id="notaris_manual" style="display:none;">
                                <div class="form-group">
                                    <label>Nama Terlapor</label>
                                    <input type="text" name="nama_terlapor_manual" id="nama_terlapor_manual" class="form-control">
                                </div>
                            </div>

                            <input type="hidden" name="id_kedudukan" value="<?= (int)$session_kedudukan; ?>">

                            <div class="form-group">
                                <label>Nama Pelapor</label>
                                <input type="text" name="nama_pelapor" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Nomor HP Pelapor</label>
                                <input type="text" name="no_hp_pelapor" id="no_hp_pelapor" class="form-control nomor-hp-format" maxlength="16" placeholder="Contoh: 081234567890">
                            </div>

                            <div class="form-group">
                                <label>Nomor HP Terlapor</label>
                                <input type="text" name="no_hp_terlapor" id="no_hp_terlapor" class="form-control nomor-hp-format" maxlength="16" placeholder="Contoh: 081234567890">
                            </div>

                            <div class="form-group">
                                <label>Alamat Pelapor</label>
                                <textarea name="alamat_pelapor" class="form-control" rows="4"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Alamat Terlapor</label>
                                <textarea name="alamat_terlapor" class="form-control" rows="4"></textarea>
                            </div>

                            <!-- BERKAS UTAMA TAHAP REGISTRASI PERKARA -->
                            <div class="form-group">
                                <label>Surat Laporan Pengaduan <span class="text-danger">*Hanya PDF, Maks 5MB</span></label>
                                <input type="file" name="surat_pengaduan" class="form-control file-upload-check" accept="application/pdf" required>
                            </div>

                            <div class="form-group">
                                <label>SK Penetapan Majelis Pemeriksa <span class="text-dark">*Hanya PDF, Maks 5MB (Opsional)</span></label>
                                <input type="file" name="sk_majelis_pemeriksa" class="form-control file-upload-check" accept="application/pdf">
                            </div>

                            <div class="form-group">
                                <label>Data Dukung Google Drive (Link Eksternal)</label>
                                <input type="url" name="data_dukung_link" class="form-control" placeholder="https://drive.google.com/...">
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Registrasi</button>
                            <a href="perkara_index.php" class="btn btn-default">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>

<script>
$(document).ready(function() {
    // 1. Logika Switch Tampilan Tipe Terlapor
    $('#jenis_terlapor').change(function(){
        let jenis = $(this).val();
        if(jenis == 'database'){
            $('#notaris_database').show();
            $('#id_notaris_select').attr('required', true);
            
            $('#notaris_manual').hide();
            $('#nama_terlapor_manual').val('').attr('required', false);
        } else {
            $('#notaris_database').hide();
            $('#id_notaris_select').val('').attr('required', false);
            
            $('#notaris_manual').show();
            $('#nama_terlapor_manual').attr('required', true);
        }
    });

    // 2. Validasi File PDF & Max 5MB
    $('.file-upload-check').change(function() {
        if (this.files && this.files[0]) {
            let file = this.files[0];
            let maxSize = 5 * 1024 * 1024; 
            let fileSize = file.size;
            let fileName = file.name;
            let fileExtension = fileName.split('.').pop().toLowerCase();

            if (fileExtension !== 'pdf') {
                alert('Peringatan: Dokumen "' + fileName + '" ditolak!\nFormat file harus berupa PDF (.pdf).');
                $(this).val(''); 
                return false;
            }

            if (fileSize > maxSize) {
                alert('Peringatan: Dokumen "' + fileName + '" berukuran terlalu besar!\nUkuran file maksimal yang diperbolehkan adalah 5 MB.');
                $(this).val(''); 
                return false;
            }
        }
    });

    // 3. Validasi Real-time Input Angka HP
    $('.nomor-hp-format').on('input fieldchange', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    $('.nomor-hp-format').on('drop paste', function(e) {
        let el = this;
        setTimeout(function() {
            el.value = el.value.replace(/[^0-9]/g, '');
        }, 10);
    });
});
</script>