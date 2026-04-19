<?PHP
    include'../config/koneksi.php'; 
?>
<html lang="en">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>REGISTRASI AKUN</title>

    <!-- BOOTSTRAP STYLES-->
    <link href="<?=$url;?>assets/css/bootstrap.css" rel="stylesheet" />
    <link href="<?=$url;?>assets/css/select2.min.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="<?=$url;?>assets/css/font-awesome.css" rel="stylesheet" />
       <!--CUSTOM BASIC STYLES-->
    <link href="<?=$url;?>assets/css/basic.css" rel="stylesheet" />
    <!--CUSTOM MAIN STYLES-->
    <link href="<?=$url;?>assets/css/custom.css" rel="stylesheet" />
    <link href="<?=$url;?>assets/css/jquery.dataTables.min.css" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="../laponot.ico" type="image/x-icon">
    
    <!-- GOOGLE FONTS-->
    <link href="<?=$url;?>assets/css/fonts.googleapis.css" rel="stylesheet"  /> <!--type='text/css'-->
    <style type="text/css">
    /* Style all input fields */
        /* The message box is shown when the user clicks on the password field */
        #message {
          display:none;      
        }
        #message h4 {
          font-size: 12px;
        }
        /* Add a green text color and a checkmark when the requirements are right */
       .valid {
          color: green;
          
        }
        .valid:before {
          position: relative;
        }
        .valid:after {
          position: relative;
          content: ": Benar";
        }
        /* Add a red text color and an "x" icon when the requirements are wrong */
        .invalid {
          color: red;
          
        }
        .invalid:before {
          position: relative;
        }
        .invalid:after{
          position: relative;
          content: ": Salah";
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                </button>
                <!-- <a class="navbar-brand" href="index.php">KANWIL KEMENKUMHAM JABAR</a> -->
            </div>

            <div class="header-right">
              <span class="jam"></span><?php date_default_timezone_set('Asia/Jakarta');
                $tanggal=date('Y-M-d'); echo " | "; echo $tanggal ;?>
                
            </div>
        </nav>
        <!-- /. NAV TOP  -->

        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li>
                        <div class="user-img-div">
                            <center>
                            <img src="../assets/img/logo_kumham.png" style="border-radius: 100%;" class="img-thumbnail"/>
                            </center>
                            <div class="inner-text" style="margin-top: -15px;">
                                <center>   
                               <br/>                             
                             
                            </div>
                        </center>
                        </div>

                    </li>
                    <li>
                        <a  href="<?php echo $url?>"><i class="fa fa-home "></i>Halaman Login</a>
                    </li>
                    <li>
                        <a  href="<?php echo $url.'register/index'?>"><i class="fa fa-user "></i>Registrasi Akun</a>
                    </li>
                    <li>
                        <a  href="<?php echo $url.'register/panduan'?>"><i class="fa fa-bookmark"></i>Panduan</a>
                    </li>
                     <li>
                        <a  href="<?php echo $url.'register/lupapassword'?>"><i class="fa fa-key "></i>Lupa Password</a>
                    </li>
                </ul>
            </div>
        </nav>
    

        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">REGISTRASI AKUN APLIKASI LAPORAN NOTARIS</h1>
                    </div>
                </div>
              <!-- /. ROW  -->
            <div class="row">
                
                     <!--    Hover Rows  -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <form action="<?=$url;?>act/tambah-notaris_proses.php" method="POST">
                                    <div class="col-md-12">
                                        <label>Nama (Tanpa Gelar)</label>
                                        <input type="text" name="nama" class="form-control" required/>
                                        <br>
                                    </div>
                                    <div class="col-md-12">
                                        <label>NIK</label>
                                        <input type="text" name="nik" class="form-control" maxlength="16"
                                            pattern="[0-9]{16}" 
                                            onkeypress="return isNumberKey(event)"
                                            placeholder="Masukkan NIK (16 digit)" required/>
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
                                        <label>Captcha</label><br>
                                        <img src="captcha.php" alt="gambar" width="300" height="200" /> 
                                        <br><BR>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Masukkan Captcha</label>
                                        <input type="text" name="captcha" class="form-control" required placeholder="Isikan kata yang ada pada gambar di atas"/>
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

 <div id="footer-sec">
        &copy; 2020 KANWIL KEMENKUMHAM JABAR 
        <!--  &copy; 2018 SMAN 22 Bandung| Design By : <a href="http://www.binarytheme.com/" target="_blank">BinaryTheme.com</a> -->
    </div>
    <!-- /. FOOTER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script type="text/javascript">
        var check = function() {
          if(document.getElementById('psw').value == document.getElementById('pswulang').value) {
            document.getElementById('messagepwd').style.color = 'green';
            document.getElementById('messagepwd').innerHTML = 'Password dan Masukkan Kembali Password Sama';
            document.getElementById('sbmt').disabled = false;
          } else {
            document.getElementById('messagepwd').style.color = 'red'; 
            document.getElementById('messagepwd').innerHTML = 'Password dan Masukkan Kembali Password Tidak Sama';
            document.getElementById('sbmt').disabled = true;
          }
        }

         var check2 = function() {
           document.getElementById('pswulang').value = '';
           document.getElementById('messagepwd').style.color = 'red'; 
           document.getElementById('messagepwd').innerHTML = 'Password dan Masukkan Kembali Password Tidak Sama';
           document.getElementById('sbmt').disabled = true;
        }

        function isNumberKey(evt){
        var angka=(evt.which)?evt.which:event.keyCode
        if(angka>31 && (angka<48 || angka>57))
        
        return false;
        return true;
    }
    </script>

    <script  type="text/javascript">
        var myInput = document.getElementById("psw");
        var letter = document.getElementById("letter");
        var capital = document.getElementById("capital");
        var number = document.getElementById("number");
        var length = document.getElementById("length");

        // When the user clicks on the password field, show the message box
        myInput.onfocus = function() {
          document.getElementById("message").style.display = "block";
        }

        // When the user clicks outside of the password field, hide the message box
        myInput.onblur = function() {
          document.getElementById("message").style.display = "none";
        }

        // When the user starts to type something inside the password field
        myInput.onkeyup = function() {
          // Validate lowercase letters
          var lowerCaseLetters = /[a-z]/g;
          if(myInput.value.match(lowerCaseLetters)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
          } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
        }

          // Validate capital letters
          var upperCaseLetters = /[A-Z]/g;
          if(myInput.value.match(upperCaseLetters)) {
            capital.classList.remove("invalid");
            capital.classList.add("valid");
          } else {
            capital.classList.remove("valid");
            capital.classList.add("invalid");
          }

          // Validate numbers
          var numbers = /[0-9]/g;
          if(myInput.value.match(numbers)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
          } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
          }

          // Validate length
          if(myInput.value.length >= 6) {
            length.classList.remove("invalid");
            length.classList.add("valid");
          } else {
            length.classList.remove("valid");
            length.classList.add("invalid");
          }
        }
    </script>

    <script src="<?=$url;?>assets/js/jquery.min.js"></script>
    <script src="<?=$url;?>assets/js/select2.full.min.js"></script>
    <script type='text/javascript'>
    $(function () {
      $("select").select2();
    });
    </script>
    <script type="text/javascript">
                                            $(document).ready(function() {
                                              $(".add-more").click(function(){ 
                                                  var html = $(".copy").html();
                                                  $(".after-add-more").after(html);
                                              });
                                              $("body").on("click",".remove",function(){ 
                                                  $(this).parents(".control-group").remove();
                                              });
                                            });
                                        </script>
    <script>
        $(document).ready(function(){
         
         $(document).on('click', '.add', function(){
          var html = '';
          html += '<tr>';
          html += '<td><select name="item_unit[]" class="form-control item_unit"><option value="">Select Unit</option><?php echo $output; ?></select></td>';
          html += '<td><button type="button" name="remove" class="btn btn-danger btn-sm remove"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
          $('#item_table').append(html);
         });
         
         $(document).on('click', '.remove', function(){
          $(this).closest('tr').remove();
         });
         
         
         
        });
        </script>
    
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="<?=$url;?>assets/js/bootstrap.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="<?=$url;?>assets/js/jquery.metisMenu.js"></script>
       <!-- CUSTOM SCRIPTS -->
    <script src="<?=$url;?>assets/js/custom.js"></script>
    <script src="<?=$url;?>assets/js/jquery.dataTables.min.js"></script>
    <script>
        $('form input[type=text], form textarea, form input[type=number]').on('change invalid', function() {
                var textfield = $(this).get(0);
                
                // hapus dulu pesan yang sudah ada
                textfield.setCustomValidity('');
                
                if (!textfield.validity.valid) {
                  textfield.setCustomValidity('Form Tidak Boleh Kosong!');  
                }
            });
            $('table.data').DataTable({
            "ordering": true,
            "autoWidth": true,
            "Searching": true,
            "scrollY": '200vh',
            "scrollCollapse": true
            
        });
        </script>
    
<script type="text/javascript">
    function jam() {
    var time = new Date(),
        hours = time.getHours(),
        minutes = time.getMinutes(),
        seconds = time.getSeconds();
    document.querySelectorAll('.jam')[0].innerHTML = harold(hours) + ":" + harold(minutes) + ":" + harold(seconds);
      
    function harold(standIn) {
        if (standIn < 10) {
          standIn = '0' + standIn
        }
        return standIn;
        }
    }
    setInterval(jam, 1000);
</script>

</body>
</html>