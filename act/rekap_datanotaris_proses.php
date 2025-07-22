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
  $pdf->SetTitle('Data Notaris');
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
  $kedudukan = $_SESSION["kedudukan"];
  $tanggal_cetak = date('d F Y ');
 
  $status = $_POST['status'];

  // set font
  $pdf->SetFont('helvetica', 'B', 20);

  // add a page
  $pdf->AddPage();

  $pdf->Write(0, 'Rekap Data Notaris di Wilayah '.getWilayah($koneksi, $kedudukan), '', 0, 'C', true, 0, false, false, 0);

  $pdf->SetFont('helvetica', 'B', 15);

  $pdf->Write(0, 'Per Tanggal :'.$tanggal_cetak, '', 0, 'C', true, 0, false, false, 0);

  $pdf->SetFont('helvetica', 'B', 11);

  $pdf->Write(0, 'Status Notaris : '.$status,'', 0, 'C', true, 0, false, false, 0);

  $pdf->SetFont('helvetica', '', 11);

  // NON-BREAKING TABLE (nobr="true")
  $tbl ='
  <table border="1" cellpadding="4" cellspacing="4" nobr="true">
   <tr>
    <th width="5%" align="center"><b>NO</b></th>
    <th width="20%" align="center"><b>NAMA</b></th>
    <th width="15%" align="center"><b>JENIS KELAMIN</b></th>
    <th width="19%" align="center"><b>EMAIL</b></th>
    <th width="18%" align="center"><b>TELEPON</b></th>
    <th width="25%" align="center"><b>ALAMAT</b></th>
   </tr>';


  $no=1;

  if($status == "Semua"){
    $ambil=$koneksi->prepare("SELECT nama, jenis_kelamin, email, telepon, alamat 
      FROM notaris
      where id_kedudukan =:id_kedudukan and level = 2 
      order by nama asc");
  }
  else{
    $ambil=$koneksi->prepare("SELECT nama, jenis_kelamin, email, telepon, alamat 
      FROM notaris
      where id_kedudukan =:id_kedudukan and aktif=:aktif and level = 2 
      order by nama asc");

    if($status == "Aktif"){
      $aktif = 1;
    }
    else{
      $aktif = 0;
    }

      $ambil->BindParam(":aktif",$aktif,PDO::PARAM_STR);
  }

  $ambil->BindParam(":id_kedudukan",$kedudukan, PDO::PARAM_INT);
  $ambil->execute();

  while($row=$ambil->fetch())
  {
    $tbl.='
        <tr>
          <td>'.$no.'</td>
          <td>'.$row["nama"].'</td>
          <td>'.$row["jenis_kelamin"].'</td>
          <td>'.$row["email"].'</td>
          <td>'.$row["telepon"].'</td>
          <td>'.$row["alamat"].'</td>
        </tr>
    ';
    $no++;
  }
  $koneksi =null;
  $tbl.='</table>';

  $pdf->writeHTML($tbl, true, false, false, false, '');

  // -----------------------------------------------------------------------------

  //Close and output PDF document
  $pdf->Output('DataNotaris.pdf', 'I');

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
