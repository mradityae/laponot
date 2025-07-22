<?php
include "header.php";
$kedudukan = $_SESSION["kedudukan"];
?>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line" align="left">Tambah Pengguna</h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                     <!--    Hover Rows  -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <form action="<?=$url;?>act/tambah-superadmin_proses.php" method="POST">
                                    <div class="col-md-12">
                                        <label>Nama</label>
                                        <input type="text" name="nama" class="form-control" required/>
                                        <br>
                                    </div>
                                     <div class="col-md-12">
                                        <label>Jenis Kelamin</label><br>
                                        <input type="radio"  id="laki" name="jeniskelamin" value="Laki - Laki" checked="checked"/>
                                        <label for="laki">Laki - Laki</label>
                                        <br>
                                         <input type="radio"  id="perempuan" name="jeniskelamin" value="Perempuan"/>
                                        <label for="perempuan">Perempuan</label>
                                        <br><br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Tempat Kedudukan</label>
                                        <select name="kedudukan" class="form-control">
                                          <?php
                                              $ambil=$koneksi->prepare("SELECT * FROM kedudukan");
                                              $ambil->execute();
                                              while ($row=$ambil->fetch())
                                              {
                                              ?>
                                                <option value="<?php echo $row['id_kedudukan'];?>"><?php echo $row['nama_kedudukan'];?></option>
                                              <?php
                                              }
                                          $koneksi = null;   
                                          ?>
                                        </select>
                                        <br><br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Nomor SK Pengangkatan</label>
                                        <input type="text" name="skPengangkatan" class="form-control" required/>
                                        <br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Tanggal SK Pengangkatan</label>
                                        <input type="date" name="tglSkPengangkatan" class="form-control" required/>
                                        <br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Nomor Berita Acara Pelantikan</label>
                                        <input type="text" name="baPelantikan" class="form-control" required/>
                                        <br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Tanggal Berita Acara Pelantikan</label>
                                        <input type="date" name="tglBaPelantikan" class="form-control" required/>
                                        <br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Alamat Kantor</label><br>
                                        <textarea name="alamat" rows="4" class="form-control"  required/></textarea>
                                        <br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Telepon/WhatsApp</label>
                                        <input type="text" name="telepon" class="form-control" maxlength="13" onkeypress="return isNumberKey(event)" placeholder="Isikan No Handphone yang terdaftar di Aplikasi WhatsApp" required/>
                                        <br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Hak Akses</label>
                                        <select name="level" class="form-control">
                                                <option value="1">Admin</option>
                                                <option value="2" selected>Publik</option>
                                        </select>
                                        <br><br>
                                    </div>

                                     <div class="col-md-12">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" aria-describedby="emailKet" placeholder="Pastikan Email Anda Aktif" required/>
                                        <small id="emailKet" class="form-text text-muted">
                                         Email akan digunakan ketika Anda Login. Pastikan Email Aktif untuk melakukan aktifasi akun.
                                        </small>
                                        <br><br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Password</label>
                                        <input type="password" name="password" id="psw" class="form-control" maxlength="16"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" onkeydown='check2();' aria-describedby="passwordHelpBlock" required/>
                                        <small id="passwordHelpBlock" class="form-text text-muted">
                                          Password harus mencakup 6 - 16 karakter, mencakup huruf kecil, huruf kapital dan angka dan tidak mengandung spasi ataupun emoji.
                                        </small>
                                        <div id="message">
                                          <h4><b>Validasi : </b></h4>
                                          <h4 id="letter" class="invalid"><b>- Huruf Kecil</b></h4>
                                          <h4 id="capital" class="invalid"><b>- Huruf Kapital</b></h4>
                                          <h4 id="number" class="invalid"><b>- Angka</b></h4>
                                          <h4 id="length" class="invalid"><b>- Minimal 6 Karakter</b></h4>
                                        </div>
                                        <br><br>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Masukkan Kembali Password</label>
                                        <input type="password" name="passwordulang" id="pswulang" class="form-control"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" onkeyup='check();' maxlength="16" required/>
                                        <span id='messagepwd'></span>
                                        <br>
                                   </div>

                                    <div class="col-md-12">
                                        <input type="submit" name="submit" class="btn btn-success " id="sbmt" value="Simpan" disabled>
                                        <input type="reset" class="btn btn-primary " value="Ulang" >
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