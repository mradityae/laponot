<?php
include "header.php";
include("../config/koneksi.php");
if(isset($_GET['aktif'])){
$aktif=$_GET['aktif'];
}
else{
$aktif = "Semua";
}

$kedudukan = $_SESSION["kedudukan"];
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line" align="left">Daftar Notaris</h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                     <!--    Hover Rows  -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                 <table class="table table-hover table-striped table-sm table-bordered data" align="left">
                                  <thead>
                                        <tr>
                                            <th align='center'>No</th>
                                            <th align='center'>Nama</th>
                                            <th align='center'>Email</th>
                                            <th align='center'>Jenis Kelamin</th>
                                            <th align='center'>Alamat</th>
                                            <th align='center'>Telepon</th>
                                            <th align='center'>Status</th>
                                            <th align='center'>Laporan</th>
                                            <th align='center'>Detail</th>
                                            <th align='center'>Hapus</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $no=1;
                                            $level=2;
                                            
                                            if($aktif == "0"){
                                                $ambil=$koneksi->prepare("SELECT * FROM notaris WHERE level =:level and id_kedudukan =:id_kedudukan and aktif=:aktif");
                                                $ambil->BindParam(":aktif",$aktif);
                                            }
                                            else{
                                                $ambil=$koneksi->prepare("SELECT * FROM notaris WHERE level =:level and id_kedudukan =:id_kedudukan");
                                            }

                                            $ambil->BindParam(":level",$level);
                                            $ambil->BindParam(":id_kedudukan",$kedudukan);
                                            $ambil->execute();
                                            while ($row=$ambil->fetch())
                                            {
                                                echo "<tr>";
                                                echo "<td>".$no;
                                                echo "<td>".$row['nama'];
                                                echo "<td>".$row['email'];
                                                echo "<td>".$row['jenis_kelamin'];
                                                echo "<td>".$row['alamat'];
                                                echo "<td>".$row['telepon'];
                                                if($row['aktif'] == 1)
                                                {
                                                    echo "<td> Aktif";
                                                }
                                                else
                                                {
                                                     echo "<td> Belum Aktif";
                                                }
                                                
                                                echo "<td align='center'><a href='daftar_laporan_notaris.php?id=".$row['id_notaris']."&nama=".$row['nama']."'><img src='../assets/img/clipboard.png' border='0' height='20' width='20'></img></a></td>";
                                                echo "<td align='center'><a href='edit_notaris.php?id=".$row['id_notaris']."&nama=".$row['nama']."'><img src='../assets/img/edit.png' border='0' height='20' width='20'></img></a></td>";
                                                echo "<td align='center'><a href='#' onclick='deletenotaris(".$row['id_notaris'].")'><img src='../assets/img/delete.png' border='0' height='20' width='20'></img></a></td>";
                                                $no++;
                                            }
                                            $koneksi = null;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End  Hover Rows  -->
            </div>
                <!-- /. ROW  -->
            </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
<?php
include "footer.php";
?>