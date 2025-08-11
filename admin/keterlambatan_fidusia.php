<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

session_start();
$kedudukan = $_SESSION["kedudukan"];
?>

<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line" align="center">Laporan Fidusia yang terlambat Unggah</h1>
            </div>
        </div>

        <!-- TABLE -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="rekapTable" class="table table-hover table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Notaris</th>
                                <th>Judul Akta</th>
                                <th>Tanggal Akta</th>
                                <th>Nomor Sertifikat</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT le.*, n.* FROM laporan_entitas le
                                      JOIN notaris n ON le.id_notaris = n.id_notaris
                                      WHERE n.id_kedudukan = :kedudukan AND le.status_pelanggaran = 1
                                      ORDER BY le.tanggal DESC";

                            $stmt = $koneksi->prepare($query);
                            $stmt->bindValue(":kedudukan", $kedudukan);
                            $stmt->execute();

                            $no = 1;
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['judul_akta']) . "</td>";
                                echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                                echo "<td>" . htmlspecialchars($row['no_sertifikat']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['keterangan_pelanggaran']) . "</td>";
                                echo "</tr>";
                            }

                            if ($no == 1) {
                                echo "<tr><td colspan='8' class='text-center'>Data tidak ditemukan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>
