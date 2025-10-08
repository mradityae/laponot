<?PHP
    include'../config/koneksi.php'; 
?>
<html lang="en">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PANDUAN PENGGUNAAN APLIKASI LAPORAN NOTARIS</title>

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
    <link rel="icon" href="laponot.ico" type="image/x-icon">
    
    <!-- GOOGLE FONTS-->
    <link href="<?=$url;?>assets/css/fonts.googleapis.css" rel="stylesheet"  /> <!--type='text/css'-->
    <style type="text/css">
    @import url("https://fonts.googleapis.com/css?family=Poppins:400,400i,700");
    *, *::after, *::before{
      margin: 0;
      padding: 0;
      box-sizing:border-box;
    }
    div.c{
      position: relative;
      margin:2em;
    }
    div.d{
      position: relative;
      margin:2em;
    }
    input{
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 100%;
      opacity:0;
      visibility: 0;
    }
    h3{
      background:steelblue;
      color:white;
      padding:1em;
      position: relative;
    }
    label::before{
      content:"";
      display: inline-block;
      border: 15px solid transparent;
      border-left:20px solid white;
    }
    label{
      cursor: pointer;
      position: relative;
      display: flex;
      align-items: center;
    }
    div.p{
      max-height:0px;
      overflow: hidden;
      transition:max-height 0.5s;
      background-color: white;
      box-shadow:0 0 10px 0 rgba(0, 0, 0, 0.2);
    }
    div.p p {
      padding:10px;
      font-size: 18px;
      font-weight: bold;
    }
    input:checked ~ h3 label::before{
      border-left:15px solid transparent;
      border-top:20px solid white;
      margin-top:12px;
      margin-right:10px;
    }
    input:checked ~ h3 ~ div.p{
      max-height:  max-content;
    }
    a{
      color:steelblue;
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
                <h2 class="page-head-line" align="center">PANDUAN PENGGUNA <br> APLIKASI LAPORAN NOTARIS KANWIL KUMHAM JABAR</h2>
            </div>
        </div>
        <body style="background-color: #E2E2E2;">   

        <div class="row">
            <div class="c">
                <input type="checkbox" id="faq-6">
                <h3><label for="faq-6">Mendaftarkan Akun</label></h3>
                <div class="p">
                    <p>1. Untuk mendaftarkan akun pada aplikasi Laporan Notaris Kanwil Kemenkumham Jabar, pada halaman masuk aplikasi <a href="https://kabayanpasti.kemenkumham.go.id/laponot/">Laporan Notaris</a> klik Daftar.
                        <center><img src="../assets/img/panduan/panduan_1.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>

                    <p>2. Selanjutnya akan tampil halaman registrasi untuk melakukan pendaftaran akun. Masukkan seluruh data yang diperlukan.
                    <center><img src="../assets/img/panduan/panduan_2.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>

                    <p align="justify">3. Perlu diperhatikan dalam pengisian form registrasi, pada bagian Email, pastikan email Anda aktif. Email tersebut akan digunakan untuk proses aktivasi akun setelah proses registrasi selesai. Pastikan pula dalam pengisian password sesuai dengan ketentuan dan pengisian captcha yang sesuai dengan yang ditampilkan. Jika seluruh data telah terisi, klik Simpan untuk menyelesaikan proses registrasi.
                        <center><img src="../assets/img/panduan/panduan_3.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>

                    <p align="justify">4. Akan muncul notifikasi bahwa Registrasi akun telah berhasil. Sebelum menggunakan aplikasi dengan akun yang telah terdaftar, Anda diharuskan terlebih dahulu melakukan aktivasi akun. Link aktivasi akan dikirimkan ke email yang Anda Masukkan pada saat registrasi akun. Klik Ok untuk menutup notifikasi. Proses registrasi telah selesai.
                        <center><img src="../assets/img/panduan/panduan_4.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>                   
                </div>
            </div>

            <div class="c">
                <input type="checkbox" id="faq-7">
                <h3><label for="faq-7">Aktivasi Akun</label></h3>
                <div class="p">
                    <p align="justify">1. Untuk melakukan proses aktivasi akun, pastikan bahwa Anda telah berhasil melakukan registrasi/pendaftaran akun sebelumnya pada halaman <a href="https://kabayanpasti.kemenkumham.go.id/laponot/regsitrasi/index">Registrasi Akun</a>                         
                    </p>

                    <p align="justify">2. Cek email masuk dari Laporan Notaris Kanwil Kemenkumham Jabar dengan subject PENDAFTARAN LAPORAN NOTARIS pada email yang Anda masukkan pada saat registrasi akun. Jika tidak terdapat email tersebut pada kotak masuk, cek email pada bagian SPAM. Jika setelah beberapa saat setelah proses registrasi dan email untuk aktivasi akun belum terkirim hubungi bagian layanan pengaduan dan informasi kanwil kumham jabar untuk melakukan proses aktivasi. Pada email aktivasi akun, klik tombol Aktivasi.
                    <center><img src="../assets/img/panduan/panduan_5.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>

                    <p align="justify">3. Akan muncul notifikasi bahwa Akun telah berhasil diaktifkan. Klik OK untuk menutup notifikasi.
                    <center><img src="../assets/img/panduan/panduan_6.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>

                    <p align="justify">4. Selanjutnya Anda akan diarahkan pada halaman login aplikasi Laporan Notaris. Masukkan email dan password untuk dapat menggunakan aplikasi.
                    <center><img src="../assets/img/panduan/panduan_7.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>              
                </div>
            </div>

            <div class="d">
                <input type="checkbox" id="faq-panduan-fidusia">
                <h3><label for="faq-panduan-fidusia">Panduan Pelaporan Fidusia</label></h3>
                <div class="p">

                    <!-- Halaman Utama -->
                    <div class="d" style="margin: 1em;">
                        <input type="checkbox" id="faq-halaman-fidusia">
                        <h3 style="background: #4682B4CC;"><label for="faq-halaman-fidusia">Dashboard Pelaporan Fidusia</label></h3>
                        <div class="p">
                            <p align="justify">Halaman Dashboard Pelaporan Fidusia menyajikan informasi terkait laporan fidusia secara rutin. Di sisi kiri terdapat menu navigasi yang memudahkan akses ke berbagai fitur aplikasi. Sementara di bagian tengah ditampilkan data utama Pelaporan Fidusia, meliputi jumlah laporan bulanan serta ringkasan laporan seperti total laporan dan kategori laporan, yaitu Perubahan, Pendaftaran, Penghapusan, dan Pembatalan.
                                <center><img src="../assets/img/panduan/panduan_15.png" class="img-responsive" width="700" height="400"></center>
                            </p>
                        </div>
                    </div>

                    <!-- Mengunggah Laporan Bulanan -->
                    <div class="c" style="margin: 1em;">
                        <input type="checkbox" id="faq-unggah-fidusia">
                        <h3 style="background: #4682B4CC;"><label for="faq-unggah-fidusia">Mengunggah Pelaporan Fidusia</label></h3>
                        <div class="p">
                            <p align="justify">1. Untuk mengunggah laporan, pada menu di sebelah kiri, klik Unggah Laporan.
                                <center><img src="../assets/img/panduan/panduan_16.png" class="img-responsive" width="700" height="400"></center>
                            </p>
                            <p align="justify">2. Selanjutnya pada Halaman Unggah Laporan Fidusia isikan seluruh data yang diperlukan pada form yang telah disediakan, jika sudah melengkapi klik tombol simpan.
                                <center><img src="../assets/img/panduan/panduan_17.png" class="img-responsive" width="700" height="400"></center>
                            </p>
                        </div>
                    </div>

                    <!-- Melihat Daftar Laporan -->
                    <div class="c" style="margin: 1em;">
                        <input type="checkbox" id="faq-daftar-fidusia">
                        <h3 style="background: #4682B4CC;"><label for="faq-daftar-fidusia">Melihat Daftar Laporan Fidusia</label></h3>
                        <div class="p">
                            <p align="justify">
                                1. Untuk dapat melihat daftar laporan yang telah diunggah, pada menu navigasi di sebelah kiri, klik Menu Daftar Laporan.
                                <center><img src="../assets/img/panduan/panduan_18.png" class="img-responsive" width="700" height="400"></center>
                            </p>
                            <p align="justify">
                                Akan ditampilkan halaman daftar laporan. Pada halaman ini anda dapat melihat laporan-laporan yang telah diunggah beserta informasi jenis laporannya. 
                                 <center><img src="../assets/img/panduan/panduan_19.png" class="img-responsive" width="700" height="400"></center>
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="c">
                <input type="checkbox" id="faq-panduan-laporan">
                <h3><label for="faq-panduan-laporan">Panduan Laporan Bulanan Notaris</label></h3>
                <div class="p">

                    <!-- Halaman Utama -->
                    <div class="c" style="margin: 1em;">
                        <input type="checkbox" id="faq-halaman-utama">
                        <h3 style="background: #4682B4CC;"><label for="faq-halaman-utama">Halaman Utama / Dashboard Aplikasi</label></h3>
                        <div class="p">
                            <p align="justify">1. Halaman Dashboard pada Aplikasi Laporan Notaris akan menampilkan informasi seputar laporan dari notaris yang bersangkutan. Pada sebelah kiri merupakan menu navigasi untuk mengakses beberapa fungsionalitas dari aplikasi. Pada bagian tengah merupakan informasi dari laporan notaris yang terdiri dari Jumlah Laporan dan Status Laporan ( Belum Terverifikasi, Terverifikasi dan Ditolak)
                                <center><img src="../assets/img/panduan/panduan_8.jpg" class="img-responsive" width="700" height="400"></center>
                            </p>
                        </div>
                    </div>

                    <!-- Mengunggah Laporan Bulanan -->
                    <div class="c" style="margin: 1em;">
                        <input type="checkbox" id="faq-unggah">
                        <h3 style="background: #4682B4CC;"><label for="faq-unggah">Mengunggah Laporan Bulanan</label></h3>
                        <div class="p">
                            <p align="justify">1. Untuk mengunggah laporan, pada menu di sebelah kiri, klik Unggah Laporan.
                                <center><img src="../assets/img/panduan/panduan_9.jpg" class="img-responsive" width="700" height="400"></center>
                            </p>
                            <p align="justify">2. Selanjutnya pada halaman Unggah Laporan Bulanan Notaris, isikan seluruh data yang diperlukan pada form yang telah disediakan dan unggah file laporan dalam format file .pdf. Jika sudah dilengkapi klik tombol Simpan.
                                <center><img src="../assets/img/panduan/panduan_10.jpg" class="img-responsive" width="700" height="400"></center>
                            </p>
                        </div>
                    </div>

                    <!-- Melihat Daftar Laporan -->
                    <div class="c" style="margin: 1em;">
                        <input type="checkbox" id="faq-daftar">
                        <h3 style="background: #4682B4CC;"><label for="faq-daftar">Melihat Daftar Laporan</label></h3>
                        <div class="p">
                            <p align="justify">
                                1. Untuk dapat melihat daftar laporan yang telah diunggah, pada menu navigasi di sebelah kiri, klik Menu Daftar Laporan.
                                <center><img src="../assets/img/panduan/panduan_11.jpg" class="img-responsive" width="700" height="400"></center>
                            </p>
                            <p align="justify">
                                2. Akan ditampilkan halaman Daftar Laporan. Pada halaman ini Anda dapat melihat laporan-laporan yang telah diunggah beserta informasi status hasil verifikasi dari admin.
                                 <center><img src="../assets/img/panduan/panduan_12.jpg" class="img-responsive" width="700" height="400"></center>
                            </p>
                        </div>
                    </div>

                </div>
            </div>

             <div class="c">
                <input type="checkbox" id="faq-2">
                <h3><label for="faq-2">Melengkapi Profil</label></h3>
                <div class="p">
                    <p>1. Untuk menggunakan menu Profil dapat diakses melalui menu pada bagian kiri, klik Profil.
                        <center><img src="../assets/img/panduan/panduan_13.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>
                    <p>2. Akan ditampilkan form yang berisikan data notaris. Setelah mengubah data profil, klik Simpan untuk menyimpan perubahan yang telah dilakukan.
                        <center><img src="../assets/img/panduan/panduan_14.jpg" class="img-responsive" width="700" height="400"></center>
                    </p>
                </div>
            </div>
        </div>
    </div>
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