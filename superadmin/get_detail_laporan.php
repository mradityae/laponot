<?php

include("../config/koneksi.php");

// =====================================================
// 1. PARAMETER
// =====================================================
$id = isset($_GET['id_kedudukan'])
    ? (int)$_GET['id_kedudukan']
    : 0;

$b1 = isset($_GET['bulan_awal'])
    ? (int)$_GET['bulan_awal']
    : 1;

$b2 = isset($_GET['bulan_akhir'])
    ? (int)$_GET['bulan_akhir']
    : date('m');

$th = isset($_GET['tahun'])
    ? (int)$_GET['tahun']
    : date('Y');


// Validasi
if ($b1 < 1 || $b1 > 12) {
    $b1 = 1;
}

if ($b2 < 1 || $b2 > 12) {
    $b2 = 12;
}

if ($b1 > $b2) {
    $tmp = $b1;
    $b1 = $b2;
    $b2 = $tmp;
}


// =====================================================
// 2. NAMA BULAN
// =====================================================
$nama_bulan = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];


// =====================================================
// 3. NAMA KEDUDUKAN
// =====================================================
$stmt_k = $koneksi->prepare("
    SELECT nama_kedudukan
    FROM kedudukan
    WHERE id_kedudukan = ?
");

$stmt_k->execute([$id]);

$nama_kedudukan = $stmt_k->fetchColumn();


// =====================================================
// 4. BUAT LIST BULAN SESUAI FILTER
// =====================================================
$bulan_filter = [];

for ($m = $b1; $m <= $b2; $m++) {

    $bulan_filter[$m] =
        $nama_bulan[$m] . ' ' . $th;
}


// =====================================================
// 5. AMBIL NOTARIS
// =====================================================
$sql = "
    SELECT
        n.id_notaris,
        n.nama,
        n.telepon

    FROM notaris n

    WHERE n.id_kedudukan = :id
      AND n.level = '2'
      AND n.aktif = '1'

    ORDER BY n.nama ASC
";

$stmt = $koneksi->prepare($sql);

$stmt->execute([
    ':id' => $id
]);

$notaris = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// 6. AMBIL SEMUA LAPORAN DALAM RENTANG
//
// Kita ambil per NOTARIS + BULAN
// =====================================================
$sql_laporan = "
    SELECT
        id_notaris,
        YEAR(tanggal) AS tahun,
        MONTH(tanggal) AS bulan

    FROM laporan

    WHERE YEAR(tanggal) = :tahun
      AND MONTH(tanggal) BETWEEN :b1 AND :b2

    GROUP BY
        id_notaris,
        YEAR(tanggal),
        MONTH(tanggal)
";

$stmt_laporan = $koneksi->prepare($sql_laporan);

$stmt_laporan->execute([
    ':tahun' => $th,
    ':b1'    => $b1,
    ':b2'    => $b2
]);

$laporan_rows = $stmt_laporan->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// 7. INDEX LAPORAN
//
// $laporan_map[id_notaris][bulan] = true
// =====================================================
$laporan_map = [];

foreach ($laporan_rows as $laporan) {

    $notaris_id = (int)$laporan['id_notaris'];
    $bulan      = (int)$laporan['bulan'];

    if (!isset($laporan_map[$notaris_id])) {
        $laporan_map[$notaris_id] = [];
    }

    $laporan_map[$notaris_id][$bulan] = true;
}

?>


<style>

.detail-toolbar {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.detail-filter {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.detail-filter-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-lengkap {
    color: #27ae60;
    font-weight: bold;
}

.status-belum {
    color: #e74c3c;
    font-weight: bold;
}

.bulan-belum {
    display: inline-block;
    margin: 2px;
    padding: 4px 7px;
    border-radius: 4px;
    background: #f2dede;
    color: #a94442;
    font-size: 11px;
}

.bulan-sudah {
    display: inline-block;
    margin: 2px;
    padding: 4px 7px;
    border-radius: 4px;
    background: #dff0d8;
    color: #3c763d;
    font-size: 11px;
}

.btn-edit-profil {
    white-space: nowrap;
}

</style>


<!-- =====================================================
     TOOLBAR
===================================================== -->

<div class="detail-toolbar">

    <div class="detail-filter">

        <!-- FILTER STATUS -->

        <div class="detail-filter-item">

            <label>
                Filter Status:
            </label>

            <select
                id="filterStatus"
                class="form-control input-sm"
                style="width:150px;"
            >

                <option value="">
                    Semua
                </option>

                <option value="SUDAH LENGKAP">
                    Sudah Lapor
                </option>

                <option value="BELUM LENGKAP">
                    Belum Lapor
                </option>

            </select>

        </div>


        <!-- SEARCH -->

        <div class="detail-filter-item">

            <label>
                Cari:
            </label>

            <input
                type="text"
                id="customSearch"
                class="form-control input-sm"
                placeholder="Nama / No HP..."
                style="width:200px;"
            >

        </div>

    </div>


    <!-- EXPORT PDF -->

    <a
        href="export_pdf_detail.php?id_kedudukan=<?= $id ?>&bulan_awal=<?= $b1 ?>&bulan_akhir=<?= $b2 ?>&tahun=<?= $th ?>"
        target="_blank"
        class="btn btn-danger btn-sm"
    >

        <i class="fa fa-file-pdf-o"></i>
        Export PDF

    </a>

</div>


<!-- =====================================================
     INFO PERIODE
===================================================== -->

<div class="alert alert-info">

    <strong>
        <?= htmlspecialchars($nama_kedudukan) ?>
    </strong>

    &nbsp;|&nbsp;

    Periode:

    <strong>
        <?php
        if ($b1 == $b2) {
            echo $nama_bulan[$b1] . ' ' . $th;
        } else {
            echo $nama_bulan[$b1] .
                 ' - ' .
                 $nama_bulan[$b2] .
                 ' ' .
                 $th;
        }
        ?>
    </strong>

    &nbsp;|&nbsp;

    Kewajiban setiap Notaris:

    <strong>
        <?= ($b2 - $b1 + 1) ?> laporan
    </strong>

</div>


<!-- =====================================================
     TABLE
===================================================== -->

<table
    class="table table-striped table-bordered table-hover"
    id="tablePop"
>

    <thead>

        <tr>

            <th style="width:40px;">
                No
            </th>

            <th>
                Nama Notaris
            </th>

            <th>
                No. HP
            </th>

            <th>
                Status
            </th>

            <th>
                Laporan Sudah
            </th>

            <th>
                Belum Dilaporkan
            </th>

            <th>
                Aksi
            </th>

        </tr>

    </thead>

    <tbody>

    <?php

    $no = 1;

    foreach ($notaris as $d):

        $id_notaris = (int)$d['id_notaris'];

        $jumlah_sudah = 0;
        $bulan_sudah  = [];
        $bulan_belum  = [];


        // ==============================================
        // CEK SETIAP BULAN
        // ==============================================

        for ($m = $b1; $m <= $b2; $m++) {

            if (
                isset($laporan_map[$id_notaris][$m]) &&
                $laporan_map[$id_notaris][$m] === true
            ) {

                $jumlah_sudah++;

                $bulan_sudah[] =
                    $nama_bulan[$m];

            } else {

                $bulan_belum[] =
                    $nama_bulan[$m];

            }

        }


        $jumlah_kewajiban =
            ($b2 - $b1 + 1);


        // ==============================================
        // STATUS
        // ==============================================

        if ($jumlah_sudah >= $jumlah_kewajiban) {

            $status_text = 'SUDAH LENGKAP';

            $status_class = 'status-lengkap';

        } else {

            $status_text = 'BELUM LENGKAP';

            $status_class = 'status-belum';

        }


        // ==============================================
        // EDIT PROFIL
        // ==============================================

        $edit_url =
            'https://kabayanpasti.kemenkum.go.id/laponot/superadmin/edit_pengguna_super.php?id=' .
            $id_notaris;

    ?>

    <tr>

        <td>
            <?= $no++ ?>
        </td>


        <!-- NAMA -->

        <td style="text-align:left;">

            <strong>
                <?= htmlspecialchars($d['nama']) ?>
            </strong>

        </td>


        <!-- TELEPON -->

        <td>

            <?= htmlspecialchars(
                $d['telepon'] ?: '-'
            ) ?>

        </td>


        <!-- STATUS -->

        <td
            data-search="<?= $status_text ?>"
            class="<?= $status_class ?>"
        >

            <?php if ($status_text == 'SUDAH LENGKAP'): ?>

                <span class="label label-success">
                    SUDAH LENGKAP
                </span>

            <?php else: ?>

                <span class="label label-danger">
                    BELUM LENGKAP
                </span>

            <?php endif; ?>

        </td>


        <!-- SUDAH -->

        <td style="text-align:left;">

            <?php if (count($bulan_sudah) > 0): ?>

                <?php foreach ($bulan_sudah as $bulan): ?>

                    <span class="bulan-sudah">
                        <?= htmlspecialchars($bulan) ?>
                    </span>

                <?php endforeach; ?>

            <?php else: ?>

                <span class="text-muted">
                    -
                </span>

            <?php endif; ?>

        </td>


        <!-- BELUM -->

        <td style="text-align:left;">

            <?php if (count($bulan_belum) > 0): ?>

                <?php foreach ($bulan_belum as $bulan): ?>

                    <span class="bulan-belum">
                        <?= htmlspecialchars($bulan) ?>
                    </span>

                <?php endforeach; ?>

            <?php else: ?>

                <span class="status-lengkap">
                    Semua laporan sudah masuk
                </span>

            <?php endif; ?>

        </td>


        <!-- AKSI -->

        <td>

            <a
                href="<?= htmlspecialchars($edit_url) ?>"
                target="_blank"
                class="btn btn-primary btn-xs btn-edit-profil"
                title="Edit Profil Notaris"
            >

                <i class="fa fa-pencil"></i>
                Edit Profil

            </a>

        </td>

    </tr>

    <?php endforeach; ?>

    </tbody>

</table>


<script>

(function() {

    // ================================================
    // DATATABLE
    // ================================================

    var table = $('#tablePop').DataTable({

        dom: 'lrtip',

        pageLength: 25,

        order: [
            [1, 'asc']
        ]

    });


    // ================================================
    // FILTER STATUS
    // ================================================

    $('#filterStatus').on('change', function() {

        var value = this.value;

        table
            .column(3)
            .search(value)
            .draw();

    });


    // ================================================
    // SEARCH
    // ================================================

    $('#customSearch').on('keyup', function() {

        table
            .search(this.value)
            .draw();

    });

})();

</script>