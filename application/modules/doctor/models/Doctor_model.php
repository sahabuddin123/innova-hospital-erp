<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Doctor_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertDoctor($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('doctor', $data2);
    }

    function getDoctor()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getDoctorWithoutSearch($order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getLimit()
    {
        $current = $this->db->get_where('doctor', array('hospital_id' => $this->hospital_id, 'active' => 1))->num_rows();
        $limit = $this->db->get_where('hospital', array('id' => $this->hospital_id))->row()->d_limit;
        return $limit - $current;
    }

    function getDoctorBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('doctor')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('active', 1)
            ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR name_en LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' OR department_name LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }

    function getDoctorByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getDoctorByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('doctor')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('active', 1)
            ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR name_en LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' OR department_name LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();

        return $query->result();
    }

    function getDoctorsFiltered($limit = -1, $start = 0, $search = null, $order = 'id', $dir = 'desc', $department = null, $status = null)
    {
        $this->db->select('*');
        $this->db->from('doctor');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));

        if (!empty($status) && $status !== 'all') {
            if ($status === 'active') {
                $this->db->where('active', 1);
            } elseif ($status === 'inactive') {
                $this->db->where('active', 0);
            }
        }

        if (!empty($department) && $department !== 'all') {
            $this->db->where('department', $department);
        }

        if (!empty($search)) {
            $s = $this->db->escape_like_str($search);
            $this->db->where("(id LIKE '%" . $s . "%' OR name LIKE '%" . $s . "%' OR name_en LIKE '%" . $s . "%' OR designation LIKE '%" . $s . "%' OR degrees LIKE '%" . $s . "%' OR specialization LIKE '%" . $s . "%' OR phone LIKE '%" . $s . "%' OR phone_secondary LIKE '%" . $s . "%' OR email LIKE '%" . $s . "%' OR department_name LIKE '%" . $s . "%' OR chamber LIKE '%" . $s . "%')", NULL, FALSE);
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

    function getDoctorsFilteredCount($search = null, $department = null, $status = null)
    {
        $this->db->from('doctor');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));

        if (!empty($status) && $status !== 'all') {
            if ($status === 'active') {
                $this->db->where('active', 1);
            } elseif ($status === 'inactive') {
                $this->db->where('active', 0);
            }
        }

        if (!empty($department) && $department !== 'all') {
            $this->db->where('department', $department);
        }

        if (!empty($search)) {
            $s = $this->db->escape_like_str($search);
            $this->db->where("(id LIKE '%" . $s . "%' OR name LIKE '%" . $s . "%' OR name_en LIKE '%" . $s . "%' OR designation LIKE '%" . $s . "%' OR degrees LIKE '%" . $s . "%' OR specialization LIKE '%" . $s . "%' OR phone LIKE '%" . $s . "%' OR phone_secondary LIKE '%" . $s . "%' OR email LIKE '%" . $s . "%' OR department_name LIKE '%" . $s . "%' OR chamber LIKE '%" . $s . "%')", NULL, FALSE);
        }

        return $this->db->count_all_results();
    }

    // Historical records safety: getDoctorById must return any doctor regardless of active status
    function getDoctorById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('doctor');
        return $query->row();
    }

    function getDoctorByIonUserId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('ion_user_id', $id);
        $query = $this->db->get('doctor');
        return $query->row();
    }

    function updateDoctor($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('doctor', $data);
    }

    function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('doctor');
    }

    function updateIonUser($username, $email, $password, $ion_user_id)
    {
        $uptade_ion_user = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $this->db->where('id', $ion_user_id);
        $this->db->update('users', $uptade_ion_user);
    }

    function getDoctorInfo($searchTerm)
    {
        if (!empty($searchTerm)) {
            $s = $this->db->escape_like_str($searchTerm);
            $query = $this->db->select('*')
                ->from('doctor')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where('active', 1)
                ->where("(id LIKE '%" . $s . "%' OR name LIKE '%" . $s . "%' OR name_en LIKE '%" . $s . "%' OR designation LIKE '%" . $s . "%' OR degrees LIKE '%" . $s . "%' OR department_name LIKE '%" . $s . "%')", NULL, FALSE)
                ->get();
            $users = $query->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('active', 1);
            $this->db->limit(10);
            $fetched_records = $this->db->get('doctor');
            $users = $fetched_records->result_array();
        }

        if ($this->ion_auth->in_group(array('Doctor'))) {
            $doctor_ion_id = $this->ion_auth->get_user_id();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('ion_user_id', $doctor_ion_id);
            $this->db->where('active', 1);
            $fetched_records = $this->db->get('doctor');
            $users = $fetched_records->result_array();
        }

        // Initialize Array with English text
        $data = array();
        foreach ($users as $user) {
            $doc_name = !empty($user['name_en']) ? $user['name_en'] : $user['name'];
            $sub = !empty($user['designation']) ? ' - ' . $user['designation'] : (!empty($user['department_name']) ? ' - ' . $user['department_name'] : '');
            $data[] = array("id" => $user['id'], "text" => $doc_name . $sub . ' (ID: ' . $user['id'] . ')');
        }
        return $data;
    }

    function getDoctorWithAddNewOption($searchTerm)
    {
        if (!empty($searchTerm)) {
            $s = $this->db->escape_like_str($searchTerm);
            $query = $this->db->select('*')
                ->from('doctor')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where('active', 1)
                ->where("(id LIKE '%" . $s . "%' OR name LIKE '%" . $s . "%' OR name_en LIKE '%" . $s . "%' OR designation LIKE '%" . $s . "%' OR degrees LIKE '%" . $s . "%' OR department_name LIKE '%" . $s . "%')", NULL, FALSE)
                ->get();
            $users = $query->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('active', 1);
            $this->db->limit(10);
            $fetched_records = $this->db->get('doctor');
            $users = $fetched_records->result_array();
        }

        if ($this->ion_auth->in_group(array('Doctor'))) {
            $doctor_ion_id = $this->ion_auth->get_user_id();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('ion_user_id', $doctor_ion_id);
            $this->db->where('active', 1);
            $fetched_records = $this->db->get('doctor');
            $users = $fetched_records->result_array();
        }

        // Initialize Array with fetched data in English
        $data = array();
        $data[] = array("id" => 'add_new', "text" => lang('add_new'));
        foreach ($users as $user) {
            $doc_name = !empty($user['name_en']) ? $user['name_en'] : $user['name'];
            $sub = !empty($user['designation']) ? ' - ' . $user['designation'] : (!empty($user['department_name']) ? ' - ' . $user['department_name'] : '');
            $data[] = array("id" => $user['id'], "text" => $doc_name . $sub . ' (ID: ' . $user['id'] . ')');
        }
        return $data;
    }

    function getDoctorByHospital($hospital_id)
    {
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('active', 1);
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getDoctorBySearchByDepartment($search, $order, $dir, $department)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('doctor')
            ->where('department', $department)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('active', 1)
            ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR name_en LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' OR department_name LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();
        return $query->result();
    }

    function getDoctorByLimitByDepartment($limit, $start, $order, $dir, $department)
    {
        $this->db->where('department', $department);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getDoctorByLimitBySearchByDepartment($limit, $start, $search, $order, $dir, $department)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('doctor')
            ->where('department', $department)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('active', 1)
            ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR name_en LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' OR department_name LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();

        return $query->result();
    }

    function getDoctorWithoutSearchByDepartment($order, $dir, $department)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('department', $department);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getDoctorVisitByDoctorId($id)
    {
        $this->db->where('doctor_id', $id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('doctor_visit');
        return $query->result();
    }

    public function updateAvailability($doctor_id, $available)
    {
        $this->db->set('available', $available);
        $this->db->where('id', $doctor_id);
        return $this->db->update('doctor');
    }
}
