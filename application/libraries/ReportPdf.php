<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lifecare Diagnostic Center — Standard PDF Export Engine
 * 
 * Powered by mPDF with:
 * - Complete Unicode & Bengali font rendering (autoScriptToLang, autoLangToFont)
 * - Flexible Page Sizes: A4 Portrait/Landscape, B5 Portrait/Landscape
 * - Hospital ERP branding (Logo, Address, Phone, System User)
 * - Repeated table headers across multiple pages (<thead>)
 * - Page number footer: {PAGENO} of {nbpg} + confidentiality notice
 * - Deprecation warning suppression for seamless PHP 8.2+ PDF generation
 * 
 * @package    Lifecare Hospital ERP
 * @subpackage Libraries
 */
class ReportPdf
{
    protected $ci;

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->ci =& get_instance();
        }
    }

    /**
     * Generate and stream PDF to browser
     * 
     * @param array  $payload   Standardized report payload
     * @param string $filename  Custom filename
     * @param bool   $download  True for download ('D'), False for inline view ('I')
     * @return void
     */
    public function export(array $payload, $filename = '', $download = false)
    {
        if (empty($filename)) {
            $key = !empty($payload['report_key']) ? $payload['report_key'] : 'report';
            $filename = 'lifecare_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $key) . '_' . date('Ymd_His') . '.pdf';
        }

        // Determine Page Size & Orientation
        $page_size = !empty($payload['paper_size']) ? strtoupper($payload['paper_size']) : 'A4';
        if (!in_array($page_size, ['A4', 'B5', 'LETTER', 'LEGAL'])) {
            $page_size = 'A4';
        }

        $orientation = !empty($payload['orientation']) ? strtolower($payload['orientation']) : 'portrait';
        $format = ($orientation === 'landscape' || $orientation === 'l') ? $page_size . '-L' : $page_size;

        // Ensure Composer autoloader is loaded
        if (!class_exists('\Mpdf\Mpdf')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } elseif (file_exists(APPPATH . '../vendor/autoload.php')) {
                require_once APPPATH . '../vendor/autoload.php';
            }
        }

        if (!class_exists('\Mpdf\Mpdf')) {
            show_error('mPDF library is not installed or autoloadable.', 500);
            return;
        }

        // Temporarily adjust error reporting to prevent PHP 8.2 deprecation notices from corrupting PDF binary stream
        $old_error_reporting = error_reporting();
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

        try {
            $mpdf = new \Mpdf\Mpdf([
                'format'              => $format,
                'margin_left'         => 10,
                'margin_right'        => 10,
                'margin_top'          => 34,
                'margin_bottom'       => 16,
                'margin_header'       => 8,
                'margin_footer'       => 8,
                'default_font'        => 'dejavusans',
                'default_font_size'   => 9,
                'tempDir'             => sys_get_temp_dir()
            ]);

            // Unicode & Bengali font engine settings
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont   = true;

            // 1. Build Header & Footer
            $header_html = $this->buildHeaderHtml($payload);
            $footer_html = $this->buildFooterHtml($payload);

            $mpdf->SetHTMLHeader($header_html);
            $mpdf->SetHTMLFooter($footer_html);

            // 2. Build Document Body
            $body_html = $this->buildBodyHtml($payload);
            $mpdf->WriteHTML($body_html);

            // Clean output buffers
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $output_dest = $download ? 'D' : 'I';
            $mpdf->Output($filename, $output_dest);

        } catch (\Throwable $e) {
            error_reporting($old_error_reporting);
            log_message('error', 'ReportPdf Error: ' . $e->getMessage());
            show_error('PDF Generation Failed: ' . $e->getMessage(), 500);
            return;
        }

        error_reporting($old_error_reporting);
        exit;
    }

    /**
     * Build HTML Header
     */
    protected function buildHeaderHtml(array $payload)
    {
        $org_name = !empty($payload['branding']['hospital_name']) ? $payload['branding']['hospital_name'] : 'Lifecare Diagnostic Center';
        $address = !empty($payload['branding']['address']) ? $payload['branding']['address'] : '';
        $phone = !empty($payload['branding']['phone']) ? $payload['branding']['phone'] : '';
        $report_title = !empty($payload['report_title']) ? $payload['report_title'] : 'Report';
        $period_label = !empty($payload['date_range']) ? $payload['date_range'] : (!empty($payload['period_label']) ? $payload['period_label'] : '');

        $html = '<div style="border-bottom: 2px solid #1E3A8A; padding-bottom: 6px; font-family: dejavusans, sans-serif;">';
        $html .= '<table width="100%" style="border-collapse: collapse; margin: 0; padding: 0;">';
        $html .= '<tr>';
        
        // Left: Hospital Info
        $html .= '<td width="60%" style="vertical-align: top;">';
        $html .= '<div style="font-size: 15px; font-weight: bold; color: #1E3A8A; text-transform: uppercase;">' . htmlspecialchars($org_name) . '</div>';
        if (!empty($address) || !empty($phone)) {
            $html .= '<div style="font-size: 8px; color: #555555; margin-top: 2px;">' . htmlspecialchars(trim($address . ($phone ? ' | Tel: ' . $phone : ''))) . '</div>';
        }
        $html .= '</td>';

        // Right: Report Title & Period
        $html .= '<td width="40%" style="vertical-align: top; text-align: right;">';
        $html .= '<div style="font-size: 12px; font-weight: bold; color: #111827;">' . htmlspecialchars($report_title) . '</div>';
        if (!empty($period_label)) {
            $html .= '<div style="font-size: 8.5px; color: #4B5563; margin-top: 2px;">Period: <strong>' . htmlspecialchars($period_label) . '</strong></div>';
        }
        $html .= '</td>';

        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Build HTML Footer
     */
    protected function buildFooterHtml(array $payload)
    {
        $generated_at = !empty($payload['generated_at']) ? $payload['generated_at'] : date('d/m/Y h:i A');
        $generated_by = !empty($payload['generated_by']) ? $payload['generated_by'] : 'User';

        $html = '<div style="border-top: 1px solid #E5E7EB; padding-top: 4px; font-size: 8px; color: #6B7280; font-family: dejavusans, sans-serif;">';
        $html .= '<table width="100%" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="40%">Generated: ' . htmlspecialchars($generated_at) . ' | By: ' . htmlspecialchars($generated_by) . '</td>';
        $html .= '<td width="30%" style="text-align: center; color: #9CA3AF;">Confidential &bull; Lifecare ERP</td>';
        $html .= '<td width="30%" style="text-align: right; font-weight: bold;">Page {PAGENO} of {nbpg}</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Build Document Body HTML with styles, KPI summary, and data table
     */
    protected function buildBodyHtml(array $payload)
    {
        $currency = !empty($payload['currency']) ? $payload['currency'] : '৳';
        $columns = !empty($payload['columns']) ? $payload['columns'] : [];
        $rows = !empty($payload['rows']) ? $payload['rows'] : [];

        $html = '<style>
            body { font-family: dejavusans, sans-serif; font-size: 8.5pt; color: #1F2937; }
            .kpi-container { margin-bottom: 12px; }
            .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .kpi-card { background: #F8FAFC; border: 1px solid #E2E8F0; padding: 6px 10px; text-align: left; }
            .kpi-label { font-size: 7.5pt; color: #64748B; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
            .kpi-value { font-size: 11pt; color: #0F172A; font-weight: bold; }
            .kpi-meta { font-size: 7pt; color: #94A3B8; }
            
            .rep-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
            .rep-table th { background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; font-size: 8pt; padding: 5px 6px; border: 1px solid #1E3A8A; }
            .rep-table td { padding: 4.5px 6px; border: 1px solid #E2E8F0; font-size: 8pt; vertical-align: middle; }
            .rep-table tr:nth-child(even) td { background-color: #F8FAFC; }
            .rep-table tr.total-row td { background-color: #E2E8F0; font-weight: bold; border-top: 1.5px solid #000000; border-bottom: 2.5px double #000000; color: #0F172A; }
            
            .text-left { text-align: left; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .filter-badge { display: inline-block; background: #F1F5F9; border: 1px solid #CBD5E1; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; margin-right: 4px; margin-bottom: 4px; }
        </style>';

        // 1. Applied Filters Summary Bar
        if (!empty($payload['filters']) && is_array($payload['filters'])) {
            $f_items = [];
            foreach ($payload['filters'] as $fl => $fv) {
                if ($fv !== null && $fv !== '') {
                    $f_items[] = '<span class="filter-badge"><strong>' . htmlspecialchars($fl) . ':</strong> ' . htmlspecialchars($fv) . '</span>';
                }
            }
            if (!empty($f_items)) {
                $html .= '<div style="margin-bottom: 8px;">' . implode(' ', $f_items) . '</div>';
            }
        }

        // 2. Executive KPI Cards Table
        if (!empty($payload['summary']) && is_array($payload['summary'])) {
            $cards = $payload['summary'];
            $chunks = array_chunk($cards, 4); // Max 4 cards per row
            $html .= '<div class="kpi-container">';
            foreach ($chunks as $chunk) {
                $col_w = round(100 / count($chunk), 1) . '%';
                $html .= '<table class="kpi-table"><tr>';
                foreach ($chunk as $c) {
                    $label = !empty($c['label']) ? $c['label'] : 'Metric';
                    $val = isset($c['value']) ? $c['value'] : '0';
                    $type = !empty($c['type']) ? $c['type'] : 'currency';
                    $formatted_val = ($type === 'currency' && is_numeric($val)) ? $currency . ' ' . number_format((float)$val, 2) :
                                     ((($type === 'integer' || $type === 'number') && is_numeric($val)) ? number_format((int)$val) : htmlspecialchars($val));
                    $meta = !empty($c['meta']) ? '<div class="kpi-meta">' . htmlspecialchars($c['meta']) . '</div>' : '';

                    $html .= '<td width="' . $col_w . '" class="kpi-card">';
                    $html .= '<div class="kpi-label">' . htmlspecialchars($label) . '</div>';
                    $html .= '<div class="kpi-value">' . $formatted_val . '</div>';
                    $html .= $meta;
                    $html .= '</td>';
                }
                $html .= '</tr></table>';
            }
            $html .= '</div>';
        }

        // 3. Report Data Table
        $html .= '<table class="rep-table">';
        
        // Repeated Header
        $html .= '<thead><tr>';
        foreach ($columns as $col) {
            $align = !empty($col['align']) ? $col['align'] : 'left';
            $label = !empty($col['label']) ? $col['label'] : ucfirst(str_replace('_', ' ', $col['key']));
            $w = !empty($col['width_pct']) ? ' width="' . $col['width_pct'] . '%"' : '';
            $html .= '<th class="text-' . $align . '"' . $w . '>' . htmlspecialchars($label) . '</th>';
        }
        $html .= '</tr></thead>';

        // Table Rows
        $html .= '<tbody>';
        if (empty($rows)) {
            $html .= '<tr><td colspan="' . count($columns) . '" class="text-center" style="padding: 20px; color: #94A3B8;">No records found for the selected period and criteria.</td></tr>';
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

                    $cell_content = $this->formatCellForPdf($val, $type, $currency);
                    $html .= '<td class="text-' . $align . '">' . $cell_content . '</td>';
                }
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';

        // Totals Row
        if (!empty($payload['totals']) && is_array($payload['totals'])) {
            $html .= '<tfoot><tr class="total-row">';
            $first = true;
            foreach ($columns as $col) {
                $key = $col['key'];
                $align = !empty($col['align']) ? $col['align'] : 'left';
                $type = !empty($col['type']) ? $col['type'] : 'number';

                if (isset($payload['totals'][$key])) {
                    $tval = $payload['totals'][$key];
                    $cell_content = $this->formatCellForPdf($tval, $type, $currency);
                    $html .= '<td class="text-' . $align . '">' . $cell_content . '</td>';
                } else {
                    $html .= '<td class="text-' . $align . '">' . ($first ? 'TOTAL' : '') . '</td>';
                }
                $first = false;
            }
            $html .= '</tr></tfoot>';
        }

        $html .= '</table>';

        // 4. Notes & Signatures
        if (!empty($payload['notes'])) {
            $notes_text = is_array($payload['notes']) ? implode('<br>', array_map('htmlspecialchars', $payload['notes'])) : htmlspecialchars($payload['notes']);
            $html .= '<div style="margin-top: 14px; font-size: 7.5pt; color: #64748B;"><strong>Notes:</strong><br>' . $notes_text . '</div>';
        }

        // Signature block
        $html .= '<div style="margin-top: 30px; font-family: dejavusans, sans-serif;">';
        $html .= '<table width="100%" style="border-collapse: collapse; text-align: center; font-size: 8pt; color: #4B5563;">';
        $html .= '<tr>';
        $html .= '<td width="33%"><div style="border-top: 1px dotted #9CA3AF; margin: 0 20px; padding-top: 4px;">Prepared By</div></td>';
        $html .= '<td width="33%"><div style="border-top: 1px dotted #9CA3AF; margin: 0 20px; padding-top: 4px;">Verified By</div></td>';
        $html .= '<td width="33%"><div style="border-top: 1px dotted #9CA3AF; margin: 0 20px; padding-top: 4px;">Authorized Signatory</div></td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format individual cell value for PDF rendering
     */
    protected function formatCellForPdf($val, $type = 'string', $currency = '৳')
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
                return '<span style="background: #E2E8F0; padding: 1px 4px; border-radius: 3px; font-size: 7.5pt;">' . htmlspecialchars($val) . '</span>';
            default:
                return htmlspecialchars($val);
        }
    }
}
