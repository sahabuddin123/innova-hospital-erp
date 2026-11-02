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
}