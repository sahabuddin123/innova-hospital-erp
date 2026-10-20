<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Patient_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertPatient($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('patient', $data2);
    }

    function getPatient()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('patient');
        return $query->result();
    }

    function getLimit()
    {
        $current = $this->db->get_where('patient', array('hospital_id' => $this->session->userdata('hospital_id')))->num_rows();
        $limit = $this->db->get_where('hospital', array('id' => $this->session->userdata('hospital_id')))->row()->p_limit;
        if (!is_numeric($limit)) {
            $limit = 0;
        }
        return $limit - $current;
    }

    function getPatientWithoutSearch($order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('patient');
        return $query->result();
    }

    function getPatientBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $query = $this->db->select('*')
            ->from('patient')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();;
        return $query->result();
    }

    function getPatientByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('patient');
        return $query->result();
    }

    function getPatientByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            //$this->db->order_by('id', 'desc');
            $this->db->order_by('name', 'asc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('patient')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR address LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();;
        return $query->result();
    }

    function getPatientById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('patient');
        return $query->row();
    }

    function getPatientByIonUserId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('ion_user_id', $id);
        $query = $this->db->get('patient');
        return $query->row();
    }

    function getPatientByEmail($email)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('email', $email);
        $query = $this->db->get('patient');
        return $query->row();
    }

    function updatePatient($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('patient', $data);
    }

    function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('patient');
    }

    function insertMedicalHistory($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('medical_history', $data2);
    }

    function getMedicalHistoryByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient_id', $id);
        $query = $this->db->get('medical_history');
        return $query->result();
    }

    function getMedicalHistory()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('medical_history');
        return $query->result();
    }

    function getMedicalHistoryBySearch($search)
    {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
            ->from('medical_history')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();;
        return $query->result();
    }

    function getMedicalHistoryByLimit($limit, $start)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('medical_history');
        return $query->result();
    }

    function getMedicalHistoryByLimitBySearch($limit, $start, $search)
    {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('medical_history')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();;
        return $query->result();
    }

    function getMedicalHistoryById($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('medical_history');
        return $query->row();
    }

    function updateMedicalHistory($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('medical_history', $data);
    }

    function insertDiagnosticReport($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('diagnostic_report', $data2);
    }

    function updateDiagnosticReport($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('diagnostic_report', $data);
    }

    function getDiagnosticReport()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('diagnostic_report');
        return $query->result();
    }

    function getDiagnosticReportById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('diagnostic_report');
        return $query->row();
    }

    function getDiagnosticReportByInvoiceId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('invoice', $id);
        $query = $this->db->get('diagnostic_report');
        return $query->row();
    }

    function getDiagnosticReportByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $query = $this->db->get('diagnostic_report');
        return $query->result();
    }

    function insertPatientMaterial($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('patient_material', $data2);
    }

    function getPatientMaterial()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('patient_material');
        return $query->result();
    }

    function getPatientMaterialWithoutSearch($order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('patient_material');
        return $query->result();
    }

    function getDocumentBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('patient_material')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%' OR title LIKE '%" . $search . "%' OR date_string LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();;
        return $query->result();
    }

    function getDocumentByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('patient_material');
        return $query->result();
    }

    function getDocumentByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('patient_material')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->where("(id LIKE '%" . $search . "%' OR patient_name LIKE '%" . $search . "%' OR patient_phone LIKE '%" . $search . "%' OR patient_address LIKE '%" . $search . "%' OR title LIKE '%" . $search . "%' OR date_string LIKE '%" . $search . "%')", NULL, FALSE)
            ->get();;
        return $query->result();
    }

    function getPatientMaterialById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('patient_material');
        return $query->row();
    }

    function getPatientMaterialByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $query = $this->db->get('patient_material');
        return $query->result();
    }

    function deletePatientMaterial($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('patient_material');
    }

    function deleteMedicalHistory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('medical_history');
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

    function getDueBalanceByPatientId($patient)
    {
        $query = $this->db->get_where('payment', array('patient' => $patient))->result();
        $deposits = $this->db->get_where('patient_deposit', array('patient' => $patient))->result();
        $balance = array();
        $deposit_balance = array();
        foreach ($query as $gross) {
            $balance[] = $gross->gross_total;
        }
        $balance = array_sum($balance);

        foreach ($deposits as $deposit) {
            $deposit_balance[] = $deposit->deposited_amount;
        }
        $deposit_balance = array_sum($deposit_balance);

        $bill_balance = $balance;

        return $due_balance = $bill_balance - $deposit_balance;
    }

    function getPatientInfoId($searchTerm)
    {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()
                ->like('name', $searchTerm)
                ->or_like('id', $searchTerm)
                ->or_like('phone', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            if (empty($user['age'])) {
                $dateOfBirth = $user['birthdate'];
                if (empty($dateOfBirth)) {
                    $age[0] = '0';
                } else {
                    if (strtotime($dateOfBirth)) {
                        $today = date("Y-m-d");
                        $diff = date_diff(date_create($dateOfBirth), date_create($today));
                        $age[0] = $diff->format('%y');
                    } else {
                        $age[0] = '';
                    }
                }
            } else {
                $age = explode('-', $user['age']);
            }
            $data[] = array("id" => $user['id'], "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . '- ' . lang('phone') . ': ' . $user['phone'] . '- ' . lang('age') . ': ' . $age[0] . ' years )');
        }
        return $data;
    }
    function getPatientInfo($searchTerm)
    {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()
                ->like('name', $searchTerm)
                ->or_like('id', $searchTerm)
                ->or_like('phone', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            if (empty($user['age'])) {
                $dateOfBirth = $user['birthdate'];
                if (empty($dateOfBirth)) {
                    $age[0] = '0';
                } else {
                    if (strtotime($dateOfBirth)) {
                        $today = date("Y-m-d");
                        $diff = date_diff(date_create($dateOfBirth), date_create($today));
                        $age[0] = $diff->format('%y');
                    } else {
                        $age[0] = '';
                    }
                }
            } else {
                $age = explode('-', $user['age']);
            }
            $data[] = array(
                "id" => $user['id'], 
                "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . '- ' . lang('phone') . ': ' . $user['phone'] . '- ' . lang('age') . ': ' . $age[0] . ' years )',
                "name" => $user['name'],
                "age" => $age[0],
                "phone" => $user['phone'],
                "sex" => $user['sex'],
                "gender" => $user['sex'],
                "email" => $user['email'],
                "address" => $user['address'],
                "bloodgroup" => $user['bloodgroup'],
                "birthdate" => $user['birthdate'],
                "add_date" => $user['add_date']
            );
        }
        return $data;
    }

    function getPatientinfoWithAddNewOption($searchTerm)
    {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->group_start()
                ->like('name', $searchTerm)
                ->or_like('id', $searchTerm)
                ->or_like('phone', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('patient');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        $data[] = array("id" => 'add_new', "text" => lang('add_new'));
        foreach ($users as $user) {

            if (empty($user['age'])) {
                $dateOfBirth = $user['birthdate'];
                if (empty($dateOfBirth)) {
                    $age[0] = '0';
                } else {
                    if (strtotime($dateOfBirth)) {
                        $today = date("Y-m-d");
                        $diff = date_diff(date_create($dateOfBirth), date_create($today));
                        $age[0] = $diff->format('%y');
                    } else {
                        $age[0] = '';
                    }
                }
            } else {
                $age = explode('-', $user['age']);
            }
            $data[] = array("id" => $user['id'], "text" => $user['name'] . ' (' . lang('id') . ': ' . $user['id'] . ' - ' . lang('phone') . ': ' . $user['phone'] . ' - ' . lang('age') . ': ' . $age[0] . ' ' . lang('years') . ')');
        }
        return $data;
    }

    function insertFolder($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('folder', $data2);
    }

    function getFolder()
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('folder');
        return $query->result();
    }

    function getFolderById($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('folder');
        return $query->row();
    }

    function updateFolder($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('folder', $data);
    }

    function getFolderByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient', $id);
        $query = $this->db->get('folder');
        return $query->result();
    }

    function getPatientMaterialByFolderId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('folder', $id);
        $query = $this->db->get('patient_material');
        return $query->result();
    }

    function deleteFolder($id)
    {

        $this->db->where(array('id' => $id));
        $this->db->delete('folder');
    }

    function deletePatientMaterialByFolderId($id)
    {
        $this->db->where('folder', $id);
        $this->db->delete('patient_material');
    }

    public function deleteImage($con)
    {
        // Delete image data 
        $delete = $this->db->delete($this->imgTbl, $con);

        // Return the status 
        return $delete ? true : false;
    }

    function getPatientMaterialByyPatientId($patient_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $patient_id);
        $query = $this->db->get('patient_material');
        return $query->result();
    }
    function insertVitalSign($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('vital_signs', $data2);
    }
    function updateVitalSign($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('vital_signs', $data);
    }
    function getVitalSignByPatientId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('patient_id', $id);
        $query = $this->db->get('vital_signs');
        return $query->result();
    }
    function getVitalSignById($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('vital_signs');
        return $query->row();
    }
    function deleteVitalSign($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('vital_signs');
    }
    function getOdontogram($pid)
    {
        $this->db->where('patient_id', $pid);
        $query = $this->db->get('odontogram')->row();

        if (!empty($query)) {
            return $query;
        } else {
            $value = array(
                'patient_id' => $pid, 'Tooth1' => 'white', 'Tooth2' => 'white', 'Tooth3' => 'white',
                'Tooth4' => 'white', 'Tooth5' => 'white', 'Tooth6' => 'white', 'Tooth7' => 'white', 'Tooth8' => 'white', 'Tooth1' => 'white',
                'Tooth9' => 'white', 'Tooth10' => 'white', 'Tooth11' => 'white', 'Tooth12' => 'white',
                'Tooth13' => 'white', 'Tooth14' => 'white', 'Tooth15' => 'white', 'Tooth16' => 'white', 'Tooth17' => 'white',
                'Tooth18' => 'white', 'Tooth19' => 'white', 'Tooth20' => 'white',
                'Tooth21' => 'white', 'Tooth22' => 'white', 'Tooth23' => 'white', 'Tooth24' => 'white',
                'Tooth25' => 'white', 'Tooth26' => 'white', 'Tooth27' => 'white', 'Tooth28' => 'white',
                'Tooth29' => 'white', 'Tooth30' => 'white', 'Tooth31' => 'white', 'Tooth32' => 'white',
                'description' => ''
            );
            $this->db->insert('odontogram', $value);
            $this->db->where('patient_id', $pid);
            $query = $this->db->get('odontogram')->row();
            return $query;
        }
    }
    function odontogram($pid, $value)
    {
        $this->db->where('patient_id', $pid);
        $this->db->update('odontogram', $value);
    }


    public function getConversationHistory($session_id)
    {
        // Ensure that $session_id is safe to use in a query
        $session_id = $this->session->userdata('hospital_id') . '-case-' . $this->db->escape_str($session_id);

        // Query the database for the history using the session ID
        $this->db->select('history');
        $this->db->from('gpt_memory');
        $this->db->where('session_id', $session_id);
        $query = $this->db->get();

        // Check if a history was found
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return json_decode($row['history'], true); // Return the history as a PHP array
        } else {
            return []; // Return an empty array if no history was found
        }
    }
}
