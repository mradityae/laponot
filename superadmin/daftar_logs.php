<?php
include "header.php";

$logDir = "../logs";
$files = [];

if (is_dir($logDir)) {
    $files = array_diff(scandir($logDir, SCANDIR_SORT_DESCENDING), ['.', '..']);
}
?>

<div id="page-wrapper">
    <div id="page-inner">

        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">Daftar File Log</h1>
            </div>
        </div>

        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">

                    <a href="index.php" class="btn btn-primary">
                        KEMBALI
                    </a>

                    <br><br>

                    <table class="table table-hover table-striped table-bordered data">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama File</th>
                                <th>Ukuran</th>
                                <th>Terakhir Diubah</th>
                                <th width="10%">Download</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php
                        $no = 1;

                        foreach ($files as $file)
                        {
                            $fullPath = $logDir . "/" . $file;

                            if (!is_file($fullPath)) {
                                continue;
                            }

                            $size = filesize($fullPath);
                            $modified = date('d-m-Y H:i:s', filemtime($fullPath));

                            echo "<tr>";
                            echo "<td>".$no."</td>";
                            echo "<td>".$file."</td>";
                            echo "<td>".number_format($size / 1024, 2)." KB</td>";
                            echo "<td>".$modified."</td>";

                            echo "<td align='center'>
                                <a href='download_log.php?file=".urlencode($file)."'
                                class='btn btn-success btn-xs'
                                title='Download Log'>
                                    <span class='glyphicon glyphicon-download-alt'></span>
                                </a>
                            </td>";

                            echo "</tr>";

                            $no++;
                        }

                        if ($no == 1)
                        {
                            echo "<tr>";
                            echo "<td colspan='5' align='center'>Tidak ada file log</td>";
                            echo "</tr>";
                        }
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