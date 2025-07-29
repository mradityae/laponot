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
		</script>
    
<script type="text/javascript">
    document.getElementById("preview").style.display = "none";
    
    function readURL(input) {
        if (input.files && input.files[0]){
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);

            document.getElementById("preview").style.display = "block";
        }
      }
    function terms_changed(termsCheckBox){
	    //If the checkbox has been checked
	    if(termsCheckBox.checked){
	        //Set the disabled property to FALSE and enable the button.
	        document.getElementById("submit").disabled = false;
	    }else{
	        //Otherwise, disable the submit button.
	        document.getElementById("submit").disabled = true;
	    }
	}	

     function konfirmasiKeluar()
      {
          var konfirmasi = confirm("Apakah Anda yakin akan keluar Aplikasi?"); 
          if (konfirmasi) {
              window.location ="<?=$url;?>login/login_prc.php?prc=3";
          }
      }

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

    var check = function() 
      {
        if(document.getElementById('psw').value == document.getElementById('pswulang').value) {
          document.getElementById('messagepwd').style.color = 'green';
          document.getElementById('messagepwd').innerHTML = 'Password Sama';
          document.getElementById('sbmt').disabled = false;
        } else {
          document.getElementById('messagepwd').style.color = 'red'; 
          document.getElementById('messagepwd').innerHTML = 'Password Sama';
          document.getElementById('sbmt').disabled = true;
        }
      }

      var check2 = function() 
      {
         document.getElementById('pswulang').value = '';
         document.getElementById('messagepwd').style.color = 'red'; 
         document.getElementById('messagepwd').innerHTML = 'Password Tidak Sama';
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
</script>

</body>
</html>
