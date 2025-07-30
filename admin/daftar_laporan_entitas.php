<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

session_start();
$id = $_SESSION["kode_user"];
$kedudukan = $_SESSION["kedudukan"];
$status = isset($_GET['status']) ? $_GET['status'] : "Semua";
$jenis_transaksi = isset($_GET['jenis_transaksi']) ? $_GET['jenis_transaksi'] : "";
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : "";
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : "";
$valid_jenis = ['Pendaftaran', 'Perubahan', 'Pembatalan', 'Penghapusan'];
$id_notaris = isset($_GET['id_notaris']) ? $_GET['id_notaris'] : "";

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
        <form method="GET" class="form" style="margin-bottom: 30px;">
            <div class="row">

                <div class="col-md-3">
                    <label for="jenis_transaksi">Jenis Transaksi</label>
                    <select name="jenis_transaksi" class="form-control">
                        <option value="">Semua</option>
                        <?php
                        foreach ($valid_jenis as $jenis) {
                            $selected = ($jenis_transaksi == $jenis) ? 'selected' : '';
                            echo "<option value=\"$jenis\" $selected>$jenis</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="id_notaris">Notaris</label>
                    <select name="id_notaris" class="form-control">
                        <option value="">Semua</option>
                        <?php
                        $stmt = $koneksi->prepare("SELECT id_notaris, nama FROM notaris WHERE id_kedudukan = :kedudukan");
                        $stmt->execute([':kedudukan' => $kedudukan]);
                        while ($notaris = $stmt->fetch()) {
                            $selected = ($id_notaris == $notaris['id_notaris']) ? 'selected' : '';
                            echo "<option value=\"{$notaris['id_notaris']}\" $selected>{$notaris['nama']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="tanggal_awal">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>" class="form-control">
                </div>
            </div>

            <div class="row" style="margin-top: 20px;">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    <a href="<?= basename(__FILE__) ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                </div>
            </div>
        </form>


        <?php
        if (!empty($tanggal_awal) && !empty($tanggal_akhir) && $tanggal_awal > $tanggal_akhir) {
            echo "<div class='alert alert-danger'>Tanggal awal tidak boleh lebih besar dari tanggal akhir.</div>";
        }
        ?>

        <!-- EXPORT BUTTONS -->
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-12 text-right">
                <button onclick="exportData('excel')" class="btn btn-success" style="border-radius: 6px; padding: 10px 20px; font-weight: 600; color: white; margin-right: 10px;">
                    <i class="fa fa-file-excel-o" style="margin-right: 5px;"></i> Export Excel
                </button>
                <button onclick="exportData('pdf')" class="btn btn-danger" style="border-radius: 6px; padding: 10px 20px; font-weight: 600; color: white;">
                    <i class="fa fa-file-pdf-o" style="margin-right: 5px;"></i> Export PDF
                </button>
            </div>
        </div>


        <!-- TABLE -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="rekapTable" class="table table-hover table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Akta</th>
                            <th>Pemberi Fidusia</th>
                            <th>Penerima Fidusia</th>
                            <th>Nomor Akta</th>
                            <th>Tipe</th>
                            <th>No Sertifikat</th>
                            <th>Jenis transaksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = "SELECT * FROM laporan_entitas le join notaris n on le.id_notaris = n.id_notaris 
                                  WHERE n.id_kedudukan = :isiid";

                        $params = [":isiid" => $kedudukan];

                        if ($status !== "Semua") {
                            $query .= " AND status = :status";
                            $params[":status"] = $status;
                        }

                        if (!empty($jenis_transaksi)) {
                            $query .= " AND jenis_transaksi = :jenis_transaksi";
                            $params[":jenis_transaksi"] = $jenis_transaksi;
                        }

                        if (!empty($id_notaris)) {
                            $query .= " AND le.id_notaris = :id_notaris";
                            $params[":id_notaris"] = $id_notaris;
                        }

                        if (!empty($tanggal_awal) && !empty($tanggal_akhir) && $tanggal_awal <= $tanggal_akhir) {
                            $query .= " AND tanggal BETWEEN :tanggal_awal AND :tanggal_akhir";
                            $params[":tanggal_awal"] = $tanggal_awal;
                            $params[":tanggal_akhir"] = $tanggal_akhir;
                        }

                        $query .= " ORDER BY tanggal DESC";
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
                            echo "<td>" . $row['tipe'] . "</td>";
                            echo "<td>" . $row['no_sertifikat'] . "</td>";
                            echo "<td><span class='label label-" . ($warna[$row['jenis_transaksi']] ?? 'default') . "'>" . $row['jenis_transaksi'] . "</span></td>";
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

<script>
    function exportData(type) {
        const idNotaris = document.querySelector('select[name="id_notaris"]').value;
        const jenisTransaksi = document.querySelector('select[name="jenis_transaksi"]').value;
        const tanggalAwal = document.querySelector('input[name="tanggal_awal"]').value;
        const tanggalAkhir = document.querySelector('input[name="tanggal_akhir"]').value;

        if (!idNotaris) {
            alert("Silakan pilih Notaris terlebih dahulu sebelum melakukan export.");
            return;
        }

        const baseUrl = "<?= $url ?>act/";
        const target = type === 'excel' ? 'export_excel.php' : 'export_pdf.php';

        const params = new URLSearchParams({
            id: idNotaris,
            jenis_transaksi: jenisTransaksi,
            tanggal_awal: tanggalAwal,
            tanggal_akhir: tanggalAkhir
        });

        window.location.href = baseUrl + target + "?" + params.toString();
    }
</script>

<?php include "footer.php"; ?>
