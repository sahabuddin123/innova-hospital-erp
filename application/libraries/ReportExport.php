<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lifecare Diagnostic Center — Centralized Standard Report Export Engine
 * 
 * Master coordinator supporting:
 * 1. Browser Print (ReportPrint)
 * 2. PDF generation with mPDF and Bengali Unicode support (ReportPdf)
 * 3. Native OpenXML Excel .xlsx generation (ReportExcel)
 * 4. RFC-4180 CSV export with UTF-8 BOM (ReportCsv)
 * 
 * Features:
 * - Pre-configured schema definitions for all 9 Report Sub-menus (85+ operational reports)
 * - Dynamic column/summary auto-detection fallback for custom reports
 * - 100% Filter & Data Consistency (Zero SQL query duplication)
 * - Security: OWASP formula injection protection & permission enforcement
 * - Forensic Audit Logging via Logs_model (Phase 9 integration)
 * 
 * @package    Lifecare Hospital ERP
 * @subpackage Libraries
 */
class ReportExport
{
    protected $ci;
    protected $printEngine;
    protected $pdfEngine;
    protected $excelEngine;
    protected $csvEngine;

    /**
     * Report metadata and column definitions registry
     */
    protected $registry = [];

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->ci =& get_instance();
            $this->ci->load->library('ReportPrint');
            $this->ci->load->library('ReportPdf');
            $this->ci->load->library('ReportExcel');
            $this->ci->load->library('ReportCsv');

