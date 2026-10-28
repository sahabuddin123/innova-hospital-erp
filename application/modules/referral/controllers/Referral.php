<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Referral extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('referral_model');
        $this->load->model('finance/finance_model');
        $this->load->model('doctor/doctor_model');
        $this->load->model('patient/patient_model');
        $this->load->model('settings/settings_model');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        // Module access permissions: Superadmin, Admin, Accountant, Receptionist
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'))) {
            redirect('home/permission');
        }
    }

    // ==========================================
    // REFERRER CRUD
    // ==========================================

    public function index()
    {
        $data['referrers'] = $this->referral_model->getReferrers();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('referrer', $data);
        $this->load->view('home/footer');
    }

    public function referrer()
    {
        $this->index();
    }

    public function addNewView()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            redirect('home/permission');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('add_referrer', $data);
        $this->load->view('home/footer');
    }

    public function addNew()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            redirect('home/permission');
        }

        $id = $this->input->post('id');
        $name = trim((string)$this->input->post('name'));
        $phone = trim((string)$this->input->post('phone'));
        $email = trim((string)$this->input->post('email'));
        $address = trim((string)$this->input->post('address'));
        $type = $this->input->post('type');
        $status = $this->input->post('status');
        $notes = trim((string)$this->input->post('notes'));

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error text-danger font-weight-bold mb-2">', '</div>');

        $this->form_validation->set_rules('name', 'Referrer Full Name', 'trim|required|min_length[2]|max_length[100]|xss_clean');
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
                        $clean = preg_replace('/[\s\-]/', '', (string)$phone);
                        return (bool)preg_match('/^(?:\+?880|880|0)?1[3-9]\d{8}$/', $clean);
                    }
                )
            )
        );
        $this->form_validation->set_message('valid_bd_phone', 'The {field} field must be a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).');

        if (!empty($email)) {
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]|xss_clean');
        }

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                $this->editReferrer($id);
            } else {
                $this->addNewView();
            }
        } else {
            $hospital_id = $this->session->userdata('hospital_id');
            $data = array(
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'type' => !empty($type) ? $type : 'Other',
                'status' => !empty($status) ? $status : 'Active',
                'notes' => $notes,
                'hospital_id' => $hospital_id
            );

            if (empty($id)) {
                $data['created_at'] = time();
                $data['created_by'] = $this->ion_auth->get_user_id();
                $this->referral_model->insertReferrer($data);
                show_swal(lang('added'), 'success', 'Referrer Added Successfully');
            } else {
                $data['updated_at'] = time();
                $this->referral_model->updateReferrer($id, $data);
                show_swal(lang('updated'), 'success', 'Referrer Updated Successfully');
            }
            redirect('referral');
        }
    }

    public function addNewWithAjax()
    {
        header('Content-Type: application/json');

        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'))) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'You do not have permission to add referrers.'
            ));
            return;
        }

        $name = trim((string)$this->input->post('name'));
        $phone = trim((string)$this->input->post('phone'));
        $email = trim((string)$this->input->post('email'));
        $address = trim((string)$this->input->post('address'));
        $type = trim((string)$this->input->post('type'));
        $notes = trim((string)$this->input->post('notes'));

        if (empty($name)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Referrer Full Name is required.'
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

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Please enter a valid email address.'
            ));
            return;
        }

        $hospital_id = $this->session->userdata('hospital_id');
        if (empty($hospital_id)) {
            $hospital_id = $this->hospital_id;
        }

        // Check for existing referrer with same phone in this hospital
        $existing_ref = $this->db->get_where('referrer', array('hospital_id' => $hospital_id, 'phone' => $phone))->row();
        if (!empty($existing_ref)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'A referrer with this phone number already exists (ID: ' . $existing_ref->id . ' - ' . $existing_ref->name . ').'
            ));
            return;
        }

        $data = array(
            'name' => $name,
            'phone' => $phone,
            'email' => !empty($email) ? $email : null,
            'address' => !empty($address) ? $address : null,
            'type' => !empty($type) ? $type : 'Doctor',
            'status' => 'Active',
            'notes' => !empty($notes) ? $notes : null,
            'hospital_id' => $hospital_id,
            'created_at' => time(),
            'created_by' => $this->ion_auth->get_user_id()
        );

        $id = $this->referral_model->insertReferrer($data);
        if (empty($id)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Failed to save referrer in database.'
            ));
            return;
        }

        // Formatted display text matching add_payment_view options
        $formatted_text = $name . ' (' . $data['type'] . ' - ' . $phone . ')';

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Referrer created successfully',
            'referrer' => array(
                'id' => $id,
                'name' => $name,
                'phone' => $phone,
                'type' => $data['type'],
                'text' => $formatted_text
            )
        ));
    }

    public function editReferrer($id = null)
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            redirect('home/permission');
        }
        if (empty($id)) {
            $id = $this->input->get('id');
        }
        $data['referrer'] = $this->referral_model->getReferrerById($id);
        if (empty($data['referrer'])) {
            redirect('referral');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('add_referrer', $data);
        $this->load->view('home/footer');
    }

    public function delete()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin'))) {
            redirect('home/permission');
        }
        $id = $this->input->get('id');
        if (!empty($id)) {
            $this->referral_model->deleteReferrer($id);
            show_swal(lang('deleted'), 'warning', 'Referrer deleted/deactivated.');
        }
        redirect('referral');
    }

    public function getReferrerById()
    {
        header('Content-Type: application/json');
        $id = $this->input->get('id');
        $referrer = $this->referral_model->getReferrerById($id);
        if ($referrer) {
            echo json_encode(array('success' => true, 'data' => $referrer));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Referrer not found.'));
        }
    }

    public function getReferrerInfo()
    {
        header('Content-Type: application/json');
        $searchTerm = $this->input->post('searchTerm');
        $hospital_id = $this->session->userdata('hospital_id');

        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('status', 'Active');
        if (!empty($searchTerm)) {
            $this->db->group_start();
            $this->db->like('name', $searchTerm);
            $this->db->or_like('phone', $searchTerm);
            $this->db->or_like('type', $searchTerm);
            $this->db->group_end();
        }
        $this->db->order_by('name', 'asc');
        $referrers = $this->db->get('referrer')->result();

        $data = array();
        foreach ($referrers as $ref) {
            $data[] = array(
                'id' => $ref->id,
                'text' => $ref->name . ' (' . $ref->type . ' - ' . $ref->phone . ')'
            );
        }
        echo json_encode($data);
    }

    // ==========================================
    // DASHBOARD & METRICS
    // ==========================================

    public function dashboard()
    {
        $hospital_id = $this->session->userdata('hospital_id');
        $data['metrics'] = $this->referral_model->getDashboardMetrics($hospital_id);
        $data['recent_transactions'] = $this->referral_model->getCommissionTransactions(array('hospital_id' => $hospital_id), 10, 0);
        $data['recent_withdrawals'] = $this->referral_model->getWithdrawals($hospital_id);
        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard');
        $this->load->view('dashboard', $data);
        $this->load->view('home/footer');
    }

    // ==========================================
    // WALLETS & STATEMENTS
    // ==========================================

    public function wallets()
    {
        $data['wallets'] = $this->referral_model->getWallets();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('wallets', $data);
        $this->load->view('home/footer');
    }

    public function statement()
    {
        $referrer_id = $this->input->get('referrer_id');
        if (empty($referrer_id)) {
            $referrer_id = $this->input->post('referrer_id');
        }
        if (empty($referrer_id)) {
            redirect('referral/wallets');
        }

        $date_from_str = $this->input->post('date_from');
        $date_to_str = $this->input->post('date_to');

        $date_from = !empty($date_from_str) ? strtotime($date_from_str . ' 00:00:00') : null;
        $date_to = !empty($date_to_str) ? strtotime($date_to_str . ' 23:59:59') : null;

        $data['statement'] = $this->referral_model->getReferrerStatement($referrer_id, $date_from, $date_to);
        $data['date_from'] = $date_from_str;
        $data['date_to'] = $date_to_str;
        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard');
        $this->load->view('statement', $data);
        $this->load->view('home/footer');
    }

    public function adjustWallet()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin'))) {
            redirect('home/permission');
        }

        $referrer_id = $this->input->post('referrer_id');
        $type = $this->input->post('type');
        $amount = floatval($this->input->post('amount'));
        $reason = trim((string)$this->input->post('reason'));
        $reference = trim((string)$this->input->post('reference'));
        $user_id = $this->ion_auth->get_user_id();
        $hospital_id = $this->session->userdata('hospital_id');

        if (empty($referrer_id) || $amount <= 0 || empty($reason) || !in_array($type, array('credit', 'debit'))) {
            show_swal('Invalid adjustment parameters. Reason and positive amount are required.', 'error', 'Validation Error');
            redirect('referral/wallets');
        }

        $res = $this->referral_model->recordControlledAdjustment($referrer_id, $type, $amount, $reason, $reference, $user_id, $hospital_id);
        if ($res['status'] == 'success') {
            show_swal($res['message'], 'success', 'Adjustment Complete');
        } else {
            show_swal($res['message'], 'error', 'Adjustment Failed');
        }
        redirect('referral/wallets');
    }

    public function transactions()
    {
        $filters = array(
            'hospital_id' => $this->session->userdata('hospital_id')
        );
        $referrer_id = $this->input->get('referrer_id');
        if (!empty($referrer_id)) {
            $filters['referrer_id'] = $referrer_id;
        }
        $status = $this->input->get('status');
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        $date_from_str = $this->input->get('date_from');
        if (!empty($date_from_str)) {
            $filters['from_date'] = strtotime($date_from_str . ' 00:00:00');
        }
        $date_to_str = $this->input->get('date_to');
        if (!empty($date_to_str)) {
            $filters['to_date'] = strtotime($date_to_str . ' 23:59:59');
        }

        $data['transactions'] = $this->referral_model->getCommissionTransactions($filters, 500, 0);
        $data['referrers'] = $this->referral_model->getReferrers();
        $data['selected_referrer'] = $referrer_id;
        $data['selected_status'] = $status;
        $data['date_from'] = $date_from_str;
        $data['date_to'] = $date_to_str;
        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard');
        $this->load->view('transactions', $data);
        $this->load->view('home/footer');
    }

    // ==========================================
    // WITHDRAWAL SYSTEM
    // ==========================================

    public function withdrawals()
    {
        $data['withdrawals'] = $this->referral_model->getWithdrawals();
        $data['referrers'] = $this->referral_model->getActiveReferrers();
        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard');
        $this->load->view('withdrawals', $data);
        $this->load->view('home/footer');
    }

    public function requestWithdrawal()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            redirect('home/permission');
        }

        $referrer_id = $this->input->post('referrer_id');
        $amount = floatval($this->input->post('amount'));
        $payment_method = $this->input->post('payment_method');
        $account_number = trim((string)$this->input->post('account_number'));
        $notes = trim((string)$this->input->post('notes'));

        $hospital_id = $this->session->userdata('hospital_id');
        $user_id = $this->ion_auth->get_user_id();

        if (empty($referrer_id) || $amount <= 0 || empty($payment_method) || empty($account_number)) {
            show_swal('All fields are required and amount must be positive.', 'error', 'Validation Error');
            redirect('referral/withdrawals');
        }

        $res = $this->referral_model->requestWithdrawal($referrer_id, $amount, $payment_method, $account_number, $notes, $user_id, $hospital_id);
        if ($res['status'] == 'success') {
            show_swal($res['message'], 'success', 'Withdrawal Requested');
        } else {
            show_swal($res['message'], 'error', 'Error');
        }
        redirect('referral/withdrawals');
    }

    public function updateWithdrawalStatus()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            redirect('home/permission');
        }

        $id = $this->input->post('id');
        $new_status = $this->input->post('status');
        $admin_notes = trim((string)$this->input->post('admin_notes'));
        $payment_reference = trim((string)$this->input->post('payment_reference'));
        $user_id = $this->ion_auth->get_user_id();

        $res = $this->referral_model->updateWithdrawalStatus($id, $new_status, $admin_notes, $payment_reference, $user_id);
        if ($res['status'] == 'success') {
            show_swal($res['message'], 'success', 'Status Updated');
        } else {
            show_swal($res['message'], 'error', 'Update Failed');
        }
        redirect('referral/withdrawals');
    }

    public function withdrawalSlip()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'))) {
            redirect('home/permission');
        }

        $id = $this->input->get('id');
        if (empty($id)) {
            $id = $this->input->get('ref');
        }
        if (empty($id)) {
            $id = $this->input->get('withdrawal_id');
        }
        if (empty($id)) {
            $id = $this->input->get('txn_id');
        }

        if (empty($id)) {
            show_swal('Invalid withdrawal request or missing slip identifier.', 'error', 'Error');
            redirect('referral/withdrawals');
            return;
        }

        $hospital_id = $this->session->userdata('hospital_id');
        $slip_data = $this->referral_model->getWithdrawalSlipData($id, $hospital_id);

        if (empty($slip_data) || empty($slip_data['withdrawal'])) {
            show_swal('Withdrawal record not found or access denied.', 'error', 'Record Not Found');
            redirect('referral/withdrawals');
            return;
        }

        $withdrawal = $slip_data['withdrawal'];

        // Strict Requirement: Do not allow a slip to be generated as a completed receipt for pending, rejected, or non-paid transactions
        if ($withdrawal->status !== 'Paid') {
            show_swal('An official withdrawal slip can only be generated for completed/paid withdrawals. The current status is: ' . $withdrawal->status, 'warning', 'Withdrawal Not Completed');
            redirect('referral/withdrawals');
            return;
        }

        $data = $slip_data;
        $data['settings'] = $slip_data['settings'];
        $data['redirect'] = '';

        $this->load->view('home/dashboard');
        $this->load->view('withdrawal_slip', $data);
        $this->load->view('home/footer');
    }

    public function downloadWithdrawalSlip()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'))) {
            redirect('home/permission');
        }

        $id = $this->input->get('id');
        if (empty($id)) {
            $id = $this->input->get('ref');
        }
        if (empty($id)) {
            $id = $this->input->get('withdrawal_id');
        }
        if (empty($id)) {
            $id = $this->input->get('txn_id');
        }

        if (empty($id)) {
            show_swal('Invalid withdrawal request or missing slip identifier.', 'error', 'Error');
            redirect('referral/withdrawals');
            return;
        }

        $hospital_id = $this->session->userdata('hospital_id');
        $slip_data = $this->referral_model->getWithdrawalSlipData($id, $hospital_id);

        if (empty($slip_data) || empty($slip_data['withdrawal'])) {
            show_swal('Withdrawal record not found or access denied.', 'error', 'Record Not Found');
            redirect('referral/withdrawals');
            return;
        }

        $withdrawal = $slip_data['withdrawal'];

        if ($withdrawal->status !== 'Paid') {
            show_swal('An official withdrawal slip can only be downloaded for completed/paid withdrawals. The current status is: ' . $withdrawal->status, 'warning', 'Withdrawal Not Completed');
            redirect('referral/withdrawals');
            return;
        }

        $data = $slip_data;
        $data['settings'] = $slip_data['settings'];
        $data['redirect'] = 'download';

        error_reporting(0);
        require_once APPPATH . '../vendor/autoload.php';

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A5',
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'freeserif'
        ]);

        $html = $this->load->view('withdrawal_slip_pdf', $data, true);
        $mpdf->WriteHTML($html);

        $filename = "Withdrawal-Slip-" . preg_replace('/[^A-Za-z0-9_\-]/', '', $withdrawal->withdrawal_id) . ".pdf";
        $mpdf->Output($filename, 'D');
    }


    // ==========================================
    // COMMISSION RULES CONFIGURATION
    // ==========================================

    public function commissionRules()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            redirect('home/permission');
        }
        $hospital_id = $this->session->userdata('hospital_id');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('status', 'Active');
        $this->db->order_by('category', 'asc');
        $data['categories'] = $this->db->get('payment_category')->result();
        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard');
        $this->load->view('commission_rules', $data);
        $this->load->view('home/footer');
    }

    public function updateItemCommission()
    {
        if (!$this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) {
            echo json_encode(array('status' => 'error', 'message' => 'Permission denied.'));
            return;
        }

        $id = $this->input->post('id');
        $rate = floatval($this->input->post('rate'));

        if ($rate < 0 || $rate > 100) {
            echo json_encode(array('status' => 'error', 'message' => 'Rate must be between 0 and 100%.'));
            return;
        }

        $hospital_id = $this->session->userdata('hospital_id');
        $item = $this->db->get_where('payment_category', array('id' => $id, 'hospital_id' => $hospital_id))->row();

        if (empty($item)) {
            echo json_encode(array('status' => 'error', 'message' => 'Item not found.'));
            return;
        }

        $old_rate = $item->r_commission;
        $this->db->where('id', $id)->update('payment_category', array('r_commission' => $rate));
        $this->referral_model->logAction('Commission Rate Changed', 'ITEM-' . $id, 'Old: ' . $old_rate . '%', 'New: ' . $rate . '%', $hospital_id);

        echo json_encode(array('status' => 'success', 'message' => 'Referral commission rate updated to ' . $rate . '%'));
    }

    // ==========================================
    // FINANCIAL REPORTING
    // ==========================================

    public function reports()
    {
        $hospital_id = $this->session->userdata('hospital_id');
        $filters = array('hospital_id' => $hospital_id);

        $referrer_id = $this->input->get('referrer_id');
        if (!empty($referrer_id)) {
            $filters['referrer_id'] = $referrer_id;
        }
        $doctor_id = $this->input->get('doctor_id');
        if (!empty($doctor_id)) {
            $filters['doctor_id'] = $doctor_id;
        }
        $date_from_str = $this->input->get('date_from');
        if (!empty($date_from_str)) {
            $filters['from_date'] = strtotime($date_from_str . ' 00:00:00');
        }
        $date_to_str = $this->input->get('date_to');
        if (!empty($date_to_str)) {
            $filters['to_date'] = strtotime($date_to_str . ' 23:59:59');
        }

        $data['report_data'] = $this->referral_model->getReferralReportData($filters);
        $data['referrers'] = $this->referral_model->getReferrers($hospital_id);
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['settings'] = $this->settings_model->getSettings();

        $data['selected_referrer'] = $referrer_id;
        $data['selected_doctor'] = $doctor_id;
        $data['date_from'] = $date_from_str;
        $data['date_to'] = $date_to_str;

        $this->load->view('home/dashboard');
        $this->load->view('reports', $data);
        $this->load->view('home/footer');
    }
}
