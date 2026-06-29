<?php
include "header.php";
include "../config/koneksi.php";

// Ambil ID Perkara dari URL
$id_perkara = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Tarik data perkara lama
$queryPerkara = $koneksi->prepare("SELECT * FROM perkara_mpw WHERE id_perkara = ? LIMIT 1");
$queryPerkara->execute([$id_perkara]);
$perkara = $queryPerkara->fetch(PDO::FETCH_ASSOC);

if (!$perkara) {
    echo "<script>alert('Data perkara tidak ditemukan!'); window.location.href='perkara_index.php';</script>";
    exit();
}

// Ambil daftar notaris untuk wilayah kerja perkara ini
$queryNotaris = $koneksi->prepare("
    SELECT id_notaris, nama 
    FROM notaris
    WHERE aktif='1' AND id_kedudukan = ? AND level='2'
    ORDER BY nama ASC
");
$queryNotaris->execute([$perkara['id_kedudukan']]);
$dataNotaris = $queryNotaris->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h2>EDIT REGISTER PERKARA</h2>
                <p>Nomor Register: <strong><?= $perkara['nomor_register']; ?></strong></p>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">FORM EDIT DATA PERKARA</div>
                    <div class="panel-body">
                        <form action="<?= $url ?>act/edit-perkara_proses.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_perkara" value="<?= $perkara['id_perkara']; ?>">

                            <div class="form-group">
                                <label>Judul Perkara <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" value="<?= $perkara['judul']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Uraian Pengaduan<span class="text-danger">*</span></label>
                                <textarea name="uraian_pengaduan" class="form-control" rows="3"><?= $perkara['uraian_pengaduan']; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Jenis Terlapor</label>
                                <select name="jenis_terlapor" id="jenis_terlapor" class="form-control" required>
                                    <option value="database" <?= $perkara['jenis_terlapor'] == 'database' ? 'selected' : ''; ?>>Notaris Dari Database</option>
                                    <option value="manual" <?= $perkara['jenis_terlapor'] == 'manual' ? 'selected' : ''; ?>>Input Manual</option>
                                </select>
                            </div>

                            <!-- NOTARIS DATABASE -->
                            <div id="notaris_database" style="<?= $perkara['jenis_terlapor'] == 'database' ? '' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>Nama Terlapor (Database)</label>
                                    <select name="id_notaris" id="id_notaris_select" class="form-control">
                                        <option value="">-- PILIH NOTARIS --</option>
                                        <?php foreach($dataNotaris as $notaris): ?>
                                            <option value="<?= $notaris['id_notaris']; ?>" <?= $perkara['id_notaris'] == $notaris['id_notaris'] ? 'selected' : ''; ?>><?= $notaris['nama']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- NOTARIS MANUAL -->
                            <div id="notaris_manual" style="<?= $perkara['jenis_terlapor'] == 'manual' ? '' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>Nama Terlapor (Manual)</label>
                                    <input type="text" name="nama_terlapor_manual" id="nama_terlapor_manual" class="form-control" value="<?= $perkara['nama_terlapor_manual']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nama Pelapor</label>
                                <input type="text" name="nama_pelapor" class="form-control" value="<?= $perkara['nama_pelapor']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Nomor HP Pelapor</label>
                                <input type="text" name="no_hp_pelapor" class="form-control nomor-hp-format" maxlength="16" value="<?= $perkara['no_hp_pelapor']; ?>">
                            </div>

                            <div class="form-group">
                                <label>Nomor HP Terlapor</label>
                                <input type="text" name="no_hp_terlapor" class="form-control nomor-hp-format" maxlength="16" value="<?= $perkara['no_hp_terlapor']; ?>">
                            </div>

                            <div class="form-group">
                                <label>Alamat Pelapor</label>
                                <textarea name="alamat_pelapor" class="form-control" rows="3"><?= $perkara['alamat_pelapor']; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Alamat Terlapor</label>
                                <textarea name="alamat_terlapor" class="form-control" rows="3"><?= $perkara['alamat_terlapor']; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Data Dukung Google Drive</label>
                                <input type="url" name="data_dukung_link" class="form-control" value="<?= $perkara['data_dukung_link']; ?>">
                            </div>

                            <hr>
                            <h4>Pembaruan Berkas (Biarkan kosong jika tidak ingin mengubah berkas)</h4>

                            <div class="form-group">
                                <label>Surat Laporan Pengaduan <span class="text-info">*Hanya PDF, Maks 5MB</span></label>
                                <input type="file" name="surat_pengaduan" class="form-control file-upload-check" accept="application/pdf">
                                <?php if($perkara['surat_pengaduan']): ?>
                                    <p class="help-block">Berkas saat ini: <a href="../<?= $perkara['surat_pengaduan']; ?>" target="_blank">Lihat Dokumen</a></p>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label>SK Penetapan Majelis Pemeriksa <span class="text-info">*Hanya PDF, Maks 5MB</span></label>
                                <input type="file" name="sk_majelis_pemeriksa" class="form-control file-upload-check" accept="application/pdf">
                                <?php if($perkara['sk_majelis_pemeriksa']): ?>
                                    <p class="help-block">Berkas saat ini: <a href="../<?= $perkara['sk_majelis_pemeriksa']; ?>" target="_blank">Lihat Dokumen</a></p>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="perkara_index.php" class="btn btn-default">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script>
$(document).ready(function() {
    // Switch Tampilan Tipe Terlapor
    $('#jenis_terlapor').change(function(){
        if($(this).val() == 'database'){
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

    // Validasi Real-time Input Angka HP
    $('.nomor-hp-format').on('input fieldchange', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Validasi File PDF & Ukuran
    $('.file-upload-check').change(function() {
        if (this.files && this.files[0]) {
            let file = this.files[0];
            if (file.type !== 'application/pdf') {
                alert('Format file harus berupa PDF!');
                $(this).val('');
                return false;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5 MB!');
                $(this).val('');
                return false;
            }
        }
    });
});
</script>