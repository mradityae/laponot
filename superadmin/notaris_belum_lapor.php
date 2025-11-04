<?php
include "header.php";
include("../config/koneksi.php");
include("../models/models.php");

// ambil filter kabupaten/kota (kedudukan)
$kedudukan = isset($_GET['kedudukan']) ? $_GET['kedudukan'] : 'Semua';

// fungsi buat link WA yang aman dan valid
function make_whatsapp_link($raw_phone) {
    // hapus semua karakter non-angka
    $digits = preg_replace('/\D+/', '', $raw_phone);

    if ($digits === '') return false;

    // jika mulai dengan 0 → ganti jadi 62
    if (preg_match('/^0/', $digits)) {
        $digits = preg_replace('/^0+/', '62', $digits);
    }

    // jika mulai dengan 8 → tambahkan 62 di depan
    if (preg_match('/^8\d{6,}$/', $digits)) {
        $digits = '62' . $digits;
    }

    // validasi panjang nomor (10–16 digit)
    $len = strlen($digits);
    if ($len < 10 || $len > 16) return false;

    // buat link WA resmi
    return "https://api.whatsapp.com/send?phone=" . $digits;
}
?>

<style>
    .page-head-line {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 25px;
    }

    .filter-form {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .table th {
        background-color: #2c3e50;
        color: #fff;
        text-align: center;
        vertical-align: middle !important;
        font-weight: 600;
    }

    .table td {
        vertical-align: middle !important;
        text-align: center;
    }

    .table tbody tr:hover {
        background-color: #f6f8fa;
    }

    .btn-primary {
        border-radius: 6px;
        padding: 6px 15px;
    }

    .wa-link {
        color: #25d366;
        font-weight: 600;
        text-decoration: none;
    }

    .wa-link:hover {
        color: #128c7e;
        text-decoration: underline;
    }

    .fa-whatsapp {
        margin-right: 5px;
    }
</style>

<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">
                    📋 Notaris Belum Lapor Jaminan Fidusia
                </h1>
            </div>
        </div>

        <!-- Filter by Kabupaten/Kota -->
        <form method="GET" class="form-inline filter-form">
            <div class="form-group">
                <label for="kedudukan"><b>Pilih Kabupaten/Kota:</b></label>
                <select name="kedudukan" id="kedudukan" class="form-control" style="margin-left:10px; width:250px;">
                    <option value="Semua">Semua Daerah</option>
                    <?php
                    // ambil daftar kedudukan
                    $stmt = $koneksi->prepare("SELECT id_kedudukan, nama_kedudukan FROM kedudukan ORDER BY nama_kedudukan ASC");
                    $stmt->execute();
                    while ($k = $stmt->fetch()) {
                        $selected = ($kedudukan == $k['id_kedudukan']) ? 'selected' : '';
                        echo "<option value='{$k['id_kedudukan']}' $selected>{$k['nama_kedudukan']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="margin-left:10px;">
                <i class="fa fa-search"></i> Tampilkan
            </button>
        </form>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover data" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 250px;">Nama Notaris</th>
                                <th style="width: 250px;">Email</th>
                                <th style="width: 180px;">Telepon (WA)</th>
                                <th style="width: 180px;">Kedudukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;

                            // query dinamis berdasar filter
                            if ($kedudukan == "Semua") {
                                $query = $koneksi->prepare("
                                    SELECT 
                                        n.nama AS nama_notaris,
                                        n.email,
                                        n.telepon,
                                        k.nama_kedudukan
                                    FROM notaris n
                                    JOIN kedudukan k ON n.id_kedudukan = k.id_kedudukan
                                    LEFT JOIN laporan_entitas l ON n.id_notaris = l.id_notaris
                                    WHERE l.id_notaris IS NULL 
                                      AND n.aktif = '1' 
                                      AND n.level = '2'
                                    ORDER BY k.nama_kedudukan, n.nama
                                ");
                            } else {
                                $query = $koneksi->prepare("
                                    SELECT 
                                        n.nama AS nama_notaris,
                                        n.email,
                                        n.telepon,
                                        k.nama_kedudukan
                                    FROM notaris n
                                    JOIN kedudukan k ON n.id_kedudukan = k.id_kedudukan
                                    LEFT JOIN laporan_entitas l ON n.id_notaris = l.id_notaris
                                    WHERE l.id_notaris IS NULL 
                                      AND n.aktif = '1' 
                                      AND n.level = '2'
                                      AND n.id_kedudukan = :kedudukan
                                    ORDER BY n.nama
                                ");
                                $query->bindParam(":kedudukan", $kedudukan, PDO::PARAM_INT);
                            }

                            $query->execute();

                            if ($query->rowCount() > 0) {
                                while ($row = $query->fetch()) {
                                    $raw_phone = $row['telepon'];
                                    $wa_link = make_whatsapp_link($raw_phone);

                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td style='text-align:left;'>" . htmlspecialchars($row['nama_notaris']) . "</td>";
                                    echo "<td style='text-align:left;'>" . htmlspecialchars($row['email']) . "</td>";

                                    // tampilkan link WA atau tanda invalid
                                    if ($wa_link) {
                                        echo "<td>
                                                <a href='#' class='wa-link' 
                                                   onclick=\"window.open('{$wa_link}','_blank'); return false;\">
                                                    <i class='fa fa-whatsapp'></i>" . htmlspecialchars($raw_phone) . "
                                                </a>
                                              </td>";
                                    } else {
                                        echo "<td><small class='text-muted'>No WA tidak valid</small></td>";
                                    }

                                    echo "<td>" . htmlspecialchars($row['nama_kedudukan']) . "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5' align='center'><i>Tidak ada notaris yang belum lapor untuk wilayah ini.</i></td></tr>";
                            }

                            $koneksi = null;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>
