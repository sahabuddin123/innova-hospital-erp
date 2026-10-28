<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-gradient-light" style="min-height: 100vh;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-hand-holding-usd text-primary mr-3"></i>
                        <?php echo lang('withdrawal_requests'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="referral"><?php echo lang('referral_management'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('withdrawal_requests'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <?php if ($this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) { ?>
                        <button type="button" class="btn btn-primary btn-sm px-4 py-3 shadow-sm" data-toggle="modal" data-target="#modalNewWithdrawal">
                            <i class="fa fa-plus-circle mr-1"></i> Request Withdrawal
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title font-weight-bold mb-0 text-dark">
                                <i class="fas fa-money-check-alt mr-2 text-success"></i> Withdrawal Management
                            </h3>
                        </div>

                        <div class="card-body bg-light p-4">
                            <div class="table-responsive">
                                <table class="table table-hover datatables" id="editable-sample" width="100%">
                                    <thead>
                                        <tr class="bg-light text-uppercase">
                                            <th>ID</th>
                                            <th>Referrer</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Account Info</th>
                                            <th>Requested Date</th>
                                            <th>Status</th>
                                            <th>Payment Ref</th>
                                            <th class="no-print">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($withdrawals)) {
                                            foreach ($withdrawals as $w) { ?>
                                                <tr>
                                                    <td>#<?php echo $w->id; ?></td>
                                                    <td class="font-weight-bold">
                                                        <a href="referral/statement?referrer_id=<?php echo $w->referrer_id; ?>" class="text-primary">
                                                            <?php echo html_escape($w->referrer_name); ?>
                                                        </a>
                                                        <br><small class="text-muted"><?php echo html_escape($w->referrer_phone); ?></small>
                                                    </td>
                                                    <td class="font-weight-bold text-dark" style="font-size: 1.05rem;">
                                                        <?php echo $settings->currency . ' ' . number_format($w->amount, 2); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info px-2 py-1"><?php echo html_escape($w->payment_method); ?></span>
                                                    </td>
                                                    <td>
                                                        <code><?php echo html_escape($w->account_number); ?></code>
                                                        <?php if (!empty($w->notes)) { ?>
                                                            <br><small class="text-muted"><?php echo html_escape($w->notes); ?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td><small class="text-muted"><?php echo !empty($w->created_at) ? date('d-M-Y H:i', $w->created_at) : '-'; ?></small></td>
                                                    <td>
                                                        <?php if ($w->status == 'Pending') { ?>
                                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-hourglass-half"></i> Pending</span>
                                                        <?php } elseif ($w->status == 'Approved') { ?>
                                                            <span class="badge badge-primary px-2 py-1"><i class="fas fa-check"></i> Approved</span>
                                                        <?php } elseif ($w->status == 'Paid') { ?>
                                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double"></i> Paid</span>
                                                        <?php } elseif ($w->status == 'Rejected') { ?>
                                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-times"></i> Rejected</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($w->payment_reference)) { ?>
                                                            <small class="font-weight-bold"><?php echo html_escape($w->payment_reference); ?></small>
                                                        <?php } else { ?>
                                                            <span class="text-muted">-</span>
                                                        <?php } ?>
                                                        <?php if (!empty($w->admin_notes)) { ?>
                                                            <br><small class="text-muted"><?php echo html_escape($w->admin_notes); ?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'))) { ?>
                                                            <?php if ($w->status == 'Pending' && $this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) { ?>
                                                                <button type="button" class="btn btn-outline-primary btn-sm btn-status mr-1" data-id="<?php echo $w->id; ?>" data-action="Approved" data-title="Approve Withdrawal">
                                                                    Approve
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger btn-sm btn-status" data-id="<?php echo $w->id; ?>" data-action="Rejected" data-title="Reject Withdrawal">
                                                                    Reject
                                                                </button>
                                                            <?php } elseif ($w->status == 'Approved' && $this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) { ?>
                                                                <button type="button" class="btn btn-outline-success btn-sm btn-status mr-1" data-id="<?php echo $w->id; ?>" data-action="Paid" data-title="Mark as Paid">
                                                                    Mark Paid
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger btn-sm btn-status" data-id="<?php echo $w->id; ?>" data-action="Rejected" data-title="Reject Withdrawal">
                                                                    Reject
                                                                </button>
                                                            <?php } elseif ($w->status == 'Paid') { ?>
                                                                <a href="referral/withdrawalSlip?id=<?php echo $w->id; ?>" class="btn btn-outline-primary btn-sm mr-1" target="_blank" title="Print Withdrawal Slip">
                                                                    <i class="fas fa-print mr-1"></i> Print Slip
                                                                </a>
                                                                <a href="referral/downloadWithdrawalSlip?id=<?php echo $w->id; ?>" class="btn btn-outline-secondary btn-sm" title="Download PDF">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                            <?php } else { ?>
                                                                <span class="text-muted small"><?php echo html_escape($w->status); ?></span>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal: New Withdrawal Request -->
<div class="modal fade" id="modalNewWithdrawal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-hand-holding-usd mr-2"></i> Submit Withdrawal Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="referral/requestWithdrawal" method="post">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Select Referrer <span class="text-danger">*</span></label>
                        <select class="form-control" name="referrer_id" id="new_req_referrer" required>
                            <option value="">-- Choose Referrer --</option>
                            <?php foreach ($referrers as $ref) { 
                                $w_bal = $this->referral_model->getWalletByReferrerId($ref->id);
                                $avail = !empty($w_bal) ? floatval($w_bal->available_balance) : 0;
                            ?>
                                <option value="<?php echo $ref->id; ?>" data-avail="<?php echo $avail; ?>">
                                    <?php echo html_escape($ref->name); ?> (Avail: <?php echo $settings->currency . ' ' . number_format($avail, 2); ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Available Balance</label>
                        <input type="text" class="form-control font-weight-bold text-success" id="new_req_avail" readonly value="<?php echo $settings->currency; ?> 0.00">
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Withdrawal Amount (<?php echo $settings->currency; ?>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1" class="form-control" name="amount" id="new_req_amount" placeholder="0.00" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_method" required>
                            <option value="bKash">bKash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Bank">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Account / Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_number" placeholder="01XXXXXXXXX or Bank Account details" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small text-uppercase">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-check mr-1"></i> Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Action on Status (Approve / Paid / Reject) -->
<div class="modal fade" id="modalStatusAction" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-3" id="statusModalHeader">
                <h5 class="modal-title font-weight-bold text-white" id="statusModalTitle">Process Withdrawal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="referral/updateWithdrawalStatus" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="status_w_id">
                    <input type="hidden" name="status" id="status_w_action">

                    <p id="statusActionMsg" class="font-weight-bold"></p>

                    <div class="form-group mb-3" id="refDiv">
                        <label class="font-weight-bold text-dark small text-uppercase">Payment Reference / Txn ID</label>
                        <input type="text" class="form-control" name="payment_reference" placeholder="e.g. bKash TrxID or Bank Voucher #">
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small text-uppercase">Admin Remarks / Reason</label>
                        <textarea class="form-control" name="admin_notes" rows="2" placeholder="Add administrative notes or rejection reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="statusSubmitBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#editable-sample').DataTable({
            responsive: true,
            order: [[0, "desc"]],
            dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'print'],
            pageLength: 25
        });

        $('#new_req_referrer').on('change', function() {
            var avail = parseFloat($(this).find(':selected').data('avail')) || 0;
            $('#new_req_avail').val('<?php echo $settings->currency; ?> ' + avail.toFixed(2));
            $('#new_req_amount').attr('max', avail.toFixed(2));
        });

        $('.btn-status').on('click', function() {
            var wid = $(this).data('id');
            var action = $(this).data('action');
            var title = $(this).data('title');

            $('#status_w_id').val(wid);
            $('#status_w_action').val(action);
            $('#statusModalTitle').text(title);

            var header = $('#statusModalHeader');
            var btn = $('#statusSubmitBtn');
            header.removeClass('bg-gradient-primary bg-gradient-success bg-gradient-danger');
            btn.removeClass('btn-primary btn-success btn-danger');

            if (action === 'Paid') {
                header.addClass('bg-gradient-success');
                btn.addClass('btn-success').text('Confirm Paid');
                $('#statusActionMsg').html('Are you sure you want to mark withdrawal <strong>#' + wid + '</strong> as <span class="text-success">Paid</span>? This will debit the reserved amount from the wallet and finalize the payout.');
                $('#refDiv').show();
            } else if (action === 'Approved') {
                header.addClass('bg-gradient-primary');
                btn.addClass('btn-primary').text('Confirm Approval');
                $('#statusActionMsg').html('Are you sure you want to <span class="text-primary">Approve</span> withdrawal <strong>#' + wid + '</strong>?');
                $('#refDiv').hide();
            } else if (action === 'Rejected') {
                header.addClass('bg-gradient-danger');
                btn.addClass('btn-danger').text('Confirm Rejection');
                $('#statusActionMsg').html('Are you sure you want to <span class="text-danger">Reject</span> withdrawal <strong>#' + wid + '</strong>? The reserved amount will be returned to the Referrer\'s Available Balance immediately.');
                $('#refDiv').hide();
            }

            $('#modalStatusAction').modal('show');
        });
    });
</script>
