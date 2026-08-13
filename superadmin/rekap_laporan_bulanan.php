<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

// =====================================================
// 1. FILTER
// =====================================================
$bulan_awal  = $_GET['bulan_awal'] ?? '01';
$bulan_akhir = $_GET['bulan_akhir'] ?? date('m');
$tahun       = $_GET['tahun'] ?? date('Y');

$bulan_awal  = str_pad((int)$bulan_awal, 2, '0', STR_PAD_LEFT);
$bulan_akhir = str_pad((int)$bulan_akhir, 2, '0', STR_PAD_LEFT);
$tahun       = (int)$tahun;

$nama_bulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

// Validasi bulan
if ((int)$bulan_awal < 1 || (int)$bulan_awal > 12) {
    $bulan_awal = '01';
}

if ((int)$bulan_akhir < 1 || (int)$bulan_akhir > 12) {
    $bulan_akhir = date('m');
}

// Kalau terbalik, tukar
if ((int)$bulan_awal > (int)$bulan_akhir) {
    $tmp = $bulan_awal;
    $bulan_awal = $bulan_akhir;
    $bulan_akhir = $tmp;
}

// =====================================================
// 2. JUMLAH BULAN DALAM RENTANG
// =====================================================
$jumlah_bulan = ((int)$bulan_akhir - (int)$bulan_awal) + 1;

// =====================================================
// 3. QUERY REKAP
//
// KEWAJIBAN = NOTARIS AKTIF x JUMLAH BULAN
//
// CONTOH:
// 100 notaris x Jan-Mar = 300 kewajiban
// =====================================================
$sql = "
    SELECT
        k.id_kedudukan,
        k.nama_kedudukan AS mpd,

        COUNT(DISTINCT n.id_notaris) AS jml_notaris_aktif,

        /*
         * Hitung jumlah kombinasi NOTARIS-BULAN yang sudah lapor.
         * Jadi 1 notaris lapor Januari, Februari, Maret = 3.
         */
        COUNT(
            DISTINCT CASE
                WHEN la.tanggal IS NOT NULL
                THEN CONCAT(
                    n.id_notaris,
                    '-',
                    YEAR(la.tanggal),
                    '-',
                    LPAD(MONTH(la.tanggal), 2, '0')
                )
            END
        ) AS jumlah_laporan_masuk,

        /*
         * Jumlah notaris yang minimal pernah lapor
         */
        COUNT(DISTINCT CASE
            WHEN la.tanggal IS NOT NULL
            THEN n.id_notaris
        END) AS jumlah_notaris_pernah_lapor,

        COALESCE(SUM(la.jml_buku_daftar), 0) AS jml_buku_daftar,
        COALESCE(SUM(la.jml_tangan_dibukukan), 0) AS jml_tangan_dibukukan,
        COALESCE(SUM(la.jml_tangan_disahkan), 0) AS jml_tangan_disahkan,
        COALESCE(SUM(la.jml_buku_protes), 0) AS jml_buku_protes,

        COALESCE(
            SUM(
                COALESCE(la.jml_buku_daftar, 0) +
                COALESCE(la.jml_tangan_dibukukan, 0) +
                COALESCE(la.jml_tangan_disahkan, 0) +
                COALESCE(la.jml_buku_protes, 0)
            ),
            0
        ) AS total_akta

    FROM kedudukan k

    LEFT JOIN notaris n
        ON n.id_kedudukan = k.id_kedudukan
        AND n.level = '2'
        AND n.aktif = '1'

    LEFT JOIN laporan la
        ON la.id_notaris = n.id_notaris
        AND YEAR(la.tanggal) = :tahun
        AND MONTH(la.tanggal) BETWEEN :bulan_awal AND :bulan_akhir

    GROUP BY
        k.id_kedudukan,
        k.nama_kedudukan

    ORDER BY
        k.nama_kedudukan ASC
";

$stmt = $koneksi->prepare($sql);

