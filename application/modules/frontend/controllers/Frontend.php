<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Frontend extends MX_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('frontend_model');
        $this->load->model('doctor/doctor_model');
        $this->load->model('hospital/package_model');
        $this->load->model('patient/patient_model');
        $this->load->model('frontend/slide_model');
        $this->load->model('frontend/service_model');
        $this->load->model('featured/featured_model');
        require APPPATH . 'third_party/stripe/stripe-php/init.php';
        $this->load->module('paypal');
        $this->load->model('email/email_model');
        $this->load->model('pgateway/pgateway_model');
        $this->load->model('hospital/hospital_model');
        $this->load->model('donor/donor_model');
        $this->load->model('sms/sms_model');
        $this->load->model('settings/settings_model');
        $this->load->model('facilitie/facilitie_model');
        $this->load->model('faq/faq_model');
    }

    public function index()
    {
        $data = array();
        $data['contact_message'] = $this->input->get('contact_message');
        $data['hospital_message'] = $this->input->get('hospital_message');
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['packages'] = $this->package_model->getPackage();
        $data['slides'] = $this->slide_model->getSlide();
        $data['services'] = $this->service_model->getService();
        $data['facilities'] = $this->facilitie_model->getFacilitie();
        $data['faqs'] = $this->faq_model->getFaq();
        $data['featureds'] = $this->featured_model->getFeatured();
        $data['settings1'] = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row();
        $data['gateway'] = $this->db->get_where('paymentgateway', array('name' => $data['settings1']->payment_gateway, 'hospital_id' => 'superadmin'))->row();
        
        // Hybrid / Smart Captcha generation
        $num1 = rand(1, 15);
        $num2 = rand(1, 9);
        $data['math_captcha_question'] = "$num1 + $num2 = ?";
        $this->session->set_userdata('math_captcha_answer', $num1 + $num2);
        $data['googleReCaptchaSettings'] = $this->settings_model->getGoogleReCaptchaSettings();

        // $this->load->view('front_end', $data);
        $this->load->view('index', $data);
    }

    public function refresh_math_captcha()
    {
        $num1 = rand(1, 15);
        $num2 = rand(1, 9);
        $answer = $num1 + $num2;
        $this->session->set_userdata('math_captcha_answer', $answer);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'question' => "$num1 + $num2 = ?"
        ]);
        exit;
    }

    public function frontend2()
    {
        $data = array();
        $data['contact_message'] = $this->input->get('contact_message');
        $data['hospital_message'] = $this->input->get('hospital_message');
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['packages'] = $this->package_model->getPackage();
        $data['slides'] = $this->slide_model->getSlide();
        $data['services'] = $this->service_model->getService();
        $data['featureds'] = $this->featured_model->getFeatured();
        $data['settings1'] = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row();
        $data['gateway'] = $this->db->get_where('paymentgateway', array('name' => $data['settings1']->payment_gateway, 'hospital_id' => 'superadmin'))->row();
        $this->load->view('front_end', $data);
        // $this->load->view('index', $data);
    }

    public function addNew()
    {
        $id = $this->input->post('id');

        $patient = $this->input->post('patient');

        $doctor = $this->input->post('doctor');
        $date = $this->input->post('date');
        if (!empty($date)) {
            $date = strtotime($date);
        }


        $time_slot = $this->input->post('time_slot');

        $time_slot_explode = explode('To', $time_slot);

        $s_time = trim($time_slot_explode[0]);
        $e_time = trim($time_slot_explode[1]);

        $remarks = $this->input->post('remarks');

        $sms = $this->input->post('sms');

        $status = 'Requested';

        $redirect = 'frontend';

        $request = 'Yes';

        $user = '';

        if ((empty($id))) {
            $add_date = date('m/d/y');
            $registration_time = time();
            $patient_add_date = $add_date;
            $patient_registration_time = $registration_time;
        }

        $s_time_key = $this->getArrayKey($s_time);

        $p_name = $this->input->post('p_name');
        $p_email = $this->input->post('p_email');
        if (empty($p_email)) {
            $p_email = $p_name . '-' . rand(1, 1000) . '-' . $p_name . '-' . rand(1, 1000) . '@example.com';
        }
        if (!empty($p_name)) {
            $password = $p_name . '-' . rand(1, 100000000);
        }
        $p_phone = $this->input->post('p_phone');
        $p_age = $this->input->post('p_age');
        $p_gender = $this->input->post('p_gender');
        $patient_id = rand(10000, 1000000);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        if ($patient == 'add_new') {
            $this->form_validation->set_rules('p_name', 'Patient Name', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            $this->form_validation->set_rules('p_phone', 'Patient Phone', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        }

        if ($patient == 'patient_id') {
            $this->form_validation->set_rules('patient_id', 'Patient Name', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        }


        // Validating Name Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Doctor Field
        $this->form_validation->set_rules('doctor', 'Doctor', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Date Field
        $this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Address Field   
        $this->form_validation->set_rules('remarks', 'Remarks', 'trim|min_length[1]|max_length[1000]|xss_clean');

        if ($this->form_validation->run() == FALSE) {

            show_swal('Form Validation Error!', 'error', lang('error'));
            redirect("frontend");
        } else {


            if ($patient == 'patient_id') {
                $patient = $this->input->post('patient_id');

                if (!empty($patient)) {
                    $patient_exist = $this->patient_model->getPatientById($patient)->id;
                }

                if (empty($patient_exist)) {
                    show_swal('Invalid Patient Id !', 'error', lang('error'));
                    redirect("frontend");
                }
            }

            if ($patient == 'add_new') {
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
                $username = $this->input->post('p_name');
                // Adding New Patient
                if ($this->ion_auth->email_check($p_email)) {
                    show_swal(lang('this_email_address_is_already_registered'), 'warning', lang('warning'));
                    redirect($redirect);
                } else {
                    $dfg = 5;
                    $this->ion_auth->register($username, $password, $p_email, $dfg);
                    $ion_user_id = $this->db->get_where('users', array('email' => $p_email))->row()->id;
                    $this->patient_model->insertPatient($data_p);
                    $patient_user_id = $this->db->get_where('patient', array('email' => $p_email))->row()->id;
                    $id_info = array('ion_user_id' => $ion_user_id);
                    $this->patient_model->updatePatient($patient_user_id, $id_info);
                }

                $patient = $patient_user_id;
                //    }
            }
            //$error = array('error' => $this->upload->display_errors());
            $data = array();
            $data = array(
                'patient' => $patient,
                'doctor' => $doctor,
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
                'request' => $request
            );
            $username = $this->input->post('name');
            if (empty($id)) {     // Adding New department
                $this->frontend_model->insertAppointment($data);

                if (!empty($sms)) {
                    $this->sms->sendSmsDuringAppointment($patient, $doctor, $date, $s_time, $e_time);
                }

                $patient_doctor = $this->patient_model->getPatientById($patient)->doctor;

                $patient_doctors = explode(',', $patient_doctor);

                if (!in_array($doctor, $patient_doctors)) {
                    $patient_doctors[] = $doctor;
                    $doctorss = implode(',', $patient_doctors);
                    $data_d = array();
                    $data_d = array('doctor' => $doctorss);
                    $this->patient_model->updatePatient($patient, $data_d);
                }
                show_swal('Appointment Added Successfully. Please wait. You will get a confirmation sms.', 'success', lang('success'));
            }

            if (!empty($redirect)) {
                redirect($redirect);
            } else {
                redirect('appointment');
            }
        }
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

    public function settings()
    {
        if (!$this->ion_auth->in_group('superadmin')) {
            redirect('home/permission');
        }
        $data = array();
        $data['settings'] = $this->frontend_model->getSettings();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('settings', $data);
        $this->load->view('home/footer'); // just the footer file
    }

    public function update()
    {
        if (!$this->ion_auth->in_group('superadmin')) {
            redirect('home/permission');
        }
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $emergency = $this->input->post('emergency');
        $support = $this->input->post('support');
        $currency = $this->input->post('currency');
        $logo = $this->input->post('logo');
        $block_1_text_under_title = $this->input->post('block_1_text_under_title');
        $service_block_text_under_title = $this->input->post('service_block__text_under_title');
        $doctor_block_text_under_title = $this->input->post('doctor_block__text_under_title');
        $registration_block_text = $this->input->post('registration_block_text');
        $facebook_id = $this->input->post('facebook_id');
        $twitter_id = $this->input->post('twitter_id');
        $twitter_username = $this->input->post('twitter_username');
        $google_id = $this->input->post('google_id');
        $youtube_id = $this->input->post('youtube_id');
        $skype_id = $this->input->post('skype_id');
        $comment_1 = $this->input->post('comment_1');
        $comment_2 = $this->input->post('comment_2');
        $verified_1 = $this->input->post('verified_1');
        $verified_2 = $this->input->post('verified_2');
        $comment_logo_1 = $this->input->post('comment_logo_1');
        $comment_logo_2 = $this->input->post('comment_logo_2');
        $partner_header_title = $this->input->post('partner_header_title');
        $partner_header_description = $this->input->post('partner_header_description');
        $section_title_1 = $this->input->post('section_title_1');
        $section_title_2 = $this->input->post('section_title_2');
        $section_title_3 = $this->input->post('section_title_3');
        $section_description_1 = $this->input->post('section_description_1');
        $section_description_2 = $this->input->post('section_description_2');
        $section_description_3 = $this->input->post('section_description_3');
        $section_1_text_1 = $this->input->post('section_1_text_1');
        $section_1_text_2 = $this->input->post('section_1_text_2');
        $section_1_text_3 = $this->input->post('section_1_text_3');
        $section_2_text_1 = $this->input->post('section_2_text_1');
        $section_2_text_2 = $this->input->post('section_2_text_2');
        $section_2_text_3 = $this->input->post('section_2_text_3');
        $section_3_text_1 = $this->input->post('section_3_text_1');
        $section_3_text_2 = $this->input->post('section_3_text_2');
        $section_3_text_3 = $this->input->post('section_3_text_3');
        $partner_image_1 = $this->input->post('partner_image_1');
        $partner_image_2 = $this->input->post('partner_image_2');
        $partner_image_3 = $this->input->post('partner_image_3');
        $market_title = $this->input->post('market_title');
        $market_description = $this->input->post('market_description');
        $market_button_link = $this->input->post('market_button_link');
        $market_image = $this->input->post('market_image');
        $market_commentator_name = $this->input->post('market_commentator_name');
        $market_commentator_designation = $this->input->post('market_commentator_designation');
        $market_comment = $this->input->post('market_comment');
        $commentator_profile_image = $this->input->post('commentator_profile_image');
        $commentator_logo_1 = $this->input->post('commentator_logo_1');
        $commentator_logo_2 = $this->input->post('commentator_logo_2');
        $commentator_logo_3 = $this->input->post('commentator_logo_3');
        $team_title = $this->input->post('team_title');
        $team_description = $this->input->post('team_description');
        $team_button_link = $this->input->post('team_button_link');
        $team_commentator_name = $this->input->post('team_commentator_name');
        $team_commentator_designation = $this->input->post('team_commentator_designation');
        $team_comment = $this->input->post('team_comment');
        $team_verified = $this->input->post('team_verified');
        $team_review_logo = $this->input->post('team_review_logo');
        $team_commentator_image = $this->input->post('team_commentator_image');
        $contact_us = $this->input->post('contact_us');
        $chat_js = $this->input->post('chat_js');
        $google_translation_switch_in_frontend = $this->input->post('google_translation_switch_in_frontend');
        if (empty($google_translation_switch_in_frontend)) {
            $google_translation_switch_in_frontend = '';
        }

        $question1 = $this->input->post('question1');
        $question2 = $this->input->post('question2');
        $question3 = $this->input->post('question3');
        $question4 = $this->input->post('question4');
        $question5 = $this->input->post('question5');
        $question6 = $this->input->post('question6');
        $question7 = $this->input->post('question7');
        $question8 = $this->input->post('question8');
        $question9 = $this->input->post('question9');
        $answer1 = $this->input->post('answer1');
        $answer2 = $this->input->post('answer2');
        $answer3 = $this->input->post('answer3');
        $answer4 = $this->input->post('answer4');
        $answer5 = $this->input->post('answer5');
        $answer6 = $this->input->post('answer6');
        $answer7 = $this->input->post('answer7');
        $answer8 = $this->input->post('answer8');
        $answer9 = $this->input->post('answer9');
        $tawk_to_status = $this->input->post('tawk_to_status');




        if (!empty($email)) {
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            // Validating Title Field
            $this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            // Validating Email Field
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            // Validating Address Field   
            $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[1]|max_length[1000]|xss_clean');
            // Validating Phone Field           
            $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('currency', 'Currency', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('logo', 'Logo', 'trim|min_length[1]|max_length[100]|xss_clean');

            // Validating Currency Field   
            $this->form_validation->set_rules('emergency', 'Emergency', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('support', 'Support', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('logo', 'Logo', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('block_1_text_under_title', 'Block 1 Text Under Title', 'trim|min_length[1]|max_length[500]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('service_block__text_under_title', 'Service Block Text Under Title', 'trim|min_length[1]|max_length[500]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('doctor_block__text_under_title', 'Doctor Block Text Under Title', 'trim|min_length[1]|max_length[500]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('facebook_id', 'Facebook Id', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('twitter_id', 'Teitter Id', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('twitter_username', 'Teitter Username', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('google_id', 'Google Id', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('youtube_id', 'Youtube Id', 'trim|min_length[1]|max_length[100]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('skype_id', 'Skype Id', 'trim|min_length[1]|max_length[100]|xss_clean');

            // Validating Currency Field   
            $this->form_validation->set_rules('comment_1', 'Comment 1', 'trim|min_length[1]|max_length[1000]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('comment_2', 'Comment 2', 'trim|min_length[1]|max_length[1000]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('verified_1', 'Verified 1', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('verified_2', 'Verified 2', 'trim|min_length[1]|max_length[200]|xss_clean');

            // Validating Currency Field   
            $this->form_validation->set_rules('partner_header_title', 'Header Title', 'trim|min_length[1]|max_length[300]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('partner_header_description', 'Header Description', 'trim|min_length[1]|max_length[800]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_title_1', 'Title', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_description_1', 'Description', 'trim|min_length[1]|max_length[800]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_1_text_1', 'Text 1', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_1_text_2', 'Text 2', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_1_text_3', 'Text 3', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_title_2', 'Title', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_description_2', 'Description', 'trim|min_length[1]|max_length[800]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_2_text_1', 'Text 1', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_2_text_2', 'Text 2', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_2_text_3', 'Text 3', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_title_3', 'Title', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_description_3', 'Description', 'trim|min_length[1]|max_length[800]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_3_text_1', 'Text 1', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_3_text_2', 'Text 2', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('section_3_text_3', 'Text 3', 'trim|min_length[1]|max_length[200]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('market_title', 'Title', 'trim|min_length[1]|max_length[300]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('market_description', 'Description', 'trim|min_length[1]|max_length[800]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('market_button_link', 'Button Link', 'trim|min_length[1]|max_length[300]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('market_commentator_name', 'Commentator Name', 'trim|min_length[1]|max_length[300]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('market_commentator_designation', 'Commentator Designation', 'trim|min_length[1]|max_length[500]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('market_comment', 'Comment', 'trim|min_length[1]|max_length[1000]|xss_clean');

            // Validating Currency Field   
            $this->form_validation->set_rules('team_title', 'Title', 'trim|min_length[1]|max_length[300]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('team_description', 'Description', 'trim|min_length[1]|max_length[800]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('team_button_link', 'Button Link', 'trim|min_length[1]|max_length[300]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('team_commentator_name', 'Commentator Name', 'trim|min_length[1]|max_length[300]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('team_commentator_designation', 'Commentator Designation', 'trim|min_length[1]|max_length[500]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('team_comment', 'Comment', 'trim|min_length[1]|max_length[800]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('contact_us', 'Contact Us Text', 'trim|min_length[1]|max_length[300]|xss_clean');

            if ($this->form_validation->run() == FALSE) {
                $data = array();
                $data['settings'] = $this->settings_model->getSettings();
                $this->load->view('home/dashboard'); // just the header file
                $this->load->view('settings', $data);
                $this->load->view('home/footer'); // just the footer file
            } else {

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
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "1768",
                    'max_width' => "2024"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('img_url')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];
                    $data = array();
                    $data = array(
                        'title' => $title,
                        'address' => $address,
                        'phone' => $phone,
                        'email' => $email,
                        'currency' => $currency,
                        'emergency' => $emergency,
                        'support' => $support,
                        'block_1_text_under_title' => $block_1_text_under_title,
                        'service_block__text_under_title' => $service_block_text_under_title,
                        'doctor_block__text_under_title' => $doctor_block_text_under_title,
                        'registration_block_text' => $registration_block_text,
                        'facebook_id' => $facebook_id,
                        'twitter_id' => $twitter_id,
                        'twitter_username' => $twitter_username,
                        'google_id' => $google_id,
                        'youtube_id' => $youtube_id,
                        'skype_id' => $skype_id,
                        'logo' => $img_url,
                        'comment_1' => $comment_1,
                        'comment_2' => $comment_2,
                        'verified_1' => $verified_1,
                        'verified_2' => $verified_2,
                        'partner_header_title' => $partner_header_title,
                        'partner_header_description' => $partner_header_description,
                        'section_title_1' => $section_title_1,
                        'section_description_1' => $section_description_1,
                        'section_1_text_1' => $section_1_text_1,
                        'section_1_text_2' => $section_1_text_2,
                        'section_1_text_3' => $section_1_text_3,
                        'section_title_2' => $section_title_2,
                        'section_description_2' => $section_description_2,
                        'section_2_text_1' => $section_2_text_1,
                        'section_2_text_2' => $section_2_text_2,
                        'section_2_text_3' => $section_2_text_3,
                        'section_title_3' => $section_title_3,
                        'section_description_3' => $section_description_3,
                        'section_3_text_1' => $section_3_text_1,
                        'section_3_text_2' => $section_3_text_2,
                        'section_3_text_3' => $section_3_text_3,
                        'market_title' => $market_title,
                        'market_description' => $market_description,
                        'market_button_link' => $market_button_link,
                        'market_commentator_name' => $market_commentator_name,
                        'market_commentator_designation' => $market_commentator_designation,
                        'market_comment' => $market_comment,
                        'team_title' => $team_title,
                        'team_description' => $team_description,
                        'team_button_link' => $team_button_link,
                        'team_commentator_name' => $team_commentator_name,
                        'team_commentator_designation' => $team_commentator_designation,
                        'team_comment' => $team_comment,
                        'team_verified' => $team_verified,
                        'contact_us' => $contact_us,
                        'chat_js' => $chat_js,
                        'google_translation_switch_in_frontend' => $google_translation_switch_in_frontend,

                        'question1' => $question1,
                        'question2' => $question2,
                        'question3' => $question3,
                        'question4' => $question4,
                        'question5' => $question5,
                        'question6' => $question6,
                        'question7' => $question7,
                        'question8' => $question8,
                        'question9' => $question9,
                        'answer1' => $answer1,
                        'answer2' => $answer2,
                        'answer3' => $answer3,
                        'answer4' => $answer4,
                        'answer5' => $answer5,
                        'answer6' => $answer6,
                        'answer7' => $answer7,
                        'answer8' => $answer8,
                        'answer9' => $answer9,
                        'tawk_to_status' => $tawk_to_status
                    );
                } else {
                    $data = array();
                    $data = array(
                        'title' => $title,
                        'address' => $address,
                        'phone' => $phone,
                        'email' => $email,
                        'currency' => $currency,
                        'emergency' => $emergency,
                        'support' => $support,
                        'block_1_text_under_title' => $block_1_text_under_title,
                        'service_block__text_under_title' => $service_block_text_under_title,
                        'doctor_block__text_under_title' => $doctor_block_text_under_title,
                        'registration_block_text' => $registration_block_text,
                        'facebook_id' => $facebook_id,
                        'twitter_id' => $twitter_id,
                        'twitter_username' => $twitter_username,
                        'google_id' => $google_id,
                        'youtube_id' => $youtube_id,
                        'skype_id' => $skype_id,
                        'comment_1' => $comment_1,
                        'comment_2' => $comment_2,
                        'verified_1' => $verified_1,
                        'verified_2' => $verified_2,
                        'partner_header_title' => $partner_header_title,
                        'partner_header_description' => $partner_header_description,
                        'section_title_1' => $section_title_1,
                        'section_description_1' => $section_description_1,
                        'section_1_text_1' => $section_1_text_1,
                        'section_1_text_2' => $section_1_text_2,
                        'section_1_text_3' => $section_1_text_3,
                        'section_title_2' => $section_title_2,
                        'section_description_2' => $section_description_2,
                        'section_2_text_1' => $section_2_text_1,
                        'section_2_text_2' => $section_2_text_2,
                        'section_2_text_3' => $section_2_text_3,
                        'section_title_3' => $section_title_3,
                        'section_description_3' => $section_description_3,
                        'section_3_text_1' => $section_3_text_1,
                        'section_3_text_2' => $section_3_text_2,
                        'section_3_text_3' => $section_3_text_3,
                        'market_title' => $market_title,
                        'market_description' => $market_description,
                        'market_button_link' => $market_button_link,
                        'market_commentator_name' => $market_commentator_name,
                        'market_commentator_designation' => $market_commentator_designation,
                        'market_comment' => $market_comment,
                        'team_title' => $team_title,
                        'team_description' => $team_description,
                        'team_button_link' => $team_button_link,
                        'team_commentator_name' => $team_commentator_name,
                        'team_commentator_designation' => $team_commentator_designation,
                        'team_comment' => $team_comment,
                        'team_verified' => $team_verified,
                        'contact_us' => $contact_us,
                        'chat_js' => $chat_js,
                        'google_translation_switch_in_frontend' => $google_translation_switch_in_frontend,
                        'question1' => $question1,
                        'question2' => $question2,
                        'question3' => $question3,
                        'question4' => $question4,
                        'question5' => $question5,
                        'question6' => $question6,
                        'question7' => $question7,
                        'question8' => $question8,
                        'question9' => $question9,
                        'answer1' => $answer1,
                        'answer2' => $answer2,
                        'answer3' => $answer3,
                        'answer4' => $answer4,
                        'answer5' => $answer5,
                        'answer6' => $answer6,
                        'answer7' => $answer7,
                        'answer8' => $answer8,
                        'answer9' => $answer9,
                        'tawk_to_status' => $tawk_to_status
                    );
                }


                $this->frontend_model->updateSettings($id, $data);
                $data2 = array();
                $file_name = $_FILES['block_img_url']['name'];
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
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('block_img_url')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data2 = array(
                        'block_img_url' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data2);
                }
                $data3 = array();
                $file_name = $_FILES['comment_logo_1']['name'];
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
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('comment_logo_1')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data3 = array(
                        'comment_logo_1' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data3);
                }

                $data4 = array();
                $file_name = $_FILES['comment_logo_2']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('comment_logo_2')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data4 = array(
                        'comment_logo_2' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data4);
                }

                $data5 = array();
                $file_name = $_FILES['partner_image_1']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('partner_image_1')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data5 = array(
                        'partner_image_1' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data5);
                }

                $data6 = array();
                $file_name = $_FILES['partner_image_2']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('partner_image_2')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data6 = array(
                        'partner_image_2' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data6);
                }

                $data7 = array();
                $file_name = $_FILES['partner_image_3']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('partner_image_3')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data7 = array(
                        'partner_image_3' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data7);
                }
                $data8 = array();
                $file_name = $_FILES['market_image']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('market_image')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data8 = array(
                        'market_image' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data8);
                }
                $data9 = array();
                $file_name = $_FILES['commentator_profile_image']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('commentator_profile_image')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data9 = array(
                        'commentator_profile_image' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data9);
                }
                $data10 = array();
                $file_name = $_FILES['commentator_logo_1']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('commentator_logo_1')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data10 = array(
                        'commentator_logo_1' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data10);
                }
                $data11 = array();
                $file_name = $_FILES['commentator_logo_2']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('commentator_logo_2')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data11 = array(
                        'commentator_logo_2' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data11);
                }
                $data12 = array();
                $file_name = $_FILES['commentator_logo_3']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('commentator_logo_3')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data12 = array(
                        'commentator_logo_3' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data12);
                }

                $data13 = array();
                $file_name = $_FILES['team_review_logo']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('team_review_logo')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data13 = array(
                        'team_review_logo' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data13);
                }

                $data14 = array();
                $file_name = $_FILES['team_commentator_image']['name'];
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
                    'allowed_types' => "gif|jpg|png|svg|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "10000",
                    'max_width' => "10000"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('team_commentator_image')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];

                    $data14 = array(
                        'team_commentator_image' => $img_url
                    );
                    $this->frontend_model->updateSettings($id, $data14);
                }
                show_swal(lang('updated'), 'success', lang('updated'));
                // Loading View
                redirect('frontend/settings');
            }
        } else {
            show_swal('Email Required!', 'error', lang('error'));
            redirect('frontend/settings', 'refresh');
        }
    }

    function send()
    {

        $googleReCaptchaSiteKey =  $this->settings_model->getGoogleReCaptchaSettings()->secret_key;
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = $googleReCaptchaSiteKey;
        $recaptcha_response = $_POST['recaptcha_response1'];
        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha);
        if ($recaptcha->score >= 0.5) {
        } else {
            show_swal(lang('not_sent'), 'error', lang('error'));
            redirect('frontend?contact_message=failed#contact');
        }


        $emailSettings = $this->email_model->getContactEmailSettings();
        $other_email = $this->input->post('other_email');
        $message = $this->input->post('message');
        $subject = $this->input->post('subject');
        $name = $this->input->post('name');
        $msg = $this->input->post('msg');
        $phone = $this->input->post('phone');
        $hospital_name = $this->input->post('hospital_name');

        $secretKey = $this->settings_model->getGoogleReCaptchaSettings()->secret_key;
        $token = $this->input->post('g-token');
        $ip = $_SERVER["REMOTE_ADDR"];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $token . "&remoteip=" . $ip;
        $request = file_get_contents($url);
        $response = json_decode($request);
        if ($response->success) {
            if ($response->score > 0.8) {
            } else {
                show_swal(lang('no_sent'), 'error', lang('error'));
                redirect('frontend?contact_message=failed#contact');
            }
        } else {
            show_swal(lang('no_sent'), 'error', lang('error'));
            redirect('frontend?contact_message=failed#contact');
        }

        $data1 = array(
            'other_email' => $other_email,
            'msg' => $msg,
            'name' => $name,
            'phone' => $phone,
            'hospital_name' => $hospital_name
        );

        $recipient = $other_email;
        if (!empty($other_email)) {
            $to = $other_email;
        } else {
            if (!empty($to)) {
                $to = implode(',', $to);
            }
        }

        if (!empty($to)) {
            $autoemail = $this->email_model->getContactEmailByType('contactus');
            $subject = $this->input->post('subject');
            $message1 = $autoemail->message;
            $messageprint1 = $this->parser->parse_string($message1, $data1);
            $this->email->from($other_email);
            $this->email->to($emailSettings->admin_email);
            $this->email->subject('Contact Email');
            $this->email->message($messageprint1);
            if (function_exists('mail')) {
                $this->email->send();
            }

            show_swal(lang('request_sent_successfully'), 'success', lang('success'));
            redirect('frontend?contact_message=success#contact');
        } else {
            show_swal(lang('not_sent'), 'error', lang('error'));
            redirect('frontend?contact_message=failed#contact');
        }
    }

    public function addNewHospitalPayment()
    {



        $captcha_verified = false;
        $recaptchaSettings = $this->settings_model->getGoogleReCaptchaSettings();
        $recaptcha_secret = (!empty($recaptchaSettings) && !empty($recaptchaSettings->secret_key)) ? $recaptchaSettings->secret_key : '';

        // 1. Verify Google reCAPTCHA if response exists
        $g_response = $this->input->post('g-recaptcha-response');
        if (empty($g_response)) {
            $g_response = $this->input->post('recaptcha_response');
        }

        if (!empty($g_response) && strlen(trim($g_response)) > 20) {
            // User successfully completed Google reCAPTCHA checkbox in browser
            $captcha_verified = true;

            if (!empty($recaptcha_secret)) {
                $verify_response = '';
                if (function_exists('curl_init')) {
                    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                        'secret' => $recaptcha_secret,
                        'response' => $g_response,
                        'remoteip' => $this->input->ip_address()
                    ]));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    if (defined('CURL_IPRESOLVE_V4')) {
                        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    }
                    $verify_response = curl_exec($ch);
                    curl_close($ch);
                }
            }
        }

        // 2. Verify Math Captcha if Google reCAPTCHA was not verified
        if (!$captcha_verified) {
            $user_math_answer = $this->input->post('captcha_answer');
            $session_math_answer = $this->session->userdata('math_captcha_answer');

            if ($user_math_answer !== null && $session_math_answer !== null && trim($user_math_answer) !== '') {
                if ((int)trim($user_math_answer) === (int)$session_math_answer) {
                    $captcha_verified = true;
                    $this->session->unset_userdata('math_captcha_answer');
                }
            }
        }

        if (!$captcha_verified) {
            show_swal('Captcha verification failed! Please try again.', 'error', lang('error'));
            redirect('frontend?hospital_message=failed#register');
            return;
        }




        $trial_version = $this->input->post('trial_version');
        $country = $this->input->post('country');
        $name = $this->input->post('name');
        //$password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $package = $this->input->post('package');
        $language = $this->input->post('language');
        $package_duration = $this->input->post('package_duration');
        $price = $this->input->post('price');
        $frontend_website_link = $this->input->post('username');
        $package_details = $this->db->get_where('package', array('id' => $package))->row();

        if ($trial_version == '1') {
            $data = array();
            $data = array(
                'name' => $name,
                'email' => $email,
                'address' => $address,
                'username' => $frontend_website_link,
                'phone' => $phone,
                'package' => $package,
                'language' => $language,
                'package_duration' => $package_duration,
                'price' => '0',
                'country' => $country,
                'package_details' => 'trial'
            );

            $this->addNewhospital($data);
        } else {
            $data = array();
            $data = array(
                'name' => $name,
                'email' => $email,
                'address' => $address,
                'phone' => $phone,
                'package' => $package,
                'country' => $country,
                'username' => $frontend_website_link,
            );
            $gateway = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row()->payment_gateway;

            if ($gateway == 'PayPal') {
                $data['language'] = $language;
                $data['price'] = $price;
                $data['package_duration'] = $package_duration;
                $data['cardholder'] = $this->input->post('cardholder');
                $data['card_type'] = $this->input->post('card_type');
                $data['card_number'] = $this->input->post('card_number');
                $data['expire_date'] = $this->input->post('expire_date');
                $data['cvv'] = $this->input->post('cvv_number');
                $response = $this->paypal->paymentPaypalFromFrontend($data, 'Frontend');

                if ($response == 'yes') {
                    $data['gateway'] = 'PayPal';
                    $this->addNewhospital($data);
                } else {
                    show_swal(lang('Please_check_card_details'), 'warning', lang('warning'));
                    redirect('frontend?#book');
                }
            } elseif ($gateway == 'Stripe') {

                $token = $this->input->post('token');
                $stripe = $this->db->get_where('paymentgateway', array('hospital_id' => 'superadmin', 'name' => 'Stripe'))->row();

                \Stripe\Stripe::setApiKey($stripe->secret);
                $charge = \Stripe\Charge::create(array(
                    "amount" => $price * 100,
                    "currency" => "usd",
                    "source" => $token
                ));
                $chargeJson = $charge->jsonSerialize();
                if ($chargeJson['status'] == 'succeeded') {
                    $data['gateway'] = 'Stripe';
                    $data['package_duration'] = $package_duration;
                    $data['language'] = $language;
                    $data['price'] = $price;

                    $this->addNewhospital($data);
                } else {
                    show_swal(lang('Please_check_card_details'), 'warning', lang('warning'));
                    redirect('frontend?hospital_message=Please_check_card_details#book');
                }
            } elseif ($gateway == 'Pay U Money') {
                // $dfg = 11;

                // if (!empty($package)) {
                //     $data['module'] = $this->package_model->getPackageById($package)->module;
                //     $p_limit = $this->package_model->getPackageById($package)->p_limit;
                //     $d_limit = $this->package_model->getPackageById($package)->d_limit;
                // }
                // $data['p_limit'] = $p_limit;
                // $data['d_limit'] = $d_limit;
                // $this->ion_auth->register($name, '12345', $email, $dfg);
                // $ion_user_id = $this->db->get_where('users', array('email' => $email))->row()->id;
                // $data['from_where'] = 'Website';
                // $insert = $this->hospital_model->insertHospital($data);
                // $hospital_user_id = $this->db->get_where('hospital', array('email' => $email))->row()->id;
                // $id_info = array('ion_user_id' => $ion_user_id);
                // $update = $this->hospital_model->updateHospital($hospital_user_id, $id_info);
                $this->load->module('payu');
                $data['package_duration'] = $package_duration;
                $data['language'] = $language;
                $data['price'] = $price;
                $this->payu->check4($data, $price, $hospital_user_id, 'frontend');
            } elseif ($gateway == 'Paystack') {

                $paystack = $this->db->get_where('paymentgateway', array('hospital_id' => 'superadmin', 'name' => 'Paystack'))->row();

                $ref = date('Y') . '-' . rand() . date('d') . '-' . date('m');
                $amount_in_kobo = $price;
                $callback_url = base_url() . 'frontend#book';
                $postdata = array('first_name' => $name, 'email' => $email, 'amount' => $amount_in_kobo * 100, "reference" => $ref, 'callback_url' => $callback_url);

                $url = "https://api.paystack.co/transaction/initialize";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));  //Post Fields
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $headers = [
                    'Authorization: Bearer ' . $paystack->secret,
                    'Content-Type: application/json',
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $request = curl_exec($ch);
                curl_close($ch);

                if ($request) {
                    $result = json_decode($request, true);
                }

                $redir = $result['data']['authorization_url'];

                header("Location: " . $redir);
                if ($result['status'] == 1) {
                    $data['gateway'] = 'Paystack';
                    $data['language'] = $language;
                    $data['package_duration'] = $package_duration;
                    $data['price'] = $price;
                    $this->addNewhospital($data);
                } else {
                    show_swal(lang('Please_check_card_details'), 'warning', lang('warning'));
                    redirect('frontend?ospital_message=Please_check_card_details#book&status=error');
                }
                exit();
            }
        }
    }

    public function addNewhospital($data1)
    {

        $name = $data1['name'];
        $password = '12345';
        $email = $data1['email'];
        $address = $data1['address'];
        $phone = $data1['phone'];
        $package = $data1['package'];
        $language = $data1['language'];
        $frontend_website_link = $data1['username'];
        $package_duration = $data1['package_duration'];
        $price = $data1['price'];
        $country = $data1['country'];
        $package_details = $data1['package_details'];
        if (empty($package_details)) {
            $gateway = $data1['gateway'];
        } else {
            $gateway = 'trial';
        }
        //$package_details=$this->package_model->getPackageById($package);
        if (!empty($package)) {
            $module = $this->package_model->getPackageById($package)->module;
            $p_limit = $this->package_model->getPackageById($package)->p_limit;
            $d_limit = $this->package_model->getPackageById($package)->d_limit;
        }




        $language_array = array('english', 'arabic', 'spanish', 'french', 'italian', 'portuguese');

        if (!in_array($language, $language_array)) {
            $language = 'english';
        }

        $data = array();
        $data = array(
            'name' => $name,
            'email' => $email,
            'address' => $address,
            'phone' => $phone,
            'package' => $package,
            'p_limit' => $p_limit,
            'd_limit' => $d_limit,
            'module' => $module,
            'country' => $country,
            'username' => $frontend_website_link,
            'from_where' => 'Website'
        );

        $username = $name;

        if ($this->ion_auth->email_check($email)) {
            show_swal(lang('this_email_address_is_already_registered'), 'error', lang('error'));
            redirect('frontend?hospital_message=this_email_address_is_already_registered#book');
        } else {


            $dfg = 11;
            $this->ion_auth->register($username, $password, $email, $dfg);
            $ion_user_id = $this->db->get_where('users', array('email' => $email))->row()->id;
            $this->hospital_model->insertHospital($data);
            $hospital_user_id = $this->db->get_where('hospital', array('email' => $email))->row()->id;
            $id_info = array('ion_user_id' => $ion_user_id);
            $this->hospital_model->updateHospital($hospital_user_id, $id_info);

            $hospital_settings_data = array();
            $hospital_settings_data = array(
                'hospital_id' => $hospital_user_id,
                'title' => $name,
                'email' => $email,
                'address' => $address,
                'phone' => $phone,
                'language' => $language,
                'system_vendor' => 'INNOVA Computers BD - Hospital management System',
                'discount' => 'flat',
                'sms_gateway' => 'Twilio',
                'currency' => '$',
                'emailtype' => 'Smtp',
                'footer_message' => 'By' . $name,
                'show_odontogram_in_history' => 'yes',
                'invoice_choose' => 'invoice1',
                'vat' => '0',
                'discount_percent' => '0'
            );
            $this->settings_model->insertSettings($hospital_settings_data);
            $hospital_blood_bank = array();
            $hospital_blood_bank = array('A+' => '0 Bags', 'A-' => '0 Bags', 'B+' => '0 Bags', 'B-' => '0 Bags', 'AB+' => '0 Bags', 'AB-' => '0 Bags', 'O+' => '0 Bags', 'O-' => '0 Bags');
            foreach ($hospital_blood_bank as $key => $value) {
                $data_bb = array('group' => $key, 'status' => $value, 'hospital_id' => $hospital_user_id);
                $this->donor_model->insertBloodBank($data_bb);
                $data_bb = NULL;
            }

            $data_sms_clickatell = array();
            $data_sms_clickatell = array(
                'name' => 'Clickatell',
                'username' => 'Your ClickAtell Username',
                'password' => 'Your ClickAtell Password',
                'api_id' => 'Your ClickAtell Api Id',
                'user' => 'self',
                'hospital_id' => $hospital_user_id
            );

            $this->sms_model->addSmsSettings($data_sms_clickatell);

            $data_sms_msg91 = array(
                'name' => 'MSG91',
                'username' => 'Your MSG91 Username',
                'api_id' => 'Your MSG91 API ID',
                'sender' => 'Sender Number',
                'authkey' => 'Your MSG91 Auth Key',
                'hospital_id' => $hospital_user_id
            );

            $this->sms_model->addSmsSettings($data_sms_msg91);

            $data_sms_twilio = array(
                'name' => 'Twilio',
                'sid' => 'SID Number',
                'token' => 'Token Number',
                'sendernumber' => 'Sender Number',
                'hospital_id' => $hospital_user_id
            );

            $this->sms_model->addSmsSettings($data_sms_twilio);
            $data_sms_80kobo = array(
                'name' => '80Kobo',
                'email' => 'Your 80Kobo Username',
                'password' => 'Your 80Kobo Password',
                'sender_name' => 'Sender Name',
                'hospital_id' => $hospital_user_id
            );

            $this->sms_model->addSmsSettings($data_sms_80kobo);
            $data_pgateway_paypal = array(
                'name' => 'PayPal', // Sandbox / testing mode option.
                'APIUsername' => 'PayPal API Username', // PayPal API username of the API caller
                'APIPassword' => 'PayPal API Password', // PayPal API password of the API caller
                'APISignature' => 'PayPal API Signature', // PayPal API signature of the API caller
                'status' => 'test',
                'hospital_id' => $hospital_user_id
            );

            $this->pgateway_model->addPaymentGatewaySettings($data_pgateway_paypal);

            $data_pgateway_payumoney = array(
                'name' => 'Pay U Money', // Sandbox / testing mode option.
                'merchant_key' => 'Merchant key', // PayPal API username of the API caller
                'salt' => 'Salt', // PayPal API password of the API caller
                'status' => 'test',
                'hospital_id' => $hospital_user_id
            );

            $this->pgateway_model->addPaymentGatewaySettings($data_pgateway_payumoney);

            $data_pgateway_stripe = array(
                'name' => 'Stripe', // Sandbox / testing mode option.
                'secret' => 'Secret', // Sandbox / testing mode option.
                'publish' => 'Publish', // PayPal API username of the API caller
                'hospital_id' => $hospital_user_id
            );

            $this->pgateway_model->addPaymentGatewaySettings($data_pgateway_stripe);

            $data_pgateway_payumoney = array(
                'name' => 'Paystack', // Sandbox / testing mode option.
                'public_key' => 'Public key', // PayPal API username of the API caller
                'secret' => 'secret', // PayPal API password of the API caller
                'status' => 'test',
                'hospital_id' => $hospital_user_id
            );

            $this->pgateway_model->addPaymentGatewaySettings($data_pgateway_payumoney);
            $data_email_settings = array(
                'type' => 'Domain Email',
                'admin_email' => 'Admin Email', // Sandbox / testing mode option.
                'hospital_id' => $hospital_user_id
            );
            $data_email_settings_smtp = array(
                'type' => 'Smtp',
                'smtp_host' => 'smtp_host',
                'smtp_port' => 'smtp_port',
                'send_multipart' => 'send_multipart',
                'mail_provider' => 'mail_provider',
                'hospital_id' => $hospital_user_id
            );

            $base_url = str_replace(array('http://', 'https://', ' '), '', base_url()) . "auth/login";
            $set['settings'] = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row();
            $name1 = explode(' ', $name);
            if (!isset($name1[1])) {
                $name1[1] = null;
            }
            if (empty($package_details)) {
                if ($package_duration == 'monthly') {
                    $next_due_date_stamp = time() + 2592000;
                    $package_lang = lang('monthly');
                } else {
                    $next_due_date_stamp = time() + 31536000;
                    $package_lang = lang('yearly');
                }
            } else {
                if ($package_duration == 'monthly') {
                    $package_lang = lang('monthly');
                } else {
                    $package_lang = lang('yearly');
                }
                $next_due_date_stamp = time() + 1296000;
                $package_lang = lang($package_lang);
            }
            $next_due_date = date('d-m-Y', $next_due_date_stamp);
            $package_name = $this->db->get_where('package', array('id' => $package))->row()->name;
            $data1 = array(
                'name' => $name,
                'package_name' => $package_name,
                'subscription_duration' => $package_lang,
                'base_url' => $base_url,
                'amount' => $price,
                'password' => $password,
                'username' => $email,
                'phone' => $set['settings']->phone,
                'next_payment_date' => $next_due_date
            );
            //  $autoemail = $this->email_model->getAutoEmailByTypee('hospital');
            //if ($autoemail->status == 'Active') {

            $mail_provider = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row()->emailtype;
            $settngs_name = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row()->system_vendor;
            $email_Settings = $this->email_model->getAdminEmailSettingsByIdByType($mail_provider);

            $message1 = '<strong>{name}</strong> ,<br>
Your hospital is registered successfully . Please check the details Below.<br>
Package Name: {package_name}.<br>
Subscription Length: {subscription_duration}.<br>
Amount Paid: {amount}.<br>
Next Payment Date: {next_payment_date}.<br>
<u><b>Login Details:</b></u><br>
Url: {base_url}<br>
Username: {username}<br>
Password: {password}.<br>

For Any Support Please Contact with Phone No: {phone}';
            $messageprint1 = $this->parser->parse_string($message1, $data1);
            if ($mail_provider == 'Domain Email') {
                $this->load->library('email');
                $this->email->from($email_Settings->admin_email);
            }
            if ($mail_provider == 'Smtp') {
                $config['protocol'] = 'smtp';
                $config['mailpath'] = '/usr/sbin/sendmail';
                $config['smtp_host'] = $email_Settings->smtp_host;
                $config['smtp_port'] = number_format($email_Settings->smtp_port);
                $config['smtp_user'] = $email_Settings->user;
                $config['smtp_pass'] = base64_decode($email_Settings->password);
                $config['smtp_crypto'] = 'tls';
                $config['mailtype'] = 'html';
                $config['charset'] = 'utf-8';
                $config['wordwrap'] = TRUE;
                $config['send_multipart'] = TRUE;
                $config['newline'] = "\r\n";
                $this->load->library('email');
                $this->email->initialize($config);
                $this->load->library('email');
                $this->email->from($email_Settings->user, $settngs_name);
            }
            $this->email->to($email);
            $this->email->subject('Hospital Registration confirmation');
            $this->email->message($messageprint1);
            if (function_exists('mail')) {
                $this->email->send();
            }

            $this->email_model->addEmailSettings($data_email_settings_smtp);
            $this->email_model->addEmailSettings($data_email_settings);

            $this->hospital_model->createAutoSmsTemplate($hospital_user_id);
            $this->hospital_model->createAutoEmailTemplate($hospital_user_id);

            $data_payment = array();
            $data_payment = array(
                'hospital_user_id' => $hospital_user_id,
                'price' => $price,
                'package_duration' => $package_duration,
                'next_due_date_stamp' => $next_due_date_stamp,
                'next_due_date' => $next_due_date,
                'add_date_stamp' => time(),
                'add_date' => date('d-m-Y', time()),
                'package' => $package
            );
            $this->frontend_model->addHospitalPayment($data_payment);
            $inserted_id = $this->db->insert_id('hospital_payment');
            $data_deposit = array();
            $data_deposit = array(
                'payment_id' => $inserted_id,
                'date' => time(),
                'deposited_amount' => $price,
                'deposited_amount_id' => $inserted_id . 'gp',
                'gateway' => $gateway,
                'hospital_user_id' => $hospital_user_id,
                'next_due_date_stamp' => $next_due_date_stamp,
                'next_due_date' => $next_due_date,
                'add_date_stamp' => time(),
                'add_date' => date('d-m-Y', time()),
            );
            $deposit = $this->frontend_model->addHospitalDeposit($data_deposit);
            if ($deposit) {
                $data_payment_update = array('status' => 'paid');
                $this->frontend_model->updateHospitalPayment($inserted_id, $data_payment_update);
            }

            if ($gateway != 'Paystack') {
                if ($gateway == 'Pay U Money') {
                    show_swal(lang('yes'), 'success', lang('success'));

                    redirect('frontend?hospital_message=new_hospital_created#book&status=success');
                } else {
                    show_swal(lang('new_hospital_created'), 'success', lang('success'));
                    redirect('frontend?hospital_message=new_hospital_created#book&status=success');
                }
            }
        }

        // Loading View
        //}
    }

    function getPackageForHospitalRegisteration()
    {
        $id = $this->input->get('id');
        $data['package'] = $this->package_model->getPackageById($id);
        // $data['settings'] = $this->settings_model->getSettingsByHId($id);
        echo json_encode($data);
    }





    function verifyPurchase()
    {
        $data['verified'] = $this->input->get('verify');
        //   $this->load->view('home/dashboard.php');
        $this->load->view('verify', $data);
        //  $this->load->view('home/footer.php');
    }


    function verify()
    {
        $purchase_code = $this->input->post('purchase_code');
        $base_url = base_url() . '-';
        $insertPurchase = file_get_contents("http://verify.codearistos.net/api/verify?validation=" . $purchase_code . "&base_url=" . $base_url);
        $insertPurchase = json_decode($insertPurchase);
        if ($insertPurchase->message == 3) {
            show_swal('Purcase code validated successfully', 'success', lang('success'));
            redirect("frontend/verifyPurchase?verify=yes");
        } elseif ($insertPurchase->message == 1) {
            show_swal('Already Validated', 'warning', lang('warning'));
            redirect("frontend/verifyPurchase?verify=yes");
        } elseif ($insertPurchase->message == 2) {
            show_swal('This purchase code is validated for other domain. Please purchase a new licence or send request to support for removing the prevous domain.', 'error', lang('error'));
            redirect("frontend/verifyPurchase");
        } elseif ($insertPurchase->message == 4) {
            show_swal('This domain is already registerred with another purchase code.', 'error', lang('error'));
            redirect("frontend/verifyPurchase");
        } elseif ($insertPurchase->message == 0) {
            show_swal('This purchase code is invalid', 'error', lang('error'));
            redirect("frontend/verifyPurchase");
        }
    }

    function changeLanguageFlag()
    {
        $selectedLanguage = $this->input->get('lang'); // Replace this with the selected language
        $this->session->set_userdata('language_site', $selectedLanguage);
        $cookie = array(
            'name'   => 'language_site',
            'value'  => $selectedLanguage,
            'expire' => '2595000',
            'secure' => FALSE,
        );
        $this->input->set_cookie($cookie);

        redirect($_SERVER['HTTP_REFERER']);
    }



    public function chooseLanguage()
    {
        $selectedLanguage = $this->input->get('lang'); // Replace this with the selected language
        $this->session->set_userdata('language_site', $selectedLanguage);
        $cookie = array(
            'name'   => 'language_site',
            'value'  => $selectedLanguage,
            'expire' => '2595000',
            'secure' => FALSE,
        );
        $this->input->set_cookie($cookie);

        redirect($_SERVER['HTTP_REFERER']);
    }


    public function chooseLanguageForSite()
    {
        $selectedLanguage = $this->input->get('lang'); // Replace this with the selected language
        $this->session->set_userdata('language_site', $selectedLanguage);
        $cookie = array(
            'name'   => 'language_site',
            'value'  => $selectedLanguage,
            'expire' => '2595000',
            'secure' => FALSE,
        );
        $this->input->set_cookie($cookie);
        redirect($_SERVER['HTTP_REFERER']);
    }

    function checkIfUsernameAvailable()
    {
        $username = $this->input->get('username');
        $all_username = array();
        $hospitals = $this->frontend_model->getHospital();
        foreach ($hospitals as $hospital) {
           
                $all_username[] = $hospital->username;
           
        }
        if (in_array($username, $all_username)) {
            $data['check'] = 0;
        } else {
            $data['check'] = 1;
        }

        echo json_encode($data);
    }
}

/* End of file appointment.php */
    /* Location: ./application/modules/appointment/controllers/appointment.php */
