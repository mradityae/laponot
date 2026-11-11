<?php
include "header.php";
include("../config/koneksi.php");

// Set default tahun jika tidak ada filter
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$aktif = isset($_GET['aktif']) ? $_GET['aktif'] : "Aktif";

$kedudukan = $_SESSION["kedudukan"] ?? ($_GET['id_kedudukan'] ?? '');
?>
<!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line" align="left">Daftar Notaris & Rekap Akta</h1>
            </div>
        </div>
        
        <!-- Filter & Button Row -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-8">
                <form method="GET" action="" class="form-inline">
                    <input type="hidden" name="aktif" value="<?php echo htmlspecialchars($aktif); ?>">
                    <div class="form-group">
                        <label for="tahun">Pilih Tahun: </label>
                        <select name="tahun" id="tahun" class="form-control" onchange="this.form.submit()">
                            <?php 
                            $tahun_sekarang = date('Y');
                            for($t = $tahun_sekarang; $t >= 2020; $t--){
                                $selected = ($t == $tahun) ? 'selected' : '';
                                echo "<option value='$t' $selected>$t</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </form>
            </div>
            <div class="col-md-4" align="right">
                <!-- NAMA FILE DISESUAIKAN DAN PARAMETER id_kedudukan DITAMBAHKAN VIA URL -->
                <a href="export_excel_notaris.php?tahun=<?php echo $tahun; ?>&aktif=<?php echo $aktif; ?>&id_kedudukan=<?php echo $kedudukan; ?>" class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i> Export Excel
                </a>
            </div>
        </div>

        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm table-bordered data" align="left">
                            <thead>
                                <tr>
                                    <th align='center' rowspan="2" style="vertical-align: middle;">No</th>
                                    <th align='center' rowspan="2" style="vertical-align: middle;">Nama Notaris</th>
                                    <th align='center' rowspan="2" style="vertical-align: middle;">Status</th>
                                    <th align='center' colspan="12">Jumlah Akta (Dibukukan + Disahkan + Protes) Tahun <?php echo $tahun; ?></th>
                                    <th align='center' rowspan="2" style="vertical-align: middle;">Total Tahun</th>
                                    <th align='center' rowspan="2" style="vertical-align: middle;">Laporan</th>
                                    <th align='center' rowspan="2" style="vertical-align: middle;">Detail</th>
                                    <th align='center' rowspan="2" style="vertical-align: middle;">Hapus</th>
                                </tr>
                                <tr>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mar</th>
                                    <th>Apr</th>
                                    <th>Mei</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Agu</th>
                                    <th>Sep</th>
                                    <th>Okt</th>
                                    <th>Nov</th>
                                    <th>Des</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $no = 1;
                                    $level = 2;

                                    $sql = "
                                        SELECT n.*, 
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 1 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS jan,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 2 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS feb,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 3 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS mar,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 4 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS apr,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 5 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS mei,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 6 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS jun,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 7 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS jul,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 8 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS agu,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 9 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS sep,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 10 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS okt,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 11 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS nov,
                                            COALESCE(SUM(CASE WHEN MONTH(l.tanggal) = 12 THEN (l.jml_tangan_dibukukan + l.jml_tangan_disahkan + l.jml_buku_protes) ELSE 0 END), 0) AS des
                                        FROM notaris n 
                                        LEFT JOIN laporan l ON n.id_notaris = l.id_notaris AND YEAR(l.tanggal) = :tahun
                                        WHERE n.level = :level AND n.id_kedudukan = :id_kedudukan
                                    ";

                                    if($aktif == "0"){
                                        $sql .= " AND n.aktif = :aktif ";
                                    }

                                    $sql .= " GROUP BY n.id_notaris ORDER BY n.nama ASC";

                                    $ambil = $koneksi->prepare($sql);
                                    $ambil->bindParam(":tahun", $tahun);
                                    $ambil->bindParam(":level", $level);
                                    $ambil->bindParam(":id_kedudukan", $kedudukan);
                                    
                                    if($aktif == "0"){
                                        $ambil->bindParam(":aktif", $aktif);
                                    }

                                    $ambil->execute();
                                    while ($row = $ambil->fetch())
                                    {
                                        $total_tahun = $row['jan'] + $row['feb'] + $row['mar'] + $row['apr'] + $row['mei'] + $row['jun'] + $row['jul'] + $row['agu'] + $row['sep'] + $row['okt'] + $row['nov'] + $row['des'];

                                        echo "<tr>";
                                        echo "<td align='center'>".$no."</td>";
                                        echo "<td>".$row['nama']."</td>";
                                        echo "<td>".($row['aktif'] == 1 ? "Aktif" : "Belum Aktif")."</td>";
                                        
                                        // Kolom per Bulan
                                        echo "<td align='center'>".$row['jan']."</td>";
                                        echo "<td align='center'>".$row['feb']."</td>";
                                        echo "<td align='center'>".$row['mar']."</td>";
                                        echo "<td align='center'>".$row['apr']."</td>";
                                        echo "<td align='center'>".$row['mei']."</td>";
                                        echo "<td align='center'>".$row['jun']."</td>";
                                        echo "<td align='center'>".$row['jul']."</td>";
                                        echo "<td align='center'>".$row['agu']."</td>";
                                        echo "<td align='center'>".$row['sep']."</td>";
                                        echo "<td align='center'>".$row['okt']."</td>";
                                        echo "<td align='center'>".$row['nov']."</td>";
                                        echo "<td align='center'>".$row['des']."</td>";
                                        
                                        // Total Akta 1 Tahun
                                        echo "<td align='center'><strong>".$total_tahun."</strong></td>";

                                        // Aksi
                                        echo "<td align='center'><a href='daftar_laporan_notaris.php?id=".$row['id_notaris']."&nama=".$row['nama']."'><img src='../assets/img/clipboard.png' border='0' height='20' width='20'></a></td>";
                                        echo "<td align='center'><a href='edit_notaris.php?id=".$row['id_notaris']."&nama=".$row['nama']."'><img src='../assets/img/edit.png' border='0' height='20' width='20'></a></td>";
                                        echo "<td align='center'><a href='#' onclick='deletenotaris(".$row['id_notaris'].")'><img src='../assets/img/delete.png' border='0' height='20' width='20'></a></td>";
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
</div>
<?php
include "footer.php";
?>