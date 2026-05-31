<?php
include "header.php";
include "../config/koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = $koneksi->prepare("
    SELECT 
        p.*,
        n.nama AS nama_notaris,
        k.nama_kedudukan
    FROM perkara_mpw p
    LEFT JOIN notaris n 
        ON p.id_notaris = n.id_notaris
    LEFT JOIN kedudukan k 
        ON p.id_kedudukan = k.id_kedudukan
    WHERE p.id_perkara = ?
");

$query->execute([$id]);
$data = $query->fetch(PDO::FETCH_ASSOC);

if(!$data){
    die("<div style='padding: 20px; text-align: center; font-family: sans-serif; color: #ef4444;'>Data tidak ditemukan</div>");
}

function formatTanggalIndo($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $timestamp = strtotime($datetime);
    return date('j', $timestamp) . ' ' . $bulan[(int)date('n', $timestamp)] . ' ' . date('Y • H:i', $timestamp);
}

$baseUrl = isset($url) ? $url : '';
?>

<style>
:root {
    --primary: #2563eb;
    --primary-hover: #1d4ed8;
    --primary-light: #eff6ff;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-900: #0f172a;
}

#page-wrapper {
    background-color: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: var(--slate-900);
}

.detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 16px;
}

/* Header Area */
.page-header-block {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.title-area h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--slate-900);
    margin: 0 0 4px 0;
}

.title-area p {
    margin: 0;
    color: var(--slate-600);
    font-size: 14px;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background-color: #fff;
    border: 1px solid var(--slate-200);
    border-radius: 8px;
    color: var(--slate-700);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-back:hover {
    background-color: var(--slate-50);
    border-color: #cbd5e1;
}

/* Layout Grid */
.detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

@media (max-width: 991px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}

/* Card Styling */
.detail-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid var(--slate-200);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 24px;
    margin-bottom: 24px;
}

.card-section-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--slate-900);
    margin-top: 0;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--slate-100);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Data List Style */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.info-item {
    display: grid;
    grid-template-columns: 200px 1fr;
    align-items: start;
    gap: 16px;
}

@media (max-width: 576px) {
    .info-item {
        grid-template-columns: 1fr;
        gap: 4px;
    }
}

.info-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--slate-600);
}

.info-value {
    font-size: 14px;
    color: var(--slate-900);
    line-height: 1.5;
}

.register-badge {
    background-color: var(--primary-light);
    color: var(--primary);
    padding: 4px 10px;
    border-radius: 6px;
    font-family: monospace;
    font-weight: 600;
    font-size: 13px;
    display: inline-block;
}

/* Documents & Links Section */
.doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
}

.doc-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 8px;
    background: var(--slate-50);
    border: 1px solid var(--slate-200);
    text-decoration: none !important;
    color: var(--slate-700);
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.doc-link:hover {
    background: var(--primary-light);
    border-color: #bfdbfe;
    color: var(--primary);
}

.doc-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid var(--slate-200);
    color: var(--primary);
}

/* Custom Warning/Verification Alert box */
.verification-box {
    background-color: #f0f7ff; /* Biru sangat muda */
    border: 1px solid #dbeafe;     /* Border biru muda */
    border-left: 4px solid #2563eb;/* Garis aksen kiri biru tegas */
    border-radius: 8px;
    padding: 16px;
    font-size: 14px;
    color: #1e40af;                /* Teks warna biru gelap */
    line-height: 1.5;
}
.verification-box.empty {
    background-color: var(--slate-50);
    border: 1px solid var(--slate-200);
    border-left: 4px solid var(--slate-600);
    color: var(--slate-600);
    font-style: italic;
}
</style>

