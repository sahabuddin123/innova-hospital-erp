<footer class="site-footer no-print">
    <div class="text-center">
        <?php echo date('Y'); ?> &copy;
        <?php
        $this->db->where('hospital_id', $this->hospital_id);
        $setting_row = $this->db->get('settings')->row();
        echo (!empty($setting_row) && !empty($setting_row->footer_message)) ? $setting_row->footer_message : 'By INNOVA Computers BD';
        ?>
        <a href="<?php echo current_url() . '#'; ?>" class="go-top">
            <i class="fa fa-angle-up"></i>
        </a>
    </div>
</footer>
<!--footer end-->
</section>

<?php

$language = $this->language;


if ($language == 'english') {
    $lang = 'en-ca';
    $langdate = 'en-CA';
} elseif ($language == 'spanish') {
    $lang = 'es';
    $langdate = 'es';
} elseif ($language == 'french') {
    $lang = 'fr';
    $langdate = 'fr';
} elseif ($language == 'portuguese') {
    $lang = 'pt';
    $langdate = 'pt';
} elseif ($language == 'arabic') {
    $lang = 'ar';
    $langdate = 'ar';
} elseif ($language == 'italian') {
    $lang = 'it';
    $langdate = 'it';
} elseif ($language == 'zh_cn') {
    $lang = 'zh-cn';
    $langdate = 'zh-CN';
} elseif ($language == 'japanese') {
    $lang = 'ja';
    $langdate = 'ja';
} elseif ($language == 'russian') {
    $lang = 'ru';
    $langdate = 'ru';
} elseif ($language == 'turkish') {
    $lang = 'tr';
    $langdate = 'tr';
} elseif ($language == 'indonesian') {
    $lang = 'id';
    $langdate = 'id';
} elseif ($language == 'bengali' || $language == 'bangla') {
    $lang = 'bn';
    $langdate = 'bn';
} else {
    $lang = 'en';
    $langdate = 'en';
}


?>

<script type="text/javascript">
    var langdate = "<?php echo $langdate; ?>";
    $(document).ready(function() {
        $('.readonly').keydown(function(e) {
            e.preventDefault();
        });

    })
</script>

<script type="text/javascript">
    var time_format = "<?php echo $this->settings->time_format ?>";
</script>


<script src="common/js/respond.min.js"></script>
<!-- <script type="text/javascript" src="common/assets/ckeditor/build/ckeditor.js"></script> -->
<script type="text/javascript" src="common/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script type="text/javascript" src="common/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script src="common/js/jquery.cookie.js"></script>
<!--common script for all pages-->
<script class="include" type="text/javascript" src="common/js/jquery.dcjqaccordion.2.7.js"></script>
<!--script for this page only-->
<script src="common/js/editable-table.js"></script>
<script src="common/js/bootstrap-select-country.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.26.1/axios.min.js"></script>


<script>
    if (typeof jQuery === 'undefined') {
        document.write('<script src="adminlte/plugins/jquery/jquery.min.js"><\/script>');
    }
</script>
<script src="adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>


<script src="common/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="common/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>


<?php
$dp_locale = "common/assets/bootstrap-datepicker/locales/bootstrap-datepicker." . $langdate . ".min.js";
if (!empty($langdate) && file_exists(FCPATH . $dp_locale)) {
?>
    <script src="<?php echo $dp_locale; ?>"></script>
<?php } ?>

<?php
$dtp_locale = "common/assets/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker." . $langdate . ".js";
$dtp_locale_min = "common/assets/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker." . $langdate . ".min.js";
if (!empty($langdate) && file_exists(FCPATH . $dtp_locale)) {
?>
    <script src="<?php echo $dtp_locale; ?>"></script>
<?php } elseif (!empty($langdate) && file_exists(FCPATH . $dtp_locale_min)) { ?>
    <script src="<?php echo $dtp_locale_min; ?>"></script>
<?php } ?>

<script src="common/js/advanced-form-components.js"></script>
<script src="common/js/common-scripts.js"></script>



