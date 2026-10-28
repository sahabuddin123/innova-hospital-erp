<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Hospital extends MX_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('hospital_model');
        $this->load->model('hospital/package_model');
        $this->load->model('donor/donor_model');
        $this->load->model('pgateway/pgateway_model');
        $this->load->model('sms/sms_model');
        $this->load->model('email/email_model');
        require APPPATH . 'third_party/stripe/stripe-php/init.php';
        if (!$this->ion_auth->in_group('superadmin')) {
            redirect('home/permission');
        }
    }

    public function index()
    {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $data['packages'] = $this->package_model->getPackage();
        $data['settings1'] = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row();
        $data['gateway'] = $this->db->get_where('paymentgateway', array('name' => $data['settings1']->payment_gateway, 'hospital_id' => 'superadmin'))->row();
        $this->load->view('home/dashboard');
        $this->load->view('hospital', $data);
        $this->load->view('home/footer');
    }

    public function addNewView()
    {
        // try {
        //     $verify = $this->settings_model->verify();
        //     if ($verify['verified'] == 1) {
        //     } else {
        //         redirect('settings/verifyYourPruchase776cbvcfytfytfvvn');
        //     }
        // } catch (Exception $e) {
        //     redirect('settings/verifyYourPruchase776cbvcfytfytfvvn');
        // }

        $data['packages'] = $this->package_model->getPackage();
        $data['settings'] = $this->settings_model->getSettings();
        $data['gateway'] = $this->db->get_where('paymentgateway', array('name' => $data['settings']->payment_gateway, 'hospital_id' => 'superadmin'))->row();
        $this->load->view('home/dashboard');
        $this->load->view('add_new', $data);
        $this->load->view('home/footer');
    }

    public function addNewHospital()
    {
        $trial_version = $this->input->post('trial_version');
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $frontend_website_link = $this->input->post('username');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $package = $this->input->post('package');
        $language = $this->input->post('language');
        $package_duration = $this->input->post('package_duration');
        $price = $this->input->post('price');
        $deposit_type = $this->input->post('deposit_type');
        $country = $this->input->post('country');
        if (empty($id)) {     // Adding New Hospital
            if ($this->ion_auth->email_check($email)) {
                show_swal(lang('this_email_address_is_already_registered'), 'error', lang('error'));
                redirect('hospital/addNewView');
            }
        }
        $package_details = $this->db->get_where('package', array('id' => $package))->row();
        if (!empty($package)) {
            $module = $this->package_model->getPackageById($package)->module;
            $p_limit = $this->package_model->getPackageById($package)->p_limit;
            $d_limit = $this->package_model->getPackageById($package)->d_limit;
        } else {
            $p_limit = $this->input->post('p_limit');
            $d_limit = $this->input->post('d_limit');
            $module = $this->input->post('module');
            if (!empty($module)) {
                $module = implode(',', $module);
            }
        }
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Name Field
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        // Validating Password Field
        if (empty($id)) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        }
        // Validating Email Field
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        // Validating Address Field   
        $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[5]|max_length[500]|xss_clean');
        // Validating Phone Field           
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[5]|max_length[50]|xss_clean');

        // Validating Phone Field           
        // $this->form_validation->set_rules('p_limit', 'Patient Limit', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Phone Field           
        $this->form_validation->set_rules('language', 'Language', 'trim|required|min_length[1]|max_length[50]|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect("hospital/editHospital?id=" . $id);
            } else {
                $data['packages'] = $this->package_model->getPackage();
                $data['settings'] = $this->settings_model->getSettings();
                $this->load->view('home/dashboard');
                $this->load->view('add_new', $data);
                $this->load->view('home/footer');
            }
        } else {
            $data = array();
            if (empty($id)) {
                if ($trial_version == '1') {
                    $data = array(
                        'name' => $name,
                        'email' => $email,
                        'username' => $frontend_website_link,
                        'address' => $address,
                        'phone' => $phone,
                        'password' => $password,
                        'package' => $package,
                        'language' => $language,
                        'package_duration' => $package_duration,
                        'price' => '0',
                        'id' => $id,
                        'module' => $module,
                        'p_limit' => $p_limit,
                        'd_limit' => $d_limit,
                        'country' => $country,
                        'package_details' => 'trial',
                        'invoice_choose' => 'invoice1'
                    );
                } else {
                    $data = array(
                        'name' => $name,
                        'email' => $email,
                        'username' => $frontend_website_link,
                        'address' => $address,
                        'phone' => $phone,
                        'password' => $password,
                        'package' => $package,
                        'language' => $language,
                        'package_duration' => $package_duration,
                        'price' => $price,
                        'id' => $id,
                        'module' => $module,
                        'country' => $country,
                        'p_limit' => $p_limit,
                        'd_limit' => $d_limit,
                        'invoice_choose' => 'invoice1'
                    );
                }
            } else {
                $data = array(
                    'name' => $name,
                    'username' => $frontend_website_link,
                    'email' => $email,
                    'address' => $address,
                    'phone' => $phone,
                    'country' => $country,

                );
            }
            if (empty($id)) {
                if ($trial_version == '1') {
                    $data['gateway'] = 'trial';
                    $this->addNew($data);
                } else {
                    if ($deposit_type == 'Card') {
                        $gateway = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row()->payment_gateway;
                        if ($gateway == 'PayPal') {
                            $data['cardholder'] = $this->input->post('cardholder');
                            $data['card_type'] = $this->input->post('card_type');
                            $data['card_number'] = $this->input->post('card_number');
                            $data['expire_date'] = $this->input->post('expire_date');
                            $data['cvv'] = $this->input->post('cvv_number');
                            $this->load->module('paypal');
                            $response = $this->paypal->paymentPaypalFromFrontend($data, 'backend');

                            if ($response == 'yes') {
                                $data['gateway'] = 'PayPal';
                                $this->addNew($data);
                            } else {
                                show_swal(lang('Please_check_card_details'), 'warning', lang('warning'));
                                redirect('hospital');
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
                                $this->addNew($data);
                            } else {
                                show_swal(lang('Please_check_card_details'), 'warning', lang('warning'));
                                redirect('hospital');
                            }
                        } elseif ($gateway == 'Paystack') {
                            $paystack = $this->db->get_where('paymentgateway', array('hospital_id' => 'superadmin', 'name' => 'Paystack'))->row();

                            $ref = date('Y') . '-' . rand() . date('d') . '-' . date('m');
                            $amount_in_kobo = $price;
                            $callback_url = base_url() . 'hospital';
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

                                $this->addNew($data);
                            }
                            exit();
                        } elseif ($gateway == 'Pay U Money') {

                            $this->load->module('payu');
                            $this->payu->check4($data, $price, $hospital_user_id, 'backend');
                        }
                    } else {
                        $data['gateway'] = 'Cash';

                        $this->addNew($data);
                    }
                }
            } else {

                $hospital_details = $this->hospital_model->getHospitalById($id);
                if ($email != $hospital_details->email) {
                    if ($this->ion_auth->email_check($email)) {
                        show_swal(lang('this_email_address_is_already_registered'), 'error', lang('error'));
                        redirect("hospital/editHospital?id=" . $id);
                    }
                }



                $update = $this->hospital_model->updateHospital($id, $data);
                $ion_user_id = $this->db->get_where('hospital', array('id' => $id))->row()->ion_user_id;

                if (empty($password)) {
                    $password = $this->db->get_where('users', array('id' => $ion_user_id))->row()->password;
                } else {
                    $password = $this->ion_auth_model->hash_password($password);
                }
                $this->hospital_model->updateIonUser($name, $email, $password, $ion_user_id);

                $hospital_settings_data = array();
                $hospital_settings_data = array(
                    'language' => $language,
                    'email' => $email
                );
                $this->settings_model->updateHospitalSettings($id, $hospital_settings_data);

                show_swal(lang('updated'), 'success', lang('updated'));
                redirect('hospital/editHospital?id=' . $id);
            }
        }
    }

    public function addNew($data1)
    {

        $name = $data1['name'];
        $password = $data1['password'];
        $email = $data1['email'];
        $frontend_website_link = $data1['username'];
        $address = $data1['address'];
        $phone = $data1['phone'];
        $package = $data1['package'];
        $language = $data1['language'];
        $package_duration = $data1['package_duration'];
        $price = $data1['price'];
        $gateway = $data1['gateway'];
        $id = $data1['id'];
        $country = $data1['country'];
        $package_details = $data1['package_details'];


        if (!empty($package)) {
            $module = $this->package_model->getPackageById($package)->module;
            $p_limit = $this->package_model->getPackageById($package)->p_limit;
            $d_limit = $this->package_model->getPackageById($package)->d_limit;
        } else {
            $p_limit = $data1['p_limit'];
            $d_limit = $data1['d_limit'];
            $module = $data1['module'];
        }




        $language_array = array('english', 'arabic', 'spanish', 'french', 'italian', 'portuguese');

        if (!in_array($language, $language_array)) {
            $language = 'english';
        }


        $data = array();
        $data = array(
            'name' => $name,
            'email' => $email,
            'username' => $frontend_website_link,
            'address' => $address,
            'phone' => $phone,
            'package' => $package,
            'p_limit' => $p_limit,
            'd_limit' => $d_limit,
            'country' => $country,
            'module' => $module,

        );

        $username = $name;
        if (empty($id)) {     // Adding New Hospital
            if ($this->ion_auth->email_check($email)) {
                show_swal(lang('this_email_address_is_already_registered'), 'error', lang('error'));
                redirect('hospital/addNewView');
            } else {
                $dfg = 11;
                $this->ion_auth->register($username, $password, $email, $dfg);

                $ion_user_id = $this->db->get_where('users', array('email' => $email))->row()->id;
                // $data_users=array('active'=>1);$this->hospital_model->updateUsers($ion_user_id,$data_users);
                // $data['ion_user_id']=$inserted_id;
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
                    'footer_message' => 'By ' . $name,
                    'show_odontogram_in_history' => 'yes',
                    'invoice_choose' => 'invoice1',
                    'vat' => '0',
                    'discount_percent' => '0',
                    'timezone' => 'Asia/Singapore'
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
                    'user' => $this->ion_auth->get_user_id(),
                    'hospital_id' => $hospital_user_id
                );

                $this->sms_model->addSmsSettings($data_sms_clickatell);

                $data_sms_msg91 = array(
                    'name' => 'MSG91',
                    'username' => 'Your MSG91 Username',
                    'api_id' => 'Your MSG91 API ID',
                    'sender' => 'Sender Number',
                    'authkey' => 'Your MSG91 Auth Key',
                    'user' => $this->ion_auth->get_user_id(),
                    'hospital_id' => $hospital_user_id
                );

                $this->sms_model->addSmsSettings($data_sms_msg91);

                $data_sms_twilio = array(
                    'name' => 'Twilio',
                    'sid' => 'SID Number',
                    'token' => 'Token Number',
                    'sendernumber' => 'Sender Number',
                    'user' => $this->ion_auth->get_user_id(),
                    'hospital_id' => $hospital_user_id
                );

                $this->sms_model->addSmsSettings($data_sms_twilio);

                $data_sms_80kobo = array(
                    'name' => '80Kobo',
                    'email' => 'Your 80Kobo Username',
                    'password' => 'Your 80Kobo Password',
                    'sender_name' => 'Sender Name',
                    'user' => $this->ion_auth->get_user_id(),
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
                    'user' => 'example@gmail.com',
                    'password' => 'MTIzNDU=',
                    'smtp_host' => 'smtp.gmail.com',
                    'smtp_port' => '587',
                    'send_multipart' => '1',
                    'mail_provider' => 'gmail',
                    'hospital_id' => $hospital_user_id
                );
                $base_url = str_replace(array('http://', 'https://', ' '), '', base_url()) . "auth/login";
                $set['settings'] = $this->settings_model->getSettings();
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
                $name1 = explode(' ', $name);
                if (!isset($name1[1])) {
                    $name1[1] = null;
                }
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

For Any Support Please Contact Us At: {phone}';
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
                $this->hospital_model->addHospitalPayment($data_payment);
                $inserted_id = $this->db->insert_id('hospital_payment');
                $data_deposit = array();
                $data_deposit = array(
                    'payment_id' => $inserted_id,
                    'date' => time(),
                    'deposited_amount' => $price,
                    'deposited_amount_id' => $inserted_id . 'gp',
                    'gateway' => $gateway,
                    'package_id' => $package,
                    'hospital_user_id' => $hospital_user_id,
                    'next_due_date_stamp' => $next_due_date_stamp,
                    'next_due_date' => $next_due_date,
                    'add_date_stamp' => time(),
                    'add_date' => date('d-m-Y', time()),
                );
                $deposit = $this->hospital_model->addHospitalDeposit($data_deposit);
                if ($deposit) {
                    $data_payment_update = array('status' => 'paid');
                    $this->hospital_model->updateHospitalPayment($inserted_id, $data_payment_update);
                }
                show_swal(lang('new_hospital_created'), 'success', lang('added'));
                if ($gateway != 'Paystack') {
                    redirect('hospital');
                }
            }
        } else { // Updating Hospital
            $ion_user_id = $this->db->get_where('hospital', array('id' => $id))->row()->ion_user_id;
            if (empty($password)) {
                $password = $this->db->get_where('users', array('id' => $ion_user_id))->row()->password;
            } else {
                $password = $this->ion_auth_model->hash_password($password);
            }
            $this->hospital_model->updateIonUser($username, $email, $password, $ion_user_id);
            $this->hospital_model->updateHospital($id, $data);

            $hospital_settings_data = array();
            $hospital_settings_data = array(
                'language' => $language
            );
            $this->settings_model->updateHospitalSettings($id, $hospital_settings_data);

            show_swal(lang('updated'), 'success', lang('updated'));
            redirect('hospital/editHospital?id=' . $id);
        }
    }

    function getHospital()
    {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $this->load->view('hospital', $data);
    }

    function activate()
    {
        $hospital_id = $this->input->get('hospital_id');
        $redirect = $this->input->get('redirect');
        $data = array('active' => 1);
        $this->hospital_model->activate($hospital_id, $data);
        show_swal(lang('hospital_is_activated_successfully'), 'success', lang('activated'));
        if ($redirect == 'deactive') {
            redirect('hospital/disable');
        } elseif ($redirect == 'active') {
            redirect('hospital/active');
        } else {
            redirect('hospital');
        }
    }

    function deactivate()
    {
        $hospital_id = $this->input->get('hospital_id');
        $redirect = $this->input->get('redirect');
        $data = array('active' => 0);
        $this->hospital_model->deactivate($hospital_id, $data);
        show_swal(lang('hospital_is_deactivated_successfully'), 'warning', lang('deactivated'));
        if ($redirect == 'deactive') {
            redirect('hospital/disable');
        } elseif ($redirect == 'active') {
            redirect('hospital/active');
        } else {
            redirect('hospital');
        }
    }

    function editHospital()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['packages'] = $this->package_model->getPackage();
        $data['hospital'] = $this->hospital_model->getHospitalById($id);
        $this->load->view('home/dashboard');
        $this->load->view('add_new', $data);
        $this->load->view('home/footer');
    }

    function editHospitalByJason()
    {
        $id = $this->input->get('id');
        $data['hospital'] = $this->hospital_model->getHospitalById($id);
        $data['settings'] = $this->settings_model->getSettingsByHId($id);
        echo json_encode($data);
    }

    function delete()
    {
        $data = array();
        $id = $this->input->get('id');
        $user_data = $this->db->get_where('hospital', array('id' => $id))->row();
        $ion_user_id = $user_data->ion_user_id;
        $this->db->where('id', $ion_user_id);
        $this->db->delete('users');
        $this->hospital_model->delete($id);
        redirect('hospital');
    }


    public function requested()
    {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $data['packages'] = $this->package_model->getPackage();
        $this->load->view('home/dashboard');
        $this->load->view('requested', $data);
        $this->load->view('home/footer');
    }

    public function active()
    {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $data['packages'] = $this->package_model->getPackage();
        $this->load->view('home/dashboard');
        $this->load->view('active_hospital', $data);
        $this->load->view('home/footer');
    }

    public function disable()
    {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $data['packages'] = $this->package_model->getPackage();
        $this->load->view('home/dashboard');
        $this->load->view('disable_hospital', $data);
        $this->load->view('home/footer');
    }

    public function getHospitalinfo()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->hospital_model->getHospitalInfo($searchTerm);

        echo json_encode($response);
    }

    function getHospitalCurrentpackage()
    {
        $id = $this->input->get('id');
        $data['hospital'] = $this->hospital_model->getHospitalPaymentByHospitalId($id);
        echo json_encode($data);
    }

    function getPackageForHospitalRegisteration()
    {
        $id = $this->input->get('id');
        $data['package'] = $this->package_model->getPackageById($id);
        $modules = ['accountant', 'appointment', 'lab', 'radiology', 'bed', 'department', 'donor', 'finance', 'pharmacy', 'laboratorist', 'medicine', 'nurse', 'patient', 'pharmacist', 'prescription', 'receptionist', 'report', 'notice', 'email', 'sms', 'file', 'payroll', 'attendance', 'leave', 'chat'];
        $package_in_list = explode(",", $data['package']->module);
        $option = '';
        for ($i = 0; $i < count($modules); $i++) {
            if (in_array($modules[$i], $package_in_list)) {
                if ($modules[$i] == 'finance') {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-check text-white"></i> ' . lang('finance') . '</div>';
                } elseif ($modules[$i] == 'lab') {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-check text-white"></i> ' . lang('lab_tests') . '</div>';
                } elseif ($modules[$i] == 'radiology') {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-check text-white"></i> ' . lang('radiology') . '</div>';
                } else {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-check text-white"></i> ' . lang($modules[$i]) . '</div>';
                }
            } else {
                if ($modules[$i] == 'finance') {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-times text-white"></i> ' . lang('finance') . '</div>';
                } elseif ($modules[$i] == 'lab') {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-times text-white"></i> ' . lang('lab_tests') . '</div>';
                } elseif ($modules[$i] == 'radiology') {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-times text-white"></i> ' . lang('radiology') . '</div>';
                } else {
                    $option .= '<div class="col-sm-3 bg-white text-white"><i class="fa fa-times text-white"></i> ' . lang($modules[$i]) . '</div>';
                }
            }
        }
        $data['option'] = $option;

        echo json_encode($data);
    }

    public function lisenceExpired()
    {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $data['packages'] = $this->package_model->getPackage();
        $data['settings1'] = $this->db->get_where('settings', array('hospital_id' => 'superadmin'))->row();
        $data['gateway'] = $this->db->get_where('paymentgateway', array('name' => $data['settings1']->payment_gateway, 'hospital_id' => 'superadmin'))->row();
        $this->load->view('home/dashboard');
        $this->load->view('expired_hospital', $data);
        $this->load->view('home/footer');
    }

    public function reportSubscription()
    {
        $country = $this->input->post('country');
        $subscription = $this->input->post('subscription');
        $submit = $this->input->post('submit');
        $package = $this->input->post('package');
        if ($submit == 'reset') {
            $data['from'] = '';
            $data['to'] = '';
            $data['subscription'] = "";
            $data['package_select'] = $package;
            $data['country_select'] = "";
            $data['deposits'] = $this->hospital_model->getHospitalPaymentDeposits();
        }
        if ($submit == 'submit') {
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');

            if (!empty($date_from) && !empty($date_to)) {
                if ($package == 'all' && empty($country)) {
                    $data['from'] = $date_from;
                    $data['to'] = $date_to;
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDate(strtotime($date_from), strtotime($date_to));
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDateBySubscription(strtotime($date_from), strtotime($date_to), $subscription);
                    }
                } elseif ($package == 'all' && !empty($country)) {
                    $data['from'] = $date_from;
                    $data['to'] = $date_to;
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDateByCountry(strtotime($date_from), strtotime($date_to), $country);
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDateByCountryBySubscription(strtotime($date_from), strtotime($date_to), $country, $subscription);
                    }
                } elseif ($package != 'all' && !empty($country)) {
                    $data['from'] = $date_from;
                    $data['to'] = $date_to;
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDateByCountry(strtotime($date_from), strtotime($date_to), $country);
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDateByCountryBySubscription(strtotime($date_from), strtotime($date_to), $country, $subscription);
                    }
                } else {
                    $data['from'] = $date_from;
                    $data['to'] = $date_to;
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDate(strtotime($date_from), strtotime($date_to));
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByDateBySubscription(strtotime($date_from), strtotime($date_to), $subscription);
                    }
                }
            } else {
                if ($package == 'all' && empty($country)) {
                    $data['from'] = '';
                    $data['to'] = '';
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalPaymentDepositsReport();
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalPaymentDepositsReportBySubscription($subscription);
                    }
                } elseif ($package == 'all' && !empty($country)) {
                    $data['from'] = '';
                    $data['to'] = '';
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByCountry($country);
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByCountryBySubscription($country, $subscription);
                    }
                } elseif ($package != 'all' && !empty($country)) {
                    $data['from'] = '';
                    $data['to'] = '';
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByCountry($country);
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalDepositByCountryBySubscription($country, $subscription);
                    }
                } else {
                    $data['from'] = '';
                    $data['to'] = '';
                    $data['subscription'] = $subscription;
                    $data['package_select'] = $package;
                    $data['country_select'] = $country;
                    if ($subscription == 'all') {
                        $data['deposits'] = $this->hospital_model->getHospitalPaymentDepositsReport();
                    } else {
                        $data['deposits'] = $this->hospital_model->getHospitalPaymentDepositsReportBySubscription($subscription);
                    }
                }
            }
        }
        if (empty($submit)) {
            $data['package_select'] = '';
            $data['country_select'] = '';
            $data['subscription'] = '';
            $data['deposits'] = $this->hospital_model->getHospitalPaymentDepositsReport();
        }

        $data['settings'] = $this->settings_model->getSettings();
        $data['packages'] = $this->package_model->getPackage();
        $this->load->view('home/dashboard');
        $this->load->view('report_subscription', $data);
        $this->load->view('home/footer');
    }

    function getPackageForHospitalRegisterationOnlyPackage()
    {
        $id = $this->input->get('id');
        $data['package'] = $this->package_model->getPackageById($id);

        echo json_encode($data);
    }

    function checkIfUsernameAvailable()
    {
        $username = $this->input->get('username');
        $id = $this->input->get('id');
        $all_username = array();
        $current_hosiptal_username = $this->hospital_model->getHospitalById($id)->username;
        $hospitals = $this->hospital_model->getHospital();
        foreach ($hospitals as $hospital) {
            if ($hospital->username != $current_hosiptal_username) {
                $all_username[] = $hospital->username;
            }
        }
        if (in_array($username, $all_username)) {
            $data['check'] = 0;
        } else {
            $data['check'] = 1;
        }

        echo json_encode($data);
    }
}

/* End of file hospital.php */
/* Location: ./application/modules/hospital/controllers/hospital.php */
