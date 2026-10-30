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
}
