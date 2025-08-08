<?PHP
include("../config/koneksi.php");
include'../models/models.php';
if(!isset($_GET['prc']))
{
	try
	{
		$emailcek = strtolower($_POST['email']);
		$passcek = md5($_POST['password']);

		$koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    $ambil=$koneksi->prepare("SELECT * FROM notaris WHERE email=:isiemail and password=:isipasssword");
	    $ambil->BindParam(":isiemail",$emailcek);
	    $ambil->BindParam(":isipasssword",$passcek);
		$ambil->execute();
		
		$count = $ambil->rowCount();
		if($count == 1)
		{
		    $result = $ambil->fetch(PDO::FETCH_ASSOC);

		    if($result['aktif'] == 0)
			{
				$koneksi = null;
				echo"
						<script>
							alert('Akun Anda Belum Aktif. Silahkan Cek Email Anda Untuk Melakukan Aktivasi atau Hubungi Admin.');
						</script>
					";
					$link = $url;
					header("refresh:0.1; url=$link");
			}
			else
			{
				
				session_save_path('session');
				session_start();
				$_SESSION['email'] = $_POST['email'];
				$_SESSION["nama"] = $result['nama'];
				$_SESSION["user_role"] = $result['level'];
				$_SESSION["hak_akses"] = $result['level'];
				$_SESSION["kode_user"] = $result['id_notaris'];
				$_SESSION["kedudukan"] = $result['id_kedudukan'];
/*
				$sub_query = "
			        INSERT INTO login_details 
			        (id) 
			        VALUES ('".$result['id']."')
			        ";
			        $statement = $koneksi->prepare($sub_query);
			        $statement->execute();
			        $_SESSION['login_details_id'] = $koneksi->lastInsertId();*/
			        $koneksi = null;

				if ($_SESSION['user_role'] == 1) {
					header('Location:../admin/dashboard_fidusia');
				}
				else if($_SESSION['user_role'] == 2)
				{
					header('Location:../pengguna/dashboard_fidusia');
				}
				else if($_SESSION['user_role'] == 0)
				{
					header('Location:../superadmin/dashboard_fidusia');
				}
				else
				{
					echo"
						<script>
							alert('Invalid User Role');
							
						</script>
					";
					$link = $url;
					header("refresh:0.1; url=$link");
				}	
			}
		}
		else{
			
			$koneksi = null;
			echo"
				<script>
					alert('Invalid Username & Password');
					
				</script>
			";
			$link = $url;
			header("refresh:0.1; url=$link");
		}
	}	
	catch(Exception $Ex)
	{
		echo "Koneksi Gagal ".$Ex->getMessage();
	}
} 
else 
{
	/*session_save_path('session');
session_start();
session_unset('email');
session_destroy();*/
header('LOCATION:../survey/survey.php');
}
	
?>