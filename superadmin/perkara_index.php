<?php
include "header.php";
include "../config/koneksi.php";

// ==========================================
// 1. FUNGSI PEMBANTU
// ==========================================

function formatTanggalIndo($datetime) {

    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }

    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $timestamp = strtotime($datetime);

    return date('j', $timestamp) . ' ' .
           $bulan[(int)date('n', $timestamp)] . ' ' .
           date('Y', $timestamp) . ' ' .
           date('H:i', $timestamp);
}

function getHariLiburNasional() {

    static $list_libur = null;

    if ($list_libur !== null) {
        return $list_libur;
    }

    $list_libur = [];

    $tahun_sekarang = date('Y');

    $api_url = "https://dayoffapi.vercel.app/api?year=" . $tahun_sekarang;

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 2
        ]
    ]);

    $response = @file_get_contents($api_url, false, $ctx);

    if ($response) {

        $data = json_decode($response, true);

        if (is_array($data)) {

            foreach ($data as $row) {

                if (isset($row['date'])) {
                    $list_libur[] = $row['date'];
                }

            }

        }

    }

    return $list_libur;
}

function hitungSisaHariKerja($tgl_input, $daftar_libur = []) {

    $target_days = 30;

    $start_date = strtotime(date('Y-m-d', strtotime($tgl_input)));
    $current_date = strtotime(date('Y-m-d'));

    if (empty($daftar_libur)) {
        $daftar_libur = getHariLiburNasional();
    }

    $days_elapsed = 0;
    $tmp_date = $start_date;

    while ($tmp_date <= $current_date) {

        $w = date('N', $tmp_date);
        $tanggal_format = date('Y-m-d', $tmp_date);

        if ($w < 6 && !in_array($tanggal_format, $daftar_libur)) {
            $days_elapsed++;
        }

        $tmp_date = strtotime("+1 day", $tmp_date);
    }

    return $target_days - $days_elapsed;
}

// ==========================================
// 2. FILTER
// ==========================================

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

$sql = "SELECT 
            p.*,
            n.nama AS nama_notaris,
            k.nama_kedudukan
        FROM perkara_mpw p
        LEFT JOIN notaris n 
            ON p.id_notaris = n.id_notaris
        LEFT JOIN kedudukan k 
            ON p.id_kedudukan = k.id_kedudukan
        WHERE 1=1";

if ($filter == 'register') {

    $sql .= " AND status = 'Register Perkara'";

} elseif ($filter == 'pemeriksaan') {

    $sql .= " AND status = 'Perkara Pemeriksaan'";

} elseif ($filter == 'Pending') {

    $sql .= " AND status = 'Pending'";

} elseif ($filter == 'Terverifikasi') {

    $sql .= " AND verifikasi = 'Terverifikasi'";

} elseif ($filter == 'Tidak_Terverifikasi') {

    $sql .= " AND verifikasi = 'Tidak Terverifikasi'";
}

$sql .= " ORDER BY p.id_perkara DESC";

$query = $koneksi->prepare($sql);
$query->execute();

$daftar_libur = getHariLiburNasional();

?>

<style>

.page-bg{
    background:#f8fafc;
}

.inner-wrapper{
    padding:25px 15px;
}

.panel-header-custom{
    border:none;
    border-radius:12px;
    box-shadow:0 4px 6px rgba(0,0,0,.05);
    margin-bottom:25px;
    background:#fff;
}

.panel-body-header{
    padding:24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
}

.header-title{
    margin:0;
    font-weight:700;
    color:#1e293b;
    font-size:26px;
    display:flex;
    align-items:center;
    gap:12px;
}

.header-icon{
    color:#3b82f6;
    background:#eff6ff;
    padding:10px;
    border-radius:10px;
}

.header-subtitle{
    color:#64748b;
    margin-top:6px;
    font-size:14px;
}

