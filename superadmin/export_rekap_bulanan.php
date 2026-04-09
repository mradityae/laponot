<?php
include("../config/koneksi.php");

$bulan_awal  = $_GET['bulan_awal'] ?? '01';
$bulan_akhir = $_GET['bulan_akhir'] ?? '12';
$tahun       = $_GET['tahun'] ?? date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$data_notaris_aktif = [
    'Kabupaten Bandung' => 360, 'Kabupaten Bandung Barat' => 178, 'Kabupaten Bekasi' => 218,
    'Kabupaten Bogor' => 350, 'Kabupaten Ciamis' => 56, 'Kabupaten Cianjur' => 118,
    'Kabupaten Cirebon' => 383, 'Kabupaten Garut' => 210, 'Kabupaten Indramayu' => 201,
    'Kabupaten Karawang' => 247, 'Kabupaten Kuningan' => 110, 'Kabupaten Majalengka' => 104,
    'Kabupaten Pangandaran' => 33, 'Kabupaten Purwakarta' => 132, 'Kabupaten Subang' => 198,
    'Kabupaten Sukabumi' => 168, 'Kabupaten Sumedang' => 136, 'Kabupaten Tasikmalaya' => 94,
    'Kota Bandung' => 164, 'Kota Banjar' => 20, 'Kota Bekasi' => 237, 'Kota Bogor' => 207,
    'Kota Cimahi' => 102, 'Kota Cirebon' => 126, 'Kota Depok' => 185, 'Kota Sukabumi' => 71,
    'Kota Tasikmalaya' => 91
];

// Header agar file terunduh sebagai Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Laporan_Bulanan_{$bulan_awal}_sd_{$bulan_akhir}_{$tahun}.xls");
?>

<center>
    <h3>REKAP KEPATUHAN LAPORAN BULANAN NOTARIS</h3>
    <h4>Periode: <?= $nama_bulan[$bulan_awal] ?> - <?= $nama_bulan[$bulan_akhir] ?> <?= $tahun ?></h4>
</center>

<table border="1">
    <thead>
        <tr style="background-color: #2c3e50; color: white;">
            <th rowspan="2">No</th>
            <th rowspan="2">MPD (Kedudukan)</th>
            <th rowspan="2">Jml Notaris Aktif</th>
            <th rowspan="2">Jml Notaris Kirim</th>
            <th rowspan="2">Kepatuhan (%)</th>
            <th colspan="4">Rincian Akta</th>
            <th rowspan="2">Total Akta</th>
        </tr>
        <tr style="background-color: #2c3e50; color: white;">
            <th>Buku Daftar</th>
            <th>Waarmerking</th>
            <th>Legalisasi</th>
            <th>Buku Protes</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "
        SELECT 
            k.nama_kedudukan AS mpd,
            COUNT(DISTINCT la.id_notaris) AS jumlah_notaris_kirim,
            SUM(la.jml_buku_daftar) AS jml_buku_daftar,
            SUM(la.jml_tangan_dibukukan) AS jml_tangan_dibukukan,
            SUM(la.jml_tangan_disahkan) AS jml_tangan_disahkan,
            SUM(la.jml_buku_protes) AS jml_buku_protes,
            SUM(la.jml_buku_daftar + la.jml_tangan_dibukukan + la.jml_tangan_disahkan + la.jml_buku_protes) AS total_akta
        FROM laporan la
        JOIN notaris n ON n.id_notaris = la.id_notaris
        JOIN kedudukan k ON k.id_kedudukan = n.id_kedudukan
        WHERE YEAR(la.tanggal) = :tahun
          AND MONTH(la.tanggal) BETWEEN :bulan_awal AND :bulan_akhir
        GROUP BY k.id_kedudukan
        ORDER BY k.nama_kedudukan";

        $stmt = $koneksi->prepare($sql);
        $stmt->bindParam(':tahun', $tahun);
        $stmt->bindParam(':bulan_awal', $bulan_awal);
        $stmt->bindParam(':bulan_akhir', $bulan_akhir);
        $stmt->execute();

        $no = 1;
        $g_total_notaris = 0;
        $g_total_kirim = 0;
        $g_total_akta = 0;

        while ($row = $stmt->fetch()) {
            $notaris_aktif = $data_notaris_aktif[$row['mpd']] ?? 0;
            $persentase = ($notaris_aktif > 0) ? ($row['jumlah_notaris_kirim'] / $notaris_aktif) * 100 : 0;

            $g_total_notaris += $notaris_aktif;
            $g_total_kirim += $row['jumlah_notaris_kirim'];
            $g_total_akta += $row['total_akta'];

            echo "<tr>
                <td align='center'>$no</td>
                <td>{$row['mpd']}</td>
                <td align='center'>$notaris_aktif</td>
                <td align='center'>{$row['jumlah_notaris_kirim']}</td>
                <td align='center'>".number_format($persentase, 2)."%</td>
                <td align='center'>{$row['jml_buku_daftar']}</td>
                <td align='center'>{$row['jml_tangan_dibukukan']}</td>
                <td align='center'>{$row['jml_tangan_disahkan']}</td>
                <td align='center'>{$row['jml_buku_protes']}</td>
                <td align='center'><b>{$row['total_akta']}</b></td>
            </tr>";
            $no++;
        }
        $persentase_total = ($g_total_notaris > 0) ? ($g_total_kirim / $g_total_notaris) * 100 : 0;
        ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #eee; font-weight: bold;">
            <td colspan="2" align="center">TOTAL KESELURUHAN</td>
            <td align="center"><?= $g_total_notaris ?></td>
            <td align="center"><?= $g_total_kirim ?></td>
            <td align="center"><?= number_format($persentase_total, 2) ?>%</td>
            <td colspan="4"></td>
            <td align="center"><?= $g_total_akta ?></td>
        </tr>
    </tfoot>
</table>