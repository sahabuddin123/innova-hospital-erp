<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-file-invoice text-info mr-2"></i><?php echo lang('journal_entry_register'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Double-Entry Journal Voucher Register</small>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#createJournalModal">
                        <i class="fas fa-plus-circle mr-1"></i> New Journal Entry
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2 shadow-sm" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="container-fluid">
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'journal_register']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/journalEntryRegister" class="row align-items-center">
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">-- All Status --</option>
                            <option value="posted" <?php echo ($filters['status'] ?? '') === 'posted' ? 'selected' : ''; ?>>Posted</option>
                            <option value="reversed" <?php echo ($filters['status'] ?? '') === 'reversed' ? 'selected' : ''; ?>>Reversed</option>
                            <option value="draft" <?php echo ($filters['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold">Journal Type</label>
                        <select name="journal_type" class="form-control form-control-sm">
                            <option value="">-- All Types --</option>
                            <option value="general" <?php echo ($filters['journal_type'] ?? '') === 'general' ? 'selected' : ''; ?>>General</option>
                            <option value="receipt" <?php echo ($filters['journal_type'] ?? '') === 'receipt' ? 'selected' : ''; ?>>Receipt</option>
                            <option value="payment" <?php echo ($filters['journal_type'] ?? '') === 'payment' ? 'selected' : ''; ?>>Payment</option>
                            <option value="sales" <?php echo ($filters['journal_type'] ?? '') === 'sales' ? 'selected' : ''; ?>>Sales</option>
                            <option value="purchase" <?php echo ($filters['journal_type'] ?? '') === 'purchase' ? 'selected' : ''; ?>>Purchase</option>
                            <option value="adjustment" <?php echo ($filters['journal_type'] ?? '') === 'adjustment' ? 'selected' : ''; ?>>Adjustment</option>
                            <option value="reversal" <?php echo ($filters['journal_type'] ?? '') === 'reversal' ? 'selected' : ''; ?>>Reversal</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary w-100 mr-1 mt-4">Filter</button>
                        <a href="finance/journalEntryRegister" class="btn btn-sm btn-outline-secondary mt-4">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-list-alt text-muted"></i> Posted & Recorded Journal Vouchers</h5>
                    <span class="badge badge-info"><?php echo count($entries); ?> Entries Recorded</span>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th style="width: 130px;">Journal #</th>
                                    <th style="width: 100px;">Date</th>
                                    <th style="width: 90px;">Type</th>
                                    <th>Description / Lines Detail</th>
                                    <th style="width: 110px;">Reference</th>
                                    <th class="text-right" style="width: 120px;">Total Debit (৳)</th>
                                    <th class="text-right" style="width: 120px;">Total Credit (৳)</th>
                                    <th class="text-center" style="width: 90px;">Status</th>
                                    <th class="text-center" style="width: 90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($entries)): ?>
                                    <?php foreach ($entries as $e): ?>
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold text-dark"><?php echo $e['journal_number']; ?></span>
                                                <div class="small text-muted"><?php echo htmlspecialchars($e['creator_name'] ?? 'system'); ?></div>
                                            </td>
                                            <td><?php echo date('d-m-Y', strtotime($e['date'])); ?></td>
                                            <td><span class="badge badge-light border"><?php echo ucfirst($e['journal_type']); ?></span></td>
                                            <td>
                                                <div class="font-weight-bold mb-1"><?php echo htmlspecialchars($e['description']); ?></div>
                                                <!-- Line Items Table -->
                                                <table class="table table-sm table-bordered bg-white mb-0" style="font-size: 11px;">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Account</th>
                                                            <th>Narration</th>
                                                            <th class="text-right" style="width: 90px;">Dr</th>
                                                            <th class="text-right" style="width: 90px;">Cr</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($e['items'] as $item): ?>
                                                            <tr>
                                                                <td>
                                                                    <span class="font-weight-bold">[<?php echo $item['account_code']; ?>]</span> 
                                                                    <?php echo htmlspecialchars($item['account_name']); ?>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                                                <td class="text-right acc-dr font-weight-bold"><?php echo $item['debit'] > 0 ? number_format($item['debit'], 2) : '-'; ?></td>
                                                                <td class="text-right acc-cr font-weight-bold"><?php echo $item['credit'] > 0 ? number_format($item['credit'], 2) : '-'; ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td><?php echo htmlspecialchars($e['reference_number'] ?: '-'); ?></td>
                                            <td class="acc-amount acc-dr font-weight-bold">৳ <?php echo number_format($e['total_debit'], 2); ?></td>
                                            <td class="acc-amount acc-cr font-weight-bold">৳ <?php echo number_format($e['total_credit'], 2); ?></td>
                                            <td class="text-center">
                                                <?php if ($e['status'] === 'posted'): ?>
                                                    <span class="badge badge-success">POSTED</span>
                                                <?php elseif ($e['status'] === 'reversed'): ?>
                                                    <span class="badge badge-danger">REVERSED</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">DRAFT</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($e['status'] === 'posted'): ?>
                                                    <button type="button" class="btn btn-xs btn-outline-danger reverse-journal-btn" data-id="<?php echo $e['id']; ?>" data-num="<?php echo $e['journal_number']; ?>" title="Reverse Journal Entry">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted small">&mdash;</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No journal entries recorded yet. Click "New Journal Entry" above to post a balanced voucher.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Signatures for print -->
            <div class="acc-signatures-block">
                <div class="acc-sig-grid">
                    <div class="acc-sig-line">Prepared By<br><small>Senior Accountant</small></div>
                    <div class="acc-sig-line">Checked & Verified By<br><small>Head of Accounts & Finance</small></div>
                    <div class="acc-sig-line">Approved By<br><small>Managing Director / CFO</small></div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Create Journal Modal -->
<div class="modal fade" id="createJournalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="createJournalForm" method="post" action="finance/createJournalEntry">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> New Double-Entry Journal Voucher</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold small">Journal Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold small">Journal Type <span class="text-danger">*</span></label>
                            <select name="journal_type" class="form-control form-control-sm" required>
                                <option value="general">General Journal</option>
                                <option value="receipt">Receipt Journal</option>
                                <option value="payment">Payment Journal</option>
                                <option value="sales">Sales Journal</option>
                                <option value="purchase">Purchase Journal</option>
                                <option value="adjustment">Adjustment Journal</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold small">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control form-control-sm" placeholder="e.g. BILL-999 or REC-102">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Description / Header Narration <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Purpose or explanation of this journal entry" required>
                    </div>

                    <div class="border rounded p-2 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold small m-0">Journal Line Items (Debit must equal Credit)</label>
                            <button type="button" class="btn btn-xs btn-outline-info" id="addLineBtn"><i class="fas fa-plus"></i> Add Line</button>
                        </div>
                        <table class="table table-sm table-bordered bg-white mb-2" id="journalLinesTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40%;">Account <span class="text-danger">*</span></th>
                                    <th>Narration</th>
                                    <th style="width: 20%;" class="text-right">Debit (Dr) ৳</th>
                                    <th style="width: 20%;" class="text-right">Credit (Cr) ৳</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Line 1 -->
                                <tr class="journal-line-row">
                                    <td>
                                        <select class="form-control form-control-sm acc-select" required>
                                            <option value="">-- Select Account --</option>
                                            <?php foreach ($accounts as $a): ?>
                                                <option value="<?php echo $a['id']; ?>">[<?php echo $a['account_code']; ?>] <?php echo htmlspecialchars($a['account_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm line-desc" placeholder="Narration"></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm text-right line-dr" value="0.00"></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm text-right line-cr" value="0.00"></td>
                                    <td class="text-center"><button type="button" class="btn btn-xs btn-link text-danger remove-line-btn">&times;</button></td>
                                </tr>
                                <!-- Line 2 -->
                                <tr class="journal-line-row">
                                    <td>
                                        <select class="form-control form-control-sm acc-select" required>
                                            <option value="">-- Select Account --</option>
                                            <?php foreach ($accounts as $a): ?>
                                                <option value="<?php echo $a['id']; ?>">[<?php echo $a['account_code']; ?>] <?php echo htmlspecialchars($a['account_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm line-desc" placeholder="Narration"></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm text-right line-dr" value="0.00"></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm text-right line-cr" value="0.00"></td>
                                    <td class="text-center"><button type="button" class="btn btn-xs btn-link text-danger remove-line-btn">&times;</button></td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="2" class="text-right">Totals:</td>
                                    <td class="text-right text-primary" id="footerTotalDr">৳ 0.00</td>
                                    <td class="text-right text-success" id="footerTotalCr">৳ 0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">Balance Check:</td>
                                    <td colspan="2" class="text-center" id="balanceStatusBadge">
                                        <span class="badge badge-success">Balanced (0.00 Diff)</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-info" id="submitJournalBtn">Post Journal Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('.datatable-export')) {
        $('.datatable-export').DataTable().destroy();
    }
    $('.datatable-export').DataTable({
        responsive: true,
        pageLength: 20,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Journal_Register' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Journal_Register' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Journal_Register' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });

    function calculateTotals() {
        var totalDr = 0;
        var totalCr = 0;
        $('.journal-line-row').each(function() {
            var dr = parseFloat($(this).find('.line-dr').val()) || 0;
            var cr = parseFloat($(this).find('.line-cr').val()) || 0;
            totalDr += dr;
            totalCr += cr;
        });

        $('#footerTotalDr').text('৳ ' + totalDr.toFixed(2));
        $('#footerTotalCr').text('৳ ' + totalCr.toFixed(2));

        var diff = Math.abs(totalDr - totalCr);
        if (diff < 0.009 && totalDr > 0) {
            $('#balanceStatusBadge').html('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Balanced (৳ 0.00 Diff)</span>');
            $('#submitJournalBtn').prop('disabled', false);
        } else {
            $('#balanceStatusBadge').html('<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Out of Balance (৳ ' + diff.toFixed(2) + ' Diff)</span>');
            $('#submitJournalBtn').prop('disabled', true);
        }
    }

    $(document).on('input', '.line-dr, .line-cr', function() {
        calculateTotals();
    });

    $('#addLineBtn').on('click', function() {
        var sampleRow = $('.journal-line-row:first').clone();
        sampleRow.find('input').val('');
        sampleRow.find('.line-dr, .line-cr').val('0.00');
        sampleRow.find('select').val('');
        $('#journalLinesTable tbody').append(sampleRow);
        calculateTotals();
    });

    $(document).on('click', '.remove-line-btn', function() {
        if ($('.journal-line-row').length > 2) {
            $(this).closest('tr').remove();
            calculateTotals();
        } else {
            alert('A double-entry journal requires at least two line items.');
        }
    });

    $('#createJournalForm').on('submit', function(e) {
        e.preventDefault();
        var items = [];
        $('.journal-line-row').each(function() {
            var accId = $(this).find('.acc-select').val();
            var desc = $(this).find('.line-desc').val();
            var dr = parseFloat($(this).find('.line-dr').val()) || 0;
            var cr = parseFloat($(this).find('.line-cr').val()) || 0;
            if (accId) {
                items.push({
                    account_id: accId,
                    description: desc,
                    debit: dr,
                    credit: cr
                });
            }
        });

        var formData = $(this).serializeArray();
        formData.push({ name: 'items', value: JSON.stringify(items) });

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(resp) {
                if (resp.status) {
                    location.reload();
                } else {
                    alert(resp.message || 'Error saving journal entry.');
                }
            },
            error: function() {
                alert('Server error occurred.');
            }
        });
    });

    $('.reverse-journal-btn').on('click', function() {
        var id = $(this).data('id');
        var num = $(this).data('num');
        var reason = prompt('Please enter the reason for reversing ' + num + ':');
        if (reason) {
            $.ajax({
                url: 'finance/reverseJournalEntryAction',
                type: 'POST',
                data: { journal_id: id, reason: reason },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status) {
                        alert('Journal entry reversed successfully: ' + resp.reversal_journal_number);
                        location.reload();
                    } else {
                        alert(resp.message || 'Reversal failed.');
                    }
                }
            });
        }
    });
});
</script>
