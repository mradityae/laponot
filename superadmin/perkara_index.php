<?php
include "header.php";
include "../config/koneksi.php";

// ==========================================
// 1. FUNGSI PEMBANTU
// ==========================================

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

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

    if ($timestamp === false) {
        return '-';
    }

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

    $target_days = 31;

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
$filter_map = [
    'semua' => null,
    'pending' => [
        'condition' => 'LOWER(p.status) = ?',
        'value' => 'pending'
    ],
    'proses' => [
        'condition' => 'p.status = ?',
        'value' => 'Proses Pemeriksaan MPD'
    ],
    'selesai_mpd' => [
        'condition' => 'p.status = ?',
        'value' => 'Selesai di MPD'
    ],
    'diteruskan_mpw' => [
        'condition' => 'p.status = ?',
        'value' => 'Diteruskan ke MPW'
    ]
];

if ($filter == 'Pending') {
    $filter = 'pending';
}

if (!array_key_exists($filter, $filter_map)) {
    $filter = 'semua';
}

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

$params = [];
$filter_condition = $filter_map[$filter];

if ($filter_condition !== null) {
    $sql .= " AND {$filter_condition['condition']}";
    $params[] = $filter_condition['value'];
}

$sql .= " ORDER BY p.id_perkara DESC";

$query = $koneksi->prepare($sql);
$query->execute($params);

$daftar_libur = getHariLiburNasional();

?>

