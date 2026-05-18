<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

$id         = $_SESSION["kode_user"];
$kedudukan  = $_SESSION["kedudukan"];

// ========================================
// FILTER KEPATUHAN
// ========================================
$bulan_berjalan  = $_GET['bulan_berjalan'] ?? date('m', strtotime('-1 month'));
$tahun       = $_GET['tahun'] ?? date('Y');

// ========================================
// TOTAL NOTARIS
// ========================================
$q_total = $koneksi->prepare("
    SELECT COUNT(*) as total
    FROM notaris
    WHERE id_kedudukan = ?
    AND level = '2'
    AND aktif='1'
");

$q_total->execute([$kedudukan]);

$total_notaris = $q_total->fetch()['total'];

// ========================================
// SUDAH LAPOR
// ========================================
$q_sudah = $koneksi->prepare("
    SELECT COUNT(DISTINCT la.id_notaris) as total

    FROM laporan la

    INNER JOIN notaris n
        ON la.id_notaris = n.id_notaris

    WHERE n.id_kedudukan = ?
    AND n.level = '2'
    AND n.aktif='1'

    AND YEAR(la.tanggal) = ?
    AND MONTH(la.tanggal) = ?
");

$q_sudah->execute([
    $kedudukan,
    $tahun,
    $bulan_berjalan
]);

$sudah_lapor = $q_sudah->fetch()['total'];

$belum_lapor = $total_notaris - $sudah_lapor;

$persentase = ($total_notaris > 0)
    ? ($sudah_lapor / $total_notaris) * 100
    : 0;
?>

<style>

.kepatuhan-card{
    background:#fff;
    border-radius:10px;
    padding:20px;
    margin-bottom:20px;
    border:1px solid #eaeaea;
    box-shadow:0 2px 6px rgba(0,0,0,0.05);
}

.kepatuhan-item{
    text-align:center;
    padding:20px;
    border-radius:10px;
    color:#fff;
    min-height:180px;
}

.bg-total{
    background:#3498db;
}

.bg-sudah{
    background:#27ae60;
}

.bg-belum{
    background:#e74c3c;
}

.bg-persentase{
    background:#f39c12;
}

.kepatuhan-item h2{
    margin:0;
    font-size:36px;
    font-weight:bold;
}

.kepatuhan-item p{
    margin-top:10px;
    font-size:15px;
}

.btn-detail-lapor{
    margin-top:15px;
    border-radius:30px;
    font-weight:bold;
}

</style>

<!-- /. NAV SIDE  -->
<div id="page-wrapper">

    <div id="page-inner">

        <div class="row">

            <div class="col-md-12">

                <h1 align="center">
                    <b>DASHBOARD APLIKASI LAPORAN NOTARIS</b>
                </h1>

                <h3 class="page-head-line" align="center">
                    WILAYAH <?php echo getWilayah($koneksi, $kedudukan);?>
                </h3>

            </div>

        </div>

        <!-- ===================================== -->
        <!-- FILTER -->
        <!-- ===================================== -->
        <div class="row">

            <div class="col-md-12">

                <form method="GET" class="form-inline">

                    <label>Bulan Laporan Berjalan :</label>

                    <select name="bulan_berjalan" class="form-control">

                        <?php
                        $bulan = [
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember'
                        ];

                        foreach($bulan as $key => $val):
                        ?>

                        <option value="<?= $key ?>"
                            <?= ($bulan_berjalan == $key ? 'selected' : '') ?>>

                            <?= $val ?>

                        </option>

                        <?php endforeach; ?>

                    </select>

                    <input type="number"
                           name="tahun"
                           class="form-control"
                           value="<?= $tahun ?>"
                           style="width:100px;">

                    <button type="submit"
                            class="btn btn-primary">

                        Tampilkan

                    </button>

                </form>

            </div>

        </div>

        <br>

        <!-- ===================================== -->
        <!-- CARD KEPATUHAN -->
        <!-- ===================================== -->
        <div class="row">

            <!-- TOTAL -->
            <div class="col-md-3">

                <div class="kepatuhan-card">

                    <div class="kepatuhan-item bg-total">

                        <h2>
                            <?= number_format($total_notaris) ?>
                        </h2>

                        <p>Total Notaris</p>

                    </div>

                </div>

            </div>

            <!-- SUDAH -->
            <div class="col-md-3">

                <div class="kepatuhan-card">

                    <div class="kepatuhan-item bg-sudah">

                        <h2>
                            <?= number_format($sudah_lapor) ?>
                        </h2>

                        <p>Sudah Melapor</p>

                    </div>

                </div>

            </div>

            <!-- BELUM -->
            <div class="col-md-3">

                <div class="kepatuhan-card">

                    <div class="kepatuhan-item bg-belum">

                        <h2>
                            <?= number_format($belum_lapor) ?>
                        </h2>

                        <p>Belum Melapor</p>

                    </div>

                </div>

            </div>

            <!-- PERSENTASE -->
            <div class="col-md-3">

                <div class="kepatuhan-card">

                    <div class="kepatuhan-item bg-persentase">

                        <h2>
                            <?= number_format($persentase,2) ?>%
                        </h2>

                        <p>Persentase Kepatuhan</p>

                        <button class="btn btn-default btn-detail-lapor"
                                onclick="showDetailLapor()">

                            Lihat Detail

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <!-- ===================================== -->
        <!-- GRAFIK PERTAMA -->
        <!-- ===================================== -->
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <center>

                    <h4>
                        <b>
                            Jumlah Laporan Tahun <?php echo date("Y");?> :
                            <?php echo chartLaporanPerTahun($koneksi, date("Y"),$kedudukan);?>
                            Laporan
                        </b>
                    </h4>

                    <h4>
                        <b>
                            Jumlah Laporan Setiap Bulannya Pada Tahun
                            <?php echo date("Y");?>
                        </b>
                    </h4>

                </center>

                <br>

                <div id="firtsChart"
                     style="width:100%;height:470px;">
                </div>

                <script type="text/javascript">

                var firtsChart = echarts.init(
                    document.getElementById('firtsChart')
                );

                window.onresize = function() {
                    firtsChart.resize();
                };

                var option = {

                    title: {
                        text: ''
                    },

                    tooltip: {},

                    legend: {

                        data: [
                            'JUMLAH LAPORAN SETIAP BULANNYA TAHUN <?php echo date("Y");?>'
                        ]

                    },

                    xAxis: {

                        data: [
                            'Jan','Feb','Mar','Apr',
                            'Mei','Jun','Jul','Aug',
                            'Sep','Oct','Nov','Dec'
                        ]

                    },

                    yAxis: {},

                    series: [

                        {

                            name: 'JUMLAH PERMOHONAN SETIAP BULANNYA TAHUN <?php echo date("Y");?>',

                            type: 'bar',

                            barWidth: '50%',

                            barGap: 0,

                            itemStyle: {
                                color: '#0B1D51'
                            },

                            data: [

                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 1, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 2, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 3, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 4, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 5, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 6, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 7, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 8, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 9, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 10, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 11, $kedudukan);?>,
                                <?php echo chartLaporanPerbulan($koneksi, date("Y"), 12, $kedudukan);?>

                            ]

                        }

                    ]

                };

                firtsChart.setOption(option);

                </script>

            </div>

        </div>

        <!-- ===================================== -->
        <!-- ROW KEDUA -->
        <!-- ===================================== -->
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                <center>
                    <h4><b>Perbandingan Jumlah Laporan Per-Tahun</b></h4>
                </center>

                <br>

                <div id="thirdChart"
                     style="width:400px;height:400px;">
                </div>

                <p id="aria-1"></p>

                <script type="text/javascript">

                var thirdChart = echarts.init(
                    document.getElementById('thirdChart')
                );

                window.onresize = function() {
                    thirdChart.resize();
                };

                thirdChart.setOption({

                    aria: {
                        enabled: false
                    },

                    tooltip : {

                        trigger: 'item',

                        formatter: "{a} <br/>{b} : {c} ({d}%)"

                    },

                    legend: {

                        orient: 'horizontal',

                        left: 'center'

                    },

                    series : [

                        {

                            name: 'Jumlah Laporan',

                            type: 'pie',

                            radius : '60%',

                            center: ['65%', '40%'],

                            selectedMode: 'single',

                            data:[

                                {
                                    value:<?php echo chartLaporanPerTahun($koneksi, date("Y")-3,$kedudukan);?>,
                                    name:'<?php echo date("Y")-3;?>'
                                },

                                {
                                    value:<?php echo chartLaporanPerTahun($koneksi, date("Y")-2,$kedudukan);?>,
                                    name:'<?php echo date("Y")-2;?>'
                                },

                                {
                                    value:<?php echo chartLaporanPerTahun($koneksi, date("Y")-1,$kedudukan);?>,
                                    name:'<?php echo date("Y")-1;?>'
                                },

                                {
                                    value:<?php echo chartLaporanPerTahun($koneksi, date("Y"),$kedudukan);?>,
                                    name:'<?php echo date("Y");?>'
                                }

                            ]

                        }

                    ]

                });

                </script>

            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                <center>
                    <h4><b>Perbandingan Jumlah Notaris Terdaftar Per-Tahun</b></h4>
                </center>

                <br>

                <div id="fourthChart"
                     style="width:400px;height:400px;">
                </div>

                <script type="text/javascript">

                var fourthChart = echarts.init(
                    document.getElementById('fourthChart')
                );

                window.onresize = function() {
                    fourthChart.resize();
                };

                var option = {

                    tooltip: {},

                    legend: {

                        data: ['JUMLAH NOTARIS']

                    },

                    xAxis: {

                        data: [

                            '<?php echo date("Y")-4;?>',
                            '<?php echo date("Y")-3;?>',
                            '<?php echo date("Y")-2;?>',
                            '<?php echo date("Y")-1;?>',
                            '<?php echo date("Y");?>'

                        ]

                    },

                    yAxis: {},

                    series: [

                        {

                            name: 'JUMLAH NOTARIS TERDAFTAR',

                            type: 'bar',

                            barWidth: '70%',

                            itemStyle: {
                                color: '#725CAD'
                            },

                            data: [

                                <?php echo chartNotarisPeryear($koneksi, date("Y")-4,$kedudukan);?>,
                                <?php echo chartNotarisPeryear($koneksi, date("Y")-3,$kedudukan);?>,
                                <?php echo chartNotarisPeryear($koneksi, date("Y")-2,$kedudukan);?>,
                                <?php echo chartNotarisPeryear($koneksi, date("Y")-1,$kedudukan);?>,
                                <?php echo chartNotarisPeryear($koneksi, date("Y"),$kedudukan);?>

                            ]

                        }

                    ]

                };

                fourthChart.setOption(option);

                </script>

            </div>

        </div>

        <!-- ===================================== -->
        <!-- CARD LAMA -->
        <!-- ===================================== -->
        <div class="containercustom">

            <div class="card">

                <div class="face face1">

                    <div class="content">

                        <center>
                            <img src="<?=$url;?>assets/img/notaris_all.png">
                        </center>

                        <h3>Notaris Terdaftar</h3>

                    </div>

                </div>

                <div class="face face2">

                    <div class="content">

                        <p>
                            <?php echo jmlNotaris($koneksi,$kedudukan,"All");?>
                            Notaris
                        </p>

                        <a href="daftar_notaris.php">
                            Daftar Notaris
                        </a>

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="face face1">

                    <div class="content">

                        <center>
                            <img src="<?=$url;?>assets/img/notaris_one.png">
                        </center>

                        <h3>Notaris Yang Belum Aktif</h3>

                    </div>

                </div>

                <div class="face face2">

                    <div class="content">

                        <p>
                            <?php echo jmlNotaris($koneksi,$kedudukan,"0");?>
                            Notaris
                        </p>

                        <a href="daftar_notaris.php?aktif=0">
                            Daftar Notaris
                        </a>

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="face face1">

                    <div class="content">

                        <center>
                            <img src="<?=$url;?>assets/img/clipboard.png">
                        </center>

                        <h3>Seluruh Laporan</h3>

                    </div>

                </div>

                <div class="face face2">

                    <div class="content">

                        <p>
                            <?php echo jmlLaporanAdmin($koneksi,$kedudukan,"All");?>
                            Laporan
                        </p>

                        <a href="daftar_laporan.php">
                            Daftar Laporan
                        </a>

                    </div>

                </div>

            </div>

        </div>

        <!-- ===================================== -->
        <!-- CARD LAMA 2 -->
        <!-- ===================================== -->
        <div class="containercustom">

            <div class="card">

                <div class="face face1">

                    <div class="content">

                        <center>
                            <img src="<?=$url;?>assets/img/laporan_terverifikasi.png">
                        </center>

                        <h3>Laporan Terverifikasi</h3>

                    </div>

                </div>

                <div class="face face2">

                    <div class="content">

                        <p>
                            <?php echo jmlLaporanAdmin($koneksi,$kedudukan,"Terverifikasi");?>
                            Laporan
                        </p>

                        <a href="daftar_laporan.php?status=Terverifikasi">
                            Daftar Laporan
                        </a>

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="face face1">

                    <div class="content">

                        <center>
                            <img src="<?=$url;?>assets/img/laporan_belum.png">
                        </center>

                        <h3>Laporan Belum Terverifikasi</h3>

                    </div>

                </div>

                <div class="face face2">

                    <div class="content">

                        <p>
                            <?php echo jmlLaporanAdmin($koneksi,$kedudukan,"Laporan Terkirim");?>
                            Laporan
                        </p>

                        <a href="daftar_laporan.php?status=Laporan%20Terkirim">
                            Daftar Laporan
                        </a>

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="face face1">

                    <div class="content">

                        <center>
                            <img src="<?=$url;?>assets/img/laporan_ditolak.png">
                        </center>

                        <h3>Laporan Ditolak</h3>

                    </div>

                </div>

                <div class="face face2">

                    <div class="content">

                        <p>
                            <?php echo jmlLaporanAdmin($koneksi,$kedudukan,"Ditolak");?>
                            Laporan
                        </p>

                        <a href="daftar_laporan.php?status=ditolak">
                            Daftar Laporan
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- ===================================== -->
<!-- MODAL DETAIL -->
<!-- ===================================== -->
<div class="modal fade"
     id="modalDetailLapor"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-lg"
         role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    &times;

                </button>

                <h4 id="modalTitleLapor"></h4>

            </div>

            <div class="modal-body"
                 id="modalContentLapor">

                <center>Memuat data...</center>

            </div>

        </div>

    </div>

</div>

<script>

function showDetailLapor(){

    $('#modalDetailLapor').modal('show');

    $('#modalContentLapor').html(
        '<center>Memuat data...</center>'
    );

    $.get('get_detail_kepatuhan_mpd.php', {

        bulan_berjalan: '<?= $bulan_berjalan ?>',
        tahun: '<?= $tahun ?>'

    }, function(data){

        $('#modalContentLapor').html(data);

    });

}

</script>

<?php
include "footer.php";
?>