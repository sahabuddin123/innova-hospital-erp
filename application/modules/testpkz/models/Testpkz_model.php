<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Testpkz_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function insertTestpkz($data) {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('testpkz', $data2);
    }

    function getTestpkz() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('testpkz');
        return $query->result();
    }

    function getTestpkzById($id) {
        // $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('testpkz');
        return $query->row();
    }

    function updateTestpkz($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('testpkz', $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('testpkz');
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
    function getTestpkzBySearch($search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
                ->from('testpkz')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }
    function getTestpkzWithoutSearch($order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('testpkz');
        return $query->result();
    }
    function getTestpkzByLimitBySearch($limit, $start, $search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('testpkz')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }
    function getTestpkzByLimit($limit, $start, $order, $dir) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('testpkz');
        return $query->result();
    }
    function getTestpkzByAvailablity($searchTerm) {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()
                ->like('name', $searchTerm)
                ->or_like('id', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('testpkz');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('testpkz');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['name']);
        }
        return $data;
    }
    function getTestpkzByAvailablityOnlicecenter($hospital_id, $searchTerm) {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $hospital_id);
            $this->db->group_start()
                ->like('name', $searchTerm)
                ->or_like('id', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('testpkz');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $hospital_id);
            $this->db->limit(10);
            $fetched_records = $this->db->get('testpkz');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['name']);
        }
        return $data;
    }

    function getTestpkzBySearchBySuper($search, $hospital_id) {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
                ->from('testpkz')
                ->where('hospital_id', $hospital_id)
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        return $query->result();
    }
    function getTestpkzBySuper($hospital_id) {
        $this->db->where('hospital_id', $hospital_id);
        $query = $this->db->get('testpkz');
        return $query->result();
    }
    function getTestpkzByLimitBySearchBySuper($limit, $start, $search, $hospital_id) {
        $this->db->like('id', $search);
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('testpkz')
                ->where('hospital_id', $hospital_id)
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();

        return $query->result();
    }
    function getTestpkzByLimitBySuper($limit, $start, $hospital_id) {

        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $this->db->where('hospital_id', $hospital_id);
        $query = $this->db->get('testpkz');
        return $query->result();
    }
}