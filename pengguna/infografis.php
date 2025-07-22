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
                <h1 class="page-head-line" align="center">INFOGRAFIS</h1>
            </div>
        </div>
        <body style="background-color: #E2E2E2;">  

        <center>
            <div id="canvas-holder" style="width:70%">
                <canvas id="chart-area"></canvas>
            </div>
            <br>
            <div id="canvas-holder" style="width:70%">
                <canvas id="chart-area3"></canvas>
            </div>
            <br>
            <div id="canvas-holder" style="width:70%">
                <canvas id="chart-area2"></canvas>
            </div>
            
        </center>

        <script>
            var config = {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            10,
                            20,
                            10,
                            10,
                            50,
                        ],
                        backgroundColor: [
                            window.chartColors.red,
                            window.chartColors.orange,
                            window.chartColors.yellow,
                            window.chartColors.green,
                            window.chartColors.blue,
                        ],
                        label: 'Dataset 1'
                    }],
                    labels: [
                        'Red',
                        'Orange',
                        'Yellow',
                        'Green',
                        'Blue'
                    ]
                },
                options: {
                    responsive: true
                }
            };

            /*-----------------------------------------------------------------*/

            var config2 = {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            40,
                            60,
                            20,
                            50,
                            20,
                        ],
                        backgroundColor: [
                            window.chartColors.red,
                            window.chartColors.orange,
                            window.chartColors.yellow,
                            window.chartColors.green,
                            window.chartColors.blue,
                        ],
                        label: 'Dataset 2'
                    }],
                    labels: [
                        'Red',
                        'Orange',
                        'Yellow',
                        'Green',
                        'Blue'
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
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'Dataset 1',
                    backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.red,
                    borderWidth: 1,
                    data: [
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7
                    ]
                }, {
                    label: 'Dataset 2',
                    backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.blue,
                    borderWidth: 1,
                    data: [
                        7,
                        6,
                        5,
                        4,
                        3,
                        2,
                        1
                    ]
                }]

            };

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            window.onload = function() {
                var ctx = document.getElementById('chart-area').getContext('2d');
                window.myPie = new Chart(ctx, config);
                
                var ctx2 = document.getElementById('chart-area2').getContext('2d');
                window.myPie = new Chart(ctx2, config2);

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
                            text: 'Contoh grafik'
                        }
                    }
                });

            };
        </script>
    </div>
</div>
<?php
include "footer.php";
?>