.filter-container{
    margin-bottom:20px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.btn-filter{
    border-radius:20px;
    font-weight:600;
    padding:6px 18px;
}

.panel-table-container{
    border:none;
    border-radius:12px;
    box-shadow:0 10px 15px rgba(0,0,0,.05);
    background:#fff;
    overflow:hidden;
    position:relative;
}

.table-loading-overlay{
    position:absolute;
    top:0;
    left:0;
    right:0;
    bottom:0;
    background:rgba(255,255,255,.85);
    z-index:100;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:12px;
}

.spinner-custom{
    width:40px;
    height:40px;
    border:4px solid #e2e8f0;
    border-top:4px solid #2563eb;
    border-radius:50%;
    animation:spin .8s linear infinite;
}

@keyframes spin{
    100%{
        transform:rotate(360deg);
    }
}

.loading-text{
    font-weight:600;
    color:#475569;
    font-size:14px;
}

.table-custom > thead > tr > th{
    background:#f8fafc;
    color:#475569;
    font-weight:600;
    font-size:11px;
    padding:14px 15px !important;
    border-bottom:2px solid #e2e8f0 !important;
    text-transform:uppercase;
}

.table-custom > tbody > tr > td{
    vertical-align:middle !important;
    padding:16px 15px !important;
    font-size:13px;
    color:#334155;
    border-bottom:1px solid #f1f5f9 !important;
}

.flex-center-column{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:4px;
}

.badge-progress-register{
    font-size:11px;
    padding:5px 10px;
    border-radius:6px;
    font-weight:600;
    background:#f1f5f9;
    color:#475569;
    border:1px solid #cbd5e1;
}

.badge-progress-pemeriksaan{
    font-size:11px;
    padding:5px 10px;
    border-radius:6px;
    font-weight:600;
    background:#e0f2fe;
    color:#0369a1;
    border:1px solid #bae6fd;
}

</style>

<div id="page-wrapper" class="page-bg">

    <div id="page-inner" class="inner-wrapper">

        <div class="row">

            <div class="col-md-12">

                <div class="panel panel-default panel-header-custom">

                    <div class="panel-body panel-body-header">

                        <div>

                            <h2 class="header-title">
                                <i class="fa fa-gavel header-icon"></i>
                                Register Perkara
                            </h2>

                            <p class="header-subtitle">
                                Manajemen daftar perkara hukum Majelis Pengawas
                            </p>

                        </div>

                    </div>

                </div>

                <div class="filter-container">

                    <a href="perkara_index.php?filter=semua"
                       class="btn btn-filter <?= ($filter == 'semua') ? 'btn-primary' : 'btn-default'; ?>">
                        Semua Data
                    </a>

                    <a href="perkara_index.php?filter=Pending"
                       class="btn btn-filter <?= ($filter == 'Pending') ? 'btn-primary' : 'btn-default'; ?>">
                        Pending
                    </a>

                    <a href="perkara_index.php?filter=register"
                       class="btn btn-filter <?= ($filter == 'register') ? 'btn-primary' : 'btn-default'; ?>">
                        Register Perkara
                    </a>

                    <a href="perkara_index.php?filter=pemeriksaan"
                       class="btn btn-filter <?= ($filter == 'pemeriksaan') ? 'btn-primary' : 'btn-default'; ?>">
                        Pemeriksaan
                    </a>

                    <a href="perkara_index.php?filter=Terverifikasi"
                       class="btn btn-filter <?= ($filter == 'Terverifikasi') ? 'btn-primary' : 'btn-default'; ?>">
                        Terverifikasi
                    </a>

                    <a href="perkara_index.php?filter=Tidak_Terverifikasi"
                       class="btn btn-filter <?= ($filter == 'Tidak_Terverifikasi') ? 'btn-primary' : 'btn-default'; ?>">
                        Tidak Terverifikasi
                    </a>

                </div>

                <div class="panel panel-default panel-table-container">

                    <div id="loadingOverlay" class="table-loading-overlay">
                        <div class="spinner-custom"></div>
                        <div class="loading-text">Memproses data perkara...</div>
                    </div>

                    <div class="panel-body" style="padding:0;">

                        <div class="table-responsive">

                            <table id="tablePerkara"
                                   class="table table-hover table-custom"
                                   style="width:100%; margin-bottom:0;">

                                <thead>

                                    <tr>
                                        <th style="width:40px; text-align:center;">No</th>
                                        <th style="width:140px;">No Register</th>
                                        <th>Judul Perkara</th>
                                        <th style="width:170px;">Tanggal Input</th>
                                        <th style="width:220px;">Terlapor</th>
                                        <th style="width:220px;">Pelapor</th>
                                        <th style="width:170px; text-align:center;">Tahapan</th>
                                        <th style="width:220px; text-align:center;">Aksi</th>
                                        <th style="width:180px; text-align:center;">Status Verifikasi</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                while($data = $query->fetch(PDO::FETCH_ASSOC)):

                                    $status = isset($data['status']) ? $data['status'] : 'Pending';
                                ?>

                                    <tr>

                                        <td style="text-align:center; font-weight:600;">
                                            <?= $no++; ?>
                                        </td>

                                        <td style="font-weight:600; color:#1e293b;">

                                            <?php if(!empty($data['nomor_register'])): ?>

                                                <?= htmlspecialchars($data['nomor_register']); ?>

                                            <?php else: ?>

                                                <center>-</center>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <?= !empty($data['judul'])
                                                ? htmlspecialchars($data['judul'])
                                                : '<span class="text-muted"><i>Tidak ada judul</i></span>'; ?>

                                        </td>

                                        <td>
                                            <?= formatTanggalIndo($data['created_at']); ?>
                                        </td>

                                        <td>

                                            <div style="font-weight:600;">

                                                <?=
                                                ($data['jenis_terlapor'] == 'database')
                                                ? htmlspecialchars($data['nama_notaris'])
                                                : htmlspecialchars($data['nama_terlapor_manual']);
                                                ?>

                                            </div>

                                            <?php if(!empty($data['nama_kedudukan'])): ?>

                                                <div style="font-size:11px; color:#64748b;">
                                                    <i class="fa fa-map-marker"></i>
                                                    <?= htmlspecialchars($data['nama_kedudukan']); ?>
                                                </div>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <div style="font-weight:600;">
                                                <i class="fa fa-user"></i>
                                                <?= htmlspecialchars($data['nama_pelapor']); ?>
                                            </div>

                                        </td>

                                        <td style="text-align:center;">

                                            <div class="flex-center-column">

                                                <?php if($status == 'Pending'): ?>

                                                    <span class="badge badge-secondary">
                                                        Pending
                                                    </span>

                                                <?php elseif($status == 'Register Perkara'): ?>

                                                    <span class="badge-progress-register">
                                                        Register Perkara
                                                    </span>

                                                    <?php
                                                    $sisa_hari = hitungSisaHariKerja(
                                                        $data['created_at'],
                                                        $daftar_libur
                                                    );
                                                    ?>

                                                    <?php if($sisa_hari > 5): ?>

                                                        <small class="text-success">
                                                            Sisa <?= $sisa_hari; ?> Hari Kerja
                                                        </small>

                                                    <?php elseif($sisa_hari >= 0): ?>

                                                        <small class="text-warning">
                                                            Sisa <?= $sisa_hari; ?> Hari
                                                        </small>

                                                    <?php else: ?>

                                                        <small class="text-danger">
                                                            Lewat <?= abs($sisa_hari); ?> Hari
                                                        </small>

                                                    <?php endif; ?>

                                                <?php elseif($status == 'Perkara Pemeriksaan'): ?>

                                                    <span class="badge-progress-pemeriksaan">
                                                        Perkara Pemeriksaan
                                                    </span>

                                                <?php elseif($status == 'Selesai'): ?>

                                                    <span class="badge-progress-pemeriksaan">
                                                        Selesai
                                                    </span>

                                                <?php endif; ?>

                                            </div>

                                        </td>

                                        <td style="text-align:center;">

                                            <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">

                                                <a href="perkara_detail.php?id=<?= $data['id_perkara']; ?>"
                                                   class="btn btn-info btn-sm">

                                                    <i class="fa fa-eye"></i>
                                                    Detail

                                                </a>

                                                <?php if(isset($data['status']) && !in_array($data['status'], ['Pending', 'Register Perkara'])): ?>

                                                    <a href="perkara_verifikasi.php?id=<?= $data['id_perkara']; ?>"
                                                       target="_blank"
                                                       class="btn btn-primary btn-sm">

                                                        <i class="fa fa-check-square-o"></i>
                                                        Verifikasi

                                                    </a>

                                                <?php endif; ?>

                                                
                                                <!-- Tombol Hapus -->
                                                <a href="<?= $url; ?>act/hapus_perkara.php?id=<?= $data['id_perkara']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    style="font-weight: 600; width:100%;"
                                                    onclick="return confirm('Yakin ingin menghapus perkara ini? Seluruh data dan file akan dihapus permanen.')">
                                                        <i class="fa fa-trash"></i> Hapus
                                                </a>

                                            </div>

                                        </td>

                                        <td style="text-align:center;">

                                            <?php if($status == 'Perkara Pemeriksaan' || $status == 'Selesai'): ?>

                                                <?php if(isset($data['verifikasi']) && $data['verifikasi'] == 'Terverifikasi'): ?>

                                                    <span class="badge badge-success" style="padding:8px 12px;">
                                                        <i class="fa fa-check-circle"></i>
                                                        Terverifikasi
                                                    </span>

                                                <?php elseif(isset($data['verifikasi']) && $data['verifikasi'] == 'Tidak Terverifikasi'): ?>

                                                    <span class="badge badge-danger" style="padding:8px 12px;">
                                                        <i class="fa fa-times-circle"></i>
                                                        Tidak Terverifikasi
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge badge-warning" style="padding:8px 12px;">
                                                        Belum Verifikasi
                                                    </span>

                                                <?php endif; ?>

                                            <?php else: ?>

                                                <span style="color:#94a3b8;">-</span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "footer.php"; ?>

<script>

$(document).ready(function(){

    $('#tablePerkara').DataTable({
        "processing": true,
        "pageLength": 10,
        "ordering": true,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Awal",
                "last": "Akhir",
                "next": "›",
                "previous": "‹"
            }
        },
        "initComplete": function() {
            $('#loadingOverlay').fadeOut(200);
        }
    });

});

</script>