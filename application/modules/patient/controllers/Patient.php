<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Patient extends MX_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('patient_model');
        $this->load->model('donor/donor_model');
        $this->load->model('appointment/appointment_model');
        $this->load->model('doctorvisit/doctorvisit_model');
        $this->load->model('bed/bed_model');
        $this->load->model('lab/lab_model');
        $this->load->model('finance/finance_model');
        $this->load->model('finance/pharmacy_model');
        $this->load->model('sms/sms_model');
        $this->load->model('pgateway/pgateway_model');
        $this->load->module('sms');
        $this->load->model('prescription/prescription_model');
        require APPPATH . 'third_party/stripe/stripe-php/init.php';
        $this->load->model('medicine/medicine_model');
        $this->load->model('doctor/doctor_model');
        $this->load->module('paypal');
        $this->load->model('symptom/symptom_model');
        $this->load->model('diagnosis/diagnosis_model');
        $this->load->model('treatment/treatment_model');
        $this->load->model('advice/advice_model');
        if (!$this->ion_auth->in_group(array('admin', 'Nurse', 'Patient', 'Doctor', 'Laboratorist', 'Accountant', 'Receptionist'))) {
            redirect('home/permission');
        }
    }

    public function index()
    {
        if ($this->ion_auth->in_group(array('Patient'))) {
            redirect('home/permission');
        }
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['groups'] = $this->donor_model->getBloodBank();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('patient', $data);
        $this->load->view('home/footer');
    }

    public function calendar()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('calendar', $data);
        $this->load->view('home/footer');
    }

    public function addNewView()
    {
        if ($this->ion_auth->in_group(array('Patient'))) {
            redirect('home/permission');
        }
        $data = array();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['groups'] = $this->donor_model->getBloodBank();
        $this->load->view('home/dashboard');
        $this->load->view('add_new', $data);
        $this->load->view('home/footer');
    }

    public function addNewViewQuick()
    {
        if ($this->ion_auth->in_group(array('Patient'))) {
            redirect('home/permission');
        }
        $data = array();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['groups'] = $this->donor_model->getBloodBank();
        $this->load->view('home/dashboard');
        $this->load->view('add_new', $data);
        $this->load->view('home/footer');
    }

    public function addNew()
    {

        $id = $this->input->post('id');

        $national_id = $this->input->post('national_id');
        $height = $this->input->post('height');
        $weight = $this->input->post('weight');
        $known_allergies = $this->input->post('known_allergies');
        $medical_history = $this->input->post('medical_history');
        $emergency_contact_name = $this->input->post('emergency_contact_name');
        $emergency_contact_number = $this->input->post('emergency_contact_number');

        if (empty($id)) {
            $limit = $this->patient_model->getLimit();
            if ($limit <= 0) {
                show_swal(lang('patient_limit_exceed'), 'error', lang('error'));
                redirect('patient');
            }
        }

        $redirect = $this->input->get('redirect');
        if (empty($redirect)) {
            $redirect = $this->input->post('redirect');
        }
        $name = $this->input->post('name');
        $password = $this->input->post('password');
        $sms = $this->input->post('sms');
        $doctor = $this->input->post('doctor');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $sex = $this->input->post('sex');
        $birthdate = $this->input->post('birthdate');
        $age_input = $this->input->post('age');

        if ($age_input !== null && $age_input !== '') {
            $years = intval($age_input);
            $months = '0';
            $days = '0';
            $age = $years . '-' . $months . '-' . $days;
        } elseif (!empty($birthdate)) {
            $dateOfBirth = $birthdate;
            $today = date("Y-m-d");
            $diff = date_diff(date_create($dateOfBirth), date_create($today));
            $years = $diff->format('%y');
            $months = $diff->format('%m');
            $days = $diff->format('%d');
            $age = $years . '-' . $months . '-' . $days;
        } else {
            $years = $this->input->post('years');
            $months = $this->input->post('months');
            $days = $this->input->post('days');
            if (!empty($years) || !empty($months) || !empty($days)) {
                $years = !empty($years) ? $years : '0';
                $months = !empty($months) ? $months : '0';
                $days = !empty($days) ? $days : '0';
                $age = $years . '-' . $months . '-' . $days;
            } else {
                if (!empty($id)) {
                    $existing = $this->patient_model->getPatientById($id);
                    $age = !empty($existing) ? $existing->age : '';
                } else {
                    $age = '';
                }
            }
        }

        $bloodgroup = $this->input->post('bloodgroup');
        $patient_id = $this->input->post('p_id');
        if (empty($patient_id)) {
            $patient_id = rand(10000, 1000000);
        }
        if (empty($id)) {
            $add_date = date('m/d/y');
            $registration_time = time();
        } else {
            $patient_details = $this->patient_model->getPatientById($id);
            $add_date = !empty($patient_details) ? $patient_details->add_date : date('m/d/y');
            $registration_time = !empty($patient_details) ? $patient_details->registration_time : time();
        }

        $email = $this->input->post('email');
        if (empty($email)) {
            $clean_phone = preg_replace('/[^0-9]/', '', (string)$phone);
            $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$name));
            if (empty($clean_name)) {
                $clean_name = 'patient';
            }
            $email = $clean_name . '.' . $clean_phone . '@lifecare.local';
            $counter = 1;
            while ($this->ion_auth->email_check($email) || $this->db->get_where('patient', array('email' => $email))->num_rows() > 0) {
                $email = $clean_name . '.' . $clean_phone . '.' . $counter . '@lifecare.local';
                $counter++;
            }
        }

        if (empty($password)) {
            $password = '12345';
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error text-danger font-weight-bold mb-2">', '</div>');

        // Validating Name Field (Required)
        $this->form_validation->set_rules('name', 'Patient Full Name', 'trim|required|min_length[2]|max_length[100]|xss_clean');

        // Validating Phone Field (Required)
        if (empty($id)) {
            $this->form_validation->set_message('valid_bd_phone', 'The {field} field must be a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).');
            $this->form_validation->set_rules(
                'phone',
                'Phone Number',
                array(
                    'trim',
                    'required',
                    'xss_clean',
                    array(
                        'valid_bd_phone',
                        function ($phone) {
                            $clean_phone = preg_replace('/[\s\-]/', '', (string)$phone);
                            return (bool)preg_match('/^(?:\+?880|880|0)?1[3-9]\d{8}$/', $clean_phone);
                        }
                    )
                )
            );
        } else {
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[2]|max_length[50]|xss_clean');
        }

        // Validating Optional Fields: Age, Gender, Weight, Address
        $this->form_validation->set_rules('age', 'Age', 'trim|is_natural|max_length[3]|xss_clean');
        $this->form_validation->set_rules('sex', 'Gender', 'trim|max_length[100]|xss_clean');
        $this->form_validation->set_rules('weight', 'Weight', 'trim|numeric|max_length[10]|xss_clean');
        $this->form_validation->set_rules('address', 'Contact Address', 'trim|max_length[500]|xss_clean');

        // Optional rules for fields when submitted via edit modal or legacy forms
        if (!empty($this->input->post('password'))) {
            $this->form_validation->set_rules('password', 'Password', 'trim|min_length[3]|max_length[100]|xss_clean');
        }
        if (!empty($this->input->post('email'))) {
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|min_length[2]|max_length[100]|xss_clean');
        }
        $this->form_validation->set_rules('birthdate', 'Birth Date', 'trim|max_length[500]|xss_clean');
        $this->form_validation->set_rules('bloodgroup', 'Blood Group', 'trim|max_length[10]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                show_swal(lang('validation_error'), 'error', lang('error'));
                redirect("patient/editPatient?id=$id");
            } else {
                $data = array();
                $data['setval'] = 'setval';
                $data['doctors'] = $this->doctor_model->getDoctor();
                $data['groups'] = $this->donor_model->getBloodBank();
                $this->load->view('home/dashboard');
                $this->load->view('add_new', $data);
                $this->load->view('home/footer');
            }
        } else {
            $img_url = null;
            $file_name = !empty($_FILES['img_url']['name']) ? $_FILES['img_url']['name'] : '';
            if (!empty($file_name)) {
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
                    'max_size' => "10000000",
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('img_url')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];
                }
            } elseif (!empty($id)) {
                $existing = $this->patient_model->getPatientById($id);
                if (!empty($existing) && !empty($existing->img_url)) {
                    $img_url = $existing->img_url;
                }
            }

            $data = array(
                'patient_id' => $patient_id,
                'name' => $name,
                'email' => $email,
                'address' => $address,
                'doctor' => $doctor,
                'phone' => $phone,
                'sex' => $sex,
                'birthdate' => $birthdate,
                'bloodgroup' => $bloodgroup,
                'add_date' => $add_date,
                'registration_time' => $registration_time,
                'payment_confirmation' => 'Active',
                'appointment_confirmation' => 'Active',
                'appointment_creation' => 'Active',
                'meeting_schedule' => 'Active',
                'national_id' => $national_id,
                'height' => $height,
                'weight' => $weight,
                'known_allergies' => $known_allergies,
                'medical_history' => $medical_history,
                'emergency_contact_name' => $emergency_contact_name,
                'emergency_contact_number' => $emergency_contact_number,
                'age' => $age
            );
            if (!empty($img_url)) {
                $data['img_url'] = $img_url;
            }

            $username = $name;

            if (empty($id)) {     // Adding New Patient
                if ($this->ion_auth->email_check($email)) {
                    show_swal(lang('this_email_address_is_already_registered'), 'error', lang('error'));
                    redirect('patient/addNewView');
                } else {
                    $dfg = 5;
                    $this->ion_auth->register($username, $password, $email, $dfg);
                    $user_row = $this->db->get_where('users', array('email' => $email))->row();
                    $ion_user_id = !empty($user_row) ? $user_row->id : null;
                    $this->patient_model->insertPatient($data);
                    if (!isset($this->logs_model)) { $this->load->model('logs/logs_model'); }
                    $this->logs_model->logUserActivity([
                        'module_name' => 'patient',
                        'action_type' => 'CREATE',
                        'record_type' => 'patient',
                        'record_id' => $patient_id,
                        'record_reference' => 'PAT-#' . $patient_id,
                        'description' => "Registered new patient: {$name} (Phone: {$phone})",
                        'metadata' => ['patient_id' => $patient_id, 'name' => $name, 'phone' => $phone, 'sex' => $sex]
                    ]);
                    $patient_row = $this->db->get_where('patient', array('email' => $email))->row();
                    $patient_user_id = !empty($patient_row) ? $patient_row->id : $this->db->insert_id();
                    if (!empty($ion_user_id)) {
                        $id_info = array('ion_user_id' => $ion_user_id);
                        $this->patient_model->updatePatient($patient_user_id, $id_info);
                        $this->hospital_model->addHospitalIdToIonUser($ion_user_id, $this->hospital_id);
                    }
                    $base_url = str_replace(array('http://', 'https://', ' '), '', base_url()) . "auth/login";

                    // SMS notification if active
                    $set['settings'] = $this->settings_model->getSettings();
                    $autosms = $this->sms_model->getAutoSmsByType('patient');
                    if (!empty($autosms) && isset($autosms->status) && $autosms->status == 'Active') {
                        try {
                            $message = $autosms->message;
                            $to = $phone;
                            $name1 = explode(' ', (string)$name);
                            $data1 = array(
                                'firstname' => $name1[0],
                                'lastname' => isset($name1[1]) ? $name1[1] : null,
                                'name' => $name,
                                'base_url' => $base_url,
                                'email' => $email,
                                'password' => $password,
                                'doctor' => $doctor,
                                'company' => !empty($set['settings']) ? $set['settings']->system_vendor : ''
                            );
                            $messageprint = $this->parser->parse_string($message, $data1);
                            $data2[] = array($to => $messageprint);
                            $this->sms->sendSms($to, $message, $data2);
                        } catch (\Throwable $e) {
                            log_message('error', 'Patient SMS error: ' . $e->getMessage());
                        }
                    }

                    // Email notification if active
                    $autoemail = $this->email_model->getAutoEmailByType('patient');
                    if (!empty($autoemail) && isset($autoemail->status) && $autoemail->status == 'Active') {
                        try {
                            $mail_provider = $this->settings_model->getSettings()->emailtype;
                            $settngs_name = $this->settings_model->getSettings()->system_vendor;
                            $email_Settings = $this->email_model->getEmailSettingsByType($mail_provider);

                            if (!empty($email_Settings->user)) {
                                $name1 = explode(' ', (string)$name);
                                $data1 = array(
                                    'firstname' => $name1[0],
                                    'lastname' => isset($name1[1]) ? $name1[1] : null,
                                    'name' => $name,
                                    'base_url' => $base_url,
                                    'email' => $email,
                                    'password' => $password,
                                    'doctor' => $doctor,
                                    'company' => !empty($set['settings']) ? $set['settings']->system_vendor : ''
                                );
                                $message1 = $autoemail->message;
                                $messageprint1 = $this->parser->parse_string($message1, $data1);
                                if ($mail_provider == 'Domain Email') {
                                    $this->email->from($email_Settings->admin_email);
                                }
                                if ($mail_provider == 'Smtp') {
                                    $this->email->from($email_Settings->user, $settngs_name);
                                }
                                $this->email->to($email);
                                $this->email->subject('Registration confirmation');
                                $this->email->message($messageprint1);
                                $this->email->send();
                            }
                        } catch (\Throwable $e) {
                            log_message('error', 'Patient Email error: ' . $e->getMessage());
                        }
                    }

                    show_swal(lang('new_pateint_added_successfully'), 'success', lang('added'));
                }
            } else {

                $patient_details = $this->patient_model->getPatientById($id);
                if (!empty($email) && !empty($patient_details) && $email != $patient_details->email) {
                    if ($this->ion_auth->email_check($email)) {
                        show_swal(lang('this_email_address_is_already_registered'), 'error', lang('error'));
                        redirect("patient/editPatient?id=" . $id);
                    }
                }

                $patient_row = $this->db->get_where('patient', array('id' => $id))->row();
                $ion_user_id = !empty($patient_row) ? $patient_row->ion_user_id : null;
                if (!empty($ion_user_id)) {
                    if (empty($password)) {
                        $user_row = $this->db->get_where('users', array('id' => $ion_user_id))->row();
                        $password = !empty($user_row) ? $user_row->password : '';
                    } else {
                        $password = $this->ion_auth_model->hash_password($password);
                    }
                    $this->patient_model->updateIonUser($username, $email, $password, $ion_user_id);
                }
                $this->patient_model->updatePatient($id, $data);
                if (!isset($this->logs_model)) { $this->load->model('logs/logs_model'); }
                $this->logs_model->logUserActivity([
                    'module_name' => 'patient',
                    'action_type' => 'UPDATE',
                    'record_type' => 'patient',
                    'record_id' => $id,
                    'record_reference' => 'PAT-#' . ($patient_details->patient_id ?? $id),
                    'description' => "Updated patient record: {$name} (ID: {$id})",
                    'metadata' => ['id' => $id, 'name' => $name, 'phone' => $phone]
                ]);
                show_swal(lang('patient_details_updated_successfully'), 'success', lang('updated'));
            }

            if (!empty($redirect)) {
                redirect($redirect);
            } else {
                redirect('patient');
            }
        }
    }

    function editPatient()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['patient'] = $this->patient_model->getPatientById($id);
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['groups'] = $this->donor_model->getBloodBank();
        $this->load->view('home/dashboard');
        $this->load->view('add_new', $data);
        $this->load->view('home/footer');
    }

    function editPatientByJason()
    {
        $id = $this->input->get('id');
        $data['patient'] = $this->patient_model->getPatientById($id);
        $data['doctor'] = $this->doctor_model->getDoctorById($data['patient']->doctor);
        echo json_encode($data);
    }

    function getPatientByJason()
    {
        $id = $this->input->get('id');
        $data['patient'] = $this->patient_model->getPatientById($id);

        $doctor = $data['patient']->doctor;
        $data['doctor'] = $this->doctor_model->getDoctorById($doctor);

        if (!empty($data['patient']->birthdate)) {
            $birthDate = strtotime($data['patient']->birthdate);
            $birthDate = date('m/d/Y', $birthDate);
            $birthDate = explode("/", $birthDate);
            $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
            $data['age'] = $age . ' Year(s)';
        }

        echo json_encode($data);
    }

    function getPatientById()
    {
        $patient_id = $this->input->get('patient_id');
        if (empty($patient_id)) {
            $patient_id = $this->input->get('id');
        }
        
        if (empty($patient_id)) {
            echo json_encode(array('success' => false, 'message' => 'Patient ID is required'));
            return;
        }
        
        $patient = $this->patient_model->getPatientById($patient_id);
        
        if ($patient) {
            $display_age = '';
            if (!empty($patient->age)) {
                $age_parts = explode('-', $patient->age);
                $display_age = $age_parts[0];
            } elseif (!empty($patient->birthdate) && strtotime($patient->birthdate)) {
                $diff = date_diff(date_create($patient->birthdate), date_create(date("Y-m-d")));
                $display_age = $diff->format('%y');
            }

            $data = array(
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'address' => !empty($patient->address) ? $patient->address : '',
                'email' => $patient->email,
                'sex' => $patient->sex,
                'age' => $display_age,
                'raw_age' => $patient->age,
                'weight' => !empty($patient->weight) ? $patient->weight : '',
                'birthdate' => $patient->birthdate
            );
            
            echo json_encode(array('success' => true, 'data' => $data, 'patient' => $data));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Patient not found'));
        }
    }

    public function addNewWithAjax()
    {
        header('Content-Type: application/json');
        
        $limit = $this->patient_model->getLimit();
        if ($limit <= 0) {
            echo json_encode(array(
                'status' => 'error',
                'message' => lang('patient_limit_exceed')
            ));
            return;
        }

        $name = trim((string)$this->input->post('name'));
        $phone = trim((string)$this->input->post('phone'));
        $age_input = $this->input->post('age');
        $sex = $this->input->post('sex');
        $weight = $this->input->post('weight');
        $address = $this->input->post('address');

        if (empty($name)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Patient Full Name is required.'
            ));
            return;
        }

        if (empty($phone)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Phone Number is required.'
            ));
            return;
        }

        $clean_phone = preg_replace('/[\s\-]/', '', (string)$phone);
        if (!preg_match('/^(?:\+?880|880|0)?1[3-9]\d{8}$/', $clean_phone)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Please enter a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).'
            ));
            return;
        }

        $hospital_id = $this->session->userdata('hospital_id');
        if (empty($hospital_id)) {
            $hospital_id = $this->hospital_id;
        }

        $existing_patient = $this->db->get_where('patient', array(
            'hospital_id' => $hospital_id,
            'phone' => $phone,
            'name' => $name
        ))->row();
        if (!empty($existing_patient)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'A patient with this name and phone number already exists (ID: ' . $existing_patient->id . ').'
            ));
            return;
        }

        if ($age_input !== null && $age_input !== '') {
            $age = intval($age_input) . '-0-0';
            $display_age = (string)intval($age_input);
        } else {
            $age = '';
            $display_age = '';
        }

        $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$name));
        if (empty($clean_name)) {
            $clean_name = 'patient';
        }
        $digits_phone = preg_replace('/[^0-9]/', '', (string)$phone);
        $email = $clean_name . '.' . $digits_phone . '@lifecare.local';
        $counter = 1;
        while ($this->ion_auth->email_check($email) || $this->db->get_where('patient', array('email' => $email))->num_rows() > 0) {
            $email = $clean_name . '.' . $digits_phone . '.' . $counter . '@lifecare.local';
            $counter++;
        }

        $password = '12345';
        $patient_id = rand(10000, 1000000);
        $add_date = date('m/d/y');
        $registration_time = time();

        $data_p = array(
            'patient_id' => $patient_id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'sex' => !empty($sex) ? $sex : '',
            'address' => !empty($address) ? $address : '',
            'weight' => !empty($weight) ? $weight : '',
            'age' => $age,
            'add_date' => $add_date,
            'registration_time' => $registration_time,
            'how_added' => 'from_pos',
            'payment_confirmation' => 'Active',
            'appointment_confirmation' => 'Active',
            'appointment_creation' => 'Active',
            'meeting_schedule' => 'Active',
        );

        $dfg = 5;
        $this->ion_auth->register($name, $password, $email, $dfg);
        $user_row = $this->db->get_where('users', array('email' => $email))->row();
        $ion_user_id = !empty($user_row) ? $user_row->id : null;
        $this->patient_model->insertPatient($data_p);
        $patient_user_id = $this->db->insert_id();
        if (empty($patient_user_id)) {
            $p_row = $this->db->get_where('patient', array('email' => $email))->row();
            $patient_user_id = !empty($p_row) ? $p_row->id : null;
        }
        if (!empty($ion_user_id) && !empty($patient_user_id)) {
            $id_info = array('ion_user_id' => $ion_user_id);
            $this->patient_model->updatePatient($patient_user_id, $id_info);
            $this->hospital_model->addHospitalIdToIonUser($ion_user_id, $this->hospital_id);
        }

        $formatted_text = $name . ' (' . lang('id') . ': ' . $patient_user_id . ' - ' . lang('phone') . ': ' . $phone . ($display_age !== '' ? ' - ' . lang('age') . ': ' . $display_age . ' ' . lang('years') : '') . ')';

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Patient created successfully',
            'patient' => array(
                'id' => $patient_user_id,
                'text' => $formatted_text,
                'name' => $name,
                'phone' => $phone,
                'age' => $display_age,
                'sex' => !empty($sex) ? $sex : '',
                'weight' => !empty($weight) ? $weight : '',
                'address' => !empty($address) ? $address : ''
            )
        ));
    }

    function patientDetails()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['patient'] = $this->patient_model->getPatientById($id);
        $this->load->view('home/dashboard');
        $this->load->view('details', $data);
        $this->load->view('home/footer');
    }

    function report()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['payment'] = $this->finance_model->getPaymentById($id);
        $this->load->view('home/dashboard');
        $this->load->view('diagnostic_report_details', $data);
        $this->load->view('home/footer');
    }

    function addDiagnosticReport()
    {
        $id = $this->input->post('id');
        $invoice = $this->input->post('invoice');
        $patient = $this->input->post('patient');
        $report = $this->input->post('report');

        $date = time();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // Validating Name Field
        $this->form_validation->set_rules('invoice', 'Invoice', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Password Field

        $this->form_validation->set_rules('report', 'Report', 'trim|min_length[1]|max_length[10000]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            show_swal(lang('validation_error'), 'error', lang('error'));
            redirect('patient/report?id=' . $invoice);
        } else {


            $data = array();
            $data = array(
                'invoice' => $invoice,
                'date' => $date,
                'report' => $report
            );

            if (empty($id)) {     // Adding New department
                $this->patient_model->insertDiagnosticReport($data);
                show_swal(lang('added'), 'success', lang('added'));
            } else { // Updating department
                $this->patient_model->updateDiagnosticReport($id, $data);
                show_swal(lang('updated'), 'success', lang('updated'));
            }
            // Loading View
            redirect('patient/report?id=' . $invoice);
        }
    }

    function patientPayments()
    {
        $data['groups'] = $this->donor_model->getBloodBank();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('patient_payments', $data);
        $this->load->view('home/footer');
    }

    function caseList()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $data['patients'] = $this->patient_model->getPatient();
        $data['medical_histories'] = $this->patient_model->getMedicalHistory();
        $this->load->view('home/dashboard');
        $this->load->view('case_list', $data);
        $this->load->view('home/footer');
    }


    public function caseListQuick()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $data['patients'] = $this->patient_model->getPatient();
        $data['medical_histories'] = $this->patient_model->getMedicalHistory();
        $this->load->view('home/dashboard');
        $this->load->view('case_list', $data);
        $this->load->view('home/footer');
    }


    function documents()
    {
        $data['patients'] = $this->patient_model->getPatient();
        $data['files'] = $this->patient_model->getPatientMaterial();
        $this->load->view('home/dashboard');
        $this->load->view('documents', $data);
        $this->load->view('home/footer');
    }

    function myCaseList()
    {
        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $patient_id = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
            $data['medical_histories'] = $this->patient_model->getMedicalHistoryByPatientId($patient_id);
            $this->load->view('home/dashboard');
            $this->load->view('my_case_list', $data);
            $this->load->view('home/footer');
        }
    }

    function myDocuments()
    {
        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $patient_id = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
            $data['files'] = $this->patient_model->getPatientMaterialByPatientId($patient_id);
            $this->load->view('home/dashboard');
            $this->load->view('my_documents', $data);
            $this->load->view('home/footer');
        }
    }

    function myPrescription()
    {
        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $patient_id = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
            $data['doctors'] = $this->doctor_model->getDoctor();
            $data['prescriptions'] = $this->prescription_model->getPrescriptionByPatientId($patient_id);
            $data['settings'] = $this->settings_model->getSettings();
            $this->load->view('home/dashboard', $data);
            $this->load->view('my_prescription', $data);
            $this->load->view('home/footer');
        }
    }

    public function myPayment()
    {
        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $patient_id = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
            $data['settings'] = $this->settings_model->getSettings();
            $data['payments'] = $this->finance_model->getPaymentByPatientId($patient_id);
            $this->load->view('home/dashboard');
            $this->load->view('my_payment', $data);
            $this->load->view('home/footer');
        }
    }

    function myPaymentHistory()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }


        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $patient = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
        }
        $data['settings'] = $this->settings_model->getSettings();
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        if (!empty($date_from)) {
            $data['payments'] = $this->finance_model->getPaymentByPatientIdByDate($patient, $date_from, $date_to);
            $data['deposits'] = $this->finance_model->getDepositByPatientIdByDate($patient, $date_from, $date_to);
            $data['gateway'] = $this->finance_model->getGatewayByName($data['settings']->payment_gateway);
        } else {
            $data['payments'] = $this->finance_model->getPaymentByPatientId($patient);
            $data['pharmacy_payments'] = $this->pharmacy_model->getPaymentByPatientId($patient);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByPatientId($patient);
            $data['deposits'] = $this->finance_model->getDepositByPatientId($patient);
            $data['gateway'] = $this->finance_model->getGatewayByName($data['settings']->payment_gateway);
        }



        $data['patient'] = $this->patient_model->getPatientByid($patient);
        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard');
        $this->load->view('my_payments_history', $data);
        $this->load->view('home/footer');
    }

    function deposit()
    {
        $id = $this->input->post('id');

        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $patient = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
        } else {
            show_swal(lang('undefined_patient_id'), 'error', lang('error'));
            redirect('patient/myPaymentsHistory');
        }



        $payment_id = $this->input->post('payment_id');
        $date = time();

        $deposited_amount = $this->input->post('deposited_amount');

        $deposit_type = $this->input->post('deposit_type');

        if ($deposit_type != 'Card') {
            show_swal(lang('undefined_payment_type'), 'error', lang('error'));
            redirect('patient/myPaymentsHistory');
        }

        $user = $this->ion_auth->get_user_id();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Patient Name Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|min_length[1]|max_length[100]|xss_clean');
        // Validating Deposited Amount Field
        $this->form_validation->set_rules('deposited_amount', 'Deposited Amount', 'trim|min_length[1]|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            redirect('patient/myPaymentsHistory');
        } else {
            $data = array();
            $data = array(
                'patient' => $patient,
                'payment_id' => $payment_id,
                'deposited_amount' => $deposited_amount,
                'deposit_type' => $deposit_type,
                'user' => $user
            );
            if ($payment_details->payment_from == 'admitted_patient_bed_medicine') {
                $data['payment_from'] = 'admitted_patient_bed_medicine';
            } elseif ($payment_details->payment_from == 'admitted_patient_bed_medicine') {
                $data['payment_from'] = 'admitted_patient_bed_medicine';
            } elseif ($payment_details->payment_from == 'payment') {
                $data['payment_from'] = 'payment';
            }
            if (empty($id)) {
                $data['date'] = $date;
            }
            $patient_details = $this->patient_model->getPatientById($patient);
            if (empty($id)) {
                $data_logs = array(
                    'date_time' => date('d-m-Y H:i'),
                    'patientname' => $patient_details->name,
                    'invoice_id' => $payment_id,
                    'action' => 'Added/deposited',
                    'deposit_type' => $deposit_type,
                    'amount' => $deposited_amount,
                    'user' => $this->ion_auth->get_user_id()


                );
                if ($deposit_type == 'Card') {
                    $payment_details = $this->finance_model->getPaymentById($payment_id);
                    $gateway = $this->settings_model->getSettings()->payment_gateway;
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
                        $this->logs_model->insertTransactionLogs($data_logs);
                        $this->paypal->paymentPaypal($all_details);
                    } elseif ($gateway == 'Paystack') {
                        $this->logs_model->insertTransactionLogs($data_logs);
                        $ref = date('Y') . '-' . rand() . date('d') . '-' . date('m');
                        $amount_in_kobo = $deposited_amount;
                        $this->load->module('paystack');
                        $this->paystack->paystack_standard($amount_in_kobo, $ref, $patient, $payment_id, $user, '2');
                    } elseif ($gateway == 'Stripe') {
                        $card_number = $this->input->post('card_number');
                        $expire_date = $this->input->post('expire_date');
                        $cvv = $this->input->post('cvv_number');
                        $token = $this->input->post('token');

                        $stripe = $this->pgateway_model->getPaymentGatewaySettingsByName('Stripe');
                        \Stripe\Stripe::setApiKey($stripe->secret);
                        $charge = \Stripe\Charge::create(array(
                            "amount" => $deposited_amount * 100,
                            "currency" => "usd",
                            "source" => $token
                        ));
                        $chargeJson = $charge->jsonSerialize();
                        if ($chargeJson['status'] == 'succeeded') {
                            $data1 = array(
                                'date' => $date,
                                'patient' => $patient,
                                'payment_id' => $payment_id,
                                'deposited_amount' => $amount_received,
                                'gateway' => 'Stripe',
                                'user' => $user,
                                'hospital_id' => $this->session->userdata('hospital_id')
                            );
                            $this->logs_model->insertTransactionLogs($data_logs);
                            $this->finance_model->insertDeposit($data1);
                            show_swal(lang('amount_deposited_successfully'), 'success', lang('added'));
                        } else {
                            show_swal(lang('transaction_failed'), 'error', lang('error'));
                        }
                        //  redirect("finance/invoice?id=" . "$inserted_id");
                        redirect('patient/myPaymentHistory');
                    } elseif ($gateway == 'Pay U Money') {
                        $this->logs_model->insertTransactionLogs($data_logs);
                        redirect("payu/check?deposited_amount=" . "$deposited_amount" . '&payment_id=' . $payment_id);
                    } else {
                        show_swal(lang('payment_failed_no_gateway_selected'), 'warning', lang('warning'));
                        redirect('patient/myPaymentHistory');
                    }
                } else {
                    $this->logs_model->insertTransactionLogs($data_logs);
                    $this->finance_model->insertDeposit($data);
                    show_swal(lang('amount_deposited_successfully'), 'success', lang('added'));
                }
            } else {
                $data_logs = array(
                    'date_time' => date('d-m-Y H:i'),
                    'patientname' => $patient_details->name,
                    'invoice_id' => $payment_id,
                    'action' => 'Updated/deposited',
                    'deposit_type' => $deposit_type,
                    'amount' => $deposited_amount,
                    'user' => $this->ion_auth->get_user_id()


                );
                $this->finance_model->updateDeposit($id, $data);
                $this->logs_model->insertTransactionLogs($data_logs);

                $amount_received_id = $this->finance_model->getDepositById($id)->amount_received_id;
                if (!empty($amount_received_id)) {
                    $amount_received_payment_id = explode('.', $amount_received_id);
                    $payment_id = $amount_received_payment_id[0];
                    $data_amount_received = array('amount_received' => $deposited_amount);
                    $this->finance_model->updatePayment($amount_received_payment_id[0], $data_amount_received);
                }

                show_swal(lang('amount_updated_successfully'), 'success', lang('updated'));
            }
            redirect('patient/myPaymentHistory');
        }
    }

    function myInvoice()
    {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['payment'] = $this->finance_model->getPaymentById($id);
        $this->load->view('home/dashboard');
        $this->load->view('myInvoice', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function addMedicalHistory()
    {
        $id = $this->input->post('id');
        $patient_id = $this->input->post('patient_id');
$doctor_id = $this->input->post('doctor_id');
        $date = $this->input->post('date');
        $redirect_tab = $this->input->post('redirect_tab');
        $title = $this->input->post('title');

        if (!empty($date)) {
            $date = strtotime($date);
        } else {
            $date = time();
        }
        $diagnosis_ids = $this->input->post('diagnosis_id');
        $treatment_ids = $this->input->post('treatment_id');
        $symptom_ids = $this->input->post('symptom_id');
        $test_ids = $this->input->post('test_id');
        $advice_ids = $this->input->post('advice_id');
        $description = $this->input->post('description');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $redirect = $this->input->post('redirect');
        if (empty($redirect)) {
            $redirect = 'patient/medicalHistory?id=' . $patient_id . "&redirect_tab=" . $redirect_tab;
        }

        // Validating Name Field
        $this->form_validation->set_rules('date', 'Date', 'trim|min_length[1]|max_length[100]|xss_clean');

        // Validating Title Field
        $this->form_validation->set_rules('title', 'Title', 'trim|min_length[1]|max_length[100]|xss_clean');

        // Validating Password Field

        // $this->form_validation->set_rules('description', 'Description', 'trim|min_length[5]|max_length[10000]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect("patient/editMedicalHistory?id=$id");
            } else {
                $this->load->view('home/dashboard');
                $this->load->view('add_new');
                $this->load->view('home/footer');
            }
        } else {

            if (!empty($patient_id)) {
                $patient_details = $this->patient_model->getPatientById($patient_id);
                $patient_name = $patient_details->name;
                $patient_phone = $patient_details->phone;
                $patient_address = $patient_details->address;
            } else {
                $patient_name = 0;
                $patient_phone = 0;
                $patient_address = 0;
            }

            $diagnosis_ids_str = is_array($diagnosis_ids) ? implode(',', $diagnosis_ids) : '';
            $treatment_ids_str = is_array($treatment_ids) ? implode(',', $treatment_ids) : '';
            $symptom_ids_str = is_array($symptom_ids) ? implode(',', $symptom_ids) : '';
            $test_ids_str = is_array($test_ids) ? implode(',', $test_ids) : '';

            // $diagnosis_ids_str = implode(',', $diagnosis_ids);
            // $treatment_ids_str = implode(',', $treatment_ids);
            // $symptom_ids_str = implode(',', $symptom_ids);
            // $test_ids_str = implode(',', $test_ids);
            $advice_ids_str = is_array($advice_ids) ? implode(',', $advice_ids) : '';
            $data = array();
            $data = array(
                'patient_id' => $patient_id,
                'doctor_id' => $doctor_id,
                'date' => $date,
                'title' => $title,
                'description' => $description,
                'patient_name' => $patient_name,
                'patient_phone' => $patient_phone,
                'patient_address' => $patient_address,
                'diagnosis_id' => $diagnosis_ids_str,
                'treatment_id' => $treatment_ids_str,
                'symptom_id' => $symptom_ids_str,
                'test_id' => $test_ids_str,
                'advice_id' => $advice_ids_str
            );

            if (empty($id)) {     // Adding New department
                $this->patient_model->insertMedicalHistory($data);
                $inserted_id = $this->db->insert_id();
                show_swal(lang('case_history_added_successfully'), 'success', lang('added'));
            } else { // Updating department
                $this->patient_model->updateMedicalHistory($id, $data);
                show_swal(lang('case_history_updated_successfully'), 'success', lang('updated'));
            }

            if ($redirect == 'patientcase') {
                redirect("patient/patientCaseList?id=" . $patient_id . "&case_id=" . $inserted_id);
            } else {
                redirect($redirect);
            }
        }
    }

    public function diagnosticReport()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if ($this->ion_auth->in_group(array('Patient'))) {
            $current_user = $this->ion_auth->get_user_id();
            $patient_user_id = $this->patient_model->getPatientByIonUserId($current_user)->id;
            $data['payments'] = $this->finance_model->getPaymentByPatientId($patient_user_id);
        } else {
            $data['payments'] = $this->finance_model->getPayment();
        }

        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('diagnostic_report', $data);
        $this->load->view('home/footer');
    }

    function medicalHistory()
    {
        $data = array();
        $id = $this->input->get('id');
$case_id = $this->input->get('case_id');
        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $id = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
        }


        $patient_hospital_id = $this->patient_model->getPatientById($id)->hospital_id;
        if ($patient_hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $data['redirect_tab'] = $this->input->get('redirect_tab');
        $data['patient'] = $this->patient_model->getPatientById($id);
        $data['appointments'] = $this->appointment_model->getAppointmentByPatient($data['patient']->id);
        $data['patients'] = $this->patient_model->getPatient();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['prescriptions'] = $this->prescription_model->getPrescriptionByPatientId($id);
        $data['labs'] = $this->lab_model->getLabByPatientId($id);
        $data['beds'] = $this->bed_model->getBedAllotmentsByPatientId($id);
        $data['medical_histories'] = $this->patient_model->getMedicalHistoryByPatientId($id);
        $data['patient_materials'] = $this->patient_model->getPatientMaterialByPatientId($id);
        $data['folders'] = $this->patient_model->getFolderByPatientId($id);
        $data['vital_signs'] = $this->patient_model->getVitalSignByPatientId($id);
        $data['odontogram'] = $this->patient_model->getOdontogram($id);
        $data['settings'] = $this->settings_model->getSettings();
        $data['groups'] = $this->donor_model->getBloodBank();
        
        $data['case'] = $this->patient_model->getMedicalHistoryById($case_id);
        $data['symptoms'] = $this->symptom_model->getSymptom();
        $data['tests'] = $this->finance_model->getTest();
        $data['diagnosiss'] = $this->diagnosis_model->getDiagnosis();
        $data['treatments'] = $this->treatment_model->getTreatment();
        $data['advices'] = $this->advice_model->getAdvice();

        foreach ($data['appointments'] as $appointment) {
            $doctor_details = $this->doctor_model->getDoctorById($appointment->doctor);
            $doctor_name = !empty($doctor_details) ? $doctor_details->name : '';

            if ($this->settings->time_format == '24') {
                $appointment->s_time = $this->settings_model->convert_to_24h($appointment->s_time);
                $appointment->e_time = $this->settings_model->convert_to_24h($appointment->e_time);
            }

            if (in_array('appointment', $this->modules)) {
                $timeline[$appointment->date + 1] = '<div class="card-body profile-activity">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="badge badge-primary px-3 py-2">' . lang('appointment') . '</h5>
                        <h5 class="text-muted">' . date('d M Y', $appointment->date) . '</h5>
                    </div>
                    <div class="activity bg-light rounded p-3">
                        <div class="activity-content bg-white rounded shadow-sm p-4">
                            <div class="mb-3">
                                <i class="fa fa-calendar text-primary mr-2"></i>
                                <span class="font-weight-bold">' . date('d M Y', $appointment->date) . '</span>
                            </div>
                            <div class="mb-3">
                                <i class="fa fa-user-md text-primary mr-2"></i>
                                <span class="font-weight-bold">' . $doctor_name . '</span>
                            </div>
                            <div>
                                <i class="fa fa-clock-o text-primary mr-2"></i>
                                <span>' . $appointment->s_time . ' - ' . $appointment->e_time . '</span>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }

        foreach ($data['prescriptions'] as $prescription) {
            $doctor_details = $this->doctor_model->getDoctorById($prescription->doctor);
            $doctor_name = !empty($doctor_details) ? $doctor_details->name : '';

            if (in_array('prescription', $this->modules)) {
                $timeline[$prescription->date + 2] = '<div class="card-body profile-activity">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="badge badge-info px-3 py-2">' . lang('prescription') . '</h5>
                        <h5 class="text-muted">' . date('d M Y', $prescription->date) . '</h5>
                    </div>
                    <div class="activity bg-light rounded p-3">
                        <div class="activity-content bg-white rounded shadow-sm p-4">
                            <div class="mb-3">
                                <i class="fa fa-calendar text-info mr-2"></i>
                                <span class="font-weight-bold">' . date('d M Y', $prescription->date) . '</span>
                            </div>
                            <div class="mb-3">
                                <i class="fa fa-user-md text-info mr-2"></i>
                                <span class="font-weight-bold">' . $doctor_name . '</span>
                            </div>
                            <a href="prescription/viewPrescription?id=' . $prescription->id . '" class="btn btn-info btn-sm">
                                <i class="fa fa-eye mr-1"></i>' . lang('view') . '
                            </a>
                        </div>
                    </div>
                </div>';
            }
        }

        foreach ($data['labs'] as $lab) {
            $doctor_details = $this->doctor_model->getDoctorById($lab->doctor);
            $lab_doctor = !empty($doctor_details) ? $doctor_details->name : '';

            if (in_array('lab', $this->modules)) {
                $timeline[$lab->date + 3] = '<div class="card-body profile-activity">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="badge badge-warning px-3 py-2">' . lang('lab') . '</h5>
                        <h5 class="text-muted">' . date('d M Y', $lab->date) . '</h5>
                    </div>
                    <div class="activity bg-light rounded p-3">
                        <div class="activity-content bg-white rounded shadow-sm p-4">
                            <div class="mb-3">
                                <i class="fa fa-calendar text-warning mr-2"></i>
                                <span class="font-weight-bold">' . date('d M Y', $lab->date) . '</span>
                            </div>
                            <div class="mb-3">
                                <i class="fa fa-user-md text-warning mr-2"></i>
                                <span class="font-weight-bold">' . $lab_doctor . '</span>
                            </div>
                            <a href="lab/invoice?id=' . $lab->id . '" class="btn btn-warning btn-sm text-white">
                                <i class="fa fa-file-text mr-1"></i>' . lang('report') . '
                            </a>
                        </div>
                    </div>
                </div>';
            }
        }

        foreach ($data['medical_histories'] as $medical_history) {
            $timeline[$medical_history->date + 4] = '<div class="card-body profile-activity">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="badge badge-success px-3 py-2">' . lang('case_history') . '</h5>
                    <h5 class="text-muted">' . date('d M Y', $medical_history->date) . '</h5>
                </div>
                <div class="activity bg-light rounded p-3">
                    <div class="activity-content bg-white rounded shadow-sm p-4">
                        <div class="mb-3">
                            <i class="fa fa-calendar text-success mr-2"></i>
                            <span class="font-weight-bold">' . date('d M Y', $medical_history->date) . '</span>
                        </div>
                        <div>
                            <i class="fa fa-file-text text-success mr-2"></i>
                            <p class="mb-0">' . $medical_history->description . '</p>
                        </div>
                    </div>
                </div>
            </div>';
        }

        foreach ($data['patient_materials'] as $patient_material) {
            $timeline[$patient_material->date + 5] = '<div class="card-body profile-activity">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="badge badge-secondary px-3 py-2">' . lang('documents') . '</h5>
                    <h5 class="text-muted">' . date('d M Y', $patient_material->date) . '</h5>
                </div>
                <div class="activity bg-light rounded p-3">
                    <div class="activity-content bg-white rounded shadow-sm p-4">
                        <div class="mb-3">
                            <i class="fa fa-calendar text-secondary mr-2"></i>
                            <span class="font-weight-bold">' . date('d M Y', $patient_material->date) . '</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">' . $patient_material->title . '</h5>
                            <a href="' . $patient_material->url . '" download class="btn btn-secondary btn-sm">
                                <i class="fa fa-download mr-1"></i>' . lang('download') . '
                            </a>
                        </div>
                    </div>
                </div>
            </div>';
        }

        if (!empty($timeline)) {
            $data['timeline'] = $timeline;
        }
        $this->load->view('home/dashboard');
        $this->load->view('medical_history', $data);
        $this->load->view('home/footer');
    }

    function editMedicalHistoryByJason()
    {
        $id = $this->input->get('id');
        $data['medical_history'] = $this->patient_model->getMedicalHistoryById($id);
        $data['patient'] = $this->patient_model->getPatientById($data['medical_history']->patient_id);
$data['doctor'] = $this->doctor_model->getDoctorById($data['medical_history']->doctor_id);
        $diagnosis_ids = explode(',', $data['medical_history']->diagnosis_id);
        if (!empty($diagnosis_ids)) {
            $this->db->where_in('id', $diagnosis_ids);
            $query = $this->db->get('diagnosis'); // Assuming 'diagnoses' is your diagnosis table
            $diagnosis_names = $query->result_array();
            $diagnosis = array();
            foreach ($diagnosis_names as $diagnosis_name) {
                $diagnosis[] = array(
                    "id" => $diagnosis_name['id'],
                    "text" => $diagnosis_name['name'] . ' (' . lang('id') . ': ' . $diagnosis_name['id'] . '- ' . lang('code') . ': ' . $diagnosis_name['code'] . ')'
                );
            }
            $data['diagnosis'] = $diagnosis;
        } else {
            $data['diagnosis'] = [];
        }


        $treatment_ids = explode(',', $data['medical_history']->treatment_id);
        if (!empty($treatment_ids)) {
            $this->db->where_in('id', $treatment_ids);
            $query = $this->db->get('treatment'); // Assuming 'diagnoses' is your diagnosis table
            $treatment_names = $query->result_array();
            $treatment = array();
            foreach ($treatment_names as $treatment_name) {
                $treatment[] = array(
                    "id" => $treatment_name['id'],
                    "text" => $treatment_name['name'] . ' (' . lang('id') . ': ' . $treatment_name['id'] . '- ' . lang('code') . ': ' . $treatment_name['code'] . ')'
                );
            }

            $data['treatment'] = $treatment;
        } else {
            $data['treatment'] = [];
        }


        $symptom_ids = explode(',', $data['medical_history']->symptom_id);
        if (!empty($symptom_ids)) {
            $this->db->where_in('id', $symptom_ids);
            $query = $this->db->get('symptom'); // Assuming 'diagnoses' is your diagnosis table
            $symptom_names = $query->result_array();
            $symptom = array();
            foreach ($symptom_names as $symptom_name) {
                $symptom[] = array(
                    "id" => $symptom_name['id'],
                    "text" => $symptom_name['name']
                );
            }

            $data['symptom'] = $symptom;
        } else {
            $data['symptom'] = [];
        }

        $test_ids = explode(',', $data['medical_history']->test_id);
        if (!empty($test_ids)) {
            $this->db->where_in('id', $test_ids);
            $query = $this->db->get('payment_category'); // Assuming 'diagnoses' is your diagnosis table
            $test_names = $query->result_array();
            $test = array();
            foreach ($test_names as $test_name) {
                $test[] = array(
                    "id" => $test_name['id'],
                    "text" => $test_name['category']
                );
            }

            $data['test'] = $test;
        } else {
            $data['test'] = [];
        }


        $advice_ids = explode(',', $data['medical_history']->advice_id);
        if (!empty($advice_ids)) {
            $this->db->where_in('id', $advice_ids);
            $query = $this->db->get('advice'); // Assuming 'diagnoses' is your diagnosis table
            $advice_names = $query->result_array();
            $advice = array();
            foreach ($advice_names as $advice_name) {
                $advice[] = array(
                    "id" => $advice_name['id'],
                    "text" => $advice_name['name']
                );
            }

            $data['advice'] = $advice;
        } else {
            $data['advice'] = [];
        }

        echo json_encode($data);
    }

    function getCaseDetailsByJason()
    {
        $id = $this->input->get('id');

        $data['case'] = $this->patient_model->getMedicalHistoryById($id);
        $patient = $data['case']->patient_id;
        $data['patient'] = $this->patient_model->getPatientById($patient);
$data['doctor'] = $this->doctor_model->getDoctorById($data['case']->doctor_id);
        $diagnosis_ids = explode(',', $data['case']->diagnosis_id);
        if (!empty($diagnosis_ids)) {
            $this->db->where_in('id', $diagnosis_ids);
            $query = $this->db->get('diagnosis'); // Assuming 'diagnoses' is your diagnosis table
            $diagnosis_names = $query->result_array();
            $diagnosis = array();
            foreach ($diagnosis_names as $diagnosis_name) {
                $diagnosis[] = $diagnosis_name['name'];
            }
            $data['diagnosis'] = $diagnosis;
        } else {
            $data['diagnosis'] = [];
        }


        $treatment_ids = explode(',', $data['case']->treatment_id);
        if (!empty($treatment_ids)) {
            $this->db->where_in('id', $treatment_ids);
            $query = $this->db->get('treatment'); // Assuming 'diagnoses' is your diagnosis table
            $treatment_names = $query->result_array();
            $treatment = array();
            foreach ($treatment_names as $treatment_name) {
                $treatment[] = $treatment_name['name'];
            }
            $data['treatment'] = $treatment;
        } else {
            $data['treatment'] = [];
        }

        $symptom_ids = explode(',', $data['case']->symptom_id);
        if (!empty($symptom_ids)) {
            $this->db->where_in('id', $symptom_ids);
            $query = $this->db->get('symptom'); // Assuming 'diagnoses' is your diagnosis table
            $symptom_names = $query->result_array();
            $symptom = array();
            foreach ($symptom_names as $symptom_name) {
                $symptom[] = $symptom_name['name'];
            }
            $data['symptom'] = $symptom;
        } else {
            $data['symptom'] = [];
        }

        $test_ids = explode(',', $data['case']->test_id);
        if (!empty($test_ids)) {
            $this->db->where_in('id', $test_ids);
            $query = $this->db->get('payment_category'); // Assuming 'diagnoses' is your diagnosis table
            $test_names = $query->result_array();
            $test = array();
            foreach ($test_names as $test_name) {
                $test[] = $test_name['category'];
            }
            $data['test'] = $test;
        } else {
            $data['test'] = [];
        }

        $advice_ids = explode(',', $data['case']->advice_id);
        if (!empty($advice_ids)) {
            $this->db->where_in('id', $advice_ids);
            $query = $this->db->get('advice'); // Assuming 'diagnoses' is your diagnosis table
            $advice_names = $query->result_array();
            $advice = array();
            foreach ($advice_names as $advice_name) {
                $advice[] = $advice_name['name'];
            }
            $data['advice'] = $advice;
        } else {
            $data['advice'] = [];
        }

        // Return the data as JSON
        echo json_encode($data);
    }


    function getPatientByAppointmentByDctorId($doctor_id)
    {
        $data = array();
        $appointments = $this->appointment_model->getAppointmentByDoctor($doctor_id);
        foreach ($appointments as $appointment) {
            $patient_exists = $this->patient_model->getPatientById($appointment->patient);
            if (!empty($patient_exists)) {
                $patients[] = $appointment->patient;
            }
        }

        if (!empty($patients)) {
            $patients = array_unique($patients);
        } else {
            $patients = '';
        }

        return $patients;
    }

    function patientMaterial()
    {
        $data = array();
        $id = $this->input->get('patient');
        $data['settings'] = $this->settings_model->getSettings();
        $data['patient'] = $this->patient_model->getPatientById($id);
        $data['patient_materials'] = $this->patient_model->getPatientMaterialByPatientId($id);
        $this->load->view('home/dashboard', $data);
        $this->load->view('patient_material', $data);
        $this->load->view('home/footer');
    }

    function addPatientMaterial()
    {
        $title = $this->input->post('title');
        $patient_id = $this->input->post('patient');
        $img_url = $this->input->post('img_url');
        $folder = $this->input->post('folder');
        $type = $this->input->post('type');
        $date = time();
        $redirect = $this->input->post('redirect');
        $redirect_tab = $this->input->post('redirect_tab');
        if ($this->ion_auth->in_group(array('Patient'))) {
            if (empty($patient_id)) {
                $current_patient = $this->ion_auth->get_user_id();
                $patient_id = $this->patient_model->getPatientByIonUserId($current_patient)->id;
            }
        }


        if (empty($redirect)) {
            $redirect = "patient/medicalHistory?id=" . $patient_id . "&redirect_tab=" . $redirect_tab;
        }
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // Validating Patient Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|min_length[1]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            show_swal(lang('validation_error'), 'error', lang('error'));
            redirect($redirect);
        } else {

            if (!empty($patient_id)) {
                $patient_details = $this->patient_model->getPatientById($patient_id);
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
                'upload_path' => "./uploads/documents/",
                'allowed_types' => "gif|jpg|png|jpeg|pdf|docx|doc|odt",
                'overwrite' => False,
                'max_size' => "48000000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                'max_height' => "10000",
                'max_width' => "10000"
            );

            $this->load->library('Upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('img_url')) {
                $path = $this->upload->data();
                $img_url = "uploads/documents/" . $path['file_name'];
                $data = array();
                $data = array(
                    'date' => $date,
                    'type' => $type,
                    'folder' => $folder,
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
                show_swal(lang('upload_error'), 'warning', lang('warning'));
            }

            $this->patient_model->insertPatientMaterial($data);
            show_swal(lang('documents_uploaded_successfully'), 'success', lang('added'));

            if ($type == 'doc') {
                redirect("patient/medicalHistoryByFolder?id=" . $folder);
            } else {
                redirect($redirect);
            }
        }
    }

    function deleteCaseHistory()
    {
        $id = $this->input->get('id');
        $redirect = $this->input->get('redirect');
        $redirect_tab = 'case';
        $case_history = $this->patient_model->getMedicalHistoryById($id);
        $this->patient_model->deleteMedicalHistory($id);
        show_swal(lang('case_history_deleted'), 'warning', lang('deleted'));
        if ($redirect == 'case') {
            redirect('patient/caseList');
        } elseif ($redirect == 'patient') {
            redirect("patient/patientCaseList?id=" . $case_history->patient_id);
        } else {
            redirect("patient/MedicalHistory?id=" . $case_history->patient_id .  "&redirect_tab=" . $redirect_tab);
        }
    }

    function deletePatientMaterial()
    {
        $id = $this->input->get('id');
        $redirect = $this->input->get('redirect');
        $redirect_tab = 'files';
        $patient_material = $this->patient_model->getPatientMaterialById($id);
        $path = $patient_material->url;
        if (!empty($path)) {
            unlink($path);
        }
        $this->patient_model->deletePatientMaterial($id);
        show_swal(lang('documments_deleted'), 'warning', lang('deleted'));
        if ($redirect == 'documents') {
            redirect('patient/documents');
        } else {
            redirect("patient/MedicalHistory?id=" . $patient_material->patient . "&redirect_tab=" . $redirect_tab);
        }
    }

    function delete()
    {
        $data = array();
        $id = $this->input->get('id');

        $patient_hospital_id = $this->patient_model->getPatientById($id)->hospital_id;
        if ($patient_hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $user_data = $this->db->get_where('patient', array('id' => $id))->row();
        $path = $user_data->img_url;

        if (!empty($path)) {
            unlink($path);
        }
        $ion_user_id = $user_data->ion_user_id;
        $this->db->where('id', $ion_user_id);
        $this->db->delete('users');
        $this->patient_model->delete($id);
        show_swal(lang('patient_deleted'), 'warning', lang('deleted'));
        redirect('patient');
    }

    function getPatient()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "category",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['patients'] = $this->patient_model->getPatientBysearch($search, $order, $dir);
            } else {
                $data['patients'] = $this->patient_model->getPatientWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['patients'] = $this->patient_model->getPatientByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['patients'] = $this->patient_model->getPatientByLimit($limit, $start, $order, $dir);
            }
        }

        $i = 0;

        foreach ($data['patients'] as $patient) {
            $i = $i + 1;

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {
                $options1 = '<a type="button" class="btn btn-outline-primary editbutton btn-sm mb-1" title="' . lang('edit') . '" data-toggle="modal" data-id="' . $patient->id . '"><i class="fa fa-edit"></i> ' . lang('') . '</a>';
            }

            $options2 = '<a class="btn btn-outline-info detailsbutton btn-sm mb-1" title="' . lang('info') . '" href="patient/patientDetails?id=' . $patient->id . '"><i class="fa fa-info"></i> ' . lang('') . '</a>';

            $options3 = '<a class="btn btn-outline-info btn-sm mb-1" title="' . lang('details') . '" href="patient/medicalHistory?id=' . $patient->id . '"><i class="fa fa-stethoscope"></i> ' . lang('details') . '</a>';

            $options4 = '<a class="btn btn-outline-secondary btn-sm mb-1" title="' . lang('payment') . '" href="finance/patientPaymentHistory?patient=' . $patient->id . '"><i class="fa fa-money-bill-alt"></i> ' . lang('payment') . '</a>';

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {
                $options5 = '<a class="btn btn-outline-danger delete_button btn-sm mb-1" title="' . lang('delete') . '" href="patient/delete?id=' . $patient->id . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"></i> ' . lang('') . '</a>';
            }

            $options6 = '<a type="button" class="btn btn-outline-warning detailsbutton inffo btn-sm mb-1" title="' . lang('info') . '" data-toggle="modal" data-id="' . $patient->id . '"><i class="fa fa-info"></i> ' . lang('') . '</a>';

            if ($this->ion_auth->in_group('Doctor')) {
                $options7 = '<a class="btn btn-outline-success detailsbutton btn-sm mb-1" title="' . lang('instant_meeting') . '" href="meeting/instantLive?id=' . $patient->id . '" onclick="return confirm(\'Are you sure you want to start a live meeting with this patient? SMS and Email will be sent to the Patient.\');"><i class="fa fa-headphones"></i> ' . lang('start_live') . '</a>';
            } else {
                $options7 = '';
            }

            $cases = '<a class="btn btn-outline-success btn-sm mb-1" title="' . lang('cases') . '" href="patient/patientCaseList?id=' . $patient->id . '"><i class="fa fa-eye"></i> ' . lang('cases') . '</a>';

            if ($this->ion_auth->in_group(array('admin'))) {
                $info[] = array(
                    '<span class="badge badge-secondary">#' . $patient->id . '</span>',
                    '<strong>' . $patient->name . '</strong>',
                    '<span class="badge badge-info">' . $patient->phone . '</span>',
                    '<span class="badge badge-success">' . $this->settings_model->getSettings()->currency . $this->patient_model->getDueBalanceByPatientId($patient->id) . '</span>',
                    $options1 . ' ' . $options6 . '  ' . $options3 . ' ' . $options4 . ' ' . $options5
                );
            }

            if ($this->ion_auth->in_group(array('Receptionist'))) {
                $info[] = array(
                    '<span class="badge badge-secondary">#' . $patient->id . '</span>',
                    '<strong>' . $patient->name . '</strong>',
                    '<span class="badge badge-info">' . $patient->phone . '</span>',
                    '<span class="badge badge-success">' . $this->settings_model->getSettings()->currency . $this->patient_model->getDueBalanceByPatientId($patient->id) . '</span>',
                    $options1 . ' ' . $options6 . '  ' . $options3 . ' ' . $options4
                );
            }

            if ($this->ion_auth->in_group(array('Accountant'))) {
                $info[] = array(
                    '<span class="badge badge-secondary">#' . $patient->id . '</span>',
                    '<strong>' . $patient->name . '</strong>',
                    '<span class="badge badge-info">' . $patient->phone . '</span>',
                    '<span class="badge badge-success">' . $this->settings_model->getSettings()->currency . $this->patient_model->getDueBalanceByPatientId($patient->id) . '</span>',
                    $options1 . ' ' . $options6 . ' ' . $options4
                );
            }

            if ($this->ion_auth->in_group(array('Laboratorist', 'Nurse', 'Doctor'))) {
                $info[] = array(
                    '<span class="badge badge-secondary">#' . $patient->id . '</span>',
                    '<strong>' . $patient->name . '</strong>',
                    '<span class="badge badge-info">' . $patient->phone . '</span>',
                    $options1 . ' ' . $options6 . '  ' . $options3
                );
            }
        }

        if (!empty($data['patients'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->patient_model->getPatient()),
                "recordsFiltered" => count($this->patient_model->getPatient()),
                "data" => $info
            );
        } else {
            $output = array(
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    function getPatientPayments()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "phone",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['patients'] = $this->patient_model->getPatientBysearch($search, $order, $dir);
            } else {
                $data['patients'] = $this->patient_model->getPatientWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['patients'] = $this->patient_model->getPatientByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['patients'] = $this->patient_model->getPatientByLimit($limit, $start, $order, $dir);
            }
        }

        $i = 0;
        foreach ($data['patients'] as $patient) {
            $i = $i + 1;

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {

                $options1 = ' <a type="button" class="btn editbutton" title="' . lang('edit') . '" data-toggle = "modal" data-id="' . $patient->id . '"><i class="fa fa-edit"> </i> ' . lang('edit') . '</a>';
            }

            $options2 = '<a class="btn detailsbutton" title="' . lang('info') . '" style="color: #fff;" href="patient/patientDetails?id=' . $patient->id . '"><i class="fa fa-info"></i> ' . lang('info') . '</a>';

            $options3 = '<a class="btn green" title="' . lang('history') . '" style="color: #fff;" href="patient/medicalHistory?id=' . $patient->id . '"><i class="fa fa-stethoscope"></i> ' . lang('history') . '</a>';

            $options4 = '<a class="btn btn-warning btn-sm" title="' . lang('payment') . ' ' . lang('history') . '" href="finance/patientPaymentHistory?patient=' . $patient->id . '"><i class="fa fa-money-bill-alt"></i> ' . lang('payment') . ' ' . lang('history') . '</a>';

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {
                $options5 = '<a class="btn delete_button" title="' . lang('delete') . '" href="patient/delete?id=' . $patient->id . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"></i> ' . lang('delete') . '</a>';
            }

            $due = $this->settings_model->getSettings()->currency . $this->patient_model->getDueBalanceByPatientId($patient->id);

            $info[] = array(
                $patient->id,
                $patient->name,
                $patient->phone,
                $due,
                $options4
            );
        }

        if (!empty($data['patients'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->patient_model->getPatient()),
                "recordsFiltered" => count($this->patient_model->getPatient()),
                "data" => $info
            );
        } else {
            $output = array(
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    function getCaseList()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['cases'] = $this->patient_model->getMedicalHistoryBySearch($search);
            } else {
                $data['cases'] = $this->patient_model->getMedicalHistory();
            }
        } else {
            if (!empty($search)) {
                $data['cases'] = $this->patient_model->getMedicalHistoryByLimitBySearch($limit, $start, $search);
            } else {
                $data['cases'] = $this->patient_model->getMedicalHistoryByLimit($limit, $start);
            }
        }

        foreach ($data['cases'] as $case) {

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {
                $options1 = ' <a type="button" class="btn btn-info editbutton btn-sm mt-1" title="' . lang('edit') . '" data-toggle="modal" data-id="' . $case->id . '"><i class="fa fa-edit"></i></a>';
            }

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {
                $options2 = '<a class="btn btn-danger delete_button btn-sm mt-1" title="' . lang('delete') . '" href="patient/deleteCaseHistory?id=' . $case->id . '&redirect=case" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"></i></a>';
                $options3 = ' <a type="button" class="btn btn-success btn-sm detailsbutton case mt-1" title="' . lang('case') . '" data-toggle="modal" data-id="' . $case->id . '"><i class="fa fa-file"></i></a>';
            }


            if ($this->ion_auth->in_group(array('admin', 'Doctor'))) {
                $options4 = ' <a style="background: #88A788; border: #88A788" type="button" class="btn btn-success btn-sm gptButton mt-1" title="' . lang('gpt_button') . '" data-toggle="modal" data-description="' . $case->description . '" data-id="' . $case->id . '"><i class="far fa-comment"></i></a>';
            }



            if (!empty($case->patient_id)) {
                $patient_info = $this->patient_model->getPatientById($case->patient_id);
                if (!empty($patient_info)) {
                    $patient_details = $patient_info->name . '</br>' . $patient_info->address . '</br>' . $patient_info->phone . '</br>';
                } else {
                    $patient_details = $case->patient_name . '</br>' . $case->patient_address . '</br>' . $case->patient_phone . '</br>';
                }
            } else {
                $patient_details = '';
            }

            $dropdownOptions = '';
            if (!empty($options1) || !empty($options3) || !empty($options4) || !empty($options5)  || !empty($options6)) {
                $dropdownOptions = '
            <div class="btn-group">
            <button type="button" class="btn btn-info btn-sm label-primary dropdown-toggle action_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
            <i class="fas fa-bars"></i> ' . lang('actions') . ' <span class="caret"></span>
        </button>
                <ul class="dropdown-menu">
                    ' . ($options3 ? '<li><a class="detailsbutton case" title="' . lang('case') . '" data-toggle = "modal" data-id="' . $case->id . '">  <i class="fa fa-file"></i> ' . lang('details') . '</a></li>' : '') . '
                    ' . ($options1 ? '<li><a class="editbutton" title="' . lang('edit') . '" data-toggle = "modal" data-id="' . $case->id . '"> <i class="fa fa-edit"></i> ' . lang('edit') . ' </a></li>' : '') . '
                    ' . ($options2 ? '<li><a class="" title="' . lang('delete') . '" href="patient/deleteCaseHistory?id=' . $case->id . '&redirect=case" onclick="return confirm(\'Are you sure you want to delete this item?\');"> <i class="fa fa-print"></i> ' . lang('delete') . ' </a></li>' : '') . '
                </ul>
            </div>';
            }

            $info[] = array(
                date('d-m-Y', $case->date),
                $patient_details,
                $case->title,
                '<span class="d-flex gap-1">' . $options3 . ' ' . $options1 . ' ' . $options2 . ' ' . $options4 . '</span>'
                // $dropdownOptions
            );
        }

        if (!empty($data['cases'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->patient_model->getMedicalHistory()),
                "recordsFiltered" => count($this->patient_model->getMedicalHistory()),
                "data" => $info
            );
        } else {
            $output = array(
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    function getDocuments()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "date",
            "1" => "patient",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['documents'] = $this->patient_model->getDocumentBySearch($search, $order, $dir);
            } else {
                $data['documents'] = $this->patient_model->getPatientMaterialWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['documents'] = $this->patient_model->getDocumentByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['documents'] = $this->patient_model->getDocumentByLimit($limit, $start, $order, $dir);
            }
        }


        foreach ($data['documents'] as $document) {

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {

                $options1 = '<a class="btn btn-info btn-sm" href="' . $document->url . '" download> ' . lang('download') . ' </a>';
            }
            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist', 'Nurse', 'Doctor'))) {
                $options2 = '<a class="btn btn-danger btn-sm delete_button" href="patient/deletePatientMaterial?id=' . $document->id . '&redirect=documents" onclick="return confirm(\'You want to delete the item??\');"> X </a>';
            }

            if (!empty($document->patient)) {
                $patient_info = $this->patient_model->getPatientById($document->patient);
                if (!empty($patient_info)) {
                    $patient_details = $patient_info->name . '</br>' . $patient_info->address . '</br>' . $patient_info->phone . '</br>';
                } else {
                    $patient_details = $document->patient_name . '</br>' . $document->patient_address . '</br>' . $document->patient_phone . '</br>';
                }
            } else {
                $patient_details = '';
            }
            $extension_url = explode(".", $document->url);

            $length = count($extension_url);
            $extension = $extension_url[$length - 1];

            if (strtolower($extension) == 'pdf') {
                $files = '<a download class="example-image-link" href="' . $document->url . '" data-title="' . $document->title . '" target="_blank">' . '<img class="example-image" src="uploads/image/pdf.png" width="100px" height="100px"alt="image-1">' . '</a>';
            } elseif (strtolower($extension) == 'docx') {
                $files = '<a download class="example-image-link" href="' . $document->url . '" data-title="' . $document->title . '">' . '<img class="example-image" src="uploads/image/docx.png" width="100px" height="100px"alt="image-1">' . '</a>';
            } elseif (strtolower($extension) == 'doc') {
                $files = '<a download class="example-image-link" href="' . $document->url . '" data-title="' . $document->title . '">' . '<img class="example-image" src="uploads/image/doc.png" width="100px" height="100px"alt="image-1">' . '</a>';
            } elseif (strtolower($extension) == 'odt') {
                $files = '<a download class="example-image-link" href="' . $document->url . '" data-title="' . $document->title . '">' . '<img class="example-image" src="uploads/image/odt.png" width="100px" height="100px"alt="image-1">' . '</a>';
            } else {
                $files = '<a download class="example-image-link" href="' . $document->url . '" data-lightbox="example-1" data-title="' . $document->title . '">' . '<img class="example-image" src="' . $document->url . '" width="100px" height="100px"alt="image-1">' . '</a>';
            }

            $dropdownOptions = '';
            $dropdownOptions = '
            <div class="btn-group">
            <button type="button" class="btn btn-info btn-sm label-primary dropdown-toggle action_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
            <i class="fas fa-bars"></i> ' . lang('actions') . ' <span class="caret"></span>
        </button>
                <ul class="dropdown-menu">
                    ' . ($options1 ? '<li><a class="detailsbutton case" title="' . lang('dpwnload') . '"   href="' . $document->url . '" download>  <i class="fa fa-file"></i> ' . lang('download') . '</a></li>' : '') . '
                    ' . ($options2 ? '<li><a class="editbutton" title="' . lang('delete') . '" href="patient/deletePatientMaterial?id=' . $document->id . '&redirect=documents" onclick="return confirm(\'You want to delete the item??\');"> <i class="fa fa-edit"></i> ' . lang('delete') . ' </a></li>' : '') . '
                </ul>
            </div>';


            $info[] = array(
                date('d-m-y', $document->date),
                $patient_details,
                $document->title,
                $files,
                // $dropdownOptions 
                $options1 . ' ' . $options2
            );
        }

        if (!empty($data['documents'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->patient_model->getPatientMaterial()),
                "recordsFiltered" => count($this->patient_model->getPatientMaterial()),
                "data" => $info
            );
        } else {
            $output = array(
                // "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    function getMedicalHistoryByJason()
    {
        $data = array();

        $from_where = $this->input->get('from_where');
        $id = $this->input->get('id');

        if (!empty($from_where)) {
            $this->db->where('id', $id);
            $id = $this->db->get('appointment')->row()->patient;
        }


        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $id = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
        }

        $patient = $this->patient_model->getPatientById($id);
        $appointments = $this->appointment_model->getAppointmentByPatient($patient->id);
        $patients = $this->patient_model->getPatient();
        $doctors = $this->doctor_model->getDoctor();
        $data['prescriptions'] = $this->prescription_model->getPrescriptionByPatientId($id);
        $beds = $this->bed_model->getBedAllotmentsByPatientId($id);

        $labs = $this->lab_model->getLabByPatientId($id);
        $medical_histories = $this->patient_model->getMedicalHistoryByPatientId($id);
        $patient_materials = $this->patient_model->getPatientMaterialByPatientId($id);
        $vital_signs = $this->patient_model->getVitalSignByPatientId($id);
        if (!$this->ion_auth->in_group(array('Patient'))) {
            $odontogram = $this->patient_model->getOdontogram($id);
            $settings = $this->settings_model->getSettings();
            if ($settings->show_odontogram_in_history == 'yes') {
                $li_option = '<li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#odontogram">' . lang("odontogram") . '</a>
        </li>';
            } else {
                $li_option = ' ';
            }
        } else {
            $li_option = ' ';
        }
        foreach ($appointments as $appointment) {

            $doctor_details = $this->doctor_model->getDoctorById($appointment->doctor);
            if (!empty($doctor_details)) {
                $doctor_name = $doctor_details->name;
            } else {
                $doctor_name = '';
            }

            $timeline[$appointment->date + 1] = '<div class="card-body profile-activity" >
                <h5 class="float-left"><span class="label float-right r-activity">' . lang('appointment') . '</span></h5>
                                            <h5 class="float-right">' . date('d-m-Y', $appointment->date) . '</h5>
                                            <div class="activity terques">
                                                <span>
                                                    <i class="fa fa-stethoscope"></i>
                                                </span>
                                                <div class="activity-desk">
                                                    <div class="card col-md-6">
                                                        <div class="card-body">
                                                            <div class="arrow"></div>
                                                            <i class=" fa fa-calendar"></i>
                                                            <h4>' . date('d-m-Y', $appointment->date) . '</h4>
                                                            <p></p>
                                                            <i class=" fa fa-user-md"></i>
                                                                <h4>' . $doctor_name . '</h4>
                                                                    <p></p>
                                                                    <i class=" fa fa-clock-o"></i>
                                                                <p>' . $appointment->s_time . ' - ' . $appointment->e_time . '</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
        }


        foreach ($data['prescriptions'] as $prescription) {
            $doctor_details = $this->doctor_model->getDoctorById($prescription->doctor);
            if (!empty($doctor_details)) {
                $doctor_name = $doctor_details->name;
            } else {
                $doctor_name = '';
            }
            $timeline[$prescription->date + 6] = '<div class="card-body profile-activity" >
                                           <h5 class="float-left"><span class="label float-right r-activity">' . lang('prescription') . '</span></h5>
                                            <h5 class="float-right">' . date('d-m-Y', $prescription->date) . '</h5>
                                            <div class="activity purple">
                                                <span>
                                                    <i class="fa fa-medkit"></i>
                                                </span>
                                                <div class="activity-desk">
                                                    <div class="card col-md-6">
                                                        <div class="card-body">
                                                            <div class="arrow"></div>
                                                            <i class=" fa fa-calendar"></i>
                                                            <h4>' . date('d-m-Y', $prescription->date) . '</h4>
                                                            <p></p>
                                                            <i class=" fa fa-user-md"></i>
                                                                <h4>' . $doctor_name . '</h4>
                                                                    <a class="btn btn-info btn-sm detailsbutton" title="View" href="prescription/viewPrescription?id=' . $prescription->id . '"><i class="fa fa-eye"> View</i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
        }
        foreach ($labs as $lab) {

            $doctor_details = $this->doctor_model->getDoctorById($lab->doctor);
            if (!empty($doctor_details)) {
                $lab_doctor = $doctor_details->name;
            } else {
                $lab_doctor = '';
            }

            $timeline[$lab->date + 3] = '<div class="card-body profile-activity" >
                                            <h5 class="float-left"><span class="label float-right r-activity">' . lang('lab') . '</span></h5>
                                            <h5 class="float-right">' . date('d-m-Y', $lab->date) . '</h5>
                                            <div class="activity blue">
                                                <span>
                                                    <i class="fa fa-flask"></i>
                                                </span>
                                                <div class="activity-desk">
                                                    <div class="card col-md-6">
                                                        <div class="card-body">
                                                            <div class="arrow"></div>
                                                            <i class=" fa fa-calendar"></i>
                                                            <h4>' . date('d-m-Y', $lab->date) . '</h4>
                                                            <p></p>
                                                             <i class=" fa fa-user-md"></i>
                                                                <h4>' . $lab_doctor . '</h4>
                                                                    <a class="btn btn-sm" title="Lab" style="color: #fff;" href="lab/invoice?id=' . $lab->id . '"><i class="fa fa-file-text"></i>' . lang('report') . '</a>
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>';
        }

        foreach ($medical_histories as $medical_history) {
            $timeline[$medical_history->date + 4] = '<div class="card-body profile-activity" >
                                            <h5 class="float-left"><span class="label float-right r-activity">' . lang('case_history') . '</span></h5>
                                            <h5 class="float-right">' . date('d-m-Y', $medical_history->date) . '</h5>
                                            <div class="activity greenn">
                                                <span>
                                                    <i class="fa fa-file"></i>
                                                </span>
                                                <div class="activity-desk">
                                                    <div class="card col-md-6">
                                                        <div class="card-body">
                                                            <div class="arrow"></div>
                                                            <i class=" fa fa-calendar"></i>
                                                            <h4>' . date('d-m-Y', $medical_history->date) . '</h4>
                                                            <p></p>
                                                             <i class=" fa fa-note"></i> 
                                                                <p>' . $medical_history->description . '</p>
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>';
        }

        foreach ($patient_materials as $patient_material) {
            $timeline[$patient_material->date + 5] = '<div class="card-body profile-activity" >
                                           <h5 class="float-left"><span class="label float-right r-activity">' . lang('documents') . '</span></h5>
                                            <h5 class="float-right">' . date('d-m-Y', $patient_material->date) . '</h5>
                                            <div class="activity purplee">
                                                <span>
                                                    <i class="fa fa-file-o"></i>
                                                </span>
                                                <div class="activity-desk">
                                                    <div class="card col-md-6">
                                                        <div class="card-body">
                                                            <div class="arrow"></div>
                                                            <i class=" fa fa-calendar"></i>
                                                            <h4>' . date('d-m-Y', $patient_material->date) . ' <a class="float-right" title="' . lang('download') . '"  href="' . $patient_material->url . '" download=""> <i class=" fa fa-download"></i> </a> </h4>
                                                                
                                                                 <h4>' . $patient_material->title . '</h4>
                                                            
                                                                
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>';
        }





        if (!empty($timeline)) {
            krsort($timeline);
            $timeline_value = '';
            foreach ($timeline as $key => $value) {
                $timeline_value .= $value;
            }
        }

        $all_appointments = '';
        foreach ($appointments as $appointment) {

            $doctor_details = $this->doctor_model->getDoctorById($appointment->doctor);
            if (!empty($doctor_details)) {
                $appointment_doctor = $doctor_details->name;
            } else {
                $appointment_doctor = "";
            }


            $time_slot = $appointment->time_slot;
            if ($time_slot != 'Not Selected') {
                $time_slot_exploded = explode(' To ', $time_slot);
                $time_slot =  $time_slot_exploded[0] . ' ' . lang('to') . ' ' . $time_slot_exploded[1];

                $this->db->where('hospital_id', $this->hospital_id);
                $this->settings = $this->db->get('settings')->row();

                if ($this->settings->time_format == 24) {
                    $s_time = $this->settings_model->convert_to_24h($time_slot_exploded[0]);
                    $e_time = $this->settings_model->convert_to_24h($time_slot_exploded[1]);
                    $time_slot = $s_time . ' - ' . $e_time;
                }
            } else {
                $time_slot = lang('not_selected');
            }

            if (!$this->ion_auth->in_group(array('Patient'))) {
                $edit_button = '<a type="button" href="appointment/editAppointment?id=' . $appointment->id . '" class="btn btn-info btn-sm btn_width" title="Edit" data-id="' . $appointment->id . '">' . lang('edit') . '</a>';
            } else {
                $edit_button = '';
            }


            $patient_appointments = '<tr class = "">

        <td>' . date("d-m-Y", $appointment->date) . '
        </td>
        <td>' . $time_slot . '</td>
        <td>'
                . $appointment_doctor . '
        </td>
        <td>' . $appointment->status . '</td>
        <td>' . $edit_button . '</td>

        </tr>';

            $all_appointments .= $patient_appointments;
        }




        if (empty($all_appointments)) {
            $all_appointments = '';
        }



        $all_vitals = '';
        foreach ($vital_signs as $vital_sign) {





            $patient_vitals = '<tr class = "">

        <td>' . $vital_sign->add_date_time . '
        </td>
        <td>' . $vital_sign->heart_rate . '</td>
        <td>' . $vital_sign->systolic_blood_pressure . '/' . $vital_sign->diastolic_blood_pressure . '</td>
        <td>' . $vital_sign->temperature . '</td>
        <td>' . $vital_sign->oxygen_saturation . '</td>
        <td>' . $vital_sign->respiratory_rate . '</td>
        <td>' . $vital_sign->bmi_weight . '</td>
        <td>' . $vital_sign->bmi_height . '</td>
        
        

        </tr>';

            $all_vitals .= $patient_vitals;
        }




        if (empty($all_vitals)) {
            $all_vitals = '';
        }


        $all_case = '';

        foreach ($medical_histories as $medical_history) {
            $patient_case = ' <tr class="">
                                                    <td>' . date("d-m-Y", $medical_history->date) . '</td>
                                                    <td>' . $medical_history->title . '</td>
                                                    <td>' . $medical_history->description . '</td>
                                                </tr>';

            $all_case .= $patient_case;
        }


        if (empty($all_case)) {
            $all_case = '';
        }
        $all_prescription = '';

        foreach ($data['prescriptions'] as $prescription) {
            $doctor_details = $this->doctor_model->getDoctorById($prescription->doctor);
            if (!empty($doctor_details)) {
                $prescription_doctor = $doctor_details->name;
            } else {
                $prescription_doctor = '';
            }
            $medicinelist = '';
            if (!empty($prescription->medicine)) {
                $medicine = explode('###', $prescription->medicine);

                foreach ($medicine as $key => $value) {
                    $medicine_id = explode('***', $value);
                    $medicine_details = $this->medicine_model->getMedicineById($medicine_id[0]);
                    if (!empty($medicine_details)) {
                        $medicine_name_with_dosage = $medicine_details->name . ' -' . $medicine_id[1];
                        $medicine_name_with_dosage = $medicine_name_with_dosage . ' | ' . $medicine_id[3] . '<br>';
                        rtrim($medicine_name_with_dosage, ',');
                        $medicinelist .= '<p>' . $medicine_name_with_dosage . '</p>';
                    }
                }
            } else {
                $medicinelist = '';
            }

            $option1 = '<a class="btn btn-info btn-sm btn_width" href="prescription/viewPrescription?id=' . $prescription->id . '"><i class="fa fa-eye">' . lang('view') . '</i></a>';
            $prescription_case = ' <tr class="">
                                                    <td>' . date('m/d/Y', $prescription->date) . '</td>
                                                    <td>' . $prescription_doctor . '</td>
                                                    <td>' . $medicinelist . '</td>
                                                         <td>' . $option1 . '</td>
                                                </tr>';

            $all_prescription .= $prescription_case;
        }


        if (empty($all_prescription)) {
            $all_prescription = '';
        }


        $all_lab = '';

        foreach ($labs as $lab) {
            if ($lab->status == 'completed') {
                $doctor_details = $this->doctor_model->getDoctorById($lab->doctor);
                if (!empty($doctor_details)) {
                    $lab_doctor = $doctor_details->name;
                } else {
                    $lab_doctor = "";
                }
                $option1 = '<a class="btn btn-info btn-sm btn_width" href="lab/invoice?id=' . $lab->id . '"><i class="fa fa-eye">' . lang('report') . '</i></a>';
                $lab_class = ' <tr class="">
                                                    <td>' . $lab->id . '</td>
                                                    <td>' . date("m/d/Y", $lab->date) . '</td>
                                                    <td>' . $lab_doctor . '</td>
                                                         <td>' . $option1 . '</td>
                                                </tr>';

                $all_lab .= $lab_class;
            }
        }


        if (empty($all_lab)) {
            $all_lab = '';
        }
        $all_bed = '';

        foreach ($beds as $bed) {


            $bed_case = ' <tr class="">
                                                    <td>' . $bed->bed_id . '</td>
                                                    <td>' . $bed->a_time . '</td>
                                                    <td>' . $bed->d_time . '</td>
                                                         
                                                </tr>';

            $all_bed .= $bed_case;
        }


        if (empty($all_bed)) {
            $all_bed = '';
        }


        $all_material = '';
        foreach ($patient_materials as $patient_material) {

            if (!empty($patient_material->title)) {
                $patient_documents = $patient_material->title;
            }


            $patient_material = '
            
                                            <div class="card col-md-3"  style="height: 200px; margin-right: 10px; margin-bottom: 36px; background: #f1f1f1; padding: 34px;">

                                                <div class="post-info">
                                                    <img src="' . $patient_material->url . '" height="100" width="100">
                                                </div>
                                                <div class="post-info">
                                                    
                                                ' . $patient_documents . '

                                                </div>
                                                <p></p>
                                                <div class="post-info">
                                                    <a class="btn btn-info btn-sm btn_width" href="' . $patient_material->url . '" download> ' . lang("download") . ' </a>
                                                    <a class="btn btn-info btn-sm btn_width" title="' . lang("delete") . '" href="patient/deletePatientMaterial?id=' . $patient_material->id . '"onclick="return confirm("Are you sure you want to delete this item?");"> X </a>
                                                </div>

                                                <hr>

                                            </div>';
            $all_material .= $patient_material;
        }

        if (empty($all_material)) {
            $all_material = ' ';
        }


        if (!empty($patient->img_url)) {
            $profile_image = '<a href="#">
                            <img  style="height: 100px;" src="' . $patient->img_url . '" alt="">
                        </a>';
        } else {
            $profile_image = '';
        }
        if (!$this->ion_auth->in_group(array('Patient'))) {
            if ($settings->show_odontogram_in_history == 'yes') {
                $description = $odontogram->description;
                $option = '  <div id="odontogram" class="tab-pane">
      <style>
                     .tooth-chart {
                         width: 450px;
                     }

                     #Spots polygon,
                     #Spots path {
                         -webkit-transition: fill .25s;
                         transition: fill .25s;
                     }
                     #Spots polygon:hover,
                     #Spots polygon:active,
                     #Spots path:hover,
                     #Spots path:active {
                         fill: #dddddd !important;
                     }
                     .clickUl {
                         margin-top:20px;

                     }
                     .clickUl li {
                         display:inline-block;
                     }
                     .clickUl li a {
                         padding:10px;

                     }
                 </style>
         <div class="">
            <form action="patient/odontogram" method="POST">
            <input type="hidden" id="t32" name="tooth[Tooth32]" value="' . $odontogram->Tooth32 . '"></input>
                 <input type="hidden" id="t31" name="tooth[Tooth31]" value="' . $odontogram->Tooth31 . '"></input>
                 <input type="hidden" id="t30" name="tooth[Tooth30]" value="' . $odontogram->Tooth30 . '"></input>
                 <input type="hidden" id="t29" name="tooth[Tooth29]" value="' . $odontogram->Tooth29 . '"></input>
                 <input type="hidden" id="t28" name="tooth[Tooth28]" value="' . $odontogram->Tooth28 . '"></input>
                 <input type="hidden" id="t27" name="tooth[Tooth27]" value="' . $odontogram->Tooth27 . '"></input>
                 <input type="hidden" id="t26" name="tooth[Tooth26]" value="' . $odontogram->Tooth26 . '"></input>
                 <input type="hidden" id="t25" name="tooth[Tooth25]" value="' . $odontogram->Tooth25 . '"></input>
                 <input type="hidden" id="t24" name="tooth[Tooth24]" value="' . $odontogram->Tooth24 . '"></input>
                 <input type="hidden" id="t23" name="tooth[Tooth23]" value="' . $odontogram->Tooth23 . '"></input>
                 <input type="hidden" id="t22" name="tooth[Tooth22]" value="' . $odontogram->Tooth22 . '"></input>
                 <input type="hidden" id="t21" name="tooth[Tooth21]" value="' . $odontogram->Tooth21 . '"></input>
                 <input type="hidden" id="t20" name="tooth[Tooth20]" value="' . $odontogram->Tooth20 . '"></input>
                 <input type="hidden" id="t19" name="tooth[Tooth19]" value="' . $odontogram->Tooth19 . '"></input>
                 <input type="hidden" id="t18" name="tooth[Tooth18]" value="' . $odontogram->Tooth18 . '"></input>
                 <input type="hidden" id="t17" name="tooth[Tooth17]" value="' . $odontogram->Tooth17 . '"></input>
                 <input type="hidden" id="t16" name="tooth[Tooth16]" value="' . $odontogram->Tooth16 . '"></input>
                 <input type="hidden" id="t15" name="tooth[Tooth15]" value="' . $odontogram->Tooth15 . '"></input>
                 <input type="hidden" id="t14" name="tooth[Tooth14]" value="' . $odontogram->Tooth14 . '"></input>
                 <input type="hidden" id="t13" name="tooth[Tooth13]" value="' . $odontogram->Tooth13 . '"></input>
                 <input type="hidden" id="t12" name="tooth[Tooth12]" value="' . $odontogram->Tooth12 . '"></input>
                 <input type="hidden" id="t11" name="tooth[Tooth11]" value="' . $odontogram->Tooth11 . '"></input>
                 <input type="hidden" id="t10" name="tooth[Tooth10]" value="' . $odontogram->Tooth10 . '"></input>
                 <input type="hidden" id="t9" name="tooth[Tooth9]" value="' . $odontogram->Tooth9 . '"></input>
                 <input type="hidden" id="t8" name="tooth[Tooth8]" value="' . $odontogram->Tooth8 . '"></input>
                 <input type="hidden" id="t7" name="tooth[Tooth7]" value="' . $odontogram->Tooth7 . '"></input>
                 <input type="hidden" id="t6" name="tooth[Tooth6]" value="' . $odontogram->Tooth6 . '"></input>
                 <input type="hidden" id="t5" name="tooth[Tooth5]" value="' . $odontogram->Tooth5 . '"></input>
                 <input type="hidden" id="t4" name="tooth[Tooth4]" value="' . $odontogram->Tooth4 . '"></input>
                 <input type="hidden" id="t3" name="tooth[Tooth3]" value="' . $odontogram->Tooth3 . '"></input>
                 <input type="hidden" id="t2" name="tooth[Tooth2]" value="' . $odontogram->Tooth2 . '"></input>
                 <input type="hidden" id="t1" name="tooth[Tooth1]" value="' . $odontogram->Tooth1 . '"></input>
<div style=" width:40%; margin-left: 20px; margin-right:20px; margin-bottom: 60px; float:left;">
                     <ul class="clickUl">
                         <li><a data-id="1" id="1" onClick="cause(this.id)" style="background:#00ba72; color:#fff;">K</a></li>
                         <li><a data-id="2" id="2" onClick="cause(this.id)" style="background:#004eff; color:#fff;">C</a></li>
                         <li><a data-id="3" id="3" onClick="cause(this.id)" style="background:#ff0000; color:#fff;">Ce</a></li>
                         <li><a data-id="4" id="4" onClick="cause(this.id)" style="background:#ff9000; color:#fff;">D</a></li>
                         <li><a data-id="5" id="5" onClick="cause(this.id)" style="background:#9c00ff; color:#fff;">KR</a></li>
                         <li><a data-id="6" id="6" onClick="cause(this.id)" style="background:#8e0101; color:#fff;">PS</a></li>
                         <li><a data-id="7" id="7" onClick="cause(this.id)" style="background:#006666; color:#fff;">IP</a></li>
                         <li><a data-id="8" id="8" onClick="cause(this.id)" style="background:#00c0ff; color:#fff;">X</a></li>
                     </ul>
                   
                     <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                          viewBox="0 0 450 700" enable-background="new 0 0 450 700" xml:space="preserve">
                     <g id="toothLabels">
                     <text id="lbl32" transform="matrix(1 0 0 1 97.9767 402.1409)" font-family="Avenir-Heavy" font-size="21px">32</text>
                     <text id="lbl31" transform="matrix(1 0 0 1 94.7426 449.1693)" font-family="Avenir-Heavy" font-size="21px">31</text>
                     <text id="lbl30" transform="matrix(1 0 0 1 106.0002 495.5433)" font-family="Avenir-Heavy" font-size="21px">30</text>
                     <text id="lbl29" transform="matrix(1 0 0 1 118.0002 538.667)" font-family="Avenir-Heavy" font-size="21px">29</text>
                     <text id="lbl28" transform="matrix(0.9999 -1.456241e-02 1.456241e-02 0.9999 136.4086 573.5098)" font-family="Avenir-Heavy" font-size="21px">28</text>
                     <text id="lbl27" transform="matrix(1 0 0 1 157.3335 603.8164)" font-family="Avenir-Heavy" font-size="17px">27</text>
                     <text id="lbl26" transform="matrix(1 0 0 1 179.3335 623.8164)" font-family="Avenir-Heavy" font-size="18px">26</text>
                     <text id="lbl25" transform="matrix(1 0 0 1 204.6669 628.483)" font-family="Avenir-Heavy" font-size="18px">25</text>
                     <text id="lbl24" transform="matrix(1 0 0 1 231.3335 628.1497)" font-family="Avenir-Heavy" font-size="18px">24</text>
                     <text id="lbl23" transform="matrix(1 0 0 1 256.3335 619.1497)" font-family="Avenir-Heavy" font-size="17px">23</text>
                     <text id="lbl22" transform="matrix(1 0 0 1 276.3335 602.483)" font-family="Avenir-Heavy" font-size="18px">22</text>
                     <text id="lbl21" transform="matrix(1 0 0 1 286.6669 573.1497)" font-family="Avenir-Heavy" font-size="21px">21</text>
                     <text id="lbl20" transform="matrix(1 0 0 1 303.6327 538.667)" font-family="Avenir-Heavy" font-size="21px">20</text>
                     <text id="lbl19" transform="matrix(1 0 0 1 322.983 495.5432)" font-family="Avenir-Heavy" font-size="21px">19</text>
                     <text id="lbl18" transform="matrix(1 0 0 1 325.1251 449.1686)" font-family="Avenir-Heavy" font-size="21px">18</text>
                     <text id="lbl17" transform="matrix(1 0 0 1 324.0004 402.1405)" font-family="Avenir-Heavy" font-size="21px">17</text>
                     <text id="lbl16" transform="matrix(1 0 0 1 312.8534 324.1021)" font-family="Avenir-Heavy" font-size="21px">16</text>
                     <text id="lbl15" transform="matrix(1 0 0 1 315.3335 275.3333)" font-family="Avenir-Heavy" font-size="21px">15</text>
                     <text id="lbl14" transform="matrix(1 0 0 1 311.3335 236)" font-family="Avenir-Heavy" font-size="21px">14</text>
                     <text id="lbl13" transform="matrix(1 0 0 1 300.3335 200.6667)" font-family="Avenir-Heavy" font-size="21px">13</text>
                     <text id="lbl12" transform="matrix(1 0 0 1 286.6669 172)" font-family="Avenir-Heavy" font-size="21px">12</text>
                     <text id="lbl11" transform="matrix(1 0 0 1 270.2269 142.439)" font-family="Avenir-Heavy" font-size="21px">11</text>
                     <text id="lbl10" transform="matrix(1 0 0 1 247.5099 118.9722)" font-family="Avenir-Heavy" font-size="21px">10</text>
                     <text id="lbl9" transform="matrix(1 0 0 1 227.8432 112.9722)" font-family="Avenir-Heavy" font-size="21px">9</text>
                     <text id="lbl8" transform="matrix(1 0 0 1 200.1766 112.9722)" font-family="Avenir-Heavy" font-size="21px">8</text>
                     <text id="lbl7" transform="matrix(1 0 0 1 170.5099 117.6388)" font-family="Avenir-Heavy" font-size="21px">7</text>
                     <text id="lbl6" transform="matrix(1 0 0 1 148.6667 134.167)" font-family="Avenir-Heavy" font-size="21px">6</text>
                     <text id="lbl5" transform="matrix(1 0 0 1 131.3605 164.8335)" font-family="Avenir-Heavy" font-size="21px">5</text>
                     <text id="lbl4" transform="matrix(1 0 0 1 119.3927 195.6387)" font-family="Avenir-Heavy" font-size="21px">4</text>
                     <text id="lbl3" transform="matrix(1 0 0 1 103.8631 234.4391)" font-family="Avenir-Heavy" font-size="21px">3</text>
                     <text id="lbl2" transform="matrix(1 0 0 1 96.2504 275.9999)" font-family="Avenir-Heavy" font-size="21px">2</text>
                     <text id="lbl1" transform="matrix(1 0 0 1 93.9767 324.769)" font-family="Avenir-Heavy" font-size="21px">1</text>
                     </g>

                     <g id="dmftLabels">
                     <text id="txtTooth32" transform="matrix(1 0 0 1 5.0001 386.3778)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth31" transform="matrix(1 0 0 1 0.9998 449.7374)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth30" transform="matrix(1 0 0 1 9.6668 513.5912)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth29" transform="matrix(1 0 0 1 36.3335 578.2579)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth28" transform="matrix(1 0 0 1 74.3335 626.9246)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth27" transform="matrix(1 0 0 1 109.0001 660.9246)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth26" transform="matrix(1 0 0 1 145.6668 678.2579)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth25" transform="matrix(1 0 0 1 191.6668 687.5912)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth24" transform="matrix(1 0 0 1 233.0001 687.5915)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth23" transform="matrix(1 0 0 1 283.0001 673.5915)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth22" transform="matrix(1 0 0 1 329.6668 644.9248)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth21" transform="matrix(1 0 0 1 359.6668 604.9248)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth20" transform="matrix(1 0 0 1 390.3334 558.2581)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth19" transform="matrix(1 0 0 1 412.6435 494.2493)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth18" transform="matrix(1 0 0 1 416.1565 449.7382)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth17" transform="matrix(1 0 0 1 409.9765 386.378)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth16" transform="matrix(1 0 0 1 410.5356 325.845)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth15" transform="matrix(1 0 0 1 414.0005 251.8453)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth14" transform="matrix(1 0 0 1 408.7707 211.7113)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth13" transform="matrix(1 0 0 1 386.7073 165.7383)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth12" transform="matrix(1 0 0 1 360.5876 123.5825)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth11" transform="matrix(1 0 0 1 344.0069 89.5916)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth10" transform="matrix(1 0 0 1 301.0546 54.1648)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth9" transform="matrix(1 0 0 1 229.2251 29.2916)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth8" transform="matrix(1 0 0 1 172.7413 30.3285)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth7" transform="matrix(1 0 0 1 114.3296 51.5455)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth6" transform="matrix(1 0 0 1 72.0002 91.2056)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth5" transform="matrix(1 0 0 1 48.5357 127.8719)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth4" transform="matrix(1 0 0 1 27.2052 167.0134)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth3" transform="matrix(1 0 0 1 8.7983 212.3336)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth2" transform="matrix(1 0 0 1 3.25 260.1059)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     <text id="txtTooth1" transform="matrix(1 0 0 1 5.0001 338.4393)" font-family="MyriadPro-Regular" font-size="16px"></text>
                     </g>

                     <g id="Spots">
                     <polygon id="Tooth32" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth32 . '" data-key="32" points="66.7,369.7 59,370.3 51,373.7 43.7,384.3 42.3,392 38.7,406 41,415.3 44.3,420.3
                              47.3,424 51.7,424.3 57.7,424 62.3,422.7 66.7,422.7 71,424.3 76.3,422.7 80.7,419.3 84.7,412.3 85.3,405 87.3,391.7 85,380
                              80.7,375 73.7,371.3 	"/>
                     <polygon id="Tooth31" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth31 . '" data-key="31" points="76,425.7 80.3,427.7 83.3,433 85.3,447.7 84.3,458.7 79.7,472.3 73,475 50.3,479.7
                              46.7,476.7 37.7,446.3 39.7,438.3 43.3,432 49,426.7 56,424.7 65,424.7 	"/>
                     <polygon id="Tooth30" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth30 . '" data-key="30" points="78.7,476 85,481 90.3,488.3 96.3,499.3 97.7,511.3 93,522 86,526.3 67,533
                              60.3,529.7 56.3,523.7 51.7,511 47.7,494.7 47.7,488.3 50.3,483.3 55,479.7 67,476.7 	"/>
                     <polygon id="Tooth29" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth29 . '" data-key="29" points="93.3,525 99.3,527.3 108.3,536 114,546.7 115.7,559.3 114.3,567.3 106.3,573
                              98.3,578.3 88,579 82,575 75,565 69.3,552.3 67.3,542 69.7,536 74.3,531.7 84.3,528.3 	"/>
                     <path id="Tooth28" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth28 . '" data-key="28" d="M117.3,569.7l7.7,1.3l6.3,3.7l6.3,7.7l4,8.3L144,602l-1.3,6.7l-6.7,6.7l-7.7,3.3l-7.3-1l-7-3
                           l-7.3-7l-5-9l-2-10c0,0-0.7-7,0.3-7.3c1-0.3,5.3-6.7,5.3-6.7l9-5H117.3z"/>
                     <polygon id="Tooth27" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth27 . '" data-key="27" points="155.7,611 160.3,615.3 165,624.7 161.7,634.3 156,641.3 149,644 140.7,644.3
                              133.3,641.3 128.7,634.7 128.7,629 132.7,621.3 137.7,615 143.7,611 149.7,610 	"/>
                     <polygon id="Tooth26" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth26 . '" data-key="26" points="178.3,627 186,629 187.7,633.7 188.7,644 189,657 189.3,662.7 186.3,663.7 176.7,663
                              168,656.3 159.3,649.7 156.7,644 162,639.3 	"/>
                     <polygon id="Tooth25" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth25 . '" data-key="25" points="214,637 218,642.7 223,654.3 225.7,664 225.3,666.3 219,668.3 206.7,668 196,665.7
                              190.3,662.7 193,657.3 199.7,647.3 207,638 210.7,635.5 	"/>
                     <path id="Tooth24" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth24 . '" data-key="24" d="M235.3,637c0,0,3-2,4-2.3c1-0.3,4.3,0,4.3,0l5,4.3l5.3,7.3l3.3,6.7l2,7.3l-2,3l-7.7,2.7
                           l-10,0.3h-10l-2-6.7l2.7-7.3L235.3,637z"/>
                     <polygon id="Tooth23" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth23 . '" data-key="23" points="269.3,624 273.3,624.7 275.3,627.3 279,628.7 281.7,631.3 285.3,634.7 289.3,638.3
                              292,643.3 291.3,650 287,655 280.7,658.7 272,660 265,660.7 261.3,657.3 261.7,650 263.7,637 264.3,627 	"/>
                     <polygon id="Tooth22" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth22 . '" data-key="22" points="286,629.3 286.7,633.3 291.3,638.7 295.3,642.3 302,644 311.7,643.3 318.3,637.7
                              321,630 321.3,620.3 317,614.3 308,608 298.3,607 291,609.3 287,612.3 286.7,617.7 287.3,624.7 	"/>
                     <polygon id="Tooth21" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth21 . '" data-key="21" points="331,565.7 335,565.7 341.3,568 349.3,574.3 352.3,578.3 352.7,583.7 350.7,593.7
                              342.7,604 337.7,609 328,612.7 320,613.3 315,611 308.3,604.7 306.7,598 307.3,591.3 309,584.7 312.7,578.3 318.3,571.7 	"/>
                     <polygon id="Tooth20" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth20 . '" data-key="20" points="334,561 338.7,566 346,570 354.7,573 360.7,571.7 368,568.3 383,545 385.3,532.7
                              381.3,524.3 374,520.7 363.7,516.3 356.3,515.3 351.3,518.3 346.3,524 340.3,534.3 336,546.7 	"/>
                     <path id="Tooth19" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth19 . '" data-key="19" d="M398,470l4.7,5.7l3,7.7l-0.3,11.7l-6,13.3l-6.3,10.3l-8.3,4.3l-7.3-1l-16.3-7c0,0-2.7-6-3-7.3
                           c-0.3-1.3-0.3-11-0.3-11l3.7-14.3l3.7-7l5.3-6.7l8-2l9.7-0.7L398,470z"/>
                     <polygon id="Tooth18" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth18 . '" data-key="18" points="410,435 408.7,447.3 404.3,459 399.3,467.7 393.7,468 388,466 376.3,466.3
                              369.7,466.3 365.7,460 364.7,444.7 366.3,434.3 369,424 378.3,417.3 386.7,415.7 391.7,415.3 396,418 399.7,418 404,421.7
                              407.7,427.3 	"/>
                     <polygon id="Tooth17" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth17 . '" data-key="17" points="371.7,417 378.3,417.3 386.7,415.7 391.7,415.3 397.3,417.7 402.7,416.3 407.7,409.7
                              406.7,395 401,377.7 397.3,373 390.7,367.3 380,365 373,366.7 367.3,369 364,374.3 360,389 363.3,401.3 367.7,412.3 	"/>
                     <polygon id="Tooth16" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth16 . '" data-key="16" points="404.3,293.7 408.7,299.3 408.7,308 405.3,318.7 401,329.7 392.3,339.7 382.7,341
                              369,339.7 359,335 354.7,327.7 354.3,316 358.3,304 363.7,294 368.7,294.7 378.7,296 389,296 	"/>
                     <polygon id="Tooth15" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth15 . '" data-key="15" points="362.3,247.3 357.3,251 357,259.3 358.7,268 359.7,279.7 361.3,286.7 365,291.7
                              371,294.3 392,295 404.3,293.7 410,280.7 412,263.3 407.3,246.7 401,240.3 396,239.7 389.3,243 	"/>
                     <polygon id="Tooth14" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth14 . '" data-key="14" points="359.7,243.7 350.7,224 345.7,211.7 348.7,205 358.3,202.7 375.7,197 388.7,193
                              393,196 399.3,207 401.3,222.7 400,234.3 394.7,240.7 381.7,244.7 371,246 	"/>
                     <polygon id="Tooth13" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth13 . '" data-key="13" points="386,188.7 383.3,192.7 377.7,196 356.3,203.3 345.7,202.3 341.7,199.7 338.7,196.3
                              335,188.7 332,177 333.7,169.7 338,164.7 346.3,161 353.7,156.7 360.3,150.3 364,151 370.7,156.3 376.3,164.3 380,170.3
                              383.3,178.3 	"/>
                     <polygon id="Tooth12" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth12 . '" data-key="12" points="358.7,134.3 360.3,145.7 357.3,152.7 352,157.3 346.3,161 336,164 329.7,163.3
                              321.7,157.7 314.3,149 310.7,139.3 310,133.7 312.3,127 318.3,125.7 326,122 332.7,116 334.7,114.3 337.7,117.3 343.3,119.7
                              348.7,122.7 354.3,127.7 	"/>
                     <polygon id="Tooth11" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth11 . '" data-key="11" points="336,93.3 337.7,100 336,104.7 332.7,113.7 324.3,121.3 315.3,125.7 306.3,126
                              297.3,120.3 294,112 295.7,102.7 299,95 303.3,90 309.3,88 316.3,87.3 322.7,87.3 328,88.3 	"/>
                     <polygon id="Tooth10" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth10 . '" data-key="10" points="310.3,83.3 298,90.7 286,95 276.3,98.3 270.3,93.3 269,82.7 269,69.3 270,58.7
                              274.7,54.7 282,53 287.7,54.7 297.3,60.3 304,64.3 308.7,68.7 312.3,74 313,81 	"/>
                     <polygon id="Tooth9" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth9 . '" data-key="9" points="273.3,52 266.7,61.7 258.3,72.3 253.3,79.7 247.3,85 239,87.7 232.3,82 224.7,67
                              222,58.3 219,50 220,44.3 224.3,40.3 230,38.7 237.3,38.7 253,39.3 258.7,41.3 264.3,43.7 268.3,45.7 	"/>
                     <polygon id="Tooth8" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth8 . '" data-key="8" points="176.7,46.3 195,41 203.3,39.7 209.3,40.7 215.3,42.7 217,47 217.7,54.3 215,64.7
                              212.3,75.7 208,83 201.7,85.7 195.7,86.7 189.7,83.3 183.7,74.7 175,62 171.7,54 172.7,49.7 	"/>
                     <path id="Tooth7" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth7 . '" data-key="7" d="M167,55l6.7,6.3L174,68l0.3,8l1,10l-2,8.3l-4.7,4.3l-6.7,1.7l-8-4.3l-7.3-4.7l-9.3-4.7
                           l-6.3-5.3l-1-4.3l1.3-5c0,0,3.3-6,4.3-6s5.3-6,6.3-6s10.3-4.7,10.3-4.7L167,55z"/>
                     <polygon id="Tooth6" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth6 . '" data-key="6" points="126.3,82 134.3,86.3 139.7,92.3 144.7,104.7 145.7,115.3 143.7,120.7 138,124.3
                              131.3,125 121,125 114.7,119.3 110.3,112.3 108.3,104.7 108.7,94.7 110.7,88.7 116,84 	"/>
                     <polygon id="Tooth5" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth5 . '" data-key="5" points="109,116.7 116,122.3 122.7,125.3 127.7,131.3 128.3,141 122.7,153.7 114,161.7
                              105.7,162.3 96.7,161 85.7,156 82,150 81,139.3 86.3,128 93,121.3 100.7,117.3 	"/>
                     <polygon id="Tooth4" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth4 . '" data-key="4" points="82,155.3 102.3,163.3 108.7,172 109.3,182 104.7,192 100,199 94,203.7 85.3,201.7
                              73.7,201 64.3,196.7 60.3,190.7 59,183.3 61.7,175.3 66.3,167.7 71.3,161.3 	"/>
                     <path id="Tooth3" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth3 . '" data-key="3" d="M92.7,207.3l2,5.3l-1.7,8l-1.7,9l-4,8l-5,7.7l-11,4.7l-13.7,0.7l-10-7l-1.7-5L45,220l3-10.7
                           l5-7.3l4-3.3l4.7-2.7l5.3,3.7l6.7,1.3c0,0,7.3,1.3,9.3,1.3s6.3,0.7,6.3,0.7L92.7,207.3z"/>
                     <polygon id="Tooth2" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth2 . '" data-key="2" points="79.7,288.3 71.7,291 55,293 40.3,291.3 36,287 33,273.7 36.3,260 42,248.7 44.7,244.7
                              50.3,246.7 56,249 65.3,250.7 74,249.7 80.3,249.7 82.3,254 85.3,259.3 87,267.7 87.7,274.7 85.3,282.7 	"/>
                     <polygon id="Tooth1" onClick="reply_click(this.id)" fill="' . $odontogram->Tooth1 . '" data-key="1" points="33,314.3 38,325.7 45.7,335.7 55.7,341.7 64.7,343 73.3,340 77.7,335.7 81.3,326.3
                              82,314.3 81.3,302 80.7,292.7 73.7,292 51.3,293.7 38.7,293.7 34,298 31.7,302.3 32,311 	"/>
                     </g>

                     <g id="adult-outlines">
                     <g id="XMLID_210_">
                     <path id="XMLID_208_" fill="#010101" d="M372.6,180.5c0.2,1.4-2,2.3-2.9,1.2c-0.7-1.1,1.5-1.8,2.4-0.9L372.6,180.5z"/>
                     <path id="XMLID_207_" fill="#010101" d="M71.4,392.6c-0.5,1.1-2,1.5-2.9,0.9c-0.3-1.6,2.6-2.4,3.2-0.9L71.4,392.6z"/>
                     <path id="XMLID_199_" fill="#010101" d="M83.6,183.9c1.2,0.1,2.2,1.1,2.3,2.3c-1.2,1.3-3.7-1.1-2.4-2.3L83.6,183.9z"/>
                     <path id="XMLID_192_" fill="#010101" d="M341.6,587.6c-0.3-0.9,1.1-1.3,2-1.1c0.7,1.1-0.3,2.8-1.6,2.8
                           C341.2,589.2,341,588,341.6,587.6L341.6,587.6z"/>
                     <path id="XMLID_188_" fill="#010101" d="M87.8,552.3c-1.5,0-3,0-4.6,0c-0.4-0.6-0.5-1.3-0.4-2c1.4-0.4,2.8-0.5,4.2-0.3
                           c0.3,0.7,0.6,1.5,0.8,2.2L87.8,552.3z"/>
                     <path id="XMLID_186_" fill="#010101" d="M63.1,269.9c2.1,0.4,3.5,2.9,2.7,4.9c-1.8-0.7-3-2.8-2.7-4.7L63.1,269.9z"/>
                     <path id="XMLID_64_" fill="#010101" d="M407.7,456.5c5.4-9,6.6-22,0.9-30c-0.6-1.7-1.7-3.4-2.9-4.4c-0.9-0.7-1.8-1.4-2.6-2.1
                           c-0.4-0.4-0.8-0.7-1.2-1c2.4-1.1,4.5-3.1,5.6-5.4c2.5-5.1,1.8-11,0.8-16.6c-1.6-8.7-4.1-17.6-9.8-24.5c-5.6-6.9-15-11.3-23.5-8.9
                           c-9.2,2.6-14.9,12.4-15.5,21.9c-0.6,9.5,3,18.8,7.2,27.4c1,2.1,2.1,4.3,2.2,6.7c0,2.1-0.8,4.2-1.5,6.2c-3.5,9.5-4.8,19.7-4.1,29.8
                           c0.4,4.9,2.8,10.8,6.5,13.2c-0.6,0.6-1.2,1.5-1.8,2.1c-1.2,1.2-2.5,2.3-3.6,3.6c-5,4.6-6.7,12.7-7.1,19.9
                           c-0.5,8.9-0.8,18.9-7.3,24.9c-9.4,8.5-15.3,20.7-16.3,33.3c-0.4,4.8-0.9,10.9-5.5,12.3c-16.4,5.2-26.6,24.8-21.3,41.2
                           c-8.6-1-20.5,0.4-21.6,9c-0.4,3.3,1.1,6.5,0.9,9.8c-0.1,2.3-1.9,4.8-4,5.4c-1.4-1.1-2.7-2.2-4.5-2.8c-1.3-0.4-1.7-0.9-2.4-1.7
                           c0.1,0,0.2,0,0.3,0.1c-1.4-4.1-8-3.8-10.7-0.3c-2.7,3.4-2.7,8.2-2.9,12.5c-0.2,4.4-1,9.2-4.5,11.8c-2.2-4.9-4.5-10-8.7-13.3
                           S238,632,234,635.6c-5.2,4.7-2.9,13.6-6.3,19.8c-4.4-1.8-5.7-7.3-7-11.9c-1.3-4.6-4.6-9.9-9.4-9.1c-2.6,0.4-4.4,2.6-6.1,4.6
                           c-4.8,5.8-9.5,11.6-14.3,17.4c-4.6-9,3.5-22.7-4.5-29c-6.7-5.2-15.8,1.6-21.4,7.9c1-5.8,2.1-11.8,0.3-17.4
                           c-1.8-5.6-7.4-10.4-13.1-9.2c-5.6,1.2-8.2-6.7-8.1-12.4c0.1-4.8-0.7-11.1-4.4-13.2c-1.3-1.9-2.7-3.8-4-5.7c-1.7-2.5-3.2-4.2-6-5.6
                           c0,0-0.1,0-0.1,0c-3.4-2.8-7.7-4.4-12-4.4c3.2-16.9-5.5-35.3-20.6-43.5c4.2-10.4,2.9-22.8-3-32.3c-3.1-5.8-7.1-11.1-12.4-14.8
                           c3.8-12.1,5.3-24.8,4.6-37.5c-0.2-2.9-0.8-6.2-2.4-8.6c-0.4-1.2-1-2.3-1.9-3.1c-1.1-0.9-2.6-1.6-4.1-2.1c1.1-0.7,2.1-1.6,2.9-2.6
                           c3-3.6,4.3-8.2,5.4-12.7c2.4-9.5,4.5-19.9,0.6-28.9c-3.2-7.3-10.3-12.7-18.2-13.8s-16.2,2.2-21.3,8.3c-4.6,5.6-6.4,13.1-7.9,20.2
                           c-2.1,9.3-3.3,20.9,4.5,26.4c2,1.4,1.7,4.7,0.3,6.7s-3.6,3.5-5.1,5.5c-2.6,3.6-2.5,8.5-2,13c1.5,12.7,5.6,25.1,11.8,36.3
                           c-0.4,0.7-0.9,1.3-1.2,2c-0.8,1.5-1,3.2-1.1,4.8c-0.8,3.2-0.2,6.9,0.5,10.2c3,14.2,8.1,30.9,21.9,35.3c-5,5.4-2.4,14,0.5,20.8
                           c2.7,6.4,5.5,12.9,10.3,18c4.8,5,12.1,8.3,18.7,6.4c-4,19.4,13.3,40,33,40.1c-1.1,2.1-2.1,4.2-3.1,6.4c-0.2,0.4-0.1,0.8,0.1,1.1
                           c-2.2,6.2,0.8,14.6,7.4,16.3c7.7,2,18.2-2.8,22.3,3.9c5.4,9,15.4,15,25.9,15.7c-0.2-0.2-0.5-0.3-0.7-0.5c1,0.1,2,0.2,3,0.2
                           c1.5,0.1,2.8,0.2,4.1-0.6c6.6,5.3,15.8,7.3,24,5.3c2.2,0,4.3,0.2,6.5-0.2c2.3-0.4,4.4-1,6.3-2.3c8.3,3.6,18.2,3.2,26.2-1
                           c0.3-0.1,0.5-0.1,0.8-0.2c1.3-0.3,2.5-0.6,3.5-1.5c0.2-0.2,0.3-0.5,0.3-0.7c1.2-0.9,2.3-1.8,3.5-2.7c13.1,6.3,31.1-2.4,34.2-16.7
                           c7.4,3.6,17.1,1.8,22.7-4.2c5.6-6,6.8-15.8,2.7-22.9c19.4-1.8,35.2-21.6,32.6-40.9c21.2-5.9,36-29.1,32.3-50.8
                           c9.8-4.6,14.6-15.7,18.6-25.8c3.1-7.9,5.7-17.9-0.4-23.8C399.1,470.9,404,462.6,407.7,456.5z M40.6,410c-1-1.9-0.5-4.3,0-6.4
                           c1.1-4.4,2.2-8.8,3.3-13.2c1.5-5.8,3.3-12.1,8.1-15.6c1.4-1,2.9-1.7,4.5-2.2c7.1-2.5,15.4-1.7,21.5,2.7c6.1,4.4,9.5,12.5,7.6,19.7
                           c-1.5,6-0.9,12.3-2.8,18.2c-1.9,5.8-7.9,11.3-13.7,9.2c-7.2-2.5-16.2,4.1-22.4-0.4C43.1,419.3,42.8,414,40.6,410z M45.6,471.3
                           c-1.3-5-2.5-10.1-3.8-15.1c-1-3.8-1.9-7.7-1.8-11.6c0.3-6.5,3.9-12.8,9.5-16.3c5.5-3.5,12.8-4,18.8-1.5c2.1,0.9,4.5,0.8,6.7,0
                           c1.8,0.3,3.9,1,5.3,2c3.9,11.8,4.2,24.7,1,36.6c-0.6,2.2-1.4,4.6-3.2,6c-1.5,1.3-3.5,1.7-5.5,2.1c-6.8,1.5-13.7,3-20.5,4.5
                           C48.6,479,46.5,474.7,45.6,471.3z M63.2,530c-3.3-1.7-5.2-5.3-6.6-8.7c-4.3-9.8-7-20.3-8.1-31c0.1-1,0.2-2.1,0.7-3
                           c0.4-0.9,1.1-1.7,1.6-2.6c0.2-0.1,0.4-0.1,0.6-0.3c0.4-0.2,0.5-0.6,0.4-1c8-4.9,17.7-7.1,27-6.1c0,0,0,0,0,0
                           c7.9,4.7,12.8,13.2,16.4,21.4c0,0.1,0.1,0.2,0.2,0.2c0.9,3.1,1.4,6.2,1.3,9.4c-0.1,7.2-4.2,14.8-11.1,16.8
                           C78,527.3,70.2,533.6,63.2,530z M89.1,577.8c-6.7-1.7-10.3-8.7-13.2-15c-1.4-3-2.7-6.1-4.1-9.1c-1.7-3.8-3.4-7.8-2.7-11.9
                           c0.7-3.9,3.5-7.2,6.9-9.3c3.4-2.1,7.2-3.2,11-4.3c2.1-0.6,4.3-1.2,6.5-1.1c4,0.2,7.5,2.6,10.3,5.4c6.6,6.5,10.6,15.4,11.1,24.6
                           c0.1,2.6,0,5.2-1.1,7.5c-1.3,2.7-3.8,4.5-6.1,6.3C102.3,575.2,95.8,579.5,89.1,577.8z M120.8,616.5c-7.1-1.9-12.8-7.5-16.2-14
                           c-3-5.7-4.5-12.3-3-18.6c1.5-6.2,6.4-11.8,12.7-13c6.2-1.2,12.2,1.8,17.6,5.1c1.1,1.2,2.1,2.6,3.1,4.1c1.2,1.7,2.3,3.4,3.5,5
                           c3.6,8,6.2,17.3,1.6,24.6C136.4,615.9,127.9,618.4,120.8,616.5z M150.4,642.4c-5.6,2-12.3,1.4-16.7-2.6c-3-2.7-4.5-7-3.9-10.9
                           c0,0,0,0,0,0c1.3-2.7,2.6-5.4,4-8c3.6-4.3,7.6-8.8,13.1-9.8c7.7-1.5,15.6,5.5,16.1,13.3C163.7,632.3,157.9,639.8,150.4,642.4z
                           M184.5,662.6c-1.6-0.1-3.2-0.3-4.8-0.4c-5.9-3.9-11.8-7.7-17.6-11.6c-1.4-0.9-3-2-3.4-3.7c-0.6-2.6,1.7-4.8,3.8-6.4
                           c3.9-2.9,7.8-5.9,11.7-8.8c2.2-1.7,4.7-3.4,7.5-3c4.8,0.7,6,7.1,6,12c0,7.1,0,14.1,0.1,21.2c0.3,0.3,0.6,0.6,0.9,0.9
                           C187.4,663,185.8,662.7,184.5,662.6z M212.9,667.5C212.9,667.5,212.8,667.5,212.9,667.5c-7.3-0.3-14.5-2.1-21-5.4
                           c4.7-8,10.1-15.6,16.1-22.7c0.9-1,2-2.2,3.3-2.1c1.3,0,2.4,1.2,3.2,2.3c5.6,7.7,9.2,16.8,10.3,26.3c0.1,0,0.1,0.1,0.2,0.1
                           C221.2,667.9,217.1,667.3,212.9,667.5z M257.1,662.6c-0.3-0.1-0.6,0-0.9,0.2c-1,0.9-2.6,1-3.8,1.3c-0.4,0.1-0.8,0.3-1.3,0.4
                           l-12.4,1c-3.6,0.3-8.3-0.1-9.4-3.5c-0.6-1.7,0.1-3.6,0.7-5.3c1.7-4.7,3.5-9.5,5.2-14.2c1.3-3.6,4-7.9,7.7-6.9
                           c1.4,0.4,2.5,1.5,3.4,2.6C252.6,645.1,259.2,654,257.1,662.6z M366.7,407.2c-2.7-7.6-5.5-15.8-3.5-23.6c0.6-2.6,1.8-5.1,2.1-7.7
                           c0.4-3.1,2.8-5.8,5.7-7.2c2.8-1.4,6.1-1.8,9.3-1.8c5.7,0,11.8,1.4,15.8,5.4c5.1,5.2,5.6,13.2,7.5,20.3c0.9,3.4,2.2,6.7,2.8,10.2
                           s0.2,7.3-1.9,10.1c-2.1,2.8-6.3,4.2-9.3,2.3c-7-4.4-17.3,4.1-24-0.7C368.8,412.8,367.7,409.9,366.7,407.2z M368.9,463.2
                           c-1.7-1.9-2-4.6-2.2-7.2c-0.8-9.6-1.5-19.8,2.9-28.3s15.9-14.2,24-9c1.8,1.2,4,1.4,6.1,0.9c1.4,1.1,2.5,2.3,3.9,3.3
                           c1.5,1.1,3.2,2.9,3.4,4.8c0.1,0.4,0.3,0.7,0.6,0.8c3.2,9.3-0.5,21.4-4.7,31.2c-1.8,4.2-6.5,9.1-9.8,6
                           C386.9,460.1,374.5,469.6,368.9,463.2z M285,655.6c-4.7,3.2-10.7,3.7-16.3,4.2c-1.5,0.1-3.2,0.2-4.5-0.7c-1.9-1.4-1.7-4.2-1.3-6.5
                           c1.3-8.2,2.6-16.5,3.8-24.7c1.6-1.4,3.7-2.3,5.8-2.5c1.3,0.9,1.7,2.6,3.2,3.3c0.9,0.5,2,0.5,2.9,1c0.5,0.3,1.1,0.7,1.6,1.1
                           c1.7,4.1,7.2,6,9.6,9.9C292.6,645.7,289.7,652.4,285,655.6z M311.4,641.3c-7.7,3.9-18.2,0.5-22.1-7.2c-0.7-1.4-0.8-3.1-0.8-4.6
                           c0-2.8-0.1-5.5-0.1-8.3c-0.1-3.2,0-6.6,1.9-9.1c2.2-2.7,6-3.5,9.5-3.4c7.5,0.2,15.3,3.8,18.8,10.5
                           C322.5,626.9,319,637.4,311.4,641.3z M349.8,590.1c-3.7,7.8-8.6,15.5-16.2,19.6c-7.6,4.1-18.5,3.1-23.2-4.2
                           c-3-4.6-3-10.6-1.5-15.8c2.3-8.3,7.9-15.7,15.4-20c2.7-1.6,5.7-2.8,8.8-2.6c3.9,0.2,7.4,2.6,10.6,4.8c3.6,2.6,7.6,5.7,8.1,10.1
                           C352.1,584.8,351,587.5,349.8,590.1z M382.6,543c-1.9,4.3-4.8,8.1-7.3,12.1c-3.4,5.4-6.2,11.7-11.8,14.7c-6.2,3.2-13.8,1.4-19.9-2
                           c-3.5-2-6.9-4.7-8-8.6c-1.1-3.9,0.5-8.1,2-11.9c1.8-4.4,3.6-8.8,5.4-13.3c2.8-7,6.6-14.8,13.9-16.7c6.1-1.5,12.2,1.8,17.6,5
                           c3.1,1.9,6.4,3.9,8.2,7C385.1,533.4,384.5,538.7,382.6,543z M397.9,508c-2.4,4.8-5.1,10-10,12.1c-5.6,2.4-12,0-17.6-2.4
                           c-8-3.4-11.8-13.2-11-21.9c0.7-7.7,4.2-14.8,7.9-21.7c0.5-0.5,1-0.9,1.5-1.4c0.5-0.5,1.1-1,1.5-1.5c0.2-0.2,1.1-1.6,1.3-1.6
                           c0.3,0.1,0.5,0,0.7-0.1c1,0.2,2.1,0.2,3.2-0.2c8.8-2.8,19.7-1.8,25.3,5.5C407.9,484.2,403,497.5,397.9,508z"/>
                     <path id="XMLID_183_" fill="#010101" d="M378.3,306.7c1.2,0.4,1.9,1.7,1.7,2.9c-1.9,0.2-3.7-1.6-3.6-3.4c0.5-0.6,1.6-0.3,1.8,0.4
                           L378.3,306.7z"/>
                     <path id="XMLID_177_" fill="#010101" d="M358.7,536.6c0.7,2.3,2.4,4.2,4.7,5.2c3.3-3,6.9-6.1,11.4-6.2c-1.9,3.5-5.3,6.2-9.1,7.1
                           c-3.2,0.8-4.9,4.6-4.4,7.9c0.5,3.3,2.6,6.1,4.6,8.7c-1.2,1.5-3.5-0.3-4.4-2c-0.9-1.7-2.9-3.7-4.3-2.4c-1.2-2.8,1.5-5.7,1.7-8.7
                           c0.3-4.4-4.6-8.2-3.5-12.4c0.5-0.8,1.8-0.5,2.4,0.2S358.5,535.7,358.7,536.6z"/>
                     <path id="XMLID_176_" fill="#010101" d="M63.1,270.1c-1.4-0.5-2.4-2.1-2.2-3.6c0.2-1.5,1.5-2.9,3-3.1c-0.2,2.2-0.5,4.4-0.9,6.7
                           L63.1,270.1z"/>
                     <path id="XMLID_175_" fill="#010101" d="M320.6,597.9c-0.2-1-0.3-1.9-0.5-2.9c1.7-0.7,3.5,0.6,5.3,0.9c3.5,0.6,6.7-2.8,7.3-6.3
                           s-0.8-7-2.1-10.3c0.6-0.1,1.2-0.2,1.7-0.3c5.3,5.5,4,15.7-2.4,19.8c-0.6,0.4-1.3,0.8-2.1,0.8C325.4,599.9,323,596.8,320.6,597.9z"
                           />
                     <path id="XMLID_174_" fill="#010101" d="M119.7,592.5c2.5-1.5,6.2-0.5,7.6,2.1C124.7,595.7,121.3,594.8,119.7,592.5z"/>
                     <path id="XMLID_172_" fill="#010101" d="M389.2,304.3c1.4-0.6,2.6,1.8,1.7,3c-1,1.3-2.7,1.4-4.3,1.5c-0.6-1.8,0.9-3.9,2.8-4
                           L389.2,304.3z"/>
                     <path id="XMLID_167_" fill="#010101" d="M97.4,545.2c-0.7,1.1-1.4,2.1-2.1,3.2c-0.8,0.8-2.3-0.3-2.3-1.4c0-1.1,0.9-2.1,1.7-2.9
                           c0.9-0.9,1.8-1.8,2.7-2.7C98.3,542.4,98.3,544.2,97.4,545.2L97.4,545.2z"/>
                     <path id="XMLID_165_" fill="#010101" d="M58.9,456c-0.1-1.2-0.3-2.3-0.4-3.5c0.7-0.1,1.5-0.2,2.2-0.3c-0.4,1.4,0.2,2.9,0.8,4.3
                           c0.6,1.4,1.2,2.9,0.7,4.3c-0.5,1.4-2.6,2.1-3.5,0.9C58.4,459.7,58.5,457.8,58.9,456L58.9,456z"/>
                     <path id="XMLID_163_" fill="#010101" d="M59,444.6c-0.2-1.4,1.6-2.4,2.9-1.8c1.2,0.6,1.6,2.4,1,3.6c-0.6,1.3-2,2-3.3,2.3
                           c-2,0.3-3.2-3.1-1.4-4.1L59,444.6z"/>
                     <path id="XMLID_162_" fill="#010101" d="M378.1,510.6c0.5-3.6,0-7.3-1.3-10.7c1.9,1.7,4.9,1.8,7,0.3c2-1.5,2.8-4.5,1.7-6.8
                           c2.9,1,5.9,1.8,8.9,2.3c-6,3.6-12.5,8-13.6,14.8C379.9,510.6,379,510.6,378.1,510.6z"/>
                     <path id="XMLID_161_" fill="#010101" d="M66.5,229c0.7,1.9,1.4,3.8,2.1,5.7c-0.7,0.2-1.4,0.3-2.1,0.5c-1-2.7-2.1-5.4-3.1-8.1
                           C64.3,226,65.9,227.6,66.5,229z"/>
                     <path id="XMLID_157_" fill="#010101" d="M373.1,216.3c1.2-2.9,3.1-5.5,5.5-7.5c0.8,0,1.6,0,2.4,0
                           C379.5,212.4,377,216.7,373.1,216.3z"/>
                     <path id="XMLID_154_" fill="#010101" d="M63.1,219.6c-0.2,2.4-1.4,4.6-3.4,6c-1.2-0.9-0.8-2.8-0.3-4.2c1-2.8,2-5.6,3.1-8.3
                           C64.3,214.7,64.6,217.8,63.1,219.6L63.1,219.6z"/>
                     <path id="XMLID_150_" fill="#010101" d="M91.9,552.9c2.1,0.3,4.5,0.9,5.6,2.7c1.1,1.8-0.7,5-2.7,4c-1.9-2.4-3.9-4.8-5.8-7.2
                           C90,552.6,91,552.8,91.9,552.9z"/>
                     <path id="XMLID_148_" fill="#010101" d="M111.7,137.3c-3.4,0.7-6.9,0.6-10.2-0.3c-1.3-1.3-1.6-3.5-0.6-5c1.3,2,3.7,3.3,6,3.2
                           c1,0,2.1-0.3,3.1-0.1C111.1,135.3,112,136.3,111.7,137.3L111.7,137.3z"/>
                     <path id="XMLID_129_" fill="#010101" d="M102.5,140.9c-0.4,1.9-1,3.7-1.8,5.4c-2.4,0.3-4.7,0.6-7.1,0.9c2.5-2.7,4.9-5.4,7.4-8
                           c0.2-0.5,1.1-0.5,1.4,0C102.7,139.6,102.6,140.3,102.5,140.9z"/>
                     <path id="XMLID_119_" fill="#010101" d="M262.1,54.8c-4.1-0.8-8.2-2.1-12.1-3.7c-0.5-0.2-0.9-0.8-1-1.4
                           C253.8,47.6,259.9,50,262.1,54.8z"/>
                     <path id="XMLID_117_" fill="#010101" d="M359.4,184.9c2.1-2.4,4.2-4.8,6.3-7.2c0.1,4.3-2.2,8.6-5.9,10.8c-0.8,0.3-1.6-0.6-1.6-1.4
                           C358.3,186.2,358.9,185.5,359.4,184.9z"/>
                     <path id="XMLID_97_" fill="#010101" d="M77.7,167c1.7,0.3,3,1.6,4.3,2.8c2,1.9,4,3.8,6,5.8c-3.1,0.1-5.4,2.7-7.5,4.9
                           c-2.1,2.2-5,4.4-8,3.6c-0.1-0.7-0.2-1.5-0.3-2.2c3.3-0.2,6.5-2.5,7.6-5.6C81,173.1,80.1,169.3,77.7,167z"/>
                     <path id="XMLID_67_" fill="#010101" d="M201.2,50.3c-6.4,2.4-13.2,3.8-20.1,4.1C186.6,49.8,194.4,48.2,201.2,50.3z"/>
                     <path id="XMLID_60_" fill="#010101" d="M391.5,280.2c-1.3-2.9-4.6-4.4-7.2-6.3c-2.6-1.9-5-5.3-3.6-8.2c0.5-1,1.4-1.8,2-2.7
                           c1.1-1.5,1.7-3.3,1.6-5.1c1.3,1.2,1.6,3.3,1.3,5.1c-0.3,1.8-1,3.5-1.1,5.3c-0.2,1.8,0.3,3.9,1.9,4.8c1.5,1,4.1,0,4.1-1.8
                           C391.7,274.1,392.1,277.3,391.5,280.2z"/>
                     <path id="XMLID_49_" fill="#010101" d="M70.8,209.5c1.2,2.9,2.5,5.9,3.7,8.8c0.3,0.7,0.6,1.4,0.5,2.1c-0.1,0.7-0.5,1.3-0.8,1.8
                           c-1.2,1.8-2.4,3.6-3.6,5.4c-1.4-0.3-1.7-2.2-1.2-3.5c0.5-1.3,1.6-2.4,2-3.7c0.9-3.6-3.4-7.1-2.2-10.6
                           C69.6,209.3,70.4,209.2,70.8,209.5z"/>
                     <path id="XMLID_48_" fill="#010101" d="M292.7,71.3c-0.8-0.7-1.6-2.1-0.7-2.6c4.8,1.5,9,4.8,11.6,9.1c-0.4,0.6-1.1,1-1.8,1.1
                           C298.8,76.3,295.7,73.8,292.7,71.3z"/>
                     <path id="XMLID_46_" fill="#010101" d="M382.4,441.6c-0.7-0.5-1.3-1.3-1.4-2.1c3.8-1.8,5.5-6.9,3.7-10.6c1.3-0.9,2.4,1.3,3.6,2.3
                           c1.7,1.6,4.4,0.7,6.3-0.4c2-1.2,4.1-2.6,6.3-2.1c-0.8,3-3.7,5.3-6.8,5.4c-2.8,0.1-5.1,3.1-4.5,5.8
                           C387.5,438,383.5,438.9,382.4,441.6z"/>
                     <path id="XMLID_44_" fill="#010101" d="M366.5,164.1c-0.4,1.3-0.7,2.6-1.4,3.8c-2.4,4.5-8.6,6.6-13.2,4.3
                           c2.9-3.2,9.5-1.5,11.4-5.4c0.4-0.8,0.5-1.7,1-2.4S366,163.5,366.5,164.1z"/>
                     <path id="XMLID_43_" fill="#010101" d="M392.5,251.6c-1.6,3-0.8,7.1,1.9,9.3c1.4,1.2,1.9,3.3,1.2,5c-0.8,1.7-2.8,2.7-4.6,2.3
                           c1.3-2.6,1.3-5.9,0.1-8.6c-0.8-1.8-2.2-3.4-2.3-5.3C388.7,252.4,391,250.3,392.5,251.6z"/>
                     <path id="XMLID_39_" fill="#010101" d="M370.9,231.8c1.5-4.6,5.7-8.2,10.5-8.9c1.6-0.2,4,0.9,3.2,2.4
                           C380.1,227.5,375.5,229.6,370.9,231.8z"/>
                     <path id="XMLID_35_" fill="#010101" d="M385,401c0.2-3.3-2-6.6-5.1-7.7c-0.9-0.3-2-0.5-2.8-1.1c-0.8-0.6-1.3-1.8-0.6-2.6
                           c4.7-0.4,9.5,2.4,11.5,6.7c0.6,1.2,0.9,2.7,0.3,3.9C387.7,401.5,385.9,402,385,401z"/>
                     <path id="XMLID_66_" fill="#010101" d="M408.9,285.8c7.9-15.8,6-38.2-9.1-47.3c7.5-16.1,2.5-37.1-11.5-48.1
                           c-2.6-15.9-11.2-30.8-23.7-41.1c-3.5-2.9-3.3-8.2-3.9-12.7c-0.3-2.2-1.3-4.7-2.7-6.4c0,0,0-0.1-0.1-0.1c-0.7-1.1-1.7-2-2.6-2.8
                           c-1.4-2-3.1-4-5-5.3c-0.4-0.3-0.8-0.5-1.3-0.8c0.1,0,0.3,0,0.4,0c-0.4-0.2-0.8-0.3-1.2-0.5c-0.8-0.4-1.7-0.8-2.4-1.4
                           c-1.1-0.7-1.9-1.1-2.9-1.1c-1.4-0.8-2.7-1.8-3.8-3c-2.7-3-3.9-7.8-1.4-11c4-5.3,0.2-13.6-5.8-16.5s-13.1-2.1-19.7-1.2
                           c3.3-3.9,3.4-9.8,1.4-14.5c-2.1-4.7-6-8.3-10.2-11.2c-8.1-5.6-17.6-9.1-27.4-10c-2.4-1.7-4.3-3.7-6.5-5.4c-2.5-1.9-5.6-3-8.4-4.3
                           c-0.1,0-0.1,0-0.2,0c-12.1-6.2-27.1-6.6-39.4-0.7c-4.2,2-9-0.1-13.5-1.3c-14.4-4-31,2.2-39.3,14.6c-15.1-3.5-32.1,5.4-37.9,19.8
                           c-1.4,3.4-3.4,7.8-7,7.1c-6.8-1.2-13.3,4.4-15.5,11c-2.2,6.6-1,13.7,0.4,20.5c0.6,2.8-3.4,4-6.2,4.4c-13.6,2-24.2,16.2-22.3,29.8
                           c0.4,2.5,0.9,5.6-1,7.2c-8,6.9-16.4,14.4-19.6,24.5c-1.8,5.7-1.1,12.4,1.7,17.5c0,0-0.1,0-0.1,0.1c-1,0.7-2.1,1.4-3.1,2
                           c-0.4,0.2-0.7,0.5-1.1,0.7c-6.1,0.9-10.5,7.4-11.6,13.7c-1.2,6.9,0.3,14.1-0.4,21.1c-1,10.4-6.6,19.8-9.9,29.7
                           c-3.3,9.9-3.8,22.3,3.8,29.5c-3.6,2.2-6.3,5.9-7.2,10c-0.2,0.2-0.2,0.4-0.3,0.6c-0.2,0.2-0.3,0.4-0.3,0.7c0,2.3,0,4.6,0.8,6.8
                           c0.3,6.8,3.2,13.5,7.8,18.5c0.2,0.5,0.4,0.9,0.7,1.3c0,0,0,0,0,0c1.5,2.6,3.5,4.6,6.1,6.4c2,1.4,4,3.3,6.1,4.7
                           c4.3,4.6,12.1,5.7,18,3c7-3.2,11.5-10.5,13.2-18.1s1-15.4,0.3-23.1c-0.4-4.3-0.7-8.5-1.1-12.8c1.8-2.6,3.1-5.5,4-8.5
                           c0.3-0.7,0.6-1.3,0.8-2c0.4-1.5,0.6-3.2,1-4.7c0.2-0.7,0.3-1.3,0.3-2c3.4-9.7-9.3-22.2-2.6-30.3c8.7-10.4,12.1-25,9-38.2l2-1.8
                           c0.9-0.3,1.7-0.8,2.4-1.6c1-1.2,2.3-2,3.3-3.3c0.6-0.8,1.1-1.6,1.5-2.4c0.5-0.5,1-1,1.5-1.6c0-0.1,0.1-0.1,0.1-0.2
                           c3.6-3.1,4.9-9.4,4.8-14.6c-0.2-7-0.1-15.7,6.2-18.7c11.4-5.6,16.9-21,11.4-32.5c6.1-0.7,12.5-2.7,16.2-7.6
                           c6.6-8.8,1.2-21.2-4.3-30.7c9.3,2.2,16.2,12.8,25.7,11.6c6.5-0.8,11.1-7.3,11.9-13.7s-1.1-13-3.1-19.2c8.3,4.9,11.6,17,21,19.4
                           c6.8,1.8,13.9-2.8,17.4-8.9c3.5-6.1,4.2-13.3,4.9-20.3c5.4,3.6,7,10.6,9,16.7c2,6.1,6,12.9,12.4,13.3c4.8,0.4,9-3,12.5-6.3
                           c5.5-5.4,10.6-11.3,14.9-17.7c3,5.6,1.5,12.3,1,18.6c-0.4,6.3,1.2,13.9,7.2,16.1c7.7,2.7,14.8-6,23-6.9c-3,7.9-7.4,16.3-4.6,24.2
                           c2.5,7.1,10.3,11.1,17.8,11.1c-0.7,0.9-1.3,1.9-1.5,3c-0.4,1.8-0.1,3.8-0.1,5.6c0,0.1,0,0.3,0.1,0.4c-1,9.7,7,19.7,16,25
                           c3.6,2.1,8,5.4,6.6,9.2c-2.5,6.8-1,14.8,3.5,20.4c0.3,2.7,2.6,5.2,4.3,7.2c1.5,1.8,3.2,3.4,5.2,4.5c0.5,1,1,2.1,1.5,3.1
                           c-1.2,1.6-1.1,4.2-0.9,6c0.1,1.3,0.3,2.7,0.7,3.9c0.4,1.1,1.1,2,1.5,3.2c1.5,6.7,4,13.2,7.3,19.1c1.3,2.3,2.8,4.8,2.3,7.5
                           c-2.5,14-1.1,28.8,4.1,42c1.6,4.1-0.5,8.6-2.4,12.6c-2.8,5.6-5.4,11.5-6.1,17.7c-0.7,6.2,0.7,13,5.2,17.4
                           c5.3,5.3,13.3,6.2,20.7,6.7c3.7,0.2,7.4,0.4,10.9-0.7c8-2.5,12.5-10.9,16.1-18.5c4.2-8.8,8.1-20,1.9-27.5
                           C405.4,293.1,407.3,289,408.9,285.8z M73.7,338.6c-5.9,4.3-13.9,3.3-21,1c-1.7-1.2-3.4-2.8-5-3.9c-3-2.1-5.4-4.3-6.9-7.7
                           c-0.1-0.2-0.2-0.3-0.3-0.3c-0.3-0.7-0.5-1.5-0.8-2.2c-2.2-5.4-4.3-10.8-6.5-16.2c-0.1-0.2-0.2-0.3-0.3-0.5c-0.4-1.6-0.4-3.3-0.3-5
                           c0.2-0.2,0.2-0.5,0.2-0.7c2.2-2.7,4.5-5.4,6.7-8.1c11.8-0.5,23.7-1,35.5-1.6c1.5-0.1,3.2-0.1,4.3,0.8c1.5,1.2,1.7,3.4,1.7,5.3
                           c0,5.1,0.1,10.3,0.1,15.4C81.2,323.6,80.6,333.5,73.7,338.6z M84.1,260.4c0.5,4.1,0.4,8.7,2.4,12.1c0.2,2.1-0.8,4.8-1.3,6.7
                           c-0.4,1.7-1.8,4.2-3,5.3c-0.1,0.1-0.1,0.1-0.1,0.2c-5.9,4.8-15.2,6.3-23.2,7c-8.3,0.7-18.8,0.2-22.4-7.3
                           c-1.8-3.6-1.3-7.9-0.8-11.9c1.2-9.5,2.9-19.9,10.3-26c8.5,5.4,19.2,7.1,29,4.6C79.7,249.7,83.5,255.5,84.1,260.4z M93,214.7
                           c-0.7,12.3-5.3,25.3-15.8,31.7c-10.5,6.4-27.2,2.5-30.3-9.4c-3.3-12.5,0.2-26.5,8.9-36.1c0.9-0.3,1.6-0.8,2.4-1.3
                           c1.2-0.7,2.4-1.5,3.6-2.3c0.1,0,0.1-0.1,0.1-0.1c2.2,3.2,5.3,5.6,9.2,6.4c7.5,1.6,18-1.8,21.3,5.2C93.2,210.6,93.1,212.7,93,214.7
                           z M102,194.9c-0.4,0.1-0.6,0.5-0.7,0.9c0,0,0,0,0,0.1c-0.7,0.7-1.3,1.4-2,2.1c-0.9,0.9-1.9,1.9-3,2.6c-0.5,0.3-1,0.4-1.5,0.6
                           c-4.2-0.2-8.4-0.4-12.5-0.7c-6.7-0.3-14.3-1.1-18.5-6.4c-6.7-8.4,0-20.5,6.6-29c3.2-4,7.5-8.5,12.5-7.4
                           c10.3,2.3,22.3,6.3,24.9,16.5C109.7,181.4,105.9,188.6,102,194.9z M123.9,148.8c-3.2,5.8-8,11.5-14.5,12.7
                           c-4.4,0.8-8.9-0.6-13.1-2c-3-1-6.1-2-8.5-4c-5.1-4.1-6.5-11.6-4.7-17.8c1.8-6.3,6.4-11.4,11.5-15.5c1.3-1.1,2.7-2.1,4.2-2.8
                           c5.1-2.4,11.8-1,15.5,3.2c2.1,2.4,5.7,2.8,8.3,4.5c3.3,2.2,5,6.4,4.9,10.4C127.4,141.5,125.8,145.3,123.9,148.8z M143,105
                           c0.8,3,1.6,6,1.4,9.1s-1.6,6.2-4.1,7.9c-2.5,1.7-5.6,1.8-8.6,1.8c-4,0.1-8.1,0.1-11.6-1.5c-4-1.8-6.9-5.6-8.4-9.8
                           c-1.5-4.1-1.7-8.6-1.6-13c0.2-4.3,0.8-9,3.8-12.2c4.7-5.2,13.5-4.5,19-0.1C138.3,91.4,141,98.3,143,105z M169.3,97.2
                           c-3.9,2.5-9.2,1.3-13.1-1.1s-7.3-5.7-11.5-7.8c-4.5-2.2-10.4-3.4-12.3-8c-2.2-5.1,2.2-10.6,6.5-14.1c2.3-1.9,4.8-3.6,7.3-5.2
                           c4.6-2.9,9.8-5.5,15.3-5.2s11,4.3,11.4,9.8c0.1,1.8-0.3,3.5-0.5,5.2c-0.4,4.6,1,9.2,1.4,13.9C174.3,89.4,173.2,94.7,169.3,97.2z
                           M215.9,55.6c-0.9,3.8-1.7,7.6-2.6,11.4c-0.9,4-1.8,8.1-4,11.6c-2.2,3.5-5.8,6.4-9.9,6.4c-5.8,0.1-10.2-4.9-13.8-9.5
                           c-4.6-5.9-9.2-12-11.4-19.2c-0.6-2-1-4.2-0.1-6.1c1-2.1,3.4-3.2,5.6-4c6.5-2.4,13.2-4.1,20-5c3.1-0.4,6.3-0.7,9.4,0.1
                           c3,0.8,5.9,2.8,7,5.7C217.1,49.8,216.5,52.8,215.9,55.6z M253.2,78.2c-2.8,3.4-6.1,7.1-10.5,7.4c-7.2,0.6-11.7-7.5-14.3-14.3
                           c-1.9-5-3.9-10.1-5.8-15.1c-1.4-3.6-2.7-7.7-0.9-11c2-3.9,7.2-4.9,11.6-5.1c9.6-0.4,19.3,0.9,28.5,3.6c2.6,1.2,5.4,2.4,7.4,4.3
                           c1,1,2,1.8,3,2.6C266.5,60.3,260.2,69.5,253.2,78.2z M285.7,94.2c-4.4,1.9-10.3,3.5-13.3-0.2c-1.7-2-1.8-5-1.7-7.6l0.4-23.8
                           c0.1-5.1,5.8-8.5,10.8-8.2c5,0.4,9.5,3.4,13.6,6.3c2.6,1.9,5.3,3.7,7.9,5.6c4.3,3,9,7.2,8.1,12.3c-0.8,4.4-5.5,6.9-9.7,8.6
                           C296.5,89.6,291.1,91.9,285.7,94.2z M305.4,123.7c-2.5-0.8-4.9-2.2-6.6-4.2c-4-4.9-3-12.1-0.9-18c1.7-4.8,4.3-9.8,9-11.6
                           c3.4-1.3,7.1-0.7,10.7-0.6c4,0.2,8-0.2,11.8,1.1c3.8,1.2,7.2,4.7,6.8,8.6c-0.8,7.5-4,14.8-9.7,19.8
                           C320.7,123.7,312.5,126,305.4,123.7z M322.8,157.1c-6-5.6-10.1-13.2-11.6-21.2c0-1.1,0-2.1,0-3.2c0.1-1.8,0.7-2.9,1.8-4.2
                           c0.2-0.2,0.2-0.4,0.2-0.6c8-0.9,15.2-5.7,21.2-11.1c1.9,1.1,3.9,2.1,6,2.8c3.1,1.8,6.6,3.6,9.3,5.9c0.7,0.6,1.2,1.4,1.9,2
                           c0.2,0.2,0.4,0.3,0.6,0.5c0,0,0,0,0,0c0.7,0.5,1.4,1,2,1.5c0.4,0.6,0.8,1.2,1.1,1.8c0.2,0.3,0.5,0.5,0.8,0.5
                           c3,4.4,3.6,10.5,1.6,15.5c-2.6,6.8-8.9,11.6-15.7,14.1c-3,1.1-6.1,1.7-9.3,1.3C329,162.1,325.6,159.8,322.8,157.1z M341.6,198.1
                           c-1-1.1-2.5-2.8-3.5-4.5l-0.8-2.2c0-0.1,0-0.1,0-0.2c0-0.3-0.1-0.5-0.3-0.7l-1.3-3.8c-1.6-4.7-3.3-9.8-1.5-14.5
                           c3.7-9.8,18.7-9.8,24.5-18.5c2.3-3.4,7.5-0.5,10.1,2.7c5.9,7.2,10.8,15.2,14.5,23.7c0.9,2,1.7,4.1,1.4,6.3c-0.5,4.3-5,6.8-9,8.6
                           c-10.1,4.6-21.5,9.2-31.8,5.3C343.1,199.7,342.3,198.9,341.6,198.1z M347.3,212.8c-0.2-1.5-0.2-3.3,0.2-4.5
                           c11.8-4.2,23.7-8.3,35.5-12.5c1.7-0.6,3.4-1.2,5.2-0.9c2.3,0.3,4.2,2.1,5.6,4c4.5,6.2,5.3,14.2,5.9,21.8
                           c0.4,5.3,0.7,11.2-2.7,15.2c-2.3,2.8-5.9,4.1-9.4,5.1c-5.9,1.7-12,2.8-18.2,3.3c-3.1,0.2-6.5,0.2-8.8-1.9
                           c-1.3-1.2-2.1-2.9-2.8-4.6C354.3,229.6,350.8,221.2,347.3,212.8z M360.8,269.5c-0.6-3.5-2-6.9-2.5-10.4s0.2-7.6,3-9.8
                           c1.9-1.4,4.3-1.8,6.6-2c8.9-0.7,18.5,0.3,25.9-4.8c3-2,7.2-0.5,9.8,2.1c7.1,6.8,6.6,18.1,5.7,27.9c-0.7,7.5-2.3,16.3-9.1,19.5
                           c-2.9,1.4-6.1,1.4-9.3,1.4c-3.8,0-7.6,0.1-11.4,0.1c-5.3,0-11.3-0.2-15-4.1C359.9,284.3,361.9,276.3,360.8,269.5z M402.2,323.4
                           c-2.2,4.8-4.6,9.9-9,12.8c-5.7,3.8-13.2,3-20,1.9c-4-0.7-8.1-1.4-11.6-3.6s-6.2-5.9-5.8-10c0.6-7.6,2.2-15.1,4.8-22.3
                           c0.9-2.4,2.1-5,4.5-6c1.7-0.7,3.6-0.3,5.4,0c9.6,1.7,19.4,1.4,28.9-0.6c1.3-0.3,2.7-0.6,4-0.3c2.4,0.7,3.6,3.4,4,5.9
                           C408.6,308.9,405.3,316.4,402.2,323.4z"/>
                     <path id="XMLID_33_" fill="#010101" d="M79.4,509.8c0.8,0.4,0.3,1.7-0.6,1.9c-0.8,0.2-1.7-0.2-2.6-0.3c-3.9-0.5-6.7,4.6-10.6,4.6
                           c0-1.1,0.7-2,1.3-2.8c2-2.6,4-5.1,6-7.7C73.4,508.4,76.5,510.5,79.4,509.8z"/>
                     <path id="XMLID_32_" fill="#010101" d="M64.9,501.7c0.4-2.9,0.9-5.8,1.3-8.7c0.2-1.2,0.5-2.6,1.7-3c2.3,3.5,6.6,5.5,10.8,5
                           c-1.9,2.6-6,1.8-9,0.7C69.5,498.5,67.5,501.1,64.9,501.7z"/>
                     <path id="XMLID_30_" fill="#010101" d="M380.9,376.7c0.2-0.9,1.6-0.7,2.1,0.1s0.2,1.7,0.2,2.6c-0.3,4.6,4.5,8.7,9,7.8
                           c-0.7,1.9-2.6,3.2-4.6,3.2C383.2,387.4,380.5,382,380.9,376.7z"/>
                     <path id="XMLID_25_" fill="#010101" d="M339.6,130.4c2.1,3.2,3.6,6.8,4.7,10.5c-4.2-1.5-9.2,2.8-8.3,7.2c-2-0.7-2.5-3.5-1.7-5.5
                           c0.8-2,2.3-3.6,3.3-5.5c1-1.9,1.3-4.5-0.2-5.9C338.1,130.9,338.8,130.7,339.6,130.4z"/>
                     <path id="XMLID_21_" fill="#010101" d="M381.7,454.8c-1.1-0.5-0.7-2.4,0.4-3.1c1.1-0.7,2.4-0.7,3.5-1.3c3-1.5,3.3-5.5,3.2-8.8
                           c1.3-0.3,2.3,1.4,2.2,2.8c0,1.4-0.5,2.9,0.2,4c0.8,1.4,2.7,1.5,4.2,2c1.5,0.5,3.1,2.2,2.1,3.5
                           C392.5,451.9,386.5,452.2,381.7,454.8z"/>
                     <path id="XMLID_20_" fill="#010101" d="M386.8,329.1c0.4-5-2.7-10.1-7.4-12.1c0.1-1.4,2.3-1,3.6-0.6c2.7,0.9,5.8,0.1,7.7-2
                           c1.2,0.2,1.1,2.1,0.3,3c-0.8,0.9-2.1,1.4-2.7,2.5c-0.9,1.7,0.4,3.6,1,5.4S388.4,330,386.8,329.1z"/>
                     <path id="XMLID_19_" fill="#010101" d="M113.4,601.5c-0.9-0.8-0.7-2.2-0.4-3.3c0.4-2,0.8-4,1.2-5.9c0.9-4.5,3.8-8.6,7.7-10.9
                           c1,1.4,0.4,3.4-0.7,4.8s-2.5,2.5-3.3,4c-1,1.9-1,4.1-1.3,6.2C116.3,598.5,115.4,600.8,113.4,601.5z"/>
                     <path id="XMLID_18_" fill="#010101" d="M388.3,481.6c-0.7,3-1.5,6.2-3.5,8.5c-2.1,2.3-5.9,3.4-8.4,1.5c5.6-2.1,9.5-8.1,9.2-14.1
                           c0.7-0.8,2.2-0.3,2.7,0.7C388.8,479.3,388.6,480.5,388.3,481.6z"/>
                     <path id="XMLID_15_" fill="#010101" d="M155,66.8c1.3-0.8,3.7-1,3.7,0.5c-5.6,3.5-11.1,7-16.8,10.4c-1.6-1,0.1-3.4,1.7-4.3
                           C147.4,71.2,151.2,69,155,66.8z"/>
                     <path id="XMLID_13_" fill="#010101" d="M56,411.5c-1.4-1.5,0.9-3.6,2.6-4.9c3.5-2.6,5.3-7.3,4.4-11.6c0.9-1,2.8-0.5,3.4,0.7
                           c0.6,1.2,0.4,2.7-0.1,4C64.7,404.8,60.8,409,56,411.5z"/>
                     <path id="XMLID_7_" fill="#010101" d="M55,311.6c-1.1,2.8-3.7,4.9-6.7,5.5c2.3-2.7,3.9-6.1,4.5-9.7c2.3,0.8,4.7,1.6,7,2.4
                           c0.9,0.3,2.1,1.3,1.4,2c-1.2,1.4-0.2,3.6,0.4,5.3c0.7,1.8,0.4,4.5-1.5,4.6C59.7,317.9,57.8,314.3,55,311.6z"/>
                     <path id="XMLID_6_" fill="#010101" d="M47.9,271.6c1.3-4.4,3-8.8,4.9-13c1.6,0.1,2.4,2.2,2,3.7c-0.4,1.6-1.5,2.9-2,4.4
                           c-0.6,1.5-0.3,3.6,1.1,4.2c2.7,1.1,4.3,4.2,3.8,7.1c-1,1-2.2-0.9-2.6-2.2C54.3,272.8,50.8,270.2,47.9,271.6z"/>
                     </g>
                     </g>


                     </svg>

</div>
<input type="hidden" name="patient_id" value="' . $patient->id . '"></input>
<div style=" float:left; width:50%; margin-top:80px; ">
                     <label style="font-size: 20px;">' . lang('description') . '</label>
                     <textarea style=" width:100%; height:150px; padding:10px; font-size:1.2em; resize:none;" name="description">' . $description . '</textarea>
                     <input type="submit" class="btn btn-info btn-md" style="color: #fff; background-color: #39B27C !important;" value="' . lang('submit') . '">
</div>    
<input type="hidden" name="redirect" value="popuphome">
</form>

         </div>
     </div>';
            } else {
                $option = ' ';
            }
        } else {
            $option = ' ';
        }

        $data['view'] = '
        <section class="col-md-3">
            <header class="card-header clearfix">
                <div class="">
                    ' . lang("patient") . ' ' . lang("info") . ' 
                </div>

            </header> 




            <aside class="profile-nav">
                <section>
                    <div class="user-heading round text-center">
                        ' . $profile_image . '
                        <h1>' . $patient->name . '</h1>
                        <p> ' . $patient->email . ' </p>
                    </div>
            
                    <ul class="nav flex-column p-3">
                        <li class="nav-item active">
                            ' . lang("patient") . ' ' . lang("name") . '<span class="float-end r-activity">' . $patient->name . '</span>
                        </li>
                        <li class="nav-item">
                            ' . lang("patient_id") . ' <span class="float-end r-activity">' . $patient->id . '</span>
                        </li>
                        <li class="nav-item">
                            ' . lang("phone") . '<span class="float-end r-activity">' . $patient->phone . '</span>
                        </li>
                        <li class="nav-item">
                            ' . lang("email") . '<span class="float-end r-activity">' . $patient->email . '</span>
                        </li>
                        <li class="nav-item">
                            ' . lang("gender") . '<span class="float-end r-activity">' . $patient->sex . '</span>
                        </li>
                        <li class="nav-item">
                            ' . lang("birth_date") . '<span class="float-end r-activity">' . $patient->birthdate . '</span>
                        </li>
                        <li class="nav-item" style="height: 200px;">
                            ' . lang("address") . '<span class="float-end" style="height: 200px;">' . $patient->address . '</span>
                        </li>
                    </ul>
            
                </section>
           </aside>
        


        </section>





        <section class="col-md-9">
            <header class="card-header clearfix">
                <div class="col-md-7">
                    ' . lang("history") . ' | ' . $patient->name . '
                </div>

            </header>

            <section class="card-body">   
            <header class="card-header tab-bg-dark-navy-blueee">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#vital">' . lang("vital_signs") . '</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#appointments">' . lang("appointments") . '</a>
                </li>
                <li class="nav-item">' . $li_option . '</li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#home">' . lang("case_history") . '</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#prescription">' . lang("prescription") . '</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#lab">' . lang("lab") . '</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#profile">' . lang("documents") . '</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#bed">' . lang("bed") . '</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#timeline">' . lang("timeline") . '</a>
                </li>
            </ul>
        </header>
        
                <div class="card">
                    <div class="tab-content">
                    <div id="vital" class="tab-pane active">
                     <div class="">

                        <div class="adv-table editable-table ">
                        <table class="table table-bordered table-hover" id="">
                                <thead>
                                    <tr style="font-size:10px !important;">
                                        <th>' . lang("date_time") . '</th>
                                        <th>' . lang("heart_rate") . '</th>
                                        <th>' . lang("blood_pressure") . '</th>
                                        <th>' . lang("temp") . '</th>
                                        <th>' . lang("oxygen_saturation") . '</th>
                                        <th>' . lang("respiratory_rate") . '</th>
                                        <th>' . lang("bmi_weight") . '</th>
                                        <th>' . lang("bmi_height") . '</th>
                                       
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $all_vitals . '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                        <div id="appointments" class="tab-pane">
                            <div class="">

                                <div class="adv-table editable-table ">
                                    <table class="table table-bordered table-hover" id="">
                                        <thead>
                                            <tr>
                                                <th>' . lang("date") . '</th>
                                                <th>' . lang("time_slot") . '</th>
                                                <th>' . lang("doctor") . '</th>
                                                <th>' . lang("status") . '</th>
                                                <th>' . lang("option") . '</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            ' . $all_appointments . '
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>' . $option . '
                        <div id="home" class="tab-pane">
                            <div class="">



                                <div class="adv-table editable-table ">


                                <table class="table table-bordered table-hover" id="">
                                        <thead>
                                            <tr>
                                                <th>' . lang("date") . '</th>
                                                <th>' . lang("title") . '</th>
                                                <th>' . lang("description") . '</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            ' . $all_case . '
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
            
                                    <div id="prescription" class="tab-pane">
                                           <div class="">



                                       <div class="adv-table editable-table ">


                                    <table class="table table-bordered table-hover" id="">
                                        <thead>
                                            <tr>
                                                <th>' . lang("date") . '</th>
                                                <th>' . lang("doctor") . '</th>
                                                <th>' . lang("medicine") . '</th>
                                                <th>' . lang("options") . '</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            ' . $all_prescription . '
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                        <div id="lab" class="tab-pane"> <div class="">
                                <div class="adv-table editable-table ">
                                    <table class="table table-bordered table-hover" id="">
                                        <thead>
                                            <tr>
                                                <th>' . lang("id") . '</th>
                                                <th>' . lang("date") . '</th>
                                                <th>' . lang("doctor") . '</th>
                                                <th>' . lang("options") . '</th>
                                            </tr>
                                        </thead>
                                        <tbody>'
            . $all_lab .
            '</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                           <div id="bed" class="tab-pane"> <div class="">
                                <div class="adv-table editable-table ">
                                    <table class="table table-bordered table-hover" id="">
                                        <thead>
                                            <tr>
                                                <th>' . lang("bed_id") . '</th>
                                                <th>' . lang("alloted_time") . '</th>
                                                <th>' . lang("discharge_time") . '</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>'
            . $all_bed .
            '</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <div id="profile" class="tab-pane"> <div class="">

                                <div class="adv-table editable-table ">
                                    <div class="">
                                        ' . $all_material . '
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="timeline" class="tab-pane"> 
                            <div class="">
                                <div class="">
                                    <section class="card ">
                                        <header class="card-header">
                                            Timeline
                                        </header>


                                        ' . $timeline_value . '

                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</section>



