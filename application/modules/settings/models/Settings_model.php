<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertSettings($hospital_settings_data)
    {
        $this->db->insert('settings', $hospital_settings_data);
    }

    function getSettings()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('settings');
        return $query->row();
    }

    function updateSettings($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('settings', $data);
    }

    function updateHospitalSettings($id, $data)
    {
        $this->db->where('hospital_id', $id);
        $this->db->update('settings', $data);
    }

    function getSubscription()
    {
        $this->db->where('id', $this->hospital_id);
        $query = $this->db->get('hospital');
        return $query->row();
    }

    function getHospitalPaymentsById($id)
    {
        return $this->db->where('hospital_user_id', $id)
            ->get('hospital_payment')->row();
    }

    function getStaffinfoWithAddNewOption($searchTerm)
    {
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
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()->like('name', $searchTerm)->or_like('id', $searchTerm)->group_end();
            $fetched_records6 = $this->db->get('doctor')->result_array();
            $users = array_merge($fetched_records1, $fetched_records2, $fetched_records3, $fetched_records4, $fetched_records5, $fetched_records6);
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
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(2);
            $fetched_records6 = $this->db->get('doctor')->result_array();
            $users = array_merge($fetched_records1, $fetched_records2, $fetched_records3, $fetched_records4, $fetched_records5, $fetched_records6);
        }

        $data = array();

        foreach ($users as $user) {
            $data[] = array("id" => $user['ion_user_id'], "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . ')');
        }
        return $data;
    }

    function getColumnOrder($order, $columns_valid = array())
    {
        $col = 0;
        $dir = "";
        $values = array();
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }
        if ($dir != "asc" && $dir != "desc") {
            $dir = "asc";
        }
        if (!isset($columns_valid[$col])) {
            $order = null;
        } else {
            $order = $columns_valid[$col];
        }
        $values[] = $dir;
        $values[] = $order;
        return $values;
    }



    function getGoogleReCaptchaSettings()
    {
        $query = $this->db->get('google_captcha');
        $row = $query ? $query->row() : null;
        if (!empty($row) && !empty($row->site_key)) {
            return $row;
        }
        return (object)[
            'id' => 1,
            'site_key' => '6LdWmOEtAAAAAOr0-9VcyljiEHYn4SurE1jrlk3u',
            'secret_key' => '6LdWmOEtAAAAAPs9rIdEEJ19GIM6qZy6nbl0IJ8z'
        ];
    }

    function addGoogleReCaptcha($data)
    {
        $this->db->insert('google_captcha', $data);
    }

    function updateGoogleReCaptcha($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('google_captcha', $data);
    }


    function dateToFrench($date, $format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'AM', 'PM', 'To');
        $french_days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche', 'UN M', 'PM', 'À');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date))));
    }



    function convert_to_24h($time)
    {
        $time_parts = explode(':', $time);
        $hours = (int) $time_parts[0];
        $minutes = (int) $time_parts[1];
        $ampm = substr($time, -2);
        if ($ampm == 'PM' && $hours != 12) {
            $hours += 12;
        } elseif ($ampm == 'AM' && $hours == 12) {
            $hours = 0;
        }
        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    }


    function convert_to_12h($time)
    {
        $time_parts = explode(':', $time);
        $hours = (int) $time_parts[0];
        $minutes = (int) $time_parts[1];
        $ampm = 'AM';
        if ($hours >= 12) {
            $ampm = 'PM';
            if ($hours > 12) {
                $hours -= 12;
            }
        }
        if ($hours == 0) {
            $hours = 12;
        }
        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ' ' . $ampm;
    }


    function checkPurchaseCodeAndBaseUrl($data)
    {
        $this->db->where($data);
        $query = $this->db->get('verify');
        return $query->row();
    }
    // function checkPurchaseCode($purchase_code)
    // {
    //     $this->db->where('purchase_code', $purchase_code);
    //     $query = $this->db->get('verify');
    //     return $query->row();
    // }

    function getPurchaseCode()
    {
        $this->db->where('hospital_id', 'superadmin');
        $query = $this->db->get('settings');
        return $query->row()->codec_purchase_code;
    }

    function checkBaseUrl($base_url)
    {
        $this->db->where('base_url', $base_url);
        $query = $this->db->get('verify');
        return $query->row();
    }

    function insertPurcaseCode($data)
    {
        $this->db->insert('verify', $data);
    }





    function verify()
    {

        $personalToken = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row()->token;
        $purchase_code = $this->getPurchaseCode();
        $code = trim($purchase_code);
        if (!preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $code)) {
            throw new Exception("Invalid purchase code");
        }
        $ch = curl_init();

        try {
            curl_setopt_array($ch, array(
                CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$personalToken}",
                    "User-Agent: Purchase code verification script"
                )
            ));

            $response = @curl_exec($ch);
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($responseCode == 200) {
                $response = json_decode($response);
                $data['supported_until'] = strtotime($response->supported_until);
                $data['verified'] = true;
            }
            return $data;
        } catch (Exception $e) {
        }
    }


    function addLanguage($data)
    {
        $this->db->insert('language', $data);
    }

    function getLanguages()
    {
        $query = $this->db->get('language');
        return $query->result();
    }

    function getLanguageById($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('language');
        return $query->row();
    }
    function updateLanguage($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('language', $data);
    }

    function getLanguageByName($language_name)
    {
        $this->db->where('language', $language_name);
        $query = $this->db->get('language');
        return $query->row();
    }
}
