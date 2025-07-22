<?php
include "header.php";
date_default_timezone_Set('Asia/Jakarta');
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line"><center>Unggah Laporan Bulanan Notaris</center></h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                 <!--    Hover Rows  -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                        <form action="<?=$url;?>act/unggah-laporan_proses.php" method="POST" enctype="multipart/form-data"> 
                            <div class="col-md-12">
                                <input type="hidden" name="id" class="form-control"  value="<?php echo $_SESSION['kode_user'] ?>" readonly/>
                                <br>
                            </div>

                            <div class="col-md-10">
                                <div class="col-md-4">
                                    <label>Periode Laporan</label>
                                    <input type="date" name="tanggal_laporan" class="form-control" required/>
                                </div>
                                <div class="col-md-6">
                                     <small id="emailKet" class="form-text text-muted" style="color: red">
                                        Untuk mengisi periode laporan, jika menggunakan browser Google Chrome, klik icon kalender pada bagian pojok kanan tempat mengisi periode laporan lalu pilih bulan dan tanggal dari kalender yang muncul. Jika menggunakan browser Firefox, klik pada tempat mengisi periode, lalu pilih tanggal dan bulan dari kalender yang muncul.
                                    </small>    
                                </div>
                                 <br>   
                            </div>

                            <div class="col-md-12">
                                <label>Jumlah Akta Daftar Akta</label>
                                <input type="number" name="jml_buku_daftar" class="form-control" required/>
                                <br>
                            </div>

                            <div class="col-md-12">
                                <label>Jumlah Akta Surat Di Bawah Tangan Yang Dibukukan</label>
                                <input type="number" name="jml_tangan_dibukukan" class="form-control" required/>
                                <br>
                            </div>

                            <div class="col-md-12">
                                <label>Jumlah Akta Surat Di Bawah Tangan Yang Disahkan</label>
                                <input type="number" name="jml_tangan_disahkan" class="form-control" required/>
                                <br>
                            </div>

                            <div class="col-md-12">
                                <label>Jumlah Akta Protes</label>
                                <input type="number" name="jml_buku_protes" class="form-control" required/>
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
