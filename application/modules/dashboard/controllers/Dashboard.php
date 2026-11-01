<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard/dashboard_model');
        $this->load->model('patient/patient_model');
        $this->load->model('doctor/doctor_model');
        $this->load->model('finance/finance_model');
        $this->load->model('appointment/appointment_model');
        $this->load->model('lab/lab_model');
        $this->load->model('bed/bed_model');
        $this->load->model('inventory/inventory_model');
        $this->load->model('settings/settings_model');
        $this->load->library('ion_auth');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    public function index()
    {
        $user_groups = $this->ion_auth->get_users_groups()->result();
        $user_role = !empty($user_groups) ? $user_groups[0]->name : 'Patient';
        
        // Redirect to appropriate dashboard based on user role
        switch (strtolower($user_role)) {
            case 'admin':
            case 'superadmin':
                redirect('dashboard/executive');
                break;
            case 'doctor':
                redirect('dashboard/clinical');
                break;
            case 'accountant':
                redirect('dashboard/financial');
                break;
            case 'nurse':
            case 'receptionist':
                redirect('dashboard/operational');
                break;
            default:
                redirect('dashboard/operational');
                break;
        }
    }

    public function executive()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin'))) {
            redirect('home/permission');
        }

        $data = array();
        $data['page'] = 'executive_dashboard';
        $data['page_title'] = 'Executive Dashboard';
        $data['settings'] = $this->settings_model->getSettings();
        
        // Get executive-level metrics
        $data['metrics'] = $this->dashboard_model->getExecutiveMetrics();
        $data['revenue_data'] = $this->dashboard_model->getRevenueData(12); // Last 12 months
        $data['patient_flow'] = $this->dashboard_model->getPatientFlowData(30); // Last 30 days
        $data['department_performance'] = $this->dashboard_model->getDepartmentPerformance();
        $data['quality_metrics'] = $this->dashboard_model->getQualityMetrics();
        $data['financial_summary'] = $this->dashboard_model->getFinancialSummary();
        $data['operational_efficiency'] = $this->dashboard_model->getOperationalEfficiency();
        $data['percentage_changes'] = $this->dashboard_model->getPercentageChanges();
        
        $this->load->view('home/dashboard');
        $this->load->view('dashboard/executive', $data);
        $this->load->view('home/footer');
    }

    public function clinical()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Doctor', 'Nurse'))) {
            redirect('home/permission');
        }

        $data = array();
        $data['page'] = 'clinical_dashboard';
        $data['page_title'] = 'Clinical Dashboard';
        $data['settings'] = $this->settings_model->getSettings();
        
        // Get clinical metrics
        $data['patient_metrics'] = $this->dashboard_model->getPatientMetrics();
        $data['bed_occupancy'] = $this->dashboard_model->getBedOccupancyData();
        $data['lab_results'] = $this->dashboard_model->getLabResultsData();
        $data['prescription_metrics'] = $this->dashboard_model->getPrescriptionMetrics();
        $data['emergency_cases'] = $this->dashboard_model->getEmergencyCases();
        $data['clinical_quality'] = $this->dashboard_model->getClinicalQualityMetrics();
        $data['patient_satisfaction'] = $this->dashboard_model->getPatientSatisfactionData();
        
        $this->load->view('home/dashboard');
        $this->load->view('dashboard/clinical', $data);
        $this->load->view('home/footer');
    }

    public function financial()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            redirect('home/permission');
        }

        $period_type = $this->input->get_post('period_type') ?: 'this_month';
        $selected_date = $this->input->get_post('date');
        $date_from = $this->input->get_post('date_from');
        $date_to = $this->input->get_post('date_to');

        // Handle date_range string e.g. "01/11/2026 - 30/11/2026"
        $date_range = $this->input->get_post('date_range');
        if (!empty($date_range) && strpos($date_range, ' - ') !== false) {
            $parts = explode(' - ', $date_range);
            if (count($parts) == 2) {
                $d1 = trim($parts[0]);
                $d2 = trim($parts[1]);
                if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $d1)) {
                    $date_from = preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$2-$1', $d1);
                } else {
                    $date_from = date('Y-m-d', strtotime($d1));
                }
                if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $d2)) {
                    $date_to = preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$2-$1', $d2);
                } else {
                    $date_to = date('Y-m-d', strtotime($d2));
                }
                $period_type = 'custom';
            }
        }

        $report = $this->finance_model->getFinancialPerformanceReportData($period_type, $selected_date, $date_from, $date_to);

        $data = array();
        $data['page'] = 'financial_dashboard';
        $data['page_title'] = 'Financial Dashboard';
        $data['settings'] = $this->settings_model->getSettings();
        $data['report'] = $report;
        $data['summary'] = $report['summary'];
        $data['breakdown'] = $report['breakdown'];
        $data['doctor_breakdown'] = $report['doctor_breakdown'];
        $data['referrer_breakdown'] = $report['referrer_breakdown'];
        $data['expense_breakdown'] = $report['expense_breakdown'];
        $data['service_perf'] = $report['service_perf'];
        $data['methods'] = $report['methods'];
        $data['chart_data'] = $report['chart_data'];
        $data['period_type'] = $period_type;
        $data['selected_date'] = $selected_date;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        
        $this->load->view('home/dashboard', $data);
        $this->load->view('dashboard/financial', $data);
        $this->load->view('home/footer');
    }

    public function getFinancialData()
    {
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            return;
        }

        $period_type = $this->input->get_post('period_type') ?: 'this_month';
        $selected_date = $this->input->get_post('date');
        $date_from = $this->input->get_post('date_from');
        $date_to = $this->input->get_post('date_to');

        $date_range = $this->input->get_post('date_range');
        if (!empty($date_range) && strpos($date_range, ' - ') !== false) {
            $parts = explode(' - ', $date_range);
            if (count($parts) == 2) {
                $d1 = trim($parts[0]);
                $d2 = trim($parts[1]);
                if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $d1)) {
                    $date_from = preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$2-$1', $d1);
                } else {
                    $date_from = date('Y-m-d', strtotime($d1));
                }
                if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $d2)) {
                    $date_to = preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$2-$1', $d2);
                } else {
                    $date_to = date('Y-m-d', strtotime($d2));
                }
                $period_type = 'custom';
            }
        }

        $report = $this->finance_model->getFinancialPerformanceReportData($period_type, $selected_date, $date_from, $date_to);

        echo json_encode(['status' => 'success', 'data' => $report]);
    }

    public function operational()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Nurse', 'Receptionist', 'Doctor'))) {
            redirect('home/permission');
        }

        $data = array();
        $data['page'] = 'operational_dashboard';
        $data['page_title'] = 'Operational Dashboard';
        $data['settings'] = $this->settings_model->getSettings();
        
        // Get operational metrics
        $data['appointment_metrics'] = $this->dashboard_model->getAppointmentMetrics();
        $data['staff_metrics'] = $this->dashboard_model->getStaffMetrics();
        $data['inventory_status'] = $this->dashboard_model->getInventoryStatus();
        $data['facility_utilization'] = $this->dashboard_model->getFacilityUtilization();
        $data['patient_queue'] = $this->dashboard_model->getPatientQueueData();
        $data['system_alerts'] = $this->dashboard_model->getSystemAlerts();
        $data['recent_activities'] = $this->dashboard_model->getRecentActivities();
        
        $this->load->view('home/dashboard');
        $this->load->view('dashboard/operational', $data);
        $this->load->view('home/footer');
    }

    // AJAX endpoints for real-time data updates
    public function getRealTimeMetrics()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $dashboard_type = $this->input->post('dashboard_type');
        $hospital_id = $this->session->userdata('hospital_id');
        
        $data = array();
        
        switch ($dashboard_type) {
            case 'executive':
                $data = $this->dashboard_model->getExecutiveMetrics($hospital_id);
                break;
            case 'clinical':
                $data = $this->dashboard_model->getPatientMetrics($hospital_id);
                break;
            case 'financial':
                $data = $this->dashboard_model->getRevenueMetrics($hospital_id);
                break;
            case 'operational':
                $data = $this->dashboard_model->getAppointmentMetrics($hospital_id);
                break;
        }
        
        echo json_encode($data);
    }

    public function getChartData()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $chart_type = $this->input->post('chart_type');
        $period = $this->input->post('period') ?: 30;
        $hospital_id = $this->session->userdata('hospital_id');
        
        $data = array();
        
        switch ($chart_type) {
            case 'revenue_trend':
                $data = $this->dashboard_model->getRevenueTrendData($period, $hospital_id);
                break;
            case 'patient_flow':
                $data = $this->dashboard_model->getPatientFlowData($period, $hospital_id);
                break;
            case 'bed_occupancy':
                $data = $this->dashboard_model->getBedOccupancyData($hospital_id);
                break;
            case 'appointment_trends':
                $data = $this->dashboard_model->getAppointmentTrendsData($period, $hospital_id);
                break;
            case 'expense_breakdown':
                $data = $this->dashboard_model->getExpenseBreakdown($hospital_id);
                break;
        }
        
        echo json_encode($data);
    }

    public function exportDashboard()
    {
        $dashboard_type = $this->input->get('type');
        $format = $this->input->get('format') ?: 'pdf';
        $hospital_id = $this->session->userdata('hospital_id');
        
        $data = array();
        $data['settings'] = $this->settings_model->getSettings();
        
        switch ($dashboard_type) {
            case 'executive':
                $data['metrics'] = $this->dashboard_model->getExecutiveMetrics($hospital_id);
                $data['revenue_data'] = $this->dashboard_model->getRevenueData(12, $hospital_id);
                break;
            case 'clinical':
                $data['patient_metrics'] = $this->dashboard_model->getPatientMetrics($hospital_id);
                $data['bed_occupancy'] = $this->dashboard_model->getBedOccupancyData($hospital_id);
                break;
            case 'financial':
                $data['revenue_metrics'] = $this->dashboard_model->getRevenueMetrics($hospital_id);
                $data['expense_breakdown'] = $this->dashboard_model->getExpenseBreakdown($hospital_id);
                break;
            case 'operational':
                $data['appointment_metrics'] = $this->dashboard_model->getAppointmentMetrics($hospital_id);
                $data['staff_metrics'] = $this->dashboard_model->getStaffMetrics($hospital_id);
                break;
        }
        
        if ($format === 'pdf') {
            $this->load->library('pdf');
            $html = $this->load->view('dashboard/export/' . $dashboard_type, $data, TRUE);
            $this->pdf->generate($html, 'dashboard_' . $dashboard_type . '_' . date('Y-m-d') . '.pdf');
        } else {
            // Export as Excel
            $this->load->library('excel');
            $this->excel->exportDashboard($data, $dashboard_type);
        }
    }
}
