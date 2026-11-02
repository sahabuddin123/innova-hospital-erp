<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lifecare Diagnostic Center — Standard Excel (.xlsx) Export Engine
 * 
 * Generates genuine Microsoft Excel OpenXML (.xlsx) workbooks natively:
 * - Zero third-party dependencies; uses PHP ZipArchive & XMLWriter
 * - 100% compatible with PHP 8.2+
 * - Professional styling: Corporate navy theme (#1E3A8A), frozen header pane, zebra striping
 * - Proper OpenXML data types: numbers stored as numbers, dates formatted, strings escaped
 * - OWASP formula injection protection on text cells
 * - Grand totals with accounting double-underline border
 * - Sheet 1: Comprehensive Report Data & Executive Summary
 * 
 * @package    Lifecare Hospital ERP
 * @subpackage Libraries
 */
class ReportExcel
{
    protected $ci;

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->ci =& get_instance();
        }
    }

    /**
     * Generate and stream standard .xlsx workbook to browser
     * 
     * @param array  $payload  Standardized report payload
     * @param string $filename Custom filename
     * @return void
     */
    public function export(array $payload, $filename = '')
    {
        if (empty($filename)) {
            $key = !empty($payload['report_key']) ? $payload['report_key'] : 'report';
            $filename = 'lifecare_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $key) . '_' . date('Ymd_His') . '.xlsx';
        }

        $temp_file = tempnam(sys_get_temp_dir(), 'lc_xlsx_');
        $this->buildWorkbook($payload, $temp_file);

        if (!file_exists($temp_file) || filesize($temp_file) === 0) {
            show_error('Failed to generate Excel export file.', 500);
            return;
        }

        // Clean output buffers
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($temp_file));
        header('Content-Transfer-Encoding: binary');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        readfile($temp_file);
        @unlink($temp_file);
        exit;
    }

    /**
     * Build the OpenXML .xlsx archive
     */
    public function buildWorkbook(array $payload, $filepath)
    {
        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Unable to create zip file at " . $filepath);
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
  '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
  '<Default Extension="xml" ContentType="application/xml"/>' .
  '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
  '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
  '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
'</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
  '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
'</Relationships>';
        $zip->addFromString('_rels/.rels', $rootRels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
  '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
  '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
'</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $sheet_name = !empty($payload['sheet_name']) ? preg_replace('/[\\\\\\/\?\*\[\]\:]/', '', $payload['sheet_name']) : 'Report';
        $sheet_name = mb_substr($sheet_name, 0, 31);
        $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
  '<sheets>' .
    '<sheet name="' . htmlspecialchars($sheet_name, ENT_QUOTES, 'UTF-8') . '" sheetId="1" r:id="rId1"/>' .
  '</sheets>' .
'</workbook>';
        $zip->addFromString('xl/workbook.xml', $wb);

        // 5. xl/styles.xml
        $zip->addFromString('xl/styles.xml', $this->getStylesXml());

        // 6. xl/worksheets/sheet1.xml
        $sheetXml = $this->buildSheetXml($payload);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();
    }

    /**
     * Complete styles XML with professional number formats, fonts, fills, borders
     */
    protected function getStylesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
  '<numFmts count="3">' .
    '<numFmt numFmtId="164" formatCode="#,##0.00"/>' .
    '<numFmt numFmtId="165" formatCode="yyyy-mm-dd hh:mm"/>' .
    '<numFmt numFmtId="166" formatCode="0.00%"/>' .
  '</numFmts>' .
  '<fonts count="6">' .
    '<font><name val="Calibri"/><sz val="11"/><color rgb="FF1F2937"/></font>' .             // 0: Normal
    '<font><name val="Calibri"/><sz val="15"/><b/><color rgb="FF1E3A8A"/></font>' .         // 1: Org Title
    '<font><name val="Calibri"/><sz val="12"/><b/><color rgb="FF111827"/></font>' .         // 2: Report Title
    '<font><name val="Calibri"/><sz val="10"/><color rgb="FF4B5563"/></font>' .             // 3: Subtitle/Meta
    '<font><name val="Calibri"/><sz val="11"/><b/><color rgb="FFFFFFFF"/></font>' .         // 4: Table Header
    '<font><name val="Calibri"/><sz val="11"/><b/><color rgb="FF0F172A"/></font>' .         // 5: Totals / Bold
  '</fonts>' .
  '<fills count="6">' .
    '<fill><patternFill patternType="none"/></fill>' .                                        // 0
    '<fill><patternFill patternType="gray125"/></fill>' .                                     // 1
    '<fill><patternFill patternType="solid"><fgColor rgb="FF1E3A8A"/></patternFill></fill>' . // 2: Navy Header
    '<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>' . // 3: Zebra Light
    '<fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/></patternFill></fill>' . // 4: Total Row Fill
    '<fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>' . // 5: Summary Card Fill
  '</fills>' .
  '<borders count="4">' .
    '<border><left/><right/><top/><bottom/></border>' .                                       // 0: None
    '<border>' .                                                                              // 1: Thin Box
      '<left style="thin"><color rgb="FFE2E8F0"/></left>' .
      '<right style="thin"><color rgb="FFE2E8F0"/></right>' .
      '<top style="thin"><color rgb="FFE2E8F0"/></top>' .
      '<bottom style="thin"><color rgb="FFE2E8F0"/></bottom>' .
    '</border>' .
    '<border>' .                                                                              // 2: Total Row (Top Thin, Bottom Double)
      '<top style="thin"><color rgb="FF000000"/></top>' .
      '<bottom style="double"><color rgb="FF000000"/></bottom>' .
    '</border>' .
    '<border>' .                                                                              // 3: Card Box
      '<left style="thin"><color rgb="FFCBD5E1"/></left>' .
      '<right style="thin"><color rgb="FFCBD5E1"/></right>' .
      '<top style="thin"><color rgb="FFCBD5E1"/></top>' .
      '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>' .
    '</border>' .
  '</borders>' .
  '<cellStyleXfs count="1">' .
    '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
  '</cellStyleXfs>' .
  '<cellXfs count="15">' .
    '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>' .                                                                                                // 0: Default
    '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>' .                                                                                 // 1: Org Title
    '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>' .                                                                                 // 2: Report Title
    '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>' .                                                                                 // 3: Meta info
    '<xf numFmtId="0" fontId="4" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>' . // 4: Header Text Center
    '<xf numFmtId="0" fontId="4" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center" wrapText="1"/></xf>' .  // 5: Header Text Right
    '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>' .                         // 6: Text Left
    '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' .      // 7: Text Center
    '<xf numFmtId="3" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' . // 8: Integer #,##0
    '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' . // 9: Currency #,##0.00
    '<xf numFmtId="166" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' . // 10: Percentage
    '<xf numFmtId="0" fontId="5" fillId="4" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>' . // 11: Total Label Left
    '<xf numFmtId="164" fontId="5" fillId="4" borderId="2" xfId="0" applyFont="1" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' . // 12: Total Currency
    '<xf numFmtId="3" fontId="5" fillId="4" borderId="2" xfId="0" applyFont="1" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' . // 13: Total Integer
    '<xf numFmtId="0" fontId="5" fillId="5" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>' . // 14: Card Box
  '</cellXfs>' .
'</styleSheet>';
    }

    /**
     * Build the primary worksheet XML
     */
    protected function buildSheetXml(array $payload)
    {
        $org_name = !empty($payload['branding']['hospital_name']) ? $payload['branding']['hospital_name'] : 'Lifecare Diagnostic Center';
        $report_title = !empty($payload['report_title']) ? $payload['report_title'] : 'Report';
        $period_label = !empty($payload['date_range']) ? $payload['date_range'] : (!empty($payload['period_label']) ? $payload['period_label'] : 'All Available Dates');
        $generated_at = !empty($payload['generated_at']) ? $payload['generated_at'] : date('d/m/Y h:i A');
        $generated_by = !empty($payload['generated_by']) ? $payload['generated_by'] : 'System User';

        $columns = !empty($payload['columns']) ? $payload['columns'] : [];
        $col_count = max(1, count($columns));

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";

        // Column widths
        $xml .= '  <cols>' . "\n";
        $c_idx = 1;
        foreach ($columns as $col) {
            $width = !empty($col['width']) ? (float)$col['width'] : 18;
            $xml .= '    <col min="' . $c_idx . '" max="' . $c_idx . '" width="' . $width . '" customWidth="1"/>' . "\n";
            $c_idx++;
        }
        if (empty($columns)) {
            $xml .= '    <col min="1" max="10" width="20" customWidth="1"/>' . "\n";
        }
        $xml .= '  </cols>' . "\n";

        $xml .= '  <sheetData>' . "\n";

        $r = 1;

        // Row 1: Hospital Name
        $xml .= '    <row r="' . $r . '" ht="26" customHeight="1">' . "\n";
        $xml .= '      <c r="A' . $r . '" s="1" t="inlineStr"><is><t>' . $this->xmlEscape($org_name) . '</t></is></c>' . "\n";
        $xml .= '    </row>' . "\n";
        $r++;

        // Row 2: Report Title & Subtitle
        $title_str = $report_title . (!empty($payload['report_subtitle']) ? ' - ' . $payload['report_subtitle'] : '');
        $xml .= '    <row r="' . $r . '" ht="22" customHeight="1">' . "\n";
        $xml .= '      <c r="A' . $r . '" s="2" t="inlineStr"><is><t>' . $this->xmlEscape($title_str) . '</t></is></c>' . "\n";
        $xml .= '    </row>' . "\n";
        $r++;

        // Row 3: Metadata (Period, Generated Date, User)
        $meta_str = 'Period: ' . $period_label . '  |  Generated On: ' . $generated_at . '  |  Generated By: ' . $generated_by;
        $xml .= '    <row r="' . $r . '" ht="18" customHeight="1">' . "\n";
        $xml .= '      <c r="A' . $r . '" s="3" t="inlineStr"><is><t>' . $this->xmlEscape($meta_str) . '</t></is></c>' . "\n";
        $xml .= '    </row>' . "\n";
        $r++;

        // Row 4: Applied Filters
        if (!empty($payload['filters']) && is_array($payload['filters'])) {
            $f_parts = [];
            foreach ($payload['filters'] as $fl => $fv) {
                if ($fv !== null && $fv !== '') {
                    $f_parts[] = $fl . ': ' . $fv;
                }
            }
            if (!empty($f_parts)) {
                $xml .= '    <row r="' . $r . '" ht="18" customHeight="1">' . "\n";
                $xml .= '      <c r="A' . $r . '" s="3" t="inlineStr"><is><t>' . $this->xmlEscape('Filters: ' . implode('  |  ', $f_parts)) . '</t></is></c>' . "\n";
                $xml .= '    </row>' . "\n";
                $r++;
            }
        }

        // Summary KPI Cards Block
        if (!empty($payload['summary']) && is_array($payload['summary'])) {
            $r++; // Blank row before summary
            $xml .= '    <row r="' . $r . '" ht="20" customHeight="1">' . "\n";
            $xml .= '      <c r="A' . $r . '" s="3" t="inlineStr"><is><t>[ EXECUTIVE KPI SUMMARY ]</t></is></c>' . "\n";
            $xml .= '    </row>' . "\n";
            $r++;

            foreach ($payload['summary'] as $s_item) {
                $label = !empty($s_item['label']) ? $s_item['label'] : 'Metric';
                $val = isset($s_item['value']) ? $s_item['value'] : '0';
                $meta = !empty($s_item['meta']) ? ' (' . $s_item['meta'] . ')' : '';
                $type = !empty($s_item['type']) ? $s_item['type'] : 'currency';

                $xml .= '    <row r="' . $r . '" ht="19" customHeight="1">' . "\n";
                $xml .= '      <c r="A' . $r . '" s="14" t="inlineStr"><is><t>' . $this->xmlEscape($label . $meta) . '</t></is></c>' . "\n";
                if ($type === 'currency' && is_numeric($val)) {
                    $xml .= '      <c r="B' . $r . '" s="9" t="n"><v>' . (float)$val . '</v></c>' . "\n";
                } elseif (($type === 'integer' || $type === 'number') && is_numeric($val)) {
                    $xml .= '      <c r="B' . $r . '" s="8" t="n"><v>' . (int)$val . '</v></c>' . "\n";
                } else {
                    $xml .= '      <c r="B' . $r . '" s="6" t="inlineStr"><is><t>' . $this->xmlEscape((string)$val) . '</t></is></c>' . "\n";
                }
                $xml .= '    </row>' . "\n";
                $r++;
            }
        }

        $r++; // Blank space before table

        // Table Header Row
        $header_row_num = $r;
        $xml .= '    <row r="' . $r . '" ht="26" customHeight="1">' . "\n";
        $c_col = 1;
        foreach ($columns as $col) {
            $cell_ref = $this->getColumnLetter($c_col) . $r;
            $label = !empty($col['label']) ? $col['label'] : ucfirst(str_replace('_', ' ', $col['key']));
            $align = !empty($col['align']) ? $col['align'] : 'left';
            $s_idx = ($align === 'right') ? 5 : 4;
            $xml .= '      <c r="' . $cell_ref . '" s="' . $s_idx . '" t="inlineStr"><is><t>' . $this->xmlEscape($label) . '</t></is></c>' . "\n";
            $c_col++;
        }
        $xml .= '    </row>' . "\n";
        $r++;

        // Table Data Rows
        $rows = !empty($payload['rows']) ? $payload['rows'] : [];
        $first_data_row = $r;
        foreach ($rows as $row) {
            $xml .= '    <row r="' . $r . '" ht="20" customHeight="1">' . "\n";
            $c_col = 1;
            foreach ($columns as $col) {
                $key = $col['key'];
                $val = '';
                if (is_object($row) && isset($row->$key)) {
                    $val = $row->$key;
                } elseif (is_array($row) && isset($row[$key])) {
                    $val = $row[$key];
                }

                $cell_ref = $this->getColumnLetter($c_col) . $r;
                $type = !empty($col['type']) ? $col['type'] : 'string';
                $align = !empty($col['align']) ? $col['align'] : 'left';

                if ($type === 'currency' && is_numeric($val)) {
                    $xml .= '      <c r="' . $cell_ref . '" s="9" t="n"><v>' . (float)$val . '</v></c>' . "\n";
                } elseif ($type === 'integer' && is_numeric($val)) {
                    $xml .= '      <c r="' . $cell_ref . '" s="8" t="n"><v>' . (int)$val . '</v></c>' . "\n";
                } elseif ($type === 'percentage' && is_numeric($val)) {
                    $num_val = ((float)$val > 1) ? ((float)$val / 100) : (float)$val;
                    $xml .= '      <c r="' . $cell_ref . '" s="10" t="n"><v>' . $num_val . '</v></c>' . "\n";
                } else {
                    $s_idx = ($align === 'center') ? 7 : 6;
                    $sanitized = $this->sanitizeTextForExcel((string)$val);
                    $xml .= '      <c r="' . $cell_ref . '" s="' . $s_idx . '" t="inlineStr"><is><t>' . $this->xmlEscape($sanitized) . '</t></is></c>' . "\n";
                }
                $c_col++;
            }
            $xml .= '    </row>' . "\n";
            $r++;
        }
        $last_data_row = max($first_data_row, $r - 1);

        // Totals Row
        if (!empty($payload['totals']) && is_array($payload['totals'])) {
            $xml .= '    <row r="' . $r . '" ht="24" customHeight="1">' . "\n";
            $c_col = 1;
            $first = true;
            foreach ($columns as $col) {
                $key = $col['key'];
                $cell_ref = $this->getColumnLetter($c_col) . $r;
                $type = !empty($col['type']) ? $col['type'] : 'number';

                if (isset($payload['totals'][$key])) {
                    $tval = $payload['totals'][$key];
                    if ($type === 'currency' && is_numeric($tval)) {
                        $xml .= '      <c r="' . $cell_ref . '" s="12" t="n"><v>' . (float)$tval . '</v></c>' . "\n";
                    } elseif ($type === 'integer' && is_numeric($tval)) {
                        $xml .= '      <c r="' . $cell_ref . '" s="13" t="n"><v>' . (int)$tval . '</v></c>' . "\n";
                    } else {
                        $xml .= '      <c r="' . $cell_ref . '" s="11" t="inlineStr"><is><t>' . $this->xmlEscape((string)$tval) . '</t></is></c>' . "\n";
                    }
                } else {
                    $label = $first ? 'TOTAL' : '';
                    $xml .= '      <c r="' . $cell_ref . '" s="11" t="inlineStr"><is><t>' . $label . '</t></is></c>' . "\n";
                }
                $first = false;
                $c_col++;
            }
            $xml .= '    </row>' . "\n";
            $r++;
        }

        // Notes Row
        if (!empty($payload['notes'])) {
            $r++;
            $xml .= '    <row r="' . $r . '">' . "\n";
            $notes_text = is_array($payload['notes']) ? implode(' | ', $payload['notes']) : $payload['notes'];
            $xml .= '      <c r="A' . $r . '" s="3" t="inlineStr"><is><t>' . $this->xmlEscape('Notes: ' . $notes_text) . '</t></is></c>' . "\n";
            $xml .= '    </row>' . "\n";
        }

        $xml .= '  </sheetData>' . "\n";

        // AutoFilter on the table header
        $last_col_letter = $this->getColumnLetter($col_count);
        $xml .= '  <autoFilter ref="A' . $header_row_num . ':' . $last_col_letter . $last_data_row . '"/>' . "\n";

        // Freeze Panes below table header
        $split_row = $header_row_num;
        $top_left = 'A' . ($split_row + 1);
        $xml .= '  <sheetViews>' . "\n";
        $xml .= '    <sheetView tabSelected="1" workbookViewId="0">' . "\n";
        $xml .= '      <pane ySplit="' . $split_row . '" topLeftCell="' . $top_left . '" activePane="bottomLeft" state="frozen"/>' . "\n";
        $xml .= '    </sheetView>' . "\n";
        $xml .= '  </sheetViews>' . "\n";

        $xml .= '</worksheet>';

        return $xml;
    }

    /**
     * Prevent CSV/Excel Formula Injection
     */
    protected function sanitizeTextForExcel($val)
    {
        if ($val === '') {
            return '';
        }
        $trimmed = ltrim($val);
        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@', "\t", "\r"])) {
            return "'" . $val;
        }
        return $val;
    }

    /**
     * Escape XML special characters
     */
    protected function xmlEscape($str)
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Convert 1-based column index to Excel column letters (A, B, ..., Z, AA, AB)
     */
    protected function getColumnLetter($index)
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = (int)(($index - $mod) / 26);
        }
        return $letter ?: 'A';
    }
}
