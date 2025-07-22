<?php
session_start();

if(isset($_POST['submit'])){

	require '../config/koneksi.php';
	require '../models/models.php';
	require 'PHPMailerAutoload.php';
	$mail = new PHPMailer;

	try
	{
		$email          	= RemoveSpecialChar(strtolower($_POST['email']));
		$password       	= RemoveSpecialChar($_POST['password']);
		$passwordulang  	= RemoveSpecialChar($_POST['passwordulang']);
		$captcha        	= RemoveSpecialChar($_POST['captcha']);

		//CEK APAKAH EMAIL TELAH TERDAFTAR DI DATABASE UNTUK DIJADIKAN AKUN
		if(cekEmailReset($koneksi, $email, 2) == false)
		{
			//CEK CAPTCHA APAKAH BENAR ATAU SALAH, JIKA SALAH KEMBALI KE HALAMAN REGISTRASIU
			if ($_SESSION["code"] != $captcha) 
			{
				echo "<script>alert('CAPTCHA SALAH !')</script>";
				$link = $url."register/lupapassword";
				header("refresh:0; url=$link");
			}
			//JIKA BENAR, TAMBAHKAN DATA PENGGUNA KE DATABASE
			else
			{	
				//TAMBAH DATA KE DATABASE
				if (resetPassword($koneksi,$email, $password)) 
				{					
					//JIKA BERHASIL MASUK DATABASE, KIRIM EMAIL NOTIFIKASI KEPADA EMAIL PENGGUNA
					
					$mail->isSMTP();                                      // Set mailer to use SMTP
					$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = 'ahu.kumhamjabar@gmail.com';                 // SMTP username
					$mail->Password = 'vzrrmknpiivdztgp';                           // SMTP password
					$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					$mail->Port = 587;                                    // TCP port to connect to

					$mail->setFrom('smtp@kanwiljabar.com', 'Laporan Notaris Kanwil Kemenkumham Jabar');
					$mail->addAddress($email, '');     // Add a recipient
					$mail->isHTML(true);                                  // Set email format to HTML

					$mail->Subject = 'RESET PASSWORD APLIKASI LAPORAN NOTARIS';
					$mail->Body    = 
					'
					<h3>
						<center>RESET PASSWORD APLIKASI LAPORAN NOTARIS KANWIL JABAR</center><br><br>
						Anda telah melakukan reset password akun pada Aplikasi Laporan Notaris Kanwil Jabar.<br><br>
						<center>Berikut adalah password baru Anda : <b>'.$password.'</b><center><br><br>
						Rahasiakan password untuk keamanan akun Anda. Silahkan klik pada di link berikut untuk mengakses Aplikasi Laporan Notaris : <a href = "http://kabayanpasti.kemenkumham.go.id/laponot/">Aplikasi Laporan Notaris</a>
					</h3>
					';
					$mail->AltBody = '';

					if(!$mail->send()) 
					{
						echo "<script>alert('Reset Password Berhasil. Silhakan login menggunakan password baru Anda.')</script>";
						
						$link = $url;
						header("refresh:0; url=$link");
					    /*echo 'Message could not be sent.';
					    echo 'Mailer Error: ' . $mail->ErrorInfo;*/
					} 
					else 
					{
					    echo "<script>alert('Reset Password Berhasil. Silahkan Cek email masuk ke Inbox atau Spam untuk mendapatkan password baru Anda.')</script>";
						$link = $url;
						header("refresh:0; url=$link");
						/*echo 'Message has been sent';*/
					}					
				}
				//KONDISI JIKA GAGAL MENGUBAH PASSWORD KE DATABASE
				else
				{
					echo "<script>alert('Reset Password Gagal. Coba lagi beberapa saat.')</script>";
					$link = $url."lupapassword/index";
					header("refresh:0; url=$link");
				}
			}
		}
		else
		{
			echo "<script>alert('EMAIL BELUM TERDAFTAR PADA APLIKASI LAPORAN NOTARIS. GUNAKAN EMAIL YANG ANDA GUNAKAN KETIKA MENDAFTAR.')</script>";
			$link = $url."register/lupapassword";
			header("refresh:0; url=$link");
		}
	}
	catch(Exception $Ex)
	{
		/*echo "<script>alert('Registrasi Gagal. Coba lagi beberapa saat.')</script>";
		$link = $url."register/index.php";
		header("refresh:0.1; url=$link");*/
		echo "Something Wrong. ".$Ex;
	}
}
//KONDISI POST SUBMIT BELUM DI ASSIGN
else
{
	echo "WRONG ACCESS";
	$link = "https://kabayanpasti.kemenkumham.go.id";
	header("refresh:0.1; $link");	
}

?>