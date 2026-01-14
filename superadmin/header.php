<?PHP
    include'../config/koneksi.php';
    $tgl=date('Y-m-d');
    header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    session_save_path('../login/session');
    session_start();
    if(!isset($_SESSION['email'])) {
        session_destroy();
        $link = $url;
        //header("refresh:0.1; url=$link");
        header("Location:../");
    }
     else if($_SESSION['user_role'] != 0)
    {
        session_destroy();
        $link = $url;
        //header("refresh:0.1; url=$link");
        header("Location:../");
    }
     else 
     {
    ?>
<html lang="en">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPORAN NOTARIS - SUPER ADMIN</title>

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

     <link href="<?=$url;?>assets/css/card.css" rel="stylesheet" />
    <script src="<?=$url;?>assets/echart/echarts.js"></script>
    
    <!-- GOOGLE FONTS-->
    <link href="<?=$url;?>assets/css/fonts.googleapis.css" rel="stylesheet"  /> <!--type='text/css'-->
    <link rel="shortcut icon" href="laponot.ico" type="image/x-icon">
    <noscript>
        <center>
            Mohon aktifkan javascript.
        </center>
    </noscript>
    <script src="<?=$url;?>assets/chart/Chart.bundle.js"></script>
    <script src="<?=$url;?>assets/chart/utils.js"></script>
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
            </div>

            <div class="header-right">
              <?php date_default_timezone_set('Asia/Jakarta');
                $tanggal=date('d-M-Y'); echo $tanggal ; echo " | ";?> <span class="jam"></span>
                
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
                                   <h4>Selamat Datang</h4>
                                   <h5><?php echo $_SESSION['nama'];?></h5>
                                </div>
                            </center>
                        </div>
                    </li>                   
                    <li>
                        <a href="index"><i class="fa fa-dashboard "></i>Dashboard Laporan Notaris <span class="fa arrow"></span></a></a>
                            <ul class="nav nav-second-level ">
                                <li>
                                    <a href="index"><i class="fa fa-bar-chart"></i>Grafik</a>
                                </li>
                                <li>
                                    <a href="index_waktu"><i class="fa fa-calendar"></i>Waktu</a>
                                </li>
                            </ul>
                    </li>
                    <li>
                        <a href="dashboard_fidusia"><i class="fa fa-dashboard "></i>Dashboard Fidusia</a>
                    </li>
                    <li>
                        <a href=""><i class="fa fa-users"></i>Pengguna<span class="fa arrow"></span></a>
                         <ul class="nav nav-second-level ">
                            <li>
                                <a href="daftar_pengguna_super"><i class="fa fa-file-text-o"></i>Daftar</a>
                            </li>
                            <li>
                                <a href="tambah_pengguna_super"><i class="fa fa-plus-square-o "></i>Tambah</a>
                            </li>
                            <!-- <li>
                                <a href="daftar_notaris_migrasi"><i class="fa fa-file-text-o"></i>Data Notaris Hasil Migrasi</a>
                            </li> -->
                        </ul>
                    </li>
                    <li>
                        <a href="index"><i class="fa fa-file-text-o"></i>Fidusia <span class="fa arrow"></span></a></a>
                            <ul class="nav nav-second-level ">
                                <li>
                                    <a href="rekap_data_entitas"><i class="fa fa-file-text-o"></i>Rekap Laporan Fidusia</a>
                                </li>
                                <li>
                                    <a href="daftar_laporan_entitas"><i class="fa fa-file-text-o"></i>Laporan Fidusia Per Wilayah</a>
                                </li>
                                <li>
                                    <a href="rekap_status_fidusia"><i class="fa fa-file-text-o"></i>Status Laporan Fidusia</a>
                                </li>
                                <!-- <li>
                                    <a href="notaris_belum_lapor"><i class="fa fa-bar-chart "></i>Notaris Belum Lapor Fidusia</a>
                                </li> -->
                            </ul>
                    </li>
                    <li>
                        <a href="hasil_survey"><i class="fa fa-bar-chart "></i>Survey</a>
                    </li>
                    <li>
                        <a href="#"  onclick='konfirmasiKeluar()'><i class="fa fa-sign-out"></i>Keluar</a>
                    </li> 
                </ul>
            </div>
        </nav>
        <?PHP
        } 
    ?>
