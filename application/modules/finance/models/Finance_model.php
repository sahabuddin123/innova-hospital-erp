<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Finance_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
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
    public function getFinancialPerformanceReportData($period_type = 'monthly', $selected_date = null, $date_from = null, $date_to = null, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        if (empty($hospital_id)) {
            $hospital_id = 1;
        }

        $this->db->reset_query();

        // 1. Determine Timestamps & Period Labels
        $today_str = date('Y-m-d');
        if (empty($selected_date)) {
            $selected_date = $today_str;
        }

        if ($period_type == 'daily') {
            $selected_date = date('Y-m-d', strtotime($selected_date));
            $start_time = strtotime($selected_date . ' 00:00:00');
            $end_time = strtotime($selected_date . ' 23:59:59');
            $prev_start = $start_time - 86400;
            $prev_end = $end_time - 86400;
            $period_label = date('d F Y', $start_time);
            $prev_label = 'vs previous day';
        } elseif ($period_type == 'weekly') {
            if (!empty($date_from) && !empty($date_to)) {
                $start_time = strtotime(date('Y-m-d 00:00:00', strtotime($date_from)));
                $end_time = strtotime(date('Y-m-d 23:59:59', strtotime($date_to)));
                $span = $end_time - $start_time + 1;
                $prev_start = $start_time - $span;
                $prev_end = $start_time - 1;
                $selected_date = date('Y-m-d', $start_time);
            } else {
                $ts = strtotime($selected_date);
                $w_start = strtotime('monday this week', $ts);
                if ($w_start > $ts) {
                    $w_start = strtotime('-7 days', $w_start);
                }
                $start_time = strtotime(date('Y-m-d 00:00:00', $w_start));
                $end_time = $start_time + (7 * 86400) - 1;
                $prev_start = $start_time - (7 * 86400);
                $prev_end = $start_time - 1;
            }
            $period_label = date('d M Y', $start_time) . ' - ' . date('d M Y', $end_time);
            $prev_label = 'vs previous week';
        } elseif ($period_type == 'yearly') {
            $year = date('Y', strtotime($selected_date));
            $start_time = strtotime($year . '-01-01 00:00:00');
            $end_time = strtotime($year . '-12-31 23:59:59');
            $prev_start = strtotime(($year - 1) . '-01-01 00:00:00');
            $prev_end = strtotime(($year - 1) . '-12-31 23:59:59');
            $period_label = $year;
            $prev_label = 'vs previous year';
        } else {
            $period_type = 'monthly';
            $ts = strtotime($selected_date);
            $start_time = strtotime(date('Y-m-01 00:00:00', $ts));
            $end_time = strtotime(date('Y-m-t 23:59:59', $ts));
            $prev_start = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month', $start_time)));
            $prev_end = strtotime(date('Y-m-t 23:59:59', $prev_start));
            $period_label = date('F Y', $start_time);
            $prev_label = 'vs previous month';
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
        $current_data['summary']['date_from_formatted'] = date('d/m/Y', $start_time);
        $current_data['summary']['date_to_formatted'] = date('d/m/Y', $end_time);
        $current_data['summary']['prev_label'] = $prev_label;

        // Card trend badges
        $cards = ['total_invoices', 'gross_billing', 'total_discount', 'net_billing', 'total_collection', 'total_due', 'doctor_commission', 'referral_commission', 'total_expenses', 'gross_profit', 'net_profit', 'net_margin'];
        $trends = [];
        foreach ($cards as $c) {
            $curr_val = floatval($current_data['summary'][$c]);
            $p_val = floatval($prev_data['summary'][$c]);
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
     * Internal period calculator engine: provides exact reconciliation
     */
    protected function _calculateFinancialPeriod($start_time, $end_time, $period_type, $hospital_id)
    {
        $this->db->reset_query();

        // 1. Initialize Breakdown slots
        $breakdown = [];
        if ($period_type == 'daily') {
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
        } elseif ($period_type == 'weekly' || $period_type == 'monthly') {
            $cur = $start_time;
            while ($cur <= $end_time) {
                $slot_key = date('Y-m-d', $cur);
                $breakdown[$slot_key] = [
                    'key' => $slot_key,
                    'label' => ($period_type == 'weekly') ? date('D, d M', $cur) : date('d M', $cur),
                    'invoices' => 0, 'gross_billing' => 0.00, 'discount' => 0.00, 'net_billing' => 0.00,
                    'collection' => 0.00, 'due' => 0.00, 'doctor_comm' => 0.00, 'referral_comm' => 0.00,
                    'expense' => 0.00, 'net_profit' => 0.00
                ];
                $cur += 86400;
            }
        } elseif ($period_type == 'yearly') {
            for ($m = 1; $m <= 12; $m++) {
                $slot_key = sprintf('%s-%02d', date('Y', $start_time), $m);
                $breakdown[$slot_key] = [
                    'key' => $slot_key,
                    'label' => date('F', strtotime($slot_key . '-01')),
                    'invoices' => 0, 'gross_billing' => 0.00, 'discount' => 0.00, 'net_billing' => 0.00,
                    'collection' => 0.00, 'due' => 0.00, 'doctor_comm' => 0.00, 'referral_comm' => 0.00,
                    'expense' => 0.00, 'net_profit' => 0.00
                ];
            }
        }

        // 2. Fetch Invoices / Payments
        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $payments = $this->db->get()->result();

        // AUDIT-008 FIX: Preload all payment_items for this batch in a single query (O(1) instead of O(N))
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

        $cancelled_invoices = 0;
        $refunded_amount = 0.00;
        $doctor_breakdown = [];
        $service_perf = [];
        $paid_doctor_comm = 0.00;
        $pending_doctor_comm = 0.00;

        foreach ($payments as $p) {
            if (strtolower(trim($p->status)) === 'cancelled') {
                $cancelled_invoices++;
                $refunded_amount += floatval($p->amount_received);
                continue;
            }

            $slot_key = ($period_type == 'daily') ? date('H:00', $p->date) : (($period_type == 'yearly') ? date('Y-m', $p->date) : date('Y-m-d', $p->date));

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

            if (isset($breakdown[$slot_key])) {
                $breakdown[$slot_key]['invoices']++;
                $breakdown[$slot_key]['gross_billing'] += $gross;
                $breakdown[$slot_key]['discount'] += $disc;
                $breakdown[$slot_key]['net_billing'] += $net;
                $breakdown[$slot_key]['doctor_comm'] += $d_comm;
            }

            // Doctor Paid/Pending status
            $is_paid = (strtolower(trim($p->status)) === 'paid' || floatval($p->amount_received) >= floatval($p->gross_total));
            if ($is_paid) {
                $paid_doctor_comm += $d_comm;
            } else {
                $pending_doctor_comm += $d_comm;
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
                    'commission' => 0.00
                ];
            }
            $doctor_breakdown[$doc_id]['invoices']++;
            $doctor_breakdown[$doc_id]['billing'] += $net;
            $doctor_breakdown[$doc_id]['commission'] += $d_comm;

            // Service Performance: Use preloaded in-memory items (Zero queries inside loop)
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

                    if (!isset($service_perf[$item_name])) {
                        $service_perf[$item_name] = [
                            'name' => $item_name,
                            'qty' => 0,
                            'gross' => 0.00,
                            'discount' => 0.00,
                            'net' => 0.00
                        ];
                    }
                    $service_perf[$item_name]['qty'] += $qty;
                    $service_perf[$item_name]['gross'] += $subtotal;
                    $service_perf[$item_name]['discount'] += $line_disc;
                    $service_perf[$item_name]['net'] += $line_net;
                }
            } elseif (!empty($p->category_name)) {
                $cats = explode(',', $p->category_name);
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

                        if (!isset($service_perf[$item_name])) {
                            $service_perf[$item_name] = [
                                'name' => $item_name,
                                'qty' => 0,
                                'gross' => 0.00,
                                'discount' => 0.00,
                                'net' => 0.00
                            ];
                        }
                        $service_perf[$item_name]['qty'] += $qty;
                        $service_perf[$item_name]['gross'] += $subtotal;
                        $service_perf[$item_name]['discount'] += $line_disc;
                        $service_perf[$item_name]['net'] += $line_net;
                    }
                }
            }
        }

        // 3. Fetch Deposits / Collections
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
            $slot_key = ($period_type == 'daily') ? date('H:00', $d->date) : (($period_type == 'yearly') ? date('Y-m', $d->date) : date('Y-m-d', $d->date));
            if (isset($breakdown[$slot_key])) {
                $breakdown[$slot_key]['collection'] += $amt;
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

        // 4. Fetch Referral Commissions
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

        foreach ($ref_ledgers as $rl) {
            if ($rl->status === 'Reversed') continue;
            $comm = floatval($rl->commission_amount);
            $earned = floatval($rl->earned_amount);
            $slot_key = ($period_type == 'daily') ? date('H:00', $rl->created_date) : (($period_type == 'yearly') ? date('Y-m', $rl->created_date) : date('Y-m-d', $rl->created_date));
            if (isset($breakdown[$slot_key])) {
                $breakdown[$slot_key]['referral_comm'] += $comm;
            }
            $total_ref_comm += $comm;
            $earned_ref_comm += $earned;
            $pending_ref_comm += ($comm - $earned);

            $ref_id = $rl->referrer_id;
            $ref_name = !empty($rl->referrer_name) ? $rl->referrer_name : 'Referrer #' . $ref_id;
            if (!isset($referrer_breakdown[$ref_id])) {
                $referrer_breakdown[$ref_id] = [
                    'id' => $ref_id,
                    'name' => $ref_name,
                    'patients' => [],
                    'invoices' => [],
                    'billing' => 0.00,
                    'commission' => 0.00
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
        }

        // Referral Wallet Metrics
        $this->db->reset_query();
        $w_row = $this->db->select_sum('available_balance')->select_sum('total_withdrawn')->where('hospital_id', $hospital_id)->get('referral_wallet')->row();
        $available_wallet = $w_row ? floatval($w_row->available_balance) : 0.00;
        $total_withdrawn = $w_row ? floatval($w_row->total_withdrawn) : 0.00;

        // 5. Fetch Hospital Expenses
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
            $slot_key = ($period_type == 'daily') ? date('H:00', $e->date) : (($period_type == 'yearly') ? date('Y-m', $e->date) : date('Y-m-d', $e->date));
            if (isset($breakdown[$slot_key])) {
                $breakdown[$slot_key]['expense'] += $amt;
            }
            $cat = !empty($e->category) ? trim($e->category) : 'Other Expenses';
            if (!isset($expense_breakdown[$cat])) $expense_breakdown[$cat] = 0.00;
            $expense_breakdown[$cat] += $amt;
        }

        // 6. Final Summary Aggregation & Complete Reconciliation
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
            'earned_ref_comm' => $earned_ref_comm,
            'pending_ref_comm' => $pending_ref_comm,
            'withdrawn_ref_comm' => $total_withdrawn,
            'available_wallet_balance' => $available_wallet
        ];

        // Format chart data arrays
        $chart_labels = [];
        $chart_billing = [];
        $chart_collection = [];
        $chart_due = [];

        foreach ($breakdown as $k => &$row) {
            $row['due'] = max(0.00, $row['net_billing'] - $row['collection']);
            $row['net_profit'] = $row['net_billing'] - $row['doctor_comm'] - $row['referral_comm'] - $row['expense'];

            $summary['total_invoices'] += $row['invoices'];
            $summary['gross_billing'] += $row['gross_billing'];
            $summary['total_discount'] += $row['discount'];
            $summary['net_billing'] += $row['net_billing'];
            $summary['total_collection'] += $row['collection'];
            $summary['total_due'] += $row['due'];
            $summary['doctor_commission'] += $row['doctor_comm'];
            $summary['referral_commission'] += $row['referral_comm'];
            $summary['total_expenses'] += $row['expense'];
            $summary['net_profit'] += $row['net_profit'];

            $chart_labels[] = $row['label'];
            $chart_billing[] = round($row['net_billing'], 2);
            $chart_collection[] = round($row['collection'], 2);
            $chart_due[] = round($row['due'], 2);
        }
        unset($row);

        $summary['gross_profit'] = $summary['net_billing'] - $summary['doctor_commission'] - $summary['referral_commission'];
        $summary['net_margin'] = ($summary['net_billing'] > 0) ? round(($summary['net_profit'] / $summary['net_billing']) * 100, 2) : 0.00;

        // Service Performance sorted desc
        uasort($service_perf, function ($a, $b) {
            return $b['gross'] <=> $a['gross'];
        });

        // Payment Method Collection List
        $method_list = [];
        $std_methods = ['Cash', 'bKash', 'Nagad', 'Rocket', 'Bank', 'Card'];
        $coll_total = $summary['total_collection'];
        $other_total = 0.00;

        foreach ($methods as $m_name => $m_amt) {
            if (in_array($m_name, $std_methods)) {
                $pct = ($coll_total > 0) ? round(($m_amt / $coll_total) * 100, 1) : 0.0;
                $method_list[$m_name] = [
                    'name' => $m_name,
                    'amount' => $m_amt,
                    'percentage' => $pct
                ];
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
        $summary['bkash_collection'] = isset($methods['bKash']) ? $methods['bKash'] : 0.00;
        $summary['nagad_collection'] = isset($methods['Nagad']) ? $methods['Nagad'] : 0.00;
        $summary['rocket_collection'] = isset($methods['Rocket']) ? $methods['Rocket'] : 0.00;
        $summary['bank_collection'] = isset($methods['Bank']) ? $methods['Bank'] : 0.00;
        $summary['card_collection'] = isset($methods['Card']) ? $methods['Card'] : 0.00;
        $summary['other_collection'] = $other_total;

        return [
            'summary' => $summary,
            'breakdown' => $breakdown,
            'doctor_breakdown' => $doctor_breakdown,
            'referrer_breakdown' => $referrer_breakdown,
            'expense_breakdown' => $expense_breakdown,
            'service_perf' => array_values($service_perf),
            'methods' => $method_list,
            'chart_data' => [
                'labels' => $chart_labels,
                'billing' => $chart_billing,
                'collection' => $chart_collection,
                'due' => $chart_due
            ]
        ];
    }
}
