<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Doctor extends MX_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('doctor_model');

        $this->load->model('department/department_model');
        $this->load->model('appointment/appointment_model');
        $this->load->model('patient/patient_model');
        $this->load->model('doctorvisit/doctorvisit_model');
        $this->load->model('prescription/prescription_model');
        $this->load->model('schedule/schedule_model');
        $this->load->model('settings/settings_model');
        $this->load->module('patient');
        $this->load->module('sms');
        if (!$this->ion_auth->in_group(array('admin', 'Accountant', 'Doctor', 'Receptionist', 'Nurse', 'Laboratorist', 'Patient'))) {
            redirect('home/permission');
        }
    }

    public function index()
    {

        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['departments'] = $this->department_model->getDepartment();
        $this->load->view('home/dashboard');
        $this->load->view('doctor', $data);
        $this->load->view('home/footer');
    }

    public function addNewView()
    {
        $data = array();
        $data['departments'] = $this->department_model->getDepartment();
        $this->load->view('home/dashboard');
        $this->load->view('add_new', $data);
        $this->load->view('home/footer');
    }

    public function addNew()
    {

        $id = $this->input->post('id');

        if (empty($id)) {
            $limit = $this->doctor_model->getLimit();
            if ($limit <= 0) {
                show_swal(lang('doctor_limit_exceed'), 'error', lang('error'));
                redirect('doctor');
            }
        }

        $redirect = $this->input->post('redirect');
        $name = $this->input->post('name');
        $degrees = $this->input->post('degrees');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $department = $this->input->post('department');
        $department_details = $this->department_model->getDepartmentById($department);
        $profile = $this->input->post('profile');

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Name Field
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Password Field
        if (empty($id)) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        }
        // Validating Email Field
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        // Validating Address Field   
        $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[1]|max_length[500]|xss_clean');
        // Validating Phone Field           
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[1]|max_length[50]|xss_clean');
        // Validating Department Field   
        $this->form_validation->set_rules('department', 'Department', 'trim|min_length[1]|max_length[500]|xss_clean');
        // Validating Phone Field           
        // $this->form_validation->set_rules('profile', 'Profile', 'trim|required|min_length[1]|max_length[50]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                $data = array();
                $data['departments'] = $this->department_model->getDepartment();
                $data['doctor'] = $this->doctor_model->getDoctorById($id);
                $this->load->view('home/dashboard');
                $this->load->view('add_new', $data);
                $this->load->view('home/footer');
            } else {
                $data = array();
                $data['setval'] = 'setval';
                $data['departments'] = $this->department_model->getDepartment();
                $this->load->view('home/dashboard');
                $this->load->view('add_new', $data);
                $this->load->view('home/footer');
            }
        } else {
            $file_name = $_FILES['img_url']['name'];
            $file_name_pieces = explode('_', $file_name);
            $new_file_name = '';
            $count = 1;
            foreach ($file_name_pieces as $piecee) {
                if ($count !== 1) {
                    $piecee = ucfirst($piecee);
                }

                $new_file_name .= $piecee;
                $count++;
            }

            $file_name1 = $_FILES['signature']['name'];
            $file_name_pieces1 = explode('_', $file_name1);
            $new_file_name1 = '';
            $count1 = 1;
            foreach ($file_name_pieces1 as $piece) {
                if ($count1 !== 1) {
                    $piece = ucfirst($piece);
                }

                $new_file_name1 .= $piece;
                $count1++;
            }

            $config1 = array(
                'file_name' => $new_file_name1,
                'upload_path' => "./uploads/",
                'allowed_types' => "gif|jpg|png|jpeg",
                'overwrite' => False,
                'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                'max_height' => "1768",
                'max_width' => "2024"
            );
            $config = array(
                'file_name' => $new_file_name,
                'upload_path' => "./uploads/",
                'allowed_types' => "gif|jpg|png|jpeg",
                'overwrite' => False,
                'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                'max_height' => "1768",
                'max_width' => "2024"
            );

            $this->load->library('Upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('img_url')) {
                $path = $this->upload->data();
                $img_url = "uploads/" . $path['file_name'];

                $this->load->library('Upload', $config1);
                $this->upload->initialize($config1);
                if ($this->upload->do_upload('signature')) {
                    $path1 = $this->upload->data();
                    $signature = "uploads/" . $path1['file_name'];
                    $data = array();
                    $data = array(
                        'img_url' => $img_url,
                        'name' => $name,
                        'degrees' => $degrees,
                        'email' => $email,
                        'address' => $address,
                        'phone' => $phone,
                        'department' => $department,
                        'department_name' => $department_details->name,
                        'profile' => $profile,
                        'signature' => $signature,
                        'appointment_confirmation' => 'Active',

                    );
                } else {
                    $data = array();
                    $data = array(
                        'img_url' => $img_url,
                        'name' => $name,
                        'degrees' => $degrees,
                        'email' => $email,
                        'address' => $address,
                        'phone' => $phone,
                        'department' => $department,
                        'department_name' => $department_details->name,
                        'profile' => $profile,

                        'appointment_confirmation' => 'Active',

                    );
                }
            } else {
                $this->load->library('Upload', $config1);
                $this->upload->initialize($config1);

                if ($this->upload->do_upload('signature')) {
                    $path1 = $this->upload->data();
                    $signature = "uploads/" . $path1['file_name'];

                    $data = array();
                    $data = array(

                        'name' => $name,
                        'degrees' => $degrees,
                        'email' => $email,
                        'address' => $address,
                        'phone' => $phone,
                        'department' => $department,
                        'department_name' => $department_details->name,
                        'profile' => $profile,
                        'signature' => $signature,
                        'appointment_confirmation' => 'Active',

                    );
                } else {

                    $data = array();
                    $data = array(
                        'name' => $name,
                        'degrees' => $degrees,
                        'email' => $email,
                        'address' => $address,
                        'phone' => $phone,
                        'department' => $department,
                        'profile' => $profile,
                        'department_name' => $department_details->name,

                        'appointment_confirmation' => 'Active',

                    );
                }
            }
            $username = $this->input->post('name');
            if (empty($id)) {     // Adding New Doctor
                if ($this->ion_auth->email_check($email)) {
                    show_swal(lang('this_email_address_is_already_registered'), 'warning', lang('warning'));
                    redirect('doctor/addNewView');
                } else {
                    $dfg = 4;
                    $this->ion_auth->register($username, $password, $email, $dfg);
                    $ion_user_id = $this->db->get_where('users', array('email' => $email))->row()->id;
                    $this->doctor_model->insertDoctor($data);
                    $doctor_user_id = $this->db->get_where('doctor', array('email' => $email))->row()->id;
                    $id_info = array('ion_user_id' => $ion_user_id);
                    $this->doctor_model->updateDoctor($doctor_user_id, $id_info);
                    $this->hospital_model->addHospitalIdToIonUser($ion_user_id, $this->hospital_id);
                    $base_url = str_replace(array('http://', 'https://', ' '), '', base_url()) . "auth/login";
                    //sms
                    $set['settings'] = $this->settings_model->getSettings();
                    $autosms = $this->sms_model->getAutoSmsByType('doctor');
                    $message = $autosms->message;
                    $to = $phone;
                    $name1 = explode(' ', $name);
                    if (!isset($name1[1])) {
                        $name1[1] = null;
                    }
                    $data1 = array(
                        'firstname' => $name1[0],
                        'lastname' => $name1[1],
                        'name' => $name,
                        'base_url' => $base_url,
                        'email' => $email,
                        'password' => $password,
                        'department' => $department,
                        'company' => $set['settings']->system_vendor
                    );

                    if ($autosms->status == 'Active') {
                        $messageprint = $this->parser->parse_string($message, $data1);
                        $data2[] = array($to => $messageprint);
                        $this->sms->sendSms($to, $message, $data2);
                    }
                    //end
                    //email

                    $autoemail = $this->email_model->getAutoEmailByType('doctor');
                    if ($autoemail->status == 'Active') {
                        $mail_provider = $this->settings_model->getSettings()->emailtype;
                        $settngs_name = $this->settings_model->getSettings()->system_vendor;
                        $email_Settings = $this->email_model->getEmailSettingsByType($mail_provider);
                        if (!empty($email_Settings->user)) {
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
                            if (function_exists('mail')) {
                                $this->email->send();
                            }
                        }
                    }

                    //end


                    show_swal(lang('new_doctor_added_successfully'), 'success', lang('added'));
                }
            } else { // Updating Doctor
                $doctor_details = $this->doctor_model->getDoctorById($id);
                if ($email != $doctor_details->email) {
                    if ($this->ion_auth->email_check($email)) {
                        show_swal(lang('this_email_address_is_already_registered'), 'warning', lang('warning'));
                        redirect("doctor/editDoctor?id=" . $id);
                    }
                }
                $ion_user_id = $this->db->get_where('doctor', array('id' => $id))->row()->ion_user_id;
                if (empty($password)) {
                    $password = $this->db->get_where('users', array('id' => $ion_user_id))->row()->password;
                } else {
                    $password = $this->ion_auth_model->hash_password($password);
                }
                $this->doctor_model->updateIonUser($username, $email, $password, $ion_user_id);
                $this->doctor_model->updateDoctor($id, $data);
                show_swal(lang('doctor_details_updated'), 'success', lang('updated'));
            }
            // Loading View
            if ($redirect == 'doctor') {
                redirect('doctor/details');
            } else {
                redirect('doctor');
            }
        }
    }

    function editDoctor()
    {
        $data = array();
        $data['departments'] = $this->department_model->getDepartment();
        $id = $this->input->get('id');
        $data['doctor'] = $this->doctor_model->getDoctorById($id);
        $this->load->view('home/dashboard');
        $this->load->view('add_new', $data);
        $this->load->view('home/footer');
    }

    function details()
    {

        $data = array();

        if ($this->ion_auth->in_group(array('Doctor'))) {

            $doctor_ion_id = $this->ion_auth->get_user_id();
            $id = $this->doctor_model->getDoctorByIonUserId($doctor_ion_id)->id;
            $data['doctor'] = $this->doctor_model->getDoctorById($id);
            $data['todays_appointments'] = $this->appointment_model->getAppointmentByDoctorByToday($id);
            $data['appointments'] = $this->appointment_model->getAppointmentByDoctor($id);
            $data['patients'] = $this->patient_model->getPatient();
            $data['appointment_patients'] = $this->patient->getPatientByAppointmentByDctorId($id);
            $data['doctors'] = $this->doctor_model->getDoctor();
            $data['prescriptions'] = $this->prescription_model->getPrescriptionByDoctorId($id);
            $data['holidays'] = $this->schedule_model->getHolidaysByDoctor($id);
            $data['schedules'] = $this->schedule_model->getScheduleByDoctor($id);
            $data['departments'] = $this->department_model->getDepartment();
            $this->load->view('home/dashboard');
            $this->load->view('details', $data);
            $this->load->view('home/footer');
        } else {
            redirect('home');
        }
    }

    function editDoctorByJason()
    {
        $id = $this->input->get('id');
        $data['doctor'] = $this->doctor_model->getDoctorById($id);
        if ($data['doctor']->department == null && $data['doctor']->department_name != null) {
            $department_details = $this->department_model->getDepartmentByName($data['doctor']->department_name);
            if ($department_details) {
                $department_id = $department_details->id;
            }
            $data['doctor']->department = $department_id;
        }
        echo json_encode($data);
    }

    function delete()
    {

        if ($this->ion_auth->in_group(array('Patient'))) {
            redirect('home/permission');
        }

        $data = array();
        $id = $this->input->get('id');
        $user_data = $this->db->get_where('doctor', array('id' => $id))->row();
        $path = $user_data->img_url;
        $path1 = $user_data->signature;

        if (!empty($path)) {
            unlink($path);
        }
        if (!empty($path1)) {
            unlink($path);
        }
        $ion_user_id = $user_data->ion_user_id;
        $this->db->where('id', $ion_user_id);
        $this->db->delete('users');
        $this->doctor_model->delete($id);
        show_swal(lang('a_doctor_deleted_from_the_list'), 'warning', lang('deleted'));
        redirect('doctor');
    }

    function getDoctor()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "email",
            "3" => "phone",
            "4" => "department",
            "5" => "profile",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['doctors'] = $this->doctor_model->getDoctorBysearch($search, $order, $dir);
            } else {
                $data['doctors'] = $this->doctor_model->getDoctorWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['doctors'] = $this->doctor_model->getDoctorByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['doctors'] = $this->doctor_model->getDoctorByLimit($limit, $start, $order, $dir);
            }
        }


        $i = 0;
        foreach ($data['doctors'] as $doctor) {
            $i = $i + 1;
            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) {
                $options1 = '<a type="button" class="btn btn-primary btn-sm editbutton mr-1" title="' . lang('edit') . '" data-toggle="modal" data-id="' . $doctor->id . '"><i class="fa fa-edit"></i> ' . lang('') . '</a>';
            }
            $options2 = '<a class="btn btn-success btn-sm detailsbutton mr-1" title="' . lang('appointments') . '" href="appointment/getAppointmentByDoctorId?id=' . $doctor->id . '"><i class="fa fa-calendar"></i> ' . lang('appointments') . '</a>';

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) {
                $options3 = '<a class="btn btn-danger btn-sm delete_button mr-1" title="' . lang('delete') . '" href="doctor/delete?id=' . $doctor->id . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"></i> ' . lang('') . '</a>';
            }

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) {
                $options4 = '<a href="schedule/holidays?doctor=' . $doctor->id . '" class="btn btn-warning btn-sm mr-1" data-id="' . $doctor->id . '"><i class="fa fa-book"></i> ' . lang('holiday') . '</a>';
                $options5 = '<a href="schedule/timeSchedule?doctor=' . $doctor->id . '" class="btn btn-secondary btn-sm mr-1" data-id="' . $doctor->id . '"><i class="fa fa-book"></i> ' . lang('time_schedule') . '</a>';
                $options6 = '<a type="button" class="btn btn-info btn-sm detailsbutton inffo mr-1" title="' . lang('info') . '" data-toggle="modal" data-id="' . $doctor->id . '"><i class="fa fa-info"></i> ' . lang('') . '</a>';
            }


            $department_details = $this->department_model->getDepartmentById($doctor->department);
            if (!empty($department_details)) {
                $depart = $department_details->name;
            } else {
                $depart = $doctor->department_name;
            }



            $dropdownOptions = '';
            if (!empty($options6) || !empty($options1) || !empty($options2) || !empty($options4)  || !empty($options5)  || !empty($options3)) {
                $dropdownOptions = '
            <div class="btn-group">
            <button type="button" class="btn btn-info btn-xs label-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
            <i class="fas fa-bars"></i> ' . lang('actions') . ' <span class="caret"></span>
        </button>
                <ul class="dropdown-menu">
                    ' . ($options6 ? '<li><a class="detailsbutton inffo"' . lang('info') . '" data-toggle = "modal" data-id="' . $doctor->id . '"> <i class="fa fa-file-invoice"></i> ' . lang('info') . ' </a></li>' : '') . '
                    ' . ($options1 ? '<li><a class="editbutton" title="' . lang('edit') . '" data-toggle = "modal" data-id="' . $doctor->id . '">  <i class="fa fa-edit"></i> ' . lang('edit') . '</a></li>' : '') . '
                    ' . ($options2 ? '<li><a  href="appointment/getAppointmentByDoctorId?id=' . $doctor->id . '"> <i class="fa fa-print"></i> ' . lang('appointments') . ' </a></li>' : '') . '
                    ' . ($options4 ? '<li><a href="schedule/holidays?doctor=' . $doctor->id . '" > <i class="fa fa-money-check"></i> ' . lang('holiday') . ' ' . lang('') . ' </a></li>' : '') . '
                    ' . ($options5 ? '<li><a href="' . site_url("schedule/timeSchedule?doctor=" . $doctor->id) . '" > <i class="fa fa-book"></i> ' . lang('time_schedule') . ' </a></li>' : '') . '
                    ' . ($options6 ? '<li><a href="' . site_url("doctor/delete?id=" . $doctor->id) . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"> <i class="fa fa-trash"></i> ' . lang('delete') . ' </a></li>' : '') . '

                </ul>
            </div>';
            }




            $profile_display = '';
            if (!empty($doctor->degrees)) {
                $profile_display .= '<div class="text-primary font-weight-bold">' . html_escape($doctor->degrees) . '</div>';
            }
            if (!empty($doctor->profile)) {
                $profile_display .= '<div>' . $doctor->profile . '</div>';
            }

            $info[] = array(
                $doctor->id,
                $doctor->name,
                $doctor->email,
                $doctor->phone,
                $depart,
                $profile_display,
                $options6 . ' ' . $options1 . ' ' . $options2 . ' ' . $options4 . ' ' . $options5 . ' ' . $options3,
                // $dropdownOptions
            );
        }

        if (!empty($data['doctors'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->doctor_model->getDoctor()),
                "recordsFiltered" => $i,
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

    public function getDoctorInfo()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->doctor_model->getDoctorInfo($searchTerm);

        echo json_encode($response);
    }

    public function getDoctorWithAddNewOption()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->doctor_model->getDoctorWithAddNewOption($searchTerm);

        echo json_encode($response);
    }

    public function addNewWithAjax()
    {
        header('Content-Type: application/json');

        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'))) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'You do not have permission to add doctors.'
            ));
            return;
        }

        $limit = $this->doctor_model->getLimit();
        if ($limit <= 0) {
            echo json_encode(array(
                'status' => 'error',
                'message' => lang('doctor_limit_exceed')
            ));
            return;
        }

        $name = trim((string)$this->input->post('name'));
        $name_en = trim((string)$this->input->post('name_en'));
        $degrees = trim((string)$this->input->post('degrees'));
        $phone = trim((string)$this->input->post('phone'));
        $department_name = trim((string)$this->input->post('department_name'));
        $profile = trim((string)$this->input->post('profile'));
        $address = trim((string)$this->input->post('address'));
        $email = trim((string)$this->input->post('email'));

        if (empty($name)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Doctor Name is required.'
            ));
            return;
        }

        if (empty($phone)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Phone number is required.'
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

        // Check for existing doctor with same phone in this hospital
        $existing_doctor = $this->db->get_where('doctor', array('hospital_id' => $hospital_id, 'phone' => $phone))->row();
        if (!empty($existing_doctor)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'A doctor with this phone number already exists (ID: ' . $existing_doctor->id . ' - ' . $existing_doctor->name . ').'
            ));
            return;
        }

        // Auto-generate email if empty
        if (empty($email)) {
            $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$name_en));
            if (empty($clean_name)) {
                $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$name));
            }
            if (empty($clean_name)) {
                $clean_name = 'doctor';
            }
            $digits_phone = preg_replace('/[^0-9]/', '', (string)$phone);
            $email = $clean_name . '.' . $digits_phone . '@lifecare.local';
            $counter = 1;
            while ($this->ion_auth->email_check($email) || $this->db->get_where('doctor', array('email' => $email))->num_rows() > 0) {
                $email = $clean_name . '.' . $digits_phone . '.' . $counter . '@lifecare.local';
                $counter++;
            }
        } elseif ($this->ion_auth->email_check($email) || $this->db->get_where('doctor', array('email' => $email))->num_rows() > 0) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'This email address is already registered.'
            ));
            return;
        }

        $password = '12345';
        $dfg = 4; // Doctor group
        $this->ion_auth->register($name, $password, $email, $dfg);
        $user_row = $this->db->get_where('users', array('email' => $email))->row();
        $ion_user_id = !empty($user_row) ? $user_row->id : null;

        $doctor_data = array(
            'name' => $name,
            'name_en' => !empty($name_en) ? $name_en : null,
            'degrees' => !empty($degrees) ? $degrees : null,
            'email' => $email,
            'address' => !empty($address) ? $address : '',
            'phone' => $phone,
            'department_name' => !empty($department_name) ? $department_name : '',
            'profile' => !empty($profile) ? $profile : '',
            'appointment_confirmation' => 'Active',
            'available' => '1',
            'active' => 1,
            'hospital_id' => $hospital_id
        );

        $this->doctor_model->insertDoctor($doctor_data);
        $doctor_id = $this->db->insert_id();
        if (empty($doctor_id)) {
            $d_row = $this->db->get_where('doctor', array('email' => $email, 'hospital_id' => $hospital_id))->row();
            $doctor_id = !empty($d_row) ? $d_row->id : null;
        }

        if (!empty($ion_user_id) && !empty($doctor_id)) {
            $this->doctor_model->updateDoctor($doctor_id, array('ion_user_id' => $ion_user_id));
            $this->hospital_model->addHospitalIdToIonUser($ion_user_id, $hospital_id);
        }

        $display_title = $name . (!empty($name_en) ? ' (' . $name_en . ')' : '');
        $formatted_text = $display_title . ' (' . lang('id') . ': ' . $doctor_id . ')';

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Doctor created successfully',
            'doctor' => array(
                'id' => $doctor_id,
                'name' => $name,
                'name_en' => $name_en,
                'degrees' => $degrees,
                'text' => $formatted_text,
                'phone' => $phone,
                'department_name' => $department_name
            )
        ));
    }
    function getDoctorByDepartment()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];
        $department = $this->input->post("id");
        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "email",
            "3" => "phone",
            "4" => "department",
            "5" => "profile",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['doctors'] = $this->doctor_model->getDoctorBysearchByDepartment($search, $order, $dir, $department);
            } else {
                $data['doctors'] = $this->doctor_model->getDoctorWithoutSearchByDepartment($order, $dir, $department);
            }
        } else {
            if (!empty($search)) {
                $data['doctors'] = $this->doctor_model->getDoctorByLimitBySearchByDepartment($limit, $start, $search, $order, $dir, $department);
            } else {
                $data['doctors'] = $this->doctor_model->getDoctorByLimitByDepartment($limit, $start, $order, $dir, $department);
            }
        }


        $i = 0;
        foreach ($data['doctors'] as $doctor) {
            $i = $i + 1;
            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) {
                $options1 = '<a type="button" class="btn btn-warning btn-sm editbutton" title="' . lang('edit') . '" data-toggle="modal" data-id="' . $doctor->id . '"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>';
            }

            $options2 = '<a class="btn btn-primary btn-sm detailsbutton" title="' . lang('appointments') . '" href="appointment/getAppointmentByDoctorId?id=' . $doctor->id . '"><i class="fa fa-calendar"></i> ' . lang('appointments') . '</a>';

            if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) {
                $options3 = '<a class="btn btn-danger btn-sm delete_button" title="' . lang('delete') . '" href="doctor/delete?id=' . $doctor->id . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"></i> ' . lang('delete') . '</a>';
                $options4 = '<a href="schedule/holidays?doctor=' . $doctor->id . '" class="btn btn-success btn-sm" data-toggle="modal" data-id="' . $doctor->id . '"><i class="fa fa-book"></i> ' . lang('holiday') . '</a>';
                $options5 = '<a href="schedule/timeSchedule?doctor=' . $doctor->id . '" class="btn btn-success btn-sm" data-toggle="modal" data-id="' . $doctor->id . '"><i class="fa fa-book"></i> ' . lang('time_schedule') . '</a>';
                $options6 = '<a type="button" class="btn btn-info btn-sm detailsbutton inffo" title="' . lang('info') . '" data-toggle="modal" data-id="' . $doctor->id . '"><i class="fa fa-info"></i> ' . lang('info') . '</a>';
            }



            $department_details = $this->department_model->getDepartmentById($doctor->department);
            if (!empty($department_details)) {
                $depart = $department_details->name;
            } else {
                $depart = $doctor->department_name;
            }



            $dropdownOptions = '';
            if (!empty($options6) || !empty($options1) || !empty($options2) || !empty($options4)  || !empty($options5)  || !empty($options3)) {
                $dropdownOptions = '
            <div class="btn-group">
            <button type="button" class="btn btn-info btn-xs label-primary dropdown-toggle action_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
            <i class="fas fa-bars"></i> ' . lang('actions') . ' <span class="caret"></span>
        </button>
                <ul class="dropdown-menu">
                    ' . ($options6 ? '<li><a class="detailsbutton inffo"' . lang('info') . '" data-toggle = "modal" data-id="' . $doctor->id . '"> <i class="fa fa-file-invoice"></i> ' . lang('info') . ' </a></li>' : '') . '
                    ' . ($options1 ? '<li><a class="editbutton" title="' . lang('edit') . '" data-toggle = "modal" data-id="' . $doctor->id . '">  <i class="fa fa-edit"></i> ' . lang('edit') . '</a></li>' : '') . '
                    ' . ($options2 ? '<li><a  href="appointment/getAppointmentByDoctorId?id=' . $doctor->id . '" target="_blank"> <i class="fa fa-print"></i> ' . lang('appointments') . ' </a></li>' : '') . '
                    ' . ($options4 ? '<li><a href="schedule/holidays?doctor=' . $doctor->id . '" > <i class="fa fa-money-check"></i> ' . lang('holiday') . ' ' . lang('') . ' </a></li>' : '') . '
                    ' . ($options5 ? '<li><a href="' . site_url("schedule/timeSchedule?doctor=" . $doctor->id) . '" > <i class="fa fa-book"></i> ' . lang('time_schedule') . ' </a></li>' : '') . '
                    ' . ($options6 ? '<li><a href="' . site_url("doctor/delete?id=" . $doctor->id) . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"> <i class="fa fa-trash"></i> ' . lang('delete') . ' </a></li>' : '') . '

                </ul>
            </div>';
            }



            $info[] = array(
                $doctor->id,
                $doctor->name,
                $doctor->email,
                $doctor->phone,
                $depart,
                $doctor->profile,
                // $dropdownOptions
                $options6 . ' ' . $options1 . ' ' . $options2 . ' ' . $options4 . ' ' . $options5 . ' ' . $options3,
            );
        }

        if (!empty($data['doctors'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->db->get_where('doctor', array('department' => $department))->result()),
                "recordsFiltered" => $i,
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

    function deleteDoctorImage()
    {
        $id = $this->input->get('id');
        $user_data = $this->db->get_where('doctor', array('id' => $id))->row();
        $path = $user_data->signature;
        if (!empty($path)) {
            unlink($path);
        }
        $data = array('signature' => '');
        $this->doctor_model->updateDoctor($id, $data);
        $data_response = array();
        $data_response['response'] = 'yes';
        echo json_encode($data_response);
    }
    public function getDoctorVisit()
    {
        $id = $this->input->get('id');
        // $description = $this->input->get('description');
        $visits = $this->doctor_model->getDoctorVisitByDoctorId($id);
        $option = '<option value="">' . lang('select') . '</option>';
        foreach ($visits as $visit) {

            $option .= '<option value="' . $visit->id . '">' . $visit->visit_description . '</option>';
        }
        $data['response'] = $option;
        echo json_encode($data);
    }
    public function getDoctorVisitCharges()
    {
        $id = $this->input->get('id');
        $data['response'] = $this->doctorvisit_model->getDoctorvisitById($id);


        echo json_encode($data);
    }
    public function getDoctorVisitForEdit()
    {
        $id = $this->input->get('id');
        $description = $this->input->get('description');
        $visits = $this->doctor_model->getDoctorVisitByDoctorId($id);
        $option = '<option value="">' . lang('select') . '</option>';
        foreach ($visits as $visit) {
            if ($visit->id == $description) {
                $option .= '<option value="' . $visit->id . '" selected ="selected">' . $visit->visit_description . '</option>';
            } else {
                $option .= '<option value="' . $visit->id . '">' . $visit->visit_description . '</option>';
            }
        }
        $data['response'] = $option;
        $data['visit_description'] = $option;
        echo json_encode($data);
    }

    public function updateAvailability()
    {
        $doctor_id = $this->input->post('doctor_id');
        $available = $this->input->post('available');
        $update_status = $this->doctor_model->updateAvailability($doctor_id, $available);
        if ($update_status) {
            echo json_encode(['message' => 'Availability updated successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to update availability.']);
        }
    }
}

/* End of file doctor.php */
/* Location: ./application/modules/doctor/controllers/doctor.php */