<?php
include "header.php";
date_default_timezone_Set('Asia/Jakarta');
?>

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

<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-head-line" align="center">PANDUAN PENGGUNA <br> PENDAFTARAN PELANTIKAN ONLINE KANWIL KUMHAM JABAR</h2>
            </div>
        </div>
        <body style="background-color: #E2E2E2;">
        <div class="row">

            <!-- Mendaftarkan Akun -->
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

            <!-- Aktivasi Akun -->
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

            <!-- Panduan Laporan Fidusia -->
            <div class="d">
                <input type="checkbox" id="faq-panduan-fidusia">
                <h3><label for="faq-panduan-fidusia">Panduan Pelaporan Fidusia</label></h3>
                <div class="p">

                    <!-- Halaman Utama -->
                    <div class="d" style="margin: 1em;">
                        <input type="checkbox" id="faq-halaman-fidusia">
                        <h3 style="background: #4682B4CC;"><label for="faq-halaman-fidusia">Dashboard Pelaporan Fidusia</label></h3>
                        <div class="p">
                            <p align="justify">Halaman Dashboard Pelaporan Fidusia menyajikan informasi terkait laporan fidusia secara rutin. Di sisi kiri terdapat menu navigasi yang memudahkan akses ke berbagai fitur aplikasi. Sementara di bagian tengah ditampilkan data utama Pelaporan Fidusia, meliputi jumlah laporan bulanan serta ringkasan laporan seperti total laporan dan kategori laporan, yaitu Perubahan, Pendaftaran, Penghapusan, dan Perbaikan.
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

            <!-- Panduan Laporan Bulanan Notaris -->
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

            <!-- Melengkapi Profil -->
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

<?php
include "footer.php";
?>
