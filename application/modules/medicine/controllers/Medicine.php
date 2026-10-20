<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Medicine extends MX_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('medicine_model');
        if (!$this->ion_auth->in_group(array('admin', 'Pharmacist', 'Doctor', 'Nurse', 'Receptionist'))) {
            redirect('home/permission');
        }
    }

    public function index()
    {

        $data['medicines'] = $this->medicine_model->getMedicine();
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard', $data);
        $this->load->view('medicine', $data);
        $this->load->view('home/footer');
    }

    public function medicineByPageNumber()
    {
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['medicines'] = $this->medicine_model->getMedicineByPageNumber($page_number);
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $data['pagee_number'] = $page_number;
        $data['p_n'] = $page_number;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('medicine', $data);
        $this->load->view('home/footer');
    }

    public function medicineStockAlert()
    {
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['p_n'] = '0';
        $data['medicines'] = $this->medicine_model->getMedicineByStockAlert($page_number);

        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $data['pagee_number'] = $page_number;
        $data['settings'] = $this->settings_model->getSettings();
        $data['alert'] = 'Alert Stock';
        $this->load->view('home/dashboard', $data);
        $this->load->view('medicine_stock_alert', $data);
        $this->load->view('home/footer');
    }

    public function medicineStockAlertByPageNumber()
    {
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['p_n'] = $page_number;
        $data['medicines'] = $this->medicine_model->getMedicineByStockAlert($page_number);
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $data['pagee_number'] = $page_number;
        $data['alert'] = 'Alert Stock';
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('medicine_stock_alert', $data);
        $this->load->view('home/footer');
    }

    function searchMedicine()
    {
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['p_n'] = $page_number;
        $key = $this->input->get('key');
        $data['medicines'] = $this->medicine_model->getMedicineByKey($page_number, $key);
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $data['settings'] = $this->settings_model->getSettings();
        $data['pagee_number'] = $page_number;
        $data['key'] = $key;
        $this->load->view('home/dashboard', $data);
        $this->load->view('medicine', $data);
        $this->load->view('home/footer');
    }

    function searchMedicineInAlertStock()
    {
        $page_number = $this->input->get('page_number');
        if (empty($page_number)) {
            $page_number = 0;
        }
        $data['p_n'] = $page_number;
        $key = $this->input->get('key');
        $data['medicines'] = $this->medicine_model->getMedicineByKeyByStockAlert($page_number, $key);
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $data['settings'] = $this->settings_model->getSettings();
        $data['pagee_number'] = $page_number;
        $data['key'] = $key;
        $this->load->view('home/dashboard', $data);
        $this->load->view('medicine_stock_alert', $data);
        $this->load->view('home/footer');
    }

    public function addMedicineView()
    {
        $data = array();
        $data['settings'] = $this->settings_model->getSettings();
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $this->load->view('home/dashboard', $data);
        $this->load->view('add_new_medicine_view', $data);
        $this->load->view('home/footer');
    }

    public function addNewMedicine()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $category = $this->input->post('category');
        $price = $this->input->post('price');
        $box = $this->input->post('box');
        $s_price = $this->input->post('s_price');
        $generic = $this->input->post('generic');
        $company = $this->input->post('company');
        $effects = $this->input->post('effects');
        if ((empty($id))) {
            $add_date = date('m/d/y');
        } else {
            $add_date = $this->db->get_where('medicine', array('id' => $id))->row()->add_date;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Name Field
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]|xss_clean');
        // Validating Category Field
        $this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Purchase Price Field
        $this->form_validation->set_rules('price', 'Purchase Price', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Store Box Field
        $this->form_validation->set_rules('box', 'Store Box', 'trim|min_length[1]|max_length[100]|xss_clean');
        // Validating Selling Price Field
        $this->form_validation->set_rules('s_price', 'Selling Price', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        // Validating Generic Name Field
        $this->form_validation->set_rules('generic', 'Generic Name', 'trim|required|min_length[2]|max_length[100]|xss_clean');
        // Validating Company Name Field
        $this->form_validation->set_rules('company', 'Company', 'trim|min_length[2]|max_length[100]|xss_clean');
        // Validating Effects Field
        $this->form_validation->set_rules('effects', 'Effects', 'trim|min_length[2]|max_length[100]|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect('medicine/editMedicine?id=' . $id);
            } else {
                $data = array();
                $data['categories'] = $this->medicine_model->getMedicineCategory();
                $data['settings'] = $this->settings_model->getSettings();
                $this->load->view('home/dashboard', $data);
                $this->load->view('add_new_medicine_view', $data);
                $this->load->view('home/footer');
            }
        } else {
            $data = array();
            $data = array(
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'box' => $box,
                's_price' => $s_price,
                'generic' => $generic,
                'company' => $company,
                'effects' => $effects,
                'add_date' => $add_date,
            );
            if (empty($id)) {
                $this->medicine_model->insertMedicine($data);
                show_swal(lang('added'), 'success', lang('added'));
            } else {
                $this->medicine_model->updateMedicine($id, $data);
                show_swal(lang('updated'), 'success', lang('updated'));
            }
            redirect('medicine');
        }
    }

    function editMedicine()
    {
        $data = array();
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $id = $this->input->get('id');
        $data['medicine'] = $this->medicine_model->getMedicineById($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('add_new_medicine_view', $data);
        $this->load->view('home/footer');
    }

    function load()
    {
        $id = $this->input->post('id');
        $qty = $this->input->post('qty');
        $previous_qty = $this->db->get_where('medicine', array('id' => $id))->row()->quantity;
        $new_qty = $previous_qty + $qty;
        $data = array();
        $data = array('quantity' => $new_qty);
        $this->medicine_model->updateMedicine($id, $data);
        show_swal('feedback', lang('medicine_loaded'));
        redirect('medicine');
    }

    function editMedicineByJason()
    {
        $id = $this->input->get('id');
        $data['medicine'] = $this->medicine_model->getMedicineById($id);
        echo json_encode($data);
    }

    function checkDeleteSafety()
    {
        $id = $this->input->get('id');
        $related_records = $this->medicine_model->checkMedicineRelatedRecords($id);
        
        $response = array(
            'can_delete' => empty($related_records),
            'related_records' => $related_records,
            'medicine_name' => $this->medicine_model->getMedicineById($id)->name
        );
        
        echo json_encode($response);
    }

    function delete()
    {
        $id = $this->input->get('id');
        
        // Check for related records before deletion
        $related_records = $this->medicine_model->checkMedicineRelatedRecords($id);
        
        if (!empty($related_records)) {
            show_swal('Cannot delete medicine with related records. Please delete related records first.', 'error', 'Cannot Delete');
            redirect('medicine');
            return;
        }
        
        $this->medicine_model->deleteMedicine($id);
        show_swal(lang('deleted'), 'warning', lang('deleted'));
        redirect('medicine');
    }

    public function medicineCategory()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['categories'] = $this->medicine_model->getMedicineCategory();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('medicine_category', $data);
        $this->load->view('home/footer');
    }

    public function addCategoryView()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('add_new_category_view');
        $this->load->view('home/footer');
    }

    public function addNewCategory()
    {
        $id = $this->input->post('id');
        $category = $this->input->post('category');
        $description = $this->input->post('description');

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Category Name Field
        $this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[2]|max_length[100]|xss_clean');
        // Validating Description Field
        $this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect('medicine/edit_category?id=' . $id);
            } else {
                $data['settings'] = $this->settings_model->getSettings();
                $this->load->view('home/dashboard', $data);
                $this->load->view('add_new_category_view');
                $this->load->view('home/footer');
            }
        } else {
            $data = array();
            $data = array(
                'category' => $category,
                'description' => $description
            );
            if (empty($id)) {
                $this->medicine_model->insertMedicineCategory($data);
                show_swal(lang('added'), 'success', lang('added'));
            } else {
                $this->medicine_model->updateMedicineCategory($id, $data);
                show_swal(lang('updated'), 'success', lang('updated'));
            }
            redirect('medicine/medicineCategory');
        }
    }

    function edit_category()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['medicine'] = $this->medicine_model->getMedicineCategoryById($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('add_new_category_view', $data);
        $this->load->view('home/footer');
    }

    function editMedicineCategoryByJason()
    {
        $id = $this->input->get('id');
        $data['medicinecategory'] = $this->medicine_model->getMedicineCategoryById($id);
        echo json_encode($data);
    }

    function deleteMedicineCategory()
    {
        $id = $this->input->get('id');
        $this->medicine_model->deleteMedicineCategory($id);
        show_swal(lang('deleted'), 'warning', lang('deleted'));
        redirect('medicine/medicineCategory');
    }

    function getMedicineList()
    {
        $requestData = $_REQUEST;
        $start = isset($requestData['start']) ? $requestData['start'] : 0;
        $limit = isset($requestData['length']) ? $requestData['length'] : 100;
        $search = !empty($this->input->post('search')['value']) ? $this->input->post('search')['value'] : '';

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "category",
            "3" => "box",
            "4" => "price",
            "5" => "s_price",
            "6" => "quantity",
            "7" => "generic",
            "8" => "company",
            "9" => "effects",
            "10" => "e_date",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['medicines'] = $this->medicine_model->getMedicineBySearch($search, $order, $dir);
            } else {
                $data['medicines'] = $this->medicine_model->getMedicineWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['medicines'] = $this->medicine_model->getMedicineByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['medicines'] = $this->medicine_model->getMedicineByLimit($limit, $start, $order, $dir);
            }
        }

        $info = array();
        $i = 0;
        if (!empty($data['medicines'])) {
            $settings = $this->settings_model->getSettings();
            $currency = !empty($settings->currency) ? $settings->currency : '$';
            foreach ($data['medicines'] as $medicine) {
                $i = $i + 1;
                if ($medicine->quantity <= 0) {
                    $quan = '<p class="os">Stock Out</p>';
                } else {
                    $quan = $medicine->quantity;
                }
                $option1 = '<a type="button" class="btn btn-primary btn-sm btn_width editbutton" data-toggle="modal" data-id="' . $medicine->id . '"><i class="fa fa-edit"> </i> ' . lang('edit') . '</a>';
                $option2 = '<a class="btn btn-danger btn-sm btn_width delete_button" href="medicine/delete?id=' . $medicine->id . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash"> </i> ' . lang('delete') . '</a>';

                $info[] = array(
                    $i,
                    $medicine->name,
                    $medicine->category,
                    $medicine->box,
                    $currency . $medicine->price,
                    $currency . $medicine->s_price,
                    $quan,
                    $medicine->generic,
                    $medicine->company,
                    $medicine->effects,
                    $medicine->e_date,
                    '<span class="d-flex gap-2">' . $option1 . ' ' . $option2 . '</span>'
                );
            }
        }

        $totalMedicines = count($this->medicine_model->getMedicine());
        $filteredMedicines = !empty($search) ? count($this->medicine_model->getMedicineBySearch($search, $order, $dir)) : $totalMedicines;

        $output = array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => $totalMedicines,
            "recordsFiltered" => $filteredMedicines,
            "data" => $info
        );

        echo json_encode($output);
    }

    public function getMedicinenamelist()
    {
        $searchTerm = $this->input->post('searchTerm');

        $response = $this->medicine_model->getMedicineNameByAvailablity($searchTerm);
        $data = array();
        foreach ($response as $responses) {
            $data[] = array("id" => $responses->id, "data-id" => $responses->id, "data-med_name" => $responses->name, "text" => $responses->name);
        }

        echo json_encode($data);
    }

    public function getMedicineListForSelect2()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->medicine_model->getMedicineInfo($searchTerm);

        echo json_encode($response);
    }

    public function getMedicineForPharmacyMedicine()
    {
        // Search term
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->medicine_model->getMedicineInfoForPharmacySale($searchTerm);

        echo json_encode($response);
    }
    function getGenericNameInfoByAll()
    {
        $searchTerm = $this->input->post('searchTerm');

        // Get users
        $response = $this->medicine_model->getGenericInfoByAll($searchTerm);

        echo json_encode($response);
    }
    function getMedicineByGeneric()
    {
        $id = $this->input->get('id');
        $medicines = $this->medicine_model->getMedicineByGeneric($id);
        $option = '<option  value="select">' . lang('select') . '</option>';
        foreach ($medicines as $medicine) {
            $option .= '<option value="' . $medicine->id . '">' . $medicine->name . '</option>';
        }
        $data['response'] = $option;
        echo json_encode($data);
    }

    // ========== SUPPLIER MANAGEMENT ==========
    
    public function suppliers()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['suppliers'] = $this->medicine_model->getSuppliers();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('suppliers', $data);
        $this->load->view('home/footer');
    }

    public function addSupplierView()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('add_supplier_view');
        $this->load->view('home/footer');
    }

    public function addNewSupplier()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $company_name = $this->input->post('company_name');
        $contact_person = $this->input->post('contact_person');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $postal_code = $this->input->post('postal_code');
        $tax_number = $this->input->post('tax_number');
        $bank_name = $this->input->post('bank_name');
        $bank_account = $this->input->post('bank_account');
        $payment_terms = $this->input->post('payment_terms');
        $credit_limit = $this->input->post('credit_limit');
        $license_number = $this->input->post('license_number');
        $drug_license = $this->input->post('drug_license');
        $gst_number = $this->input->post('gst_number');
        $status = $this->input->post('status');
        $notes = $this->input->post('notes');

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        $this->form_validation->set_rules('name', 'Supplier Name', 'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|min_length[10]|max_length[20]|xss_clean');
        $this->form_validation->set_rules('credit_limit', 'Credit Limit', 'trim|decimal|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect('medicine/editSupplier?id=' . $id);
            } else {
                $data = array();
                $data['settings'] = $this->settings_model->getSettings();
                $this->load->view('home/dashboard', $data);
                $this->load->view('add_supplier_view', $data);
                $this->load->view('home/footer');
            }
        } else {
            $data = array(
                'name' => $name,
                'company_name' => $company_name,
                'contact_person' => $contact_person,
                'email' => $email,
                'phone' => $phone,
                'mobile' => $mobile,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'postal_code' => $postal_code,
                'tax_number' => $tax_number,
                'bank_name' => $bank_name,
                'bank_account' => $bank_account,
                'payment_terms' => $payment_terms,
                'credit_limit' => (!empty($credit_limit)) ? $credit_limit : 0.00,
                'license_number' => $license_number,
                'drug_license' => $drug_license,
                'gst_number' => $gst_number,
                'status' => (!empty($status)) ? $status : 'active',
                'notes' => $notes,
            );
            
            if (empty($id)) {
                $this->medicine_model->insertSupplier($data);
                show_swal(lang('added'), 'success', lang('added'));
            } else {
                $this->medicine_model->updateSupplier($id, $data);
                show_swal(lang('updated'), 'success', lang('updated'));
            }
            redirect('medicine/suppliers');
        }
    }

    function editSupplier()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['supplier'] = $this->medicine_model->getSupplierById($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('add_supplier_view', $data);
        $this->load->view('home/footer');
    }

    function deleteSupplier()
    {
        $id = $this->input->get('id');
        $this->medicine_model->deleteSupplier($id);
        show_swal(lang('deleted'), 'warning', lang('deleted'));
        redirect('medicine/suppliers');
    }

    // ========== PURCHASE MANAGEMENT ==========
    
    public function purchases()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['purchases'] = $this->medicine_model->getPurchases();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('purchases', $data);
        $this->load->view('home/footer');
    }

    public function addPurchaseView()
    {
        $data['suppliers'] = $this->medicine_model->getActiveSuppliers();
        $data['medicines'] = $this->medicine_model->getMedicine();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('add_purchase_view', $data);
        $this->load->view('home/footer');
    }

    public function addNewPurchase()
    {
        $id = $this->input->post('id');
        $purchase_order_no = $this->input->post('purchase_order_no');
        $supplier_id = $this->input->post('supplier_id');
        $purchase_date = $this->input->post('purchase_date');
        $expected_delivery_date = $this->input->post('expected_delivery_date');
        $invoice_number = $this->input->post('invoice_number');
        $invoice_date = $this->input->post('invoice_date');
        $payment_terms = $this->input->post('payment_terms');
        $transport_charges = $this->input->post('transport_charges');
        $other_charges = $this->input->post('other_charges');
        $notes = $this->input->post('notes');
        
        // Purchase items
        $medicine_ids = $this->input->post('medicine_id');
        $quantities = $this->input->post('quantity');
        $unit_costs = $this->input->post('unit_cost');
        $expiry_dates = $this->input->post('expiry_date');
        $batch_numbers = $this->input->post('batch_number');

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        $this->form_validation->set_rules('purchase_order_no', 'Purchase Order No', 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('purchase_date', 'Purchase Date', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect('medicine/editPurchase?id=' . $id);
            } else {
                $this->addPurchaseView();
            }
        } else {
            // Calculate totals
            $total_amount = 0;
            $tax_amount = 0;
            $net_amount = 0;
            
            if (!empty($medicine_ids)) {
                for ($i = 0; $i < count($medicine_ids); $i++) {
                    if (!empty($medicine_ids[$i]) && !empty($quantities[$i]) && !empty($unit_costs[$i])) {
                        $line_total = $quantities[$i] * $unit_costs[$i];
                        $total_amount += $line_total;
                    }
                }
            }
            
            $transport_charges = (!empty($transport_charges)) ? $transport_charges : 0.00;
            $other_charges = (!empty($other_charges)) ? $other_charges : 0.00;
            $net_amount = $total_amount + $transport_charges + $other_charges + $tax_amount;

            $purchase_data = array(
                'purchase_order_no' => $purchase_order_no,
                'supplier_id' => $supplier_id,
                'purchase_date' => $purchase_date,
                'expected_delivery_date' => (!empty($expected_delivery_date)) ? $expected_delivery_date : null,
                'invoice_number' => $invoice_number,
                'invoice_date' => (!empty($invoice_date)) ? $invoice_date : null,
                'total_amount' => $total_amount,
                'tax_amount' => $tax_amount,
                'net_amount' => $net_amount,
                'balance_amount' => $net_amount,
                'payment_terms' => $payment_terms,
                'transport_charges' => $transport_charges,
                'other_charges' => $other_charges,
                'notes' => $notes,
                'purchase_status' => 'received', // Automatically mark as received
                'payment_status' => 'pending'
            );
            
            if (empty($id)) {
                $purchase_id = $this->medicine_model->insertPurchase($purchase_data);
                
                // Insert purchase items and update medicine quantities immediately
                if (!empty($medicine_ids) && $purchase_id) {
                    for ($i = 0; $i < count($medicine_ids); $i++) {
                        if (!empty($medicine_ids[$i]) && !empty($quantities[$i]) && !empty($unit_costs[$i])) {
                            $item_data = array(
                                'purchase_id' => $purchase_id,
                                'medicine_id' => $medicine_ids[$i],
                                'quantity_ordered' => $quantities[$i],
                                'quantity_received' => $quantities[$i], // Set received quantity same as ordered
                                'unit_cost' => $unit_costs[$i],
                                'total_cost' => $quantities[$i] * $unit_costs[$i],
                                'net_amount' => $quantities[$i] * $unit_costs[$i],
                                'expiry_date' => (!empty($expiry_dates[$i])) ? $expiry_dates[$i] : null,
                                'batch_number' => (!empty($batch_numbers[$i])) ? $batch_numbers[$i] : null,
                                'received_status' => 'received' // Automatically mark as received
                            );
                            $this->medicine_model->insertPurchaseItem($item_data);
                            
                            // Update medicine quantity immediately
                            $this->medicine_model->updateMedicineTotalStock($medicine_ids[$i]);
                        }
                    }
                }
                
                show_swal(lang('added'), 'success', lang('added'));
            } else {
                $this->medicine_model->updatePurchase($id, $purchase_data);
                show_swal(lang('updated'), 'success', lang('updated'));
            }
            redirect('medicine/purchases');
        }
    }

    // ========== BATCH MANAGEMENT ==========
    
    public function batches()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['batches'] = $this->medicine_model->getBatches();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('batches', $data);
        $this->load->view('home/footer');
    }

    public function expiringMedicines()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['expiring_medicines'] = $this->medicine_model->getExpiringMedicines();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('expiring_medicines', $data);
        $this->load->view('home/footer');
    }

    public function receivePurchase()
    {
        $id = $this->input->get('id');
        $data['purchase'] = $this->medicine_model->getPurchaseById($id);
        $data['purchase_items'] = $this->medicine_model->getPurchaseItems($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('receive_purchase_view', $data);
        $this->load->view('home/footer');
    }

    public function updateAllMedicineQuantities()
    {
        $medicines = $this->medicine_model->getMedicine();
        $updated_count = 0;
        
        foreach ($medicines as $medicine) {
            $old_quantity = $medicine->quantity;
            $new_quantity = $this->medicine_model->updateMedicineQuantity($medicine->id);
            
            if ($old_quantity != $new_quantity) {
                $updated_count++;
                log_message('debug', "Updated medicine {$medicine->id} ({$medicine->name}): $old_quantity -> $new_quantity");
            }
        }
        
        show_swal("Updated $updated_count medicines", 'success', 'Quantities Updated');
        redirect('medicine');
    }

    public function processReceivePurchase()
    {
        $purchase_id = $this->input->post('purchase_id');
        $item_ids = $this->input->post('item_id');
        $received_quantities = $this->input->post('received_quantity');
        $batch_numbers = $this->input->post('batch_number');
        $expiry_dates = $this->input->post('expiry_date');
        $manufacturing_dates = $this->input->post('manufacturing_date');
        $manufacturers = $this->input->post('manufacturer');

        $this->db->trans_start();

        if (!empty($item_ids)) {
            for ($i = 0; $i < count($item_ids); $i++) {
                if (!empty($received_quantities[$i]) && $received_quantities[$i] > 0) {
                    $item_id = $item_ids[$i];
                    $received_qty = $received_quantities[$i];
                    
                    // Update purchase item
                    $this->medicine_model->updatePurchaseItem($item_id, array(
                        'quantity_received' => $received_qty,
                        'received_status' => 'received'
                    ));
                    
                    // Get purchase item details
                    $item = $this->medicine_model->getPurchaseItemById($item_id);
                    
                    // Create or update batch
                    $batch_data = array(
                        'medicine_id' => $item->medicine_id,
                        'batch_number' => (!empty($batch_numbers[$i])) ? $batch_numbers[$i] : 'BATCH-' . date('Ymd') . '-' . $item_id,
                        'manufacturing_date' => (!empty($manufacturing_dates[$i])) ? $manufacturing_dates[$i] : null,
                        'expiry_date' => $expiry_dates[$i],
                        'manufacturer' => (!empty($manufacturers[$i])) ? $manufacturers[$i] : null,
                        'supplier_id' => $item->supplier_id,
                        'purchase_item_id' => $item_id,
                        'quantity_received' => $received_qty,
                        'current_stock' => $received_qty,
                        'unit_cost' => $item->unit_cost,
                        'selling_price' => $item->unit_cost * 1.2, // 20% markup as default
                        'status' => 'active'
                    );
                    
                    $this->medicine_model->insertBatch($batch_data);
                    
                    // Update medicine total quantity
                    $this->medicine_model->updateMedicineTotalStock($item->medicine_id);
                }
            }
        }

        // Update purchase status
        $this->medicine_model->updatePurchase($purchase_id, array('purchase_status' => 'received'));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            show_swal('Error', 'error', 'Failed to process purchase receipt');
        } else {
            show_swal('Success', 'success', 'Purchase received successfully');
        }
        
        redirect('medicine/purchases');
    }

    public function deletePurchase()
    {
        $id = $this->input->get('id');
        
        // Check if purchase can be deleted (only pending purchases should be deletable)
        $purchase = $this->medicine_model->getPurchaseById($id);
        
        if (!$purchase) {
            show_swal('Purchase not found', 'error', 'Error');
            redirect('medicine/purchases');
            return;
        }
        
        // Allow deletion of received purchases (since they're now the default)
        // Only prevent deletion if purchase has been processed further
        if ($purchase->purchase_status == 'cancelled') {
            show_swal('Cancelled purchases cannot be deleted', 'error', 'Cannot Delete');
            redirect('medicine/purchases');
            return;
        }
        
        // Delete purchase items first
        $this->medicine_model->deletePurchaseItems($id);
        
        // Delete the purchase
        $this->medicine_model->deletePurchase($id);
        
        show_swal('Purchase deleted successfully', 'success', 'Deleted');
        redirect('medicine/purchases');
    }

    public function viewPurchase()
    {
        $id = $this->input->get('id');
        $data['purchase'] = $this->medicine_model->getPurchaseById($id);
        $data['purchase_items'] = $this->medicine_model->getPurchaseItems($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('view_purchase', $data);
        $this->load->view('home/footer');
    }

    public function editPurchase()
    {
        $id = $this->input->get('id');
        $data['purchase'] = $this->medicine_model->getPurchaseById($id);
        $data['purchase_items'] = $this->medicine_model->getPurchaseItems($id);
        $data['suppliers'] = $this->medicine_model->getActiveSuppliers();
        $data['medicines'] = $this->medicine_model->getMedicine();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('edit_purchase_view', $data);
        $this->load->view('home/footer');
    }
}

/* End of file medicine.php */
/* Location: ./application/modules/medicine/controllers/medicine.php */
