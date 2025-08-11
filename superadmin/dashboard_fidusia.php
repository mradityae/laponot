<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models_fidusia.php");

$tahunIni = date('Y');
$bulanSekarang = date('n'); // bulan angka
$tahunSekarang = date('Y');

// Ambil data grafik bulanan
$dataChart = getChartLaporanTahunanSuper($koneksi, $tahunIni);
$jumlahTahunIni = array_sum($dataChart);

// Ambil top jenis transaksi
$topJenis = getTopJenisTransaksiSuper($koneksi);

// Ambil data telat upload per kedudukan
$sqlKedudukan = "SELECT 
                    k.nama_kedudukan, COUNT(*) AS total 
                 FROM laporan_entitas AS le
                 JOIN notaris AS n ON le.id_notaris = n.id_notaris
                 JOIN kedudukan AS k ON n.id_kedudukan = k.id_kedudukan
                 WHERE le.status_pelanggaran='1'
                 AND MONTH(le.tanggal) = :bulan
                 AND YEAR(le.tanggal) = :tahun
                 GROUP BY k.nama_kedudukan
                 ORDER BY total DESC";
$stmtKedudukan = $koneksi->prepare($sqlKedudukan);
$stmtKedudukan->execute([
    'bulan' => $bulanSekarang,
    'tahun' => $tahunSekarang
]);

$dataKedudukan = $stmtKedudukan->fetchAll(PDO::FETCH_ASSOC);

$kedudukanLabels = [];
$kedudukanTotals = [];
foreach ($dataKedudukan as $row) {
    $kedudukanLabels[] = $row['nama_kedudukan'];
    $kedudukanTotals[] = (int)$row['total'];
}

?>
<style>
    .container-topjenis {
        display: flex;
        flex-wrap: wrap;
        gap: 25px;
        margin-top: 40px;
        justify-content: flex-start;
    }
    .card-transaksi {
        flex: 1 1 calc(33.333% - 25px);
        background: linear-gradient(to bottom right, #ffffff, #f0f3f8);
        padding: 30px 25px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
        min-width: 240px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-transaksi:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.15);
    }
    .card-transaksi h3 {
        margin: 0;
        font-size: 1.6rem;
        color: #0B1D51;
    }
    .card-transaksi p {
        margin-top: 12px;
        font-size: 2rem;
        font-weight: bold;
        color: #333;
    }
    .card-transaksi a {
        display: inline-block;
        margin-top: 20px;
        background-color: #0B1D51;
        color: #fff;
        padding: 12px 26px;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    .card-transaksi a:hover {
        background-color: #1a2e7a;
    }
    @media (max-width: 768px) {
        .card-transaksi {
            flex: 1 1 100%;
        }
    }
</style>

<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 align="center"><b>DASHBOARD APLIKASI LAPORAN FIDUSIA</b></h1>
            </div>
        </div>

        <!-- Grafik Laporan Bulanan -->
        <div class="row">
            <div class="col-lg-12">
                <center>
                    <h4><b>Jumlah Laporan Tahun <?= $tahunIni; ?> : <?= $jumlahTahunIni; ?> Laporan</b></h4>
                    <h4><b>Jumlah Laporan Setiap Bulannya Pada Tahun <?= $tahunIni; ?></b></h4>
                </center><br>
                <div id="firtsChart" style="width: 100%;height:470px;"></div>
                <script type="text/javascript">
                  var firtsChart = echarts.init(document.getElementById('firtsChart'));
                  window.onresize = function() { firtsChart.resize(); };

                  var option = {
                    tooltip: {},
                    legend: { data: ['JUMLAH LAPORAN PER BULAN'] },
                    xAxis: { data: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
                    yAxis: {},
                    series: [{
                        name: 'JUMLAH LAPORAN PER BULAN',
                        type: 'bar',
                        barWidth: '50%',
                        itemStyle: {color: '#0B1D51'},
                        data: <?= json_encode(array_values($dataChart)); ?>
                    }]
                  };
                  firtsChart.setOption(option);
                </script>
            </div>
        </div>

        <!-- Grafik Per Kedudukan -->
        <div class="row" style="margin-top: 50px;">
            <div class="col-lg-12">
                <center><h4><b>Jumlah Laporan per Kedudukan</b></h4></center>
                <div id="kedudukanChart" style="width: 100%;height:470px;"></div>
                <script type="text/javascript">
                  var kedudukanChart = echarts.init(document.getElementById('kedudukanChart'));
                  window.onresize = function() { kedudukanChart.resize(); };

                  var optionKedudukan = {
                    tooltip: { trigger: 'axis' },
                    xAxis: { type: 'category', data: <?= json_encode($kedudukanLabels); ?> },
                    yAxis: { type: 'value' },
                    series: [{
                        data: <?= json_encode($kedudukanTotals); ?>,
                        type: 'bar',
                        itemStyle: { color: '#1a73e8' }
                    }]
                  };
                  kedudukanChart.setOption(optionKedudukan);
                </script>
            </div>
        </div>
        
        <!-- Ringkasan -->
        <h4 style="margin-top: 40px;"><b>Ringkasan Laporan Anda</b></h4>
        <div class="container-topjenis">
            <div class="card-transaksi">
                <h3>Total Laporan Fidusia</h3>
                <p><?= jmlLaporanSuperUser($koneksi, "All"); ?> Laporan</p>
                <a href="daftar_laporan_entitas">Lihat Semua</a>
            </div>

            <?php foreach ($topJenis as $row): ?>
                <div class="card-transaksi">
                    <h3><?= htmlspecialchars($row['jenis_transaksi']) ?></h3>
                    <p><?= $row['jumlah']; ?> Laporan</p>
                    <a href="daftar_laporan_entitas?jenis_transaksi=<?= urlencode($row['jenis_transaksi']) ?>">Lihat Detail</a>
                </div>
            <?php endforeach; ?>
        </div>

                <!-- Tabel Kedudukan Telat Upload -->
        <div class="row" style="margin-top: 50px;">
            <div class="col-lg-12">
                <center>
                    <h4><b>Data Kedudukan yang Telat Upload - 
                        <?= date('F', mktime(0, 0, 0, $bulanSekarang, 1)); ?> <?= $tahunSekarang; ?>
                    </b></h4>
                </center>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead style="background-color:#0B1D51; color:white;">
                            <tr>
                                <th>No</th>
                                <th>Kedudukan</th>
                                <th>Total Telat Upload</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($dataKedudukan) > 0): ?>
                                <?php $no=1; foreach ($dataKedudukan as $row): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nama_kedudukan']); ?></td>
                                        <td><?= $row['total']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data telat upload bulan ini</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include "footer.php"; ?>