</section>';

        echo json_encode($data);
    }

    public function getPatientinfo()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->patient_model->getPatientInfo($searchTerm);

        echo json_encode($response);
    }

    public function getPatientinfoWithAddNewOption()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->patient_model->getPatientinfoWithAddNewOption($searchTerm);

        echo json_encode($response);
    }

    public function getPatientinfoWithId()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->patient_model->getPatientInfoId($searchTerm);

        echo json_encode($response);
    }
    public function addNewFolder()
    {
        // if (!$this->ion_auth->in_group(array('admin'))) {
        //     redirect('home/permission');
        // }
        $redirect_tab = $this->input->post('redirect_tab');
        $id = $this->input->post('id');
        $folder_name = $this->input->post('folder_name');
        $folder_path = $this->input->post('folder_path');
        $patient_id = $this->input->post('patient');
        if ((empty($id))) {
            $add_date = date('m/d/y');
        } else {
            $add_date = $this->db->get_where('folder', array('id' => $id))->row()->add_date;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        $this->form_validation->set_rules('folder_name', 'Folder Name', 'trim|required|min_length[2]|max_length[500]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect("patient/medicalHistory?id=" . $patient_id . "&redirect_tab=" . $redirect_tab);
            } else {
                redirect("patient/medicalHistory?id=" . $patient_id . "&redirect_tab=" . $redirect_tab);
            }
        } else {


            $data = array();
            $data = array(
                'folder_path' => '/uploads/documents/' . $folder_name,
                'folder_name' => $folder_name,
                'patient' => $patient_id,
                'add_date' => $add_date
            );

            if (empty($id)) {
                $this->patient_model->insertFolder($data);
                show_swal(lang('folder_added_successfully'), 'success', lang('added'));
            } else {
                $this->patient_model->updateFolder($id, $data);
                show_swal(lang('folder_updated_successfully'), 'success', lang('updated'));
            }


            redirect("patient/medicalHistory?id=" . $patient_id . "&redirect_tab=" . $redirect_tab);
        }
    }

    function editFolderByJason()
    {
        $id = $this->input->get('id');
        $data['folder'] = $this->patient_model->getFolderById($id);
        echo json_encode($data);
    }

    function delet()
    {
        $data = array();
        $id = $this->input->get('id');
        $user_data = $this->db->get_where('patient', array('id' => $id))->row();
        $path = $user_data->img_url;

        if (!empty($path)) {
            unlink($path);
        }
        $ion_user_id = $user_data->ion_user_id;
        $this->db->where('id', $ion_user_id);
        $this->db->delete('users');
        $this->patient_model->delete($id);
        show_swal(lang('patient_deleted'), 'warning', lang('deleted'));
        redirect('patient');
    }

    function deleteFolder()
    {
        $data = array();
        $id = $this->input->get('id');
        $patient = $this->input->get('patient');
        $folder = $this->patient_model->getFolderById($id);
        $delete = $this->patient_model->deleteFolder($id);
        $redirect_tab = 'files';
        $user_data = $this->db->get_where('patient_material', array('folder' => $id))->row();
        $path = $user_data->url;
        if (!empty($path)) {

            unlink($path);
        }
        $delete = $this->patient_model->deletePatientMaterialByFolderId($id);
        foreach ($delete as $del) {

            $del;
        }

        show_swal(lang('folder_deleted'), 'warning', lang('deleted'));
        redirect("patient/MedicalHistory?id=" . $folder->patient . "&redirect_tab=" . $redirect_tab);
    }

    function getPatientMaterialByPatientIdByJason()
    {
        $id = $this->input->get('id');
        $data['patientMaterialByPatientId'] = $this->patient_model->getPatientMaterialByPatientId($id);
        echo json_encode($data);
    }

    function medicalHistoryByFolder()
    {
        $data = array();
        $id = $this->input->get('id');

        $data['patient'] = $this->patient_model->getPatientById($id);
        $data['folder'] = $this->patient_model->getFolderById($id);
        $data['patient_materials'] = $this->patient_model->getPatientMaterialByFolderId($id);
        $this->load->view('home/dashboard');
        $this->load->view('medical_history_by_folder', $data);
        $this->load->view('home/footer');
    }

    function deletePatientMaterialInFolder()
    {
        $id = $this->input->get('id');
        $redirect = $this->input->get('redirect');
        $patient_material = $this->patient_model->getPatientMaterialById($id);
        $path = $patient_material->url;
        if (!empty($path)) {
            unlink($path);
        }
        $this->patient_model->deletePatientMaterial($id);
        show_swal(lang('document_deleted'), 'warning', lang('deleted'));
        if ($redirect == 'documents') {
            redirect('patient/documents');
        } else {
            redirect("patient/medicalHistoryByFolder?id=" . $patient_material->folder);
        }
    }
    public function addNewVitalSign()
    {
        $id = $this->input->post('id');
        $patient_id = $this->input->post('patient');
        $bmi_height = $this->input->post('bmi_height');
        $bmi_weight = $this->input->post('bmi_weight');
        $redirect_tab = $this->input->post('redirect_tab');
        $respiratory_rate = $this->input->post('respiratory_rate');
        $oxygen_saturation = $this->input->post('oxygen_saturation');
        $temperature = $this->input->post('temperature');
        $diastolic_blood_pressure = $this->input->post('diastolic_blood_pressure');
        $systolic_blood_pressure = $this->input->post('systolic_blood_pressure');
        $heart_rate = $this->input->post('heart_rate');
        if (empty($id)) {
            $add_date_time = date('d-m-Y H:i:s');
        }
        $data = array(
            'patient_id' => $patient_id,
            'bmi_height' => $bmi_height,
            'bmi_weight' => $bmi_weight,
            'respiratory_rate' => $respiratory_rate,
            'oxygen_saturation' => $oxygen_saturation,
            'temperature' => $temperature,
            'diastolic_blood_pressure' => $diastolic_blood_pressure,
            'systolic_blood_pressure' => $systolic_blood_pressure,
            'heart_rate' => $heart_rate,


        );
        if (empty($id)) {
            $data['add_date_time'] = $add_date_time;
            $this->patient_model->insertVitalSign($data);
            show_swal(lang('vital_signs_added_successfully'), 'success', lang('added'));
        } else {
            $this->patient_model->updateVitalSign($id, $data);
            show_swal(lang('vital_signs_updated_successfully'), 'success', lang('updated'));
        }
        redirect('patient/medicalHistory?id=' . $patient_id . "&redirect_tab=" . $redirect_tab);
    }
    function deleteVitalSign()
    {
        $id = $this->input->get('id');
        $redirect_tab = 'vital';
        $patient_id = $this->patient_model->getVitalSignById($id)->patient_id;
        $this->patient_model->deleteVitalSign($id);
        show_swal(lang('vital_signs_deleted'), 'warning', lang('deleted'));
        redirect("patient/MedicalHistory?id=" . $patient_id . "&redirect_tab=" . $redirect_tab);
    }
    function editVitalSignByJason()
    {
        $id = $this->input->get('id');
        $data['vital_sign'] = $this->patient_model->getVitalSignById($id);

        echo json_encode($data);
    }
    function odontogram()
    {

        $data = $_POST['tooth'];
        $patient_id = $_POST['patient_id'];
        $redirect_tab = $this->input->post('redirect_tab');
        $description = $_POST['description'];
        $value = array(
            'description' => $description,
            'Tooth1' => $data['Tooth1'],
            'Tooth2' => $data['Tooth2'],
            'Tooth3' => $data['Tooth3'],
            'Tooth4' => $data['Tooth4'],
            'Tooth5' => $data['Tooth5'],
            'Tooth6' => $data['Tooth6'],
            'Tooth7' => $data['Tooth7'],
            'Tooth8' => $data['Tooth8'],
            'Tooth9' => $data['Tooth9'],
            'Tooth10' => $data['Tooth10'],
            'Tooth11' => $data['Tooth11'],
            'Tooth12' => $data['Tooth12'],
            'Tooth13' => $data['Tooth13'],
            'Tooth14' => $data['Tooth14'],
            'Tooth15' => $data['Tooth15'],
            'Tooth16' => $data['Tooth16'],
            'Tooth17' => $data['Tooth17'],
            'Tooth18' => $data['Tooth18'],
            'Tooth19' => $data['Tooth19'],
            'Tooth20' => $data['Tooth20'],
            'Tooth21' => $data['Tooth21'],
            'Tooth22' => $data['Tooth22'],
            'Tooth23' => $data['Tooth23'],
            'Tooth24' => $data['Tooth24'],
            'Tooth25' => $data['Tooth25'],
            'Tooth26' => $data['Tooth26'],
            'Tooth27' => $data['Tooth27'],
            'Tooth28' => $data['Tooth28'],
            'Tooth29' => $data['Tooth29'],
            'Tooth30' => $data['Tooth30'],
            'Tooth31' => $data['Tooth31'],
            'Tooth32' => $data['Tooth32'],

        );
        $this->patient_model->odontogram($patient_id, $value);
        if (!empty($this->input->post('redirect'))) {
            redirect('home');
        } else {
            redirect("patient/MedicalHistory?id=" . $patient_id . "&redirect_tab=" . $redirect_tab);
        }
    }
    function getDepositByInvoiceIdForDeposit()
    {
        $id = $this->input->get('id');
        $deposit = $this->finance_model->getDepositByInvoiceId($id);
        $payment = $this->finance_model->getPaymentById($id);
        $data['patient'] = $this->patient_model->getPatientById($payment->patient);
        if (!empty($deposit)) {
            foreach ($deposit as $depos) {
                $deposits[] = $depos->deposited_amount;
            }
            $data['response'] = $payment->gross_total - array_sum($deposits);
        } else {
            $data['response'] = $payment->gross_total;
        }
        echo json_encode($data);
    }




    function download()
    {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['payment'] = $this->finance_model->getPaymentById($id);
        $payment = $this->finance_model->getPaymentById($id);
        $settings1 = $this->settings_model->getSettings();
        $patient_info = $this->db->get_where('patient', array('id' => $data['payment']->patient))->row();
        if (!empty($patient_info)) {
            $age = explode('-', $patient_info->age);
        }
        if (!empty($patient_info)) {
            if ($patient_info->sex == 'Male') {
                $sex = lang('male');
            } else {
                $sex = lang('female');
            }
        }
        $numlength = strlen((string) $data['payment']->id);
        $remaining = 10 - $numlength;
        $patient_info_id = '';
        $invoice_id = '';
        if ($remaining < 10) {
            for ($i = 0; $i < $remaining; $i++) {
                $invoice_id .= '0';
                $patient_info_id .= '0';
            }
            $patient_info_id .= $patient_info->id;
            $invoice_id .=  $data['payment']->id;
        } else {
            $invoice_id =  $data['payment']->id;
            $patient_info_id = $patient_info->id;
        }

        if (!empty($payment->doctor)) {
            $doc_details = $this->doctor_model->getDoctorById($payment->doctor);
            if (!empty($doc_details)) {
                $doctor = $doc_details->name . ' - <br>' . $doc_details->profile . ' <br>';
            } else {
                $doctor = $payment->doctor_name . ' <br>';
            }
        }



        error_reporting(0);
        $data['redirect'] = 'download';
        $header = '<div id="invoice_header" style="width:100%;">
                        <table class="info_rer">
                            <tr class="tr_info">

                                <td id="first_td" style="">
                                    <img class="img_class_logo" alt="" src="' . $this->settings_model->getSettings()->logo . '" width="120">
                                    <br>
                                    
                                        <div style="">
                                        
                                        <strong id="invoice_word">' . lang('invoice') . '</strong><br>
                                       
                                        
                                        
                                        <span class="invoice_id">' . $invoice_id . '</span>

                                       
                                        </div>
                                </td>
                                <td class="second_td">
                                   
                                        <div class="paragraphprint col-md-12" style="padding-top:-20px;">
                                            <p style="font-size:18px !important;margin-top: -20px; font-weight:800; ">
                                            <h4>
                                                ' . $settings1->title . '
                                            </h4><br>
                                            <h6 style="font-size:18px !important;margin-top: -20px; ">
                                            ' . $settings1->address . '
                                            </h6>
                                            <h4>
                                                Phone: ' . $settings1->phone . '
                                            </h4>
                                            </p>


                                        </div>
                                   
                                </td>


                                <td class="last_td">
                                   
                                        <table class="information_table">
                                            <tr>
                                                <td colspan="2">
                                                    <div class="paragraphprint col-md-12">
                                                       
                                                        <label class="control-label"> ' . lang('name') . ' </label>
                                                        <span class="info_text"> :
                                                            
                                                               ' . $patient_info->name . ' <br>
                                                           
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>

                                                    <label class="control-label">' . lang('age') . '</label>
                                                    <span class="info_text"> :
                                                        
                                                            ' . $age[0] . ' ' . lang('y') . ' ' . $age[1] . ' ' . lang('m') . ' ' . $age[2] . ' ' . lang('d') . '<br>
                                                        
                                                    </span>

                                                </td>
                                                <td>

                                                    <label class="control-label">' . lang('gender') . ' </label>
                                                    <span class="info_text"> :
                                                        ' . $sex . '
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="paragraphprint col-md-6 info_test">

                                                        <label class="control-label">HN </label>
                                                        <span class="info_text"> :
                                                            
                                                            ' . $patient_info_id . ' <br>
                                                           
                                                        </span>
                                                    </div>

                                                </td>
                                                <td>

                                                    <div class="paragraphprint col-md-6 info_test">
                                                        <label class="control-label"> ' . lang('phone') . ' </label>
                                                        <span class="info_text"> :
                                                           ' . $patient_info->phone . '
                                                            
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="paragraphprint col-md-6 info_test">
                                                        <label class="control-label">VN </label>
                                                        <span class="info_text"> :
                                                            ' . $invoice_id . '
                                                            
                                                        </span>
                                                    </div>

                                                </td>
                                                <td>

                                                    <div class="paragraphprint col-md-6 info_test">
                                                        <label class="control-label">VN  ' . lang('date') . ' </label>
                                                        <span class="info_text"> :
                                                            
                                                              ' . date('d/m/Y', $payment->date) . '
                                                           
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="paragraphprint col-md-12 last_td_info">
                                                        <label class="control-label"> ' . lang('doctor') . ' </label>
                                                        <span class="info_text"> :
                                                            ' . $doctor . '
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                   
                                </td>

                            </tr>
                        </table>
                        <table class="table_bar" style="margin-bottom: -35px;">
                            <tr>
                                <td class="barcode_first_td">
                                    <div class="paragraphprint">
                                        <label class="control-label image_text">VN </label>
                                        <span class="info_text"> :
                                            <img class="image_bar" alt="testing" src="' . site_url('lab/barcode') . '?text=' . $invoice_id . '&print=true" />
                                        </span>
                                    </div>
                                </td>

                                <td class="barcode_third_td">
                                    <div class="paragraphprint">
                                        <label class="control-label image_text">HN </label>
                                        <span class="info_text"> :
                                            <img class="image_bar" alt="testing" src="' . site_url('lab/barcode') . '?text=' . $patient_info_id . '&print=true" />
                                        </span>
                                    </div>

                                </td>

                            </tr>
                        </table>
                    </div>';
        if ($data['settings']->invoice_choose == 'invoice1') {

            $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->SetHTMLHeader($header);
            //$this->autoMarginPadding = 300;
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetHTMLFooter('
        <div style="text-align:center;font-weight: bold; font-size: 7pt; !important;">' .
                $settings1->footer_invoice_message . '</div>', 'O');
            $html = $this->load->view('finance/invoiceA4_download', $data, true);
        } else {
            $mpdf = new \Mpdf\Mpdf(['format' => 'A5', 'setAutoTopMargin' => 'true', 'margin_top' => -30]);
            $mpdf->SetHTMLHeader($header);
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->SetHTMLHeader($header);
            $mpdf->setAutoBottomMargin = 'stretch';
            // $html = $this->load->view('fiance/invoice_test', $data, true); 
            // This was for A5 page size. 
            // We disabled A5 download for patient login by commenting this line of code.
            $html = $this->load->view('fiance/invoiceA4_download', $data, true);
            $mpdf->SetHTMLFooter('
        <div style="text-align:center;font-weight: bold; font-size: 7pt; !important;">' .
                $settings1->footer_invoice_message . '</div>', 'O');
        }

        $mpdf->WriteHTML($html);

        $filename = "invoice--00" . $id . ".pdf";
        $mpdf->Output($filename, 'D');
    }



    function chatWithGpt()
    {
        $case_id = $this->input->post('id');
        $case_details = $this->patient_model->getMedicalHistoryById($case_id);
        $session_id = $case_details->hospital_id . '-case-' . $case_id;
        $patient = $this->patient_model->getPatientById($case_details->patient);
        $patient_description = 'The name of the patient is ' . $patient->name . '. The patient is ' . $patient->age . ' years old. The patient is ' . $patient->gender;
        $message = $this->input->post('description');
        $message = "You are a doctor. Advice according to the case described here. This is the case: "  . $message;
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        $conversation_history = $this->getConversationHistory($session_id) ?: [];
        $conversation_history[] = ["role" => "user", "content" => $message];

        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "model" => "gpt-3.5-turbo",
            'messages' => $conversation_history,
            "temperature" => 0,
            "max_tokens" => 1000,
            "top_p" => 0,
            "frequency_penalty" => 0,
            "presence_penalty" => 0
        ]));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->settings->chatgpt_api_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            $responseArray = json_decode($response, true); // Convert JSON response to PHP array
            if (isset($responseArray['choices'][0]['message']['content'])) {
                $conversation_history[] = ["role" => "system", "content" => $responseArray['choices'][0]['message']['content']];
                $this->storeConversationHistory($case_id, $session_id, $conversation_history);
                echo json_encode(['message' => $responseArray['choices'][0]['message']['content']]);
            } else {
                $error = $responseArray['error']['message'] ?? 'Unknown error';
                error_log("OpenAI API Error: " . $error);
                echo json_encode(['message' => 'Message from AI: ' . $error]);
                // echo json_encode(['message' => 'No response']); // Handle no response scenario
            }
        }
        curl_close($ch);
    }





    public function storeConversationHistory($case_id, $session_id, $conversation_history)
    {
        $history_json = json_encode($conversation_history); // Convert array to JSON
        $data = array(
            'module_name' => 'case',
            'module_row_id' => $case_id,
            'session_id' => $session_id,
            'history' => $history_json
        );
        $exists = $this->db->get_where('gpt_memory', array('session_id' => $session_id))->row_array();
        if ($exists) {
            $this->db->where('session_id', $session_id);
            $this->db->update('gpt_memory', $data);
        } else {
            $this->db->insert('gpt_memory', $data);
        }
    }

    // Function to retrieve conversation history
    public function getConversationHistory($session_id)
    {
        $query = $this->db->get_where('gpt_memory', array('session_id' => $session_id));
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return json_decode($result['history'], true); // Convert JSON back into PHP array
        }
        return null;
    }


    public function getConversationHistoryAjax()
    {
        // Check for AJAX request
        if ($this->input->is_ajax_request()) {
            $session_id = $this->input->post('id');  // Assuming the session_id is sent as a POST request
            $history = $this->patient_model->getConversationHistory($session_id);
            echo json_encode(['history' => $history]);
        } else {
            // Handle non-AJAX request here
            show_error('No direct script access allowed');
        }
    }


    function patientCaseList()
    {
        $data = array();
        $id = $this->input->get('id');
        $case_id = $this->input->get('case_id');
        if ($this->ion_auth->in_group(array('Patient'))) {
            $patient_ion_id = $this->ion_auth->get_user_id();
            $id = $this->patient_model->getPatientByIonUserId($patient_ion_id)->id;
        }


        $patient_hospital_id = $this->patient_model->getPatientById($id)->hospital_id;
        if ($patient_hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }


        $data['patient'] = $this->patient_model->getPatientById($id);

        $data['case'] = $this->patient_model->getMedicalHistoryById($case_id);
        $data['medical_histories'] = $this->patient_model->getMedicalHistoryByPatientId($id);
        $data['patient_materials'] = $this->patient_model->getPatientMaterialByPatientId($id);
        $data['settings'] = $this->settings_model->getSettings();
        $data['symptoms'] = $this->symptom_model->getSymptom();
        $data['tests'] = $this->finance_model->getTest();
        $data['diagnosiss'] = $this->diagnosis_model->getDiagnosis();
        $data['treatments'] = $this->treatment_model->getTreatment();
        $data['advices'] = $this->advice_model->getAdvice();

        $this->load->view('home/dashboard');
        $this->load->view('patient_case_list', $data);
        $this->load->view('home/footer');
    }
} 
   
/* End of file patient.php */
    /* Location: ./application/modules/patient/controllers/patient.php */