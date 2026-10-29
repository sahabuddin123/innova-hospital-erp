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
        $hospital_id = $this->session->userdata('hospital_id');
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['departments'] = $this->department_model->getDepartment();
        $data['total_doctors'] = $this->db->where('hospital_id', $hospital_id)->count_all_results('doctor');
        $data['active_doctors'] = $this->db->where('hospital_id', $hospital_id)->where('active', 1)->count_all_results('doctor');
        $data['total_departments'] = count($data['departments']);
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
        $name_en = $this->input->post('name_en');
        if (empty($name_en)) {
            $name_en = $name;
        }
        $designation = $this->input->post('designation');
        $degrees = $this->input->post('degrees');
        $specialization = $this->input->post('specialization');
        $experience = $this->input->post('experience');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $chamber = $this->input->post('chamber');
        $phone = $this->input->post('phone');
        $phone_secondary = $this->input->post('phone_secondary');
        $department = $this->input->post('department');
        $department_details = $this->department_model->getDepartmentById($department);
        $profile = $this->input->post('profile');

        $consultation_fee = $this->input->post('consultation_fee');
        if (empty($consultation_fee) || (float)$consultation_fee <= 0) {
            $consultation_fee = 700.00;
        } else {
            $consultation_fee = (float)$consultation_fee;
        }

        $followup_fee = $this->input->post('followup_fee');
        if (empty($followup_fee) || (float)$followup_fee <= 0) {
            $followup_fee = 400.00;
        } else {
            $followup_fee = (float)$followup_fee;
        }

        $active = $this->input->post('active') !== null ? (int)$this->input->post('active') : 1;

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
        $this->form_validation->set_rules('address', 'Address', 'trim|min_length[1]|max_length[500]|xss_clean');
        // Validating Phone Field           
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[1]|max_length[50]|xss_clean');
        // Validating Department Field   
        $this->form_validation->set_rules('department', 'Department', 'trim|min_length[1]|max_length[500]|xss_clean');

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
                'max_size' => "2048", // 2 MB restriction
                'max_height' => "1768",
                'max_width' => "2024"
            );
            $config = array(
                'file_name' => $new_file_name,
                'upload_path' => "./uploads/",
                'allowed_types' => "gif|jpg|png|jpeg",
                'overwrite' => False,
                'max_size' => "2048", // 2 MB restriction
                'max_height' => "1768",
                'max_width' => "2024"
            );

            $img_url = null;
            $signature = null;

            if (!empty($_FILES['img_url']['name'])) {
                $this->load->library('Upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('img_url')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];
                }
            }

            if (!empty($_FILES['signature']['name'])) {
                $this->load->library('Upload', $config1);
                $this->upload->initialize($config1);
                if ($this->upload->do_upload('signature')) {
                    $path1 = $this->upload->data();
                    $signature = "uploads/" . $path1['file_name'];
                }
            }

            $data = array(
                'name' => $name,
                'name_en' => $name_en,
                'designation' => $designation,
                'degrees' => $degrees,
                'specialization' => $specialization,
                'experience' => $experience,
                'email' => $email,
                'address' => $address,
                'chamber' => $chamber,
                'phone' => $phone,
                'phone_secondary' => $phone_secondary,
                'department' => $department,
                'department_name' => !empty($department_details) ? $department_details->name : '',
                'consultation_fee' => $consultation_fee,
                'followup_fee' => $followup_fee,
                'profile' => $profile,
                'active' => $active,
                'appointment_confirmation' => 'Active',
            );

            if (!empty($img_url)) {
                $data['img_url'] = $img_url;
            }
            if (!empty($signature)) {
                $data['signature'] = $signature;
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

                    // Synchronize Consultation Fee (BDT 700 standard) in doctor_visit
                    $h_id = $this->session->userdata('hospital_id') ? $this->session->userdata('hospital_id') : $this->hospital_id;
                    $this->db->insert('doctor_visit', array(
                        'doctor_id' => $doctor_user_id,
                        'visit_description' => 'Consultation Fee',
                        'visit_charges' => $consultation_fee,
                        'status' => 'active',
                        'hospital_id' => $h_id
                    ));
                    if (!empty($followup_fee)) {
                        $this->db->insert('doctor_visit', array(
                            'doctor_id' => $doctor_user_id,
                            'visit_description' => 'Follow-up',
                            'visit_charges' => $followup_fee,
                            'status' => 'active',
                            'hospital_id' => $h_id
                        ));
                    }

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

                // Synchronize Consultation Fee (BDT 700 standard) in doctor_visit
                $h_id = $this->session->userdata('hospital_id') ? $this->session->userdata('hospital_id') : $this->hospital_id;
                $existing_consult = $this->db->get_where('doctor_visit', array('doctor_id' => $id, 'visit_description' => 'Consultation Fee', 'hospital_id' => $h_id))->row();
                if (!empty($existing_consult)) {
                    $this->db->where('id', $existing_consult->id)->update('doctor_visit', array('visit_charges' => $consultation_fee, 'status' => 'active'));
                } else {
                    $this->db->insert('doctor_visit', array(
                        'doctor_id' => $id,
                        'visit_description' => 'Consultation Fee',
                        'visit_charges' => $consultation_fee,
                        'status' => 'active',
                        'hospital_id' => $h_id
                    ));
                }

                if (!empty($followup_fee)) {
                    $existing_fu = $this->db->get_where('doctor_visit', array('doctor_id' => $id, 'visit_description' => 'Follow-up', 'hospital_id' => $h_id))->row();
                    if (!empty($existing_fu)) {
                        $this->db->where('id', $existing_fu->id)->update('doctor_visit', array('visit_charges' => $followup_fee, 'status' => 'active'));
                    } else {
                        $this->db->insert('doctor_visit', array(
                            'doctor_id' => $id,
                            'visit_description' => 'Follow-up',
                            'visit_charges' => $followup_fee,
                            'status' => 'active',
                            'hospital_id' => $h_id
                        ));
                    }
                }

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
        $doctor = $this->doctor_model->getDoctorById($id);
        if (empty($doctor)) {
            echo json_encode(array('doctor' => null, 'schedules' => [], 'holidays' => [], 'visits' => []));
            return;
        }
        if ($doctor->department == null && $doctor->department_name != null) {
            $department_details = $this->department_model->getDepartmentByName($doctor->department_name);
            if ($department_details) {
                $doctor->department = $department_details->id;
            }
        }
        $data['doctor'] = $doctor;
        $data['schedules'] = $this->db->get_where('time_schedule', array('doctor' => $id, 'hospital_id' => $doctor->hospital_id))->result();
        $data['holidays'] = $this->schedule_model->getHolidaysByDoctor($id);
        $data['visits'] = $this->doctor_model->getDoctorVisitByDoctorId($id);
        echo json_encode($data);
    }

    public function toggleActive()
    {
        header('Content-Type: application/json');
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'))) {
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
            return;
        }

        $id = $this->input->post('id');
        $doctor = $this->doctor_model->getDoctorById($id);
        if (empty($doctor)) {
            echo json_encode(array('status' => 'error', 'message' => 'Doctor record not found'));
            return;
        }

        $new_status = ($doctor->active == 1) ? 0 : 1;
        $this->doctor_model->updateDoctor($id, array('active' => $new_status));

        echo json_encode(array(
            'status' => 'success',
            'new_status' => $new_status,
            'message' => 'Doctor ' . html_escape($doctor->name) . ' has been ' . ($new_status ? 'activated' : 'deactivated') . ' successfully.'
        ));
    }

    function delete()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin'))) {
            redirect('home/permission');
        }

        $id = $this->input->get('id');
        $doctor = $this->doctor_model->getDoctorById($id);
        if (empty($doctor)) {
            redirect('doctor');
        }

        if (!$this->ion_auth->in_group(array('superadmin')) && $doctor->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $uploads_real = realpath('./uploads/');
        $path = $doctor->img_url;
        $path1 = $doctor->signature;

        if (!empty($path) && file_exists($path) && $uploads_real && strpos(realpath($path), $uploads_real) === 0) {
            @unlink($path);
        }
        if (!empty($path1) && file_exists($path1) && $uploads_real && strpos(realpath($path1), $uploads_real) === 0) {
            @unlink($path1);
        }

        $ion_user_id = $doctor->ion_user_id;
        if (!empty($ion_user_id)) {
            $this->db->where('id', $ion_user_id);
            $this->db->delete('users');
        }
        $this->doctor_model->delete($id);
        show_swal(lang('a_doctor_deleted_from_the_list'), 'warning', lang('deleted'));
        redirect('doctor');
    }

    function getDoctor()
    {
        $requestData = $_REQUEST;
        $start = isset($requestData['start']) ? intval($requestData['start']) : 0;
        $limit = isset($requestData['length']) ? intval($requestData['length']) : 10;
        $search = isset($requestData['search']['value']) ? trim($requestData['search']['value']) : '';
        $department = $this->input->post('department');
        $status = $this->input->post('status');

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "degrees",
            "3" => "department_name",
            "4" => "phone",
            "5" => "consultation_fee",
            "6" => "id",
            "7" => "active",
            "8" => "id",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        $totalData = $this->doctor_model->getDoctorsFilteredCount(null, null, null);
        $totalFiltered = $this->doctor_model->getDoctorsFilteredCount($search, $department, $status);
        $doctors = $this->doctor_model->getDoctorsFiltered($limit, $start, $search, $order, $dir, $department, $status);

        $info = array();
        foreach ($doctors as $doctor) {
            // Photo & Name & Designation
            $photo = (!empty($doctor->img_url) && file_exists($doctor->img_url)) ? $doctor->img_url : 'uploads/cardiology-patient-icon-vector-6244713.jpg';
            $doc_name = !empty($doctor->name_en) ? $doctor->name_en : $doctor->name;
            $name_cell = '<div class="d-flex align-items-center">';
            $name_cell .= '<img src="' . base_url($photo) . '" class="rounded-circle mr-3 border shadow-sm" style="width:48px;height:48px;object-fit:cover;">';
            $name_cell .= '<div>';
            $name_cell .= '<div class="font-weight-bold text-dark" style="font-size:14px;">' . html_escape($doc_name) . '</div>';
            if (!empty($doctor->designation)) {
                $name_cell .= '<div class="text-primary small font-weight-bold">' . html_escape($doctor->designation) . '</div>';
            }
            if (!empty($doctor->experience)) {
                $name_cell .= '<span class="badge badge-light border text-muted small mt-1">' . html_escape($doctor->experience) . '</span>';
            }
            $name_cell .= '</div></div>';

            // Qualifications & Specialization
            $qual_cell = '';
            if (!empty($doctor->degrees)) {
                $qual_cell .= '<div class="font-weight-bold text-dark mb-1"><i class="fas fa-graduation-cap text-secondary mr-1"></i> ' . html_escape($doctor->degrees) . '</div>';
            }
            if (!empty($doctor->specialization)) {
                $qual_cell .= '<div class="text-muted small"><i class="fas fa-stethoscope text-info mr-1"></i> ' . html_escape($doctor->specialization) . '</div>';
            }
            if (empty($qual_cell)) {
                $qual_cell = '<span class="text-muted small">N/A</span>';
            }

            // Department
            $dept_name = !empty($doctor->department_name) ? $doctor->department_name : 'General';
            $dept_cell = '<span class="badge badge-info p-2 font-weight-bold" style="font-size:11px; border-radius:4px;"><i class="fas fa-hospital mr-1"></i> ' . html_escape($dept_name) . '</span>';

            // Contact
            $contact_cell = '<div class="small">';
            $contact_cell .= '<div><i class="fas fa-phone-alt text-success mr-1"></i> <a href="tel:' . html_escape($doctor->phone) . '" class="text-dark font-weight-bold">' . html_escape($doctor->phone) . '</a></div>';
            if (!empty($doctor->phone_secondary)) {
                $contact_cell .= '<div class="text-muted"><i class="fas fa-phone text-muted mr-1"></i> ' . html_escape($doctor->phone_secondary) . '</div>';
            }
            if (!empty($doctor->email)) {
                $contact_cell .= '<div class="text-muted mt-1"><i class="fas fa-envelope text-primary mr-1"></i> ' . html_escape($doctor->email) . '</div>';
            }
            if (!empty($doctor->chamber)) {
                $contact_cell .= '<div class="text-muted small mt-1"><i class="fas fa-map-marker-alt text-danger mr-1"></i> ' . html_escape($doctor->chamber) . '</div>';
            }
            $contact_cell .= '</div>';

            // Consultation Fee: Standard BDT 700
            $fee_cell = '<div class="text-center">';
            $fee_cell .= '<span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size:12px; border-radius:4px;"><i class="fas fa-money-bill-wave mr-1"></i> BDT ' . number_format($doctor->consultation_fee, 0) . '</span>';
            if (!empty($doctor->followup_fee) && $doctor->followup_fee > 0) {
                $fee_cell .= '<div class="text-muted small mt-1">Follow-up: BDT ' . number_format($doctor->followup_fee, 0) . '</div>';
            }
            $fee_cell .= '</div>';

            // Schedule & Flag
            $sched_cell = '';
            if (!empty($doctor->schedule_flag)) {
                $sched_cell = '<div class="badge badge-warning p-2 text-wrap text-left shadow-sm" style="font-size:11px; max-width:220px; background-color:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:4px;"><i class="fas fa-exclamation-triangle mr-1 text-warning"></i> ' . html_escape($doctor->schedule_flag) . '</div>';
            } else {
                $schedules = $this->db->get_where('time_schedule', array('doctor' => $doctor->id, 'hospital_id' => $doctor->hospital_id))->result();
                if (!empty($schedules)) {
                    $sched_list = array();
                    foreach ($schedules as $sc) {
                        $sched_list[] = '<span class="font-weight-bold text-dark">' . substr($sc->weekday, 0, 3) . ':</span> ' . $sc->s_time . ' - ' . $sc->e_time;
                    }
                    $sched_cell = '<div class="small bg-white p-2 rounded border" style="font-size:11px; line-height:1.5;">' . implode('<br>', $sched_list) . '</div>';
                } else {
                    $sched_cell = '<span class="badge badge-secondary p-2 small">No schedule configured</span>';
                }
            }

            // Status & Quick Action
            $status_cell = '<div class="text-center">';
            if ($doctor->active == 1) {
                $status_cell .= '<span class="badge badge-success px-2 py-1 mb-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Active</span><br>';
                $status_cell .= '<button type="button" class="btn btn-outline-danger btn-xs toggle-status-btn mt-1" data-id="' . $doctor->id . '" title="Click to deactivate"><i class="fas fa-ban mr-1"></i> Deactivate</button>';
            } else {
                $status_cell .= '<span class="badge badge-danger px-2 py-1 mb-1 font-weight-bold"><i class="fas fa-times-circle mr-1"></i> Inactive</span><br>';
                $status_cell .= '<button type="button" class="btn btn-outline-success btn-xs toggle-status-btn mt-1" data-id="' . $doctor->id . '" title="Click to activate"><i class="fas fa-check mr-1"></i> Activate</button>';
            }
            $status_cell .= '</div>';

            // Actions
            $actions = '<div class="btn-group btn-group-sm" role="group">';
            $actions .= '<a type="button" class="btn btn-info inffo mr-1" title="View Profile" data-id="' . $doctor->id . '"><i class="fas fa-eye"></i></a>';
            $actions .= '<a type="button" class="btn btn-primary editbutton mr-1" title="Edit Doctor" data-id="' . $doctor->id . '"><i class="fas fa-edit"></i></a>';
            $actions .= '<a href="schedule/timeSchedule?doctor=' . $doctor->id . '" class="btn btn-secondary mr-1" title="Weekly Time Schedule"><i class="fas fa-clock"></i></a>';
            $actions .= '<a href="schedule/holidays?doctor=' . $doctor->id . '" class="btn btn-warning mr-1" title="Holidays & Leave"><i class="fas fa-calendar-times"></i></a>';
            $actions .= '<a href="appointment/getAppointmentByDoctorId?id=' . $doctor->id . '" class="btn btn-success mr-1" title="Doctor Appointments"><i class="fas fa-calendar-check"></i></a>';
            $actions .= '<a href="doctor/delete?id=' . $doctor->id . '" class="btn btn-danger" title="Delete" onclick="return confirm(\'Are you sure you want to delete this doctor?\');"><i class="fas fa-trash-alt"></i></a>';
            $actions .= '</div>';

            $info[] = array(
                '<strong>#' . $doctor->id . '</strong>',
                $name_cell,
                $qual_cell,
                $dept_cell,
                $contact_cell,
                $fee_cell,
                $sched_cell,
                $status_cell,
                $actions,
            );
        }

        $output = array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $info
        );

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