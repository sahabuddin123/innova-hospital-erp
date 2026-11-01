<?php
$currency = !empty($settings->currency) ? $settings->currency : 'BDT';
$period_label = !empty($summary['period_label']) ? $summary['period_label'] : ucfirst($period_type);
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        .hdr { font-size: 14pt; font-weight: bold; color: #1e3a8a; }
        .subhdr { font-size: 11pt; color: #475569; }
        .sec-hdr { font-size: 12pt; font-weight: bold; background-color: #f1f5f9; color: #0f172a; border-bottom: 2px solid #cbd5e1; }
        .th { background-color: #f8fafc; font-weight: bold; border: 1px solid #cbd5e1; text-align: left; }
        .td { border: 1px solid #e2e8f0; }
        .td-num { border: 1px solid #e2e8f0; text-align: right; }
        .total-row { font-weight: bold; background-color: #f1f5f9; border-top: 2px solid #94a3b8; }
        .highlight { font-weight: bold; background-color: #dcfce7; color: #166534; }
    </style>
</head>
<body>

<table>
    <tr>
        <td colspan="6" class="hdr">LIFECARE DIAGNOSTIC CENTER</td>
    </tr>
    <tr>
        <td colspan="6" class="subhdr">FINANCIAL PERFORMANCE &amp; PROFIT/LOSS STATEMENT</td>
    </tr>
    <tr>
        <td colspan="6" class="subhdr">Period: <?php echo html_escape($period_label); ?> (<?php echo ucfirst($period_type); ?>)</td>
    </tr>
    <tr>
        <td colspan="6" class="subhdr">Generated Date: <?php echo date('d/m/Y, h:i A'); ?></td>
    </tr>
    <tr><td></td></tr>

    <!-- Summary Metrics -->
    <tr>
        <td colspan="6" class="sec-hdr">EXECUTIVE SUMMARY METRICS</td>
    </tr>
    <tr>
        <th class="th">Metric</th>
        <th class="th">Amount</th>
        <th class="th">Metric</th>
        <th class="th">Amount</th>
        <th class="th">Metric</th>
        <th class="th">Amount</th>
    </tr>
    <tr>
        <td class="td">Total Invoices</td>
        <td class="td-num"><?php echo $summary['total_invoices']; ?></td>
        <td class="td">Total Collection</td>
        <td class="td-num"><?php echo number_format($summary['total_collection'], 2); ?></td>
        <td class="td">Total Expenses</td>
        <td class="td-num"><?php echo number_format($summary['total_expenses'], 2); ?></td>
    </tr>
    <tr>
        <td class="td">Gross Billing</td>
        <td class="td-num"><?php echo number_format($summary['gross_billing'], 2); ?></td>
        <td class="td">Total Due</td>
        <td class="td-num"><?php echo number_format($summary['total_due'], 2); ?></td>
        <td class="td">Gross Profit</td>
        <td class="td-num"><?php echo number_format($summary['gross_profit'], 2); ?></td>
    </tr>
    <tr>
        <td class="td">Total Discount</td>
        <td class="td-num"><?php echo number_format($summary['total_discount'], 2); ?></td>
        <td class="td">Doctor Commission</td>
        <td class="td-num"><?php echo number_format($summary['doctor_commission'], 2); ?></td>
        <td class="td highlight">NET PROFIT</td>
        <td class="td-num highlight"><?php echo number_format($summary['net_profit'], 2); ?></td>
    </tr>
    <tr>
        <td class="td">Net Billing</td>
        <td class="td-num"><?php echo number_format($summary['net_billing'], 2); ?></td>
        <td class="td">Referral Commission</td>
        <td class="td-num"><?php echo number_format($summary['referral_commission'], 2); ?></td>
        <td class="td highlight">Net Margin %</td>
        <td class="td-num highlight"><?php echo number_format($summary['net_margin'], 2); ?>%</td>
    </tr>
    <tr><td></td></tr>

    <!-- Period Breakdown Table -->
    <tr>
        <td colspan="11" class="sec-hdr">PERIOD BREAKDOWN (RECONCILED)</td>
    </tr>
    <tr>
        <th class="th"><?php echo ($period_type == 'daily') ? 'Hour' : (($period_type == 'yearly') ? 'Month' : 'Date'); ?></th>
        <th class="th">Invoices</th>
        <th class="th">Gross Billing</th>
        <th class="th">Discount</th>
        <th class="th">Net Billing</th>
        <th class="th">Collection</th>
        <th class="th">Due</th>
        <th class="th">Doctor Comm</th>
        <th class="th">Referral Comm</th>
        <th class="th">Expense</th>
        <th class="th">Net Profit</th>
    </tr>
    <?php foreach ($breakdown as $row): ?>
        <tr>
            <td class="td"><?php echo html_escape($row['label']); ?></td>
            <td class="td-num"><?php echo $row['invoices']; ?></td>
            <td class="td-num"><?php echo number_format($row['gross_billing'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['discount'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['net_billing'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['collection'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['due'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['doctor_comm'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['referral_comm'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['expense'], 2); ?></td>
            <td class="td-num"><?php echo number_format($row['net_profit'], 2); ?></td>
        </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td class="td">TOTAL</td>
        <td class="td-num"><?php echo $summary['total_invoices']; ?></td>
        <td class="td-num"><?php echo number_format($summary['gross_billing'], 2); ?></td>
        <td class="td-num"><?php echo number_format($summary['total_discount'], 2); ?></td>
        <td class="td-num"><?php echo number_format($summary['net_billing'], 2); ?></td>
        <td class="td-num"><?php echo number_format($summary['total_collection'], 2); ?></td>
        <td class="td-num"><?php echo number_format($summary['total_due'], 2); ?></td>
        <td class="td-num"><?php echo number_format($summary['doctor_commission'], 2); ?></td>
        <td class="td-num"><?php echo number_format($summary['referral_commission'], 2); ?></td>
        <td class="td-num"><?php echo number_format($summary['total_expenses'], 2); ?></td>
        <td class="td-num highlight"><?php echo number_format($summary['net_profit'], 2); ?></td>
    </tr>
    <tr><td></td></tr>

    <!-- Doctor Commission Breakdown -->
    <tr>
        <td colspan="5" class="sec-hdr">DOCTOR COMMISSION BREAKDOWN</td>
    </tr>
    <tr>
        <th class="th">Doctor</th>
        <th class="th">Invoices</th>
        <th class="th">Billing</th>
        <th class="th">Commission</th>
    </tr>
    <?php if (!empty($doctor_breakdown)): ?>
        <?php foreach ($doctor_breakdown as $doc): ?>
            <tr>
                <td class="td"><?php echo html_escape($doc['name']); ?></td>
                <td class="td-num"><?php echo is_array($doc['invoices']) ? count($doc['invoices']) : $doc['invoices']; ?></td>
                <td class="td-num"><?php echo number_format($doc['billing'], 2); ?></td>
                <td class="td-num"><?php echo number_format($doc['commission'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4" class="td">No doctor commission records</td></tr>
    <?php endif; ?>
    <tr><td></td></tr>

    <!-- Referral Commission Breakdown -->
    <tr>
        <td colspan="5" class="sec-hdr">REFERRAL COMMISSION BREAKDOWN</td>
    </tr>
    <tr>
        <th class="th">Referrer</th>
        <th class="th">Patients</th>
        <th class="th">Invoices</th>
        <th class="th">Commission</th>
    </tr>
    <?php if (!empty($referrer_breakdown)): ?>
        <?php foreach ($referrer_breakdown as $ref): ?>
            <tr>
                <td class="td"><?php echo html_escape($ref['name']); ?></td>
                <td class="td-num"><?php echo is_array($ref['patients']) ? count($ref['patients']) : $ref['patients']; ?></td>
                <td class="td-num"><?php echo is_array($ref['invoices']) ? count($ref['invoices']) : $ref['invoices']; ?></td>
                <td class="td-num"><?php echo number_format($ref['commission'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4" class="td">No referral commission records</td></tr>
    <?php endif; ?>
    <tr><td></td></tr>

    <!-- Hospital Expenses -->
    <tr>
        <td colspan="3" class="sec-hdr">HOSPITAL EXPENSES</td>
    </tr>
    <tr>
        <th class="th">Expense Category</th>
        <th class="th">Amount</th>
    </tr>
    <?php if (!empty($expense_breakdown)): ?>
        <?php foreach ($expense_breakdown as $cat_name => $cat_amt): ?>
            <tr>
                <td class="td"><?php echo html_escape($cat_name); ?></td>
                <td class="td-num"><?php echo number_format($cat_amt, 2); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    <tr class="total-row">
        <td class="td">Total Expenses</td>
        <td class="td-num"><?php echo number_format($summary['total_expenses'], 2); ?></td>
    </tr>
    <tr><td></td></tr>

    <!-- Service Performance -->
    <tr>
        <td colspan="8" class="sec-hdr">SERVICE / TEST PERFORMANCE (TEST-WISE INCOME &amp; REFERRAL COMMISSION)</td>
    </tr>
    <tr>
        <th class="th">#</th>
        <th class="th">Service / Test</th>
        <th class="th">Qty</th>
        <th class="th">Gross Amount</th>
        <th class="th">Discount</th>
        <th class="th">Net Amount</th>
        <th class="th">Ref Commission</th>
        <th class="th">Hospital Net</th>
    </tr>
    <?php if (!empty($service_perf)): ?>
        <?php $i = 1; foreach ($service_perf as $s): ?>
            <tr>
                <td class="td"><?php echo $i++; ?></td>
                <td class="td"><?php echo html_escape($s['name']); ?></td>
                <td class="td-num"><?php echo $s['qty']; ?></td>
                <td class="td-num"><?php echo number_format($s['gross'], 2); ?></td>
                <td class="td-num"><?php echo number_format($s['discount'], 2); ?></td>
                <td class="td-num"><?php echo number_format($s['net'], 2); ?></td>
                <td class="td-num"><?php echo number_format($s['ref_comm'], 2); ?></td>
                <td class="td-num"><?php echo number_format($s['hospital_net'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    <tr><td></td></tr>

    <!-- Payment Methods -->
    <tr>
        <td colspan="3" class="sec-hdr">PAYMENT METHOD WISE COLLECTION</td>
    </tr>
    <tr>
        <th class="th">Payment Method</th>
        <th class="th">Amount</th>
        <th class="th">Percentage</th>
    </tr>
    <?php foreach ($methods as $m): ?>
        <tr>
            <td class="td"><?php echo html_escape($m['name']); ?></td>
            <td class="td-num"><?php echo number_format($m['amount'], 2); ?></td>
            <td class="td-num"><?php echo number_format($m['percentage'], 1); ?>%</td>
        </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td class="td">Total Collection</td>
        <td class="td-num"><?php echo number_format($summary['total_collection'], 2); ?></td>
        <td class="td-num">100%</td>
    </tr>
</table>

</body>
</html>
