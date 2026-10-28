<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Advice_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertAdvice($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('advice', $data2);
    }

    function getAdvice()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('advice');
        return $query->result();
    }

    function getAdviceById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('advice');
        return $query->row();
    }
    function getAdviceByStatus()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'active');
        $query = $this->db->get('advice');
        return $query->result();
    }
    function updateAdvice($id, $data)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->update('advice', $data);
    }

    function delete($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->delete('advice');
    }

    function getAdviceBysearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->like('id', $search);
        $this->db->or_like('name', $search);
        // $this->db->or_like('code', $search);
        $query = $this->db->get('advice');
        return $query->result();
    }

    function getAdviceByLimit($limit, $start, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->limit($limit, $start);
        $query = $this->db->get('advice');
        return $query->result();
    }

    function getAdviceByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->like('id', $search);

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }


        $this->db->or_like('name', $search);
        // $this->db->or_like('code', $search);

        $this->db->limit($limit, $start);
        $query = $this->db->get('advice');
        return $query->result();
    }

    function getAdviceWithoutSearch($order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('advice');
        return $query->result();
    }


    function getAdviceInfo($searchTerm)
    {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $s = $this->db->escape_like_str($searchTerm);
            $this->db->where("(name LIKE '%" . $s . "%' OR id LIKE '%" . $s . "%')", NULL, FALSE);
            $fetched_records = $this->db->get('advice');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('advice');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['name'] );
        }
        return $data;
    }
}
