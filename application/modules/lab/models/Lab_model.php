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
        $this->db->where("(id LIKE '%" . $search . "%' OR invoice_id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
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
        $this->db->where("(id LIKE '%" . $search . "%'  OR invoice_id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%'OR doctor_name LIKE '%" . $search . "%'OR date_string LIKE '%" . $search . "%')", NULL, FALSE);
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
}
