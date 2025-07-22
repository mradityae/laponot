<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");
$id = $_SESSION["kode_user"];
?>

<!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line"align="center"><b>DASHBOARD APLIKASI LAPORAN NOTARIS<b></h1>
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
<?php
include "footer.php";
?>