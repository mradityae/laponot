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
                <h1 class="page-head-line" align="center">Daftar Laporan Fidusia</h1>
                <!-- <center><h4><b>Status :  <?php //echo $status;?></b></h4></center> -->
            </div>
        </div>
        <a href="<?= $url; ?>act/export_excel_pengguna.php?id=<?= $id ?>"
               class="btn btn-success me-2" style="border-radius: 6px; padding: 10px 20px; font-weight: 600; color: white;">
                <i class="fa fa-file-excel-o" style="margin-right: 5px;"></i> Export Excel
        </a>
        <a href="<?= $url; ?>act/export_pdf_pengguna.php?id=<?= $id ?>"
            class="btn btn-danger" style="border-radius: 6px; padding: 10px 20px; font-weight: 600; color: white;">
            <i class="fa fa-file-pdf-o" style="margin-right: 5px;"></i> Export PDF
        </a>
		<div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered data" align="left">
                      <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Akta</th>
                                <th>Pemberi Fidusia</th>
                                <th>Penerima Fidusia</th>
                                <th>Nomor Akta</th>
                                <th>Tipe</th>
                                <th>No Sertifikat</th>
                                <th>Jenis transaksi</th>
                                <!-- <th>File</th> -->
                                <!-- <th>Status</th> -->
                                <!-- <th>Keterangan</th> -->
                                <th>Edit</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no=1;

                                if ($status == "Semua"){
                                    $ambil=$koneksi->prepare("SELECT * FROM laporan_entitas WHERE id_notaris=:isiid");
                                }
                                else{
                                    $ambil=$koneksi->prepare("SELECT * FROM laporan_entitas WHERE id_notaris=:isiid and status=:status");
                                    $ambil->BindParam(":status",$status);   
                                }
                                
                                $ambil->BindParam(":isiid",$id);
                                $ambil->execute();

                                while ($row=$ambil->fetch())
                                {
                                    echo "<tr>";
                                    echo "<td>".$no;
                                    echo "<td>".date('d-F-Y', strtotime($row['tanggal']));
                                    echo "<td>".$row['pemberi'];
                                    echo "<td>".$row['penerima'];
                                    echo "<td>".$row['nomor'];
                                    echo "<td>".$row['tipe'];
                                    echo "<td>".$row['no_sertifikat'];
                                    echo "<td>".$row['jenis_transaksi'];
                                    // echo "<td> <a href=".$row['file_upload']." target='_blank'>Lihat File Laporan</a>";
                                    // echo "<td>".$row['status'];
                                    
                                    if ($row['keterangan'] == null || $row['keterangan'] == "") 
                                    {
                                        // echo "<td>-</td>";
                                    }
                                    else
                                    {
                                        //--echo "<td>".date('d-M-Y', strtotime($row['tanggal_pelantikan']));
                                        // echo "<td>".$row['keterangan'];
                                    }
                                    echo "<td align='center'>
                                              <a href='edit_laporan.php?id=" . $row['id_laporan'] . "' title='Edit'><img src='../assets/img/edit.png' height='20'></a>
                                          </td>";
                                    echo "<td aligh='center'>
                                             <a href='hapus_laporan.php?id=" . $row['id_laporan'] . "' title='Hapus' onclick=\"return confirm('Yakin ingin menghapus laporan ini?')\"><img src='../assets/img/delete.png' height='20'></a>
                                          </td>";

                                    
                                    // if($row['status'] == "Ditolak")
                                    // {
                                    //     echo "<td align='center'>
                                    //         <a href='kirim_ulang_entitas.php?idLaporan=".$row['id_laporan']."'><img src='../assets/img/edit.png' border='0' height='20' width='20'></img>
                                    //         </a>
                                    //         </td>";
                                    // }
                                    // else
                                    // {
                                    //     echo "<td> - </td>";
                                    // }

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