<?php
include("../config/koneksi.php");

$id_laporan = $_GET['id'] ?? null;

if ($id_laporan) {
    $stmt = $koneksi->prepare("DELETE FROM laporan_entitas WHERE id_laporan = :id_laporan");
    $stmt->bindParam(':id_laporan', $id_laporan);
    $stmt->execute();

    echo "<script>alert('Laporan berhasil dihapus'); window.location.href='daftar_laporan_entitas.php';</script>";
} else {
    echo "ID tidak ditemukan.";
}
?>