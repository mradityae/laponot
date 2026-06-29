<?php
include "header.php";
include "../config/koneksi.php";

// ==========================================
// 1. FUNGSI PEMBANTU (HELPERS)
// ==========================================

/**
 * Fungsi formatting tanggal Indonesia
 */
function formatTanggalIndo($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') return '-';
    
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($datetime);
    return date('j', $timestamp) . ' ' . $bulan[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp) . ' ' . date('H:i', $timestamp);
}

/**
 * Mengambil data hari libur nasional dari API publik (Cached)
 */
function getHariLiburNasional() {
    static $list_libur = null;
    if ($list_libur !== null) return $list_libur;

    $list_libur = [];
    $tahun_sekarang = date('Y');
    $api_url = "https://dayoffapi.vercel.app/api?year=" . $tahun_sekarang;
    
    $ctx = stream_context_create([
        'http' => ['timeout' => 2] 
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

/**
 * Hitung sisa 30 hari kerja menggunakan data API Hari Libur Nasional
 */
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
        $w = date('N', $tmp_date); // 1 = Senin, 7 = Minggu
        $tanggal_format = date('Y-m-d', $tmp_date);
        
        if ($w < 6 && !in_array($tanggal_format, $daftar_libur)) {
            $days_elapsed++;
        }
        $tmp_date = strtotime("+1 day", $tmp_date);
    }
    
    return $target_days - $days_elapsed;
}

// ==========================================
// 2. LOGIKA UTAMA & QUERY DATABASE
// ==========================================

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$kedudukan = $_SESSION['kedudukan'] ?? null;
$filter_map = [
    'semua' => null,
    'pending' => [
        'condition' => 'LOWER(p.status) = :status_filter',
        'value' => 'pending'
    ],
    'proses' => [
        'condition' => 'p.status = :status_filter',
        'value' => 'Proses Pemeriksaan MPD'
    ],
    'selesai_mpd' => [
        'condition' => 'p.status = :status_filter',
        'value' => 'Selesai di MPD'
    ],
    'diteruskan_mpw' => [
        'condition' => 'p.status = :status_filter',
        'value' => 'Diteruskan ke MPW'
    ]
];

if ($filter == 'Pending') {
    $filter = 'pending';
}

if (!array_key_exists($filter, $filter_map)) {
    $filter = 'semua';
}

// Base Query (p.* akan otomatis menarik kolom judul baru)
$params = ['id_kedudukan' => $kedudukan];
$sql = "SELECT p.*, n.nama AS nama_notaris, k.nama_kedudukan 
        FROM perkara_mpw p
        LEFT JOIN notaris n ON p.id_notaris = n.id_notaris
        LEFT JOIN kedudukan k ON p.id_kedudukan = k.id_kedudukan
        WHERE p.id_kedudukan = :id_kedudukan";

if ($filter_map[$filter] !== null) {
    $sql .= " AND " . $filter_map[$filter]['condition'];
    $params['status_filter'] = $filter_map[$filter]['value'];
}

$sql .= " ORDER BY p.id_perkara DESC";

// Eksekusi PDO
$query = $koneksi->prepare($sql);
$query->execute($params);

// Ambil daftar hari libur 1x di awal
$daftar_libur = getHariLiburNasional();
?>

<!-- ==========================================
     3. STYLING (BERSIH DARI INLINE-CSS)
     ========================================== -->
<style>
    .page-bg { background-color: #f8fafc; }
    .inner-wrapper { padding: 25px 15px; }
    .panel-header-custom { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 25px; background: #fff; }
    .panel-body-header { padding: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
    .header-title { margin: 0; font-weight: 700; color: #1e293b; font-size: 26px; letter-spacing: -0.5px; display: flex; align-items: center; gap: 12px; }
    .header-icon { color: #3b82f6; background: #eff6ff; padding: 10px; border-radius: 10px; font-size: 20px; }
    .header-subtitle { color: #64748b; margin: 6px 0 0 0; font-size: 14px; }
    .btn-add-perkara { font-weight: 600; padding: 10px 20px; border-radius: 8px; border: none; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); display: inline-flex; align-items: center; gap: 8px; }
    .filter-container { margin-bottom: 20px; display: flex; gap: 10px; }
    .btn-filter { border-radius: 20px; font-weight: 600; padding: 6px 18px; }
    .panel-table-container { border: none; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); background: #fff; overflow: hidden; position: relative; min-height: 200px; }
    
    /* -------------------------------------------
        LOADING STATE OVERLAY STYLING
       ------------------------------------------- */
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
    .loading-text {
        font-weight: 600;
        color: #475569;
        font-size: 14px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    /* ------------------------------------------- */

    /* Tabel Spesifik */
    .table-custom > tbody > tr > td { vertical-align: middle !important; padding: 16px 15px !important; color: #334155; border-bottom: 1px solid #f1f5f9 !important; font-size: 13px; }
    .table-custom > thead > tr > th { background-color: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.75px; padding: 14px 15px !important; border-bottom: 2px solid #e2e8f0 !important; border-top: none !important; }
    .doc-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; margin: 2px 2px 2px 0; background-color: #f1f5f9; color: #475569; border-radius: 4px; font-size: 11px; text-decoration: none !important; border: 1px solid #e2e8f0; font-weight: 500; }
    .doc-badge:hover { background-color: #fee2e2; color: #dc2626; border-color: #fca5a5; }
    .btn-link-action { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-weight: 600; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none !important; font-size: 11px; }
    .badge-progress-register { font-size: 11px; padding: 5px 10px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .badge-progress-pemeriksaan { font-size: 11px; padding: 5px 10px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .btn-action-pemeriksaan { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none !important; }
    .btn-action-pemeriksaan:hover { background-color: #1d4ed8; color: #fff; }
    .phone-container { font-size: 12px; color: #475569; background: #f8fafc; padding: 3px 6px; border-radius: 4px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px; }
    .flex-center-column { display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .btn-dukung-tambahan { width: 100%; max-width: 160px; display: flex; align-items: center; justify-content: center; gap: 5px; white-space: normal; text-align: center; line-height: 1.4; padding: 6px 10px; }

    .badge-progress-proses{
        background:#FEF3C7;
        color:#92400E;
        border:1px solid #FCD34D;
    }

    .badge-progress-selesai{
        background:#DCFCE7;
        color:#166534;
        border:1px solid #BBF7D0;
    }

    .badge-progress-mpw{
        background:#DBEAFE;
        color:#1D4ED8;
        border:1px solid #BFDBFE;
    }
</style>

<!-- ==========================================
     4. VIEW / LAYOUT HTML
     ========================================== -->
<div id="page-wrapper" class="page-bg">
    <div id="page-inner" class="inner-wrapper">
        <div class="row">
            <div class="col-md-12">
                
                <!-- Card Header Panel -->
                <div class="panel panel-default panel-header-custom">
                    <div class="panel-body panel-body-header">
                        <div>
                            <h2 class="header-title">
                                <i class="fa fa-gavel header-icon"></i> Register Perkara
                            </h2>
                            <p class="header-subtitle">Manajemen daftar perkara hukum Majelis Pengawas</p>
                        </div>
                        <div>
                            <a href="perkara_tambah.php" class="btn btn-success btn-add-perkara">
                                <i class="fa fa-plus-circle"></i> Tambah Perkara Baru
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bagian Navigasi Filter -->
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
                                <option value="<?= htmlspecialchars($filter_key); ?>" <?= ($filter == $filter_key) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($filter_label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <!-- Main Content Table Card -->
                <div class="panel panel-default panel-table-container">
                    
                    <!-- ELEMENT LOADING OVERLAY -->
                    <div id="loadingOverlay" class="table-loading-overlay">
                        <div class="spinner-custom"></div>
                        <div class="loading-text">Memproses data perkara...</div>
                    </div>

                    <div class="panel-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table id="tablePerkara" class="table table-hover table-custom" style="margin-bottom: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; text-align: center;">No</th>
                                        <th style="width: 130px;">No. Register</th>
                                        <th style="min-width: 150px;">Judul Perkara</th>
                                        <th style="width: 120px;">Tgl. Input</th>
                                        <th style="min-width: 140px;">Pihak Terlapor</th>
                                        <th style="min-width: 140px;">Pihak Pelapor</th>
                                        <th style="width: 180px; text-align: center;">Status Penanganan</th>
                                        <th style="width: 90px; text-align: center;">Aksi</th>
                                        <th style="width:180px; text-align:center;">Status Verifikasi</th>
                                        <th style="min-width: 250px;">Daftar Seluruh Berkas Perkara (PDF)</th>
                                        <th style="width: 130px; text-align: center;">Data Dukung</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while($data = $query->fetch()):
                                        $status = $data['status'] ?? 'Pending';
                                        $has_documents = !empty($data['surat_pengaduan']) || !empty($data['ba_pemeriksaan']) || 
                                                         !empty($data['laporan_hasil_pemeriksaan']) || !empty($data['sk_majelis_pemeriksa']) || 
                                                         !empty($data['surat_pemanggilan']) || !empty($data['rekomendasi']);
                                        $baseUrl = $url ?? '';
                                    ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: 600; color: #94a3b8;"><?= $no++; ?></td>
                                        <td style="font-weight: 600; color: #1e293b;">

                                            <?php if(!empty($data['nomor_register'])): ?>

                                                <?= htmlspecialchars($data['nomor_register']); ?>

                                            <?php else: ?>

                                                <a href="../act/buat-register-perkara.php?id=<?= $data['id_perkara']; ?>"
                                                class="btn btn-outline-success btn-sm shadow-sm"
                                                style="font-weight:600; display: inline-flex; align-items: center; gap: 5px;"
                                                onclick="return confirm('Setelah nomor register dibuat, perkara wajib diperiksa selama 30 hari kerja. Lanjutkan?')">
                                                    <i class="fa fa-file-text"></i> Buat Nomor Register Perkara
                                                </a>

                                            <?php endif; ?>

                                        </td>
                                        <td style="font-weight: 500; color: #334155;">
                                            <?= !empty($data['judul']) ? htmlspecialchars($data['judul']) : '<span class="text-muted" style="font-style: italic;">Tidak ada judul</span>'; ?>
                                        </td>
                                        <td style="font-size: 12px; color: #475569; font-weight: 500;">
                                            <span><i class="fa fa-calendar" style="color: #94a3b8; margin-right: 2px;"></i> <?= formatTanggalIndo($data['created_at']); ?></span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0f172a;">
                                                <?= ($data['jenis_terlapor'] == 'database') ? htmlspecialchars($data['nama_notaris']) : htmlspecialchars($data['nama_terlapor_manual']); ?>
                                            </div>
                                            <?php if(!empty($data['nama_kedudukan'])): ?>
                                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;"><i class="fa fa-map-marker"></i> <?= htmlspecialchars($data['nama_kedudukan']); ?></div>
                                            <?php endif; ?>
                                            <?php if(!empty($data['no_hp_terlapor'])): ?>
                                                <div class="phone-container"><i class="fa fa-phone"></i> <?= htmlspecialchars($data['no_hp_terlapor']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #334155;">
                                                <i class="fa fa-user" style="color: #cbd5e1;"></i> <?= htmlspecialchars($data['nama_pelapor']); ?>
                                            </div>
                                            <?php if(!empty($data['no_hp_pelapor'])): ?>
                                                <div class="phone-container" style="background-color: #ffffff;"><i class="fa fa-phone"></i> <?= htmlspecialchars($data['no_hp_pelapor']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex flex-column align-items-center justify-content-center" style="gap: 6px;">
                                                
                                                <?php 
                                                // Pindahkan inisialisasi logika ke atas agar rapi
                                                $sisa_hari = hitungSisaHariKerja($data['created_at'], $daftar_libur); 
                                                ?>

                                                <?php if($status == 'Pending'): ?>
                                                    <span class="badge badge-secondary px-2 py-1c">
                                                        <i class="fa fa-clock-o mr-1"></i> Pending
                                                    </span>

                                                <?php elseif($status == 'Proses Pemeriksaan MPD'): ?>
                                                    <span class="badge badge-warning text-dark px-2 py-1">
                                                        <i class="fa fa-hourglass-half mr-1"></i> Proses Pemeriksaan MPD
                                                    </span>

                                                    <?php if($sisa_hari > 5): ?>
                                                        <small class="text-success font-weight-bold">
                                                            <i class="fa fa-check-circle-o"></i> Sisa <?= $sisa_hari ?> Hari Kerja
                                                        </small>
                                                    <?php elseif($sisa_hari >= 0): ?>
                                                        <small class="text-warning font-weight-bold">
                                                            <i class="fa fa-exclamation-triangle"></i> Sisa <?= $sisa_hari ?> Hari
                                                        </small>
                                                    <?php else: ?>
                                                        <small class="text-danger font-weight-bold">
                                                            <i class="fa fa-times-circle"></i> Lewat <?= abs($sisa_hari) ?> Hari
                                                        </small>
                                                    <?php endif; ?>

                                                <?php elseif($status == 'Selesai di MPD'): ?>
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="fa fa-check-circle mr-1"></i> Selesai di MPD
                                                    </span>

                                                <?php elseif($status == 'Diteruskan ke MPW'): ?>
                                                    <span class="badge badge-info px-2 py-1">
                                                        <i class="fa fa-share-square mr-1"></i> Diteruskan ke MPW
                                                    </span>
                                                <?php endif; ?>

                                            </div>
                                        </td>
                                        <td style="text-align: center; white-space: nowrap; vertical-align: middle;">
                                            <!-- Tombol Periksa hanya muncul jika sudah ada nomor register -->
                                            <?php if(!empty($data['nomor_register'])): ?>
                                                <a href="perkara_periksa.php?id=<?= $data['id_perkara']; ?>"
                                                class="btn btn-info btn-sm"
                                                style="font-weight: 600; margin-right: 4px;">
                                                    <i class="fa fa-search"></i> Periksa
                                                </a>
                                            <?php endif; ?>

                                            <!-- Tombol Edit -->
                                            <a href="perkara_edit.php?id=<?= $data['id_perkara']; ?>"
                                            class="btn btn-warning btn-sm"
                                            style="font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>

                                        </td>
                                        <td style="text-align:center;">
                                            <?php if($status=='Diteruskan ke MPW'): ?>

                                                <?php if(isset($data['verifikasi']) && $data['verifikasi'] == 'Terverifikasi'): ?>

                                                    <span class="badge badge-success" style="padding:8px 12px;">
                                                        <i class="fa fa-check-circle"></i>
                                                        Terverifikasi
                                                    </span>

                                                <?php elseif(isset($data['verifikasi']) && $data['verifikasi'] == 'Tidak Terverifikasi'): ?>

                                                    <span class="badge badge-danger" style="padding:8px 12px;">
                                                        <i class="fa fa-times-circle"></i>
                                                        Ditolak
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
                                            <div style="display: flex; flex-wrap: wrap; gap: 2px;">
                                                <?php if(!empty($data['surat_pengaduan'])): ?>
                                                    <a href="<?= $baseUrl . $data['surat_pengaduan']; ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> Surat Pengaduan</a>
                                                <?php endif; ?>
                                                
                                                <?php if(!empty($data['sk_majelis_pemeriksa'])): ?>
                                                    <a href="<?= $baseUrl . $data['sk_majelis_pemeriksa']; ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> SK Majelis</a>
                                                <?php endif; ?>

                                                <?php if(!empty($data['surat_pemanggilan'])): ?>
                                                    <a href="<?= $baseUrl . $data['surat_pemanggilan']; ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> S. Pemanggilan</a>
                                                <?php endif; ?>

                                                <?php if(!empty($data['ba_pemeriksaan'])): ?>
                                                    <a href="<?= $baseUrl . $data['ba_pemeriksaan']; ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> BA Pemeriksaan</a>
                                                <?php endif; ?>
                                                
                                                <?php if(!empty($data['laporan_hasil_pemeriksaan'])): ?>
                                                    <a href="<?= $baseUrl . $data['laporan_hasil_pemeriksaan']; ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> LHP MPD</a>
                                                <?php endif; ?>
                                                
                                                <?php if(!empty($data['rekomendasi'])): ?>
                                                    <a href="<?= $baseUrl . $data['rekomendasi']; ?>" target="_blank" class="doc-badge"><i class="fa fa-file-pdf-o text-danger"></i> Rekomendasi</a>
                                                <?php endif; ?>

                                                <?php if(!$has_documents): ?>
                                                    <span style="color: #94a3b8; font-style: italic; font-size: 12px;">Belum ada berkas</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="flex-center-column">
                                                <?php if(!empty($data['data_dukung_link'])): ?>
                                                    <a href="<?= $data['data_dukung_link']; ?>" target="_blank" class="btn btn-link-action btn-xs" style="width: 105px;"><i class="fa fa-external-link"></i> Data Dukung</a>
                                                <?php endif; ?>
                                                
                                                <?php if(!empty($data['data_dukung_tambahan'])): ?>
                                                    <a href="<?= $data['data_dukung_tambahan']; ?>" target="_blank" class="btn btn-link-action btn-xs btn-dukung-tambahan">
                                                        <i class="fa fa-external-link"></i>
                                                        <span>Data Dukung Tambahan</span>
                                                    </a> 
                                                <?php endif; ?>
                                                
                                                <?php if(empty($data['data_dukung_link']) && empty($data['data_dukung_tambahan'])): ?>
                                                    <span style="color: #cbd5e1;">-</span>
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

<!-- ==========================================
     5. JAVASCRIPT INITIALIZATION
     ========================================== -->
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
