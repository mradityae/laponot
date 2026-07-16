<?php
include "header.php";
include("../config/koneksi.php");
date_default_timezone_set('Asia/Jakarta');
?>

<!-- Flatpickr Theme Material Blue -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<!-- CDN DataTables Terbaru (Mengatasi Error 404 gambar sort_asc.png / sort_desc.png) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

<div id="page-wrapper" style="background-color:#EAF5F8; min-height: 600px;">
    <div id="page-inner">
        
        <h1 class="page-head-line text-center" style="color:#0B2447; border-bottom:2px solid #007BFF; padding-bottom:10px;">Data Register MPW</h1>

        <div class="panel panel-default" style="border: 1px solid #ddd; margin-top: 20px;">
            <div class="panel-body" style="background: #fff; padding: 20px;">
                
                <div style="margin-bottom: 20px;">
                    <a href="register_mpw_tambah.php" class="btn btn-primary btn-lg"><i class="fa fa-plus"></i> Tambah Register MPW</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tableMpw" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>No Surat</th>
                                <th>No Register</th>
                                <th>Tanggal Surat</th>
                                <th>Tanggal Pemanggilan</th>
                                <th>Pelapor</th>
                                <th>Terlapor</th>
                                <th>Nomor Surat Panggilan</th>
                                <th width="18%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $no = 1;
                                $stmt = $koneksi->prepare("SELECT * FROM register_mpw ORDER BY id DESC");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <tr data-row="<?php echo $jsonData; ?>">
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['no_surat']); ?></td>
                                        <td><?php echo htmlspecialchars($row['no_register_perkara']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_surat']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_pemanggilan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['pelapor_nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['terlapor_nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nomor_surat_panggilan']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-xs btn-detail">
                                                <i class="fa fa-eye"></i> Detail
                                            </button>
                                            
                                            <button type="button" class="btn btn-warning btn-xs btn-edit">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            
                                            <button type="button" class="btn btn-danger btn-xs btn-hapus">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='9'>Error: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL (Atribut aria-hidden DIBUANG untuk mencegah amukan browser) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalDetailLabel"><i class="fa fa-eye"></i> Detail Register MPW</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tr><th width="30%">No Surat</th><td id="det_no_surat"></td></tr>
                    <tr><th>No Register Perkara</th><td id="det_no_register_perkara"></td></tr>
                    <tr><th>Tanggal Surat</th><td id="det_tanggal_surat"></td></tr>
                    <tr><th>Tanggal Pemanggilan</th><td id="det_tanggal_pemanggilan"></td></tr>
                    <tr><th>Nama Pelapor</th><td id="det_pelapor_nama"></td></tr>
                    <tr><th>Telepon Pelapor</th><td id="det_pelapor_telepon"></td></tr>
                    <tr><th>Alamat Pelapor</th><td id="det_pelapor_alamat"></td></tr>
                    <tr><th>Status Hadir Pelapor</th><td id="det_pelapor_status_hadir"></td></tr>
                    <tr><th>Nama Terlapor</th><td id="det_terlapor_nama"></td></tr>
                    <tr><th>Kedudukan Notaris</th><td id="det_kedudukan_notaris"></td></tr>
                    <tr><th>Telepon Terlapor</th><td id="det_terlapor_telepon"></td></tr>
                    <tr><th>Status Hadir Terlapor</th><td id="det_terlapor_status_hadir"></td></tr>
                    <tr><th>Nomor Surat Panggilan</th><td id="det_nomor_surat_panggilan"></td></tr>
                    <tr><th>Created At</th><td id="det_created_at"></td></tr>
                    <tr><th>Updated At</th><td id="det_updated_at"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT (Atribut aria-hidden DIBUANG) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="form-horizontal" action="../act/mpw-action_proses.php" method="POST">
            <input type="hidden" name="aksi" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalEditLabel"><i class="fa fa-edit"></i> Edit Register MPW</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">No Surat</label>
                        <div class="col-sm-9">
                            <input type="text" name="no_surat" id="edit_no_surat" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">No Register Perkara</label>
                        <div class="col-sm-9">
                            <input type="text" name="no_register_perkara" id="edit_no_register_perkara" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tanggal Surat</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" name="tanggal_surat" id="edit_tanggal_surat" class="form-control datepicker-mod" required style="background-color: #fff;">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tanggal Pemanggilan</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" name="tanggal_pemanggilan" id="edit_tanggal_pemanggilan" class="form-control datepicker-mod" required style="background-color: #fff;">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h5 style="margin-left: 15px; font-weight: bold; color: #337ab7;">DATA PELAPOR</h5>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nama Pelapor</label>
                        <div class="col-sm-9">
                            <input type="text" name="pelapor_nama" id="edit_pelapor_nama" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Telepon Pelapor</label>
                        <div class="col-sm-9">
                            <input type="number" name="pelapor_telepon" id="edit_pelapor_telepon" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Alamat Pelapor</label>
                        <div class="col-sm-9">
                            <textarea name="pelapor_alamat" id="edit_pelapor_alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Status Hadir Pelapor</label>
                        <div class="col-sm-9">
                            <select name="pelapor_status_hadir" id="edit_pelapor_status_hadir" class="form-control" style="width: 100%;" required>
                                <option value="">-- Pilih Status Hadir --</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Tidak Hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr>
                    <h5 style="margin-left: 15px; font-weight: bold; color: #337ab7;">DATA TERLAPOR</h5>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nama Terlapor</label>
                        <div class="col-sm-9">
                            <input type="text" name="terlapor_nama" id="edit_terlapor_nama" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Kedudukan Notaris</label>
                        <div class="col-sm-9">
                            <select name="kedudukan_notaris" id="edit_kedudukan_notaris" class="form-control" style="width: 100%;" required>
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
                                <option value="Kabupaten Karawang">Kabawang</option>
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
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Telepon Terlapor</label>
                        <div class="col-sm-9">
                            <input type="number" name="terlapor_telepon" id="edit_terlapor_telepon" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Status Hadir Terlapor</label>
                        <div class="col-sm-9">
                            <select name="terlapor_status_hadir" id="edit_terlapor_status_hadir" class="form-control" style="width: 100%;" required>
                                <option value="">-- Pilih Status Hadir --</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Tidak Hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nomor Surat Panggilan</label>
                        <div class="col-sm-9">
                            <input type="text" name="nomor_surat_panggilan" id="edit_nomor_surat_panggilan" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>

<!-- ========================================================================= -->
<!-- PANGGIL SCRIPT DEPENDENSI & DATA TABLES -->
<!-- ========================================================================= -->
<!-- DataTables JS CDN -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.css"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<!-- 
    JQUERY STEPS PLUGIN CDN 
    Diletakkan di sini untuk mengatasi `$(...).steps is not a function` pada custom.js
-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>

<!-- LOGIKAL SCRIPT UTAMA -->
<script type="text/javascript">
$(document).ready(function() {
    // Jalankan DataTables
    $('#tableMpw').DataTable();

    // Inisialisasi Instansiasi Flatpickr
    const fpSurat = flatpickr("#edit_tanggal_surat", {
        locale: "id",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        allowInput: false,
        disableMobile: "true"
    });

    const fpPanggilan = flatpickr("#edit_tanggal_pemanggilan", {
        locale: "id",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        allowInput: false,
        disableMobile: "true"
    });

    // Event Delegation untuk Tombol Detail (Bekerja aman meski tabel di-sorting/search)
    $('#tableMpw').on('click', '.btn-detail', function() {
        const rowData = $(this).closest('tr').data('row');
        
        $('#det_no_surat').text(rowData.no_surat || '-');
        $('#det_no_register_perkara').text(rowData.no_register_perkara || '-');
        $('#det_tanggal_surat').text(rowData.tanggal_surat || '-');
        $('#det_tanggal_pemanggilan').text(rowData.tanggal_pemanggilan || '-');
        $('#det_pelapor_nama').text(rowData.pelapor_nama || '-');
        $('#det_pelapor_telepon').text(rowData.pelapor_telepon || '-');
        $('#det_pelapor_alamat').text(rowData.pelapor_alamat || '-');
        $('#det_pelapor_status_hadir').text(rowData.pelapor_status_hadir || '-');
        $('#det_terlapor_nama').text(rowData.terlapor_nama || '-');
        $('#det_kedudukan_notaris').text(rowData.kedudukan_notaris || '-');
        $('#det_terlapor_telepon').text(rowData.terlapor_telepon || '-');
        $('#det_terlapor_status_hadir').text(rowData.terlapor_status_hadir || '-');
        $('#det_nomor_surat_panggilan').text(rowData.nomor_surat_panggilan || '-');
        $('#det_created_at').text(rowData.created_at || '-');
        $('#det_updated_at').text(rowData.updated_at || '-');
        
        $('#modalDetail').modal('show');
    });

    // Event Delegation untuk Tombol Edit
    $('#tableMpw').on('click', '.btn-edit', function() {
        const rowData = $(this).closest('tr').data('row');
        
        $('#edit_id').val(rowData.id);
        $('#edit_no_surat').val(rowData.no_surat);
        $('#edit_no_register_perkara').val(rowData.no_register_perkara);
        
        if (rowData.tanggal_surat) fpSurat.setDate(rowData.tanggal_surat);
        if (rowData.tanggal_pemanggilan) fpPanggilan.setDate(rowData.tanggal_pemanggilan);

        $('#edit_pelapor_nama').val(rowData.pelapor_nama);
        $('#edit_pelapor_telepon').val(rowData.pelapor_telepon);
        $('#edit_pelapor_alamat').val(rowData.pelapor_alamat);
        $('#edit_pelapor_status_hadir').val(rowData.pelapor_status_hadir).change();
        $('#edit_terlapor_nama').val(rowData.terlapor_nama);
        $('#edit_kedudukan_notaris').val(rowData.kedudukan_notaris).change();
        $('#edit_terlapor_telepon').val(rowData.terlapor_telepon);
        $('#edit_terlapor_status_hadir').val(rowData.terlapor_status_hadir).change();
        $('#edit_nomor_surat_panggilan').val(rowData.nomor_surat_panggilan);
        
        $('#modalEdit').modal('show');
    });

    // Event Delegation untuk Tombol Hapus
    $('#tableMpw').on('click', '.btn-hapus', function() {
        const rowData = $(this).closest('tr').data('row');
        const id = rowData.id;

        swal({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dd6b55",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                window.location.href = "../act/mpw-action_proses.php?aksi=hapus&id=" + id;
            }
        });
    });
});
</script>

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success_tambah') {
        echo "<script>swal('Sukses!', 'Data berhasil ditambahkan.', 'success');</script>";
    } elseif ($_GET['status'] == 'success_update') {
        echo "<script>swal('Sukses!', 'Data berhasil diperbarui.', 'success');</script>";
    } elseif ($_GET['status'] == 'success_hapus') {
        echo "<script>swal('Sukses!', 'Data berhasil dihapus.', 'success');</script>";
    } elseif ($_GET['status'] == 'failed') {
        echo "<script>swal('Gagal!', 'Terjadi kesalahan sistem.', 'error');</script>";
    }
}
?>