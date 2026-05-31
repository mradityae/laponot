<?php
include "header.php";
include "../config/koneksi.php";

$id_perkara = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = $koneksi->prepare("
    SELECT p.*, 
           n.nama as nama_notaris,
           k.nama_kedudukan
    FROM perkara_mpw p
    LEFT JOIN notaris n ON p.id_notaris = n.id_notaris
    LEFT JOIN kedudukan k ON p.id_kedudukan = k.id_kedudukan
    WHERE p.id_perkara = ?
    LIMIT 1
");

$query->execute([$id_perkara]);
$data = $query->fetch(PDO::FETCH_ASSOC);

if(!$data){
    echo "<script>alert('Data perkara tidak ditemukan'); window.close();</script>";
    exit;
}

$nama_terlapor = ($data['jenis_terlapor'] == 'database')
    ? $data['nama_notaris']
    : $data['nama_terlapor_manual'];

// Basis URL jika berkas disimpan di direktori berbeda (kosongkan jika relatif)
$baseUrl = $url ?? '';

// Cek ketersediaan dokumen fisik/PDF
$has_documents = !empty($data['surat_pengaduan']) || !empty($data['ba_pemeriksaan']) || 
                 !empty($data['laporan_hasil_pemeriksaan']) || !empty($data['sk_majelis_pemeriksa']) || 
                 !empty($data['surat_pemanggilan']) || !empty($data['rekomendasi']);

// Cek ketersediaan link data dukung cloud
$has_links = !empty($data['data_dukung_link']) || !empty($data['data_dukung_tambahan']);
?>

<!-- Penambahan Style Modern Master Slate & Blue secara Inline untuk konsistensi -->
<style>
    .panel-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .panel-custom .panel-heading-custom {
        background: #1e293b;
        color: #ffffff;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.5px;
        border-top-left-radius: 7px;
        border-top-right-radius: 7px;
    }
    .panel-custom .panel-heading-primary {
        background: #2563eb;
    }
    .panel-custom .panel-body {
        padding: 20px;
    }
    .table-detail th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
    }
    .table-detail td {
        color: #1e293b;
    }
    .doc-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    .doc-list-item:last-child {
        margin-bottom: 0;
    }
    .doc-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        color: #334155;
    }
    .btn-view-doc {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        font-weight: 600;
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-view-doc:hover {
        background-color: #dbeafe;
        color: #1d4ed8;
    }
    .btn-cloud-doc {
        background-color: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    .btn-cloud-doc:hover {
        background-color: #dcfce7;
        color: #15803d;
    }
</style>

<div id="page-wrapper">
    <div id="page-inner">

        <div class="row">
            <div class="col-md-12">
                <h2 style="color: #1e293b; font-weight: 700;">VERIFIKASI PERKARA MPW</h2>
                <hr style="border-top: 2px solid #e2e8f0;">
            </div>
        </div>

        <div class="row">

            <!-- SISI KIRI: DETAIL PERKARA & BERKAS UNGGAHAN -->
            <div class="col-md-6">

                <!-- PANEL DETAIL PERKARA -->
                <div class="panel-custom">
                    <div class="panel-heading-custom">
                        <i class="fa fa-info-circle"></i> DETAIL DATA PERKARA
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-detail" style="margin-bottom: 0;">
                            <tr>
                                <th width="35%">Nomor Register</th>
                                <td style="font-weight: bold;">
                                    <?= !empty($data['nomor_register']) ? htmlspecialchars($data['nomor_register']) : '<span class="text-muted">- Belum Diregistrasi -</span>'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Judul Perkara</th>
                                <td><?= htmlspecialchars($data['judul']); ?></td>
                            </tr>
                            <tr>
                                <th>Pihak Terlapor</th>
                                <td>
                                    <strong><?= htmlspecialchars($nama_terlapor); ?></strong>
                                    <?php if(!empty($data['nama_kedudukan'])): ?>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;"><i class="fa fa-map-marker"></i> Kedudukan: <?= htmlspecialchars($data['nama_kedudukan']); ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Pihak Pelapor</th>
                                <td><?= htmlspecialchars($data['nama_pelapor']); ?></td>
                            </tr>
                            <tr>
                                <th>Status Alur</th>
                                <td>
                                    <span class="label label-info" style="padding: 5px 10px; font-size: 11px;">
                                        <?= htmlspecialchars($data['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Waktu Masuk</th>
                                <td style="font-size: 12px; color: #475569;">
                                    <i class="fa fa-calendar"></i> <?= date('d M Y H:i', strtotime($data['created_at'])); ?> WIB
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- PANEL FILE DAN BERKAS UNGGAHAN -->
                <div class="panel-custom">
                    <div class="panel-heading-custom" style="background: #475569;">
                        <i class="fa fa-folder-open"></i> BERKAS DAN DATA DUKUNG PERKARA
                    </div>
                    <div class="panel-body">
                        
                        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Dokumen Fisik (PDF)</h4>
                        <div style="margin-bottom: 20px;">
                            <?php if($has_documents): ?>
                                
                                <?php if(!empty($data['surat_pengaduan'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-file-pdf-o text-danger"></i> Surat Pengaduan</div>
                                    <a href="<?= $baseUrl . $data['surat_pengaduan']; ?>" target="_blank" class="btn-view-doc">Lihat File</a>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($data['sk_majelis_pemeriksa'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-file-pdf-o text-danger"></i> SK Majelis Pemeriksa</div>
                                    <a href="<?= $baseUrl . $data['sk_majelis_pemeriksa']; ?>" target="_blank" class="btn-view-doc">Lihat File</a>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($data['surat_pemanggilan'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-file-pdf-o text-danger"></i> Surat Pemanggilan</div>
                                    <a href="<?= $baseUrl . $data['surat_pemanggilan']; ?>" target="_blank" class="btn-view-doc">Lihat File</a>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($data['ba_pemeriksaan'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-file-pdf-o text-danger"></i> Berita Acara (BA) Pemeriksaan</div>
                                    <a href="<?= $baseUrl . $data['ba_pemeriksaan']; ?>" target="_blank" class="btn-view-doc">Lihat File</a>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($data['laporan_hasil_pemeriksaan'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-file-pdf-o text-danger"></i> Laporan Hasil Pemeriksaan (LHP)</div>
                                    <a href="<?= $baseUrl . $data['laporan_hasil_pemeriksaan']; ?>" target="_blank" class="btn-view-doc">Lihat File</a>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($data['rekomendasi'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-file-pdf-o text-danger"></i> Rekomendasi</div>
                                    <a href="<?= $baseUrl . $data['rekomendasi']; ?>" target="_blank" class="btn-view-doc">Lihat File</a>
                                </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div style="color: #94a3b8; font-style: italic; font-size: 13px; padding: 10px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px;">
                                    <i class="fa fa-info-circle"></i> Tidak ada dokumen fisik PDF yang diunggah.
                                </div>
                            <?php endif; ?>
                        </div>

                        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Link Eksternal Cloud Storage</h4>
                        <div>
                            <?php if($has_links): ?>
                                
                                <?php if(!empty($data['data_dukung_link'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-external-link text-success"></i> Data Dukung Utama</div>
                                    <a href="<?= $data['data_dukung_link']; ?>" target="_blank" class="btn-view-doc btn-cloud-doc">Buka Link</a>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($data['data_dukung_tambahan'])): ?>
                                <div class="doc-list-item">
                                    <div class="doc-info"><i class="fa fa-external-link text-success"></i> Data Dukung Tambahan</div>
                                    <a href="<?= $data['data_dukung_tambahan']; ?>" target="_blank" class="btn-view-doc btn-cloud-doc">Buka Link</a>
                                </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div style="color: #94a3b8; font-style: italic; font-size: 13px; padding: 10px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px;">
                                    <i class="fa fa-info-circle"></i> Tidak ada tautan cloud storage eksternal.
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>

            <!-- SISI KANAN: FORM VERIFIKASI -->
            <div class="col-md-6">

                <div class="panel-custom">
                    <div class="panel-heading-custom panel-heading-primary">
                        <i class="fa fa-check-square-o"></i> FORM VERIFIKASI MAJELIS PENGAWAS WILAYAH
                    </div>

                    <div class="panel-body">

                        <form action="../act/verifikasi-perkara_proses.php" method="POST">

                            <input type="hidden" name="id_perkara" value="<?= $data['id_perkara']; ?>">

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="color: #334155; font-weight: 600; margin-bottom: 8px;">Status Hasil Verifikasi</label>
                                <select name="verifikasi" class="form-control" style="height: 42px; border-radius: 6px; font-weight: 500;" required>
                                    <option value="">-- PILIH STATUS VERIFIKASI --</option>
                                    <option value="Terverifikasi" <?= ($data['verifikasi'] == 'Terverifikasi') ? 'selected' : ''; ?>>
                                        ✓ Terverifikasi (Berkas Lengkap & Valid)
                                    </option>
                                    <option value="Tidak Terverifikasi" <?= ($data['verifikasi'] == 'Tidak Terverifikasi') ? 'selected' : ''; ?>>
                                        ✗ Tidak Terverifikasi (Ditolak / Butuh Perbaikan)
                                    </option>
                                </select>
                            </div>

                            <div class="form-group" style="margin-bottom: 25px;">
                                <label style="color: #334155; font-weight: 600; margin-bottom: 8px;">Catatan Pemeriksaan Verifikasi</label>
                                <textarea 
                                    name="catatan_verifikasi" 
                                    class="form-control" 
                                    rows="8" 
                                    style="border-radius: 6px; resize: vertical; padding: 12px;"
                                    placeholder="Tuliskan catatan detail mengenai keabsahan dokumen, kekurangan berkas, atau alasan penolakan di sini..."><?= htmlspecialchars($data['catatan_verifikasi'] ?? ''); ?></textarea>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 600; border-radius: 6px; background-color: #2563eb; border: none; flex: 1;">
                                    <i class="fa fa-save"></i> Simpan Hasil Verifikasi
                                </button>
                                
                                <button type="button" onclick="window.close();" class="btn btn-default" style="padding: 10px 20px; font-weight: 600; border-radius: 6px; background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                    Tutup Halaman
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<?php include "footer.php"; ?>