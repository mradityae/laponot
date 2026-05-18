<?php
include("../config/koneksi.php");

$id = $_GET['id_kedudukan'];
$b1 = $_GET['bulan_awal'];
$b2 = $_GET['bulan_akhir'];
$th = $_GET['tahun'];

// Ambil Nama Kedudukan untuk Judul PDF
$stmt_k = $koneksi->prepare("SELECT nama_kedudukan FROM kedudukan WHERE id_kedudukan = ?");
$stmt_k->execute([$id]);
$nama_kedudukan = $stmt_k->fetchColumn();

$sql = "SELECT n.nama, n.telepon,
        (SELECT COUNT(*) FROM laporan l WHERE l.id_notaris = n.id_notaris 
         AND YEAR(l.tanggal) = :th AND MONTH(l.tanggal) BETWEEN :b1 AND :b2) as cek
        FROM notaris n 
        WHERE n.id_kedudukan = :id AND n.level = '2' AND n.aktif='1'
        ORDER BY n.nama ASC";

$stmt = $koneksi->prepare($sql);
$stmt->execute([':id'=>$id, ':th'=>$th, ':b1'=>$b1, ':b2'=>$b2]);
$data = $stmt->fetchAll();
?>

<!-- Toolbar Modal -->
<div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <div style="display: flex; gap: 15px; align-items: center;">
        <!-- Filter Status -->
        <div>
            <label>Filter Status: </label>
            <select id="filterStatus" class="form-control input-sm" style="display:inline-block; width: 150px;">
                <option value="">Semua</option>
                <option value="SUDAH LAPOR">Sudah Lapor</option>
                <option value="BELUM LAPOR">Belum Lapor</option>
            </select>
        </div>
        
        <!-- Input Search Kustom -->
        <div>
            <label>Cari: </label>
            <input type="text" id="customSearch" class="form-control input-sm" placeholder="Ketik nama / telepon..." style="display:inline-block; width: 200px;">
        </div>
    </div>
    
    <a href="export_pdf_detail.php?id_kedudukan=<?=$id?>&bulan_awal=<?=$b1?>&bulan_akhir=<?=$b2?>&tahun=<?=$th?>" 
       target="_blank" class="btn btn-danger btn-sm">
        <i class="fa fa-file-pdf-o"></i> Export PDF
    </a>
</div>

<table class="table table-striped table-bordered" id="tablePop">
    <thead>
        <tr>
            <th>Nama Notaris</th>
            <th>Telepon</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $d){ ?>
        <tr>
            <td style="text-align:left"><?= $d['nama'] ?></td>
            <td><?= $d['telepon'] ?></td>
            <td>
                <?php if($d['cek'] > 0): ?>
                    <span class="label label-success">SUDAH LAPOR</span>
                    <span style="display:none;">SUDAH LAPOR</span> <!-- Untuk filter DT -->
                <?php else: ?>
                    <span class="label label-danger">BELUM LAPOR</span>
                    <span style="display:none;">BELUM LAPOR</span> <!-- Untuk filter DT -->
                <?php endif; ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    // Inisialisasi DataTables
    var table = $('#tablePop').DataTable({
        "dom": 'lrtip' // Sembunyikan search box default bawaan DT
    });

    // Logika Filter Status (Kolom indeks ke-2)
    $('#filterStatus').on('change', function(){
        table.column(2).search(this.value).draw();
    });

    // Logika Live Search Kustom (Global Search)
    $('#customSearch').on('keyup', function(){
        table.search(this.value).draw();
    });
</script>