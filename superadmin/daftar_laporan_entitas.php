<?php
include "header.php";
include "../config/koneksi.php";
?>

<div id="page-wrapper">
  <div id="page-inner">
    <div class="row">
      <div class="col-md-12">
        <h1 class="page-head-line" align="center">DAFTAR LAPORAN FIDUSIA</h1>
      </div>
    </div>

    <form id="filterForm" method="post">
      <div class="form-group">
        <label>Dari Tanggal</label>
        <input type="date" name="tgl_a" class="form-control" value="2025-01-01">
      </div>
      <div class="form-group">
        <label>Sampai Tanggal</label>
        <input type="date" name="tgl_b" class="form-control" value="<?= date('Y-m-d') ?>">
      </div>
      <div class="form-group">
        <label>Kedudukan</label>
        <select name="id_kedudukan" class="form-control">
          <option value="">-- Semua Kedudukan --</option>
          <?php
          $res = $koneksi->query("SELECT * FROM kedudukan ORDER BY nama_kedudukan ASC");
          while($r = $res->fetch()) {
              echo "<option value='{$r['id_kedudukan']}'>{$r['nama_kedudukan']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label>Nama Notaris</label>
        <select name="id_notaris" class="form-control">
          <option value="">-- Semua Notaris --</option>
          <?php
          $res = $koneksi->query("SELECT id_notaris, nama FROM notaris WHERE level='2' AND aktif='1' ORDER BY nama ASC");
          while($r = $res->fetch()) {
              echo "<option value='{$r['id_notaris']}'>{$r['nama']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label>Jenis Transaksi</label>
        <select name="jenis_transaksi" class="form-control">
          <option value="">-- Semua --</option>
          <option value="Pendaftaran">Pendaftaran</option>
          <option value="Perubahan">Perubahan</option>
          <option value="Penghapusan">Penghapusan</option>
          <option value="Pembatalan">Pembatalan</option>
        </select>
      </div>
      <button type="submit" class="btn btn-success">Tampilkan</button>
    </form>

    <hr>

    <table id="daftarFidusia" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Notaris</th>
          <th>Pemberi</th>
          <th>Penerima</th>
          <th>Jenis Transaksi</th>
          <th>Nilai Penjaminan</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

  </div>
</div>

<?php include "footer.php"; ?>
