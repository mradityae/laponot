<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 2)){

	
	include '../config/koneksi.php';
	include '../models/models.php';

	try
	{
		$id_laporan				= $_POST['id_laporan'];
		$id_notaris				= $_POST['id_notaris'];
		$jml_buku_daftar 		= $_POST['jml_buku_daftar'];
		$tanggal_laporan		= $_POST['tanggal_laporan'];
		$jml_tangan_dibukukan 	= $_POST['jml_tangan_dibukukan'];
		$jml_tangan_disahkan 	= $_POST['jml_tangan_disahkan'];
		$jml_buku_protes 		= $_POST['jml_buku_protes'];
		$file    	   			= $_POST['file'];

		$namaFile = $_FILES['file']['name'];
		$x = explode('.',$namaFile);
		$ekstensi = strtolower(end($x));
		$ukuran = $_FILES['file']['size'];
		$fileTemp = $_FILES['file']['tmp_name'];
		$direktory = 'upload/'.$id_notaris;
		$tanggal_laporan = date('F_Y', strtotime($tanggal_laporan));
		$dirSaveFile = $direktory.'/'.'Laporan-'.$tanggal_laporan.'.'.$ekstensi;
		$fullDirBaru = $url.'act/'.$dirSaveFile; 

		if ($ukuran > 5242880) 
		{
			echo "<script>alert('Ukuran File Terlalu Besar. Lebih dari 5 MB')</script>";
			$link = $url."pengguna/index.php";
			header("refresh:0.1; url=$link");
		}
		else if(strtolower($ekstensi) != "pdf")
		{
			echo "<script>alert('File tidak valid. Bukan dalam format .pdf !')</script>";
			$link = $url."pengguna/index.php";
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
					$link = $url."pengguna/index.php";
					header("refresh:0.1; url=$link");
				}
			}

			$move = move_uploaded_file($fileTemp, $dirSaveFile);
			if ($move == false) 
			{
				echo "<script>alert('Ukuran File Terlalu Besar. Gagal Simpan File')</script>";
				$link = $url."pengguna/index.php";
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
				
				if (kirimUlangLaporan($koneksi, $id_laporan, $jml_buku_daftar, $jml_tangan_dibukukan, $jml_tangan_disahkan, $jml_buku_protes, $fullDirBaru)) {
					echo "<script>alert('Laporan Revisi Berhasil Dikirim')</script>";
					$link = $url."pengguna/index.php";
					header("refresh:0.1; url=$link");
				}
				else{
					echo "<script>alert('Gagal untuk mengirim Laporan')</script>";
					$link = $url."pengguna/index.php";
					header("refresh:0.1; url=$link");
				}
			}		
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