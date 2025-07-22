<?php
include "header.php";
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Profil Notaris</h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="container bootstrap snippet">
			    <div class="row">
			      	<?php
		                $id=$_SESSION['kode_user'];
		                $ambil=$koneksi->prepare("SELECT * FROM notaris join kedudukan on notaris.id_kedudukan = kedudukan.id_kedudukan WHERE id_notaris=:id");
		                $ambil->BindParam(":id",$id,PDO::PARAM_INT);
		                $ambil->execute();
		                $d = $ambil->fetch(PDO::FETCH_ASSOC);
	                ?>
			    	
			    </div>
			    <div class="row">
			  		<div class="col-sm-3"><!--left col-->
					  <div class="text-center">
				            <img src="<?php if($d['photo'] != null){echo $d['photo'];}else{echo "../act/photo/user.png";}?>" width="250" height="250" class="img-circle" alt="avatar">
			                <h4><b>UBAH FOTO</b></h4>
                        <form action="<?=$url;?>act/edit-profil_proses.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="file" id="file" class="text-center center-block file-upload" accept=".jpg, .jpeg, .png" onchange="readURL(this);"/>
                                Ketentuan File :<br>
                                1. Format file .jpg, .jpeg atau .png<br>
                                2. Maksimal ukuran file 1 MB<br><br>
                                <div id="preview">
                                    <p><b>Pratinjau Gambar</b></p>
                                    <img id="blah" src="#" alt="your image" width="250" height="250" class="img-circle"/><br>
                                </div>                                
                        </div></hr><br>

			        </div><!--/col-3-->
			    	<div class="col-sm-9">
			          <div class="tab-content">
			            <div class="tab-pane active" id="home">
			                <hr>			              
			                      <input type="hidden" name="id_notaris" value="<?php echo $d['id_notaris']?>"></input>

			                      <div class="form-group">
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="nama"><h4>Nama</h4></label>
			                              <input type="text" name="nama" class="form-control" value="<?php echo $d['nama']?>" required/>
			                          </div>

			                          <div class="col-xs-6" style="margin-bottom: 20">
			                            <label for="email"><h4>Email</h4></label>
			                              <input type="text" name="email" class="form-control" value="<?php echo $d['email']?>" readonly/>
			                          </div>
			                      </div>

			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="jeniskelamin"><h4>Jenis Kelamin</h4></label><br>
			                              <input type="radio"   id="laki" name="jeniskelamin" value="Laki - Laki" <?php if($d['jenis_kelamin'] == 'Laki - Laki') echo 'Checked' ?>/>
	                                      <label for="laki">Laki - Laki</label>
	                                        
	                                      <input type="radio"  id="perempuan" name="jeniskelamin" value="Perempuan" <?php if($d['jenis_kelamin'] == 'Perempuan') echo 'Checked' ?>/>
	                                      <label for="perempuan">Perempuan</label>
			                          </div>

			                           <div class="col-xs-6" style="margin-bottom: 20">
			                            <label for="email"><h4>Kedudukan</h4></label>
			                              <input type="text" name="kedudukan" class="form-control" value="<?php echo $d['nama_kedudukan']?>" readonly/>
			                          </div>
			                      </div>

			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="telepon"><h4>Telepon/WhatsApp</h4></label>
			                              <input type="text" name="telepon" class="form-control" maxlength="13" onkeypress="return isNumberKey(event)" required placeholder="Isikan No Handphone yang terdaftar di Aplikasi WhatsApp" value="<?php echo $d['telepon']?>"/>
			                          </div>
			                      </div>
			          
			                      <div class="form-group">
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                             <label for="alamat"><h4>Alamat</h4></label>
			                              <input type="text" name="alamat" class="form-control" value="<?php echo $d['alamat']?>" maxlength="200" required/>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="sk"><h4>Nomor SK Pengangkatan</h4></label>
			                              <td><input type="text" name="sk" class="form-control" value="<?php echo $d['sk']?>" required/></td>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="tgl_sk"><h4>Tanggal SK Pengangkatan</h4></label>
			                              <input type="date" name="tgl_sk" class="form-control" value="<?php echo $d['tanggal_sk']?>" required/>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="ba"><h4>Nomor Berita Acara Pelantikan</h4></label>
			                              <input type="text" name="ba" class="form-control" value="<?php echo $d['no_ba_pelantikan']?>" required/>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="tgl_ba"><h4>Tanggal Berita Acara Pelantikan</h4></label>
			                              <input type="date" name="tgl_ba" class="form-control" value="<?php echo $d['tgl_ba_pelantikan']?>" required/>
			                          </div>
			                      </div>

			                      

			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                              <label for="password"><h4>Password Baru</h4></label>
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
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6" style="margin-bottom: 20">
			                            <label for="password2"><h4>Konfirmasi Password</h4></label>
			                               <input type="password" name="passwordulang" id="pswulang" class="form-control"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" onkeyup='check();' maxlength="16" placeholder="Kosongkan jika tidak akan mengubah password"/>
                                            <span id='messagepwd'></span>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                           <div class="col-xs-12" style="margin-bottom: 20">
			                                <br>
			                              	<button class="btn btn-lg btn-success" type="submit" name="submit"><i class="glyphicon glyphicon-ok-sign"></i> Simpan</button>
			                               	<button class="btn btn-lg" type="reset"><i class="glyphicon glyphicon-repeat"></i> Ulangi</button>
			                            </div>
			                      </div>
	              	</form>
          						<?php  
								
								$koneksi = null;
							    ?>
			          	</div><!--/tab-content-->
			        </div><!--/col-9-->
			    </div><!--/row-->
            </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
<?php
include "footer.php";
?>