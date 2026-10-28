<!DOCTYPE html>
<html lang="en" <?php
                if (!$this->ion_auth->in_group(array('superadmin'))) {

                    $this->db->where('hospital_id', $this->hospital_id);
                    $settings_lang = $this->db->get('settings')->row()->language;
                    if ($this->language == 'arabic') {
                ?> dir="rtl" <?php } else { ?> dir="ltr" <?php
                                                        }
                                                    } else {
                                                        $this->db->where('hospital_id', 'superadmin');
                                                        $settings_lang = $this->db->get('settings')->row()->language;
                                                        if ($this->language == 'arabic') {
                                                            ?> dir="rtl" <?php } else { ?> dir="ltr" <?php
                                                                                                    }
                                                                                                }
                                                                                                        ?>>

<head>
    <base href="<?php echo base_url(); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $class_name = $this->router->fetch_class();
    $class_name_lang = lang($class_name);
    if (empty($class_name_lang)) {
        $class_name_lang = $class_name;
    }
    ?>

    <title> <?php echo $class_name_lang; ?> |
        <?php
        if ($this->ion_auth->in_group(array('superadmin'))) {
            $this->db->where('hospital_id', 'superadmin');
        } else {
            $this->db->where('hospital_id', $this->hospital_id);
        }
        ?>
        <?php
        $settings = $this->db->get('settings')->row();
        echo $settings->system_vendor;
        ?>
    </title>

    <!-- <link rel="stylesheet" href="common/css/bootstrap-select.min.css"> -->

    <!-- Google Fonts -->

    <!-- design the sidebar with more professional css  -->



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="adminlte/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="adminlte/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="adminlte/plugins/summernote/summernote-bs4.min.css">


    <!-- <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css"> -->
    <link rel="stylesheet" href="adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">


    <link rel="stylesheet" href="adminlte/dist/css/changes.css">

    <link rel="stylesheet" href="adminlte/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="adminlte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" href="adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="adminlte/plugins/daterangepicker/daterangepicker.css">


    <link rel="stylesheet" href="adminlte/plugins/fullcalendar/main.css">
    <link rel="stylesheet" href="adminlte/plugins/flag-icon-css/css/flag-icon.min.css">

    <link rel="stylesheet" href="common/assets/bootstrap-datepicker/css/bootstrap-datepicker.css" />
    <link rel="stylesheet" type="text/css" href="common/assets/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="common/assets/bootstrap-datetimepicker/css/datetimepicker.css" />
    <link rel="stylesheet" href="common/assets/bootstrap-timepicker/compiled/timepicker.css" />
    <link rel="stylesheet" type="text/css" href="common/assets/jquery-multi-select/css/multi-select.css" />
    <link rel="stylesheet" type="text/css" href="common/css/lightbox.css" />


    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="adminlte/plugins/toastr/toastr.min.css">

    <!-- dropzonejs -->
    <link rel="stylesheet" href="adminlte/plugins/dropzone/min/dropzone.min.css">



    <?php

    if ($this->language == 'arabic') { ?>
        <link rel="stylesheet" href="adminlte/dist/css/rtl.css">
    <?php } ?>

    <!-- <link rel="stylesheet" href="common/css/bootstrap-select-country.min.css"> -->



</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">




        <div class="">
            <div class="card">

                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row"> <!-- Added row class here -->
                        <form role="form" action="api/addCase" class="clearfix w-100" method="post" enctype="multipart/form-data">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('date'); ?>  <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date"  value="<?php if ($case->id) {
                                                                                                     echo date('d-m-Y', $case->date);
                                                                                                 } ?>">
                                </div>

                                 <input type="hidden" name="patient_id" id="patient_id" value="<?php if ($case->patient_id) {
                                                                                                     echo $case->patient_id;
                                                                                                 } elseif ($patient_id) {
                                                                                                     echo $patient_id;
                                                                                                 } ?>">
 <?php if (!empty($case->id)) { ?>
<input type="hidden" name="hospital_id" id="hospitalId"  value="<?php echo $case->hospital_id; ?>">
<?php } else{ ?>
<input type="hidden" name="hospital_id" id="hospitalId"  value="<?php echo $hospital_id; ?>">
<?php } ?>
                                <?php if (!empty($case->id)) { ?>
                                    <div class="form-group col-md-12">
                                        <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang(''); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="" value="<?php echo $case->patient_name; ?>(ID: <?php echo $case->patient_id; ?>)" placeholder="" readonly>
                                    </div>
                                <?php } ?>
                                <div class="form-group col-md-12">
                                    <label for="exampleInputEmail1"> <?php echo lang('case'); ?> <?php echo lang('title'); ?> &ast;</label>
                                    <input type="text" class="form-control form-control-lg" name="title" value="<?php echo $case->title; ?>" placeholder="" required="">
                                </div>
                                <div class="form-group col-md-12">
    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('symptom'); ?></label>
    <div class="input-group">
        <select class="form-control form-control-lg shadow-sm" multiple id="symptomchoose" name="symptom_id[]">
           <?php 
