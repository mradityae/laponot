<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include "header.php";
include("../config/koneksi.php");
require '../vendor/autoload.php'; // pastikan ini sesuai path composer autoload
include_once("../log_activity.php");

// Default filter
$status = 1;
$defaultKedudukanId = 12; // Kota Bandung
$kedudukan = isset($_GET['kedudukan']) ? $_GET['kedudukan'] : $defaultKedudukanId;

// Generate password massal
if (isset($_POST['generate_password'])) {
    $stmt = $koneksi->prepare("SELECT id_notaris, nama, email FROM notaris WHERE aktif = 1 AND level = 2 AND is_migration = 1 AND id_kedudukan = :kedudukan");
    $stmt->bindParam(":kedudukan", $kedudukan);
    $stmt->execute();

    while ($row = $stmt->fetch()) {
        $id = $row['id_notaris'];
        $email = $row['email'];
        $nama = $row['nama'];
        $plainPassword = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz123456789'), 0, 8);
        $hashed = md5($plainPassword);

        // Simpan ke DB
        $update = $koneksi->prepare("
            UPDATE notaris 
            SET password = :password, ket = :plain, last_generate = NOW() 
            WHERE id_notaris = :id
        ");

        $update->execute([
            ':password' => $hashed,
            ':plain' => $plainPassword,
            ':id' => $id
        ]);

        // Kirim email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'radityamuhammad275@gmail.com'; // ganti sesuai kredensial
            $mail->Password   = 'keolfcaxzkkrtpvf ';          // app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('smtp@kanwiljabar.com', 'Aplikasi Fidusia Kanwil Kemenkumham Jabar');
            $mail->addAddress($email, $nTama);
            $mail->isHTML(true);
            $mail->Subject = 'AKUN APLIKASI FIDUSIA ANDA';
            $mail->Body    = "
                <h3>Halo $nama,</h3>
                <p>Berikut adalah akun Anda untuk mengakses Aplikasi Fidusia:</p>
                <ul>
                    <li><strong>Email:</strong> $email</li>
                    <li><strong>Password:</strong> $plainPassword</li>
                </ul>
                <p>Silakan login dan segera ubah password Anda setelah masuk ke sistem.</p>
                <br><br>
                <em>Email ini dikirim otomatis oleh sistem Aplikasi Fidusia Kanwil Kemenkumham Jawa Barat.</em>
            ";
            $mail->AltBody = "Email: $email\nPassword: $plainPassword";

            // $mail->send();
        } catch (Exception $e) {
            write_log("Gagal kirim email ke $email: {$mail->ErrorInfo}");
        }

        // Log lokal (opsional)
        // file_put_contents("generated_passwords_{$kedudukan}.txt", "ID: $id | $email | Password: $plainPassword\n", FILE_APPEND);
    }

    echo "<script>alert('✅ Password berhasil digenerate dan dikirim ke email notaris.'); window.location.href='daftar_generate_password.php';</script>";
}
?>

<div id="page-wrapper">
    <div id="page-inner">
        <h2 class="page-head-line">Daftar Notaris Aktif - Wilayah Terpilih</h2>

        <!-- Filter Wilayah -->
        <form method="GET" class="form-inline mb-4">
            <label class="mr-2 font-weight-bold">Filter Wilayah:</label>
            <select name="kedudukan" class="form-control mr-2">
                <?php
                $wilayah = $koneksi->query("SELECT * FROM kedudukan ORDER BY nama_kedudukan ASC");
                while ($data = $wilayah->fetch()) {
                    $selected = $kedudukan == $data['id_kedudukan'] ? "selected" : "";
                    echo "<option value='{$data['id_kedudukan']}' $selected>{$data['nama_kedudukan']}</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>

        <!-- Tombol Generate Password -->
        <form method="POST" class="mb-4">
            <input type="hidden" name="kedudukan" value="<?= $kedudukan ?>">
            <button type="submit" name="generate_password" class="btn btn-warning" onclick="return confirm('Generate password baru dan kirim email untuk semua notaris di wilayah ini?')">
                🔐 Generate Password & Kirim Email
            </button>
        </form>

        <!-- Tabel Pengguna -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-dark text-white">Daftar Notaris Aktif</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="rekapTable" class="table table-striped table-bordered table-hover table-sm m-0">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th width="50">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Kedudukan</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Last Generate </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = $koneksi->prepare("SELECT notaris.*, kedudukan.nama_kedudukan 
                                FROM notaris
                                JOIN kedudukan ON kedudukan.id_kedudukan = notaris.id_kedudukan
                                WHERE notaris.aktif = :aktif AND notaris.level = 2 AND notaris.id_kedudukan = :kedudukan
                                AND notaris.is_migration = 1
                                ORDER BY notaris.id_notaris DESC");
                            $query->execute([
                                ':aktif' => $status,
                                ':kedudukan' => $kedudukan
                            ]);

                            while ($row = $query->fetch()) {
                                echo "<tr>";
                                echo "<td class='text-center'>{$no}</td>";
                                echo "<td>{$row['nama']}</td>";
                                echo "<td>{$row['email']}</td>";
                                echo "<td>{$row['nama_kedudukan']}</td>";
                                echo "<td class='text-center'>Publik</td>";
                                echo "<td class='text-center'><span class='badge badge-success'>Aktif</span></td>";
                                echo "<td class='text-center'>" . date('d-M-Y', strtotime($row['createDate'])) . "</td>";
                                if (empty($row['last_generate'])){
                                    echo "<td><center>-</center></td>";
                                }
                                else{
                                    echo "<td>{$row['last_generate']}</td>";
                                }
                                echo "</tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>
