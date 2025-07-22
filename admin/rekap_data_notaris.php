<?php
include "header.php";
?>

  <!-- /. NAV SIDE  -->
<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line" align="center">REKAP DATA NOTARIS</h1>
            </div>
        </div>
        <body style="background-color: #E2E2E2;">    
          <div class="panel panel-default">
          <form action="<?=$url;?>act/rekap_datanotaris_proses.php" method="post" target="_blank" required>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label" for="tgl_b">Status</label>
                        <select name="status" class="form-control">
                            <option value="Semua" selected>Semua</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>   
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success" name="submit" value="Cetak">
                    <button type="reset" class="btn btn-danger">Reset</button>                    
                </div>
            </form>              
        </div>
    </div>
</div>
<?php
include "footer.php";
?>