<div id="footer-sec">
    &copy; 2020 KANWIL KEMENKUMHAM JABAR
</div>

<!-- SCRIPTS -->
<script src="<?=$url;?>assets/js/jquery.min.js"></script>
<script src="<?=$url;?>assets/js/select2.full.min.js"></script>
<script>
    $(function () {
        $("select").select2();

        $(document).on('click', '.add-more', function () {
            let html = $(".copy").html();
            $(".after-add-more").after(html);
        });

        $(document).on('click', '.remove', function () {
            $(this).closest(".control-group").remove();
        });

        $(document).on('click', '.add', function () {
            let html = `<tr>
                            <td>
                                <select name="item_unit[]" class="form-control item_unit">
                                    <option value="">Select Unit</option>
                                    <?php echo $output; ?>
                                </select>
                            </td>
                            <td>
                                <button type="button" name="remove" class="btn btn-danger btn-sm remove">
                                    <span class="glyphicon glyphicon-minus"></span>
                                </button>
                            </td>
                        </tr>`;
            $('#item_table').append(html);
        });

        $(document).on('click', '.remove', function () {
            $(this).closest('tr').remove();
        });

        $('form input[type=text], form textarea, form input[type=number]').on('change invalid', function () {
            const textfield = this;
            textfield.setCustomValidity('');
            if (!textfield.validity.valid) {
                textfield.setCustomValidity('Form Tidak Boleh Kosong!');
            }
        });

        $('table.data').DataTable({
            ordering: true,
            fixedHeader: true,
            autoWidth: true,
            searching: true,
            scrollY: 800,
            scrollX: true,
            responsive: true,
            scrollCollapse: true
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
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
                $('#preview').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function terms_changed(checkbox) {
        document.getElementById("submit").disabled = !checkbox.checked;
    }

    function konfirmasiKeluar() {
        if (confirm("Apakah Anda yakin akan keluar Aplikasi?")) {
            window.location = "<?=$url;?>login/login_prc.php?prc=3";
        }
    }

    function jam() {
        const time = new Date();
        const format = n => n < 10 ? '0' + n : n;
        const waktu = `${format(time.getHours())}:${format(time.getMinutes())}:${format(time.getSeconds())}`;
        document.querySelector('.jam').innerText = waktu;
    }
    setInterval(jam, 1000);

    const check = () => {
        const pwd = document.getElementById('psw');
        const ulang = document.getElementById('pswulang');
        const msg = document.getElementById('messagepwd');
        const btn = document.getElementById('sbmt');

        if (pwd.value === ulang.value) {
            msg.style.color = 'green';
            msg.innerHTML = 'Password Sama';
            btn.disabled = false;
        } else {
            msg.style.color = 'red';
            msg.innerHTML = 'Password Tidak Sama';
            btn.disabled = true;
        }
    };

    const check2 = () => {
        document.getElementById('pswulang').value = '';
        const msg = document.getElementById('messagepwd');
        msg.style.color = 'red';
        msg.innerHTML = 'Password Tidak Sama';
        document.getElementById('sbmt').disabled = true;
    };

    const myInput = document.getElementById("psw");
    const validations = {
        letter: /[a-z]/g,
        capital: /[A-Z]/g,
        number: /[0-9]/g,
        length: /.{6,}/g
    };

    myInput.onfocus = () => document.getElementById("message").style.display = "block";
    myInput.onblur = () => document.getElementById("message").style.display = "none";
    myInput.onkeyup = () => {
        for (const key in validations) {
            const el = document.getElementById(key);
            if (myInput.value.match(validations[key])) {
                el.classList.add("valid");
                el.classList.remove("invalid");
            } else {
                el.classList.add("invalid");
                el.classList.remove("valid");
            }
        }
    };

    function isNumberKey(evt) {
        const code = evt.which || evt.keyCode;
        return !(code > 31 && (code < 48 || code > 57));
    }
</script>

<script src="<?=$url;?>assets/js/bootstrap.js"></script>
<script src="<?=$url;?>assets/js/jquery.metisMenu.js"></script>
<script src="<?=$url;?>assets/js/custom.js"></script>
<script src="<?=$url;?>assets/js/jquery.dataTables.min.js"></script>
</body>
</html>
