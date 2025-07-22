<?php
include '../config/koneksi.php';
include '../models/models.php';

try
{
    if ($_GET['email'] != null && $_GET['secretCode'] != null) 
    {      
        if (aktivasiAkun($koneksi, $_GET['email'], $_GET['secretCode'])) {
            echo "<script>alert('Akun Anda Telah Berhasil Diaktifkan. Silahkan Login !')</script>";
            $link = $url;
            header("refresh:0.1; url=$link");
        } else {
            echo "<script>alert('Gagal Untuk Aktivasi Akun')</script>";
            $link = $url;
            header("refresh:0.1; url=$link");
        }
    } 
    else 
    {
        echo "WRONG ACCESS";
        $link = "https://kabayanpasti.kemenkumham.go.id";
        header("refresh:0.1; $link");	
    }
}
catch(Exception $ex)
{
    $link = $url;
    header("refresh:0.1; url=$link");
}
