<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Diagnosis_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertDiagnosis($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('diagnosis', $data2);
    }

    function getDiagnosis()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('diagnosis');
        return $query->result();
    }

    function getDiagnosisById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('diagnosis');
        return $query->row();
    }
    function getDiagnosisByStatus()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'active');
        $query = $this->db->get('diagnosis');
        return $query->result();
    }
    function updateDiagnosis($id, $data)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->update('diagnosis', $data);
    }

    function delete($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->delete('diagnosis');
    }

    function getDiagnosisBysearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->like('id', $search);
        $this->db->or_like('name', $search);
        $this->db->or_like('code', $search);
        $query = $this->db->get('diagnosis');
        return $query->result();
    }

    function getDiagnosisByLimit($limit, $start, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->limit($limit, $start);
        $query = $this->db->get('diagnosis');
        return $query->result();
    }

    function getDiagnosisByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->like('id', $search);

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }


        $this->db->or_like('name', $search);
        $this->db->or_like('code', $search);

        $this->db->limit($limit, $start);
        $query = $this->db->get('diagnosis');
        return $query->result();
    }

    function getDiagnosisWithoutSearch($order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('diagnosis');
        return $query->result();
    }


    function getDiagnosisInfo($searchTerm)
    {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()
                ->like('name', $searchTerm)
                ->or_like('id', $searchTerm)
                ->or_like('code', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('diagnosis');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('diagnosis');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . '- ' . lang('code') . ': ' . $user['code'] . ')');
        }
        return $data;
    }
}
