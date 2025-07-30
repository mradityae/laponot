<?php 
    include_once '../log_activity.php';
    date_default_timezone_set('Asia/Jakarta');

	function getTopJenisTransaksi($koneksi, $id_notaris) {
		$sql = "SELECT jenis_transaksi, COUNT(*) AS jumlah
				FROM laporan_entitas
				WHERE id_notaris = :id_notaris
				GROUP BY jenis_transaksi
				ORDER BY jumlah DESC
				LIMIT 5";
		$stmt = $koneksi->prepare($sql);
		$stmt->execute([':id_notaris' => $id_notaris]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function getTopJenisTransaksiAdmin($koneksi, $id_kedudukan) {
		$sql = "SELECT le.jenis_transaksi, COUNT(*) AS jumlah
				FROM laporan_entitas le 
				join notaris n on le.id_notaris = n.id_notaris
				WHERE n.id_kedudukan = :id_kedudukan
				GROUP BY le.jenis_transaksi
				ORDER BY jumlah DESC
				LIMIT 5";
		$stmt = $koneksi->prepare($sql);
		$stmt->execute([':id_kedudukan' => $id_kedudukan]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function jmlLaporanAdmin($koneksi, $id_kedudukan, $status){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	if ($status == "All") {
    		$ambil=$koneksi->prepare("SELECT count(id_laporan) as 'jml' FROM laporan_entitas le join notaris n on le.id_notaris = n.id_notaris WHERE n.id_kedudukan = :id_kedudukan");
    	}
    	else{
    		$ambil=$koneksi->prepare("SELECT count(id_laporan) as 'jml' FROM laporan_entitas le join notaris n on le.id_notaris = n.id_notaris WHERE n.id_kedudukan = :id_kedudukan and status=:status");
    		$ambil->BindParam(":status",$status,PDO::PARAM_STR);
    	}

        $ambil->BindParam(":id_kedudukan",$id_kedudukan,PDO::PARAM_STR);

		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function jmlLaporan($koneksi, $id_notaris, $status){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	if ($status == "All") {
    		$ambil=$koneksi->prepare("SELECT count(id_laporan) as 'jml' FROM laporan_entitas WHERE id_notaris=:id_notaris");
    	}
    	else{
    		$ambil=$koneksi->prepare("SELECT count(id_laporan) as 'jml' FROM laporan_entitas WHERE id_notaris=:id_notaris and status=:status");
    		$ambil->BindParam(":status",$status,PDO::PARAM_STR);
    	}

        $ambil->BindParam(":id_notaris",$id_notaris,PDO::PARAM_STR);

		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function getWilayah($koneksi, $id_kedudukan){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ambil=$koneksi->prepare("SELECT nama_kedudukan FROM kedudukan WHERE id_kedudukan=:id_kedudukan");
        $ambil->BindParam(":id_kedudukan",$id_kedudukan,PDO::PARAM_STR);
		$ambil->execute();
		$count = $ambil->rowCount();
		$row=$ambil->fetch();
		if($count == 0)
		{
			$koneksi = null;
			$output = "-";
		}
		else
		{
			$koneksi = null;
			$output = $row['nama_kedudukan'];
		}

		return $output;
	}
	
	
	function getChartLaporanTahunan(PDO $koneksi, int $tahun, $kedudukan = null, $id_notaris = null): array {
		try {
			$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$params = [':tahun_awal' => "$tahun-01-01", ':tahun_akhir' => "$tahun-12-31"];
			$where = "WHERE laporan_entitas.tanggal BETWEEN :tahun_awal AND :tahun_akhir";
			if (!empty($id_notaris)){
				$where .= ' AND notaris.id_notaris = :id_notaris';
				$params[':id_notaris'] = $id_notaris;
			}

			if (!empty($kedudukan) && $kedudukan !== "Semua Daerah") {
				$where .= " AND notaris.id_kedudukan = :kedudukan";
				$params[':kedudukan'] = $kedudukan;
			}

			$sql = "SELECT MONTH(laporan_entitas.tanggal) AS bulan, COUNT(*) AS jml
					FROM laporan_entitas
					JOIN notaris ON notaris.id_notaris = laporan_entitas.id_notaris
					$where
					GROUP BY bulan
					ORDER BY bulan";

			$stmt = $koneksi->prepare($sql);
			$stmt->execute($params);

			$results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
			$data = array_fill(1, 12, 0);

			foreach ($results as $bulan => $jumlah) {
				$data[(int)$bulan] = (int)$jumlah;
			}

			return $data;
		} catch (PDOException $e) {
			// Jika tersedia fungsi log, catat ke file log
			if (function_exists('write_log')) {
				write_log("Error getChartLaporanTahunan: " . $e->getMessage());
			}
			// Kembalikan array kosong jika terjadi error
			return array_fill(1, 12, 0);
		}
	}

	function unggahLaporanEntitas($koneksi, $id_notaris, $tipe, $nomor, $tanggal, $pemberi, $penerima, $no_sertifikat, $judul_akta, $jenis_transaksi, $nilai_jaminan, $keterangan) {
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Cek pelanggaran berdasarkan tanggal input vs tanggal akta
		$tanggal_input = new DateTime(); // hari ini
		$tanggal_akta = new DateTime($tanggal);

		// Batas input adalah tanggal 5 bulan setelah tanggal akta
		$batas_input = (clone $tanggal_akta)->modify('first day of next month')->setDate(
			$tanggal_akta->format('Y'),
			$tanggal_akta->format('m') + 0,
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