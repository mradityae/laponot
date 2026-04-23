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

    <div class="modal fade" id="modalFAQ" tabindex="-1" role="dialog" aria-labelledby="modalFAQLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h4 class="modal-title text-white font-weight-bold" id="modalFAQLabel">
                    <i class="feather-icon mr-10"><i data-feather="info"></i></i> 
                    Informasi Penting Pelaporan Notaris
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card border-0 mb-2 shadow-sm" style="border-radius: 10px;">
                <div class="card-header bg-white border-0" id="headingThree">
                    <button class="btn btn-link btn-block text-left text-dark font-weight-bold py-3 collapsed d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseThree" style="font-size: 1.2rem;">
                        <span><i class="feather-icon mr-10 text-primary"><i data-feather="play-circle"></i></i> Tutorial Penggunaan Aplikasi</span>
                        <i class="feather-icon font-12"><i data-feather="chevron-down"></i></i>
                    </button>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionFAQ">
                    <div class="card-body pt-0 text-dark text-center" style="font-size: 1.1rem;">
                        <p class="mb-3">Masih bingung cara menggunakan aplikasi?</p>
                        <a target="_blank" href="<?=$url;?>register/panduan" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                            Lihat Panduan Pelaporan
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="modal-body bg-light p-4">
                <div class="accordion shadow-sm" id="accordionFAQ">
                    
                    <div class="card border-0 mb-2 shadow-sm" style="border-radius: 10px;">
                        <div class="card-header bg-white border-0" id="headingOne">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold py-3 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" style="font-size: 1.2rem;">
                                <span><i class="feather-icon mr-10 text-primary"><i data-feather="calendar"></i></i> Ketentuan Periode Laporan</span>
                                <i class="feather-icon font-12"><i data-feather="chevron-down"></i></i>
                            </button>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionFAQ">
                            <div class="card-body pt-0 text-dark" style="font-size: 1.1rem; line-height: 1.6;">
                                <p>Punten Bapak/Ibu Notaris, mohon diperhatikan bahwa <strong>Periode Laporan</strong> yang dipilih pada aplikasi adalah <strong>bulan kegiatan/isi laporan</strong>, bukan bulan saat penginputan.</p>
                                <div class="bg-white p-3 rounded mt-2 border-left border-primary shadow-sm" style="border-left-width: 6px !important;">
                                    <em class="text-primary">Contoh: Laporan untuk kegiatan bulan <strong>Maret</strong> tetap memilih periode <strong>Maret</strong>, meskipun diinput pada bulan April.</em>
                                </div>
                                <p class="mt-3 text-danger font-weight-bold" style="font-size: 1.15rem;">
                                    <i data-feather="alert-circle"></i> Batas maksimal penyampaian laporan adalah tanggal 15 setiap bulannya.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 mb-2 shadow-sm" style="border-radius: 10px;">
                        <div class="card-header bg-white border-0" id="headingTwo">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold py-3 collapsed d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseTwo" style="font-size: 1.2rem;">
                                <span><i class="feather-icon mr-10 text-primary"><i data-feather="file-text"></i></i> Lampiran Laporan Fidusia</span>
                                <i class="feather-icon font-12"><i data-feather="chevron-down"></i></i>
                            </button>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionFAQ">
                            <div class="card-body pt-0 text-dark" style="font-size: 1.1rem;">
                                <p>Bapak/Ibu Notaris tetap berkewajiban untuk <strong>melampirkan laporan fidusia</strong> ke dalam berkas laporan bulanan secara lengkap guna pemenuhan tertib administrasi pelaporan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light justify-content-center pb-30">
                <button type="button" class="btn btn-primary btn-lg px-5 shadow-lg" data-dismiss="modal" style="border-radius: 8px; min-width: 200px;">SAYA MENGERTI</button>
            </div>
        </div>
    </div>
</div>

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
    <script type="text/javascript">
        $(document).ready(function() {
            // Memunculkan modal dengan ID modalFAQ secara otomatis
            $('#modalFAQ').modal('show');
        });
</script>
</body>
</html>
