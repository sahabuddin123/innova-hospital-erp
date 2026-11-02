<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lifecare Diagnostic Center — Standard Browser Print Engine
 * 
 * Generates high-fidelity, print-optimized HTML documents for browser printing:
 * - Dedicated print layout with repeated table headers (thead) across pages
 * - Hospital branding, logo, contact, metadata, and filter badges
 * - Printable KPI executive summary section
 * - Professional table formatting with alignment and currency styling
 * - Signatures block (Prepared By, Verified By, Authorized Signatory)
 * - Auto-print trigger and responsive on-screen control toolbar (hidden when printing)
 * 
 * @package    Lifecare Hospital ERP
 * @subpackage Libraries
 */
class ReportPrint
{
    protected $ci;

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->ci =& get_instance();
        }
    }

    /**
     * Render and output printable HTML document to browser
     * 
     * @param array $payload Standardized report payload
     * @param bool  $autoprint Whether to auto-trigger window.print()
     * @return void
     */
    public function render(array $payload, $autoprint = false)
    {
        $org_name = !empty($payload['branding']['hospital_name']) ? $payload['branding']['hospital_name'] : 'Lifecare Diagnostic Center';
        $address = !empty($payload['branding']['address']) ? $payload['branding']['address'] : '';
        $phone = !empty($payload['branding']['phone']) ? $payload['branding']['phone'] : '';
        $logo = !empty($payload['branding']['logo']) ? $payload['branding']['logo'] : '';
        $currency = !empty($payload['currency']) ? $payload['currency'] : '৳';
        $report_title = !empty($payload['report_title']) ? $payload['report_title'] : 'Report';
        $period_label = !empty($payload['date_range']) ? $payload['date_range'] : (!empty($payload['period_label']) ? $payload['period_label'] : '');
        $generated_at = !empty($payload['generated_at']) ? $payload['generated_at'] : date('d/m/Y h:i A');
        $generated_by = !empty($payload['generated_by']) ? $payload['generated_by'] : 'User';

        $orientation = !empty($payload['orientation']) ? strtolower($payload['orientation']) : 'portrait';
        $paper_size = !empty($payload['paper_size']) ? strtoupper($payload['paper_size']) : 'A4';
        $page_css_size = ($orientation === 'landscape' || $orientation === 'l') ? $paper_size . ' landscape' : $paper_size . ' portrait';

        $columns = !empty($payload['columns']) ? $payload['columns'] : [];
        $rows = !empty($payload['rows']) ? $payload['rows'] : [];

        // Build HTML
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($report_title) . ' - ' . htmlspecialchars($org_name) . '</title>
    <base href="' . base_url() . '">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @page {
            size: ' . $page_css_size . ';
            margin: 12mm 10mm 15mm 10mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1f2937;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .print-page {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Screen Control Toolbar */
        .no-print-toolbar {
            max-width: 1000px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e3a8a;
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .toolbar-btn {
            background: #ffffff;
            color: #1e3a8a;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: background 0.15s ease;
        }
        .toolbar-btn:hover { background: #f1f5f9; }
        .toolbar-btn-close { background: rgba(255,255,255,0.2); color: #ffffff; }
        .toolbar-btn-close:hover { background: rgba(255,255,255,0.3); }

        /* Report Header */
        .rep-header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .rep-logo-title h1 {
            margin: 0 0 2px 0;
            font-size: 20px;
            color: #1e3a8a;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .rep-logo-title p {
            margin: 0;
            font-size: 11px;
            color: #4b5563;
        }
        .rep-meta-block {
            text-align: right;
        }
        .rep-meta-block h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
            color: #111827;
            font-weight: 700;
        }
        .rep-meta-block p {
            margin: 0;
            font-size: 10.5px;
            color: #6b7280;
        }

        /* Filter Badges */
        .filters-wrap {
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .filter-badge {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            color: #334155;
        }

        /* KPI Summary Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .kpi-label {
            font-size: 9.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .kpi-val {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }
        .kpi-meta {
            font-size: 9px;
            color: #94a3b8;
        }

        /* Table */
        table.print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.print-table th {
            background: #1e3a8a;
            color: #ffffff;
            font-weight: 700;
            font-size: 10.5px;
            padding: 6px 8px;
            border: 1px solid #1e3a8a;
            white-space: nowrap;
        }
        table.print-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
            vertical-align: middle;
        }
        table.print-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        table.print-table tr.totals-row td {
            background-color: #e2e8f0;
            font-weight: 700;
            border-top: 1.5px solid #000000;
            border-bottom: 2.5px double #000000;
            color: #0f172a;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Signatures Block */
        .signatures-wrap {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sig-col {
            text-align: center;
            width: 25%;
        }
        .sig-line {
            border-top: 1px dotted #9ca3af;
            padding-top: 4px;
            font-size: 10px;
            color: #4b5563;
            font-weight: 600;
        }

        /* Footer */
        .rep-footer {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            color: #9ca3af;
        }

        /* Print Specifics */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .print-page {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print, .no-print-toolbar {
                display: none !important;
            }
            thead {
                display: table-header-group;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Toolbar -->
    <div class="no-print-toolbar no-print">
        <div style="font-weight: 600; font-size: 13px;">
            <i class="fas fa-print"></i> Print Preview &bull; ' . htmlspecialchars($report_title) . '
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="toolbar-btn"><i class="fas fa-print"></i> Print Document</button>
            <button onclick="window.close()" class="toolbar-btn toolbar-btn-close"><i class="fas fa-times"></i> Close</button>
        </div>
    </div>

    <div class="print-page">

        <!-- Report Header -->
        <div class="rep-header">
            <div class="rep-logo-title">
                <h1>' . htmlspecialchars($org_name) . '</h1>
                ' . (!empty($address) ? '<p>' . htmlspecialchars($address) . '</p>' : '') . '
                ' . (!empty($phone) ? '<p>Tel / Hotline: ' . htmlspecialchars($phone) . '</p>' : '') . '
            </div>
            <div class="rep-meta-block">
                <h2>' . htmlspecialchars($report_title) . (!empty($payload['report_subtitle']) ? ' - ' . htmlspecialchars($payload['report_subtitle']) : '') . '</h2>
                ' . (!empty($period_label) ? '<p>Period: <strong>' . htmlspecialchars($period_label) . '</strong></p>' : '') . '
                <p>Generated: ' . htmlspecialchars($generated_at) . ' | User: ' . htmlspecialchars($generated_by) . '</p>
            </div>
        </div>';

        // Applied Filters
        if (!empty($payload['filters']) && is_array($payload['filters'])) {
            $f_items = [];
            foreach ($payload['filters'] as $fl => $fv) {
                if ($fv !== null && $fv !== '') {
                    $f_items[] = '<span class="filter-badge"><strong>' . htmlspecialchars($fl) . ':</strong> ' . htmlspecialchars($fv) . '</span>';
                }
            }
            if (!empty($f_items)) {
                $html .= '<div class="filters-wrap">' . implode('', $f_items) . '</div>';
            }
        }

        // Summary Cards
        if (!empty($payload['summary']) && is_array($payload['summary'])) {
            $html .= '<div class="kpi-grid">';
            foreach ($payload['summary'] as $s) {
                $label = !empty($s['label']) ? $s['label'] : 'Metric';
                $val = isset($s['value']) ? $s['value'] : '0';
                $type = !empty($s['type']) ? $s['type'] : 'currency';
                $formatted_val = ($type === 'currency' && is_numeric($val)) ? $currency . ' ' . number_format((float)$val, 2) :
                                 ((($type === 'integer' || $type === 'number') && is_numeric($val)) ? number_format((int)$val) : htmlspecialchars($val));
                $meta = !empty($s['meta']) ? '<div class="kpi-meta">' . htmlspecialchars($s['meta']) . '</div>' : '';

                $html .= '<div class="kpi-card">';
                $html .= '<div class="kpi-label">' . htmlspecialchars($label) . '</div>';
                $html .= '<div class="kpi-val">' . $formatted_val . '</div>';
                $html .= $meta;
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        // Table
        $html .= '<table class="print-table">';
        $html .= '<thead><tr>';
        foreach ($columns as $col) {
            $align = !empty($col['align']) ? $col['align'] : 'left';
            $label = !empty($col['label']) ? $col['label'] : ucfirst(str_replace('_', ' ', $col['key']));
            $html .= '<th class="text-' . $align . '">' . htmlspecialchars($label) . '</th>';
        }
        $html .= '</tr></thead>';

        // Table Body
        $html .= '<tbody>';
        if (empty($rows)) {
            $html .= '<tr><td colspan="' . count($columns) . '" class="text-center" style="padding: 24px; color: #94a3b8;">No records found for the selected period and criteria.</td></tr>';
        } else {
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ($columns as $col) {
                    $key = $col['key'];
                    $align = !empty($col['align']) ? $col['align'] : 'left';
                    $type = !empty($col['type']) ? $col['type'] : 'string';

                    $val = '';
                    if (is_object($row) && isset($row->$key)) {
                        $val = $row->$key;
                    } elseif (is_array($row) && isset($row[$key])) {
                        $val = $row[$key];
                    }

                    $cell_content = $this->formatCellForPrint($val, $type, $currency);
                    $html .= '<td class="text-' . $align . '">' . $cell_content . '</td>';
                }
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';

        // Totals Row
        if (!empty($payload['totals']) && is_array($payload['totals'])) {
            $html .= '<tfoot><tr class="totals-row">';
            $first = true;
            foreach ($columns as $col) {
                $key = $col['key'];
                $align = !empty($col['align']) ? $col['align'] : 'left';
                $type = !empty($col['type']) ? $col['type'] : 'number';

                if (isset($payload['totals'][$key])) {
                    $tval = $payload['totals'][$key];
                    $cell_content = $this->formatCellForPrint($tval, $type, $currency);
                    $html .= '<td class="text-' . $align . '">' . $cell_content . '</td>';
                } else {
                    $html .= '<td class="text-' . $align . '">' . ($first ? 'TOTAL' : '') . '</td>';
                }
                $first = false;
            }
            $html .= '</tr></tfoot>';
        }

        $html .= '</table>';

        // Notes
        if (!empty($payload['notes'])) {
            $notes_text = is_array($payload['notes']) ? implode('<br>', array_map('htmlspecialchars', $payload['notes'])) : htmlspecialchars($payload['notes']);
            $html .= '<div style="margin-top: 14px; font-size: 10px; color: #64748b;"><strong>Notes:</strong><br>' . $notes_text . '</div>';
        }

        // Signatures Block
        $html .= '<div class="signatures-wrap">
            <div class="sig-col"><div class="sig-line">Prepared By</div></div>
            <div class="sig-col"><div class="sig-line">Verified By</div></div>
            <div class="sig-col"><div class="sig-line">Authorized Signatory</div></div>
        </div>';

        // Footer
        $html .= '<div class="rep-footer">
            <div>Confidential &bull; Lifecare Hospital ERP & Diagnostic Center</div>
            <div>Generated: ' . htmlspecialchars($generated_at) . ' | By: ' . htmlspecialchars($generated_by) . '</div>
        </div>';

        $html .= '</div>'; // .print-page

        if ($autoprint) {
            $html .= '<script>window.onload = function() { setTimeout(function() { window.print(); }, 400); };</script>';
        }

        $html .= '</body></html>';

        echo $html;
        exit;
    }

    /**
     * Format cell for printable view
     */
    protected function formatCellForPrint($val, $type = 'string', $currency = '৳')
    {
        if ($val === null || $val === '') {
            return '&mdash;';
        }

        switch ($type) {
            case 'currency':
                return is_numeric($val) ? htmlspecialchars($currency) . ' ' . number_format((float)$val, 2) : htmlspecialchars($val);
            case 'integer':
            case 'number':
                return is_numeric($val) ? number_format((float)$val) : htmlspecialchars($val);
            case 'percentage':
                return is_numeric($val) ? number_format((float)$val, 2) . '%' : htmlspecialchars($val);
            case 'date':
                if (is_numeric($val) && (int)$val > 100000) {
                    return date('d/m/Y', (int)$val);
                }
                return htmlspecialchars($val);
            case 'datetime':
                if (is_numeric($val) && (int)$val > 100000) {
                    return date('d/m/Y h:i A', (int)$val);
                }
                return htmlspecialchars($val);
            case 'badge':
                return '<span style="background: #e2e8f0; padding: 2px 5px; border-radius: 4px; font-size: 10px;">' . htmlspecialchars($val) . '</span>';
            default:
                return htmlspecialchars($val);
        }
    }
}
