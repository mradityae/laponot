<?php
	include("db/module.php");
	$koneksidb = koneksi();
	include_once("data_source.php");
	$tahun = date("Y");
?>
<!DOCTYPE html>
<html>
<head>
	<title>SURVEY KEPUASAN</title>
	<script src="chartjs/Chart.bundle.js"></script>
	<script src="chartjs/utils.js"></script>
</head>
<body>
	<style type="text/css">
		body{
			font-family: roboto;
		}
		canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
		}
	</style>
	
	<h2 align="center">HASIL KEPUASAN MASYARAKAT TAHUN <?php echo $tahun;?></h2>
	<h2 align="center">Jumlah Data : <?php echo JumlahData($koneksidb, 2020); ?> Orang</h2>
	<center>
		<div id="container" style="width: 100%;">
			<canvas id="canvas"></canvas>
		</div>
		<br>
	</center>

	<script>
		var color = Chart.helpers.color;
		var barChartData = {
			labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
			datasets: 
			[
				{
					label: 'Sangat Baik',
					backgroundColor: 'rgb(66, 135, 245)',
					//borderColor: window.chartColors.red,
					borderWidth: 1,
					data: [
						<?php echo GetData($koneksidb, 1,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 2,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 3,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 4,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 5,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 6,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 7,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 8,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 9,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 10,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 11,$tahun,"sangat-baik"); ?>,
						<?php echo GetData($koneksidb, 12,$tahun,"sangat-baik"); ?>
					]
				}, 
				{
					label: 'Baik',
					backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
					//borderColor: window.chartColors.red,
					borderWidth: 1,
					data: [
						<?php echo GetData($koneksidb, 1,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 2,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 3,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 4,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 5,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 6,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 7,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 8,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 9,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 10,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 11,$tahun,"baik"); ?>,
						<?php echo GetData($koneksidb, 12,$tahun,"baik"); ?>
					]
				}, 
				{
					label: 'Sedang',
					backgroundColor: color(window.chartColors.yellow).alpha(0.5).rgbString(),
					//borderColor: window.chartColors.red,
					borderWidth: 1,
					data: [
						<?php echo GetData($koneksidb, 1,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 2,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 3,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 4,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 5,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 6,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 7,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 8,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 9,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 10,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 11,$tahun,"sedang"); ?>,
						<?php echo GetData($koneksidb, 12,$tahun,"sedang"); ?>
					]
				},
				{
					label: 'Kurang',
					backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
					//borderColor: window.chartColors.red,
					borderWidth: 1,
					data: [
						<?php echo GetData($koneksidb, 1,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 2,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 3,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 4,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 5,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 6,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 7,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 8,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 9,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 10,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 11,$tahun,"kurang"); ?>,
						<?php echo GetData($koneksidb, 12,$tahun,"kurang"); ?>
					]
				}, 
				{
					label: 'Buruk',
					backgroundColor: color(window.chartColors.grey).alpha(0.5).rgbString(),
					//borderColor: window.chartColors.red,
					borderWidth: 1,
					data: [
						<?php echo GetData($koneksidb, 1,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 2,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 3,$$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 4,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 5,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 6,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 7,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 8,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 9,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 10,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 11,$tahun,"buruk"); ?>,
						<?php echo GetData($koneksidb, 12,$tahun,"buruk"); ?>
					]
				}, 
				{
					label: 'Sangat Buruk',
					backgroundColor: color(window.chartColors.black).alpha(0.5).rgbString(),
					//borderColor: window.chartColors.red,
					borderWidth: 1,
					data: [
						<?php echo GetData($koneksidb, 1,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 2,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 3,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 4,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 5,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 6,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 7,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 8,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 9,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 10,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 11,$tahun,"sangat-buruk"); ?>,
						<?php echo GetData($koneksidb, 12,$tahun,"sangat-buruk"); ?>
					]
				}
			]
		};

		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'bottom',
					},
					title: {
						display: false,
						text: 'Hasil Survey'
					},
					scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Bulan'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Jumlah'
						}
					}]
				}
				}
			});

		};
	</script>

	<!--script>
		var ctx = document.getElementById("myChart").getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: ["Baik", "Sedang", "Buruk"],
				datasets: [{
					label: 'Grafik Kepuasan Masyarakat',
					data: [<?php echo $jbaris_baik; ?>, <?php echo $jbaris_sedang; ?>, <?php echo $jbaris_buruk; ?>],
					backgroundColor: [
					'rgba(54, 162, 235, 0.2)',
					'rgba(255, 206, 86, 0.2)',
					'rgba(255, 99, 132, 0.2)'
					],
					borderColor: [
					'rgba(54, 162, 235,1)',
					'rgba(255, 206, 86, 1)',
					'rgba(255,99,132, 1)'
					],
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true
						}
					}]
				}
			}
		});
	</script-->
</body>
</html>