if (!empty($selected_symptoms)) { 
    foreach ($selected_symptoms as $symptom) { ?>
        <option value="<?php echo $symptom['id']; ?>" selected>
            <?php echo $symptom['name']; ?>
        </option>
    <?php } 
} ?>



        </select>
        <div class="input-group-append">
            <a data-toggle="modal" href="#mySymptomModal" class="btn btn-success"><i class="fa fa-plus"></i></a>
        </div>
    </div>
</div>


                                <div class="form-group col-md-12">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('lab_test'); ?></label>
                                    <select class="form-control form-control-lg shadow-sm" multiple id="testchoose" name="test_id[]">
                                        <?php 
if (!empty($selected_labtest)) { 
    foreach ($selected_labtest as $test) { ?>
        <option value="<?php echo $test['id']; ?>" selected>
            <?php echo $test['category']; ?> <!-- Display lab test category -->
        </option>
    <?php } 
} ?>

                                    </select>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('diagnosis'); ?></label>
                                    <div class="input-group">
                                        <select class="form-control form-control-lg shadow-sm" multiple id="diagnosischoose" name="diagnosis_id[]">
                                            <?php 
if (!empty($selected_diagnosis)) { 
    foreach ($selected_diagnosis as $diagnosis) { ?>
        <option value="<?php echo $diagnosis['id']; ?>" selected>
            <?php echo $diagnosis['name']; ?> <!-- Display diagnosis name -->
        </option>
    <?php } 
} ?>

                                        </select>
                                        <div class="input-group-append">
                                            <a data-toggle="modal" href="#myDiagnosisModal" class="btn btn-warning"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('treatment'); ?></label>
                                    <div class="input-group">
                                        <select class="form-control form-control-lg shadow-sm" multiple id="treatmentchoose" name="treatment_id[]">
                                           <?php 
if (!empty($selected_treatment)) { 
    foreach ($selected_treatment as $treatment) { ?>
        <option value="<?php echo $treatment['id']; ?>" selected>
            <?php echo $treatment['name']; ?> <!-- Display treatment name -->
        </option>
    <?php } 
} ?>

                                        </select>
                                        <div class="input-group-append">
                                            <a data-toggle="modal" href="#myTreatmentModal" class="btn btn-success"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('advice'); ?></label>
                                    <div class="input-group">
                                        <select class="form-control form-control-lg shadow-sm" multiple id="advicechoose" name="advice_id[]">
                                            <?php 
