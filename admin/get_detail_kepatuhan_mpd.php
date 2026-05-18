<?php
include("../config/koneksi.php");

session_save_path('../login/session');
session_start();

if (!isset($_SESSION['kedudukan'])) {
    echo "<div class='alert alert-danger'>Session login habis.</div>";
    exit;
}

$kedudukan   = $_SESSION['kedudukan'];
$status      = $_GET['status'] ?? '';
$bulan_berjalan  = $_GET['bulan_berjalan'] ?? date('m');
$tahun       = $_GET['tahun'] ?? date('Y');

if ($status == 'sudah') {

    $sql = "
        SELECT DISTINCT
            n.id_notaris,
            n.nama,
            n.email,
            n.telepon
        FROM laporan la
        INNER JOIN notaris n
            ON la.id_notaris = n.id_notaris
        WHERE n.id_kedudukan = ?
        AND n.level='2'
        AND n.aktif='1'
        AND YEAR(la.tanggal)=?
        AND MONTH(la.tanggal)=?
        ORDER BY n.nama ASC
    ";

} else {

    $sql = "
        SELECT
            n.id_notaris,
            n.nama,
            n.email,
            n.telepon
        FROM notaris n
        WHERE n.id_kedudukan = ?
        AND n.level='2'
        AND n.aktif='1'
        AND n.id_notaris NOT IN (
            SELECT DISTINCT la.id_notaris
            FROM laporan la
            WHERE YEAR(la.tanggal)=?
            AND MONTH(la.tanggal)=?
        )
        ORDER BY n.nama ASC
    ";
}

$stmt = $koneksi->prepare($sql);

$stmt->execute([
    $kedudukan,
    $tahun,
    $bulan_berjalan
]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($data);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">

<style>

.wrapper-kepatuhan{
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.content-kepatuhan{
    padding:20px;
}

.header-action{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
    flex-wrap:wrap;
    gap:10px;
}

.total-data{
    font-size:18px;
    font-weight:700;
    color:#0B1D51;
}

.btn-export-pdf{
    background:#dc3545;
    color:#fff!important;
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none!important;
    font-size:14px;
    font-weight:600;
    transition:.2s;
    box-shadow:0 2px 8px rgba(0,0,0,.1);
}

.btn-export-pdf:hover{
    background:#c82333;
    color:#fff!important;
}

.table-kepatuhan{
    width:100%!important;
    border-radius:12px;
}

.table-kepatuhan thead th{
    background:#0B1D51!important;
    color:#fff;
    text-align:center;
    font-size:15px;
    border:none!important;
    padding:16px 12px!important;
    white-space:nowrap;
}

.table-kepatuhan tbody td{
    vertical-align:middle!important;
    font-size:14px;
    padding:14px 12px!important;
    white-space:nowrap;
}

.table-kepatuhan tbody tr:hover{
    background:#f4f8ff!important;
    transition:.2s;
}

.nama-notaris{
    font-size:15px;
    font-weight:600;
    color:#2c3e50;
}

.email-text{
    color:#3498db;
    font-size:13px;
}

.hp-text{
    font-size:14px;
    font-weight:600;
    color:#555;
}

.badge-status{
    padding:8px 15px;
    border-radius:30px;
    color:#fff;
    font-size:12px;
    font-weight:bold;
    display:inline-block;
}

.badge-sudah{
    background:#27ae60;
}

.badge-belum{
    background:#e74c3c;
}

.dataTables_wrapper .dataTables_filter input{
    border:1px solid #ddd;
    border-radius:8px;
    padding:7px 10px;
    margin-left:8px;
    font-size:14px;
}

.dataTables_wrapper .dataTables_length select{
    border:1px solid #ddd;
    border-radius:8px;
    padding:5px;
    font-size:14px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button{
    border-radius:8px!important;
    margin:0 2px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current{
    background:#0B1D51!important;
    color:#fff!important;
    border:none!important;
}

.dataTables_wrapper .dataTables_info{
    font-size:14px;
}

.empty-data{
    padding:40px;
    text-align:center;
    color:#999;
}

.dataTables_scrollBody{
    border-bottom:1px solid #dee2e6!important;
}

.dataTables_scrollHeadInner,
.dataTables_scrollHeadInner table{
    width:100%!important;
}

.dataTables_scrollBody::-webkit-scrollbar{
    height:10px;
    width:10px;
}

.dataTables_scrollBody::-webkit-scrollbar-thumb{
    background:#b5b5b5;
    border-radius:10px;
}

.dataTables_scrollBody::-webkit-scrollbar-track{
    background:#f1f1f1;
}

table.dataTable{
    width:100%!important;
}

</style>

<div class="wrapper-kepatuhan">

    <div class="content-kepatuhan">

        <div class="header-action">

            <div class="total-data">
                Total Data : <?= number_format($total) ?>
            </div>

            <a href="export_pdf_kepatuhan.php?status=<?= $status ?>&bulan_berjalan=<?= $bulan_berjalan ?>&tahun=<?= $tahun ?>"
               target="_blank"
               class="btn-export-pdf">

                <i class="fa fa-file-pdf-o"></i>
                Export PDF

            </a>

        </div>

        <div class="table-responsive">

            <table id="tableKepatuhan" class="table table-bordered table-hover table-kepatuhan">

                <thead>

                    <tr>

                        <th width="5%">No</th>

                        <th>Nama Notaris</th>

                        <th>Email</th>

                        <th>No HP</th>

                        <th width="15%">Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if($total > 0): ?>

                        <?php $no = 1; ?>

                        <?php foreach($data as $d): ?>

                            <tr>

                                <td align="center">
                                    <b><?= $no++ ?></b>
                                </td>

                                <td>

                                    <div class="nama-notaris">
                                        <?= htmlspecialchars($d['nama']) ?>
                                    </div>

                                </td>

                                <td>

                                    <div class="email-text">
                                        <?= htmlspecialchars($d['email']) ?>
                                    </div>

                                </td>

                                <td>

                                    <div class="hp-text">
                                        <?= htmlspecialchars($d['telepon']) ?>
                                    </div>

                                </td>

                                <td align="center">

                                    <?php if($status == 'sudah'): ?>

                                        <span class="badge-status badge-sudah">
                                            SUDAH LAPOR
                                        </span>

                                    <?php else: ?>

                                        <span class="badge-status badge-belum">
                                            BELUM LAPOR
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="5">

                                <div class="empty-data">

                                    <i class="fa fa-folder-open fa-3x"></i>

                                    <h4>Tidak Ada Data</h4>

                                    <p>Belum terdapat data notaris pada periode ini.</p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<script>

$(document).ready(function(){

    $('#tableKepatuhan').DataTable({

        pageLength: 10,

        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,"Semua"]
        ],

        scrollX: true,
        scrollY: "500px",
        scrollCollapse: true,
        fixedHeader: true,

        language: {

            search: "Cari Notaris :",

            lengthMenu: "Tampilkan _MENU_ data",

            zeroRecords: "Data tidak ditemukan",

            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data",

            paginate: {

                first: "Awal",

                last: "Akhir",

                next: "›",

                previous: "‹"

            }

        }

    });

});

</script>