<div id="page-wrapper">
    <div id="page-inner">
        <div class="detail-container">
            
            <!-- Top Header Action -->
            <div class="page-header-block">
                <div class="title-area">
                    <h2>Detail Perkara</h2>
                    <p>Manajemen Berkas & Riwayat Pemeriksaan MPW</p>
                </div>
                <a href="<?= $url ?>superadmin/perkara_index" class="btn-back">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>

            <!-- Main Layout Grid -->
            <div class="detail-grid">
                
                <!-- Left Column: Primary Details -->
                <div class="main-content-column">
                    
                    <div class="detail-card">
                        <h3 class="card-section-title">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Informasi Utama Perkara
                        </h3>
                        <div class="info-list">
                            <div class="info-item">
                                <div class="info-label">Nomor Register</div>
                                <div class="info-value">
                                    <span class="register-badge"><?= htmlspecialchars($data['nomor_register']); ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Tahapan</div>
                                <div class="info-value">
                                    <span class="register-badge"><?= htmlspecialchars($data['status']); ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Judul Perkara</div>
                                <div class="info-value" style="font-weight: 600; font-size: 15px;">
                                    <?= htmlspecialchars($data['judul']); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Terlapor</div>
                                <div class="info-value">
                                    <strong>
                                        <?= ($data['jenis_terlapor'] == 'database') ? htmlspecialchars($data['nama_notaris']) : htmlspecialchars($data['nama_terlapor_manual']); ?>
                                    </strong>
                                    <?php if(!empty($data['nama_kedudukan'])): ?>
                                        <span style="color: var(--slate-600); font-size: 13px; display: block;">Kedudukan: <?= htmlspecialchars($data['nama_kedudukan']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Pelapor</div>
                                <div class="info-value"><?= htmlspecialchars($data['nama_pelapor']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Tanggal Input Sistem</div>
                                <div class="info-value" style="color: var(--slate-600);"><?= formatTanggalIndo($data['created_at']); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="detail-card">
                        <h3 class="card-section-title">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                            Dokumen Perkara Berkas Utama
                        </h3>
                        <div class="doc-grid">
                            <?php 
                            $docs = [
                                'surat_pengaduan' => 'Surat Pengaduan',
                                'sk_majelis_pemeriksa' => 'SK Majelis',
                                'surat_pemanggilan' => 'Surat Pemanggilan',
                                'ba_pemeriksaan' => 'BA Pemeriksaan',
                                'laporan_hasil_pemeriksaan' => 'Laporan Hasil Pemeriksaan',
                                'rekomendasi' => 'Rekomendasi'
                            ];
                            $hasDoc = false;

                            foreach($docs as $field => $label): 
                                if(!empty($data[$field])): 
                                    $hasDoc = true;
                            ?>
                                <a href="<?= $baseUrl . $data[$field]; ?>" target="_blank" class="doc-link">
                                    <div class="doc-icon">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <span><?= $label; ?></span>
                                </a>
                            <?php 
                                endif;
                            endforeach; 

                            if(!$hasDoc) {
                                echo "<div style='color: var(--slate-600); font-size: 14px; font-style: italic; grid-column: 1/-1;'>Tidak ada dokumen utama yang diunggah.</div>";
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Additional Data Supports Section -->
                    <div class="detail-card">
                        <h3 class="card-section-title">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            Data Dukung Tambahan
                        </h3>
                        <div class="doc-grid">
                            <?php if(!empty($data['data_dukung_link'])): ?>
                                <a href="<?= htmlspecialchars($data['data_dukung_link']); ?>" target="_blank" class="doc-link">
                                    <div class="doc-icon" style="color: #059669;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </div>
                                    <span>Data Dukung</span>
                                </a>
                            <?php endif; ?>

                            <?php if(!empty($data['data_dukung_tambahan'])): ?>
                                <a href="<?= htmlspecialchars($data['data_dukung_tambahan']); ?>" target="_blank" class="doc-link">
                                    <div class="doc-icon" style="color: #059669;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </div>
                                    <span>Data Dukung Tambahan</span>
                                </a>
                            <?php endif; ?>

                            <?php if(empty($data['data_dukung_link']) && empty($data['data_dukung_tambahan'])): ?>
                                <div style="color: var(--slate-600); font-size: 14px; font-style: italic; grid-column: 1/-1;">Tidak ada tautan data dukung eksternal.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Verification & Contact Info -->
                <div class="side-content-column">
                    
                    <!-- Verification Card -->
                    <div class="detail-card">
                        <h3 class="card-section-title">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Catatan Verifikasi
                        </h3>
                        <?php if(!empty($data['catatan_verifikasi'])): ?>
                            <div class="verification-box">
                                <?= nl2br(htmlspecialchars($data['catatan_verifikasi'])); ?>
                            </div>
                        <?php else: ?>
                            <div class="verification-box empty">
                                Tidak ada catatan verifikasi untuk perkara ini.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Contact Card -->
                    <div class="detail-card">
                        <h3 class="card-section-title">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Informasi Kontak & Alamat
                        </h3>
                        <div class="info-list" style="gap: 14px;">
                            <div>
                                <div class="info-label" style="margin-bottom: 2px;">No. HP Pelapor</div>
                                <div class="info-value" style="font-family: monospace; font-size: 14px;">
                                    <?= !empty($data['no_hp_pelapor']) ? htmlspecialchars($data['no_hp_pelapor']) : '-'; ?>
                                </div>
                            </div>
                            <div>
                                <div class="info-label" style="margin-bottom: 2px;">No. HP Terlapor</div>
                                <div class="info-value" style="font-family: monospace; font-size: 14px;">
                                    <?= !empty($data['no_hp_terlapor']) ? htmlspecialchars($data['no_hp_terlapor']) : '-'; ?>
                                </div>
                            </div>
                            <div style="border-top: 1px dashed var(--slate-200); padding-top: 10px;">
                                <div class="info-label" style="margin-bottom: 4px;">Alamat Pelapor</div>
                                <div class="info-value" style="font-size: 13px; color: var(--slate-700);">
                                    <?= !empty($data['alamat_pelapor']) ? nl2br(htmlspecialchars($data['alamat_pelapor'])) : '-'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<?php include "footer.php"; ?>