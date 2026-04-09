<?php
session_start();

if(isset($_POST['submit'])){

	require '../config/koneksi.php';
	require '../models/models.php';
	require 'PHPMailerAutoload.php';
	$mail = new PHPMailer;

	try
	{
		$nama 				= RemoveSpecialChar($_POST['nama']);
		$jenis_kelamin   	= RemoveSpecialChar($_POST['jeniskelamin']);
		$kedudukan   		= RemoveSpecialChar($_POST['kedudukan']);
		$skPengangkatan   	= RemoveSpecialChar($_POST['skPengangkatan']);
		$tglSkPengangkatan  = RemoveSpecialChar($_POST['tglSkPengangkatan']);
		$baPelantikan   	= RemoveSpecialChar($_POST['baPelantikan']);
		$tglBaPelantikan   	= RemoveSpecialChar($_POST['tglBaPelantikan']);
		$alamat         	= RemoveSpecialChar($_POST['alamat']);
		$telepon        	= RemoveSpecialChar($_POST['telepon']);
		$email          	= RemoveSpecialChar(strtolower($_POST['email']));
		$password       	= RemoveSpecialChar($_POST['password']);
		$passwordulang  	= RemoveSpecialChar($_POST['passwordulang']);
		$captcha        	= RemoveSpecialChar($_POST['captcha']);

		$fullDirBaru 		= $url.'act/photo/user.png';

		//CEK APAKAH EMAIL TELAH TERDAFTAR DI DATABASE UNTUK DIJADIKAN AKUN
		if(cekEmail($koneksi, $email))
		{
			//CEK CAPTCHA APAKAH BENAR ATAU SALAH, JIKA SALAH KEMBALI KE HALAMAN REGISTRASI
			if ($_SESSION["code"] != $captcha)
			{
				echo "<script>alert('CAPTCHA SALAH !')</script>";
				$link = $url."register/index.php";
				header("refresh:0; url=$link");
			}
			//JIKA BENAR, TAMBAHKAN DATA PENGGUNA KE DATABASE
			else
			{
				//TAMBAH DATA KE DATABASE
				if (tambahNotaris($koneksi, $nama, $jenis_kelamin, $kedudukan, $skPengangkatan, $tglSkPengangkatan, $baPelantikan, $tglBaPelantikan ,$alamat, $telepon, $email, $password,2,1, $fullDirBaru))
				{
					//tambah ke tabel tocode buat link invitation
					$secretCode = addToCode($koneksi, $email);
					//JIKA BERHASIL MASUK DATABASE, KIRIM EMAIL NOTIFIKASI KEPADA EMAIL PENGGUNA

					if ($secretCode != "Gagal")
					{
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

						$mail->Subject = 'PENDAFTARAN LAPORAN NOTARIS';
						$mail->Body    =
						'
						<h3><center>
							PENDAFTARAN ONLINE APLIKASI LAPORAN NOTARIS KANWIL JABAR<br><br>
							Anda telah melakukan registrasi akun pada Aplikasi Laporan Notaris Kanwil Jabar.<br>
							Silahkan klik pada tombol di bawah untuk melakukan Aktivasi Akun Anda.<br><br>
							<a href = "https://kabayanpasti.kemenkumham.go.id/laponot/act/aktivasi_akun_proses.php?email='.$email.'&secretCode='.$secretCode.'"><input type="button" name="buttonLink" value= "Aktivasi"></input</a>
						</center></h3>
						';
						$mail->AltBody = '';

						if(!$mail->send())
						{
							echo "<script>alert('Registrasi Berhasil. Silahkan untuk login atau masuk ke aplikasi menggunakan email dan password yang telah didaftarkan.')</script>";

							$link = $url;
							header("refresh:0; url=$link");
						    /*echo 'Message could not be sent.';
						    echo 'Mailer Error: ' . $mail->ErrorInfo;*/
						}
						else
						{
						    echo "<script>alert('Registrasi Berhasil. Silahkan untuk login atau masuk ke aplikasi menggunakan email dan password yang telah didaftarkan.')</script>";
							$link = $url;
							header("refresh:0; url=$link");
							/*echo 'Message has been sent';*/
						}
					}
					else
					{
						echo "<script>alert('Gagal untuk mengirim Email Aktivasi. Mohon tunggu admin untuk mengaktifkan akun Anda. Gagal Secret Code')</script>";
						$link = $url;
						header("refresh:0; url=$link");
					}
				}
				//KONDISI JIKA GAGAL MENYIMPAN DATA PENGGUNA KE DATABASE
				else
				{
					echo "<script>alert('Registrasi Gagal. Coba lagi beberapa saat.')</script>";
					$link = $url."register/index.php";
					header("refresh:0; url=$link");
				}
			}
		}
		//KONDISI JIKA EMAIL TELAH TERDAFTAR.
		else
		{
			echo "<script>alert('EMAIL TELAH TERDAFTAR. TIDAK BISA MEMBUAT AKUN DENGAN EMAIL YANG SAMA')</script>";
			$link = $url."register/index.php";
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