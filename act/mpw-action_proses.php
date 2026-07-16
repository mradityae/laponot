<?php

session_save_path('../login/session');
session_start();
include "../config/koneksi.php";
require '../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\IOFactory;
date_default_timezone_set('Asia/Jakarta');

// Pastikan autoload vendor untuk PhpSpreadsheet sudah masuk jika digunakan
// require '../vendor/autoload.php'; 

$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : (isset($_GET['aksi']) ? $_GET['aksi'] : '');

switch ($aksi) {
    case 'tambah':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $sql = "INSERT INTO register_mpw (
                            no_surat, no_register_perkara, tanggal_surat, tanggal_pemanggilan, 
                            pelapor_nama, pelapor_telepon, pelapor_alamat, pelapor_status_hadir, 
                            terlapor_nama, kedudukan_notaris, terlapor_telepon, terlapor_status_hadir, 
                            nomor_surat_panggilan, created_at, updated_at
                        ) VALUES (
                            :no_surat, :no_register_perkara, :tanggal_surat, :tanggal_pemanggilan, 
                            :pelapor_nama, :pelapor_telepon, :pelapor_alamat, :pelapor_status_hadir, 
                            :terlapor_nama, :kedudukan_notaris, :terlapor_telepon, :terlapor_status_hadir, 
                            :nomor_surat_panggilan, NOW(), NOW()
                        )";
                
                $stmt = $koneksi->prepare($sql);
                
                $stmt->bindParam(':no_surat', $_POST['no_surat']);
                $stmt->bindParam(':no_register_perkara', $_POST['no_register_perkara']);
                $stmt->bindParam(':tanggal_surat', $_POST['tanggal_surat']);
                $stmt->bindParam(':tanggal_pemanggilan', $_POST['tanggal_pemanggilan']);
                $stmt->bindParam(':pelapor_nama', $_POST['pelapor_nama']);
                $stmt->bindParam(':pelapor_telepon', $_POST['pelapor_telepon']);
                $stmt->bindParam(':pelapor_alamat', $_POST['pelapor_alamat']);
                $stmt->bindParam(':pelapor_status_hadir', $_POST['pelapor_status_hadir']);
                $stmt->bindParam(':terlapor_nama', $_POST['terlapor_nama']);
                $stmt->bindParam(':kedudukan_notaris', $_POST['kedudukan_notaris']);
                $stmt->bindParam(':terlapor_telepon', $_POST['terlapor_telepon']);
                $stmt->bindParam(':terlapor_status_hadir', $_POST['terlapor_status_hadir']);
                $stmt->bindParam(':nomor_surat_panggilan', $_POST['nomor_surat_panggilan']);
                
                $stmt->execute();
                
                header("Location: ../superadmin/register_mpw_tabel.php?status=success_tambah");
                exit();
            } catch (PDOException $e) {
                header("Location: ../superadmin/register_mpw_tabel.php?status=failed");
                exit();
            }
        }
        break;

    case 'upload_excel':
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_excel_mpw'])) {

            try {
                $file_tmp = $_FILES['file_excel_mpw']['tmp_name'];

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_tmp);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();

                // Mulai baca dari baris ke-2 (baris pertama adalah header)
                for ($row = 2; $row <= $highestRow; $row++) {

                    $no_surat                = trim((string)$sheet->getCell("A$row")->getValue());
                    $no_register_perkara     = trim((string)$sheet->getCell("B$row")->getValue());

                    // Skip jika data utama kosong
                    if ($no_surat == '' || $no_register_perkara == '') {
                        continue;
                    }

                    // ===========================
                    // TANGGAL SURAT
                    // ===========================
                    $tanggal_surat = $sheet->getCell("C$row")->getValue();

                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($sheet->getCell("C$row"))) {
                        $tanggal_surat = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal_surat)
                            ->format('Y-m-d');
                    } else {
                        if (!empty($tanggal_surat)) {
                            // Jika format excel berupa teks dd/mm/yyyy, ubah '/' menjadi '-' agar dipahami strtotime
                            $tanggal_surat_clean = str_replace('/', '-', $tanggal_surat);
                            $tanggal_surat = date('Y-m-d', strtotime($tanggal_surat_clean));
                        } else {
                            $tanggal_surat = null;
                        }
                    }

                    // ===========================
                    // TANGGAL PEMANGGILAN
                    // ===========================
                    $tanggal_pemanggilan = $sheet->getCell("D$row")->getValue();

                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($sheet->getCell("D$row"))) {
                        $tanggal_pemanggilan = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal_pemanggilan)
                            ->format('Y-m-d');
                    } else {
                        if (!empty($tanggal_pemanggilan)) {
                            // Jika format excel berupa teks dd/mm/yyyy, ubah '/' menjadi '-'
                            $tanggal_pemanggilan_clean = str_replace('/', '-', $tanggal_pemanggilan);
                            $tanggal_pemanggilan = date('Y-m-d', strtotime($tanggal_pemanggilan_clean));
                        } else {
                            $tanggal_pemanggilan = null;
                        }
                    }

                    $pelapor_nama          = trim((string)$sheet->getCell("E$row")->getValue());
                    $pelapor_telepon       = trim((string)$sheet->getCell("F$row")->getValue());
                    $pelapor_status_hadir  = trim((string)$sheet->getCell("G$row")->getValue());
                    $pelapor_alamat        = trim((string)$sheet->getCell("H$row")->getValue());

                    $terlapor_nama         = trim((string)$sheet->getCell("I$row")->getValue());
                    $kedudukan_notaris     = trim((string)$sheet->getCell("J$row")->getValue());
                    $terlapor_telepon      = trim((string)$sheet->getCell("K$row")->getValue());
                    $terlapor_status_hadir = trim((string)$sheet->getCell("L$row")->getValue());

                    $nomor_surat_panggilan = trim((string)$sheet->getCell("M$row")->getValue());

                    // ===========================
                    // CEK DUPLIKAT
                    // ===========================
                    $cek = $koneksi->prepare("
                        SELECT COUNT(*)
                        FROM register_mpw
                        WHERE no_surat = ?
                        AND no_register_perkara = ?
                    ");

                    $cek->execute([
                        $no_surat,
                        $no_register_perkara
                    ]);

                    if ($cek->fetchColumn() > 0) {
                        continue;
                    }

                    // ===========================
                    // INSERT INTO DATABASE
                    // ===========================
                    $insert = $koneksi->prepare("
                        INSERT INTO register_mpw
                        (
                            no_surat,
                            no_register_perkara,
                            tanggal_surat,
                            tanggal_pemanggilan,
                            pelapor_nama,
                            pelapor_telepon,
                            pelapor_alamat,
                            pelapor_status_hadir,
                            terlapor_nama,
                            kedudukan_notaris,
                            terlapor_telepon,
                            terlapor_status_hadir,
                            nomor_surat_panggilan,
                            created_at,
                            updated_at
                        )
                        VALUES
                        (
                            ?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW()
                        )
                    ");

                    $insert->execute([
                        $no_surat,
                        $no_register_perkara,
                        $tanggal_surat,
                        $tanggal_pemanggilan,
                        $pelapor_nama,
                        $pelapor_telepon,
                        $pelapor_alamat,
                        $pelapor_status_hadir,
                        $terlapor_nama,
                        $kedudukan_notaris,
                        $terlapor_telepon,
                        $terlapor_status_hadir,
                        $nomor_surat_panggilan
                    ]);

                }
                header("Location: ../superadmin/register_mpw_tabel.php?status=success_tambah");
                exit();
            } catch (Exception $e) {
                header("Location: ../superadmin/register_mpw_tabel.php?status=failed");
                exit();
            }
        }
    break;
        
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $sql = "UPDATE register_mpw SET 
                            no_surat = :no_surat, 
                            no_register_perkara = :no_register_perkara, 
                            tanggal_surat = :tanggal_surat, 
                            tanggal_pemanggilan = :tanggal_pemanggilan, 
                            pelapor_nama = :pelapor_nama, 
                            pelapor_telepon = :pelapor_telepon, 
                            pelapor_alamat = :pelapor_alamat, 
                            pelapor_status_hadir = :pelapor_status_hadir, 
                            terlapor_nama = :terlapor_nama, 
                            kedudukan_notaris = :kedudukan_notaris, 
                            terlapor_telepon = :terlapor_telepon, 
                            terlapor_status_hadir = :terlapor_status_hadir, 
                            nomor_surat_panggilan = :nomor_surat_panggilan, 
                            updated_at = NOW() 
                        WHERE id = :id";
                
                $stmt = $koneksi->prepare($sql);
                
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':no_surat', $_POST['no_surat']);
                $stmt->bindParam(':no_register_perkara', $_POST['no_register_perkara']);
                $stmt->bindParam(':tanggal_surat', $_POST['tanggal_surat']);
                $stmt->bindParam(':tanggal_pemanggilan', $_POST['tanggal_pemanggilan']);
                $stmt->bindParam(':pelapor_nama', $_POST['pelapor_nama']);
                $stmt->bindParam(':pelapor_telepon', $_POST['pelapor_telepon']);
                $stmt->bindParam(':pelapor_alamat', $_POST['pelapor_alamat']);
                $stmt->bindParam(':pelapor_status_hadir', $_POST['pelapor_status_hadir']);
                $stmt->bindParam(':terlapor_nama', $_POST['terlapor_nama']);
                $stmt->bindParam(':kedudukan_notaris', $_POST['kedudukan_notaris']);
                $stmt->bindParam(':terlapor_telepon', $_POST['terlapor_telepon']);
                $stmt->bindParam(':terlapor_status_hadir', $_POST['terlapor_status_hadir']);
                $stmt->bindParam(':nomor_surat_panggilan', $_POST['nomor_surat_panggilan']);
                
                $stmt->execute();
                
                header("Location: ../superadmin/register_mpw_tabel.php?status=success_update");
                exit();
            } catch (PDOException $e) {
                header("Location: ../superadmin/register_mpw_tabel.php?status=failed");
                exit();
            }
        }
        break;

    case 'hapus':
        if (isset($_GET['id'])) {
            try {
                $sql = "DELETE FROM register_mpw WHERE id = :id";
                $stmt = $koneksi->prepare($sql);
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                
                header("Location: ../superadmin/register_mpw_tabel.php?status=success_hapus");
                exit();
            } catch (PDOException $e) {
                header("Location: ../superadmin/register_mpw_tabel.php?status=failed");
                exit();
            }
        }
        break;

    default:
        header("Location: ../superadmin/register_mpw_tabel.php");
        exit();
}
?>