            $this->printEngine = $this->ci->reportprint;
            $this->pdfEngine   = $this->ci->reportpdf;
            $this->excelEngine = $this->ci->reportexcel;
            $this->csvEngine   = $this->ci->reportcsv;
        } else {
            $this->printEngine = new ReportPrint();
            $this->pdfEngine   = new ReportPdf();
            $this->excelEngine = new ReportExcel();
            $this->csvEngine   = new ReportCsv();
        }

        $this->initRegistry();
    }

    /**
     * Intercept and handle export request in any controller method
     * 
     * @param string $report_key Report key identifier
     * @param array  $data       Controller data array
     * @param array  $options    Optional schema overrides
     * @return bool True if export was handled, False if regular HTML page should render
     */
    public function handle($report_key, array $data = [], array $options = [])
    {
        $format = $this->ci->input->get_post('export_format');
        if (!empty($format)) {
            $this->export($report_key, $format, $data, $options);
            return true;
        }
        return false;
    }

    /**
     * Centralized export execution method
     * 
     * @param string $report_key  Report identifier key
     * @param string $format      Format: 'print', 'pdf', 'excel', 'xlsx', 'csv'
     * @param array  $data        Data array prepared by controller
     * @param array  $options     Optional overrides (e.g. orientation, paper_size, title)
     * @return void
     */
    public function export($report_key, $format, array $data = [], array $options = [])
    {
        // 1. Validate Authentication
        if (!$this->ci->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
            return;
        }

        // 2. Normalize Format
        $format = strtolower(trim($format));
        if ($format === 'xlsx') {
            $format = 'excel';
        }
        if (!in_array($format, ['print', 'pdf', 'excel', 'csv'])) {
            $format = 'pdf'; // Default fallback
        }

        // 3. Build Standardized Payload
        $payload = $this->buildPayload($report_key, $data, $options);

        // 4. Log Forensic Audit Trail
        $this->logExportActivity($report_key, $format, $payload);

        // 5. Filename convention: lifecare_{report_key}_{YYYYMMDD_HHMMSS}.{ext}
        $clean_key = preg_replace('/[^a-zA-Z0-9_-]/', '_', $report_key);
        $timestamp = date('Ymd_His');
        $ext = ($format === 'excel') ? 'xlsx' : (($format === 'print') ? 'html' : $format);
        $filename = 'lifecare_' . $clean_key . '_' . $timestamp . '.' . $ext;

        // 6. Dispatch to Specialized Engine
        switch ($format) {
            case 'print':
                $autoprint = !isset($options['autoprint']) || $options['autoprint'];
                $this->printEngine->render($payload, $autoprint);
                break;

            case 'pdf':
                $download = isset($options['download']) ? (bool)$options['download'] : false;
                $this->pdfEngine->export($payload, $filename, $download);
                break;

            case 'excel':
                $this->excelEngine->export($payload, $filename);
                break;

            case 'csv':
                $this->csvEngine->export($payload, $filename);
                break;
        }
    }

    /**
     * Build unified, consistent payload structure from controller data and registry
     * 
     * @param string $report_key
     * @param array  $data
     * @param array  $options
     * @return array
     */
    public function buildPayload($report_key, array $data = [], array $options = [])
    {
        $schema = isset($this->registry[$report_key]) ? $this->registry[$report_key] : [];

        // Branding
        $settings = isset($data['settings']) ? $data['settings'] : 
                   (isset($this->ci->settings_model) ? $this->ci->settings_model->getSettings() : null);

        $hospital_name = !empty($settings->system_vendor) ? $settings->system_vendor : 'Lifecare Diagnostic Center';
        $address = !empty($settings->address) ? $settings->address : '';
        $phone = !empty($settings->phone) ? $settings->phone : '';
        $currency = !empty($settings->currency) ? $settings->currency : '৳';
        $logo = !empty($settings->logo) ? $settings->logo : '';

        // User
        $user_obj = $this->ci->ion_auth->user()->row();
        $generated_by = !empty($user_obj->username) ? $user_obj->username : 'Staff';

        // Title & Description
        $report_title = !empty($options['title']) ? $options['title'] : 
                       (!empty($schema['title']) ? $schema['title'] : ucwords(str_replace('_', ' ', $report_key)));
        $report_subtitle = !empty($options['subtitle']) ? $options['subtitle'] : 
                          (!empty($schema['subtitle']) ? $schema['subtitle'] : '');

        // Period & Date Range
        $period_label = '';
        if (!empty($data['period']['period_label'])) {
            $period_label = $data['period']['period_label'];
        } elseif (!empty($data['period_label'])) {
            $period_label = $data['period_label'];
        } elseif (!empty($data['date_from']) && !empty($data['date_to'])) {
            $period_label = $data['date_from'] . ' to ' . $data['date_to'];
        } elseif (!empty($data['year'])) {
            $period_label = 'Year ' . $data['year'];
        } elseif (!empty($data['month']) && !empty($data['year'])) {
            $period_label = $data['month'] . ' / ' . $data['year'];
        }

        // Applied Filters
        $filters = $this->resolveAppliedFilters($data, $options);

        // Extract Rows / Records
        $rows = $this->resolveRows($data);

        // Resolve Columns
        $columns = !empty($options['columns']) ? $options['columns'] : 
                  (!empty($schema['columns']) ? $schema['columns'] : $this->inferColumns($rows));

        // Resolve Summary KPI Cards
        $summary = $this->resolveSummary($data, $schema, $options);

        // Compute / Extract Totals
        $totals = $this->resolveTotals($data, $rows, $columns);

        // Page settings
        $orientation = !empty($options['orientation']) ? $options['orientation'] : 
                      (!empty($schema['orientation']) ? $schema['orientation'] : 'portrait');
        $paper_size = !empty($options['paper_size']) ? $options['paper_size'] : 
                     (!empty($schema['paper_size']) ? $schema['paper_size'] : 'A4');

        return [
            'report_key'      => $report_key,
            'report_title'    => $report_title,
            'report_subtitle' => $report_subtitle,
            'sheet_name'      => !empty($schema['sheet_name']) ? $schema['sheet_name'] : mb_substr($report_title, 0, 30),
            'generated_at'    => date('d/m/Y h:i A'),
            'generated_by'    => $generated_by,
            'date_range'      => $period_label,
            'period_label'    => $period_label,
            'filters'         => $filters,
            'columns'         => $columns,
            'rows'            => $rows,
            'summary'         => $summary,
            'totals'          => $totals,
            'currency'        => $currency,
            'orientation'     => $orientation,
            'paper_size'      => $paper_size,
            'notes'           => !empty($options['notes']) ? $options['notes'] : (!empty($schema['notes']) ? $schema['notes'] : []),
            'branding'        => [
                'hospital_name' => $hospital_name,
                'address'       => $address,
                'phone'         => $phone,
                'logo'          => $logo,
                'currency'      => $currency
            ]
        ];
    }

    /**
     * Resolve rows from varied controller data structures
     */
    protected function resolveRows(array $data)
    {
        if (isset($data['records']) && is_array($data['records'])) {
            return $data['records'];
        }
        if (isset($data['rows']) && is_array($data['rows'])) {
            return $data['rows'];
        }
        if (isset($data['report']['breakdown']) && is_array($data['report']['breakdown'])) {
            return $data['report']['breakdown'];
        }
        if (isset($data['breakdown']) && is_array($data['breakdown'])) {
            return $data['breakdown'];
        }
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }
        if (isset($data['logs']) && is_array($data['logs'])) {
            return $data['logs'];
        }
        if (isset($data['activities']) && is_array($data['activities'])) {
            return $data['activities'];
        }
        if (isset($data['tests']) && is_array($data['tests'])) {
            return $data['tests'];
        }
        if (isset($data['expenses']) && is_array($data['expenses'])) {
            return $data['expenses'];
        }
        return [];
    }

    /**
     * Resolve applied filter labels and values
     */
    protected function resolveAppliedFilters(array $data, array $options)
    {
        $filters = [];
        if (!empty($options['filters']) && is_array($options['filters'])) {
            return $options['filters'];
        }

        if (!empty($data['period']['period_label'])) {
            $filters['Date Range'] = $data['period']['period_label'];
        } elseif (!empty($data['date_from']) && !empty($data['date_to'])) {
            $filters['Date Range'] = $data['date_from'] . ' to ' . $data['date_to'];
        }

        if (!empty($data['selected_cashier'])) {
            $filters['Cashier'] = '#' . $data['selected_cashier'];
        }
        if (!empty($data['selected_deposit_type'])) {
            $filters['Payment Method'] = $data['selected_deposit_type'];
        }
        if (!empty($data['selected_category'])) {
            $filters['Category'] = '#' . $data['selected_category'];
        }
        if (!empty($data['selected_doctor'])) {
            $filters['Doctor'] = '#' . $data['selected_doctor'];
        }
        if (!empty($data['selected_referrer'])) {
            $filters['Referrer'] = '#' . $data['selected_referrer'];
        }
        if (!empty($data['selected_department'])) {
            $filters['Department'] = '#' . $data['selected_department'];
        }
        if (!empty($data['selected_user'])) {
            $filters['User / Staff'] = '#' . $data['selected_user'];
        }
        if (!empty($data['selected_status'])) {
            $filters['Status'] = ucfirst($data['selected_status']);
        }

        return $filters;
    }

    /**
     * Resolve Executive KPI summary cards
     */
    protected function resolveSummary(array $data, array $schema, array $options)
    {
        if (!empty($options['summary']) && is_array($options['summary'])) {
            return $options['summary'];
        }

        $raw_summary = [];
        if (isset($data['summary']) && is_array($data['summary'])) {
            $raw_summary = $data['summary'];
        } elseif (isset($data['report']['summary']) && is_array($data['report']['summary'])) {
            $raw_summary = $data['report']['summary'];
        }

        if (empty($raw_summary)) {
            return [];
        }

        $cards = [];
        if (!empty($schema['summary_keys'])) {
            foreach ($schema['summary_keys'] as $sk) {
                $k = $sk['key'];
                if (isset($raw_summary[$k])) {
                    $cards[] = [
                        'label' => $sk['label'],
                        'value' => $raw_summary[$k],
                        'type'  => !empty($sk['type']) ? $sk['type'] : 'currency',
                        'meta'  => !empty($sk['meta']) ? $sk['meta'] : ''
                    ];
                }
            }
        } else {
            // Dynamic fallback for summary cards
            foreach ($raw_summary as $k => $v) {
                if (is_scalar($v) && !in_array($k, ['hospital_id', 'id', 'created_at'])) {
                    $type = (strpos($k, 'count') !== false || strpos($k, 'total_transactions') !== false || strpos($k, 'num_') !== false) ? 'integer' :
                            ((strpos($k, 'rate') !== false || strpos($k, 'margin') !== false || strpos($k, 'pct') !== false) ? 'percentage' : 'currency');
                    $cards[] = [
                        'label' => ucwords(str_replace('_', ' ', $k)),
                        'value' => $v,
                        'type'  => $type,
                        'meta'  => ''
                    ];
                }
            }
        }

        return $cards;
    }

    /**
     * Compute or extract grand totals
     */
    protected function resolveTotals(array $data, array $rows, array $columns)
    {
        if (isset($data['totals']) && is_array($data['totals'])) {
            return $data['totals'];
        }
        if (isset($data['summary']['totals']) && is_array($data['summary']['totals'])) {
            return $data['summary']['totals'];
        }

        // Auto-calculate for columns defined with 'total' => true
        $totals = [];
        foreach ($columns as $col) {
            if (!empty($col['total'])) {
                $key = $col['key'];
                $sum = 0;
                foreach ($rows as $row) {
                    $val = 0;
                    if (is_object($row) && isset($row->$key) && is_numeric($row->$key)) {
                        $val = (float)$row->$key;
                    } elseif (is_array($row) && isset($row[$key]) && is_numeric($row[$key])) {
                        $val = (float)$row[$key];
                    }
                    $sum += $val;
                }
                $totals[$key] = $sum;
            }
        }

        return $totals;
    }

    /**
     * Dynamic column inference fallback for unlisted reports
     */
    protected function inferColumns(array $rows)
    {
        if (empty($rows)) {
            return [
                ['key' => 'id', 'label' => '#', 'align' => 'center', 'type' => 'integer', 'width' => 10],
                ['key' => 'description', 'label' => 'Description', 'align' => 'left', 'type' => 'string', 'width' => 30]
            ];
        }

        $sample = reset($rows);
        $keys = is_object($sample) ? array_keys(get_object_vars($sample)) : array_keys($sample);

        $cols = [];
        foreach ($keys as $k) {
            if (in_array($k, ['hospital_id', 'password', 'salt', 'token', 'deleted'])) {
                continue; // Skip internal/sensitive fields
            }

            $type = 'string';
            $align = 'left';
            $is_total = false;

            if (strpos($k, 'amount') !== false || strpos($k, 'price') !== false || strpos($k, 'total') !== false || strpos($k, 'balance') !== false || strpos($k, 'cost') !== false || strpos($k, 'revenue') !== false) {
                $type = 'currency';
                $align = 'right';
                $is_total = true;
            } elseif (strpos($k, 'date') !== false || strpos($k, 'time') !== false) {
                $type = 'datetime';
                $align = 'center';
            } elseif (strpos($k, 'qty') !== false || strpos($k, 'count') !== false || strpos($k, 'id') !== false) {
                $type = 'integer';
                $align = 'center';
            }

            $cols[] = [
                'key'   => $k,
                'label' => ucwords(str_replace('_', ' ', $k)),
                'align' => $align,
                'type'  => $type,
                'width' => 18,
                'total' => $is_total
            ];
        }

        return $cols;
    }

    /**
     * Log forensic audit telemetry
     */
    protected function logExportActivity($report_key, $format, array $payload)
    {
        try {
            if (!empty($this->ci->logs_model) && method_exists($this->ci->logs_model, 'logPrintOrExport')) {
                $module = 'report';
                if (strpos($report_key, 'collection') !== false || strpos($report_key, 'expense') !== false || strpos($report_key, 'pnl') !== false || strpos($report_key, 'finance') !== false || strpos($report_key, 'commission') !== false) {
                    $module = 'finance';
                } elseif (strpos($report_key, 'referral') !== false) {
                    $module = 'referral';
                } elseif (strpos($report_key, 'lab') !== false || strpos($report_key, 'test') !== false || strpos($report_key, 'diagnostic') !== false) {
                    $module = 'lab';
                } elseif (strpos($report_key, 'log') !== false || strpos($report_key, 'activity') !== false) {
                    $module = 'logs';
                }

                $action = strtoupper('EXPORT_' . $format);
                $desc = 'Exported ' . $payload['report_title'] . ' (' . strtoupper($format) . ') | Period: ' . $payload['date_range'];
                $this->ci->logs_model->logPrintOrExport($module, $payload['report_title'], $action, $desc);
            }
        } catch (\Throwable $e) {
            // Silently suppress audit failure to not break user download
            log_message('error', 'ReportExport Audit Log failed: ' . $e->getMessage());
        }
    }

    /**
     * Initialize schema registry for all 9 Report Sub-menus
     */
    protected function initRegistry()
    {
        $this->registry = [
            // =================================================================
            // 1. FINANCIAL DASHBOARD
            // =================================================================
            'financial_performance' => [
                'title'       => 'Financial Performance Statement',
                'subtitle'    => 'Comprehensive Operational Financial Health & Cash Flow',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'period_label', 'label' => 'Period', 'align' => 'left', 'type' => 'string', 'width' => 20],
                    ['key' => 'total_billed', 'label' => 'Gross Billed', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'discount_given', 'label' => 'Discount', 'align' => 'right', 'type' => 'currency', 'width' => 14, 'total' => true],
                    ['key' => 'net_billed', 'label' => 'Net Billed', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'cash_collection', 'label' => 'Cash Coll.', 'align' => 'right', 'type' => 'currency', 'width' => 15, 'total' => true],
                    ['key' => 'online_collection', 'label' => 'Digital Coll.', 'align' => 'right', 'type' => 'currency', 'width' => 15, 'total' => true],
                    ['key' => 'total_collection', 'label' => 'Total Coll.', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'total_expense', 'label' => 'Expenses', 'align' => 'right', 'type' => 'currency', 'width' => 15, 'total' => true],
                    ['key' => 'doctor_commission', 'label' => 'Doc Comm', 'align' => 'right', 'type' => 'currency', 'width' => 15, 'total' => true],
                    ['key' => 'referral_commission', 'label' => 'Ref Comm', 'align' => 'right', 'type' => 'currency', 'width' => 15, 'total' => true],
                    ['key' => 'net_profit', 'label' => 'Net Operating Profit', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true]
                ],
                'summary_keys' => [
                    ['key' => 'total_sales', 'label' => 'Gross Billing', 'type' => 'currency'],
                    ['key' => 'total_collected', 'label' => 'Total Collection', 'type' => 'currency'],
                    ['key' => 'total_expense', 'label' => 'Total Expenses', 'type' => 'currency'],
                    ['key' => 'net_profit', 'label' => 'Net Profit', 'type' => 'currency']
                ]
            ],

            // =================================================================
            // 2. INCOME & COLLECTION REPORTS
            // =================================================================
            'daily_collection' => [
                'title'       => 'Daily Collection Report',
                'subtitle'    => 'Itemized patient deposits & cashier payment transactions',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'date', 'label' => 'Date & Time', 'align' => 'center', 'type' => 'datetime', 'width' => 17],
                    ['key' => 'invoice_id', 'label' => 'Invoice #', 'align' => 'center', 'type' => 'string', 'width' => 14],
                    ['key' => 'patient_name', 'label' => 'Patient Name', 'align' => 'left', 'type' => 'string', 'width' => 24],
                    ['key' => 'patient_phone', 'label' => 'Phone', 'align' => 'center', 'type' => 'string', 'width' => 15],
                    ['key' => 'deposit_type', 'label' => 'Payment Method', 'align' => 'center', 'type' => 'string', 'width' => 15],
                    ['key' => 'cashier_name', 'label' => 'Cashier / Staff', 'align' => 'left', 'type' => 'string', 'width' => 18],
                    ['key' => 'amount', 'label' => 'Collected Amount', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true]
                ],
                'summary_keys' => [
                    ['key' => 'total_transactions', 'label' => 'Total Transactions', 'type' => 'integer'],
                    ['key' => 'total_collection', 'label' => 'Total Collection', 'type' => 'currency'],
                    ['key' => 'cash_collection', 'label' => 'Cash Collection', 'type' => 'currency'],
                    ['key' => 'digital_collection', 'label' => 'Digital Collection', 'type' => 'currency'],
                    ['key' => 'bank_collection', 'label' => 'Bank Collection', 'type' => 'currency']
                ]
            ],

            'monthly_collection' => [
                'title'       => 'Monthly Collection & Sales Report',
                'subtitle'    => 'Calendar month collection aggregation and billing volume',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'month_name', 'label' => 'Month', 'align' => 'left', 'type' => 'string', 'width' => 16],
                    ['key' => 'invoice_count', 'label' => 'Total Invoices', 'align' => 'center', 'type' => 'integer', 'width' => 14, 'total' => true],
                    ['key' => 'gross_amount', 'label' => 'Gross Sales', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'discount', 'label' => 'Discounts', 'align' => 'right', 'type' => 'currency', 'width' => 15, 'total' => true],
                    ['key' => 'net_amount', 'label' => 'Net Sales', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'collected_amount', 'label' => 'Total Collection', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true],
                    ['key' => 'due_amount', 'label' => 'Outstanding Due', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true]
                ],
                'summary_keys' => [
                    ['key' => 'total_invoices', 'label' => 'Total Invoices', 'type' => 'integer'],
                    ['key' => 'total_gross', 'label' => 'Gross Sales', 'type' => 'currency'],
                    ['key' => 'total_collected', 'label' => 'Total Collected', 'type' => 'currency'],
                    ['key' => 'total_due', 'label' => 'Remaining Due', 'type' => 'currency']
                ]
            ],

            'due_collection' => [
                'title'       => 'Patient Due Collection Report',
                'subtitle'    => 'Receivables, pending dues, and settled invoice balances',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'invoice_id', 'label' => 'Invoice #', 'align' => 'center', 'type' => 'string', 'width' => 14],
                    ['key' => 'date', 'label' => 'Invoice Date', 'align' => 'center', 'type' => 'date', 'width' => 14],
                    ['key' => 'patient_name', 'label' => 'Patient Name', 'align' => 'left', 'type' => 'string', 'width' => 22],
                    ['key' => 'patient_phone', 'label' => 'Phone', 'align' => 'center', 'type' => 'string', 'width' => 15],
                    ['key' => 'gross_total', 'label' => 'Gross Total', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'paid_amount', 'label' => 'Paid Amount', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'due_balance', 'label' => 'Due Balance', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'payment_status', 'label' => 'Status', 'align' => 'center', 'type' => 'badge', 'width' => 14]
                ]
            ],

            // =================================================================
            // 3. EXPENSE & ACCOUNTS REPORTS
            // =================================================================
            'daily_expense' => [
                'title'       => 'Daily Expense Report',
                'subtitle'    => 'Voucher-wise operating expenditures and disbursements',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'date', 'label' => 'Date & Time', 'align' => 'center', 'type' => 'datetime', 'width' => 16],
                    ['key' => 'voucher_no', 'label' => 'Voucher #', 'align' => 'center', 'type' => 'string', 'width' => 14],
                    ['key' => 'category_name', 'label' => 'Expense Category', 'align' => 'left', 'type' => 'string', 'width' => 20],
                    ['key' => 'recipient', 'label' => 'Vendor / Payee', 'align' => 'left', 'type' => 'string', 'width' => 20],
                    ['key' => 'payment_type', 'label' => 'Payment Mode', 'align' => 'center', 'type' => 'string', 'width' => 14],
                    ['key' => 'created_by', 'label' => 'Authorized By', 'align' => 'left', 'type' => 'string', 'width' => 16],
                    ['key' => 'amount', 'label' => 'Amount', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true]
                ],
                'summary_keys' => [
                    ['key' => 'total_vouchers', 'label' => 'Total Vouchers', 'type' => 'integer'],
                    ['key' => 'total_expense', 'label' => 'Total Expenses', 'type' => 'currency'],
                    ['key' => 'cash_expense', 'label' => 'Cash Paid', 'type' => 'currency'],
                    ['key' => 'bank_expense', 'label' => 'Bank / Transfer', 'type' => 'currency']
                ]
            ],

            'cash_ledger' => [
                'title'       => 'Cash Ledger Statement',
                'subtitle'    => 'Physical cash counter inflows, outflows, and running cash balance',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'date', 'label' => 'Date', 'align' => 'center', 'type' => 'date', 'width' => 14],
                    ['key' => 'ref_no', 'label' => 'Reference #', 'align' => 'center', 'type' => 'string', 'width' => 15],
                    ['key' => 'particulars', 'label' => 'Particulars / Description', 'align' => 'left', 'type' => 'string', 'width' => 28],
                    ['key' => 'debit', 'label' => 'Debit (Inflow)', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'credit', 'label' => 'Credit (Outflow)', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'balance', 'label' => 'Running Balance', 'align' => 'right', 'type' => 'currency', 'width' => 18]
                ]
            ],

            // =================================================================
            // 4. DOCTOR COMMISSION REPORTS
            // =================================================================
            'doctor_wise_commission' => [
                'title'       => 'Doctor-wise Commission Report',
                'subtitle'    => 'Referral and diagnostic consultation earnings by doctor',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'doctor_name', 'label' => 'Doctor Name', 'align' => 'left', 'type' => 'string', 'width' => 24],
                    ['key' => 'department', 'label' => 'Department', 'align' => 'left', 'type' => 'string', 'width' => 18],
                    ['key' => 'total_invoices', 'label' => 'Referred Invoices', 'align' => 'center', 'type' => 'integer', 'width' => 15, 'total' => true],
                    ['key' => 'total_bill', 'label' => 'Total Billed Amount', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true],
                    ['key' => 'commission_amount', 'label' => 'Commission Earned', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true],
                    ['key' => 'paid_commission', 'label' => 'Paid Commission', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true],
                    ['key' => 'payable_commission', 'label' => 'Net Payable', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true]
                ]
            ],

            // =================================================================
            // 5. REFERRAL COMMISSION REPORTS
            // =================================================================
            'referrer_wise_commission' => [
                'title'       => 'Referrer-wise Commission Statement',
                'subtitle'    => 'Outside referrers, agents, and clinic commission statements',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'referrer_name', 'label' => 'Referrer / Agent', 'align' => 'left', 'type' => 'string', 'width' => 24],
                    ['key' => 'referrer_phone', 'label' => 'Phone', 'align' => 'center', 'type' => 'string', 'width' => 15],
                    ['key' => 'case_count', 'label' => 'Patient Cases', 'align' => 'center', 'type' => 'integer', 'width' => 14, 'total' => true],
                    ['key' => 'gross_revenue', 'label' => 'Gross Revenue', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true],
                    ['key' => 'commission_earned', 'label' => 'Commission Earned', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true],
                    ['key' => 'wallet_balance', 'label' => 'Wallet Balance', 'align' => 'right', 'type' => 'currency', 'width' => 16, 'total' => true]
                ]
            ],

            // =================================================================
            // 6. PROFIT & LOSS REPORTS
            // =================================================================
            'profit_loss' => [
                'title'       => 'Profit & Loss Statement',
                'subtitle'    => 'Accrual and cash-basis net operating performance',
                'orientation' => 'portrait',
                'columns'     => [
                    ['key' => 'line_item', 'label' => 'Accounting Line Item', 'align' => 'left', 'type' => 'string', 'width' => 36],
                    ['key' => 'notes', 'label' => 'Schedule / Details', 'align' => 'left', 'type' => 'string', 'width' => 22],
                    ['key' => 'amount', 'label' => 'Amount', 'align' => 'right', 'type' => 'currency', 'width' => 20]
                ]
            ],

            // =================================================================
            // 7. DIAGNOSTIC PERFORMANCE REPORTS
            // =================================================================
            'diagnostic_volume' => [
                'title'       => 'Diagnostic Test Volume & Revenue',
                'subtitle'    => 'Test performance, sample counts, and laboratory workload',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'test_name', 'label' => 'Investigation / Test', 'align' => 'left', 'type' => 'string', 'width' => 26],
                    ['key' => 'category_name', 'label' => 'Category', 'align' => 'left', 'type' => 'string', 'width' => 18],
                    ['key' => 'test_count', 'label' => 'Test Volume', 'align' => 'center', 'type' => 'integer', 'width' => 14, 'total' => true],
                    ['key' => 'unit_price', 'label' => 'Standard Rate', 'align' => 'right', 'type' => 'currency', 'width' => 14],
                    ['key' => 'total_revenue', 'label' => 'Gross Revenue', 'align' => 'right', 'type' => 'currency', 'width' => 18, 'total' => true],
                    ['key' => 'avg_tat', 'label' => 'Avg TAT (Hours)', 'align' => 'center', 'type' => 'number', 'width' => 15]
                ]
            ],

            // =================================================================
            // 8. FINANCIAL AUDIT LOGS
            // =================================================================
            'financial_audit_logs' => [
                'title'       => 'Financial Audit Trail & Forensic Logs',
                'subtitle'    => 'Immutable transactional logs, adjustments, voids, and overrides',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'date_time', 'label' => 'Timestamp', 'align' => 'center', 'type' => 'datetime', 'width' => 16],
                    ['key' => 'user_name', 'label' => 'Staff / User', 'align' => 'left', 'type' => 'string', 'width' => 16],
                    ['key' => 'action_type', 'label' => 'Action Type', 'align' => 'center', 'type' => 'badge', 'width' => 14],
                    ['key' => 'entity_type', 'label' => 'Entity', 'align' => 'center', 'type' => 'string', 'width' => 14],
                    ['key' => 'entity_id', 'label' => 'Record ID', 'align' => 'center', 'type' => 'string', 'width' => 12],
                    ['key' => 'amount', 'label' => 'Amount Impact', 'align' => 'right', 'type' => 'currency', 'width' => 15],
                    ['key' => 'details', 'label' => 'Forensic Details', 'align' => 'left', 'type' => 'string', 'width' => 30],
                    ['key' => 'ip_address', 'label' => 'IP Address', 'align' => 'center', 'type' => 'string', 'width' => 14]
                ]
            ],

            // =================================================================
            // 9. USER ACTIVITY REPORTS
            // =================================================================
            'user_activity' => [
                'title'       => 'User Activity & Audit Report',
                'subtitle'    => 'Administrative telemetry, sessions, and security monitoring',
                'orientation' => 'landscape',
                'columns'     => [
                    ['key' => 'created_at', 'label' => 'Date & Time', 'align' => 'center', 'type' => 'datetime', 'width' => 16],
                    ['key' => 'username', 'label' => 'Username', 'align' => 'left', 'type' => 'string', 'width' => 16],
                    ['key' => 'role', 'label' => 'User Role', 'align' => 'center', 'type' => 'string', 'width' => 14],
                    ['key' => 'module', 'label' => 'Module', 'align' => 'center', 'type' => 'string', 'width' => 14],
                    ['key' => 'action_type', 'label' => 'Action', 'align' => 'center', 'type' => 'badge', 'width' => 14],
                    ['key' => 'description', 'label' => 'Description / Action Details', 'align' => 'left', 'type' => 'string', 'width' => 30],
                    ['key' => 'ip_address', 'label' => 'IP Address', 'align' => 'center', 'type' => 'string', 'width' => 14]
                ]
            ]
        ];
    }
}
