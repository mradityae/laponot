<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 2))
{
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
		$requestId = 'ULP-' . $id_notaris . '-' . date('Ym') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));

		$monthYear = date("F_Y", strtotime($tanggal));
		$namaFile = $_FILES['file']['name'];
		$x = explode('.', $namaFile);
		$ekstensi = strtolower(end($x));
		$ukuran = $_FILES['file']['size'];
		$fileTemp = $_FILES['file']['tmp_name'];

		$direktory = 'upload/'.$id_notaris;
		$dirSaveFile = $direktory.'/'.'Laporan-'.$monthYear.'.'.$ekstensi;
		$fullDirBaru = $url.'act/'.$dirSaveFile;

		write_log(
			"[{$requestId}] Mulai Upload Laporan" .
			" | Notaris ID: " . $id_notaris .
			" | File: " . $namaFile .
			" | Size: " . $ukuran .
			" | Periode: " . $tanggal
		);

		if (cekUploaded($koneksi, $id_notaris, $tanggal) == false)
		{
			write_log(
				"[{$requestId}] Upload Ditolak (Sudah Upload Periode Ini)" .
				" | Notaris ID: " . $id_notaris .
				" | Periode: " . $tanggal
			);

			echo "<script>alert('Anda telah mengirim file pada bulan yang bersangkutan')</script>";
			$link = $url."pengguna/index";
			header("refresh:0.1; url=$link");
		}
		else if ($ukuran > 5242880)
		{
			write_log(
				"[{$requestId}] Upload Ditolak (File > 5MB)" .
				" | Notaris ID: " . $id_notaris .
				" | File: " . $namaFile .
				" | Size: " . $ukuran
			);

			echo "<script>alert('Ukuran File Terlalu Besar. Lebih dari 5 MB')</script>";
			$link = $url."pengguna/unggah_laporan";
			header("refresh:0.1; url=$link");
		}
		else if (strtolower($ekstensi) != "pdf")
		{
			write_log(
				"[{$requestId}] Upload Ditolak (Bukan PDF)" .
				" | Notaris ID: " . $id_notaris .
				" | File: " . $namaFile .
				" | Ekstensi: " . $ekstensi
			);

			echo "<script>alert('File tidak valid. Bukan dalam format .pdf !')</script>";
			$link = $url."pengguna/unggah_laporan";
			header("refresh:0.1; url=$link");
		}
		else
		{
			if (is_dir($direktory) == false)
			{
				$buatDir = mkdir($direktory, 0777, true);

				if ($buatDir == false)
				{
					write_log(
						"[{$requestId}] Gagal Membuat Direktori" .
						" | Notaris ID: " . $id_notaris .
						" | Direktori: " . $direktory
					);

					echo "<script>alert('Gagal Membuat Direktori Untuk Simpan File')</script>";
					$link = $url."pengguna/unggah_laporan";
					header("refresh:0.1; url=$link");
					exit;
				}
				else
				{
					write_log(
						"[{$requestId}] Direktori Berhasil Dibuat" .
						" | Notaris ID: " . $id_notaris .
						" | Direktori: " . $direktory
					);
				}
			}

			write_log(
				"[{$requestId}] Proses move_uploaded_file()" .
				" | Notaris ID: " . $id_notaris .
				" | Source: " . $fileTemp .
				" | Destination: " . $dirSaveFile
			);

			$move = move_uploaded_file($fileTemp, $dirSaveFile);

			if ($move == false)
			{
				$filename = 'Laporan-' . $monthYear . '.' . $ekstensi;

				write_log(
					"[{$requestId}] Upload Laporan Gagal" .
					" | File: " . $filename .
					" | Notaris ID: " . $id_notaris .
					" | Tmp File: " . $fileTemp .
					" | Tujuan: " . $dirSaveFile .
					" | Size: " . $ukuran .
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
				write_log(
					"[{$requestId}] Upload File Berhasil" .
					" | Notaris ID: " . $id_notaris .
					" | File: " . $dirSaveFile
				);

				header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				clearstatcache();

				if (
					unggahLaporan(
						$koneksi,
						$id_notaris,
						$tanggal,
						$jml_buku_daftar,
						$jml_tangan_dibukukan,
						$jml_tangan_disahkan,
						$jml_buku_protes,
						$fullDirBaru
					)
				)
				{
					write_log(
						"[{$requestId}] Laporan Berhasil Disimpan" .
						" | Notaris ID: " . $id_notaris .
						" | File: " . $fullDirBaru .
						" | Periode: " . $tanggal
					);

					echo "<script>alert('Laporan Berhasil Dikirim')</script>";
					$link = $url."pengguna/index";
					header("refresh:0.1; url=$link");
				}
				else
				{
					write_log(
						"[{$requestId}] Gagal Simpan Database" .
						" | Notaris ID: " . $id_notaris .
						" | File: " . $fullDirBaru .
						" | Periode: " . $tanggal
					);

					echo "<script>alert('Gagal untuk mengirim Laporan')</script>";
					$link = $url."pengguna/unggah_laporan";
					header("refresh:0.1; url=$link");
				}
			}
		}
	}
	catch(Exception $Ex)
	{
		write_log(
			"[{$requestId}] Exception Upload Laporan" .
			" | Notaris ID: " . ($id_notaris ?? '-') .
			" | Error: " . $Ex->getMessage()
		);

		echo "Something Wrong. ".$Ex;
	}
}
else
{
	include("../log_activity.php");

	write_log(
		"[{$requestId}] Wrong Access unggah-laporan_proses.php" .
		" | IP: " . ($_SERVER['REMOTE_ADDR'] ?? '-') .
		" | User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? '-')
	);

	echo "WRONG ACCESS";

	$link = "https://kabayanpasti.kemenkum.go.id";
	header("refresh:0.1; url=$link");
}
?>