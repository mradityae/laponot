<?php
session_save_path('../login/session');
session_start();

// Include the main TCPDF library (search for installation path).
include("../config/koneksi.php");
include("../models/models.php");
require_once('../assets/TCPDF-main/tcpdf.php');


if(isset($_POST['submit']))
{
  // create new PDF document
  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

  // set document information
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetAuthor('Kanwil Kemenkumham Jabar');
  $pdf->SetTitle('Laporan Notaris');
  $pdf->SetSubject('Kanwil Kemenkumham Jabar');
  $pdf->SetKeywords('Laporan, Notaris, Kanwil, Jabar');

  // set default header data
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

  // set header and footer fonts
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  // set margins
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

  // set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

  // set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

  // set some language-dependent strings (optional)
  if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
  }

  // ---------------------------------------------------------

  $tanggal_awal = date('Y-m-d', strtotime($_POST['tgl_a']));
  $tanggal_akhir = date('Y-m-d', strtotime($_POST['tgl_b']));

  $tanggal_awal_display = date('F Y', strtotime($_POST['tgl_a']));
  $tanggal_akhir_display = date('F Y', strtotime($_POST['tgl_b']));

  $status = $_POST['status'];

  // set font
  $pdf->SetFont('helvetica', 'B', 20);

  // add a page
  $pdf->AddPage();

  $pdf->Write(0, 'Rekap Data Laporan Notaris', '', 0, 'C', true, 0, false, false, 0);

  $pdf->SetFont('helvetica', 'B', 15);

  $pdf->Write(0, 'Periode '.$tanggal_awal_display.' (-) '.$tanggal_akhir_display, '', 0, 'C', true, 0, false, false, 0);

  $pdf->SetFont('helvetica', 'B', 11);

  $pdf->Write(0, 'Status Laporan : '.$status,'', 0, 'C', true, 0, false, false, 0);

  $pdf->SetFont('helvetica', '', 11);

  // NON-BREAKING TABLE (nobr="true")
  $tbl ='
  <table border="1" cellpadding="4" cellspacing="4" nobr="true">
   <tr>
    <th width="5%" align="center"><b>NO</b></th>
    <th width="25%" align="center"><b>NAMA</b></th>
    <th width="20%" align="center"><b>PERIODE</b></th>
    <th width="20%" align="center"><b>STATUS</b></th>
    <th width="30%" align="center"><b>KETERANGAN</b></th>
   </tr>';

  $kedudukan = $_SESSION["kedudukan"];
  $no=1;

  if($status == "Semua"){
    $ambil=$koneksi->prepare("SELECT notaris.nama, l.tanggal, l.status, l.keterangan 
      FROM laporan as l
      join notaris on notaris.id_notaris = l.id_notaris
      WHERE notaris.id_kedudukan=:id_kedudukan and month(l.tanggal) BETWEEN month(:tgl_awal) and month(:tgl_akhir)
      order by l.tanggal asc");
  }
  else{
    $ambil=$koneksi->prepare("SELECT notaris.nama, l.tanggal, l.status, l.keterangan 
      FROM laporan as l
      join notaris on notaris.id_notaris = l.id_notaris
      WHERE notaris.id_kedudukan=:id_kedudukan and l.status =:status and month(l.tanggal) BETWEEN month(:tgl_awal) and month(:tgl_akhir)
      order by l.tanggal asc");

    $ambil->BindParam(":status",$status,PDO::PARAM_STR);
  }

  $ambil->BindParam(":id_kedudukan",$kedudukan, PDO::PARAM_INT);
  $ambil->BindParam(":tgl_awal",$tanggal_awal, PDO::PARAM_STR);
  $ambil->BindParam(":tgl_akhir",$tanggal_akhir, PDO::PARAM_STR);
  $ambil->execute();

  while($row=$ambil->fetch())
  {
    $tbl.='
        <tr>
          <td>'.$no.'</td>
          <td>'.$row["nama"].'</td>
          <td>'.date("F-Y", strtotime($row["tanggal"])).'</td>
          <td>'.$row["status"].'</td>
          <td>'.$row["keterangan"].'</td>
        </tr>
    ';
    $no++;
  }
  $koneksi =null;
  $tbl.='</table>';

  $pdf->writeHTML($tbl, true, false, false, false, '');

  // -----------------------------------------------------------------------------

  //Close and output PDF document
  $pdf->Output('LaporanNotaris.pdf', 'I');

  //============================================================+
  // END OF FILE
  //============================================================+

}
else
{
  echo "WRONG ACCESS";
        $link = "https://kabayanpasti.kemenkumham.go.id";
        header("refresh:0.1; $link");	
}
