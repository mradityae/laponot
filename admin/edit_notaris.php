<?php
include "header.php";
$id=$_GET['id'];
$nama=$_GET['nama'];
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Profil Notaris : <?php echo $nama?></h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                     <!--    Hover Rows  -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <a href="daftar_notaris.php" class="btn btn-primary">KEMBALI</a>
                            <div class="container bootstrap snippet">
			    <div class="row">
			      	<?php
		                $ambil=$koneksi->prepare("SELECT * FROM notaris  WHERE id_notaris=:id");
		                $ambil->BindParam(":id",$id,PDO::PARAM_INT);
		                $ambil->execute();
		                $d = $ambil->fetch(PDO::FETCH_ASSOC);
	                ?>
			    	
			    </div>
			    <div class="row">
			  		<div class="col-sm-3"><!--left col-->
			      <div class="text-center">
			        <img src="<?php echo $d['photo']?>" width="250" height="250" class="img-circle" alt="avatar">
			        
			        
				   	
	      		</div></hr><br>

			               
			          <!-- <div class="panel panel-default">
			            <div class="panel-heading">Website <i class="fa fa-link fa-1x"></i></div>
			            <div class="panel-body"><a href="http://bootnipets.com">bootnipets.com</a></div>
			          </div> -->
			          
			          
			         <!--  <ul class="list-group">
			            <li class="list-group-item text-muted">Activity <i class="fa fa-dashboard fa-1x"></i></li>
			            <li class="list-group-item text-right"><span class="pull-left"><strong>Shares</strong></span> 125</li>
			            <li class="list-group-item text-right"><span class="pull-left"><strong>Likes</strong></span> 13</li>
			            <li class="list-group-item text-right"><span class="pull-left"><strong>Posts</strong></span> 37</li>
			            <li class="list-group-item text-right"><span class="pull-left"><strong>Followers</strong></span> 78</li>
			          </ul>  -->
			               
			          <!-- <div class="panel panel-default">
			            <div class="panel-heading">Social Media</div>
			            <div class="panel-body">
			            	<i class="fa fa-facebook fa-2x"></i> <i class="fa fa-github fa-2x"></i> <i class="fa fa-twitter fa-2x"></i> <i class="fa fa-pinterest fa-2x"></i> <i class="fa fa-google-plus fa-2x"></i>
			            </div>
			          </div> -->
			          
			        </div><!--/col-3-->
			    	<div class="col-sm-9">
			          <div class="tab-content">
			            <div class="tab-pane active" id="home">
			                <hr>
			                <form action="<?=$url;?>act/edit-notaris_proses.php" method="POST" enctype="multipart/form-data">
			                      <input type="hidden" name="id_notaris" value="<?php echo $d['id_notaris']?>"></input>

			                      <div class="form-group">
			                          <div class="col-xs-6">
			                              <label for="nama"><h4>Nama</h4></label>
			                              <input type="text" name="nama" class="form-control" value="<?php echo $d['nama']?>" required/>
			                          </div>
			                      </div>
								  <div class="form-group">
									<div class="col-xs-6">
										<label for="nik"><h4>NIK</h4></label>
										<input type="text"
											name="nik"
											id="nik"
											class="form-control"
											value="<?php echo $d['nik']?>"
											maxlength="16"
											onkeypress="return isNumberKey(event)"
											<?php echo ($d['aktif'] == 1) ? 'required' : ''; ?> />
									</div>
								</div>
			                      <div class="form-group">
			                          <div class="col-xs-6">
			                            <label for="email"><h4>Email</h4></label>
			                              <input type="text" name="email" class="form-control" value="<?php echo $d['email']?>" readonly/>
			                          </div>
			                      </div>

			                      <div class="form-group">
			                          
			                          <div class="col-xs-6">
			                              <label for="jeniskelamin"><h4>Jenis Kelamin</h4></label><br>
			                              <input type="radio"   id="laki" name="jeniskelamin" value="Laki - Laki" <?php if($d['jenis_kelamin'] == 'Laki - Laki') echo 'Checked' ?>/>
	                                      <label for="laki">Laki - Laki</label>
	                                        
	                                      <input type="radio"  id="perempuan" name="jeniskelamin" value="Perempuan" <?php if($d['jenis_kelamin'] == 'Perempuan') echo 'Checked' ?>/>
	                                      <label for="perempuan">Perempuan</label>
			                          </div>
			                      </div>
			          
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6">
			                              <label for="telepon"><h4>Telepon/WhatsApp</h4></label>
			                              <input type="text" name="telepon" class="form-control" maxlength="13" onkeypress="return isNumberKey(event)" required placeholder="Isikan No Handphone yang terdaftar di Aplikasi WhatsApp" value="<?php echo $d['telepon']?>"/>
			                          </div>
			                      </div>
			          
			                      <div class="form-group">
			                          <div class="col-xs-6">
			                             <label for="alamat"><h4>Alamat</h4></label>
			                              <input type="text" name="alamat" class="form-control" value="<?php echo $d['alamat']?>" maxlength="200" required/>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6">
			                              <label for="sk"><h4>Nomor SK Pengangkatan</h4></label>
			                              <td><input type="text" name="sk" class="form-control" value="<?php echo $d['sk']?>" required/></td>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6">
			                              <label for="tgl_sk"><h4>Tanggal SK Pengangkatan</h4></label>
			                              <input type="date" name="tgl_sk" class="form-control" value="<?php echo $d['tanggal_sk']?>" required/>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6">
			                              <label for="ba"><h4>Nomor Berita Acara Pelantikan</h4></label>
			                              <input type="text" name="ba" class="form-control" value="<?php echo $d['no_ba_pelantikan']?>" required/>
			                          </div>
			                      </div>
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6">
			                              <label for="tgl_ba"><h4>Tanggal Berita Acara Pelantikan</h4></label>
			                              <input type="date" name="tgl_ba" class="form-control" value="<?php echo $d['tgl_ba_pelantikan']?>" required/>
			                          </div>
			                      </div>								  
								  
			                      <div class="form-group">
			                          
			                          <div class="col-xs-6">
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
			                          
			                          <div class="col-xs-6">
			                            <label for="passwordulang"><h4>Konfirmasi Password</h4></label>
			                               <input type="password" name="passwordulang" id="pswulang" class="form-control"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" onkeyup='check();' maxlength="16" placeholder="Kosongkan jika tidak akan mengubah password"/>
                                            <span id='messagepwd'></span>
			                          </div>
			                      </div>

								  <div class="form-group">			                          
			                          <div class="col-xs-6">
			                              <label for="aktif"><h4>Status Akun</h4></label>
			                              <select name="aktif" class="form-control" onchange="toggleAlasan(this.value)">
												<option value="1" <?php if($d['aktif'] == 1) echo "selected";?>>Aktif</option>
												<option value="0" <?php if($d['aktif'] == 0) echo "selected";?>>Belum Aktif</option>
											</select>
			                          </div>
			                      </div>

								  <div class="form-group" id="alasanBox" style="display: <?php echo ($d['aktif'] == 0) ? 'block' : 'none'; ?>;">
									<div class="col-xs-6">
										<label for="alasan"><h4>Alasan Belum Aktif</h4></label>
										<input type="text" name="alasan" class="form-control"
											value="<?php echo $d['alasan_tidak_aktif']; ?>"
											placeholder="Masukkan alasan"/>
									</div>
								</div>

			                      <div class="form-group">
			                           <div class="col-xs-12">
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

<script>
function toggleAlasan(val){
    var alasanBox = document.getElementById('alasanBox');
    var nik = document.getElementById('nik');

    if(val == '0'){
        alasanBox.style.display = 'block';
        nik.removeAttribute('required');
    } else {
        alasanBox.style.display = 'none';
        nik.setAttribute('required', 'required');
    }
}


// trigger saat pertama load
window.onload = function(){
    var val = document.querySelector('select[name="aktif"]').value;
    toggleAlasan(val);
}
</script>