$stmt->execute([
    ':tahun'      => $tahun,
    ':bulan_awal' => (int)$bulan_awal,
    ':bulan_akhir'=> (int)$bulan_akhir
]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =====================================================
// 4. SUMMARY GLOBAL
// =====================================================
$g_total_notaris       = 0;
$g_total_kewajiban     = 0;
$g_total_laporan_masuk = 0;
$g_total_akta          = 0;

foreach ($results as $res) {

    $notaris = (int)$res['jml_notaris_aktif'];
    $laporan  = (int)$res['jumlah_laporan_masuk'];

    $g_total_notaris       += $notaris;
    $g_total_kewajiban     += ($notaris * $jumlah_bulan);
    $g_total_laporan_masuk += $laporan;
    $g_total_akta          += (int)$res['total_akta'];
}

$g_belum_lapor = max(
    0,
    $g_total_kewajiban - $g_total_laporan_masuk
);

$g_persentase = ($g_total_kewajiban > 0)
    ? ($g_total_laporan_masuk / $g_total_kewajiban) * 100
    : 0;

// Nama periode
if ($bulan_awal == $bulan_akhir) {
    $periode_text = $nama_bulan[$bulan_awal] . ' ' . $tahun;
} else {
    $periode_text =
        $nama_bulan[$bulan_awal] .
        ' - ' .
        $nama_bulan[$bulan_akhir] .
        ' ' .
        $tahun;
}
?>

<link rel="stylesheet"
      type="text/css"
      href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<style>
    .page-head-line {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 25px;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 10px;
    }

    .filter-form {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        border: 1px solid #eee;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .summary-card h4 {
        margin: 0;
        color: #7f8c8d;
        font-size: 14px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .summary-card p {
        margin: 5px 0 0;
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
    }

    .border-blue {
        border-left: 5px solid #3498db;
    }

    .border-green {
        border-left: 5px solid #27ae60;
    }

    .border-red {
        border-left: 5px solid #e74c3c;
    }

    .border-orange {
        border-left: 5px solid #f39c12;
    }

    .table th {
        background: #2c3e50 !important;
        color: #fff;
        text-align: center;
        font-weight: 600;
        vertical-align: middle !important;
        font-size: 11px;
    }

    .table td {
        vertical-align: middle !important;
        text-align: center;
        font-size: 13px;
    }

    .btn-detail {
        color: #3498db;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-detail:hover {
        text-decoration: underline;
    }

    .periode-info {
        margin-bottom: 15px;
        font-size: 14px;
        color: #555;
    }
</style>

<div id="page-wrapper">
    <div id="page-inner">

        <h1 class="page-head-line">
            📊 Rekap Kepatuhan Laporan Bulanan
        </h1>

        <div class="periode-info">
            <strong>Periode:</strong>
            <?= htmlspecialchars($periode_text) ?>
            &nbsp; | &nbsp;
            <strong><?= $jumlah_bulan ?></strong> bulan
        </div>

        <!-- FILTER -->
        <form method="GET" class="form-inline filter-form">

            <label>Periode:</label>

            <select name="bulan_awal" class="form-control input-sm">
                <?php foreach ($nama_bulan as $m => $nm): ?>
                    <option value="<?= $m ?>"
                        <?= ($bulan_awal == $m ? 'selected' : '') ?>>
                        <?= $nm ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="bulan_akhir" class="form-control input-sm">
                <?php foreach ($nama_bulan as $m => $nm): ?>
                    <option value="<?= $m ?>"
                        <?= ($bulan_akhir == $m ? 'selected' : '') ?>>
                        <?= $nm ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input
                type="number"
                name="tahun"
                value="<?= htmlspecialchars($tahun) ?>"
                class="form-control input-sm"
                style="width:80px;"
            >

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-search"></i>
                Tampilkan
            </button>

            <a
                href="export_rekap_bulanan.php?bulan_awal=<?= $bulan_awal ?>&bulan_akhir=<?= $bulan_akhir ?>&tahun=<?= $tahun ?>"
                class="btn btn-success btn-sm"
            >
                <i class="fa fa-file-excel-o"></i>
                Export Rekap
            </a>

            <a
                href="export_detail_excel_all.php?bulan_awal=<?= $bulan_awal ?>&bulan_akhir=<?= $bulan_akhir ?>&tahun=<?= $tahun ?>"
                class="btn btn-warning btn-sm"
            >
                <i class="fa fa-file-excel-o"></i>
                Export Status Belum Lapor
            </a>

            <a
                href="export_detail_excel_sudah_lapor.php?bulan_awal=<?= $bulan_awal ?>&bulan_akhir=<?= $bulan_akhir ?>&tahun=<?= $tahun ?>"
                class="btn btn-success btn-sm"
            >
                <i class="fa fa-file-excel-o"></i>
                Export Status Sudah Lapor
            </a>

        </form>

        <!-- SUMMARY -->
        <div class="row" style="margin-bottom:25px;">

            <div class="col-md-3">
                <div class="summary-card border-blue">
                    <h4>Total Notaris Aktif</h4>
                    <p>
                        <?= number_format($g_total_notaris) ?>
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card border-green">
                    <h4>Total Laporan Masuk</h4>
                    <p>
                        <?= number_format($g_total_laporan_masuk) ?>
                        /
                        <?= number_format($g_total_kewajiban) ?>
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card border-red">
                    <h4>Total Laporan Belum Masuk</h4>
                    <p>
                        <?= number_format($g_belum_lapor) ?>
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card border-orange">
                    <h4>% Kepatuhan</h4>
                    <p>
                        <?= number_format($g_persentase, 2) ?>%
                    </p>
                </div>
            </div>

        </div>

        <!-- TABLE -->
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="table-responsive">

                    <table id="tableRekap"
                           class="table table-bordered table-hover">

                        <thead>

                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">MPD (Kedudukan)</th>
                                <th rowspan="2">Notaris Aktif</th>
                                <th rowspan="2">
                                    Laporan Masuk
                                </th>
                                <th rowspan="2">
                                    Kewajiban
                                </th>
                                <th rowspan="2">
                                    Belum
                                </th>
                                <th rowspan="2">
                                    Kepatuhan (%)
                                </th>

                                <th colspan="4">
                                    Rincian Akta
                                </th>

                                <th rowspan="2">
                                    Total Akta
                                </th>
                            </tr>

                            <tr>
                                <th>Daftar</th>
                                <th>Waarmerk</th>
                                <th>Legalisasi</th>
                                <th>Protes</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php
                        $no = 1;

                        foreach ($results as $row):

                            $notaris_aktif =
                                (int)$row['jml_notaris_aktif'];

                            $laporan_masuk =
                                (int)$row['jumlah_laporan_masuk'];

                            $kewajiban =
                                $notaris_aktif * $jumlah_bulan;

                            $belum =
                                max(0, $kewajiban - $laporan_masuk);

                            $persen =
                                ($kewajiban > 0)
                                ? ($laporan_masuk / $kewajiban) * 100
                                : 0;

                            if ($persen >= 100) {
                                $color = 'text-success';
                            } elseif ($persen < 50) {
                                $color = 'text-danger';
                            } else {
                                $color = 'text-warning';
                            }
                        ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td style="text-align:left">

                                <span
                                    class="btn-detail"
                                    onclick="showDetail(
                                        '<?= htmlspecialchars($row['id_kedudukan'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($row['mpd'], ENT_QUOTES) ?>'
                                    )"
                                >
                                    <?= htmlspecialchars($row['mpd']) ?>
                                </span>

                            </td>

                            <td>
                                <b>
                                    <?= number_format($notaris_aktif) ?>
                                </b>
                            </td>

                            <td>
                                <?= number_format($laporan_masuk) ?>
                            </td>

                            <td>
                                <?= number_format($kewajiban) ?>
                            </td>

                            <td class="text-danger">
                                <b>
                                    <?= number_format($belum) ?>
                                </b>
                            </td>

                            <td class="<?= $color ?>">
                                <b>
                                    <?= number_format($persen, 2) ?>%
                                </b>
                            </td>

                            <td>
                                <?= number_format($row['jml_buku_daftar']) ?>
                            </td>

                            <td>
                                <?= number_format($row['jml_tangan_dibukukan']) ?>
                            </td>

                            <td>
                                <?= number_format($row['jml_tangan_disahkan']) ?>
                            </td>

                            <td>
                                <?= number_format($row['jml_buku_protes']) ?>
                            </td>

                            <td class="info">
                                <b>
                                    <?= number_format($row['total_akta']) ?>
                                </b>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </div>
</div>


<!-- MODAL DETAIL -->

<div class="modal fade"
     id="modalDetail"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-lg"
         role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">
                    &times;
                </button>

                <h4 id="modalTitle"></h4>

            </div>

            <div
                class="modal-body"
                id="detailContent"
            ></div>

        </div>

    </div>

</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script
    type="text/javascript"
    src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js">
</script>


<script>

$(document).ready(function() {

    $('#tableRekap').DataTable({
        pageLength: 50,
        order: [[1, 'asc']]
    });

});


function showDetail(id, nama) {

    $('#modalTitle').html(
        'Detail Notaris - ' + nama
    );

    $('#modalDetail').modal('show');

    $('#detailContent').html(
        '<center>' +
        '<i class="fa fa-spinner fa-spin"></i> ' +
        'Sedang memuat...' +
        '</center>'
    );

    $.get(
        'get_detail_laporan.php',
        {
            id_kedudukan: id,
            bulan_awal: '<?= $bulan_awal ?>',
            bulan_akhir: '<?= $bulan_akhir ?>',
            tahun: '<?= $tahun ?>'
        },
        function(data) {

            $('#detailContent').html(data);

        }
    );

}

</script>

<?php include "footer.php"; ?>