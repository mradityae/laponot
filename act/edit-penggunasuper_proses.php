<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 0))
{	
	include '../config/koneksi.php';
	include '../models/models.php';
	try
	{
		$id_notaris 		= RemoveSpecialChar($_POST['id_notaris']);
		$nama 				= RemoveSpecialChar($_POST['nama']);
		$jenis_kelamin   	= RemoveSpecialChar($_POST['jeniskelamin']);
		$id_kedudukan       = RemoveSpecialChar($_POST['kedudukan']);
		$sk 				= RemoveSpecialChar($_POST['sk']);
		$tanggal_sk 		= RemoveSpecialChar($_POST['tgl_sk']);
		$no_ba_pelantikan   = RemoveSpecialChar($_POST['ba']);
		$tgl_ba_pelantikan  = RemoveSpecialChar($_POST['tgl_ba']);
		$alamat       		= RemoveSpecialChar($_POST['alamat']);
		$telepon			= RemoveSpecialChar($_POST['telepon']);
		$level       		= RemoveSpecialChar($_POST['level']);
		$aktif       		= RemoveSpecialChar($_POST['aktif']);
		$email          	= RemoveSpecialChar($_POST['email']);
		$password       	= RemoveSpecialChar($_POST['password']);
		$passwordulang  	= RemoveSpecialChar($_POST['passwordulang']);
		$nik 				= RemoveSpecialChar($_POST['nik']);
		

		//CEK APAKAN PASSWORD DAN MASUKKAN KEMBALI PASSWORD KOSONG
		if (($password == null || $password == "") || ($passwordulang == null || $passwordulang == "")) 
		{	//CEK APAKAH EMAIL BARU KOSONG
			
			if (ubahNotarisSuper($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $aktif, $level, $id_kedudukan, 0, $nik)) 
			{	//KONDISI BERHASIL MENGUBAH DATA
				echo "<script>alert('Data Notaris Berhasil Diubah')</script>";
				$link = $url."superadmin/daftar_pengguna_super";
				header("refresh:0.1; url=$link");
			}
			else
			{	//KONDISI GAGAL MENGUBAH DATA
				echo "<script>alert('Gagal untuk mengubah data Notaris')</script>";
				$link = $url."superadmin/daftar_pengguna_super";
				header("refresh:0.1; url=$link");
			}
		}
		else // KONDISI JIKA USER MENGGANTI PASSWORD
		{	//CEK APAKAH PASSWORD DAN PASSWORD ULANG SUDAH SAMA UNTUK MEMASTIKAN
			if ($password != $passwordulang) 
			{	//KONDISI TIDAK SAMA GAGAL
				echo "<script>alert('PASSWORD TIDAK SAMA !')</script>";
				$link = $url."superadmin/daftar_pengguna_super";
				header("refresh:0.1; url=$link");
			}
			else
			{	
				if (ubahNotarisSuper($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $aktif, $level, $id_kedudukan, $password, $nik)) 
				{	//BERHASIL
					echo "<script>alert('Data Notaris Berhasil Diubah')</script>";
					$link = $url."superadmin/daftar_pengguna_super";
					header("refresh:0.1; url=$link");
				}
				else
				{	//GAGAL
					echo "<script>alert('Gagal untuk mengubah data Notaris')</script>";
					$link = $url."superadmin/daftar_pengguna_super";
					header("refresh:0.1; url=$link");
				}
					
			}
		}
	}
	catch(Exception $Ex)
	{
		echo "Soemthing Wrong. ".$Ex;
		$link = $url."superadmin/daftar_pengguna_super";
		header("refresh:0.1; url=$link");
	}
}
else
{
	echo "WRONG ACCESS";
        $link = "https://kabayanpasti.kemenkumham.go.id";
        header("refresh:0.1; $link");	
}

?>