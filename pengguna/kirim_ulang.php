
<?php
include "header.php";
include("../config/koneksi.php");
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Kirim Ulang File Laporan</h1>
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

								$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					            $ambil=$koneksi->prepare("SELECT * FROM laporan where id_laporan=:id_laporan");
					            $ambil->BindParam(":id_laporan",$idLaporan);
								$ambil->execute();
								$count = $ambil->rowCount();
								$d = $ambil->fetch(PDO::FETCH_ASSOC);
								$displayDate = date('Y-m', strtotime($d['tanggal']));
								?>
								<form action="<?=$url;?>act/kirim-ulang_proses.php" method="POST" enctype="multipart/form-data">
									
									<input type="hidden" name="id_laporan" value="<?php echo $d['id_laporan']?>"></input>
									<input type="hidden" name="id_notaris" value="<?php echo $d['id_notaris']?>"></input>

									<div class="col-md-12">
		                                <label>Periode Laporan</label>
		                                <input type="month" name="tanggal_laporan" class="form-control" readonly="true" value="<?php echo $displayDate?>" required/>
		                                <br>
		                            </div>

		                            <div class="col-md-12">
		                                <label>Jumlah Buku Daftar Akta</label>
		                                <input type="number" name="jml_buku_daftar" class="form-control" value="<?php echo $d['jml_buku_daftar']?>" required/>
		                                <br>
		                            </div>

		                            <div class="col-md-12">
		                                <label>Jumlah Buku Surat Di Bawah Tangan Yang Dibukukan</label>
		                                <input type="number" name="jml_tangan_dibukukan" class="form-control" value="<?php echo $d['jml_tangan_dibukukan']?>" required/>
		                                <br>
		                            </div>

		                            <div class="col-md-12">
		                                <label>Jumlah Buku Surat Di Bawah Tangan Yang Disahkan</label>
		                                <input type="number" name="jml_tangan_disahkan" class="form-control" value="<?php echo $d['jml_tangan_disahkan']?>" required/>
		                                <br>
		                            </div>

		                            <div class="col-md-12">
		                                <label>Jumlah Buku Protes</label>
		                                <input type="number" name="jml_buku_protes" class="form-control" value="<?php echo $d['jml_buku_protes']?>" required/>
		                                <br>
		                            </div>

		                            <div class="col-md-12">
		                                <label>Unggah File</label><br>
		                                Ketentuan File :<br>
		                                1. Format file dalam bentuk .pdf<br>
		                                2. Maksimal ukuran file 5 MB<br>
		                                <input type="file" name="file" class="form-control" accept="application/pdf" required/>
		                                <br>
		                            </div>
		                            <div class="col-md-12">
		                                <input type="checkbox" id="terms_and_conditions" value="1" onclick="terms_changed(this)" />
		                                <label for="terms_and_conditions" style="color: red">Pastikan data Anda benar dan dapat dipertanggungjawabkan sesuai dengan perundang - undangan yang berlaku</label>
		                             </div>
		                             <br><br>
		                             <div class="col-md-12">
									     <input type="submit" name="submit" id="submit" class="btn btn-success " value="Simpan" disabled>
								     </div>
								</form>                  
								<?php  
								$koneksi = null;
							    ?>
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