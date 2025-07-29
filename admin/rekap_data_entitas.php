<?php
include "header.php";
include "../config/koneksi.php";
include "../models/models.php";
include_once("../log_activity.php");

// Ambil dari session
$id_kedudukan = $_SESSION['kedudukan'];

// Ambil data filter tanggal
$tgl_a = $_GET['tgl_a'] ?? '';
$tgl_b = $_GET['tgl_b'] ?? '';
$filter_ready = $tgl_a && $tgl_b;

// Fungsi kolom bulan dinamis
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
?>

<div id="page-wrapper">
  <div id="page-inner">
    <div class="row">
      <div class="col-md-12">
        <h1 class="page-head-line" align="center">REKAP LAPORAN FIDUSIA</h1>
      </div>
    </div>

    <div class="panel panel-default">
      <form method="get" action="">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="tgl_a" class="form-label">Dari Tanggal</label>
                <input type="date" id="tgl_a" name="tgl_a" class="form-control form-control-sm" value="<?= $tgl_a ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="tgl_b" class="form-label">Sampai Tanggal</label>
                <input type="date" id="tgl_b" name="tgl_b" class="form-control form-control-sm" value="<?= $tgl_b ?>" required>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Tampilkan</button>
          <button type="button" class="btn btn-danger" onclick="window.location='rekap_data_entitas'">Reset</button>
        </div>
      </form>
    </div>

    <?php if ($filter_ready): ?>
      <?php
        try {
            $bulan_cols = getMonthColumns($tgl_a, $tgl_b);
            $bulan_sql = implode(",\n  ", array_column($bulan_cols, 'sql'));

            $sql = "
              SELECT
                n.id_notaris, 
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
              WHERE n.level = 2
                AND n.id_kedudukan = :id_kedudukan
              GROUP BY n.id_notaris, n.nama, k.nama_kedudukan
              ORDER BY n.nama ASC";

            $stmt = $koneksi->prepare($sql);
            $stmt->bindParam(':tgl_a', $tgl_a);
            $stmt->bindParam(':tgl_b', $tgl_b);
            $stmt->bindParam(':id_kedudukan', $id_kedudukan);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            write_log("Berhasil mengambil rekap laporan per notaris dari $tgl_a sampai $tgl_b untuk kedudukan ID $id_kedudukan");
        } catch (PDOException $e) {
            write_log("ERROR saat ambil rekap laporan notaris: " . $e->getMessage());
            $data = []; // supaya tetap aman diproses meskipun error
        }

      ?>

      <div style="margin-bottom: 20px;">
        <a href="<?= $url ?>act/export_excel_rekap.php?tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&id_kedudukan=<?= $id_kedudukan ?>" class="btn btn-success">
          <i class="fa fa-file-excel-o"></i> Export Excel
        </a>
        <a href="<?= $url ?>act/export_pdf_rekap.php?tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&id_kedudukan=<?= $id_kedudukan ?>" class="btn btn-danger">
          <i class="fa fa-file-pdf-o"></i> Export PDF
        </a>
      </div>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="table-responsive">
            <h4>Rekap dari <b><?= date('d M Y', strtotime($tgl_a)) ?></b> sampai <b><?= date('d M Y', strtotime($tgl_b)) ?></b></h4>
            <table id="rekapTable" class="table table-bordered table-striped display nowrap" style="width:100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Notaris</th>
                  <th>Kedudukan</th>
                  <?php foreach ($bulan_cols as $b): ?>
                    <th><?= $b['label'] ?></th>
                  <?php endforeach; ?>
                  <th>Total</th>
                  <th>Keterangan</th>
                  <th>Detail</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; ?>
                <?php foreach ($data as $row): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_notaris']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kedudukan']) ?></td>
                    <?php foreach ($bulan_cols as $b): ?>
                      <td><?= $row[$b['label']] ?? 0 ?></td>
                    <?php endforeach; ?>
                    <td><?= $row['total'] ?></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td>
                      <a href="rekap_laporan_entitas?tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&id_notaris=<?= $row['id_notaris'] ?? '' ?>" class="btn btn-info btn-sm">
                        <i class="fa fa-search"></i> Lihat
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  table.dataTable thead th {
    white-space: nowrap;
  }
</style>

<?php include "footer.php"; ?>
