<?php
session_save_path('../login/session');
session_start();
if(isset($_GET['idlaporan']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 1)) 
{
    include '../config/koneksi.php';
    include '../models/models.php';

    try
    {
        if ($_GET['idlaporan'] != null) 
        {       
            $id_laporan = $_GET['idlaporan'];
            if (deleteLaporan($koneksi, $id_laporan)) 
            {
                echo "<script>alert('Data berhasil dihapus !')</script>";
                $link = $url."admin/daftar_laporan";
                header("refresh:0.1; url=$link");
            } 
            else 
            {
                echo "<script>alert('Data gagal dihapus !')</script>";
                $link = $url."admin/daftar_laporan";
                header("refresh:0.1; url=$link");
            }
        } 
        else 
        {
            $link = $url."admin/daftar_laporan";
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