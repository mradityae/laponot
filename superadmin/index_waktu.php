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
        <center>
        	<H4><b>MONITORING DATA BERDASARKAN RENTANG WAKTU</b></H4> 
        </center>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <form action="<?=$url;?>superadmin/index_waktu" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label" for="tgl_a">Dari Tanggal</label>
                                <input type="date" class="form-control" id="tgl_a" name="tgl_a"required></input>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="tgl_b">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="tgl_b" name="tgl_b" required></input>
                            </div>
                        </div>   
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-success" name="cari_data" value="CARI">
                            <button type="reset" class="btn btn-danger">ULANGI</button>                    
                        </div>
                    </form>
                    <?php
                    if(isset($_POST['cari_data']))
                    {
                        $tanggal_awal = date('Y-m-d', strtotime($_POST['tgl_a']));
                        $tanggal_akhir = date('Y-m-d', strtotime($_POST['tgl_b']));
                    ?>     
                    <center><h3><b>Pemantauan Data Pada Periode <?php echo date('d F Y', strtotime($tanggal_awal)).' (s.d) '.date('d F Y', strtotime($tanggal_akhir));?> </b></h3></center><br>  
                        <h4>Total Notaris Terdaftar :<b> <?php echo jmlNotarisRanged($koneksi, $tanggal_awal, $tanggal_akhir);?> Notaris</b></h4>             
                        <h4>Total Laporan :<b> <?php echo jmlLaporanRanged($koneksi, $tanggal_awal, $tanggal_akhir);?> Laporan</b></h4>            
                    <?php
                    }
                    ?> 
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>