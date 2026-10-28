<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-gradient-light" style="min-height: 100vh;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-file-invoice-dollar text-primary mr-3"></i>
                        <?php echo lang('referrer_statement'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="referral"><?php echo lang('referral_management'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('referrer_statement'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2 mr-2" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Print Statement
                    </button>
                    <a href="referral/wallets" class="btn btn-secondary btn-sm px-3 py-2">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Wallets
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-4">
        <div class="container-fluid">
            <!-- Referrer Header & Date Filter -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h3 class="font-weight-bold mb-1 text-dark">
                                <?php echo html_escape($statement['referrer']->name); ?>
                            </h3>
                            <p class="text-muted mb-0">
                                <span class="badge badge-info mr-2"><?php echo html_escape($statement['referrer']->type); ?></span>
                                <i class="fas fa-phone mr-1"></i> <?php echo html_escape($statement['referrer']->phone); ?>
                                <?php if (!empty($statement['referrer']->email)) { ?>
                                    <span class="mx-2">|</span> <i class="fas fa-envelope mr-1"></i> <?php echo html_escape($statement['referrer']->email); ?>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <form action="referral/statement" method="post" class="form-inline justify-content-md-end">
                                <input type="hidden" name="referrer_id" value="<?php echo $statement['referrer']->id; ?>">
                                <div class="form-group mr-2 mb-2">
                                    <label class="sr-only">Date From</label>
                                    <input type="date" class="form-control form-control-sm" name="date_from" value="<?php echo !empty($date_from) ? $date_from : ''; ?>">
                                </div>
                                <div class="form-group mr-2 mb-2">
                                    <label class="sr-only">Date To</label>
                                    <input type="date" class="form-control form-control-sm" name="date_to" value="<?php echo !empty($date_to) ? $date_to : ''; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mb-2 px-3">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100 bg-white p-3 text-center">
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Opening Balance</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">
                            <?php echo $settings->currency . ' ' . number_format($statement['opening_balance'], 2); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100 bg-white p-3 text-center border-left-success">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Referral Earnings</div>
                        <div class="h5 mb-0 font-weight-bold text-success">
                            +<?php echo $settings->currency . ' ' . number_format($statement['earnings'], 2); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100 bg-white p-3 text-center border-left-danger">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Commission Reversals</div>
                        <div class="h5 mb-0 font-weight-bold text-danger">
                            -<?php echo $settings->currency . ' ' . number_format($statement['reversals'], 2); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100 bg-white p-3 text-center border-left-warning">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Withdrawals</div>
                        <div class="h5 mb-0 font-weight-bold text-warning">
                            -<?php echo $settings->currency . ' ' . number_format($statement['withdrawals'], 2); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0 h-100 bg-gradient-primary text-white p-3 text-center">
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">Closing / Available Balance</div>
                        <div class="h4 mb-0 font-weight-bold">
                            <?php echo $settings->currency . ' ' . number_format($statement['closing_balance'], 2); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ledger Transactions Detail Table -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-list-alt mr-2 text-primary"></i> Statement Transactions Ledger
                    </h4>
                </div>
                <div class="card-body bg-light p-4">
                    <div class="table-responsive">
                        <table class="table table-hover datatables" id="editable-sample" width="100%">
                            <thead>
                                <tr class="bg-light text-uppercase">
                                    <th>Date</th>
                                    <th>Description / Reference</th>
                                    <th>Credit (+)</th>
                                    <th>Debit (-)</th>
                                    <th>Balance After</th>
                                    <th class="no-print text-center" style="width: 140px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($statement['transactions'])) {
                                    foreach ($statement['transactions'] as $txn) { ?>
                                        <tr>
                                            <td><small class="font-weight-bold text-muted"><?php echo date('d-M-Y H:i', $txn->created_at); ?></small></td>
                                            <td>
                                                <div class="font-weight-bold"><?php echo html_escape($txn->description); ?></div>
                                                <?php if (!empty($txn->reference_id)) { ?>
                                                    <small class="text-muted">Ref: <?php echo html_escape($txn->reference_id); ?></small>
                                                <?php } ?>
                                            </td>
                                            <td class="font-weight-bold text-success">
                                                <?php if ($txn->type == 'credit') {
                                                    echo '+' . $settings->currency . ' ' . number_format($txn->amount, 2);
                                                } else {
                                                    echo '-';
                                                } ?>
                                            </td>
                                            <td class="font-weight-bold text-danger">
                                                <?php if ($txn->type == 'debit') {
                                                    echo '-' . $settings->currency . ' ' . number_format($txn->amount, 2);
                                                } else {
                                                    echo '-';
                                                } ?>
                                            </td>
                                            <td class="font-weight-bold text-dark">
                                                <?php echo $settings->currency . ' ' . number_format($txn->balance_after, 2); ?>
                                            </td>
                                            <td class="no-print text-center">
                                                <?php if ($txn->source == 'withdrawal' && !empty($txn->reference_id)) { ?>
                                                    <a href="referral/withdrawalSlip?ref=<?php echo urlencode($txn->reference_id); ?>" class="btn btn-xs btn-outline-primary py-1 px-2 shadow-sm mr-1" target="_blank" title="Print Withdrawal Slip">
                                                        <i class="fas fa-print mr-1"></i> Print Slip
                                                    </a>
                                                    <a href="referral/downloadWithdrawalSlip?ref=<?php echo urlencode($txn->reference_id); ?>" class="btn btn-xs btn-outline-secondary py-1 px-2 shadow-sm" title="Download PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted small">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No transactions found for the selected period.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
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
    });
</script>
