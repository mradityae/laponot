<?php
session_save_path('../login/session');
session_start();

if(isset($_GET['idnotaris']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 0)) 
{
    include '../config/koneksi.php';
    include '../models/models.php';

    try
    {
        $idnotaris = $_GET['idnotaris'];

        if (deleteNotaris($koneksi, $idnotaris)) 
        {
            echo "<script>alert('Data berhasil dihapus !')</script>";
            $link = $url."superadmin/daftar_pengguna_super";
            header("refresh:0.1; url=$link");
        } 
        else 
        {
            echo "<script>alert('Data gagal dihapus !')</script>";
            $link = $url."superadmin/daftar_pengguna_super";
            header("refresh:0.1; url=$link");
        }
    }
    catch(Exception $Ex)
    {
        echo "Something Wrong. ".$Ex;
    }
} 
else 
{
    echo "WRONG ACCESS";
        $link = "https://kabayanpasti.kemenkumham.go.id";
        header("refresh:0.1; $link");	
}