<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

session_start();
$id = $_SESSION["kode_user"];
$status = isset($_GET['status']) ? $_GET['status'] : "Semua";
$jenis_transaksi = isset($_GET['jenis_transaksi']) ? $_GET['jenis_transaksi'] : "";
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : "";
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : "";

$valid_jenis = ['Pendaftaran', 'Perubahan', 'Perbaikan', 'Penghapusan'];
if (!in_array($jenis_transaksi, $valid_jenis)) {
    $jenis_transaksi = "";
}

?>

<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line" align="center">Daftar Laporan Fidusia</h1>
            </div>
        </div>

        <!-- FILTER FORM -->
        <form method="GET" class="form-inline" style="margin-bottom: 20px;">
            <div class="form-group">
                <label for="jenis_transaksi">Jenis Transaksi: </label>
                <select name="jenis_transaksi" class="form-control" style="margin: 0 10px;">
                    <option value="">Semua</option>
                    <?php
                    foreach ($valid_jenis as $jenis) {
                        $selected = ($jenis_transaksi == $jenis) ? 'selected' : '';
                        echo "<option value=\"$jenis\" $selected>$jenis</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_awal">Periode: </label>
                <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>" class="form-control" style="margin: 0 10px;">
                <span>sampai</span>
                <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>" class="form-control" style="margin: 0 10px;">
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= basename(__FILE__) ?>" class="btn btn-default">Reset</a>
        </form>

        <?php
        if (!empty($tanggal_awal) && !empty($tanggal_akhir) && $tanggal_awal > $tanggal_akhir) {
            echo "<div class='alert alert-danger'>Tanggal awal tidak boleh lebih besar dari tanggal akhir.</div>";
        }
        ?>

        <!-- EXPORT BUTTONS -->
        <a href="<?= $url; ?>act/export_excel.php?id=<?= $id ?>&jenis_transaksi=<?= urlencode($jenis_transaksi) ?>&tanggal_awal=<?= urlencode($tanggal_awal) ?>&tanggal_akhir=<?= urlencode($tanggal_akhir) ?>"
           class="btn btn-success me-2" style="border-radius: 6px; padding: 10px 20px; font-weight: 600; color: white;" title="Export ke Excel">
            <i class="fa fa-file-excel-o" style="margin-right: 5px;"></i> Export Excel
        </a>
        <a href="<?= $url; ?>act/export_pdf.php?id=<?= $id ?>&jenis_transaksi=<?= urlencode($jenis_transaksi) ?>&tanggal_awal=<?= urlencode($tanggal_awal) ?>&tanggal_akhir=<?= urlencode($tanggal_akhir) ?>"
           class="btn btn-danger" style="border-radius: 6px; padding: 10px 20px; font-weight: 600; color: white;" title="Export ke PDF">
            <i class="fa fa-file-pdf-o" style="margin-right: 5px;"></i> Export PDF
        </a>

        <!-- TABLE -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="rekapTable" class="table table-hover table-striped table-bordered" style="width:100%;">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Akta</th>
                            <th>Pemberi Fidusia</th>
                            <th>Penerima Fidusia</th>
                            <th>Nomor Akta</th>
                            <th>No Sertifikat</th>
                            <th>Tanggal Input</th>
                            <th>Jenis transaksi</th>
                            <th>Edit</th>
                            <th>Hapus</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = "SELECT * FROM laporan_entitas WHERE id_notaris = :isiid";
                        $params = [":isiid" => $id];

                        if ($status !== "Semua") {
                            $query .= " AND status = :status";
                            $params[":status"] = $status;
                        }

                        if (!empty($jenis_transaksi)) {
                            $query .= " AND jenis_transaksi = :jenis_transaksi";
                            $params[":jenis_transaksi"] = $jenis_transaksi;
                        }

                        if (!empty($tanggal_awal) && !empty($tanggal_akhir) && $tanggal_awal <= $tanggal_akhir) {
                            $query .= " AND tanggal BETWEEN :tanggal_awal AND :tanggal_akhir";
                            $params[":tanggal_awal"] = $tanggal_awal;
                            $params[":tanggal_akhir"] = $tanggal_akhir;
                        }

                        $query .= " ORDER BY created_at DESC";
                        $ambil = $koneksi->prepare($query);
                        foreach ($params as $key => $val) {
                            $ambil->bindValue($key, $val);
                        }
                        $ambil->execute();

                        $warna = [
                            'Pendaftaran' => 'success',
                            'Perubahan' => 'info',
                            'Pembatalan' => 'warning',
                            'Penghapusan' => 'danger'
                        ];

                        $no = 1;
                        while ($row = $ambil->fetch()) {
                            echo "<tr>";
                            echo "<td>" . $no . "</td>";
                            echo "<td>" . date('d-F-Y', strtotime($row['tanggal'])) . "</td>";
                            echo "<td>" . $row['pemberi'] . "</td>";
                            echo "<td>" . $row['penerima'] . "</td>";
                            echo "<td>" . $row['nomor'] . "</td>";
                            echo "<td>" . $row['no_sertifikat'] . "</td>";
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "<td><span class='label label-" . ($warna[$row['jenis_transaksi']] ?? 'default') . "'>" . $row['jenis_transaksi'] . "</span></td>";
                            echo "<td align='center'>
                                    <a href='edit_laporan.php?id=" . $row['id_laporan'] . "' title='Edit'>
                                        <img src='../assets/img/edit.png' height='20'>
                                    </a>
                                  </td>";
                            echo "<td align='center'>
                                    <a href='hapus_laporan.php?id=" . $row['id_laporan'] . "' title='Hapus' onclick=\"return confirm('Yakin ingin menghapus laporan ini?')\">
                                        <img src='../assets/img/delete.png' height='20'>
                                    </a>
                                  </td>";
                            echo "</tr>";
                            $no++;
                        }
                        $koneksi = null;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>
