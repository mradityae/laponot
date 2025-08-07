<?php
include "header.php";
include "../config/koneksi.php";
include "../models/models.php";

// Ambil data filter
$tgl_a = $_GET['tgl_a'] ?? date('Y') . '-01-01';
$tgl_b = $_GET['tgl_b'] ?? date('Y-m-d');
$id_kedudukan = $_GET['id_kedudukan'] ?? '';
$filter_ready = $tgl_a && $tgl_b;

// Ambil daftar kedudukan
$daftar_kedudukan = $koneksi->query("SELECT id_kedudukan, nama_kedudukan FROM kedudukan ORDER BY nama_kedudukan ASC");

// Fungsi kolom bulan
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
          <div class="form-group">
            <label>Dari Tanggal</label>
            <input type="date" name="tgl_a" class="form-control" value="<?= $tgl_a ?>">
          </div>
          <div class="form-group">
            <label>Sampai Tanggal</label>
            <input type="date" name="tgl_b" class="form-control" value="<?= $tgl_b ?>">
          </div>
          <div class="form-group">
            <label>Kedudukan</label><br>
            <select name="id_kedudukan" class="form-control" required>
              <br>
              <option value="">-- Semua Kedudukan --</option>
              <?php while ($n = $daftar_kedudukan->fetch()) : ?>
                <option value="<?= $n['id_kedudukan'] ?>" <?= ($id_kedudukan == $n['id_kedudukan']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($n['nama_kedudukan']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Tampilkan</button>
          <button type="button" class="btn btn-danger" onclick="window.location='rekap_data_entitas'">Reset</button>
        </div>
      </form>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" style="display: none; text-align: center; margin-top: 20px;">
      <img src="https://i.gifer.com/ZZ5H.gif" width="50" alt="Loading..." />
      <p>Memuat data...</p>
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
                k.id_kedudukan,
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

        } catch (PDOException $e) {
            // Tulis ke log (pastikan fungsi write_log() tersedia)
            write_log("Query Error: " . $e->getMessage());
            // Bisa juga tampilkan pesan jika debugging
            echo "<div class='alert alert-danger'>Terjadi kesalahan saat mengambil data laporan: " . htmlspecialchars($e->getMessage()) . "</div>";
            $data = []; // supaya tetap return array kosong
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
                    <td>
                        <a href="daftar_laporan_entitas?tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&id_kedudukan=<?= $row['id_kedudukan']; ?>&id_notaris=<?= $row['id_notaris'] ?>" 
                          class="btn btn-info btn-sm">
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

<!-- JavaScript Spinner -->
<script>
  const form = document.querySelector("form");
  const spinner = document.getElementById("loadingSpinner");

  form.addEventListener("submit", function () {
    spinner.style.display = "block";
  });

  window.addEventListener("load", function () {
    const params = new URLSearchParams(window.location.search);
    if (params.has("tgl_a") && params.has("tgl_b")) {
      spinner.style.display = "none";
    }
  });
</script>

<?php include "footer.php"; ?>
