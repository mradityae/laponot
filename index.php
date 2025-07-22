<?PHP
    include "config/koneksi.php";
    session_save_path('login/session');
    session_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-74C9ZJMN0B"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-74C9ZJMN0B');
    </script>
	<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>LAPORAN NOTARIS</title>
    <meta name="description" content="Aplikasi Untuk Pengiriman Laporan Notaris"/>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="laponot.ico" type="image/x-icon">

    <!-- Custom CSS -->
    <link href="login/dist/css/style.css" rel="stylesheet" type="text/css">

</head>
<body>
	<!-- Preloader -->
    <div class="preloader-it">
        <div class="loader-pendulums"></div>
    </div>
    <!-- /Preloader -->

	<!-- HK Wrapper -->
	<div class="hk-wrapper">

        <!-- Main Content -->
        <div class="hk-pg-wrapper hk-auth-wrapper">
            <header class="d-flex justify-content-between align-items-center">
                <a class="d-flex auth-brand" href="<?php echo $url_root;?>">
                    <img class="brand-img" src="../images/icon/Kabayan_Pasti_logo.png" alt="brand" height="40" />
                </a>
                <!-- <div class="btn-group btn-group-sm">
                    <a href="#" class="btn btn-outline-secondary">Help</a>
                    <a href="#" class="btn btn-outline-secondary">About Us</a>
                </div> -->
            </header>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-5 pa-0">
                        <div id="owl_demo_1" class="owl-carousel dots-on-item owl-theme">
                            <div class="fadeOut item auth-cover-img overlay-wrap" style="background-image:url(login/dist/img/notaris1.jpg);">
                                <div class="auth-cover-info py-xl-0 pt-100 pb-50">
                                    <div class="auth-cover-content text-center w-xxl-75 w-sm-90 w-xs-100">
                                        <h1 class="display-3 text-white mb-20">Laporan Notaris</h1>
                                        <p class="text-white">Untuk Notaris Wilayah Jawa Barat melaporkan kegiatannya</p>
                                    </div>
                                </div>
                                <div class="bg-overlay bg-trans-dark-50"></div>
                            </div>
                            <div class="fadeOut item auth-cover-img overlay-wrap" style="background-image:url(login/dist/img/notaris2.jpg);">
                                <div class="auth-cover-info py-xl-0 pt-100 pb-50">
                                    <div class="auth-cover-content text-center w-xxl-75 w-sm-90 w-xs-100">
                                        <h1 class="display-3 text-white mb-20">Pengawasan Notaris</h1>
                                        <p class="text-white">Laporan Kegiatan Notaris dalam rangka pengawasan oleh Majelis Pengawas Daerah</p>
                                    </div>
                                </div>
                                <div class="bg-overlay bg-trans-dark-50"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 pa-0">
                        <div class="auth-form-wrap py-xl-0 py-50">
                            <div class="auth-form w-xxl-55 w-xl-75 w-sm-90 w-xs-100">
                                <form action="login/login_prc.php" method="POST">
                                    <h1 class="display-4 mb-10">Selamat Datang :)</h1>
                                    <p class="mb-30">Silahkan Login Untuk Dapat Menggunakan Fitur Pada Aplikasi Ini</p>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Email" type="email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="Password" type="password" name="password" id="password" required>
                                           <!--  <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="feather-icon">
                                                        <i data-feather="eye-off"></i>
                                                    </span>
                                                </span>
                                            </div> -->
                                        </div>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-25">
                                        <input class="custom-control-input" id="same-address" type="checkbox" onclick="showPassword()">
                                        <label class="custom-control-label font-14" for="same-address">Lihat Password</label>
                                        <p class="text-center">Lupa Password ? <a href="<?=$url;?>register/lupapassword">UBAH PASSWORD</a></p>
                                    </div>
                                    <button class="btn btn-primary btn-block" type="submit">Login</button>
                                    <br>

                                    <p class="text-center">Belum mempunyai akun ? <a href="<?=$url;?>register/index">DAFTAR</a></p>
                                    <br>
                                    <p class="text-center"><a href="<?=$url;?>register/panduan">PANDUAN PENGGUNAAN APLIKASI</a></p>
                                    <br>
                                    <p class="text-center"><a href="<?=$url_root;?>">KEMBALI KE HALAMAN UTAMA</a></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Main Content -->

    </div>
	<!-- /HK Wrapper -->

    <!-- jQuery -->
    <script type="text/javascript">
        function showPassword(){
            var x = document.getElementById("password");
            if (x.type == "password") {
                x.type ="text";
            }
            else{
                x.type = "password";
            }
        }
    </script>
    <!-- <script src="login/vendors/jquery/dist/jquery.min.js"></script> -->
    <script src="login/vendors/jquery/dist/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="login/vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="login/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="login/dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="login/dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Owl JavaScript -->
    <script src="login/vendors/owl.carousel/dist/owl.carousel.min.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="login/dist/js/feather.min.js"></script>

    <!-- Init JavaScript -->
    <script src="login/dist/js/init.js"></script>
    <script src="login/dist/js/login-data.js"></script>
</body>
</html>
