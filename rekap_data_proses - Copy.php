<?php
try
{
	if(isset($_POST['status']))
	{
		include'../config/koneksi.php';
		$tanggal_awal = date('Y-m-d', strtotime($_POST['tgl_a']));
		$tanggal_akhir = date('Y-m-d', strtotime($_POST['tgl_b']));

		$tanggal_awal_display = date('d/m/Y', strtotime($_POST['tgl_a']));
		$tanggal_akhir_display = date('d/m/Y', strtotime($_POST['tgl_b']));

		$status = $_POST['status'];


		$content = "
		<html>
			<body>
				<div style='padding:4mm; border:2px solid;' align='center'>
					<span style='font-size:20px;'>DATA PELANTIKAN KUMHAM JABAR PERIODE : ".$tanggal_awal_display." - ".$tanggal_akhir_display."</span>
				</div>
				<br><br><br>
				
				<table border='1' align='center' width='100%'>
					<tr>
				        <th align='center'>No</th>
				        <th align='center'>Nama</th>
				        <th align='center'>Agama</th>
				        <th align='center'>Instansi</th>
				        <th align='center'>Jenis Pelayanan</th>
				        <th align='center'>Status</th>
				        <th align='center'>Tanggal Pelantikan</th>
				    </tr>
				    ";

				    $no=1;                   
                   

				    if($status == "Semua")
				    {
				    	$ambil=$koneksi->prepare("SELECT pengguna.nama, pengguna.instansi, pengguna.id, pengguna.agama, id_pelantikan, tanggal_daftar, tanggal_pelantikan, status, jenis_pelantikan,fileKirim,keterangan  FROM pelantikan join pengguna ON pelantikan.id = pengguna.id  WHERE tanggal_daftar between :tanggalAwal and :tanggalAkhir order by tanggal_pelantikan desc");
			    	   	$ambil->BindParam(":tanggalAwal",$tanggal_awal);
                    	$ambil->BindParam(":tanggalAkhir",$tanggal_akhir);
				    }
				    else
				    {
				    	$ambil=$koneksi->prepare("SELECT pengguna.nama, pengguna.instansi, pengguna.id, pengguna.agama, id_pelantikan, tanggal_daftar, tanggal_pelantikan, status, jenis_pelantikan,fileKirim,keterangan  FROM pelantikan join pengguna ON pelantikan.id = pengguna.id  WHERE tanggal_daftar between :tanggalAwal and :tanggalAkhir and status=:status order by tanggal_pelantikan desc");
			    	   	$ambil->BindParam(":tanggalAwal",$tanggal_awal);
                    	$ambil->BindParam(":tanggalAkhir",$tanggal_akhir);
                    	$ambil->BindParam(":status",$status);
				    }
                    $ambil->execute();
		           
		            while ($row=$ambil->fetch())
		            {
		            	if($row['tanggal_pelantikan'] == null || $row['tanggal_pelantikan'] == "")
		            	{
		            		$tanggal = "-";
		            	}
		            	else
		            	{
		            		$tanggal = date('d-m-Y', strtotime($row['tanggal_pelantikan']));

		            	}

		        		$content.= "
		        		<tr>
		    			<td align='center'>".$no."</td>
				        <td>".$row['nama']."</td>
				        <td align='center'>".$row['agama']."</td>
				        <td align='center'>".$row['instansi']."</td>
				        <td>".$row['jenis_pelantikan']."</td>
				        <td align='center'>".$row['status']."</td>
				        <td align='center'>".$tanggal."</td>
				        </tr>
		        		";
		    			$no++;
		            }

		$content.= "
				</table>
			</body
		</html>
		";


		require_once('../assets/html2pdf/html2pdf.class.php');
		$html2pdf = new HTML2PDF('P','LEGAL','en');
		$html2pdf->WriteHTML($content);
		ob_end_clean();
		$html2pdf->Output('data_pelantikan.pdf');
	}
	else
	{
		$link = $url;
		header("refresh:0.1; url=$link");
	}
}
catch(Exception $Ex)
{
	echo "<script>alert('Cetak Laporan Gagal.')</script>";
	$link = $url."admin/index.php";
	header("refresh:0.1; url=$link");
}
?>