<?php

	session_save_path('../login/session');
	include_once("../log_activity.php");
	session_start();

	if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 2)){
		
		include '../config/koneksi.php';
		include '../models/models_fidusia.php';

		try
		{
			$id_notaris     = $_POST['id'] ?? null;
			$tipe           = $_POST['tipe'] ?? null;
			$nomor          = $_POST['nomor'] ?? null;
			$tanggal        = $_POST['tanggal'] ?? null;
			$pemberi        = $_POST['pemberi'] ?? null;
			$penerima       = $_POST['penerima'] ?? null;
			$no_sertifikat  = $_POST['no_sertifikat'] ?? null;
			$judul_akta      = $_POST['judul_akta'] ?? null;
			$jenis_transaksi = $_POST['jenis_transaksi'] ?? null;
			$nilai_jaminan   = $_POST['nilai_jaminan'] ?? null;
			$keterangan 	 = $_POST['ket'] ?? null;
			$no_sertifikat_lama = $_POST['no_sertifikat_lama'] ?? null;
			$tanggal_nihil = $_POST['tanggal_nihil'] ?? null; 

			clearstatcache();

			if (isset($_POST['laporan_nihil'])) {

				if (empty($tanggal_nihil)) {
					echo "<script>alert('Periode laporan NIHIL wajib diisi');history.back();</script>";
					exit;
				}

				// jadikan tanggal = tanggal pertama di bulan tsb
				$tanggal_fix = $tanggal_nihil . "-01";

				$id_notaris = $_POST['id'];
				$tipe = "fidusia";
				$status = "Terverifikasi";

				$sql = "INSERT INTO laporan_entitas 
						(id_notaris, tipe, nomor, tanggal, pemberi, penerima, no_sertifikat,
						judul_akta, jenis_transaksi, nilai_penjaminan, status, keterangan, created_at)
						VALUES
						(:id_notaris, :tipe, 'NIHIL', :tanggal,
						'NIHIL', 'NIHIL', 'NIHIL',
						'NIHIL', 'NIHIL', 'Tidak Relevan',
						:status, 'NIHIL', NOW())";

				$stmt = $koneksi->prepare($sql);
				$stmt->execute([
					':id_notaris' => $id_notaris,
					':tipe'       => $tipe,
					':tanggal'    => $tanggal_fix,
					':status'     => $status
				]);

				write_log("Notaris ID $id_notaris mengunggah laporan fidusia NIHIL periode $tanggal_nihil - BERHASIL.");

				echo "<script>
					alert('Laporan NIHIL periode $tanggal_nihil berhasil dikirim');
					window.location.href = '$url/pengguna/daftar_laporan_entitas';
				</script>";
				exit;
			}

			if (unggahLaporanEntitas($koneksi, $id_notaris, $tipe, $nomor, $tanggal, $pemberi, $penerima, $no_sertifikat, $judul_akta, $jenis_transaksi, $nilai_jaminan, $keterangan, $no_sertifikat_lama)) {
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