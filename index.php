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
    <link class="shortcut icon" href="favicon.ico">
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

    <!-- MODAL PENGUMUMAN WAJIB PMPJ -->
    <div class="modal fade" id="modalFAQ" tabindex="-1" role="dialog" aria-labelledby="modalFAQLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                
                <!-- Header Modal -->
                <div class="modal-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h4 class="modal-title text-white font-weight-bold d-flex align-items-center" id="modalFAQLabel">
                        <i class="feather-icon mr-10"><i data-feather="file-text"></i></i> 
                        PEMBERITAHUAN RESMI
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body bg-light p-4" style="max-height: 70vh; overflow-y: auto;">
                    
                    <!-- BANNER UTAMA: PEMBERITAHUAN PMPJ JAWA BARAT -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-body p-4 bg-white text-dark">
                            <div class="text-center mb-3">
                                <span class="badge badge-soft-primary px-3 py-2 font-13 font-weight-700 mb-2 text-uppercase tracking-wider">
                                    Kantor Wilayah Jawa Barat
                                </span>
                                <h3 class="font-weight-bold text-dark font-22 mb-10">
                                    Pemberitahuan Pengisian Kuesioner PMPJ<br>Bagi Seluruh Notaris di Wilayah Jawa Barat
                                </h3>
                                <hr class="hr-light w-25 my-3">
                            </div>
                            
                            <p class="text-dark font-15 text-justify mb-25" style="line-height: 1.7;">
                                Dalam rangka mendukung pelaksanaan audit pengawasan PMPJ tahun 2026, 
                                <strong>seluruh Notaris diwajibkan</strong> melakukan pengisian kuesioner PMPJ 
                                sebagai bagian dari pemetaan tingkat risiko dan pelaksanaan pengawasan berbasis risiko.
                            </p>

                            <!-- Kotak Deadline & Link Form -->
                            <div class="p-4 bg-light rounded border border-gray-300 mb-15">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center mb-3 mb-md-0 border-right border-gray-300">
                                        <span class="d-block font-12 font-weight-600 text-muted text-uppercase tracking-wide">Batas Waktu Pengisian</span>
                                        <h4 class="text-danger font-weight-bold font-20 mt-1 mb-0">1 Agustus 2026</h4>
                                    </div>
                                    <div class="col-md-8 text-center pl-md-4">
                                        <p class="font-14 text-dark font-weight-500 mb-3">Silakan akses formulir melalui tautan di bawah ini:</p>
                                        <!-- Perubahan Ukuran dan Teks Tombol -->
                                        <a href="https://docs.google.com/forms/d/e/1FAIpQLScWj4gtPVXwen8C1_k1a9wlj6c4yb68XNj2zqQK2NH2EBuCMw/viewform" 
                                           target="_blank" 
                                           class="btn btn-primary btn-lg btn-block rounded-pill px-4 py-3 font-weight-bold text-white shadow-md font-16">
                                            <i class="feather-icon mr-2"><i data-feather="external-link"></i></i> Klik Ini Untuk Mengisi Kuesioner PMPJ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DROPDOWN PANDUAN DAN FAQ DI BAGIAN BAWAH -->
                    <div class="accordion shadow-sm" id="accordionFAQ">
                        
                        <!-- Dropdown 1: Ketentuan Sanksi Kelalaian -->
                        <div class="card border-0 mb-2 shadow-sm" style="border-radius: 10px;">
                            <div class="card-header bg-white border-0" id="headingSanksi">
                                <button class="btn btn-link btn-block text-left text-dark font-weight-bold py-3 d-flex justify-content-between align-items-center shadow-none" 
                                        type="button" data-toggle="collapse" data-target="#collapseSanksi" aria-expanded="false" style="font-size: 1.1rem;">
                                    <span><i class="feather-icon mr-10 text-danger"><i data-feather="alert-triangle"></i></i> Ketentuan Sanksi & Konsekuensi</span>
                                    <i class="feather-icon font-12"><i data-feather="chevron-down"></i></i>
                                </button>
                            </div>
                            <div id="collapseSanksi" class="collapse" aria-labelledby="headingSanksi" data-parent="#accordionFAQ">
                                <div class="card-body pt-0 text-dark font-14" style="line-height: 1.6;">
                                    <p class="mb-2">
                                        Notaris yang tidak melakukan pengisian kuesioner PMPJ sampai batas waktu yang ditentukan akan diajukan ke Direktorat Jenderal Administrasi Hukum Umum untuk mendapatkan sanksi administratif berupa <strong>Pemblokiran Akun AHU Online</strong>.
                                    </p>
                                    <p class="font-weight-600 text-muted font-13 mb-0">
                                        * Sesuai ketentuan Pasal 34 Peraturan Menteri Hukum Nomor 10 Tahun 2026 tentang PMPJ bagi Notaris.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown 2: Ketentuan Periode Bulanan Rutin -->
                        <!-- <div class="card border-0 mb-2 shadow-sm" style="border-radius: 10px;">
                            <div class="card-header bg-white border-0" id="headingOne">
                                <button class="btn btn-link btn-block text-left text-dark font-weight-bold py-3 d-flex justify-content-between align-items-center shadow-none" 
                                        type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" style="font-size: 1.1rem;">
                                    <span><i class="feather-icon mr-10 text-primary"><i data-feather="calendar"></i></i> Ketentuan Periode Laporan Bulanan</span>
                                    <i class="feather-icon font-12"><i data-feather="chevron-down"></i></i>
                                </button>
                            </div>
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionFAQ">
                                <div class="card-body pt-0 text-dark font-14" style="line-height: 1.6;">
                                    <p>
                                        Mohon diperhatikan kembali untuk pelaporan bulanan rutin, opsi <strong>Periode Laporan</strong> pada aplikasi harus disesuaikan dengan <strong>bulan pelaksanaan kegiatan</strong>, bukan waktu penginputan.
                                    </p>
                                    <div class="bg-light p-3 rounded mt-2 border-left border-primary mb-2">
                                        <em class="text-primary font-13">
                                            Contoh: Laporan kegiatan bulan Maret tetap memilih periode Maret, meskipun diinput pada bulan April.
                                        </em>
                                    </div>
                                    <p class="text-danger font-weight-bold font-13 mb-0">
                                        <i class="feather-icon font-12"><i data-feather="info"></i></i> Batas maksimal penyampaian laporan bulanan rutin adalah tanggal 15 setiap bulannya.
                                    </p>
                                </div>
                            </div>
                        </div> -->

                        <!-- Dropdown 3: Solusi Aplikasi Kendala Login (Kepental) -->
                        <!-- <div class="card border-0 mb-2 shadow-sm" style="border-radius: 10px;">
                            <div class="card-header bg-white border-0" id="headingTwo">
                                <button class="btn btn-link btn-block text-left text-dark font-weight-bold py-3 d-flex justify-content-between align-items-center shadow-none" 
                                        type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" style="font-size: 1.1rem;">
                                    <span><i class="feather-icon mr-10 text-warning"><i data-feather="help-circle"></i></i> Kendala Aplikasi (Sering Keluar / Kepental otomatis)</span>
                                    <i class="feather-icon font-12"><i data-feather="chevron-down"></i></i>
                                </button>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionFAQ">
                                <div class="card-body pt-0 text-dark font-14">
                                    <p class="mb-2">Apabila setelah login aplikasi secara berulang kembali ke halaman login utama, silakan terapkan penanganan berikut:</p>
                                    <ul class="mb-3 pl-20 font-13 text-muted">
                                        <li>Coba gunakan browser alternatif (Google Chrome / Microsoft Edge terbaru).</li>
                                        <li>Gunakan mode penyamaran (Incognito Window).</li>
                                        <li>Bersihkan data histori penjelajahan beserta cache dan cookies browser Anda.</li>
                                    </ul>
                                    <a href="https://support.google.com/accounts/answer/32050?hl=en" target="_blank" class="text-primary font-weight-bold font-13">
                                        Panduan Resmi Pembersihan Cache Browser
                                    </a>
                                </div>
                            </div>
                        </div> -->

                        <!-- Dropdown 4: Panduan Penggunaan Aplikasi -->
                        <!-- <div class="card border-0 mb-2 shadow-sm" style="border-radius: 10px;">
                            <div class="card-header bg-white border-0" id="headingThree">
                                <button class="btn btn-link btn-block text-left text-dark font-weight-bold py-3 d-flex justify-content-between align-items-center shadow-none" 
                                        type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" style="font-size: 1.1rem;">
                                    <span><i class="feather-icon mr-10 text-success"><i data-feather="book-open"></i></i> Dokumen Panduan Penggunaan Aplikasi</span>
                                    <i class="feather-icon font-12"><i data-feather="chevron-down"></i></i>
                                </button>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionFAQ">
                                <div class="card-body pt-0 text-center py-4">
                                    <p class="text-muted mb-3 font-13">Akses panduan lengkap pengoperasian aplikasi Laporan Notaris Kabayan PASTI:</p>
                                    <a href="<?=$url;?>register/panduan" target="_blank" class="btn btn-sm btn-primary rounded-pill px-4 py-2 shadow-sm mr-2 mb-2">
                                        <i class="feather-icon mr-2"><i data-feather="monitor"></i></i> Panduan Online
                                    </a>
                                    <a href="<?=$url;?>assets/templates/tutorial_aplikasi_kabayan_pasti.pdf" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-4 py-2 mb-2">
                                        <i class="feather-icon mr-2"><i data-feather="file-text"></i></i> Unduh PDF
                                    </a>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer border-0 bg-light justify-content-center pb-30">
                    <button type="button" class="btn btn-primary btn-lg px-5 shadow-lg font-weight-bold" data-dismiss="modal" style="border-radius: 8px; min-width: 200px;">SAYA MENGERTI</button>
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
            $('#modalFAQ').modal('show');
        });
    </script>
</body>
</html>