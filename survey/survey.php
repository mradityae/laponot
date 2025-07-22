<?PHP
include '../config/koneksi.php';
include '../models/models.php';
$tgl=date('Y-m-d');
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_save_path('../login/session');
session_start();
if(!isset($_SESSION['email'])) 
{
    // Initialize the session.
		// If you are using session_name("something"), don't forget it now!
		session_start();

		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

	
		// Finally, destroy the session.
		session_destroy();
    header("Location:../");
}
else if($_SESSION['user_role'] != 2)
{
    // Initialize the session.
		// If you are using session_name("something"), don't forget it now!
		session_start();

		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

	
		// Finally, destroy the session.
		session_destroy();
    header("Location:../");
} 
else 
{
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Survey Kepuasan Masyarakat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="laponot.ico" type="image/x-icon">
    <!-- <script src="https://kit.fontawesome.com/a076d05399.js"></script> -->
    <script language="JavaScript" src="a076d05399.js"></script>
  </head>
  <body>
    <br>
    <center>
      <div class="container">
        <div class="star-widget">
          <font style="color:#fe7">
            <font style="font-size: 22px">Survey Kepuasan Masyarakat<br></font>
            <font style="font-size: 16px">Aplikasi Laporan Notaris</font><br>
            <img src="../assets/img/logohumas1.png" width="80" height="80"><br>
          </font>
           <form action="<?=$url;?>act/tambah-survey_proses.php" method="POST" enctype="multipart/form-data">
            <input type="radio" name="rate" id="rate-6" value="6">
            <label for="rate-6" class="fas fa-star"></label>
            <input type="radio" name="rate" id="rate-5" value="5">
            <label for="rate-5" class="fas fa-star"></label>
            <input type="radio" name="rate" id="rate-4" value="4">
            <label for="rate-4" class="fas fa-star"></label>
            <input type="radio" name="rate" id="rate-3" value="3">
            <label for="rate-3" class="fas fa-star"></label>
            <input type="radio" name="rate" id="rate-2" value="2">
            <label for="rate-2" class="fas fa-star"></label>
            <input type="radio" name="rate" id="rate-1" value="1">
            <label for="rate-1" class="fas fa-star"></label>
            <header></header>
            <div class="textarea">
              <textarea cols="50" name="keterangan" placeholder="Saran dan Masukan"></textarea>
            </div>
            <div class="btn">
              <button type="submit" name="submit"><font style="color:#fe7">Kirim</font></button>
            </div>
          </form>
        </div>
      </div>
    </center>
  </body>
</html>
<?PHP
}
?>
