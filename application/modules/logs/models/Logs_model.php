<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logs_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertLogs($data) {
       $this->db->insert('logs',$data);
    }
    function getLogsWithoutSearch($order, $dir) {
        $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->where('hospital_id',$hospital_ion_user_id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('logs');
        return $query->result();
    }

    function getLogsBySearch($search, $order, $dir) {
        $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $query = $this->db->select('*')
                ->from('logs')
                ->where('hospital_id', $hospital_ion_user_id)
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' )", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    function getLogsByLimit($limit, $start, $order, $dir) {
        $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;

        $this->db->where('hospital_id', $hospital_ion_user_id);
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('logs');
        return $query->result();
    }

    function getLogsByLimitBySearch($limit, $start, $search, $order, $dir) {
        $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('logs')
                ->where('hospital_id', $hospital_ion_user_id)
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' )", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }
    function getLogsWithoutSearchForSuperadmin($order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
     
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('logs');
        return $query->result();
    }

    function getLogsBySearchForSuperadmin($search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $query = $this->db->select('*')
                ->from('logs')
              
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' )", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    function getLogsByLimitForSuperadmin($limit, $start, $order, $dir) {
      
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('logs');
        return $query->result();
    }

    function getLogsByLimitBySearchForSuperadmin($limit, $start, $search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('logs')
                
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' )", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }
    public function insertTransactionLogs($data) {
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;
        $data1 = array('hospital_id' => $hospital_id);
        $data2 = array_merge($data, $data1);
        $this->db->insert('transaction_logs', $data2);

        // Synchronize with Phase 8 Financial Audit Logs
        if ($this->db->table_exists('financial_audit_logs')) {
            $user_id = $data['user'] ?? ($this->ion_auth->get_user_id() ?: 'system');
            $user_obj = $this->ion_auth->user($user_id)->row();
            $username = $user_obj ? $user_obj->username : ($data['patientname'] ?? 'User #' . $user_id);

            $action_str = strtolower($data['action'] ?? '');
            $action_type = 'PAYMENT';
            if (strpos($action_str, 'added') !== false && !empty($data['deposit_type'])) {
                $action_type = 'PAYMENT';
            } elseif (strpos($action_str, 'added') !== false) {
                $action_type = 'CREATE';
            } elseif (strpos($action_str, 'updated') !== false || strpos($action_str, 'deposited') !== false) {
                $action_type = 'PAYMENT';
            } elseif (strpos($action_str, 'discount') !== false) {
                $action_type = 'ADJUSTMENT';
            } elseif (strpos($action_str, 'refund') !== false) {
                $action_type = 'REFUND';
            }

            $mod = 'payments';
            if (strpos($action_str, 'discount') !== false) {
                $mod = 'billing';
            } elseif (strpos($action_str, 'due') !== false) {
                $mod = 'due_collection';
            }

            $amt = isset($data['amount']) ? floatval($data['amount']) : 0.00;
            $inv_id = $data['invoice_id'] ?? null;
            $pt_name = $data['patientname'] ?? '';
            $dep_type = $data['deposit_type'] ?? 'Cash';

            $now = time();
            $hour = intval(date('H', $now));
            $is_suspicious = 0;
            $suspicious_reason = null;
            if ($action_type === 'REFUND') {
                $is_suspicious = 1;
                $suspicious_reason = 'Refund Transaction';
            } elseif ($hour >= 23 || $hour < 6) {
                $is_suspicious = 1;
                $suspicious_reason = 'After-hours transaction (' . date('h:i A', $now) . ')';
            }

            // Check duplicate
            $dup = $this->db->select('id')->from('financial_audit_logs')
                ->where('module_name', $mod)
                ->where('action_type', $action_type)
                ->where('record_id', $inv_id)
                ->where('date_time >=', $now - 3)
                ->get()->row();

            if (!$dup) {
                $this->db->insert('financial_audit_logs', array(
                    'audit_uuid' => 'AUD-TX-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 16)),
                    'transaction_reference' => $inv_id ? 'INV-#' . $inv_id : null,
                    'module_name' => $mod,
                    'action_type' => $action_type,
                    'record_id' => $inv_id,
                    'patient_id' => null,
                    'amount' => $amt,
                    'previous_value' => null,
                    'new_value' => json_encode(['invoice_id' => $inv_id, 'patient' => $pt_name, 'payment_method' => $dep_type, 'amount' => $amt]),
                    'change_description' => ($data['action'] ?? 'Transaction') . ' - Patient: ' . $pt_name . ' (' . $dep_type . ') Amount: ' . $amt,
                    'reason_for_change' => $data['action'] ?? null,
                    'is_suspicious' => $is_suspicious,
                    'suspicious_reason' => $suspicious_reason,
                    'is_historical' => 0,
                    'user_id' => (string)$user_id,
                    'username' => $username,
                    'date_time' => $now,
                    'created_at_formatted' => date('Y-m-d H:i:s', $now),
                    'ip_address' => $this->input->ip_address(),
                    'hospital_id' => $hospital_id
                ));
            }
        }
     }


     function getTransactionLogs() {
         $this->db->where('hospital_id',$this->session->userdata('hospital_id'));
         $this->db->order_by('id', 'desc');
         $query = $this->db->get('transaction_logs');
         return $query->result();
     }



     function getTransactionLogsWithoutSearch($order, $dir) {
       // $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;
        
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->where('hospital_id',$this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('transaction_logs');
        return $query->result();
    }

    function getTransactionLogsBySearch($search, $order, $dir) {
       // $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $query = $this->db->select('*')
                ->from('transaction_logs')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR date_time LIKE '%" . $search . "%' OR deposit_type LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%'  OR action LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    function getTransactionLogsByLimit($limit, $start, $order, $dir) {
       // $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('transaction_logs');
        return $query->result();
    }

    function getTransactionLogsByLimitBySearch($limit, $start, $search, $order, $dir) {
       // $hospital_ion_user_id=$this->db->where('id',$this->session->userdata('hospital_id'))->get('hospital')->row()->ion_user_id;

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('transaction_logs')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR date_time LIKE '%" . $search . "%' OR deposit_type LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%'  OR action LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    // =========================================================================
    // PHASE 9: USER ACTIVITY REPORTS & MONITORING ENGINE
    // =========================================================================

    /**
     * Helper to parse user agent
     */
    private function _getUserAgentDetails() {
        if (!isset($this->agent)) {
            $this->load->library('user_agent');
        }
        $browser = 'Unknown';
        if ($this->agent->is_browser()) {
            $browser = $this->agent->browser() . ' ' . $this->agent->version();
        } elseif ($this->agent->is_robot()) {
            $browser = $this->agent->robot();
        } elseif ($this->agent->is_mobile()) {
            $browser = $this->agent->mobile();
        }

        $platform = $this->agent->platform() ?: 'Unknown OS';
        $device = $this->agent->is_mobile() ? 'Mobile (' . ($this->agent->mobile() ?: 'Smartphone') . ')' : 'Desktop / PC';

        return [
            'browser' => $browser,
            'os' => $platform,
            'device' => $device,
            'user_agent' => $this->agent->agent_string()
        ];
    }

    /**
     * Log successful authentication
     */
    public function logLoginSuccess($user_id, $username, $full_name, $role, $session_reference) {
        $now = time();
        $dt_str = date('Y-m-d H:i:s', $now);
        $agent = $this->_getUserAgentDetails();
        $ip = $this->input->ip_address();
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;

        $insert_data = [
            'session_reference' => $session_reference,
            'user_id' => $user_id,
            'username' => $username,
            'full_name' => $full_name,
            'role' => $role,
            'login_time' => $now,
            'login_datetime' => $dt_str,
            'last_activity_time' => $now,
            'login_status' => 'SUCCESS',
            'session_status' => 'ACTIVE',
            'logout_type' => 'INCOMPLETE',
            'ip_address' => $ip,
            'browser' => $agent['browser'],
            'operating_system' => $agent['os'],
            'device' => $agent['device'],
            'user_agent' => $agent['user_agent'],
            'is_historical' => 0,
            'hospital_id' => $hospital_id
        ];

        $this->db->insert('user_login_logs', $insert_data);
        $login_id = $this->db->insert_id();

        // Also log activity
        $this->logUserActivity([
            'session_reference' => $session_reference,
            'user_id' => $user_id,
            'username' => $username,
            'full_name' => $full_name,
            'role' => $role,
            'module_name' => 'auth',
            'action_type' => 'LOGIN',
            'description' => "User {$username} logged in successfully via {$agent['browser']} on {$agent['os']}",
            'ip_address' => $ip
        ]);

        return $login_id;
    }

    /**
     * Log failed authentication attempt
     */
    public function logLoginFailure($username_entered, $failure_reason = 'Invalid username or password') {
        $now = time();
        $dt_str = date('Y-m-d H:i:s', $now);
        $agent = $this->_getUserAgentDetails();
        $ip = $this->input->ip_address();
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;

        // Lookup if username exists (safely, without exposing password)
        $user_row = $this->db->select('id, username, first_name, last_name')->from('users')
            ->where('username', $username_entered)
            ->or_where('email', $username_entered)
            ->get()->row();
        
        $uid = $user_row ? $user_row->id : null;
        $uname = $user_row ? $user_row->username : $username_entered;
        $fname = $user_row ? trim($user_row->first_name . ' ' . $user_row->last_name) : 'Unregistered User';

        // Check recent failed attempts from this IP within 15 minutes
        $fifteen_mins_ago = $now - (15 * 60);
        $recent_fails = $this->db->select('COUNT(*) as cnt')->from('user_login_logs')
            ->where('login_status', 'FAILED')
            ->where('ip_address', $ip)
            ->where('login_time >=', $fifteen_mins_ago)
            ->get()->row()->cnt ?? 0;

        $is_suspicious = 0;
        $suspicious_reason = null;
        if ($recent_fails >= 2) {
            $is_suspicious = 1;
            $suspicious_reason = "Multiple failed login attempts from IP ($ip) within 15 minutes";
        }

        $sess_ref = 'FAIL-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));

        $insert_data = [
            'session_reference' => $sess_ref,
            'user_id' => $uid,
            'username' => $uname,
            'full_name' => $fname,
            'role' => 'Guest',
            'login_time' => $now,
            'login_datetime' => $dt_str,
            'last_activity_time' => $now,
            'login_status' => 'FAILED',
            'failure_reason' => $failure_reason,
            'session_status' => 'INCOMPLETE',
            'logout_type' => 'INCOMPLETE',
            'ip_address' => $ip,
            'browser' => $agent['browser'],
            'operating_system' => $agent['os'],
            'device' => $agent['device'],
            'user_agent' => $agent['user_agent'],
            'is_suspicious' => $is_suspicious,
            'suspicious_reason' => $suspicious_reason,
            'is_historical' => 0,
            'hospital_id' => $hospital_id
        ];

        $this->db->insert('user_login_logs', $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Log user logout
     */
    public function logLogout($session_reference, $logout_type = 'MANUAL') {
        if (empty($session_reference)) return false;

        $sess = $this->db->get_where('user_login_logs', ['session_reference' => $session_reference])->row();
        if (!$sess) return false;

        $now = time();
        $dt_str = date('Y-m-d H:i:s', $now);
        $duration = max(0, $now - $sess->login_time);

        $update_data = [
            'logout_time' => $now,
            'logout_datetime' => $dt_str,
            'session_duration' => $duration,
            'logout_type' => $logout_type,
            'session_status' => 'LOGGED_OUT'
        ];
        $this->db->where('id', $sess->id)->update('user_login_logs', $update_data);

        // Activity log
        $dur_formatted = gmdate("H:i:s", $duration);
        $this->logUserActivity([
            'session_reference' => $session_reference,
            'user_id' => $sess->user_id,
            'username' => $sess->username,
            'full_name' => $sess->full_name,
            'role' => $sess->role,
            'module_name' => 'auth',
            'action_type' => 'LOGOUT',
            'description' => "User {$sess->username} logged out ({$logout_type}, duration: {$dur_formatted})",
            'ip_address' => $this->input->ip_address()
        ]);

        return true;
    }

    /**
     * Log generic user activity
     */
    public function logUserActivity($data) {
        $user_id = $data['user_id'] ?? ($this->ion_auth->get_user_id() ?: null);
        $username = $data['username'] ?? null;
        $full_name = $data['full_name'] ?? null;
        $role = $data['role'] ?? null;

        if ($user_id && (empty($username) || empty($role))) {
            $user_obj = $this->ion_auth->user($user_id)->row();
            if ($user_obj) {
                $username = $username ?: $user_obj->username;
                $full_name = $full_name ?: trim(($user_obj->first_name ?? '') . ' ' . ($user_obj->last_name ?? ''));
                if (empty($role)) {
                    $grp = $this->ion_auth->get_users_groups($user_id)->row();
                    $role = $grp ? $grp->name : 'User';
                }
            }
        }

        $now = time();
        $module = strtolower($data['module_name'] ?? 'general');
        $action = strtoupper($data['action_type'] ?? 'VIEW');
        $rec_id = $data['record_id'] ?? null;

        // Idempotency / duplicate check within 2 seconds
        if ($user_id && $rec_id) {
            $dup = $this->db->select('id')->from('user_activity_logs')
                ->where('user_id', $user_id)
                ->where('module_name', $module)
                ->where('action_type', $action)
                ->where('record_id', (string)$rec_id)
                ->where('created_at >=', $now - 2)
                ->get()->row();
            if ($dup) return $dup->id;
        }

        $agent = $this->_getUserAgentDetails();
        $ip = $data['ip_address'] ?? ($this->input->ip_address() ?: '127.0.0.1');
        $sess_ref = $data['session_reference'] ?? ($this->session->userdata('activity_session_ref') ?: null);
        $hospital_id = $data['hospital_id'] ?? ($this->session->userdata('hospital_id') ?: 1);

        $uuid = $data['activity_uuid'] ?? ('ACT-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 16)));
        $meta = is_array($data['metadata'] ?? null) ? json_encode($data['metadata'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($data['metadata'] ?? null);

        $insert_data = [
            'activity_uuid' => $uuid,
            'session_reference' => $sess_ref,
            'user_id' => $user_id,
            'username' => $username,
            'full_name' => $full_name,
            'role' => $role,
            'module_name' => $module,
            'action_type' => $action,
            'record_type' => $data['record_type'] ?? null,
            'record_id' => $rec_id ? (string)$rec_id : null,
            'record_reference' => $data['record_reference'] ?? null,
            'description' => $data['description'] ?? ($action . ' on ' . $module),
            'metadata' => $meta,
            'ip_address' => $ip,
            'user_agent' => $agent['user_agent'],
            'is_suspicious' => intval($data['is_suspicious'] ?? 0),
            'suspicious_reason' => $data['suspicious_reason'] ?? null,
            'is_historical' => intval($data['is_historical'] ?? 0),
            'created_at' => $now,
            'created_at_formatted' => date('Y-m-d H:i:s', $now),
            'hospital_id' => $hospital_id
        ];

        $this->db->insert('user_activity_logs', $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Track human module access and update heartbeat
     */
    public function trackModuleAccess($controller, $method, $session_reference = null) {
        $user_id = $this->ion_auth->get_user_id();
        if (!$user_id) return;

        $now = time();

        // 1. Update session heartbeat
        if ($session_reference) {
            $this->db->where('session_reference', $session_reference)
                ->update('user_login_logs', ['last_activity_time' => $now]);
        }

        // 2. Throttle module access logging (max 1 per module per user per 60 seconds)
        $recent_access = $this->session->userdata('last_access_' . $controller);
        if ($recent_access && ($now - $recent_access) < 60) {
            return;
        }
        $this->session->set_userdata('last_access_' . $controller, $now);

        $module_map = [
            'home' => 'Dashboard',
            'patient' => 'Patient Management',
            'appointment' => 'Appointment Management',
            'doctor' => 'Doctor Management',
            'finance' => 'Billing & Finance',
            'lab' => 'Laboratory & Diagnostics',
            'report' => 'Report Management',
            'expense' => 'Expense Management',
            'referral' => 'Referral Management',
            'settings' => 'System Settings',
            'logs' => 'Audit & System Logs',
            'radiology' => 'Radiology & Imaging',
            'inventory' => 'Hospital Inventory'
        ];
        $mod_name = $module_map[$controller] ?? ucfirst($controller);

        $this->logUserActivity([
            'session_reference' => $session_reference,
            'user_id' => $user_id,
            'module_name' => $controller,
            'action_type' => 'VIEW',
            'description' => "Accessed module: {$mod_name} ({$controller}/{$method})"
        ]);
    }

    /**
     * Log Print or Export Action
     */
    public function logPrintOrExport($module_name, $report_name, $action_type, $reference = null) {
        $this->logUserActivity([
            'module_name' => $module_name,
            'action_type' => $action_type,
            'record_reference' => $reference,
            'description' => "{$action_type} for {$report_name}" . ($reference ? " ({$reference})" : "")
        ]);
    }

    /**
     * Build WHERE conditions for user_login_logs
     */
    private function _buildLoginLogsQuery($filters = []) {
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);

        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['username'])) {
            $this->db->like('username', $filters['username']);
        }
        if (!empty($filters['role'])) {
            $this->db->where('role', $filters['role']);
        }
        if (!empty($filters['login_status'])) {
            $this->db->where('login_status', $filters['login_status']);
        }
        if (!empty($filters['session_status'])) {
            $this->db->where('session_status', $filters['session_status']);
        }
        if (!empty($filters['logout_type'])) {
            $this->db->where('logout_type', $filters['logout_type']);
        }
        if (!empty($filters['ip_address'])) {
            $this->db->like('ip_address', $filters['ip_address']);
        }
        if (isset($filters['is_suspicious']) && $filters['is_suspicious'] !== '') {
            $this->db->where('is_suspicious', intval($filters['is_suspicious']));
        }
        if (!empty($filters['start_date'])) {
            $start_ts = strtotime($filters['start_date'] . ' 00:00:00');
            if ($start_ts) $this->db->where('login_time >=', $start_ts);
        }
        if (!empty($filters['end_date'])) {
            $end_ts = strtotime($filters['end_date'] . ' 23:59:59');
            if ($end_ts) $this->db->where('login_time <=', $end_ts);
        }
        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $this->db->group_start();
            $this->db->like('username', $term);
            $this->db->or_like('full_name', $term);
            $this->db->or_like('ip_address', $term);
            $this->db->or_like('browser', $term);
            $this->db->or_like('operating_system', $term);
            $this->db->group_end();
        }
    }

    public function getLoginHistory($filters = [], $limit = 50, $offset = 0) {
        $this->db->from('user_login_logs');
        $this->_buildLoginLogsQuery($filters);
        $this->db->order_by('login_time', 'DESC');
        if ($limit > 0) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    public function countLoginHistory($filters = []) {
        $this->db->from('user_login_logs');
        $this->_buildLoginLogsQuery($filters);
        return $this->db->count_all_results();
    }

    /**
     * Build WHERE conditions for user_activity_logs
     */
    private function _buildActivityLogsQuery($filters = []) {
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);

        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['username'])) {
            $this->db->like('username', $filters['username']);
        }
        if (!empty($filters['role'])) {
            $this->db->where('role', $filters['role']);
        }
        if (!empty($filters['module_name'])) {
            if (is_array($filters['module_name'])) {
                $this->db->where_in('module_name', $filters['module_name']);
            } else {
                $this->db->where('module_name', $filters['module_name']);
            }
        }
        if (!empty($filters['action_type'])) {
            if (is_array($filters['action_type'])) {
                $this->db->where_in('action_type', $filters['action_type']);
            } else {
                $this->db->where('action_type', $filters['action_type']);
            }
        }
        if (!empty($filters['record_type'])) {
            $this->db->where('record_type', $filters['record_type']);
        }
        if (!empty($filters['record_id'])) {
            $this->db->where('record_id', (string)$filters['record_id']);
        }
        if (!empty($filters['ip_address'])) {
            $this->db->like('ip_address', $filters['ip_address']);
        }
        if (isset($filters['is_suspicious']) && $filters['is_suspicious'] !== '') {
            $this->db->where('is_suspicious', intval($filters['is_suspicious']));
        }
        if (!empty($filters['start_date'])) {
            $start_ts = strtotime($filters['start_date'] . ' 00:00:00');
            if ($start_ts) $this->db->where('created_at >=', $start_ts);
        }
        if (!empty($filters['end_date'])) {
            $end_ts = strtotime($filters['end_date'] . ' 23:59:59');
            if ($end_ts) $this->db->where('created_at <=', $end_ts);
        }
        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $this->db->group_start();
            $this->db->like('username', $term);
            $this->db->or_like('full_name', $term);
            $this->db->or_like('description', $term);
            $this->db->or_like('record_reference', $term);
            $this->db->or_like('ip_address', $term);
            $this->db->group_end();
        }
    }

    public function getUserActivities($filters = [], $limit = 50, $offset = 0) {
        $this->db->from('user_activity_logs');
        $this->_buildActivityLogsQuery($filters);
        $this->db->order_by('created_at', 'DESC');
        if ($limit > 0) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    public function countUserActivities($filters = []) {
        $this->db->from('user_activity_logs');
        $this->_buildActivityLogsQuery($filters);
        return $this->db->count_all_results();
    }

    public function getActivityDetailById($id) {
        $this->db->from('user_activity_logs');
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('activity_uuid', $id);
        }
        $row = $this->db->get()->row_array();
        if ($row) {
            $row['metadata_decoded'] = json_decode($row['metadata'] ?? '', true);
        }
        return $row;
    }

    /**
     * Aggregate Module Access Report
     */
    public function getModuleAccessSummary($filters = []) {
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;
        $this->db->select("
            module_name,
            COUNT(*) as access_count,
            COUNT(DISTINCT user_id) as unique_users,
            MAX(created_at) as last_access_time,
            MAX(created_at_formatted) as last_access_datetime
        ");
        $this->db->from('user_activity_logs');
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('action_type', 'VIEW');

        if (!empty($filters['start_date'])) {
            $start_ts = strtotime($filters['start_date'] . ' 00:00:00');
            if ($start_ts) $this->db->where('created_at >=', $start_ts);
        }
        if (!empty($filters['end_date'])) {
            $end_ts = strtotime($filters['end_date'] . ' 23:59:59');
            if ($end_ts) $this->db->where('created_at <=', $end_ts);
        }

        $this->db->group_by('module_name');
        $this->db->order_by('access_count', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * User Activity Monitoring Dashboard Data & Charts Aggregation
     */
    public function getActivityDashboardData($start_date = null, $end_date = null) {
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;
        $where_hosp = '(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)';

        $start_ts = $start_date ? strtotime($start_date . ' 00:00:00') : strtotime('-30 days 00:00:00');
        $end_ts = $end_date ? strtotime($end_date . ' 23:59:59') : time();
        $today_start = strtotime('today 00:00:00');

        // 1. Total Registered Users
        $total_users = $this->db->where('active', 1)->count_all_results('users');

        // 2. Active Users Today
        $active_today = $this->db->query("
            SELECT COUNT(DISTINCT user_id) as cnt FROM user_login_logs 
            WHERE login_time >= $today_start AND login_status = 'SUCCESS'
        ")->row()->cnt ?? 0;

        // 3. Login Attempts & Success/Fail
        $login_stats = $this->db->query("
            SELECT 
                COUNT(*) as total_logins,
                SUM(CASE WHEN login_status = 'SUCCESS' THEN 1 ELSE 0 END) as success_logins,
                SUM(CASE WHEN login_status = 'FAILED' THEN 1 ELSE 0 END) as failed_logins,
                COUNT(DISTINCT user_id) as unique_login_users
            FROM user_login_logs
            WHERE login_time >= $start_ts AND login_time <= $end_ts
        ")->row_array();

        // 4. Total Activities
        $total_activities = $this->db->query("
            SELECT COUNT(*) as cnt FROM user_activity_logs 
            WHERE created_at >= $start_ts AND created_at <= $end_ts
        ")->row()->cnt ?? 0;

        // 5. Most Active User
        $top_user = $this->db->query("
            SELECT username, COUNT(*) as cnt FROM user_activity_logs 
            WHERE created_at >= $start_ts AND created_at <= $end_ts AND username IS NOT NULL
            GROUP BY username ORDER BY cnt DESC LIMIT 1
        ")->row_array();

        // 6. Most Accessed Module
        $top_module = $this->db->query("
            SELECT module_name, COUNT(*) as cnt FROM user_activity_logs 
            WHERE created_at >= $start_ts AND created_at <= $end_ts
            GROUP BY module_name ORDER BY cnt DESC LIMIT 1
        ")->row_array();

        // 7. Daily Login Trend (Chart 1)
        $daily_logins = $this->db->query("
            SELECT 
                DATE(login_datetime) as log_date,
                SUM(CASE WHEN login_status = 'SUCCESS' THEN 1 ELSE 0 END) as success_cnt,
                SUM(CASE WHEN login_status = 'FAILED' THEN 1 ELSE 0 END) as fail_cnt
            FROM user_login_logs
            WHERE login_time >= $start_ts AND login_time <= $end_ts
            GROUP BY DATE(login_datetime) ORDER BY log_date ASC
        ")->result_array();

        // 8. User-wise Activity (Chart 3)
        $user_activity_chart = $this->db->query("
            SELECT username, COUNT(*) as act_count FROM user_activity_logs
            WHERE created_at >= $start_ts AND created_at <= $end_ts AND username IS NOT NULL
            GROUP BY username ORDER BY act_count DESC LIMIT 7
        ")->result_array();

        // 9. Role-wise Activity (Chart 4)
        $role_activity_chart = $this->db->query("
            SELECT role, COUNT(*) as act_count FROM user_activity_logs
            WHERE created_at >= $start_ts AND created_at <= $end_ts AND role IS NOT NULL
            GROUP BY role ORDER BY act_count DESC
        ")->result_array();

        // 10. Module-wise Access (Chart 5)
        $module_access_chart = $this->db->query("
            SELECT module_name, COUNT(*) as access_count FROM user_activity_logs
            WHERE created_at >= $start_ts AND created_at <= $end_ts
            GROUP BY module_name ORDER BY access_count DESC LIMIT 8
        ")->result_array();

        // 11. Hourly Activity Distribution (Chart 6)
        $hourly_chart = $this->db->query("
            SELECT HOUR(created_at_formatted) as act_hour, COUNT(*) as count FROM user_activity_logs
            WHERE created_at >= $start_ts AND created_at <= $end_ts
            GROUP BY HOUR(created_at_formatted) ORDER BY act_hour ASC
        ")->result_array();

        // 12. Print & Export Activity (Chart 7)
        $print_export_chart = $this->db->query("
            SELECT action_type, COUNT(*) as count FROM user_activity_logs
            WHERE created_at >= $start_ts AND created_at <= $end_ts 
            AND action_type IN ('PRINT_INITIATED', 'PDF_EXPORT', 'EXCEL_EXPORT', 'CSV_EXPORT')
            GROUP BY action_type
        ")->result_array();

        // 13. Data Modification Trends (Chart 8: CREATE, UPDATE, DELETE)
        $crud_chart = $this->db->query("
            SELECT 
                DATE(created_at_formatted) as act_date,
                SUM(CASE WHEN action_type = 'CREATE' THEN 1 ELSE 0 END) as creates,
                SUM(CASE WHEN action_type = 'UPDATE' THEN 1 ELSE 0 END) as updates,
                SUM(CASE WHEN action_type = 'DELETE' THEN 1 ELSE 0 END) as deletes
            FROM user_activity_logs
            WHERE created_at >= $start_ts AND created_at <= $end_ts 
            AND action_type IN ('CREATE', 'UPDATE', 'DELETE')
            GROUP BY DATE(created_at_formatted) ORDER BY act_date ASC
        ")->result_array();

        // 14. Recent Suspicious Events
        $suspicious_events = $this->db->query("
            SELECT * FROM user_activity_logs
            WHERE is_suspicious = 1 AND created_at >= $start_ts AND created_at <= $end_ts
            ORDER BY created_at DESC LIMIT 10
        ")->result_array();

        return [
            'kpi' => [
                'total_users' => $total_users,
                'active_users_today' => $active_today,
                'total_login_attempts' => $login_stats['total_logins'] ?? 0,
                'successful_logins' => $login_stats['success_logins'] ?? 0,
                'failed_logins' => $login_stats['failed_logins'] ?? 0,
                'unique_active_users' => $login_stats['unique_login_users'] ?? 0,
                'total_activities' => $total_activities,
                'most_active_user' => $top_user['username'] ?? 'N/A',
                'most_active_user_count' => $top_user['cnt'] ?? 0,
                'most_accessed_module' => $top_module['module_name'] ?? 'N/A',
                'most_accessed_module_count' => $top_module['cnt'] ?? 0
            ],
            'daily_logins' => $daily_logins,
            'user_activity_chart' => $user_activity_chart,
            'role_activity_chart' => $role_activity_chart,
            'module_access_chart' => $module_access_chart,
            'hourly_chart' => $hourly_chart,
            'print_export_chart' => $print_export_chart,
            'crud_chart' => $crud_chart,
            'suspicious_events' => $suspicious_events,
            'start_date' => date('Y-m-d', $start_ts),
            'end_date' => date('Y-m-d', $end_ts)
        ];
    }
}
