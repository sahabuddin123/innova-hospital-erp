<!--sidebar end-->
<!--main content start-->



<link href="common/extranal/css/finance/payments.css" rel="stylesheet">
<div class="content-wrapper bg-gradient-light" style="min-height: 2726.9px;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-money-bill-wave text-primary mr-3"></i>
                        <?php echo lang('due'); ?> <?php echo lang('payments'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('due'); ?> <?php echo lang('payments'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bold mb-0"><?php echo lang('All the due invoices'); ?></h3>
                            <span class="badge badge-primary px-3 py-2 font-weight-bold" id="due_pwa_badge" style="font-size:12px;">
                                <i class="fas fa-bolt mr-1"></i> PWA Offline Ready
                            </span>
                        </div>

                        <!-- Offline Mode Notice Banner -->
                        <div id="due_offline_banner" class="alert alert-warning mx-4 mt-3 shadow-sm border-warning" style="display:none;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-wifi text-danger mr-3 fa-2x"></i>
                                <div>
                                    <strong class="text-dark">Offline Due Collection Active:</strong> You can accept patient due deposits without internet.
                                    <div class="small text-muted">Money receipts will be generated & printed immediately. Deposits will auto-sync to the central hospital server once connection is restored.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 px-3 align-items-center">
                            <div class="col-md-5 date_field mt-2">
                                <div class="input-group input-large" data-date="13/07/2013" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control form-control-lg default-date-picker" name="date_from" id="date_from" value="" placeholder="<?php echo lang('date_from'); ?>" readonly="">
                                    <span class="input-group-addon"> <?php echo lang(''); ?></span>
                                    <input type="text" class="form-control form-control-lg default-date-picker" name="date_to" id="date_to" value="" placeholder="<?php echo lang('date_to'); ?>" readonly="">
                                </div>
                            </div>
                            <div class="col-md-5 ml-auto mt-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white text-primary font-weight-bold"><i class="fas fa-receipt mr-1"></i> Quick Due</span>
                                    </div>
                                    <input type="text" id="quick_due_search_input" class="form-control form-control-lg" placeholder="Invoice # (e.g. 1052)...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary font-weight-bold px-3" id="btn_quick_due_lookup">
                                            <i class="fas fa-search mr-1"></i> Collect Due
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body bg-light p-4">
                            <table class="table table-hover datatables" id="editable-sample4" width="100%">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="font-weight-bold"><?php echo lang('invoice_id'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('patient'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('doctor'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('date'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('from'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('sub_total'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('discount'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('grand_total'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('paid'); ?> <?php echo lang('amount'); ?></th>
                                        <th class="font-weight-bold"><?php echo lang('due'); ?></th>
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









<!--main content end-->
<!--footer start-->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('add_deposit'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form role="form" action="finance/deposit" id="deposit-form" class="clearfix" method="post"
                    enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('name'); ?>
                                &ast; </label>
                            <input type="text" class="form-control form-control-lg" name="name" id="name" value='' placeholder=""
                                readonly="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1"> <?php echo lang('invoice_no'); ?> &ast; </label>
                            <input type="text" class="form-control form-control-lg" name="invoice_no" id="invoice_no" value=''
                                placeholder="" readonly="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1"> <?php echo lang('date'); ?> &ast; </label>
                            <input type="text" class="form-control form-control-lg" name="date" id="date" value='' placeholder=""
                                readonly="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1"> <?php echo lang('due'); ?>
                                <?php echo lang('amount'); ?></label>
                            <input type="text" class="form-control form-control-lg font-weight-bold" id="due_amount" name="due" value='' placeholder=""
                                readonly style="background-color: #f8f9fa;">
                            <small class="form-text text-muted mt-1" id="remaining_due_wrapper" style="display:none;">
                                Remaining: <strong class="text-danger">৳<span id="remaining_due_display">0.00</span></strong>
                            </small>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="deposited_amount"> <?php echo lang('deposit_amount'); ?></label>
                            <input type="number" step="any" min="0" class="form-control form-control-lg font-weight-bold" name="deposited_amount" id="deposited_amount"
                                value='' placeholder="0.00" onfocus="this.select()" onclick="this.select()">
                        </div>
                        <div class="form-group col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="additional_discount" class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-tag text-warning mr-1"></i><?php echo lang('discount'); ?> <?php echo lang('amount'); ?>
                                </label>
                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size:11px;" title="Previous discount given on this invoice">
                                    Prev: ৳<span id="prev_discount_badge">0.00</span>
                                </span>
                            </div>
                            <input type="number" step="any" min="0" class="form-control form-control-lg font-weight-bold text-success" name="additional_discount" id="additional_discount"
                                value='0' placeholder="0.00" onfocus="this.select()" onclick="this.select()">
                            <input type="hidden" id="prev_discount_input" name="prev_discount" value="0">
                            <small class="form-text text-muted mt-1" id="total_discount_wrapper">
                                Total Discount: <strong class="text-success">৳<span id="total_discount_display">0.00</span></strong>
                            </small>
                        </div>



                        <div class="form-group col-md-12">
                            <div class="">
                                <label for="exampleInputEmail1"> <?php echo lang('deposit_type'); ?></label>
                            </div>
                            <div class="">
                                <select class="form-control form-control-lg m-bot15 js-example-basic-single selecttype" id="selecttype"
                                    name="deposit_type" value=''>
                                    <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                        <option value="Cash"> <?php echo lang('cash'); ?> </option>
                                        <!-- <option value="Insurance"> <?php echo lang('insurance'); ?> </option> -->
                                        <option value="Card"> <?php echo lang('card'); ?> </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="hidden insurance_div">

                                <div class="form-group" style="margin-top:10px;">
                                    <label for="exampleInputEmail1"> <?php echo lang('insurance_company'); ?>
                                        <?php echo lang('name'); ?></label>

                                    <div class="company_div">
                                        <select class="form-control form-control-lg m-bot15 js-example-basic-single"
                                            name="insurance_company" id="insurance_company" value=''>
                                            <option value="">Select Company</option>
                                            <?php foreach ($insurance_companys as $insurance_company) { ?>
                                                <option value="<?php echo $insurance_company->id; ?>" <?php


                                                                                                        ?>>
                                                    <?php echo $insurance_company->name; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="">
                                    <div class="payment_label" style="margin-top:10px;">
                                        <label for="exampleInputEmail1"> <?php echo lang('insurance_details'); ?>
                                        </label>
                                    </div>
                                    <div class="">
                                        <textarea class="form-control form-control-lg" name="insurance_details" rows="2"
                                            cols="20"></textarea>
                                    </div>

                                </div>
                            </div>

                            <?php
                            $payment_gateway = $settings->payment_gateway;
                            ?>



                            <div class="cardPayment row mt-3">

                                <div class="col-md-12 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('accepted'); ?>
                                        <?php echo lang('cards'); ?></label>
                                    <div class="payment pad_bot">
                                        <img src="uploads/card.png" width="100%">
                                    </div>
                                </div>
                                <?php
                                if ($payment_gateway == 'PayPal') {
                                ?>

                                    <div class="col-md-12 payment pad_bot">
                                        <label for="exampleInputEmail1"> <?php echo lang('card'); ?>
                                            <?php echo lang('type'); ?></label>
                                        <select class="form-control form-control-lg m-bot15" name="card_type" value=''>

                                            <option value="Mastercard"> <?php echo lang('mastercard'); ?> </option>
                                            <option value="Visa"> <?php echo lang('visa'); ?> </option>
                                            <option value="American Express"> <?php echo lang('american_express'); ?>
                                            </option>
                                        </select>
                                    </div>
                                <?php } ?>
                                <?php if ($payment_gateway == '2Checkout' || $payment_gateway == 'PayPal') {
                                ?>
                                    <div class="col-md-12 payment pad_bot">
                                        <label for="exampleInputEmail1"> <?php echo lang('cardholder'); ?>
                                            <?php echo lang('name'); ?></label>
                                        <input type="text" id="cardholder" class="form-control pay_in" name="cardholder"
                                            value='' placeholder="">
                                    </div>
                                <?php } ?>
                                <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != 'SSLCOMMERZ' && $payment_gateway != 'Paytm') { ?>
                                    <div class="col-md-12 payment pad_bot mt-3">
                                        <label for="exampleInputEmail1"> <?php echo lang('card'); ?>
                                            <?php echo lang('number'); ?></label>
                                        <input type="text" class="form-control pay_in" id="card" name="card_number" value=''
                                            placeholder="">
                                    </div>



                                    <div class="col-md-8 payment pad_bot mt-3">
                                        <label for="exampleInputEmail1"> <?php echo lang('expire'); ?>
                                            <?php echo lang('date'); ?></label>
                                        <input type="text" class="form-control pay_in" id="expire" data-date=""
                                            data-date-format="MM YY" placeholder="Expiry (MM/YY)" name="expire_date"
                                            maxlength="7" aria-describedby="basic-addon1" value='' placeholder="">
                                    </div>
                                    <div class="col-md-4 payment pad_bot mt-3">
                                        <label for="exampleInputEmail1"> <?php echo lang('cvv'); ?> </label>
                                        <input type="text" class="form-control pay_in" id="cvv" maxlength="3" name="cvv"
                                            value='' placeholder="">
                                    </div>
                                <?php
                                }
                                ?>
                            </div>



                        </div>


                        <input type="hidden" name="redirect" value="due">
                        <input type="hidden" name="id" id="id" value=''>
                        <input type="hidden" name="patient" id="patient_id" value=''>
                        <input type="hidden" name="payment_id" id="payment_id" value=''>
                        <div class="form-group cashsubmit payment right-six col-md-12">
                            <button type="submit" name="submit2" id="submit1" class="btn btn-info row float-right mr-0">
                                <?php echo lang('submit'); ?></button>
                        </div>
                        <div class="form-group cardsubmit right-six col-md-12 d-none">
                            <?php $twocheckout = $this->db->get_where('paymentgateway', array('name =' => '2Checkout'))->row(); ?>
                            <button type="submit" name="pay_now" id="submit-btn"
                                class="btn btn-info row float-right mr-0"
                                <?php if ($settings->payment_gateway == 'Stripe') {
                                ?>onClick="stripePay(event);"
                                <?php }
                                ?><?php if ($settings->payment_gateway == '2Checkout' && $twocheckout->status == 'live') {
                                    ?>onClick="twoCheckoutPay(event);"
                                <?php }
                                ?>>
                                <?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
</div>



<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script src="common/extranal/js/finance/due_collection.js?v=<?php echo time(); ?>"></script>
<?php if (!empty($gateway->publish)) {
    $gateway_stripe = $gateway->publish;
} else {
    $gateway_stripe = '';
} ?>

<script type="text/javascript">
    var publish = "<?php echo $gateway_stripe; ?>";
</script>
<script src="common/js/moment.min.js"></script>

<script type="text/javascript">
    var payment_gateway = "<?php echo $settings->payment_gateway; ?>";
</script>

<script src="common/extranal/js/finance/patient_deposit.js?v=<?php echo time(); ?>"></script>

<script type="text/javascript">
    // Direct safety handlers for Deposit Modal & Discount live calculation
    $(document).ready(function() {
        function liveRecalculateDueDeposit() {
            var originalDue = parseFloat($("#due_amount").data("original-due")) || 0;
            var prevDiscount = parseFloat($("#prev_discount_input").val()) || parseFloat($("#due_amount").data("prev-discount")) || 0;
            var addDiscount = parseFloat($("#additional_discount").val()) || 0;
            var deposit = parseFloat($("#deposited_amount").val()) || 0;

            if (addDiscount < 0) {
                addDiscount = 0;
                $("#additional_discount").val(0);
            }
            if (deposit < 0) {
                deposit = 0;
                $("#deposited_amount").val(0);
            }

            var totalDiscount = prevDiscount + addDiscount;
            $("#total_discount_display").text(totalDiscount.toFixed(2));

            var remainingDue = originalDue - addDiscount - deposit;
            if (remainingDue < 0) {
                remainingDue = 0;
            }
            $("#remaining_due_display").text(remainingDue.toFixed(2));

            if (addDiscount > 0 || deposit > 0) {
                $("#remaining_due_wrapper").show();
            } else {
                $("#remaining_due_wrapper").hide();
            }
        }

        $(document).off("input keyup change", "#additional_discount, #deposited_amount").on("input keyup change", "#additional_discount, #deposited_amount", function() {
            liveRecalculateDueDeposit();
        });

        $(document).off("focus click", "#additional_discount, #deposited_amount").on("focus click", "#additional_discount, #deposited_amount", function() {
            var self = this;
            setTimeout(function() {
                self.select();
            }, 50);
        });

        function fillDepositModal(response, from, iid) {
            var d = new Date();
            var strDate = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
            var due = (response.response !== undefined && response.response !== null) ? parseFloat(response.response) : (parseFloat(response.due) || 0);
            var prev_discount = (response.previous_discount !== undefined && response.previous_discount !== null) ? parseFloat(response.previous_discount) : (parseFloat(response.discount) || 0);
            var pName = response.patient ? (response.patient.name || response.patient_name || "") : (response.patient_name || "");
            var pId = response.patient ? (response.patient.id || response.patient_id || "") : (response.patient_id || "");

            $("#due_amount").val(due.toFixed(2));
            $("#due_amount").data("original-due", due);
            $("#due_amount").data("prev-discount", prev_discount);
            $("#prev_discount_input").val(prev_discount.toFixed(2));
            $("#prev_discount_badge").text(prev_discount.toFixed(2));
            $("#total_discount_display").text(prev_discount.toFixed(2));

            if (from == 'appointment') {
                $("#deposited_amount").val(due.toFixed(2));
                $('#deposited_amount').attr('readonly', true);
                $('#additional_discount').attr('readonly', true);
            } else {
                $('#deposited_amount').attr('readonly', false);
                $('#additional_discount').attr('readonly', false);
            }
            $("#payment_id").val(iid);
            $("#patient_id").val(pId);
            $("#name").val(pName);
            $("#invoice_no").val(iid);
            $("#date").val(strDate);
        }

        $(document).off("click", ".depositButton").on("click", ".depositButton", function(e) {
            e.preventDefault();
            $("#loader").show();
            var iid = $(this).attr("data-id");
            var from = $(this).attr("data-from");
            $("#due_amount").val("");
            $("#payment_id").val("");
            $("#patient_id").val("");
            $("#name").val("");
            $("#invoice_no").val("");
            $("#date").val("");
            $("#deposited_amount").val("");
            $("#additional_discount").val("0");
            $("#prev_discount_badge").text("0.00");
            $("#prev_discount_input").val("0.00");
            $("#total_discount_display").text("0.00");
            $("#remaining_due_display").text("0.00");
            $("#remaining_due_wrapper").hide();
            if ($("#deposit-form").length) {
                $("#deposit-form").trigger("reset");
            }
            $("#myModal").modal("show");

            if (!navigator.onLine) {
                if (window.OfflineDB && window.OfflineDB.getCatalog) {
                    window.OfflineDB.getCatalog('due_invoices_cache').then(function(cached) {
                        var found = cached ? cached.find(function(c) { return String(c.id) === String(iid); }) : null;
                        if (found) {
                            fillDepositModal(found, from, iid);
                        } else {
                            var d = new Date();
                            var strDate = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
                            $("#payment_id").val(iid);
                            $("#invoice_no").val(iid);
                            $("#date").val(strDate);
                            $("#name").attr('readonly', false);
                            $("#due_amount").attr('readonly', false);
                        }
                        $("#loader").hide();
                    }).catch(function() {
                        $("#loader").hide();
                    });
                } else {
                    $("#loader").hide();
                }
            } else {
                $.ajax({
                    url: "finance/getDepositByInvoiceIdForDeposit?id=" + iid,
                    method: "GET",
                    data: "",
                    dataType: "json",
                    success: function(response) {
                        fillDepositModal(response, from, iid);
                    },
                    error: function(xhr, status, error) {
                        console.warn("Could not fetch online due info, checking offline cache for invoice " + iid);
                        if (window.OfflineDB && window.OfflineDB.getCatalog) {
                            window.OfflineDB.getCatalog('due_invoices_cache').then(function(cached) {
                                var found = cached ? cached.find(function(c) { return String(c.id) === String(iid); }) : null;
                                if (found) {
                                    fillDepositModal(found, from, iid);
                                }
                            });
                        }
                    },
                    complete: function() {
                        $("#loader").hide();
                    }
                });
            }
        });

        // Quick Due Collect Lookup by Invoice #
        $(document).on("click", "#btn_quick_due_lookup", function(e) {
            e.preventDefault();
            var val = $.trim($("#quick_due_search_input").val());
            if (!val) {
                alert("Please enter an Invoice Number.");
                $("#quick_due_search_input").focus();
                return;
            }
            $("<button class='depositButton' data-id='" + val + "' data-from='payment'></button>").trigger("click");
        });

        $("#quick_due_search_input").on("keypress", function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $("#btn_quick_due_lookup").trigger("click");
            }
        });

        function updateDueOfflineBanner() {
            if (!navigator.onLine) {
                $("#due_offline_banner").slideDown();
            } else {
                $("#due_offline_banner").slideUp();
            }
        }
        updateDueOfflineBanner();
        window.addEventListener("online", updateDueOfflineBanner);
        window.addEventListener("offline", updateDueOfflineBanner);
    });
</script>

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
                    $('#editable-sample4').DataTable().destroy().clear();
                    "use strict";
                    var table = $("#editable-sample4").DataTable({
                        responsive: true,
                        //   dom: 'lfrBtip',

                        processing: true,
                        serverSide: true,
                        searchable: true,
                        ajax: {
                            url: "finance/getDuePayment?start_date=" + date_from + "&end_date=" +
                                date_to,
                            type: "POST",
                        },
                        scroller: {
                            loadingIndicator: true,
                        },
                        dom: "<'row'<'col-md-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                        buttons: [{
                                extend: "copyHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "excelHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "csvHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "pdfHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "print",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                        ],
                        aLengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"],
                        ],
                        iDisplayLength: 100,

                        order: [
                            [0, "desc"]
                        ],

                        language: {
                            lengthMenu: "_MENU_",
                            search: "_INPUT_",
                            url: "common/assets/DataTables/languages/" + language + ".json",
                        },
                    });
                    table.buttons().container().appendTo(".custom_buttons");
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
                var date_from_new = date_from_split[1] + '/' + date_from_split[0] + '/' + date_from_split[
                    2];

            }
            if (date_from != '' || date_from != null) {
                if (Date.parse(date_to_new) <= Date.parse(date_from_new)) {

                    alert('Select a Valid Date. End Date should be Greater than Start Date');
                    $(this).val("");
                } else {
                    $('#editable-sample4').DataTable().destroy().clear();
                    "use strict";
                    var table = $("#editable-sample4").DataTable({
                        responsive: true,
                        //   dom: 'lfrBtip',

                        processing: true,
                        serverSide: true,
                        searchable: true,
                        ajax: {
                            url: "finance/getDuePayment?start_date=" + date_from + "&end_date=" +
                                date_to,
                            type: "POST",
                        },
                        scroller: {
                            loadingIndicator: true,
                        },
                        dom: "<'row'<'col-md-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                        buttons: [{
                                extend: "copyHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "excelHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "csvHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "pdfHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                            {
                                extend: "print",
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                },
                            },
                        ],
                        aLengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"],
                        ],
                        iDisplayLength: 100,

                        order: [
                            [0, "desc"]
                        ],

                        language: {
                            lengthMenu: "_MENU_",
                            search: "_INPUT_",
                            url: "common/assets/DataTables/languages/" + language + ".json",
                        },
                    });
                    table.buttons().container().appendTo(".custom_buttons");
                }
            }
        })
    })
</script>