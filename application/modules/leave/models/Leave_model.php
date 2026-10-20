<?php

class Leave_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function insertLeave($data) {
        $this->db->insert('leaves', $data);
    }
    
    public function updateLeave($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('leaves', $data);
    }
    
    public function deleteLeave($id) {
        $this->db->where('id', $id);
        $this->db->delete('leaves');
    }
    
    public function getLeaveById($id) {
        $this->db->where('id', $id);
        return $this->db->get('leaves')->row();
    }
    
    function getLeave() {
        if($this->ion_auth->in_group(array('admin'))) {
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->order_by('date', 'asc');
            $query = $this->db->get('leaves');
            return $query->result();
        } else {
            $staff = $this->ion_auth->user()->row()->id;
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('staff', $staff);
            $this->db->order_by('date', 'asc');
            $query = $this->db->get('leaves');
            return $query->result();
        }
        
    }

    function getLeaveBySearch($search) {
        if($this->ion_auth->in_group(array('admin'))) {
            $this->db->order_by('date', 'asc');
            $query = $this->db->select('*')
                    ->from('leaves')
                    ->where('hospital_id', $this->session->userdata('hospital_id'))
                    ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
                    ->get();
            ;
            return $query->result();
        } else {
            $staff = $this->ion_auth->user()->row()->id;
            $this->db->order_by('date', 'asc');
            $query = $this->db->select('*')
                    ->from('leaves')
                    ->where('hospital_id', $this->session->userdata('hospital_id'))
                    ->where('staff', $staff)
                    ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
                    ->get();
            ;
            return $query->result();
        }
    }

    function getLeaveByLimit($limit, $start) {
        if($this->ion_auth->in_group(array('admin'))) {
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->order_by('date', 'asc');
            $this->db->limit($limit, $start);
            $query = $this->db->get('leaves');
            return $query->result();
        } else {
            $staff = $this->ion_auth->user()->row()->id;
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('staff', $staff);
            $this->db->order_by('date', 'asc');
            $this->db->limit($limit, $start);
            $query = $this->db->get('leaves');
            return $query->result();
        }
        
    }

    function getLeaveByLimitBySearch($limit, $start, $search) {
        if($this->ion_auth->in_group(array('admin'))) {
            $this->db->order_by('date', 'asc');
            $this->db->limit($limit, $start);
            $query = $this->db->select('*')
                    ->from('leaves')
                    ->where('hospital_id', $this->session->userdata('hospital_id'))
                    ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
                    ->get();
            ;
            return $query->result();
        } else {
            $staff = $this->ion_auth->user()->row()->id;
            $this->db->order_by('date', 'asc');
            $this->db->limit($limit, $start);
            $query = $this->db->select('*')
                    ->from('leaves')
                    ->where('hospital_id', $this->session->userdata('hospital_id'))
                    ->where('staff', $staff)
                    ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
                    ->get();
            ;
            return $query->result();
        }
        
    }
    
    function getLeaveType() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('leave_type');
        return $query->result();
    }

    function getLeaveTypeBySearch($search) {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
                ->from('leave_type')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    function getLeaveTypeByLimit($limit, $start) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('leave_type');
        return $query->result();
    }

    function getLeaveTypeByLimitBySearch($limit, $start, $search) {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('leave_type')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }
    
    public function addLeaveType($data) {
        $this->db->insert('leave_type', $data);
    }
    
    public function updateLeaveType($id, $data) {
        $this->db->where('id',  $id);
        $this->db->update('leave_type', $data);
    }
    
    public function deleteLeaveType($id) {
        $this->db->where('id', $id);
        $this->db->delete('leave_type');
    }
    
    public function getLeaveTypeById($id) {
        $this->db->where('id', $id);
        return $this->db->get('leave_type')->row();
    }
    
    function getStaffinfoWithAddNewOption($searchTerm) {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()->like('name', $searchTerm)->or_like('id', $searchTerm)->group_end();
            $fetched_records1 = $this->db->get('accountant')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()->like('name', $searchTerm)->or_like('id', $searchTerm)->group_end();
            $fetched_records2 = $this->db->get('laboratorist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()->like('name', $searchTerm)->or_like('id', $searchTerm)->group_end();
            $fetched_records3 = $this->db->get('receptionist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()->like('name', $searchTerm)->or_like('id', $searchTerm)->group_end();
            $fetched_records4 = $this->db->get('pharmacist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()->like('name', $searchTerm)->or_like('id', $searchTerm)->group_end();
            $fetched_records5 = $this->db->get('nurse')->result_array();
            $users = array_merge($fetched_records1, $fetched_records2, $fetched_records3, $fetched_records4, $fetched_records5);
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(2);
            $fetched_records1 = $this->db->get('accountant')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(2);
            $fetched_records2 = $this->db->get('laboratorist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(2);
            $fetched_records3 = $this->db->get('receptionist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(2);
            $fetched_records4 = $this->db->get('pharmacist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(2);
            $fetched_records5 = $this->db->get('nurse')->result_array();
            $users = array_merge($fetched_records1, $fetched_records2, $fetched_records3, $fetched_records4, $fetched_records5);
        }
        // Initialize Array with fetched data
        $data = array();
       
        foreach ($users as $user) {
            $data[] = array("id" => $user['ion_user_id'], "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . ')');
        }
        return $data;
    }
}