<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script src="adminlte/plugins/moment/moment.min.js"></script>
<script src="adminlte/plugins/chart.js/Chart.min.js"></script>
<script src="adminlte/plugins/sparklines/sparkline.js"></script>
<?php if ($this->router->fetch_class() == 'home' && $this->router->fetch_method() == 'index') { ?>
<script src="adminlte/dist/js/pages/dashboard.js"></script>
<?php } ?>
<script src="adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="adminlte/plugins/jszip/jszip.min.js"></script>
<script src="adminlte/plugins/pdfmake/pdfmake.min.js"></script>
<script src="adminlte/plugins/pdfmake/vfs_fonts.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="adminlte/plugins/select2/js/select2.full.min.js"></script>
<script src="adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<script src="adminlte/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="adminlte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script type="text/javascript" src="common/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
<script type="text/javascript" src="common/assets/bootstrap-fileupload/bootstrap-fileupload.js"></script>
<script type="text/javascript" src="common/assets/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="common/assets/jquery-multi-select/js/jquery.quicksearch.js"></script>
<script src="common/js/lightbox.js"></script>
<script src="adminlte/plugins/fullcalendar/main.js"></script>
<?php
$fc_locale = "adminlte/plugins/fullcalendar/locales/" . $lang . ".js";
if (!empty($lang) && file_exists(FCPATH . $fc_locale)) {
?>
    <script src="<?php echo $fc_locale; ?>"></script>
<?php } ?>
<script src="adminlte/plugins/dropzone/min/dropzone.min.js"></script>
<!-- SweetAlert2 -->
<script src="adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="adminlte/plugins/toastr/toastr.min.js"></script>

<script src="adminlte/plugins/daterangepicker/daterangepicker.js"></script>

<script src="adminlte/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>



<script>
    $(document).ready(function() {
        "use strict";

        var calendarEl = document.getElementById('calendar');

        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: "<?php echo $lang; ?>",
                themeSystem: 'bootstrap',
                events: "appointment/getAppointmentByJason",
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay"
                },
                firstDay: 1,
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                eventContent: function(arg) {
                    var bgColor;
                    switch (arg.event.extendedProps.status) {
                        case 'Pending Confirmation':
                            bgColor = "linear-gradient(135deg, #5E35B1, #8E24AA)";
                            bgColor = '#6C5B7B';

                            bgColor = '#FFD54F';


                            break;
                        case 'Confirmed':
                            bgColor = "linear-gradient(160deg, #6C5B7B, #C06C84)";
                            bgColor = "#5E35B1";
                            break;
                        case 'Cancelled':
                            bgColor = "linear-gradient(145deg, #83a4d4, #b6fbff)";
                            bgColor = "#8B0000";
                            break;
                        case 'Requested':
                            bgColor = "#36b9cc";
                            break;
                        case 'Treated':
                            bgColor = "#858796";
                            break;
                        default:
                            bgColor = "#4e73df";
                    }
                    return {
                        html: `<div style="background: ${bgColor}; padding: 10px; font-size: 10px; border-radius: 5px; overflow: hidden; word-wrap: break-word; text-overflow: ellipsis; max-width: 100%;">
        <span style="color: white;">${arg.timeText}</span><br/>
        <span style="color: white;">${arg.event.title}</span>
    </div>`
                    };
                },



                eventClick: function(info) {
                    $("#medical_history").html("");
                    $("#loader").show();
                    if (info.event.id) {
                        $.ajax({
                            url: "patient/getMedicalHistoryByJason?id=" + info.event.id + "&from_where=calendar",
                            method: "GET",
                            dataType: "json",
                            success: function(response) {
                                "use strict";
                                $("#medical_history").html(response.view);
                                $("#loader").hide();
                            }
                        });
                    }

                    $("#cmodal").modal("show");
                },
                slotDuration: "00:05:00",
                businessHours: false,
                slotEventOverlap: false,
                editable: false,
                selectable: false,
                lazyFetching: true,
                initialView: "dayGridMonth", // default view
                timeZone: false
            });

            calendar.render();
        }
    });
</script>

<script src="common/extranal/js/footer.js?v=<?php echo time(); ?>"></script>





