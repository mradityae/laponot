<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models_fidusia.php");

$id         = $_SESSION["kode_user"] ?? null;
$kedudukan  = $_SESSION["kedudukan"] ?? null;

$tahunIni = date('Y');
$bulanIni = date('n');

// Ambil data grafik hanya sekali
$dataChart = getChartLaporanTahunan($koneksi, $tahunIni, $kedudukan);
$jumlahTahunIni = array_sum($dataChart);
$topJenis = getTopJenisTransaksiAdmin($koneksi, $kedudukan);
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


/. NAV SIDE 
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 align="center"><b>DASHBOARD APLIKASI LAPORAN FIDUSIA<b></h1>
                <h3 class="page-head-line" align="center">WILAYAH <?php echo getWilayah($koneksi, $kedudukan);?></h3>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <center>
                    <h4><b>Jumlah Laporan Tahun <?php echo $tahunIni; ?> : <?php echo $jumlahTahunIni; ?> Laporan</b></h4>
                    <h4><b>Jumlah Laporan Setiap Bulannya Pada Tahun <?php echo $tahunIni; ?> </b></h4>
                </center><br>
                <div id="firtsChart" style="width: 100%;height:470px;"></div>
                <script type="text/javascript">
                  var firtsChart = echarts.init(document.getElementById('firtsChart'));

                  window.onresize = function() {
                    firtsChart.resize();
                  };

                  var option = {
                    title: { text: '' },
                    tooltip: {},
                    legend: {
                      data: ['JUMLAH LAPORAN SETIAP BULANNYA TAHUN <?php echo $tahunIni; ?>']
                    },
                    xAxis: {
                      data: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    },
                    yAxis: {},
                    series: [
                      {
                        name: 'JUMLAH LAPORAN SETIAP BULANNYA TAHUN <?php echo $tahunIni; ?>',
                        type: 'bar',
                        barWidth: '50%',
                        barGap: 0,
                        itemStyle: {color: '#0B1D51'},
                        data: <?= json_encode(array_values($dataChart)); ?>
                      }
                    ]
                  };

                  firtsChart.setOption(option);
                </script>
            </div>
        </div>
        <h4 style="margin-top: 40px;"><b>Ringkasan Wilayah <?php echo getWilayah($koneksi, $kedudukan);?></b></h4>
        <div class="container-topjenis">
            <!-- Card Jumlah Total -->
            <div class="card-transaksi">
                <h3>Total Laporan Fidusia</h3>
                <p><?= jmlLaporanAdmin($koneksi, $kedudukan, "All"); ?> Laporan</p>
                <a href="daftar_laporan_entitas">Lihat Semua</a>
            </div>

            <!-- Top 5 Jenis Transaksi -->
            <?php foreach ($topJenis as $row): ?>
                <div class="card-transaksi">
                    <h3><?= htmlspecialchars($row['jenis_transaksi']) ?></h3>
                    <p><?= $row['jumlah']; ?> Laporan</p>
                    <a href="daftar_laporan_entitas?jenis_transaksi=<?= urlencode($row['jenis_transaksi']) ?>">Lihat Detail</a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>
<?php include "footer.php"; ?>
