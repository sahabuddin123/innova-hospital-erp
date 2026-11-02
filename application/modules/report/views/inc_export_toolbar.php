<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Lifecare Diagnostic Center — Standard Export Toolbar Component with Granular RBAC
 * 
 * Conditionally renders export buttons strictly based on user's active permissions:
 * - Print (can_print)
 * - PDF (can_export_pdf)
 * - Excel (can_export_excel)
 * - CSV (can_export_csv)
 */
$export_key = !empty($export_report_key) ? $export_report_key : (!empty($active_report) ? $active_report : '');

// Default permissions
$can_print = true;
$can_pdf   = true;
$can_excel = true;
$can_csv   = true;

if (isset($this->rbac) && !empty($export_key)) {
    $can_print = $this->rbac->canExport($export_key, 'print');
    $can_pdf   = $this->rbac->canExport($export_key, 'pdf');
    $can_excel = $this->rbac->canExport($export_key, 'excel');
    $can_csv   = $this->rbac->canExport($export_key, 'csv');
}
?>
<?php if ($can_print || $can_pdf || $can_excel || $can_csv): ?>
<div class="rep-export-toolbar d-print-none" data-report-key="<?php echo html_escape($export_key); ?>">
    <div class="btn-group rep-export-group" role="group" aria-label="Export Controls">
        <?php if ($can_print): ?>
        <button type="button" class="btn btn-outline-secondary btn-sm rep-export-btn rep-btn-print" data-format="print" title="Print Clean Report Layout">
            <i class="fas fa-print"></i> <span>Print</span>
        </button>
        <?php endif; ?>
        <?php if ($can_pdf): ?>
        <button type="button" class="btn btn-outline-danger btn-sm rep-export-btn rep-btn-pdf" data-format="pdf" title="Export to PDF Document">
            <i class="fas fa-file-pdf"></i> <span>PDF</span>
        </button>
        <?php endif; ?>
        <?php if ($can_excel): ?>
        <button type="button" class="btn btn-outline-success btn-sm rep-export-btn rep-btn-excel" data-format="excel" title="Export to Excel Spreadsheet (.xlsx)">
            <i class="fas fa-file-excel"></i> <span>Excel</span>
        </button>
        <?php endif; ?>
        <?php if ($can_csv): ?>
        <button type="button" class="btn btn-outline-info btn-sm rep-export-btn rep-btn-csv" data-format="csv" title="Export to CSV Data File">
            <i class="fas fa-file-csv"></i> <span>CSV</span>
        </button>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
