<?php
	// UPDATE UNTUK FUNGSIONALITAS CHAT
	date_default_timezone_set('Asia/Jakarta');
	//---------------------------------------------------------------------------------------
	function hasilSurveyBulanan($koneksi, $rating, $bulan){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ambil=$koneksi->prepare("SELECT count(id_survey) as 'jml'
        	FROM survey
        	where month(surveyDate) =:bulan and rating =:rating and year(surveyDate) =:years ");
        $years = date("Y");
		$ambil->BindParam(":years",$years,PDO::PARAM_INT);
		$ambil->BindParam(":bulan",$bulan,PDO::PARAM_INT);
		$ambil->BindParam(":rating",$rating,PDO::PARAM_INT);
		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function hasilSurvey($koneksi, $parameter){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if ($parameter == "All") {
			$ambil=$koneksi->prepare("SELECT  count(id_survey) as 'jml' FROM survey");
		}
		else{
			$ambil=$koneksi->prepare("SELECT  count(id_survey) as 'jml' FROM survey where rating=:rating");
			$ambil->BindParam(":rating",$parameter,PDO::PARAM_STR);
		}

		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function tambahSurvey($koneksi, $rating, $keterangan){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("INSERT INTO survey(rating, keterangan, surveyDate) VALUES (:rating, :keterangan, :createDate)");

		$isiDate = date("Y-m-d H:i:s");

		$ambil->BindParam(":rating", $rating, PDO::PARAM_STR);
		$ambil->BindParam(":keterangan", $keterangan, PDO::PARAM_STR);
		$ambil->BindParam(":createDate",$isiDate, PDO::PARAM_STR);

		$ambil->execute();

		$lastInsertId = $koneksi->lastInsertId();
		if ($lastInsertId) {
			$koneksi = null;
			return true;
		}
		else{
			$koneksi = null;
			return false;
		}
	}

	function fetch_user_last_activity($user_id, $koneksi)
	{
	 $query = "
	 SELECT * FROM login_details
	 WHERE ID = '$user_id'
	 ORDER BY last_activity DESC
	 LIMIT 1
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $result = $statement->fetchAll();
	 foreach($result as $row)
	 {
	  return $row['last_activity'];
	 }
	}

	//---------------------------------------------------------------------------------------
	function fetch_user_chat_history($from_user_id, $to_user_id, $koneksi)
	{
	 $query = "
	 SELECT * FROM chat_message
	 WHERE (from_user_id = '".$from_user_id."'
	 AND to_user_id = '".$to_user_id."')
	 OR (from_user_id = '".$to_user_id."'
	 AND to_user_id = '".$from_user_id."')
	 ORDER BY timestamp ASC
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $result = $statement->fetchAll();
	 $output = '<ul class="list-unstyled">';
	 foreach($result as $row)
	 {
	  $user_name = '';
	  $dynamic_background = '';
	  $chat_message = '';
	  if($row["from_user_id"] == $from_user_id)
	  {
	   if($row["status"] == '2')
	   {
	    $chat_message = '<em>Pesan ini telah dihapus</em>';
	    $user_name = '<b class="text-success">Kamu</b>';
	   }
	   else
	   {
	    $chat_message = $row['chat_message'];
	    $user_name = '<button type="button" class="btn btn-danger btn-sm remove_chat" id="'.$row['chat_message_id'].'">x</button>&nbsp;<b class="text-success">Kamu</b>';
	   }


	   $dynamic_background = 'background-color:#edfaf0;';
	  }
	  else
	  {
	   if($row["status"] == '2')
	   {
	    $chat_message = '<em>Pesan ini telah dihapus</em>';
	   }
	   else
	   {
	    $chat_message = $row["chat_message"];
	   }
	   $user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $koneksi).'</b>';
	   $dynamic_background = 'background-color:#fffff2;';
	  }
	  $output .= '
	  <li style="border-bottom:1px dotted #ccc;padding-top:8px; padding-left:8px; padding-right:8px;'.$dynamic_background.'">
	   <p>'.$user_name.' - '.$chat_message.'
	    <div align="right">
	     - <small><em>'.$row['timestamp'].'</em></small>
	    </div>
	   </p>
	  </li>
	  ';
	 }
	 $output .= '</ul>';
	 $query = "
	 UPDATE chat_message
	 SET status = '0'
	 WHERE from_user_id = '".$to_user_id."'
	 AND to_user_id = '".$from_user_id."'
	 AND status = '1'
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 return $output;
	}


	//---------------------------------------------------------------------------------------
	function fetch_group_chat_history($koneksi)
	{
	 $query = "
	 SELECT * FROM chat_message
	 WHERE to_user_id = '0'
	 ORDER BY timestamp ASC
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $result = $statement->fetchAll();
	 $output = '<ul class="list-unstyled">';
	 foreach($result as $row)
	 {
	  $user_name = '';
	  $chat_message = '';
	  $dynamic_background = '';

	  if($row['from_user_id'] == $_SESSION['user_id'])
	  {
	   if($row["status"] == '2')
	   {
	    $chat_message = '<em>Pesan ini telah dihapus</em>';
	    $user_name = '<b class="text-success">Kamu</b>';
	   }
	   else
	   {
	    $chat_message = $row['chat_message'];
	    $user_name = '<button type="button" class="btn btn-danger btn-sm remove_chat" id="'.$row['chat_message_id'].'">x</button>&nbsp;<b class="text-success">Kamu</b>';
	   }
	   $dynamic_background = 'background-color:#edfaf0;';
	  }
	  else
	  {
	   if($row["status"] == '2')
	   {
	    $chat_message = '<em>Pesan ini telah dihapus</em>';
	   }
	   else
	   {
	    $chat_message = $row['chat_message'];
	   }
	   $user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $koneksi).'</b>';
	   $dynamic_background = 'background-color:#fffff2;';
	  }
	  $output .= '
	  <li style="border-bottom:1px dotted #ccc;padding-top:8px; padding-left:8px; padding-right:8px;'.$dynamic_background.'">
	   <p>'.$user_name.' - '.$chat_message.'
	    <div align="right">
	     - <small><em>'.$row['timestamp'].'</em></small>
	    </div>
	   </p>

	  </li>
	  ';
	 }
	 $output .= '</ul>';
	 return $output;
	}


	//---------------------------------------------------------------------------------------
	function get_user_name($user_id, $koneksi)
	{
	 $query = "SELECT nama FROM pengguna WHERE id = '$user_id'";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $result = $statement->fetchAll();
	 foreach($result as $row)
	 {
	  return $row['nama'];
	 }
	}

	//---------------------------------------------------------------------------------------
	function count_unseen_message($from_user_id, $to_user_id, $koneksi)
	{
	 $query = "
	 SELECT * FROM chat_message
	 WHERE from_user_id = '$from_user_id'
	 AND to_user_id = '$to_user_id'
	 AND status = '1'
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $count = $statement->rowCount();
	 $output = '';
	 if($count > 0)
	 {
	  $output = '<span class="badge badge-success">'.$count.'</span>';
	 }
	 return $output;
	}

	//---------------------------------------------------------------------------------------

	function count_unseen_all($koneksi)
	{
	 $query = "
	 SELECT * FROM chat_message
	 WHERE to_user_id = 4 and status = '1'
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $count = $statement->rowCount();
	 $output = '';
	 if($count > 0)
	 {
	  $output = '<div class="alert alert-danger" role="alert">ANDA MEMILIKI '.$count.' PESAN YANG BELUM TERBACA. SILAHKAN CEK FITUR CHAT</div>';
	 }
	 return $output;
	}

	//---------------------------------------------------------------------------------------
	function count_unseen_user($koneksi, $id)
	{
	 $query = "
	 SELECT * FROM chat_message
	 WHERE to_user_id = $id and status = '1'
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $count = $statement->rowCount();
	 $output = '';
	 if($count > 0)
	 {
	  $output = '<div class="alert alert-danger" role="alert">ANDA MEMILIKI '.$count.' PESAN YANG BELUM TERBACA. SILAHKAN CEK FITUR CHAT</div>';
	 }
	 return $output;
	}

	//---------------------------------------------------------------------------------------
	function fetch_is_type_status($user_id, $koneksi)
	{
	 $query = "
	 SELECT is_type FROM login_details
	 WHERE user_id = '".$user_id."'
	 ORDER BY last_activity DESC
	 LIMIT 1
	 ";
	 $statement = $koneksi->prepare($query);
	 $statement->execute();
	 $result = $statement->fetchAll();
	 $output = '';
	 foreach($result as $row)
	 {
	  if($row["is_type"] == 'yes')
	  {
	   $output = ' - <small><em><span class="text-muted">mengetik...</span></em></small>';
	  }
	 }
	 return $output;
	}

	// END FUNGSIONALITAS CHAT

	function RemoveSpecialChar($value){
		$title = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), ' ', $value);

		return $title;
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

	function notifUpload($koneksi, $id_notaris, $tanggal){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$dateObj = new DateTime($tanggal_sekarang);
		$dateObj->modify('-1 month'); 
		
		$bulanLalu = $dateObj->format('n'); 
		$tahunLalu = $dateObj->format('Y');

		$currentDate = new DateTime($tanggal_sekarang);
		$deadline = "10 " . strtoupper($currentDate->format('F Y'));
        $ambil=$koneksi->prepare("SELECT * FROM laporan WHERE id_notaris=:id_notaris and month(tanggal)=:tanggal and year(tanggal)=:tahun");

        $ambil->BindParam(":id_notaris",$id_notaris,PDO::PARAM_STR);
		$ambil->BindParam(":tanggal", $bulanLalu, PDO::PARAM_INT);
	    $ambil->BindParam(":tahun", $tahunLalu, PDO::PARAM_INT);
		$ambil->execute();
		$count = $ambil->rowCount();
		if($count == 0)
		{
			$koneksi = null;
			// $output = '<div class="alert alert-danger" role="alert">ANDA BELUM MENGUNGGAH LAPORAN BULAN INI</div>';
			$output = '<div class="alert alert-danger" role="alert">ANDA BELUM MENGUNGGAH LAPORAN BULANAN</div>';			
		}
		else
		{
			$koneksi = null;
			// $output = '<div class="alert alert-warning" role="alert">ANDA SUDAH MENGUNGGAH LAPORAN BULAN INI</div>';
			$output = '<div class="alert alert-warning" role="alert">ANDA SUDAH MENGUNGGAH LAPORAN BULANAN</div>';
		}

		return $output;
	}


	function jmlLaporanSuperAdmin($koneksi, $status){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	if ($status == "All") {
    		$ambil=$koneksi->prepare("SELECT  count(l.id_laporan) as 'jml' FROM laporan as l join notaris on notaris.id_notaris = l.id_notaris");
    	}
    	else{
    		$ambil=$koneksi->prepare("SELECT  count(l.id_laporan) as 'jml' FROM laporan as l join notaris on notaris.id_notaris = l.id_notaris WHERE l.status=:status");
    		$ambil->BindParam(":status",$status,PDO::PARAM_STR);
    	}

		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function jmlLaporanAdmin($koneksi, $id_kedudukan, $status){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	if ($status == "All") {
    		$ambil=$koneksi->prepare("SELECT  count(l.id_laporan) as 'jml' FROM laporan as l join notaris on notaris.id_notaris = l.id_notaris WHERE notaris.id_kedudukan=:id_kedudukan");
    	}
    	else{
    		$ambil=$koneksi->prepare("SELECT  count(l.id_laporan) as 'jml' FROM laporan as l join notaris on notaris.id_notaris = l.id_notaris WHERE notaris.id_kedudukan=:id_kedudukan and l.status=:status");
    		$ambil->BindParam(":status",$status,PDO::PARAM_STR);
    	}

        $ambil->BindParam(":id_kedudukan",$id_kedudukan,PDO::PARAM_STR);

		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function jmlNotaris($koneksi, $id_kedudukan, $aktif){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	$level =2;

    	if ($aktif == "All") {
    		$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml' FROM notaris WHERE id_kedudukan=:id_kedudukan and level=:level");
    		$ambil->BindParam(":id_kedudukan",$id_kedudukan,PDO::PARAM_STR);
    	}
    	else if($aktif == "Super Admin All")
    	{
    		$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml' FROM notaris WHERE level=:level");
    	}
    	else if($aktif == "Super Admin Notaktif")
    	{
    		$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml' FROM notaris WHERE level=:level and aktif= 0  and id_kedudukan=:id_kedudukan");
			$ambil->BindParam(":id_kedudukan",$id_kedudukan,PDO::PARAM_STR);
    	}
		else if($aktif == "Super Admin NotaktifAll")
    	{
    		$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml' FROM notaris WHERE level=:level and aktif= 0");
    	}
    	else if($aktif == "Jumlah AdminAll")
    	{
    		$level =1;
    		$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml' FROM notaris WHERE level=:level");
    	}
		else if($aktif == "Jumlah Admin")
    	{
    		$level =1;
    		$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml' FROM notaris WHERE level=:level and id_kedudukan=:id_kedudukan");
			$ambil->BindParam(":id_kedudukan",$id_kedudukan,PDO::PARAM_STR);
    	}
    	else{
    		$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml' FROM notaris WHERE id_kedudukan=:id_kedudukan and aktif=:aktif and level=:level");
    		$ambil->BindParam(":aktif",$aktif,PDO::PARAM_STR);
    		$ambil->BindParam(":id_kedudukan",$id_kedudukan,PDO::PARAM_STR);
    	}


        $ambil->BindParam(":level",$level,PDO::PARAM_INT);

		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function jmlLaporan($koneksi, $id_notaris, $status){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	if ($status == "All") {
    		$ambil=$koneksi->prepare("SELECT count(id_laporan) as 'jml' FROM laporan WHERE id_notaris=:id_notaris");
    	}
    	else{
    		$ambil=$koneksi->prepare("SELECT count(id_laporan) as 'jml' FROM laporan WHERE id_notaris=:id_notaris and status=:status");
    		$ambil->BindParam(":status",$status,PDO::PARAM_STR);
    	}

        $ambil->BindParam(":id_notaris",$id_notaris,PDO::PARAM_STR);

		$ambil->execute();
		$row=$ambil->fetch();
		$koneksi = null;
		return $row['jml'];
	}

	function cekUpdated($koneksi, $id_notaris, $tanggal, $id_laporan){
		$stmt = $koneksi->prepare("
			SELECT 1 
			FROM laporan 
			WHERE id_notaris = :id_notaris
			AND MONTH(tanggal) = :bulan
			AND YEAR(tanggal) = :tahun
			AND id_laporan != :id_laporan
			LIMIT 1
		");

		$bulan = (int)date('n', strtotime($tanggal));
		$tahun = (int)date('Y', strtotime($tanggal));

		$stmt->execute([
			':id_notaris'  => $id_notaris,
			':bulan'       => $bulan,
			':tahun'       => $tahun,
			':id_laporan'  => $id_laporan
		]);

		return $stmt->rowCount() === 0;
	}


	function cekUploaded($koneksi, $id_notaris, $tanggal){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ambil=$koneksi->prepare("SELECT * FROM laporan WHERE id_notaris=:id_notaris and month(tanggal)=:tanggal and year(tanggal)=:tahun");

        $bulanInt = date('n', strtotime($tanggal));
        $tahunInt = date('Y', strtotime($tanggal));
        $ambil->BindParam(":id_notaris",$id_notaris,PDO::PARAM_STR);
        $ambil->BindParam(":tanggal",$bulanInt,PDO::PARAM_STR);
        $ambil->BindParam(":tahun",$tahunInt,PDO::PARAM_STR);
		$ambil->execute();
		$count = $ambil->rowCount();
		if($count == 0)
		{
			$koneksi = null;
			return true;
		}
		else
		{
			$koneksi = null;
			return false;
		}
	}

	function cekEmail($koneksi, $email){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ambil=$koneksi->prepare("SELECT * FROM notaris WHERE email=:isiemail");
        $ambil->BindParam(":isiemail",$email,PDO::PARAM_STR);
		$ambil->execute();
		$count = $ambil->rowCount();
		if($count == 0)
		{
			$koneksi = null;
			return true;
		}
		else
		{
			$koneksi = null;
			return false;
		}
	}

	function unggahLaporan($koneksi, $id_notaris, $tanggal, $jml_buku_daftar, $jml_tangan_dibukukan, $jml_tangan_disahkan, $jml_buku_protes,$fullDirBaru){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$ambil=$koneksi->prepare("INSERT INTO laporan(id_notaris, tanggal, jml_buku_daftar, jml_tangan_dibukukan,jml_tangan_disahkan,jml_buku_protes, file_upload, status) VALUES (:id_notaris, :tanggal, :jml_buku_daftar, :jml_tangan_dibukukan, :jml_tangan_disahkan, :jml_buku_protes, :file_upload, :status)");

		$status = "Laporan Terkirim";
    	$ambil->BindParam(":id_notaris", $id_notaris, PDO::PARAM_INT);
    	$ambil->BindParam(":tanggal",  $tanggal, PDO::PARAM_STR);
    	$ambil->BindParam(":jml_buku_daftar", $jml_buku_daftar, PDO::PARAM_INT);
    	$ambil->BindParam(":jml_tangan_dibukukan", $jml_tangan_dibukukan, PDO::PARAM_INT);
    	$ambil->BindParam(":jml_tangan_disahkan", $jml_tangan_disahkan, PDO::PARAM_INT);
    	$ambil->BindParam(":jml_buku_protes", $jml_buku_protes, PDO::PARAM_INT);
    	$ambil->BindParam(":file_upload",$fullDirBaru, PDO::PARAM_STR);
    	$ambil->BindParam(":status",$status, PDO::PARAM_STR);

		$ambil->execute();
		$lastInsertId = $koneksi->lastInsertId();
		if ($lastInsertId)
		{
			$koneksi = null;
			return true;
		}
		else
		{
			$koneksi = null;
			return false;
		}
	}

	function editLaporanBulanan(
		$koneksi,
		$id_laporan,
		$tanggal,
		$jml_buku_daftar,
		$jml_tangan_dibukukan,
		$jml_tangan_disahkan,
		$jml_buku_protes,
		$file_upload
	){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $koneksi->prepare("
			UPDATE laporan SET
				tanggal = :tanggal,
				jml_buku_daftar = :a,
				jml_tangan_dibukukan = :b,
				jml_tangan_disahkan = :c,
				jml_buku_protes = :d,
				file_upload = :f
			WHERE id_laporan = :id
		");

		return $stmt->execute([
			':tanggal' => $tanggal,
			':a'       => $jml_buku_daftar,
			':b'       => $jml_tangan_dibukukan,
			':c'       => $jml_tangan_disahkan,
			':d'       => $jml_buku_protes,
			':f'       => $file_upload,
			':id'      => $id_laporan
		]);
	}

	function kirimUlangLaporan($koneksi, $id_laporan, $jml_buku_daftar, $jml_tangan_dibukukan, $jml_tangan_disahkan, $jml_buku_protes, $file_upload){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("UPDATE laporan set jml_buku_daftar =:jml_buku_daftar, jml_tangan_dibukukan =:jml_tangan_dibukukan, jml_tangan_disahkan =:jml_tangan_disahkan, jml_buku_protes =:jml_buku_protes, keterangan=:keterangan, status=:status, file_upload=:file_upload WHERE id_laporan=:id_laporan");

		$keterangan = "Notaris Telah Mengirim Ulang Laporan";
		$status = "Laporan Terkirim";

    	$ambil->BindParam(":jml_buku_daftar", $jml_buku_daftar, PDO::PARAM_STR);
    	$ambil->BindParam(":jml_tangan_dibukukan", $jml_tangan_dibukukan, PDO::PARAM_STR);
    	$ambil->BindParam(":jml_tangan_disahkan", $jml_tangan_disahkan, PDO::PARAM_STR);
    	$ambil->BindParam(":jml_buku_protes", $jml_buku_protes, PDO::PARAM_STR);
    	$ambil->BindParam(":status", $status, PDO::PARAM_STR);
    	$ambil->BindParam(":keterangan", $keterangan, PDO::PARAM_STR);
    	$ambil->BindParam(":file_upload", $file_upload, PDO::PARAM_STR);
    	$ambil->BindParam(":id_laporan",$id_laporan, PDO::PARAM_INT);
    	$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function verifikasiLaporan($koneksi, $id_laporan, $status, $keterangan){

		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$ambil=$koneksi->prepare("UPDATE laporan SET status =:status, keterangan=:keterangan WHERE id_laporan =:id_laporan");

	    $ambil->BindParam(":status", $status, PDO::PARAM_STR);
	    $ambil->BindParam(":keterangan",$keterangan, PDO::PARAM_STR);
	    $ambil->BindParam(":id_laporan",$id_laporan, PDO::PARAM_INT);

	    $ambil->execute();
		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function tambahNotaris($koneksi, $nama, $jenis_kelamin, $kedudukan, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan ,$alamat, $telepon, $email, $password, $level, $aktif, $photo){

		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("INSERT INTO notaris(id_kedudukan, email, password, nama, jenis_kelamin, alamat, telepon, sk, tanggal_sk, no_ba_pelantikan, tgl_ba_pelantikan, level, aktif, createDate, photo) VALUES (:kedudukan, :email, :password, :nama, :jenis_kelamin, :alamat, :telepon, :sk, :tanggal_sk, :no_ba_pelantikan, :tgl_ba_pelantikan, :level, :aktif, :createDate, :photo)");

		$encrypPass = md5($password);
		$isiDate = date("Y-m-d H:i:s");

    	$ambil->BindParam(":kedudukan", $kedudukan, PDO::PARAM_STR);
    	$ambil->BindParam(":email", $email, PDO::PARAM_STR);
    	$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
    	$ambil->BindParam(":nama",$nama, PDO::PARAM_STR);
    	$ambil->BindParam(":jenis_kelamin",$jenis_kelamin, PDO::PARAM_STR);
    	$ambil->BindParam(":alamat",$alamat, PDO::PARAM_STR);
    	$ambil->BindParam(":telepon",$telepon, PDO::PARAM_STR);
    	$ambil->BindParam(":sk",$sk, PDO::PARAM_STR);
    	$ambil->BindParam(":tanggal_sk",$tanggal_sk, PDO::PARAM_STR);
    	$ambil->BindParam(":no_ba_pelantikan",$no_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":tgl_ba_pelantikan",$tgl_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":level",$level, PDO::PARAM_INT);
    	$ambil->BindParam(":aktif",$aktif, PDO::PARAM_INT);
    	$ambil->BindParam(":createDate",$isiDate, PDO::PARAM_STR);
		//tambah ditectory untuk foto
    	$ambil->BindParam(":photo",$photo, PDO::PARAM_STR);

		$ambil->execute();

		$lastInsertId = $koneksi->lastInsertId();
		if ($lastInsertId) {
			$koneksi = null;
			return true;
		}
		else{
			$koneksi = null;
			return false;
		}
	}

	function tambahPenggunaBySuper($koneksi, $email, $password, $nama, $agama, $jenis_kelamin, $instansi, $kewarganegaraan, $alamat, $telepon, $level){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("INSERT INTO pengguna(email, password, nama, agama, jenis_kelamin, instansi, kewarganegaraan, alamat, telepon, level, aktif, createDate) VALUES (:email, :password, :nama, :agama, :jenis_kelamin, :instansi, :kewarganegaraan, :alamat, :telepon, :level, :aktif, :createDate)");

		$encrypPass = md5($password);
		$isiAktif = 1;
		$isiDate = date("Y-m-d H:i:s");

    	$ambil->BindParam(":email", $email, PDO::PARAM_STR);
    	$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
    	$ambil->BindParam(":nama",$nama, PDO::PARAM_STR);
    	$ambil->BindParam(":agama",$agama, PDO::PARAM_STR);
    	$ambil->BindParam(":jenis_kelamin",$jenis_kelamin, PDO::PARAM_STR);
    	$ambil->BindParam(":instansi",$instansi, PDO::PARAM_STR);
    	$ambil->BindParam(":kewarganegaraan",$kewarganegaraan, PDO::PARAM_STR);
    	$ambil->BindParam(":alamat",$alamat, PDO::PARAM_STR);
    	$ambil->BindParam(":telepon",$telepon, PDO::PARAM_STR);
    	$ambil->BindParam(":level",$level, PDO::PARAM_INT);
    	$ambil->BindParam(":aktif",$isiAktif, PDO::PARAM_INT);
    	$ambil->BindParam(":createDate",$isiDate, PDO::PARAM_STR);
		$ambil->execute();

		$lastInsertId = $koneksi->lastInsertId();
		if ($lastInsertId) {
			$koneksi = null;
			return true;
		}
		else{
			$koneksi = null;
			return false;
		}
	}

	function ubahNotarisSuper($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $aktif, $level, $id_kedudukan, $password){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if($password == 0){
			$ambil=$koneksi->prepare("UPDATE notaris SET id_kedudukan=:id_kedudukan, nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan, level=:level, aktif=:aktif WHERE id_notaris =:id_notaris");
		}
		else{
			$ambil=$koneksi->prepare("UPDATE notaris SET password=:password, id_kedudukan=:id_kedudukan, nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan, level=:level, aktif=:aktif WHERE id_notaris =:id_notaris");
			$encrypPass = md5($password);
    		$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
		}


		$ambil->BindParam(":id_kedudukan", $id_kedudukan, PDO::PARAM_STR);
    	$ambil->BindParam(":nama", $nama, PDO::PARAM_STR);
    	$ambil->BindParam(":jenis_kelamin",$jenis_kelamin, PDO::PARAM_STR);
    	$ambil->BindParam(":alamat",$alamat, PDO::PARAM_STR);
    	$ambil->BindParam(":telepon",$telepon, PDO::PARAM_STR);
    	$ambil->BindParam(":sk",$sk, PDO::PARAM_STR);
    	$ambil->BindParam(":tanggal_sk",$tanggal_sk, PDO::PARAM_STR);
    	$ambil->BindParam(":no_ba_pelantikan",$no_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":tgl_ba_pelantikan",$tgl_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":level",$level, PDO::PARAM_INT);
    	$ambil->BindParam(":aktif",$aktif, PDO::PARAM_INT);
    	$ambil->BindParam(":id_notaris",$id_notaris, PDO::PARAM_INT);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function ubahNotaris($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $aktif){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("UPDATE notaris SET nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan, aktif=:aktif WHERE id_notaris =:id_notaris");

    	$ambil->BindParam(":nama", $nama, PDO::PARAM_STR);
    	$ambil->BindParam(":jenis_kelamin",$jenis_kelamin, PDO::PARAM_STR);
    	$ambil->BindParam(":alamat",$alamat, PDO::PARAM_STR);
    	$ambil->BindParam(":telepon",$telepon, PDO::PARAM_STR);
    	$ambil->BindParam(":sk",$sk, PDO::PARAM_STR);
    	$ambil->BindParam(":tanggal_sk",$tanggal_sk, PDO::PARAM_STR);
    	$ambil->BindParam(":no_ba_pelantikan",$no_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":tgl_ba_pelantikan",$tgl_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":aktif",$aktif, PDO::PARAM_INT);
    	$ambil->BindParam(":id_notaris",$id_notaris, PDO::PARAM_INT);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function ubahProfil($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $password, $foto){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//TIDAK MENGUBAH PASSWORD DAN FOTO
		if($password == null && $foto == null)
		{
			$ambil=$koneksi->prepare("UPDATE notaris SET nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan WHERE id_notaris =:id_notaris");
		}
		//TIDAK MENGUBAH PASSWORD MENGUBAH FOTO
		else if($password == null && $foto != null)
		{
			$ambil=$koneksi->prepare("UPDATE notaris SET photo=:photo, nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan WHERE id_notaris =:id_notaris");

    		$ambil->BindParam(":photo", $foto, PDO::PARAM_STR);
		}
		//MENGUBAH PASSWORD TIDAK MENGUBAH FOTO
		else if($password != null && $foto == null)
		{
			$ambil=$koneksi->prepare("UPDATE notaris SET password=:password, nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan WHERE id_notaris =:id_notaris");

			$encrypPass = md5($password);
    		$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
		}
		//MENGUBAH PASSWORD DAN FOTO
		else
		{
			$ambil=$koneksi->prepare("UPDATE notaris SET password=:password, photo=:photo, nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan WHERE id_notaris =:id_notaris");

    		$encrypPass = md5($password);
    		$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
    		$ambil->BindParam(":photo", $foto, PDO::PARAM_STR);
		}


    	$ambil->BindParam(":nama", $nama, PDO::PARAM_STR);
    	$ambil->BindParam(":jenis_kelamin",$jenis_kelamin, PDO::PARAM_STR);
    	$ambil->BindParam(":alamat",$alamat, PDO::PARAM_STR);
    	$ambil->BindParam(":telepon",$telepon, PDO::PARAM_STR);
    	$ambil->BindParam(":sk",$sk, PDO::PARAM_STR);
    	$ambil->BindParam(":tanggal_sk",$tanggal_sk, PDO::PARAM_STR);
    	$ambil->BindParam(":no_ba_pelantikan",$no_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":tgl_ba_pelantikan",$tgl_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":id_notaris",$id_notaris, PDO::PARAM_INT);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function ubahNotarisPassword($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $aktif,$password){

		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("UPDATE notaris SET password=:password, nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan, aktif=:aktif WHERE id_notaris =:id_notaris");

		$encrypPass = md5($password);
    	$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
    	$ambil->BindParam(":nama", $nama, PDO::PARAM_STR);
    	$ambil->BindParam(":jenis_kelamin",$jenis_kelamin, PDO::PARAM_STR);
    	$ambil->BindParam(":alamat",$alamat, PDO::PARAM_STR);
    	$ambil->BindParam(":telepon",$telepon, PDO::PARAM_STR);
    	$ambil->BindParam(":sk",$sk, PDO::PARAM_STR);
    	$ambil->BindParam(":tanggal_sk",$tanggal_sk, PDO::PARAM_STR);
    	$ambil->BindParam(":no_ba_pelantikan",$no_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":tgl_ba_pelantikan",$tgl_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":aktif",$aktif, PDO::PARAM_STR);
    	$ambil->BindParam(":id_notaris",$id_notaris, PDO::PARAM_INT);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function ubahProfilPassword($koneksi, $id_notaris, $nama, $jenis_kelamin, $alamat, $telepon, $sk, $tanggal_sk, $no_ba_pelantikan, $tgl_ba_pelantikan, $password){

		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("UPDATE notaris SET password=:password, nama=:nama, jenis_kelamin=:jenis_kelamin, alamat=:alamat, telepon=:telepon, sk=:sk, tanggal_sk=:tanggal_sk, no_ba_pelantikan=:no_ba_pelantikan, tgl_ba_pelantikan=:tgl_ba_pelantikan WHERE id_notaris =:id_notaris");

		$encrypPass = md5($password);
    	$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
    	$ambil->BindParam(":nama", $nama, PDO::PARAM_STR);
    	$ambil->BindParam(":jenis_kelamin",$jenis_kelamin, PDO::PARAM_STR);
    	$ambil->BindParam(":alamat",$alamat, PDO::PARAM_STR);
    	$ambil->BindParam(":telepon",$telepon, PDO::PARAM_STR);
    	$ambil->BindParam(":sk",$sk, PDO::PARAM_STR);
    	$ambil->BindParam(":tanggal_sk",$tanggal_sk, PDO::PARAM_STR);
    	$ambil->BindParam(":no_ba_pelantikan",$no_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":tgl_ba_pelantikan",$tgl_ba_pelantikan, PDO::PARAM_STR);
    	$ambil->BindParam(":id_notaris",$id_notaris, PDO::PARAM_INT);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}


	function deleteLaporan($koneksi, $id_laporan){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("DELETE FROM laporan WHERE id_laporan =:id_laporan");
    	$ambil->BindParam(":id_laporan",$id_laporan, PDO::PARAM_INT);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function deleteNotaris($koneksi, $id_notaris){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("DELETE FROM notaris WHERE id_notaris =:id_notaris");
    	$ambil->BindParam(":id_notaris",$id_notaris, PDO::PARAM_INT);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function addToCode($koneksi, $email){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("INSERT INTO tocode(email, secretCode) VALUES (:email, :secretCode)");
		$secretCode = acakKode();
    	$ambil->BindParam(":email", $email, PDO::PARAM_STR);
    	$ambil->BindParam(":secretCode", $secretCode, PDO::PARAM_STR);
		$ambil->execute();

		$lastInsertId = $koneksi->lastInsertId();
		if ($lastInsertId) {
			$koneksi = null;
			return $secretCode;
		}
		else{
			$koneksi = null;
			return "Gagal";
		}
	}

	function cekCode($koneksi, $email, $secretCode){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ambil=$koneksi->prepare("SELECT * FROM tocode WHERE email=:isiemail and secretCode=:isiscode");
        $ambil->BindParam(":isiemail",$email,PDO::PARAM_STR);
        $ambil->BindParam(":isiscode",$secretCode,PDO::PARAM_STR);
		$ambil->execute();
		$count = $ambil->rowCount();
		if($count == 1)
		{
			return true;
		}
		else
		{
			$koneksi = null;
			return false;
		}
	}

	function aktivasiAkun($koneksi, $email, $secretCode){
		if(cekCode($koneksi, $email, $secretCode))
		{
			$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$ambil=$koneksi->prepare("UPDATE notaris set aktif =:aktif WHERE email=:email");
			$isiAktif = 1;
        	$ambil->BindParam(":aktif", $isiAktif, PDO::PARAM_INT);
        	$ambil->BindParam(":email", $email, PDO::PARAM_STR);
        	$ambil->execute();
        	$count = $ambil->rowCount();
        	if($count != 0)
        	{
        		$koneksi = null;
        		return true;
        	}
        	else
        	{
        		$koneksi = null;
        		return false;
        	}
		}
		else
		{
			return false;
		}

	}

	function acakKode(){
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array();

	   //masukkan -2 dalam string length
	    $panjangAlpha = strlen($alphabet) - 2;
	    for ($i = 0; $i < 16; $i++) {
	        $n = rand(0, $panjangAlpha);
	        $pass[] = $alphabet[$n];
	    }

	   //ubah array menjadi string
	    return implode($pass);
	}

	function resetPassword($koneksi, $email, $password){

		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("UPDATE notaris SET password=:password WHERE email =:email");

		$encrypPass = md5($password);
    	$ambil->BindParam(":password", $encrypPass, PDO::PARAM_STR);
    	$ambil->BindParam(":email",$email, PDO::PARAM_STR);
		$ambil->execute();

		$count = $ambil->rowCount();
    	if($count != 0)
    	{
    		$koneksi = null;
    		return true;
    	}
    	else
    	{
    		$koneksi = null;
    		return false;
    	}
	}

	function cekEmailReset($koneksi, $email, $level){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ambil=$koneksi->prepare("SELECT * FROM notaris WHERE email=:isiemail and level=:level");
        $ambil->BindParam(":isiemail",$email,PDO::PARAM_STR);
        $ambil->BindParam(":level",$level,PDO::PARAM_INT);
		$ambil->execute();
		$count = $ambil->rowCount();
		if($count == 0)
		{
			$koneksi = null;
			return true;
		}
		else
		{
			$koneksi = null;
			return false;
		}
	}

	function jmlLaporanRanged($koneksi, $tgl_awal, $tgl_akhir){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$ambil=$koneksi->prepare("SELECT count(id_laporan) as 'jml'
		FROM laporan
		WHERE date(tanggal) BETWEEN :tgl_awal and :tgl_akhir");

		$ambil->BindParam(":tgl_awal",$tgl_awal, PDO::PARAM_STR);
		$ambil->BindParam(":tgl_akhir",$tgl_akhir, PDO::PARAM_STR);


		$ambil->execute();
		$count = $ambil->rowCount();
		$row=$ambil->fetch();

		if($count > 0){
			$koneksi = null;
		   	return $row['jml'];
		}
		else{
			$koneksi = null;
			return "null";
		}
	}
	function jmlNotarisRanged($koneksi, $tgl_awal, $tgl_akhir){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	$level =2;

    	$ambil=$koneksi->prepare("SELECT count(id_notaris) as 'jml'
		FROM notaris
		WHERE level=:level and
		date(createDate) BETWEEN :tgl_awal and :tgl_akhir
		");

		$ambil->BindParam(":tgl_awal",$tgl_awal, PDO::PARAM_STR);
		$ambil->BindParam(":tgl_akhir",$tgl_akhir, PDO::PARAM_STR);
		$ambil->BindParam(":level",$level,PDO::PARAM_INT);

		$ambil->execute();
		$count = $ambil->rowCount();
		$row=$ambil->fetch();

		if($count > 0){
			$koneksi = null;
		   	return $row['jml'];
		}
		else{
			$koneksi = null;
			return "null";
		}
	}

	function chartLaporanPerbulan($koneksi, $tahun, $bulan, $kedudukan){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("select count(laporan.id_laporan) as 'jml'
				from laporan
				join notaris on notaris.id_notaris = laporan.id_notaris
				where YEAR(laporan.tanggal) =:tahun
				AND MONTH(laporan.tanggal) =:bulan
				AND notaris.id_kedudukan =:kedudukan");

		$ambil->BindParam(":tahun", $tahun, PDO::PARAM_INT);
		$ambil->BindParam(":bulan", $bulan, PDO::PARAM_INT);
		$ambil->BindParam(":kedudukan", $kedudukan, PDO::PARAM_INT);
		$ambil->execute();
		$count = $ambil->rowCount();
		$row=$ambil->fetch();


		if($count > 0){
			$koneksi = null;
			return $row['jml'];
		}
		else{
			$koneksi = null;
			return 0;
		}
	}
	function chartLaporanPerTahun($koneksi, $tahun, $kedudukan){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("select count(laporan.id_laporan) as 'jml'
				from laporan
				join notaris on notaris.id_notaris = laporan.id_notaris
				where YEAR(laporan.tanggal) =:tahun
				AND notaris.id_kedudukan =:kedudukan");

		$ambil->BindParam(":tahun", $tahun, PDO::PARAM_INT);
		$ambil->BindParam(":kedudukan", $kedudukan, PDO::PARAM_INT);
		$ambil->execute();
		$count = $ambil->rowCount();
		$row=$ambil->fetch();


		if($count > 0){
			$koneksi = null;
			return $row['jml'];
		}
		else{
			$koneksi = null;
			return 0;
		}
	}
	//
	function chartNotarisPeryear($koneksi, $tahun, $kedudukan){
		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ambil=$koneksi->prepare("select count(id_notaris) as 'jml'
				from notaris
				where YEAR(createdate) = :tahun
				AND level = 2
				AND notaris.id_kedudukan =:kedudukan");

		$ambil->BindParam(":tahun", $tahun, PDO::PARAM_STR);
		$ambil->BindParam(":kedudukan", $kedudukan, PDO::PARAM_INT);
		$ambil->execute();
		$count = $ambil->rowCount();
		$row=$ambil->fetch();


		if($count > 0){
			$koneksi = null;
			return $row['jml'];
		}
		else{
			$koneksi = null;
			return 0;
		}
	}


?>