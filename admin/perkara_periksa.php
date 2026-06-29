<?php
include "header.php";
include "../config/koneksi.php";

$id_perkara = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$queryPerkara = $koneksi->prepare("
    SELECT p.*, n.nama as nama_notaris, k.nama_kedudukan 
    FROM perkara_mpw p
    LEFT JOIN notaris n ON p.id_notaris = n.id_notaris
    LEFT JOIN kedudukan k ON p.id_kedudukan = k.id_kedudukan
    WHERE p.id_perkara = ? LIMIT 1
");
$queryPerkara->execute([$id_perkara]);
$perkara = $queryPerkara->fetch(PDO::FETCH_ASSOC);

if (!$perkara) {
    echo "<script>alert('Data perkara tidak ditemukan!'); window.location.href='perkara_index.php';</script>";
    exit();
}

$nama_terlapor = ($perkara['jenis_terlapor'] == 'database') ? $perkara['nama_notaris'] : $perkara['nama_terlapor_manual'];
$status_pengaduan = trim($perkara['status'] ?? '');

switch ($status_pengaduan) {

    case 'Proses Pemeriksaan MPD':
        $statusBadge = '
            <span class="status-badge status-proses">
                <i class="fa fa-hourglass-half"></i>
                Proses Pemeriksaan MPD
            </span>';
        break;

    case 'Selesai di MPD':
        $statusBadge = '
            <span class="status-badge status-mpd">
                <i class="fa fa-check-circle"></i>
                Selesai di MPD
            </span>';
        break;

    case 'Diteruskan ke MPW':
        $statusBadge = '
            <span class="status-badge status-mpw">
                <i class="fa fa-share-square"></i>
                Diteruskan ke MPW
            </span>';
        break;

    default:
        $statusBadge = '
            <span class="status-badge status-belum">
                <i class="fa fa-minus-circle"></i>
                Belum Ditentukan
            </span>';
}

// Helper fungsional yang menghasilkan badge modern dan tombol aksi dengan style seragam
function renderFileStatus($nama_file_db) {
    if (!empty($nama_file_db)) {
        return '
        <div class="status-doc-wrapper">
            <span class="badge-custom badge-success-light"><i class="fa fa-check-circle"></i> Tersedia</span>
            <a href="/laponot/'.$nama_file_db.'" target="_blank" class="btn-view-doc">
                <i class="fa fa-eye"></i> Lihat Berkas
            </a>
        </div>';
    }
    return '
    <div class="status-doc-wrapper">
        <span class="badge-custom badge-danger-light"><i class="fa fa-times-circle"></i> Belum Diunggah</span>
    </div>';
}
?>

<!-- ==========================================
     STYLING: MODERN DASHBOARD DESIGN SYSTEM
     ========================================== -->
<style>
    /* Global & Layout Base */
    .page-bg-gray { background-color: #f8fafc; min-height: 100vh; }
    .inner-padded { padding: 30px 20px; }
    
    /* Header Section */
    .header-card { background: #fff; border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 24px; margin-bottom: 30px; }
    .header-title-flex { display: flex; align-items: center; gap: 16px; }
    .header-icon-box { background: #eff6ff; color: #2563eb; padding: 12px; border-radius: 10px; font-size: 24px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; }
    .header-text-group h2 { margin: 0; font-weight: 700; color: #1e293b; font-size: 24px; letter-spacing: -0.5px; }
    .header-text-group p { margin: 4px 0 0 0; color: #64748b; font-size: 14px; }

    /* Card Panels */
    .panel-custom-card { background: #fff; border: none; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.05); margin-bottom: 30px; overflow: hidden; }
    .panel-custom-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 20px 24px; font-weight: 700; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    .panel-custom-header i { color: #3b82f6; font-size: 18px; }
    .panel-custom-body { padding: 24px; }

    /* Sidebar Information */
    .info-block { padding: 14px 0; border-bottom: 1px dashed #f1f5f9; }
    .info-block:last-child { border-bottom: none; padding-bottom: 0; }
    .info-block:first-child { padding-top: 0; }
    .info-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; color: #94a3b8; margin-bottom: 4px; display: block; }
    .info-value { font-size: 15px; color: #334155; margin: 0; font-weight: 500; }
    .info-value strong { color: #0f172a; font-weight: 700; }
    .register-highlight { font-size: 20px; font-weight: 700; color: #2563eb; margin: 0; letter-spacing: -0.5px; }

    /* Custom Input File & Badges */
    .upload-grid-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 20px; transition: all 0.2s ease; }
    .upload-grid-item:hover { border-color: #cbd5e1; background: #f1f5f9; }
    .upload-label-text { font-weight: 600; color: #334155; font-size: 14px; margin-bottom: 10px; display: block; }
    
    .status-doc-wrapper { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; flex-wrap: wrap; }
    .badge-custom { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .badge-success-light { background: #dcfce7; color: #166534; }
    .badge-danger-light { background: #fee2e2; color: #991b1b; }
    
    .btn-view-doc { background: #fff; color: #2563eb; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none !important; transition: all 0.2s; }
    .btn-view-doc:hover { background: #2563eb; color: #fff; border-color: #2563eb; }
    
    .input-file-custom { border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; padding: 6px 12px; font-size: 13px; color: #475569; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02); }
    .input-file-custom:focus { border-color: #3b82f6; outline: none; }

    /* Link Field & Addons */
    .input-group-custom { display: flex; width: 100%; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .addon-custom { background: #f1f5f9; border: 1px solid #cbd5e1; border-right: none; color: #64748b; padding: 10px 14px; display: flex; align-items: center; justify-content: center; }
    .input-url-custom { border: 1px solid #cbd5e1; padding: 10px 14px; width: 100%; font-size: 14px; border-radius: 0 8px 8px 0; }
    .input-url-custom:focus { border-color: #3b82f6; outline: none; }
    .btn-external-link { background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 600; padding: 6px 12px; border-radius: 6px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none !important; margin-top: 10px; }
    .btn-external-link:hover { background-color: #166534; color: #fff; }

    /* Action Form Buttons */
    .form-actions-wrapper { display: flex; align-items: center; gap: 12px; margin-top: 10px; padding-top: 10px; }
    .btn-action-save { background: #2563eb; color: #fff; font-weight: 600; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2); display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s; }
    .btn-action-save:hover { background: #1d4ed8; }
    .btn-action-back { background: #fff; color: #475569; font-weight: 600; border: 1px solid #cbd5e1; padding: 12px 24px; border-radius: 8px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important; transition: all 0.2s; }
    .btn-action-back:hover { background: #f8fafc; color: #1e293b; border-color: #94a3b8; }

    /* Status Pengaduan */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-proses{
        background:#FEF3C7;
        color:#92400E;
        border:1px solid #FCD34D;
    }

    .status-mpd{
        background:#DCFCE7;
        color:#166534;
        border:1px solid #BBF7D0;
    }

    .status-mpw{
        background:#DBEAFE;
        color:#1D4ED8;
        border:1px solid #BFDBFE;
    }

    .status-belum{
        background:#F1F5F9;
        color:#64748B;
        border:1px solid #CBD5E1;
    }

    /* ===================== */

    .decision-box{
        background:#F8FAFC;
        border:1px solid #E2E8F0;
        border-radius:12px;
        padding:18px;
        margin-bottom:25px;
    }

    .decision-title{
        font-size:15px;
        font-weight:700;
        color:#1E293B;
        margin-bottom:5px;
    }

    .decision-desc{
        color:#64748B;
        font-size:13px;
        margin-bottom:15px;
    }

    .form-select-custom{
        width:100%;
        height:46px;
        border:1px solid #CBD5E1;
        border-radius:8px;
        padding:0 14px;
        background:#fff;
    }

    .form-select-custom:focus{
        outline:none;
        border-color:#2563EB;
        box-shadow:0 0 0 3px rgba(37,99,235,.15);
    }
</style>

<!-- ==========================================
     MAIN VIEW INTERFACE
     ========================================== -->
<div id="page-wrapper" class="page-bg-gray">
    <div id="page-inner" class="inner-padded">
        
        <!-- Header Panel Modern -->
        <div class="header-card">
            <div class="header-title-flex">
                <div class="header-icon-box">
                    <i class="fa fa-folder-open"></i>
                </div>
                <div class="header-text-group">
                    <h2>Tahap Pemeriksaan Berkas Perkara</h2>
                    <p>Kelola proses unggah berkas, administrasi, dan validasi dokumen pemeriksaan secara digital terintegrasi.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Sidebar: Ringkasan Detail Perkara -->
            <div class="col-md-4">
                <div class="panel-custom-card">
                    <div class="panel-custom-header">
                        <i class="fa fa-info-circle"></i> Ringkasan Identitas Perkara
                    </div>
                    <div class="panel-custom-body">
                        <div class="info-block">
                            <span class="info-label">Nomor Register</span>
                            <h4 class="register-highlight"><?= htmlspecialchars($perkara['nomor_register']); ?></h4>
                        </div>
                        <div class="info-block">
                            <span class="info-label">Status Pengaduan</span>
                            <?= $statusBadge; ?>
                        </div>
                        <div class="info-block">
                            <span class="info-label">Judul Perkara</span>
                            <p class="info-value" style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($perkara['judul'] ?: '-'); ?></p>
                        </div>
                        <div class="info-block">
                            <span class="info-label">Pihak Terlapor (Notaris)</span>
                            <p class="info-value"><strong><?= htmlspecialchars($nama_terlapor); ?></strong></p>
                        </div>
                        <div class="info-block">
                            <span class="info-label">Pihak Pelapor</span>
                            <p class="info-value"><i class="fa fa-user" style="color: #cbd5e1; margin-right: 4px;"></i> <?= htmlspecialchars($perkara['nama_pelapor']); ?></p>
                        </div>
                        <div class="info-block">
                            <span class="info-label">Wilayah Kedudukan</span>
                            <p class="info-value"><i class="fa fa-map-marker" style="color: #cbd5e1; margin-right: 5px;"></i> <?= htmlspecialchars($perkara['nama_kedudukan'] ?: '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Area: Dokumen Form Berkas -->
            <div class="col-md-8">
                <div class="panel-custom-card">
                    <div class="panel-custom-header">
                        <i class="fa fa-cloud-upload"></i> Unggah Dokumen Administrasi Sidang
                    </div>
                    <div class="panel-custom-body">
                        <form action="<?= $url; ?>act/pemeriksaan-perkara_proses.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_perkara" value="<?= $perkara['id_perkara']; ?>">

                            <!-- Grid Upload 2 Kolom Sejajar -->
                            <div class="row">
                                <?php 
                                $fields = [
                                    ['ba_pemeriksaan', 'Berita Acara (BA) Pemeriksaan'],
                                    ['laporan_hasil_pemeriksaan', 'Laporan Hasil Pemeriksaan (LHP)'],
                                    ['surat_pemanggilan', 'Surat Pemanggilan Sidang'],
                                    ['rekomendasi', 'Berkas Rekomendasi Majelis']
                                ];
                                foreach($fields as $f) : ?>
                                <div class="col-md-6">
                                    <div class="upload-grid-item">
                                        <span class="upload-label-text"><?= $f[1]; ?></span>
                                        
                                        <!-- Render Status Eksistensi File & Tombol Lihat -->
                                        <?= renderFileStatus($perkara[$f[0]]); ?>
                                        
                                        <input type="file" name="<?= $f[0]; ?>" class="form-control input-file-custom" accept="application/pdf">
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Input Tautan Data Pendukung -->
                            <div class="form-group" style="margin-top: 5px; margin-bottom: 25px;">
                                <label class="upload-label-text" style="font-size: 14px;">Data Dukung via Google Drive</label>
                                <div class="input-group-custom">
                                    <span class="addon-custom"><i class="fa fa-link"></i></span>
                                    <input type="url" name="data_dukung_tambahan" class="input-url-custom" placeholder="Contoh: https://drive.google.com/share-folder" value="<?= htmlspecialchars($perkara['data_dukung_tambahan'] ?? ''); ?>">
                                </div>
                                <?php if(!empty($perkara['data_dukung_tambahan'])): ?>
                                    <div>
                                        <a href="<?= htmlspecialchars($perkara['data_dukung_tambahan']); ?>" target="_blank" class="btn-external-link">
                                            <i class="fa fa-external-link"></i> Akses Link Penyimpanan Eksternal
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="decision-box">

                                <div class="decision-title">
                                    <i class="fa fa-balance-scale text-primary"></i>
                                    Keputusan Penanganan Pengaduan
                                </div>

                                <div class="decision-desc">
                                    Tentukan hasil pemeriksaan perkara.
                                </div>

                                <div class="form-group">

                                    <label>Status Penanganan</label>

                                    <select name="status" class="form-select-custom">

                                        <option value="Proses Pemeriksaan MPD"
                                            <?= ($perkara['status']=="Proses Pemeriksaan MPD")?'selected':'';?>>
                                            🟡 Proses Pemeriksaan MPD
                                        </option>

                                        <option value="Selesai di MPD"
                                            <?= ($perkara['status']=="Selesai di MPD")?'selected':'';?>>
                                            🟢 Selesai di MPD
                                        </option>

                                        <option value="Diteruskan ke MPW"
                                            <?= ($perkara['status']=="Diteruskan ke MPW")?'selected':'';?>>
                                            🔵 Diteruskan ke MPW
                                        </option>

                                    </select>

                                </div>

                                <div class="form-group" style="margin-top:20px;">

                                    <label>Keterangan</label>

                                    <textarea
                                        class="form-control"
                                        rows="5"
                                        name="keterangan"
                                        placeholder="Masukkan catatan hasil pemeriksaan atau keputusan majelis..."><?= htmlspecialchars($perkara['keterangan']); ?></textarea>

                                </div>

                            </div>

                            <hr style="border-top: 1px solid #f1f5f9; margin: 25px 0;">
                            
                            <!-- Blok Tombol Aksi Utama Form -->
                            <div class="form-actions-wrapper">
                                <button type="submit" class="btn-action-save">
                                    <i class="fa fa-check-circle"></i> Simpan Perubahan Berkas
                                </button>
                                <a href="perkara_index.php" class="btn-action-back">
                                    <i class="fa fa-arrow-left"></i> Kembali ke Index
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>