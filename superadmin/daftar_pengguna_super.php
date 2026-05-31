<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");
if(isset($_GET['kedudukan'])){
    $status = $_GET["status"];
    $kedudukan = $_GET["kedudukan"];
}
else{
    $status = "Semua";
    $kedudukan = "Semua Daerah";
}
?>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line" align="left">Daftar Pengguna : <?php
                                                                                         if($kedudukan == "Semua Daerah"){
                                                                                            echo $kedudukan;
                                                                                         }else{
                                                                                            echo getWilayah($koneksi, $kedudukan);
                                                                                         }  
                                                                                    ?></h1>
                    </div>
                </div>
              <div class="row">
                     <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                 <table class="table table-hover table-striped table-sm table-bordered data">
                                  <thead>
                                        <tr>
                                            <th align='center'>No</th>
                                            <th align='center'>Nama</th>
                                            <th align='center'>Email</th>
                                            <th align='center'>Kedudukan</th>
                                            <th align='center'>NIK</th>
                                            <th align='center'>Terakhir Login</th>
                                            <th align='center'>Level</th>
                                            <th align='center'>Akun</th>
                                            <th align='center'>Terdaftar</th>
                                            <th align='center'>Laporan</th>
                                            <th align='center'>Detail</th>
                                            <th align='center'>Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $no=1;
                                            $level = 0;
                                            if($status == "Semua" && $kedudukan =="Semua Daerah"){
                                                $ambil=$koneksi->prepare("SELECT notaris.*, kedudukan.nama_kedudukan 
                                                FROM notaris
                                                join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan                                    
                                                WHERE level!=:level
                                                order by notaris.id_notaris desc");                                                
                                            }
                                            else if ($status == "Semua" && $kedudukan != "Semua Daerah") {
                                                $ambil=$koneksi->prepare("SELECT notaris.*, kedudukan.nama_kedudukan 
                                                FROM notaris
                                                join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan                                    
                                                WHERE level!=:level and notaris.id_kedudukan=:kedudukan
                                                order by notaris.id_notaris desc");
                                                $ambil->BindParam(":kedudukan",$kedudukan,PDO::PARAM_INT);
                                            }
                                            else if ($status != "Semua" && $kedudukan == "Semua Daerah") {
                                                $ambil=$koneksi->prepare("SELECT notaris.*, kedudukan.nama_kedudukan 
                                                FROM notaris
                                                join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan                                    
                                                WHERE level!=:level and notaris.aktif=:aktif
                                                order by notaris.id_notaris desc");
                                                $ambil->BindParam(":aktif",$status,PDO::PARAM_INT);
                                            }
                                            else{
                                                $ambil=$koneksi->prepare("SELECT notaris.*, kedudukan.nama_kedudukan 
                                                FROM notaris
                                                join kedudukan on kedudukan.id_kedudukan = notaris.id_kedudukan                                    
                                                WHERE level!=:level and notaris.aktif=:aktif and notaris.id_kedudukan=:kedudukan
                                                order by notaris.id_notaris desc");
                                                $ambil->BindParam(":kedudukan",$kedudukan,PDO::PARAM_INT);
                                                $ambil->BindParam(":aktif",$status,PDO::PARAM_INT);
                                            }
                                            $ambil->BindParam(":level",$level,PDO::PARAM_INT);
                                            $ambil->execute();

                                            while ($row=$ambil->fetch())
                                            {
                                                echo "<tr>";
                                                echo "<td>".$no."</td>";
                                                echo "<td>".$row['nama']."</td>";
                                                echo "<td>".$row['email']."</td>";                                             
                                                echo "<td>".$row['nama_kedudukan']."</td>";
                                                echo "<td>".$row['nik']."</td>";
                                                echo "<td>".$row['terakhir_login']."</td>";

                                                
                                                if($row['level'] == 0)
                                                {
                                                    echo "<td>Super Admin</td>";
                                                }
                                                else if($row['level'] == 1)
                                                {
                                                    echo "<td>Admin</td>";
                                                }
                                                else
                                                {
                                                    echo "<td>Publik</td>";
                                                }

                                                if($row['aktif'] == 1)
                                                {
                                                    echo "<td> Aktif</td>";
                                                }
                                                else
                                                {
                                                     echo "<td> Belum Aktif</td>";
                                                }

                                                echo "<td>".date('d-F-Y', strtotime($row['createDate']))."</td>";
                                                
                                                // DI SINI: Ditambahkan &kedudukan= pada parameter GET URL target _blank
                                                echo "<td align='center'>
                                                        <a href='daftar_laporan_notaris.php?id=".$row['id_notaris']."&nama=".urlencode($row['nama'])."&kedudukan=".$row['id_kedudukan']."' target='_blank'>
                                                            <button class='btn btn-xs btn-info'><i class='fa fa-file'></i> Lihat Laporan</button>
                                                        </a>
                                                      </td>";

                                                echo "<td align='center'><a href='edit_pengguna_super.php?id=".$row['id_notaris']."'><img src='../assets/img/edit.png' border='0' height='20' width='20'></img></a></td>";

                                                echo "<td align='center'><a href='#' onclick='deletepengguna(".$row['id_notaris'].")'><img src='../assets/img/delete.png' border='0' height='20' width='20'></img></a></td>";
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
            </div>
        </div>
<?php
include "footer.php";
?>