<?php
session_start();
include "../config/koneksi.php"; 
require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Fungsi untuk menentukan kategori teks dan nilai nominal PNBP
 * berdasarkan Nilai Penjaminan yang diinput.
 */
function mappingKategori($input) {
    $input = trim(strtolower(
        str_replace(['–','—'], '-', $input)
    ));

    $map = [
        '<=50 juta' => ['label' => '<=50 juta', 'pnbp' => 50000],
        '50-100 juta' => ['label' => '50-100 juta', 'pnbp' => 100000],
        '100-250 juta' => ['label' => '100-250 juta', 'pnbp' => 200000],
        '250-500 juta' => ['label' => '250-500 juta', 'pnbp' => 450000],
        '500 juta - 1 m' => ['label' => '500 juta – 1 M', 'pnbp' => 850000],
        '1 - 100 m' => ['label' => '1 – 100 M', 'pnbp' => 1800000],
        '100 - 500 m' => ['label' => '100 – 500 M', 'pnbp' => 3500000],
        '500 m - 1 t' => ['label' => '500 M – 1 T', 'pnbp' => 6800000],
        '>1 t' => ['label' => '>1 T', 'pnbp' => 13300000],
        'tidak relevan' => ['label' => 'Tidak Relevan', 'pnbp' => 0],
    ];

    return $map[$input] ?? null;
}

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

        // --- SOLUSI BARIS KOSONG & HITUNG TOTAL ---
        $filteredData = [];
        foreach ($sheetData as $i => $row) {
            if ($i == 1) continue; // Skip header
            if (isset($row['B']) && !empty(trim($row['B']))) {
                $filteredData[$i] = $row;
            }
        }

        $jumlah_baris = count($filteredData);
        if ($jumlah_baris > 3000) {
            throw new Exception("GAGAL: Maksimal 3000 record. Terdeteksi $jumlah_baris record.");
        }
        
        // --- AMBIL DATA EXISTING DARI DATABASE SEKALIGUS (OPTIMASI MEMORI) ---
        $sqlExisting = "SELECT nomor, DATE_FORMAT(tanggal,'%Y-%m') AS bulan FROM laporan_entitas WHERE id_notaris = ?";
        $stmtExisting = $koneksi->prepare($sqlExisting);
        $stmtExisting->execute([$id_notaris]);
        
        $dbExisting = [];
        while ($r = $stmtExisting->fetch(PDO::FETCH_ASSOC)) {
            $dbExisting[$r['nomor'] . '|' . $r['bulan']] = true;
        }

        $koneksi->beginTransaction();

        $dataToInsert = [];
        $duplicateCheckFile = []; 

        foreach ($filteredData as $i => $row) {
            $judul       = isset($row['A']) ? trim($row['A']) : '';
            $nomor       = trim($row['B']);
            $tanggal     = isset($row['C']) ? trim($row['C']) : ''; 
            $pemberi     = isset($row['D']) ? trim($row['D']) : '';
            $penerima    = isset($row['E']) ? trim($row['E']) : '';
            $sertifikat  = isset($row['F']) ? trim($row['F']) : '';
            $input_nilai = isset($row['G']) ? trim($row['G']) : '';
            
            // Berikan nilai otomatis 'Notaris' jika kolom DAFTAR OLEH kosong
            $daftar_oleh = (isset($row['H']) && trim($row['H']) !== '') ? trim($row['H']) : 'Notaris';

            if (empty($input_nilai)) {
                $input_nilai=50000000;
                //throw new Exception("Gagal: Kolom NILAI PENJAMINAN tidak boleh kosong (Baris $i)");
            }

            // Bersihkan format string uang jika input berupa angka/nominal rupiah
            $clean_numeric = str_replace(['Rp', 'rp', '.', ',', ' '], '', $input_nilai);

            if (is_numeric($clean_numeric) && !empty($clean_numeric)) {
                $raw_nilai = (float) $clean_numeric;
                $hasilPNBP = hitungPNBP($raw_nilai);

                $label_penjaminan = $hasilPNBP['label'];
                $value_penjaminan = $raw_nilai;
            } else {
                $hasilMap = mappingKategori($input_nilai);

                if (!$hasilMap) {
                    throw new Exception("Gagal: Format NILAI PENJAMINAN tidak dikenali (Baris $i) -> '$input_nilai'");
                }

                $label_penjaminan = $hasilMap['label'];
                $value_penjaminan = $hasilMap['pnbp'];
            }

            // Validasi Mandatory Akhir
            if (empty($nomor) || empty($tanggal)) continue;

            // Cek Format Tanggal
            $time = strtotime($tanggal);
            if ($time === false) {
                continue; 
            }
            $bulan_tahun = date('Y-m', $time);
            $unique_key  = $nomor . "|" . $bulan_tahun;

            // --- CEK DUPLIKAT DI EXCEL ---
            if (isset($duplicateCheckFile[$unique_key])) {
                throw new Exception("Gagal: Terdeteksi Nomor Akta ganda [$nomor] pada periode [$bulan_tahun] di file Excel (Baris $i).");
            }
            $duplicateCheckFile[$unique_key] = true;

            // --- CEK DUPLIKAT DI DATABASE ---
            if (isset($dbExisting[$unique_key])) {
                throw new Exception("Gagal: Nomor Akta [$nomor] pada periode [$bulan_tahun] sudah pernah terdaftar di database (Baris $i).");
            }

            // Gabungkan semua parameter untuk Bulk Insert nanti
            $dataToInsert[] = $id_notaris;
            $dataToInsert[] = $judul;
            $dataToInsert[] = $tipe;
            $dataToInsert[] = $nomor;
            $dataToInsert[] = date('Y-m-d', $time);
            $dataToInsert[] = $pemberi;
            $dataToInsert[] = $penerima;
            $dataToInsert[] = $sertifikat;
            $dataToInsert[] = $label_penjaminan;
            $dataToInsert[] = $value_penjaminan;
            $dataToInsert[] = $daftar_oleh;
        }

        $total_data = count($dataToInsert) / 11; // 11 kolom per baris

        if ($total_data === 0) {
            throw new Exception("Tidak ada data valid untuk diunggah.");
        }

        // --- OPTIMASI TERBESAR: BULK INSERT PREPARED STATEMENTS ---
        // Membuat string placeholders secara dinamis, misal: (?,?,?,?,?,?,?,?,?,?,?), (?,?,?,?,?,?,?,?,?,?,?)
        $rowPlaces = '(' . implode(',', array_fill(0, 11, '?')) . ', "Pendaftaran", "Terverifikasi", NOW())';
        $allPlaces = implode(',', array_fill(0, $total_data, $rowPlaces));

        $sql_ins = "INSERT INTO laporan_entitas (
                        id_notaris, judul_akta, tipe, nomor, tanggal, 
                        pemberi, penerima, no_sertifikat, nilai_penjaminan, 
                        value_penjaminan, daftar_oleh, jenis_transaksi, status, created_at
                    ) VALUES $allPlaces";
        
        $stmt_ins = $koneksi->prepare($sql_ins);
        $stmt_ins->execute($dataToInsert); // Eksekusi sekaligus 1 kali kirim untuk 3000 data

        $koneksi->commit();
        echo "<script>alert('Berhasil! " . $total_data . " data telah diunggah dengan aman.'); window.location='../pengguna/unggah_laporan';</script>";

    } catch (Exception $e) {
        if (isset($koneksi) && $koneksi->inTransaction()) {
            $koneksi->rollBack();
        }
        echo "<script>alert('PROSES BERHENTI: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}