<style>
    .page-bg { background-color: #f8fafc; }
    .inner-wrapper { padding: 25px 15px; }
    .panel-header-custom { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 25px; background: #fff; }
    .panel-body-header { padding: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
    .header-title { margin: 0; font-weight: 700; color: #1e293b; font-size: 26px; letter-spacing: -0.5px; display: flex; align-items: center; gap: 12px; }
    .header-icon { color: #3b82f6; background: #eff6ff; padding: 10px; border-radius: 10px; font-size: 20px; }
    .header-subtitle { color: #64748b; margin: 6px 0 0 0; font-size: 14px; }
    .filter-container { margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-filter { border-radius: 20px; font-weight: 600; padding: 6px 18px; }
    .panel-table-container { border: none; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); background: #fff; overflow: hidden; position: relative; min-height: 200px; }

    .table-loading-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(255, 255, 255, 0.85);
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 12px;
        transition: opacity 0.3s ease-in-out;
    }
    .spinner-custom {
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid #2563eb;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .loading-text { font-weight: 600; color: #475569; font-size: 14px; }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .table-custom > tbody > tr > td { vertical-align: middle !important; padding: 16px 15px !important; color: #334155; border-bottom: 1px solid #f1f5f9 !important; font-size: 13px; }
    .table-custom > thead > tr > th { background-color: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.75px; padding: 14px 15px !important; border-bottom: 2px solid #e2e8f0 !important; border-top: none !important; }
    .doc-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; margin: 2px 2px 2px 0; background-color: #f1f5f9; color: #475569; border-radius: 4px; font-size: 11px; text-decoration: none !important; border: 1px solid #e2e8f0; font-weight: 500; }
    .doc-badge:hover { background-color: #fee2e2; color: #dc2626; border-color: #fca5a5; }
    .btn-link-action { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-weight: 600; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none !important; font-size: 11px; }
    .badge-progress-register { font-size: 11px; padding: 5px 10px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .badge-progress-pemeriksaan { font-size: 11px; padding: 5px 10px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .phone-container { font-size: 12px; color: #475569; background: #f8fafc; padding: 3px 6px; border-radius: 4px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px; }
    .flex-center-column { display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .btn-dukung-tambahan { width: 100%; max-width: 160px; display: flex; align-items: center; justify-content: center; gap: 5px; white-space: normal; text-align: center; line-height: 1.4; padding: 6px 10px; }
    .action-stack { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
    .action-stack .btn { font-weight: 600; }

    .badge-progress-proses { background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; }
    .badge-progress-selesai { background:#DCFCE7; color:#166534; border:1px solid #BBF7D0; }
    .badge-progress-mpw { background:#DBEAFE; color:#1D4ED8; border:1px solid #BFDBFE; }
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

                    <?php
                    $filter_labels = [
                        'semua' => 'Semua Data',
                        'pending' => 'Pending',
                        'proses' => 'Proses MPD',
                        'selesai_mpd' => 'Selesai MPD',
                        'diteruskan_mpw' => 'Diteruskan MPW'
                    ];
                    ?>
                    <form method="GET" action="perkara_index.php" style="display:inline-flex; align-items:center; gap:8px;">
                        <label for="filterPerkara" style="margin:0; font-weight:600; color:#475569;">Filter</label>
                        <select id="filterPerkara"
                                name="filter"
                                class="form-control"
                                style="width:220px; border-radius:8px; font-weight:600;"
                                onchange="this.form.submit()">
                            <?php foreach ($filter_labels as $filter_key => $filter_label): ?>
                                <option value="<?= e($filter_key); ?>" <?= ($filter == $filter_key) ? 'selected' : ''; ?>>
                                    <?= e($filter_label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>

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
                                        <th style="width:130px;">No. Register</th>
                                        <th style="min-width:150px;">Judul Perkara</th>
                                        <th style="width:120px;">Tgl. Input</th>
                                        <th style="min-width:140px;">Pihak Terlapor</th>
                                        <th style="min-width:140px;">Pihak Pelapor</th>
                                        <th style="width:180px; text-align:center;">Status Penanganan</th>
                                        <th style="width:220px; text-align:center;">Aksi</th>
                                        <th style="width:180px; text-align:center;">Status Verifikasi</th>
                                        <th style="min-width:250px;">Daftar Seluruh Berkas Perkara (PDF)</th>
                                        <th style="width:130px; text-align:center;">Data Dukung</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                while($data = $query->fetch(PDO::FETCH_ASSOC)):

                                    $status = !empty($data['status']) ? $data['status'] : 'Pending';
                                    if (strtolower($status) == 'pending') {
                                        $status = 'Pending';
                                    }

                                    $baseUrl = $url ?? '';
                                    $nama_terlapor = ($data['jenis_terlapor'] == 'database')
                                        ? ($data['nama_notaris'] ?? '')
                                        : ($data['nama_terlapor_manual'] ?? '');
                                    $has_documents = !empty($data['surat_pengaduan']) ||
                                                     !empty($data['ba_pemeriksaan']) ||
                                                     !empty($data['laporan_hasil_pemeriksaan']) ||
                                                     !empty($data['sk_majelis_pemeriksa']) ||
                                                     !empty($data['surat_pemanggilan']) ||
                                                     !empty($data['rekomendasi']);
                                    $can_verify = ($status == 'Diteruskan ke MPW');
                                ?>

                                    <tr>
                                        <td style="text-align:center; font-weight:600; color:#94a3b8;">
                                            <?= $no++; ?>
                                        </td>

                                        <td style="font-weight:600; color:#1e293b;">
                                            <?= !empty($data['nomor_register']) ? e($data['nomor_register']) : '<span style="color:#94a3b8;">-</span>'; ?>
                                        </td>

                                        <td style="font-weight:500; color:#334155;">
                                            <?= !empty($data['judul'])
                                                ? e($data['judul'])
                                                : '<span class="text-muted" style="font-style:italic;">Tidak ada judul</span>'; ?>
                                        </td>

                                        <td style="font-size:12px; color:#475569; font-weight:500;">
                                            <span><i class="fa fa-calendar" style="color:#94a3b8; margin-right:2px;"></i> <?= formatTanggalIndo($data['created_at']); ?></span>
                                        </td>

                                        <td>
                                            <div style="font-weight:600; color:#0f172a;">
                                                <?= !empty($nama_terlapor) ? e($nama_terlapor) : '-'; ?>
                                            </div>
                                            <?php if(!empty($data['nama_kedudukan'])): ?>
                                                <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                                    <i class="fa fa-map-marker"></i> <?= e($data['nama_kedudukan']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($data['no_hp_terlapor'])): ?>
                                                <div class="phone-container"><i class="fa fa-phone"></i> <?= e($data['no_hp_terlapor']); ?></div>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div style="font-weight:600; color:#334155;">
                                                <i class="fa fa-user" style="color:#cbd5e1;"></i> <?= e($data['nama_pelapor']); ?>
                                            </div>
                                            <?php if(!empty($data['no_hp_pelapor'])): ?>
                                                <div class="phone-container" style="background-color:#fff;"><i class="fa fa-phone"></i> <?= e($data['no_hp_pelapor']); ?></div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex flex-column align-items-center justify-content-center" style="gap: 6px;">
                                                
                                                <?php if ($status == 'Pending'): ?>
                                                    <span class="badge badge-secondary px-2 py-1">
                                                        <i class="fa fa-clock-o mr-1"></i> Pending
                                                    </span>

                                                <?php elseif ($status == 'Proses Pemeriksaan MPD' || $status == 'Register Perkara'): ?>
                                                    <span class="badge-progress-proses px-2 py-1">
                                                        <i class="fa fa-hourglass-half mr-1"></i> 
                                                        <?= ($status == 'Register Perkara') ? 'Register Perkara' : 'Proses Pemeriksaan MPD'; ?>
                                                    </span>

                                                    <?php 
                                                    $sisa_hari = hitungSisaHariKerja($data['created_at'], $daftar_libur); 
                                                    if ($sisa_hari > 5): 
                                                    ?>
                                                        <small class="text-success font-weight-bold mt-1">
                                                            <i class="fa fa-calendar-check-o mr-1"></i> Sisa <?= $sisa_hari; ?> Hari Kerja
                                                        </small>
                                                    <?php elseif ($sisa_hari >= 0): ?>
                                                        <small class="text-warning font-weight-bold mt-1">
                                                            <i class="fa fa-exclamation-triangle mr-1"></i> Sisa <?= $sisa_hari; ?> Hari
                                                        </small>
                                                    <?php else: ?>
                                                        <small class="text-danger font-weight-bold mt-1">
                                                            <i class="fa fa-exclamation-circle mr-1"></i> Lewat <?= abs($sisa_hari); ?> Hari
                                                        </small>
                                                    <?php endif; ?>

                                                <?php elseif ($status == 'Selesai di MPD'): ?>
                                                    <span class="badge-progress-selesai px-2 py-1">
                                                        <i class="fa fa-check-circle mr-1"></i> Selesai di MPD
                                                    </span>

                                                <?php elseif ($status == 'Diteruskan ke MPW'): ?>
                                                    <span class="badge-progress-mpw px-2 py-1">
                                                        <i class="fa fa-share-square mr-1"></i> Diteruskan ke MPW
                                                    </span>

                                                <?php elseif ($status == 'Perkara Pemeriksaan'): ?>
                                                    <span class="badge-progress-pemeriksaan px-2 py-1">
                                                        <i class="fa fa-check-square-o mr-1"></i> Perkara Pemeriksaan
                                                    </span>

                                                <?php elseif ($status == 'Selesai'): ?>
                                                    <span class="badge-progress-selesai px-2 py-1">
                                                        <i class="fa fa-check-circle mr-1"></i> Selesai
                                                    </span>
                                                <?php endif; ?>

                                            </div>
                                        </td>

                                        <td class="text-center align-middle" style="vertical-align: middle; min-width: 160px;">
                                            <div class="d-flex flex-column align-items-center justify-content-center" style="gap: 8px;">
                                                
                                                <a href="perkara_detail.php?id=<?= (int)$data['id_perkara']; ?>"
                                                class="btn btn-outline-info font-weight-bold fw-bold px-4 py-2 shadow-sm w-100" 
                                                style="max-width: 140px; font-size: 13px; border-width: 2px;">
                                                    <i class="fa fa-eye mr-2 me-2"></i> Detail
                                                </a>

                                                <?php if($can_verify): ?>
                                                    <a href="perkara_verifikasi.php?id=<?= (int)$data['id_perkara']; ?>"
                                                    target="_blank"
                                                    class="btn btn-outline-primary font-weight-bold fw-bold px-4 py-2 shadow-sm w-100"
                                                    style="max-width: 140px; font-size: 13px; border-width: 2px;">
                                                        <i class="fa fa-check-square-o mr-2 me-2"></i> Verifikasi
                                                    </a>
                                                <?php endif; ?>

                                                <a href="<?= e($url); ?>act/hapus_perkara.php?id=<?= (int)$data['id_perkara']; ?>"
                                                class="btn btn-outline-danger font-weight-bold fw-bold px-4 py-2 shadow-sm w-100"
                                                style="max-width: 140px; font-size: 13px; border-width: 2px;"
                                                onclick="return confirm('Yakin ingin menghapus perkara ini? Seluruh data dan file akan dihapus permanen.')">
                                                    <i class="fa fa-trash mr-2 me-2"></i> Hapus
                                                </a>

                                            </div>
                                        </td>

                                        <td style="text-align:center;">

                                            <?php if($can_verify): ?>

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

                                        <td>
                                            <div style="display:flex; flex-wrap:wrap; gap:2px;">
                                                <?php if(!empty($data['surat_pengaduan'])): ?>
                                                    <a href="<?= e($baseUrl . $data['surat_pengaduan']); ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> Surat Pengaduan</a>
                                                <?php endif; ?>
                                                <?php if(!empty($data['sk_majelis_pemeriksa'])): ?>
                                                    <a href="<?= e($baseUrl . $data['sk_majelis_pemeriksa']); ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> SK Majelis</a>
                                                <?php endif; ?>
                                                <?php if(!empty($data['surat_pemanggilan'])): ?>
                                                    <a href="<?= e($baseUrl . $data['surat_pemanggilan']); ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> S. Pemanggilan</a>
                                                <?php endif; ?>
                                                <?php if(!empty($data['ba_pemeriksaan'])): ?>
                                                    <a href="<?= e($baseUrl . $data['ba_pemeriksaan']); ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> BA Pemeriksaan</a>
                                                <?php endif; ?>
                                                <?php if(!empty($data['laporan_hasil_pemeriksaan'])): ?>
                                                    <a href="<?= e($baseUrl . $data['laporan_hasil_pemeriksaan']); ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> LHP MPD</a>
                                                <?php endif; ?>
                                                <?php if(!empty($data['rekomendasi'])): ?>
                                                    <a href="<?= e($baseUrl . $data['rekomendasi']); ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> Rekomendasi</a>
                                                <?php endif; ?>
                                                <?php if(!$has_documents): ?>
                                                    <span style="color:#94a3b8; font-style:italic; font-size:12px;">Belum ada berkas</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td style="text-align:center;">
                                            <div class="flex-center-column">
                                                <?php if(!empty($data['data_dukung_link'])): ?>
                                                    <a href="<?= e($data['data_dukung_link']); ?>" target="_blank" class="btn btn-link-action btn-xs" style="width:105px;"><i class="fa fa-external-link"></i> Data Dukung</a>
                                                <?php endif; ?>
                                                <?php if(!empty($data['data_dukung_tambahan'])): ?>
                                                    <a href="<?= e($data['data_dukung_tambahan']); ?>" target="_blank" class="btn btn-link-action btn-xs btn-dukung-tambahan">
                                                        <i class="fa fa-external-link"></i>
                                                        <span>Data Dukung Tambahan</span>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(empty($data['data_dukung_link']) && empty($data['data_dukung_tambahan'])): ?>
                                                    <span style="color:#cbd5e1;">-</span>
                                                <?php endif; ?>
                                            </div>
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
                "next": "\u203a",
                "previous": "\u2039"
            }
        },
        "initComplete": function() {
            $('#loadingOverlay').fadeOut(200);
        }
    });

});

</script>
