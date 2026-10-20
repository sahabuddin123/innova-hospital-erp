<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function getEmailSettingsById($id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('email_settings');
        return $query->row();
    }

    function getEmailByUser($user) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('user', $user);
        $query = $this->db->get('email');
        return $query->result();
    }

    function getEmailSettings() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('email_settings');
        return $query->row();
    }

    function updateEmailSettings($id,$data) {
        $this->db->where('id', $id);
        $this->db->update('email_settings', $data);
    }

    function addEmailSettings($data) {
        $this->db->insert('email_settings', $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('email');
    }

    function insertEmail($data) {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('email', $data2);
    }

    function getEmail() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('email');
        return $query->result();
    }

    function getAutoEmailTemplate() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('autoemailtemplate');
        return $query->result();
    }

    function getAutoEmailTemplateBySearch($search) {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
                ->from('autoemailtemplate')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR message LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        return $query->result();
    }

    function getAutoEmailTemplateByLimit($limit, $start) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'asc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('autoemailtemplate');
        return $query->result();
    }

    function getAutoEmailTemplateByLimitBySearch($limit, $start, $search) {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('autoemailtemplate')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR message LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        return $query->result();
    }

    function getAutoEmailTemplateById($id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('autoemailtemplate');
        return $query->row();
    }

    function getAutoEmailTemplateTag($type) {
        $this->db->order_by('id', 'desc');
        $this->db->where('type', $type);
        $query = $this->db->get('autoemailshortcode');
        return $query->result();
    }

    function updateAutoEmailTemplate($data, $id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->update('autoemailtemplate', $data);
    }

    function getManualEmailTemplate($type) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('type', $type);
        $query = $this->db->get('manual_email_template');
        return $query->result();
    }

    function getManualEmailShortcodeTag($type) {
        $this->db->order_by('id', 'desc');
        $this->db->where('type', $type);
        $query = $this->db->get('manualemailshortcode');
        return $query->result();
    }

    function getManualEmailTemplateById($id, $type) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $query = $this->db->get('manual_email_template');
        return $query->row();
    }

    function addManualEmailTemplate($data) {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('manual_email_template', $data2);
    }

    function updateManualEmailTemplate($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('manual_email_template', $data);
    }

    function getManualEmailTemplateBySearch($search, $type) {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
                ->from('manual_email_template')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where('type', $type)
                ->where("(id LIKE '%" . $search . "%' OR message LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        return $query->result();
    }

    function getManualEmailTemplateByLimit($limit, $start, $type) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('type', $type);
        $this->db->limit($limit, $start);
        $query = $this->db->get('manual_email_template');
        return $query->result();
    }

    function getManualEmailTemplateByLimitBySearch($limit, $start, $search, $type) {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('manual_email_template')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where('type', $type)
                ->where("(id LIKE '%" . $search . "%' OR message LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        return $query->result();
    }

    function deleteManualEmailTemplate($id) {
        $this->db->where('id', $id);
        $this->db->delete('manual_email_template');
    }

    function getManualEmailTemplateListSelect2($searchTerm, $type) {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->like('name', $searchTerm);
            $this->db->where('type', $type);
            $fetched_records = $this->db->get('manual_email_template');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(20);
            $fetched_records = $this->db->get('manual_email_template');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        $current_user_id = $this->ion_auth->user()->row()->id;
        foreach ($users as $user) {
            if ($data[] = $user['added_by'] == $current_user_id) {
                $data[] = array("id" => $user['id'], "text" => $user['name']);
            }
        }
        return $data;
    }

    function getAutoEmailByType($type) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('type', $type);
        $query = $this->db->get('autoemailtemplate');
        return $query->row();
    }

    function getAdminEmailSettingsById() {
        $this->db->where('hospital_id', 'superadmin');
        $query = $this->db->get('email_settings');
        return $query->row();
    }

    function updateAdminEmailSettings($data) {
        $this->db->where('hospital_id', 'superadmin');
        $this->db->update('email_settings', $data);
    }

    function getAutoEmailByTypee($type) {
        $this->db->where('hospital_id', 'superadmin');
        $this->db->where('type', $type);
        $query = $this->db->get('autoemailtemplate');
        return $query->row();
    }

    function getContactEmailSettingsById() {
        $this->db->where('hospital_id', 'contactus');
        $query = $this->db->get('email_settings');
        return $query->row();
    }

    function getContactEmailSettings() {
        $this->db->where('hospital_id', 'contactus');
        $query = $this->db->get('email_settings');
        return $query->row();
    }
    
    
    function addContactEmailSettings($data) {       
        $data1 = array('hospital_id' => 'contactus');
        $data2 = array_merge($data, $data1);
        $this->db->insert('email_settings', $data2);
    }

    function updateContactEmailSettings($data) {
        $this->db->where('hospital_id', 'contactus');
        $this->db->update('email_settings', $data);
    }

    function getContactEmailByType($type) {
       
        $this->db->where('type', $type);
        $query = $this->db->get('autoemailtemplate');
        return $query->row();
    }

    function getHospitalEmailSettings() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('email_settings');
        return $query->result();
    }

    function getHospitalEmailSettingsById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('email_settings');
        return $query->row();
    }

    function getEmailSettingsByType($type) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('type', $type);
        $query = $this->db->get('email_settings');
        return $query->row();
    }

    function getAdminEmailSettingsByIdByType($type) {
        $this->db->where('hospital_id', 'superadmin');
        $this->db->where('type', $type);
        $query = $this->db->get('email_settings');
        return $query->row();
    }

    function getEmailSettingsByTypeByHospital($type, $hospital) {
        $this->db->where('hospital_id', $hospital);
        $this->db->where('type', $type);
        $query = $this->db->get('email_settings');
        return $query->row();
    }
    function getPharmacistInfo($searchTerm)
    {
        if (!empty($searchTerm)) {
            $query = $this->db->select('*')
                ->from('pharmacist')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $searchTerm . "%' OR name LIKE '%" . $searchTerm . "%')", NULL, FALSE)
                ->get();
            $users = $query->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('pharmacist');
            $users = $fetched_records->result_array();
        }


        if ($this->ion_auth->in_group(array('Pharmacist'))) {
            $nurse_ion_id = $this->ion_auth->get_user_id();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('ion_user_id', $nurse_ion_id);
            $fetched_records = $this->db->get('pharmacist');
            $users = $fetched_records->result_array();
        }


        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['name'] . ' (' . lang('email') . ': ' . $user['email'] . ')');
        }
        return $data;
    }
}
