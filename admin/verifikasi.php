<?php
include "header.php";
include("../config/koneksi.php");
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Verifikasi Laporan</h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                     <!--    Hover Rows  -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <a href="daftar_laporan.php" class="btn btn-primary">KEMBALI</a>
                            <br><br>
                            <div class="table-responsive">
                                <?php
								$idLaporan=$_GET['idLaporan'];
								$nama=$_GET['nama'];

								$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					            $ambil=$koneksi->prepare("SELECT * FROM laporan where id_laporan=:id_laporan");
					            $ambil->BindParam(":id_laporan",$idLaporan);
								$ambil->execute();
								$count = $ambil->rowCount();
								$d = $ambil->fetch(PDO::FETCH_ASSOC);
								$displayDate = date('F Y', strtotime($d['tanggal']));
								?>
								<form action="<?=$url;?>act/verifikasi_proses.php" method="POST" enctype="multipart/form-data">
									
									<input type="hidden" name="id_laporan" value="<?php echo $d['id_laporan']?>"></input>
									<input type="hidden" name="id_notaris" value="<?php echo $d['id_notaris']?>"></input>

									<table class="table table-bordered table-striped">
									<tr>
										<th width="40%">Notaris</th>
										<td><?php echo $nama?></td>
									</tr>
									<tr>
										<th width="40%">Periode Laporan</th>
										<td><?php echo $displayDate?></td>
									</tr>
									<tr>
										<th width="40%">Jumlah Akta Daftar Akta</th>
										<td><?php echo $d['jml_buku_daftar'] ?></td>
									</tr>
									<tr>
										<th width="40%">Jumlah Akta Surat Di Bawah Tangan Yang Dibukukan</th>
										<td><?php echo $d['jml_tangan_dibukukan'] ?></td>
									</tr>
									<tr>
										<th width="40%">Jumlah Akta Surat Di Bawah Tangan Yang Disahkan</th>
										<td><?php echo $d['jml_tangan_disahkan'] ?></td>
									</tr>
									<tr>
										<th width="40%">Jumlah Akta Protes</th>
										<td><?php echo $d['jml_buku_protes'] ?></td>
									</tr>
									<tr>
										<th width="40%">File Laporan</th>
										<td><a href="<?php echo $d['file_upload']?>"  target="_blank">Lihat File Laporan</a></td>
									</tr>

									<tr>
										<th width="40%">Status</th>
										<td>
											<select name="status" class="form-control">
												<option value="Laporan Terkirim" <?php if($d['status'] == 'Laporan Terkirim') echo 'Selected' ?>>Laporan Terkirim</option>
												<option value="Terverifikasi" <?php if($d['status'] == 'Terverifikasi') echo 'Selected' ?>>Terverifikasi</option>
												<option value="Ditolak" <?php if($d['status'] == 'Ditolak') echo 'Selected' ?>>Ditolak</option>
											</select>
										</td>
									</tr>
					
									<tr>
										<th width="40%">Keterangan</th>
										<td>
											<input type="text" name="keterangan" class="form-control" placeholder="Isikan Keterangan untuk memberikan penjelasan status kepada pendaftar"  value="<?php echo $d['keterangan'] ?>"/>
										</td>
									</tr>
									<tr>
										<td colspan="2" align="center">
											<input type="submit" name="submit" class="btn btn-success " value="SIMPAN" >
										</td>
									</tr>
									</table> 
								</form>                  
								
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