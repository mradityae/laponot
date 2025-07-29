<?php
ini_set('memory_limit', '1024M');
require '../config/koneksi.php';
require '../vendor/autoload.php'; // dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil filter
$tgl_a = $_GET['tgl_a'] ?? '';
$tgl_b = $_GET['tgl_b'] ?? '';
$id_kedudukan = $_GET['id_kedudukan'] ?? '';

// Fungsi bulan dinamis
function getMonthColumns($tgl_a, $tgl_b) {
  $start = new DateTime($tgl_a);
  $end = new DateTime($tgl_b);
  $end->modify('first day of next month');

  $columns = [];
  while ($start < $end) {
    $ym = $start->format('Y-m');
    $label = $start->format('F Y');
    $columns[] = [
      'ym' => $ym,
      'label' => $label,
      'sql' => "SUM(CASE WHEN DATE_FORMAT(l.tanggal, '%Y-%m') = '$ym' THEN 1 ELSE 0 END) AS `$label`"
    ];
    $start->modify('+1 month');
  }
  return $columns;
}

$bulan_cols = getMonthColumns($tgl_a, $tgl_b);
$bulan_sql = implode(",\n  ", array_column($bulan_cols, 'sql'));

// Query
$sql = "
SELECT 
  n.nama AS nama_notaris,
  k.nama_kedudukan,
  $bulan_sql,
  COUNT(l.id_laporan) AS total,
  COUNT(l.id_laporan) AS keterangan
FROM notaris n
LEFT JOIN kedudukan k ON n.id_kedudukan = k.id_kedudukan
LEFT JOIN laporan_entitas l 
  ON l.id_notaris = n.id_notaris 
  AND l.tanggal BETWEEN :tgl_a AND :tgl_b
WHERE n.level = 2";

if (!empty($id_kedudukan)) {
  $sql .= " AND n.id_kedudukan = :id_kedudukan";
}

$sql .= "
GROUP BY n.id_notaris, n.nama, k.nama_kedudukan
ORDER BY n.nama ASC";

$stmt = $koneksi->prepare($sql);
$stmt->bindParam(':tgl_a', $tgl_a);
$stmt->bindParam(':tgl_b', $tgl_b);
if (!empty($id_kedudukan)) {
  $stmt->bindParam(':id_kedudukan', $id_kedudukan);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Bangun HTML
ob_start();
?>

<style>
  body { font-family: Arial, sans-serif; font-size: 10pt; }
  table { border-collapse: collapse; width: 100%; margin-top: 10px; }
  th, td { border: 1px solid #000; padding: 4px; text-align: center; }
  th { background-color: #f2f2f2; }
</style>

<h3 align="center">FORMAT LAPORAN AKTA JAMINAN FIDUSIA YANG DIBUAT NOTARIS</h3>
<p align="center">PERIODE <?= strtoupper(date('F Y', strtotime($tgl_a))) ?> S.D <?= strtoupper(date('F Y', strtotime($tgl_b))) ?></p>
<p align="right">Tanggal Cetak: <?= date('d-m-Y') ?></p>

<table>
  <thead>
    <tr>
      <th>Nama Notaris</th>
      <th>Kedudukan</th>
      <?php foreach ($bulan_cols as $b): ?>
        <th><?= $b['label'] ?></th>
      <?php endforeach; ?>
      <th>Total</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['nama_notaris']) ?></td>
        <td><?= htmlspecialchars($row['nama_kedudukan']) ?></td>
        <?php foreach ($bulan_cols as $b): ?>
          <td><?= $row[$b['label']] ?? 0 ?></td>
        <?php endforeach; ?>
        <td><?= $row['total'] ?></td>
        <td><?= $row['keterangan'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php
$html = ob_get_clean();

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'landscape');
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('rekap_laporan_notaris_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
exit;
