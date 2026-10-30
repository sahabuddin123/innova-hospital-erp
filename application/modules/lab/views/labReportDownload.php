<html>

<head>
    <base href="<?php echo base_url(); ?>">
    <link href="common/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="screen">
    <link href="common/css/bootstrap-reset.css" rel="stylesheet" type="text/css" media="screen">
    <link href="common/assets/fontawesome5pro/css/all.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="common/assets/DataTables/datatables.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="common/assets/DataTables/Responsive/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="common/assets/DataTables/Responsive/css/responsive.dataTables.css" rel="stylesheet" type="text/css" media="screen" />


    <link href="common/css/style.css" rel="stylesheet" type="text/css" media="screen">

    <link rel="stylesheet" type="text/css" media="screen" href="common/assets/bootstrap-datepicker/css/bootstrap-datepicker.css" />
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/assets/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/assets/bootstrap-datetimepicker/css/datetimepicker.css" />
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/assets/bootstrap-timepicker/compiled/timepicker.css">
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/assets/jquery-multi-select/css/multi-select.css" />
    <link href="common/css/invoice-print.css" rel="stylesheet" type="text/css" media="screen" media="print">
    <link href="common/assets/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" media="screen">
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/assets/select2/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/css/lightbox.css" />
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/assets/bootstrap-fileupload/bootstrap-fileupload.css" />
    <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="common/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />

    <link rel="stylesheet" type="text/css" media="screen" href="common/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="common/css/bootstrap-select-country.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="common/extranal/toast.css">


    <link href="common/extranal/css/medical_history_calendar_modal.css" rel="stylesheet" type="text/css" media="screen">


    <link href="common/css/style-responsive.css" rel="stylesheet" type="text/css" media="screen" />
    <!--        <link href="common/extranal/css/finance/print_and_design_A4.css" rel="stylesheet" />-->

</head>
<style>
    body {
        print-color-adjust: exact;
    }

    hr {
        border-top: 1px solid #000 !important;
        width: 100%;
    }

    h1,
    h3,
    h2,
    h4,
    h5,
    h6 {
        margin: 0px;
    }

    p {
        margin: 3px 0px;
        font-size: .85rem;
    }

    .flex-wrapper {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
        justify-content: flex-start;
    }

    #footer {
        margin-top: auto;
    }

    .panel {
        border: 0px solid #5c5c47;
        background: #fff !important;
        height: 100%;
        margin: 20px 5px 5px 5px;
        border-radius: 0px !important;
        min-height: 700px;
    }

    .panel-body {
        padding: 15px 15px !important;
    }

    .table-qr-hr {
        margin-top: 10px !important;
        margin-bottom: 20px !important;
    }

    .info_text {
        font-size: 11px;
    }

    .control_label {
        font-size: 12px;
        width: 50px;
    }

    .reportBlock table {
        border: 1px solid black;
    }

    .reportBlock table td {
        border: 1px solid black;
    }

    .reportBlock{
        page-break-inside: avoid;
        position: auto;
    }

    .image_bar {
        vertical-align: middle !important;
    }

    .reportBlock{
        overflow: wrap;
    }

    .text-tiny{
        font-size: .7em;
    }

    .text-small{
        font-size: .85em;
    }

    .text-big{
        font-size: 1.4em;
    }

    .text-huge{
        font-size: 1.8em;
    }

    @media print {
        .col-md-7 {
            padding: 0px !important;
        }

        .panel {
            margin: 0px;
            padding: 0px;
        }

        .panel-body {
            padding: 0px;
        }

        .wrapper {
            margin: 0px !important;
            padding: 0px !important;
        }


    }

    #invoice_header {
        border-bottom: 1px solid black;
    }
    
</style>

<body>
    <div class="flex-wrapper">
        <div class="reportBlock" style="padding: 10px 5px;">
            <?php echo $lab->report; ?>
        </div>
    </div>
</body>

</html>


<script src="common/js/codearistos.min.js"></script>
