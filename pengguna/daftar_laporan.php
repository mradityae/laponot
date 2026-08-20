<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

if(isset($_GET['status'])){
$status=$_GET['status'];
}
else{
$status = "Semua";
}

$id = $_SESSION["kode_user"];
?>

  <!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line" align="center">Daftar Laporan Bulanan</h1>
                <center><h4><b>Status :  <?php echo $status;?></b></h4></center>
            </div>
        </div>
        
		<div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered data" align="left">
                      <thead>
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th>Jumlah Akta Daftar Akta</th>
                                <th>Jumlah Akta Surat Di Bawah Tangan Yang Dibukukan</th>
                                <th>Jumlah Akta Surat Di Bawah Tangan Yang Disahkan</th>
                                <th>Jumlah Akta Protes</th>
                                <th>Jumlah Akta Fidusia</th>
                                <th>Jumlah Akta Badan Usaha</th>
                                <th>Jumlah Akta Wasiat</th>
                                <th>Jenis Laporan</th>
                                <th>File Laporan</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no=1;

                                if ($status == "Semua"){
                                    $ambil=$koneksi->prepare("SELECT * FROM laporan WHERE id_notaris=:isiid");
                                }
                                else{
                                    $ambil=$koneksi->prepare("SELECT * FROM laporan WHERE id_notaris=:isiid and status=:status");
                                    $ambil->BindParam(":status",$status);   
                                }
                                
                                $ambil->BindParam(":isiid",$id);
                                $ambil->execute();

                                while ($row=$ambil->fetch())
                                {
                                    echo "<tr>";
                                    echo "<td>".$no;
                                    echo "<td>".date('F-Y', strtotime($row['tanggal']));
                                    echo "<td>".$row['jml_buku_daftar'];
                                    echo "<td>".$row['jml_tangan_dibukukan'];
                                    echo "<td>".$row['jml_tangan_disahkan'];
                                    echo "<td>".$row['jml_buku_protes'];
                                    echo "<td>".$row['jml_akta_fidusia'];
                                    echo "<td>".$row['jml_akta_badan_usaha'];
                                    echo "<td>".$row['jml_akta_wasiat'];
                                    echo "<td>".(empty($row['jenis_laporan']) ? "-" : $row['jenis_laporan']);
                                    echo "<td><a href='".$row['file_upload']."?v=".time()."' target='_blank'>Lihat File Laporan</a></td>";
                                    echo "<td>".$row['status'];
                                    
                                    if ($row['keterangan'] == null || $row['keterangan'] == "") 
                                    {
                                        echo "<td>-</td>";
                                    }
                                    else
                                    {
                                        //echo "<td>".date('d-M-Y', strtotime($row['tanggal_pelantikan']));
                                        echo "<td>".$row['keterangan'];
                                    }
                                    
                                    if($row['status'] == "Ditolak")
                                    {
                                        echo "<td align='center'>
                                            <a href='kirim_ulang.php?idLaporan=".$row['id_laporan']."'><img src='../assets/img/edit.png' border='0' height='20' width='20'></img>
                                            </a>
                                            </td>";
                                    }
                                    else
                                    {
                                        echo "<td align='center'>
                                                <a href='edit_laporan_bulanan.php?idLaporan=".$row['id_laporan']."'>
                                                    <img src='../assets/img/edit.png' height='20' width='20'>
                                                </a>
                                            </td>";
                                    }

                                    echo "</tr>";
                                    $no++;
                                }
                                $koneksi = null;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>