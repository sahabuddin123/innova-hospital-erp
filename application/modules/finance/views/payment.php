<link href="common/extranal/css/doctor/doctor.css" rel="stylesheet">
<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-money-bill-wave text-primary mr-3"></i>
                        <?php echo lang('invoices'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('invoices'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="finance/addPaymentView" class="btn btn-primary btn-sm px-4 py-3">
                        <i class="fa fa-plus"></i> <?php echo lang('add_new'); ?> <?php echo lang('invoice'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">
                        <!-- <div class="card-header bg-gradient-primary py-4">
                            <h2 class="card-title mb-0 text-white display-6 font-weight-800"><?php echo lang('list_of_invoices_from_opd_ipd_and_appointments'); ?></h2>
                        </div> -->

                        <div class="card-body bg-light p-4">
                            <div class="col-md-4 mb-4">
                                <div class="input-group input-large" data-date="13/07/2013" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date_from" id="date_from" value="" placeholder="<?php echo lang('date_from'); ?>" readonly="">
                                    <span class="input-group-addon mx-2"></span>
                                    <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date_to" id="date_to" value="" placeholder="<?php echo lang('date_to'); ?>" readonly="">
                                </div>
                            </div>

                            <table class="table table-hover datatables text-sm" id="editable-sample3" width="100%">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="font-weight-bold"><?php echo lang(''); ?> #</th>
                                        <th class="font-weight-bold"><?php echo lang('patient'); ?></th>
                                        <!-- <th class="font-weight-bold"><?php echo lang('doctor'); ?></th> -->
                                        <th class="font-weight-bold"><?php echo lang('date'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('total'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('vat'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('discount'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('grand_total'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('paid'); ?> <?php echo lang(''); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('due'); ?></th>
                                        <!-- <th class="font-weight-bold"><?php echo lang('remarks'); ?></th> -->
                                        <th class="font-weight-bold"><?php echo lang('from'); ?></th>
                                        <th class="font-weight-bold no-print"><?php echo lang('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>













<!-- Include Global Modal Styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>application/assets/css/global-modal-styles.css">

<div class="modal fade modal-enhanced" id="editPaymentModal" role="dialog" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Payment
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- The edit payment form will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Discount to Invoice -->
<div class="modal fade" id="addDiscountModal" tabindex="-1" role="dialog" aria-labelledby="addDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3 bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0 text-white" id="addDiscountModalLabel">
                            <?php echo lang('add_discount') ?: 'Add Discount'; ?> - Invoice #<span id="disc_invoice_id_display" class="badge badge-light text-primary font-weight-bolder"></span>
                        </h5>
                        <small class="text-white-50" id="disc_patient_info_display">Patient Information</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="addDiscountForm" autocomplete="off">
                <input type="hidden" name="id" id="disc_invoice_id" value="">
                
                <div class="modal-body p-4 bg-light">
                    <!-- Loading Spinner Overlay -->
                    <div id="disc_modal_loader" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted font-weight-500">Loading invoice details...</p>
                    </div>

                    <!-- Modal Content Container -->
                    <div id="disc_modal_content" style="display: none;">
                        <!-- 4 Financial Stat Cards -->
                        <div class="row mb-3">
                            <!-- Total Bill / Subtotal -->
                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border-0 shadow-sm h-100 bg-white">
                                    <div class="card-body p-3">
                                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">
                                            <i class="fas fa-file-invoice-dollar mr-1 text-primary"></i> Total Bill
                                        </div>
                                        <div class="h5 font-weight-bold text-dark mb-0" id="disc_card_subtotal">৳0.00</div>
                                        <small class="text-muted" id="disc_card_vat">VAT: ৳0.00</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Existing Discount Given -->
                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border-0 shadow-sm h-100 bg-white">
                                    <div class="card-body p-3">
                                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">
                                            <i class="fas fa-percent mr-1 text-warning"></i> Discount Given
                                        </div>
                                        <div class="h5 font-weight-bold text-warning mb-0" id="disc_card_existing_discount">৳0.00</div>
                                        <small class="text-muted">Previous Discount</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Paid Amount -->
                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border-0 shadow-sm h-100 bg-white">
                                    <div class="card-body p-3">
                                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">
                                            <i class="fas fa-check-circle mr-1 text-success"></i> Total Paid
                                        </div>
                                        <div class="h5 font-weight-bold text-success mb-0" id="disc_card_paid">৳0.00</div>
                                        <small class="text-muted">Deposited Amount</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Current Due -->
                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border-0 shadow-sm h-100 bg-white">
                                    <div class="card-body p-3">
                                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">
                                            <i class="fas fa-exclamation-circle mr-1 text-danger"></i> Current Due
                                        </div>
                                        <div class="h5 font-weight-bold text-danger mb-0" id="disc_card_due">৳0.00</div>
                                        <small class="text-muted" id="disc_card_grand_total">Total: ৳0.00</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discount Entry Card -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                    <label class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-calculator text-primary mr-2"></i> Discount Mode
                                    </label>
                                    <div class="btn-group btn-group-toggle mt-2 mt-sm-0" data-toggle="buttons">
                                        <label class="btn btn-outline-primary btn-sm active" id="mode_additional_label">
                                            <input type="radio" name="discount_mode" id="mode_additional" value="additional" checked>
                                            <i class="fas fa-plus-circle mr-1"></i> Add to Existing
                                        </label>
                                        <label class="btn btn-outline-primary btn-sm" id="mode_total_label">
                                            <input type="radio" name="discount_mode" id="mode_total" value="total">
                                            <i class="fas fa-edit mr-1"></i> Set New Total
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="disc_amount_input" class="font-weight-600 text-secondary" id="disc_amount_label">
                                        Additional Discount Amount (<span class="disc_curr">৳</span>) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg shadow-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0 font-weight-bold text-primary disc_curr">৳</span>
                                        </div>
                                        <input type="number" step="any" min="0" class="form-control form-control-lg border-left-0 font-weight-bold text-dark" id="disc_amount_input" name="discount_amount" placeholder="0.00" required>
                                    </div>
                                    <div id="disc_error_feedback" class="text-danger small mt-1 font-weight-bold" style="display: none;"></div>
                                </div>

                                <!-- Fast Shortcut Buttons -->
                                <div class="d-flex flex-wrap align-items-center mb-3">
                                    <span class="text-muted small mr-2 font-weight-500">Quick Add:</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary mr-1 mb-1 quick-disc" data-amount="50">+50</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary mr-1 mb-1 quick-disc" data-amount="100">+100</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary mr-1 mb-1 quick-disc" data-amount="200">+200</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary mr-1 mb-1 quick-disc" data-amount="500">+500</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary mr-1 mb-1 quick-disc" data-amount="1000">+1000</button>
                                    <button type="button" class="btn btn-xs btn-outline-warning mr-1 mb-1" id="btn_waive_due"><i class="fas fa-hand-holding-heart mr-1"></i> Full Due Waiver</button>
                                    <button type="button" class="btn btn-xs btn-outline-danger mb-1" id="btn_clear_disc"><i class="fas fa-times mr-1"></i> Clear</button>
                                </div>

                                <div class="form-group mb-0">
                                    <label for="disc_remarks" class="font-weight-600 text-secondary">
                                        Reason / Remarks (Optional)
                                    </label>
                                    <input type="text" class="form-control" id="disc_remarks" name="remarks" placeholder="e.g. Approved by Director, Referral courtesy, Poor patient relief...">
                                </div>
                            </div>
                        </div>

                        <!-- Live Calculation Preview Card -->
                        <div class="card border-0 shadow-sm bg-white" style="border-left: 4px solid #4e73df !important;">
                            <div class="card-body p-3">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-chart-line mr-1"></i> Live Calculation Preview
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted py-1" style="width: 50%;">Subtotal (Total Bill):</td>
                                                <td class="text-right font-weight-bold text-dark py-1" id="preview_subtotal">৳0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted py-1">VAT:</td>
                                                <td class="text-right text-dark py-1" id="preview_vat">৳0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted py-1">Previous Discount:</td>
                                                <td class="text-right text-muted py-1" id="preview_prev_disc">৳0.00</td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td class="font-weight-bold py-1 text-dark">New Total Discount:</td>
                                                <td class="text-right font-weight-bold text-warning py-1" id="preview_new_disc">৳0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold py-1 text-dark">New Grand Total:</td>
                                                <td class="text-right font-weight-bold text-dark py-1" id="preview_grand_total">৳0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted py-1">Total Paid (Deposits):</td>
                                                <td class="text-right text-success py-1" id="preview_paid">৳0.00</td>
                                            </tr>
                                            <tr class="table-light border-top">
                                                <td class="font-weight-bold h6 py-2 text-dark">New Due Balance:</td>
                                                <td class="text-right font-weight-bold h6 py-2" id="preview_new_due">৳0.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white py-3 px-4">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btn_save_discount">
                        <i class="fas fa-check-circle mr-1"></i> Save Discount
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>





<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<!-- <script defer type="text/javascript" src="common/assets/DataTables/datatables.min.js"></script> -->
<script src="common/extranal/js/finance/payments.js"></script>
<script>
    $(document).ready(function() {

        $('#date_from').on('change', function() {
            var date_from = $(this).val();
            var date_to = $('#date_to').val();
            var date_from_split = date_from.split('-');
            var date_from_new = date_from_split[1] + '/' + date_from_split[0] + '/' + date_from_split[2]
            if (date_to != '' || date_to != null) {
                var date_to_split = date_to.split('-');
                var date_to_new = date_to_split[1] + '/' + date_to_split[0] + '/' + date_to_split[2];
            }
            if (date_to != '' || date_to != null) {
                if (Date.parse(date_to_new) <= Date.parse(date_from_new)) {

                    alert('Select a Valid Date. End Date should be Greater than Start Date');
                    $(this).val("");
                } else {
                    $('#editable-sample3').DataTable().destroy().clear();
                    "use strict";
                    var table = $('#editable-sample3').DataTable({


                        responsive: true,

                        processing: true,
                        serverSide: true,
                        searchable: true,
                        bScrollCollapse: true,
                        ajax: {
                            url: "finance/getPayment?start_date=" + date_from + "&end_date=" + date_to,
                            type: "POST",
                        },

                        scroller: {
                            loadingIndicator: true
                        },
                        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                        buttons: [{
                                extend: "copyHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "excelHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "csvHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "pdfHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "print",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                        ],
                        aLengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        iDisplayLength: 100,

                        "order": [
                            [0, "desc"]
                        ],

                        "language": {
                            "lengthMenu": "_MENU_",
                            search: "_INPUT_",
                            "url": "common/assets/DataTables/languages/" + language + ".json"
                        }
                    });
                    table.buttons().container().appendTo('.custom_buttons');
                }
            }
        })
        $('#date_to').on('change', function() {
            var date_to = $(this).val();
            var date_from = $('#date_from').val();

            var date_to_split = date_to.split('-');
            var date_to_new = date_to_split[1] + '/' + date_to_split[0] + '/' + date_to_split[2];
            if (date_from != '' || date_from != null) {
                var date_from_split = date_from.split('-');
                var date_from_new = date_from_split[1] + '/' + date_from_split[0] + '/' + date_from_split[2];

            }
            if (date_from != '' || date_from != null) {
                if (Date.parse(date_to_new) <= Date.parse(date_from_new)) {

                    alert('Select a Valid Date. End Date should be Greater than Start Date');
                    $(this).val("");
                } else {
                    $('#editable-sample3').DataTable().destroy().clear();
                    "use strict";
                    var table = $('#editable-sample3').DataTable({
                        responsive: true,

                        processing: true,
                        serverSide: true,
                        searchable: true,
                        bScrollCollapse: true,
                        ajax: {
                            url: "finance/getPayment?start_date=" + date_from + "&end_date=" + date_to,
                            type: "POST",
                        },
                        scroller: {
                            loadingIndicator: true
                        },
                        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                        buttons: [{
                                extend: "copyHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "excelHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "csvHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "pdfHtml5",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                            {
                                extend: "print",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                                }
                            },
                        ],
                        aLengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        iDisplayLength: 100,

                        "order": [
                            [0, "desc"]
                        ],

                        "language": {
                            "lengthMenu": "_MENU_",
                            search: "_INPUT_",
                            "url": "common/assets/DataTables/languages/" + language + ".json"
                        }
                    });
                    table.buttons().container().appendTo('.custom_buttons');
                }
            }
        })
    })
</script>

<script>
    $(document).ready(function() {
        var currentInvoice = null;

        // Open Add Discount Modal on button click (using event delegation for DataTables)
        $(document).on('click', '.add_discount_btn', function(e) {
            e.preventDefault();
            var invoiceId = $(this).data('id');
            if (!invoiceId) return;

            // Reset modal form
            $('#addDiscountForm')[0].reset();
            $('#mode_additional').prop('checked', true);
            $('#mode_additional_label').addClass('active');
            $('#mode_total_label').removeClass('active');
            $('#disc_amount_label').html('Additional Discount Amount (<span class="disc_curr">৳</span>) <span class="text-danger">*</span>');
            $('#disc_error_feedback').hide().text('');
            $('#btn_save_discount').prop('disabled', false);

            $('#disc_invoice_id').val(invoiceId);
            $('#disc_invoice_id_display').text(invoiceId);
            $('#disc_patient_info_display').text('Loading patient details...');

            $('#disc_modal_loader').show();
            $('#disc_modal_content').hide();
            $('#addDiscountModal').modal('show');

            // Fetch invoice details
            $.ajax({
                url: 'finance/getInvoiceDiscountDetails',
                type: 'GET',
                data: { id: invoiceId },
                dataType: 'json',
                success: function(res) {
                    $('#disc_modal_loader').hide();
                    if (res.status === 'success') {
                        currentInvoice = res;
                        $('.disc_curr').text(res.currency || '৳');
                        $('#disc_patient_info_display').html(
                            '<strong>' + (res.patient_name || 'N/A') + '</strong>' + 
                            (res.patient_phone ? ' &bull; ' + res.patient_phone : '') + 
                            ' &bull; Date: ' + (res.date || '')
                        );

                        $('#disc_card_subtotal').text((res.currency) + ' ' + parseFloat(res.subtotal).toFixed(2));
                        $('#disc_card_vat').text('VAT: ' + (res.currency) + ' ' + parseFloat(res.vat).toFixed(2));
                        $('#disc_card_existing_discount').text((res.currency) + ' ' + parseFloat(res.existing_discount).toFixed(2));
                        $('#disc_card_paid').text((res.currency) + ' ' + parseFloat(res.paid).toFixed(2));
                        $('#disc_card_due').text((res.currency) + ' ' + parseFloat(res.due).toFixed(2));
                        $('#disc_card_grand_total').text('Grand Total: ' + (res.currency) + ' ' + parseFloat(res.gross_total).toFixed(2));

                        $('#disc_modal_content').fadeIn(200);
                        recalcDiscount();
                        setTimeout(function() { $('#disc_amount_input').focus(); }, 300);
                    } else {
                        $('#addDiscountModal').modal('hide');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', res.message || 'Unable to fetch invoice details.', 'error');
                        } else {
                            alert(res.message || 'Unable to fetch invoice details.');
                        }
                    }
                },
                error: function() {
                    $('#disc_modal_loader').hide();
                    $('#addDiscountModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Network or server error while loading invoice details.', 'error');
                    } else {
                        alert('Network or server error while loading invoice details.');
                    }
                }
            });
        });

        // Toggle discount mode
        $('input[name="discount_mode"]').on('change', function() {
            var mode = $(this).val();
            if (mode === 'total') {
                $('#disc_amount_label').html('New Total Discount Amount (<span class="disc_curr">' + (currentInvoice ? currentInvoice.currency : '৳') + '</span>) <span class="text-danger">*</span>');
                if (currentInvoice && $('#disc_amount_input').val() === '') {
                    $('#disc_amount_input').val(parseFloat(currentInvoice.existing_discount).toFixed(2));
                }
            } else {
                $('#disc_amount_label').html('Additional Discount Amount (<span class="disc_curr">' + (currentInvoice ? currentInvoice.currency : '৳') + '</span>) <span class="text-danger">*</span>');
                if ($('#disc_amount_input').val() !== '' && currentInvoice && parseFloat($('#disc_amount_input').val()) === parseFloat(currentInvoice.existing_discount)) {
                    $('#disc_amount_input').val('');
                }
            }
            recalcDiscount();
        });

        // Quick add buttons
        $(document).on('click', '.quick-disc', function() {
            var addVal = parseFloat($(this).data('amount')) || 0;
            var mode = $('input[name="discount_mode"]:checked').val();
            var curVal = parseFloat($('#disc_amount_input').val()) || 0;
            if (mode === 'additional') {
                $('#disc_amount_input').val((curVal + addVal).toFixed(2));
            } else {
                if (currentInvoice) {
                    var base = curVal > 0 ? curVal : parseFloat(currentInvoice.existing_discount);
                    $('#disc_amount_input').val((base + addVal).toFixed(2));
                }
            }
            recalcDiscount();
        });

        // Full due waiver button
        $('#btn_waive_due').on('click', function() {
            if (!currentInvoice) return;
            var due = parseFloat(currentInvoice.due);
            if (due <= 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Info', 'This invoice has no outstanding due to waive.', 'info');
                } else {
                    alert('This invoice has no outstanding due to waive.');
                }
                return;
            }
            var mode = $('input[name="discount_mode"]:checked').val();
            if (mode === 'additional') {
                $('#disc_amount_input').val(due.toFixed(2));
            } else {
                var newTotal = parseFloat(currentInvoice.existing_discount) + due;
                $('#disc_amount_input').val(newTotal.toFixed(2));
            }
            recalcDiscount();
        });

        // Clear button
        $('#btn_clear_disc').on('click', function() {
            $('#disc_amount_input').val('');
            recalcDiscount();
        });

        // Input change event
        $('#disc_amount_input').on('input keyup change', function() {
            recalcDiscount();
        });

        // Recalculate Live Preview function
        function recalcDiscount() {
            if (!currentInvoice) return;
            var curr = currentInvoice.currency || '৳';
            var subtotal = parseFloat(currentInvoice.subtotal) || 0;
            var vat = parseFloat(currentInvoice.vat) || 0;
            var existingDisc = parseFloat(currentInvoice.existing_discount) || 0;
            var paid = parseFloat(currentInvoice.paid) || 0;

            var inputVal = parseFloat($('#disc_amount_input').val()) || 0;
            var mode = $('input[name="discount_mode"]:checked').val();

            var newTotalDisc = 0;
            if (mode === 'total') {
                newTotalDisc = inputVal;
            } else {
                newTotalDisc = existingDisc + inputVal;
            }

            var hasError = false;
            var errorMsg = '';

            if (inputVal < 0) {
                hasError = true;
                errorMsg = 'Discount amount cannot be negative.';
            } else if (newTotalDisc > subtotal) {
                hasError = true;
                errorMsg = 'Total discount (' + curr + ' ' + newTotalDisc.toFixed(2) + ') cannot exceed subtotal (' + curr + ' ' + subtotal.toFixed(2) + ').';
            }

            if (hasError) {
                $('#disc_error_feedback').show().text(errorMsg);
                $('#btn_save_discount').prop('disabled', true);
            } else {
                $('#disc_error_feedback').hide().text('');
                $('#btn_save_discount').prop('disabled', false);
            }

            var newGrandTotal = Math.max(0, subtotal - newTotalDisc + vat);
            var newDue = newGrandTotal - paid;

            $('#preview_subtotal').text(curr + ' ' + subtotal.toFixed(2));
            $('#preview_vat').text(curr + ' ' + vat.toFixed(2));
            $('#preview_prev_disc').text(curr + ' ' + existingDisc.toFixed(2));
            $('#preview_new_disc').text(curr + ' ' + newTotalDisc.toFixed(2));
            $('#preview_grand_total').text(curr + ' ' + newGrandTotal.toFixed(2));
            $('#preview_paid').text(curr + ' ' + paid.toFixed(2));

            var dueEl = $('#preview_new_due');
            if (newDue > 0) {
                dueEl.html('<span class="text-danger">' + curr + ' ' + newDue.toFixed(2) + ' (Due)</span>');
            } else if (newDue === 0) {
                dueEl.html('<span class="text-success">' + curr + ' 0.00 (Paid in Full)</span>');
            } else {
                dueEl.html('<span class="text-info">' + curr + ' ' + Math.abs(newDue).toFixed(2) + ' (Refundable/Overpaid)</span>');
            }
        }

        // Submit form
        $('#addDiscountForm').on('submit', function(e) {
            e.preventDefault();
            var submitBtn = $('#btn_save_discount');
            var originalBtnText = submitBtn.html();

            var inputVal = parseFloat($('#disc_amount_input').val());
            if (isNaN(inputVal) || inputVal < 0) {
                $('#disc_error_feedback').show().text('Please enter a valid discount amount.');
                return;
            }

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

            $.ajax({
                url: 'finance/saveInvoiceDiscount',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                    if (res.status === 'success') {
                        $('#addDiscountModal').modal('hide');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Discount Saved!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert(res.message);
                        }
                        // Reload datatable
                        if ($.fn.DataTable.isDataTable('#editable-sample3')) {
                            $('#editable-sample3').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', res.message || 'Failed to save discount.', 'error');
                        } else {
                            alert(res.message || 'Failed to save discount.');
                        }
                    }
                },
                error: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Server error while updating discount.', 'error');
                    } else {
                        alert('Server error while updating discount.');
                    }
                }
            });
        });
    });
</script>