<?php
session_start();
include "../config/koneksi.php"; 
require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Fungsi untuk menentukan kategori teks dan nilai nominal PNBP
 * berdasarkan Nilai Penjaminan yang diinput.
 */
function hitungPNBP($nominal_input) {
    if ($nominal_input <= 0) {
        return ['label' => 'Tidak Relevan', 'pnbp' => 0];
    } elseif ($nominal_input <= 50000000) {
        return ['label' => '<=50 juta', 'pnbp' => 50000];
    } elseif ($nominal_input <= 100000000) {
        return ['label' => '50-100 juta', 'pnbp' => 100000];
    } elseif ($nominal_input <= 250000000) {
        return ['label' => '100-250 juta', 'pnbp' => 200000];
    } elseif ($nominal_input <= 500000000) {
        return ['label' => '250-500 juta', 'pnbp' => 450000];
    } elseif ($nominal_input <= 1000000000) {
        return ['label' => '500 juta – 1 M', 'pnbp' => 850000];
    } elseif ($nominal_input <= 100000000000) {
        return ['label' => '1 – 100 M', 'pnbp' => 1800000];
    } elseif ($nominal_input <= 500000000000) {
        return ['label' => '100 – 500 M', 'pnbp' => 3500000];
    } elseif ($nominal_input <= 1000000000000) {
        return ['label' => '500 M – 1 T', 'pnbp' => 6800000];
    } else {
        return ['label' => '>1 T', 'pnbp' => 13300000];
    }
}

if (isset($_POST['submit'])) {
    $id_notaris = $_POST['id'];
    $tipe       = $_POST['tipe']; // fidusia
    $file_tmp   = $_FILES['file_excel']['tmp_name'];

    try {
        if (!file_exists($file_tmp)) {
            throw new Exception("File tidak ditemukan.");
        }

        $spreadsheet = IOFactory::load($file_tmp);
        $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // --- SOLUSI BARIS KOSONG (FILTERING) ---
        // Kita hanya mengambil baris yang kolom B (Nomor Akta) tidak kosong
        $filteredData = array_filter($sheetData, function($row) {
            return !empty(trim($row['B'])); 
        });

        $jumlah_baris = count($filteredData);
        // Cek jika header ikut terhitung (asumsi baris 1 adalah header)
        foreach($filteredData as $idx => $val) {
            if($idx == 1) { $jumlah_baris--; break; }
        }

        if ($jumlah_baris > 3000) {
            throw new Exception("GAGAL: Maksimal 3000 record. Terdeteksi $jumlah_baris record.");
        }
        
        $koneksi->beginTransaction();

        $dataToInsert = [];
        $duplicateCheckFile = []; 

        foreach ($filteredData as $i => $row) {
            if ($i == 1) continue;

            $judul       = trim($row['A']);
            $nomor       = trim($row['B']);
            $tanggal     = trim($row['C']); 
            $pemberi     = trim($row['D']);
            $penerima    = trim($row['E']);
            $sertifikat  = trim($row['F']);
            
            // --- PERUBAHAN 1: Default Nilai Penjaminan ---
            $raw_nilai = preg_replace('/[^0-9]/', '', $row['G']); 
            if (empty($raw_nilai)) {
                // Jika kosong, set default ke kategori <= 50 juta
                $label_penjaminan = '<=50 juta';
                $value_penjaminan = 50000; // Sesuai permintaan "gocap"
            } else {
                $hasilPNBP = hitungPNBP((float)$raw_nilai);
                $label_penjaminan = $hasilPNBP['label'];
                $value_penjaminan = $raw_nilai; 
            }

            $daftar_oleh = isset($row['H']) ? trim($row['H']) : 'Notaris';

            // Validasi Mandatory: Jika nomor/tanggal kosong total, baru skip
            if (empty($nomor) || empty($tanggal)) continue;

            // Cek Format Tanggal
            $time = strtotime($tanggal);
            if (!$time) {
                // Kita skip saja jika tanggalnya ngaco agar proses tidak berhenti
                continue; 
            }
            $bulan_tahun = date('Y-m', $time);
            $unique_key  = $nomor . "|" . $bulan_tahun;

            // --- PERUBAHAN 2: Skip jika Duplikat di Excel ---
            if (in_array($unique_key, $duplicateCheckFile)) {
                throw new Exception("Gagal: Terdeteksi Nomor Akta ganda [$nomor] pada periode [$bulan_tahun] di file Excel (Baris $i). Silakan periksa kembali file Anda.");
                //continue; // Lewati ke baris berikutnya
            }
            $duplicateCheckFile[] = $unique_key;

            // --- PERUBAHAN 3: Skip jika Duplikat di Database ---
            $sql_cek = "SELECT id_laporan FROM laporan_entitas 
                        WHERE nomor = :nomor 
                        AND DATE_FORMAT(tanggal, '%Y-%m') = :bulan_tahun 
                        AND id_notaris = :id_notaris LIMIT 1";
            
            $stmt_cek = $koneksi->prepare($sql_cek);
            $stmt_cek->execute([
                ':nomor'       => $nomor,
                ':bulan_tahun' => $bulan_tahun,
                ':id_notaris'  => $id_notaris
            ]);

            if ($stmt_cek->fetch()) {
                continue; // Lewati jika data sudah ada di database
            }

            // Masukkan ke array bulk insert
            $dataToInsert[] = [
                $id_notaris, $judul, $tipe, $nomor, date('Y-m-d', $time), 
                $pemberi, $penerima, $sertifikat, $label_penjaminan, 
                $value_penjaminan, $daftar_oleh
            ];
        }

        if (empty($dataToInsert)) {
            throw new Exception("Tidak ada data valid untuk diunggah.");
        }

        // --- PROSES INSERT ---
        $sql_ins = "INSERT INTO laporan_entitas (
                        id_notaris, judul_akta, tipe, nomor, tanggal, 
                        pemberi, penerima, no_sertifikat, nilai_penjaminan, 
                        value_penjaminan, daftar_oleh, jenis_transaksi, status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendaftaran', 'Terverifikasi', NOW())";
        
        $stmt_ins = $koneksi->prepare($sql_ins);

        foreach ($dataToInsert as $dataRow) {
            $stmt_ins->execute($dataRow);
        }

        $koneksi->commit();
        echo "<script>alert('Berhasil! ".count($dataToInsert)." data telah diunggah.'); window.location='../pengguna/daftar_laporan_entitas';</script>";

    } catch (Exception $e) {
        if ($koneksi->inTransaction()) {
            $koneksi->rollBack();
        }
        echo "<script>alert('PROSES BERHENTI: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}