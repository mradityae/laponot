<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");
$id         = $_SESSION["kode_user"];
$kedudukan  = $_SESSION["kedudukan"];
?>

<!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 align="center"><b>DASHBOARD APLIKASI LAPORAN NOTARIS<b></h1>
                <h3 class="page-head-line" align="center">WILAYAH <?php echo getWilayah($koneksi, $kedudukan);?></h3>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <center>
                    <h4><b>Jumlah Laporan Tahun <?php echo date("Y");?> : <?php echo chartLaporanPerTahun($koneksi, date("Y"),19);?> Laporan</b></h4>
                    <h4><b>Jumlah Laporan Setiap Bulannya Pada Tahun <?php echo date("Y");?> </b></h4>
                </center><br>
                <div id="firtsChart" style="width: 100%;height:470px;"></div>
                    <script type="text/javascript">
                      // Initialize the echarts instance based on the prepared dom
                      var firtsChart = echarts.init(document.getElementById('firtsChart'));

                      //auto resize
                      window.onresize = function() {
                        firtsChart.resize();
                      };

                      // Specify the configuration items and data for the chart
                      var option = {
                        title: {
                          text: ''
                        },
                        tooltip: {},
                        legend: {
                          data: ['JUMLAH LAPORAN SETIAP BULANNYA TAHUN <?php echo date("Y");?>']
                        },
                        xAxis: {
                          data: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                        },
                        yAxis: {},
                        series: [
                          {
                            name: 'JUMLAH PERMOHONAN SETIAP BULANNYA TAHUN <?php echo date("Y");?>',
                            type: 'bar',
                            barWidth: '50%',
                            barGap: 0,
                            itemStyle: {color: '#0B1D51'},
                            data: [<?php echo chartLaporanPerbulan($koneksi, date("Y"), 1, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 2, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 3, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 4, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 5, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 6, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 7, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 8, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 9, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 10, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 11, 19);?>,
                            <?php echo chartLaporanPerbulan($koneksi, date("Y"), 12, 19);?>]
                          }
                        ]
                      };

                      // Display the chart using the configuration items and data just specified.
                      firtsChart.setOption(option);
                    </script>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <center>
                    <h4><b>Perbandingan Jumlah Laporan Per-Tahun</b></h4>
                </center><br>
                <div id="thirdChart" style="width: 400px;height:400px;"></div>
                <p id="aria-1"></p>
                <script type="text/javascript">
                    // Initialize the echarts instance based on the prepared dom
                    var thirdChart = echarts.init(document.getElementById('thirdChart'));

                    window.onresize = function() {
                        thirdChart.resize();
                    };

                    thirdChart.setOption({
                    aria: {
                        enabled: false
                    },
                    title : {
                        text: '',
                        subtext: '',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient: 'horizontal',
                        left: 'center',
                        data: ['<?php echo date("Y")-3;?>','<?php echo date("Y")-2;?>','<?php echo date("Y")-1;?>', '<?php echo date("Y");?>']
                    },
                    series : [
                        {
                            name: 'Jumlah Laporan',
                            type: 'pie',
                            radius : '60%',
                            center: ['65%', '40%'],
                            selectedMode: 'single',
                            data:[
                                {value:<?php echo chartLaporanPerTahun($koneksi, date("Y")-3,19);?>, name:<?php echo date("Y")-3;?>, itemStyle: {color: '#0B1D51'}},
                                {value:<?php echo chartLaporanPerTahun($koneksi, date("Y")-2,19);?>, name:<?php echo date("Y")-2;?>, itemStyle: {color: '#725CAD'}},
                                {value:<?php echo chartLaporanPerTahun($koneksi, date("Y")-1,19);?>, name:<?php echo date("Y")-1;?>, itemStyle: {color: '#8CCDEB'}},
                                {value:<?php echo chartLaporanPerTahun($koneksi, date("Y"),19);?>, name:<?php echo date("Y");?>, itemStyle: {color: '#FFE3A9'}},
                            ],
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                });

                document.getElementById('aria-1').innerText = thirdChart.getDom().getAttribute('aria-label');
                </script>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <center>
                    <h4><b>Perbandingan Jumlah Notaris Terdaftar Per-Tahun</b></h4>
                </center><br>

                <div id="fourthChart" style="width: 400px;height:400px;"></div>
                <p id="aria-1"></p>
                <script type="text/javascript">
                    // Initialize the echarts instance based on the prepared dom
                    var fourthChart = echarts.init(document.getElementById('fourthChart'));

                      //auto resize
                      window.onresize = function() {
                        fourthChart.resize();
                      };

                      // Specify the configuration items and data for the chart
                      var option = {
                        title: {
                          text: ''
                        },
                        tooltip: {},
                        legend: {
                          data: ['JUMLAH NOTARIS']
                        },
                        xAxis: {
                          data: ['<?php echo date("Y")-4;?>',
                          '<?php echo date("Y")-3;?>',
                          '<?php echo date("Y")-2;?>',
                          '<?php echo date("Y")-1;?>',
                          '<?php echo date("Y");?>']
                        },
                        yAxis: {},
                        series: [
                          {
                            name: 'JUMLAH NOTARIS TERDAFTAR',
                            type: 'bar',
                            barWidth: '70%',
                            barGap: 0,
                            itemStyle: {color: '#725CAD'},
                            data: [<?php echo chartNotarisPeryear($koneksi, date("Y")-4,19);?>,
                            <?php echo chartNotarisPeryear($koneksi, date("Y")-3,19);?>,
                            <?php echo chartNotarisPeryear($koneksi, date("Y")-2,19);?>,
                            <?php echo chartNotarisPeryear($koneksi, date("Y")-1,19);?>,
                            <?php echo chartNotarisPeryear($koneksi, date("Y"),19);?>]
                          }
                        ]
                      };

                      // Display the chart using the configuration items and data just specified.
                      fourthChart.setOption(option);
                    </script>
            </div>
        </div>

        <div class="containercustom">
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/notaris_all.png"></center>
                        <h3>Notaris Terdaftar</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlNotaris($koneksi,$kedudukan,"All");?> Notaris</p>
                        <a href="daftar_notaris.php">Daftar Notaris</a>
                    </div>
                </div>
            </div>
             <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/notaris_one.png"></center>
                        <h3>Notaris Yang Belum Aktif</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlNotaris($koneksi,$kedudukan,"0");?> Notaris</p>
                        <a href="daftar_notaris.php?aktif=0">Daftar Notaris</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/clipboard.png"></center>
                        <h3>Seluruh Laporan</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporanAdmin($koneksi,$kedudukan,"All");?> Laporan</p>
                         <a href="daftar_laporan.php">Daftar Laporan</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="containercustom">
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_terverifikasi.png"></center>
                        <h3>Laporan Terverifikasi</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporanAdmin($koneksi,$kedudukan,"Terverifikasi");?> Laporan</p>
                        <a href="daftar_laporan.php?status=Terverifikasi">Daftar Laporan</a>
                    </div>
                </div>
            </div>
             <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_belum.png"></center>
                        <h3>Laporan Belum Terverifikasi</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporanAdmin($koneksi,$kedudukan,"Laporan Terkirim");?> Laporan</p>
                        <a href="daftar_laporan.php?status=Laporan%20Terkirim">Daftar Laporan</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="face face1">
                    <div class="content">
                        <center><img src="<?=$url;?>assets/img/laporan_ditolak.png"></center>
                        <h3>Laporan Ditolak</h3>
                    </div>
                </div>
                <div class="face face2">
                    <div class="content">
                        <p><?php echo jmlLaporanAdmin($koneksi,$kedudukan,"Ditolak");?> Laporan</p>
                        <a href="daftar_laporan.php?status=ditolak">Daftar Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>