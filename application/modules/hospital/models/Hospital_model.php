<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Hospital_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function hospitalId() {
        if (!$this->ion_auth->in_group(array('superadmin'))) {
            if ($this->ion_auth->in_group(array('admin'))) {
                $current_user_id = $this->ion_auth->user()->row()->id;
                $hospital_id = $this->db->get_where('hospital', array('ion_user_id' => $current_user_id))->row()->id;
                return $hospital_id;
            } else {
                $current_user_id = $this->ion_auth->user()->row()->id;
                $group_id = $this->db->get_where('users_groups', array('user_id' => $current_user_id))->row()->group_id;
                $group_name = $this->db->get_where('groups', array('id' => $group_id))->row()->name;
                $group_name = strtolower($group_name);
                $hospital_id = $this->db->get_where($group_name, array('ion_user_id' => $current_user_id))->row()->hospital_id;
                return $hospital_id;
            }
        }
    }

    function modules() {
        if (!$this->ion_auth->in_group(array('superadmin'))) {
            if ($this->ion_auth->in_group(array('admin'))) {
                $current_user_id = $this->ion_auth->user()->row()->id;
                $modules = $this->db->get_where('hospital', array('ion_user_id' => $current_user_id))->row()->module;
                $module = explode(',', $modules);
                return $module;
            } else {
                $current_user_id = $this->ion_auth->user()->row()->id;
                $group_id = $this->db->get_where('users_groups', array('user_id' => $current_user_id))->row()->group_id;
                $group_name = $this->db->get_where('groups', array('id' => $group_id))->row()->name;
                $group_name = strtolower($group_name);
                $hospital_id = $this->db->get_where($group_name, array('ion_user_id' => $current_user_id))->row()->hospital_id;
                $modules = $this->db->get_where('hospital', array('id' => $hospital_id))->row()->module;
                $module = explode(',', $modules);
                return $module;
            }
        }
    }

    function addHospitalIdToIonUser($ion_user_id, $hospital_id) {
        $hospital_ion_id = $this->db->get_where('hospital', array('id' => $hospital_id))->row()->ion_user_id;
        $uptade_ion_user = array(
            'hospital_ion_id' => $hospital_ion_id,
        );
        $this->db->where('id', $ion_user_id);
        $this->db->update('users', $uptade_ion_user);
    }

    function insertHospital($data) {
        $this->db->insert('hospital', $data);
    }

    function getHospital() {
        $query = $this->db->get('hospital');
        return $query->result();
    }

    function getHospitalById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('hospital');
        return $query->row();
    }

    function updateHospital($id, $data) {

        $this->db->where('id', $id);
        $this->db->update('hospital', $data);
    }

    function updateHospitalByIonId($id, $data) {
        $this->db->where('ion_user_id', $id);
        $this->db->update('hospital', $data);
    }

    function activate($id, $data) {
        $this->db->where('id', $id);
        $this->db->or_where('hospital_ion_id', $id);
        $this->db->update('users', $data);
    }

    function deactivate($id, $data) {
        $this->db->where('hospital_ion_id', $id);
        $this->db->or_where('id', $id);
        $this->db->update('users', $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('hospital');
    }

    function updateIonUser($username, $email, $password, $ion_user_id) {
        $uptade_ion_user = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $this->db->where('id', $ion_user_id);
        $this->db->update('users', $uptade_ion_user);
    }

    function getHospitalId($current_user_id) {
        $group_id = $this->db->get_where('users_groups', array('user_id' => $current_user_id))->row()->group_id;
        $group_name = $this->db->get_where('groups', array('id' => $group_id))->row()->name;
        $group_name = strtolower($group_name);
        $hospital_id = $this->db->get_where($group_name, array('ion_user_id' => $current_user_id))->row()->hospital_id;
        return $hospital_id;
    }

    function createAutoSmsTemplate($hospital_id) {
        $data = array();
        $data = array('0' => array(
                'name' => 'Payment successful sms to patient',
                'message' => 'Dear {name}, Your paying amount - Tk {amount} was successful. Thank You Please contact our support for further queries.',
                'type' => 'payment',
                'status' => 'Active',
            ),
            '1' => array(
                'name' => 'Appointment Confirmation sms to patient',
                'message' => 'Dear {name}, Your appointment with {doctorname} on {appoinmentdate} at {time_slot} is confirmed. For more information contact with {hospital_name} Regards',
                'type' => 'appoinment_confirmation',
                'status' => 'Active',
            ),
            '2' => array(
                'name' => 'Appointment creation sms to patient',
                'message' => 'Dear {name}, You have an appointment with {doctorname} on {appoinmentdate} at {time_slot} .Please confirm your appointment. For more information contact with {hospital_name} Regards',
                'type' => 'appoinment_creation',
                'status' => 'Active',
            ),
            '3' => array(
                'name' => 'Meeting Schedule Notification To Patient',
                'message' => 'Dear {patient_name}, You have a Live Video Meeting with {doctor_name} on {start_time}. For more information contact with {hospital_name} . Regards',
                'type' => 'meeting_creation',
                'status' => 'Active',
            ),
            '4' => array(
                'name' => 'send appoint confirmation to Doctor',
                'message' => 'Dear {name}, You are appointed as a doctor in {department} . Thank You {company}',
                'type' => 'doctor',
                'status' => 'Active',
            ),
            '5' => array(
                'name' => 'Patient Registration Confirmation',
                'message' => 'Dear {name}, You are registred to {company} as a patient to {doctor}. Regards',
                'type' => 'patient',
                'status' => 'Active',
        ),'6' => array(
                'name' => 'Appointment Remainder sms to patient',
                'message' => 'Dear {name}, REMAINDER!!<br> You have an appointment with {doctorname} on {appoinmentdate} at {time_slot} . For more information contact with {hospital_name} Regards',
                'type' => 'appoinment_remainder',
                'status' => 'Active',
            )
            );

        foreach ($data as $row) {
            $data1 = array();
            $data1 = array(
                'name' => $row['name'],
                'message' => $row['message'],
                'type' => $row['type'],
                'status' => $row['status'],
                'hospital_id' => $hospital_id
            );
            $this->db->insert('autosmstemplate', $data1);
        }
    }

    function createAutoEmailTemplate($hospital_id) {
        $data = array();
        $data = array('0' => array(
                'name' => 'Payment successful email to patient',
                'message' => 'Dear {name}, Your paying amount - Tk {amount} was successful. Thank You Please contact our support for further queries.',
                'type' => 'payment',
                'status' => 'Active',
            ),
            '1' => array(
                'name' => 'Appointment Confirmation email to patient',
                'message' => 'Dear {name}, Your appointment with {doctorname} on {appoinmentdate} at {time_slot} is confirmed. For more information contact with {hospital_name} Regards',
                'type' => 'appoinment_confirmation',
                'status' => 'Active',
            ),
            '2' => array(
                'name' => 'Appointment creation email to patient',
                'message' => 'Dear {name}, You have an appointment with {doctorname} on {appoinmentdate} at {time_slot} .Please confirm your appointment. For more information contact with {hospital_name} Regards',
                'type' => 'appoinment_creation',
                'status' => 'Active',
            ),
            '3' => array(
                'name' => 'Meeting Schedule Notification To Patient',
                'message' => 'Dear {patient_name}, You have a Live Video Meeting with {doctor_name} on {start_time}. For more information contact with {hospital_name} . Regards',
                'type' => 'meeting_creation',
                'status' => 'Active',
            ),
            '4' => array(
                'name' => 'Send Appointment confirmation to Doctor',
                'message' => 'Dear {name},<br> You are registered to {company} as a New Doctor in {department}.<br><br> Your Details:<br> Url Link: {base_url}<br> Username : {email}<br> Password: {password}<br><br> Thank You for registration.<br> Regards',
                'type' => 'doctor',
                'status' => 'Active',
            ),
            '5' => array(
                'name' => 'Patient Registration Confirmation',
                'message' => 'Dear {name},<br> You are registered to {company} as a New Patient to {doctor}.<br><br> Your Details:<br> Url Link: {base_url}<br> Username : {email}<br> Password: {password}<br><br> Thank You for registration.<br> Regards',
                'type' => 'patient',
                'status' => 'Active',
            ),
            '6' => array(
                'name' => 'Nurse Registration Confirmation',
                'message' => 'Dear {name},<br> You are registered to {company} as a New Nurse.<br><br> Your Details:<br> Url Link: {base_url}<br> Username : {email}<br> Password: {password}<br><br> Thank You for registration.<br> Regards',
                'type' => 'nurse',
                'status' => 'Active',
            ),
            '7' => array(
                'name' => 'Pharmacist Registration Confirmation',
                'message' => 'Dear {name},<br> You are registered to {company} as a New Pharmacist.<br><br> Your Details:<br> Url Link: {base_url}<br> Username : {email}<br> Password: {password}<br><br> Thank You for registration.<br> Regards',
                'type' => 'pharmacist',
                'status' => 'Active',
            ),
            '8' => array(
                'name' => 'Laboratorist Registration Confirmation',
                'message' => 'Dear {name},<br> You are registered to {company} as a New Laboratorist.<br><br> Your Details:<br> Url Link: {base_url}<br> Username : {email}<br> Password: {password}<br><br> Thank You for registration.<br> Regards',
                'type' => 'laboratorist',
                'status' => 'Active',
            ),
            '9' => array(
                'name' => 'Accountant Registration Confirmation',
                'message' => 'Dear {name},<br> You are registered to {company} as a New Accountant.<br><br> Your Details:<br> Url Link: {base_url}<br> Username : {email}<br> Password: {password}<br><br> Thank You for registration.<br> Regards',
                'type' => 'accountant',
                'status' => 'Active',
            ),
            '10' => array(
                'name' => 'Receptionist Registration Confirmation',
                'message' => 'Dear {name},<br> You are registered to {company} as a New Receptionist.<br><br> Your Details:<br> Url Link: {base_url}<br> Username : {email}<br> Password: {password}<br><br> Thank You for registration.<br> Regards',
                'type' => 'receptionist',
                'status' => 'Active',
            ),
            '11' => array(
                'name' => 'Appointment Remainder email to patient',
                'message' => 'Dear {name}, REMAINDER!!<br> You have an appointment with {doctorname} on {appoinmentdate} at {time_slot} . For more information contact with {hospital_name} Regards',
                'type' => 'appoinment_remainder',
                'status' => 'Active',
            ),
            '12' => array(
                'name' => 'Send Patient Appointment confirmation to Doctor',
                'message' => 'Dear {name}, <br> {patientname} creates a appointment  with you on {appoinmentdate} at {time_slot} . Please See details on your login panel. <br> Regards',
                'type' => 'appoinment_creation_to_doctor',
                'status' => 'Active',
            ),
            '13' => array(
                'name' => 'Appointment Cancellation',
                'message' => '<p>Dear {name}, Your appointment with {doctorname} on {appoinmentdate} at {time_slot} has been cancelled. For more information contact with {hospital_name} Regards</p>',
                'type' => 'appointment_cancel',
                'status' => 'Active',
            ),
            '14' => array(
                'name' => 'Appointment Update',
                'message' => '<p>Dear {name}, Your appointment with {doctorname} on {appoinmentdate} at {time_slot} has been updated. For more information contact with {hospital_name} Regards</p>',
                'type' => 'appointment_update',
                'status' => 'Active',
            ),
            '15' => array(
                'name' => 'Doctor Appointment Update',
                'message' => '<p>Dear {name}, Your appointment with {patientname} on {appoinmentdate} at {time_slot} has been updated. For more information contact with {hospital_name} Regards</p>',
                'type' => 'doctor_appointment_update',
                'status' => 'Active',
            )
            ,
            '16' => array(
                'name' => 'Doctor Appointment Cancellation',
                'message' => '<p>Dear {name}, Your appointment with {patientname} on {appoinmentdate} at {time_slot} has been cancelled. For more information contact with {hospital_name} Regards</p>',
                'type' => 'doctor_appointment_cancel',
                'status' => 'Active',
            )
        );

        foreach ($data as $row) {
            $data1 = array();
            $data1 = array(
                'name' => $row['name'],
                'message' => $row['message'],
                'type' => $row['type'],
                'status' => $row['status'],
                'hospital_id' => $hospital_id
            );
            $this->db->insert('autoemailtemplate', $data1);
        }
    }

    function getHospitalByIonId($id) {
        $this->db->where('ion_user_id', $id);
        $query = $this->db->get('hospital');
        return $query->row();
    }

    function getHospitalInfo($searchTerm) {
        if (!empty($searchTerm)) {
            $this->db->select('*');

            $this->db->group_start()
                ->like('name', $searchTerm)
                ->or_like('id', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('hospital');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');

            $this->db->limit(10);
            $fetched_records = $this->db->get('hospital');
            $users = $fetched_records->result_array();
        }

        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . ')');
        }
        return $data;
    }

    function addHospitalPayment($data) {
        $this->db->insert('hospital_payment', $data);
    }

    function addHospitalDeposit($data) {
        return $this->db->insert('hospital_deposit', $data);
    }

    function updateHospitalPayment($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('hospital_payment', $data);
    }

    function updateHospitalPaymentByHospitalId($id, $data) {
        $this->db->where('hospital_user_id', $id);
        $this->db->update('hospital_payment', $data);
    }

    function getHospitalPaymentByHospitalId($id) {
        $this->db->where('hospital_user_id', $id);
        return $this->db->get('hospital_payment')->row();
    }

    function getHospitalPaymentById($id) {
        $this->db->where('id', $id);
        return $this->db->get('hospital_payment')->row();
    }

    function thisMonthlyDepositCount() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if ($hospital_payment->package_duration == 'monthly') {
                    $total[] = 1;
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisYearlyDepositCount() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if ($hospital_payment->package_duration == 'yearly') {
                    $total[] = 1;
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function getPaymentPerMonthThisYear() {


        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->add_date_stamp)) {

                if (date('m', $q->add_date_stamp) == '01') {
                    $total['january'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '02') {
                    $total['february'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '03') {
                    $total['march'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '04') {
                    $total['april'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '05') {
                    $total['may'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '06') {
                    $total['june'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '07') {
                    $total['july'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '08') {
                    $total['august'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '09') {
                    $total['september'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '10') {
                    $total['october'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '11') {
                    $total['november'][] = $q->deposited_amount;
                }
                if (date('m', $q->add_date_stamp) == '12') {
                    $total['december'][] = $q->deposited_amount;
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

    function thisMonthlyDeposit() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if ($hospital_payment->package_duration == 'monthly') {
                    $total[] = $q->deposited_amount;
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisYearlyDeposit() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('m/Y', time()) == date('m/Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if ($hospital_payment->package_duration == 'yearly') {
                    $total[] = $q->deposited_amount;
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisDayMonthlyPayment() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('d/m/Y', time()) == date('d/m/Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if ($hospital_payment->package_duration == 'monthly') {
                    $total[] = $q->deposited_amount;
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisDayYearlyPayment() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('d/m/Y', time()) == date('d/m/Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if ($hospital_payment->package_duration == 'yearly') {
                    $total[] = $q->deposited_amount;
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisYearYearlyPayment() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if (!empty($hospital_payment)) {
                    if ($hospital_payment->package_duration == 'yearly') {
                        $total[] = $q->deposited_amount;
                    }
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function thisYearMonthlyPayment() {

        $query = $this->db->get('hospital_deposit')->result();
        $total = array();
        foreach ($query as $q) {
            if (date('Y', time()) == date('Y', $q->add_date_stamp)) {
                $hospital_payment = $this->db->get_where('hospital_payment', array('id' => $q->payment_id))->row();
                if (!empty($hospital_payment)) {
                    if ($hospital_payment->package_duration == 'monthly') {
                        $total[] = $q->deposited_amount;
                    }
                }
            }
        }
        if (!empty($total)) {
            return array_sum($total);
        } else {
            return 0;
        }
    }

    function getHospitalDepositById($id) {
        return $this->db->where('id', $id)->get('hospital_deposit')->row();
    }

    function getHospitalPaymentDeposits() {
        return $this->db->order_by('date', 'desc')->get('hospital_deposit')->result();
    }
    function getHospitalPaymentDepositsReportBySubscription($subscription) {
        if($subscription=='new'){
            return $this->db->order_by('date', 'desc')->where('deposited_amount_id')->get('hospital_deposit')->result();
        }else{
            return $this->db->order_by('date', 'desc')->where('deposited_amount_id IS NOT NULL')->get('hospital_deposit')->result();
        }
        
    }
    function getHospitalPaymentDepositsReport() {
        return $this->db->order_by('date', 'desc')->get('hospital_deposit')->result();
    }
    function getHospitalDepositByDate($date_from, $date_to) {
        return $this->db->order_by('date', 'desc')->where('add_date_stamp >=', $date_from)
                        ->where('add_date_stamp <=', $date_to)
                        ->get('hospital_deposit')->result();
    }
    function getHospitalDepositByDateBySubscription($date_from, $date_to,$subscription) {
        if($subscription=='new'){
            return $this->db->order_by('date', 'desc')
                            ->where('add_date_stamp >=', $date_from) 
                            ->where('add_date_stamp <=', $date_to)
                            ->where('deposited_amount_id')
                            ->get('hospital_deposit')->result();
           
        }else{
            return $this->db->order_by('date', 'desc')
                            ->where('add_date_stamp >=', $date_from) 
                            ->where('add_date_stamp <=', $date_to)
                            ->where('deposited_amount_id IS NOT NULL')
                            ->get('hospital_deposit')->result();
           
        } 
       
    }
 
    function getHospitalDepositByDateByCountry($date_from, $date_to, $country) {

        return $this->db->select("*")
                        ->order_by('date', 'desc')
                        ->where('add_date_stamp >=', $date_from)
                        ->where('add_date_stamp <=', $date_to)
                        ->from('hospital_deposit')
                        ->where('country', $country)
                        ->join('hospital', 'hospital_deposit.hospital_user_id = hospital.id')->get()->result();
    }
    function getHospitalDepositByDateByCountryBySubscription($date_from, $date_to, $country,$subscription) {

        if($subscription=='new'){
            return $this->db->select("*")
                        ->order_by('date', 'desc')
                        ->where('add_date_stamp >=', $date_from)
                        ->where('add_date_stamp <=', $date_to)
                        ->where('deposited_amount_id')
                        ->from('hospital_deposit')
                        ->where('country', $country)
                        ->join('hospital', 'hospital_deposit.hospital_user_id = hospital.id')->get()->result();
           
        }else{
            return $this->db->select("*")
            ->order_by('date', 'desc')
            ->where('add_date_stamp >=', $date_from)
            ->where('add_date_stamp <=', $date_to)
            ->where('deposited_amount_id IS NOT NULL')
            ->from('hospital_deposit')
            ->where('country', $country)
            ->join('hospital', 'hospital_deposit.hospital_user_id = hospital.id')->get()->result();
           
        } 
       
        
    }

    function getHospitalDepositByCountry($country) {
        return $this->db->select("*")
                        ->order_by('date', 'desc')
                        ->from('hospital_deposit')
                        ->where('country', $country)
                        ->join('hospital', 'hospital_deposit.hospital_user_id = hospital.id')->get()->result();
    }
    
    function getHospitalDepositByCountryBySubscription($country,$subscription) {
        if($subscription=='new'){
            return $this->db->select("*")
                        ->order_by('date', 'desc')
                        ->where('deposited_amount_id')
                        ->from('hospital_deposit')
                        ->where('country', $country)
                        ->join('hospital', 'hospital_deposit.hospital_user_id = hospital.id')->get()->result();
        }else{
            return $this->db->select("*")
                        ->order_by('date', 'desc')
                        ->where('deposited_amount_id IS NOT NULL')
                        ->from('hospital_deposit')
                        ->where('country', $country)
                        ->join('hospital', 'hospital_deposit.hospital_user_id = hospital.id')->get()->result();
        }
        
    }
    
    
    function getHospitalFromWebsite() {
        $this->db->where('from_where', 'Website');
        $query = $this->db->get('hospital');
        return $query->result();
    }
    
    function updateUsers($id, $data) {

        $this->db->where('id', $id);
        $this->db->update('users', $data);
    }
    
    
    

}
