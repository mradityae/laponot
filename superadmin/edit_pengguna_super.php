<?php
include "header.php";
$id=$_GET['id'];
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Profil Pengguna</h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                     <!--    Hover Rows  -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <a href="daftar_pengguna_super.php" class="btn btn-primary">KEMBALI</a>
                            <br><br>
                            <div class="table-responsive">
                                <?php
								$ambil=$koneksi->prepare("SELECT * FROM notaris WHERE id_notaris=:id");
                                $ambil->BindParam(":id",$id,PDO::PARAM_INT);
                                $ambil->execute();
								while($d=$ambil->fetch()){
								?>
								<form action="<?=$url;?>act/edit-penggunasuper_proses.php" method="POST" enctype="multipart/form-data">
									
									<input type="hidden" name="id_notaris" value="<?php echo $d['id_notaris']?>"></input>
									
									<table class="table table-bordered table-striped">
									<tr>
										<th>Nama</th>
										<td><input type="text" name="nama" class="form-control" value="<?php echo $d['nama']?>" required/></td>
									</tr>
									<tr>
										<th>NIK</th>
										<td>
											<input type="text" name="nik" class="form-control" 
												value="<?php echo $d['nik']?>" 
												maxlength="16" 
												onkeypress="return isNumberKey(event)" 
												required/>
										</td>
									</tr>

									<tr>
										<th>Jenis Kelamin</th>
										<td>
											<input type="radio"  id="laki" name="jeniskelamin" value="Laki - Laki" <?php if($d['jenis_kelamin'] == 'Laki - Laki') echo 'Checked' ?>/>
	                                        <label for="laki">Laki - Laki</label>
	                                        
	                                        <input type="radio"  id="perempuan" name="jeniskelamin" value="Perempuan" <?php if($d['jenis_kelamin'] == 'Perempuan') echo 'Checked' ?>/>
	                                        <label for="perempuan">Perempuan</label>
										</td>
									</tr>

									<tr>
										<th>Kedudukan</th>
										<td>
											<select name="kedudukan" class="form-control">
                                          	<?php
                                              $ambil2=$koneksi->prepare("SELECT * FROM kedudukan");
                                              $ambil2->execute();
                                              while ($row2=$ambil2->fetch())
                                              {
                                              ?>
                                                <option value="<?php echo $row2['id_kedudukan'];?>"  <?php if($d['id_kedudukan'] == $row2['id_kedudukan']) echo "selected";?>><?php echo $row2['nama_kedudukan'];?></option>
                                              <?php
                                              }
                                          	?>
                                        	</select>
										</td>
									</tr>

									<tr>
										<th>Nomor SK Pengangkatan</th>
										<td><input type="text" name="sk" class="form-control" value="<?php echo $d['sk']?>" required/></td>
									</tr>

									<tr>
										<th>Tanggal SK Pengangkatan</th>
										<td><input type="date" name="tgl_sk" class="form-control" value="<?php echo $d['tanggal_sk']?>" required/></td>
									</tr>

									<tr>
										<th>Nomor Berita Acara Pelantikan</th>
										<td><input type="text" name="ba" class="form-control" value="<?php echo $d['no_ba_pelantikan']?>" required/></td>
									</tr>

									<tr>
										<th>Tanggal Berita Acara Pelantikan</th>
										<td><input type="date" name="tgl_ba" class="form-control" value="<?php echo $d['tgl_ba_pelantikan']?>" required/></td>
									</tr>

									<tr>
										<th>Alamat kantor</th>
										<td>
											 <textarea name="alamat" rows="4" cols="106" required/><?php echo $d['alamat']?></textarea>
										</td>
									</tr>

									<tr>
										<th>Telepon/WhatsApp</th>
										 <td><input type="text" name="telepon" class="form-control" maxlength="13" onkeypress="return isNumberKey(event)" required placeholder="Isikan No Handphone yang terdaftar di Aplikasi WhatsApp" value="<?php echo $d['telepon']?>"/></td>
									</tr>

									<tr>
										<th>Hak Akses</th>
										<td>
											<select name="level" class="form-control">
												<option value="0" <?php if($d['level'] == 0) echo "selected";?>>Super Admin</option>
												<option value="1" <?php if($d['level'] == 1) echo "selected";?>>Admin</option>
												<option value="2" <?php if($d['level'] == 2) echo "selected";?>>Publik</option>
											</select>
										</td>
									</tr>

									<tr>
										<th>Status Akun</th>
										<td>
											<select name="aktif" class="form-control">
												<option value="1" <?php if($d['aktif'] == 1) echo "selected";?>>Aktif</option>
												<option value="0" <?php if($d['aktif'] == 0) echo "selected";?>>Belum Aktif</option>
											</select>
										</td>
									</tr>
								
									<tr>
										<th>Email</th>
										<td><input type="text" name="email" class="form-control" value="<?php echo $d['email']?>" readonly/></td>
									</tr>
									
									<tr>
										<th>Password Baru</th>
										<td>
											 <input type="password" name="password" id="psw" class="form-control" maxlength="16"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" onkeydown='check2();' aria-describedby="passwordHelpBlock" placeholder="Kosongkan jika tidak akan mengubah password"/>
	                                        <small id="passwordHelpBlock" class="form-text text-muted">
	                                          Password harus mencakup 6 - 16 karakter, mencakup huruf kecil, huruf kapital dan angka dan tidak mengandung spasi ataupun emoji.
	                                        </small><br>
	                                        <div id="message">
	                                          <h4><b>Validasi : </b></h4>
	                                          <h4 id="letter" class="invalid"><b>- Huruf Kecil</b></h4>
	                                          <h4 id="capital" class="invalid"><b>- Huruf Kapital</b></h4>
	                                          <h4 id="number" class="invalid"><b>- Angka</b></h4>
	                                          <h4 id="length" class="invalid"><b>- Minimal 6 Karakter</b></h4>
	                                        </div>
										</td>
									</tr>
									<tr>
										<th>Ketik Ulang Password Baru</th>
										<td>
											<input type="password" name="passwordulang" id="pswulang" class="form-control"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" onkeyup='check();' maxlength="16" placeholder="Kosongkan jika tidak akan mengubah password"/>
                                        	<span id='messagepwd'></span>
										</td>
									</tr>
									<tr>
										<td colspan="2" align="center">
											<input type="submit" name="submit" class="btn btn-success" value="SIMPAN" >
										</td>
									</tr>
									</table> 
								</form>                  
								<?php  
								}
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