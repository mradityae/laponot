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
    if(!isset($_SESSION['email'])) 
    {
        session_destroy();
        //$link = $url;
        //header("refresh:0.1; url=$link");
        header("Location:../");
    }
    else if($_SESSION['user_role'] != 2)
    {
       session_destroy();
        //$link = $url;
        //header("refresh:0.1; url=$link");
        header("Location:../");
    } 
    else {
        $id = $_SESSION["kode_user"];
    ?>
<html lang="en">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPORAN NOTARIS</title>

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
    <script src="<?=$url;?>assets/echart/echarts.js"></script>
    
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
			  <?php date_default_timezone_set('Asia/Jakarta');
				$tanggal=date('d-M-Y'); echo $tanggal ; echo " | ";?> <span class="jam"></span>
               <!--  <a href="<?=$url;?>login/login_prc.php?prc=3" class="btn btn-primary" title="Logout">KELUAR</a> -->
            </div>
        </nav>
        <!-- /. NAV TOP  -->

        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li>
                        <div class="user-img-div">
                            <center>
                            <?php
                                $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $ambil=$koneksi->prepare("SELECT photo FROM notaris WHERE id_notaris=:isiId");
                                $ambil->BindParam(":isiId",$id);
                                $ambil->execute();

                                $result = $ambil->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <img src="<?php echo $result['photo']?>" style="border-radius: 100%;" width="145" height="145"/>
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
                        <a href=""><i class="fa fa-dashboard"></i>Dashboard<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level ">
                            <li>
                                <a href="index"><i class="fa fa-file-text-o"></i>Laporan Notaris</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-file-text-o"></i>Daftar Laporan <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="#"><i class="fa fa-file-text-o"></i>Daftar Laporan Bulanan <span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="daftar_laporan.php"><i class="fa fa-file-text-o"></i>Semua</a>
                                    </li>
                                    <li>
                                        <a href="daftar_laporan.php?status=Laporan%20Terkirim"><i class="fa fa-file-text-o"></i>Laporan Terkirim</a>
                                    </li>
                                    <li>
                                        <a href="daftar_laporan.php?status=Terverifikasi"><i class="fa fa-file-text-o"></i>Laporan Terverifikasi</a>
                                    </li>
                                    <li>
                                        <a href="daftar_laporan.php?status=Ditolak"><i class="fa fa-file-text-o"></i>Laporan Ditolak</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="daftar_laporan_entitas"><i class="fa fa-file-text-o"></i>Laporan Fidusia</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="unggah_laporan"><i class="fa fa-book"></i>Unggah Laporan</a>
                    </li>
                    <li>
                        <a href="dashboard_fidusia"><i class="fa fa-book"></i>Rekap Laporan Fidusia</a>
                    </li>
                    <li>
                        <a href="profil"><i class="fa fa-user "></i>Profil</a>
                    </li>
                    <li>
                        <a href="panduan"><i class="fa fa-bookmark"></i>Panduan</a>
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
