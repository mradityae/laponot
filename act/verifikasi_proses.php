<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 1))
{

	include '../models/models.php';
	include '../config/koneksi.php';

	try
	{
		
		$id_laporan 		= RemoveSpecialChar($_POST['id_laporan']);
		$status 			= RemoveSpecialChar($_POST['status']);
		$keterangan   		= RemoveSpecialChar($_POST['keterangan']);

		
		if (verifikasiLaporan($koneksi, $id_laporan, $status, $keterangan)) 
		{
			echo "<script>alert('Laporan Berhasil Terverifikasi')</script>";
			$link = $url."admin/daftar_laporan.php";
			header("refresh:0.1; url=$link");
		}
		else{
			echo "<script>alert('Laporan Gagal Terverifikasi')</script>";
			$link = $url."admin/daftar_laporan.php";
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

?>