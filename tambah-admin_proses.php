<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 1))
{

	include '../config/koneksi.php';
	include '../models/models.php';

	try
	{
		$nama 				= RemoveSpecialChar($_POST['nama']);
		$jenis_kelamin   	= RemoveSpecialChar($_POST['jeniskelamin']);
		$kedudukan   		= RemoveSpecialChar($_POST['kedudukan']);
		$skPengangkatan   	= RemoveSpecialChar($_POST['skPengangkatan']);
		$tglSkPengangkatan  = RemoveSpecialChar($_POST['tglSkPengangkatan']);
		$baPelantikan   	= RemoveSpecialChar($_POST['baPelantikan']);
		$tglBaPelantikan   	= RemoveSpecialChar($_POST['tglBaPelantikan']);
		$alamat         	= RemoveSpecialChar($_POST['alamat']);
		$telepon        	= RemoveSpecialChar($_POST['telepon']);
		$email          	= RemoveSpecialChar(strtolower($_POST['email']));
		$password       	= RemoveSpecialChar($_POST['password']);
		$passwordulang  	= RemoveSpecialChar($_POST['passwordulang']);

		$fullDirBaru 		= $url.'act/photo/user.png';

		//CEK APAKAH EMAIL TELAH TERDAFTAR DI DATABASE UNTUK DIJADIKAN AKUN
		if(cekEmail($koneksi, $email))
		{
			//TAMBAH DATA KE DATABASE
			if (tambahNotaris($koneksi, $nama, $jenis_kelamin, $kedudukan, $skPengangkatan, $tglSkPengangkatan, $baPelantikan, $tglBaPelantikan ,$alamat, $telepon, $email, $password, 2, 1, $fullDirBaru)) 
			{
				//JIKA BERHASIL MASUK DATABASE, KIRIM EMAIL NOTIFIKASI KEPADA EMAIL PENGGUNA
				echo "<script>alert('Registrasi Berhasil.Silahkan Informasikan Notaris tentang akun yang telah dibuat')</script>";
				$link = $url."admin/daftar_notaris";
				header("refresh:0.1; url=$link");
			}
			//KONDISI JIKA GAGAL MENYIMPAN DATA PENGGUNA KE DATABASE
			else
			{
				echo "<script>alert('Registrasi Gagal. Coba lagi beberapa saat.')</script>";
				$link = $url."admin/daftar_notaris";
				header("refresh:0.1; url=$link");
			}
		}
		//KONDISI JIKA EMAIL TELAH TERDAFTAR.
		else
		{
			echo "<script>alert('EMAIL TELAH TERDAFTAR. TIDAK BISA MEMBUAT AKUN DENGAN EMAIL YANG SAMA')</script>";
			$link = $url."admin/daftar_notaris";
			header("refresh:0.1; url=$link");
		}
	}
	catch(Exception $Ex)
	{
		echo "Something Wrong. ".$Ex;
	}
}
//KONDISI POST SUBMIT BELUM DI ASSIGN
else{
	echo "WRONG ACCESS";
	$link = "https://kabayanpasti.kemenkumham.go.id";
	header("refresh:0.1; $link");	
}

?>