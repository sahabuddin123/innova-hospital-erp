<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Lifecare Diagnostic Center — Standard Export Toolbar Component
 * 
 * Reusable export buttons for all reports:
 * - Print (Dedicated high-contrast layout with signatures)
 * - Export PDF (mPDF with Bengali Unicode)
 * - Export Excel (.xlsx OpenXML workbook)
 * - Export CSV (RFC-4180 with UTF-8 BOM)
 */
$export_key = !empty($export_report_key) ? $export_report_key : (!empty($active_report) ? $active_report : '');
?>
<div class="rep-export-toolbar d-print-none" data-report-key="<?php echo html_escape($export_key); ?>">
    <div class="btn-group rep-export-group" role="group" aria-label="Export Controls">
        <button type="button" class="btn btn-outline-secondary btn-sm rep-export-btn rep-btn-print" data-format="print" title="Print Clean Report Layout">
            <i class="fas fa-print"></i> <span>Print</span>
        </button>
        <button type="button" class="btn btn-outline-danger btn-sm rep-export-btn rep-btn-pdf" data-format="pdf" title="Export to PDF Document">
            <i class="fas fa-file-pdf"></i> <span>PDF</span>
        </button>
        <button type="button" class="btn btn-outline-success btn-sm rep-export-btn rep-btn-excel" data-format="excel" title="Export to Excel Spreadsheet (.xlsx)">
            <i class="fas fa-file-excel"></i> <span>Excel</span>
        </button>
        <button type="button" class="btn btn-outline-info btn-sm rep-export-btn rep-btn-csv" data-format="csv" title="Export to CSV Data File">
            <i class="fas fa-file-csv"></i> <span>CSV</span>
        </button>
    </div>
</div>
