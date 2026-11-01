<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-gradient-light" style="min-height: 100vh;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-wallet text-primary mr-3"></i>
                        <?php echo lang('referral_wallets'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="referral"><?php echo lang('referral_management'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('referral_wallets'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="referral/withdrawals" class="btn btn-success btn-sm px-3 py-2 shadow-sm">
                        <i class="fas fa-hand-holding-usd mr-1"></i> <?php echo lang('withdrawal_requests'); ?>
                    </a>
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
                                <i class="fas fa-coins mr-2 text-warning"></i> Referrer Wallet Balances & Summaries
                            </h3>
                        </div>

                        <div class="card-body bg-light p-4">
                            <div class="table-responsive">
                                <table class="table table-hover datatables" id="editable-sample" width="100%">
                                    <thead>
                                        <tr class="bg-light text-uppercase">
                                            <th>Referrer</th>
                                            <th>Type</th>
                                            <th><?php echo lang('total_earned'); ?></th>
                                            <th><?php echo lang('pending_commission'); ?></th>
                                            <th><?php echo lang('available_balance'); ?></th>
                                            <th><?php echo lang('pending_withdrawal'); ?></th>
                                            <th><?php echo lang('total_withdrawn'); ?></th>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($wallets)) {
                                            foreach ($wallets as $w) { ?>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <a href="referral/statement?referrer_id=<?php echo $w->referrer_id; ?>" class="text-primary">
                                                            <?php echo html_escape($w->referrer_name); ?>
                                                        </a>
                                                        <br><small class="text-muted"><?php echo html_escape($w->referrer_phone); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info px-2 py-1"><?php echo html_escape($w->referrer_type); ?></span>
                                                    </td>
                                                    <td class="font-weight-bold text-dark">
                                                        <?php echo $settings->currency . ' ' . number_format($w->total_earned, 2); ?>
                                                    </td>
                                                    <td class="font-weight-bold text-muted">
                                                        <?php echo $settings->currency . ' ' . number_format($w->pending_commission, 2); ?>
                                                    </td>
                                                    <td class="font-weight-bold text-success" style="font-size: 1.05rem;">
                                                        <?php echo $settings->currency . ' ' . number_format($w->available_balance, 2); ?>
                                                    </td>
                                                    <td class="font-weight-bold text-warning">
                                                        <?php echo $settings->currency . ' ' . number_format($w->pending_withdrawal, 2); ?>
                                                    </td>
                                                    <td class="font-weight-bold text-secondary">
                                                        <?php echo $settings->currency . ' ' . number_format($w->total_withdrawn, 2); ?>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-outline-info btn-sm mr-1" title="Statement" href="referral/statement?referrer_id=<?php echo $w->referrer_id; ?>">
                                                            <i class="fas fa-file-invoice mr-1"></i> Statement
                                                        </a>
                                                        <?php if ($w->available_balance > 0 && $this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) { ?>
                                                            <button type="button" class="btn btn-outline-success btn-sm btn-payout mr-1" data-id="<?php echo $w->referrer_id; ?>" data-name="<?php echo html_escape($w->referrer_name); ?>" data-avail="<?php echo $w->available_balance; ?>">
                                                                <i class="fas fa-hand-holding-usd mr-1"></i> Withdraw
                                                            </button>
                                                        <?php } ?>
                                                        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin'))) { ?>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-adjust" data-id="<?php echo $w->referrer_id; ?>" data-name="<?php echo html_escape($w->referrer_name); ?>" data-avail="<?php echo $w->available_balance; ?>" title="Controlled Balance Adjustment">
                                                                <i class="fas fa-sliders-h mr-1"></i> Adjust
                                                            </button>
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

<!-- Modal for Requesting Withdrawal directly from Wallets list -->
<div class="modal fade" id="modalWithdraw" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-hand-holding-usd mr-2"></i> Request Withdrawal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="referral/requestWithdrawal" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="referrer_id" id="modal_referrer_id">
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Referrer</label>
                        <input type="text" class="form-control" id="modal_referrer_name" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Available Balance</label>
                        <input type="text" class="form-control font-weight-bold text-success" id="modal_available_balance" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Withdrawal Amount (<?php echo $settings->currency; ?>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1" class="form-control" name="amount" id="modal_withdraw_amount" required>
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
                        <input type="text" class="form-control" name="account_number" placeholder="e.g. 01XXXXXXXXX or Bank Account details" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes..."></textarea>
                    </div>

                    <?php if ($this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) { ?>
                        <div class="form-group mb-0 p-2 rounded border bg-light">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="modal_auto_approve" name="auto_approve" value="1">
                                <label class="custom-control-label font-weight-bold text-primary small" for="modal_auto_approve">
                                    <i class="fas fa-magic mr-1"></i> Approve immediately &amp; auto-record to Expense (Ref Commission)
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-3"><i class="fas fa-check mr-1"></i> Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Controlled Balance Adjustment (Admin Only) -->
<div class="modal fade" id="modalAdjust" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-secondary text-white py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-sliders-h mr-2"></i> Controlled Wallet Adjustment</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="referral/adjustWallet" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="referrer_id" id="adj_referrer_id">
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Referrer</label>
                        <input type="text" class="form-control" id="adj_referrer_name" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Current Available Balance</label>
                        <input type="text" class="form-control font-weight-bold text-primary" id="adj_current_balance" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Adjustment Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="type" required>
                            <option value="credit">Credit (+) - Increase Balance</option>
                            <option value="debit">Debit (-) - Decrease Balance</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Adjustment Amount (<?php echo $settings->currency; ?>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="amount" placeholder="0.00" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Reason / Justification <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="2" placeholder="Mandatory audit explanation for this adjustment..." required></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small text-uppercase">Reference / Memo ID</label>
                        <input type="text" class="form-control" name="reference" placeholder="e.g. ADJ-MANUAL-001">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-check mr-1"></i> Apply Adjustment</button>
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
            order: [[4, "desc"]],
            dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'print'],
            pageLength: 25
        });

        $('.btn-payout').on('click', function() {
            var refId = $(this).data('id');
            var refName = $(this).data('name');
            var avail = parseFloat($(this).data('avail'));

            $('#modal_referrer_id').val(refId);
            $('#modal_referrer_name').val(refName);
            $('#modal_available_balance').val('<?php echo $settings->currency; ?> ' + avail.toFixed(2));
            $('#modal_withdraw_amount').attr('max', avail.toFixed(2)).val(avail.toFixed(2));
            $('#modalWithdraw').modal('show');
        });

        $('.btn-adjust').on('click', function() {
            var refId = $(this).data('id');
            var refName = $(this).data('name');
            var avail = parseFloat($(this).data('avail'));

            $('#adj_referrer_id').val(refId);
            $('#adj_referrer_name').val(refName);
            $('#adj_current_balance').val('<?php echo $settings->currency; ?> ' + avail.toFixed(2));
            $('#modalAdjust').modal('show');
        });
    });
</script>