<script>
    Dropzone.autoDiscover = false

    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#template")
    if (previewNode) {
        previewNode.id = ""
        var previewTemplate = previewNode.parentNode.innerHTML
        previewNode.parentNode.removeChild(previewNode)

        var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
            url: "/target-url", // Set the url
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: "#previews", // Define the container to display the previews
            clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
        })

        myDropzone.on("addedfile", function(file) {
            // Hookup the start button
            file.previewElement.querySelector(".start").onclick = function() {
                myDropzone.enqueueFile(file)
            }
        })
    }
</script>



<script>
    $(".default-date-picker").datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
        todayHighlight: true,
        startDate: "01-01-1900",
        clearBtn: true,
        language: langdate,
    });
</script>


<?php if ($this->session->flashdata('swal_message')) { ?>
    <script>
        $(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            <?php
            if ($this->session->flashdata('swal_message')) { ?>
                Toast.fire({
                    icon: '<?= $this->session->flashdata('swal_type') ?>',
                    title: '<?= $this->session->flashdata('swal_title') ?> ',
                    text: '<?= $this->session->flashdata('swal_message') ?> ',
                });
            <?php } ?>
        });
    </script>
<?php } ?>

<?php
$this->session->unset_userdata('swal_message');
$this->session->unset_userdata('swal_type');
$this->session->unset_userdata('swal_title');
?>

<script>
    $('.collapse-server').on('click', function() {
        var sidebarCollapsed = $('body').hasClass('sidebar-collapse') ? 1 : 0;
        $.ajax({
            url: 'home/updateSidebarState',
            method: 'POST',
            data: {
                sidebar: sidebarCollapsed
            },
            success: function(response) {
                console.log('Sidebar state updated successfully.');
            },
            error: function(error) {
                console.error('Error updating sidebar state:', error);
            }
        });
    });
</script>

<!-- Auto-hide flash messages -->
<script>
    $(document).ready(function() {
        // Auto-hide flash messages after 5 seconds
        $('.alert').each(function() {
            const alert = $(this);
            setTimeout(function() {
                alert.alert('close');
            }, 5000);
        });
        
        // Also hide flash messages when clicking the close button
        $('.alert .close').on('click', function() {
            $(this).closest('.alert').alert('close');
        });
    });
</script>

<?php
// Clear flash messages after they've been displayed
if ($this->session->flashdata('success') || $this->session->flashdata('error') || $this->session->flashdata('warning') || $this->session->flashdata('debug') || $this->session->flashdata('info')) {
    $this->session->unset_userdata('success');
    $this->session->unset_userdata('error');
    $this->session->unset_userdata('warning');
    $this->session->unset_userdata('debug');
    $this->session->unset_userdata('info');
}
?>

<!-- load avaiable_js.php -->

<?php $this->load->view('available_js'); ?>




<script>
    $(document).ready(function() {
        $('#darkModeToggle').change(function() {
            if ($(this).is(':checked')) {
                $('body').toggleClass('dark-mode');
                $('.main-header').toggleClass('navbar-dark navbar-light');
                $('.main-sidebar').toggleClass('sidebar-dark-primary sidebar-light-primary');
                $('.custom-control-label i').toggleClass('fa-moon fa-sun');
            } else {
                $('body').toggleClass('dark-mode');
                $('.main-header').toggleClass('navbar-dark navbar-light');
                $('.main-sidebar').toggleClass('sidebar-dark-primary sidebar-light-primary');
                $('.custom-control-label i').toggleClass('fa-moon fa-sun');
            }
            drawChartTopServices();
            drawChartTopDiagnoses();
            drawChartBedOccupancy();
            drawChartTopTreatments();
            drawSalesExpenseChart();

            var darkModeValue = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: 'home/updateDarkMode',
                method: 'POST',
                data: {
                    darkMode: darkModeValue
                }
            });






        });
    });
</script>

<!--<script src="https://formai-gilt.vercel.app/bot.js" data-bot="demo-form-filler"></script>-->


<!--<script src="https://embed-translator.vercel.app/embed.js" data-site-key="hmz-site"></script>-->

<!-- <script src="https://formai-gilt.vercel.app/bot.js" data-bot="demo-form-filler"></script> -->

</body>

</html>