if (!empty($selected_advice)) { 
    foreach ($selected_advice as $advice) { ?>
        <option value="<?php echo $advice['id']; ?>" selected>
            <?php echo $advice['name']; ?> <!-- Display advice name -->
        </option>
    <?php } 
} ?>

                                        </select>
                                        <div class="input-group-append">
                                            <a data-toggle="modal" href="#myAdviceModal" class="btn btn-success"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12 no-print">
                                    <label><?php echo lang('case'); ?> &ast;</label>
                                    <textarea class="form-control ckeditor" name="description" id="editor1" rows="10"><?php echo $case->description ?? ''; ?></textarea>
                                </div>
                                <input type="hidden" name="redirect" value='patient/caseList'>
                                <input type="hidden" name="case_id" value='<?php if (!empty($case->id)) {
                                                                                echo $case->id;
                                                                            } ?>'>
                                <section class="col-md-12 no-print">
                                    <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                                </section>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>

    </div>
    
    
    <div class="modal fade no-print" id="mySymptomModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> <?php echo lang('add_new_symptom'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body row">

                <div class="form-row">

                    <div class="form-group col-md-12">
                        <label for="exampleInputEmail1" class="col-sm-4"><?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-inline input-medium" id="name" name="name" value='' placeholder="" required>
                    </div>
                    <section class="col-md-12">
                        <div id="addSymptom" class="btn btn-info submit_button float-right">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="button-text"><?php echo lang('submit'); ?></span>
                        </div>
                    </section>

                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal fade no-print" id="myTestModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> <?php echo lang('add_new_test'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body row">

                <div class="form-row">

                    <div class="form-group col-md-12">
                        <label for="exampleInputEmail1" class="col-sm-4"><?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-inline input-medium" id="category" name="category" value='' placeholder="" required>
                    </div>
                    <input type="hidden" name="type" id="type" value="diagnostic">
                    <section class="col-md-12">
                        <div id="addTest" class="btn btn-info submit_button float-right">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="button-text"><?php echo lang('submit'); ?></span>
                        </div>
                    </section>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal fade no-print" id="myDiagnosisModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> <?php echo lang('add_new_diagnosis'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="form-group d-flex">
                    <label for="exampleInputEmail1" class="col-sm-3"><?php echo lang('disease'); ?> <?php echo lang('name'); ?> </label>
                    <input type="text" class="form-control col-sm-9" name="name" id="name2" value='' placeholder="" required>
                </div>
                <div class="form-group d-flex">
                    <label for="exampleInputEmail1" class="col-sm-3"><?php echo lang('icd 10'); ?> <?php echo lang('code'); ?></label>
                    <input type="text" class="form-control col-sm-9" name="code" id="code" value='' placeholder="" required>
                </div>
                <div class="form-group d-flex">
                    <label for="exampleInputEmail1" class="col-sm-3"><?php echo lang('description'); ?> </label>
                    <textarea class="form-control col-sm-9 ckeditor" id="editor2" name="description" value="" rows="10" cols="20"></textarea>


                </div>
                <div class="form-group d-flex disease_div">
                    <input type="checkbox" name="disease_with_outbreak_potential" value="1" id="disease_with_outbreak_potential" class="disease_with_outbreak_potential">
                    <label for="exampleInputEmail1" class=""> <?php echo lang('Disease With Outbreak Potential'); ?> </label>
                </div>
                <div id="maximum">
                    <div class="form-group d-flex">
                        <label for="exampleInputEmail1" class="col-sm-3"> <?php echo lang('Maximum Expected Number Of Patient In A Week'); ?></label>
                        <input type="number" class="form-control col-sm-9" name="maximum_expected_number_of_patient_in_a_week" id="maximum_expected_number_of_patient_in_a_week" value='' placeholder="">
                    </div>
                </div>
                <section class="col-md-12">
                    <div id="addDiagnosis" class="btn btn-info submit_button float-right">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        <span id="button-text"><?php echo lang('submit'); ?></span>
                    </div>
                </section>


            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal fade no-print" id="myTreatmentModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> <?php echo lang('add_new_treatment'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body row">

                <div class="form-row">

                    <div class="form-group col-md-12">
                        <label for="exampleInputEmail1" class="col-sm-4"><?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-inline input-medium" id="name3" name="name" value='' placeholder="" required>
                    </div>
                    <section class="col-md-12">
                        <div id="addTreatment" class="btn btn-info submit_button float-right">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="button-text"><?php echo lang('submit'); ?></span>
                        </div>
                    </section>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal fade no-print" id="myAdviceModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> <?php echo lang('add_new_advice'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body row">

                <div class="form-row">

                    <div class="form-group col-md-12">
                        <label for="exampleInputEmail1" class="col-sm-4"><?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-inline input-medium" id="name1" name="name" value='' placeholder="" required>
                    </div>
                    <section class="col-md-12">
                        <div id="addAdvice" class="btn btn-info submit_button float-right">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="button-text"><?php echo lang('submit'); ?></span>
                        </div>
                    </section>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


    <script src="common/js/codearistos.min.js"></script>
    <script src="common/assets/tinymce/tinymce.min.js"></script>
 <script type="text/javascript">
    var select_patient = "<?php echo lang('select_patient'); ?>";
    var select_diagnosis = "<?php echo lang('select_diagnosis'); ?>";
    var select_treatment = "<?php echo lang('select_treatment'); ?>";

    var select_advice = "<?php echo lang('select_advice'); ?>";
    var select_symptom = "<?php echo lang('select_symptom'); ?>";
    var select_test = "<?php echo lang('select_test'); ?>";
</script>
    <script type="text/javascript">
        var language = "<?php echo $this->language; ?>";
    </script>
    <script src="common/extranal/js/patient/api_case_list.js"></script>


    <script>
        tinymce.init({
            selector: '#editor1',
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
            branding: false,
            promotion: false
        });
    </script>


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





    <!-- Load jQuery -->


    <!-- Load DataTables after jQuery -->



    <script src="common/js/respond.min.js"></script>
    <script type="text/javascript" src="common/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="common/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="common/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>


    <script type="text/javascript" src="common/assets/ckeditor/build/ckeditor.js"></script>
    <!-- <script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/decoupled-document/ckeditor.js"></script> 
<script src="https://cdn.ckeditor.com/ckeditor5/12.3.1/classic/ckeditor.js"></script>-->
    <!-- <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script> -->
    <script type="text/javascript" src="common/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

    <script type="text/javascript" src="common/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
    <script src="common/js/advanced-form-components.js"></script>
    <script src="common/js/jquery.cookie.js"></script>
    <!--common script for all pages-->
    <!-- <script src="common/js/jquery.nicescroll.js" type="text/javascript"></script> -->
    <script src="common/js/common-scripts.js"></script>
    <script src="common/js/lightbox.js"></script>
    <script class="include" type="text/javascript" src="common/js/jquery.dcjqaccordion.2.7.js"></script>
    <!--script for this page only-->
    <script src="common/js/editable-table.js"></script>




    <!-- <script src="common/js/bootstrap-select.min.js"></script> -->
    <script src="common/js/bootstrap-select-country.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.26.1/axios.min.js"></script>





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









    <!-- jQuery -->
    <script src="adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="adminlte/dist/js/adminlte.min.js"></script>
    <!-- <script src="adminlte/dist/js/adminlte.js"></script> -->

    <script src="adminlte/plugins/moment/moment.min.js"></script>


    <script src="adminlte/plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="adminlte/plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <!-- <script src="adminlte/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js"></script> -->
    <!-- jQuery Knob Chart -->
    <!-- <script src="adminlte/plugins/jquery-knob/jquery.knob.min.js"></script> -->
    <!-- daterangepicker -->
    <!-- Tempusdominus Bootstrap 4 -->
    <!-- <script src="adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script> -->
    <!-- Summernote -->
    <!-- <script src="adminlte/plugins/summernote/summernote-bs4.min.js"></script> -->
    <!-- overlayScrollbars -->
    <!-- <script src="adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="adminlte/dist/js/pages/dashboard.js"></script>


    <!-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> -->

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
    <!-- Bootstrap4 Duallistbox -->
    <script src="adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="adminlte/plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <!-- <script src="adminlte/plugins/daterangepicker/daterangepicker.js"></script> -->
    <!-- bootstrap color picker -->
    <script src="adminlte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>

    <script type="text/javascript" src="common/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="common/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
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

    <!-- <script src="common/assets/fullcalendar/fullcalendar.js"></script>
<script src='common/assets/fullcalendar/locale/<?php echo $lang; ?>.js'></script> -->



    <!-- dropzonejs -->
    <script src="adminlte/plugins/dropzone/min/dropzone.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="adminlte/plugins/toastr/toastr.min.js"></script>


    <script>
        $(document).ready(function() {
            "use strict";

            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: "en",
                themeSystem: 'bootstrap', // Enable Bootstrap theme
                events: "appointment/getAppointmentByJason",
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay"
                },
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

                            bgColor = '#FFD54F'; // deeper shade of yellow blending into a lighter shade
                            // textColor = '#333333'; // using a darker text color for better readability on yellow


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
                            bgColor = "#36b9cc"; // I've kept one of the previous colors for 'Requested'. Adjust if needed.
                            break;
                        case 'Treated':
                            bgColor = "#858796"; // I've kept one of the previous colors for 'Treated'. Adjust if needed.
                            break;
                        default:
                            bgColor = "#4e73df"; // default color if no status matches. Adjust if needed.
                    }
                    return {
                        html: `<div style="background: ${bgColor}; padding: 10px; border-radius: 5px;">
                    <span style="color: white;">${arg.timeText}</span><br/>
                    <span style="color: white;">${arg.event.title}</span>
               </div>`
                    };
                },



                eventClick: function(info) {
                    $("#medical_history").html("");
                    if (info.event.id) {
                        $.ajax({
                            url: "patient/getMedicalHistoryByJason?id=" + info.event.id + "&from_where=calendar",
                            method: "GET",
                            dataType: "json",
                            success: function(response) {
                                "use strict";
                                $("#medical_history").html(response.view);
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
        });
    </script>

    <script src="common/extranal/js/footer.js"></script>




    <!-- 
<script>
    $(document).ready(function() {
        var url = window.location;
        // Will only work if string in href matches with location
        $('.treeview-menu li a[href="' + url + '"]').parent().addClass('active');
        // Will also work for relative and absolute hrefs
        $('.treeview-menu li a').filter(function() {
            return this.href == url;
        }).parent().parent().parent().addClass('active');


    });
</script> -->

    <script>
        Dropzone.autoDiscover = false

        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template")
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




</body>

</html>