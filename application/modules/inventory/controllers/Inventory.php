<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inventory extends MX_Controller
{

    function __construct()
    {
        parent::__construct();
        
        $this->load->model('inventory/inventory_model');
        $this->load->model('inventory/supplier_model');
        $this->load->model('inventory/purchase_model');
        $this->load->model('inventory/usage_model');
        $this->load->model('patient/patient_model');
        $this->load->model('doctor/doctor_model');
        $this->load->model('department/department_model');
        
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    public function index()
    {
        $data = array();
        $data['page'] = 'inventory_dashboard';
        $data['page_title'] = $this->lang->line('inventory_management');
        
        // Dashboard statistics
        $data['total_items'] = count($this->inventory_model->getActiveInventoryItems());
        $data['low_stock_items'] = count($this->inventory_model->getLowStockItems());
        $data['pending_orders'] = count($this->purchase_model->getPurchaseOrdersByStatus('sent'));
        $data['overdue_deliveries'] = count($this->purchase_model->getOverdueDeliveries());
        
        // Recent activities
        $data['recent_usage'] = $this->usage_model->getUsageLogs(5);
        $data['recent_transactions'] = $this->inventory_model->getStockTransactions(10);
        $data['expiring_items'] = $this->inventory_model->getExpiringItems(30);
        
        // Charts data
        $data['inventory_valuation'] = $this->inventory_model->getInventoryValuation();
        $data['top_used_items'] = $this->inventory_model->getTopUsedItems(10);
        
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/index', $data);
        $this->load->view('home/footer');
    }

    // =================== INVENTORY ITEMS METHODS ===================

    public function items()
    {
        // Ensure the inventory_categories table exists and has sample data
        $this->ensureCategoriesTableExists();
        
        $data = array();
        $data['page'] = 'inventory_items';
        $data['page_title'] = $this->lang->line('inventory_items');
        $data['items'] = $this->inventory_model->getInventoryItems();
        $data['categories'] = $this->inventory_model->getInventoryCategories();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/items', $data);
        $this->load->view('home/footer');
    }

    public function add_item()
    {
        // Check if this is a POST request with form data
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('name')) {
            
            // Validate required fields
            $this->form_validation->set_rules('name', 'Item Name', 'required|trim');
            $this->form_validation->set_rules('item_code', 'Item Code', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Item name and code are required');
                redirect('inventory/items');
                return;
            }
            
            $data = array(
                'item_code' => $this->input->post('item_code'),
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'category' => $this->input->post('category'),
                'subcategory' => $this->input->post('subcategory'),
                'unit_of_measure' => $this->input->post('unit_of_measure'),
                'minimum_stock' => $this->input->post('minimum_stock') ?: 0,
                'maximum_stock' => $this->input->post('maximum_stock') ?: 0,
                'reorder_level' => $this->input->post('reorder_level') ?: 0,
                'current_stock' => 0, // Stock will be managed through purchase orders only
                'unit_cost' => $this->input->post('unit_cost') ?: 0,
                'selling_price' => $this->input->post('selling_price') ?: 0,
                'storage_location' => $this->input->post('storage_location'),
                'expiry_tracking' => $this->input->post('expiry_tracking') ?: 'no',
                'barcode' => $this->input->post('barcode'),
                'manufacturer' => $this->input->post('manufacturer'),
                'brand' => $this->input->post('brand'),
                'model_number' => $this->input->post('model_number'),
                'specifications' => $this->input->post('specifications'),
                'status' => $this->input->post('status') ?: 'active',
                'hospital_id' => $this->session->userdata('hospital_id'),
                'created_by' => $this->ion_auth->user()->row()->id,
                'created_at' => date('Y-m-d H:i:s')
            );

            $item_id = $this->inventory_model->insertInventoryItem($data);
            
            if ($item_id) {
                $this->session->set_flashdata('success', 'Inventory item added successfully with ID: ' . $item_id . '. Add stock through purchase orders.');
            } else {
                $this->session->set_flashdata('error', 'Failed to add inventory item. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/items');
        } else {
            // If not a proper POST, redirect to items list
            redirect('inventory/items');
        }
    }

    public function edit_item($id = null)
    {
        // Check if this is a POST request for updating item
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('item_id')) {
            $item_id = $this->input->post('item_id');
            
            // Validate required fields
            $this->form_validation->set_rules('name', 'Item Name', 'required|trim');
            $this->form_validation->set_rules('item_code', 'Item Code', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Item name and code are required');
                redirect('inventory/items');
                return;
            }
            
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'category' => $this->input->post('category'),
                'subcategory' => $this->input->post('subcategory'),
                'unit_of_measure' => $this->input->post('unit_of_measure'),
                'minimum_stock' => $this->input->post('minimum_stock') ?: 0,
                'maximum_stock' => $this->input->post('maximum_stock') ?: 0,
                'reorder_level' => $this->input->post('reorder_level') ?: 0,
                'unit_cost' => $this->input->post('unit_cost') ?: 0,
                'selling_price' => $this->input->post('selling_price') ?: 0,
                'storage_location' => $this->input->post('storage_location'),
                'expiry_tracking' => $this->input->post('expiry_tracking') ?: 'no',
                'barcode' => $this->input->post('barcode'),
                'manufacturer' => $this->input->post('manufacturer'),
                'brand' => $this->input->post('brand'),
                'model_number' => $this->input->post('model_number'),
                'specifications' => $this->input->post('specifications'),
                'status' => $this->input->post('status') ?: 'active',
                'updated_by' => $this->ion_auth->user()->row()->id,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $result = $this->inventory_model->updateInventoryItem($item_id, $data);
            
            if ($result) {
                $this->session->set_flashdata('success', 'Inventory item updated successfully');
            } else {
                $this->session->set_flashdata('error', 'Failed to update inventory item. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/items');
        } else {
            // If not a proper POST, redirect to items list
            redirect('inventory/items');
        }
    }

    // =================== SERVER-SIDE DATATABLE METHODS ===================

    public function getInventoryItemsList()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "item_code",
            "1" => "name", 
            "2" => "category",
            "3" => "current_stock",
            "4" => "unit_cost",
            "5" => "status"
        );
        
        $dir = "asc";
        $order_column = "name";
        if (!empty($order)) {
            $dir = $order[0]['dir'];
            if (isset($columns_valid[$order[0]['column']])) {
                $order_column = $columns_valid[$order[0]['column']];
            }
        }

        if ($limit == -1) {
            if (!empty($search)) {
                $data['items'] = $this->inventory_model->getInventoryItemsBySearch($search, $order_column, $dir);
            } else {
                $data['items'] = $this->inventory_model->getInventoryItemsWithPagination(null, null, $order_column, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['items'] = $this->inventory_model->getInventoryItemsBySearch($search, $order_column, $dir, $limit, $start);
            } else {
                $data['items'] = $this->inventory_model->getInventoryItemsWithPagination($limit, $start, $order_column, $dir);
            }
        }

        // Get calculated current stock for all items
        $item_ids = array_column($data['items'], 'id');
        $calculated_stocks = $this->inventory_model->calculateCurrentStockBulk($item_ids);

        $info = array();
        foreach ($data['items'] as $item) {
            // Use calculated stock instead of database value
            $calculated_stock = isset($calculated_stocks[$item->id]) ? $calculated_stocks[$item->id] : 0;
            $status_badge = $item->status == 'active' 
                ? '<span class="badge badge-success">Active</span>' 
                : '<span class="badge badge-secondary">Inactive</span>';
            
            $stock_badge = '';
            if ($calculated_stock <= $item->reorder_level) {
                $stock_badge = '<span class="badge badge-danger ml-1">Low Stock</span>';
            }
            if ($calculated_stock == 0) {
                $stock_badge = '<span class="badge badge-danger ml-1">Out of Stock</span>';
            }
            
            $category_name = $item->category_name ?: 'Uncategorized';
            
            $actions = '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#editItemModal" data-toggle="modal" onclick="loadItemData(' . $item->id . ', \'' . addslashes($item->item_code) . '\', \'' . addslashes($item->name) . '\', \'' . addslashes($item->description) . '\', \'' . $item->category . '\', \'' . addslashes($item->subcategory) . '\', \'' . addslashes($item->unit_of_measure) . '\', ' . $item->minimum_stock . ', ' . $item->maximum_stock . ', ' . $item->reorder_level . ', ' . $calculated_stock . ', ' . $item->unit_cost . ', ' . $item->selling_price . ', \'' . addslashes($item->storage_location) . '\', \'' . addslashes($item->expiry_tracking) . '\', \'' . addslashes($item->barcode) . '\', \'' . addslashes($item->manufacturer) . '\', \'' . addslashes($item->brand) . '\', \'' . addslashes($item->model_number) . '\', \'' . addslashes($item->specifications) . '\', \'' . addslashes($item->status) . '\')">
                            <i class="fas fa-edit mr-2"></i>' . lang('edit') . '
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="inventory/delete_item/' . $item->id . '" onclick="return confirm(\'Are you sure?\')">
                            <i class="fas fa-trash mr-2"></i>' . lang('delete') . '
                        </a>
                    </div>
                </div>';
            
            $info[] = array(
                $item->item_code,
                $item->name,
                $category_name,
                $calculated_stock . $stock_badge,
                number_format($item->unit_cost, 2),
                $status_badge,
                $actions
            );
        }

        $totalRecords = $this->inventory_model->getInventoryItemsCount();
        $filteredRecords = !empty($search) 
            ? $this->inventory_model->getInventoryItemsSearchCount($search)
            : $totalRecords;

        $output = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $info
        );

        echo json_encode($output);
    }

    public function getPurchaseOrderData($id)
    {
        $purchase_order = $this->purchase_model->getPurchaseOrderById($id);
        if (!$purchase_order) {
            echo json_encode(['error' => 'Purchase order not found']);
            return;
        }

        $items = $this->purchase_model->getPurchaseOrderItems($id);
        
        $data = array(
            'po' => $purchase_order,
            'items' => $items
        );
        
        echo json_encode($data);
    }



    public function delete_item($id)
    {
        // Check if item has any transactions
        $transactions = $this->inventory_model->getStockTransactionsByItem($id, 1);
        if (!empty($transactions)) {
            $this->session->set_flashdata('error', 'Cannot delete item with existing transactions');
        } else {
            $this->inventory_model->deleteInventoryItem($id);
            $this->session->set_flashdata('success', 'Inventory item deleted successfully');
        }
        redirect('inventory/items');
    }

    public function adjust_stock($id)
    {
        $item = $this->inventory_model->getInventoryItemById($id);
        if (!$item) {
            show_404();
        }

        if ($this->input->post('submit')) {
            $new_quantity = $this->input->post('new_quantity');
            $reason = $this->input->post('reason');
            $notes = $this->input->post('notes');

            $result = $this->inventory_model->adjustStock($id, $new_quantity, $reason, $notes);
            
            if ($result) {
                $this->session->set_flashdata('success', 'Stock adjusted successfully');
            } else {
                $this->session->set_flashdata('error', 'Failed to adjust stock');
            }
            redirect('inventory/items');
        }

        $data = array();
        $data['page'] = 'adjust_stock';
        $data['page_title'] = 'Adjust Stock';
        $data['item'] = $item;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/adjust_stock', $data);
        $this->load->view('home/footer');
    }

    public function low_stock()
    {
        $data = array();
        $data['page'] = 'low_stock_items';
        $data['page_title'] = 'Low Stock Items';
        $data['items'] = $this->inventory_model->getLowStockItems();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/low_stock', $data);
        $this->load->view('home/footer');
    }

    public function stock_transactions()
    {
        $data = array();
        $data['page'] = 'stock_transactions';
        $data['page_title'] = 'Stock Transactions';
        $data['transactions'] = $this->inventory_model->getStockTransactions(100);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/stock_transactions', $data);
        $this->load->view('home/footer');
    }

    // =================== CATEGORIES METHODS ===================

    public function categories()
    {
        // Ensure the inventory_categories table exists and has sample data
        $this->ensureCategoriesTableExists();
        
        $data = array();
        $data['page'] = 'inventory_categories';
        $data['page_title'] = 'Inventory Categories';
        $data['categories'] = $this->inventory_model->getInventoryCategories();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/categories', $data);
        $this->load->view('home/footer');
    }

    public function add_category()
    {
        // Debug: Log all POST data
        log_message('debug', 'POST data received: ' . print_r($this->input->post(), true));
        
        // Check if this is a POST request with form data (instead of checking for submit field)
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('name')) {
            $this->session->set_flashdata('debug', 'Form submitted detected - Name: ' . $this->input->post('name'));
            
            // Validate required fields
            $this->form_validation->set_rules('name', 'Category Name', 'required|trim');
            $this->form_validation->set_rules('status', 'Status', 'required');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Validation failed: ' . validation_errors());
                redirect('inventory/categories');
                return;
            }
            
            $parent_id = $this->input->post('parent_id');
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'parent_id' => !empty($parent_id) ? $parent_id : null,
                'status' => $this->input->post('status'),
                'hospital_id' => $this->session->userdata('hospital_id'),
                'created_by' => $this->ion_auth->user()->row()->id,
                'created_at' => date('Y-m-d H:i:s')
            );

            // Debug: Log prepared data
            log_message('debug', 'Prepared data for insert: ' . print_r($data, true));

            // Debug: Check if data is being prepared correctly
            if (empty($data['name'])) {
                $this->session->set_flashdata('error', 'Category name is empty after processing');
                redirect('inventory/categories');
                return;
            }

            if (empty($data['hospital_id'])) {
                $this->session->set_flashdata('error', 'Hospital ID not found in session. Current user: ' . ($this->ion_auth->user()->row()->id ?? 'not found'));
                redirect('inventory/categories');
                return;
            }

            $result = $this->inventory_model->insertInventoryCategory($data);
            
            if ($result) {
                $this->session->set_flashdata('success', 'Category added successfully with ID: ' . $result);
            } else {
                $this->session->set_flashdata('error', 'Failed to add category. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/categories');
        } else {
            // Debug: Not a proper POST request
            $this->session->set_flashdata('debug', 'Form not properly submitted. Method: ' . $_SERVER['REQUEST_METHOD'] . ', Name field: ' . ($this->input->post('name') ? 'present' : 'missing'));
            redirect('inventory/categories');
        }
    }

    public function get_category_data()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('category_id')) {
            $category_id = $this->input->post('category_id');
            $category = $this->inventory_model->getInventoryCategoryById($category_id);
            
            if ($category) {
                $response = array(
                    'success' => true,
                    'data' => array(
                        'id' => $category->id,
                        'name' => $category->name,
                        'description' => $category->description,
                        'parent_id' => $category->parent_id,
                        'status' => $category->status
                    )
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Category not found'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Invalid request'
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function edit_category()
    {
        // Debug: Log all POST data
        log_message('debug', 'Edit Category POST data: ' . print_r($this->input->post(), true));
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('category_id')) {
            $category_id = $this->input->post('category_id');
            
            // Debug: Log category ID
            log_message('debug', 'Category ID: ' . $category_id);
            
            // Validate required fields
            $this->form_validation->set_rules('name', 'Category Name', 'required|trim');
            $this->form_validation->set_rules('status', 'Status', 'required');
            
            if ($this->form_validation->run() == FALSE) {
                $error_message = 'Validation failed: ' . validation_errors();
                log_message('error', $error_message);
                $this->session->set_flashdata('error', $error_message);
                redirect('inventory/categories');
                return;
            }
            
            try {
                $parent_id = $this->input->post('parent_id');
                $data = array(
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'parent_id' => !empty($parent_id) ? $parent_id : null,
                    'status' => $this->input->post('status')
                );

                // Debug: Log data being updated
                log_message('debug', 'Update data: ' . print_r($data, true));

                $this->inventory_model->updateInventoryCategory($category_id, $data);
                $this->session->set_flashdata('success', 'Category updated successfully');
                redirect('inventory/categories');
            } catch (Exception $e) {
                log_message('error', 'Error updating category: ' . $e->getMessage());
                $this->session->set_flashdata('error', 'Error updating category: ' . $e->getMessage());
                redirect('inventory/categories');
            }
        } else {
            $error_message = 'Invalid request - Method: ' . $_SERVER['REQUEST_METHOD'] . ', Category ID: ' . $this->input->post('category_id');
            log_message('error', $error_message);
            $this->session->set_flashdata('error', $error_message);
            redirect('inventory/categories');
        }
    }

    public function delete_category($id)
    {
        // Check if category has any items or subcategories
        $items = $this->inventory_model->getInventoryItemsByCategory($id);
        $subcategories = $this->inventory_model->getSubcategories($id);
        
        if (!empty($items) || !empty($subcategories)) {
            $this->session->set_flashdata('error', 'Cannot delete category with existing items or subcategories');
        } else {
            $this->inventory_model->deleteInventoryCategory($id);
            $this->session->set_flashdata('success', 'Category deleted successfully');
        }
        redirect('inventory/categories');
    }

    public function getSuppliers()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id", // Order by ID for newest first
            "1" => "company_name",
            "2" => "contact_person",
            "3" => "email",
            "4" => "phone",
            "5" => "city",
            "6" => "current_balance",
            "7" => "status"
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['suppliers'] = $this->supplier_model->getSuppliersBySearch($search, $order, $dir);
            } else {
                $data['suppliers'] = $this->supplier_model->getSuppliersWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['suppliers'] = $this->supplier_model->getSuppliersByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['suppliers'] = $this->supplier_model->getSuppliersByLimit($limit, $start, $order, $dir);
            }
        }

        $info = array();
        foreach ($data['suppliers'] as $supplier) {
            // Email column
            $email = '';
            if (!empty($supplier->email)) {
                $email = '<a href="mailto:' . $supplier->email . '">' . $supplier->email . '</a>';
            }

            // Phone column
            $phone = '';
            if (!empty($supplier->phone)) {
                $phone = '<a href="tel:' . $supplier->phone . '">' . $supplier->phone . '</a>';
            }

            // Current balance column with badge
            $balance_class = ($supplier->current_balance > 0) ? 'warning' : 'success';
            $balance = '<span class="badge badge-' . $balance_class . '">' . 
                      $this->settings_model->getSettings()->currency . ' ' . 
                      number_format($supplier->current_balance, 2) . '</span>';

            // Status column with badge
            $status_class = ($supplier->status == 'active') ? 'success' : 'secondary';
            $status = '<span class="badge badge-' . $status_class . '">' . ucfirst($supplier->status) . '</span>';

            // Options column
            $options = '<div class="btn-group">
                          <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fas fa-cog"></i>
                          </button>
                          <div class="dropdown-menu">
                              <a class="dropdown-item" href="inventory/supplier/view/' . $supplier->id . '">
                                  <i class="fas fa-eye mr-2"></i>' . lang('view') . '
                              </a>
                              <a class="dropdown-item" href="#" onclick="editSupplier(' . $supplier->id . ')" data-toggle="modal" data-target="#editSupplierModal">
                                  <i class="fas fa-edit mr-2"></i>' . lang('edit') . '
                              </a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item text-danger" href="inventory/supplier/delete/' . $supplier->id . '" onclick="return confirm(\'Are you sure?\')">
                                  <i class="fas fa-trash mr-2"></i>' . lang('delete') . '
                              </a>
                          </div>
                        </div>';

            $info[] = array(
                $supplier->name,
                $supplier->company_name,
                $supplier->contact_person,
                $email,
                $phone,
                $supplier->city,
                $balance,
                $status,
                $options
            );
        }

        if (!empty($data['suppliers'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->supplier_model->getSuppliers()),
                "recordsFiltered" => count($this->supplier_model->getSuppliers()),
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

    public function get_supplier_data()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('supplier_id')) {
            $supplier_id = $this->input->post('supplier_id');
            $supplier = $this->supplier_model->getSupplierById($supplier_id);
            
            if ($supplier) {
                $response = array(
                    'success' => true,
                    'data' => array(
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'company_name' => $supplier->company_name,
                        'contact_person' => $supplier->contact_person,
                        'email' => $supplier->email,
                        'phone' => $supplier->phone,
                        'mobile' => $supplier->mobile,
                        'address' => $supplier->address,
                        'city' => $supplier->city,
                        'state' => $supplier->state,
                        'country' => $supplier->country,
                        'postal_code' => $supplier->postal_code,
                        'tax_number' => $supplier->tax_number,
                        'bank_name' => $supplier->bank_name,
                        'bank_account' => $supplier->bank_account,
                        'payment_terms' => $supplier->payment_terms,
                        'credit_limit' => $supplier->credit_limit,
                        'status' => $supplier->status,
                        'notes' => $supplier->notes
                    )
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Supplier not found'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Invalid request'
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getPurchaseOrders()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "po.id", // Order by ID for newest first
            "1" => "s.name",
            "2" => "po.order_date", 
            "3" => "po.expected_delivery_date",
            "4" => "total_quantity",
            "5" => "po.grand_total",
            "6" => "po.status"
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['purchase_orders'] = $this->purchase_model->getPurchaseOrdersBySearch($search, $order, $dir);
            } else {
                $data['purchase_orders'] = $this->purchase_model->getPurchaseOrdersWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['purchase_orders'] = $this->purchase_model->getPurchaseOrdersByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['purchase_orders'] = $this->purchase_model->getPurchaseOrdersByLimit($limit, $start, $order, $dir);
            }
        }

        $info = array();
        foreach ($data['purchase_orders'] as $po) {
            // PO Number column with link
            $po_number = '<a href="inventory/purchase/view/' . $po->id . '">' . $po->po_number . '</a>';

            // Order date column
            $order_date = date('Y-m-d', strtotime($po->order_date));

            // Expected delivery date column
            $delivery_date = '';
            if (!empty($po->expected_delivery_date)) {
                $delivery_date = date('Y-m-d', strtotime($po->expected_delivery_date));
            } else {
                $delivery_date = '<span class="text-muted">Not set</span>';
            }

            // Grand total column with currency
            $grand_total = $this->settings_model->getSettings()->currency . ' ' . number_format($po->grand_total, 2);

            // Status column with badge
            $status_colors = array(
                'draft' => 'secondary',
                'sent' => 'primary',
                'confirmed' => 'info',
                'partially_received' => 'warning',
                'completed' => 'success',
                'cancelled' => 'danger'
            );
            $color = isset($status_colors[$po->status]) ? $status_colors[$po->status] : 'secondary';
            $status = '<span class="badge badge-' . $color . '">' . ucfirst(str_replace('_', ' ', $po->status)) . '</span>';

            // Options column with dynamic actions based on status
            $options = '<div class="btn-group">
                          <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fas fa-cog"></i>
                          </button>
                          <div class="dropdown-menu">
                              <a class="dropdown-item" href="#" onclick="viewPurchaseOrder(' . $po->id . ')" data-toggle="modal" data-target="#viewPurchaseOrderModal">
                                  <i class="fas fa-eye mr-2"></i>' . lang('view') . '
                              </a>';

            // Allow editing for most statuses (exclude only completed and cancelled)
            if (!in_array($po->status, array('completed', 'cancelled'))) {
                $options .= '<a class="dropdown-item" href="#editPurchaseOrderModal" data-toggle="modal" onclick="loadPurchaseOrderData(' . $po->id . ')">
                                 <i class="fas fa-edit mr-2"></i>' . lang('edit') . '
                             </a>';
            }
            
            // Add quick status change options
            if ($po->status == 'draft') {
                $options .= '<a class="dropdown-item" href="inventory/purchase/send_to_supplier/' . $po->id . '" onclick="return confirm(\'Send this purchase order to supplier?\')">
                                 <i class="fas fa-paper-plane mr-2"></i>Send to Supplier
                             </a>';
            }

            if (in_array($po->status, array('sent', 'confirmed', 'partially_received'))) {
                $options .= '<a class="dropdown-item" href="inventory/purchase/receive_items/' . $po->id . '">
                                 <i class="fas fa-truck mr-2"></i>' . lang('receive_items') . '
                             </a>';
            }

            $options .= '<div class="dropdown-divider"></div>
                         <a class="dropdown-item" href="inventory/purchase/print_po/' . $po->id . '" target="_blank">
                             <i class="fas fa-print mr-2"></i>' . lang('print') . '
                         </a>';

            if (in_array($po->status, array('draft', 'sent'))) {
                $options .= '<a class="dropdown-item text-danger" href="inventory/purchase/delete/' . $po->id . '" onclick="return confirm(\'Are you sure?\')">
                                 <i class="fas fa-trash mr-2"></i>' . lang('delete') . '
                             </a>';
            }

            $options .= '</div></div>';

            // Format total quantity with badge
            $quantity_badge = '';
            if ($po->total_items > 0) {
                $quantity_badge = ' <small class="badge badge-info ml-1">' . $po->total_items . ' items</small>';
            }
            $total_quantity = number_format($po->total_quantity, 0) . $quantity_badge;

            $info[] = array(
                $po_number,
                $po->supplier_name,
                $order_date,
                $delivery_date,
                $total_quantity,
                $grand_total,
                $status,
                $options
            );
        }

        if (!empty($data['purchase_orders'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => count($this->purchase_model->getPurchaseOrders()),
                "recordsFiltered" => count($this->purchase_model->getPurchaseOrders()),
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

    public function get_purchase_order_view_data()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('purchase_order_id')) {
            $po_id = $this->input->post('purchase_order_id');
            $purchase_order = $this->purchase_model->getPurchaseOrderById($po_id);
            $items = $this->purchase_model->getPurchaseOrderItems($po_id);
            
            if ($purchase_order) {
                // Format status with color
                $status_colors = array(
                    'draft' => 'secondary',
                    'sent' => 'primary',
                    'confirmed' => 'info',
                    'partially_received' => 'warning',
                    'completed' => 'success',
                    'cancelled' => 'danger'
                );
                $color = isset($status_colors[$purchase_order->status]) ? $status_colors[$purchase_order->status] : 'secondary';
                $status_badge = '<span class="badge badge-' . $color . '">' . ucfirst(str_replace('_', ' ', $purchase_order->status)) . '</span>';

                // Format dates
                $order_date = !empty($purchase_order->order_date) ? date('Y-m-d', strtotime($purchase_order->order_date)) : '-';
                $delivery_date = !empty($purchase_order->expected_delivery_date) ? date('Y-m-d', strtotime($purchase_order->expected_delivery_date)) : 'Not set';

                // Format currency
                $currency = $this->settings_model->getSettings()->currency;

                $response = array(
                    'success' => true,
                    'data' => array(
                        'purchase_order' => array(
                            'id' => $purchase_order->id,
                            'po_number' => $purchase_order->po_number,
                            'status' => $purchase_order->status,
                            'status_badge' => $status_badge,
                            'order_date' => $order_date,
                            'expected_delivery_date' => $delivery_date,
                            'payment_terms' => $purchase_order->payment_terms ?: '-',
                            'notes' => $purchase_order->notes ?: 'No notes available',
                            'total_amount' => $currency . ' ' . number_format($purchase_order->total_amount, 2),
                            'tax_amount' => $currency . ' ' . number_format($purchase_order->tax_amount, 2),
                            'discount_amount' => $currency . ' ' . number_format($purchase_order->discount_amount, 2),
                            'shipping_amount' => $currency . ' ' . number_format($purchase_order->shipping_amount, 2),
                            'grand_total' => $currency . ' ' . number_format($purchase_order->grand_total, 2)
                        ),
                        'supplier' => array(
                            'name' => $purchase_order->supplier_name ?: '-',
                            'company_name' => $purchase_order->company_name ?: '-',
                            'contact_person' => $purchase_order->contact_person ?: '-',
                            'email' => $purchase_order->email ?: '-',
                            'phone' => $purchase_order->phone ?: '-'
                        ),
                        'items' => $items,
                        'currency' => $currency
                    )
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Purchase order not found'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Invalid request'
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getCategories()
    {
        try {
            $requestData = $_REQUEST;
            $start = $requestData['start'];
            $limit = $requestData['length'];
            $search = $this->input->post('search')['value'];

        $order = $this->input->post("order"); 
        $columns_valid = array(
            "0" => "id", // Order by ID for newest first
            "1" => "description", 
            "2" => "parent_id",
            "3" => "status"
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['categories'] = $this->inventory_model->getCategoriesBySearch($search, $order, $dir);
            } else {
                $data['categories'] = $this->inventory_model->getCategoriesWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['categories'] = $this->inventory_model->getCategoriesByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['categories'] = $this->inventory_model->getCategoriesByLimit($limit, $start, $order, $dir);
            }
        }

        // Get all categories for parent lookup
        $all_categories = $this->inventory_model->getInventoryCategories();
        $parent_lookup = array();
        foreach ($all_categories as $cat) {
            $parent_lookup[$cat->id] = $cat->name;
        }

        $info = array();
        foreach ($data['categories'] as $category) {
            // Parent category column
            $parent_name = '-';
            if (!empty($category->parent_id) && isset($parent_lookup[$category->parent_id])) {
                $parent_name = $parent_lookup[$category->parent_id];
            }

            // Status column with badge
            $status_class = ($category->status == 'active') ? 'success' : 'secondary';
            $status = '<span class="badge badge-' . $status_class . '">' . ucfirst($category->status) . '</span>';

            // Options column
            $options = '<div class="btn-group">
                          <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fas fa-cog"></i>
                          </button>
                          <div class="dropdown-menu">
                              <a class="dropdown-item" href="#" onclick="editCategory(' . $category->id . ')" data-toggle="modal" data-target="#editCategoryModal">
                                  <i class="fas fa-edit mr-2"></i>' . lang('edit') . '
                              </a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item text-danger" href="inventory/delete_category/' . $category->id . '" onclick="return confirm(\'Are you sure?\')">
                                  <i class="fas fa-trash mr-2"></i>' . lang('delete') . '
                              </a>
                          </div>
                        </div>';

            $info[] = array(
                $category->name,
                $category->description ?: '-',
                $parent_name,
                $status,
                $options
            );
        }

        // Get total count for recordsTotal and recordsFiltered
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'active');
        $total_count = $this->db->count_all_results('inventory_categories');
        
        if (!empty($data['categories'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => $total_count,
                "recordsFiltered" => $total_count,
                "data" => $info
            );
        } else {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => $total_count,
                "recordsFiltered" => $total_count,
                "data" => []
            );
        }

            echo json_encode($output);
        } catch (Exception $e) {
            // Log the error
            log_message('error', 'Error in getCategories: ' . $e->getMessage());
            
            // Return error response
            $output = array(
                "draw" => intval($_REQUEST['draw'] ?? 1),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "An error occurred while loading categories"
            );
            echo json_encode($output);
        }
    }

    public function test_category_insert()
    {
        redirect('home/permission');
    }

    public function debug_categories()
    {
        redirect('home/permission');
    }

    public function init_categories()
    {
        redirect('home/permission');
    }

    public function check_session()
    {
        redirect('home/permission');
    }

    private function ensureCategoriesTableExists()
    {
        // Check if table exists
        if (!$this->db->table_exists('inventory_categories')) {
            // Create the table
            $this->db->query("
                CREATE TABLE `inventory_categories` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `description` text DEFAULT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  `status` enum('active','inactive') DEFAULT 'active',
                  `hospital_id` int(11) NOT NULL,
                  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `created_by` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_hospital_id` (`hospital_id`),
                  KEY `idx_parent_id` (`parent_id`),
                  KEY `idx_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            log_message('debug', 'Created inventory_categories table');
        }
        
        // Check if we have any categories for the current hospital
        $hospital_id = $this->session->userdata('hospital_id') ?: 1;
        
        // Also check for any categories in the table (in case hospital_id is different)
        $total_categories = $this->db->count_all_results('inventory_categories');
        $existing_categories = $this->db->where('hospital_id', $hospital_id)->count_all_results('inventory_categories');
        
        log_message('debug', 'Hospital ID: ' . $hospital_id . ', Total categories: ' . $total_categories . ', Existing categories: ' . $existing_categories);
        
        // If no categories exist at all, or no categories for current hospital, insert sample data
        if ($total_categories == 0 || $existing_categories == 0) {
            // Insert sample categories
            $sample_categories = array(
                array(
                    'name' => 'Medical Equipment',
                    'description' => 'Medical devices and equipment',
                    'parent_id' => null,
                    'status' => 'active',
                    'hospital_id' => $hospital_id,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ),
                array(
                    'name' => 'Surgical Instruments',
                    'description' => 'Surgical tools and instruments',
                    'parent_id' => null,
                    'status' => 'active',
                    'hospital_id' => $hospital_id,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ),
                array(
                    'name' => 'Pharmaceuticals',
                    'description' => 'Medicines and drugs',
                    'parent_id' => null,
                    'status' => 'active',
                    'hospital_id' => $hospital_id,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ),
                array(
                    'name' => 'Laboratory Supplies',
                    'description' => 'Lab consumables and reagents',
                    'parent_id' => null,
                    'status' => 'active',
                    'hospital_id' => $hospital_id,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ),
                array(
                    'name' => 'Office Supplies',
                    'description' => 'Administrative and office materials',
                    'parent_id' => null,
                    'status' => 'active',
                    'hospital_id' => $hospital_id,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                )
            );
            
            $result = $this->db->insert_batch('inventory_categories', $sample_categories);
            log_message('debug', 'Inserted sample categories. Result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            if (!$result) {
                log_message('error', 'Database error: ' . print_r($this->db->error(), true));
            }
        }
    }

    // =================== SUPPLIER METHODS ===================

    public function supplier($action = 'index', $id = null)
    {
        switch ($action) {
            case 'add':
                return $this->add_supplier();
            case 'edit':
                return $this->edit_supplier($id);
            case 'view':
                return $this->view_supplier($id);
            case 'delete':
                return $this->delete_supplier($id);
            case 'outstanding_balances':
                return $this->outstanding_balances();
            case 'contacts':
                return $this->supplier_contacts();
            case 'performance_report':
                return $this->supplier_performance_report();
            default:
                return $this->suppliers();
        }
    }

    public function suppliers()
    {
        $data = array();
        $data['page'] = 'suppliers';
        $data['page_title'] = 'Suppliers';
        $data['suppliers'] = $this->supplier_model->getSuppliers();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/suppliers', $data);
        $this->load->view('home/footer');
    }

    public function add_supplier()
    {
        // Check if this is a POST request with form data
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('name')) {
            
            // Validate required fields
            $this->form_validation->set_rules('name', 'Supplier Name', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Supplier name is required');
                redirect('inventory/supplier');
                return;
            }
            
            $data = array(
                'name' => $this->input->post('name'),
                'company_name' => $this->input->post('company_name'),
                'contact_person' => $this->input->post('contact_person'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'mobile' => $this->input->post('mobile'),
                'address' => $this->input->post('address'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'country' => $this->input->post('country'),
                'postal_code' => $this->input->post('postal_code'),
                'tax_number' => $this->input->post('tax_number'),
                'bank_name' => $this->input->post('bank_name'),
                'bank_account' => $this->input->post('bank_account'),
                'payment_terms' => $this->input->post('payment_terms'),
                'credit_limit' => $this->input->post('credit_limit'),
                'status' => $this->input->post('status'),
                'notes' => $this->input->post('notes'),
                'hospital_id' => $this->session->userdata('hospital_id'),
                'created_by' => $this->ion_auth->user()->row()->id,
                'created_at' => date('Y-m-d H:i:s')
            );

            $supplier_id = $this->supplier_model->insertSupplier($data);
            
            if ($supplier_id) {
                $this->session->set_flashdata('success', 'Supplier added successfully with ID: ' . $supplier_id);
            } else {
                $this->session->set_flashdata('error', 'Failed to add supplier. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/supplier');
        } else {
            // If not a proper POST, redirect to suppliers list
            redirect('inventory/supplier');
        }
    }

    public function edit_supplier($id = null)
    {
        // Check if this is a POST request for updating supplier
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('supplier_id')) {
            $supplier_id = $this->input->post('supplier_id');
            
            // Validate required fields
            $this->form_validation->set_rules('name', 'Supplier Name', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Supplier name is required');
                redirect('inventory/supplier');
                return;
            }
            
            $data = array(
                'name' => $this->input->post('name'),
                'company_name' => $this->input->post('company_name'),
                'contact_person' => $this->input->post('contact_person'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'mobile' => $this->input->post('mobile'),
                'address' => $this->input->post('address'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'country' => $this->input->post('country'),
                'postal_code' => $this->input->post('postal_code'),
                'tax_number' => $this->input->post('tax_number'),
                'bank_name' => $this->input->post('bank_name'),
                'bank_account' => $this->input->post('bank_account'),
                'payment_terms' => $this->input->post('payment_terms'),
                'credit_limit' => $this->input->post('credit_limit'),
                'status' => $this->input->post('status'),
                'notes' => $this->input->post('notes'),
                'updated_by' => $this->ion_auth->user()->row()->id,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $result = $this->supplier_model->updateSupplier($supplier_id, $data);
            
            if ($result) {
                $this->session->set_flashdata('success', 'Supplier updated successfully');
            } else {
                $this->session->set_flashdata('error', 'Failed to update supplier. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/supplier');
        } else {
            // If not a proper POST or no supplier ID, redirect to suppliers list
            redirect('inventory/supplier');
        }
    }

    public function view_supplier($id)
    {
        $supplier = $this->supplier_model->getSupplierById($id);
        if (!$supplier) {
            show_404();
        }

        $data = array();
        $data['page'] = 'view_supplier';
        $data['page_title'] = 'Supplier Details';
        $data['supplier'] = $supplier;
        $data['purchase_history'] = $this->supplier_model->getSupplierPurchaseHistory($id, 10);
        $data['performance'] = $this->supplier_model->getSupplierPerformanceReport($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/view_supplier', $data);
        $this->load->view('home/footer');
    }

    public function delete_supplier($id)
    {
        $result = $this->supplier_model->deleteSupplier($id);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Supplier deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Cannot delete supplier with existing purchase orders');
        }
        redirect('inventory/supplier');
    }

    public function outstanding_balances()
    {
        $data = array();
        $data['page'] = 'outstanding_balances';
        $data['page_title'] = 'Outstanding Supplier Balances';
        $data['suppliers'] = $this->supplier_model->getSuppliersWithOutstandingBalance();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/outstanding_balances', $data);
        $this->load->view('home/footer');
    }

    public function supplier_contacts()
    {
        $data = array();
        $data['page'] = 'supplier_contacts';
        $data['page_title'] = 'Supplier Contacts';
        $data['contacts'] = $this->supplier_model->getSupplierContacts();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/supplier_contacts', $data);
        $this->load->view('home/footer');
    }

    public function supplier_performance_report()
    {
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');

        $data = array();
        $data['page'] = 'supplier_performance';
        $data['page_title'] = 'Supplier Performance Report';
        $data['suppliers'] = $this->supplier_model->getTopSuppliersByVolume(20, $start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/supplier_performance', $data);
        $this->load->view('home/footer');
    }

    // =================== PURCHASE METHODS ===================

    public function purchase($action = 'index', $id = null)
    {
        switch ($action) {
            case 'add':
                return $this->add_purchase_order();
            case 'edit':
                return $this->edit_purchase_order($id);
            case 'view':
                return $this->view_purchase_order($id);
            case 'delete':
                return $this->delete_purchase_order($id);
            case 'send_to_supplier':
                return $this->send_to_supplier($id);
            case 'receive_items':
                return $this->receive_items($id);
            case 'quotations':
                return $this->vendor_quotations();
            case 'add_quotation':
                return $this->add_quotation();
            case 'convert_quotation_to_po':
                return $this->convert_quotation_to_po($id);
            case 'purchase_report':
                return $this->purchase_report();
            case 'pending_deliveries':
                return $this->pending_deliveries();
            case 'top_purchased_items':
                return $this->top_purchased_items();
            case 'print_po':
                return $this->print_po($id);
            case 'generate_pdf':
                return $this->generate_pdf($id);
            default:
                return $this->purchase_orders();
        }
    }

    public function purchase_orders()
    {
        $data = array();
        $data['page'] = 'purchase_orders';
        $data['page_title'] = 'Purchase Orders';
        $data['purchase_orders'] = $this->purchase_model->getPurchaseOrders();
        $data['suppliers'] = $this->supplier_model->getActiveSuppliers();
        $data['inventory_items'] = $this->inventory_model->getActiveInventoryItems();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/purchase_orders', $data);
        $this->load->view('home/footer');
    }

    public function add_purchase_order()
    {
        // Check if this is a POST request with form data
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('supplier_id')) {
            
            // Validate required fields
            $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
            $this->form_validation->set_rules('order_date', 'Order Date', 'required');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Supplier and order date are required');
                redirect('inventory/purchase');
                return;
            }
            
            $po_data = array(
                'po_number' => $this->purchase_model->generatePurchaseOrderNumber(),
                'supplier_id' => $this->input->post('supplier_id'),
                'order_date' => $this->input->post('order_date'),
                'expected_delivery_date' => $this->input->post('expected_delivery_date'),
                'payment_terms' => $this->input->post('payment_terms'),
                'delivery_address' => $this->input->post('delivery_address'),
                'notes' => $this->input->post('notes'),
                'status' => 'completed',
                'hospital_id' => $this->session->userdata('hospital_id'),
                'created_by' => $this->ion_auth->user()->row()->id,
                'created_at' => date('Y-m-d H:i:s')
            );

            $po_id = $this->purchase_model->insertPurchaseOrder($po_data);
            
            if ($po_id) {
                // Add items to purchase order
                $items = $this->input->post('items');
                $items_added = 0;
                
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if (!empty($item['inventory_item_id']) && !empty($item['quantity'])) {
                            $item_data = array(
                                'purchase_order_id' => $po_id,
                                'inventory_item_id' => $item['inventory_item_id'],
                                'quantity_ordered' => $item['quantity'],
                                'quantity_received' => $item['quantity'], // Auto-mark as received
                                'unit_price' => $item['unit_price'] ?: 0,
                                'total_price' => $item['quantity'] * ($item['unit_price'] ?: 0),
                                'received_at' => date('Y-m-d H:i:s'),
                                'received_by' => $this->ion_auth->user()->row()->id
                            );
                            
                            $item_id = $this->purchase_model->insertPurchaseOrderItem($item_data);
                            if ($item_id) {
                                $items_added++;
                                
                                // Automatically increase stock since items are received
                                $stock_updated = $this->inventory_model->increaseStock(
                                    $item['inventory_item_id'],
                                    $item['quantity'],
                                    'purchase',
                                    $po_id,
                                    'Purchase received - PO#' . $po_data['po_number']
                                );
                                
                                if (!$stock_updated) {
                                    log_message('error', 'Failed to update stock for item ID: ' . $item['inventory_item_id'] . ' in purchase order: ' . $po_id);
                                }
                            }
                        }
                    }
                }
                
                // Calculate and update the purchase order totals
                $total_amount = $this->purchase_model->calculatePurchaseOrderTotal($po_id);
                $tax_amount = $this->input->post('tax_amount') ?: 0;
                $discount_amount = $this->input->post('discount_amount') ?: 0;
                $shipping_amount = $this->input->post('shipping_amount') ?: 0;
                $grand_total = $total_amount + $tax_amount + $shipping_amount - $discount_amount;
                
                $update_totals = array(
                    'total_amount' => $total_amount,
                    'tax_amount' => $tax_amount,
                    'discount_amount' => $discount_amount,
                    'shipping_amount' => $shipping_amount,
                    'grand_total' => $grand_total
                );
                
                $this->purchase_model->updatePurchaseOrder($po_id, $update_totals);
                
                if ($items_added > 0) {
                    $this->session->set_flashdata('success', 'Purchase order created and completed successfully with ID: ' . $po_id . '. ' . $items_added . ' items received and stock updated.');
                } else {
                    $this->session->set_flashdata('warning', 'Purchase order created (ID: ' . $po_id . ') but no items were added. Please add items manually.');
                }
            } else {
                $this->session->set_flashdata('error', 'Failed to create purchase order. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/purchase');
        } else {
            // If not a proper POST, redirect to purchase orders list
            redirect('inventory/purchase');
        }
    }

    public function edit_purchase_order($id = null)
    {
        // Handle modal form submission (when purchase_order_id is posted)
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('purchase_order_id')) {
            
            // Validate required fields
            $this->form_validation->set_rules('purchase_order_id', 'Purchase Order ID', 'required');
            $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
            $this->form_validation->set_rules('order_date', 'Order Date', 'required');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Please fill all required fields correctly');
                redirect('inventory/purchase');
                return;
            }
            
            $po_id = $this->input->post('purchase_order_id');
            
            // Check if PO exists
            $existing_po = $this->purchase_model->getPurchaseOrderById($po_id);
            if (!$existing_po) {
                $this->session->set_flashdata('error', 'Purchase order not found');
                redirect('inventory/purchase');
                return;
            }
            
            // Check if items can be edited (only in draft or sent status)
            $items_editable = in_array($existing_po->status, array('draft', 'sent'));
            
            $po_data = array(
                'supplier_id' => $this->input->post('supplier_id'),
                'order_date' => $this->input->post('order_date'),
                'expected_delivery_date' => $this->input->post('expected_delivery_date'),
                'payment_terms' => $this->input->post('payment_terms'),
                'status' => $this->input->post('status'),
                'notes' => $this->input->post('notes'),
                'updated_by' => $this->ion_auth->user()->row()->id,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $updated = $this->purchase_model->updatePurchaseOrder($po_id, $po_data);
            
            if ($updated) {
                $items_message = '';
                
                // Only update items if they are editable
                if ($items_editable) {
                // Delete existing items and add new ones
                $this->purchase_model->deletePurchaseOrderItems($po_id);
                
                // Add new items
                $items = $this->input->post('items');
                $items_added = 0;
                
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if (!empty($item['inventory_item_id']) && !empty($item['quantity'])) {
                            $item_data = array(
                                'purchase_order_id' => $po_id,
                                'inventory_item_id' => $item['inventory_item_id'],
                                'quantity_ordered' => $item['quantity'],
                                'unit_price' => $item['unit_price'] ?: 0,
                                'total_price' => $item['quantity'] * ($item['unit_price'] ?: 0)
                            );
                            
                            $item_id = $this->purchase_model->insertPurchaseOrderItem($item_data);
                            if ($item_id) {
                                $items_added++;
                            }
                        }
                    }
                    }
                    
                    // Calculate and update the purchase order totals after items are updated
                    $total_amount = $this->purchase_model->calculatePurchaseOrderTotal($po_id);
                    $tax_amount = $this->input->post('tax_amount') ?: 0;
                    $discount_amount = $this->input->post('discount_amount') ?: 0;
                    $shipping_amount = $this->input->post('shipping_amount') ?: 0;
                    $grand_total = $total_amount + $tax_amount + $shipping_amount - $discount_amount;
                    
                    $update_totals = array(
                        'total_amount' => $total_amount,
                        'tax_amount' => $tax_amount,
                        'discount_amount' => $discount_amount,
                        'shipping_amount' => $shipping_amount,
                        'grand_total' => $grand_total
                    );
                    
                    $this->purchase_model->updatePurchaseOrder($po_id, $update_totals);
                    
                    $items_message = ' ' . $items_added . ' items updated.';
                } else {
                    $items_message = ' Items not modified (status does not allow item changes).';
                }
                
                $this->session->set_flashdata('success', 'Purchase order updated successfully.' . $items_message);
            } else {
                $this->session->set_flashdata('error', 'Failed to update purchase order. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/purchase');
            return;
        }

        // Handle page-based editing (original functionality)
        if ($id === null) {
            show_404();
        }

        $purchase_order = $this->purchase_model->getPurchaseOrderById($id);
        if (!$purchase_order) {
            show_404();
        }

        if ($this->input->post('submit')) {
            $po_data = array(
                'supplier_id' => $this->input->post('supplier_id'),
                'order_date' => $this->input->post('order_date'),
                'expected_delivery_date' => $this->input->post('expected_delivery_date'),
                'payment_terms' => $this->input->post('payment_terms'),
                'delivery_address' => $this->input->post('delivery_address'),
                'tax_amount' => $this->input->post('tax_amount'),
                'discount_amount' => $this->input->post('discount_amount'),
                'shipping_amount' => $this->input->post('shipping_amount'),
                'notes' => $this->input->post('notes'),
                'updated_by' => $this->ion_auth->user()->row()->id
            );

            // Calculate totals
            $total_amount = $this->purchase_model->calculatePurchaseOrderTotal($id);
            $po_data['total_amount'] = $total_amount;
            $po_data['grand_total'] = $total_amount + $po_data['tax_amount'] + $po_data['shipping_amount'] - $po_data['discount_amount'];

            $this->purchase_model->updatePurchaseOrder($id, $po_data);
            $this->session->set_flashdata('success', 'Purchase order updated successfully');
        }

        $data = array();
        $data['page'] = 'edit_purchase_order';
        $data['page_title'] = 'Edit Purchase Order';
        $data['purchase_order'] = $purchase_order;
        $data['items'] = $this->purchase_model->getPurchaseOrderItems($id);
        $data['suppliers'] = $this->supplier_model->getActiveSuppliers();
        $data['inventory_items'] = $this->inventory_model->getActiveInventoryItems();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/edit_purchase_order', $data);
        $this->load->view('home/footer');
    }

    public function view_purchase_order($id)
    {
        $purchase_order = $this->purchase_model->getPurchaseOrderById($id);
        if (!$purchase_order) {
            show_404();
        }

        $data = array();
        $data['page'] = 'view_purchase_order';
        $data['page_title'] = 'Purchase Order Details';
        $data['purchase_order'] = $purchase_order;
        $data['items'] = $this->purchase_model->getPurchaseOrderItems($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/view_purchase_order', $data);
        $this->load->view('home/footer');
    }

    public function delete_purchase_order($id)
    {
        $purchase_order = $this->purchase_model->getPurchaseOrderById($id);
        if ($purchase_order && in_array($purchase_order->status, array('draft', 'sent'))) {
            $this->purchase_model->deletePurchaseOrder($id);
            $this->session->set_flashdata('success', 'Purchase order deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Cannot delete purchase order in current status');
        }
        redirect('inventory/purchase');
    }

    public function send_to_supplier($id)
    {
        $this->purchase_model->updatePurchaseOrder($id, array(
            'status' => 'sent',
            'updated_by' => $this->ion_auth->user()->row()->id
        ));
        
        $this->session->set_flashdata('success', 'Purchase order sent to supplier');
        redirect('inventory/purchase/view/' . $id);
    }

    public function receive_items($id)
    {
        $purchase_order = $this->purchase_model->getPurchaseOrderById($id);
        if (!$purchase_order || !in_array($purchase_order->status, array('sent', 'confirmed', 'partially_received'))) {
            show_404();
        }

        if ($this->input->post('submit')) {
            $items = $this->input->post('items');
            
            foreach ($items as $item_id => $item_data) {
                if ($item_data['quantity_received'] > 0) {
                    // Get purchase order item details
                    $po_item = $this->purchase_model->getPurchaseOrderItemById($item_id);
                    
                    if ($po_item) {
                        // Update purchase order item
                        $received = $this->purchase_model->receivePurchaseOrderItem(
                            $item_id,
                            $item_data['quantity_received'],
                            $this->ion_auth->user()->row()->id,
                            $item_data['notes'],
                            $item_data['batch_number'],
                            $item_data['expiry_date']
                        );
                        
                        if ($received) {
                            // Update inventory stock (increase)
                            $stock_updated = $this->inventory_model->increaseStock(
                                $po_item->inventory_item_id, 
                                $item_data['quantity_received'], 
                                'purchase', 
                                $id, 
                                'Purchase received - PO#' . $purchase_order->po_number
                            );
                            
                            if (!$stock_updated) {
                                log_message('error', 'Failed to update stock for item ID: ' . $po_item->inventory_item_id . ' after receiving purchase order item ID: ' . $item_id);
                            }
                        }
                    }
                }
            }
            
            $this->session->set_flashdata('success', 'Items received successfully');
            redirect('inventory/purchase/view/' . $id);
        }

        $data = array();
        $data['page'] = 'receive_items';
        $data['page_title'] = 'Receive Items';
        $data['purchase_order'] = $purchase_order;
        $data['items'] = $this->purchase_model->getPurchaseOrderItems($id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/receive_items', $data);
        $this->load->view('home/footer');
    }

    // Purchase Order Items Methods
    public function add_purchase_item($po_id)
    {
        $inventory_item_id = $this->input->post('inventory_item_id');
        $quantity = $this->input->post('quantity');
        $unit_price = $this->input->post('unit_price');
        
        $item_data = array(
            'purchase_order_id' => $po_id,
            'inventory_item_id' => $inventory_item_id,
            'quantity_ordered' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $quantity * $unit_price
        );

        $item_id = $this->purchase_model->insertPurchaseOrderItem($item_data);
        
        if ($item_id) {
            echo json_encode(array('status' => 'success', 'message' => 'Item added successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to add item'));
        }
    }

    public function update_purchase_item()
    {
        $item_id = $this->input->post('item_id');
        $quantity = $this->input->post('quantity');
        $unit_price = $this->input->post('unit_price');
        
        $item_data = array(
            'quantity_ordered' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $quantity * $unit_price
        );

        $this->purchase_model->updatePurchaseOrderItem($item_id, $item_data);
        echo json_encode(array('status' => 'success', 'message' => 'Item updated successfully'));
    }

    public function delete_purchase_item($item_id)
    {
        $this->purchase_model->deletePurchaseOrderItem($item_id);
        echo json_encode(array('status' => 'success', 'message' => 'Item deleted successfully'));
    }

    // Vendor Quotations
    public function vendor_quotations()
    {
        $data = array();
        $data['page'] = 'vendor_quotations';
        $data['page_title'] = 'Vendor Quotations';
        $data['quotations'] = $this->purchase_model->getVendorQuotations();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/vendor_quotations', $data);
        $this->load->view('home/footer');
    }

    public function add_quotation()
    {
        if ($this->input->post('submit')) {
            $quotation_data = array(
                'quotation_number' => $this->purchase_model->generateQuotationNumber(),
                'supplier_id' => $this->input->post('supplier_id'),
                'quotation_date' => $this->input->post('quotation_date'),
                'validity_date' => $this->input->post('validity_date'),
                'terms_conditions' => $this->input->post('terms_conditions'),
                'notes' => $this->input->post('notes'),
                'status' => 'draft',
                'created_by' => $this->ion_auth->user()->row()->id
            );

            $quotation_id = $this->purchase_model->insertVendorQuotation($quotation_data);
            
            if ($quotation_id) {
                $this->session->set_flashdata('success', 'Quotation created successfully');
                redirect('inventory/purchase/edit_quotation/' . $quotation_id);
            } else {
                $this->session->set_flashdata('error', 'Failed to create quotation');
            }
        }

        $data = array();
        $data['page'] = 'add_quotation';
        $data['page_title'] = 'Create Quotation Request';
        $data['suppliers'] = $this->supplier_model->getActiveSuppliers();
        $data['generated_quotation_number'] = $this->purchase_model->generateQuotationNumber();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/add_quotation', $data);
        $this->load->view('home/footer');
    }

    public function convert_quotation_to_po($quotation_id)
    {
        $po_id = $this->purchase_model->convertQuotationToPurchaseOrder($quotation_id);
        
        if ($po_id) {
            $this->session->set_flashdata('success', 'Quotation converted to purchase order successfully');
            redirect('inventory/purchase/view/' . $po_id);
        } else {
            $this->session->set_flashdata('error', 'Failed to convert quotation to purchase order');
            redirect('inventory/purchase/quotations');
        }
    }

    // Purchase Reports
    public function purchase_report()
    {
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');

        $data = array();
        $data['page'] = 'purchase_report';
        $data['page_title'] = 'Purchase Report';
        $data['purchases'] = $this->purchase_model->getPurchaseReportByDateRange($start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/purchase_report', $data);
        $this->load->view('home/footer');
    }

    public function pending_deliveries()
    {
        $data = array();
        $data['page'] = 'pending_deliveries';
        $data['page_title'] = 'Pending Deliveries';
        $data['pending_orders'] = $this->purchase_model->getPendingDeliveries();
        $data['overdue_orders'] = $this->purchase_model->getOverdueDeliveries();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/pending_deliveries', $data);
        $this->load->view('home/footer');
    }

    public function top_purchased_items()
    {
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');

        $data = array();
        $data['page'] = 'top_purchased_items';
        $data['page_title'] = 'Top Purchased Items';
        $data['items'] = $this->purchase_model->getTopPurchasedItems(20, $start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/top_purchased_items', $data);
        $this->load->view('home/footer');
    }

    public function print_po($id)
    {
        $data = array();
        $data['purchase_order'] = $this->purchase_model->getPurchaseOrderById($id);
        $data['items'] = $this->purchase_model->getPurchaseOrderItems($id);
        $data['settings'] = $this->settings_model->getSettings();
        
        // Print view doesn't need header/footer
        $this->load->view('inventory/print_purchase_order', $data);
    }

    public function generate_pdf($id)
    {
        // Load PDF library (you may need to install/configure a PDF library)
        $this->load->library('pdf');
        
        $data = array();
        $data['purchase_order'] = $this->purchase_model->getPurchaseOrderById($id);
        $data['items'] = $this->purchase_model->getPurchaseOrderItems($id);
        $data['settings'] = $this->settings_model->getSettings();
        
        $html = $this->load->view('inventory/pdf_purchase_order', $data, true);
        $this->pdf->generate($html, 'PO_' . $data['purchase_order']->po_number);
    }

    // =================== USAGE METHODS ===================

    public function usage($action = 'index', $id = null)
    {
        switch ($action) {
            case 'add':
                return $this->add_usage();
            case 'edit':
                return $this->edit_usage($id);
            case 'view':
                return $this->view_usage($id);
            case 'delete':
                return $this->delete_usage($id);
            case 'by_patient':
                return $this->usage_by_patient($id);
            case 'by_doctor':
                return $this->usage_by_doctor($id);
            case 'by_department':
                return $this->usage_by_department($id);
            case 'by_item':
                return $this->usage_by_item($id);
            case 'bulk_add':
                return $this->bulk_add_usage();
            case 'reports':
                return $this->usage_reports();
            case 'daily_report':
                return $this->daily_usage_report();
            case 'monthly_report':
                return $this->monthly_usage_report();
            case 'cost_analysis':
                return $this->usage_cost_analysis();
            case 'trend_analysis':
                return $this->usage_trend_analysis($id);
            default:
                return $this->usage_logs();
        }
    }

    public function usage_logs()
    {
        $data = array();
        $data['page'] = 'usage_logs';
        $data['page_title'] = 'Usage Logs';
        // Note: usage_logs data is now loaded via server-side DataTables
        $data['inventory_items'] = $this->inventory_model->getActiveInventoryItemsWithCalculatedStock();
        $data['patients'] = $this->patient_model->getPatient();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['departments'] = $this->department_model->getDepartment();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_logs', $data);
        $this->load->view('home/footer');
    }

    public function add_usage()
    {
        // Check if this is a POST request with form data
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('inventory_item_id')) {
            
            // Validate required fields
            $this->form_validation->set_rules('inventory_item_id', 'Inventory Item', 'required');
            $this->form_validation->set_rules('quantity_used', 'Quantity Used', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('used_by_type', 'Used By Type', 'required');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', 'Please fill all required fields correctly');
                redirect('inventory/usage');
                return;
            }
            
            $inventory_item_id = $this->input->post('inventory_item_id');
            $quantity_used = $this->input->post('quantity_used');
            
            // Get item details for cost calculation
            $item = $this->inventory_model->getInventoryItemById($inventory_item_id);
            
            if (!$item) {
                $this->session->set_flashdata('error', 'Inventory item not found');
                redirect('inventory/usage');
                return;
            }
            
            $calculated_stock = $this->inventory_model->calculateCurrentStock($inventory_item_id);
            if ($calculated_stock < $quantity_used) {
                $this->session->set_flashdata('error', 'Insufficient stock. Available: ' . $calculated_stock . ', Requested: ' . $quantity_used);
                redirect('inventory/usage');
                return;
            }

            $data = array(
                'inventory_item_id' => $inventory_item_id,
                'department_id' => $this->input->post('department_id'),
                'used_by_type' => $this->input->post('used_by_type'),
                'used_by_id' => $this->input->post('used_by_id'),
                'patient_id' => $this->input->post('patient_id'),
                'doctor_id' => $this->input->post('doctor_id'),
                'quantity_used' => $quantity_used,
                'unit_cost' => $item->unit_cost,
                'total_cost' => $quantity_used * $item->unit_cost,
                'usage_date' => $this->input->post('usage_date') ?: date('Y-m-d H:i:s'),
                'purpose' => $this->input->post('purpose'),
                'notes' => $this->input->post('notes'),
                'batch_number' => $this->input->post('batch_number'),
                'expiry_date' => $this->input->post('expiry_date'),
                'hospital_id' => $this->session->userdata('hospital_id'),
                'created_by' => $this->ion_auth->user()->row()->id,
                'created_at' => date('Y-m-d H:i:s')
            );

            $usage_id = $this->usage_model->insertUsage($data);
            
            if ($usage_id) {
                // Update stock quantity (decrease)
                $stock_updated = $this->inventory_model->decreaseStock(
                    $inventory_item_id, 
                    $quantity_used, 
                    'usage', 
                    $usage_id, 
                    'Usage logged: ' . ($data['purpose'] ?: 'General usage')
                );
                
                if ($stock_updated) {
                    $this->session->set_flashdata('success', 'Usage logged successfully with ID: ' . $usage_id . '. Stock updated.');
                } else {
                    $this->session->set_flashdata('warning', 'Usage logged (ID: ' . $usage_id . ') but stock update failed. Please check manually.');
                }
            } else {
                $this->session->set_flashdata('error', 'Failed to log usage. Database error: ' . $this->db->error()['message']);
            }
            redirect('inventory/usage');
        } else {
            // If not a proper POST, redirect to usage logs list
            redirect('inventory/usage');
        }
    }

    public function edit_usage($id)
    {
        $usage = $this->usage_model->getUsageLogById($id);
        if (!$usage) {
            show_404();
        }

        if ($this->input->post('submit')) {
            $new_quantity = $this->input->post('quantity_used');
            $old_quantity = $usage->quantity_used;
            $quantity_diff = $new_quantity - $old_quantity;

            // Check if we have enough stock for the adjustment
            $item = $this->inventory_model->getInventoryItemById($usage->inventory_item_id);
            if ($quantity_diff > 0 && $item->current_stock < $quantity_diff) {
                $this->session->set_flashdata('error', 'Insufficient stock for this adjustment');
                redirect('inventory/usage/edit/' . $id);
                return;
            }

            $data = array(
                'department_id' => $this->input->post('department_id'),
                'used_by_type' => $this->input->post('used_by_type'),
                'used_by_id' => $this->input->post('used_by_id'),
                'patient_id' => $this->input->post('patient_id'),
                'doctor_id' => $this->input->post('doctor_id'),
                'quantity_used' => $new_quantity,
                'total_cost' => $new_quantity * $usage->unit_cost,
                'usage_date' => $this->input->post('usage_date'),
                'purpose' => $this->input->post('purpose'),
                'notes' => $this->input->post('notes'),
                'batch_number' => $this->input->post('batch_number'),
                'expiry_date' => $this->input->post('expiry_date')
            );

            $this->usage_model->updateUsageLog($id, $data);

            // Adjust inventory stock for the difference
            if ($quantity_diff != 0) {
                $new_stock = $item->current_stock - $quantity_diff;
                $this->inventory_model->updateInventoryItem($usage->inventory_item_id, array(
                    'current_stock' => $new_stock
                ));

                // Create adjustment transaction
                $transaction_data = array(
                    'inventory_item_id' => $usage->inventory_item_id,
                    'transaction_type' => 'adjustment',
                    'reference_type' => 'usage_adjustment',
                    'reference_id' => $id,
                    'quantity' => -$quantity_diff,
                    'unit_cost' => $usage->unit_cost,
                    'total_cost' => -$quantity_diff * $usage->unit_cost,
                    'previous_stock' => $item->current_stock,
                    'new_stock' => $new_stock,
                    'notes' => 'Usage log adjustment',
                    'created_by' => $this->ion_auth->user()->row()->id
                );
                $this->inventory_model->insertStockTransaction($transaction_data);
            }

            $this->session->set_flashdata('success', 'Usage updated successfully');
            redirect('inventory/usage');
        }

        $data = array();
        $data['page'] = 'edit_usage';
        $data['page_title'] = 'Edit Usage';
        $data['usage'] = $usage;
        $data['patients'] = $this->patient_model->getPatient();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['departments'] = $this->department_model->getDepartment();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/edit_usage', $data);
        $this->load->view('home/footer');
    }

    public function view_usage($id)
    {
        $usage = $this->usage_model->getUsageLogById($id);
        if (!$usage) {
            show_404();
        }

        $data = array();
        $data['page'] = 'view_usage';
        $data['page_title'] = 'Usage Details';
        $data['usage'] = $usage;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/view_usage', $data);
        $this->load->view('home/footer');
    }

    public function delete_usage($id)
    {
        $this->usage_model->deleteUsageLog($id);
        $this->session->set_flashdata('success', 'Usage log deleted successfully');
        redirect('inventory/usage');
    }

    // Usage Filtering Methods
    public function usage_by_patient($patient_id)
    {
        $data = array();
        $data['page'] = 'usage_by_patient';
        $data['page_title'] = 'Usage by Patient';
        $data['usage_logs'] = $this->usage_model->getUsageByPatient($patient_id);
        $data['patient'] = $this->patient_model->getPatientById($patient_id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_by_patient', $data);
        $this->load->view('home/footer');
    }

    public function usage_by_doctor($doctor_id)
    {
        $data = array();
        $data['page'] = 'usage_by_doctor';
        $data['page_title'] = 'Usage by Doctor';
        $data['usage_logs'] = $this->usage_model->getUsageByDoctor($doctor_id);
        $data['doctor'] = $this->doctor_model->getDoctorById($doctor_id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_by_doctor', $data);
        $this->load->view('home/footer');
    }

    public function usage_by_department($department_id)
    {
        $data = array();
        $data['page'] = 'usage_by_department';
        $data['page_title'] = 'Usage by Department';
        $data['usage_logs'] = $this->usage_model->getUsageByDepartment($department_id);
        $data['department'] = $this->department_model->getDepartmentById($department_id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_by_department', $data);
        $this->load->view('home/footer');
    }

    public function usage_by_item($item_id)
    {
        $data = array();
        $data['page'] = 'usage_by_item';
        $data['page_title'] = 'Usage by Item';
        $data['usage_logs'] = $this->usage_model->getUsageByItem($item_id);
        $data['item'] = $this->inventory_model->getInventoryItemById($item_id);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_by_item', $data);
        $this->load->view('home/footer');
    }

    // Bulk Operations
    public function bulk_add_usage()
    {
        if ($this->input->post('submit')) {
            $usage_data = $this->input->post('usage_data');
            $parsed_data = json_decode($usage_data, true);
            
            if ($parsed_data && is_array($parsed_data)) {
                $usage_array = array();
                
                foreach ($parsed_data as $row) {
                    $item = $this->inventory_model->getInventoryItemById($row['inventory_item_id']);
                    $calculated_stock = $this->inventory_model->calculateCurrentStock($row['inventory_item_id']);
                    if ($item && $calculated_stock >= $row['quantity_used']) {
                        $usage_array[] = array(
                            'inventory_item_id' => $row['inventory_item_id'],
                            'department_id' => $row['department_id'],
                            'used_by_type' => $row['used_by_type'],
                            'used_by_id' => $row['used_by_id'],
                            'patient_id' => $row['patient_id'],
                            'doctor_id' => $row['doctor_id'],
                            'quantity_used' => $row['quantity_used'],
                            'unit_cost' => $item->unit_cost,
                            'total_cost' => $row['quantity_used'] * $item->unit_cost,
                            'usage_date' => $row['usage_date'],
                            'purpose' => $row['purpose'],
                            'notes' => $row['notes'],
                            'created_by' => $this->ion_auth->user()->row()->id
                        );
                    }
                }
                
                if (!empty($usage_array)) {
                    $inserted = $this->usage_model->bulkInsertUsage($usage_array);
                    
                    if ($inserted > 0) {
                        // Update stock levels for each item used
                        $stock_update_failures = 0;
                        foreach ($usage_array as $usage_record) {
                            $stock_updated = $this->inventory_model->decreaseStock(
                                $usage_record['inventory_item_id'], 
                                $usage_record['quantity_used'], 
                                'bulk_usage', 
                                null, 
                                'Bulk usage: ' . ($usage_record['purpose'] ?: 'General usage')
                            );
                            
                            if (!$stock_updated) {
                                $stock_update_failures++;
                                log_message('error', 'Failed to update stock for item ID: ' . $usage_record['inventory_item_id'] . ' in bulk usage operation');
                            }
                        }
                        
                        if ($stock_update_failures == 0) {
                            $this->session->set_flashdata('success', $inserted . ' usage records inserted successfully. All stock levels updated.');
                        } else {
                            $this->session->set_flashdata('warning', $inserted . ' usage records inserted, but ' . $stock_update_failures . ' stock updates failed. Please check manually.');
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Failed to insert usage records');
                    }
                } else {
                    $this->session->set_flashdata('error', 'No valid usage records to insert');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid data format');
            }
            
            redirect('inventory/usage');
        }

        $data = array();
        $data['page'] = 'bulk_add_usage';
        $data['page_title'] = 'Bulk Add Usage';
        $data['inventory_items'] = $this->inventory_model->getActiveInventoryItemsWithCalculatedStock();
        $data['patients'] = $this->patient_model->getPatient();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['departments'] = $this->department_model->getDepartment();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/bulk_add_usage', $data);
        $this->load->view('home/footer');
    }

    // Usage Reports
    public function usage_reports()
    {
        $data = array();
        $data['page'] = 'usage_reports';
        $data['page_title'] = 'Usage Reports';
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_reports', $data);
        $this->load->view('home/footer');
    }

    public function daily_usage_report()
    {
        $date = $this->input->get('date') ?: date('Y-m-d');

        $data = array();
        $data['page'] = 'daily_usage_report';
        $data['page_title'] = 'Daily Usage Report';
        $data['usage_data'] = $this->usage_model->getDailyUsageReport($date);
        $data['date'] = $date;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/daily_usage_report', $data);
        $this->load->view('home/footer');
    }

    public function monthly_usage_report()
    {
        $year = $this->input->get('year') ?: date('Y');
        $month = $this->input->get('month') ?: date('m');

        $data = array();
        $data['page'] = 'monthly_usage_report';
        $data['page_title'] = 'Monthly Usage Report';
        $data['usage_data'] = $this->usage_model->getMonthlyUsageReport($year, $month);
        $data['year'] = $year;
        $data['month'] = $month;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/monthly_usage_report', $data);
        $this->load->view('home/footer');
    }

    public function usage_cost_analysis()
    {
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');

        $data = array();
        $data['page'] = 'usage_cost_analysis';
        $data['page_title'] = 'Usage Cost Analysis';
        $data['cost_data'] = $this->usage_model->getUsageCostAnalysis($start_date, $end_date);
        $data['statistics'] = $this->usage_model->getUsageStatistics($start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_cost_analysis', $data);
        $this->load->view('home/footer');
    }

    public function usage_trend_analysis($item_id)
    {
        $months = $this->input->get('months') ?: 6;
        
        $data = array();
        $data['page'] = 'usage_trend_analysis';
        $data['page_title'] = 'Usage Trend Analysis';
        $data['trend_data'] = $this->usage_model->getUsageTrendByItem($item_id, $months);
        $data['item'] = $this->inventory_model->getInventoryItemById($item_id);
        $data['predicted_usage'] = $this->usage_model->getPredictedUsage($item_id, 30);
        $data['months'] = $months;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/usage_trend_analysis', $data);
        $this->load->view('home/footer');
    }

    // =================== UTILITY METHODS ===================

    /**
     * Recalculate totals for all purchase orders (utility function)
     */
    public function recalculate_purchase_totals()
    {
        // Get all purchase orders
        $this->db->select('id');
        $this->db->from('purchase_orders');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get();
        $purchase_orders = $query->result();
        
        $updated_count = 0;
        foreach ($purchase_orders as $po) {
            $total_amount = $this->purchase_model->calculatePurchaseOrderTotal($po->id);
            
            // Get existing PO data to preserve other fields
            $existing_po = $this->purchase_model->getPurchaseOrderById($po->id);
            $tax_amount = $existing_po->tax_amount ?: 0;
            $discount_amount = $existing_po->discount_amount ?: 0;
            $shipping_amount = $existing_po->shipping_amount ?: 0;
            $grand_total = $total_amount + $tax_amount + $shipping_amount - $discount_amount;
            
            $update_data = array(
                'total_amount' => $total_amount,
                'grand_total' => $grand_total
            );
            
            $this->purchase_model->updatePurchaseOrder($po->id, $update_data);
            $updated_count++;
        }
        
        $this->session->set_flashdata('success', "Recalculated totals for {$updated_count} purchase orders.");
        redirect('inventory/purchase');
    }

    // =================== REPORTS METHODS ===================

    public function reports()
    {
        $data = array();
        $data['page'] = 'inventory_reports';
        $data['page_title'] = 'Inventory Reports';
        
        // Get real analytics data for Quick Analysis
        $data['total_items'] = count($this->inventory_model->getActiveInventoryItems());
        $data['low_stock_items'] = count($this->inventory_model->getLowStockItems());
        $data['total_value'] = $this->inventory_model->getTotalInventoryValue();
        $data['monthly_usage_value'] = $this->usage_model->getMonthlyUsageValue();
        
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/reports', $data);
        $this->load->view('home/footer');
    }

    public function valuation_report()
    {
        $data = array();
        $data['page'] = 'valuation_report';
        $data['page_title'] = 'Inventory Valuation Report';
        $data['items'] = $this->inventory_model->getInventoryValuation();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/valuation_report', $data);
        $this->load->view('home/footer');
    }

    public function movement_report()
    {
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');

        $data = array();
        $data['page'] = 'movement_report';
        $data['page_title'] = 'Inventory Movement Report';
        $data['movements'] = $this->inventory_model->getInventoryMovementReport($start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard');
        $this->load->view('inventory/movement_report', $data);
        $this->load->view('home/footer');
    }

    // =================== EXPORT METHODS ===================

    public function export($type)
    {
        try {
            $start_date = $this->input->get('start_date') ?: date('Y-m-01');
            $end_date = $this->input->get('end_date') ?: date('Y-m-d');
            
            switch ($type) {
                case 'inventory':
                    $this->export_inventory_items();
                    break;
                case 'usage':
                    $this->export_usage_data($start_date, $end_date);
                    break;
                case 'purchases':
                    $this->export_purchase_orders($start_date, $end_date);
                    break;
                case 'suppliers':
                    $this->export_suppliers();
                    break;
                default:
                    show_404();
            }
        } catch (Exception $e) {
            log_message('error', 'Export error: ' . $e->getMessage());
            show_error('Export failed: ' . $e->getMessage(), 500);
        }
    }

    private function export_inventory_items()
    {
        try {
            $items = $this->inventory_model->getActiveInventoryItems();
            
            if (empty($items)) {
                throw new Exception('No inventory items found');
            }
            
            $filename = 'inventory_items_' . date('Y-m-d') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($output, array(
                'Item Code',
                'Item Name',
                'Category',
                'Current Stock',
                'Unit Cost',
                'Total Value',
                'Unit of Measure',
                'Status'
            ));
            
            // CSV Data
            foreach ($items as $item) {
                fputcsv($output, array(
                    $item->item_code,
                    $item->name,
                    $item->category,
                    $item->current_stock,
                    $item->unit_cost,
                    $item->current_stock * $item->unit_cost,
                    $item->unit_of_measure,
                    $item->status
                ));
            }
            
            fclose($output);
            exit;
        } catch (Exception $e) {
            log_message('error', 'Inventory export error: ' . $e->getMessage());
            show_error('Inventory export failed: ' . $e->getMessage(), 500);
        }
    }

    private function export_usage_data($start_date, $end_date)
    {
        try {
            $usage_logs = $this->usage_model->getUsageLogsByDateRange($start_date, $end_date);
            
            $filename = 'usage_data_' . $start_date . '_to_' . $end_date . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($output, array(
                'Usage Date',
                'Item Name',
                'Item Code',
                'Quantity Used',
                'Unit Cost',
                'Total Cost',
                'Used By Type',
                'Purpose',
                'Notes'
            ));
            
            // CSV Data
            foreach ($usage_logs as $usage) {
                fputcsv($output, array(
                    $usage->usage_date,
                    $usage->item_name,
                    $usage->item_code,
                    $usage->quantity_used,
                    $usage->unit_cost,
                    $usage->total_cost,
                    $usage->used_by_type,
                    $usage->purpose,
                    $usage->notes
                ));
            }
            
            fclose($output);
            exit;
        } catch (Exception $e) {
            log_message('error', 'Usage export error: ' . $e->getMessage());
            show_error('Usage export failed: ' . $e->getMessage(), 500);
        }
    }

    private function export_purchase_orders($start_date, $end_date)
    {
        try {
            $purchase_orders = $this->purchase_model->getPurchaseOrdersByDateRange($start_date, $end_date);
            
            $filename = 'purchase_orders_' . $start_date . '_to_' . $end_date . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($output, array(
                'Order Number',
                'Supplier',
                'Order Date',
                'Expected Delivery',
                'Status',
                'Total Amount',
                'Grand Total'
            ));
            
            // CSV Data
            foreach ($purchase_orders as $po) {
                fputcsv($output, array(
                    $po->order_number,
                    $po->supplier_name,
                    $po->order_date,
                    $po->expected_delivery_date,
                    $po->status,
                    $po->total_amount,
                    $po->grand_total
                ));
            }
            
            fclose($output);
            exit;
        } catch (Exception $e) {
            log_message('error', 'Purchase orders export error: ' . $e->getMessage());
            show_error('Purchase orders export failed: ' . $e->getMessage(), 500);
        }
    }

    private function export_suppliers()
    {
        try {
            $suppliers = $this->supplier_model->getSuppliers();
            
            if (empty($suppliers)) {
                throw new Exception('No suppliers found');
            }
            
            $filename = 'suppliers_' . date('Y-m-d') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($output, array(
                'Supplier Name',
                'Company Name',
                'Contact Person',
                'Email',
                'Phone',
                'Mobile',
                'Address',
                'City',
                'State',
                'Country',
                'Status'
            ));
            
            // CSV Data
            foreach ($suppliers as $supplier) {
                fputcsv($output, array(
                    $supplier->name,
                    $supplier->company_name,
                    $supplier->contact_person,
                    $supplier->email,
                    $supplier->phone,
                    $supplier->mobile,
                    $supplier->address,
                    $supplier->city,
                    $supplier->state,
                    $supplier->country,
                    $supplier->status
                ));
            }
            
            fclose($output);
            exit;
        } catch (Exception $e) {
            log_message('error', 'Suppliers export error: ' . $e->getMessage());
            show_error('Suppliers export failed: ' . $e->getMessage(), 500);
        }
    }

    // =================== AJAX METHODS ===================

    public function get_usage_view_data()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('usage_id')) {
            $usage_id = $this->input->post('usage_id');
            $usage = $this->usage_model->getUsageLogById($usage_id);
            
            if ($usage) {
                // Generate HTML content for the modal
                $html = $this->generate_usage_view_html($usage);
                
                echo json_encode(array(
                    'success' => true,
                    'html' => $html
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Usage log not found'
                ));
            }
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Invalid request'
            ));
        }
    }

    private function generate_usage_view_html($usage)
    {
        $settings = $this->settings_model->getSettings();
        
        // Generate detailed HTML for usage view
        $html = '
        <div class="container-fluid p-4">
            <div class="row">
                <!-- Item Information -->
                <div class="col-md-6">
                    <div class="card border-left-primary h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-box mr-2"></i>Item Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4 font-weight-bold">Item Name:</div>
                                <div class="col-sm-8">' . htmlspecialchars($usage->item_name) . '</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-4 font-weight-bold">Item Code:</div>
                                <div class="col-sm-8"><code>' . htmlspecialchars($usage->item_code) . '</code></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-4 font-weight-bold">Category:</div>
                                <div class="col-sm-8">' . htmlspecialchars($usage->category ?: 'N/A') . '</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-4 font-weight-bold">Unit of Measure:</div>
                                <div class="col-sm-8">' . htmlspecialchars($usage->unit_of_measure) . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Usage Details -->
                <div class="col-md-6">
                    <div class="card border-left-success h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-clipboard-list mr-2"></i>Usage Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-5 font-weight-bold">Quantity Used:</div>
                                <div class="col-sm-7">
                                    <span class="badge badge-info badge-lg">' . $usage->quantity_used . ' ' . htmlspecialchars($usage->unit_of_measure) . '</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-5 font-weight-bold">Usage Date:</div>
                                <div class="col-sm-7">' . date('Y-m-d H:i', strtotime($usage->usage_date)) . '</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-5 font-weight-bold">Unit Cost:</div>
                                <div class="col-sm-7">' . $settings->currency . ' ' . number_format($usage->unit_cost, 2) . '</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-5 font-weight-bold">Total Cost:</div>
                                <div class="col-sm-7">
                                    <span class="font-weight-bold text-success">' . $settings->currency . ' ' . number_format($usage->total_cost, 2) . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <!-- Usage Context -->
                <div class="col-md-6">
                    <div class="card border-left-warning h-100">
                        <div class="card-header bg-warning text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-users mr-2"></i>Usage Context
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4 font-weight-bold">Used By Type:</div>
                                <div class="col-sm-8">
                                    <span class="badge badge-primary">' . ucfirst($usage->used_by_type) . '</span>
                                </div>
                            </div>';
                            
        if ($usage->patient_name) {
            $html .= '
                            <div class="row mt-2">
                                <div class="col-sm-4 font-weight-bold">Patient:</div>
                                <div class="col-sm-8">' . htmlspecialchars($usage->patient_name) . '</div>
                            </div>';
        }
        
        if ($usage->doctor_name) {
            $html .= '
                            <div class="row mt-2">
                                <div class="col-sm-4 font-weight-bold">Doctor:</div>
                                <div class="col-sm-8">' . htmlspecialchars($usage->doctor_name) . '</div>
                            </div>';
        }
        
        if ($usage->department_name) {
            $html .= '
                            <div class="row mt-2">
                                <div class="col-sm-4 font-weight-bold">Department:</div>
                                <div class="col-sm-8">' . htmlspecialchars($usage->department_name) . '</div>
                            </div>';
        }
        
        $html .= '
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="col-md-6">
                    <div class="card border-left-info h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle mr-2"></i>Additional Information
                            </h6>
                        </div>
                        <div class="card-body">';
                        
        if ($usage->purpose) {
            $html .= '
                            <div class="row">
                                <div class="col-sm-3 font-weight-bold">Purpose:</div>
                                <div class="col-sm-9">' . htmlspecialchars($usage->purpose) . '</div>
                            </div>';
        }
        
        if ($usage->notes) {
            $html .= '
                            <div class="row mt-2">
                                <div class="col-sm-3 font-weight-bold">Notes:</div>
                                <div class="col-sm-9">' . nl2br(htmlspecialchars($usage->notes)) . '</div>
                            </div>';
        }
        
        if ($usage->batch_number) {
            $html .= '
                            <div class="row mt-2">
                                <div class="col-sm-3 font-weight-bold">Batch Number:</div>
                                <div class="col-sm-9"><code>' . htmlspecialchars($usage->batch_number) . '</code></div>
                            </div>';
        }
        
        if ($usage->expiry_date) {
            $html .= '
                            <div class="row mt-2">
                                <div class="col-sm-3 font-weight-bold">Expiry Date:</div>
                                <div class="col-sm-9">' . date('Y-m-d', strtotime($usage->expiry_date)) . '</div>
                            </div>';
        }
        
        $html .= '
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-sm-3 font-weight-bold">Created By:</div>
                                <div class="col-sm-9">' . htmlspecialchars($usage->created_by_name ?: 'System') . '</div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-3 font-weight-bold">Created At:</div>
                                <div class="col-sm-9">' . date('Y-m-d H:i:s', strtotime($usage->created_at)) . '</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        
        return $html;
    }

    public function get_item_details()
    {
        $id = $this->input->post('id');
        $item = $this->inventory_model->getInventoryItemById($id);
        
        if ($item) {
            echo json_encode(array('status' => 'success', 'item' => $item));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Item not found'));
        }
    }

    public function search_items()
    {
        $search_term = $this->input->post('search');
        $items = $this->inventory_model->searchInventoryItems($search_term);
        echo json_encode($items);
    }

    public function generate_item_code()
    {
        $prefix = $this->input->post('prefix') ?: 'ITM';
        $code = $this->inventory_model->generateItemCode($prefix);
        echo json_encode(array('code' => $code));
    }

    public function get_supplier_info()
    {
        $supplier_id = $this->input->post('supplier_id');
        $supplier = $this->supplier_model->getSupplierById($supplier_id);
        
        if ($supplier) {
            echo json_encode(array('status' => 'success', 'supplier' => $supplier));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Supplier not found'));
        }
    }

    public function get_item_price()
    {
        $item_id = $this->input->post('item_id');
        $item = $this->inventory_model->getInventoryItemById($item_id);
        
        if ($item) {
            echo json_encode(array(
                'status' => 'success', 
                'unit_cost' => $item->unit_cost,
                'unit_of_measure' => $item->unit_of_measure
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Item not found'));
        }
    }

    public function get_supplier_details()
    {
        $id = $this->input->post('id');
        $supplier = $this->supplier_model->getSupplierById($id);
        
        if ($supplier) {
            echo json_encode(array('status' => 'success', 'supplier' => $supplier));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Supplier not found'));
        }
    }

    public function search_suppliers()
    {
        $search_term = $this->input->post('search');
        $suppliers = $this->supplier_model->searchSuppliers($search_term);
        echo json_encode($suppliers);
    }

    public function update_supplier_balance()
    {
        $supplier_id = $this->input->post('supplier_id');
        $amount = $this->input->post('amount');
        $operation = $this->input->post('operation'); // 'add' or 'subtract'
        
        $new_balance = $this->supplier_model->updateSupplierBalance($supplier_id, $amount, $operation);
        
        if ($new_balance !== false) {
            echo json_encode(array('status' => 'success', 'new_balance' => $new_balance));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to update balance'));
        }
    }

    public function get_suppliers_by_category()
    {
        $category = $this->input->post('category');
        $suppliers = $this->supplier_model->getSuppliersByCategory($category);
        echo json_encode($suppliers);
    }

    public function get_item_stock()
    {
        $item_id = $this->input->post('item_id');
        $item = $this->inventory_model->getInventoryItemById($item_id);
        
        if ($item) {
            echo json_encode(array(
                'status' => 'success',
                'current_stock' => $item->current_stock,
                'unit_cost' => $item->unit_cost,
                'unit_of_measure' => $item->unit_of_measure
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Item not found'));
        }
    }

    public function get_usage_statistics()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $stats = $this->usage_model->getUsageStatistics($start_date, $end_date);
        echo json_encode(array('status' => 'success', 'data' => $stats));
    }

    public function search_by_date_range()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $usage_logs = $this->usage_model->getUsageByDateRange($start_date, $end_date);
        echo json_encode($usage_logs);
    }

    // =================== AJAX ENDPOINTS FOR SERVER-SIDE DATATABLES ===================

    public function getUsageLogsAjax()
    {
        try {
            // Set proper headers for AJAX response
            header('Content-Type: application/json');
            
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $order = $this->input->get("order");
            $search = $this->input->get("search");
            $search_value = isset($search['value']) ? $search['value'] : '';

            $column_order = array('ul.usage_date', 'ii.name', 'ul.quantity_used', 'ul.used_by_type', 'ul.purpose', 'ul.total_cost', null);
            $column_search = array('ul.usage_date', 'ii.name', 'ul.quantity_used', 'ul.used_by_type', 'ul.purpose', 'ul.total_cost');

            // Safe order handling
            $order_column = 'ul.usage_date'; // default
            $order_dir = 'DESC'; // default
            
            if (isset($order[0]['column']) && isset($order[0]['dir'])) {
                $col_index = intval($order[0]['column']);
                if (isset($column_order[$col_index]) && $column_order[$col_index] !== null) {
                    $order_column = $column_order[$col_index];
                }
                $order_dir = strtoupper($order[0]['dir']);
            }

        $totalData = $this->usage_model->getUsageLogsWithoutSearch($order_column, $order_dir);

        $totalFiltered = $totalData;

        if (empty($search_value)) {
            $usage_logs = $this->usage_model->getUsageLogsByLimit($length, $start, $order_column, $order_dir);
        } else {
            $usage_logs = $this->usage_model->getUsageLogsByLimitBySearch($length, $start, $search_value, $order_column, $order_dir);
            $totalFiltered = $this->usage_model->getUsageLogsBySearch($search_value, $order_column, $order_dir);
        }

        $data = array();
        foreach ($usage_logs as $usage) {
            $nestedData = array();

            // Usage Date
            $nestedData[] = date('Y-m-d H:i', strtotime($usage->usage_date));

            // Item Name with Code
            $nestedData[] = '<div class="font-weight-bold">
                                <a onclick="viewUsage(' . $usage->id . ')" style="text-decoration: none;">' . $usage->item_name . '</a>
                                <br><small class="text-muted">' . $usage->item_code . '</small>
                            </div>';

            // Quantity Used
            $nestedData[] = '<span class="badge badge-info">' . $usage->quantity_used . ' ' . $usage->unit_of_measure . '</span>';

            // Used By Type
            $badge_class = 'secondary';
            if ($usage->used_by_type == 'patient') $badge_class = 'primary';
            else if ($usage->used_by_type == 'doctor') $badge_class = 'success';
            else if ($usage->used_by_type == 'nurse') $badge_class = 'warning';
            
            $nestedData[] = '<span class="badge badge-' . $badge_class . '">' . ucfirst($usage->used_by_type) . '</span>';

            // Purpose
            $nestedData[] = $usage->purpose ? $usage->purpose : '-';

            // Total Cost
            $nestedData[] = $this->settings_model->getSettings()->currency . ' ' . number_format($usage->total_cost, 2);

            // Actions
            $actions = '<div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" onclick="viewUsage(' . $usage->id . ')">
                                    <i class="fas fa-eye mr-2"></i> View
                                </a>
                                <a class="dropdown-item" href="' . base_url('inventory/usage/edit/' . $usage->id) . '">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="' . base_url('inventory/usage/delete/' . $usage->id) . '" onclick="return confirm(\'Are you sure? This will return the used quantity to inventory.\')">
                                    <i class="fas fa-trash mr-2"></i> Delete
                                </a>
                            </div>
                        </div>';
            
            $nestedData[] = $actions;

            $data[] = $nestedData;
        }

            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );

            echo json_encode($json_data);
            
        } catch (Exception $e) {
            // Log the error
            log_message('error', 'getUsageLogsAjax AJAX Error: ' . $e->getMessage());
            
            // Return error response
            $error_response = array(
                "draw" => isset($draw) ? intval($draw) : 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
                "error" => "An error occurred while loading usage logs. Please refresh the page."
            );
            
            echo json_encode($error_response);
        }
    }
    
    /**
     * Helper function to clear all flash messages
     */
    private function clearFlashMessages()
    {
        $this->session->unset_userdata('success');
        $this->session->unset_userdata('error');
        $this->session->unset_userdata('warning');
        $this->session->unset_userdata('debug');
        $this->session->unset_userdata('info');
    }
}