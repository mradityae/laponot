<?php
include "header.php";
include "../config/koneksi.php";
include "../models/models.php";
include_once("../log_activity.php");

// Ambil data filter
$tgl_a = $_GET['tgl_a'] ?? '';
$tgl_b = $_GET['tgl_b'] ?? '';
$id_kedudukan = $_GET['id_kedudukan'] ?? '';
$id_notaris = $_GET['id_notaris'] ?? '';
$filter_ready = $tgl_a && $tgl_b && $id_kedudukan;

// Ambil daftar kedudukan
$daftar_kedudukan = $koneksi->query("SELECT id_kedudukan, nama_kedudukan FROM kedudukan ORDER BY nama_kedudukan ASC");

// Ambil daftar notaris berdasarkan kedudukan jika sudah dipilih
$daftar_notaris = [];
if (!empty($id_kedudukan)) {
    $stmt_notaris = $koneksi->prepare("SELECT id_notaris, nama FROM notaris WHERE id_kedudukan = :id_kedudukan and level='2' and aktif='1' ORDER BY nama ASC");
    $stmt_notaris->bindParam(":id_kedudukan", $id_kedudukan);
    $stmt_notaris->execute();
    $daftar_notaris = $stmt_notaris->fetchAll();
}
?>

<div id="page-wrapper">
  <div id="page-inner">
    <div class="row">
      <div class="col-md-12">
        <h1 class="page-head-line" align="center">DAFTAR LAPORAN FIDUSIA</h1>
      </div>
    </div>

    <div class="panel panel-default">
      <form method="get" action="">
        <div class="modal-body">
          <div class="form-group">
            <label>Dari Tanggal</label>
            <input type="date" name="tgl_a" class="form-control" value="<?= $tgl_a ?>" required>
          </div>
          <div class="form-group">
            <label>Sampai Tanggal</label>
            <input type="date" name="tgl_b" class="form-control" value="<?= $tgl_b ?>" required>
          </div>
          <div class="form-group">
            <label>Kedudukan</label>
            <select name="id_kedudukan" class="form-control" onchange="this.form.submit()" required>
              <option value="">-- Semua Kedudukan --</option>
              <?php while ($k = $daftar_kedudukan->fetch()) : ?>
                <option value="<?= $k['id_kedudukan'] ?>" <?= ($id_kedudukan == $k['id_kedudukan']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($k['nama_kedudukan']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <?php if (!empty($id_kedudukan)): ?>
          <div class="form-group">
            <label>Nama Notaris</label>
            <select name="id_notaris" class="form-control">
              <option value="">-- Semua Notaris --</option>
              <?php foreach ($daftar_notaris as $n) : ?>
                <option value="<?= $n['id_notaris'] ?>" <?= ($id_notaris == $n['id_notaris']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($n['nama']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Tampilkan</button>
          <button type="reset" class="btn btn-danger" onclick="window.location.href=window.location.pathname">Reset</button>
        </div>
      </form>
    </div>

    <?php if ($filter_ready): ?>
      <div style="margin-bottom: 20px;">
        <a href="<?= $url; ?>act/export_excel.php?id=<?= $id_notaris ?>&tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>" class="btn btn-success">
          <i class="fa fa-file-excel-o"></i> Export Excel
        </a>
        <a href="<?= $url; ?>act/export_pdf.php?id=<?= $id_notaris ?>&tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>" class="btn btn-danger">
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
                  <th>Pemberi</th>
                  <th>Penerima</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>
              <?php
                try {
                  $sql = "SELECT n.nama, l.pemberi, l.penerima, l.tanggal, l.status
                  FROM laporan_entitas l
                  JOIN notaris n ON l.id_notaris = n.id_notaris
                  WHERE DATE(l.tanggal) BETWEEN :tgl_a AND :tgl_b
                  AND n.id_kedudukan = :id_kedudukan";

                  if (!empty($id_notaris)) {
                      $sql .= " AND n.id_notaris = :id_notaris";
                  }

                  $sql .= " ORDER BY l.tanggal ASC";

                  $stmt = $koneksi->prepare($sql);
                  $stmt->bindParam(":tgl_a", $tgl_a);
                  $stmt->bindParam(":tgl_b", $tgl_b);
                  $stmt->bindParam(":id_kedudukan", $id_kedudukan);
                  if (!empty($id_notaris)) {
                      $stmt->bindParam(":id_notaris", $id_notaris);
                  }
                  $stmt->execute();

                  $no = 1;
                  while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>{$no}</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pemberi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['penerima']) . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                    echo "</tr>";
                    $no++;
                  }
                }
                catch(PDOException $e){
                  write_log("Error saat mengambil laporan ".$e->getMessage());
                }
              ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include "footer.php"; ?>
