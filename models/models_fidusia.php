<?php 
    
    date_default_timezone_set('Asia/Jakarta');

	function unggahLaporanEntitas($koneksi, $id_notaris, $tipe, $nomor, $tanggal, $pemberi, $penerima, $no_sertifikat, $judul_akta, $jenis_transaksi, $nilai_jaminan, $keterangan) {
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Cek pelanggaran berdasarkan tanggal input vs tanggal akta
		$tanggal_input = new DateTime(); // hari ini
		$tanggal_akta = new DateTime($tanggal);

		// Batas input adalah tanggal 5 bulan setelah tanggal akta
		$batas_input = (clone $tanggal_akta)->modify('first day of next month')->setDate(
			$tanggal_akta->format('Y'),
			$tanggal_akta->format('m') + 1,
			15
		);

		if (empty($keterangan)){
			$keterangan = NULL;
		}

		$status_pelanggaran = 0;
		$keterangan_pelanggaran = null;

		if ($tanggal_input > $batas_input) {
			$status_pelanggaran = 1;

			$diff_days = $batas_input->diff($tanggal_input)->days;

			$keterangan_pelanggaran = 
				"Laporan melebihi batas waktu input. Periode laporan: " . $tanggal_akta->format('d-m-Y') .
				", Maksimal: " . $batas_input->format('d-m-Y') .
				", Diinput: " . $tanggal_input->format('d-m-Y') .
				", Terlambat: " . $diff_days . " hari.";
		}

		$status = "Terverifikasi";

		$ambil = $koneksi->prepare("INSERT INTO laporan_entitas 
        (id_notaris, tipe, nomor, tanggal, pemberi, penerima, no_sertifikat, judul_akta, jenis_transaksi, nilai_penjaminan, status, status_pelanggaran, keterangan_pelanggaran, keterangan) 
        VALUES 
        (:id_notaris, :tipe, :nomor, :tanggal, :pemberi, :penerima, :no_sertifikat, :judul_akta, :jenis_transaksi, :nilai_jaminan, :status, :status_pelanggaran, :keterangan_pelanggaran, :keterangan)");

		$ambil->bindParam(":id_notaris", $id_notaris);
		$ambil->bindParam(":tipe", $tipe);
		$ambil->bindParam(":nomor", $nomor);
		$ambil->bindParam(":tanggal", $tanggal);
		$ambil->bindParam(":pemberi", $pemberi);
		$ambil->bindParam(":penerima", $penerima);
		$ambil->bindParam(":no_sertifikat", $no_sertifikat);
		$ambil->bindParam(":judul_akta", $judul_akta);
		$ambil->bindParam(":jenis_transaksi", $jenis_transaksi);
		$ambil->bindParam(":nilai_jaminan", $nilai_jaminan);
		$ambil->bindParam(":status", $status);
		$ambil->bindParam(":status_pelanggaran", $status_pelanggaran);
		$ambil->bindParam(":keterangan_pelanggaran", $keterangan_pelanggaran);
		$ambil->bindParam(":keterangan", $keterangan);


		$ambil->execute();

		$lastInsertId = $koneksi->lastInsertId();
		if ($lastInsertId) {
			$koneksi = null;
			return true;
		} else {
			$koneksi = null;
			return false;
		}
	}