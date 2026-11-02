<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lab_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertLab($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('lab', $data2);
    }

    function getLab()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getMyLab()
    {
        if($this->ion_auth->in_group(array('Patient'))){
            $patient_ion_id = $this->ion_auth->get_user_id();
            $patient = $this->db->get_where('patient', array('ion_user_id' => $patient_ion_id))->row()->id;

        }
        $query = $this->db->select('*')
            ->from('lab')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('patient', $patient)
            ->get();
        return $query->result();
    }


    function getLabByTestStatus($status)
    {
        $query = $this->db->select('*')
            ->from('lab')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('test_status', $status)
            ->get();
        return $query->result();
    }

    function getLabByReportStatus($status)
    {
        $query = $this->db->select('*')
            ->from('lab')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where('status', $status)
            ->get();
        return $query->result();
    }

    function getLabWithoutSearch($order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('lab')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();

        return $query->result();
    }

    function getLabByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('lab')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();

        return $query->result();
    }

    function getTemplateCount($user_id, $category)
    {
        if (trim($user_id)) {
            $this->db->where('user', $user_id);
        }
        if (trim($category)) {
            $this->db->where('x', $category);
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('template');
        return $query->result();
    }

    function getTemplateWithoutSearch($order, $dir, $user_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if (trim($user_id)) {
            $this->db->where('user', $user_id);
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('template');
        return $query->result();
    }

    function getTemplateBySearch($search, $order, $dir, $user_id)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        if (trim($user_id)) {
            $this->db->where('user', $user_id);
        }

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('template');

        return $query->result();
    }

    function getTemplateByLimit($limit, $start, $order, $dir, $user_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        if (trim($user_id)) {
            $this->db->where('user', $user_id);
        }

        $this->db->limit($limit, $start);
        $query = $this->db->get('template');
        return $query->result();
    }

    function getTemplateByLimitBySearch($limit, $start, $search, $order, $dir, $user_id)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        if (trim($user_id)) {
            $this->db->where('user', $user_id);
        }

        $this->db->limit($limit, $start);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('template');

        return $query->result();
    }

    function changeTestStatus($id, $status, $done_by)
    {
        if ($status == 'done') {
            $data = array(
                'test_status' => "done",
                'test_status_date' => strtotime(date('Y-m-d H:i:s')),
                'status' => "pending",
                'done_by' => $done_by
            );
        } else {
            $data = array(
                'test_status' => "not_done",
                'test_status_date' => null,
                'status' => null,
                'done_by' => null
            );
        }
        $this->db->where("id", $id);
        $this->db->update("lab", $data);
    }

    function changeReportStatus($id, $status)
    {
        if ($status == 'completed') {
            $data = array(
                'status' => "completed",
                'delivery_status' => "pending",
            );
        } else {
            $data = array(
                'status' => $status,
                'delivery_status' => null,
            );
        }
        $this->db->where("id", $id);
        $this->db->update("lab", $data);
    }

    function changeDeliveryStatus($id, $data)
    {
        $this->db->where("id", $id);
        $this->db->update("lab", $data);
    }

    function getTestStatusLab($status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }


        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->or_where('test_status', 'not_done');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else  if (count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        $query = $this->db->get('lab');
        return $query->result();
    }

    function getTestStatusLabWithoutSearch($order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->or_where('test_status', 'not_done');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else  if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getTestStatusLabBySearch($search, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }

        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->or_where('test_status', 'not_done');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else  if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        $this->db->select('*');
        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where("(id LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getTestStatusLabByLimit($limit, $start, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->or_where('test_status', 'not_done');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else  if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getTestStatusLabByLimitBySearch($limit, $start, $search, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }

        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->or_where('test_status', 'not_done');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        $this->db->limit($limit, $start);
        $this->db->select('*');
        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where("(id LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabReport($status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', 'pending');
            $this->db->or_where('status', 'drafted');
            $this->db->or_where('status', 'completed');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabReportWithoutSearch($order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', 'pending');
            $this->db->or_where('status', 'drafted');
            $this->db->or_where('status', 'completed');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }



        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabReportBySearch($search, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }

        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', 'pending');
            $this->db->or_where('status', 'drafted');
            $this->db->or_where('status', 'completed');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        $this->db->select('*');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where("(id LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%' OR patient LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabReportByLimit($limit, $start, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));

        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', 'pending');
            $this->db->or_where('status', 'drafted');
            $this->db->or_where('status', 'completed');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }


        $this->db->limit($limit, $start);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabReportByLimitBySearch($limit, $start, $search, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }

        $this->db->where('test_status', 'done');
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', 'pending');
            $this->db->or_where('status', 'drafted');
            $this->db->or_where('status', 'completed');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('test_status', 'done');
            $this->db->where('status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }


        $this->db->limit($limit, $start);
        $this->db->select('*');
        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where("(id LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%' OR patient LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getDeliveryReport($status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('test_status', 'done');
        $this->db->where('status', 'completed');
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('delivery_status', 'pending');
            $this->db->or_where('delivery_status', 'delivered');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('delivery_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        $query = $this->db->get('lab');
        return $query->result();
    }

    function getDeliveryReportWithoutSearch($order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('test_status', 'done');
        $this->db->where('status', 'completed');
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('delivery_status', 'pending');
            $this->db->or_where('delivery_status', 'delivered');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('delivery_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }


        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getDeliveryReportBySearch($search, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('test_status', 'done');
        $this->db->where('status', 'completed');
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('delivery_status', 'pending');
            $this->db->or_where('delivery_status', 'delivered');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('delivery_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }

        $this->db->select('*');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where("(id LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%' OR patient LIKE '%" . $search . "%' OR patient IN (SELECT id FROM patient WHERE patient_id LIKE '%" . $search . "%') OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%' OR doctor_name LIKE '%" . $search . "%' OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getDeliveryReportByLimit($limit, $start, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('test_status', 'done');
        $this->db->where('status', 'completed');
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('delivery_status', 'pending');
            $this->db->or_where('delivery_status', 'delivered');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('delivery_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }


        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getDeliveryReportByLimitBySearch($limit, $start, $search, $order, $dir, $status, $category, $from, $to)
    {
        if ($category != 'all') {
            $this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        } else {
            //$this->db->where('payment_category', $category);
            $all_categories = $this->db->get('payment_category')->result();
            //$all_categories = $this->db->get_where('payment_category', array('payment_category', $category))->result_array();
            $array = array();
            foreach ($all_categories as $cat) {
                array_push($array, $cat->id);
            }
        }

        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('invoice_id', 'desc');
        }

        $this->db->where('test_status', 'done');
        $this->db->where('status', 'completed');
        if ($from) {
            $this->db->where('date >=', strtotime($from));
        }
        if ($to) {
            $this->db->where('date <=', strtotime($to));
        }
        if ($status == 'all') {
            $this->db->group_start();
            $this->db->where('delivery_status', 'pending');
            $this->db->or_where('delivery_status', 'delivered');
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where('delivery_status', $status);
            $this->db->group_end();
        }

        if ($category != 'all' && count($array) > 0) {
            $this->db->where_in('category_id', $array);
        } else if ($category != 'all' && count($array) == 0) {
            $this->db->where('category_id', 0);
        }


        $this->db->limit($limit, $start);
        $this->db->select('*');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where("(id LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%' OR patient LIKE '%" . $search . "%' OR patient IN (SELECT id FROM patient WHERE patient_id LIKE '%" . $search . "%') OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%' OR doctor_name LIKE '%" . $search . "%' OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('lab');
        return $query->row();
    }

    function getLabByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $id);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabByPatientIdByDate($id, $date_from, $date_to)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getLabByUserId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('user', $id);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getOtLabByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $id);
        $query = $this->db->get('ot_lab');
        return $query->result();
    }

    function getLabByPatientIdByStatus($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $this->db->where('status', 'unpaid');
        $query = $this->db->get('lab');
        return $query->result();
    }

    function updateLab($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('lab', $data);
    }

    function insertLabCategory($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('lab_category', $data2);
    }

    function getLabCategory()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('lab_category');
        return $query->result();
    }

    function getLabCategoryById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('lab_category');
        return $query->row();
    }

    function updateLabCategory($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('lab_category', $data);
    }

    function deleteLab($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('lab');
    }

    function deleteLabCategory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('lab_category');
    }

    function getLabByDoctor($doctor)
    {
        $this->db->select('*');
        $this->db->from('lab');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $query = $this->db->get();
        return $query->result();
    }

    function getLabByDate($date_from, $date_to)
    {
        $this->db->select('*');
        $this->db->from('lab');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getLabByDoctorDate($doctor, $date_from, $date_to)
    {
        $this->db->select('*');
        $this->db->from('lab');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function getLabByUserIdByDate($user, $date_from, $date_to)
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('*');
        $this->db->from('lab');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('user', $user);
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get();
        return $query->result();
    }

    function insertTemplate($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('template', $data2);
    }

    function getTemplate()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('template');
        return $query->result();
    }

    function updateTemplate($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('template', $data);
    }

    function getTemplateById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('template');
        return $query->row();
    }

    function getTemplateByCategoryId($category_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('category_id', $category_id);
        $query = $this->db->get('template');
        return $query->row();
    }

    function getTemplateByName($name)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('name', $name);
        $query = $this->db->get('template');
        return $query->row();
    }

    function deletetemplate($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('template');
    }

    function getLabByInvoice($invoice)
    {

        $this->db->where('invoice_id', $invoice);
        $query = $this->db->get('lab');
        return $query->result();
    }

    function getAllLabels($invoice, $category)
    {
        $this->db->where('invoice_id', $invoice);
        $this->db->where('category_id', $category);
        $result = $this->db->get('lab')->row();
        return $result;
    }

    function lastRowByLab()
    {
        return $this->db->where('hospital_id', $this->session->userdata('hospital_id'))
            ->order_by('id', "desc")->limit(1)->get('lab')->row();
    }
    function getLabByBedId($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('lab')->result();
    }
    function deleteLabByDiagnosticId($id)
    {
        $this->db->where('diagnostic_id', $id);
        $this->db->delete('lab');
    }
    function getLabByDiagnosticId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('diagnostic_id', $id);
        $query = $this->db->get('lab');
        return $query->row();
    }
    function getLabByPaymentProcedureId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('category_id', $id);
        $query = $this->db->get('lab');
        return $query->row();
    }
    function getDiagnosticByBedId($id)
    {
        return $this->db->where('alloted_bed_id', $id)
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->get('bed_diagnostic')->result();
    }

    /**
     * Dynamically calculates and renders Rayto RT-7600 hematology histogram curves
     * (WBC CURVE, PLT CURVE, RBC CURVE) based on patient numeric test results.
     */
    public function renderDynamicCbcReport($html)
    {
        if (empty($html)) {
            return $html;
        }

        // Check if report contains CBC/Haematology keywords or curve placeholders
        if (stripos($html, 'CURVE') === false && stripos($html, 'HAEMATOLOGY') === false && stripos($html, 'TOTAL WBC') === false) {
            return $html;
        }

        // Helper to extract numeric value from table cell
        $extractVal = function($paramRegex, $default = null) use ($html) {
            if (preg_match('/<td[^>]*>[^<]*?' . $paramRegex . '[^<]*?<\/td>\s*<td[^>]*>([\s\S]*?)<\/td>/i', $html, $m)) {
                $val = trim(strip_tags($m[1]));
                return $val !== '' ? $val : $default;
            }
            return $default;
        };

        $total_wbc = $extractVal('TOTAL\s+WBC(?:\s+COUNT)?', '10850');
        $neut_pct  = $extractVal('Neutrophils?', '52');
        $lymph_pct = $extractVal('Lymphocytes?', '41');
        $mono_pct  = $extractVal('Monocytes?', '4');
        $eos_pct   = $extractVal('Eosinophils?', '3');
        $baso_pct  = $extractVal('Basophils?', '0');

        $plt_count = $extractVal('TOTAL\s+PLATELET(?:\s+COUNT)?', '314000');
        $mpv       = $extractVal('MPV', '7.3');
        $pdw       = $extractVal('PDW', '13.7');

        $rbc_count = $extractVal('RBC\s+COUNT', '6.19');
        $mcv       = $extractVal('MCV', '72.9');
        $rdw_cv    = $extractVal('RDW\s*-\s*CV', '15.2');
        $rdw_sd    = $extractVal('RDW\s*-\s*SD', '42.4');

        $wbc_uri = $this->generateWbcCurve($total_wbc, $neut_pct, $lymph_pct, $mono_pct, $eos_pct, $baso_pct);
        $plt_uri = $this->generatePltCurve($plt_count, $mpv, $pdw);
        $rbc_uri = $this->generateRbcCurve($rbc_count, $mcv, $rdw_cv, $rdw_sd);

        // Replace WBC CURVE img
        $html = preg_replace(
            '/<img[^>]+alt=["\']WBC CURVE["\'][^>]*>/i',
            '<img src="' . $wbc_uri . '" alt="WBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />',
            $html
        );

        // Replace PLT CURVE img
        $html = preg_replace(
            '/<img[^>]+alt=["\']PLT CURVE["\'][^>]*>/i',
            '<img src="' . $plt_uri . '" alt="PLT CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />',
            $html
        );

        // Replace RBC CURVE img
        $html = preg_replace(
            '/<img[^>]+alt=["\']RBC CURVE["\'][^>]*>/i',
            '<img src="' . $rbc_uri . '" alt="RBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />',
            $html
        );

        return $html;
    }

    public function generateWbcCurve($total_wbc, $neut_pct, $lymph_pct, $mono_pct = 4, $eos_pct = 3, $baso_pct = 0)
    {
        $total_wbc = !empty($total_wbc) ? (float)str_replace([',', ' '], '', $total_wbc) : 7500;
        $neut_pct  = !empty($neut_pct)  ? (float)$neut_pct : 55;
        $lymph_pct = !empty($lymph_pct) ? (float)$lymph_pct : 35;
        $mid_pct   = ((float)$mono_pct + (float)$eos_pct + (float)$baso_pct);
        if ($mid_pct <= 0) $mid_pct = 10;

        $wbc_scale = max(0.75, min(1.30, $total_wbc / 8500));

        $h_lymph = max(14, min(58, ($lymph_pct / 65) * 48 * $wbc_scale));
        $h_neut  = max(16, min(62, ($neut_pct / 70) * 52 * $wbc_scale));
        $h_mid   = max(6,  min(28, ($mid_pct / 18) * 22 * $wbc_scale));

        $baseline = 72;
        $y1 = round($baseline - $h_lymph, 1);
        $y2 = round($baseline - $h_neut, 1);
        $yv = round($baseline - $h_mid, 1);

        $x1 = 34.0;
        $xv = 52.0;
        $x2 = 76.0;

        $path_d = sprintf(
            "M 12 72 L 18 72 C 24 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 66, 112 72 L 118 72 L 12 72 Z",
            $x1 - 5.0, $y1, $x1, $y1,
            $x1 + 6.0, $y1, $xv - 6.0, $yv, $xv, $yv,
            $xv + 6.0, $yv, $x2 - 6.0, $y2, $x2, $y2,
            $x2 + 7.0, $y2, $x2 + 20.0
        );

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">'
            . '<defs>'
            . '<pattern id="wbcHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse">'
            . '<line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>'
            . '<path d="' . $path_d . '" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>'
            . '<path d="' . $path_d . '" fill="url(#wbcHatchDynamic)"/>'
            . '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>'
            . '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">WBC CURVE</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function generatePltCurve($plt_count, $mpv, $pdw = 13.5)
    {
        $plt_count = !empty($plt_count) ? (float)str_replace([',', ' '], '', $plt_count) : 280000;
        $mpv       = !empty($mpv)       ? (float)$mpv : 8.5;
        $pdw       = !empty($pdw)       ? (float)$pdw : 13.5;

        $peak_x = max(18.0, min(36.0, 20.0 + (($mpv - 6.5) / 5.0) * 14.0));
        $plt_scale = max(0.55, min(1.35, $plt_count / 300000));
        $h_plt = max(16.0, min(63.0, 52.0 * $plt_scale));
        $peak_y = round(72.0 - $h_plt, 1);

        $tail_x = max(65.0, min(112.0, 75.0 + (($pdw - 12.0) / 6.0) * 18.0));

        $path_d = sprintf(
            "M 12 72 C 14 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 56, %0.1f 69 C %0.1f 71, 108 72, 118 72 L 12 72 Z",
            $peak_x - 3.5, $peak_y + 10.0, $peak_x, $peak_y,
            $peak_x + 5.0, $peak_y, $peak_x + 18.0, $tail_x,
            $tail_x + 12.0
        );

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">'
            . '<defs>'
            . '<pattern id="pltHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse">'
            . '<line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>'
            . '<path d="' . $path_d . '" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>'
            . '<path d="' . $path_d . '" fill="url(#pltHatchDynamic)"/>'
            . '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>'
            . '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">PLT CURVE</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function generateRbcCurve($rbc_count, $mcv, $rdw_cv = 13.5, $rdw_sd = 42.0)
    {
        $rbc_count = !empty($rbc_count) ? (float)$rbc_count : 5.0;
        $mcv       = !empty($mcv)       ? (float)$mcv : 85.0;
        $rdw_cv    = !empty($rdw_cv)    ? (float)$rdw_cv : 13.5;

        // Shift center based on MCV (microcytic < 76 shifts left, macrocytic > 94 shifts right)
        $center_x = max(42.0, min(86.0, 64.0 + ($mcv - 85.0) * 1.15));
        $half_width = max(13.0, min(26.0, 17.0 + ($rdw_cv - 13.0) * 1.5));

        $rbc_scale = max(0.65, min(1.30, $rbc_count / 5.2));
        $h_rbc = max(24.0, min(62.0, 54.0 * $rbc_scale));
        $peak_y = round(72.0 - $h_rbc, 1);

        $left_base  = max(10.0, round($center_x - $half_width * 1.6, 1));
        $right_base = min(120.0, round($center_x + $half_width * 1.6, 1));

        $path_d = sprintf(
            "M 10 72 L %0.1f 72 C %0.1f 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 72, %0.1f 72 L 120 72 L 10 72 Z",
            $left_base,
            $left_base + 5.0, $center_x - $half_width * 0.5, $peak_y, $center_x, $peak_y,
            $center_x + $half_width * 0.5, $peak_y, $right_base - 5.0, $right_base
        );

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">'
            . '<defs>'
            . '<pattern id="rbcHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse">'
            . '<line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>'
            . '<path d="' . $path_d . '" fill="#333d4b" stroke="#11161d" stroke-width="1.5"/>'
            . '<path d="' . $path_d . '" fill="url(#rbcHatchDynamic)"/>'
            . '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>'
            . '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">RBC CURVE</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

// ==========================================
    // PHASE 7: DIAGNOSTIC PERFORMANCE REPORTS
    // ==========================================

    public function resolveHospitalId($hospital_id = null)
    {
        if (!empty($hospital_id)) {
            return $hospital_id;
        }
        if ($this->session && $this->session->userdata('hospital_id')) {
            return $this->session->userdata('hospital_id');
        }
        return '98';
    }

    public function resolveReportPeriod($period_type = 'this_month', $date_from = null, $date_to = null, $selected_date = null)
    {
        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $today_end   = strtotime(date('Y-m-d 23:59:59'));

        switch ($period_type) {
            case 'today':
                return [
                    'start_time' => $today_start,
                    'end_time'   => $today_end,
                    'label'      => 'Today (' . date('d M Y') . ')',
                    'type'       => 'today'
                ];
            case 'yesterday':
                return [
                    'start_time' => strtotime('-1 day', $today_start),
                    'end_time'   => strtotime('-1 day', $today_end),
                    'label'      => 'Yesterday (' . date('d M Y', strtotime('-1 day')) . ')',
                    'type'       => 'yesterday'
                ];
            case 'this_week':
                $w_start = strtotime('monday this week 00:00:00');
                $w_end   = strtotime('sunday this week 23:59:59');
                return [
                    'start_time' => $w_start,
                    'end_time'   => $w_end,
                    'label'      => 'This Week (' . date('d M', $w_start) . ' - ' . date('d M Y', $w_end) . ')',
                    'type'       => 'this_week'
                ];
            case 'this_month':
                $m_start = strtotime(date('Y-m-01 00:00:00'));
                $m_end   = strtotime(date('Y-m-t 23:59:59'));
                return [
                    'start_time' => $m_start,
                    'end_time'   => $m_end,
                    'label'      => 'This Month (' . date('F Y') . ')',
                    'type'       => 'this_month'
                ];
            case 'last_month':
                $lm_start = strtotime(date('Y-m-01 00:00:00', strtotime('first day of last month')));
                $lm_end   = strtotime(date('Y-m-t 23:59:59', strtotime('last day of last month')));
                return [
                    'start_time' => $lm_start,
                    'end_time'   => $lm_end,
                    'label'      => 'Last Month (' . date('F Y', $lm_start) . ')',
                    'type'       => 'last_month'
                ];
            case 'this_year':
                $y_start = strtotime(date('Y-01-01 00:00:00'));
                $y_end   = strtotime(date('Y-12-31 23:59:59'));
                return [
                    'start_time' => $y_start,
                    'end_time'   => $y_end,
                    'label'      => 'This Year (' . date('Y') . ')',
                    'type'       => 'this_year',
                    'date_from'  => date('Y-01-01', $y_start),
                    'date_to'    => date('Y-12-31', $y_end)
                ];
            case 'last_year':
                $ly = date('Y') - 1;
                $ly_start = strtotime("{$ly}-01-01 00:00:00");
                $ly_end   = strtotime("{$ly}-12-31 23:59:59");
                return [
                    'start_time' => $ly_start,
                    'end_time'   => $ly_end,
                    'label'      => 'Last Year (' . $ly . ')',
                    'type'       => 'last_year',
                    'date_from'  => date('Y-01-01', $ly_start),
                    'date_to'    => date('Y-12-31', $ly_end)
                ];
            case 'custom':
            default:
                if (!empty($date_from) && !empty($date_to)) {
                    $s = strtotime($date_from . ' 00:00:00');
                    $e = strtotime($date_to . ' 23:59:59');
                    return [
                        'start_time' => $s,
                        'end_time'   => $e,
                        'label'      => date('d M Y', $s) . ' - ' . date('d M Y', $e),
                        'type'       => 'custom'
                    ];
                } elseif (!empty($selected_date)) {
                    $s = strtotime($selected_date . ' 00:00:00');
                    $e = strtotime($selected_date . ' 23:59:59');
                    return [
                        'start_time' => $s,
                        'end_time'   => $e,
                        'label'      => date('d M Y', $s),
                        'type'       => 'custom'
                    ];
                } else {
                    $m_start = strtotime(date('Y-m-01 00:00:00'));
                    $m_end   = strtotime(date('Y-m-t 23:59:59'));
                    return [
                        'start_time' => $m_start,
                        'end_time'   => $m_end,
                        'label'      => 'This Month (' . date('F Y') . ')',
                        'type'       => 'this_month'
                    ];
                }
        }
    }

    /**
     * 1. DIAGNOSTIC OVERVIEW DASHBOARD DATA
     * High-level operational executive overview with 11 KPI cards and 10 Chart.js datasets
     */
    public function getDiagnosticDashboardData($period_type = 'this_month', $selected_date = null, $date_from = null, $date_to = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $period = $this->resolveReportPeriod($period_type, $date_from, $date_to, $selected_date);
        $start_time = $period['start_time'];
        $end_time = $period['end_time'];

        // --- 1. CORE WORKFLOW COUNTS FROM LAB ---
        $this->db->reset_query();
        $this->db->select("COUNT(l.id) as total_tests_ordered,
                           COUNT(DISTINCT l.invoice_id) as total_invoices,
                           COUNT(DISTINCT l.patient) as total_patients_served,
                           COALESCE(SUM(CASE WHEN (l.status IN ('sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered') OR l.test_status = 'done') THEN 1 ELSE 0 END), 0) as samples_collected,
                           COALESCE(SUM(CASE WHEN (l.status = 'pending' AND (l.test_status IS NULL OR l.test_status != 'done')) THEN 1 ELSE 0 END), 0) as samples_pending,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'pending' OR l.status = 'waiting') THEN 1 ELSE 0 END), 0) as tests_in_progress,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as tests_completed,
                           COALESCE(SUM(CASE WHEN ((l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending')) AND (l.delivery_status != 'delivered' OR l.delivery_status IS NULL)) THEN 1 ELSE 0 END), 0) as reports_pending,
                           COALESCE(SUM(CASE WHEN ((l.signed_by IS NOT NULL AND l.signed_by != '' AND l.signed_by != '0') OR l.status IN ('delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as reports_verified,
                           COALESCE(SUM(CASE WHEN l.delivery_status = 'delivered' THEN 1 ELSE 0 END), 0) as reports_delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as cancelled_tests");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $kpi_row = $this->db->get()->row();

        // --- 2. REGISTERED PATIENTS IN PERIOD ---
        $this->db->reset_query();
        $this->db->select("COUNT(*) as registered_patients");
        $this->db->from('patient');
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('registration_time >=', $start_time);
        $this->db->where('registration_time <=', $end_time);
        $reg_pat_row = $this->db->get()->row();
        $registered_patients = intval($reg_pat_row ? $reg_pat_row->registered_patients : 0);

        // --- 3. INFORMATIONAL FINANCIAL INDICATORS ---
        $this->db->reset_query();
        $this->db->select("COALESCE(SUM(amount), 0) as gross_billing,
                           COALESCE(SUM(gross_total), 0) as net_billing");
        $this->db->from('payment');
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $fin_row = $this->db->get()->row();

        // Total Investigation items from payment_items
        $this->db->reset_query();
        $this->db->select("COUNT(*) as total_items");
        $this->db->from('payment_items');
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('created_at >=', $start_time);
        $this->db->where('created_at <=', $end_time);
        $item_row = $this->db->get()->row();
        $total_inv_items = intval($item_row ? $item_row->total_items : 0);
        if ($total_inv_items == 0 && $kpi_row) {
            $total_inv_items = intval($kpi_row->total_tests_ordered);
        }

        $summary = [
            'total_registered_patients' => $registered_patients,
            'total_patients_served'     => intval($kpi_row ? $kpi_row->total_patients_served : 0),
            'total_invoices'            => intval($kpi_row ? $kpi_row->total_invoices : 0),
            'total_investigation_items' => $total_inv_items,
            'total_tests_ordered'       => intval($kpi_row ? $kpi_row->total_tests_ordered : 0),
            'samples_collected'         => intval($kpi_row ? $kpi_row->samples_collected : 0),
            'samples_pending'           => intval($kpi_row ? $kpi_row->samples_pending : 0),
            'tests_in_progress'         => intval($kpi_row ? $kpi_row->tests_in_progress : 0),
            'tests_completed'           => intval($kpi_row ? $kpi_row->tests_completed : 0),
            'reports_pending'           => intval($kpi_row ? $kpi_row->reports_pending : 0),
            'reports_verified'          => intval($kpi_row ? $kpi_row->reports_verified : 0),
            'reports_delivered'         => intval($kpi_row ? $kpi_row->reports_delivered : 0),
            'cancelled_tests'           => intval($kpi_row ? $kpi_row->cancelled_tests : 0),
            'info_gross_billing'        => floatval($fin_row ? $fin_row->gross_billing : 0.0),
            'info_net_billing'          => floatval($fin_row ? $fin_row->net_billing : 0.0)
        ];

        // --- 4. CHARTS DATA ---
        // A. Daily Trends (last 14 days or current month days)
        $chart_days = [];
        $day_count = max(7, min(31, intval(($end_time - $start_time) / 86400) + 1));
        for ($d = 0; $d < $day_count; $d++) {
            $t = $start_time + ($d * 86400);
            if ($t > $end_time) break;
            $chart_days[date('Y-m-d', $t)] = [
                'label'              => date('d M', $t),
                'patients'           => 0,
                'tests_ordered'      => 0,
                'samples_collected'  => 0,
                'tests_completed'    => 0,
                'reports_delivered'  => 0,
                'cancelled'          => 0
            ];
        }

        // Daily Lab Orders & Workflow
        $this->db->reset_query();
        $this->db->select("FROM_UNIXTIME(l.date, '%Y-%m-%d') as dt,
                           COUNT(l.id) as orders,
                           COUNT(DISTINCT l.patient) as patients,
                           COALESCE(SUM(CASE WHEN (l.status IN ('sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered') OR l.test_status = 'done') THEN 1 ELSE 0 END), 0) as samples,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as completed,
                           COALESCE(SUM(CASE WHEN l.delivery_status = 'delivered' THEN 1 ELSE 0 END), 0) as delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as cancelled");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $this->db->group_by("FROM_UNIXTIME(l.date, '%Y-%m-%d')");
        $day_rows = $this->db->get()->result();

        foreach ($day_rows as $dr) {
            if (isset($chart_days[$dr->dt])) {
                $chart_days[$dr->dt]['tests_ordered']     = intval($dr->orders);
                $chart_days[$dr->dt]['patients']          = intval($dr->patients);
                $chart_days[$dr->dt]['samples_collected'] = intval($dr->samples);
                $chart_days[$dr->dt]['tests_completed']   = intval($dr->completed);
                $chart_days[$dr->dt]['reports_delivered'] = intval($dr->delivered);
                $chart_days[$dr->dt]['cancelled']         = intval($dr->cancelled);
            }
        }

        // B. Category-wise Distribution
        $this->db->reset_query();
        $this->db->select("COALESCE(pc.payment_category_name, c.category, 'General Diagnostics') as cat_name,
                           COUNT(l.id) as test_count");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $this->db->group_by("cat_name");
        $this->db->order_by("test_count", "DESC");
        $cat_dist = $this->db->get()->result();

        // C. Department-wise Distribution
        $this->db->reset_query();
        $this->db->select("COALESCE(dep.name, 'General Clinical') as dept_name,
                           COUNT(l.id) as test_count");
        $this->db->from('lab l');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $this->db->group_by("dept_name");
        $this->db->order_by("test_count", "DESC");
        $dept_dist = $this->db->get()->result();

        // D. Top 10 Most Ordered Tests
        $this->db->reset_query();
        $this->db->select("COALESCE(pc.category, l.category_name, 'Test') as test_name,
                           COUNT(l.id) as order_count");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $this->db->group_by("test_name");
        $this->db->order_by("order_count", "DESC");
        $this->db->limit(10);
        $top_tests = $this->db->get()->result();

        // E. Monthly Volume Trend (Last 12 Months)
        $m_trend = [];
        for ($m = 11; $m >= 0; $m--) {
            $m_time = strtotime("-$m months", strtotime(date('Y-m-01 00:00:00')));
            $m_key  = date('Y-m', $m_time);
            $m_trend[$m_key] = [
                'label' => date('M Y', $m_time),
                'count' => 0
            ];
        }
        $y12_start = strtotime('-11 months', strtotime(date('Y-m-01 00:00:00')));
        $y12_end   = strtotime(date('Y-m-t 23:59:59'));

        $this->db->reset_query();
        $this->db->select("FROM_UNIXTIME(l.date, '%Y-%m') as ym, COUNT(l.id) as cnt");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $y12_start);
        $this->db->where('l.date <=', $y12_end);
        $this->db->group_by("ym");
        $m_rows = $this->db->get()->result();
        foreach ($m_rows as $mr) {
            if (isset($m_trend[$mr->ym])) {
                $m_trend[$mr->ym]['count'] = intval($mr->cnt);
            }
        }

        return [
            'period'    => $period,
            'summary'   => $summary,
            'charts'    => [
                'daily_trends'       => array_values($chart_days),
                'category_dist'      => $cat_dist,
                'department_dist'    => $dept_dist,
                'top_tests'          => $top_tests,
                'monthly_volume'     => array_values($m_trend)
            ]
        ];
    }

    /**
     * 2. DAILY DIAGNOSTIC PERFORMANCE REPORT
     * 24-hour time slots breakdown + comprehensive itemized test drilldown
     */
    public function getDailyDiagnosticPerformanceReport($selected_date = null, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $dt = !empty($selected_date) ? $selected_date : date('Y-m-d');
        $start_time = strtotime($dt . ' 00:00:00');
        $end_time   = strtotime($dt . ' 23:59:59');

        // 24 Hourly Slots
        $hourly_slots = [];
        for ($h = 0; $h < 24; $h++) {
            $slot_label = sprintf('%02d:00 - %02d:59', $h, $h);
            $hourly_slots[sprintf('%02d:00', $h)] = [
                'hour_slot'          => $slot_label,
                'tests_ordered'      => 0,
                'samples_collected'  => 0,
                'tests_completed'    => 0,
                'reports_delivered'  => 0,
                'cancelled'          => 0
            ];
        }

        // Detailed Itemized Test Rows
        $this->db->reset_query();
        $this->db->select("l.*, 
                           COALESCE(pc.category, l.category_name, 'Diagnostic Test') as test_name,
                           COALESCE(pc.payment_category_name, c.category, 'General') as category_name_resolved,
                           COALESCE(pc.c_price, 0) as test_price,
                           COALESCE(pc.delivery_days, 0) as sla_days,
                           COALESCE(doc.name, l.doctor_name, 'Hospital Direct') as doctor_name_resolved,
                           COALESCE(dep.name, 'Clinical OPD') as department_name,
                           p.registration_time as patient_reg_time,
                           u.username as operator_name");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->join('patient p', 'p.id = l.patient', 'left');
        $this->db->join('users u', 'u.id = l.user', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->where('(pc.payment_category = ' . $this->db->escape($filters['category_id']) . ' OR pc.id = ' . $this->db->escape($filters['category_id']) . ')', NULL, FALSE);
        }
        if (!empty($filters['doctor_id'])) {
            $this->db->where('l.doctor', $filters['doctor_id']);
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] == 'completed') {
                $this->db->where("(l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivered'))", NULL, FALSE);
            } elseif ($filters['status'] == 'pending') {
                $this->db->where("(l.test_status != 'done' OR l.test_status IS NULL)", NULL, FALSE);
                $this->db->where("(l.status NOT IN ('complete', 'completed', 'delivered', 'cancelled') OR l.status IS NULL)", NULL, FALSE);
            } elseif ($filters['status'] == 'delivered') {
                $this->db->where("l.delivery_status", 'delivered');
            } elseif ($filters['status'] == 'cancelled') {
                $this->db->where("(l.test_status = 'cancelled' OR l.status = 'cancelled')", NULL, FALSE);
            }
        }

        $this->db->order_by('l.id', 'DESC');
        $tests = $this->db->get()->result();

        $tot_ordered   = count($tests);
        $tot_samples   = 0;
        $tot_completed = 0;
        $tot_verified  = 0;
        $tot_delivered = 0;
        $tot_pending   = 0;
        $tot_cancelled = 0;
        $tot_new_pat   = 0;
        $tot_ret_pat   = 0;

        $unique_patients = [];
        $unique_invoices = [];

        foreach ($tests as $t) {
            $unique_invoices[$t->invoice_id] = true;
            $unique_patients[$t->patient] = true;

            // Hourly Slot Placement
            $h_key = sprintf('%02d:00', date('G', $t->date));
            if (isset($hourly_slots[$h_key])) {
                $hourly_slots[$h_key]['tests_ordered']++;
            }

            // Status Evaluations
            $is_cancelled = ($t->test_status == 'cancelled' || $t->status == 'cancelled');
            $is_completed = ($t->test_status == 'done' || in_array($t->status, ['complete', 'completed', 'delivery_pending', 'delivered']));
            $is_sample_col = ($is_completed || in_array($t->status, ['sample_taken', 'waiting']));
            $is_verified  = (!empty($t->signed_by) || in_array($t->status, ['delivery_pending', 'delivered']));
            $is_delivered = ($t->delivery_status == 'delivered');

            if ($is_cancelled) {
                $tot_cancelled++;
                if (isset($hourly_slots[$h_key])) $hourly_slots[$h_key]['cancelled']++;
            } else {
                if ($is_sample_col) {
                    $tot_samples++;
                    if (isset($hourly_slots[$h_key])) $hourly_slots[$h_key]['samples_collected']++;
                }
                if ($is_completed) {
                    $tot_completed++;
                    if (isset($hourly_slots[$h_key])) $hourly_slots[$h_key]['tests_completed']++;
                } else {
                    $tot_pending++;
                }
                if ($is_verified) {
                    $tot_verified++;
                }
                if ($is_delivered) {
                    $tot_delivered++;
                    if (isset($hourly_slots[$h_key])) $hourly_slots[$h_key]['reports_delivered']++;
                }
            }

            // Patient Classification
            if (!empty($t->patient_reg_time)) {
                if ($t->patient_reg_time >= $start_time && $t->patient_reg_time <= $end_time) {
                    $tot_new_pat++;
                } else {
                    $tot_ret_pat++;
                }
            } else {
                $tot_new_pat++;
            }
        }

        return [
            'selected_date' => $dt,
            'summary'       => [
                'total_patients'     => count($unique_patients),
                'new_patients'       => $tot_new_pat,
                'returning_patients' => $tot_ret_pat,
                'total_invoices'     => count($unique_invoices),
                'tests_ordered'      => $tot_ordered,
                'samples_collected'  => $tot_samples,
                'samples_pending'    => max(0, $tot_ordered - $tot_samples - $tot_cancelled),
                'tests_completed'    => $tot_completed,
                'reports_verified'   => $tot_verified,
                'reports_delivered'  => $tot_delivered,
                'reports_pending'    => $tot_pending,
                'cancelled_tests'    => $tot_cancelled
            ],
            'hourly_slots'  => array_values($hourly_slots),
            'tests'         => $tests
        ];
    }

    /**
     * 3. MONTHLY DIAGNOSTIC PERFORMANCE REPORT
     * 31-day daily breakdown matrix, MoM variance, and performance metrics
     */
    public function getMonthlyDiagnosticPerformanceReport($selected_month = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $ym = !empty($selected_month) ? $selected_month : date('Y-m');
        $m_start = strtotime($ym . '-01 00:00:00');
        $m_end   = strtotime(date('Y-m-t 23:59:59', $m_start));
        $days_in_month = intval(date('t', $m_start));

        // Prior Month for MoM Comparison
        $prev_m_start = strtotime('-1 month', $m_start);
        $prev_m_end   = strtotime(date('Y-m-t 23:59:59', $prev_m_start));

        $daily_matrix = [];
        for ($d = 1; $d <= $days_in_month; $d++) {
            $d_str = sprintf('%s-%02d', $ym, $d);
            $daily_matrix[$d_str] = [
                'date'              => $d_str,
                'day_num'           => $d,
                'day_name'          => date('D', strtotime($d_str)),
                'patients'          => 0,
                'invoices'          => 0,
                'tests_ordered'     => 0,
                'samples_collected' => 0,
                'tests_completed'   => 0,
                'reports_verified'  => 0,
                'reports_delivered' => 0,
                'reports_pending'   => 0,
                'cancelled'         => 0
            ];
        }

        // Current Month Daily Aggregation
        $this->db->reset_query();
        $this->db->select("FROM_UNIXTIME(l.date, '%Y-%m-%d') as dt,
                           COUNT(DISTINCT l.patient) as patients,
                           COUNT(DISTINCT l.invoice_id) as invoices,
                           COUNT(l.id) as tests_ordered,
                           COALESCE(SUM(CASE WHEN (l.status IN ('sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered') OR l.test_status = 'done') THEN 1 ELSE 0 END), 0) as samples,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as completed,
                           COALESCE(SUM(CASE WHEN ((l.signed_by IS NOT NULL AND l.signed_by != '' AND l.signed_by != '0') OR l.status IN ('delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as verified,
                           COALESCE(SUM(CASE WHEN l.delivery_status = 'delivered' THEN 1 ELSE 0 END), 0) as delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as cancelled");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $m_start);
        $this->db->where('l.date <=', $m_end);
        $this->db->group_by("FROM_UNIXTIME(l.date, '%Y-%m-%d')");
        $rows = $this->db->get()->result();

        $tot_ordered   = 0;
        $tot_samples   = 0;
        $tot_completed = 0;
        $tot_verified  = 0;
        $tot_delivered = 0;
        $tot_cancelled = 0;

        foreach ($rows as $r) {
            if (isset($daily_matrix[$r->dt])) {
                $daily_matrix[$r->dt]['patients']          = intval($r->patients);
                $daily_matrix[$r->dt]['invoices']          = intval($r->invoices);
                $daily_matrix[$r->dt]['tests_ordered']     = intval($r->tests_ordered);
                $daily_matrix[$r->dt]['samples_collected'] = intval($r->samples);
                $daily_matrix[$r->dt]['tests_completed']   = intval($r->completed);
                $daily_matrix[$r->dt]['reports_verified']  = intval($r->verified);
                $daily_matrix[$r->dt]['reports_delivered'] = intval($r->delivered);
                $daily_matrix[$r->dt]['reports_pending']   = max(0, intval($r->tests_ordered) - intval($r->completed) - intval($r->cancelled));
                $daily_matrix[$r->dt]['cancelled']         = intval($r->cancelled);

                $tot_ordered   += intval($r->tests_ordered);
                $tot_samples   += intval($r->samples);
                $tot_completed += intval($r->completed);
                $tot_verified  += intval($r->verified);
                $tot_delivered += intval($r->delivered);
                $tot_cancelled += intval($r->cancelled);
            }
        }

        // Previous Month Totals for MoM Comparison
        $this->db->reset_query();
        $this->db->select("COUNT(l.id) as prev_tests_ordered,
                           COUNT(DISTINCT l.invoice_id) as prev_invoices,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as prev_completed");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $prev_m_start);
        $this->db->where('l.date <=', $prev_m_end);
        $prev_row = $this->db->get()->row();

        $prev_ordered = intval($prev_row ? $prev_row->prev_tests_ordered : 0);
        $diff_tests = $tot_ordered - $prev_ordered;
        $growth_pct = ($prev_ordered > 0) ? round(($diff_tests / $prev_ordered) * 100, 1) : (($tot_ordered > 0) ? 100.0 : 0.0);

        return [
            'selected_month'   => $ym,
            'month_label'      => date('F Y', $m_start),
            'summary'          => [
                'total_tests_ordered' => $tot_ordered,
                'samples_collected'   => $tot_samples,
                'tests_completed'     => $tot_completed,
                'reports_verified'    => $tot_verified,
                'reports_delivered'   => $tot_delivered,
                'reports_pending'     => max(0, $tot_ordered - $tot_completed - $tot_cancelled),
                'cancelled_tests'     => $tot_cancelled,
                'completion_rate'     => ($tot_ordered > 0) ? round(($tot_completed / $tot_ordered) * 100, 1) : 0.0,
                'delivery_rate'       => ($tot_completed > 0) ? round(($tot_delivered / $tot_completed) * 100, 1) : 0.0,
                'cancellation_rate'   => ($tot_ordered > 0) ? round(($tot_cancelled / $tot_ordered) * 100, 1) : 0.0
            ],
            'mom_comparison'   => [
                'current_month_tests' => $tot_ordered,
                'prev_month_tests'    => $prev_ordered,
                'variance_tests'      => $diff_tests,
                'growth_pct'          => $growth_pct
            ],
            'daily_rows'       => array_values($daily_matrix)
        ];
    }

    /**
     * 4. YEARLY DIAGNOSTIC PERFORMANCE REPORT
     * 12-month calendar aggregation matrix, YoY comparison, and annual metrics
     */
    public function getYearlyDiagnosticPerformanceReport($selected_year = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $y = !empty($selected_year) ? $selected_year : date('Y');
        $y_start = strtotime($y . '-01-01 00:00:00');
        $y_end   = strtotime($y . '-12-31 23:59:59');

        $prev_y = strval(intval($y) - 1);
        $prev_y_start = strtotime($prev_y . '-01-01 00:00:00');
        $prev_y_end   = strtotime($prev_y . '-12-31 23:59:59');

        $month_matrix = [];
        for ($m = 1; $m <= 12; $m++) {
            $ym = sprintf('%s-%02d', $y, $m);
            $month_matrix[$ym] = [
                'month_key'         => $ym,
                'month_name'        => date('F', strtotime($ym . '-01')),
                'month_short'       => date('M', strtotime($ym . '-01')),
                'patients'          => 0,
                'invoices'          => 0,
                'tests_ordered'     => 0,
                'samples_collected' => 0,
                'tests_completed'   => 0,
                'reports_verified'  => 0,
                'reports_delivered' => 0,
                'reports_pending'   => 0,
                'cancelled'         => 0
            ];
        }

        $this->db->reset_query();
        $this->db->select("FROM_UNIXTIME(l.date, '%Y-%m') as ym,
                           COUNT(DISTINCT l.patient) as patients,
                           COUNT(DISTINCT l.invoice_id) as invoices,
                           COUNT(l.id) as tests_ordered,
                           COALESCE(SUM(CASE WHEN (l.status IN ('sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered') OR l.test_status = 'done') THEN 1 ELSE 0 END), 0) as samples,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as completed,
                           COALESCE(SUM(CASE WHEN ((l.signed_by IS NOT NULL AND l.signed_by != '' AND l.signed_by != '0') OR l.status IN ('delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as verified,
                           COALESCE(SUM(CASE WHEN l.delivery_status = 'delivered' THEN 1 ELSE 0 END), 0) as delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as cancelled");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $y_start);
        $this->db->where('l.date <=', $y_end);
        $this->db->group_by("FROM_UNIXTIME(l.date, '%Y-%m')");
        $rows = $this->db->get()->result();

        $tot_ordered   = 0;
        $tot_samples   = 0;
        $tot_completed = 0;
        $tot_verified  = 0;
        $tot_delivered = 0;
        $tot_cancelled = 0;

        foreach ($rows as $r) {
            if (isset($month_matrix[$r->ym])) {
                $month_matrix[$r->ym]['patients']          = intval($r->patients);
                $month_matrix[$r->ym]['invoices']          = intval($r->invoices);
                $month_matrix[$r->ym]['tests_ordered']     = intval($r->tests_ordered);
                $month_matrix[$r->ym]['samples_collected'] = intval($r->samples);
                $month_matrix[$r->ym]['tests_completed']   = intval($r->completed);
                $month_matrix[$r->ym]['reports_verified']  = intval($r->verified);
                $month_matrix[$r->ym]['reports_delivered'] = intval($r->delivered);
                $month_matrix[$r->ym]['reports_pending']   = max(0, intval($r->tests_ordered) - intval($r->completed) - intval($r->cancelled));
                $month_matrix[$r->ym]['cancelled']         = intval($r->cancelled);

                $tot_ordered   += intval($r->tests_ordered);
                $tot_samples   += intval($r->samples);
                $tot_completed += intval($r->completed);
                $tot_verified  += intval($r->verified);
                $tot_delivered += intval($r->delivered);
                $tot_cancelled += intval($r->cancelled);
            }
        }

        // Previous Year for YoY
        $this->db->reset_query();
        $this->db->select("COUNT(l.id) as prev_tests_ordered");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $prev_y_start);
        $this->db->where('l.date <=', $prev_y_end);
        $prev_y_row = $this->db->get()->row();
        $prev_y_ordered = intval($prev_y_row ? $prev_y_row->prev_tests_ordered : 0);
        $yoy_diff = $tot_ordered - $prev_y_ordered;
        $yoy_pct  = ($prev_y_ordered > 0) ? round(($yoy_diff / $prev_y_ordered) * 100, 1) : (($tot_ordered > 0) ? 100.0 : 0.0);

        return [
            'selected_year' => $y,
            'summary'       => [
                'total_tests_ordered' => $tot_ordered,
                'samples_collected'   => $tot_samples,
                'tests_completed'     => $tot_completed,
                'reports_verified'    => $tot_verified,
                'reports_delivered'   => $tot_delivered,
                'reports_pending'     => max(0, $tot_ordered - $tot_completed - $tot_cancelled),
                'cancelled_tests'     => $tot_cancelled,
                'completion_rate'     => ($tot_ordered > 0) ? round(($tot_completed / $tot_ordered) * 100, 1) : 0.0,
                'delivery_rate'       => ($tot_completed > 0) ? round(($tot_delivered / $tot_completed) * 100, 1) : 0.0
            ],
            'yoy_comparison' => [
                'current_year_tests' => $tot_ordered,
                'prev_year_tests'    => $prev_y_ordered,
                'variance_tests'     => $yoy_diff,
                'growth_pct'         => $yoy_pct
            ],
            'month_rows'    => array_values($month_matrix)
        ];
    }

    /**
     * 5. TEST-WISE PERFORMANCE REPORT
     * Granular investigation-level metrics, completion, delivery, billing, and SLA TAT
     */
    public function getTestPerformanceReport($start_time, $end_time, $preset = 'all', $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("l.category_id as test_id,
                           MAX(COALESCE(pc.category, l.category_name, 'Diagnostic Test')) as test_name,
                           MAX(COALESCE(pc.payment_category_name, c.category, 'General')) as category_name,
                           MAX(COALESCE(pc.c_price, 0)) as unit_price,
                           MAX(COALESCE(pc.delivery_days, 0)) as sla_days,
                           COUNT(l.id) as total_ordered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as total_completed,
                           COALESCE(SUM(CASE WHEN (l.delivery_status = 'delivered') THEN 1 ELSE 0 END), 0) as total_delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as total_cancelled,
                           AVG(CASE WHEN (l.test_status_date > l.date) THEN (l.test_status_date - l.date) ELSE NULL END) as avg_tat_seconds,
                           COALESCE(SUM(COALESCE(pc.c_price, 0)), 0) as gross_billing");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->where('(pc.payment_category = ' . $this->db->escape($filters['category_id']) . ' OR pc.id = ' . $this->db->escape($filters['category_id']) . ')', NULL, FALSE);
        }

        $this->db->group_by('l.category_id');

        // Preset Ordering & Limits
        if ($preset == 'top10') {
            $this->db->order_by('total_ordered', 'DESC');
            $this->db->limit(10);
        } elseif ($preset == 'top20') {
            $this->db->order_by('total_ordered', 'DESC');
            $this->db->limit(20);
        } elseif ($preset == 'least') {
            $this->db->order_by('total_ordered', 'ASC');
            $this->db->limit(20);
        } else {
            $this->db->order_by('total_ordered', 'DESC');
        }

        $records = $this->db->get()->result();

        $tot_ordered = 0;
        $tot_completed = 0;
        $tot_delivered = 0;
        $tot_cancelled = 0;
        $tot_gross = 0.00;

        foreach ($records as $r) {
            $r->total_pending = max(0, intval($r->total_ordered) - intval($r->total_completed) - intval($r->total_cancelled));
            $r->avg_tat_hours = ($r->avg_tat_seconds !== null) ? round(floatval($r->avg_tat_seconds) / 3600, 1) : null;
            $r->completion_rate = ($r->total_ordered > 0) ? round(($r->total_completed / $r->total_ordered) * 100, 1) : 0.0;
            $r->net_billing = floatval($r->gross_billing); // estimated net without line discounts

            $tot_ordered   += intval($r->total_ordered);
            $tot_completed += intval($r->total_completed);
            $tot_delivered += intval($r->total_delivered);
            $tot_cancelled += intval($r->total_cancelled);
            $tot_gross     += floatval($r->gross_billing);
        }

        return [
            'summary' => [
                'total_tests_catalog' => count($records),
                'total_ordered'       => $tot_ordered,
                'total_completed'     => $tot_completed,
                'total_pending'       => max(0, $tot_ordered - $tot_completed - $tot_cancelled),
                'total_delivered'     => $tot_delivered,
                'total_cancelled'     => $tot_cancelled,
                'total_gross_billing' => $tot_gross
            ],
            'records' => $records
        ];
    }

    /**
     * 6. CATEGORY-WISE PERFORMANCE REPORT
     * Dynamic categories performance from category table
     */
    public function getCategoryPerformanceReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("COALESCE(c.id, 0) as category_id,
                           MAX(COALESCE(pc.payment_category_name, c.category, 'General Diagnostics')) as category_name,
                           COUNT(l.id) as total_ordered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as total_completed,
                           COALESCE(SUM(CASE WHEN (l.delivery_status = 'delivered') THEN 1 ELSE 0 END), 0) as total_delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as total_cancelled,
                           COALESCE(SUM(COALESCE(pc.c_price, 0)), 0) as category_billing");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        if (!empty($filters['category_id'])) {
            $this->db->where('(pc.payment_category = ' . $this->db->escape($filters['category_id']) . ' OR c.id = ' . $this->db->escape($filters['category_id']) . ')', NULL, FALSE);
        }

        $this->db->group_by("COALESCE(c.id, 0)");
        $this->db->order_by("total_ordered", "DESC");
        $categories = $this->db->get()->result();

        $tot_ordered = 0;
        $tot_billing = 0.00;

        foreach ($categories as $cat) {
            $cat->pending_tests = max(0, intval($cat->total_ordered) - intval($cat->total_completed) - intval($cat->total_cancelled));
            $cat->pending_delivery = max(0, intval($cat->total_completed) - intval($cat->total_delivered));
            $cat->completion_rate = ($cat->total_ordered > 0) ? round(($cat->total_completed / $cat->total_ordered) * 100, 1) : 0.0;
            $tot_ordered += intval($cat->total_ordered);
            $tot_billing += floatval($cat->category_billing);
        }

        foreach ($categories as $cat) {
            $cat->volume_share_pct = ($tot_ordered > 0) ? round(($cat->total_ordered / $tot_ordered) * 100, 1) : 0.0;
        }

        return [
            'summary' => [
                'categories_count' => count($categories),
                'total_ordered'    => $tot_ordered,
                'total_billing'    => $tot_billing
            ],
            'categories' => $categories
        ];
    }

    /**
     * 7. DEPARTMENT-WISE PERFORMANCE REPORT
     * Department performance, referral doctor volumes, turnaround times
     */
    public function getDepartmentPerformanceReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("COALESCE(dep.id, 0) as department_id,
                           MAX(COALESCE(dep.name, 'General Clinical')) as department_name,
                           COUNT(DISTINCT l.doctor) as doctor_count,
                           COUNT(DISTINCT l.patient) as total_patients_served,
                           COUNT(l.id) as total_tests_ordered,
                           COALESCE(SUM(CASE WHEN (l.status IN ('sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered') OR l.test_status = 'done') THEN 1 ELSE 0 END), 0) as samples_collected,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as tests_completed,
                           COALESCE(SUM(CASE WHEN ((l.signed_by IS NOT NULL AND l.signed_by != '' AND l.signed_by != '0') OR l.status IN ('delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as reports_verified,
                           COALESCE(SUM(CASE WHEN l.delivery_status = 'delivered' THEN 1 ELSE 0 END), 0) as reports_delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as cancelled_tests,
                           AVG(CASE WHEN (l.test_status_date > l.date) THEN (l.test_status_date - l.date) ELSE NULL END) as avg_tat_seconds");
        $this->db->from('lab l');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }

        $this->db->group_by("dep.id");
        $this->db->order_by("total_tests_ordered", "DESC");
        $departments = $this->db->get()->result();

        $tot_ordered = 0;
        $tot_patients = 0;

        foreach ($departments as $d) {
            $d->pending_tests = max(0, intval($d->total_tests_ordered) - intval($d->tests_completed) - intval($d->cancelled_tests));
            $d->avg_tat_hours = ($d->avg_tat_seconds !== null) ? round(floatval($d->avg_tat_seconds) / 3600, 1) : null;
            $d->completion_rate = ($d->total_tests_ordered > 0) ? round(($d->tests_completed / $d->total_tests_ordered) * 100, 1) : 0.0;
            $tot_ordered += intval($d->total_tests_ordered);
            $tot_patients += intval($d->total_patients_served);
        }

        return [
            'summary' => [
                'departments_count' => count($departments),
                'total_patients'    => $tot_patients,
                'total_ordered'     => $tot_ordered
            ],
            'departments' => $departments
        ];
    }

    /**
     * 8. SAMPLE COLLECTION REPORT
     * Specimen-level audit, collection status, collector, specimen types
     */
    public function getSampleCollectionReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("l.*,
                           COALESCE(pc.category, l.category_name, 'Test') as test_name,
                           COALESCE(pc.payment_category_name, c.category, 'General') as category_group,
                           COALESCE(dep.name, 'Clinical OPD') as department_name,
                           u.username as collector_name");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->join('users u', 'u.id = l.user', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }
        if (!empty($filters['sample_status'])) {
            if ($filters['sample_status'] == 'collected') {
                $this->db->where("(l.status IN ('sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered') OR l.test_status = 'done')", NULL, FALSE);
            } elseif ($filters['sample_status'] == 'pending') {
                $this->db->where("(l.status = 'pending' AND (l.test_status IS NULL OR l.test_status != 'done'))", NULL, FALSE);
            }
        }

        $this->db->order_by('l.date', 'DESC');
        $samples = $this->db->get()->result();

        $tot_all = count($samples);
        $tot_collected = 0;
        $tot_pending = 0;
        $tot_imaging = 0;

        foreach ($samples as $s) {
            $cat_lower = strtolower($s->category_group);
            if (strpos($cat_lower, 'x-ray') !== false || strpos($cat_lower, 'ultra') !== false || strpos($cat_lower, 'ecg') !== false || strpos($cat_lower, 'echo') !== false) {
                $s->sample_type = 'Non-Specimen / Direct';
                $s->collection_status = 'Not Applicable';
                $tot_imaging++;
            } elseif (strpos($cat_lower, 'blood') !== false || strpos($cat_lower, 'path') !== false || strpos($cat_lower, 'hormone') !== false) {
                $s->sample_type = 'Blood Specimen';
                $is_col = (in_array($s->status, ['sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered']) || $s->test_status == 'done');
                $s->collection_status = $is_col ? 'Collected' : 'Pending Collection';
                if ($is_col) $tot_collected++; else $tot_pending++;
            } elseif (strpos($cat_lower, 'urine') !== false) {
                $s->sample_type = 'Urine Specimen';
                $is_col = (in_array($s->status, ['sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered']) || $s->test_status == 'done');
                $s->collection_status = $is_col ? 'Collected' : 'Pending Collection';
                if ($is_col) $tot_collected++; else $tot_pending++;
            } elseif (strpos($cat_lower, 'stool') !== false) {
                $s->sample_type = 'Stool Specimen';
                $is_col = (in_array($s->status, ['sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered']) || $s->test_status == 'done');
                $s->collection_status = $is_col ? 'Collected' : 'Pending Collection';
                if ($is_col) $tot_collected++; else $tot_pending++;
            } else {
                $s->sample_type = 'General Specimen';
                $is_col = (in_array($s->status, ['sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered']) || $s->test_status == 'done');
                $s->collection_status = $is_col ? 'Collected' : 'Pending Collection';
                if ($is_col) $tot_collected++; else $tot_pending++;
            }
        }

        return [
            'summary' => [
                'total_samples'     => $tot_all,
                'collected_samples' => $tot_collected,
                'pending_samples'   => $tot_pending,
                'non_specimen_tests'=> $tot_imaging,
                'collection_rate'   => (($tot_collected + $tot_pending) > 0) ? round(($tot_collected / ($tot_collected + $tot_pending)) * 100, 1) : 0.0
            ],
            'samples' => $samples
        ];
    }

    /**
     * 9. PENDING TEST REPORT
     * Investigation-item level pending queue classified by exact workflow stage
     */
    public function getPendingTestReport($filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("l.*,
                           COALESCE(pc.category, l.category_name, 'Test') as test_name,
                           COALESCE(pc.payment_category_name, c.category, 'General') as category_group,
                           COALESCE(dep.name, 'Clinical OPD') as department_name,
                           COALESCE(doc.name, l.doctor_name, 'Direct') as doctor_name_resolved,
                           u.username as operator_name");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->join('users u', 'u.id = l.user', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        
        // Exclude delivered and cancelled
        $this->db->where("COALESCE(l.delivery_status, '') !=", 'delivered');
        $this->db->where("COALESCE(l.test_status, '') !=", 'cancelled');
        $this->db->where("COALESCE(l.status, '') !=", 'cancelled');

        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->where('(pc.payment_category = ' . $this->db->escape($filters['category_id']) . ' OR c.id = ' . $this->db->escape($filters['category_id']) . ')', NULL, FALSE);
        }

        $this->db->order_by('l.date', 'ASC'); // Oldest orders first
        $rows = $this->db->get()->result();

        $classified = [];
        $st_await_sample   = 0;
        $st_sample_col     = 0;
        $st_processing     = 0;
        $st_await_verify   = 0;
        $st_await_delivery = 0;

        $now = time();

        foreach ($rows as $r) {
            $order_time = intval($r->date);
            $elapsed_hours = ($order_time > 0) ? round(($now - $order_time) / 3600, 1) : 0;
            $r->elapsed_hours = $elapsed_hours;

            // Classify exact stage
            if ($r->test_status == 'done') {
                if (!empty($r->signed_by) || $r->status == 'delivery_pending') {
                    $stage = 'Awaiting Delivery';
                    $st_await_delivery++;
                } else {
                    $stage = 'Awaiting Verification';
                    $st_await_verify++;
                }
            } elseif ($r->status == 'waiting' || $r->test_status == 'pending') {
                $stage = 'Processing';
                $st_processing++;
            } elseif ($r->status == 'sample_taken') {
                $stage = 'Sample Collected';
                $st_sample_col++;
            } else {
                $stage = 'Awaiting Sample Collection';
                $st_await_sample++;
            }

            $r->workflow_stage = $stage;

            // Optional filter by stage
            if (!empty($filters['stage']) && $filters['stage'] != 'all' && $filters['stage'] != $stage) {
                continue;
            }

            $classified[] = $r;
        }

        return [
            'summary' => [
                'total_pending'             => count($rows),
                'awaiting_sample_collection'=> $st_await_sample,
                'sample_collected'          => $st_sample_col,
                'processing'                => $st_processing,
                'awaiting_verification'     => $st_await_verify,
                'awaiting_delivery'         => $st_await_delivery
            ],
            'pending_tests' => $classified
        ];
    }

    /**
     * 10. REPORT DELIVERY REPORT
     * Handover audit, delivery timestamps, receiver names, delivery methods
     */
    public function getReportDeliveryReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("l.*,
                           COALESCE(pc.category, l.category_name, 'Test') as test_name,
                           COALESCE(pc.payment_category_name, c.category, 'General') as category_group,
                           COALESCE(dep.name, 'Clinical OPD') as department_name,
                           u.username as operator_name");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->join('users u', 'u.id = l.user', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        if (!empty($filters['delivery_status'])) {
            $this->db->where('l.delivery_status', $filters['delivery_status']);
        }

        $this->db->order_by('l.delivery_status_date', 'DESC');
        $rows = $this->db->get()->result();

        $tot_prepared = 0;
        $tot_delivered = 0;
        $tot_pending_del = 0;

        foreach ($rows as $r) {
            $is_prepared = ($r->test_status == 'done' || in_array($r->status, ['complete', 'completed', 'delivery_pending', 'delivered']));
            if ($is_prepared) $tot_prepared++;

            if ($r->delivery_status == 'delivered') {
                $tot_delivered++;
                $r->delivery_method = 'Printed Copy (Counter)';
            } else {
                if ($is_prepared) $tot_pending_del++;
                $r->delivery_method = 'Pending Handover';
            }
        }

        return [
            'summary' => [
                'total_reports_prepared'  => $tot_prepared,
                'total_reports_delivered' => $tot_delivered,
                'pending_delivery'        => $tot_pending_del,
                'delivery_rate'           => ($tot_prepared > 0) ? round(($tot_delivered / $tot_prepared) * 100, 1) : 0.0
            ],
            'reports' => $rows
        ];
    }

    /**
     * 11. REPORT VERIFICATION REPORT
     * Quality sign-off audit, consultant signatures, verified dates
     */
    public function getReportVerificationReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("l.*,
                           COALESCE(pc.category, l.category_name, 'Test') as test_name,
                           COALESCE(dep.name, 'Clinical OPD') as department_name,
                           u.username as preparer_name,
                           COALESCE(sdoc.name, l.signed_by, 'Authorized Consultant') as verifier_name");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->join('users u', 'u.id = l.reported_by', 'left');
        $this->db->join('doctor sdoc', 'sdoc.id = l.signed_by', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        $this->db->order_by('l.date', 'DESC');
        $rows = $this->db->get()->result();

        $tot_authored = 0;
        $tot_verified = 0;
        $tot_pending_verify = 0;

        foreach ($rows as $r) {
            $is_authored = (!empty($r->reported_by) || !empty($r->report) || $r->status == 'completed' || $r->test_status == 'done');
            $is_verified = (!empty($r->signed_by) || in_array($r->status, ['delivery_pending', 'delivered']));

            if ($is_authored) $tot_authored++;
            if ($is_verified) {
                $tot_verified++;
                $r->verification_status = 'Verified & Signed';
            } else {
                if ($is_authored) $tot_pending_verify++;
                $r->verification_status = 'Awaiting Sign-off';
            }
        }

        return [
            'summary' => [
                'total_authored'        => $tot_authored,
                'total_verified'        => $tot_verified,
                'pending_verification'  => $tot_pending_verify,
                'verification_rate'     => ($tot_authored > 0) ? round(($tot_verified / $tot_authored) * 100, 1) : 0.0
            ],
            'verifications' => $rows
        ];
    }

    /**
     * 12. TURNAROUND TIME (TAT) REPORT
     * Reliable timestamp analysis, SLA benchmark comparisons, minimum, average, and maximum TAT
     */
    public function getTurnaroundTimeReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("pc.id as test_id,
                           MAX(COALESCE(pc.category, l.category_name, 'Test')) as test_name,
                           MAX(COALESCE(pc.payment_category_name, c.category, 'General')) as category_group,
                           MAX(COALESCE(dep.name, 'Clinical OPD')) as department_name,
                           MAX(COALESCE(pc.delivery_days, 0)) as sla_days,
                           COUNT(l.id) as completed_count,
                           AVG(CASE WHEN (l.test_status_date > l.date) THEN (l.test_status_date - l.date) ELSE NULL END) as avg_tat_seconds,
                           MIN(CASE WHEN (l.test_status_date > l.date) THEN (l.test_status_date - l.date) ELSE NULL END) as min_tat_seconds,
                           MAX(CASE WHEN (l.test_status_date > l.date) THEN (l.test_status_date - l.date) ELSE NULL END) as max_tat_seconds");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $this->db->where('l.test_status_date >', 'l.date', FALSE);

        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->where('(pc.payment_category = ' . $this->db->escape($filters['category_id']) . ' OR c.id = ' . $this->db->escape($filters['category_id']) . ')', NULL, FALSE);
        }

        $this->db->group_by("pc.id");
        $this->db->order_by("completed_count", "DESC");
        $rows = $this->db->get()->result();

        $tot_completed = 0;
        $tot_on_time   = 0;
        $tot_delayed   = 0;
        $tat_sum_sec   = 0;
        $tat_count     = 0;

        foreach ($rows as $r) {
            $r->avg_tat_hours = ($r->avg_tat_seconds !== null) ? round(floatval($r->avg_tat_seconds) / 3600, 1) : 0.0;
            $r->min_tat_hours = ($r->min_tat_seconds !== null) ? round(floatval($r->min_tat_seconds) / 3600, 1) : 0.0;
            $r->max_tat_hours = ($r->max_tat_seconds !== null) ? round(floatval($r->max_tat_seconds) / 3600, 1) : 0.0;

            $sla_hours = intval($r->sla_days) * 24;
            $r->sla_target_label = ($r->sla_days > 0) ? ($r->sla_days . ' Days (' . $sla_hours . 'h)') : 'Standard Routine (24h)';

            // Target evaluation
            $target_hours = ($sla_hours > 0) ? $sla_hours : 24;
            if ($r->avg_tat_hours <= $target_hours) {
                $r->sla_status = 'Within SLA';
                $r->within_sla_count = intval($r->completed_count);
                $r->delayed_count = 0;
                $tot_on_time += intval($r->completed_count);
            } else {
                $r->sla_status = 'Delayed';
                $r->within_sla_count = 0;
                $r->delayed_count = intval($r->completed_count);
                $tot_delayed += intval($r->completed_count);
            }

            $tot_completed += intval($r->completed_count);
            if ($r->avg_tat_seconds !== null) {
                $tat_sum_sec += floatval($r->avg_tat_seconds) * intval($r->completed_count);
                $tat_count   += intval($r->completed_count);
            }
        }

        $global_avg_hours = ($tat_count > 0) ? round(($tat_sum_sec / $tat_count) / 3600, 1) : 0.0;

        return [
            'summary' => [
                'total_completed_analyzed' => $tot_completed,
                'within_sla_count'         => $tot_on_time,
                'delayed_count'            => $tot_delayed,
                'overall_on_time_rate'     => ($tot_completed > 0) ? round(($tot_on_time / $tot_completed) * 100, 1) : 0.0,
                'overall_average_tat_hours'=> $global_avg_hours
            ],
            'records' => $rows
        ];
    }

    /**
     * 13. CANCELLED TEST REPORT
     * Cancellation rates, departments, reasons, original amounts
     */
    public function getCancelledTestReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        // Cancelled rows in lab
        $this->db->reset_query();
        $this->db->select("l.*,
                           COALESCE(pc.category, l.category_name, 'Test') as test_name,
                           COALESCE(pc.payment_category_name, c.category, 'General') as category_group,
                           COALESCE(dep.name, 'Clinical OPD') as department_name,
                           COALESCE(pc.c_price, 0) as test_price,
                           u.username as cashier_name");
        $this->db->from('lab l');
        $this->db->join('payment_category pc', 'pc.id = l.category_id', 'left');
        $this->db->join('category c', 'c.id = pc.payment_category', 'left');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->join('users u', 'u.id = l.user', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $this->db->where("(l.test_status = 'cancelled' OR l.status = 'cancelled')", NULL, FALSE);

        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }

        $this->db->order_by('l.date', 'DESC');
        $cancelled_rows = $this->db->get()->result();

        // Total ordered in denominator
        $this->db->reset_query();
        $this->db->select("COUNT(id) as total_ordered");
        $this->db->from('lab');
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('date >=', $start_time);
        $this->db->where('date <=', $end_time);
        $tot_ordered = intval($this->db->get()->row()->total_ordered);

        $tot_cancelled = count($cancelled_rows);
        $cancelled_amount = 0.00;
        foreach ($cancelled_rows as $cr) {
            $cancelled_amount += floatval($cr->test_price);
            $cr->cancellation_reason = 'Order Withdrawn / Investigation Cancelled';
        }

        $cancellation_rate = ($tot_ordered > 0) ? round(($tot_cancelled / $tot_ordered) * 100, 2) : 0.00;

        return [
            'summary' => [
                'total_tests_ordered' => $tot_ordered,
                'total_cancelled'     => $tot_cancelled,
                'cancellation_rate'   => $cancellation_rate,
                'cancelled_amount'    => $cancelled_amount
            ],
            'cancelled_tests' => $cancelled_rows
        ];
    }

    /**
     * 14. DOCTOR-WISE DIAGNOSTIC PERFORMANCE REPORT
     * Operational analysis of referring doctors, volumes, and specialty breakdown (strictly no commission math)
     */
    public function getDoctorDiagnosticPerformanceReport($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("COALESCE(doc.id, 0) as doctor_id,
                           MAX(COALESCE(doc.name, l.doctor_name, 'Self / Direct')) as doctor_name,
                           MAX(COALESCE(dep.name, 'Hospital OPD')) as department_name,
                           COUNT(DISTINCT l.patient) as referred_patients,
                           COUNT(DISTINCT l.invoice_id) as associated_invoices,
                           COUNT(l.id) as total_tests_ordered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as completed_tests,
                           COALESCE(SUM(CASE WHEN l.delivery_status = 'delivered' THEN 1 ELSE 0 END), 0) as delivered_reports,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as cancelled_tests");
        $this->db->from('lab l');
        $this->db->join('doctor doc', 'doc.id = l.doctor', 'left');
        $this->db->join('department dep', 'dep.id = doc.department', 'left');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);

        if (!empty($filters['doctor_id'])) {
            $this->db->where('l.doctor', $filters['doctor_id']);
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('dep.id', $filters['department_id']);
        }

        $this->db->group_by("l.doctor");
        $this->db->order_by("total_tests_ordered", "DESC");
        $doctors = $this->db->get()->result();

        $tot_ordered = 0;
        $tot_patients = 0;

        foreach ($doctors as $d) {
            $d->pending_tests = max(0, intval($d->total_tests_ordered) - intval($d->completed_tests) - intval($d->cancelled_tests));
            $d->completion_rate = ($d->total_tests_ordered > 0) ? round(($d->completed_tests / $d->total_tests_ordered) * 100, 1) : 0.0;
            $tot_ordered += intval($d->total_tests_ordered);
            $tot_patients += intval($d->referred_patients);
        }

        return [
            'summary' => [
                'doctors_count'  => count($doctors),
                'total_patients' => $tot_patients,
                'total_ordered'  => $tot_ordered
            ],
            'doctors' => $doctors
        ];
    }

    /**
     * 15. DIAGNOSTIC OPERATIONAL EFFICIENCY REPORT
     * Comprehensive throughput scorecards, rates with clearly defined numerators/denominators
     */
    public function getDiagnosticOperationalEfficiencyData($start_time, $end_time, $filters = [], $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("COUNT(l.id) as total_tests_ordered,
                           COALESCE(SUM(CASE WHEN (l.status IN ('sample_taken', 'waiting', 'complete', 'completed', 'delivery_pending', 'delivered') OR l.test_status = 'done') THEN 1 ELSE 0 END), 0) as samples_collected,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as tests_completed,
                           COALESCE(SUM(CASE WHEN ((l.reported_by IS NOT NULL AND l.reported_by != '') OR (l.report IS NOT NULL AND l.report != '') OR l.status = 'completed') THEN 1 ELSE 0 END), 0) as reports_prepared,
                           COALESCE(SUM(CASE WHEN ((l.signed_by IS NOT NULL AND l.signed_by != '' AND l.signed_by != '0') OR l.status IN ('delivery_pending', 'delivered')) THEN 1 ELSE 0 END), 0) as reports_verified,
                           COALESCE(SUM(CASE WHEN l.delivery_status = 'delivered' THEN 1 ELSE 0 END), 0) as reports_delivered,
                           COALESCE(SUM(CASE WHEN (l.test_status = 'cancelled' OR l.status = 'cancelled') THEN 1 ELSE 0 END), 0) as cancelled_tests,
                           AVG(CASE WHEN (l.test_status_date > l.date) THEN (l.test_status_date - l.date) ELSE NULL END) as avg_tat_seconds");
        $this->db->from('lab l');
        $this->db->where('(l.hospital_id = ' . $this->db->escape($hospital_id) . ' OR l.hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('l.date >=', $start_time);
        $this->db->where('l.date <=', $end_time);
        $kpi = $this->db->get()->row();

        $tot_ordered    = intval($kpi ? $kpi->total_tests_ordered : 0);
        $samples_col    = intval($kpi ? $kpi->samples_collected : 0);
        $completed      = intval($kpi ? $kpi->tests_completed : 0);
        $prepared       = intval($kpi ? $kpi->reports_prepared : 0);
        if ($prepared < $completed) $prepared = $completed;
        $verified       = intval($kpi ? $kpi->reports_verified : 0);
        $delivered      = intval($kpi ? $kpi->reports_delivered : 0);
        $cancelled      = intval($kpi ? $kpi->cancelled_tests : 0);
        $avg_tat_hours  = ($kpi && $kpi->avg_tat_seconds !== null) ? round(floatval($kpi->avg_tat_seconds) / 3600, 1) : 0.0;

        return [
            'metrics' => [
                'sample_collection_efficiency' => [
                    'label'       => 'Sample Collection Efficiency',
                    'rate'        => ($tot_ordered > 0) ? round(($samples_col / $tot_ordered) * 100, 1) : 0.0,
                    'numerator'   => $samples_col,
                    'denominator' => $tot_ordered,
                    'formula'     => 'Samples Collected / Total Tests Ordered'
                ],
                'test_completion_rate' => [
                    'label'       => 'Test Completion Rate',
                    'rate'        => ($tot_ordered > 0) ? round(($completed / $tot_ordered) * 100, 1) : 0.0,
                    'numerator'   => $completed,
                    'denominator' => $tot_ordered,
                    'formula'     => 'Completed Tests / Total Tests Ordered'
                ],
                'report_verification_rate' => [
                    'label'       => 'Report Verification Rate',
                    'rate'        => ($prepared > 0) ? round(($verified / $prepared) * 100, 1) : 0.0,
                    'numerator'   => $verified,
                    'denominator' => $prepared,
                    'formula'     => 'Reports Verified / Reports Prepared'
                ],
                'report_delivery_rate' => [
                    'label'       => 'Report Delivery Rate',
                    'rate'        => ($prepared > 0) ? round(($delivered / $prepared) * 100, 1) : 0.0,
                    'numerator'   => $delivered,
                    'denominator' => $prepared,
                    'formula'     => 'Reports Delivered / Reports Prepared'
                ],
                'cancellation_rate' => [
                    'label'       => 'Order Cancellation Rate',
                    'rate'        => ($tot_ordered > 0) ? round(($cancelled / $tot_ordered) * 100, 1) : 0.0,
                    'numerator'   => $cancelled,
                    'denominator' => $tot_ordered,
                    'formula'     => 'Cancelled Tests / Total Tests Ordered'
                ],
                'average_tat_hours' => [
                    'label'       => 'Average Completion Turnaround Time',
                    'value'       => $avg_tat_hours . ' Hours',
                    'formula'     => 'Mean of (test_completion_time - order_time)'
                ]
            ]
        ];
    }


    // =========================================================================
    // TEST ITEM-WISE REPORT MODULE METHODS
    // =========================================================================

    /**
     * Get active diagnostic tests from payment_category for searchable dropdown
     */
    public function getActiveDiagnosticTests($hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);
        $this->db->select('id, category as name, code, c_price, payment_category_name');
        $this->db->from('payment_category');
        $this->db->where('(hospital_id = ' . $this->db->escape($hospital_id) . ' OR hospital_id IS NULL)', NULL, FALSE);
        $this->db->where('type', 'diagnostic');
        $this->db->where('status', 'Active');
        $this->db->order_by('category', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * REPORT 1: Selected Test Item Report
     * Allows selecting a specific test item and viewing its chronological date-wise performed count
     */
    public function getSelectedTestItemReport($test_id, $start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        // Fetch test details
        $this->db->select('id, category as name, code, c_price, payment_category_name');
        $this->db->from('payment_category');
        $this->db->where('id', $test_id);
        $test_info = $this->db->get()->row();
        $test_name = !empty($test_info) ? $test_info->name : 'Unknown Test';

        $this->db->reset_query();
        $sql = "
            SELECT 
                DATE(FROM_UNIXTIME(CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END)) AS report_date,
                MAX(COALESCE(pc.category, l.category_name, " . $this->db->escape($test_name) . ")) AS test_name,
                CAST(ROUND(SUM(COALESCE(pi_sub.quantity, 1))) AS UNSIGNED) AS total_performed
            FROM lab l
            LEFT JOIN payment_category pc ON pc.id = l.category_id
            LEFT JOIN (
                SELECT payment_id, item_id, SUM(quantity) / COUNT(*) AS quantity
                FROM payment_items
                GROUP BY payment_id, item_id
            ) pi_sub ON pi_sub.payment_id = l.invoice_id AND pi_sub.item_id = l.category_id
            WHERE l.category_id = " . $this->db->escape($test_id) . "
              AND (l.test_status != 'cancelled' AND (l.status != 'cancelled' OR l.status IS NULL))
              AND (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered'))
              AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) >= " . intval($start_time) . "
              AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) <= " . intval($end_time) . "
              AND (l.hospital_id = " . $this->db->escape($hospital_id) . " OR l.hospital_id IS NULL)
            GROUP BY report_date
            ORDER BY report_date ASC
        ";

        $records = $this->db->query($sql)->result();

        $grand_total = 0;
        foreach ($records as $r) {
            $r->date_formatted = date('d-m-Y', strtotime($r->report_date));
            $r->total_performed = intval($r->total_performed);
            $grand_total += $r->total_performed;
        }

        return [
            'test_id'   => $test_id,
            'test_name' => $test_name,
            'test_info' => $test_info,
            'records'   => $records,
            'summary'   => [
                'total_performed' => $grand_total,
                'active_days'     => count($records)
            ]
        ];
    }

    /**
     * REPORT 2: All Test Items Summary Report
     * Summary of all performed diagnostic test items in period, ranked by volume descending
     */
    public function getAllTestItemsSummaryReport($start_time, $end_time, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $sql = "
            SELECT 
                l.category_id AS test_id,
                COALESCE(pc.category, l.category_name, 'Unknown Test') AS test_name,
                COALESCE(pc.code, '') AS test_code,
                COALESCE(pc.c_price, 0) AS unit_price,
                CAST(ROUND(SUM(COALESCE(pi_sub.quantity, 1))) AS UNSIGNED) AS total_performed
            FROM lab l
            LEFT JOIN payment_category pc ON pc.id = l.category_id
            LEFT JOIN (
                SELECT payment_id, item_id, SUM(quantity) / COUNT(*) AS quantity
                FROM payment_items
                GROUP BY payment_id, item_id
            ) pi_sub ON pi_sub.payment_id = l.invoice_id AND pi_sub.item_id = l.category_id
            WHERE (l.test_status != 'cancelled' AND (l.status != 'cancelled' OR l.status IS NULL))
              AND (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered'))
              AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) >= " . intval($start_time) . "
              AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) <= " . intval($end_time) . "
              AND (l.hospital_id = " . $this->db->escape($hospital_id) . " OR l.hospital_id IS NULL)
            GROUP BY l.category_id, test_name, test_code, unit_price
            ORDER BY total_performed DESC, test_name ASC
        ";

        $records = $this->db->query($sql)->result();

        $grand_total = 0;
        $sl = 1;
        foreach ($records as $r) {
            $r->sl = $sl++;
            $r->total_performed = intval($r->total_performed);
            $grand_total += $r->total_performed;
        }

        return [
            'records' => $records,
            'summary' => [
                'total_performed' => $grand_total,
                'distinct_tests'  => count($records),
                'top_test'        => !empty($records) ? $records[0]->test_name : 'None'
            ]
        ];
    }

    /**
     * Date-wise breakdown for modal detail view in Report 2
     */
    public function getTestItemDateBreakdown($test_id, $start_time, $end_time, $hospital_id = null)
    {
        return $this->getSelectedTestItemReport($test_id, $start_time, $end_time, $hospital_id);
    }
}
