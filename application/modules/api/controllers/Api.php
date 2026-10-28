<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
include("./vendor/autoload.php");

use Omnipay\Omnipay;

class Api extends MX_Controller
{

    public $hospitalID;

    function __construct()
    {
        parent::__construct();
        $this->load->model('api/api_model');
        $this->load->library(array('ion_auth', 'form_validation'));
        require APPPATH . 'third_party/stripe/stripe-php2/init.php';
        $this->load->module('paypal');
    }

    public function authenticateNew()
    {
        $identity = $this->input->post('email');
        $password = $this->input->post('password');
        $remember = false;
        $group = $this->input->post('group');

        if (empty($identity) || empty($password)) {
            $data = array('idToken' => null, 'ion_id' => null, 'expiresIn' => null, 'message' => 'failed1', 'error' => 'Missing credentials');
            echo json_encode($data);
            return;
        }

        if ($this->ion_auth->login($identity, $password, $remember)) {
            $ion_user_id = $this->ion_auth->get_user_id();
            if ($group == 'Doctor' || $group == 'doctor') {
                $doctor_details = $this->db->get_where('doctor', array('ion_user_id' => $ion_user_id))->row();
                $data['user_id'] = !empty($doctor_details) ? $doctor_details->id : null;
            } else {
                $patient_details = $this->db->get_where('patient', array('ion_user_id' => $ion_user_id))->row();
                $data['user_id'] = !empty($patient_details) ? $patient_details->id : null;
            }

            $data['ion_id'] = $ion_user_id;
            $data['hospital_id'] = $this->getHospitalID($ion_user_id);
            $data['message'] = 'successful';
            $data['idToken'] = bin2hex(random_bytes(16));
            $data['expiresIn'] = 86400;
            $data['error'] = null;
            echo json_encode($data);
        } else {
            $data = array('idToken' => null, 'ion_id' => null, 'expiresIn' => null, 'message' => 'failed1', 'error' => 'Invalid email or password');
            echo json_encode($data);
        }
    }

    public function authenticate()
    {
        $identity = $this->input->post('email');
        $password = $this->input->post('password');
        $remember = false;
        $group = $this->input->post('group');

        if (empty($identity) || empty($password)) {
            $data = array('idToken' => null, 'ion_id' => null, 'expiresIn' => null, 'message' => 'failed2', 'error' => 'Missing credentials');
            echo json_encode($data);
            return;
        }

        if ($this->ion_auth->login($identity, $password, $remember)) {
            $id = $this->ion_auth->get_user_id();
            $group_id = $this->db->get_where('users_groups', array('user_id' => $id))->row();
            $group2 = !empty($group_id) ? $this->db->get_where('groups', array('id' => $group_id->group_id))->row() : null;
            if (!empty($group2) && strtolower($group2->name) == strtolower($group)) {
                if (strtolower($group) == 'doctor') {
                    $user_id = $this->db->get_where('doctor', array('ion_user_id' => $id))->row();
                } else {
                    $user_id = $this->db->get_where('patient', array('ion_user_id' => $id))->row();
                }

                $data['message'] = 'successful';
                $data['idToken'] = bin2hex(random_bytes(16));
                $data['ion_id'] = $id;
                $data['user_id'] = !empty($user_id) ? $user_id->id : null;
                $data['expiresIn'] = 86400;
                $data['error'] = null;
                $data['hospital_id'] = $this->getHospitalID($id);
                echo json_encode($data);
            } else {
                $data['message'] = 'failed1';
                $data['error'] = 'Group mismatch';
                echo json_encode($data);
            }
        } else {
            $data['email'] = $identity;
            $data['message'] = 'failed2';
            echo json_encode($data);
            // echo json_encode("c");
        }
    }

    //Patient Login Function
    function patientLogin()
    {
        $identity = $this->input->post('email');
        $password = $this->input->post('password');
        $remember = (bool) $this->input->post('remember');
        $validate_data = array('identity' => $identity, 'password' => $password);
        $this->form_validation->set_data($validate_data);
        $this->form_validation->set_rules('identity', 'Identity', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == true) {
            if ($this->ion_auth->login($identity, $password, $remember)) {
                $data['message'] = 'successful';
                $data['ion_id'] = $this->ion_auth->get_user_id();
                $data['hospital_id'] = $this->getHospitalID($data['ion_id']);
                echo json_encode($data);
            } else {
                $data['message'] = 'wrong';
                echo json_encode($data);
            }
        } else {
            $data['message'] = 'invalid';
            echo json_encode($data);
        }
    }

    //Function to logout
    function logout()
    {
        $logout = $this->ion_auth->logout();
        $data['message'] = 'invalid';
        echo json_encode($data);
    }

    //Get Profile Function
    function getProfile()
    {
        $id = $this->input->get('id');
        $data = $this->api_model->apiGetProfileById($id);

        echo json_encode($data);
    }

    function getPatientProfile()
    {
        $id = $this->input->post('id');
        $data = $this->db->get_where('patient', array('ion_user_id' => $id))->row();

        echo json_encode($data);
    }

    function getDoctorProfile()
    {
        $id = $this->input->post('id');
        $data = $this->db->get_where('doctor', array('ion_user_id' => $id))->row();

        echo json_encode($data);
    }

    //Update Profile Function
    function updateProfile()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $password = $this->input->post('password');
        $email = $this->input->post('email');

        $data['profile'] = $this->api_model->apiGetProfileById($id);
        if ($data['profile']->email != $email) {
            if ($this->ion_auth->email_check($email)) {
                $data = 'emailExists';
                echo json_encode($data);
            }
        }

