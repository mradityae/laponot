<?php
session_save_path('../login/session');
session_start();

if(isset($_POST['submit']) && isset($_SESSION['email']) && (($_SESSION['user_role']) == 2))
{

	include '../config/koneksi.php';
	include '../models/models.php';

	try
	{
		$rating 			= RemoveSpecialChar($_POST['rate']);
		$keterangan         = RemoveSpecialChar($_POST['keterangan']);
	
		if ($rating != null || $rating != "") {
		
			if (tambahSurvey($koneksi, $rating, $keterangan)) 
			{
				echo "<script>alert('Terima Kasih Telah Mengisi Survey')</script>";
				$link = $url."survey/survey.php";
				header("refresh:0.1; url=$link");
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
				echo "<script>alert('Gagal Untuk Menyimpan Hasil Survey Ke Database')</script>";
				$link = $url."survey/survey.php";
				header("refresh:0.1; url=$link");
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
		}
		else{
			echo "<script>alert('Rating Pada Icon Bintang Belum Dipilih.')</script>";
			$link = $url."survey/survey.php";
			header("refresh:0.1; url=$link");
		}
	}
	catch(Exception $Ex)
	{
		echo "Something Wrong. ".$Ex;
	}
}
//KONDISI POST SUBMIT BELUM DI ASSIGN
else{
	echo "WRONG ACCESS";
	$link = "https://kabayanpasti.kemenkumham.go.id";
	header("refresh:0.1; $link");	
}

?>