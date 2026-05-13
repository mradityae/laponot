<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 2))
{	
	include '../config/koneksi.php';
	include '../models/models.php';
	try
	{
		$id_notaris 		= RemoveSpecialChar($_POST['id_notaris']);
		$nama 				= RemoveSpecialChar($_POST['nama']);
		$jenis_kelamin   	= RemoveSpecialChar($_POST['jeniskelamin']);
		$sk 				= RemoveSpecialChar($_POST['sk']);
		$tanggal_sk 		= RemoveSpecialChar($_POST['tgl_sk']);
		$no_ba_pelantikan   = RemoveSpecialChar($_POST['ba']);
		$tgl_ba_pelantikan  = RemoveSpecialChar($_POST['tgl_ba']);
		$alamat       		= RemoveSpecialChar($_POST['alamat']);
		$telepon			= RemoveSpecialChar($_POST['telepon']);
		$email          	= RemoveSpecialChar($_POST['email']);
		$password       	= RemoveSpecialChar($_POST['password']);
		$passwordulang  	= RemoveSpecialChar($_POST['passwordulang']);
		$nik 			    = RemoveSpecialChar($_POST['nik']);
		$id_kedudukan = RemoveSpecialChar($_POST['id_kedudukan']);
		$file 				= $_POST['file'];

		$namaFile = $_FILES['file']['name'];
		$x = explode('.',$namaFile);
		$ekstensi = strtolower(end($x));
		$ukuran = $_FILES['file']['size'];
		$fileTemp = $_FILES['file']['tmp_name'];
		$direktory = 'photo/'.$id_notaris.'/';
		$dirSaveFile = $direktory.$id_notaris.'.'.$ekstensi;
		$fullDirBaru = $url.'act/'.$dirSaveFile;
		

		//CEK APAKAN PASSWORD DAN MASUKKAN KEMBALI PASSWORD KOSONG
		if (($password == null || $password == "") || ($passwordulang == null || $passwordulang == "")) 
		{	//CEK APAKAH EMAIL BARU KOSONG
			
			if($namaFile != null || $namaFile != "")
			{
				if ($ukuran > 1048576) 
				{
					echo "<script>alert('Ukuran File Terlalu Besar. Lebih dari 1 MB')</script>";
					$link = $url."pengguna/profil";
					header("refresh:0.1; url=$link");
				}
				else if(strtolower($ekstensi) != "jpg" && strtolower($ekstensi) != "jpeg" && strtolower($ekstensi) != "png")
				{
					echo "<script>alert('File Tidak Valid. Pastikan file dalam format .jpg, .jpeg ataupun .png !')</script>";
					$link = $url."pengguna/profil";
					header("refresh:0.1; url=$link");
				}
				else
				{
					if (is_dir($direktory) == false) 
					{
						$buatDir = mkdir($direktory,0777,true);
						if ($buatDir == false) 
						{
							echo "<script>alert('Gagal Membuat Direktori Untuk Simpan File')</script>";
							$link = $url."pengguna/profil";
							header("refresh:0.1; url=$link");
						}
					}

					$move = move_uploaded_file($fileTemp, $dirSaveFile);
					if ($move == false) 
					{
						echo "<script>alert('Gagal Menyimpan File')</script>";
						$link = $url."pengguna/profil";
						header("refresh:0.1; url=$link");
					}
					else
					{
						header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
						header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
						header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
						header("Cache-Control: post-check=0, pre-check=0", false);
						header("Pragma: no-cache");
						clearstatcache();

						if (ubahProfil($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, null, $fullDirBaru, $nik, $id_kedudukan)) {
							echo "<script>alert('Profil Berhasil Diubah')</script>";
							$link = $url."pengguna/profil";
							header("refresh:0.1; url=$link");
						}
						else{
							echo "<script>alert('Gagal untuk mengubah profil')</script>";
							$link = $url."pengguna/profil";
							header("refresh:0.1; url=$link");
						}
					}
				}
			}
			else
			{
				if (ubahProfil($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, null, null, $nik, $id_kedudukan)) 
				{	//KONDISI BERHASIL MENGUBAH DATA
					echo "<script>alert('Data Profil Berhasil Diubah')</script>";
					$link = $url."pengguna/profil";
					header("refresh:0.1; url=$link");
				}
				else
				{	//KONDISI GAGAL MENGUBAH DATA
					echo "<script>alert('Gagal untuk mengubah data')</script>";
					$link = $url."pengguna/profil";
					header("refresh:0.1; url=$link");
				}
			}
		}
		else // KONDISI JIKA USER MENGGANTI PASSWORD
		{	//CEK APAKAH PASSWORD DAN PASSWORD ULANG SUDAH SAMA UNTUK MEMASTIKAN
			if ($password != $passwordulang) 
			{	//KONDISI TIDAK SAMA GAGAL
				echo "<script>alert('PASSWORD TIDAK SAMA !')</script>";
				$link = $url."pengguna/profil";
				header("refresh:0.1; url=$link");
			}
			else
			{

				if($namaFile != null || $namaFile != "")
				{
					if ($ukuran > 1048576) 
					{
						echo "<script>alert('Ukuran File Terlalu Besar. Lebih dari 1 MB')</script>";
						$link = $url."pengguna/profil";
						header("refresh:0.1; url=$link");
					}
					else if(strtolower($ekstensi) != "jpg" && strtolower($ekstensi) != "jpeg" && strtolower($ekstensi) != "png")
					{
						echo "<script>alert('File Tidak Valid. Pastikan file dalam format .jpg, .jpeg ataupun .png !')</script>";
						$link = $url."pengguna/profil";
						header("refresh:0.1; url=$link");
					}
					else
					{
						if (is_dir($direktory) == false) 
						{
							$buatDir = mkdir($direktory,0777,true);
							if ($buatDir == false) 
							{
								echo "<script>alert('Gagal Membuat Direktori Untuk Simpan File')</script>";
								$link = $url."pengguna/profil";
								header("refresh:0.1; url=$link");
							}
						}

						$move = move_uploaded_file($fileTemp, $dirSaveFile);
						if ($move == false) 
						{
							echo "<script>alert('Gagal Menyimpan File')</script>";
							$link = $url."pengguna/profil";
							header("refresh:0.1; url=$link");
						}
						else
						{
							header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
							header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
							header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
							header("Cache-Control: post-check=0, pre-check=0", false);
							header("Pragma: no-cache");
							clearstatcache();

							if (ubahProfil($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $password, $fullDirBaru, $nik, $id_kedudukan)) 
							{
								echo "<script>alert('Profil Berhasil Diubah')</script>";
								$link = $url."pengguna/profil";
								header("refresh:0.1; url=$link");
							}
							else
							{
								echo "<script>alert('Gagal untuk mengubah profil')</script>";
								$link = $url."pengguna/profil";
								header("refresh:0.1; url=$link");
							}
						}
					}

				}
				else
				{
					if (ubahProfil($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $password, null, $nik, $id_kedudukan)) 
					{	//BERHASIL
						echo "<script>alert('Data Profil Berhasil Diubah')</script>";
						$link = $url."pengguna/profil";
						header("refresh:0.1; url=$link");
					}
					else
					{	//GAGAL
						echo "<script>alert('Gagal untuk mengubah data profil')</script>";
						$link = $url."pengguna/profil";
						header("refresh:0.1; url=$link");
					}
				}	
			}
		}
	}
	catch(Exception $Ex)
	{
		echo "Soemthing Wrong. ".$Ex;
		$link = $url."pengguna/profil";
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