        $validate_data = array('name' => $name, 'password' => $password, 'email' => $email);
        $this->form_validation->set_data($validate_data);
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        }
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $data = 'failed';
            echo json_encode($data);
            //$data['profile'] = $this->api_model->apiGetProfileById($id);
        } else {
            $data2 = array();
            $data2 = array(
                'name' => $name,
                'email' => $email,
            );

            $username = $name;
            $ion_user_id = $id;
            $group_id = $this->api_model->getUsersGroups($ion_user_id)->row()->group_id;
            $group_name = $this->api_model->getGroups($group_id)->row()->name;
            $group_name = strtolower($group_name);
            if (empty($password)) {
                $password = $this->db->get_where('users', array('id' => $ion_user_id))->row()->password;
            } else {
                $password = $this->ion_auth_model->hash_password($password);
            }
            $this->api_model->updateIonUser($username, $email, $password, $ion_user_id);
            $this->api_model->updateProfile($ion_user_id, $data2, $group_name);

            $data = 'successful';
            echo json_encode($data);
            //$data['profile'] = $this->api_model->apiGetProfileById($id);
        }
    }

    function updatePatientProfile()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');

        $data['profile'] = $this->api_model->apiGetProfileById($id);

        $validate_data = array('name' => $name, 'password' => $password, 'email' => $email);
        $this->form_validation->set_data($validate_data);
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        }
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $data = 'failed';
            echo json_encode($data);
        } else {
            $data2 = array();
            $data2 = array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address
            );

            $username = $name;
            $ion_user_id = $id;
            $group_id = $this->api_model->getUsersGroups($ion_user_id)->row()->group_id;
            $group_name = $this->api_model->getGroups($group_id)->row()->name;
            $group_name = strtolower($group_name);
            if (empty($password)) {
                $password = $this->db->get_where('users', array('id' => $ion_user_id))->row()->password;
            } else {
                $password = $this->ion_auth_model->hash_password($password);
            }
            $this->api_model->updateIonUser($username, $email, $password, $ion_user_id);
            $this->api_model->updateProfile($ion_user_id, $data2, $group_name);

            $data = 'successful';
            echo json_encode($data);
            //$data['profile'] = $this->api_model->apiGetProfileById($id);
        }
    }

    function updateDoctorProfile()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $department = $this->input->post('department');
        $phone = $this->input->post('phone');

        $data['profile'] = $this->api_model->apiGetProfileById($id);

        $validate_data = array('name' => $name, 'password' => $password, 'email' => $email);
        $this->form_validation->set_data($validate_data);
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        }
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $data = 'failed';
            echo json_encode($data);
        } else {
            $data2 = array();
            $data2 = array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'department' => $department
            );

            $username = $name;
            $ion_user_id = $id;
            $group_id = $this->api_model->getUsersGroups($ion_user_id)->row()->group_id;
            $group_name = $this->api_model->getGroups($group_id)->row()->name;
            $group_name = strtolower($group_name);
            if (empty($password)) {
                $password = $this->db->get_where('users', array('id' => $ion_user_id))->row()->password;
            } else {
                $password = $this->ion_auth_model->hash_password($password);
            }
            $this->api_model->updateIonUser($username, $email, $password, $ion_user_id);
            $this->api_model->updateProfile($ion_user_id, $data2, $group_name);

            $data = 'successful';
            echo json_encode($data);
        }
    }

    //Get Donor Info Function
    function getDonor()
    {
        //user ion id
        $id = $this->input->get('user_ion_id');
        $this->hospitalID = $this->getHospitalID($id);
        $data['donors'] = $this->api_model->getDonor($this->hospitalID);
        $data['groups'] = $this->api_model->getBloodBank($this->hospitalID);
        $data['message'] = 'successful';
        echo json_encode($data);
    }

    //Get Patient Specific Report
    function myReport()
    {
        $userID = $this->input->get('user_ion_id');
        $this->hospitalID = $this->getHospitalID($userID);
        $id = $this->input->get('id');
        $data['report'] = $this->api_model->getReportById($id, $this->hospitalID);
        $data['message'] = 'successful';
        echo json_encode($data);
    }

    //Get Patient all reports
    function myReports()
    {
        $data['reports'] = array();
        $userId = $this->input->get('user_ion_id');
        $this->hospitalID = $this->getHospitalID($userId);
        $reports = $this->api_model->getReport($this->hospitalID);
        foreach ($reports as $report) {
            if ($userId == explode('*', $report->patient)[1]) {
                array_push($data['reports'], $report);
            }
        }
        echo json_encode($data);
    }

    //Get Patient Document
    function myDocument()
    {
        $patient_ion_id = $this->input->get('id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient_id = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        $data['files'] = $this->api_model->getPatientMaterialByPatientId($patient_id, $this->hospitalID);
        $data['message'] = 'successful';
        echo json_encode($data);
    }

    //Get Patient Case List Function
    function myCaseList()
    {
        $patient_ion_id = $this->input->get('id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient_id = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        $data['medical_histories'] = $this->api_model->getMedicalHistoryByPatientId($patient_id, $this->hospitalID);
        $data['message'] = 'successful';
        echo json_encode($data);
    }

    function caseListByPatientId()
    {
        $patient_id = $this->input->get('id');
        $patient_ion_id = $this->api_model->getPatientByIdWithoutHospitalId($patient_id)->ion_user_id;
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $data = $this->api_model->getMedicalHistoryByPatientId($patient_id, $this->hospitalID);
        echo json_encode($data);
    }

    // function documentsByPatientId()
    // {
    //     $patient_id = $this->input->get('id');
    //     $patient_ion_id = $this->api_model->getPatientByIdWithoutHospitalId($patient_id)->ion_user_id;
    //     $this->hospitalID = $this->getHospitalID($patient_ion_id);
    //     $data = $this->api_model->getPatientMaterialByPatientId($patient_id, $this->hospitalID);
    //     echo json_encode($data);
    // }

    //Get Prescription Function
    function getPatientPrescription()
    {
        // $patient_ion_id = 859;
        $patient_ion_id = $this->input->post('id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient_id = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        $data = $this->api_model->getPrescriptionByPatientId($patient_id, $this->hospitalID);
        echo json_encode($data);
    }

    function getDoctorPrescription()
    {
        // $patient_ion_id = 859;
        $doctor_ion_id = $this->input->post('id');
        $this->hospitalID = $this->getHospitalID($doctor_ion_id);
        $doctor_id = $this->api_model->getDoctorByIonUserId($doctor_ion_id, $this->hospitalID)->id;
        $data = $this->api_model->getPrescriptionByDoctorId($doctor_id, $this->hospitalID);
        echo json_encode($data);
    }

    function deleteAppointment()
    {
        $id = $this->input->get('id');
        $this->db->where('id', $id);
        $this->db->delete('appointment');
        echo json_encode('success');
    }

    //Delete Prescription
    function deletePrescription()
    {
        $id = $this->input->get('id');
        $patient_ion_id = 764;
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $data['prescription'] = $this->api_model->getPrescriptionById($id, $this->hospitalID);
        if (!empty($data['prescription']->hospital_id)) {
            if ($data['prescription']->hospital_id != $this->hospitalID) {
                $data['message'] = 'failed';
                echo json_encode($data);
            } else {
                $this->api_model->deletePrescription($id);
                $data['message'] = 'successful';
                echo json_encode($data);
            }
        } else {
            $data['message'] = 'failed';
            echo json_encode($data);
        }
    }

    //Get Lab By filtering
    function myLab()
    {

        $userId = $this->input->post('user_ion_id');
        $group = $this->input->post('group');
        $this->hospitalID = $this->getHospitalID($userId);
        $data['labs'] = array();
        $labs = $this->api_model->getLab($this->hospitalID);

        if ($group == 'Patient') {
            $patient_user_id = $userId;
            $patient_id = $this->api_model->getPatientByIonUserId($patient_user_id, $this->hospitalID)->id;
        }

        foreach ($labs as $lab) {
            if ($patient_id == $lab->patient) {
                $date = date('d-m-y', $lab->date);

                $doctor_info = $this->api_model->getDoctorById($lab->doctor, $this->hospitalID);
                if (!empty($doctor_info)) {
                    $doctor = $doctor_info->name;
                } else {
                    if (!empty($lab->doctor_name)) {
                        $doctor = $lab->doctor_name;
                    } else {
                        $doctor = ' ';
                    }
                }


                $patient_info = $this->api_model->getPatientById($lab->patient, $this->hospitalID);
                if (!empty($patient_info)) {
                    $patient_details = $patient_info->name . '</br>' . $patient_info->address . '</br>' . $patient_info->phone . '</br>';
                } else {
                    $patient_details = ' ';
                }
                $data['labs'] = array(
                    'id' => $lab->id,
                    'name' => $patient_info->name,
                    'address' => $patient_info->address,
                    'phone' => $patient_info->phone,
                    'date' => $date,
                );
            }
        }

        echo json_encode($data);
    }

    //Get Invoice Function
    function invoice()
    {
        $userId = $this->input->get('user_ion_id');
        $this->hospitalID = $this->getHospitalID($userId);
        $id = $this->input->get('invoice_id');
        $data['settings'] = $this->api_model->getSettings($this->hospitalID);
        $data['lab'] = $this->api_model->getLabById($id, $this->hospitalID);

        $data['message'] = 'successful';

        echo json_encode($data);
    }

    //to view a prescription
    function viewPrescription()
    {
        $userId = $this->input->post('user_ion_id');
        $this->hospitalID = $this->getHospitalID($userId);
        $id = $this->input->post('id');
        $data['prescription'] = $this->api_model->getPrescriptionById($id, $this->hospitalID);
        if (!empty($data['prescription']->hospital_id)) {
            if ($data['prescription']->hospital_id != $this->hospitalID) {
                $data['message'] = 'invalid';
                echo json_encode($data);
            } else {
                $data['settings'] = $this->api_model->getSettings($this->hospitalID);
                $data['doctor'] = $this->api_model->getDoctorById($data['prescription']->doctor, $this->hospitalID);
                $data['user'] = $this->api_model->getPatientById($data['prescription']->patient, $this->hospitalID);
                $data['message'] = 'successful';
                echo json_encode($data);
            }
        } else {
            $data['message'] = 'failed';
            echo json_encode($data);
        }
    }

    function downloadPrescription()
    {
        $userId = $this->input->get('user_ion_id');
        $this->hospitalID = $this->getHospitalID($userId);
        $id = $this->input->get('id');
        $data['prescription'] = $this->api_model->getPrescriptionById($id, $this->hospitalID);
        $data['settings'] = $this->api_model->getSettings($this->hospitalID);
        $data['doctor'] = $this->api_model->getDoctorById($data['prescription']->doctor, $this->hospitalID);
        $data['patient'] = $this->api_model->getPatientById($data['prescription']->patient, $this->hospitalID);
        $data['redirect'] = 'download';
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetHTMLFooter('
        <div style="font-weight: bold; font-size: 8pt; font-style: italic;">
             ' . lang('user') . ' : ' . $this->ion_auth->user()->row()->username . '
        </div>', 'O');
        $html = $this->load->view('prescription/prescription_view_download', $data, true);
        $mpdf->WriteHTML($html);

        $filename = "precripstion--00" . $id . ".pdf";
        $mpdf->Output($filename, 'D');
        // $this->load->view('home/dashboard'); // just the header file
        //   $this->load->view('invoice', $data);
        //  $this->load->view('home/footer'); // just the footer fi
    }

    //Add New Appointment Function
    public function addNewAppointment()
    {
        $id = $this->input->post('id');
        $patient = $this->input->post('patient');
        $doctor = $this->input->post('doctor');
        $date = $this->input->post('date');
        $time_slot = $this->input->post('time_slot');
        $remarks = $this->input->post('remarks');
        $sms = $this->input->post('sms');
        $status = $this->input->post('status');
        $redirect = $this->input->post('redirect');
        $request = $this->input->post('request');
        $p_name = $this->input->post('p_name');
        $p_email = $this->input->post('p_email');
        $p_phone = $this->input->post('p_phone');
        $p_age = $this->input->post('p_age');
        $p_gender = $this->input->post('p_gender');
        $name = $this->input->post('name');
        $ion_user_id = $this->findDoctorIonId($doctor);

        $this->hospitalID = $this->getHospitalID($ion_user_id);
        if (!empty($date)) {
            $date = strtotime($date);
        }


        $time_slot = $time_slot;

        $time_slot_explode = explode('To', $time_slot);

        $s_time = trim($time_slot_explode[0]);
        $e_time = trim($time_slot_explode[1]);

        $remarks = $remarks;

        $sms = $sms;

        $status = $status;

        $redirect = $redirect;

        $request = $request;

        if (empty($request)) {
            $request = '';
        }


        $user = $ion_user_id;

        if ($this->ion_auth->in_group(array('Patient'))) {
            $user = '';
        }



        if ((empty($id))) {
            $add_date = date('m/d/y');
            $registration_time = time();
            $patient_add_date = $add_date;
            $patient_registration_time = $registration_time;
        } else {
            $add_date = $this->api_model->getAppointmentById($id, $this->hospitalID)->add_date;
            $registration_time = $this->api_model->getAppointmentById($id, $this->hospitalID)->registration_time;
        }

        $s_time_key = $this->getArrayKey($s_time);

        $p_name = $p_name;
        $p_email = $p_email;
        if (empty($p_email)) {
            $p_email = $p_name . '-' . rand(1, 1000) . '-' . $p_name . '-' . rand(1, 1000) . '@example.com';
        }
        if (!empty($p_name)) {
            $password = $p_name . '-' . rand(1, 100000000);
        }
        $p_phone = $p_phone;
        $p_age = $p_age;
        $p_gender = $p_gender;
        $patient_id = rand(10000, 1000000);

        $validate_data = array(
            'p_name' => $p_name,
            'p_phone' => $p_phone,
            'patient' => $patient,
            'doctor' => $doctor,
            'date' => $date,
            's_time' => $s_time,
            'e_time' => $e_time,
            'remarks' => $remarks
        );

        $this->load->library('form_validation');
        $this->form_validation->set_data($validate_data);

        if ($patient == 'add_new') {
            $this->form_validation->set_rules('p_name', 'Patient Name', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            $this->form_validation->set_rules('p_phone', 'Patient Phone', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        }

        // Validating Name Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Password Field
        $this->form_validation->set_rules('doctor', 'Doctor', 'trim|required|min_length[1]|max_length[100]|xss_clean');

        // Validating Email Field
        $this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Email Field
        $this->form_validation->set_rules('s_time', 'Start Time', 'trim|min_length[1]|max_length[100]|xss_clean');
        // Validating Email Field
        $this->form_validation->set_rules('e_time', 'End Time', 'trim|min_length[1]|max_length[100]|xss_clean');
        // Validating Address Field   
        $this->form_validation->set_rules('remarks', 'Remarks', 'trim|min_length[1]|max_length[1000]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            // $data['patients'] = $this->api_model->getPatient($this->hospitalID);
            // $data['doctors'] = $this->api_model->getDoctor($this->hospitalID);
            // $data['settings'] = $this->api_model->getSettings($this->hospitalID);
            // $data['message'] = "invalid";
            // return $data;
            echo json_encode($data['message'] = 'failed');
        } else {

            if ($patient == 'add_new') {

                $limit = $this->api_model->getLimit($this->hospitalID);
                if ($limit <= 0) {
                    $data['message'] = "Limit Excessed";
                    return $data;
                }

                $data_p = array(
                    'patient_id' => $patient_id,
                    'name' => $p_name,
                    'email' => $p_email,
                    'phone' => $p_phone,
                    'sex' => $p_gender,
                    'age' => $p_age,
                    'add_date' => $patient_add_date,
                    'registration_time' => $patient_registration_time,
                    'how_added' => 'from_appointment'
                );
                $username = $p_name;
                // Adding New Patient
                if ($this->ion_auth->email_check($p_email)) {
                    $data['message'] = "Email exits";
                    return $data;
                } else {
                    $dfg = 5;
                    $this->ion_auth->register($username, $password, $p_email, $dfg);
                    $ion_user_id = $this->db->get_where('users', array('email' => $p_email))->row()->id;
                    $this->api_model->insertPatient($data_p, $this->hospitalID);
                    $patient_user_id = $this->db->get_where('patient', array('email' => $p_email))->row()->id;
                    $id_info = array('ion_user_id' => $ion_user_id);
                    $this->api_model->updatePatient($patient_user_id, $id_info);
                    $this->api_model->addHospitalIdToIonUser($ion_user_id, $this->hospitalID);
                }

                $patient = $patient_user_id;
            }

            $patient_phone = $this->api_model->getPatientById($patient, $this->hospitalID)->phone;

            $temp_phone = str_replace('+', '', $patient_phone);

            if (empty($id)) {
                $room_id = 'hms-meeting-' . $temp_phone . '-' . rand(10000, 1000000) . '-' . $this->hospital_id;
                $live_meeting_link = 'https://meet.jit.si/' . $room_id;
            } else {
                $appointment_details = $this->api_model->getAppointmentById($id);
                $room_id = $appointment_details->room_id;
                $live_meeting_link = $appointment_details->live_meeting_link;
            }




            $patientname = $this->api_model->getPatientById($patient, $this->hospitalID)->name;
            $doctorname = $this->api_model->getDoctorById($doctor, $this->hospitalID)->name;
            $data = array();
            $data = array(
                'patient' => $patient,
                'patientname' => $patientname,
                'doctor' => $doctor,
                'doctorname' => $doctorname,
                'date' => $date,
                's_time' => $s_time,
                'e_time' => $e_time,
                'time_slot' => $time_slot,
                'remarks' => $remarks,
                'add_date' => $add_date,
                'registration_time' => $registration_time,
                'status' => $status,
                's_time_key' => $s_time_key,
                'user' => $user,
                'request' => $request,
                'room_id' => $room_id,
                'live_meeting_link' => $live_meeting_link
            );
            $username = $name;
            if (empty($id)) {     // Adding New department
                $this->api_model->insertAppointment($data, $this->hospitalID);

                $patient_doctor = $this->api_model->getPatientById($patient, $this->hospitalID)->doctor;

                $patient_doctors = explode(',', $patient_doctor);

                if (!in_array($doctor, $patient_doctors)) {
                    $patient_doctors[] = $doctor;
                    $doctorss = implode(',', $patient_doctors);
                    $data_d = array();
                    $data_d = array('doctor' => $doctorss);
                    $this->api_model->updatePatient($patient, $data_d);
                }
                $this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status);
            } else { // Updating department
                $previous_status = $this->api_model->getAppointmentById($id, $this->hospitalID)->status;
                if ($previous_status != "Confirmed") {
                    if ($status == "Confirmed") {
                        $this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status);
                    }
                }
                $this->api_model->updateAppointment($id, $data);
            }
            $data['message'] = "invalid";
            echo json_encode($data);
        }
    }

    /* For Flutter APP */

    public function addAppointment()
    {
        $id = $this->input->post('id');
        $patient = $this->input->post('patient');
        $doctor = $this->input->post('doctor');
        $date = $this->input->post('date');
        $time_slot = $this->input->post('time_slot');
        $status = $this->input->post('status');
        $remarks = $this->input->post('remarks');
        $user_type = $this->input->post('user_type');
        $time_slot_explode = explode('To', $time_slot);

        $s_time = trim($time_slot_explode[0]);
        $e_time = trim($time_slot_explode[1]);
        $s_time_key = $this->getArrayKey($s_time);
        if ($user_type == 'patient') {
            $ion_user_id = $this->findPatientIonId($patient);
        } else {
            $ion_user_id = $this->findDoctorIonId($doctor);
        }

        // $user = $this->findPatientIonId($patient);

        $this->hospitalID = $this->getHospitalID($ion_user_id);

        if (!empty($date)) {
            $date = strtotime($date);
        }


        $time_slot = $time_slot;

        $time_slot_explode = explode('To', $time_slot);

        $s_time = trim($time_slot_explode[0]);
        $e_time = trim($time_slot_explode[1]);

        $patient_phone = $this->api_model->getPatientById($patient, $this->hospitalID)->phone;
        $add_date = date('m/d/y');
        $registration_time = time();
        $temp_phone = str_replace('+', '', $patient_phone);
        $temp_phone = str_replace('.', '', $temp_phone);
        $room_id = 'hms-meeting-' . $temp_phone . '-' . rand(10000, 1000000) . '-' . $this->hospital_id;
        $live_meeting_link = 'https://meet.jit.si/' . $room_id;

        $patient_details = $this->api_model->getPatientById($patient, $this->hospitalID);
        $patientname = $this->api_model->getPatientById($patient, $this->hospitalID)->name;
        $doctorname = $this->api_model->getDoctorById($doctor, $this->hospitalID)->name;
        $data = array();
        $data = array(
            'patient' => $patient,
            'patientname' => $patientname,
            'doctor' => $doctor,
            'doctorname' => $doctorname,
            'date' => $date,
            's_time' => $s_time,
            'e_time' => $e_time,
            'time_slot' => $time_slot,
            'add_date' => $add_date,
            'remarks' => $remarks,
            'registration_time' => $registration_time,
            'status' => $status,
            'payment_status' => 'unpaid',
            's_time_key' => $s_time_key,
            'user' => $user,
            'room_id' => $room_id,
            'live_meeting_link' => $live_meeting_link
        );
        $data_appointment = array(
            'category_name' => 'Consultant Fee',
            'patient' => $patient,
            'amount' => '0',
            'doctor' => $doctor,
            'discount' => '0',
            'flat_discount' => '0',
            'gross_total' => '0',
            'status' => 'unpaid',
            'hospital_amount' => '0',
            'doctor_amount' => '0',
            'user' => $user,
            'patient_name' => $patient_details->name,
            'patient_phone' => $patient_details->phone,
            'patient_address' => $patient_details->address,
            'doctor_name' => $doctorname,
            'remarks' => $remarks,
            'payment_from' => 'appointment'
        );
        $username = $name;

        if (empty($id)) {
            // Adding New department
            $this->api_model->insertAppointment($data, $this->hospitalID);
            $appointment_id = $this->db->insert_id('appointment');
            $data_appointment['appointment_id'] = $appointment_id;
            $data_appointment['date'] = time();
            $data_appointment['date_string'] = date('d-m-Y');
            $this->api_model->insertPayment($data_appointment, $this->hospitalID);
            $inserted_id = $this->db->insert_id('payment');
            $data_update_payment_id_in_appointment = array('payment_id' => $inserted_id);
            $this->api_model->updateAppointment($appointment_id, $data_update_payment_id_in_appointment, $this->hospitalID);

            $patient_doctor = $this->api_model->getPatientById($patient, $this->hospitalID)->doctor;

            $patient_doctors = explode(',', $patient_doctor);

            if (!in_array($doctor, $patient_doctors)) {
                $patient_doctors[] = $doctor;
                $doctorss = implode(',', $patient_doctors);
                $data_d = array();
                $data_d = array('doctor' => $doctorss);
                $this->api_model->updatePatient($patient, $data_d);
            }
            //$this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status);
            echo $ion_user_id;
            die();
        } else { // Updating department
            $previous_status = $this->api_model->getAppointmentById($id, $this->hospitalID)->status;
            if ($previous_status != "Confirmed") {
                if ($status == "Confirmed") {
                    $this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status);
                }
            }
            $this->api_model->updateAppointment($id, $data);
        }
        $data['message'] = "success";
        echo json_encode($data);
    }
    function getArrayKey($s_time)
    {
        $all_slot = array(
            '0' => '12:00 AM',
            '1' => '12:05 AM',
            '2' => '12:10 AM',
            '3' => '12:15 AM',
            '4' => '12:20 AM',
            '5' => '12:25 AM',
            '6' => '12:30 AM',
            '7' => '12:35 AM',
            '8' => '12:40 PM',
            '9' => '12:45 AM',
            '10' => '12:50 AM',
            '11' => '12:55 AM',
            '12' => '01:00 AM',
            '13' => '01:05 AM',
            '14' => '01:10 AM',
            '15' => '01:15 AM',
            '16' => '01:20 AM',
            '17' => '01:25 AM',
            '18' => '01:30 AM',
            '19' => '01:35 AM',
            '20' => '01:40 AM',
            '21' => '01:45 AM',
            '22' => '01:50 AM',
            '23' => '01:55 AM',
            '24' => '02:00 AM',
            '25' => '02:05 AM',
            '26' => '02:10 AM',
            '27' => '02:15 AM',
            '28' => '02:20 AM',
            '29' => '02:25 AM',
            '30' => '02:30 AM',
            '31' => '02:35 AM',
            '32' => '02:40 AM',
            '33' => '02:45 AM',
            '34' => '02:50 AM',
            '35' => '02:55 AM',
            '36' => '03:00 AM',
            '37' => '03:05 AM',
            '38' => '03:10 AM',
            '39' => '03:15 AM',
            '40' => '03:20 AM',
            '41' => '03:25 AM',
            '42' => '03:30 AM',
            '43' => '03:35 AM',
            '44' => '03:40 AM',
            '45' => '03:45 AM',
            '46' => '03:50 AM',
            '47' => '03:55 AM',
            '48' => '04:00 AM',
            '49' => '04:05 AM',
            '50' => '04:10 AM',
            '51' => '04:15 AM',
            '52' => '04:20 AM',
            '53' => '04:25 AM',
            '54' => '04:30 AM',
            '55' => '04:35 AM',
            '56' => '04:40 AM',
            '57' => '04:45 AM',
            '58' => '04:50 AM',
            '59' => '04:55 AM',
            '60' => '05:00 AM',
            '61' => '05:05 AM',
            '62' => '05:10 AM',
            '63' => '05:15 AM',
            '64' => '05:20 AM',
            '65' => '05:25 AM',
            '66' => '05:30 AM',
            '67' => '05:35 AM',
            '68' => '05:40 AM',
            '69' => '05:45 AM',
            '70' => '05:50 AM',
            '71' => '05:55 AM',
            '72' => '06:00 AM',
            '73' => '06:05 AM',
            '74' => '06:10 AM',
            '75' => '06:15 AM',
            '76' => '06:20 AM',
            '77' => '06:25 AM',
            '78' => '06:30 AM',
            '79' => '06:35 AM',
            '80' => '06:40 AM',
            '81' => '06:45 AM',
            '82' => '06:50 AM',
            '83' => '06:55 AM',
            '84' => '07:00 AM',
            '85' => '07:05 AM',
            '86' => '07:10 AM',
            '87' => '07:15 AM',
            '88' => '07:20 AM',
            '89' => '07:25 AM',
            '90' => '07:30 AM',
            '91' => '07:35 AM',
            '92' => '07:40 AM',
            '93' => '07:45 AM',
            '94' => '07:50 AM',
            '95' => '07:55 AM',
            '96' => '08:00 AM',
            '97' => '08:05 AM',
            '98' => '08:10 AM',
            '99' => '08:15 AM',
            '100' => '08:20 AM',
            '101' => '08:25 AM',
            '102' => '08:30 AM',
            '103' => '08:35 AM',
            '104' => '08:40 AM',
            '105' => '08:45 AM',
            '106' => '08:50 AM',
            '107' => '08:55 AM',
            '108' => '09:00 AM',
            '109' => '09:05 AM',
            '110' => '09:10 AM',
            '111' => '09:15 AM',
            '112' => '09:20 AM',
            '113' => '09:25 AM',
            '114' => '09:30 AM',
            '115' => '09:35 AM',
            '116' => '09:40 AM',
            '117' => '09:45 AM',
            '118' => '09:50 AM',
            '119' => '09:55 AM',
            '120' => '10:00 AM',
            '121' => '10:05 AM',
            '122' => '10:10 AM',
            '123' => '10:15 AM',
            '124' => '10:20 AM',
            '125' => '10:25 AM',
            '126' => '10:30 AM',
            '127' => '10:35 AM',
            '128' => '10:40 AM',
            '129' => '10:45 AM',
            '130' => '10:50 AM',
            '131' => '10:55 AM',
            '132' => '11:00 AM',
            '133' => '11:05 AM',
            '134' => '11:10 AM',
            '135' => '11:15 AM',
            '136' => '11:20 AM',
            '137' => '11:25 AM',
            '138' => '11:30 AM',
            '139' => '11:35 AM',
            '140' => '11:40 AM',
            '141' => '11:45 AM',
            '142' => '11:50 AM',
            '143' => '11:55 AM',
            '144' => '12:00 PM',
            '145' => '12:05 PM',
            '146' => '12:10 PM',
            '147' => '12:15 PM',
            '148' => '12:20 PM',
            '149' => '12:25 PM',
            '150' => '12:30 PM',
            '151' => '12:35 PM',
            '152' => '12:40 PM',
            '153' => '12:45 PM',
            '154' => '12:50 PM',
            '155' => '12:55 PM',
            '156' => '01:00 PM',
            '157' => '01:05 PM',
            '158' => '01:10 PM',
            '159' => '01:15 PM',
            '160' => '01:20 PM',
            '161' => '01:25 PM',
            '162' => '01:30 PM',
            '163' => '01:35 PM',
            '164' => '01:40 PM',
            '165' => '01:45 PM',
            '166' => '01:50 PM',
            '167' => '01:55 PM',
            '168' => '02:00 PM',
            '169' => '02:05 PM',
            '170' => '02:10 PM',
            '171' => '02:15 PM',
            '172' => '02:20 PM',
            '173' => '02:25 PM',
            '174' => '02:30 PM',
            '175' => '02:35 PM',
            '176' => '02:40 PM',
            '177' => '02:45 PM',
            '178' => '02:50 PM',
            '179' => '02:55 PM',
            '180' => '03:00 PM',
            '181' => '03:05 PM',
            '182' => '03:10 PM',
            '183' => '03:15 PM',
            '184' => '03:20 PM',
            '185' => '03:25 PM',
            '186' => '03:30 PM',
            '187' => '03:35 PM',
            '188' => '03:40 PM',
            '189' => '03:45 PM',
            '190' => '03:50 PM',
            '191' => '03:55 PM',
            '192' => '04:00 PM',
            '193' => '04:05 PM',
            '194' => '04:10 PM',
            '195' => '04:15 PM',
            '196' => '04:20 PM',
            '197' => '04:25 PM',
            '198' => '04:30 PM',
            '199' => '04:35 PM',
            '200' => '04:40 PM',
            '201' => '04:45 PM',
            '202' => '04:50 PM',
            '203' => '04:55 PM',
            '204' => '05:00 PM',
            '205' => '05:05 PM',
            '206' => '05:10 PM',
            '207' => '05:15 PM',
            '208' => '05:20 PM',
            '209' => '05:25 PM',
            '210' => '05:30 PM',
            '211' => '05:35 PM',
            '212' => '05:40 PM',
            '213' => '05:45 PM',
            '214' => '05:50 PM',
            '215' => '05:55 PM',
            '216' => '06:00 PM',
            '217' => '06:05 PM',
            '218' => '06:10 PM',
            '219' => '06:15 PM',
            '220' => '06:20 PM',
            '221' => '06:25 PM',
            '222' => '06:30 PM',
            '223' => '06:35 PM',
            '224' => '06:40 PM',
            '225' => '06:45 PM',
            '226' => '06:50 PM',
            '227' => '06:55 PM',
            '228' => '07:00 PM',
            '229' => '07:05 PM',
            '230' => '07:10 PM',
            '231' => '07:15 PM',
            '232' => '07:20 PM',
            '233' => '07:25 PM',
            '234' => '07:30 PM',
            '235' => '07:35 PM',
            '236' => '07:40 PM',
            '237' => '07:45 PM',
            '238' => '07:50 PM',
            '239' => '07:55 PM',
            '240' => '08:00 PM',
            '241' => '08:05 PM',
            '242' => '08:10 PM',
            '243' => '08:15 PM',
            '244' => '08:20 PM',
            '245' => '08:25 PM',
            '246' => '08:30 PM',
            '247' => '08:35 PM',
            '248' => '08:40 PM',
            '249' => '08:45 PM',
            '250' => '08:50 PM',
            '251' => '08:55 PM',
            '252' => '09:00 PM',
            '253' => '09:05 PM',
            '254' => '09:10 PM',
            '255' => '09:15 PM',
            '256' => '09:20 PM',
            '257' => '09:25 PM',
            '258' => '09:30 PM',
            '259' => '09:35 PM',
            '260' => '09:40 PM',
            '261' => '09:45 PM',
            '262' => '09:50 PM',
            '263' => '09:55 PM',
            '264' => '10:00 PM',
            '265' => '10:05 PM',
            '266' => '10:10 PM',
            '267' => '10:15 PM',
            '268' => '10:20 PM',
            '269' => '10:25 PM',
            '270' => '10:30 PM',
            '271' => '10:35 PM',
            '272' => '10:40 PM',
            '273' => '10:45 PM',
            '274' => '10:50 PM',
            '275' => '10:55 PM',
            '276' => '11:00 PM',
            '277' => '11:05 PM',
            '278' => '11:10 PM',
            '279' => '11:15 PM',
            '280' => '11:20 PM',
            '281' => '11:25 PM',
            '282' => '11:30 PM',
            '283' => '11:35 PM',
            '284' => '11:40 PM',
            '285' => '11:45 PM',
            '286' => '11:50 PM',
            '287' => '11:55 PM',
        );

        $key = array_search($s_time, $all_slot);
        return $key;
    }


    //     public function updateAppointment() {
    //                 $id = $this->input->post('id'); 
    //                 $patient = $this->input->post('patient');
    //                 $doctor = $this->input->post('doctor');
    //                 $date=$this->input->post('date');
    //                 $time_slot=$this->input->post('time_slot');
    //                 $status=$this->input->post('status');
    //                 $remarks=$this->input->post('remarks');
    //                 $user_type = $this->input->post('user_type');
    //                 if($user_type == 'patient') {
    //                     $ion_user_id= $this->findPatientIonId($patient);
    //                 } else {
    //                     $ion_user_id = $this->findDoctorIonId($doctor);
    //                 }
    //         $this->hospitalID = $this->getHospitalID($ion_user_id);
    //         if (!empty($date)) {
    //             $date = strtotime($date);
    //         }
    //         $time_slot = $time_slot;
    //         $time_slot_explode = explode('To', $time_slot);
    //         $s_time = trim($time_slot_explode[0]);
    //         $e_time = trim($time_slot_explode[1]);
    //             $patient_phone = $this->api_model->getPatientById($patient, $this->hospitalID)->phone;
    //             $add_date = date('m/d/y');
    //             $registration_time = time();
    // $temp_phone = str_replace('+','',$patient_phone);
    // $temp_phone = str_replace('.','',$temp_phone);
    //                 $room_id = 'hms-meeting-' . $temp_phone . '-' . rand(10000, 1000000) . '-' . $this->hospital_id;
    //                 $live_meeting_link = 'https://meet.jit.si/' . $room_id;
    //             //$error = array('error' => $this->upload->display_errors());
    //             $patientname = $this->api_model->getPatientById($patient, $this->hospitalID)->name;
    //             $doctorname = $this->api_model->getDoctorById($doctor, $this->hospitalID)->name;
    //             $data = array();
    //             $data = array(
    //                 'patient' => $patient,
    //                 'patientname' => $patientname,
    //                 'doctor' => $doctor,
    //                 'doctorname' => $doctorname,
    //                 'date' => $date,
    //                 's_time' => $s_time,
    //                 'e_time' => $e_time,
    //                 'time_slot' => $time_slot,
    //                 'add_date' => $add_date,
    //                 'remarks' => $remarks,
    //                 'registration_time' => $registration_time,
    //                 'status' => $status,
    //                 's_time_key' => $s_time_key,
    //                 'user' => $user,
    //                 'room_id' => $room_id,
    //                 'live_meeting_link' => $live_meeting_link
    //             );
    //             $username = $name;
    //             if ($id) {  
    //                 $previous_status = $this->api_model->getAppointmentById($id, $this->hospitalID)->status;
    //                 if ($previous_status != "Confirmed") {
    //                     if ($status == "Confirmed") {
    //                         $this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status, $this->hospitalID);
    //                     }
    //                 }
    //                 $this->api_model->updateAppointment($id, $data);
    //                 /* if (!empty($sms)) {
    //                   $this->sms->sendSmsDuringAppointment($patient, $doctor, $date, $s_time, $e_time);
    //                   } */
    //                 $patient_doctor = $this->api_model->getPatientById($patient, $this->hospitalID)->doctor;
    //                 $patient_doctors = explode(',', $patient_doctor);
    //                 if (!in_array($doctor, $patient_doctors)) {
    //                     $patient_doctors[] = $doctor;
    //                     $doctorss = implode(',', $patient_doctors);
    //                     $data_d = array();
    //                     $data_d = array('doctor' => $doctorss);
    //                     $this->api_model->updatePatient($patient, $data_d);
    //                 }
    //                 //$this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status);
    //  //                                                               echo $ion_user_id;
    // //die();
    //             }
    //             $data['message'] = "success";
    //             echo json_encode($data);
    //     }

    public function updateAppointment()
    {

        $id = $this->input->post('id');
        $patient = $this->input->post('patient');
        $doctor = $this->input->post('doctor');
        $date = $this->input->post('date');
        $time_slot = $this->input->post('time_slot');
        $status = $this->input->post('status');
        $remarks = $this->input->post('remarks');
        $user_type = $this->input->post('user_type');

        if ($user_type == 'patient') {
            $ion_user_id = $this->findPatientIonId($patient);
        } else {
            $ion_user_id = $this->findDoctorIonId($doctor);
        }



        $this->hospitalID = $this->getHospitalID($ion_user_id);

        if (!empty($date)) {
            $date = strtotime($date);
        }


        $time_slot = $time_slot;

        $time_slot_explode = explode('To', $time_slot);

        $s_time = trim($time_slot_explode[0]);
        $e_time = trim($time_slot_explode[1]);

        $patient_phone = $this->api_model->getPatientById($patient, $this->hospitalID)->phone;


        $temp_phone = str_replace('+', '', $patient_phone);
        $temp_phone = str_replace('.', '', $temp_phone);
        $room_id = 'hms-meeting-' . $temp_phone . '-' . rand(10000, 1000000) . '-' . $this->hospital_id;
        $live_meeting_link = 'https://meet.jit.si/' . $room_id;

        //$error = array('error' => $this->upload->display_errors());
        $patientname = $this->api_model->getPatientById($patient, $this->hospitalID)->name;
        $doctorname = $this->api_model->getDoctorById($doctor, $this->hospitalID)->name;
        $data = array();
        $data = array(
            'patient' => $patient,
            'patientname' => $patientname,
            'doctor' => $doctor,
            'doctorname' => $doctorname,
            'date' => $date,
            's_time' => $s_time,
            'e_time' => $e_time,
            'time_slot' => $time_slot,
            'remarks' => $remarks,
            'status' => $status,
            's_time_key' => $s_time_key,
            'user' => $user,
            'room_id' => $room_id,
            'live_meeting_link' => $live_meeting_link
        );
        $username = $name;

        if ($id) {
            $previous_status = $this->api_model->getAppointmentById($id, $this->hospitalID)->status;
            if ($previous_status != "Confirmed") {
                if ($status == "Confirmed") {
                    $this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status, $this->hospitalID);
                }
            }
            $this->api_model->updateAppointment($id, $data);

            /* if (!empty($sms)) {
              $this->sms->sendSmsDuringAppointment($patient, $doctor, $date, $s_time, $e_time);
              } */

            $patient_doctor = $this->api_model->getPatientById($patient, $this->hospitalID)->doctor;

            $patient_doctors = explode(',', $patient_doctor);

            if (!in_array($doctor, $patient_doctors)) {
                $patient_doctors[] = $doctor;
                $doctorss = implode(',', $patient_doctors);
                $data_d = array();
                $data_d = array('doctor' => $doctorss);
                $this->api_model->updatePatient($patient, $data_d);
            }
            //$this->sendSmsDuringAppointment($id, $data, $patient, $doctor, $status);
            //                                                               echo $ion_user_id;
            //die();
        }

        $data['message'] = "success";
        echo json_encode($data);
    }

    public function getAppointmentById()
    {
        $id = $this->input->get('id');
        $appointment = $this->api_model->getAppointmentByIdOnly($id);
        $data = [
            "id" => $appointment->id,
            "patient_name" => $appointment->patientname,
            "doctor_name" => $appointment->doctorname,
            "patient" => $appointment->patient,
            "doctor" => $appointment->doctor,
            "date" => date('d-m-Y', $appointment->date),
            "remarks" => $appointment->remarks,
            "status" => $appointment->status,
            'jitsi_link' => $appointment->room_id,
            'start_time' => $appointment->s_time,
            'end_time' => $appointment->e_time
        ];
        echo json_encode($data);
    }

    function sendSmsDuringAppointment($id, $data, $patient, $doctor, $status, $hospitalID)
    {
        //sms
        $this->hospitalID = $hospitalID;
        $set['settings'] = $this->api_model->getSettings($this->hospitalID);
        $patientdetails = $this->api_model->getPatientById($patient, $this->hospitalID);
        $doctordetails = $this->api_model->getDoctorById($doctor, $this->hospitalID);
        if (empty($id)) {
            // if ($status != 'Confirmed') {
            //     $autosms = $this->api_model->getAutoSmsByType('appoinment_creation', $this->hospitalID);
            //     $autoemail = $this->api_model->getAutoEmailByType('appoinment_creation', $this->hospitalID);
            // } else {
            //     $autosms = $this->api_model->getAutoSmsByType('appoinment_confirmation', $this->hospitalID);
            //     $autoemail = $this->api_model->getAutoEmailByType('appoinment_confirmation', $this->hospitalID);
            // }
        } else {

            // $autosms = $this->api_model->getAutoSmsByType('appoinment_confirmation', $this->hospitalID);
            // $autoemail = $this->api_model->getAutoEmailByType('appoinment_confirmation', $this->hospitalID);
        }
        $message = $autosms->message;
        $to = $patientdetails->phone;
        $name1 = explode(' ', $patientdetails->name);
        if (!isset($name1[1])) {
            $name1[1] = null;
        }
        $data1 = array(
            'firstname' => $name1[0],
            'lastname' => $name1[1],
            'name' => $patientdetails->name,
            'doctorname' => $doctordetails->name,
            'appoinmentdate' => date('d-m-Y', $data['date']),
            'time_slot' => $data['time_slot'],
            'hospital_name' => $set['settings']->system_vendor
        );

        if ($autosms->status == 'Active') {
            $messageprint = $this->parser->parse_string($message, $data1);

            $data2[] = array($to => $messageprint);
            $this->sendSms($to, $message, $data2);
        }
        //end
        //email
        // $autoemail = $this->email_model->getAutoEmailByType('payment');
        if ($autoemail->status == 'Active') {
            $emailSettings = $this->api_model->getEmailSettings($this->hospitalID);
            $message1 = $autoemail->message;
            $messageprint1 = $this->parser->parse_string($message1, $data1);
            $this->email->from($emailSettings->admin_email);
            $this->email->to($patientdetails->email);
            $this->email->subject(lang('appointment'));
            $this->email->message($messageprint1);
            if (function_exists('mail')) {
                $this->email->send();
            }
        }

        //end
    }

//Add Patient Material Function
    function addPatientMaterial()
    {
    
        $title = $this->input->post('title');
        $patient_id = $this->input->post('patient');
        $img_url = $this->input->post('img_url');
        $group = $this->input->post('group');
        $date = time();

        $userID = $this->findPatientIonId($patient_id);
        $this->hospitalID = $this->getHospitalID($userID);

        //$redirect = $this->input->post('redirect');

        /* if ($this->ion_auth->in_group(array('Patient'))) {
          if (empty($patient_id)) {
          $current_patient = $this->ion_auth->get_user_id();
          $patient_id = $this->api_model->getPatientByIonUserId($current_patient, $this->hospitalID)->id;
          }
          } */
        if ($group == 'Patient') {
            if (empty($patient_id)) {
                $current_patient = $userID;
                $patient_id = $this->api_model->getPatientByIonUserId($current_patient, $this->hospitalID)->id;
            }
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // Validating Patient Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|min_length[1]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $data2['message'] = 'invalid';
            echo json_encode($data2);
        } else {

            if (!empty($patient_id)) {
                $patient_details = $this->api_model->getPatientById($patient_id, $this->hospitalID);
                $patient_name = $patient_details->name;
                $patient_phone = $patient_details->phone;
                $patient_address = $patient_details->address;
            } else {
                $patient_name = 0;
                $patient_phone = 0;
                $patient_address = 0;
            }


            $file_name = $_FILES['img_url']['name'];
            $file_name_pieces = explode('_', $file_name);
            $new_file_name = '';
            $count = 1;
            foreach ($file_name_pieces as $piece) {
                if ($count !== 1) {
                    $piece = ucfirst($piece);
                }

                $new_file_name .= $piece;
                $count++;
            }
            $config = array(
                'file_name' => $new_file_name,
                'upload_path' => "./uploads/",
                'allowed_types' => "gif|jpg|png|jpeg|pdf",
                'overwrite' => False,
                'max_size' => "48000000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                'max_height' => "10000",
                'max_width' => "10000"
            );

            $this->load->library('Upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('img_url')) {
                $path = $this->upload->data();
                $img_url = "uploads/" . $path['file_name'];
                $data = array();
                $data = array(
                    'date' => $date,
                    'title' => $title,
                    'url' => $img_url,
                    'patient' => $patient_id,
                    'patient_name' => $patient_name,
                    'patient_address' => $patient_address,
                    'patient_phone' => $patient_phone,
                    'date_string' => date('d-m-y', $date),
                );
            } else {
                $data = array();
                $data = array(
                    'date' => $date,
                    'title' => $title,
                    'patient' => $patient_id,
                    'patient_name' => $patient_name,
                    'patient_address' => $patient_address,
                    'patient_phone' => $patient_phone,
                    'date_string' => date('d-m-y', $date),
                );
                //$this->session->set_flashdata('feedback', lang('upload_error'));
                $data['message'] = "error";
            }

            $this->api_model->insertPatientMaterial($data, $this->hospitalID);
            //$this->session->set_flashdata('feedback', lang('added'));

            $data2['message'] = 'successful';
            echo json_encode($data2);
        }
    }

    function patientAllInvoices()
    {
        $id = $this->input->get('id');
        //$patient_ion_id = $this->input->get('id');
        $patient_ion_id = $this->findPatientIonId($id);
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        $data = $this->api_model->getPaymentByPatientId($patient, $this->hospitalID);
        echo json_encode($data);
    }

    function paymentGateway()
    {
        $id = $this->input->get('id');
        //$patient_ion_id = $this->input->get('id');
        $patient_ion_id = $this->findPatientIonId($id);
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        $settings = $this->api_model->getSettings($this->hospitalID);
        $data = $this->api_model->getGatewayByName($settings->payment_gateway, $this->hospitalID);
        echo json_encode($data);
    }

    //Patient payment history
    function myPaymentHistory()
    {
        $id = $this->input->get('id');
        //$patient_ion_id = $this->input->get('id');
        $patient_ion_id = $this->findPatientIonId($id);
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        //  $data['settings'] = $this->api_model->getSettings($this->hospitalID);
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        if (!empty($date_from)) {
            $data['payments'] = $this->api_model->getPaymentByPatientIdByDate($patient, $date_from, $date_to, $this->hospitalID);
            $data['deposits'] = $this->api_model->getDepositByPatientIdByDate($patient, $date_from, $date_to, $this->hospitalID);
            $data['gateway'] = $this->api_model->getGatewayByName($data['settings']->payment_gateway, $this->hospitalID);
        } else {
            $data['payments'] = $this->api_model->getPaymentByPatientId($patient, $this->hospitalID);
            //   $data['pharmacy_payments'] = $this->api_model->getPharmacyPaymentByPatientId($patient, $this->hospitalID);
            //   $data['ot_payments'] = $this->api_model->getOtPaymentByPatientId($patient, $this->hospitalID);
            //   $data['deposits'] = $this->api_model->getDepositByPatientId($patient, $this->hospitalID);
            //   $data['gateway'] = $this->api_model->getGatewayByName($data['settings']->payment_gateway, $this->hospitalID);
        }



        //  $data['patient'] = $this->api_model->getPatientByid($patient, $this->hospitalID);
        //   $data['settings'] = $this->api_model->getSettings($this->hospitalID);


        $data['message'] = 'successful';
        echo json_encode($data);
    }

    //Patient Deposit Function
    function deposit()
    {



        //$id = $this->input->post('id');
        $patient_id = $this->input->post('patient_id');

        $group = $this->input->post('group');

        $userID = $this->findPatientIonId($patient_id);

        $this->hospitalID = $this->getHospitalID($userID);

        if ($group == 'Patient' || $group == 'patient') {
            $patient_ion_id = $userID;
            $patient = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        } else {
            $data['message'] = 'undefined_patient_id';
            echo json_encode($data);
        }




        $payment_id = $this->input->post('payment_id');
        $date = time();

        $deposited_amount = $this->input->post('deposited_amount');

        $deposit_type = $this->input->post('deposit_type');

        if ($deposit_type != 'Card') {
            $data['message'] = 'undefined_payment_type';
            echo json_encode($data);
        }

        $user = $userID;

        $data = array();
        $data = array(
            'patient' => $patient,
            'date' => $date,
            'payment_id' => $payment_id,
            'deposited_amount' => $deposited_amount,
            'deposit_type' => $deposit_type,
            'user' => $user
        );

        if (empty($id)) {
            if ($deposit_type == 'Card') {
                $payment_details = $this->api_model->getPaymentById($payment_id, $this->hospitalID);
                $gateway = $this->api_model->getSettings($this->hospitalID)->payment_gateway;
                if ($gateway == 'PayPal') {
                    $card_type = $this->input->post('card_type');
                    $card_number = $this->input->post('card_number');
                    $expire_date = $this->input->post('expire_date');
                    $cvv = $this->input->post('cvv_number');

                    $all_details = array(
                        'patient' => $payment_details->patient,
                        'date' => $payment_details->date,
                        'amount' => $payment_details->amount,
                        'doctor' => $payment_details->doctor_name,
                        'discount' => $payment_details->discount,
                        'flat_discount' => $payment_details->flat_discount,
                        'gross_total' => $payment_details->gross_total,
                        'status' => 'unpaid',
                        'patient_name' => $payment_details->patient_name,
                        'patient_phone' => $payment_details->patient_phone,
                        'patient_address' => $payment_details->patient_address,
                        'deposited_amount' => $deposited_amount,
                        'payment_id' => $payment_details->id,
                        'card_type' => $card_type,
                        'card_number' => $card_number,
                        'expire_date' => $expire_date,
                        'cvv' => $cvv,
                        'from' => 'patient_payment_details',
                        'user' => $user,
                        'cardholdername' => $this->input->post('cardholder')
                    );

                    $this->paymentPaypal($all_details);
                } elseif ($gateway == 'Paystack') {
                    $ref = date('Y') . '-' . rand() . date('d') . '-' . date('m');
                    $amount_in_kobo = $deposited_amount;
                    $this->load->module('paystack');
                    $this->paystack->paystack_standard($amount_in_kobo, $ref, $patient, $payment_id, $user, '2');
                } elseif ($gateway == 'Stripe') {

                    $stripe = $this->db->get_where('paymentgateway', array('name =' => 'Stripe'))->row();

                    $card_number = $this->input->post('card_number');
                    $expire_date = $this->input->post('expire_date');
                    $cvv = $this->input->post('cvv_number');

                    $exp = explode('/', $expire_date);

                    $stripe2 = new \Stripe\StripeClient($stripe->secret);
                    $response = $stripe2->tokens->create([
                        'card' => [
                            'number' => $card_number,
                            'exp_month' => trim($exp[0], " "),
                            'exp_year' => trim($exp[1], " "),
                            'cvc' => $cvv,
                        ],
                    ]);
                    //$token = $this->input->post('token');

                    $stripe3 = new \Stripe\StripeClient($stripe->secret);
                    $charge = $stripe3->charges->create([
                        'amount' => $deposited_amount * 100,
                        'currency' => 'usd',
                        'source' => $response['id'],
                    ]);
                    // \Stripe\Stripe::setApiKey($stripe->secret);
                    // $charge = \Stripe\Charge::create(array(
                    //             "amount" => $deposited_amount * 100,
                    //             "currency" => "usd",
                    //             "source" => $response['id']
                    // ));
                    $chargeJson = $charge->jsonSerialize();
                    if ($chargeJson['status'] == 'succeeded') {
                        $data1 = array(
                            'date' => $date,
                            'patient' => $patient,
                            'payment_id' => $payment_id,
                            'deposited_amount' => $deposited_amount,
                            'gateway' => 'Stripe',
                            'user' => $user,
                            'hospital_id' => $this->hospitalID
                        );
                        $this->api_model->insertDeposit($data1, $this->hospitalID);
                        $message = 'successful';
                    } else {
                        $message = 'failed';
                    }
                    //  redirect("finance/invoice?id=" . "$inserted_id");
                    echo json_encode($message);
                } elseif ($gateway == 'Pay U Money') {
                    redirect("payu/check?deposited_amount=" . "$deposited_amount" . '&payment_id=' . $payment_id);
                } else {
                    $message = 'payment_failed_no_gateway_selected';
                    echo json_encode($message);
                }
            } else {
                $this->api_model->insertDeposit($data, $this->hospitalID);
                $message = 'successful';
                echo json_encode($message);
            }
        } else {
            $this->api_model->updateDeposit($id, $data);

            $amount_received_id = $this->api_model->getDepositById($id, $this->hospitalID)->amount_received_id;
            if (!empty($amount_received_id)) {
                $amount_received_payment_id = explode('.', $amount_received_id);
                $payment_id = $amount_received_payment_id[0];
                $data_amount_received = array('amount_received' => $deposited_amount);
                $this->api_model->updatePayment($amount_received_payment_id[0], $data_amount_received, $this->hospitalID);
            }

            $data['message'] = 'updated';
            echo json_encode($data);
        }
    }

    //Patient payment history functoin
    function patientPaymentHistory()
    {
        $patient = $this->input->get('patient');
        //$this->hospitalID = $this->input->post('hospital_id');
        if (empty($patient)) {
            $patient = $this->input->post('patient');
        }

        $userIonId = $this->findPatientIonId($patient);
        $this->hospitalID = $this->getHospitalID($userIonId);

        $patient_hospital_id = $this->api_model->getPatientById($patient, $this->hospitalID)->hospital_id;

        $data['settings'] = $this->api_model->getSettings($this->hospitalID);
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        if (!empty($date_from)) {
            $data['payments'] = $this->api_model->getPaymentByPatientIdByDate($patient, $date_from, $date_to, $this->hospitalID);
            $data['deposits'] = $this->api_model->getDepositByPatientIdByDate($patient, $date_from, $date_to, $this->hospitalID);
            $data['gateway'] = $this->api_model->getGatewayByName($data['settings']->payment_gateway, $this->hospitalID);
        } else {
            $data['payments'] = $this->api_model->getPaymentByPatientId($patient, $this->hospitalID);
            $data['pharmacy_payments'] = $this->api_model->getPharmacyPaymentByPatientId($patient, $this->hospitalID);
            $data['ot_payments'] = $this->api_model->getOtPaymentByPatientId($patient);
            $data['deposits'] = $this->api_model->getDepositByPatientId($patient);
            $data['gateway'] = $this->api_model->getGatewayByName($data['settings']->payment_gateway, $this->hospitalID);
        }



        $data['patient'] = $this->api_model->getPatientById($patient, $this->hospitalID);

        $data['message'] = 'successful';
        echo json_encode($date);
    }

    //Invoice Details of Payment
    function myInvoice()
    {
        $userId = $this->input->get('user_ion_id');
        $this->hospitalID = $this->getHospitalID($userId);
        $id = $this->input->get('id');
        $data['settings'] = $this->api_model->getSettings($this->hospitalID);
        $data['discount_type'] = $this->api_model->getDiscountType($this->hospitalID);
        $data['payment'] = $this->api_model->getPaymentById($id, $this->hospitalID);
        echo json_encode($data);
    }

    //Send sms function
    function sendSms($to, $message, $data)
    {
        $sms_gateway = $this->api_model->getSettings($this->hospitalID)->sms_gateway;
        if (!empty($sms_gateway)) {
            $smsSettings = $this->api_model->getSmsSettingsByGatewayName($sms_gateway, $this->hospitalID);
        } else {
            $data['message'] = 'Gateway_NOT_Selected';
            echo json_encode($data);
        }
        $j = sizeof($data);
        foreach ($data as $key => $value) {
            foreach ($value as $key2 => $value2) {

                if ($smsSettings->name == 'Clickatell') {
                    $username = $smsSettings->username;
                    $password = $smsSettings->password;
                    $api_id = $smsSettings->api_id;

                    file_get_contents("https://api.clickatell.com/http/sendmsg"
                        . "?user=$username&password=$password&api_id=$api_id&to=$key2&text=$value2");
                }

                if ($smsSettings->name == 'MSG91') {
                    $authkey = $smsSettings->authkey;
                    $sender = $smsSettings->sender;
                    $value2 = urlencode($value2);
                    //  file_get_contents('http://api.msg91.com/api/v2/sendsms?route=4&sender=' . $sender . '&mobiles=' . $key2 . '&authkey=' . $authkey . '&message=' . $value2 . '&country=0');           // file_get_contents('https://platform.clickatell.com/messages/http/send?apiKey='.$api_id.'&to='.$to.'&content='.$message1);           // file_get_contents('https://api.clickatell.com/http/sendmsg?user=' . $username . '&password=' . $password . '&api_id=' . $api_id . '&to=' . $to . '&text=' . $message1);
                    file_get_contents('http://world.msg91.com/api/v2/sendsms?authkey=' . $authkey . '&mobiles=' . $key2 . '&message=' . $value2 . '&sender=' . $sender . '&route=4&country=0');
                }
                if ($smsSettings->name == 'Twilio') {
                    $sid = $smsSettings->sid;
                    $token = $smsSettings->token;
                    $sendername = $smsSettings->sendernumber;
                    if (!empty($sid) && !empty($token) && !empty($sendername)) {
                        $client = new Client($sid, $token);
                        $client->messages->create(
                            $key2, // Text this number
                            array(
                                'from' => $sendername, // From a valid Twilio number
                                'body' => $value2
                            )
                        );
                    }


                    //file_get_contents('https://platform.clickatell.com/messages/http/send?apiKey=' . $api_id . '&to=' . $to . '&content=' . $message);           // file_get_contents('https://platform.clickatell.com/messages/http/send?apiKey='.$api_id.'&to='.$to.'&content='.$message1);           // file_get_contents('https://api.clickatell.com/http/sendmsg?user=' . $username . '&password=' . $password . '&api_id=' . $api_id . '&to=' . $to . '&text=' . $message1);
                }
            }
        }
    }

    //GET PATIENT INFO FUNCTION
    function getPatientInfo()
    {
        $id = $this->input->get('id');
        $patient_ion_id = $this->input->get('patient_ion_id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $data['patient'] = $this->api_model->getPatientById($id, $this->hospitalID);
        $data['message'] = 'successful';
        echo json_encode($data);
    }

    //Get Appointments todays for both patient and doctor
    function getMyTodaysAppoinmentList()
    {
        // $group = $this->input->get('group');
        // $id = $this->input->get('id');

        $group = $this->input->post('group');
        $id = $this->input->post('id');

        // $group = 'patient'; 
        // $id = 62;
        if ($group == 'doctor') {
            $ion_id = $this->findDoctorIonId($id);
        } else {
            $ion_id = $this->findPatientIonId($id);
        }
        $this->hospitalID = $this->getHospitalID($ion_id);
        if ($group == 'doctor') {
            //$doctor = $this->db->get_where('doctor', array('ion_user_id' => $id))->row()->id;
            $data1['appointments'] = $this->api_model->getAppointmentListByDoctor($id, $this->hospitalID);
        } else {
            $data1['appointments'] = $this->api_model->getAppointment($this->hospitalID);
        }

        $i = 0;

        $data = [];
        foreach ($data1['appointments'] as $appointment) {
            //$i = $i + 1;

            if ($group == 'Patient') {
                $patient_ion_id = $id;
                $patient_details = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID);
                $patient_id = $patient_details->id;
                if ($patient_id == $appointment->patient) {
                    $patientdetails = $this->api_model->getPatientById($appointment->patient, $this->hospitalID);
                    if (!empty($patientdetails)) {
                        $patientname = $patientdetails->name;
                    } else {
                        $patientname = $appointment->patientname;
                    }
                    $doctordetails = $this->api_model->getDoctorById($appointment->doctor, $this->hospitalID);
                    if (!empty($doctordetails)) {
                        $doctorname = $doctordetails->name;
                    } else {
                        $doctorname = $appointment->doctorname;
                    }


                    if ($appointment->date == strtotime(date('Y-m-d'))) {
                        array_push($data, array(
                            "id" => $appointment->id,
                            "patient_name" => $patientname,
                            "doctor_name" => $doctorname,
                            "date" => date('d-m-Y', $appointment->date),
                            "remarks" => $appointment->remarks,
                            "status" => $appointment->status,
                            'jitsi_link' => $appointment->room_id,
                            'start_time' => $appointment->s_time,
                            'end_time' => $appointment->e_time,
                        ));
                        $i = $i + 1;
                    } else {
                        $info1[] = array(
                            $appointment->id,
                            $appointment->patientname,
                            $appointment->doctorname,
                            date('d-m-Y', $appointment->date) . ' <br> ' . $appointment->s_time . '-' . $appointment->e_time,
                            $appointment->remarks,
                            $appointment->status,
                        );
                    }
                }
            } else {
                $patientdetails = $this->api_model->getPatientById($appointment->patient, $this->hospitalID);
                if (!empty($patientdetails)) {
                    //$patientname = ' <a type="button" class="" data-toggle = "modal" data-id="' . $appointment->patient . '"> ' . $patientdetails->name . '</a>';
                    $patientname = $patientdetails->name;
                } else {
                    //$patientname = ' <a type="button" class="" data-toggle = "modal" data-id="' . $appointment->patient . '"> ' . $appointment->patientname . '</a>';
                    $patientname = $appointment->patientname;
                }
                $doctordetails = $this->api_model->getDoctorById($appointment->doctor, $this->hospitalID);
                if (!empty($doctordetails)) {
                    $doctorname = $doctordetails->name;
                } else {
                    $doctorname = $appointment->doctorname;
                }

                if ($appointment->date == strtotime(date('Y-m-d'))) {
                    array_push($data, array(
                        "id" => $appointment->id,
                        "patient_name" => $patientname,
                        "doctor_name" => $doctorname,
                        "date" => date('d-m-Y', $appointment->date),
                        "remarks" => $appointment->remarks,
                        "status" => $appointment->status,
                        'jitsi_link' => $appointment->room_id,
                        'start_time' => $appointment->s_time,
                        'end_time' => $appointment->e_time,
                    ));
                    $i = $i + 1;
                } else {
                    $info1[] = array(
                        $appointment->id,
                        $appointment->patientname,
                        $appointment->doctorname,
                        date('d-m-Y', $appointment->date) . ' <br> ' . $appointment->s_time . '-' . $appointment->e_time,
                        $appointment->remarks,
                        $appointment->status,
                    );
                }
            }
        }

        //$data['message'] = 'Successful';

        echo json_encode($data);
    }

    //Get Appointments todays for both patient and doctor
    function getMyAllAppoinmentList()
    {

        $id = $this->input->post('id');
        $ion_id = $this->findDoctorIonId($id);
        $this->hospitalID = $this->getHospitalID($ion_id);
        

            $doctor = $this->db->get_where('doctor', array('ion_user_id' => $ion_id))->row()->id;
            $data1['appointments'] = $this->api_model->getAppointmentListByDoctor($doctor, $this->hospitalID);
        

        $i = 0;

        $data = [];
        foreach ($data1['appointments'] as $appointment) {
                $patientdetails = $this->api_model->getPatientById($appointment->patient, $this->hospitalID);
                if (!empty($patientdetails)) {
                    $patientname = $patientdetails->name;
                } else {
                    $patientname = $appointment->patientname;
                }
                $doctordetails = $this->api_model->getDoctorById($appointment->doctor, $this->hospitalID);
                if (!empty($doctordetails)) {
                    $doctorname = $doctordetails->name;
                } else {
                    $doctorname = $appointment->doctorname;
                }

                if (true) {
                    array_push($data, array(
                        "id" => $appointment->id,
                        "patient_name" => $patientname,
                        "doctor_name" => $doctorname,
                        "date" => date('d-m-Y', $appointment->date),
                        "remarks" => $appointment->remarks,
                        "status" => $appointment->status,
                        'jitsi_link' => $appointment->room_id,
                        'meeting_id' => $appointment->meeting_id,
                        'join_url' => $appointment->join_url,
                        'start_url' => $appointment->start_url,
                        'start_time' => $appointment->s_time,
                        'end_time' => $appointment->e_time,
                    ));
                    $i = $i + 1;
                } else {
                    $info1[] = array(
                        $appointment->id,
                        $appointment->patientname,
                        $appointment->doctorname,
                        date('d-m-Y', $appointment->date),
                        $appointment->remarks,
                        $appointment->status,
                    );
                }
            
        }

        echo json_encode($data);
    }

    //Get Hospital ID from user id
    function getHospitalID($id)
    {
        if (empty($id)) {
            return null;
        }
        $current_user_id = $id;
        $user_group = $this->db->get_where('users_groups', array('user_id' => $current_user_id))->row();
        if (empty($user_group)) {
            return null;
        }
        $group_row = $this->db->get_where('groups', array('id' => $user_group->group_id))->row();
        if (empty($group_row)) {
            return null;
        }
        $group_name = strtolower($group_row->name);

        if ($group_name === 'superadmin') {
            return 'superadmin';
        }

        if ($group_name === 'admin') {
            $hosp = $this->db->get_where('hospital', array('ion_user_id' => $current_user_id))->row();
            return !empty($hosp) ? $hosp->id : null;
        }

        if ($this->db->table_exists($group_name)) {
            $member = $this->db->get_where($group_name, array('ion_user_id' => $current_user_id))->row();
            return !empty($member) ? $member->hospital_id : null;
        }

        return null;
    }

    // public function addNewPrescription() {
    //     $id = $this->input->post('id');
    //     $tab = $this->input->post('tab');
    //     $date = $this->input->post('date');
    //     $patient_ion_id = 764;
    //     $this->hospitalID = $this->getHospitalID($patient_ion_id);
    //     if (!empty($date)) {
    //         $date = strtotime($date);
    //     }
    //     $patient = $this->input->post('patient');
    //     $doctor = $this->input->post('doctor');
    //     $note = $this->input->post('note');
    //     $symptom = $this->input->post('symptom');
    //     $medicine = $this->input->post('medicine');
    //     $dosage = $this->input->post('dosage');
    //     $frequency = $this->input->post('frequency');
    //     $days = $this->input->post('days');
    //     $instruction = $this->input->post('instruction');
    //     $admin = $this->input->post('admin');
    //     $advice = $this->input->post('advice');
    //     $report = array();
    //     if (!empty($medicine)) {
    //         foreach ($medicine as $key => $value) {
    //             $report[$value] = array(
    //                 'dosage' => $dosage[$key],
    //                 'frequency' => $frequency[$key],
    //                 'days' => $days[$key],
    //                 'instruction' => $instruction[$key],
    //             );
    //             // }
    //         }
    //         foreach ($report as $key1 => $value1) {
    //             $final[] = $key1 . '***' . implode('***', $value1);
    //         }
    //         $final_report = implode('###', $final);
    //     } else {
    //         $final_report = '';
    //     }
    //     // $this->load->library('form_validation');
    //     // $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    //     // // Validating Date Field
    //     // $this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[1]|max_length[100]|xss_clean');
    //     // // Validating Patient Field
    //     // $this->form_validation->set_rules('patient', 'Patient', 'trim|required|min_length[1]|max_length[100]|xss_clean');
    //     // // Validating Doctor Field
    //     // $this->form_validation->set_rules('doctor', 'Doctor', 'trim|min_length[1]|max_length[100]|xss_clean');
    //     // // Validating Advice Field
    //     // $this->form_validation->set_rules('symptom', 'History', 'trim|min_length[1]|max_length[1000]|xss_clean');
    //     // // Validating Do And Dont Name Field
    //     // $this->form_validation->set_rules('note', 'Note', 'trim|min_length[1]|max_length[1000]|xss_clean');
    //     // // Validating Advice Field
    //     // $this->form_validation->set_rules('advice', 'Advice', 'trim|min_length[1]|max_length[1000]|xss_clean');
    //     // // Validating Validity Field
    //     // $this->form_validation->set_rules('validity', 'Validity', 'trim|min_length[1]|max_length[100]|xss_clean');
    //     if ($this->form_validation->run() == FALSE) {
    //         if (!empty($id)) {
    //             $data['message'] = 'failed';
    //         } else {
    //             // $data = array();
    //             // $data['setval'] = 'setval';
    //             // $data['medicines'] = $this->api_model->getMedicine($this->hospitalID);
    //             // $data['patients'] = $this->api_model->getPatient($this->hospitalID);
    //             // $data['doctors'] = $this->api_model->getDoctor($this->hospitalID);
    //             // $data['settings'] = $this->api_model->getSettings($this->hospitalID);
    //             // $this->load->view('home/dashboard', $data); // just the header file
    //             // $this->load->view('add_new_prescription_view', $data);
    //             // $this->load->view('home/footer'); // just the header file
    //             $data['message'] = 'failed';
    //         }
    //     } else {
    //         $data = array();
    //         $patientname = $this->api_model->getPatientById($patient, $this->hospitalID)->name;
    //         $doctorname = $this->api_model->getDoctorById($doctor, $this->hospitalID)->name;
    //         $data = array('date' => $date,
    //             'patient' => $patient,
    //             'doctor' => $doctor,
    //             'symptom' => $symptom,
    //             'medicine' => $final_report,
    //             'note' => $note,
    //             'advice' => $advice,
    //             'patientname' => $patientname,
    //             'doctorname' => $doctorname
    //         );
    //         if (empty($id)) {
    //             $this->api_model->insertPrescription($data, $this->hospitalID);
    //             $data['message'] = 'success';
    //             // $this->session->set_flashdata('feedback', lang('added'));
    //         } else {
    //             $this->prescription_model->updatePrescription($id, $data);
    //             $data['message'] = 'success';
    //             // $this->session->set_flashdata('feedback', lang('updated'));
    //         }
    //     }
    //                 echo json_encode($data);
    // }


    public function addNewPrescription()
    {
        $tab = $this->input->post('tab');
        $date = $this->input->post('date');
        $patient_ion_id = $this->input->post('ion_id');
        // $patient_ion_id = 764;
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        if (!empty($date)) {
            $date = strtotime($date);
        }

        $patient = $this->input->post('patient');
        $doctor = $this->input->post('doctor');
        $note = $this->input->post('note');
        $symptom = $this->input->post('symptom');
        $medicine = $this->input->post('medicine');
        $dosage = $this->input->post('dosage');
        $frequency = $this->input->post('frequency');
        $days = $this->input->post('days');
        $instruction = $this->input->post('instruction');
        $admin = $this->input->post('admin');

        $advice = $this->input->post('advice');

        // $report = array();
        // if (!empty($medicine)) {
        //     foreach ($medicine as $key => $value) {
        //         $report[$value] = array(
        //             'dosage' => $dosage[$key],
        //             'frequency' => $frequency[$key],
        //             'days' => $days[$key],
        //             'instruction' => $instruction[$key],
        //         );
        //         // }
        //     }
        //     foreach ($report as $key1 => $value1) {
        //         $final[] = $key1 . '***' . implode('***', $value1);
        //     }
        //     $final_report = implode('###', $final);
        // } else {
        //     $final_report = '';
        // }

        $data = array();
        $patientname = $this->api_model->getPatientById($patient, $this->hospitalID)->name;
        $doctorname = $this->api_model->getDoctorById($doctor, $this->hospitalID)->name;
        $data = array(
            'date' => $date,
            'patient' => $patient,
            'doctor' => $doctor,
            'symptom' => $symptom,
            // 'medicine' => $final_report,
            'medicine' => $medicine,
            'note' => $note,
            'advice' => $advice,
            'patientname' => $patientname,
            'doctorname' => $doctorname
        );
        $this->api_model->insertPrescription($data, $this->hospitalID);
        $data['message'] = 'success';
        echo json_encode($data);
    }

    public function editNewPrescription()
    {
        $id = $this->input->post('id');
        $tab = $this->input->post('tab');
        $date = $this->input->post('date');
        $patient_ion_id = 764;
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        if (!empty($date)) {
            $date = strtotime($date);
        }

        $patient = $this->input->post('patient');
        $doctor = $this->input->post('doctor');
        $note = $this->input->post('note');
        // $symptom = $this->input->post('symptom');
        // $medicine = $this->input->post('medicine');
        // $dosage = $this->input->post('dosage');
        // $frequency = $this->input->post('frequency');
        // $days = $this->input->post('days');
        // $instruction = $this->input->post('instruction');
        // $admin = $this->input->post('admin');
        // $advice = $this->input->post('advice');
        // $report = array();
        // if (!empty($medicine)) {
        //     foreach ($medicine as $key => $value) {
        //         $report[$value] = array(
        //             'dosage' => $dosage[$key],
        //             'frequency' => $frequency[$key],
        //             'days' => $days[$key],
        //             'instruction' => $instruction[$key],
        //         );
        //         // }
        //     }
        //     foreach ($report as $key1 => $value1) {
        //         $final[] = $key1 . '***' . implode('***', $value1);
        //     }
        //     $final_report = implode('###', $final);
        // } else {
        //     $final_report = '';
        // }

        $data = array();
        $patientname = $this->api_model->getPatientById($patient, $this->hospitalID)->name;
        $doctorname = $this->api_model->getDoctorById($doctor, $this->hospitalID)->name;
        $data = array(
            'date' => $date,
            'patient' => $patient,
            'doctor' => $doctor,
            // 'symptom' => $symptom,
            // 'medicine' => $final_report,
            'note' => $note,
            // 'advice' => $advice,
            'patientname' => $patientname,
            'doctorname' => $doctorname
        );
        if (empty($id)) {
            $this->api_model->insertPrescription($data, $this->hospitalID);
            $data['message'] = 'success';
            // $this->session->set_flashdata('feedback', lang('added'));
        } else {
            $this->prescription_model->updatePrescription($id, $data);
            $data['message'] = 'success';
            // $this->session->set_flashdata('feedback', lang('updated'));
        }
    }

    public function getPatientList()
    {
        if (!$this->ion_auth->logged_in()) {
            $patient_ion_id = $this->input->get('id');
            if (empty($patient_ion_id)) {
                echo json_encode(array('status' => 'error', 'message' => 'Authentication required', 'data' => []));
                return;
            }
        } else {
            $patient_ion_id = $this->ion_auth->get_user_id();
        }

        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        if (empty($this->hospitalID)) {
            echo json_encode([]);
            return;
        }

        $this->db->where('hospital_id', $this->hospitalID);
        $patients = $this->db->get('patient')->result();

        echo json_encode($patients);
    }

    public function getDoctorList()
    {
        $patient_ion_id = $this->input->get('id');
        if ($this->ion_auth->logged_in()) {
            $patient_ion_id = $this->ion_auth->get_user_id();
        }

        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        if (empty($this->hospitalID)) {
            $this->hospitalID = $this->session->userdata('hospital_id');
        }

        if (empty($this->hospitalID)) {
            echo json_encode([]);
            return;
        }

        $this->db->where('hospital_id', $this->hospitalID);
        $this->db->where('active', 1);
        $doctors = $this->db->get('doctor')->result();

        echo json_encode($doctors);
    }

    public function getDoctorTimeSlop()
    {
        $id = $this->input->get('doctor_id');
        $date = $this->input->get('date');
        if (empty($date)) {
            $data['message'] = 'failed';
            echo json_encode($data);
        } else {
            $day = date('l', strtotime($date));

            $this->db->where('doctor', $id);
            $this->db->where('weekday', $day);
            $result = $this->db->get('time_slot')->result();
            echo json_encode($result);
        }
    }

    public function findPatientIonId($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('patient')->row()->ion_user_id;
    }

    public function findDoctorIonId($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('doctor')->row()->ion_user_id;
    }

    function paymentPaypal($data)
    {
        $this->db->where('hospital_id', $this->hospitalID);
        $this->db->where('name', 'Paypal');
        $paypal = $this->db->get('paymentgateway')->row();
        $gateway = Omnipay::create('PayPal_Pro');
        $gateway->setUsername($paypal->APIUsername);
        $gateway->setPassword($paypal->APIPassword);
        $gateway->setSignature($paypal->APISignature);
        if ($paypal->status == 'test') {
            $gateway->setTestMode(true); // here 'true' is for sandbox. Pass 'false' when go live
        } else {
            $gateway->setTestMode(false);
        }

        $arr_expiry = explode("/", $data['expire_date']);
        $cardholdername = explode(" ", $data['cardholdername']);
        $currency = $this->currencyCode();
        $formData = array(
            'firstName' => trim($cardholdername[0]),
            'lastName' => trim($cardholdername[1]),
            'number' => $data['card_number'],
            'expiryMonth' => trim($arr_expiry[0]),
            'expiryYear' => trim($arr_expiry[1]),
            'cvv' => $data['cvv']
        );

        try {

            // Send purchase request
            $response = $gateway->purchase([
                'amount' => $data['deposited_amount'],
                'currency' => $currency,
                'card' => $formData
            ])->send();

            // Process response
            if ($response->isSuccessful()) {
                $date = time();
                $data1 = array(
                    'patient' => $data['patient'],
                    'date' => $date,
                    'payment_id' => $data['payment_id'],
                    'deposited_amount' => $data['deposited_amount'],
                    'deposit_type' => 'Card',
                    'gateway' => 'PayPal',
                    'user' => $data['user'],
                    'hospital_id' => $this->hospitalID
                );
                $this->api_model->insertDeposit($data1, $this->hospitalID);
                $data = 'successful';
                echo json_encode($data);
            } else {
                // Payment failed
                //  echo "Payment failed. " . $response->getMessage();
                echo ($response->getMessage());
                $data = 'failed';
                echo json_encode($data);
            }
        } catch (Exception $e) {
            $data = 'failed';
            echo json_encode($data);
        }
    }

    function currencyCode()
    {
        $currency = $this->db->get('settings')->row()->currency;
        if ($currency == '$' || strtoupper($currency) == 'USD') {
            $currency = 'USD';
        }
        if ($currency == 'R' || strtoupper($currency) == 'ZAR') {
            $currency = 'ZAR';
        }
        if (strtoupper($currency) == 'TK' || strtoupper($currency) == 'BDT' || strtoupper($currency) == 'TAKA' || $currency == 'ট') {
            $currency = 'BDT';
        }
        if (strtoupper($currency) == 'CNY') {
            $currency = 'CNY';
        }
        if ($currency == '€' || strtoupper($currency) == 'EUR') {
            $currency = 'EUR';
        }
        if ($currency == '₹' || strtoupper($currency) == 'INR') {
            $currency = 'INR';
        }
        if (strtoupper($currency) == 'CNY') {
            $currency = 'CNY';
        }
        if (strtoupper($currency) == 'BRL' || $currency == 'R$') {
            $currency = 'BRL';
        }
        if (strtoupper($currency) == 'GBP' || $currency == '£') {
            $currency = 'GBP';
        }
        if (strtoupper($currency) == 'IDR' || $currency == 'Rp') {
            $currency = 'IDR';
        }
        if (strtoupper($currency) == 'NGN' || $currency == '₦') {
            $currency = 'NGN';
        }

        if (strtoupper($currency) == 'RS' || strtoupper($currency) == 'INR' || strtoupper($currency) == 'RUPEE') {
            $currency = 'INR';
        }
        if (strtoupper($currency) == 'AUD') {
            $currency = 'AUD';
        }
        if (strtoupper($currency) == 'CAD') {
            $currency = 'CAD';
        }
        return $currency;
    }

    public function getPatientDeposit()
    {
        $patient = $this->input->post('id');
        $patient_ion_id = $this->input->post('ion_id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $data = $this->api_model->getDepositByPatientId($patient, $this->hospitalID);
        echo json_encode($data);
    }

    public function totalAmountPatient()
    {
        $patient = $this->input->post('id');
        $patient_ion_id = $this->input->post('ion_id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $payments = $this->api_model->getPaymentByPatientId($patient, $this->hospitalID);
        $total_bill = array();
        foreach ($payments as $payment) {
            $total_bill[] = $payment->gross_total;
        }
        if (!empty($total_bill)) {
            $data['total'] = array_sum($total_bill);
        } else {
            $data['total'] = 0;
        }

        $deposits = $this->api_model->getDepositByPatientId($patient, $this->hospitalID);
        foreach ($deposits as $deposit) {
            $total_deposit[] = $deposit->deposited_amount;
        }
        $data['deposit'] = array_sum($total_deposit);
        $data['due'] = $data['total'] - $data['deposit'];

        echo json_encode($data);
    }

    function getMedicineById()
    {
        $id = $this->input->post('id');
        $ion_id = $this->input->post('ion_id');
        $this->hospitalID = $this->getHospitalID($ion_id);
        $data = $this->api_model->getMedicineById($id, $this->hospitalID);
        echo json_encode($data);
    }

    public function getMedicineBySearch()
    {
        $search = $this->input->get('search');
        $ion_id = $this->input->get('ion_id');
        $this->hospitalID = $this->getHospitalID($ion_id);
        $data = $this->api_model->getMedicineBySearch($search, $this->hospitalID);
        echo json_encode($data);
    }

    // Aurnab functions

    function getAllDepartments()
    {
        // $id = $this->input->get('id');
        $ion_id = $this->input->post('ion_id');

        $this->hospitalID = $this->getHospitalID($ion_id);

        $data = $this->api_model->getDepartment($this->hospitalID);
        echo json_encode($data);
    }

    function getDoctorsByDepartmentname()
    {
        // $id = $this->input->get('id');
        $ion_id = $this->input->post('ion_id');

        $department = $this->input->post('department');

        $this->hospitalID = $this->getHospitalID($ion_id);

        $data = $this->api_model->getDoctorByDepartmentname($department, $this->hospitalID);
        echo json_encode($data);
    }

    //Get Lab reports
    function getLabReports()
    {
        $userionId = $this->input->post('user_ion_id');
        $this->hospitalID = $this->getHospitalID($userionId);
        $userId = $this->api_model->getPatientByIonUserId($userionId, $this->hospitalID)->id;
        $labs = $this->api_model->getPatientLabReport($userId, $this->hospitalID);

        foreach ($labs as $key => $lab) {
            $new_entry = array();
            if ($lab->category_id != null) {
                $test_name = $this->api_model->getPaymentCategoryById($lab->category_id);
                $new_entry['test_name'] = $test_name->category;
            } else {
                $$new_entry['test_name'] = "";
            }

            $lab->test_name = $test_name->category;
        }




        echo json_encode($labs);
    }

    //Get Lab reports for doctor
    function getLabReportsByDoctor()
    {
        $patient_ionid = $this->input->get('patient_ion_id');

        $this->hospitalID = $this->getHospitalID($patient_ionid);

        $patientdata = $this->db->get_where('patient', array('ion_user_id' => $patient_ionid))->row();
        $patient_id = $patientdata->id;
        $labs = $this->api_model->getPatientLabReport($patient_id, $this->hospitalID);

        echo json_encode($labs);
    }

    //Get Lab reports for doctor
    function getAllLabReports()
    {
        $user_ion_id = $this->input->get('user_ion_id');

        $this->hospitalID = $this->getHospitalID($user_ion_id);

        // echo $this->hospitalID ;
        // die();



        $labs = $this->api_model->getLab($this->hospitalID);
        // echo $labs[0]->id;
        // die();

        echo json_encode($labs);
    }

    //Get Lab report details
    function getLabReportDetails()
    {
        $labreportid = $this->input->post('id');
        $userId = $this->input->post('user_ion_id');
        // $group = $this->input->post('group');
        $this->hospitalID = $this->getHospitalID($userId);
        $data['labreport'] = $this->api_model->getLabById($labreportid, $this->hospitalID);
        if (!empty($data['labreport']->hospital_id)) {
            if ($data['labreport']->hospital_id != $this->hospitalID) {
                $data['message'] = 'invalid';
                echo json_encode($data);
            } else {
                $data['settings'] = $this->api_model->getSettings($this->hospitalID);
                $data['doctor'] = $this->api_model->getDoctorById($data['labreport']->doctor, $this->hospitalID);
                $data['user'] = $this->api_model->getPatientById($data['labreport']->patient, $this->hospitalID);
                $data['message'] = 'successful';


                if ($data['labreport']->category_id != null) {
                    $test_name = $this->api_model->getPaymentCategoryById($data['labreport']->category_id);
                    $new_entry['test_name'] = $test_name->category;
                } else {
                    $$new_entry['test_name'] = "";
                }

                $data['labreport']->test_name = $test_name->category;


                echo json_encode($data);
            }
        } else {
            $data['message'] = 'failed';
            echo json_encode($data);
        }
    }


    function getCaseByPatientIonId()
    {
        $patient_ion_id = $this->input->post('user_ion_id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient_id = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;
        $data = $this->api_model->getCaseByPatientId($patient_id, $this->hospitalID);
        echo json_encode($data);
    }


function getDocumentByPatientIonId()
    {
        $json = file_get_contents('php://input');

        // Decode the JSON data into an associative array (use true to get an array instead of an object)
        $data = json_decode($json, true);
        $patient_id = $data['patient_id'];
        $patient_ion_id = $this->api_model->getPatientByIdWithoutHospitalId($patient_id)->ion_user_id;
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $data = $this->api_model->getDocumentByPatientId($patient_id, $this->hospitalID);
        echo json_encode($data);
    }

    function addCase()
    {

        $patient_id = $this->input->post('patient_id');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $case_id = $this->input->post('case_id');
        $date = $this->input->post('date');
          $diagnosis_ids = $this->input->post('diagnosis_id');
        $treatment_ids = $this->input->post('treatment_id');
        $symptom_ids = $this->input->post('symptom_id');
        $test_ids = $this->input->post('test_id');
        $advice_ids = $this->input->post('advice_id');

        $patient = $this->api_model->getPatientByIdWithoutHospitalId($patient_id);
        $patient_name = $patient->name;
        $patient_address = $patient->address;
        $patient_phone = $patient->phone;

        // validation code
        $this->form_validation->set_rules('patient_id', 'Patient ID', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation failed'
            ]);
            return;
        }

        $this->hospitalID = $patient->hospital_id;
        $diagnosis_ids_str = is_array($diagnosis_ids) ? implode(',', $diagnosis_ids) : '';
            $treatment_ids_str = is_array($treatment_ids) ? implode(',', $treatment_ids) : '';
            $symptom_ids_str = is_array($symptom_ids) ? implode(',', $symptom_ids) : '';
            $test_ids_str = is_array($test_ids) ? implode(',', $test_ids) : '';
            $advice_ids_str = is_array($advice_ids) ? implode(',', $advice_ids) : '';

        $data = array(
            'patient_id' => $patient_id,
            'title' => $title,
            'date' => $date,
            'diagnosis_id' => $diagnosis_ids_str,
                'treatment_id' => $treatment_ids_str,
                'symptom_id' => $symptom_ids_str,
                'test_id' => $test_ids_str,
                'advice_id' => $advice_ids_str,
            'description' => $description,
            'date' => time(),
            'patient_name' => $patient_name,
            'patient_address' => $patient_address,
            'patient_phone' => $patient_phone,
            'hospital_id' => $this->hospitalID
        );

        if (!empty($case_id)) {
            if ($this->api_model->updateCase($case_id, $data)) {
                show_swal(lang('case_history_updated_successfully'), 'success', lang('updated'));
                redirect('api/addCaseView?case_id=' . $case_id);
            }
        } else {
            if ($this->api_model->insertCase($data)) {
                $case_id = $this->db->insert_id();
                show_swal(lang('case_history_added_successfully'), 'success', lang('added'));
                redirect('api/addCaseView?case_id=' . $case_id);
            } else {
                redirect('api/addCaseView?patient_id=' . $patient_id);
            }
        }
    }


    function deleteCase()
    {
        $case_id = $this->input->get('id');

        if ($this->api_model->deleteCase($case_id)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Case deleted successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to delete case'
            ]);
        }
    }



    function addCaseView()
    {
        $language = $this->input->get('language');
        $this->lang->load('system_syntax', $language);
        $patient_id = $this->input->get('patient_id');
        // $patient_id = 53;
        $data['hospital_id'] = $this->db->get_where('patient', array('id' => $patient_id))->row()->hospital_id;
        // $data['patient_ion_id'] = $this->input->get('patient_ion_id');
        $case_id = $this->input->get('case_id');
        $data['case'] = $this->api_model->getCaseById($case_id);
        if (!empty($case_id)) {
            $data['case'] = $this->api_model->getCaseById($case_id);
            $data['selected_symptoms'] = $this->api_model->getSymptomsByCaseId($case_id);
            // $data['selected_symptom_ids'] = explode(',', $data['case']->symptom_id);
            $data['selected_labtest'] = $this->api_model->getLabtestByCaseId($case_id);
            $data['selected_diagnosis'] = $this->api_model->getDiagnosisByCaseId($case_id);
            $data['selected_treatment'] = $this->api_model->getTreatmentByCaseId($case_id);
            $data['selected_advice'] = $this->api_model->getAdviceByCaseId($case_id);
            $this->load->view('add_case', $data);
        } else {
            $this->load->view('add_case', [
                'patient_id' => $patient_id,
                'hospital_id' => $data['hospital_id'] 
            ]);
        }
    }


    function addDocumentsFromDoctor()
    {

        $json = file_get_contents('php://input');

        // Decode the JSON data into an associative array (use `true` to get an array instead of an object)
        $data = json_decode($json, true);

        $patient_ion_id = $data['user_ion_id'];
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $patient_id = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID)->id;

        $document = $data['document'];
        $document_name = $data['document_name'];
        $document_type = $data['document_type'];
        $document_description = $data['document_description'];

        $data = array(
            'patient_id' => $patient_id,
            'document' => $document,
            'document_name' => $document_name,
            'document_type' => $document_type,
            'document_description' => $document_description,
            'date' => time(),
            'hospital_id' => $this->hospitalID
        );

        if ($this->api_model->insertDocument($data)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Document added successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add document'
            ]);
        }
    }
    
    
    // create a function t get toal number of appointments, prescriptions, lab reports nd cases for a patient
    public function getPatientStatistics() {
        $patient_ion_id = $this->input->post('patient_ion_id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        
        // Get patient ID from ion_user_id
        $this->db->where('ion_user_id', $patient_ion_id);
        $this->db->where('hospital_id', $this->hospitalID);
        $patient_id = $this->db->get('patient')->row()->id;
        
        // Count appointments
        $this->db->where('patient', $patient_id);
        $this->db->where('hospital_id', $this->hospitalID);
        $appointments = $this->db->count_all_results('appointment');
        
        // Count prescriptions
        $this->db->where('patient', $patient_id);
        $this->db->where('hospital_id', $this->hospitalID);
        $prescriptions = $this->db->count_all_results('prescription');
        
        // Count lab reports
        $this->db->where('patient', $patient_id);
        $this->db->where('hospital_id', $this->hospitalID);
        $lab_reports = $this->db->count_all_results('lab');
        
        // Count case histories
        $this->db->where('patient_id', $patient_id);
        $this->db->where('hospital_id', $this->hospitalID);
        $case_histories = $this->db->count_all_results('medical_history');
        
        $data = array(
            'appointments' => $appointments,
            'prescriptions' => $prescriptions,
            'lab_reports' => $lab_reports,
            'case_histories' => $case_histories
        );
        
        echo json_encode($data);
    }

    // Get patient's total bill, deposit and due balance
    public function getPatientBalance() {
        $patient_ion_id = $this->input->post('patient_ion_id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        
        // Get patient ID from ion_user_id
        $this->db->where('ion_user_id', $patient_ion_id);
        $this->db->where('hospital_id', $this->hospitalID);
        $patient_id = $this->db->get('patient')->row()->id;
        
        // Get all payments
        $this->db->where('hospital_id', $this->hospitalID);
        $this->db->where('patient', $patient_id);
        $payments = $this->db->get('payment')->result();
        
        // Calculate total bill
        $total_bill = 0;
        foreach($payments as $payment) {
            $total_bill += $payment->gross_total;
        }
        
        // Get all deposits
        $this->db->where('hospital_id', $this->hospitalID);
        $this->db->where('patient', $patient_id);
        $deposits = $this->db->get('patient_deposit')->result();
        
        // Calculate total deposit
        $total_deposit = 0;
        foreach($deposits as $deposit) {
            $total_deposit += $deposit->deposited_amount;
        }
        
        $data = array(
            'total_bill' => $total_bill,
            'total_deposit' => $total_deposit,
            'due_balance' => $total_bill - $total_deposit
        );
        
        echo json_encode($data);
    }

    // Get last 6 months statistics for patient chart
    public function getLastSixMonthsData() {
        $patient_ion_id = $this->input->post('patient_ion_id');
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        
        // Get patient ID from ion_user_id
        $this->db->where('ion_user_id', $patient_ion_id);
        $this->db->where('hospital_id', $this->hospitalID);
        $patient_id = $this->db->get('patient')->row()->id;
        
        $months = array();
        $appointments = array();
        $prescriptions = array();
        $lab_reports = array();
        
        // Get last 6 months data
        for($i = 5; $i >= 0; $i--) {
            $month = date('m', strtotime("-$i month"));
            $year = date('Y', strtotime("-$i month"));
            $months[] = date('M Y', strtotime("-$i month")); // Month name and year
            
            // Get start and end timestamps for the month
            $month_start = strtotime("$year-$month-01");
            $month_end = strtotime("$year-$month-" . date('t', strtotime("$year-$month-01")));
            
            // Count appointments
            $this->db->where('hospital_id', $this->hospitalID);
            $this->db->where('patient', $patient_id);
            $this->db->where('date >=', $month_start);
            $this->db->where('date <=', $month_end);
            $appointments[] = $this->db->count_all_results('appointment');
            
            // Count prescriptions
            $this->db->where('hospital_id', $this->hospitalID);
            $this->db->where('patient', $patient_id);
            $this->db->where('date >=', $month_start);
            $this->db->where('date <=', $month_end);
            $prescriptions[] = $this->db->count_all_results('prescription');
            
            // Count lab reports
            $this->db->where('hospital_id', $this->hospitalID);
            $this->db->where('patient', $patient_id);
            $this->db->where('date >=', $month_start);
            $this->db->where('date <=', $month_end);
            $lab_reports[] = $this->db->count_all_results('lab');
        }
        
        $data = array(
            'months' => $months,
            'appointments' => $appointments,
            'prescriptions' => $prescriptions,
            'lab_reports' => $lab_reports
        );
        
        echo json_encode($data);
    }
    
    public function getVideoType() {
        $patient_ion_id = $this->input->get('patient_ion_id');
        
        // Get patient's hospital ID
        $this->db->where('ion_user_id', $patient_ion_id);
        $patient = $this->db->get('patient')->row();

        // Get video type from hospital settings
        $this->db->where('hospital_id', $patient->hospital_id);
        $settings = $this->db->get('settings')->row();
        
        $data = array(
            'status' => 'success',
            'video_type' => (isset($settings->video_type)) ? $settings->video_type : ''
        );
        
        echo json_encode($data);
    }
 
    
    function getAllAppoinmentListByPatientIonId()
    {
    
        $id = $this->input->post('id');
        $ion_id = $this->input->post('patient_ion_id');

        
        $this->hospitalID = $this->getHospitalID($ion_id);

        $data1['appointments'] = $this->api_model->getAppointmentByPatietnId($id,$this->hospitalID);
        

        $i = 0;

        $data = [];
        foreach ($data1['appointments'] as $appointment) {
                $patient_ion_id = $ion_id;
                $patient_details = $this->api_model->getPatientByIonUserId($patient_ion_id, $this->hospitalID);
                $patient_id = $patient_details->id;
                if ($patient_id == $appointment->patient) {
                    $patientdetails = $this->api_model->getPatientById($appointment->patient, $this->hospitalID);
                    if (!empty($patientdetails)) {
                        $patientname = $patientdetails->name;
                    } else {
                        $patientname = $appointment->patientname;
                    }
                    $doctordetails = $this->api_model->getDoctorById($appointment->doctor, $this->hospitalID);
                    if (!empty($doctordetails)) {
                        $doctorname = $doctordetails->name;
                    } else {
                        $doctorname = $appointment->doctorname;
                    }


                    if (true) {
                        array_push($data, array(
                            "id" => $appointment->id,
                            "patient_name" => $patientname,
                            "doctor_name" => $doctorname,
                            "date" => date('d-m-Y', $appointment->date),
                            "remarks" => $appointment->remarks,
                            "status" => $appointment->status,
                            'jitsi_link' => $appointment->room_id,
                            'meeting_id' => $appointment->meeting_id,
                            'join_url' => $appointment->join_url,
                            'start_time' => $appointment->s_time,
                            'end_time' => $appointment->e_time,
                        ));
                        $i = $i + 1;
                    } else {
                        $info1[] = array(
                            $appointment->id,
                            $appointment->patientname,
                            $appointment->doctorname,
                            date('d-m-Y', $appointment->date),
                            $appointment->remarks,
                            $appointment->status,
                        );
                    }
                }
            
            
        }

        echo json_encode($data);
    }

    public function createMeeting() {
        $appointment_id = $this->input->post('id');
        $doctor_ion_id = $this->input->post('doctor_ion_id');
        
        // Get the doctor's Zoom credentials
        $settings = $this->api_model->getMeetingSettingsById($doctor_ion_id);
        
        if(empty($settings)) {
            echo json_encode(['status' => 'error', 'message' => 'Zoom settings not found']);
            return;
        }

        $appointment_details = $this->api_model->getAppointmentByIdOnly($appointment_id);
        
        // Create simple JWT token
        $token = $this->createZoomToken($settings->api_key, $settings->secret_key);
        
        // Create Zoom meeting
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.zoom.us/v2/users/me/meetings',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'topic' => 'Doctor Appointment',
                'type' => 2, // Scheduled meeting
                'start_time' => date('Y-m-d\TH:i:s'),
                'duration' => 30,
                'timezone' => 'UTC',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        if ($err) {
            echo json_encode(['status' => 'error', 'message' => 'Connection Error']);
            return;
        }

        $meeting_data = json_decode($response);
        
        if(isset($meeting_data->id)) {
            // Update appointment with meeting details
            $update_data = array(
                'meeting_id' => $meeting_data->id,
                'join_url' => $meeting_data->join_url,
                'start_url' => $meeting_data->start_url
            );
            
            $this->api_model->updateAppointment($appointment_id, $update_data);
            
            echo json_encode([
                'status' => 'success',
                'meeting_id' => $meeting_data->id,
                'join_url' => $meeting_data->join_url
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Failed to create meeting',
                'details' => $meeting_data
            ]);
        }
    }

    private function createZoomToken($key, $secret) {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        
        $payload = [
            'iss' => $key,
            'exp' => time() + 3600 // 1 hour expiry
        ];
        
        $header_encoded = $this->base64UrlEncode(json_encode($header));
        $payload_encoded = $this->base64UrlEncode(json_encode($payload));
        
        $signature_input = $header_encoded . "." . $payload_encoded;
        $signature = hash_hmac('sha256', $signature_input, $secret, true);
        $signature_encoded = $this->base64UrlEncode($signature);
        
        return $header_encoded . "." . $payload_encoded . "." . $signature_encoded;
    }

    private function base64UrlEncode($data) {
        $base64 = base64_encode($data);
        $base64Url = strtr($base64, '+/', '-_');
        return rtrim($base64Url, '=');
    }
    
    
        public function getPatientInfoList()
    {
        // Search term
        $this->hospitalID = $this->input->get('hospital_id');
        $searchTerm = $this->input->post('searchTerm');
        $response = $this->api_model->getPatientInfoList($searchTerm, $this->hospitalID);
        echo json_encode($response);
    }
    
      public function getDiagnosisInfo()
    {
        // $patient_ion_id = 1141;
        // print_r($patient_ion_id);die();
        // $patient_ion_id = $this->input->get('patient_ion_id');
        $this->hospitalID = $this->input->get('hospital_id');
        $searchTerm = $this->input->post('searchTerm');
        $response = $this->api_model->getDiagnosisInfo($searchTerm, $this->hospitalID);
        echo json_encode($response);
    }
    
        public function getTreatmentInfo()
    {
        $this->hospitalID = $this->input->get('hospital_id');
        $searchTerm = $this->input->post('searchTerm');
        $response = $this->api_model->getTreatmentInfo($searchTerm, $this->hospitalID);
        echo json_encode($response);
    }
    
        public function getSymptomInfo()
    {
       $this->hospitalID = $this->input->get('hospital_id');
        $searchTerm = $this->input->post('searchTerm');
        $response = $this->api_model->getSymptomInfo($searchTerm, $this->hospitalID);
        echo json_encode($response);
    }

    public function getAdviceInfo()
    {
        $this->hospitalID = $this->input->get('hospital_id');
        $searchTerm = $this->input->post('searchTerm');
        $response = $this->api_model->getAdviceInfo($searchTerm, $this->hospitalID);
        echo json_encode($response);
    }
    
        public function getTestInfo()
    {
        $this->hospitalID = $this->input->get('hospital_id');
        $searchTerm = $this->input->post('searchTerm');
        $response = $this->api_model->getTestInfo($searchTerm, $this->hospitalID);
        echo json_encode($response);
    }
    
    public function addNewSymptom()
    {
        $name = $this->input->post('name');
        $data = array(
            'name' => $name,
        );

        if (empty($id)) {
            // Adding New Symptom
            $this->api_model->insertSymptom($data);
            $new_symptom_id = $this->db->insert_id();
            // Insert and return new ID

            // Return new symptom ID and name in JSON format
            echo json_encode([
                'id' => $new_symptom_id,
                'name' => $name
            ]);
        } else {
            // Handle updates if needed
        }
    }
    
    public function addNewAdvice()
    {
        $name = $this->input->post('name');
        $data = array(
            'name' => $name,
        );

        if (empty($id)) {
            // Adding New Symptom
            $this->api_model->insertAdvice($data);
            $new_advice_id = $this->db->insert_id();
            // Insert and return new ID

            // Return new symptom ID and name in JSON format
            echo json_encode([
                'id' => $new_advice_id,
                'name' => $name
            ]);
        } else {
            // Handle updates if needed
        }
    }
    
    public function addNewDiagnosis()
    {
        $disease_with_outbreak_potential = $this->input->post('disease_with_outbreak_potential');
        $maximum_expected_number_of_patient_in_a_week = $this->input->post('maximum_expected_number_of_patient_in_a_week');
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $code = $this->input->post('code');
        $data = array(
            'disease_with_outbreak_potential' => $disease_with_outbreak_potential,
            'maximum_expected_number_of_patient_in_a_week' => $maximum_expected_number_of_patient_in_a_week,
            'name' => $name,
            'description' => $description,
            'code' => $code,
        );

        if (empty($id)) {
            // Adding New Symptom
            $this->api_model->insertDiagnosis($data);
            $new_diagnosis_id = $this->db->insert_id();
            // Insert and return new ID

            // Return new symptom ID and name in JSON format
            echo json_encode([
                'id' => $new_diagnosis_id,
                'name' => $name
            ]);
        } else {
            // Handle updates if needed
        }
    }
    
    public function addNewTreatment()
    {
        $name = $this->input->post('name');
        $data = array(
            'name' => $name,
        );

        if (empty($id)) {
            // Adding New Symptom
            $this->api_model->insertTreatment($data);
            $new_treatment_id = $this->db->insert_id();
            // Insert and return new ID

            // Return new symptom ID and name in JSON format
            echo json_encode([
                'id' => $new_treatment_id,
                'name' => $name
            ]);
        } else {
            // Handle updates if needed
        }
    }
    
    function documentsByPatientId()
    {
        $patient_id = $this->input->get('patient_id');
        $patient_ion_id = $this->api_model->getPatientByIdWithoutHospitalId($patient_id)->ion_user_id;
        $this->hospitalID = $this->getHospitalID($patient_ion_id);
        $data = $this->api_model->getPatientMaterialByPatientId($patient_id, $this->hospitalID);
        echo json_encode($data);
    }
    function deleteDocument() {
    $document_id = $this->input->get('id');
    $this->db->where('id', $document_id);
    $this->db->delete('patient_material');
    echo json_encode(['status' => 'success', 'message' => 'Document deleted successfully']);
}



















private function getZoomAccessToken($doctor_ion_id) {
    $settings = $this->api_model->getMeetingSettingsById($doctor_ion_id);
    $account_id = $settings->api_key; 
    $client_id = $settings->secret_key;
    $client_secret = $settings->url;
    
    $auth_url = 'https://zoom.us/oauth/token';
    $auth_string = base64_encode("{$client_id}:{$client_secret}");

    $headers = array(
        "Authorization: Basic $auth_string",
        "Content-Type: application/x-www-form-urlencoded",
    );

    $post_data = http_build_query(array(
        'grant_type' => 'account_credentials',
        'account_id' => $account_id,
    ));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $auth_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);

    if (isset($response_data['access_token'])) {
        return $response_data['access_token'];
    } else {
        return false;
    }
}

private function createZoomMeeting($topic, $start_time, $duration, $timezone, $doctor_ion_id) {
    $access_token = $this->getZoomAccessToken($doctor_ion_id);
    
    if(!$access_token) {
        return false;
    }

    $data = array(
        "topic" => $topic,
        "type" => 2,
        "start_time" => $start_time,
        "duration" => $duration,
        "timezone" => $timezone,
        "settings" => array(
            "host_video" => true,
            "participant_video" => true,
            "join_before_host" => false,
            "mute_upon_entry" => false,
            "auto_recording" => "none",
        ),
    );

    $headers = array(
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.zoom.us/v2/users/me/meetings');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

public function instantLiveApi() {
    $id = $this->input->post('id');
    $doctor_ion_id = $this->input->post('doctor_ion_id');
    $this->hospitalID = $this->getHospitalID($doctor_ion_id);
    
    $topic = "Doctor Appointment";
    $start_time = date('Y-m-d\TH:i:s'); // Current time
    $duration = 60;
    $timezone = "UTC";

    try {
        $meeting = $this->createZoomMeeting($topic, $start_time, $duration, $timezone, $doctor_ion_id);

        if (isset($meeting['id'])) {
            // Update appointment with meeting details
            $data_update_appointment = array(
                'meeting_id' => $meeting['id'],
                'start_url' => $meeting['start_url'],
                'join_url' => $meeting['join_url'],
            );
            
            $this->api_model->updateAppointment($id, $data_update_appointment);
            
            $response = array(
                'status' => 'success',
                'message' => 'Meeting created successfully',
                'meeting_id' => $meeting['id'],
                'start_url' => $meeting['start_url'],
                'join_url' => $meeting['join_url']
            );
            
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Failed to create meeting'
            );
        }
        
    } catch (Exception $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage()
        );
    }
    
    echo json_encode($response);
}























    
}
