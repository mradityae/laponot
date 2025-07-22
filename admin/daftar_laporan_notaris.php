<?php
include "header.php";
include("../config/koneksi.php");
$id=$_GET['id'];
$nama=$_GET['nama'];
$kedudukan = $_SESSION["kedudukan"];
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Daftar Laporan Notaris : <?php echo $nama; ?></h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                     <!--    Hover Rows  -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <a href="daftar_notaris.php" class="btn btn-primary">KEMBALI</a>
                            <br><br>
                            <table class="table table-hover table-striped table-bordered data" align="left">
                              <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Bulan</th>
                                        <th>File Laporan</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Verifikasi</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    	
                                        $no=1;

                                        $ambil=$koneksi->prepare("SELECT  l.id_laporan, l.id_notaris, l.tanggal, l.file_upload, l.status, l.keterangan, notaris.nama 
                                            FROM laporan as l
                                            join notaris on notaris.id_notaris = l.id_notaris
                                            WHERE notaris.id_notaris=:id and notaris.id_kedudukan=:kedudukan");

                                        $ambil->BindParam(":id",$id);
                                        $ambil->BindParam(":kedudukan",$kedudukan);
                                        $ambil->execute();

                                        while ($row=$ambil->fetch())
                                        {
                                            echo "<tr>";
                                            echo "<td>".$no;
                                            echo "<td>".$row['nama'];
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
                                            
                                           
                                            echo "<td align='center'>
                                                <a href='verifikasi.php?idLaporan=".$row['id_laporan']."&nama=".$row['nama']."'><img src='../assets/img/edit.png' border='0' height='20' width='20'></img>
                                                </a> </td>";
                                            echo "<td align='center'>     
                                                <a href='#' onclick='deletelaporan(".$row['id_laporan'].")'><img src='../assets/img/delete.png' border='0' height='20' width='20'></img>
                                                 </a></td>";
                                                

                                            echo "</tr>";
                                            $no++;
                                        }
                                        $koneksi = null;
                                    ?>
                                </tbody>
                            </table>
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