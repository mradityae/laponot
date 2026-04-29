<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");
$id = $_SESSION["kode_user"];

// --- CEK NIK NOTARIS (PDO STYLE) ---
try {
    $stmt = $koneksi->prepare("SELECT nik FROM notaris WHERE id_notaris = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $dataNotaris = $stmt->fetch(PDO::FETCH_ASSOC);

    // Modal muncul jika NIK null, kosong, atau cuma spasi
    $showModal = (empty($dataNotaris['nik']) || trim($dataNotaris['nik']) == '') ? true : false;
} catch (PDOException $e) {
    $showModal = false; 
}
?>

<!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line" align="center"><b>DASHBOARD APLIKASI LAPORAN NOTARIS</b></h1>
            </div>
        </div>
        <div class="row">
            <?php echo notifUpload($koneksi, $id, date('Y-m-d H:i:s'));?>
        </div>

        <div class="containercustom">  
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/clipboard.png"></center>
                        <h3>Jumlah Laporan</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporan($koneksi,$id,"All");?> File</p>
                        <a href="daftar_laporan.php">Daftar Laporan</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_belum.png"></center>
                        <h3>Laporan Belum Terverifikasi</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporan($koneksi,$id,"Laporan Terkirim");?> Laporan</p>
                        <a href="daftar_laporan.php?status=Laporan%20Terkirim">Daftar Laporan</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_terverifikasi.png"></center>
                        <h3>Jumlah Laporan Terverifikasi</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporan($koneksi,$id,"Terverifikasi");?> Laporan</p>
                        <a href="daftar_laporan.php?status=Terverifikasi">Daftar Laporan</a>
                    </div>
                </div>
            </div>            
        </div>

        <div class="containercustom">
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_ditolak.png"></center>
                        <h3>Jumlah Laporan Ditolak</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporan($koneksi,$id,"Ditolak");?> File</p>
                        <a href="daftar_laporan.php?status=ditolak">Daftar Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL WAJIB ISI NIK -->
<div class="modal fade" id="modalNik" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d9534f; color: white;">
                <h4 class="modal-title text-center"><b><i class="fa fa-warning"></i> PERINGATAN: WAJIB ISI NIK</b></h4>
            </div>
            <form action="<?=$url;?>act/proses_update_nik.php" method="POST">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        Yth. Bapak/Ibu Notaris, Anda <b>wajib menginput NIK</b> untuk dapat menggunakan fitur aplikasi ini.
                    </div>
                    <div class="form-group">
                        <label>Nomor Induk Kependudukan (16 Digit):</label>
                        <input type="text" name="nik" class="form-control" placeholder="Input NIK sesuai KTP" required onkeypress="return isNumberKey(event)" maxlength="16" minlength="16" autocomplete="off">
                        <input type="hidden" name="id_notaris" value="<?php echo $id; ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_nik" class="btn btn-primary btn-block">SIMPAN DATA NIK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>

<?php if ($showModal): ?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#modalNik').modal('show');
    });

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
</script>
<?php endif; ?>