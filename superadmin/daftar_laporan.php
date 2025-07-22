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
    $kedudukan = $_GET["kedudukan"];
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line" align="left">Daftar Laporan Bulanan Notaris</h1>
                    </div>
                </div>
                    <center>
                        <h4><b>Daftar Laporan Bulanan Notaris Wilayah :   <?php
                                                         if($kedudukan == "Semua Daerah"){
                                                            echo "Seluruh Daerah";
                                                         }else{
                                                            echo getWilayah($koneksi, $kedudukan);
                                                         }  
                                                    ?></b></h4>
                        <h4><b>Status :  <?php echo $status;?></b></h4>
                    </center>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered data" align="left">
                              <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Wilayah</th>
                                        <th>Bulan</th>
                                        <th>File Laporan</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $no=1;
                                        
                                        if ($status == "Semua" && $kedudukan == "Semua Daerah") {
                                            $ambil=$koneksi->prepare("SELECT  l.id_laporan, l.id_notaris, l.tanggal, l.file_upload, l.status, l.keterangan, notaris.nama, kedudukan.nama_kedudukan 
                                            FROM laporan as l
                                            join notaris on notaris.id_notaris = l.id_notaris
                                            join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan
                                            order by l.id_laporan desc");
                                        
                                        }
                                        else if ($status == "Semua" && $kedudukan != "Semua Daerah") {
                                            $ambil=$koneksi->prepare("SELECT  l.id_laporan, l.id_notaris, l.tanggal, l.file_upload, l.status, l.keterangan, notaris.nama, kedudukan.nama_kedudukan 
                                            FROM laporan as l
                                            join notaris on notaris.id_notaris = l.id_notaris
                                            join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan
                                            WHERE notaris.id_kedudukan=:kedudukan
                                            order by l.id_laporan desc");
                                            $ambil->BindParam(":kedudukan",$kedudukan);                                        
                                        }
                                        else if ($status != "Semua" && $kedudukan == "Semua Daerah") {
                                            $ambil=$koneksi->prepare("SELECT  l.id_laporan, l.id_notaris, l.tanggal, l.file_upload, l.status, l.keterangan, notaris.nama, kedudukan.nama_kedudukan 
                                            FROM laporan as l
                                            join notaris on notaris.id_notaris = l.id_notaris
                                            join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan
                                            WHERE l.status=:status
                                            order by l.id_laporan desc");
                                            $ambil->BindParam(":status",$status);                                       
                                        }
                                        else{
                                            $ambil=$koneksi->prepare("SELECT  l.id_laporan, l.id_notaris, l.tanggal, l.file_upload, l.status, l.keterangan, notaris.nama, kedudukan.nama_kedudukan 
                                            FROM laporan as l
                                            join notaris on notaris.id_notaris = l.id_notaris
                                            join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan
                                            WHERE l.status=:status and notaris.id_kedudukan=:kedudukan
                                            order by l.id_laporan desc");
                                            $ambil->BindParam(":status",$status);
                                            $ambil->BindParam(":kedudukan",$kedudukan);
                                        }

                                        $ambil->execute();

                                        while ($row=$ambil->fetch())
                                        {
                                            echo "<tr>";
                                            echo "<td>".$no;
                                            echo "<td>".$row['nama'];
                                            echo "<td>".$row['nama_kedudukan'];
                                            echo "<td>".date('d-F-Y', strtotime($row['tanggal']));
                                            echo "<td> <a href=".$row['file_upload']." target='_blank'>Lihat File Laporan</a>";
                                            echo "<td>".$row['status'];
                                            
                                            if ($row['keterangan'] == null || $row['keterangan'] == "") 
                                            {
                                                echo "<td>-</td>";
                                            }
                                            else
                                            {
                                                echo "<td>".$row['keterangan'];
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
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
<?php
include "footer.php";
?>