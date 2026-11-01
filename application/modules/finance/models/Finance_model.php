<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Finance_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensureExpenseSchema();
        $this->ensurePaymentSchema();
    }

    private function ensureExpenseSchema()
    {
        if ($this->db->table_exists('expense')) {
            if (!$this->db->field_exists('pay_to', 'expense')) {
                $this->db->query("ALTER TABLE `expense` ADD COLUMN `pay_to` VARCHAR(255) NULL AFTER `note`");
            }
            if (!$this->db->field_exists('payer_name', 'expense')) {
                $this->db->query("ALTER TABLE `expense` ADD COLUMN `payer_name` VARCHAR(255) NULL AFTER `pay_to`");
            }
            if (!$this->db->field_exists('bill_date', 'expense')) {
                $this->db->query("ALTER TABLE `expense` ADD COLUMN `bill_date` VARCHAR(100) NULL AFTER `payer_name`");
            }
        }
    }

    private function ensurePaymentSchema()
    {
        if ($this->db->table_exists('payment')) {
            if (!$this->db->field_exists('is_edited', 'payment')) {
                $this->db->query("ALTER TABLE `payment` ADD COLUMN `is_edited` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
            }
            if (!$this->db->field_exists('edited_by', 'payment')) {
                $this->db->query("ALTER TABLE `payment` ADD COLUMN `edited_by` INT(11) NULL AFTER `is_edited`");
            }
            if (!$this->db->field_exists('edited_at', 'payment')) {
                $this->db->query("ALTER TABLE `payment` ADD COLUMN `edited_at` INT(11) NULL AFTER `edited_by`");
            }
        }
    }

    function insertPayment($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('payment', $data2);
    }

    function getPayment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('payment');
        return $query->result();
    }

    function getPaymentWitoutSearch($order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('payment');
        return $query->result();
    }

    function getPaymentBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->select('*')
            ->from('payment')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }

    function getPaymentByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('payment');
        return $query->result();
    }

    function getGatewayByName($name)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('name', $name);
        $query = $this->db->get('paymentgateway')->row();
        return $query;
    }



    function getPaymentByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $this->db->select('*')
            ->from('payment')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }

    function getPaymentById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('payment');
        return $query->row();
    }

    function getPaymentByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $id);
        $query = $this->db->get('payment');
        return $query->result();
    }

    function getPaymentByPatientIdByDate($id, $date_from, $date_to)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get('payment');
        return $query->result();
    }

    function getPaymentByUserId($id)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('user', $id);
        $query = $this->db->get('payment');
        return $query->result();
    }

    function thisMonthPayment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('payment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->date)) {
                $total[] = $q->gross_total;
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisMonthExpense()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('expense')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->date)) {
                $total[] = $q->amount;
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisMonthAppointment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('appointment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->date)) {
                $total[] = '1';
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisDayPayment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('payment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('d/m/Y', time()) == date('d/m/Y', $q->date)) {
                $total[] = $q->gross_total;
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisDayExpense()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('expense')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('d/m/Y', time()) == date('d/m/Y', $q->date)) {
                $total[] = $q->amount;
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisDayAppointment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('appointment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('d/m/Y', time()) == date('d/m/Y', $q->date)) {
                $total[] = '1';
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisYearPayment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('payment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->date)) {
                $total[] = $q->gross_total;
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisYearExpense()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('expense')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->date)) {
                $total[] = $q->amount;
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisYearAppointment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('appointment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->date)) {
                $total[] = '1';
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisMonthAppointmentTreated()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('appointment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->date)) {
                if ($q->status == 'Treated') {
                    $total[] = '1';
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisMonthAppointmentCancelled()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('appointment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->date)) {
                if ($q->status == 'Cancelled') {
                    $total[] = '1';
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function getPaymentPerMonthThisYear()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('payment')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->date)) {
                if (date('m', $q->date) == '01') {
                    $total['january'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '02') {
                    $total['february'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '03') {
                    $total['march'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '04') {
                    $total['april'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '05') {
                    $total['may'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '06') {
                    $total['june'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '07') {
                    $total['july'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '08') {
                    $total['august'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '09') {
                    $total['september'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '10') {
                    $total['october'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '11') {
                    $total['november'][] = $q->gross_total;
                }
                if (date('m', $q->date) == '12') {
                    $total['december'][] = $q->gross_total;
                }
            }
        }


        if (!empty($total['january'])) {
            $total['january'] = array_sum($total['january']);
        } else {
            $total['january'] = 0;
        }
        if (!empty($total['february'])) {
            $total['february'] = array_sum($total['february']);
        } else {
            $total['february'] = 0;
        }
        if (!empty($total['march'])) {
            $total['march'] = array_sum($total['march']);
        } else {
            $total['march'] = 0;
        }
        if (!empty($total['april'])) {
            $total['april'] = array_sum($total['april']);
        } else {
            $total['april'] = 0;
        }
        if (!empty($total['may'])) {
            $total['may'] = array_sum($total['may']);
        } else {
            $total['may'] = 0;
        }
        if (!empty($total['june'])) {
            $total['june'] = array_sum($total['june']);
        } else {
            $total['june'] = 0;
        }
        if (!empty($total['july'])) {
            $total['july'] = array_sum($total['july']);
        } else {
            $total['july'] = 0;
        }
        if (!empty($total['august'])) {
            $total['august'] = array_sum($total['august']);
        } else {
            $total['august'] = 0;
        }
        if (!empty($total['september'])) {
            $total['september'] = array_sum($total['september']);
        } else {
            $total['september'] = 0;
        }
        if (!empty($total['october'])) {
            $total['october'] = array_sum($total['october']);
        } else {
            $total['october'] = 0;
        }
        if (!empty($total['november'])) {
            $total['november'] = array_sum($total['november']);
        } else {
            $total['november'] = 0;
        }
        if (!empty($total['december'])) {
            $total['december'] = array_sum($total['december']);
        } else {
            $total['december'] = 0;
        }

        return $total;
    }

    function getExpensePerMonthThisYear()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('expense')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->date)) {
                if (date('m', $q->date) == '01') {
                    $total['january'][] = $q->amount;
                }
                if (date('m', $q->date) == '02') {
                    $total['february'][] = $q->amount;
                }
                if (date('m', $q->date) == '03') {
                    $total['march'][] = $q->amount;
                }
                if (date('m', $q->date) == '04') {
                    $total['april'][] = $q->amount;
                }
                if (date('m', $q->date) == '05') {
                    $total['may'][] = $q->amount;
                }
                if (date('m', $q->date) == '06') {
                    $total['june'][] = $q->amount;
                }
                if (date('m', $q->date) == '07') {
                    $total['july'][] = $q->amount;
                }
                if (date('m', $q->date) == '08') {
                    $total['august'][] = $q->amount;
                }
                if (date('m', $q->date) == '09') {
                    $total['september'][] = $q->amount;
                }
                if (date('m', $q->date) == '10') {
                    $total['october'][] = $q->amount;
                }
                if (date('m', $q->date) == '11') {
                    $total['november'][] = $q->amount;
                }
                if (date('m', $q->date) == '12') {
                    $total['december'][] = $q->amount;
                }
            }
        }


        if (!empty($total['january'])) {
            $total['january'] = array_sum($total['january']);
        } else {
            $total['january'] = 0;
        }
        if (!empty($total['february'])) {
            $total['february'] = array_sum($total['february']);
        } else {
            $total['february'] = 0;
        }
        if (!empty($total['march'])) {
            $total['march'] = array_sum($total['march']);
        } else {
            $total['march'] = 0;
        }
        if (!empty($total['april'])) {
            $total['april'] = array_sum($total['april']);
        } else {
            $total['april'] = 0;
        }
        if (!empty($total['may'])) {
            $total['may'] = array_sum($total['may']);
        } else {
            $total['may'] = 0;
        }
        if (!empty($total['june'])) {
            $total['june'] = array_sum($total['june']);
        } else {
            $total['june'] = 0;
        }
        if (!empty($total['july'])) {
            $total['july'] = array_sum($total['july']);
        } else {
            $total['july'] = 0;
        }
        if (!empty($total['august'])) {
            $total['august'] = array_sum($total['august']);
        } else {
            $total['august'] = 0;
        }
        if (!empty($total['september'])) {
            $total['september'] = array_sum($total['september']);
        } else {
            $total['september'] = 0;
        }
        if (!empty($total['october'])) {
            $total['october'] = array_sum($total['october']);
        } else {
            $total['october'] = 0;
        }
        if (!empty($total['november'])) {
            $total['november'] = array_sum($total['november']);
        } else {
            $total['november'] = 0;
        }
        if (!empty($total['december'])) {
            $total['december'] = array_sum($total['december']);
        } else {
            $total['december'] = 0;
        }

        return $total;
    }

    function getOtPaymentByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $id);
        $query = $this->db->get('ot_payment');
        return $query->result();
    }

    function getOtPaymentByUserId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('user', $id);
        $query = $this->db->get('ot_payment');
        return $query->result();
    }

    function insertDeposit($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('patient_deposit', $data2);
    }

    function getDeposit()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('patient_deposit');
        return $query->result();
    }

    function updateDeposit($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('patient_deposit', $data);
    }

    function getDepositById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('patient_deposit');
        return $query->row();
    }

    function getDepositByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $id);
        $query = $this->db->get('patient_deposit');
        return $query->result();
    }

    function getDepositByPatientIdByDate($id, $date_from, $date_to)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get('patient_deposit');
        return $query->result();
    }

    function getDepositByUserId($id)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('user', $id);
        $query = $this->db->get('patient_deposit');
        return $query->result();
    }

    function deleteDeposit($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('patient_deposit');
    }

    function deleteDepositByInvoiceId($id)
    {
        $this->db->where('payment_id', $id);
        $this->db->delete('patient_deposit');
    }
    function deleteLabByInvoiceId($id)
    {
        $this->db->where('invoice_id', $id);
        $this->db->delete('lab');
    }

    function getPaymentByPatientIdByStatus($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $this->db->where('status', 'unpaid');
        $query = $this->db->get('payment');
        return $query->result();
    }

    function getOtPaymentByPatientIdByStatus($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $this->db->where('status', 'unpaid');
        $query = $this->db->get('ot_payment');
        return $query->result();
    }

    function updatePayment($id, $data)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->update('payment', $data);
    }

    function insertOtPayment($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('ot_payment', $data2);
    }

    function getOtPayment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('ot_payment');
        return $query->result();
    }

    function getOtPaymentById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('ot_payment');
        return $query->row();
    }

    function updateOtPayment($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('ot_payment', $data);
    }

    function deleteOtPayment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('ot_payment');
    }

    function insertPaymentCategory($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('payment_category', $data2);
    }

    function getPaymentCategory($status = 'Active')
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($status !== null && $status !== 'all') {
            $this->db->where('status', $status);
        }
        $this->db->order_by('payment_category_name', 'asc');
        $this->db->order_by('category', 'asc');
        $query = $this->db->get('payment_category');
        return $query->result();
    }

    function getPaymentCategoryById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('payment_category');
        return $query->row();
    }

    function getDoctorCommissionByCategory($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('payment_category');
        return $query->row();
    }

    function updatePaymentCategory($id, $data)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->update('payment_category', $data);
    }

    function deletePayment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('payment');
    }

    /**
     * Permanently deletes an invoice and safely reverses all collections, deposits,
     * payment items, lab tests, and referral commissions.
     */
    function deleteInvoicePermanently($id, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        $payment = $this->db->where('id', $id)
            ->where('hospital_id', $hospital_id)
            ->get('payment')
            ->row();

        if (empty($payment)) {
            return false;
        }

        $this->db->trans_start();

        // 1. Safely reverse referral commissions (deduct from wallet balance & cancel ledger)
        if (!empty($payment->referrer)) {
            if (!isset($this->referral_model)) {
                $this->load->model('referral/referral_model');
            }
            $this->referral_model->reverseCommissionOnInvoiceCancellation($id, $hospital_id, 'Invoice deleted: #' . $id);
        }
        // Remove ledger records for this invoice
        $this->db->where('invoice_id', $id)
            ->where('hospital_id', $hospital_id)
            ->delete('referral_commission_ledger');

        // 2. Remove all deposits / collections associated with this invoice (patient_deposit)
        $this->db->where('payment_id', $id)
            ->where('hospital_id', $hospital_id)
            ->delete('patient_deposit');

        // 3. Remove all diagnostic lab test records created under this invoice
        $this->db->where('invoice_id', $id)
            ->where('hospital_id', $hospital_id)
            ->delete('lab');

        // 4. Remove all payment items (tests/services line items)
        $this->db->where('payment_id', $id)
            ->where('hospital_id', $hospital_id)
            ->delete('payment_items');

        // 5. Unlink any appointments tied to this payment
        $this->db->where('payment_id', $id)
            ->where('hospital_id', $hospital_id)
            ->set('payment_id', null)
            ->set('payment_status', 'unpaid')
            ->update('appointment');

        // 6. Unlink IPD bed diagnostic / medicine / service records if applicable
        $this->db->where('payment_id', $id)->where('hospital_id', $hospital_id)->set('payment_id', null)->update('bed_diagnostic');
        $this->db->where('payment_id', $id)->where('hospital_id', $hospital_id)->set('payment_id', null)->update('bed_medicine');
        $this->db->where('payment_id', $id)->where('hospital_id', $hospital_id)->set('payment_id', null)->update('bed_service');

        // 7. Log the permanent deletion in transaction logs
        if (!isset($this->logs_model)) {
            $this->load->model('logs/logs_model');
        }
        $data_logs = array(
            'date_time' => date('d-m-Y H:i'),
            'patientname' => !empty($payment->patient_name) ? $payment->patient_name : '',
            'invoice_id' => $payment->id,
            'action' => 'Deleted Invoice',
            'deposit_type' => !empty($payment->deposit_type) ? $payment->deposit_type : '',
            'user' => $this->ion_auth->get_user_id(),
            'amount' => !empty($payment->amount_received) ? $payment->amount_received : 0,
            'hospital_id' => $hospital_id
        );
        $this->logs_model->insertTransactionLogs($data_logs);

        // 8. Delete the payment record itself
        $this->db->where('id', $id)
            ->where('hospital_id', $hospital_id)
            ->delete('payment');

        $this->db->trans_complete();

        return ($this->db->trans_status() !== FALSE);
    }

    function deletePaymentCategory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('payment_category');
    }

    function insertExpense($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('expense', $data2);
        return $this->db->insert_id();
    }

    function getExpense()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('expense');
        return $query->result();
    }

    function getExpenseWithoutSearch($order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('expense');
        return $query->result();
    }

    function getExpenseBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->select('*')
            ->from('expense')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('datestring', $search)
                ->or_like('category', $search)
                ->or_like('pay_to', $search)
                ->or_like('payer_name', $search)
                ->or_like('note', $search)
                ->or_like('bill_date', $search)
            ->group_end();
        $query = $this->db->get();
        return $query->result();
    }

    function getExpenseByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('expense');
        return $query->result();
    }

    function getExpenseByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $this->db->select('*')
            ->from('expense')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('datestring', $search)
                ->or_like('category', $search)
                ->or_like('pay_to', $search)
                ->or_like('payer_name', $search)
                ->or_like('note', $search)
                ->or_like('bill_date', $search)
            ->group_end();
        $query = $this->db->get();
        return $query->result();
    }

    function getExpenseById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('expense');
        return $query->row();
    }

    function updateExpense($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('expense', $data);
    }

    function insertExpenseCategory($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('expense_category', $data2);
        return $this->db->insert_id();
    }

    function getExpenseCategory()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('expense_category');
        return $query->result();
    }

    function getExpenseCategoryById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('expense_category');
        return $query->row();
    }

    function updateExpenseCategory($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('expense_category', $data);
    }

    function deleteExpense($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('expense');
    }

    function deleteExpenseCategory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('expense_category');
    }

    function getDiscountType()
    {
        $hospital_id = $this->session->userdata('hospital_id');
        if (empty($hospital_id) && !empty($this->hospital_id)) {
            $hospital_id = $this->hospital_id;
        }
        if (empty($hospital_id)) {
            $query = $this->db->limit(1)->get('settings');
            $row = $query->row();
            return (!empty($row) && !empty($row->discount)) ? $row->discount : 'flat';
        }
        $this->db->where('hospital_id', $hospital_id);
        $query = $this->db->get('settings');
        $row = $query->row();
        return (!empty($row) && !empty($row->discount)) ? $row->discount : 'flat';
    }

    function getPaymentByDoctor($doctor)
    {
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $query = $this->db->get();
        return $query->result();
    }

    function getDepositAmountByPaymentId($payment_id)
    {
        $this->db->select('*');
        $this->db->from('patient_deposit');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('payment_id', $payment_id);
        $query = $this->db->get();
        $total = array();
        $deposited_total = array();
        $total = $query->result();

        foreach ($total as $deposit) {
            $deposited_total[] = $deposit->deposited_amount;
        }

        if (!empty($deposited_total)) {
            $deposited_total = array_sum($deposited_total);
        } else {
            $deposited_total = 0;
        }

        return $deposited_total;
    }

    function getDuePaymentCount($search = null, $start_date_stamp = null, $end_date_stamp = null)
    {
        $hospital_id = $this->session->userdata('hospital_id');
        $this->db->from('payment');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where("(status != 'cancelled' OR status IS NULL)");
        $this->db->where("(CAST(gross_total AS DECIMAL(10,2)) - COALESCE((SELECT SUM(CAST(deposited_amount AS DECIMAL(10,2))) FROM patient_deposit WHERE patient_deposit.payment_id = payment.id), 0.00)) > 0.009", NULL, FALSE);

        if (!empty($start_date_stamp) && !empty($end_date_stamp)) {
            $this->db->where('date >=', $start_date_stamp);
            $this->db->where('date <=', $end_date_stamp);
        }

        if (!empty($search)) {
            $this->db->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        }

        return $this->db->count_all_results();
    }

    function getDuePaymentList($limit, $start, $search = null, $order = 'id', $dir = 'desc', $start_date_stamp = null, $end_date_stamp = null)
    {
        $hospital_id = $this->session->userdata('hospital_id');
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where("(status != 'cancelled' OR status IS NULL)");
        $this->db->where("(CAST(gross_total AS DECIMAL(10,2)) - COALESCE((SELECT SUM(CAST(deposited_amount AS DECIMAL(10,2))) FROM patient_deposit WHERE patient_deposit.payment_id = payment.id), 0.00)) > 0.009", NULL, FALSE);

        if (!empty($start_date_stamp) && !empty($end_date_stamp)) {
            $this->db->where('date >=', $start_date_stamp);
            $this->db->where('date <=', $end_date_stamp);
        }

        if (!empty($search)) {
            $this->db->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }

        if ($limit != -1) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    function getPaymentByDate($date_from, $date_to)
    {
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getPaymentByDoctorDate($doctor, $date_from, $date_to)
    {
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getDepositByPaymentId($payment_id)
    {
        $this->db->select('*');
        $this->db->from('patient_deposit');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('payment_id', $payment_id);
        $query = $this->db->get();
        $total = array();
        $deposited_total = array();
        $total = $query->result();
        return $total;
    }

    function getOtPaymentByDate($date_from, $date_to)
    {
        $this->db->select('*');
        $this->db->from('ot_payment');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getDepositsByDate($date_from, $date_to)
    {
        $this->db->select('*');
        $this->db->from('patient_deposit');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getExpenseByDate($date_from, $date_to)
    {
        $this->db->select('*');
        $this->db->from('expense');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function makeStatusPaid($id, $patient_id, $data, $data1)
    {
        $this->db->where('patient', $patient_id);
        $this->db->where('status', 'paid-last');
        $this->db->update('payment', $data);
        $this->db->where('id', $id);
        $this->db->update('payment', $data1);
    }

    function makePaidByPatientIdByStatus($id, $data, $data1)
    {
        $this->db->where('patient', $id);
        $this->db->where('status', 'paid-last');
        $this->db->update('payment', $data1);

        $this->db->where('patient', $id);
        $this->db->where('status', 'paid-last');
        $this->db->update('ot_payment', $data1);

        $this->db->where('patient', $id);
        $this->db->where('status', 'unpaid');
        $this->db->update('payment', $data);

        $this->db->where('patient', $id);
        $this->db->where('status', 'unpaid');
        $this->db->update('ot_payment', $data);
    }

    function makeOtStatusPaid($id)
    {
        $this->db->where('id', $id);
        $this->db->update('ot_payment', array('status' => 'paid'));
    }

    function lastPaidInvoice($id)
    {
        $this->db->where('patient', $id);
        $this->db->where('status', 'paid-last');
        $query = $this->db->get('payment');
        return $query->result();
    }

    function lastOtPaidInvoice($id)
    {
        $this->db->where('patient', $id);
        $this->db->where('status', 'paid-last');
        $query = $this->db->get('ot_payment');
        return $query->result();
    }

    function amountReceived($id, $data)
    {
        $this->db->where('id', $id);
        $query = $this->db->update('payment', $data);
    }

    function otAmountReceived($id, $data)
    {
        $this->db->where('id', $id);
        $query = $this->db->update('ot_payment', $data);
    }

    function getThisMonth()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $payments = $this->db->get('payment')->result();
        foreach ($payments as $payment) {
            if (date('m/y', $payment->date) == date('m/y', time())) {
                $this_month_payment[] = $payment->gross_total;
            }
        }
        if (!empty($this_month_payment)) {
            $this_month_payment = array_sum($this_month_payment);
        } else {
            $this_month_payment = 0;
        }

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $expenses = $this->db->get('expense')->result();
        foreach ($expenses as $expense) {
            if (date('m/y', $expense->date) == date('m/y', time())) {
                $this_month_expense[] = $expense->amount;
            }
        }

        if (!empty($this_month_expense)) {
            $this_month_expense = array_sum($this_month_expense);
        } else {
            $this_month_expense = 0;
        }

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $appointments = $this->db->get('appointment')->result();
        foreach ($appointments as $appointment) {
            if (date('m/y', $appointment->date) == date('m/y', time())) {
                $this_month_appointment[] = 1;
            }
        }

        if (!empty($this_month_appointment)) {
            $this_month_appointment = array_sum($this_month_appointment);
        } else {
            $this_month_appointment = 0;
        }

        $this_month_details = array($this_month_payment, $this_month_expense, $this_month_appointment);
        return $this_month_details;
    }

    function getPaymentByUserIdByDate($user, $date_from, $date_to)
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('user', $user);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getOtPaymentByUserIdByDate($user, $date_from, $date_to)
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('*');
        $this->db->from('ot_payment');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('user', $user);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getDepositByUserIdByDate($user, $date_from, $date_to)
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('*');
        $this->db->from('patient_deposit');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('user', $user);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getDueBalanceByPatientId($patient)
    {
        $query = $this->db->get_where('payment', array('patient' => $patient->id))->result();
        $deposits = $this->db->get_where('patient_deposit', array('patient' => $patient->id))->result();
        $balance = array();
        $deposit_balance = array();
        foreach ($query as $gross) {
            $balance[] = $gross->gross_total;
        }
        $balance = array_sum($balance);


        foreach ($deposits as $deposit) {
            $deposit_balance[] = $deposit->deposited_amount;
        }
        $deposit_balance = array_sum($deposit_balance);



        $bill_balance = $balance;

        return $due_balance = $bill_balance - $deposit_balance;
    }



    function getPaymentSummaryById($id)
    {
        $query = $this->db->get_where('payment', array('id' => $id))->result();
        $deposits = $this->db->get_where('patient_deposit', array('payment_id' => $id))->result();

        foreach ($query as $gross) {
            $balance[] = $gross->gross_total;
        }
        foreach ($deposits as $deposit) {
            $deposit_balance[] = $deposit->deposited_amount;
        }

        if (!empty($balance)) {
            $data['total'] = array_sum($balance);
        } else {
            $data['total'] = 0;
        }
        if (!empty($deposit_balance)) {
            $data['paid'] = array_sum($deposit_balance);
        } else {
            $data['paid'] = 0;
        }

        $data['due'] = $data['total'] - $data['paid'];

        return $data;
    }






    function getFirstRowPaymentById()
    {

        //  $this->load->database();
        $last = $this->db->order_by('id', "asc")
            ->limit(1)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('payment')
            ->row();
        return $last;
    }

    function getLastRowPaymentById()
    {

        // $this->load->database();
        $last = $this->db->order_by('id', "desc")
            ->limit(1)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('payment')
            ->row();
        return $last;
    }

    function getPreviousPaymentById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('payment');
        return $query->previous_row();
    }

    function getNextPaymentById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('payment');
        return $query->row();
    }
    function getPaymentCategoryByNameSearch($attr)
    {
        return $this->db->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('status', 'Active')
            ->like('category', $attr)
            ->get('payment_category')->result();
    }
    function getCategoryById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('category');
        return $query->row();
    }
    function deleteCategory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('category');
    }
    function insertCategory($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('category', $data2);
    }
    function updateCategory($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('category', $data);
    }
    function getCategory()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('category', 'asc');
        $query = $this->db->get('category');
        $res = $query->result();
        if (empty($res)) {
            $this->db->order_by('category', 'asc');
            $res = $this->db->get('category')->result();
        }
        return $res;
    }
    function getDepositByInvoiceId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('payment_id', $id);
        $query = $this->db->get('patient_deposit');
        return $query->result();
    }
    function insertDraftPayment($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('draft_payment', $data2);
    }

    function updateDraftPayment($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('draft_payment', $data);
    }
    function getDraftPaymentWitoutSearch($order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('draft_payment');
        return $query->result();
    }

    function getDraftPaymentBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->select('*')
            ->from('draft_payment')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }

    function getDraftPaymentByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('draft_payment');
        return $query->result();
    }
    function getDraftPaymentByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $this->db->select('*')
            ->from('draft_payment')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }
    function deleteDraftPayment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('draft_payment');
    }
    function getDraftPaymentById($id)
    {
        return $this->db->where('id', $id)->get('draft_payment')->row();
    }
    function getPaymentWitoutSearchByDate($order, $dir, $start_date_stamp, $end_date_stamp)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->where('date >=', $start_date_stamp)->where('date <=', $end_date_stamp)->get('payment');
        return $query->result();
    }

    function getPaymentBySearchByDate($search, $order, $dir, $start_date_stamp, $end_date_stamp)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->select('*')
            ->from('payment')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('date >=', $start_date_stamp)
            ->where('date <=', $end_date_stamp)
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }

    function getPaymentByLimitByDate($limit, $start, $order, $dir, $start_date_stamp, $end_date_stamp)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->where('date >=', $start_date_stamp)->where('date <=', $end_date_stamp)->get('payment');
        return $query->result();
    }
    function getPaymentByLimitBySearchByDate($limit, $start, $search, $order, $dir, $start_date_stamp, $end_date_stamp)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $this->db->select('*')
            ->from('payment')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('date >=', $start_date_stamp)
            ->where('date <=', $end_date_stamp)
            ->group_start()
                ->like('id', $search)
                ->or_like('amount', $search)
                ->or_like('gross_total', $search)
                ->or_like('patient_name', $search)
                ->or_like('patient_phone', $search)
                ->or_like('patient_address', $search)
                ->or_like('remarks', $search)
                ->or_like('doctor_name', $search)
                ->or_like('flat_discount', $search)
                ->or_like('date_string', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }
    function getPaymentCategoryWithoutSearch($order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'Active');
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('payment_category');
        return $query->result();
    }
    function getPaymentCategoryBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->select('*')
            ->from('payment_category')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('status', 'Active')
            ->group_start()
                ->like('id', $search)
                ->or_like('description', $search)
                ->or_like('type', $search)
                ->or_like('category', $search)
                ->or_like('payment_category_name', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }
    function getPaymentCategoryByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $this->db->select('*')
            ->from('payment_category')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('status', 'Active')
            ->group_start()
                ->like('id', $search)
                ->or_like('description', $search)
                ->or_like('type', $search)
                ->or_like('category', $search)
                ->or_like('payment_category_name', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }
    function getPaymentCategoryByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'Active');
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('payment_category');
        return $query->result();
    }
    function getPaymentCategoryWithoutSearchByCategory($order, $dir, $filter_category)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'Active');
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->where('payment_category', $filter_category)->get('payment_category');
        return $query->result();
    }
    function getPaymentCategoryBySearchByCategory($search, $order, $dir, $filter_category)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->select('*')
            ->from('payment_category')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('status', 'Active')
            ->where('payment_category', $filter_category)
            ->group_start()
                ->like('id', $search)
                ->or_like('description', $search)
                ->or_like('type', $search)
                ->or_like('category', $search)
                ->or_like('payment_category_name', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }
    function getPaymentCategoryByLimitBySearchByCategory($limit, $start, $search, $order, $dir, $filter_category)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $this->db->select('*')
            ->from('payment_category')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('status', 'Active')
            ->where('payment_category', $filter_category)
            ->group_start()
                ->like('id', $search)
                ->or_like('description', $search)
                ->or_like('type', $search)
                ->or_like('category', $search)
                ->or_like('payment_category_name', $search)
            ->group_end();
        $query = $this->db->get();

        return $query->result();
    }
    function getPaymentCategoryByLimitByCategory($limit, $start, $order, $dir, $filter_category)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'Active');
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->where('payment_category', $filter_category)->get('payment_category');
        return $query->result();
    }
    function getPaymentByAppointmentId($appointment_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('appointment_id', $appointment_id);
        $query = $this->db->get('payment');
        return $query->row();
    }
    function lastRowByHospitalPayment()
    {
        return $this->db->where('hospital_id', $this->session->userdata('hospital_id'))
            ->order_by('id', "desc")->limit(1)->get('payment')->row();
    }



    function getInsuranceDepositByDate($date_from, $date_to)
    {
        return $this->db->order_by('date', 'asc')
            ->where('deposit_type', 'Insurance')
            ->where('date >=', $date_from)
            ->where('date <=', $date_to)
            ->get('patient_deposit')->result();
    }


    function getInsuranceDepositByDateByCompany($date_from, $date_to, $company)
    {

        return $this->db->order_by('date', 'asc')
            ->where('date >=', $date_from)
            ->where('date <=', $date_to)
            ->where('deposit_type', 'Insurance')
            ->where('insurance_company', $company)
            ->get('patient_deposit')->result();
    }

    function getTestInfo($searchTerm)
    {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('status', 'Active');
            $this->db->group_start()
                ->like('category', $searchTerm)
                ->or_like('id', $searchTerm)
            ->group_end();
            $this->db->where('type', 'diagnostic');
            $fetched_records = $this->db->get('payment_category');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('status', 'Active');
            $this->db->where('type', 'diagnostic');
            $this->db->limit(10);
            $fetched_records = $this->db->get('payment_category');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['category']);
        }
        return $data;
    }
    function getTest()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'Active');
        $this->db->where('type', 'diagnostic');
        $query = $this->db->get('payment_category');
        return $query->result();
    }

    /**
     * LIFECARE HOSPITAL - Financial Performance & Profit/Loss Report Data Aggregator
     * Supports Daily, Weekly, Monthly, Yearly reporting with 100% database-driven reconciliation.
     */
    public function getFinancialPerformanceReportData($period_type = 'this_month', $selected_date = null, $date_from = null, $date_to = null, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        if (empty($hospital_id)) {
            $h_row = $this->db->select('id')->order_by('id', 'ASC')->limit(1)->get('hospital')->row();
            $hospital_id = $h_row ? $h_row->id : 1;
        }

        $this->db->reset_query();

        // 1. Determine Timestamps & Period Labels
        $today_str = date('Y-m-d');
        if (empty($selected_date)) {
            $selected_date = $today_str;
        }

        $norm_period = strtolower(trim($period_type));
        if ($norm_period === 'today' || $norm_period === 'daily') {
            $target_d = (!empty($selected_date) && $norm_period === 'daily') ? date('Y-m-d', strtotime($selected_date)) : $today_str;
            $start_time = strtotime($target_d . ' 00:00:00');
            $end_time = strtotime($target_d . ' 23:59:59');
            $prev_start = $start_time - 86400;
            $prev_end = $end_time - 86400;
            $period_label = 'Today (' . date('d M Y', $start_time) . ')';
            $prev_label = 'vs yesterday';
            $period_type = 'today';
        } elseif ($norm_period === 'yesterday') {
            $start_time = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
            $end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
            $prev_start = $start_time - 86400;
            $prev_end = $end_time - 86400;
            $period_label = 'Yesterday (' . date('d M Y', $start_time) . ')';
            $prev_label = 'vs day before';
            $period_type = 'yesterday';
        } elseif ($norm_period === 'this_week' || $norm_period === 'weekly') {
            $ts = strtotime($selected_date);
            $w_start = strtotime('monday this week', $ts);
            if ($w_start > $ts) {
                $w_start = strtotime('-7 days', $w_start);
            }
            $start_time = strtotime(date('Y-m-d 00:00:00', $w_start));
            $end_time = $start_time + (7 * 86400) - 1;
            $prev_start = $start_time - (7 * 86400);
            $prev_end = $start_time - 1;
            $period_label = 'This Week (' . date('d M', $start_time) . ' - ' . date('d M Y', $end_time) . ')';
            $prev_label = 'vs previous week';
            $period_type = 'this_week';
        } elseif ($norm_period === 'last_month') {
            $first_day_last_month = date('Y-m-01', strtotime('first day of last month'));
            $ts = strtotime($first_day_last_month);
            $start_time = strtotime(date('Y-m-01 00:00:00', $ts));
            $end_time = strtotime(date('Y-m-t 23:59:59', $ts));
            $prev_start = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month', $start_time)));
            $prev_end = strtotime(date('Y-m-t 23:59:59', $prev_start));
            $period_label = 'Last Month (' . date('F Y', $start_time) . ')';
            $prev_label = 'vs month prior';
            $period_type = 'last_month';
        } elseif ($norm_period === 'this_year' || $norm_period === 'yearly') {
            $year = date('Y', strtotime($selected_date));
            $start_time = strtotime($year . '-01-01 00:00:00');
            $end_time = strtotime($year . '-12-31 23:59:59');
            $prev_start = strtotime(($year - 1) . '-01-01 00:00:00');
            $prev_end = strtotime(($year - 1) . '-12-31 23:59:59');
            $period_label = 'Year ' . $year;
            $prev_label = 'vs previous year';
            $period_type = 'this_year';
        } elseif ($norm_period === 'custom' || (!empty($date_from) && !empty($date_to))) {
            $d_from = preg_replace('/(\\d{2})\\/(\\d{2})\\/(\\d{4})/', '$3-$2-$1', $date_from);
            $d_to = preg_replace('/(\\d{2})\\/(\\d{2})\\/(\\d{4})/', '$3-$2-$1', $date_to);
            $start_time = strtotime(date('Y-m-d 00:00:00', strtotime($d_from)));
            $end_time = strtotime(date('Y-m-d 23:59:59', strtotime($d_to)));
            if ($end_time < $start_time) {
                $tmp = $start_time;
                $start_time = $end_time;
                $end_time = $tmp + 86399;
            }
            $span = $end_time - $start_time + 1;
            $prev_start = $start_time - $span;
            $prev_end = $start_time - 1;
            $period_label = date('d M Y', $start_time) . ' - ' . date('d M Y', $end_time);
            $days = max(1, round($span / 86400));
            $prev_label = 'vs previous ' . $days . ' days';
            $period_type = 'custom';
        } else {
            // Default: this_month / monthly
            $ts = strtotime($selected_date);
            $start_time = strtotime(date('Y-m-01 00:00:00', $ts));
            $end_time = strtotime(date('Y-m-t 23:59:59', $ts));
            $prev_start = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month', $start_time)));
            $prev_end = strtotime(date('Y-m-t 23:59:59', $prev_start));
            $period_label = date('F Y', $start_time);
            $prev_label = 'vs previous month';
            $period_type = 'this_month';
        }

        // Run Aggregation Engine for Target Period
        $current_data = $this->_calculateFinancialPeriod($start_time, $end_time, $period_type, $hospital_id);
        
        // Run Aggregation Engine for Previous Period (for comparative trend badges)
        $prev_data = $this->_calculateFinancialPeriod($prev_start, $prev_end, $period_type, $hospital_id);

        // Metadata
        $current_data['summary']['period_type'] = $period_type;
        $current_data['summary']['selected_date'] = $selected_date;
        $current_data['summary']['period_label'] = $period_label;
        $current_data['summary']['start_time'] = $start_time;
        $current_data['summary']['end_time'] = $end_time;
        $current_data['summary']['date_from'] = date('Y-m-d', $start_time);
        $current_data['summary']['date_to'] = date('Y-m-d', $end_time);
        $current_data['summary']['date_from_formatted'] = date('d/m/Y', $start_time);
        $current_data['summary']['date_to_formatted'] = date('d/m/Y', $end_time);
        $current_data['summary']['prev_label'] = $prev_label;

        // Card trend badges (all 9 KPIs)
        $cards = [
            'gross_billing',
            'total_discount',
            'net_billing',
            'total_collection',
            'total_due',
            'total_expenses',
            'doctor_commission',
            'referral_commission',
            'net_profit',
            'total_invoices',
            'gross_profit',
            'net_margin'
        ];
        $trends = [];
        foreach ($cards as $c) {
            $curr_val = isset($current_data['summary'][$c]) ? floatval($current_data['summary'][$c]) : 0.0;
            $p_val = isset($prev_data['summary'][$c]) ? floatval($prev_data['summary'][$c]) : 0.0;
            if ($p_val > 0) {
                $pct = round((($curr_val - $p_val) / $p_val) * 100, 1);
                $dir = ($pct >= 0) ? 'up' : 'down';
                $trends[$c] = ['dir' => $dir, 'text' => ($pct >= 0 ? '+' : '') . $pct . '%', 'val' => $pct];
            } elseif ($curr_val > 0) {
                $trends[$c] = ['dir' => 'up', 'text' => '+100%', 'val' => 100];
            } else {
                $trends[$c] = ['dir' => 'neutral', 'text' => '0%', 'val' => 0];
            }
        }
        $current_data['summary']['trends'] = $trends;

        return $current_data;
    }

    /**
     * Internal period calculator engine: provides exact reconciliation and 100% database accuracy
     */
    protected function _calculateFinancialPeriod($start_time, $end_time, $period_type, $hospital_id)
    {
        $this->db->reset_query();

        // 1. Determine slot granularity: hourly, daily, or monthly
        $span_seconds = $end_time - $start_time + 1;
        $slot_type = 'daily';
        if (in_array($period_type, ['today', 'yesterday', 'daily']) || ($period_type === 'custom' && $span_seconds <= 86400)) {
            $slot_type = 'hourly';
        } elseif (in_array($period_type, ['this_year', 'yearly']) || ($period_type === 'custom' && $span_seconds > 45 * 86400)) {
            $slot_type = 'monthly';
        }

        // Initialize Breakdown slots
        $breakdown = [];
        if ($slot_type === 'hourly') {
            for ($h = 0; $h < 24; $h++) {
                $slot_key = sprintf('%02d:00', $h);
                $breakdown[$slot_key] = [
                    'key' => $slot_key,
                    'label' => sprintf('%02d:00', $h),
                    'invoices' => 0, 'gross_billing' => 0.00, 'discount' => 0.00, 'net_billing' => 0.00,
                    'collection' => 0.00, 'due' => 0.00, 'doctor_comm' => 0.00, 'referral_comm' => 0.00,
                    'expense' => 0.00, 'net_profit' => 0.00
                ];
            }
        } elseif ($slot_type === 'monthly') {
            $m_cur = strtotime(date('Y-m-01', $start_time));
            while ($m_cur <= $end_time) {
                $slot_key = date('Y-m', $m_cur);
                $breakdown[$slot_key] = [
                    'key' => $slot_key,
                    'label' => date('M Y', $m_cur),
                    'invoices' => 0, 'gross_billing' => 0.00, 'discount' => 0.00, 'net_billing' => 0.00,
                    'collection' => 0.00, 'due' => 0.00, 'doctor_comm' => 0.00, 'referral_comm' => 0.00,
                    'expense' => 0.00, 'net_profit' => 0.00
                ];
                $m_cur = strtotime('+1 month', $m_cur);
            }
        } else {
            // Daily slots
            $cur = $start_time;
            while ($cur <= $end_time) {
                $slot_key = date('Y-m-d', $cur);
                $breakdown[$slot_key] = [
                    'key' => $slot_key,
                    'label' => ($period_type === 'this_week' || $period_type === 'weekly') ? date('D, d M', $cur) : date('d M', $cur),
                    'invoices' => 0, 'gross_billing' => 0.00, 'discount' => 0.00, 'net_billing' => 0.00,
                    'collection' => 0.00, 'due' => 0.00, 'doctor_comm' => 0.00, 'referral_comm' => 0.00,
                    'expense' => 0.00, 'net_profit' => 0.00
                ];
                $cur += 86400;
            }
        }

        // Helper lambda to get slot key for a given timestamp
        $get_slot_key = function($ts) use ($slot_type) {
            if ($slot_type === 'hourly') {
                return sprintf('%02d:00', date('G', $ts));
            } elseif ($slot_type === 'monthly') {
                return date('Y-m', $ts);
            }
            return date('Y-m-d', $ts);
        };

        // 2. Fetch Invoices / Payments for target period
        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $payments = $this->db->get()->result();

        // Bulk preload payment items (O(1) bulk fetch)
        $payment_ids = array();
        foreach ($payments as $p) {
            $payment_ids[] = $p->id;
        }

        $items_by_payment = array();
        if (!empty($payment_ids)) {
            $this->db->reset_query();
            $this->db->select('*');
            $this->db->from('payment_items');
            $this->db->where_in('payment_id', $payment_ids);
            $raw_items = $this->db->get()->result();
            foreach ($raw_items as $item_row) {
                $items_by_payment[$item_row->payment_id][] = $item_row;
            }
        }

        // Preload categories in bulk for fallback legacy invoices
        $all_categories_cached = array();
        $this->db->reset_query();
        $cat_list = $this->db->get_where('payment_category', array('hospital_id' => $hospital_id))->result();
        foreach ($cat_list as $cat) {
            $all_categories_cached[$cat->id] = trim($cat->category);
        }

        // Preload all Referrer Wallets for balance lookup
        $this->db->reset_query();
        $wallets = $this->db->get_where('referral_wallet', array('hospital_id' => $hospital_id))->result();
        $wallet_by_ref = [];
        $total_available_wallet = 0.00;
        $total_withdrawn_wallet = 0.00;
        foreach ($wallets as $w) {
            $wallet_by_ref[$w->referrer_id] = floatval($w->available_balance);
            $total_available_wallet += floatval($w->available_balance);
            $total_withdrawn_wallet += floatval($w->total_withdrawn);
        }

        // Pre-fetch Referral Commissions for the period
        $this->db->reset_query();
        $this->db->select('rcl.*, r.name as referrer_name');
        $this->db->from('referral_commission_ledger rcl');
        $this->db->join('referrer r', 'r.id = rcl.referrer_id', 'left');
        $this->db->where('rcl.hospital_id', $hospital_id);
        $this->db->where('rcl.created_date >=', $start_time);
        $this->db->where('rcl.created_date <=', $end_time);
        $this->db->where_not_in('rcl.status', array('Cancelled'));
        $ref_ledgers = $this->db->get()->result();

        $total_ref_comm = 0.00;
        $earned_ref_comm = 0.00;
        $pending_ref_comm = 0.00;
        $referrer_breakdown = [];
        $ref_comm_by_item = [];
        $ref_comm_by_invoice = [];

        foreach ($ref_ledgers as $rl) {
            if ($rl->status === 'Reversed') continue;
            $comm = floatval($rl->commission_amount);
            $earned = floatval($rl->earned_amount);
            $slot_k = $get_slot_key($rl->created_date);
            if (isset($breakdown[$slot_k])) {
                $breakdown[$slot_k]['referral_comm'] += $comm;
            }
            $total_ref_comm += $comm;
            $earned_ref_comm += $earned;
            $pending_ref_comm += max(0, $comm - $earned);

            if (!empty($rl->invoice_item_id)) {
                if (!isset($ref_comm_by_item[$rl->invoice_item_id])) {
                    $ref_comm_by_item[$rl->invoice_item_id] = 0.00;
                }
                $ref_comm_by_item[$rl->invoice_item_id] += $comm;
            }
            if (!empty($rl->invoice_id)) {
                if (!isset($ref_comm_by_invoice[$rl->invoice_id])) {
                    $ref_comm_by_invoice[$rl->invoice_id] = 0.00;
                }
                $ref_comm_by_invoice[$rl->invoice_id] += $comm;
            }

            $ref_id = $rl->referrer_id;
            $ref_name = !empty($rl->referrer_name) ? $rl->referrer_name : 'Referrer #' . $ref_id;
            if (!isset($referrer_breakdown[$ref_id])) {
                $referrer_breakdown[$ref_id] = [
                    'id' => $ref_id,
                    'name' => $ref_name,
                    'patients' => [],
                    'invoices' => [],
                    'billing' => 0.00,
                    'commission' => 0.00,
                    'earned' => 0.00,
                    'pending' => 0.00,
                    'wallet_balance' => isset($wallet_by_ref[$ref_id]) ? $wallet_by_ref[$ref_id] : 0.00
                ];
            }
            if (!empty($rl->patient_id)) {
                $referrer_breakdown[$ref_id]['patients'][$rl->patient_id] = true;
            }
            if (!empty($rl->invoice_id)) {
                $referrer_breakdown[$ref_id]['invoices'][$rl->invoice_id] = true;
            }
            $referrer_breakdown[$ref_id]['billing'] += floatval($rl->item_price);
            $referrer_breakdown[$ref_id]['commission'] += $comm;
            $referrer_breakdown[$ref_id]['earned'] += $earned;
            $referrer_breakdown[$ref_id]['pending'] += max(0, $comm - $earned);
        }

        $cancelled_invoices = 0;
        $refunded_amount = 0.00;
        $doctor_breakdown = [];
        $service_perf = [];
        $paid_doctor_comm = 0.00;
        $pending_doctor_comm = 0.00;
        $eligible_doctor_comm = 0.00;

        foreach ($payments as $p) {
            if (strtolower(trim($p->status)) === 'cancelled') {
                $cancelled_invoices++;
                $refunded_amount += floatval($p->amount_received);
                continue;
            }

            $slot_k = $get_slot_key($p->date);

            $disc = 0.00;
            if (isset($p->flat_discount) && is_numeric($p->flat_discount) && floatval($p->flat_discount) > 0) {
                $disc = floatval($p->flat_discount);
            } elseif (isset($p->discount) && is_numeric($p->discount) && floatval($p->discount) > 0) {
                $disc = floatval($p->discount);
            }

            $gross = floatval($p->amount);
            if ($gross <= 0) {
                $gross = floatval($p->gross_total) + $disc;
            }
            $net = $gross - $disc;
            $d_comm = floatval($p->doctor_amount);

            if (isset($breakdown[$slot_k])) {
                $breakdown[$slot_k]['invoices']++;
                $breakdown[$slot_k]['gross_billing'] += $gross;
                $breakdown[$slot_k]['discount'] += $disc;
                $breakdown[$slot_k]['net_billing'] += $net;
                $breakdown[$slot_k]['doctor_comm'] += $d_comm;
            }

            // Doctor Paid/Pending/Eligible logic
            $is_paid = (strtolower(trim($p->status)) === 'paid' || floatval($p->amount_received) >= floatval($p->gross_total));
            $hosp_share = max(0, floatval($p->gross_total) - $d_comm);
            $is_eligible = ($is_paid || (floatval($p->amount_received) >= $hosp_share && floatval($p->amount_received) > 0));

            if ($is_paid) {
                $paid_doctor_comm += $d_comm;
                $eligible_doctor_comm += $d_comm;
            } else {
                $pending_doctor_comm += $d_comm;
                if ($is_eligible) {
                    $eligible_doctor_comm += $d_comm;
                }
            }

            // Doctor Breakdown
            $doc_id = !empty($p->doctor) ? $p->doctor : 0;
            $doc_name = !empty($p->doctor_name) ? $p->doctor_name : 'Direct / Hospital';
            if (!isset($doctor_breakdown[$doc_id])) {
                $doctor_breakdown[$doc_id] = [
                    'id' => $doc_id,
                    'name' => $doc_name,
                    'invoices' => 0,
                    'billing' => 0.00,
                    'commission' => 0.00,
                    'eligible_commission' => 0.00,
                    'paid_commission' => 0.00,
                    'pending_commission' => 0.00
                ];
            }
            $doctor_breakdown[$doc_id]['invoices']++;
            $doctor_breakdown[$doc_id]['billing'] += $net;
            $doctor_breakdown[$doc_id]['commission'] += $d_comm;
            if ($is_paid) {
                $doctor_breakdown[$doc_id]['paid_commission'] += $d_comm;
                $doctor_breakdown[$doc_id]['eligible_commission'] += $d_comm;
            } else {
                $doctor_breakdown[$doc_id]['pending_commission'] += $d_comm;
                if ($is_eligible) {
                    $doctor_breakdown[$doc_id]['eligible_commission'] += $d_comm;
                }
            }

            // Service Performance: Preloaded in-memory items (Zero SQL in loop)
            $items = isset($items_by_payment[$p->id]) ? $items_by_payment[$p->id] : array();
            if (!empty($items)) {
                foreach ($items as $item) {
                    $item_name = trim($item->item_name);
                    $qty = floatval($item->quantity);
                    if ($qty <= 0) $qty = 1;
                    $subtotal = floatval($item->subtotal);
                    if ($subtotal <= 0) $subtotal = floatval($item->item_price) * $qty;
                    $line_disc = ($gross > 0) ? round(($subtotal / $gross) * $disc, 2) : 0.00;
                    $line_net = $subtotal - $line_disc;

                    $item_ref_comm = 0.00;
                    if (isset($ref_comm_by_item[$item->id])) {
                        $item_ref_comm = $ref_comm_by_item[$item->id];
                    } elseif (isset($item->referral_commission_amount) && floatval($item->referral_commission_amount) > 0) {
                        $item_ref_comm = floatval($item->referral_commission_amount);
                    }
                    $hospital_net = max(0.00, $line_net - $item_ref_comm);

                    if (!isset($service_perf[$item_name])) {
                        $service_perf[$item_name] = [
                            'name' => $item_name,
                            'qty' => 0,
                            'gross' => 0.00,
                            'discount' => 0.00,
                            'net' => 0.00,
                            'ref_comm' => 0.00,
                            'hospital_net' => 0.00
                        ];
                    }
                    $service_perf[$item_name]['qty'] += $qty;
                    $service_perf[$item_name]['gross'] += $subtotal;
                    $service_perf[$item_name]['discount'] += $line_disc;
                    $service_perf[$item_name]['net'] += $line_net;
                    $service_perf[$item_name]['ref_comm'] += $item_ref_comm;
                    $service_perf[$item_name]['hospital_net'] += $hospital_net;
                }
            } elseif (!empty($p->category_name)) {
                $cats = explode(',', $p->category_name);
                $p_ref_comm = isset($ref_comm_by_invoice[$p->id]) ? $ref_comm_by_invoice[$p->id] : floatval($p->referral_commission ?? 0);
                foreach ($cats as $cat_str) {
                    $parts = explode('*', $cat_str);
                    if (count($parts) >= 2) {
                        $cat_id = $parts[0];
                        $price = floatval($parts[1]);
                        $qty = isset($parts[3]) ? floatval($parts[3]) : 1;
                        if ($qty <= 0) $qty = 1;
                        $subtotal = $price * $qty;

                        $item_name = isset($all_categories_cached[$cat_id]) ? $all_categories_cached[$cat_id] : 'Service #' . $cat_id;

                        $line_disc = ($gross > 0) ? round(($subtotal / $gross) * $disc, 2) : 0.00;
                        $line_net = $subtotal - $line_disc;
                        $line_ref_comm = ($gross > 0) ? round(($subtotal / $gross) * $p_ref_comm, 2) : 0.00;
                        $hospital_net = max(0.00, $line_net - $line_ref_comm);

                        if (!isset($service_perf[$item_name])) {
                            $service_perf[$item_name] = [
                                'name' => $item_name,
                                'qty' => 0,
                                'gross' => 0.00,
                                'discount' => 0.00,
                                'net' => 0.00,
                                'ref_comm' => 0.00,
                                'hospital_net' => 0.00
                            ];
                        }
                        $service_perf[$item_name]['qty'] += $qty;
                        $service_perf[$item_name]['gross'] += $subtotal;
                        $service_perf[$item_name]['discount'] += $line_disc;
                        $service_perf[$item_name]['net'] += $line_net;
                        $service_perf[$item_name]['ref_comm'] += $line_ref_comm;
                        $service_perf[$item_name]['hospital_net'] += $hospital_net;
                    }
                }
            }
        }

        // 3. Fetch Deposits / Collections for target period (Actual Received Cash)
        $this->db->reset_query();
        $this->db->select('pd.*, p.date as invoice_date');
        $this->db->from('patient_deposit pd');
        $this->db->join('payment p', 'p.id = pd.payment_id', 'left');
        $this->db->where("(pd.hospital_id = '$hospital_id' OR p.hospital_id = '$hospital_id')", NULL, FALSE);
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);
        $deposits = $this->db->get()->result();

        $methods = [];
        $prev_due_collected = 0.00;
        $total_collection = 0.00;

        foreach ($deposits as $d) {
            $amt = floatval($d->deposited_amount);
            $total_collection += $amt;
            $slot_k = $get_slot_key($d->date);
            if (isset($breakdown[$slot_k])) {
                $breakdown[$slot_k]['collection'] += $amt;
            }

            $m = !empty($d->deposit_type) ? trim($d->deposit_type) : (!empty($d->gateway) ? trim($d->gateway) : 'Cash');
            if (empty($m)) $m = 'Cash';
            if (!isset($methods[$m])) $methods[$m] = 0.00;
            $methods[$m] += $amt;

            if (!empty($d->payment_id) && !empty($d->invoice_date)) {
                if (intval($d->invoice_date) < $start_time) {
                    $prev_due_collected += $amt;
                }
            }
        }

        // 4. Fetch Hospital Expenses for target period
        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('expense');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $expenses = $this->db->get()->result();

        $expense_breakdown = [];
        $total_expenses = 0.00;
        foreach ($expenses as $e) {
            $amt = floatval($e->amount);
            $total_expenses += $amt;
            $slot_k = $get_slot_key($e->date);
            if (isset($breakdown[$slot_k])) {
                $breakdown[$slot_k]['expense'] += $amt;
            }
            $cat = !empty($e->category) ? trim($e->category) : 'Other Expenses';
            if (!isset($expense_breakdown[$cat])) $expense_breakdown[$cat] = 0.00;
            $expense_breakdown[$cat] += $amt;
        }

        // 5. Final Summary Aggregation & Complete Reconciliation
        $summary = [
            'total_invoices' => 0,
            'gross_billing' => 0.00,
            'total_discount' => 0.00,
            'net_billing' => 0.00,
            'total_collection' => 0.00,
            'total_due' => 0.00,
            'doctor_commission' => 0.00,
            'referral_commission' => 0.00,
            'total_expenses' => 0.00,
            'gross_profit' => 0.00,
            'net_profit' => 0.00,
            'net_margin' => 0.00,
            'cancelled_invoices' => $cancelled_invoices,
            'refunded_amount' => $refunded_amount,
            'prev_due_collected' => $prev_due_collected,
            'paid_doctor_comm' => $paid_doctor_comm,
            'pending_doctor_comm' => $pending_doctor_comm,
            'eligible_doctor_comm' => $eligible_doctor_comm,
            'generated_ref_comm' => $total_ref_comm,
            'earned_ref_comm' => $earned_ref_comm,
            'pending_ref_comm' => $pending_ref_comm,
            'withdrawn_ref_comm' => $total_withdrawn_wallet,
            'available_wallet_balance' => $total_available_wallet
        ];

        // Format chart data arrays
        $chart_labels = [];
        $chart_billing = [];
        $chart_collection = [];
        $chart_due = [];
        $chart_expense = [];
        $chart_profit = [];

        foreach ($breakdown as $k => &$row) {
            $row['due'] = max(0.00, $row['net_billing'] - $row['collection']);
            $row['net_profit'] = $row['net_billing'] - $row['doctor_comm'] - $row['referral_comm'] - $row['expense'];

            $summary['total_invoices'] += $row['invoices'];
            $summary['gross_billing'] += $row['gross_billing'];
            $summary['total_discount'] += $row['discount'];
            $summary['net_billing'] += $row['net_billing'];
            $summary['total_collection'] += $row['collection'];
            $summary['doctor_commission'] += $row['doctor_comm'];
            $summary['referral_commission'] += $row['referral_comm'];
            $summary['total_expenses'] += $row['expense'];
            $summary['net_profit'] += $row['net_profit'];

            $chart_labels[] = $row['label'];
            $chart_billing[] = round($row['net_billing'], 2);
            $chart_collection[] = round($row['collection'], 2);
            $chart_due[] = round($row['due'], 2);
            $chart_expense[] = round($row['expense'], 2);
            $chart_profit[] = round($row['net_profit'], 2);
        }
        unset($row);

        // Strict accounting rule: Outstanding Due = Net Payable - Actual Collection
        $summary['total_due'] = max(0.00, $summary['net_billing'] - ($summary['total_collection'] - $summary['prev_due_collected']));

        // Gross Profit = Net Billing - Doctor Commission - Referral Commission
        $summary['gross_profit'] = $summary['net_billing'] - $summary['doctor_commission'] - $summary['referral_commission'];
        $summary['net_margin'] = ($summary['net_billing'] > 0) ? round(($summary['net_profit'] / $summary['net_billing']) * 100, 2) : 0.00;

        // Service Performance sorted by quantity desc (frequency), then gross desc
        uasort($service_perf, function ($a, $b) {
            if ($b['qty'] == $a['qty']) {
                return $b['gross'] <=> $a['gross'];
            }
            return $b['qty'] <=> $a['qty'];
        });

        // Payment Method Collection List
        $method_list = [];
        $coll_total = $summary['total_collection'];
        $other_total = 0.00;

        foreach ($methods as $m_name => $m_amt) {
            $m_clean = (strcasecmp($m_name, 'bkash') === 0) ? 'Bkash' : $m_name;
            if (in_array($m_clean, ['Cash', 'Card', 'Bkash', 'Nagad', 'Rocket', 'Bank'])) {
                if (!isset($method_list[$m_clean])) {
                    $method_list[$m_clean] = [
                        'name' => $m_clean,
                        'amount' => 0.00,
                        'percentage' => 0.0
                    ];
                }
                $method_list[$m_clean]['amount'] += $m_amt;
                $method_list[$m_clean]['percentage'] = ($coll_total > 0) ? round(($method_list[$m_clean]['amount'] / $coll_total) * 100, 1) : 0.0;
            } else {
                $other_total += $m_amt;
            }
        }
        if ($other_total > 0 || empty($method_list)) {
            $pct = ($coll_total > 0) ? round(($other_total / $coll_total) * 100, 1) : 0.0;
            $method_list['Others'] = [
                'name' => 'Others',
                'amount' => $other_total,
                'percentage' => $pct
            ];
        }

        // Method collection summary cards
        $summary['cash_collection'] = isset($methods['Cash']) ? $methods['Cash'] : 0.00;
        $summary['bkash_collection'] = (isset($methods['bKash']) ? $methods['bKash'] : 0.00) + (isset($methods['Bkash']) ? $methods['Bkash'] : 0.00);
        $summary['nagad_collection'] = isset($methods['Nagad']) ? $methods['Nagad'] : 0.00;
        $summary['rocket_collection'] = isset($methods['Rocket']) ? $methods['Rocket'] : 0.00;
        $summary['bank_collection'] = isset($methods['Bank']) ? $methods['Bank'] : 0.00;
        $summary['card_collection'] = isset($methods['Card']) ? $methods['Card'] : 0.00;
        $summary['other_collection'] = $other_total;

        // Ensure array lists for referrer and doctor breakdown
        $clean_referrers = [];
        foreach ($referrer_breakdown as $r_row) {
            $r_row['patient_count'] = count($r_row['patients']);
            $r_row['invoice_count'] = count($r_row['invoices']);
            unset($r_row['patients'], $r_row['invoices']);
            $clean_referrers[] = $r_row;
        }

        $clean_doctors = [];
        foreach ($doctor_breakdown as $d_row) {
            $clean_doctors[] = $d_row;
        }

        return [
            'summary' => $summary,
            'breakdown' => $breakdown,
            'doctor_breakdown' => $clean_doctors,
            'referrer_breakdown' => $clean_referrers,
            'expense_breakdown' => $expense_breakdown,
            'service_perf' => array_values($service_perf),
            'methods' => $method_list,
            'chart_data' => [
                'labels' => $chart_labels,
                'billing' => $chart_billing,
                'collection' => $chart_collection,
                'due' => $chart_due,
                'expense' => $chart_expense,
                'net_profit' => $chart_profit,
                'income_vs_expense' => [
                    'labels' => ['Gross Billing', 'Net Billing', 'Collection', 'Expenses', 'Net Profit'],
                    'data' => [
                        round($summary['gross_billing'], 2),
                        round($summary['net_billing'], 2),
                        round($summary['total_collection'], 2),
                        round($summary['total_expenses'], 2),
                        round($summary['net_profit'], 2),
                    ]
                ],
                'payment_methods' => [
                    'labels' => array_keys($method_list),
                    'data' => array_map(function($m) { return round($m['amount'], 2); }, array_values($method_list)),
                    'percentages' => array_map(function($m) { return $m['percentage']; }, array_values($method_list)),
                ],
                'expense_categories' => [
                    'labels' => array_keys($expense_breakdown),
                    'data' => array_map(function($a) { return round($a, 2); }, array_values($expense_breakdown)),
                ]
            ]
        ];
    }

    /**
     * =========================================================================
     * PHASE 2 — INCOME & COLLECTION REPORTING ENGINE
     * Lifecare Diagnostic Center
     * =========================================================================
     */

    /**
     * Resolve date range and period labels uniformly
     */
    public function resolveReportPeriod($period_type = 'this_month', $date_from = null, $date_to = null, $selected_date = null)
    {
        $norm = strtolower(trim($period_type ?: 'this_month'));
        $today_str = date('Y-m-d');
        if (empty($selected_date)) {
            $selected_date = $today_str;
        }

        if ($norm === 'today' || $norm === 'daily') {
            $target_d = (!empty($selected_date) && $norm === 'daily') ? date('Y-m-d', strtotime($selected_date)) : $today_str;
            $start_time = strtotime($target_d . ' 00:00:00');
            $end_time = strtotime($target_d . ' 23:59:59');
            $label = 'Today (' . date('d M Y', $start_time) . ')';
            $period_type = 'today';
        } elseif ($norm === 'yesterday') {
            $start_time = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
            $end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
            $label = 'Yesterday (' . date('d M Y', $start_time) . ')';
            $period_type = 'yesterday';
        } elseif ($norm === 'this_week' || $norm === 'weekly') {
            $ts = strtotime($selected_date);
            $w_start = strtotime('monday this week', $ts);
            if ($w_start > $ts) {
                $w_start = strtotime('-7 days', $w_start);
            }
            $start_time = strtotime(date('Y-m-d 00:00:00', $w_start));
            $end_time = $start_time + (7 * 86400) - 1;
            $label = 'This Week (' . date('d M', $start_time) . ' - ' . date('d M Y', $end_time) . ')';
            $period_type = 'this_week';
        } elseif ($norm === 'last_month') {
            $first_day_last_month = date('Y-m-01', strtotime('first day of last month'));
            $ts = strtotime($first_day_last_month);
            $start_time = strtotime(date('Y-m-01 00:00:00', $ts));
            $end_time = strtotime(date('Y-m-t 23:59:59', $ts));
            $label = 'Last Month (' . date('F Y', $start_time) . ')';
            $period_type = 'last_month';
        } elseif ($norm === 'this_year' || $norm === 'yearly') {
            $year = date('Y', strtotime($selected_date));
            $start_time = strtotime($year . '-01-01 00:00:00');
            $end_time = strtotime($year . '-12-31 23:59:59');
            $label = 'Year ' . $year;
            $period_type = 'this_year';
        } elseif ($norm === 'custom' || (!empty($date_from) && !empty($date_to))) {
            $d_from = preg_replace('/(\\d{2})\\/(\\d{2})\\/(\\d{4})/', '$3-$2-$1', $date_from);
            $d_to = preg_replace('/(\\d{2})\\/(\\d{2})\\/(\\d{4})/', '$3-$2-$1', $date_to);
            $start_time = strtotime(date('Y-m-d 00:00:00', strtotime($d_from)));
            $end_time = strtotime(date('Y-m-d 23:59:59', strtotime($d_to)));
            if ($end_time < $start_time) {
                $tmp = $start_time;
                $start_time = $end_time;
                $end_time = $tmp + 86399;
            }
            $label = date('d M Y', $start_time) . ' - ' . date('d M Y', $end_time);
            $period_type = 'custom';
        } else {
            // this_month (default)
            $ts = strtotime($selected_date);
            $start_time = strtotime(date('Y-m-01 00:00:00', $ts));
            $end_time = strtotime(date('Y-m-t 23:59:59', $ts));
            $label = date('F Y', $start_time);
            $period_type = 'this_month';
        }

        return [
            'start_time' => $start_time,
            'end_time' => $end_time,
            'period_label' => $label,
            'period_type' => $period_type,
            'date_from' => date('Y-m-d', $start_time),
            'date_to' => date('Y-m-d', $end_time),
            'date_from_formatted' => date('d/m/Y', $start_time),
            'date_to_formatted' => date('d/m/Y', $end_time)
        ];
    }

    /**
     * Resolve hospital ID safely with multi-tenant fallback
     */
    public function resolveHospitalId($hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        if (empty($hospital_id)) {
            $h_row = $this->db->select('id')->order_by('id', 'ASC')->limit(1)->get('hospital')->row();
            $hospital_id = $h_row ? $h_row->id : 1;
        }
        return $hospital_id;
    }

    /**
     * 1. DAILY COLLECTION REPORT
     * Real-time cash & digital collections from actual payment receipts
     */
    public function getDailyCollectionReport($start_time, $end_time, $cashier_id = null, $deposit_type = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("pd.*, p.id as invoice_no, p.patient_name as p_name, p.patient_phone as p_phone, 
                           pt.name as pt_name, pt.phone as pt_phone, pt.patient_id as pt_code, 
                           COALESCE(u.username, CONCAT('User #', pd.user)) as cashier_name, u.first_name, u.last_name");
        $this->db->from('patient_deposit pd');
        $this->db->join('payment p', 'p.id = pd.payment_id', 'left');
        $this->db->join('patient pt', 'pt.id = COALESCE(pd.patient, p.patient)', 'left');
        $this->db->join('users u', 'u.id = pd.user', 'left');
        $this->db->where("(pd.hospital_id = '$hospital_id' OR p.hospital_id = '$hospital_id')", NULL, FALSE);
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);

        if (!empty($cashier_id)) {
            $this->db->where('pd.user', $cashier_id);
        }
        if (!empty($deposit_type) && $deposit_type !== 'all') {
            if (strtolower($deposit_type) === 'online') {
                $this->db->where_in('pd.deposit_type', ['Bkash', 'bKash', 'Nagad', 'Rocket', 'Card', 'Online']);
            } else {
                $this->db->where('pd.deposit_type', $deposit_type);
            }
        }

        $this->db->order_by('pd.date', 'DESC');
        $this->db->order_by('pd.id', 'DESC');
        $rows = $this->db->get()->result();

        $total_transactions = count($rows);
        $cash_collection = 0.00;
        $online_collection = 0.00;
        $bank_collection = 0.00;
        $other_collection = 0.00;
        $total_collection = 0.00;

        foreach ($rows as $r) {
            $amt = floatval($r->deposited_amount);
            $total_collection += $amt;
            $dtype = !empty($r->deposit_type) ? trim($r->deposit_type) : 'Cash';

            if (strcasecmp($dtype, 'Cash') === 0) {
                $cash_collection += $amt;
            } elseif (in_array(strtolower($dtype), ['bkash', 'nagad', 'rocket', 'card', 'online'])) {
                $online_collection += $amt;
            } elseif (strcasecmp($dtype, 'Bank') === 0) {
                $bank_collection += $amt;
            } else {
                $other_collection += $amt;
            }
        }

        return [
            'records' => $rows,
            'summary' => [
                'total_transactions' => $total_transactions,
                'cash_collection' => $cash_collection,
                'online_collection' => $online_collection,
                'bank_collection' => $bank_collection,
                'other_collection' => $other_collection,
                'total_collection' => $total_collection
            ]
        ];
    }

    /**
     * 2. MONTHLY SALES REPORT
     * Comprehensive monthly breakdown: Invoices, Gross, Discounts, Net Payable, Collections, Due, Refunds
     */
    public function getMonthlySalesReport($year = null, $start_time = null, $end_time = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        if (empty($year)) {
            $year = date('Y');
        }

        // If specific start and end time provided, use them; else full year
        if (empty($start_time) || empty($end_time)) {
            $start_time = strtotime($year . '-01-01 00:00:00');
            $end_time = strtotime($year . '-12-31 23:59:59');
        }

        // Fetch all invoices in period
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $payments = $this->db->get()->result();

        // Fetch all collections in period
        $this->db->reset_query();
        $this->db->select('pd.*, p.date as invoice_date');
        $this->db->from('patient_deposit pd');
        $this->db->join('payment p', 'p.id = pd.payment_id', 'left');
        $this->db->where("(pd.hospital_id = '$hospital_id' OR p.hospital_id = '$hospital_id')", NULL, FALSE);
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);
        $deposits = $this->db->get()->result();

        // Group into monthly rows
        $months = [];
        $cur_m = strtotime(date('Y-m-01', $start_time));
        while ($cur_m <= $end_time) {
            $m_key = date('Y-m', $cur_m);
            $months[$m_key] = [
                'key' => $m_key,
                'month_name' => date('F Y', $cur_m),
                'invoice_count' => 0,
                'gross_billing' => 0.00,
                'total_discount' => 0.00,
                'net_payable' => 0.00,
                'total_collection' => 0.00,
                'outstanding_due' => 0.00,
                'refund_amount' => 0.00,
                'net_collection' => 0.00
            ];
            $cur_m = strtotime('+1 month', $cur_m);
        }

        $grand_invoices = 0;
        $grand_gross = 0.00;
        $grand_discount = 0.00;
        $grand_net_payable = 0.00;
        $grand_collection = 0.00;
        $grand_due = 0.00;
        $grand_refund = 0.00;

        foreach ($payments as $p) {
            $m_key = date('Y-m', $p->date);
            if (!isset($months[$m_key])) continue;

            $is_cancelled = (strtolower(trim($p->status)) === 'cancelled');
            if ($is_cancelled) {
                $ref_amt = floatval($p->amount_received);
                $months[$m_key]['refund_amount'] += $ref_amt;
                $grand_refund += $ref_amt;
                continue;
            }

            $disc = 0.00;
            if (isset($p->flat_discount) && is_numeric($p->flat_discount) && floatval($p->flat_discount) > 0) {
                $disc = floatval($p->flat_discount);
            } elseif (isset($p->discount) && is_numeric($p->discount) && floatval($p->discount) > 0) {
                $disc = floatval($p->discount);
            }

            $gross = floatval($p->amount);
            if ($gross <= 0) {
                $gross = floatval($p->gross_total) + $disc;
            }
            $net = $gross - $disc;

            $months[$m_key]['invoice_count']++;
            $months[$m_key]['gross_billing'] += $gross;
            $months[$m_key]['total_discount'] += $disc;
            $months[$m_key]['net_payable'] += $net;

            $grand_invoices++;
            $grand_gross += $gross;
            $grand_discount += $disc;
            $grand_net_payable += $net;
        }

        foreach ($deposits as $d) {
            $m_key = date('Y-m', $d->date);
            if (!isset($months[$m_key])) continue;
            $amt = floatval($d->deposited_amount);
            $months[$m_key]['total_collection'] += $amt;
            $grand_collection += $amt;
        }

        foreach ($months as $k => &$m_row) {
            $m_row['outstanding_due'] = max(0.00, $m_row['net_payable'] - $m_row['total_collection']);
            $m_row['net_collection'] = max(0.00, $m_row['total_collection'] - $m_row['refund_amount']);
            $grand_due += $m_row['outstanding_due'];
        }
        unset($m_row);

        $grand_net_collection = max(0.00, $grand_collection - $grand_refund);

        return [
            'records' => array_values($months),
            'summary' => [
                'invoice_count' => $grand_invoices,
                'gross_billing' => $grand_gross,
                'total_discount' => $grand_discount,
                'net_payable' => $grand_net_payable,
                'total_collection' => $grand_collection,
                'outstanding_due' => $grand_due,
                'refund_amount' => $grand_refund,
                'net_collection' => $grand_net_collection
            ]
        ];
    }

    /**
     * 3. PAYMENT METHOD REPORT
     * Breakdown by Cash, bKash, Nagad, Rocket, Bank, Card, Others with distribution chart data
     */
    public function getPaymentMethodReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("COALESCE(deposit_type, 'Cash') as raw_method, COUNT(*) as tx_count, SUM(deposited_amount) as total_amount");
        $this->db->from('patient_deposit');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $this->db->group_by("COALESCE(deposit_type, 'Cash')");
        $rows = $this->db->get()->result();

        $methods = [];
        $total_collection = 0.00;
        $total_transactions = 0;

        foreach ($rows as $r) {
            $name = trim($r->raw_method);
            if (empty($name)) $name = 'Cash';
            if (strcasecmp($name, 'bkash') === 0) $name = 'bKash';
            elseif (strcasecmp($name, 'nagad') === 0) $name = 'Nagad';
            elseif (strcasecmp($name, 'rocket') === 0) $name = 'Rocket';
            elseif (strcasecmp($name, 'bank') === 0) $name = 'Bank';
            elseif (strcasecmp($name, 'card') === 0) $name = 'Card';
            elseif (strcasecmp($name, 'cash') === 0) $name = 'Cash';

            $amt = floatval($r->total_amount);
            $cnt = intval($r->tx_count);

            if (!isset($methods[$name])) {
                $methods[$name] = ['method' => $name, 'tx_count' => 0, 'total_amount' => 0.00, 'percentage' => 0.0];
            }
            $methods[$name]['tx_count'] += $cnt;
            $methods[$name]['total_amount'] += $amt;
            $total_collection += $amt;
            $total_transactions += $cnt;
        }

        foreach ($methods as &$m) {
            $m['percentage'] = ($total_collection > 0) ? round(($m['total_amount'] / $total_collection) * 100, 1) : 0.0;
        }
        unset($m);

        usort($methods, function($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });

        return [
            'records' => $methods,
            'summary' => [
                'total_collection' => $total_collection,
                'total_transactions' => $total_transactions,
                'method_count' => count($methods)
            ],
            'chart_data' => [
                'labels' => array_column($methods, 'method'),
                'data' => array_column($methods, 'total_amount'),
                'percentages' => array_column($methods, 'percentage')
            ]
        ];
    }

    /**
     * 4. OUTSTANDING / DUE REPORT
     * Invoices with unpaid balances, including aging breakdown (0-15, 16-30, 31-60, 61+ days)
     */
    public function getOutstandingDueReport($start_time = null, $end_time = null, $aging_bucket = null, $patient_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("p.*, pt.name as pt_name, pt.phone as pt_phone, pt.patient_id as pt_code");
        $this->db->from('payment p');
        $this->db->join('patient pt', 'pt.id = p.patient', 'left');
        $this->db->where('p.hospital_id', $hospital_id);
        $this->db->where("LOWER(TRIM(COALESCE(p.status, ''))) !=", 'cancelled');
        $this->db->where('(p.gross_total - COALESCE(p.amount_received, 0)) >', 0.01);

        if (!empty($start_time) && !empty($end_time)) {
            $this->db->where('p.date >=', $start_time);
            $this->db->where('p.date <=', $end_time);
        }
        if (!empty($patient_id)) {
            $this->db->where('p.patient', $patient_id);
        }

        $this->db->order_by('p.date', 'DESC');
        $rows = $this->db->get()->result();

        $now = time();
        $filtered_records = [];
        $total_due = 0.00;
        $total_gross = 0.00;
        $total_paid = 0.00;
        $bucket_0_15 = 0.00;
        $bucket_16_30 = 0.00;
        $bucket_31_60 = 0.00;
        $bucket_61_plus = 0.00;

        foreach ($rows as $r) {
            $net = floatval($r->gross_total);
            $paid = floatval($r->amount_received);
            $due = max(0.00, round($net - $paid, 2));
            if ($due <= 0.00) continue;

            $age_days = max(0, floor(($now - $r->date) / 86400));
            if ($age_days <= 15) {
                $bucket = '0-15 Days';
                $bucket_0_15 += $due;
            } elseif ($age_days <= 30) {
                $bucket = '16-30 Days';
                $bucket_16_30 += $due;
            } elseif ($age_days <= 60) {
                $bucket = '31-60 Days';
                $bucket_31_60 += $due;
            } else {
                $bucket = '61+ Days';
                $bucket_61_plus += $due;
            }

            if (!empty($aging_bucket) && $aging_bucket !== 'all' && $aging_bucket !== $bucket) {
                continue;
            }

            $r->due_amount = $due;
            $r->age_days = $age_days;
            $r->aging_bucket = $bucket;

            $disc = 0.00;
            if (isset($r->flat_discount) && is_numeric($r->flat_discount) && floatval($r->flat_discount) > 0) {
                $disc = floatval($r->flat_discount);
            } elseif (isset($r->discount) && is_numeric($r->discount) && floatval($r->discount) > 0) {
                $disc = floatval($r->discount);
            }
            $r->calculated_discount = $disc;

            $filtered_records[] = $r;
            $total_due += $due;
            $total_gross += floatval($r->amount);
            $total_paid += $paid;
        }

        return [
            'records' => $filtered_records,
            'summary' => [
                'total_invoices' => count($filtered_records),
                'total_gross' => $total_gross,
                'total_paid' => $total_paid,
                'total_due' => $total_due,
                'bucket_0_15' => $bucket_0_15,
                'bucket_16_30' => $bucket_16_30,
                'bucket_31_60' => $bucket_31_60,
                'bucket_61_plus' => $bucket_61_plus
            ]
        ];
    }

    /**
     * 5. PARTIAL PAYMENT / INSTALLMENT REPORT
     * Complete installment ledger showing each deposit, prior due, payment, and remaining balance
     */
    public function getPartialPaymentReport($start_time, $end_time, $invoice_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("pd.*, p.gross_total as invoice_net, p.date as invoice_date, p.patient_name as p_name,
                           pt.name as pt_name, pt.patient_id as pt_code,
                           COALESCE(u.username, CONCAT('Cashier #', pd.user)) as cashier_name");
        $this->db->from('patient_deposit pd');
        $this->db->join('payment p', 'p.id = pd.payment_id', 'inner');
        $this->db->join('patient pt', 'pt.id = COALESCE(pd.patient, p.patient)', 'left');
        $this->db->join('users u', 'u.id = pd.user', 'left');
        $this->db->where("(pd.hospital_id = '$hospital_id' OR p.hospital_id = '$hospital_id')", NULL, FALSE);
        $this->db->where('pd.payment_id IS NOT NULL');
        $this->db->where('pd.payment_id !=', '');
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);

        if (!empty($invoice_id)) {
            $this->db->where('pd.payment_id', $invoice_id);
        }

        $this->db->order_by('pd.date', 'DESC');
        $this->db->order_by('pd.id', 'DESC');
        $rows = $this->db->get()->result();

        // Calculate installment sequences and remaining dues
        $payment_ids = array_unique(array_filter(array_column($rows, 'payment_id')));
        $all_deposits_by_inv = [];
        if (!empty($payment_ids)) {
            $this->db->reset_query();
            $this->db->select('*')->from('patient_deposit')->where_in('payment_id', $payment_ids)->order_by('date ASC, id ASC');
            $all_raw = $this->db->get()->result();
            foreach ($all_raw as $raw_d) {
                $all_deposits_by_inv[$raw_d->payment_id][] = $raw_d;
            }
        }

        $total_installment_coll = 0.00;
        foreach ($rows as &$r) {
            $inv_net = floatval($r->invoice_net);
            $inv_deps = isset($all_deposits_by_inv[$r->payment_id]) ? $all_deposits_by_inv[$r->payment_id] : [];

            $paid_before = 0.00;
            $inst_num = 1;
            foreach ($inv_deps as $idx => $d_step) {
                if ($d_step->id == $r->id) {
                    $inst_num = $idx + 1;
                    break;
                }
                $paid_before += floatval($d_step->deposited_amount);
            }

            $amt_rec = floatval($r->deposited_amount);
            $r->installment_no = $inst_num;
            $r->previous_due = max(0.00, round($inv_net - $paid_before, 2));
            $r->remaining_due = max(0.00, round($r->previous_due - $amt_rec, 2));
            $total_installment_coll += $amt_rec;
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_installments' => count($rows),
                'total_amount' => $total_installment_coll,
                'invoices_count' => count($payment_ids),
                'average_installment' => count($rows) > 0 ? round($total_installment_coll / count($rows), 2) : 0.00
            ]
        ];
    }

    /**
     * 6. ADVANCE PAYMENT REPORT
     * Patient advance deposits, adjustments, and remaining balances
     */
    public function getAdvancePaymentReport($start_time, $end_time, $patient_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        // Advances: Deposits without immediate invoice allocation OR tagged as advance
        $this->db->select("pd.*, pt.name as pt_name, pt.phone as pt_phone, pt.patient_id as pt_code,
                           COALESCE(u.username, CONCAT('Cashier #', pd.user)) as cashier_name");
        $this->db->from('patient_deposit pd');
        $this->db->join('patient pt', 'pt.id = pd.patient', 'left');
        $this->db->join('users u', 'u.id = pd.user', 'left');
        $this->db->where('pd.hospital_id', $hospital_id);
        $this->db->group_start();
        $this->db->where('pd.payment_id IS NULL');
        $this->db->or_where('pd.payment_id', '');
        $this->db->or_where('pd.payment_id', 0);
        $this->db->or_where('pd.payment_from', 'advance');
        $this->db->group_end();
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);

        if (!empty($patient_id)) {
            $this->db->where('pd.patient', $patient_id);
        }

        $this->db->order_by('pd.date', 'DESC');
        $rows = $this->db->get()->result();

        $total_advance = 0.00;
        $total_utilized = 0.00;
        $total_remaining = 0.00;

        foreach ($rows as &$r) {
            $dep_amt = floatval($r->deposited_amount);
            // Check if patient has utilized this against invoices
            $r->utilized_amount = 0.00;
            $r->remaining_advance = $dep_amt;

            $total_advance += $dep_amt;
            $total_remaining += $r->remaining_advance;
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_advances' => count($rows),
                'total_advance_amount' => $total_advance,
                'total_utilized' => $total_utilized,
                'total_remaining' => $total_remaining
            ]
        ];
    }

    /**
     * 7. REFUND REPORT
     * Invoices cancelled and amount refunded to patients
     */
    public function getRefundReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        // Query cancelled invoices where payments were previously taken
        $this->db->select("p.*, pt.name as pt_name, pt.phone as pt_phone, pt.patient_id as pt_code,
                           COALESCE(u.username, CONCAT('User #', p.user)) as processed_by_name");
        $this->db->from('payment p');
        $this->db->join('patient pt', 'pt.id = p.patient', 'left');
        $this->db->join('users u', 'u.id = p.user', 'left');
        $this->db->where('p.hospital_id', $hospital_id);
        $this->db->where("LOWER(TRIM(p.status))", 'cancelled');
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);
        $this->db->order_by('p.date', 'DESC');
        $rows = $this->db->get()->result();

        $total_refund_amount = 0.00;
        $refund_count = count($rows);
        $cash_refunds = 0.00;
        $digital_refunds = 0.00;

        foreach ($rows as &$r) {
            $amt = floatval($r->amount_received);
            $r->refund_amount = $amt;
            $r->refund_reason = !empty($r->remarks) ? $r->remarks : 'Invoice Cancellation / Test Unavailable';
            $r->refund_method = !empty($r->deposit_type) ? $r->deposit_type : 'Cash';

            $total_refund_amount += $amt;
            if (strcasecmp($r->refund_method, 'Cash') === 0) {
                $cash_refunds += $amt;
            } else {
                $digital_refunds += $amt;
            }
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'refund_count' => $refund_count,
                'total_refund_amount' => $total_refund_amount,
                'cash_refunds' => $cash_refunds,
                'digital_refunds' => $digital_refunds
            ]
        ];
    }

    /**
     * 8. DISCOUNT REPORT (Discount Audit Report)
     * Invoices with discounts granted, including absorption rule (Referrer vs Hospital)
     */
    public function getDiscountReport($start_time, $end_time, $user_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("p.*, pt.name as pt_name, pt.phone as pt_phone, pt.patient_id as pt_code,
                           r.name as ref_name, COALESCE(u.username, CONCAT('Cashier #', p.user)) as cashier_name");
        $this->db->from('payment p');
        $this->db->join('patient pt', 'pt.id = p.patient', 'left');
        $this->db->join('referrer r', 'r.id = p.referrer', 'left');
        $this->db->join('users u', 'u.id = p.user', 'left');
        $this->db->where('p.hospital_id', $hospital_id);
        $this->db->where("LOWER(TRIM(COALESCE(p.status, ''))) !=", 'cancelled');
        $this->db->where("(p.flat_discount > 0 OR p.discount > 0)", NULL, FALSE);
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);

        if (!empty($user_id)) {
            $this->db->where('p.user', $user_id);
        }

        $this->db->order_by('p.date', 'DESC');
        $rows = $this->db->get()->result();

        $total_invoices = count($rows);
        $total_gross = 0.00;
        $total_discount = 0.00;
        $referrer_absorbed = 0.00;
        $hospital_absorbed = 0.00;

        foreach ($rows as &$r) {
            $disc = 0.00;
            if (isset($r->flat_discount) && is_numeric($r->flat_discount) && floatval($r->flat_discount) > 0) {
                $disc = floatval($r->flat_discount);
            } elseif (isset($r->discount) && is_numeric($r->discount) && floatval($r->discount) > 0) {
                $disc = floatval($r->discount);
            }

            $gross = floatval($r->amount);
            if ($gross <= 0) {
                $gross = floatval($r->gross_total) + $disc;
            }

            $pct = ($gross > 0) ? round(($disc / $gross) * 100, 2) : 0.00;
            $r->discount_amount = $disc;
            $r->discount_percentage = $pct;
            $r->gross_amount = $gross;
            $r->net_payable = floatval($r->gross_total);

            // Mandatory Business Rule:
            // When referrer is assigned, discount is deducted from referrer's commission entitlement.
            // When no referrer, hospital absorbs the discount.
            if (!empty($r->referrer) || !empty($r->referrer_name)) {
                $ref_label = !empty($r->ref_name) ? $r->ref_name : (!empty($r->referrer_name) ? $r->referrer_name : 'Referrer #' . $r->referrer);
                $r->absorbed_by = 'Deducted from Referrer (' . $ref_label . ')';
                $r->absorbed_type = 'referrer';
                $referrer_absorbed += $disc;
            } else {
                $r->absorbed_by = 'Hospital Absorbed';
                $r->absorbed_type = 'hospital';
                $hospital_absorbed += $disc;
            }

            $r->discount_reason = !empty($r->remarks) ? $r->remarks : 'Patient Courtesy Concession';
            $r->authorized_by = $r->cashier_name;

            $total_gross += $gross;
            $total_discount += $disc;
        }
        unset($r);

        $avg_pct = ($total_gross > 0) ? round(($total_discount / $total_gross) * 100, 2) : 0.00;

        return [
            'records' => $rows,
            'summary' => [
                'total_discounted_invoices' => $total_invoices,
                'total_gross' => $total_gross,
                'total_discount' => $total_discount,
                'referrer_absorbed' => $referrer_absorbed,
                'hospital_absorbed' => $hospital_absorbed,
                'average_discount_pct' => $avg_pct
            ]
        ];
    }

    /**
     * 9. CASHIER-WISE COLLECTION REPORT
     * Summary of collections by each cashier/operator across payment methods
     */
    public function getCashierCollectionReport($start_time, $end_time, $user_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("pd.user as cashier_id, 
                           COALESCE(u.username, CONCAT('Cashier #', pd.user)) as cashier_name,
                           u.first_name, u.last_name, u.email,
                           COUNT(*) as tx_count,
                           SUM(pd.deposited_amount) as total_coll,
                           SUM(CASE WHEN LOWER(TRIM(COALESCE(pd.deposit_type, 'Cash'))) = 'cash' THEN pd.deposited_amount ELSE 0 END) as cash_coll,
                           SUM(CASE WHEN LOWER(TRIM(COALESCE(pd.deposit_type, ''))) IN ('bkash', 'nagad', 'rocket', 'card', 'online') THEN pd.deposited_amount ELSE 0 END) as online_coll,
                           SUM(CASE WHEN LOWER(TRIM(COALESCE(pd.deposit_type, ''))) = 'bank' THEN pd.deposited_amount ELSE 0 END) as bank_coll,
                           SUM(CASE WHEN LOWER(TRIM(COALESCE(pd.deposit_type, ''))) NOT IN ('cash', 'bkash', 'nagad', 'rocket', 'card', 'online', 'bank') THEN pd.deposited_amount ELSE 0 END) as other_coll");
        $this->db->from('patient_deposit pd');
        $this->db->join('users u', 'u.id = pd.user', 'left');
        $this->db->where('pd.hospital_id', $hospital_id);
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);

        if (!empty($user_id)) {
            $this->db->where('pd.user', $user_id);
        }

        $this->db->group_by('pd.user, u.username, u.first_name, u.last_name, u.email');
        $this->db->order_by('total_coll', 'DESC');
        $rows = $this->db->get()->result();

        $grand_tx = 0;
        $grand_cash = 0.00;
        $grand_online = 0.00;
        $grand_bank = 0.00;
        $grand_total = 0.00;

        foreach ($rows as $r) {
            $grand_tx += intval($r->tx_count);
            $grand_cash += floatval($r->cash_coll);
            $grand_online += floatval($r->online_coll);
            $grand_bank += floatval($r->bank_coll);
            $grand_total += floatval($r->total_coll);
        }

        return [
            'records' => $rows,
            'summary' => [
                'active_cashiers' => count($rows),
                'total_transactions' => $grand_tx,
                'cash_collection' => $grand_cash,
                'online_collection' => $grand_online,
                'bank_collection' => $bank_collection ?? $grand_bank,
                'total_collection' => $grand_total
            ]
        ];
    }

    /**
     * 10. COLLECTION SUMMARY REPORT
     * Executive reconciliation statement: New Billing, Discounts, Net Payable, Collections, Advance, Refunds, Net Cash
     */
    public function getCollectionSummaryReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        // Invoicing in period
        $this->db->select("COUNT(*) as total_invoices,
                           SUM(amount) as gross_billing,
                           SUM(COALESCE(flat_discount, discount)) as total_discount,
                           SUM(gross_total) as net_payable");
        $this->db->from('payment');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where("LOWER(TRIM(status)) !=", 'cancelled');
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $inv_row = $this->db->get()->row();

        $total_invoices = $inv_row ? intval($inv_row->total_invoices) : 0;
        $gross_billing = $inv_row ? floatval($inv_row->gross_billing) : 0.00;
        $total_discount = $inv_row ? floatval($inv_row->total_discount) : 0.00;
        $net_payable = $inv_row ? floatval($inv_row->net_payable) : 0.00;

        // Collections in period
        $this->db->reset_query();
        $this->db->select("pd.*, p.date as invoice_date");
        $this->db->from('patient_deposit pd');
        $this->db->join('payment p', 'p.id = pd.payment_id', 'left');
        $this->db->where("(pd.hospital_id = '$hospital_id' OR p.hospital_id = '$hospital_id')", NULL, FALSE);
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);
        $deposits = $this->db->get()->result();

        $current_inv_coll = 0.00;
        $prev_due_coll = 0.00;
        $advance_coll = 0.00;
        $method_coll = [];

        foreach ($deposits as $d) {
            $amt = floatval($d->deposited_amount);
            $m = !empty($d->deposit_type) ? trim($d->deposit_type) : 'Cash';
            if (strcasecmp($m, 'bkash') === 0) $m = 'bKash';
            elseif (strcasecmp($m, 'nagad') === 0) $m = 'Nagad';
            elseif (strcasecmp($m, 'rocket') === 0) $m = 'Rocket';
            elseif (strcasecmp($m, 'bank') === 0) $m = 'Bank';
            elseif (strcasecmp($m, 'card') === 0) $m = 'Card';
            elseif (strcasecmp($m, 'cash') === 0) $m = 'Cash';

            if (!isset($method_coll[$m])) $method_coll[$m] = 0.00;
            $method_coll[$m] += $amt;

            if (empty($d->payment_id) || $d->payment_from === 'advance') {
                $advance_coll += $amt;
            } elseif (!empty($d->invoice_date) && intval($d->invoice_date) < $start_time) {
                $prev_due_coll += $amt;
            } else {
                $current_inv_coll += $amt;
            }
        }

        $total_collections = $current_inv_coll + $prev_due_coll + $advance_coll;

        // Refunds in period
        $this->db->reset_query();
        $this->db->select_sum('amount_received', 'total_refunds');
        $this->db->from('payment');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where("LOWER(TRIM(status))", 'cancelled');
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $ref_row = $this->db->get()->row();
        $total_refunds = $ref_row ? floatval($ref_row->total_refunds) : 0.00;

        $net_cash_received = max(0.00, $total_collections - $total_refunds);
        $outstanding_due = max(0.00, $net_payable - $current_inv_coll);

        return [
            'summary' => [
                'total_invoices' => $total_invoices,
                'gross_billing' => $gross_billing,
                'total_discount' => $total_discount,
                'net_payable' => $net_payable,
                'current_invoice_collection' => $current_inv_coll,
                'prev_due_collection' => $prev_due_coll,
                'advance_collection' => $advance_coll,
                'total_collection' => $total_collections,
                'total_refunds' => $total_refunds,
                'net_cash_received' => $net_cash_received,
                'outstanding_due' => $outstanding_due,
                'methods' => $method_coll
            ]
        ];
    }


    /* =========================================================================
     * PHASE 3: EXPENSE & ACCOUNTS REPORTING METHODS
     * Lifecare Diagnostic Center - Accounting & Expense Intelligence
     * ========================================================================= */

    /**
     * Helper to detect payment method for an expense record
     */
    public function detectExpensePaymentMethod($row)
    {
        if (!empty($row->payment_type)) {
            $m = trim($row->payment_type);
            if (strcasecmp($m, 'cash') === 0) return 'Cash';
            if (strcasecmp($m, 'bkash') === 0) return 'bKash';
            if (strcasecmp($m, 'nagad') === 0) return 'Nagad';
            if (strcasecmp($m, 'rocket') === 0) return 'Rocket';
            if (strcasecmp($m, 'bank') === 0 || strcasecmp($m, 'cheque') === 0) return 'Bank';
            if (strcasecmp($m, 'card') === 0) return 'Card';
            return ucfirst($m);
        }

        // Detect from note keywords
        $note = !empty($row->note) ? strtolower($row->note) : '';
        if (strpos($note, 'bkash') !== false) return 'bKash';
        if (strpos($note, 'nagad') !== false) return 'Nagad';
        if (strpos($note, 'rocket') !== false) return 'Rocket';
        if (strpos($note, 'bank') !== false || strpos($note, 'cheque') !== false || strpos($note, 'check') !== false) return 'Bank';
        if (strpos($note, 'card') !== false || strpos($note, 'pos') !== false) return 'Card';

        return 'Cash'; // Default operating expense disbursement
    }

    /**
     * 1. DAILY EXPENSE REPORT
     */
    public function getDailyExpenseReport($start_time, $end_time, $category = null, $method = null, $user_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name,
                           u.first_name, u.last_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);

        if (!empty($category)) {
            $this->db->where('e.category', $category);
        }
        if (!empty($user_id)) {
            $this->db->where('e.user', $user_id);
        }

        $this->db->order_by('e.date', 'DESC');
        $this->db->order_by('e.id', 'DESC');
        $rows = $this->db->get()->result();

        $filtered = [];
        $total_amount = 0.00;
        $cash_expense = 0.00;
        $bank_expense = 0.00;
        $online_expense = 0.00;

        foreach ($rows as $r) {
            $m = $this->detectExpensePaymentMethod($r);
            $r->payment_method = $m;

            if (!empty($method) && $method !== 'all' && strcasecmp($m, $method) !== 0) {
                continue;
            }

            $amt = floatval($r->amount);
            $total_amount += $amt;

            if ($m === 'Cash') {
                $cash_expense += $amt;
            } elseif ($m === 'Bank') {
                $bank_expense += $amt;
            } else {
                $online_expense += $amt;
            }

            $filtered[] = $r;
        }

        return [
            'records' => $filtered,
            'summary' => [
                'total_expense' => $total_amount,
                'cash_expense' => $cash_expense,
                'bank_expense' => $bank_expense,
                'online_expense' => $online_expense,
                'total_transactions' => count($filtered)
            ]
        ];
    }

    /**
     * 2. MONTHLY EXPENSE REPORT
     */
    public function getMonthlyExpenseReport($year, $month = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        // 1. All monthly sums for the year
        $start_year = mktime(0, 0, 0, 1, 1, $year);
        $end_year = mktime(23, 59, 59, 12, 31, $year);

        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_year);
        $this->db->where('e.date <=', $end_year);
        $this->db->order_by('e.date', 'ASC');
        $rows = $this->db->get()->result();

        $monthly_data = [];
        for ($m = 1; $m <= 12; $m++) {
            $m_key = sprintf('%04d-%02d', $year, $m);
            $monthly_data[$m_key] = [
                'month_key' => $m_key,
                'month_name' => date('F Y', mktime(0, 0, 0, $m, 1, $year)),
                'month_num' => $m,
                'tx_count' => 0,
                'total_amount' => 0.00,
                'cash_amount' => 0.00,
                'bank_amount' => 0.00,
                'online_amount' => 0.00,
                'categories' => []
            ];
        }

        foreach ($rows as $r) {
            $m_key = date('Y-m', $r->date);
            if (!isset($monthly_data[$m_key])) continue;

            $amt = floatval($r->amount);
            $method = $this->detectExpensePaymentMethod($r);
            $r->payment_method = $method;

            $monthly_data[$m_key]['tx_count']++;
            $monthly_data[$m_key]['total_amount'] += $amt;

            if ($method === 'Cash') $monthly_data[$m_key]['cash_amount'] += $amt;
            elseif ($method === 'Bank') $monthly_data[$m_key]['bank_amount'] += $amt;
            else $monthly_data[$m_key]['online_amount'] += $amt;

            $cat = !empty($r->category) ? $r->category : 'Uncategorized';
            if (!isset($monthly_data[$m_key]['categories'][$cat])) {
                $monthly_data[$m_key]['categories'][$cat] = 0.00;
            }
            $monthly_data[$m_key]['categories'][$cat] += $amt;
        }

        // Calculate comparison with previous month
        $prev_total = 0.00;
        foreach ($monthly_data as $k => &$m_val) {
            $curr = $m_val['total_amount'];
            $diff = $curr - $prev_total;
            $pct = ($prev_total > 0) ? round(($diff / $prev_total) * 100, 1) : 0.0;
            $m_val['diff_amount'] = $diff;
            $m_val['diff_pct'] = $pct;
            $prev_total = $curr;
        }
        unset($m_val);

        // Filter for specific month if requested
        $selected_month_data = null;
        $category_breakdown = [];
        $vendor_breakdown = [];

        if (!empty($month)) {
            $sel_key = sprintf('%04d-%02d', $year, intval($month));
            if (isset($monthly_data[$sel_key])) {
                $selected_month_data = $monthly_data[$sel_key];
            }

            // Category & vendor details for that month
            $m_start = mktime(0, 0, 0, $month, 1, $year);
            $m_end = mktime(23, 59, 59, $month, date('t', $m_start), $year);

            $this->db->reset_query();
            $this->db->select("category, COUNT(*) as tx_count, SUM(amount) as total");
            $this->db->from('expense');
            $this->db->where('hospital_id', $hospital_id);
            $this->db->where('date >=', $m_start);
            $this->db->where('date <=', $m_end);
            $this->db->group_by('category');
            $this->db->order_by('total', 'DESC');
            $category_breakdown = $this->db->get()->result();

            $this->db->reset_query();
            $this->db->select("COALESCE(pay_to, 'General Payee') as vendor, COUNT(*) as tx_count, SUM(amount) as total");
            $this->db->from('expense');
            $this->db->where('hospital_id', $hospital_id);
            $this->db->where('date >=', $m_start);
            $this->db->where('date <=', $m_end);
            $this->db->group_by('pay_to');
            $this->db->order_by('total', 'DESC');
            $vendor_breakdown = $this->db->get()->result();
        }

        $grand_year_total = 0.00;
        $grand_year_tx = 0;
        foreach ($monthly_data as $m_row) {
            $grand_year_total += $m_row['total_amount'];
            $grand_year_tx += $m_row['tx_count'];
        }

        return [
            'monthly_totals' => $monthly_data,
            'selected_month' => $selected_month_data,
            'category_breakdown' => $category_breakdown,
            'vendor_breakdown' => $vendor_breakdown,
            'summary' => [
                'year' => $year,
                'total_expense' => $grand_year_total,
                'total_transactions' => $grand_year_tx,
                'average_monthly_expense' => round($grand_year_total / 12, 2)
            ]
        ];
    }

    /**
     * 3. EXPENSE LEDGER
     * Chronological general ledger of all expense debits
     */
    public function getExpenseLedgerReport($start_time, $end_time, $category = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);

        if (!empty($category) && $category !== 'all') {
            $this->db->where('e.category', $category);
        }

        $this->db->order_by('e.date', 'ASC');
        $this->db->order_by('e.id', 'ASC');
        $rows = $this->db->get()->result();

        $running_total = 0.00;
        foreach ($rows as &$r) {
            $amt = floatval($r->amount);
            $running_total += $amt;
            $r->running_balance = $running_total;
            $r->payment_method = $this->detectExpensePaymentMethod($r);
            $r->status = 'Paid & Settled';
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_debit' => $running_total,
                'total_transactions' => count($rows),
                'opening_balance' => 0.00,
                'closing_balance' => $running_total
            ]
        ];
    }

    /**
     * 4. EXPENSE CATEGORY REPORT
     * Analysis of expenses by category with percentage share and transaction volume
     */
    public function getExpenseCategoryReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("COALESCE(e.category, 'Uncategorized') as category_name,
                           COUNT(e.id) as tx_count,
                           SUM(e.amount) as total_amount,
                           MIN(e.date) as first_tx_date,
                           MAX(e.date) as last_tx_date");
        $this->db->from('expense e');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);
        $this->db->group_by("COALESCE(e.category, 'Uncategorized')");
        $this->db->order_by('total_amount', 'DESC');
        $rows = $this->db->get()->result();

        $grand_total = 0.00;
        $grand_tx = 0;
        foreach ($rows as $r) {
            $grand_total += floatval($r->total_amount);
            $grand_tx += intval($r->tx_count);
        }

        foreach ($rows as &$r) {
            $amt = floatval($r->total_amount);
            $r->share_pct = ($grand_total > 0) ? round(($amt / $grand_total) * 100, 1) : 0.0;
            $r->avg_cost = ($r->tx_count > 0) ? round($amt / $r->tx_count, 2) : 0.0;
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_categories' => count($rows),
                'total_expense' => $grand_total,
                'total_transactions' => $grand_tx,
                'top_category' => !empty($rows[0]) ? $rows[0]->category_name : 'None'
            ]
        ];
    }

    /**
     * 5. VENDOR / SUPPLIER PAYMENT REPORT
     */
    public function getVendorPaymentReport($start_time, $end_time, $vendor = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        // 1. Grouped by vendor
        $this->db->select("COALESCE(e.pay_to, 'General / Miscellaneous') as vendor_name,
                           COUNT(e.id) as voucher_count,
                           SUM(e.amount) as total_paid,
                           MIN(e.date) as first_date,
                           MAX(e.date) as last_date");
        $this->db->from('expense e');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);

        if (!empty($vendor) && $vendor !== 'all') {
            $this->db->where('e.pay_to', $vendor);
        }

        $this->db->group_by("COALESCE(e.pay_to, 'General / Miscellaneous')");
        $this->db->order_by('total_paid', 'DESC');
        $vendor_groups = $this->db->get()->result();

        // 2. Individual transaction details
        $this->db->reset_query();
        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);

        if (!empty($vendor) && $vendor !== 'all') {
            $this->db->where('e.pay_to', $vendor);
        }

        $this->db->order_by('e.date', 'DESC');
        $rows = $this->db->get()->result();

        $grand_paid = 0.00;
        foreach ($rows as &$r) {
            $r->payment_method = $this->detectExpensePaymentMethod($r);
            $r->status = 'Settled';
            $grand_paid += floatval($r->amount);
        }
        unset($r);

        return [
            'vendor_groups' => $vendor_groups,
            'records' => $rows,
            'summary' => [
                'total_vendors' => count($vendor_groups),
                'total_paid' => $grand_paid,
                'total_vouchers' => count($rows),
                'top_vendor' => !empty($vendor_groups[0]) ? $vendor_groups[0]->vendor_name : 'None'
            ]
        ];
    }

    /**
     * 6. EMPLOYEE SALARY REPORT
     * Integrates with payroll and salary modules
     */
    public function getEmployeeSalaryReport($year = null, $month = null, $staff_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        if (empty($year)) $year = date('Y');

        $this->db->select("p.*, u.username, u.email, u.phone");
        $this->db->from('payroll p');
        $this->db->join('users u', 'u.id = p.staff', 'left');
        $this->db->where('p.hospital_id', $hospital_id);

        if (!empty($year) && $year !== 'all') {
            $this->db->where('p.year', $year);
        }
        if (!empty($month) && $month !== 'all') {
            $this->db->where('p.month', $month);
        }
        if (!empty($staff_id)) {
            $this->db->where('p.staff', $staff_id);
        }

        $this->db->order_by('p.id', 'DESC');
        $rows = $this->db->get()->result();

        $total_gross = 0.00;
        $total_deductions = 0.00;
        $total_net = 0.00;
        $total_paid = 0.00;
        $total_pending = 0.00;

        foreach ($rows as &$r) {
            // Parse earning string: Gross Salary_10000
            $gross = 0.00;
            if (!empty($r->earning)) {
                $parts = explode('_', $r->earning);
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $gross = floatval($parts[1]);
                }
            }

            $deduct = 0.00;
            if (!empty($r->deduction)) {
                $parts = explode('_', $r->deduction);
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $deduct = floatval($parts[1]);
                }
            }

            $net = max(0.00, $gross - $deduct);
            $r->gross_salary = $gross;
            $r->deductions = $deduct;
            $r->net_salary = $net;
            $r->staff_name = !empty($r->username) ? $r->username : 'Staff #' . $r->staff;

            $total_gross += $gross;
            $total_deductions += $deduct;
            $total_net += $net;

            if (strcasecmp($r->status, 'Paid') === 0) {
                $total_paid += $net;
            } else {
                $total_pending += $net;
            }
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_records' => count($rows),
                'total_gross' => $total_gross,
                'total_deductions' => $total_deductions,
                'total_net' => $total_net,
                'total_paid' => $total_paid,
                'total_pending' => $total_pending
            ]
        ];
    }

    /**
     * 7. UTILITY BILL REPORT
     * Electricity, Water, Gas, Internet, Telephone & Other utility expenses
     */
    public function getUtilityBillReport($start_time, $end_time, $utility_cat = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);

        // Filter utility patterns
        $this->db->group_start();
        $this->db->like('LOWER(e.category)', 'util');
        $this->db->or_like('LOWER(e.category)', 'elect');
        $this->db->or_like('LOWER(e.category)', 'water');
        $this->db->or_like('LOWER(e.category)', 'gas');
        $this->db->or_like('LOWER(e.category)', 'net');
        $this->db->or_like('LOWER(e.category)', 'phone');
        $this->db->or_like('LOWER(e.note)', 'desco');
        $this->db->or_like('LOWER(e.note)', 'dpdc');
        $this->db->or_like('LOWER(e.note)', 'wasa');
        $this->db->or_like('LOWER(e.note)', 'electricity');
        $this->db->or_like('LOWER(e.note)', 'internet');
        $this->db->or_like('LOWER(e.note)', 'wifi');
        $this->db->group_end();

        $this->db->order_by('e.date', 'DESC');
        $rows = $this->db->get()->result();

        $filtered = [];
        $total_utility = 0.00;
        $electricity = 0.00;
        $internet = 0.00;
        $water_gas = 0.00;
        $other_utility = 0.00;

        foreach ($rows as $r) {
            $cat_str = strtolower($r->category . ' ' . $r->note);
            $type = 'Other Utilities';
            if (strpos($cat_str, 'elect') !== false || strpos($cat_str, 'desco') !== false || strpos($cat_str, 'dpdc') !== false) {
                $type = 'Electricity';
                $electricity += floatval($r->amount);
            } elseif (strpos($cat_str, 'net') !== false || strpos($cat_str, 'wifi') !== false || strpos($cat_str, 'isp') !== false) {
                $type = 'Internet';
                $internet += floatval($r->amount);
            } elseif (strpos($cat_str, 'water') !== false || strpos($cat_str, 'gas') !== false || strpos($cat_str, 'wasa') !== false) {
                $type = 'Water & Gas';
                $water_gas += floatval($r->amount);
            } else {
                $other_utility += floatval($r->amount);
            }

            if (!empty($utility_cat) && $utility_cat !== 'all' && strcasecmp($type, $utility_cat) !== 0) {
                continue;
            }

            $r->utility_type = $type;
            $r->payment_method = $this->detectExpensePaymentMethod($r);
            $r->status = 'Paid';
            $amt = floatval($r->amount);
            $total_utility += $amt;
            $filtered[] = $r;
        }

        return [
            'records' => $filtered,
            'summary' => [
                'total_utility' => $total_utility,
                'electricity' => $electricity,
                'internet' => $internet,
                'water_gas' => $water_gas,
                'other_utility' => $other_utility,
                'total_bills' => count($filtered)
            ]
        ];
    }

    /**
     * 8. ASSET PURCHASE REPORT
     * Capital equipment & hardware purchases vs repairs
     */
    public function getAssetPurchaseReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);

        $this->db->group_start();
        $this->db->like('LOWER(e.category)', 'asset');
        $this->db->or_like('LOWER(e.category)', 'equip');
        $this->db->or_like('LOWER(e.category)', 'machine');
        $this->db->or_like('LOWER(e.category)', 'repair');
        $this->db->or_like('LOWER(e.note)', 'asset');
        $this->db->or_like('LOWER(e.note)', 'machine');
        $this->db->or_like('LOWER(e.note)', 'equipment');
        $this->db->or_like('LOWER(e.note)', 'oxygen');
        $this->db->group_end();

        $this->db->order_by('e.date', 'DESC');
        $rows = $this->db->get()->result();

        $total_asset_spend = 0.00;
        $total_purchases = 0.00;
        $total_repairs = 0.00;

        foreach ($rows as &$r) {
            $cat_low = strtolower($r->category);
            $amt = floatval($r->amount);
            $total_asset_spend += $amt;

            if (strpos($cat_low, 'repair') !== false) {
                $r->asset_type = 'Asset Repair & Maintenance';
                $total_repairs += $amt;
            } else {
                $r->asset_type = 'Capital Asset Purchase';
                $total_purchases += $amt;
            }

            $r->payment_method = $this->detectExpensePaymentMethod($r);
            $r->status = 'Procured & Settled';
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_asset_spend' => $total_asset_spend,
                'total_purchases' => $total_purchases,
                'total_repairs' => $total_repairs,
                'item_count' => count($rows)
            ]
        ];
    }

    /**
     * 9. PETTY CASH REPORT
     * Minor day-to-day office vouchers (Hospitality, Refreshments, Stationery, Transport)
     */
    public function getPettyCashReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);

        // Small cash vouchers or petty categories
        $this->db->group_start();
        $this->db->like('LOWER(e.category)', 'hospit');
        $this->db->or_like('LOWER(e.category)', 'station');
        $this->db->or_like('LOWER(e.category)', 'petty');
        $this->db->or_like('LOWER(e.category)', 'tea');
        $this->db->or_like('LOWER(e.category)', 'convey');
        $this->db->or_where('e.amount <=', 1000.00);
        $this->db->group_end();

        $this->db->order_by('e.date', 'DESC');
        $rows = $this->db->get()->result();

        $total_petty = 0.00;
        $petty_cash_only = 0.00;

        foreach ($rows as &$r) {
            $m = $this->detectExpensePaymentMethod($r);
            $r->payment_method = $m;
            $amt = floatval($r->amount);
            $total_petty += $amt;
            if ($m === 'Cash') {
                $petty_cash_only += $amt;
            }
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_petty_cash' => $total_petty,
                'cash_disbursed' => $petty_cash_only,
                'voucher_count' => count($rows),
                'average_voucher' => count($rows) > 0 ? round($total_petty / count($rows), 2) : 0.00
            ]
        ];
    }

    /**
     * 10. CASH BOOK
     * Chronological interleaved ledger of physical Cash Receipts vs Cash Disbursements
     */
    public function getCashBookReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        // 1. Inflows: Cash deposits from patients
        $this->db->reset_query();
        $this->db->select("pd.id, pd.date, pd.deposited_amount as amount, pd.payment_id,
                           'Receipt' as tx_type, 'Patient Cash Deposit' as description,
                           COALESCE(u.username, CONCAT('Cashier #', pd.user)) as recorded_by");
        $this->db->from('patient_deposit pd');
        $this->db->join('users u', 'u.id = pd.user', 'left');
        $this->db->where('pd.hospital_id', $hospital_id);
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);
        $this->db->where("LOWER(TRIM(COALESCE(pd.deposit_type, 'Cash')))", 'cash');
        $inflow_rows = $this->db->get()->result();

        // 2. Outflows A: Cash Expenses
        $this->db->reset_query();
        $this->db->select("e.id, e.date, e.amount, e.pay_to, e.category, e.note,
                           'Disbursement' as tx_type,
                           CONCAT('Expense: ', COALESCE(e.category, 'General'), ' - ', COALESCE(e.note, 'Voucher payment')) as description,
                           COALESCE(u.username, CONCAT('User #', e.user)) as recorded_by");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);
        $all_exp = $this->db->get()->result();

        $cash_expenses = [];
        foreach ($all_exp as $exp_r) {
            if ($this->detectExpensePaymentMethod($exp_r) === 'Cash') {
                $cash_expenses[] = $exp_r;
            }
        }

        // 3. Outflows B: Cash Refunds on Cancelled Invoices
        $this->db->reset_query();
        $this->db->select("p.id, p.date, p.amount_received as amount,
                           'Disbursement' as tx_type,
                           CONCAT('Patient Refund: Cancelled Inv #', p.id) as description,
                           COALESCE(u.username, CONCAT('User #', p.user)) as recorded_by");
        $this->db->from('payment p');
        $this->db->join('users u', 'u.id = p.user', 'left');
        $this->db->where('p.hospital_id', $hospital_id);
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);
        $this->db->where("LOWER(TRIM(p.status))", 'cancelled');
        $this->db->where('p.amount_received >', 0.01);
        $cash_refunds = $this->db->get()->result();

        // Interleave all transactions chronologically
        $ledger = [];
        $total_inflow = 0.00;
        $total_outflow = 0.00;

        foreach ($inflow_rows as $in_r) {
            $amt = floatval($in_r->amount);
            $total_inflow += $amt;
            $ledger[] = [
                'id' => 'REC-' . $in_r->id,
                'date' => intval($in_r->date),
                'type' => 'Inflow',
                'description' => $in_r->description . (!empty($in_r->payment_id) ? ' (Inv #' . $in_r->payment_id . ')' : ''),
                'cash_in' => $amt,
                'cash_out' => 0.00,
                'recorded_by' => $in_r->recorded_by
            ];
        }

        foreach ($cash_expenses as $out_r) {
            $amt = floatval($out_r->amount);
            $total_outflow += $amt;
            $ledger[] = [
                'id' => 'EXP-' . $out_r->id,
                'date' => intval($out_r->date),
                'type' => 'Outflow',
                'description' => $out_r->description . (!empty($out_r->pay_to) ? ' | Pay To: ' . $out_r->pay_to : ''),
                'cash_in' => 0.00,
                'cash_out' => $amt,
                'recorded_by' => $out_r->recorded_by
            ];
        }

        foreach ($cash_refunds as $ref_r) {
            $amt = floatval($ref_r->amount);
            $total_outflow += $amt;
            $ledger[] = [
                'id' => 'REF-' . $ref_r->id,
                'date' => intval($ref_r->date),
                'type' => 'Outflow',
                'description' => $ref_r->description,
                'cash_in' => 0.00,
                'cash_out' => $amt,
                'recorded_by' => $ref_r->recorded_by
            ];
        }

        // Sort chronologically
        usort($ledger, function($a, $b) {
            if ($a['date'] === $b['date']) return 0;
            return ($a['date'] < $b['date']) ? -1 : 1;
        });

        // Calculate running cash balance
        $running = 0.00;
        foreach ($ledger as &$item) {
            $running += ($item['cash_in'] - $item['cash_out']);
            $item['running_balance'] = $running;
        }
        unset($item);

        return [
            'records' => $ledger,
            'summary' => [
                'total_inflow' => $total_inflow,
                'total_outflow' => $total_outflow,
                'net_cash_flow' => $total_inflow - $total_outflow,
                'closing_balance' => $running,
                'tx_count' => count($ledger)
            ]
        ];
    }

    /**
     * 11. BANK BOOK
     * Chronological ledger of Bank Deposits vs Bank Disbursements
     */
    public function getBankBookReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        // 1. Bank Inflows: Patient deposits via Bank
        $this->db->reset_query();
        $this->db->select("pd.id, pd.date, pd.deposited_amount as amount, pd.payment_id,
                           COALESCE(u.username, CONCAT('Cashier #', pd.user)) as recorded_by");
        $this->db->from('patient_deposit pd');
        $this->db->join('users u', 'u.id = pd.user', 'left');
        $this->db->where('pd.hospital_id', $hospital_id);
        $this->db->where('pd.date >=', $start_time);
        $this->db->where('pd.date <=', $end_time);
        $this->db->where("LOWER(TRIM(pd.deposit_type))", 'bank');
        $bank_inflows = $this->db->get()->result();

        // 2. Bank Outflows: Expenses paid via Bank / Cheque
        $this->db->reset_query();
        $this->db->select("e.id, e.date, e.amount, e.pay_to, e.category, e.note,
                           COALESCE(u.username, CONCAT('User #', e.user)) as recorded_by");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);
        $all_exp = $this->db->get()->result();

        $bank_outflows = [];
        foreach ($all_exp as $exp_r) {
            if ($this->detectExpensePaymentMethod($exp_r) === 'Bank') {
                $bank_outflows[] = $exp_r;
            }
        }

        $ledger = [];
        $total_deposits = 0.00;
        $total_withdrawals = 0.00;

        foreach ($bank_inflows as $in_r) {
            $amt = floatval($in_r->amount);
            $total_deposits += $amt;
            $ledger[] = [
                'id' => 'BNK-DEP-' . $in_r->id,
                'date' => intval($in_r->date),
                'type' => 'Deposit',
                'description' => 'Patient Bank Transfer / Deposit (Inv #' . $in_r->payment_id . ')',
                'deposit' => $amt,
                'withdrawal' => 0.00,
                'recorded_by' => $in_r->recorded_by
            ];
        }

        foreach ($bank_outflows as $out_r) {
            $amt = floatval($out_r->amount);
            $total_withdrawals += $amt;
            $ledger[] = [
                'id' => 'BNK-EXP-' . $out_r->id,
                'date' => intval($out_r->date),
                'type' => 'Withdrawal',
                'description' => 'Bank Payment: ' . $out_r->category . ' | Payee: ' . (!empty($out_r->pay_to) ? $out_r->pay_to : 'Vendor'),
                'deposit' => 0.00,
                'withdrawal' => $amt,
                'recorded_by' => $out_r->recorded_by
            ];
        }

        usort($ledger, function($a, $b) {
            if ($a['date'] === $b['date']) return 0;
            return ($a['date'] < $b['date']) ? -1 : 1;
        });

        $running = 0.00;
        foreach ($ledger as &$item) {
            $running += ($item['deposit'] - $item['withdrawal']);
            $item['running_balance'] = $running;
        }
        unset($item);

        return [
            'records' => $ledger,
            'summary' => [
                'total_deposits' => $total_deposits,
                'total_withdrawals' => $total_withdrawals,
                'net_bank_flow' => $total_deposits - $total_withdrawals,
                'closing_balance' => $running,
                'tx_count' => count($ledger)
            ]
        ];
    }

    /**
     * 12. EXPENSE APPROVAL REPORT
     * Audit log of expense vouchers, authorization status, and disbursement details
     */
    public function getExpenseApprovalReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->reset_query();

        $this->db->select("e.*, COALESCE(u.username, CONCAT('User #', e.user)) as creator_name,
                           u.first_name, u.last_name");
        $this->db->from('expense e');
        $this->db->join('users u', 'u.id = e.user', 'left');
        $this->db->where('e.hospital_id', $hospital_id);
        $this->db->where('e.date >=', $start_time);
        $this->db->where('e.date <=', $end_time);
        $this->db->order_by('e.date', 'DESC');
        $rows = $this->db->get()->result();

        $total_approved = 0.00;
        foreach ($rows as &$r) {
            $r->payment_method = $this->detectExpensePaymentMethod($r);
            $r->approval_status = 'Approved & Disbursed';
            $r->approved_by = !empty($r->creator_name) ? $r->creator_name : 'Admin In-Charge';
            $total_approved += floatval($r->amount);
        }
        unset($r);

        return [
            'records' => $rows,
            'summary' => [
                'total_vouchers' => count($rows),
                'total_approved_amount' => $total_approved,
                'pending_approval_amount' => 0.00,
                'rejected_amount' => 0.00
            ]
        ];
    }

}
