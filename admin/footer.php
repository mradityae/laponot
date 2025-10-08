<div id="footer-sec">
        &copy; 2020 KANWIL KEMENKUMHAM JABAR 
</div>
    <!-- /. FOOTER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="<?=$url;?>assets/js/jquery.min.js"></script>
    <script src="<?=$url;?>assets/js/select2.full.min.js"></script>
    <script type='text/javascript'>
    $(function () {
      $("select").select2();
    });
    </script>
    <script type="text/javascript">
                        $(document).ready(function() {
                          $(".add-more").click(function(){ 
                            var html = $(".copy").html();
                            $(".after-add-more").after(html);
                          });
                          $("body").on("click",".remove",function(){ 
                            $(this).parents(".control-group").remove();
                          });
                        });
    </script>
    <script>
    $(document).ready(function(){
     $(document).on('click', '.add', function(){
      var html = '';
      html += '<tr>';
      html += '<td><select name="item_unit[]" class="form-control item_unit"><option value="">Select Unit</option><?php echo $output; ?></select></td>';
      html += '<td><button type="button" name="remove" class="btn btn-danger btn-sm remove"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
      $('#item_table').append(html);
     });
     
     $(document).on('click', '.remove', function(){
      $(this).closest('tr').remove();
     });
    });
    </script>
    
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="<?=$url;?>assets/js/bootstrap.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="<?=$url;?>assets/js/jquery.metisMenu.js"></script>
     <!-- CUSTOM SCRIPTS -->
    <script src="<?=$url;?>assets/js/custom.js"></script>
    <script src="<?=$url;?>assets/js/jquery.dataTables.min.js"></script>
    <script>
      $('form input[type=text], form textarea, form input[type=number]').on('change invalid', function() {
          var textfield = $(this).get(0);
          
          // hapus dulu pesan yang sudah ada
          textfield.setCustomValidity('');
          
          if (!textfield.validity.valid) {
            textfield.setCustomValidity('Form Tidak Boleh Kosong!');  
          }
        });
      $('table.data').DataTable({
      "ordering": true,
      "fixedHeader": true,
      "fixedColumns": false,
      "autoWidth": true,
      "Searching": true,
      "scrollY": 800,
      "scrollX": true,
      "responsive": true,
      "scrollCollapse": true      
    });
    
    $('#rekapTable').DataTable({
      scrollX: true,
      scrollY: 400,
      scrollCollapse: true,
      paging: true,
      searching: true,
      ordering: true,
      responsive: true,
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        zeroRecords: "Tidak ada data ditemukan",
        paginate: {
          first: "Awal",
          last: "Akhir",
          next: "Berikutnya",
          previous: "Sebelumnya"
        }
      }
    });

    $('#rekapTable-serverside').DataTable({
      processing: true,
      serverSide: true,
      deferRender: true,
      pageLength: 10,
      lengthMenu: [5,10,25,50,100],
      order: [[8,'desc']], 
      ajax: {
        url: '<?=$url;?>act/notaris_input_terbaru.php?id_kedudukan=<?= (int)$kedudukan ?>',
        type: 'POST'
      },
      columnDefs: [
        { targets: 0, orderable: false, searchable: false }
      ],
    });

    </script>
    <script type="text/javascript">
      function jam() {
      var time = new Date(),
          hours = time.getHours(),
          minutes = time.getMinutes(),
          seconds = time.getSeconds();
      document.querySelectorAll('.jam')[0].innerHTML = harold(hours) + ":" + harold(minutes) + ":" + harold(seconds);
        
      function harold(standIn) {
          if (standIn < 10) {
            standIn = '0' + standIn
          }
          return standIn;
          }
      }
      setInterval(jam, 1000);
  </script>
  <!--JS CUSTOM UPDATE-->
  <script type="text/javascript">
      var check = function() 
      {
        if(document.getElementById('psw').value == document.getElementById('pswulang').value) {
          document.getElementById('messagepwd').style.color = 'green';
          document.getElementById('messagepwd').innerHTML = 'Password dan Masukkan Kembali Password Sama';
          document.getElementById('sbmt').disabled = false;
        } else {
          document.getElementById('messagepwd').style.color = 'red'; 
          document.getElementById('messagepwd').innerHTML = 'Password dan Masukkan Kembali Password Tidak Sama';
          document.getElementById('sbmt').disabled = true;
        }
      }

      var check2 = function() 
      {
         document.getElementById('pswulang').value = '';
         document.getElementById('messagepwd').style.color = 'red'; 
         document.getElementById('messagepwd').innerHTML = 'Password dan Masukkan Kembali Password Tidak Sama';
         document.getElementById('sbmt').disabled = true;
      }

      var myInput = document.getElementById("psw");
      var letter = document.getElementById("letter");
      var capital = document.getElementById("capital");
      var number = document.getElementById("number");
      var length = document.getElementById("length");

      // When the user clicks on the password field, show the message box
      myInput.onfocus = function() 
      {
        document.getElementById("message").style.display = "block";
      }

      // When the user clicks outside of the password field, hide the message box
      myInput.onblur = function() 
      {
        document.getElementById("message").style.display = "none";
      }

      // When the user starts to type something inside the password field
      myInput.onkeyup = function() 
      {
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if(myInput.value.match(lowerCaseLetters)) {
          letter.classList.remove("invalid");
          letter.classList.add("valid");
        } else {
          letter.classList.remove("valid");
          letter.classList.add("invalid");
      }

      // Validate capital letters
      var upperCaseLetters = /[A-Z]/g;
      if(myInput.value.match(upperCaseLetters)) 
      {
        capital.classList.remove("invalid");
        capital.classList.add("valid");
      } else 
      {
        capital.classList.remove("valid");
        capital.classList.add("invalid");
      }

      // Validate numbers
      var numbers = /[0-9]/g;
      if(myInput.value.match(numbers)) 
      {
        number.classList.remove("invalid");
        number.classList.add("valid");
      } else 
      {
        number.classList.remove("valid");
        number.classList.add("invalid");
      }

        // Validate length
        if(myInput.value.length >= 6) {
          length.classList.remove("invalid");
          length.classList.add("valid");
        } else {
          length.classList.remove("valid");
          length.classList.add("invalid");
        }
      }

      function isNumberKey(evt)
      {
        var angka=(evt.which)?evt.which:event.keyCode
        if(angka>31 && (angka<48 || angka>57))
        
        return false;
        return true;
      }

      function konfirmasiKeluar()
      {
          var konfirmasi = confirm("Apakah Anda yakin akan keluar Aplikasi?"); 
          if (konfirmasi) {
              window.location ="<?=$url;?>login/login_prc.php?prc=3";
          }
      }

      function deletelaporan(idlaporan)
      {
          var konfirmasi = confirm("Apakah Anda yakin akan menghapus data ?");
          if (konfirmasi) {
              window.location ="<?=$url;?>act/hapus_laporan.php?idlaporan="+idlaporan;
          }
      }
        
      function deletenotaris(idnotaris)
      {
          var konfirmasi = confirm("Apakah Anda yakin akan menghapus data ?");
          if (konfirmasi) {
              window.location ="<?=$url;?>act/hapus_notaris.php?idnotaris="+idnotaris;
          }
      }

      function enableTanggal(idSelect){
      //If the checkbox has been checked
          if(idSelect.value == "Terverifikasi")
          {
              //Set the disabled property to FALSE and enable the button.
              document.getElementById("tanggal_pelantikan").disabled = false;
              document.getElementById("fileBalasan").disabled = false;
          } 
          else
          {
              //Otherwise, disable the submit button.
              document.getElementById("tanggal_pelantikan").disabled = true;
              document.getElementById("fileBalasan").disabled = true;
          }
      }
  </script>
</body>
</html>
