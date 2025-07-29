<?php

	session_save_path('../login/session');
	include_once("../log_activity.php");
	session_start();

	if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 2)){
		
		include '../config/koneksi.php';
		include '../models/models_fidusia.php';

		try
		{
			$id_notaris     = $_POST['id'];
			$tipe           = $_POST['tipe'];
			$nomor          = $_POST['nomor'];
			$tanggal        = $_POST['tanggal'];
			$pemberi        = $_POST['pemberi'];
			$penerima       = $_POST['penerima'];
			$no_sertifikat  = $_POST['no_sertifikat'];
			$judul_akta      = $_POST['judul_akta'];
			$jenis_transaksi = $_POST['jenis_transaksi'];
			$nilai_jaminan   = $_POST['nilai_jaminan'];
			$keterangan 	 = $_POST['keterangan'];

			clearstatcache();

			if (unggahLaporanEntitas($koneksi, $id_notaris, $tipe, $nomor, $tanggal, $pemberi, $penerima, $no_sertifikat, $judul_akta, $jenis_transaksi, $nilai_jaminan, $keterangan)) {
				write_log("Notaris ID $id_notaris mengunggah laporan '$tipe' dengan nomor $nomor pada $tanggal - BERHASIL.");
				
				echo "<script>alert('Laporan Berhasil Dikirim')</script>";
				$link = $url."pengguna/daftar_laporan_entitas";
				header("refresh:0.1; url=$link");
			}
			else{
				write_log("Notaris ID $id_notaris mencoba mengunggah laporan '$tipe' dengan nomor $nomor pada $tanggal - GAGAL.");
				
				echo "<script>alert('Gagal untuk mengirim Laporan')</script>";
				$link = $url."pengguna/unggah_laporan_entitas";
				header("refresh:0.1; url=$link");
			}
		}
		catch(Exception $Ex)
		{
			write_log("ERROR saat pengunggahan laporan oleh Notaris ID $id_notaris: " . $Ex->getMessage());
			echo "Something Wrong. ".$Ex;	
		}
	}
	else
	{
		write_log("Akses ilegal atau sesi tidak valid untuk unggah laporan oleh user: " . ($_SESSION['email'] ?? 'Unknown'));
		
		echo "WRONG ACCESS";
		$link = "https://kabayanpasti.kemenkumham.go.id";
		header("refresh:0.1; $link");	
	}
?>