<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Meeting_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function insertMeeting($data) {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('meeting', $data2);
    }

    function getMeeting() {
        if ($this->ion_auth->in_group('Doctor')) {
            $this->db->where('doctor_ion_id', $this->ion_auth->get_user_id());
        } elseif ($this->ion_auth->in_group('Patient')) {
            $this->db->where('patient_ion_id', $this->ion_auth->get_user_id());
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function getMeetingBySearch($search) {
        if ($this->ion_auth->in_group('Doctor')) {
            $this->db->where('doctor_ion_id', $this->ion_auth->get_user_id());
        } elseif ($this->ion_auth->in_group('Patient')) {
            $this->db->where('patient_ion_id', $this->ion_auth->get_user_id());
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $s = $this->db->escape_like_str($search);
        $this->db->where("(id LIKE '%" . $s . "%' OR patientname LIKE '%" . $s . "%' OR doctorname LIKE '%" . $s . "%')", NULL, FALSE);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function getMeetingByLimit($limit, $start) {
        if ($this->ion_auth->in_group('Doctor')) {
            $this->db->where('doctor_ion_id', $this->ion_auth->get_user_id());
        } elseif ($this->ion_auth->in_group('Patient')) {
            $this->db->where('patient_ion_id', $this->ion_auth->get_user_id());
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function getMeetingByLimitBySearch($limit, $start, $search) {
        if ($this->ion_auth->in_group('Doctor')) {
            $this->db->where('doctor_ion_id', $this->ion_auth->get_user_id());
        } elseif ($this->ion_auth->in_group('Patient')) {
            $this->db->where('patient_ion_id', $this->ion_auth->get_user_id());
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $s = $this->db->escape_like_str($search);
        $this->db->where("(id LIKE '%" . $s . "%' OR patientname LIKE '%" . $s . "%' OR doctorname LIKE '%" . $s . "%')", NULL, FALSE);
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function getMeetingForCalendar() {
        if ($this->ion_auth->in_group('Doctor')) {
            $this->db->where('doctor_ion_id', $this->ion_auth->get_user_id());
        } elseif ($this->ion_auth->in_group('Patient')) {
            $this->db->where('patient_ion_id', $this->ion_auth->get_user_id());
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function getMeetingByDoctor($doctor) {
        $this->db->order_by('id', 'desc');
        $this->db->where('doctor', $doctor);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function getMeetingByPatient($patient) {
        $this->db->order_by('id', 'desc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $patient);
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function getMeetingById($id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('meeting');
        return $query->row();
    }

    function getMeetingByZoomMeetingId($id) {
        $this->db->where('meeting_id', $id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('meeting');
        return $query->row();
    }

    function getMeetingByDate($date_from, $date_to) {
        $this->db->select('*');
        $this->db->from('meeting');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getMeetingByDoctorByToday($doctor_id) {
        $today = strtotime(date('Y-m-d'));
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor_id);
        $this->db->where('date', $today);
        $query = $this->db->get('meeting');
        return $query->result();
    }

    function updateMeeting($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('meeting', $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('meeting');
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

    function getRequestMeetingBySearchByDoctor($doctor, $search) {
        $this->db->order_by('id', 'desc');
        $s = $this->db->escape_like_str($search);
        $query = $this->db->select('*')
                ->from('meeting')
                ->where('status', 'Requested')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where('doctor', $doctor)
                ->where("(id LIKE '%" . $s . "%' OR patientname LIKE '%" . $s . "%' OR doctorname LIKE '%" . $s . "%')", NULL, FALSE)
                ->get();
        return $query->result();
    }

    function getMeetingSettingsById($doctor_ion_id) {
        if ($this->ion_auth->in_group('Doctor')) {
            $this->db->where('ion_user_id', $this->ion_auth->get_user_id());
        } else {
            $this->db->where('ion_user_id', $doctor_ion_id);
        }
        $query = $this->db->get('meeting_settings');
        return $query->row();
    }

    function addMeetingSettings($data) {
        $data1 = array('ion_user_id' => $this->ion_auth->get_user_id());
        $data2 = array_merge($data, $data1);
        $query = $this->db->insert('meeting_settings', $data2);
    }

    function updateMeetingSettings($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('meeting_settings', $data);
    }

}
