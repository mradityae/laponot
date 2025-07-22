<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");
$id  = $_SESSION["kode_user"];

if(isset($_POST['submit'])){
    $status=$_POST['kedudukan'];
    }
else{
    $status = "Semua Daerah";
}
?>

<!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 align="center"><b>DASHBOARD APLIKASI LAPORAN NOTARIS<b></h1>
                <h3 class="page-head-line" align="center">SUPER ADMIN</h3>
            </div>
        </div>
        <!-- SEARCH BY KEDUDUKAN -->
        <div class="row">
            <form action="index" method="POST" enctype="multipart/form-data">
                <div class="col-md-10">
                    <select name="kedudukan" class="form-control">
                            <option value="Semua Daerah" selected>Semua Daerah</option>
                        <?php
                            $ambil2=$koneksi->prepare("SELECT * FROM kedudukan");
                            $ambil2->execute();
                            while ($row2=$ambil2->fetch())
                            {
                            ?>
                            <option value="<?php echo $row2['id_kedudukan']; ?>" <?php if($status == $row2['id_kedudukan']) echo "selected";?>><?php echo $row2['nama_kedudukan'];?></option>
                            <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="submit" name="submit" class="btn btn-success" value="Tampilkan">
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3 align="center"><b>DATA DAERAH : <?php
                                                         if($status == "Semua Daerah"){
                                                            echo "Total Data Dari Seluruh Daerah";
                                                         }else{
                                                            echo getWilayah($koneksi, $status);
                                                         }  
                                                    ?>
                                    <b>                
                </h3>
            </div>
        </div>

        <!-- BAGIAN 1 -->
        <div class="containercustom">
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/notaris_all.png"></center>
                        <h3>Notaris Terdaftar</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php
                                if($status == "Semua Daerah"){
                                    echo jmlNotaris($koneksi,0,"Super Admin All");
                                }
                                else{
                                    echo jmlNotaris($koneksi,$status,"All");
                                } 
                            ?> Notaris</p>
                        <a href="daftar_pengguna_super?status=Semua&kedudukan=<?php echo $status;?>">Daftar Notaris</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/notaris_one.png"></center>
                        <h3>Notaris Yang Belum Aktif</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php 
                                if($status == "Semua Daerah"){
                                    echo jmlNotaris($koneksi,0,"Super Admin NotaktifAll");
                                }
                                else{
                                    echo jmlNotaris($koneksi,$status,"Super Admin Notaktif");
                                }
                            ?> Notaris</p>
                            <a href="daftar_pengguna_super?status=0&kedudukan=<?php echo $status;?>">Daftar Notaris</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/notaris_one.png"></center>
                        <h3>Jumlah Admin</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php
                                if($status == "Semua Daerah"){
                                    echo jmlNotaris($koneksi,0,"Jumlah AdminAll");
                                }else{
                                    echo jmlNotaris($koneksi,$status,"Jumlah Admin");
                                } 
                            ?> Admin</p>
                         <!-- <a href="daftar_pengguna_super">Daftar Admin</a> -->
                    </div>
                </div>
            </div>
        </div>

		<!-- BAGIAN 2 -->
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
                        <p><?php
                                if($status == "Semua Daerah"){
                                    echo jmlLaporanSuperAdmin($koneksi,"All");
                                }else{
                                    echo jmlLaporanAdmin($koneksi,$status,"All");
                                } 
                            ?> Laporan</p>
                            <a href="daftar_laporan.php?kedudukan=<?php echo $status;?>">Daftar Seluruh Laporan</a>
                    </div>
                </div>
            </div>
             <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_terverifikasi.png"></center>
                        <h3>Laporan Terverifikasi</h3>
                    </div>
                </div>
               <div class="face face2">
                    <div class="content">
                        <p><?php 
                                if($status == "Semua Daerah"){
                                    echo jmlLaporanSuperAdmin($koneksi,"Terverifikasi");
                                }else{
                                    echo jmlLaporanAdmin($koneksi,$status,"Terverifikasi");
                                } 
                               
                            ?> Laporan</p>
                            <a href="daftar_laporan.php?status=Terverifikasi&kedudukan=<?php echo $status;?>">Daftar Laporan Terverifikasi</a>
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
                        <p><?php
                                if($status == "Semua Daerah"){
                                    echo jmlLaporanSuperAdmin($koneksi,"Laporan Terkirim");
                                }else{
                                    echo jmlLaporanAdmin($koneksi,$status,"Laporan Terkirim");
                                }  
                            ?> Laporan</p>
                            <a href="daftar_laporan.php?status=Laporan%20Terkirim&kedudukan=<?php echo $status;?>">Daftar Laporan Belum Terverifikasi</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- BAGIAN 3 -->
        <div class="containercustom">
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_ditolak.png"></center>
                        <h3>Laporan Ditolak</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php
                                if($status == "Semua Daerah" ){
                                    echo jmlLaporanSuperAdmin($koneksi,"Ditolak");
                                }else{
                                    echo jmlLaporanAdmin($koneksi,$status,"Ditolak");
                                }   
                            ?> Laporan</p>
                            <a href="daftar_laporan.php?status=ditolak&kedudukan=<?php echo $status;?>">Daftar Laporan Ditolak</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>