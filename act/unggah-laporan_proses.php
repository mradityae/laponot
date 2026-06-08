<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 2)){
	
	include '../config/koneksi.php';
	include '../models/models.php';
	include("../log_activity.php");

	try
	{
		$id_notaris 			= $_POST['id'];
		$tanggal_input          = $_POST['tanggal_laporan'];
		$tanggal                = $tanggal_input . "-10";
		$jml_buku_daftar 		= $_POST['jml_buku_daftar'];
		$jml_tangan_dibukukan 	= $_POST['jml_tangan_dibukukan'];
		$jml_tangan_disahkan 	= $_POST['jml_tangan_disahkan'];
		$jml_buku_protes 		= $_POST['jml_buku_protes'];

		$monthYear = date("F_Y",strtotime($tanggal));
		$namaFile = $_FILES['file']['name'];
		$x = explode('.',$namaFile);
		$ekstensi = strtolower(end($x));
		$ukuran = $_FILES['file']['size'];
		$fileTemp = $_FILES['file']['tmp_name'];
		$direktory = 'upload/'.$id_notaris;
		$dirSaveFile = $direktory.'/'.'Laporan-'.$monthYear.'.'.$ekstensi;
		$fullDirBaru = $url.'act/'.$dirSaveFile; 

		if (cekUploaded($koneksi, $id_notaris, $tanggal) == false) 
		{
			echo "<script>alert('Anda telah mengirim file pada bulan yang bersangkutan')</script>";
			$link = $url."pengguna/index";
			header("refresh:0.1; url=$link");
		}
		else if ($ukuran > 5242880) 
		{
			echo "<script>alert('Ukuran File Terlalu Besar. Lebih dari 5 MB')</script>";
			$link = $url."pengguna/unggah_laporan";
			header("refresh:0.1; url=$link");
		}
		else if(strtolower($ekstensi) != "pdf")
		{
			echo "<script>alert('File tidak valid. Bukan dalam format .pdf !')</script>";
			$link = $url."pengguna/unggah_laporan";
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
					$link = $url."pengguna/unggah_laporan";
					header("refresh:0.1; url=$link");
				}
			}

			$move = move_uploaded_file($fileTemp, $dirSaveFile);
			if ($move == false) 
			{
				$filename = 'Laporan-' . $monthYear . '.' . $ekstensi;

				write_log(
					"Upload Laporan Gagal : " .
					$filename .
					" | Notaris ID: " . $id_notaris .
					" | Tmp File: " . $fileTemp .
					" | Tujuan: " . $dirSaveFile .
					" | Size: " . $ukuran . " bytes" .
					" | Upload Error: " . ($_FILES['file']['error'] ?? 'unknown') .
					" | Dir Exists: " . (is_dir($direktory) ? 'YES' : 'NO') .
					" | Dir Writable: " . (is_writable($direktory) ? 'YES' : 'NO')
				);

				echo "<script>alert('Ukuran File Terlalu Besar. Gagal Simpan File')</script>";
				$link = $url."pengguna/unggah_laporan";
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
				
				if (unggahLaporan($koneksi, $id_notaris, $tanggal, $jml_buku_daftar, $jml_tangan_dibukukan, $jml_tangan_disahkan, $jml_buku_protes,$fullDirBaru)) {
					echo "<script>alert('Laporan Berhasil Dikirim')</script>";
					$link = $url."pengguna/index";
					header("refresh:0.1; url=$link");
				}
				else{
					echo "<script>alert('Gagal untuk mengirim Laporan')</script>";
					$link = $url."pengguna/unggah_laporan";
					header("refresh:0.1; url=$link");
				}
			}		
		}
	}
	catch(Exception $Ex)
	{
	/*	echo "<script>alert('Gagal untuk mengirim permohonan pelantikan')</script>";
		$link = $url."pengguna/unggah_laporan.php";
		header("refresh:0.1; url=$link");*/
		echo "Something Wrong. ".$Ex;	
	}
}
else
{
	echo "WRONG ACCESS";
        $link = "https://kabayanpasti.kemenkum.go.id";
        header("refresh:0.1; $link");	
}

?>