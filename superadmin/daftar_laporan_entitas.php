<?php
include "header.php";
include "../config/koneksi.php";
include "../models/models.php";
include_once("../log_activity.php");

// Ambil data filter
$tgl_a = $_GET['tgl_a'] ?? date('Y') . '-01-01'; // format YYYY-MM-DD
$tgl_b = $_GET['tgl_b'] ?? date('Y-m-d'); // tanggal sekarang
$id_kedudukan = $_GET['id_kedudukan'] ?? '';
$id_notaris = $_GET['id_notaris'] ?? '';
$jenis_transaksi = $_GET['jenis_transaksi'] ?? '';
$filter_ready = $tgl_a && $tgl_b;

// Ambil daftar kedudukan
$daftar_kedudukan = $koneksi->query("SELECT id_kedudukan, nama_kedudukan FROM kedudukan ORDER BY nama_kedudukan ASC")->fetchAll();

// Ambil daftar notaris (selalu semua)
$daftar_notaris = $koneksi->query("SELECT id_notaris, nama, id_kedudukan FROM notaris WHERE level='2' AND aktif='1' ORDER BY nama ASC")->fetchAll();
?>

<div id="page-wrapper">
  <div id="page-inner">
    <div class="row">
      <div class="col-md-12">
        <h1 class="page-head-line" align="center">DAFTAR LAPORAN FIDUSIA</h1>
      </div>
    </div>

    <div class="panel panel-default">
      <form method="get" action="" onsubmit="cleanEmptyFields(this)">
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
            <select name="id_kedudukan" class="form-control" onchange="this.form.submit()">
              <option value="">-- Semua Kedudukan --</option>
              <?php foreach ($daftar_kedudukan as $k) : ?>
                <option value="<?= $k['id_kedudukan'] ?>" <?= ($id_kedudukan == $k['id_kedudukan']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($k['nama_kedudukan']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Nama Notaris</label>
            <select name="id_notaris" class="form-control">
              <option value="">-- Semua Notaris --</option>
              <?php foreach ($daftar_notaris as $n) : ?>
                <?php
                  // jika kedudukan dipilih, tampilkan notaris sesuai kedudukan, jika kosong tampil semua
                  if (!empty($id_kedudukan) && $n['id_kedudukan'] != $id_kedudukan) continue;
                ?>
                <option value="<?= $n['id_notaris'] ?>" <?= ($id_notaris == $n['id_notaris']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($n['nama']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Jenis Transaksi</label>
            <select name="jenis_transaksi" class="form-control">
              <option value="">-- Semua Jenis Transaksi --</option>
              <?php 
              $jenis_opsi = ['Pendaftaran', 'Perubahan', 'Penghapusan', 'Perbaikan'];
              foreach($jenis_opsi as $jt):
              ?>
                <option value="<?= $jt ?>" <?= ($jenis_transaksi == $jt) ? 'selected' : '' ?>><?= $jt ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Tampilkan</button>
          <button type="reset" class="btn btn-danger" onclick="window.location.href=window.location.pathname">Reset</button>
        </div>
      </form>
    </div>

    <?php if ($filter_ready): ?>
      <div style="margin-bottom: 20px;">
        <a href="<?= $url; ?>act/export_excel.php?id=<?= $id_notaris ?>&tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&jenis_transaksi=<?= $jenis_transaksi ?>" class="btn btn-success">
          <i class="fa fa-file-excel-o"></i> Export Excel
        </a>
        <a href="<?= $url; ?>act/export_pdf.php?id=<?= $id_notaris ?>&tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&jenis_transaksi=<?= $jenis_transaksi ?>" class="btn btn-danger">
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
                  <th>Kedudukan Notaris</th>
                  <th>Pemberi</th>
                  <th>Penerima</th>
                  <th>Jenis Transaksi</th>
                  <th>Nilai Penjaminan</th>
                  <th>Nomor Sertifikat</th>
                  <th>Tanggal Akta</th>
                  <th>Tanggal Input</th>
                </tr>
              </thead>
              <tbody>
              <?php
                try {
                  $sql = "SELECT n.nama, l.pemberi, l.penerima, l.tanggal, l.status, l.jenis_transaksi, l.nilai_penjaminan, l.no_sertifikat, kd.nama_kedudukan, l.created_at
                          FROM laporan_entitas l
                          JOIN notaris n ON l.id_notaris = n.id_notaris
                          JOIN kedudukan kd on n.id_kedudukan = kd.id_kedudukan
                          WHERE DATE(l.tanggal) BETWEEN :tgl_a AND :tgl_b";

                  if (!empty($id_kedudukan)) {
                      $sql .= " AND n.id_kedudukan = :id_kedudukan";
                  }

                  if (!empty($id_notaris)) {
                      $sql .= " AND n.id_notaris = :id_notaris";
                  }

                  if (!empty($jenis_transaksi)) {
                      $sql .= " AND l.jenis_transaksi = :jenis_transaksi";
                  }

                  $sql .= " ORDER BY l.created_at DESC";
                  $stmt = $koneksi->prepare($sql);
                  $stmt->bindParam(":tgl_a", $tgl_a);
                  $stmt->bindParam(":tgl_b", $tgl_b);
                  if (!empty($id_kedudukan)) $stmt->bindParam(":id_kedudukan", $id_kedudukan);
                  if (!empty($id_notaris)) $stmt->bindParam(":id_notaris", $id_notaris);
                  if (!empty($jenis_transaksi)) $stmt->bindParam(":jenis_transaksi", $jenis_transaksi);
                  $stmt->execute();

                  $no = 1;
                  while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>{$no}</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_kedudukan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pemberi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['penerima']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['jenis_transaksi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nilai_penjaminan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['no_sertifikat']) . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "</tr>";
                    $no++;
                  }
                } catch(PDOException $e){
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

<script>
  function cleanEmptyFields(form) {
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
      if (!input.value) input.name = ''; // hapus name biar nggak dikirim
    });
}
</script>

<?php include "footer.php"; ?>
