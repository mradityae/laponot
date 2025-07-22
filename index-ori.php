<?PHP
    include "config/koneksi.php"; 
    session_save_path('login/session');
    session_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPORAN NOTARIS</title>

    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap4.css" rel="stylesheet" />
    <link href="assets/css/loginbasic.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href="<?=$url;?>assets/css/fonts.googleapis.css" rel="stylesheet"  /> <!--type='text/css'-->
    <link rel="shortcut icon" href="laponot.ico" type="image/x-icon">

</head>
<body>
	<div class="container h-100">
	  <div class="judul">
		<div class="h1">
		<h1 align="center" >LAPORAN NOTARIS</h1>
		</div>
	  </div>
		<div class="d-flex justify-content-center h-100">
			<div class="user_card">
				<div class="d-flex justify-content-center">
					<div class="brand_logo_container">
						<img src="assets/img/logokumham.png" class="brand_logo" alt="Logo">
					</div>
				</div>
				<div class="d-flex justify-content-center form_container">
					<form action="login/login_prc.php" method="POST">
						<div class="input-group mb-3">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa fa-user"></i></span>
							</div>
							<input type="text" name="email" class="form-control input_user" value="" placeholder="Masukkan Email..." required>
						</div>
						<div class="input-group mb-2">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa fa-key"></i></span>
							</div>
							<input type="password" name="password"  id="password" class="form-control input_pass" value="" placeholder="Masukkan Password..." required>
						</div>

						<center><input type="checkbox" onclick="showPassword()"><font style="color:#FFF">Lihat Password</font></input></center>

						<div class="d-flex justify-content-center mt-4 login_container" style="padding:0;">
							<table>
								<tr>
									<td>
										<input type="submit" name="submit" class="btn login_btn" value="Login">
									</td>
									<td>
										<a href="<?=$url;?>register/index.php" style="color: white">
											<button type="button" class="btn btn-primary">Daftar</button>
										</a>
									</td>
								</tr>
							</table>
						</div>
					</form>
				</div>
				
 				<div class="mt-2">
					<div class="d-flex justify-content-center links">
						<a href="<?=$url;?>register/lupapassword.php" style="color: white">Lupa Password</a>
					</div>
				</div>
			</div>
			
			
		</div>
	</div>
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script type="text/javascript">
		function showPassword(){
			var x = document.getElementById("password");
			if (x.type == "password") {
				x.type ="text";
			}
			else{
				x.type = "password";
			}
		}
	</script>

</body>
</html>
