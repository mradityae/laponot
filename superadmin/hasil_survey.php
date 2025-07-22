<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");
$id = $_SESSION["kode_user"];
?>

  <!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line" align="center">HASIL SURVEY<br>APLIKASI LAPORAN NOTARIS<br></h1>
            </div>
        </div>
        <body style="background-color: #E2E2E2;">  
        <center>
            <h2>
                Jumlah Responden : <?php echo hasilSurvey($koneksi, "All");?><br>
                Periode : <?php echo date('d-F-Y');?><br><br>
                Data Survey Per Rating
            </h2>
            <div id="canvas-holder" style="width:70%">
                <canvas id="chart-area"></canvas>
            </div>
            <br>
             <h2>
                Data Survey Per Bulan Tahun <?php echo date("Y");?>
            </h2>
            <div id="canvas-holder" style="width:100%">
                <canvas id="chart-area3"></canvas>
            </div>
            <br>
            <h2>Tabel Hasil Survey</h2>
        
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm table-bordered data" align="center">
                  <thead>
                        <tr>
                            <th align='center'>No</th>
                            <th align='center'>Tanggal</th>
                            <th align='center'>Rating</th>
                            <th align='center'>Saran/Masukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no=1;
                            $level=2;
                            $ambil=$koneksi->prepare("SELECT * FROM survey order by surveyDate desc");
                            
                            $ambil->execute();
                            while ($row=$ambil->fetch())
                            {
                                echo "<tr>";
                                echo "<td>".$no."</td>";
                                echo "<td>".date('d-M-y', strtotime($row['surveyDate']))."</td>";    
                                if ($row['rating'] == 1) 
                                {
                                    echo "<td>Sangat Buruk</td>";
                                }
                                else if($row['rating'] == 2)
                                {
                                    echo "<td>Buruk</td>";
                                }
                                else if($row['rating'] == 3)
                                {
                                    echo "<td>Kurang</td>";
                                }
                                else if($row['rating'] == 4)
                                {
                                    echo "<td>Cukup</td>";
                                }
                                else if($row['rating'] == 5)
                                {
                                    echo "<td>Baik</td>";
                                }
                                else
                                {
                                     echo "<td>Sangat Baik</td>";
                                }
                                
                                echo "<td>".$row['keterangan']."</td>";

                                echo "</tr>";
                                $no++;
                            }
                            
                        ?>
                    </tbody>
                </table>
            </div>
        </center>
        <script>
            var config = {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            <?php echo hasilSurvey($koneksi, 1);?>,
                            <?php echo hasilSurvey($koneksi, 2);?>,
                            <?php echo hasilSurvey($koneksi, 3);?>,
                            <?php echo hasilSurvey($koneksi, 4);?>,
                            <?php echo hasilSurvey($koneksi, 5);?>,
                            <?php echo hasilSurvey($koneksi, 6);?>
                        ],
                        backgroundColor: [
                            window.chartColors.red,
                            window.chartColors.orange,
                            window.chartColors.yellow,
                            window.chartColors.black,
                            window.chartColors.green,
                            window.chartColors.blue
                        ],
                        label: 'Dataset 1'
                    }],
                    labels: [
                        'Sangat Buruk',
                        'Buruk',
                        'Kurang',
                        'Cukup',
                        'Baik',
                        'Sangat Baik'
                    ]
                },
                options: {
                    responsive: true
                }
            };

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var color = Chart.helpers.color;
            var barChartData = {
                labels: MONTHS,
                datasets: 
                [
                    {
                        label: 'Sangat Buruk',
                        backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.red,
                        borderWidth: 1,
                        data: [
                            <?php echo hasilSurveyBulanan($koneksi, 1, 1);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 2);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 3);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 4);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 5);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 6);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 7);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 8);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 9);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 10);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 11);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 1, 12);?>
                        ]
                    },
                    {
                        label: 'Buruk',
                        backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.orange,
                        borderWidth: 1,
                        data: [
                            <?php echo hasilSurveyBulanan($koneksi, 2, 1);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 2);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 3);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 4);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 5);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 6);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 7);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 8);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 9);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 10);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 11);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 2, 12);?>
                        ]
                    }, 
                    {
                        label: 'Kurang',
                        backgroundColor: color(window.chartColors.yellow).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.yellow,
                        borderWidth: 1,
                        data: [
                            <?php echo hasilSurveyBulanan($koneksi, 3, 1);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 2);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 3);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 4);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 5);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 6);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 7);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 8);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 9);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 10);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 11);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 3, 12);?>
                        ]
                    }, 
                    {
                        label: 'Cukup',
                        backgroundColor: color(window.chartColors.black).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.black,
                        borderWidth: 1,
                        data: [
                            <?php echo hasilSurveyBulanan($koneksi, 4, 1);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 2);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 3);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 4);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 5);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 6);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 7);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 8);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 9);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 10);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 11);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 4, 12);?>
                        ]
                    }, 
                    {
                        label: 'Baik',
                        backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.green,
                        borderWidth: 1,
                        data: [
                            <?php echo hasilSurveyBulanan($koneksi, 5, 1);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 2);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 3);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 4);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 5);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 6);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 7);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 8);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 9);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 10);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 11);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 5, 12);?>
                        ]
                    }, 
                    {
                        label: 'Sangat Baik',
                        backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.blue,
                        borderWidth: 1,
                        data: [
                            <?php echo hasilSurveyBulanan($koneksi, 6, 1);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 2);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 3);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 4);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 5);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 6);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 7);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 8);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 9);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 10);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 11);?>,
                            <?php echo hasilSurveyBulanan($koneksi, 6, 12);?>
                        ]
                    }
                ]

            };

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            window.onload = function() {
                var ctx = document.getElementById('chart-area').getContext('2d');
                window.myPie = new Chart(ctx, config);

                var ctx3 = document.getElementById('chart-area3').getContext('2d');
                window.myBar = new Chart(ctx3, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: ''
                        },
                        scales:{
                            xAxes:[
                            {
                                  display: true,
                                  scaleLabel: {
                                    display: true,
                                    labelString: 'Bulan'
                                  }
                            }],
                            yAxes:[
                            {
                                  display: true,
                                  scaleLabel: {
                                    display: true,
                                    labelString: 'Responden'
                                  }
                            }]
                        }
                    }
                });

            };
        </script>
    </div>
</div>
<?php
$koneksi = null;
include "footer.php";
?>