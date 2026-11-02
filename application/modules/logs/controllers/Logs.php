<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logs extends MX_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('logs_model');
        if (!$this->ion_auth->in_group(array('admin', 'superadmin'))) {
            redirect('home/permission');
        }
    }
    function index()
    {
        $this->load->view('home/dashboard');
        $this->load->view('logs');
        $this->load->view('home/footer');
    }
    function getLogs()
    {

        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "email",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];
        if ($this->ion_auth->in_group(array('admin'))) {
            if ($limit == -1) {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsBysearch($search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsWithoutSearch($order, $dir);
                }
            } else {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsByLimitBySearch($limit, $start, $search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsByLimit($limit, $start, $order, $dir);
                }
            }
        } else {
            if ($limit == -1) {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsBysearchForSuperadmin($search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsWithoutSearchForSuperadmin($order, $dir);
                }
            } else {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsByLimitBySearchForSuperadmin($limit, $start, $search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsByLimitForSuperadmin($limit, $start, $order, $dir);
                }
            }
        }
        $count = count($data['logs']);
        $i = 0;
        foreach ($data['logs'] as $log) {
            $i = $i + 1;

            $info[] = array(

                $log->name,
                $log->email,
                $log->role,
                $log->ip_address,
                $log->date_time
            );
        }

        if (!empty($data['logs'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => $count,
                "recordsFiltered" => $i,
                "data" => $info
            );
        } else {
            $output = array(
                // "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    function transactionLogs()
    {
        $this->load->view('home/dashboard');
        $this->load->view('transaction_logs');
        $this->load->view('home/footer');
    }

    function getTransaction()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "email",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['logs'] = $this->logs_model->getTransactionLogsBysearch($search, $order, $dir);
            } else {
                $data['logs'] = $this->logs_model->getTransactionLogsWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['logs'] = $this->logs_model->getTransactionLogsByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['logs'] = $this->logs_model->getTransactionLogsByLimit($limit, $start, $order, $dir);
            }
        }

        $count = count($data['logs']);
        $i = 0;
        foreach ($data['logs'] as $log) {
            $i = $i + 1;
            if ($log->action == 'Added') {
                $action = '<span class="badge badge-success">' . lang('added') . '</span>';
            } elseif ($log->action == 'Added/Deposited') {
                $action = '<span class="badge badge-success">' . lang('added') . ' ' . lang('deposited') . '</span>';
            } elseif ($log->action == 'Updated') {
                $action = '<span class="badge badge-success">' . lang('updated') . '</span>';
            } elseif ($log->action == 'deleted_deposit') {
                $action = '<span class="badge badge-danger">' . lang('deleted') . ' ' . 'Deposit' . '</span>';
            } elseif ($log->action == 'deleted') {
                $action = '<span class="badge badge-danger">' . lang('deleted') . '</span>';
            } else {
                $action = '<span class="badge badge-info">' . lang('updated') . ' ' . lang('deposited') . '</span>';
            }
            $user_name = $this->db->get_where('users', array('id' => $log->user))->row()->username;
            $info[] = array(

                $log->date_time,
                $log->invoice_id,
                $log->patientname,
                $log->deposit_type,
                $log->amount,
                $user_name,
                $action
            );
        }

        if (!empty($data['logs'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => $count,
                "recordsFiltered" => count($this->logs_model->getTransactionLogs()),
                "data" => $info
            );
        } else {
            $output = array(
                // "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    // =========================================================================
    // PHASE 9: USER ACTIVITY REPORTS & MONITORING CONTROLLER ACTIONS
    // =========================================================================

    private function _resolveDateRange($period = 'this_month', $start_input = null, $end_input = null) {
        $today = date('Y-m-d');
        switch ($period) {
            case 'today':
                return [$today, $today];
            case 'yesterday':
                $y = date('Y-m-d', strtotime('-1 day'));
                return [$y, $y];
            case 'this_week':
                $w = date('Y-m-d', strtotime('monday this week'));
                return [$w, $today];
            case 'this_month':
                return [date('Y-m-01'), date('Y-m-t')];
            case 'last_month':
                return [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))];
            case 'this_year':
                return [date('Y-01-01'), date('Y-12-31')];
            case 'custom':
                $s = !empty($start_input) ? date('Y-m-d', strtotime($start_input)) : date('Y-m-01');
                $e = !empty($end_input) ? date('Y-m-d', strtotime($end_input)) : date('Y-m-d');
                return [$s, $e];
            default:
                return [date('Y-m-01'), date('Y-m-t')];
        }
    }

    private function _getCommonFilterData() {
        return [
            'users' => $this->db->select('id, username, first_name, last_name, email')->get('users')->result(),
            'roles' => $this->db->select('id, name, description')->get('groups')->result(),
            'modules' => ['auth', 'patient', 'appointment', 'doctor', 'finance', 'lab', 'report', 'expense', 'referral', 'settings', 'logs', 'radiology', 'inventory'],
            'action_types' => ['LOGIN', 'LOGOUT', 'VIEW', 'CREATE', 'UPDATE', 'DELETE', 'PRINT', 'EXPORT_EXCEL', 'EXPORT_PDF', 'EXPORT_CSV']
        ];
    }

    /**
     * 1. User Activity Monitoring Dashboard
     */
    public function activityDashboard() {
        $period = $this->input->get('period') ?: 'this_month';
        $start_input = $this->input->get('start_date');
        $end_input = $this->input->get('end_date');
        list($start_date, $end_date) = $this->_resolveDateRange($period, $start_input, $end_input);

        $dashboard_data = $this->logs_model->getActivityDashboardData($start_date, $end_date);
        
        $data = array_merge($dashboard_data, [
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'activity_dashboard'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/activity_dashboard', $data);
        $this->load->view('home/footer');
    }

    /**
     * 2. User Login History
     */
    public function userLoginHistory() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'role' => $this->input->get('role'),
            'login_status' => $this->input->get('login_status'),
            'ip_address' => $this->input->get('ip_address'),
            'search' => $this->input->get('search')
        ];

        $logins = $this->logs_model->getLoginHistory($filters, 200, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'logins' => $logins,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'login_history'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/login_history', $data);
        $this->load->view('home/footer');
    }

    /**
     * 3. User Logout History
     */
    public function userLogoutHistory() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'role' => $this->input->get('role'),
            'login_status' => 'SUCCESS',
            'logout_type' => $this->input->get('logout_type'),
            'search' => $this->input->get('search')
        ];

        $logins = $this->logs_model->getLoginHistory($filters, 200, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'logins' => $logins,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'logout_history'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/logout_history', $data);
        $this->load->view('home/footer');
    }

    /**
     * 4. Failed Login Attempts
     */
    public function failedLogins() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'login_status' => 'FAILED',
            'ip_address' => $this->input->get('ip_address'),
            'is_suspicious' => $this->input->get('is_suspicious'),
            'search' => $this->input->get('search')
        ];

        $failed_logins = $this->logs_model->getLoginHistory($filters, 200, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'failed_logins' => $failed_logins,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'failed_logins'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/failed_logins', $data);
        $this->load->view('home/footer');
    }

    /**
     * 5. User-wise Activity Report
     */
    public function userActivityReport() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'role' => $this->input->get('role'),
            'module_name' => $this->input->get('module_name'),
            'action_type' => $this->input->get('action_type'),
            'search' => $this->input->get('search')
        ];

        $activities = $this->logs_model->getUserActivities($filters, 250, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'activities' => $activities,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'user_activity'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/user_activity', $data);
        $this->load->view('home/footer');
    }

    /**
     * 6. Module Access Report
     */
    public function moduleAccessReport() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        $summary = $this->logs_model->getModuleAccessSummary($filters);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'module_summary' => $summary,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'module_access'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/module_access', $data);
        $this->load->view('home/footer');
    }

    /**
     * 7. Data Entry & Modification Activity
     */
    public function dataEntryActivity() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $action_filter = $this->input->get('action_type') ?: ['CREATE', 'UPDATE', 'DELETE'];

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'role' => $this->input->get('role'),
            'module_name' => $this->input->get('module_name'),
            'action_type' => $action_filter,
            'search' => $this->input->get('search')
        ];

        $activities = $this->logs_model->getUserActivities($filters, 250, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'activities' => $activities,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'data_entry'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/data_entry', $data);
        $this->load->view('home/footer');
    }

    /**
     * 8. User Session History
     */
    public function userSessionReport() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'role' => $this->input->get('role'),
            'session_status' => $this->input->get('session_status'),
            'search' => $this->input->get('search')
        ];

        $sessions = $this->logs_model->getLoginHistory($filters, 200, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'sessions' => $sessions,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'session_report'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/session_report', $data);
        $this->load->view('home/footer');
    }

    /**
     * 9. Print & Export Activity
     */
    public function printExportActivity() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $action_filter = $this->input->get('action_type') ?: [
            'PRINT', 'PRINT_INITIATED', 'EXPORT_EXCEL', 'EXPORT_PDF', 'EXPORT_CSV', 'PDF_EXPORT', 'EXCEL_EXPORT', 'CSV_EXPORT'
        ];

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'role' => $this->input->get('role'),
            'module_name' => $this->input->get('module_name'),
            'action_type' => $action_filter,
            'search' => $this->input->get('search')
        ];

        $activities = $this->logs_model->getUserActivities($filters, 250, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'activities' => $activities,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'print_export'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/print_export', $data);
        $this->load->view('home/footer');
    }

    /**
     * 10. Suspicious Activity Monitoring
     */
    public function suspiciousActivity() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'is_suspicious' => 1,
            'search' => $this->input->get('search')
        ];

        // Fetch suspicious activities and suspicious logins
        $suspicious_activities = $this->logs_model->getUserActivities($filters, 150, 0);
        $suspicious_logins = $this->logs_model->getLoginHistory($filters, 150, 0);
        $common = $this->_getCommonFilterData();

        $data = array_merge($common, [
            'suspicious_activities' => $suspicious_activities,
            'suspicious_logins' => $suspicious_logins,
            'filters' => $filters,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'active_report' => 'suspicious_activity'
        ]);

        $this->load->view('home/dashboard', $data);
        $this->load->view('logs/suspicious_activity', $data);
        $this->load->view('home/footer');
    }

    /**
     * 11. AJAX Details Endpoint for Modal
     */
    public function getActivityDetailAjax() {
        $id = $this->input->get('id') ?: $this->input->post('id');
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
            return;
        }

        $detail = $this->logs_model->getActivityDetailById($id);
        if (!$detail) {
            echo json_encode(['status' => 'error', 'message' => 'Activity record not found']);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $detail]);
    }

    /**
     * 12. Export Activity Logs CSV
     */
    public function exportActivityCsv() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'role' => $this->input->get('role'),
            'module_name' => $this->input->get('module_name'),
            'action_type' => $this->input->get('action_type')
        ];

        $activities = $this->logs_model->getUserActivities($filters, 1000, 0);

        // Audit the export action
        $this->logs_model->logPrintOrExport('logs', 'Activity Report', 'EXPORT_CSV', "Range: $start_date to $end_date");

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=User_Activity_Report_' . date('Ymd_His') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Activity UUID', 'Date Time', 'Username', 'Full Name', 'Role', 'Module', 'Action Type', 'Record Reference', 'Description', 'IP Address', 'Suspicious']);

        foreach ($activities as $a) {
            fputcsv($out, [
                $a['activity_uuid'],
                $a['created_at_formatted'],
                $a['username'],
                $a['full_name'],
                $a['role'],
                strtoupper($a['module_name']),
                $a['action_type'],
                $a['record_reference'],
                $a['description'],
                $a['ip_address'],
                $a['is_suspicious'] ? 'YES' : 'NO'
            ]);
        }
        fclose($out);
        exit;
    }

    /**
     * 13. Export Login History CSV
     */
    public function exportLoginCsv() {
        $period = $this->input->get('period') ?: 'this_month';
        list($start_date, $end_date) = $this->_resolveDateRange($period, $this->input->get('start_date'), $this->input->get('end_date'));

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => $this->input->get('user_id'),
            'login_status' => $this->input->get('login_status')
        ];

        $logins = $this->logs_model->getLoginHistory($filters, 1000, 0);

        // Audit the export action
        $this->logs_model->logPrintOrExport('logs', 'Login History', 'EXPORT_CSV', "Range: $start_date to $end_date");

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=User_Login_History_' . date('Ymd_His') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Session Ref', 'Login Time', 'Username', 'Full Name', 'Role', 'Status', 'Logout Time', 'Duration (Sec)', 'Logout Type', 'IP Address', 'Browser', 'OS', 'Device', 'Suspicious']);

        foreach ($logins as $l) {
            fputcsv($out, [
                $l['session_reference'],
                $l['login_datetime'],
                $l['username'],
                $l['full_name'],
                $l['role'],
                $l['login_status'],
                $l['logout_datetime'] ?: 'N/A',
                $l['session_duration'],
                $l['logout_type'],
                $l['ip_address'],
                $l['browser'],
                $l['operating_system'],
                $l['device'],
                $l['is_suspicious'] ? 'YES' : 'NO'
            ]);
        }
        fclose($out);
        exit;
    }
}
