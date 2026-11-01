<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pharmacy extends MX_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('pharmacy_model');
        $this->load->model('medicine/medicine_model');
        $this->load->model('accountant/accountant_model');
        $this->load->model('settings/settings_model');
        $data['settings'] = $this->settings_model->getSettings();
        if (!$this->ion_auth->in_group(array('admin', 'Accountant', 'Pharmacist'))) {
            redirect('home/permission');
        }
    }

    function home()
    {
        $data = array();

        $data['accountants'] = $this->accountant_model->getAccountant();
        $data['latest_medicines'] = $this->medicine_model->getLatestMedicine();
        $data['settings'] = $this->settings_model->getSettings();
        $data['payments'] = $this->pharmacy_model->getPayment();
        $data['expenses'] = $this->pharmacy_model->getExpense();
        $data['today_sales_amount'] = $this->pharmacy_model->todaySalesAmount();
        $data['today_expenses_amount'] = $this->pharmacy_model->todayExpensesAmount();
        $data['medicines'] = $this->medicine_model->getMedicine();



        $data['this_month']['payment'] = $this->pharmacy_model->thisMonthPayment();
        $data['this_month']['expense'] = $this->pharmacy_model->thisMonthExpense();


        $data['this_day']['payment'] = $this->pharmacy_model->thisDayPayment();
        $data['this_day']['expense'] = $this->pharmacy_model->thisDayExpense();


        $data['this_year']['payment'] = $this->pharmacy_model->thisYearPayment();
        $data['this_year']['expense'] = $this->pharmacy_model->thisYearExpense();



        $data['this_year']['payment_per_month'] = $this->pharmacy_model->getPaymentPerMonthThisYear();


        $data['this_year']['expense_per_month'] = $this->pharmacy_model->getExpensePerMonthThisYear();




        $this->load->view('home/dashboard', $data);
        $this->load->view('finance/pharmacy/home', $data);
        $this->load->view('home/footer');
    }

    public function index()
    {
        redirect('pharmacy/financial_report');
    }

    public function payment()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['payments'] = $this->pharmacy_model->getPayment();



        $data['pagee_number'] = $page_number;
        $data['p_n'] = '0';

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/payment', $data);
        $this->load->view('home/footer');
    }

    public function paymentByPageNumber()
    {
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['payments'] = $this->pharmacy_model->getPaymentByPageNumber($page_number);
        $data['pagee_number'] = $page_number;
        $data['p_n'] = $page_number;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/payment', $data);
        $this->load->view('home/footer');
    }

    public function addPaymentView()
    {
        $data = array();
        $data['discount_type'] = $this->pharmacy_model->getDiscountType();
        $data['settings'] = $this->settings_model->getSettings();
        $data['medicines'] = $this->medicine_model->getMedicine();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/add_payment_view_enhanced', $data);
        $this->load->view('home/footer');
    }

    /**
     * Enhanced pharmacy sale view with batch management
     */
    public function addPaymentViewEnhanced()
    {
        $data = array();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/add_payment_view_enhanced', $data);
        $this->load->view('home/footer');
    }

    public function addPaymentViewDebug()
    {
        $data = array();
        $data['discount_type'] = $this->pharmacy_model->getDiscountType();
        $data['settings'] = $this->settings_model->getSettings();
        $data['medicines'] = $this->medicine_model->getMedicine();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/add_payment_view_new', $data);
        $this->load->view('home/footer');
    }

    function getMedicineByKeyJason()
    {
        $key = $this->input->get('keyword');
        $medicines = $this->medicine_model->getMedicineByKeyForPos($key);

        $data[] = array();
        $lists = array();
        $options = array();
        $selected = array();
        foreach ($medicines as $medicine) {
            if ($medicine->quantity > 0) {
                $lists[] = '<li class="ooppttiioonn ms-elem-selectable" data-id="' . $medicine->id . '" data-s_price="' . (float) $medicine->s_price . '" data-m_name="' . $medicine->name . '" data-c_name="' . trim($medicine->company) . '" id="' . $medicine->id . '-selectable"><span>' . $medicine->name . '</span></li>';
                $options[] = '<option class="ooppttiioonn" data-id="' . $medicine->id . '" data-s_price="' . (float) $medicine->s_price . '" data-m_name="' . $medicine->name . '" data-c_name="' . trim($medicine->company) . '" value="' . $medicine->id . '">' . $medicine->name . '</option>';
                $selected[] = '<li class="ooppttiioonn ms-elem-selection" data-id="' . $medicine->id . '" data-s_price="' . (float) $medicine->s_price . '"data-m_name="' . $medicine->name . '"data-c_name="' . trim($medicine->company) . '" id="' . $medicine->id . '-selection" style="display: none;"><span> ' . $medicine->name . '  </span></li>';
            }
        }
        $data['ltst'] = $lists;
        $data['opp'] = $options;
        $data['slt'] = $selected;

        $lists = NULL;
        $options = NULL;
        $selected = NULL;

        echo json_encode($data);
    }

    function searchPayment()
    {
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['p_n'] = $page_number;
        $key = $this->input->get('key');
        $data['payments'] = $this->pharmacy_model->getPaymentByKey($page_number, $key);
        $data['settings'] = $this->settings_model->getSettings();
        $data['pagee_number'] = $page_number;
        $data['key'] = $key;
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/payment', $data);
        $this->load->view('home/footer');
    }

    public function addPayment()
    {
        try {
            $id = $this->input->post('id');
            $item_selected = array();
            $quantity = array();
            $item_selected = $this->input->post('medicine_id');
            $quantity = $this->input->post('quantity');

            if (empty($item_selected)) {
                show_swal(lang('select_an_item'), 'error', lang('error'));
                redirect('finance/pharmacy/addPaymentView');
            } else {
                $item_quantity_array = array();
                
                // Handle both string and array formats
                if (is_string($item_selected)) {
                    $item_selected = explode(',', $item_selected);
                }
                if (is_string($quantity)) {
                    $quantity = explode(',', $quantity);
                }
                
                $item_quantity_array = array_combine($item_selected, $quantity);
            }
            
            foreach ($item_quantity_array as $key => $value) {
                $current_medicine = $this->db->get_where('medicine', array('id' => $key))->row();
                
                if (!$current_medicine) {
                    show_swal('Medicine not found. Please refresh and try again.', 'error', 'Error');
                    redirect('finance/pharmacy/addPaymentView');
                }
                
                $unit_price = $current_medicine->s_price;
                $cost = $current_medicine->price;
                $current_stock = (string) $current_medicine->quantity;
                $qty = $value;
                if ($current_stock < $qty) {
                    show_swal(lang('unsufficient_quantity_selected_for_medicine') . $current_medicine->name, 'error', 'quantity_check');
                    redirect('finance/pharmacy/addPaymentView');
                }
                $item_price[] = $unit_price * $value;
                $category_name[] = $key . '*' . $unit_price . '*' . $qty . '*' . $cost;
            }

            $category_name = implode(',', $category_name);

            $patient = $this->input->post('patient') ? $this->input->post('patient') : 'Walk-in Customer';
            $date = time();
            $discount = $this->input->post('discount') ? $this->input->post('discount') : 0;
            $amount_received = $this->input->post('amount_received');

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            // Validating Discount Field (optional)
            $this->form_validation->set_rules('discount', 'Discount', 'trim|xss_clean');

            if ($this->form_validation->run() == FALSE) {
                show_swal('Form validation failed. Please check your input.', 'error', 'Validation Error');
                redirect('finance/pharmacy/addPaymentView');
            } else {
                $amount = array_sum($item_price);
                $sub_total = $amount;
                
                // Always use flat discount
                $flat_discount = $discount;
                $gross_total = $sub_total - $flat_discount;

                $data = array();
                if (empty($id)) {
                    $data = array(
                        'category_name' => $category_name,
                        'patient' => $patient,
                        'date' => $date,
                        'amount' => $sub_total,
                        'discount' => $discount,
                        'flat_discount' => $flat_discount,
                        'gross_total' => $gross_total,
                        'amount_received' => $amount_received,
                        'status' => 'unpaid',
                        'hospital_id' => $this->session->userdata('hospital_id'),
                    );
                    
                    $this->pharmacy_model->insertPayment($data);
                    $inserted_id = $this->db->insert_id();
                    
                    foreach ($item_quantity_array as $key => $value) {
                        // Use batch-aware stock reduction with FIFO logic
                        $this->reduceMedicineStockWithBatches($key, $value, $inserted_id);
                    }
                    
                    show_swal(lang('pharmacy_payment_added_successfully'), 'success', lang('added'));
                    redirect("finance/pharmacy/invoice?id=" . "$inserted_id");
                } else {
                    $data = array(
                    'category_name' => $category_name,
                    'patient' => $patient,
                    'amount' => $sub_total,
                    'discount' => $discount,
                    'flat_discount' => $flat_discount,
                    'gross_total' => $gross_total,
                    'amount_received' => $amount_received,
                );

                $original_sale = $this->pharmacy_model->getPaymentById($id);
                $original_sale_quantity = array();
                $original_sale_quantity = explode(',', $original_sale->category_name);
                $o_s_value[] = array();
                // For edit operations, we need to reverse the original sale and apply the new one
                // This is complex with batches, so for now we'll log this as a new transaction
                // TODO: Implement proper batch-aware edit functionality
                foreach ($item_quantity_array as $key => $value) {
                    // Use batch-aware stock reduction
                    $this->reduceMedicineStockWithBatches($key, $value, $id);
                }
                $this->pharmacy_model->updatePayment($id, $data);
                show_swal(lang('pharmacy_payment_updated_successfully'), 'success', lang('updated'));
                redirect("finance/pharmacy/invoice?id=" . "$id");
                }
            }
        } catch (Exception $e) {
            // Show user-friendly error message
            show_swal('An error occurred while processing the sale. Please try again.', 'error', 'Error');
            redirect('finance/pharmacy/addPaymentView');
        }
    }

    function editPayment()
    {
        if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Pharmacist'))) {
            $data = array();
            $data['settings'] = $this->settings_model->getSettings();
            $data['medicines'] = $this->medicine_model->getMedicine();
            $id = $this->input->get('id');
            $data['payment'] = $this->pharmacy_model->getPaymentById($id);

            if ($data['payment']->hospital_id != $this->session->userdata('hospital_id')) {
                redirect('home/permission');
            }

            $this->load->view('home/dashboard', $data);
            $this->load->view('pharmacy/add_payment_view_enhanced', $data);
            $this->load->view('home/footer'); // just the footer file
        }
    }

    function delete()
    {
        if ($this->ion_auth->in_group('admin')) {
            $id = $this->input->get('id');

            $payment_details = $this->pharmacy_model->getPaymentById($id);
            if ($payment_details->hospital_id != $this->session->userdata('hospital_id')) {
                redirect('home/permission');
            }

            $category_name = $this->pharmacy_model->getPaymentById($id)->category_name;
            $all_product_details = array();
            $all_product_details = explode(',', $category_name);

            foreach ($all_product_details as $key => $value) {
                $product_details = array();
                $product_details = explode('*', $value);
                $product_id = $product_details[0];
                $qty = $product_details[2];
                $previous_qty = $this->medicine_model->getMedicineById($product_details[0])->quantity;
                $new_qty = $previous_qty + $qty;
                $data = array();
                $data = array('quantity' => $new_qty);
                $this->medicine_model->updateMedicine($product_id, $data);
            }

            $this->pharmacy_model->deletePayment($id);
            show_swal(lang('pharmacy_payment_deleted'), 'warning', lang('deleted'));
            redirect('finance/pharmacy/payment');
        }
    }

    public function expense()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['expenses'] = $this->pharmacy_model->getExpense();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/expense', $data);
        $this->load->view('home/footer');
    }

    public function addExpenseView()
    {
        $data = array();
        $data['settings'] = $this->settings_model->getSettings();
        $data['categories'] = $this->pharmacy_model->getExpenseCategory();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/add_expense_view', $data);
        $this->load->view('home/footer');
    }

    public function addExpense()
    {
        $id = $this->input->post('id');
        $category = $this->input->post('category');
        $date = time();
        $amount = $this->input->post('amount');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Category Field
        $this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Generic Name Field
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Company Name Field
        if ($this->form_validation->run() == FALSE) {
            $data = array();
            $data['settings'] = $this->settings_model->getSettings();
            $data['categories'] = $this->pharmacy_model->getExpenseCategory();
            $this->load->view('home/dashboard', $data);
            $this->load->view('add_expense_view', $data);
            $this->load->view('home/footer');
        } else {
            $data = array();
            if (empty($id)) {
                $data = array(
                    'category' => $category,
                    'date' => $date,
                    'amount' => $amount
                );
            } else {
                $data = array(
                    'category' => $category,
                    'amount' => $amount
                );
            }
            if (empty($id)) {
                $this->pharmacy_model->insertExpense($data);
                show_swal(lang('pharmacy_expense_added_successfully'), 'success', lang('added'));
            } else {
                $this->pharmacy_model->updateExpense($id, $data);
                show_swal(lang('pharmacy_expense_updated_successfully'), 'success', lang('updated'));
            }
            redirect('finance/pharmacy/expense');
        }
    }

    function editExpense()
    {
        $data = array();
        $data['categories'] = $this->pharmacy_model->getExpenseCategory();
        $data['settings'] = $this->settings_model->getSettings();
        $id = $this->input->get('id');
        $data['expense'] = $this->pharmacy_model->getExpenseById($id);

        if ($data['expense']->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/add_expense_view', $data);
        $this->load->view('home/footer'); // just the footer file
    }

    function deleteExpense()
    {
        $id = $this->input->get('id');

        $data['expense'] = $this->pharmacy_model->getExpenseById($id);
        if ($data['expense']->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $this->pharmacy_model->deleteExpense($id);
        show_swal(lang('pharmacy_expense_deleted'), 'warning', lang('deleted'));
        redirect('finance/pharmacy/expense');
    }

    public function expenseCategory()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['categories'] = $this->pharmacy_model->getExpenseCategory();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/expense_category', $data);
        $this->load->view('home/footer');
    }

    public function addExpenseCategoryView()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/add_expense_category');
        $this->load->view('home/footer');
    }

    public function addExpenseCategory()
    {
        $id = $this->input->post('id');
        $category = trim((string)$this->input->post('category'));
        $description = trim((string)$this->input->post('description'));
        if (empty($description)) {
            $description = $category;
        }
        $is_ajax = $this->input->is_ajax_request() || $this->input->post('is_ajax') || $this->input->get('is_ajax');

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Category Name Field
        $this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Description Field
        $this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            if ($is_ajax) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => validation_errors() ?: 'Please provide a valid category name.'
                ));
                return;
            }
            $data['settings'] = $this->settings_model->getSettings();
            $this->load->view('home/dashboard', $data);
            $this->load->view('pharmacy/add_expense_category');
            $this->load->view('home/footer');
        } else {
            $data = array();
            $data = array(
                'category' => $category,
                'description' => $description
            );
            if (empty($id)) {
                $inserted_id = $this->pharmacy_model->insertExpenseCategory($data);
                if ($is_ajax) {
                    echo json_encode(array(
                        'status' => 'success',
                        'id' => $inserted_id,
                        'category' => $category,
                        'message' => 'Pharmacy category added successfully!'
                    ));
                    return;
                }
                show_swal(lang('pharmacy_expense_category_added'), 'success', lang('added'));
            } else {
                $this->pharmacy_model->updateExpenseCategory($id, $data);
                if ($is_ajax) {
                    echo json_encode(array(
                        'status' => 'success',
                        'id' => $id,
                        'category' => $category,
                        'message' => 'Pharmacy category updated successfully!'
                    ));
                    return;
                }
                show_swal(lang('pharmacy_expense_category_updated'), 'success', lang('updated'));
            }
            redirect('finance/pharmacy/expenseCategory');
        }
    }

    function editExpenseCategory()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['category'] = $this->pharmacy_model->getExpenseCategoryById($id);

        if ($data['category']->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/add_expense_category', $data);
        $this->load->view('home/footer'); // just the footer file
    }

    function deleteExpenseCategory()
    {
        $id = $this->input->get('id');

        $data['category'] = $this->pharmacy_model->getExpenseCategoryById($id);
        if ($data['category']->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $this->pharmacy_model->deleteExpenseCategory($id);
        show_swal(lang('pharmacy_expense_category_deleted'), 'warning', lang('deleted'));
        redirect('finance/pharmacy/expenseCategory');
    }

    function invoice()
    {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->pharmacy_model->getDiscountType();
        $data['payment'] = $this->pharmacy_model->getPaymentById($id);
        $data['payment_items'] = $this->pharmacy_model->getPaymentItems($id);
        
        if ($data['payment']->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/invoice', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function printInvoice()
    {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->pharmacy_model->getDiscountType();
        $data['payment'] = $this->pharmacy_model->getPaymentById($id);
        $data['payment_items'] = $this->pharmacy_model->getPaymentItems($id);
        
        if ($data['payment']->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/print_invoice', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function downloadInvoice()
    {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->pharmacy_model->getDiscountType();
        $data['payment'] = $this->pharmacy_model->getPaymentById($id);
        $data['payment_items'] = $this->pharmacy_model->getPaymentItems($id);
        
        if ($data['payment']->hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        // Load the PDF library
        $this->load->library('pdf');
        
        // Generate PDF content
        $html = $this->load->view('pharmacy/invoice_pdf', $data, TRUE);
        
        // Create PDF
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        
        // Download the PDF
        $filename = 'Invoice_' . $id . '_' . date('Y-m-d') . '.pdf';
        $this->pdf->stream($filename, array('Attachment' => 1));
    }

    function amountReceived()
    {
        $id = $this->input->post('id');
        $amount_received = $this->input->post('amount_received');
        $previous_amount_received = $this->db->get_where('pharmacy_payment', array('id' => $id))->row()->amount_received;
        $amount_received = $amount_received + $previous_amount_received;
        $data = array();
        $data = array('amount_received' => $amount_received);
        $this->pharmacy_model->amountReceived($id, $data);
        redirect('finance/pharmacy/invoice?id=' . $id);
    }

    function amountReceivedFromPT()
    {
        $id = $this->input->post('id');
        $amount_received = $this->input->post('amount_received');
        $payments = $this->pharmacy_model->getPaymentByPatientId($id);
        foreach ($payments as $payment) {
            if ($payment->gross_total != $payment->amount_received) {
                $due_balance = $payment->gross_total - $payment->amount_received;
                if ($amount_received <= $due_balance) {
                    $data = array();
                    $new_amount_received = $amount_received + $payment->amount_received;
                    $data = array('amount_received' => $new_amount_received);
                    $this->pharmacy_model->amountReceived($payment->id, $data);
                    break;
                } else {
                    $data = array();
                    $new_amount_received = $due_balance + $payment->amount_received;
                    $data = array('amount_received' => $new_amount_received);
                    $this->pharmacy_model->amountReceived($payment->id, $data);
                    $amount_received = $amount_received - $due_balance;
                }
            }
        }
        redirect('finance/pharmacy/invoicePatientTotal?id=' . $id);
    }

    function todaySales()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $hour = 0;
        $today = strtotime($hour . ':00:00');
        $today_last = strtotime($hour . ':00:00') + 24 * 60 * 60;
        $data['settings'] = $this->settings_model->getSettings();
        $data['payments'] = $this->pharmacy_model->getPaymentByDate($today, $today_last);

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/today_sales', $data);
        $this->load->view('home/footer');
    }

    function todayExpense()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $hour = 0;
        $today = strtotime($hour . ':00:00');
        $today_last = strtotime($hour . ':00:00') + 24 * 60 * 60;
        $data['settings'] = $this->settings_model->getSettings();
        $data['expenses'] = $this->pharmacy_model->getExpenseByDate($today, $today_last);

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/today_expenses', $data);
        $this->load->view('home/footer');
    }

    function todayNetCash()
    {
        $data['today_sales_amount'] = $this->pharmacy_model->todaySalesAmount();
        $data['today_expenses_amount'] = $this->pharmacy_model->todayExpensesAmount();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/today_net_cash', $data);
        $this->load->view('home/footer');
    }

    function salesPerMonth()
    {

        $payments = $this->pharmacy_model->getPayment();
        foreach ($payments as $payment) {
            $date = $payment->date;
            $month = date('m', $date);
            $year = date('y', $date);
            if ($month = '01') {
            }
        }
    }

    function financialReport()
    {
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 24 * 60 * 60;
        }
        $data = array();
        $data['medicines'] = $this->medicine_model->getMedicine();
        $data['expense_categories'] = $this->pharmacy_model->getExpenseCategory();


        $data['payments'] = $this->pharmacy_model->getPaymentByDate($date_from, $date_to);
        $data['expenses'] = $this->pharmacy_model->getExpenseByDate($date_from, $date_to);

        $data['from'] = $this->input->post('date_from');
        $data['to'] = $this->input->post('date_to');
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/financial_report', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function getPaymentList()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "date",
            "2" => "amount",
            "3" => "discount",
            "4" => "gross_total",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['payments'] = $this->pharmacy_model->getPaymentBysearch($search, $order, $dir);
            } else {
                $data['payments'] = $this->pharmacy_model->getPaymentWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['payments'] = $this->pharmacy_model->getPaymentByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['payments'] = $this->pharmacy_model->getPaymentByLimit($limit, $start, $order, $dir);
            }
        }



        $i = 0;
        $option1 = '';
        $option2 = '';
        $option3 = '';
        foreach ($data['payments'] as $payment) {
            //$i = $i + 1;
            $settings = $this->settings_model->getSettings();
            if ($this->ion_auth->in_group(array('admin', 'Pharmacist'))) {
                $option1 = '<a class="btn btn-primary btn-sm editbutton" href="finance/pharmacy/editPayment?id=' . $payment->id . '"><i class="fa fa-edit"> </i> ' . lang('edit') . '</a>';
            }
            if ($this->ion_auth->in_group('admin')) {
                $option2 = '<a class="btn btn-danger btn-sm btn_width delete_button" href="finance/pharmacy/delete?id=' . $payment->id . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"> </i></a>';
            }
            $option3 = '<a class="btn btn-success btn-sm" href="finance/pharmacy/invoice?id=' . $payment->id . '"><i class="fa fa-file-invoice"></i> ' . lang('invoice') . '</a>';
            $options4 = '<a class="btn btn-secondary btn-sm invoicebutton" title="' . lang('print') . '" href="finance/pharmacy/printInvoice?id=' . $payment->id . '"target="_blank"> <i class="fa fa-print"></i> ' . lang('print') . '</a>';
            if (!empty($payment->flat_discount)) {
                $discount = number_format($payment->flat_discount, 2, '.', ',');
            } else {
                $discount = '0';
            }



            $dropdownOptions = '';
            $dropdownOptions = '
            <div class="btn-group">
            <button type="button" class="btn btn-info btn-sm label-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
            <i class="fas fa-bars"></i> ' . lang('actions') . ' <span class="caret"></span>
        </button>
                <ul class="dropdown-menu">
                    ' . ($option1 ? '<li><a target="_blank" href="finance/pharmacy/editPayment?id=' . $payment->id . '">' . lang('edit') . ' <i class="fa fa-edit"></i></a></li>' : '') . '
                    ' . ($option3 ? '<li><a target="_blank" href="finance/pharmacy/invoice?id=' . $payment->id . '">' . lang('invoice') . ' <i class="fa fa-file-invoice"></i></a></li>' : '') . '
                    ' . ($options4 ? '<li><a href="' . site_url("finance/pharmacy/printInvoice?id=" . $payment->id) . '" target="_blank">' . lang('print') . ' <i class="fa fa-print"></i></a></li>' : '') . '
                    ' . ($option2 ? '<li><a href="' . site_url("finance/pharmacy/delete?id=" . $payment->id) . '" onclick="return confirm(\'Are you sure you want to delete this item?\');">' . lang('delete') . ' <i class="fa fa-trash"></i></a></li>' : '') . '
                </ul>
            </div>';



            $info[] = array(
                $payment->id,
                date('d/m/y', $payment->date + 11 * 60 * 60),
                $settings->currency . '' . number_format($payment->amount, 2, '.', ','),
                $settings->currency . '' . $discount,
                $settings->currency . '' . number_format($payment->gross_total, 2, '.', ','),
                // $dropdownOptions
                $option1 . ' ' . $option3 . ' ' . $options4 . ' ' . $option2
            );
            $i = $i + 1;
        }

        if ($data['payments']) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->pharmacy_model->getPayment()),
                "recordsFiltered" => count($this->pharmacy_model->getPayment()),
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

    function previousInvoice()
    {
        $id = $this->input->get('id');
        $data1 = $this->pharmacy_model->getFirstRowPaymentById();
        if ($id == $data1->id) {
            $data = $this->pharmacy_model->getLastRowPaymentById();
            redirect('finance/pharmacy/invoice?id=' . $data->id);
        } else {
            for ($id1 = $id - 1; $id1 >= $data1->id; $id1--) {

                $data = $this->pharmacy_model->getPreviousPaymentById($id1);
                if (!empty($data)) {
                    redirect('finance/pharmacy/invoice?id=' . $data->id);
                    break;
                } elseif ($id1 == $data1->id) {
                    $data = $this->pharmacy_model->getLastRowPaymentById();
                    redirect('finance/pharmacy/invoice?id=' . $data->id);
                } else {
                    continue;
                }
            }
        }
    }

    function nextInvoice()
    {
        $id = $this->input->get('id');


        $data1 = $this->pharmacy_model->getLastRowPaymentById();



        if ($id == $data1->id) {
            $data = $this->pharmacy_model->getFirstRowPaymentById();
            redirect('finance/pharmacy/invoice?id=' . $data->id);
        } else {
            for ($id1 = $id + 1; $id1 <= $data1->id; $id1++) {

                $data = $this->pharmacy_model->getNextPaymentById($id1);


                if (!empty($data)) {
                    redirect('finance/pharmacy/invoice?id=' . $data->id);
                    break;
                } elseif ($id1 == $data1->id) {
                    $data = $this->pharmacy_model->getFirstRowPaymentById();
                    redirect('finance/pharmacy/invoice?id=' . $data->id);
                } else {
                    continue;
                }
            }
        }
    }

    function daily()
    {
        $data = array();
        $year = $this->input->get('year');
        $month = $this->input->get('month');

        if (empty($year)) {
            $year = date('Y');
        }
        if (empty($month)) {
            $month = date('m');
        }

        $first_minute = mktime(0, 0, 0, $month, 1, $year);
        $last_minute = mktime(23, 59, 59, $month, date("t", $first_minute), $year);

        $payments = $this->pharmacy_model->getPaymentByDate($first_minute, $last_minute);
        $all_payments = array();
        foreach ($payments as $payment) {
            $date = date('D d-m-y', $payment->date);
            if (array_key_exists($date, $all_payments)) {
                $all_payments[$date] = $all_payments[$date] + $payment->gross_total;
            } else {
                $all_payments[$date] = $payment->gross_total;
            }
        }

        $data['year'] = $year;
        $data['month'] = $month;
        $data['first_minute'] = $first_minute;
        $data['last_minute'] = $last_minute;
        $data['all_payments'] = $all_payments;

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/daily', $data);
        $this->load->view('home/footer');
    }

    function dailyExpense()
    {
        $data = array();
        $year = $this->input->get('year');
        $month = $this->input->get('month');

        if (empty($year)) {
            $year = date('Y');
        }
        if (empty($month)) {
            $month = date('m');
        }

        $first_minute = mktime(0, 0, 0, $month, 1, $year);
        $last_minute = mktime(23, 59, 59, $month, date("t", $first_minute), $year);

        $expenses = $this->pharmacy_model->getExpenseByDate($first_minute, $last_minute);
        $all_expenses = array();
        foreach ($expenses as $expense) {
            $date = date('D d-m-y', $expense->date);
            if (array_key_exists($date, $all_expenses)) {
                $all_expenses[$date] = $all_expenses[$date] + $expense->amount;
            } else {
                $all_expenses[$date] = $expense->amount;
            }
        }

        $data['year'] = $year;
        $data['month'] = $month;
        $data['first_minute'] = $first_minute;
        $data['last_minute'] = $last_minute;
        $data['all_expenses'] = $all_expenses;



        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/daily_expense', $data);
        $this->load->view('home/footer');
    }

    function monthly()
    {
        $data = array();
        $year = $this->input->get('year');

        if (empty($year)) {
            $year = date('Y');
        }


        $first_minute = mktime(0, 0, 0, 1, 1, $year);
        $last_minute = mktime(23, 59, 59, 12, 31, $year);

        $payments = $this->pharmacy_model->getPaymentByDate($first_minute, $last_minute);
        $all_payments = array();
        foreach ($payments as $payment) {
            $month = date('m-Y', $payment->date);
            if (array_key_exists($month, $all_payments)) {
                $all_payments[$month] = $all_payments[$month] + $payment->gross_total;
            } else {
                $all_payments[$month] = $payment->gross_total;
            }
        }

        $data['year'] = $year;
        $data['first_minute'] = $first_minute;
        $data['last_minute'] = $last_minute;
        $data['all_payments'] = $all_payments;

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/monthly', $data);
        $this->load->view('home/footer');
    }

    function monthlyExpense()
    {
        $data = array();
        $year = $this->input->get('year');

        if (empty($year)) {
            $year = date('Y');
        }


        $first_minute = mktime(0, 0, 0, 1, 1, $year);
        $last_minute = mktime(23, 59, 59, 12, 31, $year);

        $expenses = $this->pharmacy_model->getExpenseByDate($first_minute, $last_minute);
        $all_expenses = array();
        foreach ($expenses as $expense) {
            $month = date('m-Y', $expense->date);
            if (array_key_exists($month, $all_expenses)) {
                $all_expenses[$month] = $all_expenses[$month] + $expense->amount;
            } else {
                $all_expenses[$month] = $expense->amount;
            }
        }

        $data['year'] = $year;
        $data['first_minute'] = $first_minute;
        $data['last_minute'] = $last_minute;
        $data['all_expenses'] = $all_expenses;

        $this->load->view('home/dashboard', $data);
        $this->load->view('pharmacy/monthly_expense', $data);
        $this->load->view('home/footer');
    }

    // ========== BATCH-AWARE INVENTORY MANAGEMENT ==========
    
    /**
     * Reduce medicine stock using FIFO batch logic
     * @param int $medicine_id
     * @param int $quantity_to_reduce
     * @param int $sale_reference_id
     */
    private function reduceMedicineStockWithBatches($medicine_id, $quantity_to_reduce, $sale_reference_id)
    {
        try {
            // Check if medicine_batches table exists
            if (!$this->db->table_exists('medicine_batches')) {
                // Fallback to simple stock reduction
                $this->reduceMedicineStockSimple($medicine_id, $quantity_to_reduce);
                return;
            }
            
            // Get available batches ordered by expiry date (FIFO)
            $this->db->where('medicine_id', $medicine_id);
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('current_stock >', 0);
            $this->db->where('expiry_date >', date('Y-m-d')); // Only non-expired batches
            $this->db->order_by('expiry_date', 'asc'); // FIFO - oldest first
            $batches = $this->db->get('medicine_batches')->result();
        
        $remaining_to_reduce = $quantity_to_reduce;
        
        foreach ($batches as $batch) {
            if ($remaining_to_reduce <= 0) break;
            
            $quantity_from_this_batch = min($remaining_to_reduce, $batch->current_stock);
            
            // Update batch stock
            $new_batch_stock = $batch->current_stock - $quantity_from_this_batch;
            $new_quantity_sold = $batch->quantity_sold + $quantity_from_this_batch;
            
            $this->db->where('id', $batch->id);
            $this->db->update('medicine_batches', array(
                'current_stock' => $new_batch_stock,
                'quantity_sold' => $new_quantity_sold
            ));
            
            // Log stock movement
            $this->medicine_model->logStockMovement(
                $medicine_id, 
                $batch->id, 
                'sale', 
                $quantity_from_this_batch, 
                'prescription', 
                $sale_reference_id,
                'Pharmacy sale - FIFO batch reduction'
            );
            
            $remaining_to_reduce -= $quantity_from_this_batch;
        }
        
            // Update total medicine quantity
            $this->medicine_model->updateMedicineTotalStock($medicine_id);
            
            if ($remaining_to_reduce > 0) {
                log_message('warning', "Insufficient stock for medicine ID: $medicine_id. Short by: $remaining_to_reduce units");
            }
            
        } catch (Exception $e) {
            // Fallback to simple stock reduction
            $this->reduceMedicineStockSimple($medicine_id, $quantity_to_reduce);
        }
    }
    
    /**
     * Simple stock reduction fallback method
     * @param int $medicine_id
     * @param int $quantity_to_reduce
     */
    private function reduceMedicineStockSimple($medicine_id, $quantity_to_reduce)
    {
        try {
            // Update main medicine stock
            $this->db->where('id', $medicine_id);
            $this->db->set('quantity', 'quantity - ' . $quantity_to_reduce, FALSE);
            $this->db->update('medicine');
        } catch (Exception $e) {
            // Log error but don't stop the process
            log_message('error', 'Error in reduceMedicineStockSimple: ' . $e->getMessage());
        }
    }

    /**
     * Get available medicines with batch information for AJAX selection
     */
    public function getMedicinesWithBatches()
    {
        $searchTerm = $this->input->post('searchTerm');
        $hospital_id = $this->session->userdata('hospital_id');
        
        // Debug logging
        log_message('debug', 'getMedicinesWithBatches called with searchTerm: ' . $searchTerm . ', hospital_id: ' . $hospital_id);
        
        // First, get all medicines with stock > 0 (simpler approach)
        $this->db->select('m.id, m.name, m.generic, m.company, m.s_price, m.quantity');
        $this->db->from('medicine m');
        $this->db->where('m.hospital_id', $hospital_id);
        $this->db->where('m.quantity >', 0);
        
        if (!empty($searchTerm)) {
            $this->db->group_start();
            $this->db->like('m.name', $searchTerm);
            $this->db->or_like('m.generic', $searchTerm);
            $this->db->or_like('m.company', $searchTerm);
            $this->db->group_end();
        }
        
        $this->db->order_by('m.name', 'asc');
        $this->db->limit(50);
        
        $medicines = $this->db->get()->result();
        
        log_message('debug', 'Found ' . count($medicines) . ' medicines for hospital_id: ' . $hospital_id);
        
        $response = array();
        foreach ($medicines as $medicine) {
            // Ensure all fields have values
            $medicine_id = $medicine->id ?: '';
            $name = $medicine->name ?: 'Unknown Medicine';
            $company = $medicine->company ?: 'Unknown Company';
            $price = $medicine->s_price ?: 0;
            $quantity = $medicine->quantity ?: 0;
            
            log_message('debug', 'Processing medicine - ID: ' . $medicine_id . ', Name: ' . $name . ', Company: ' . $company);
            
            $response[] = array(
                'id' => $medicine_id . '*' . $price . '*' . $name . '*' . $company . '*' . $quantity . '*' . 'N/A',
                'text' => $name . ' (Stock: ' . $quantity . ')',
                'medicine_id' => $medicine_id,
                'price' => $price,
                'stock' => $quantity,
                'batch' => 'N/A',
                'expiry_date' => 'N/A',
                'days_to_expiry' => 365,
                'supplier' => 'N/A'
            );
        }
        
        log_message('debug', 'Returning ' . count($response) . ' medicine options');
        
        echo json_encode($response);
    }

    /**
     * Get medicines for pharmacy sale (simple version without batches)
     * This method provides a fallback for systems not using batch management
     */
    public function getMedicinesSimple()
    {
        $searchTerm = $this->input->post('searchTerm');
        
        $this->db->select('id, name, generic, company, s_price, quantity');
        $this->db->from('medicine');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('quantity >', 0); // Only medicines with stock
        
        if (!empty($searchTerm)) {
            $this->db->group_start();
            $this->db->like('name', $searchTerm);
            $this->db->or_like('generic', $searchTerm);
            $this->db->group_end();
        }
        
        $this->db->order_by('name', 'asc');
        $this->db->limit(50);
        
        $medicines = $this->db->get()->result();
        
        $response = array();
        foreach ($medicines as $medicine) {
            $response[] = array(
                'id' => $medicine->id . '*' . $medicine->s_price . '*' . $medicine->name . '*' . $medicine->company . '*' . $medicine->quantity . '*' . 'N/A',
                'text' => $medicine->name . ' (Stock: ' . $medicine->quantity . ')',
                'medicine_id' => $medicine->id,
                'price' => $medicine->s_price,
                'stock' => $medicine->quantity,
                'batch' => 'N/A',
                'expiry_date' => 'N/A',
                'days_to_expiry' => 365,
                'supplier' => 'N/A'
            );
        }
        
        echo json_encode($response);
    }

    /**
     * Get medicine stock summary with batch details
     */
    public function getMedicineStockSummary()
    {
        $medicine_id = $this->input->post('medicine_id');
        
        if (!$medicine_id) {
            echo json_encode(array('error' => 'Medicine ID required'));
            return;
        }
        
        // Get medicine basic info
        $medicine = $this->medicine_model->getMedicineById($medicine_id);
        
        // Get available batches
        $this->db->select('mb.*, ms.name as supplier_name');
        $this->db->from('medicine_batches mb');
        $this->db->join('medicine_suppliers ms', 'mb.supplier_id = ms.id', 'left');
        $this->db->where('mb.medicine_id', $medicine_id);
        $this->db->where('mb.hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('mb.current_stock >', 0);
        $this->db->where('mb.expiry_date >', date('Y-m-d'));
        $this->db->order_by('mb.expiry_date', 'asc');
        $batches = $this->db->get()->result();
        
        $total_available = 0;
        $batch_details = array();
        
        foreach ($batches as $batch) {
            $total_available += $batch->current_stock;
            $days_to_expiry = (strtotime($batch->expiry_date) - time()) / (60 * 60 * 24);
            
            $batch_details[] = array(
                'batch_number' => $batch->batch_number,
                'stock' => $batch->current_stock,
                'expiry_date' => $batch->expiry_date,
                'days_to_expiry' => round($days_to_expiry),
                'selling_price' => $batch->selling_price,
                'supplier' => $batch->supplier_name,
                'expiry_status' => $days_to_expiry <= 30 ? 'warning' : 'good'
            );
        }
        
        echo json_encode(array(
            'medicine' => $medicine,
            'total_available' => $total_available,
            'batches' => $batch_details
        ));
    }
}

/* End of file pharmacy.php */
/* Location: ./application/modules/pharmacy/controllers/pharmacy.php */