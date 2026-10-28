<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Status extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('patient/patient_model');
        $this->load->model('finance/finance_model');
        $this->load->model('finance/pharmacy_model');
        $this->load->model('pgateway/pgateway_model');
        $this->load->model('hospital/package_model');
        $this->load->model('hospital/hospital_model');

        $this->load->model('donor/donor_model');

        $this->load->model('sms/sms_model');
        $this->load->model('email/email_model');
    }

    public function index()
    {
        $status = $this->input->post('status');
        if (empty($status)) {
            redirect('payu');
        }

        $firstname = $this->input->post('firstname');
        $amount = $this->input->post('amount');
        $txnid = $this->input->post('txnid');
        $posted_hash = $this->input->post('hash');
        $key = $this->input->post('key');
        $productinfo = $this->input->post('productinfo');
        $email = $this->input->post('email');

        $payumoney = $this->pgateway_model->getPaymentGatewaySettingsByName('Pay U Money');

        $salt = $payumoney->salt; //  Your salt
        $add = $this->input->post('additionalCharges');
        if (isset($add)) {
            $additionalCharges = $this->input->post('additionalCharges');
            $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $data['hash'] = hash("sha512", $retHashSeq);
        $data['amount'] = $amount;
        $data['txnid'] = $txnid;
        $data['posted_hash'] = $posted_hash;
        $data['status'] = $status;

        $client_info = $this->patient_model->getpatientByEmail($email);
        $client_id = $client_info->id;

        if ($status == 'success') {

            $data = array();
            $data = array(
                'patient' => $client_id,
                'date' => time(),
                'payment_id' => $productinfo,
                'deposited_amount' => $amount,
                'deposit_type' => 'Card',
                'gateway' => 'Pay U Money',
                'user' => $this->ion_auth->get_user_id(),
                'hospital_id' => $this->session->userdata('hospital_id')
            );
            $this->finance_model->insertDeposit($data);

            $this->session->set_flashdata('feedback', 'Payment Completed Successfully');

            if ($this->ion_auth->in_group(array('Patient'))) {
                redirect('patient/myPaymentHistory');
            } else {
                redirect('finance/patientPaymentHistory?patient=' . $client_id);
            }
        } else {
            $this->session->set_flashdata('feedback', 'Payment Failed!');
            redirect('finance/patientPaymentHistory?patient=' . $client_id);
        }
    }

    public function index1()
    {
        $status = $this->input->post('status');
        if (empty($status)) {
            redirect('payu');
        }

        $firstname = $this->input->post('firstname');
        $amount = $this->input->post('amount');
        $txnid = $this->input->post('txnid');
        $posted_hash = $this->input->post('hash');
        $key = $this->input->post('key');
        $productinfo = $this->input->post('productinfo');
        $email = $this->input->post('email');
        $payumoney = $this->pgateway_model->getPaymentGatewaySettingsByName('Pay U Money');

        $salt = $payumoney->salt; //  Your salt
        $add = $this->input->post('additionalCharges');
        if (isset($add)) {
            $additionalCharges = $this->input->post('additionalCharges');
            $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $data['hash'] = hash("sha512", $retHashSeq);
        $data['amount'] = $amount;
        $data['txnid'] = $txnid;
        $data['posted_hash'] = $posted_hash;
        $data['status'] = $status;

        $client_info = $this->patient_model->getPatientByEmail($email);
        $client_id = $client_info->id;

        if ($status == 'success') {

            $data = array();
            $data = array(
                'patient' => $client_id,
                'date' => time(),
                'payment_id' => $productinfo,
                'deposited_amount' => $amount,
                'deposit_type' => 'Card',
                'gateway' => 'Pay U Money',
                'amount_received_id' => $productinfo . '.gp',
                'user' => $this->ion_auth->get_user_id(),
                'hospital_id' => $this->session->userdata('hospital_id')
            );
            $this->finance_model->insertDeposit($data);

            $data_payment = array('amount_received' => $amount, 'deposit_type' => 'Card');
            $this->finance_model->updatePayment($productinfo, $data_payment);

            $this->session->set_flashdata('feedback', 'Payment Completed Successfully');
            redirect("finance/invoice?id=" . "$productinfo");
        } else {
            $this->session->set_flashdata('feedback', 'Payment Failed!');
            redirect("finance/invoice?id=" . "$productinfo");
        }
    }

    public function index2()
    {
        $status = $this->input->post('status');
        if (empty($status)) {
            redirect('payu');
        }

        $firstname = $this->input->post('firstname');
        $amount = $this->input->post('amount');
        $txnid = $this->input->post('txnid');
        $posted_hash = $this->input->post('hash');
        $key = $this->input->post('key');
        $productinfo = $this->input->post('productinfo');
        $email = $this->input->post('email');
        $payumoney = $this->pgateway_model->getPaymentGatewaySettingsByName('Pay U Money');
        $salt = $payumoney->salt; //  Your salt
        $add = $this->input->post('additionalCharges');
        if (isset($add)) {
            $additionalCharges = $this->input->post('additionalCharges');
            $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $data['hash'] = hash("sha512", $retHashSeq);
        $data['amount'] = $amount;
        $data['txnid'] = $txnid;
        $data['posted_hash'] = $posted_hash;
        $data['status'] = $status;

        $client_info = $this->patient_model->getpatientByEmail($email);
        $client_id = $client_info->id;

        if ($status == 'success') {

            $previous_amount_received = $this->pharmacy_model->getPaymentById($productinfo)->amount_received;
            $data = array();
            $data = array(
                'amount_received' => $amount + $previous_amount_received,
            );
            $this->pharmacy_model->updatePayment($productinfo, $data);

            $this->session->set_flashdata('feedback', 'Amount Added Successfully');
            redirect("finance/pharmacy/invoice?id=" . "$productinfo");
            //  $this->load->view('success', $data);
        } else {
            $this->session->set_flashdata('feedback', 'Payment Failed!');
            redirect("finance/pharmacy/invoice?id=" . "$productinfo");
        }
    }

    public function index3()
    {

        $status = $this->input->post('status');

        $firstname = $this->input->post('firstname');
        $amount = $this->input->post('amount');
        $txnid = $this->input->post('txnid');
        $posted_hash = $this->input->post('hash');
        $key = $this->input->post('key');
        $productinfo = $this->input->post('productinfo');
        $productinfo_explode = explode("-", $productinfo);
        $email = $this->input->post('email');
        $address = $this->input->post('address1');
        $phone = $this->input->post('phone');
        $payumoney = $this->db->get_where('paymentgateway', array('hospital_id' => 'superadmin', 'name' => 'Pay U Money'))->row();
        // $payumoney = $this->pgateway_model->getPaymentGatewaySettingsByName('Pay U Money');

        $salt = $payumoney->salt; //  Your salt
        $add = $this->input->post('additionalCharges');
        if (isset($add)) {
            $additionalCharges = $this->input->post('additionalCharges');
            $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $data['hash'] = hash("sha512", $retHashSeq);
        $data['amount'] = $amount;
        $data['txnid'] = $txnid;

        $data['posted_hash'] = $posted_hash;
        $data['status'] = $status;

        if ($productinfo_explode[4] == 'backend' || $productinfo_explode[4] == 'deposit_backend' || $productinfo_explode[4] == 'expire' || $productinfo_explode[4] == 'frontend') {
            if (!empty($productinfo_explode[3])) {
                $module = $this->package_model->getPackageById($productinfo_explode[3])->module;
                $p_limit = $this->package_model->getPackageById($productinfo_explode[3])->p_limit;
                $d_limit = $this->package_model->getPackageById($productinfo_explode[3])->d_limit;
            }

            $data_up = array(
                'name' => $firstname,
                'email' => $email,
                'address' => $address,
                'phone' => $phone,
                'package_duration' => $productinfo_explode[0],
                'price' => $amount,
                'gateway' => 'Pay U Money',
                'hospital_id' => $productinfo_explode[1],
                'module' => $module,
                'language' => $productinfo_explode[2],
                'country' => $productinfo_explode[1],
                'p_limit' => $p_limit,
                'd_limit' => $d_limit,
                'package' => $productinfo_explode[3],
                'payu' => 'payu'
            );
            if ($productinfo_explode[4] == 'backend' || $productinfo_explode[4] == 'frontend') {
                $data_up['password'] = base64_decode($productinfo_explode[5]);
            }
        } else {
            $data_up = array(
                'name' => $firstname,
                'email' => $email,
                'address' => $address,
                'phone' => $phone,
                'package' => $productinfo_explode[3],
                'language' => $productinfo_explode[2],
                'package_duration' => $productinfo_explode[0],
                'price' => $amount,
                'gateway' => 'Pay U Money',
                'payu' => 'payu'
            );
        }

        if ($status == 'success') {
            if ($productinfo_explode[4] == 'backend') {

                $this->addNew($data_up);
            } elseif ($productinfo_explode[4] == 'deposit_backend' || $productinfo_explode[4] == 'expire') {
                $this->load->module('settings');
                if (!empty($productinfo_explode[5])) {
                    $data_up['from'] = $productinfo_explode[5];
                } else {
                    $data_up['from'] = '';
                }
                $this->settings->changePlan($data_up);
            } else {
                $this->load->module('frontend');
                $data_up['from_where'] = 'Website';

                $this->frontend->addNewhospital($data_up);
            }
        } else {
            if ($productinfo_explode[4] == 'deposit_backend' || $productinfo_explode[4] == 'expire') {
                $this->session->set_flashdata('feedback', 'Payment Failed!');
                if (empty($productinfo_explode[5])) {
                    redirect("settings/subscription");
                } else {
                    if ($productinfo_explode[5] == 'expire') {
                        redirect('hospital/lisenceExpired');
                    } else {
                        redirect('hospital');
                    }
                }
            } else {
                //$hospital = $this->db->get_where('hospital', array('id' => $productinfo_explode[1]))->row()->ion_user_id;
                // $this->db->where('id', $productinfo_explode[1]);
                // $this->db->delete('hospital');
                // $this->db->where('id', $productinfo_explode[1]);
                // $this->db->delete('users');
                $this->session->set_flashdata('feedback', 'Payment Failed!');
                if ($productinfo_explode[4] == 'frontend') {
                    redirect("frontend#book");
                } else {
                    redirect("hospital");
                }
            }
        }
    }
    public function addNew($data1)
    {

        $name = $data1['name'];
        $password = $data1['password'];
        $email = $data1['email'];
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
            'address' => $address,
            'phone' => $phone,
            'package' => $package,
            'p_limit' => $p_limit,
            'd_limit' => $d_limit,
            'country' => $country,
            'module' => $module
        );

        $username = $name;
        if (empty($id)) {     // Adding New Hospital
            if ($this->ion_auth->email_check($email)) {
                $this->session->set_flashdata('feedback', lang('this_email_address_is_already_registered'));
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
                    'emailtype' => 'Smtp'
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
                    'smtp_host' => 'smtp_host',
                    'smtp_port' => 'smtp_port',
                    'send_multipart' => 'send_multipart',
                    'mail_provider' => 'mail_provider',
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
                $this->hospital_model->addHospitalPayment($data_payment);
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
                $deposit = $this->hospital_model->addHospitalDeposit($data_deposit);
                if ($deposit) {
                    $data_payment_update = array('status' => 'paid');
                    $this->hospital_model->updateHospitalPayment($inserted_id, $data_payment_update);
                }
                $this->session->set_flashdata('feedback', lang('new_hospital_created'));
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

            $this->session->set_flashdata('feedback', lang('updated'));
            redirect('hospital/editHospital?id=' . $id);
        }
    }
    public function index4()
    {
        $status = $this->input->post('status');
        if (empty($status)) {
            redirect('payu');
        }

        $firstname = $this->input->post('firstname');
        $amount = $this->input->post('amount');
        $txnid = $this->input->post('txnid');
        $posted_hash = $this->input->post('hash');
        $key = $this->input->post('key');
        $productinfo = $this->input->post('productinfo');
        $email = $this->input->post('email');
        $payumoney = $this->pgateway_model->getPaymentGatewaySettingsByName('Pay U Money');

        $salt = $payumoney->salt; //  Your salt
        $add = $this->input->post('additionalCharges');
        if (isset($add)) {
            $additionalCharges = $this->input->post('additionalCharges');
            $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $data['hash'] = hash("sha512", $retHashSeq);
        $data['amount'] = $amount;
        $data['txnid'] = $txnid;
        $data['posted_hash'] = $posted_hash;
        $data['status'] = $status;

        $client_info = $this->patient_model->getPatientByEmail($email);
        $client_id = $client_info->id;

        if ($status == 'success') {

            $data = array();
            $data = array(
                'patient' => $client_id,
                'date' => time(),
                'payment_id' => $productinfo,
                'deposited_amount' => $amount,
                'deposit_type' => 'Card',
                'gateway' => 'Pay U Money',
                'amount_received_id' => $productinfo . '.gp',
                'user' => $this->ion_auth->get_user_id(),
                'hospital_id' => $this->session->userdata('hospital_id')
            );
            $this->finance_model->insertDeposit($data);

            $data_payment = array('amount_received' => $amount, 'deposit_type' => 'Card');
            $this->finance_model->updatePayment($productinfo, $data_payment);

            $this->session->set_flashdata('feedback', 'Payment Completed Successfully');
            redirect("finance/printInvoice?id=" . "$productinfo");
        } else {
            $this->session->set_flashdata('feedback', 'Payment Failed!');
            redirect("finance/printInvoice?id=" . "$productinfo");
        }
    }
    public function index5()
    {
        $status = $this->input->post('status');
        if (empty($status)) {
            redirect('payu');
        }

        $firstname = $this->input->post('firstname');
        $amount = $this->input->post('amount');
        $txnid = $this->input->post('txnid');
        $posted_hash = $this->input->post('hash');
        $key = $this->input->post('key');
        $productinfo = $this->input->post('productinfo');
        $email = $this->input->post('email');

        $payumoney = $this->pgateway_model->getPaymentGatewaySettingsByName('Pay U Money');

        $salt = $payumoney->salt; //  Your salt
        $add = $this->input->post('additionalCharges');
        if (isset($add)) {
            $additionalCharges = $this->input->post('additionalCharges');
            $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $data['hash'] = hash("sha512", $retHashSeq);
        $data['amount'] = $amount;
        $data['txnid'] = $txnid;
        $data['posted_hash'] = $posted_hash;
        $data['status'] = $status;

        $client_info = $this->patient_model->getpatientByEmail($email);
        $client_id = $client_info->id;

        if ($status == 'success') {

            $data = array();
            $data = array(
                'patient' => $client_id,
                'date' => time(),
                'payment_id' => $productinfo,
                'deposited_amount' => $amount,
                'deposit_type' => 'Card',
                'gateway' => 'Pay U Money',
                'user' => $this->ion_auth->get_user_id(),
                'hospital_id' => $this->session->userdata('hospital_id')
            );
            $this->finance_model->insertDeposit($data);

            $this->session->set_flashdata('feedback', 'Payment Completed Successfully');

            if ($this->ion_auth->in_group(array('Patient'))) {
                redirect('patient/myPaymentHistory');
            } else {
                redirect('finance/invoice?id=' . $productinfo);
            }
        } else {
            $this->session->set_flashdata('feedback', 'Payment Failed!');
            redirect('finance/invoice?id=' . $productinfo);
        }
    }
}
