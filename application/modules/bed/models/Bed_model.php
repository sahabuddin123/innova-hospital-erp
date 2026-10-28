<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bed_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertBed($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('bed', $data2);
    }

    function getBed()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('bed');
        return $query->result();
    }

    function getBedWithoutSearch($order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('bed');
        return $query->result();
    }

    function getBedBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }

    function getBedByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('bed');
        return $query->result();
    }

    function getBedByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }










    function getBedWithoutSearchByStatus($order, $dir, $status)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('bed');
        return $query->result();
    }

    function getBedBySearchByStatus($search, $order, $dir, $status)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }

    function getBedByLimitByStatus($limit, $start, $order, $dir, $status)
    {
        $this->db->where('bed.hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by('bed.' . $order, $dir);
        } else {
            $this->db->order_by('bed.id', 'desc');
        }


        if ($status == 'available') {
            $field = 'alloted_bed.d_timestamp IS NOT NULL OR alloted_bed.d_timestamp = ""';
           
        }

        if ($status == 'alloted') {
            $field = 'alloted_bed.d_timestamp IS NULL';
        }

        $this->db->group_by('bed.id');

        $this->db->limit($limit, $start);
        $query = $this->db->select('bed.id, bed.bed_id, bed.description, bed.current_allotment_id, alloted_bed.a_timestamp, alloted_bed.d_timestamp')
            ->from('bed')
            ->join('alloted_bed', 'alloted_bed.id = bed.current_allotment_id', 'left')
            ->where($field)
            ->get();



        return $query->result();
    }

    function getBedByLimitBySearchByStatus($limit, $start, $search, $order, $dir, $status)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }













    function getBedById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('bed');
        return $query->row();
    }

    function updateBed($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bed', $data);
    }

    function updateBedByBedId($bed_id, $data)
    {
        $this->db->where('bed_id', $bed_id);
        $this->db->update('bed', $data);
    }

    function insertBedCategory($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('bed_category', $data2);
    }

    function getBedCategory()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('bed_category');
        return $query->result();
    }


    function getBedAllotment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }

    function getBedAllotmentsByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }

    function getBedCategoryById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('bed_category');
        return $query->row();
    }

    function updateBedCategory($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bed_category', $data);
    }

    function deleteBed($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bed');
    }

    function deleteBedCategory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bed_category');
    }

    function insertAllotment($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('alloted_bed', $data2);
    }

    function getAllotment()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }







    function getAllotmentWithoutSearch($order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }

    function getBedAllotmentBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('alloted_bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }

    function getBedAllotmentByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }

    function getBedAllotmentByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('alloted_bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }












    function getAllotmentWithoutSearchByStatus($order, $dir, $status)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }

        if ($status == 'discharged') {
            $field = 'd_time !=';
        } else {
            $field = 'd_time';
        }
        $this->db->where($field, null);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }

    function getBedAllotmentBySearchByStatus($search, $order, $dir, $status)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        if ($status == 'discharged') {
            $field = 'd_time !=';
        } else {
            $field = 'd_time';
        }
        $query = $this->db->select('*')
            ->from('alloted_bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where($field, null)
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }

    function getBedAllotmentByLimitByStatus($limit, $start, $order, $dir, $status)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        if ($status == 'discharged') {
            $field = 'd_time !=';
        } else {
            $field = 'd_time';
        }
        $this->db->where($field, null);
        $this->db->limit($limit, $start);
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }

    function getBedAllotmentByLimitBySearchByStatus($limit, $start, $search, $order, $dir, $status)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        if ($status == 'discharged') {
            $field = 'd_time !=';
        } else {
            $field = 'd_time';
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('alloted_bed')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where($field, null)
            ->where("(id LIKE '%" . $search . "%' OR bed_id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }

    function getAllotmentById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('alloted_bed');
        return $query->row();
    }

    function updateAllotment($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('alloted_bed', $data);
    }

    function deleteBedAllotment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('alloted_bed');
    }

    function getAllotedBedByTime($a_time, $d_time, $bed_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->group_start();
        $this->db->where('bed_id', $bed_id);
        $this->db->where('a_time_search <=', strtotime(str_replace(" - ", " ", $a_time)));
        $this->db->where('d_time_search >=', strtotime(str_replace(" - ", " ", $a_time)));
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('bed_id', $bed_id);
        $this->db->where('a_time_search <=', strtotime(str_replace(" - ", " ", $d_time)));
        $this->db->where('d_time_search >=', strtotime(str_replace(" - ", " ", $d_time)));
        $this->db->group_end();
        $this->db->where('bed_id', $bed_id);
        return $this->db->get('alloted_bed')->result();
    }

    function getAllotedBedByATime($a_time, $d_time, $bed_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->group_start();
        $this->db->where('bed_id', $bed_id);
        $this->db->where('a_time_search <=', strtotime(str_replace(" - ", " ", $a_time)));
        $this->db->where('d_time_search >=', strtotime(str_replace(" - ", " ", $a_time)));
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('bed_id', $bed_id);
        $this->db->where('a_time_search <=', strtotime(str_replace(" - ", " ", $d_time)));
        $this->db->where('d_time_search >=', strtotime(str_replace(" - ", " ", $d_time)));
        $this->db->group_end();
        return $this->db->get('alloted_bed')->result();
    }

    function getNotBedAvailableList($date, $category)
    {

        $array = array('bed_id' => $category, 'a_time_search <=' => $date, 'd_time_search >=' => $date);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where($array);
        return $this->db->get('alloted_bed')->result();
    }

    function getNotBedAvailableListFromEdit($date, $category, $id)
    {

        $array = array('bed_id' => $category, 'a_time_search <=' => $date, 'd_time_search >=' => $date, 'id !=' => $id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where($array);
        return $this->db->get('alloted_bed')->result();
    }
    function getBedByCategory($id)
    {

        return $this->db->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('category', $id)
            ->get('bed')->result();
    }

    function getBloodGroup()
    {
        return $this->db->get('blood_group')->result();
    }

    function getDailyProgressByBedId($id)
    {
        return $this->db->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('alloted_bed_id', $id)

            ->get('daily_progress')->result();
    }

    function getDailyProgressById($id)
    {
        return $this->db->where('id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('daily_progress')->row();
    }

    function insertDailyProgress($data)
    {

        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('daily_progress', $data2);
    }

    function updateDailyProgress($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('daily_progress', $data);
    }
    function insertMedicineAllotedPatient($data)
    {

        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('bed_medicine', $data2);
    }
    function getMedicineAllotedPatientById($id)
    {
        return $this->db->where('id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_medicine')->row();
    }
    function updateMedicineAlloted($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bed_medicine', $data);
    }
    function getMedicineAllotedByBedId($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_medicine')->result();
    }
    function deleteMedicine($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bed_medicine');
    }
    function getServicesByDate($date)
    {
        return $this->db->where('date', $date)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_service')->row();
    }

    function updateServices($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bed_service', $data);
    }

    function insertServices($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('bed_service', $data2);
    }
    function getServiceAllotedByBedId($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_service')->result();
    }
    function getServicedById($id)
    {
        return $this->db->where('id', $id)

            ->get('bed_service')->row();
    }
    function deleteServices($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bed_service');
    }




    function getDiagnosticByDate($date)
    {
        return $this->db->where('date', $date)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_diagnostic')->row();
    }

    function updateDiagnostic($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bed_diagnostic', $data);
    }

    function insertDiagnostic($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('bed_diagnostic', $data2);
    }
    function getDiagnosticAllotedByBedId($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_diagnostic')->result();
    }
    function getDiagnosticById($id)
    {
        return $this->db->where('id', $id)

            ->get('bed_diagnostic')->row();
    }

    function getBedDiagnosticByIdByDate($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_diagnostic')->result();
    }



    function deleteDiagnostic($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bed_diagnostic');
    }




    function insertCheckout($data)
    {

        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('bed_checkout', $data2);
    }
    function updateCheckout($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bed_checkout', $data);
    }
    function getCheckoutdById($id)
    {
        return $this->db->where('id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_checkout')->row();
    }
    function getCheckoutByBedId($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_checkout')->row();
    }
    function getAllotedBedByBedIdByDate($bed_id, $date)
    {

        $this->db->where('bed_id', $bed_id)->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('a_timestamp <=', $date);

        //$this->db->limit('1');
        $query = $this->db->get('alloted_bed');
        return $query->result();
    }
    function getBedAllotmentsById($id)
    {

        $this->db->where('id', $id);
        $query = $this->db->get('alloted_bed');
        return $query->row();
    }
    // function getServicedByIdByDate($id) {
    //     return $this->db->where('alloted_bed_id', $id)
    //                     ->where('hospital_id', $this->session->userdata('hospital_id'))
    //                    ->where('date',date('d-m-Y'))
    //                     ->get('bed_service')->row();
    // }
    function getServicedByIdByDate($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_service')->result();
    }

    function updateServiceAlloted($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bed_service', $data);
    }


    function getAllBedPayments($id)
    {
        $medicine_payments = $this->getMedicineAllotedByBedId($id);
        $service_payemnts = $this->getServiceAllotedByBedId($id);
        $diagnostic_payments = $this->getDiagnosticAllotedByBedId($id);
        $payment_id = array();
        foreach ($medicine_payments as $medicine_payment) {
            if (!empty($medicine_payment->payment_id)) {
                $payment_id[] = $medicine_payment->payment_id;
            }
        }
        foreach ($service_payemnts as $service_payemnt) {
            if (!empty($service_payemnt->payment_id)) {
                $payment_id[] = $service_payemnt->payment_id;
            }
        }
        foreach ($diagnostic_payments as $diagnostic_payment) {
            if (!empty($diagnostic_payment->payment_id)) {
                $payment_id[] = $diagnostic_payment->payment_id;
            }
        }
        $payment_id = array_unique($payment_id);
        return $payment_id;
    }


    function getAllBedPaymentsSummary($id)
    {
        $medicine_payments = $this->getMedicineAllotedByBedId($id);
        $service_payemnts = $this->getServiceAllotedByBedId($id);
        $payment_id = array();
        $data = array();
        $diagnostic_payments = $this->getDiagnosticAllotedByBedId($id);
        foreach ($medicine_payments as $medicine_payment) {
            $payment_id[] = $medicine_payment->payment_id;
        }
        foreach ($service_payemnts as $service_payemnt) {
            $payment_id[] = $service_payemnt->payment_id;
        }
        foreach ($diagnostic_payments as $diagnostic_payment) {
            $payment_id[] = $diagnostic_payment->payment_id;
        }
        $payment_id = array_unique($payment_id);
        foreach ($payment_id as $payment) {
            $payment_details = $this->getPaymentSummaryById($payment);
            $amount[] = $payment_details['amount'];
            $discount[] = $payment_details['discount'];
            $total[] = $payment_details['total'];
            $paid[] = $payment_details['paid'];
            $due[] = $payment_details['due'];
        }


        if (!empty($amount)) {
            $data['amount'] = array_sum($amount);
        } else {
            $data['amount'] = 0;
        }

        if (!empty($discount)) {
            $data['discount'] = array_sum($discount);
        } else {
            $data['discount'] = 0;
        }

        if (!empty($total)) {
            $data['total'] = array_sum($total);
        } else {
            $data['total'] = 0;
        }

        if (!empty($paid)) {
            $data['paid'] = array_sum($paid);
        } else {
            $data['paid'] = 0;
        }
        
        if (!empty($due)) {
            $data['due'] = array_sum($due);
        } else {
            $data['due'] = 0;
        }


        return $data;
    }

    function getPaymentSummaryById($id)
    {
        $query = $this->db->get_where('payment', array('id' => $id))->result();
        $deposits = $this->db->get_where('patient_deposit', array('payment_id' => $id))->result();

        foreach ($query as $gross) {
            $balance[] = $gross->gross_total;
            $amount[] = $gross->amount;
            $discount[] = $gross->discount;
        }
        foreach ($deposits as $deposit) {
            $deposit_balance[] = $deposit->deposited_amount;
        }

        if (!empty($balance)) {
            $data['total'] = array_sum($balance);
        } else {
            $data['total'] = 0;
        }


        if (!empty($amount)) {
            $data['amount'] = array_sum($amount);
        } else {
            $data['amount'] = 0;
        }


        if (!empty($discount)) {
            $data['discount'] = array_sum($discount);
        } else {
            $data['discount'] = 0;
        }


        if (!empty($deposit_balance)) {
            $data['paid'] = array_sum($deposit_balance);
        } else {
            $data['paid'] = 0;
        }

        $data['due'] = $data['total'] - $data['paid'];

        return $data;
    }




    function getAvaiablePatietListforBedAllotment($searchTerm)
    {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $s = $this->db->escape_like_str($searchTerm);
            $this->db->where("(name LIKE '%" . $s . "%' OR id LIKE '%" . $s . "%' OR phone LIKE '%" . $s . "%')", NULL, FALSE);
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(20);
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $this->db->select('*');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $bed_allotments = $this->db->get('alloted_bed')->result();
        $alloted_patients = array();
        foreach ($bed_allotments as $allotment) {
            if (empty($allotment->d_timestamp) && !empty($allotment->a_timestamp)) {
                $alloted_patients[] = $allotment->patient;
            } elseif ($allotment->d_timestamp > time()) {
                $alloted_patients[] = $allotment->patient;
            }
        }

        $data = array();
        foreach ($users as $user) {
            if (!in_array($user['id'], $alloted_patients)) {
                if (empty($user['age'])) {
                    $dateOfBirth = $user['birthdate'];
                    if (empty($dateOfBirth)) {
                        $age[0] = '0';
                    } else {
                        $today = date("Y-m-d");
                        $diff = date_diff(date_create($dateOfBirth), date_create($today));
                        $age[0] = $diff->format('%y');
                    }
                } else {
                    $age = explode('-', $user['age']);
                }
                $data[] = array("id" => $user['id'], "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . '- ' . lang('phone') . ': ' . $user['phone'] . '- ' . lang('age') . ': ' . $age[0] . ' years )');
            }
        }
        return $data;
    }





    function deletePaymentByAllotmentId($id)
    {
        $this->db->where('alloted_bed_id', $id);
        $this->db->delete('payment');
    }
    function deleteServiceByAllotmentId($id)
    {
        $this->db->where('alloted_bed_id', $id);
        $this->db->delete('bed_service');
    }
    function deleteMedicineByAllotmentId($id)
    {
        $this->db->where('alloted_bed_id', $id);
        $this->db->delete('bed_medicine');
    }
    function deleteDiagnosticByAllotmentId($id)
    {
        $this->db->where('alloted_bed_id', $id);
        $this->db->delete('bed_diagnostic');
    }
    function deleteCheckoutByAllotmentId($id)
    {
        $this->db->where('alloted_bed_id', $id);
        $this->db->delete('bed_diagnostic');
    }
    function deleteDailyProgressByAllotmentId($id)
    {
        $this->db->where('alloted_bed_id', $id);
        $this->db->delete('daily_progress');
    }
    function deleteBedLab($id)
    {
        $this->db->where('alloted_bed_id', $id);
        $this->db->delete('lab');
    }


    function getPaymentByAdmissionIdAndPaymentFrom($id, $payment_from){
        $this->db->where('alloted_bed_id', $id);
        $this->db->where('payment_from', $payment_from);
        $this->db->delete('payment');
    }


}
