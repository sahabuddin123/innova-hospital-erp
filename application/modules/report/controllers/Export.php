<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lifecare Diagnostic Center — Centralized Report Export Controller
 * 
 * Direct API/URL gateway for downloading reports in Print, PDF, Excel (.xlsx), and CSV formats.
 * Example: report/export/download?report_key=daily_collection&format=excel&period_type=today
 * 
 * @package    Lifecare Hospital ERP
 * @subpackage Controllers
 */
class Export extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Ion_auth');
        $this->load->library('Rbac');
        $this->load->library('ReportExport');
        $this->load->model('settings/settings_model');
        $this->load->model('finance/finance_model');
        $this->load->model('logs/logs_model');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    /**
     * Centralized download endpoint with granular RBAC validation
     */
    public function download()
    {
        $report_key = strtolower(trim($this->input->get_post('report_key')));
        $format     = strtolower(trim($this->input->get_post('format') ?: ($this->input->get_post('export_format') ?: 'pdf')));

        if (empty($report_key)) {
            show_error('Report key parameter is missing.', 400, '400 Bad Request');
            return;
        }

        // Validate format parameter
        if ($format === 'xlsx') {
            $format = 'excel';
        }
        if (!in_array($format, ['print', 'pdf', 'excel', 'csv'])) {
            show_error('Invalid export format requested.', 400, '400 Bad Request');
            return;
        }

        // Granular RBAC Permission Check
        if (!$this->checkExportPermission($report_key, $format)) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_status_header(403);
                echo json_encode([
                    'status' => 'error',
                    'message' => '403 Forbidden: You do not have permission to export ' . htmlspecialchars($report_key) . ' in ' . strtoupper($format) . ' format.'
                ]);
                return;
            }
            redirect('home/permission');
            return;
        }

        // Fetch Data via Report Data Provider
        $data = $this->resolveReportData($report_key);

        // Sensitive Data Protection
        if (isset($data['data']) && is_array($data['data'])) {
            $this->rbac->sanitizeDataset($report_key, $data['data']);
        }

        // Execute Export
        $this->reportexport->export($report_key, $format, $data);
    }

    /**
     * Role-based permission verification per report and format
     */
    protected function checkExportPermission($report_key, $format = 'pdf')
    {
        if (isset($this->rbac)) {
            return $this->rbac->canExport($report_key, $format);
        }

        if ($this->ion_auth->in_group(['admin', 'superadmin'])) {
            return true;
        }

        // Financial reports (Sub-menus 1-6 & 8)
        if (strpos($report_key, 'collection') !== false || strpos($report_key, 'expense') !== false ||
            strpos($report_key, 'finance') !== false || strpos($report_key, 'commission') !== false ||
            strpos($report_key, 'profit') !== false || strpos($report_key, 'audit') !== false) {
            return $this->ion_auth->in_group(['Accountant']);
        }

        // Diagnostic performance (Sub-menu 7)
        if (strpos($report_key, 'lab') !== false || strpos($report_key, 'diagnostic') !== false ||
            strpos($report_key, 'test') !== false) {
            return $this->ion_auth->in_group(['Laboratorist', 'Doctor']);
        }

        // Clinical outcomes
        if (in_array($report_key, ['birth', 'operation', 'expire'])) {
            return $this->ion_auth->in_group(['Nurse', 'Doctor']);
        }

        // User activity reports (Sub-menu 9)
        if (strpos($report_key, 'activity') !== false || strpos($report_key, 'user_') !== false || strpos($report_key, 'login') !== false) {
            return $this->ion_auth->in_group(['admin', 'superadmin']);
        }

        return false;
    }

    /**
     * Resolve report dataset by delegating to corresponding existing model methods
     */
    protected function resolveReportData($report_key)
    {
        $period_type = $this->input->get_post('period_type') ?: 'today';
        $date_from   = $this->input->get_post('date_from');
        $date_to     = $this->input->get_post('date_to');
        $selected_date = $this->input->get_post('date');

        $period = method_exists($this->finance_model, 'resolveReportPeriod') ? 
                  $this->finance_model->resolveReportPeriod($period_type, $date_from, $date_to, $selected_date) : 
                  ['start_time' => strtotime($date_from ?: 'today 00:00:00'), 'end_time' => strtotime($date_to ?: 'today 23:59:59'), 'period_label' => 'Selected Period'];

        $data = [
            'period'   => $period,
            'settings' => $this->settings_model->getSettings()
        ];

        switch ($report_key) {
            case 'daily_collection':
                $cashier_id = $this->input->get_post('cashier_id');
                $deposit_type = $this->input->get_post('deposit_type');
                $result = $this->finance_model->getDailyCollectionReport($period['start_time'], $period['end_time'], $cashier_id, $deposit_type);
                $data['records'] = $result['records'];
                $data['summary'] = $result['summary'];
                break;

            case 'monthly_collection':
                $year = $this->input->get_post('year') ?: date('Y');
                $result = $this->finance_model->getMonthlySalesReport($year, $period['start_time'], $period['end_time']);
                $data['records'] = $result['records'];
                $data['summary'] = $result['summary'];
                $data['year'] = $year;
                break;

            case 'daily_expense':
                $cat_id = $this->input->get_post('category_id');
                $method = $this->input->get_post('payment_method');
                $user_id = $this->input->get_post('user_id');
                $result = $this->finance_model->getDailyExpenseReport($period['start_time'], $period['end_time'], $cat_id, $method, $user_id);
                $data['records'] = $result['records'];
                $data['summary'] = $result['summary'];
                break;

            case 'doctor_wise_commission':
                $doctor_id = $this->input->get_post('doctor_id');
                $dept_id = $this->input->get_post('department_id');
                $result = $this->finance_model->getDoctorWiseCommissionReport($period['start_time'], $period['end_time'], $doctor_id, $dept_id);
                $data['records'] = $result['records'];
                $data['summary'] = $result['summary'];
                break;

            case 'referrer_wise_commission':
                $this->load->model('referral/referral_model');
                $referrer_id = $this->input->get_post('referrer_id');
                $result = method_exists($this->referral_model, 'getReferrerWiseReportData') ?
                          $this->referral_model->getReferrerWiseReportData($period['start_time'], $period['end_time'], $referrer_id) :
                          ['records' => [], 'summary' => []];
                $data['records'] = isset($result['records']) ? $result['records'] : [];
                $data['summary'] = isset($result['summary']) ? $result['summary'] : [];
                break;

            case 'financial_performance':
                $result = $this->finance_model->getFinancialPerformanceReportData($period_type, $selected_date, $date_from, $date_to);
                $data['report'] = $result;
                $data['summary'] = $result['summary'];
                $data['records'] = isset($result['breakdown']) ? $result['breakdown'] : [];
                break;

            case 'user_activity':
                $user_id = $this->input->get_post('user_id');
                $module = $this->input->get_post('module');
                $result = method_exists($this->logs_model, 'getUserActivityData') ?
                          $this->logs_model->getUserActivityData($period['start_time'], $period['end_time'], $user_id, $module) :
                          ['records' => [], 'summary' => []];
                $data['records'] = isset($result['records']) ? $result['records'] : [];
                $data['summary'] = isset($result['summary']) ? $result['summary'] : [];
                break;

            default:
                // Generic fallback for any other registered report
                $data['records'] = [];
                $data['summary'] = [];
                break;
        }

        return $data;
    }
}

