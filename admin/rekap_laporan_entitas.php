<?php
include "header.php";
include "../config/koneksi.php";
include "../models/models.php";
include_once("../log_activity.php");

// Ambil data filter
$tgl_a = $_GET['tgl_a'] ?? '';
$tgl_b = $_GET['tgl_b'] ?? '';
$id_notaris = $_GET['id_notaris'] ?? '';
$id_kedudukan_session = $_SESSION['kedudukan'] ?? '';
$filter_ready = $tgl_a && $tgl_b;
$jenis_transaksi = $_GET['jenis_transaksi'] ?? '';

// Ambil daftar notaris berdasarkan kedudukan session
if (!empty($id_kedudukan_session)) {
    $stmt_nama = $koneksi->prepare("SELECT id_notaris, nama FROM notaris WHERE id_kedudukan = :id_kedudukan and aktif='1' and level='2' ORDER BY nama ASC");
    $stmt_nama->bindParam(':id_kedudukan', $id_kedudukan_session, PDO::PARAM_INT);
    $stmt_nama->execute();
    $daftar_nama = $stmt_nama;
} else {
    $daftar_nama = $koneksi->query("SELECT id_notaris, nama FROM notaris WHERE aktif ='1' and level='2' ORDER BY nama ASC");
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
            <label>Nama Notaris</label>
            <select name="id_notaris" class="form-control" required>
              <option value="" disabled <?= empty($id_notaris) ? 'selected' : '' ?>>-- Pilih Notaris --</option>
              <?php while ($n = $daftar_nama->fetch()) : ?>
                <option value="<?= $n['id_notaris'] ?>" <?= ($id_notaris == $n['id_notaris']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($n['nama']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Jenis Laporan</label>
            <select name="jenis_transaksi" class="form-control">
              <option value="">-- Semua Jenis --</option>
              <?php
                $jenis_list = ['Pendaftaran', 'Perubahan', 'Pembatalan', 'Penghapusan'];
                foreach ($jenis_list as $j) {
                  $selected = ($_GET['jenis_transaksi'] ?? '') === $j ? 'selected' : '';
                  echo "<option value=\"$j\" $selected>$j</option>";
                }
              ?>
            </select>
        </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Tampilkan</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </div>
      </form>
    </div>

    <?php if ($filter_ready): ?>
      <div style="margin-bottom: 20px;">
        <a href="<?php echo $url; ?>act/export_excel.php?id=<?= $id_notaris ?>&tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&jenis_transaksi=<?= $jenis_transaksi ?>" class="btn btn-success">
          <i class="fa fa-file-excel-o"></i> Export Excel
        </a>
        <a href="<?php echo $url; ?>act/export_pdf.php?id=<?= $id_notaris ?>&tgl_a=<?= $tgl_a ?>&tgl_b=<?= $tgl_b ?>&jenis_transaksi=<?= $jenis_transaksi ?>" class="btn btn-danger">
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
                  <th>Jenis Transaksi</th>
                  <th>Tanggal Akta</th>
                  <th>No Sertifikat</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
              <?php
                  try {
                    $sql = "SELECT n.nama, l.jenis_transaksi, l.pemberi, l.penerima, l.tanggal, l.status, l.no_sertifikat, l.keterangan_pelanggaran
                            FROM laporan_entitas l
                            JOIN notaris n ON l.id_notaris = n.id_notaris
                            WHERE DATE(l.tanggal) BETWEEN :tgl_a AND :tgl_b 
                              AND n.id_kedudukan = :id_kedudukan 
                              AND level = '2'";

                    if (!empty($id_notaris)) {
                        $sql .= " AND n.id_notaris = :id_notaris";
                    }
                    if (!empty($jenis_transaksi)) {
                        $sql .= " AND l.jenis_transaksi = :jenis_transaksi";
                    }
                    


                    $sql .= " ORDER BY l.tanggal ASC";

                    $stmt = $koneksi->prepare($sql);
                    $stmt->bindParam(":tgl_a", $tgl_a);
                    $stmt->bindParam(":tgl_b", $tgl_b);
                    $stmt->bindParam(":id_kedudukan", $id_kedudukan_session);

                    if (!empty($id_notaris)) {
                        $stmt->bindParam(":id_notaris", $id_notaris);
                    }
                    if (!empty($jenis_transaksi)) {
                        $stmt->bindParam(":jenis_transaksi", $jenis_transaksi);
                    }

                    $stmt->execute();

                    write_log("Berhasil mengambil data laporan entitas dari $tgl_a sampai $tgl_b (Kedudukan ID $id_kedudukan_session" . (!empty($id_notaris) ? ", Notaris ID $id_notaris" : "") . ")");

                    $no = 1;
                    while ($row = $stmt->fetch()) {
                        echo "<tr>";
                        echo "<td>{$no}</td>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['pemberi']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['penerima']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_transaksi']) . "</td>";
                        echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['no_sertifikat']) . "</td>";
                        echo "<td>" . (empty($row['tanggal']) ? '-' : $row['keterangan_pelanggaran'])  . "</td>";
                        echo "</tr>";
                        $no++;
                    }
                } catch (PDOException $e) {
                    write_log("ERROR saat mengambil laporan entitas: " . $e->getMessage());
                    echo "<tr><td colspan='6'>Terjadi kesalahan saat mengambil data.</td></tr>";
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
