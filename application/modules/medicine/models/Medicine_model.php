<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Medicine_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertMedicine($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        // Set initial quantity to 0 - will be calculated from purchases
        $data2['quantity'] = 0;
        $this->db->insert('medicine', $data2);
    }

    function calculateCurrentQuantity($medicine_id)
    {
        $hospital_id = $this->session->userdata('hospital_id');
        
        log_message('debug', "calculateCurrentQuantity called for medicine_id: $medicine_id, hospital_id: $hospital_id");
        
        // Calculate total purchased quantity (use quantity_received for received items)
        $this->db->select('SUM(quantity_received) as total_purchased');
        $this->db->from('medicine_purchase_items mpi');
        $this->db->join('medicine_purchases mp', 'mpi.purchase_id = mp.id', 'left');
        $this->db->where('mpi.medicine_id', $medicine_id);
        $this->db->where('mp.hospital_id', $hospital_id);
        $this->db->where('mp.purchase_status', 'received'); // Only count received purchases
        $this->db->where('mpi.received_status', 'received'); // Only count actually received items
        $purchased = $this->db->get()->row();
        $total_purchased = $purchased ? $purchased->total_purchased : 0;
        
        log_message('debug', "Total purchased for medicine $medicine_id: $total_purchased");
        
        // Calculate total sold quantity from pharmacy sales
        $this->db->where('hospital_id', $hospital_id);
        $pharmacy_payments = $this->db->get('pharmacy_payment')->result();
        $total_sold = 0;
        
        foreach ($pharmacy_payments as $payment) {
            if (!empty($payment->category_name)) {
                $items = explode(',', $payment->category_name);
                foreach ($items as $item) {
                    $parts = explode('*', $item);
                    if (count($parts) >= 4 && $parts[0] == $medicine_id) {
                        $total_sold += floatval($parts[2]); // parts[2] is quantity
                    }
                }
            }
        }
        
        // Calculate current quantity
        $current_quantity = $total_purchased - $total_sold;
        
        log_message('debug', "Total sold for medicine $medicine_id: $total_sold");
        log_message('debug', "Current quantity for medicine $medicine_id: $current_quantity");
        
        return max(0, $current_quantity); // Ensure quantity is never negative
    }

    function updateMedicineQuantity($medicine_id)
    {
        $current_quantity = $this->calculateCurrentQuantity($medicine_id);
        $this->db->where('id', $medicine_id);
        $this->db->update('medicine', array('quantity' => $current_quantity));
        return $current_quantity;
    }

    function getMedicine()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine');
        return $query->result();
    }

    function getMedicineWithoutSearch($order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'asc');
        }
        $query = $this->db->get('medicine');
        return $query->result();
    }

    function getLatestMedicine()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('medicine');
        return $query->result();
    }

    function getMedicineLimitByNumber($number)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine', $number);
        return $query->result();
    }

    function getMedicineByPageNumber($page_number)
    {
        $data_range_1 = 50 * $page_number;
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine', 50, $data_range_1);
        return $query->result();
    }

    function getMedicineByStockAlert()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('quantity <=', 20);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine');
        return $query->result();
    }

    function getMedicineByStockAlertByPageNumber($page_number)
    {
        $data_range_1 = 50 * $page_number;
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('quantity <=', 20);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine', 50, $data_range_1);
        return $query->result();
    }

    function getMedicineById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('medicine');
        return $query->row();
    }

    function getMedicineByKeyByStockAlert($page_number, $key)
    {
        $data_range_1 = 50 * $page_number;
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('quantity <=', 20);
        $this->db->or_like('name', $key);
        $this->db->or_like('company', $key);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine', 50, $data_range_1);
        return $query->result();
    }

    function getMedicineByKey($page_number, $key)
    {
        $data_range_1 = 50 * $page_number;
        $this->db->like('name', $key);
        $this->db->or_like('company', $key);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine', 50, $data_range_1);
        return $query->result();
    }

    function getMedicineByKeyForPos($key)
    {
        $this->db->like('name', $key);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('medicine');
        return $query->result();
    }

    function updateMedicine($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('medicine', $data);
    }

    function insertMedicineCategory($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('medicine_category', $data2);
    }

    function getMedicineCategory()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('medicine_category');
        return $query->result();
    }

    function getMedicineCategoryById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('medicine_category');
        return $query->row();
    }

    function totalStockPrice()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('medicine')->result();
        $stock_price = array();
        foreach ($query as $medicine) {
            $stock_price[] = $medicine->price * $medicine->quantity;
        }

        if (!empty($stock_price)) {
            return array_sum($stock_price);
        } else {
            return 0;
        }
    }

    function updateMedicineCategory($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('medicine_category', $data);
    }

    function deleteMedicine($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('medicine');
    }

    function checkMedicineRelatedRecords($medicine_id)
    {
        $hospital_id = $this->session->userdata('hospital_id');
        $related_records = array();
        
        // Check medicine_purchase_items
        $this->db->where('medicine_id', $medicine_id);
        $purchase_items = $this->db->get('medicine_purchase_items')->num_rows();
        if ($purchase_items > 0) {
            $related_records['purchase_items'] = $purchase_items;
        }
        
        // Check medicine_batches
        $this->db->where('medicine_id', $medicine_id);
        $batches = $this->db->get('medicine_batches')->num_rows();
        if ($batches > 0) {
            $related_records['batches'] = $batches;
        }
        
        // Check pharmacy_payment (sales)
        $this->db->where('hospital_id', $hospital_id);
        $pharmacy_payments = $this->db->get('pharmacy_payment')->result();
        $sales_count = 0;
        foreach ($pharmacy_payments as $payment) {
            if (!empty($payment->category_name)) {
                $items = explode(',', $payment->category_name);
                foreach ($items as $item) {
                    $parts = explode('*', $item);
                    if (count($parts) >= 4 && $parts[0] == $medicine_id) {
                        $sales_count++;
                        break;
                    }
                }
            }
        }
        if ($sales_count > 0) {
            $related_records['sales'] = $sales_count;
        }
        
        // Check prescriptions (if exists)
        if ($this->db->table_exists('prescription')) {
            $this->db->where('hospital_id', $hospital_id);
            $prescriptions = $this->db->get('prescription')->result();
            $prescription_count = 0;
            foreach ($prescriptions as $prescription) {
                if (!empty($prescription->medicine)) {
                    $medicines = explode(',', $prescription->medicine);
                    foreach ($medicines as $medicine) {
                        $parts = explode('*', $medicine);
                        if (count($parts) >= 2 && $parts[0] == $medicine_id) {
                            $prescription_count++;
                            break;
                        }
                    }
                }
            }
            if ($prescription_count > 0) {
                $related_records['prescriptions'] = $prescription_count;
            }
        }
        
        return $related_records;
    }

    function deleteMedicineCategory($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('medicine_category');
    }

    function getMedicineBySearch($search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
            ->from('medicine')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('category', $search)
                ->or_like('name', $search)
                ->or_like('e_date', $search)
                ->or_like('generic', $search)
                ->or_like('company', $search)
                ->or_like('effects', $search)
            ->group_end()
            ->get();
        return $query->result();
    }

    function getMedicineByLimit($limit, $start, $order, $dir)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('medicine');
        return $query->result();
    }

    function getMedicineByLimitBySearch($limit, $start, $search, $order, $dir)
    {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
            ->from('medicine')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $search)
                ->or_like('category', $search)
                ->or_like('name', $search)
                ->or_like('e_date', $search)
                ->or_like('generic', $search)
                ->or_like('company', $search)
                ->or_like('effects', $search)
            ->group_end()
            ->get();
        return $query->result();
    }

    function getMedicineNameByAvailablity($searchTerm)
    {
        if (!empty($searchTerm)) {
            $fetched_records = $this->db->select('*')
            ->from('medicine')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $searchTerm)
                ->or_like('name', $searchTerm)
            ->group_end()
            ->get();
        
        $query = $fetched_records->result();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('medicine');
            $query = $fetched_records->result();
        }

        return $query;
    }

    function getMedicineInfo($searchTerm)
    {
        if (!empty($searchTerm)) {
            $query = $this->db->select('*')
            ->from('medicine')
            ->where('hospital_id', $this->session->userdata('hospital_id'))
            ->group_start()
                ->like('id', $searchTerm)
                ->or_like('name', $searchTerm)
            ->group_end()
            ->get();
        
        $users = $query->result_array();

            // $this->db->select('*');
            // $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            // $this->db->where("id LIKE '%" . $searchTerm . "%' OR name LIKE '%" . $searchTerm . "%'");
            // $fetched_records = $this->db->get('medicine');
            // $users = $fetched_records->result_array();
        } else {
            // $this->db->select('*');
            // $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            // $this->db->limit(10);
            // $fetched_records = $this->db->get('medicine');
            // $users = $fetched_records->result_array();

            $query = $this->db->select('*')
                ->from('medicine')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->limit(10)
                ->get();
            $users = $query->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'] . '*' . $user['name'], "text" => $user['name']);
        }
        return $data;
    }

    function getMedicineInfoForPharmacySale($searchTerm)
    {
        $hospital_id = $this->session->userdata('hospital_id');
        
        // Debug: Log the parameters
        log_message('debug', 'getMedicineInfoForPharmacySale called with searchTerm: ' . $searchTerm . ', hospital_id: ' . $hospital_id);
        
        if (!empty($searchTerm)) {
            $query = $this->db->select('*')
            ->from('medicine')
            ->where('hospital_id', $hospital_id)
            ->where('quantity >', '0')
            ->group_start()
                ->like('id', $searchTerm)
                ->or_like('name', $searchTerm)
            ->group_end()
            ->get();
        
        $users = $query->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $hospital_id);
            $this->db->where('quantity >', '0');
            $this->db->limit(10);
            $fetched_records = $this->db->get('medicine');
            $users = $fetched_records->result_array();
        }
        
        // Debug: Log query results
        log_message('debug', 'Found ' . count($users) . ' medicines for hospital_id: ' . $hospital_id);
        
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            // Debug: Log each medicine record
            log_message('debug', 'Medicine record: ' . json_encode($user));
            
            // Ensure all required fields exist with defaults
            $medicine_id = isset($user['id']) ? $user['id'] : '';
            $price = isset($user['s_price']) ? (float) $user['s_price'] : 0;
            $name = isset($user['name']) ? $user['name'] : 'Unknown Medicine';
            $company = isset($user['company']) ? $user['company'] : 'Unknown Company';
            $quantity = isset($user['quantity']) ? $user['quantity'] : 0;
            
            // Debug: Log processed values
            log_message('debug', 'Processed - ID: ' . $medicine_id . ', Name: ' . $name . ', Price: ' . $price . ', Company: ' . $company . ', Quantity: ' . $quantity);
            
            $data[] = array(
                "id" => $medicine_id . '*' . $price . '*' . $name . '*' . $company . '*' . $quantity, 
                "text" => $name . ' (Stock: ' . $quantity . ')'
            );
        }
        
        // Debug: Log final data
        log_message('debug', 'Returning ' . count($data) . ' medicine options');
        
        return $data;
    }
    function getGenericInfoByAll($searchTerm)
    {

        if (!empty($searchTerm)) {
            $this->db->select('*');

            $this->db->group_start()
                ->like('id', $searchTerm)
                ->or_like('generic', $searchTerm)
            ->group_end();
            $fetched_records = $this->db->get('medicine');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->limit(10);
            $fetched_records = $this->db->get('medicine');
            $users = $fetched_records->result_array();
        }

        $user_gen = array();
        foreach ($users as $user) {
            $user_gen[] = $user['generic'];
        }
        $result = array_unique($user_gen);

        $data = array();
        $i = 0;
        foreach ($result as $user) {
            //  echo $user[$i];
            $data[] = array("id" => $user, "text" => $user);
        }

        return $data;
    }
    function getMedicineByGeneric($id)
    {
        return  $this->db->where('hospital_id', $this->session->userdata('hospital_id'))->where('generic', $id)
            ->get('medicine')
            ->result();
    }

    // ========== SUPPLIER FUNCTIONS ==========
    
    function insertSupplier($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('medicine_suppliers', $data2);
    }

    function getSuppliers()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('medicine_suppliers');
        return $query->result();
    }

    function getActiveSuppliers()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'active');
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('medicine_suppliers');
        return $query->result();
    }

    function getSupplierById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('medicine_suppliers');
        return $query->row();
    }

    function updateSupplier($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('medicine_suppliers', $data);
    }

    function deleteSupplier($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('medicine_suppliers');
    }

    // ========== PURCHASE FUNCTIONS ==========
    
    function insertPurchase($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('medicine_purchases', $data2);
        return $this->db->insert_id();
    }

    function getPurchases()
    {
        $this->db->select('mp.*, ms.name as supplier_name');
        $this->db->from('medicine_purchases mp');
        $this->db->join('medicine_suppliers ms', 'mp.supplier_id = ms.id', 'left');
        $this->db->where('mp.hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('mp.purchase_date', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    function getPurchaseById($id)
    {
        $this->db->select('mp.*, ms.name as supplier_name, ms.company_name, ms.contact_person, ms.phone, ms.email');
        $this->db->from('medicine_purchases mp');
        $this->db->join('medicine_suppliers ms', 'mp.supplier_id = ms.id', 'left');
        $this->db->where('mp.hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('mp.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function updatePurchase($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('medicine_purchases', $data);
    }

    function deletePurchase($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('medicine_purchases');
    }

    function deletePurchaseItems($purchase_id)
    {
        $this->db->where('purchase_id', $purchase_id);
        $this->db->delete('medicine_purchase_items');
    }

    // ========== PURCHASE ITEM FUNCTIONS ==========
    
    function insertPurchaseItem($data)
    {
        $this->db->insert('medicine_purchase_items', $data);
        return $this->db->insert_id();
    }

    function getPurchaseItems($purchase_id)
    {
        $this->db->select('mpi.*, m.name as medicine_name, m.generic, m.category');
        $this->db->from('medicine_purchase_items mpi');
        $this->db->join('medicine m', 'mpi.medicine_id = m.id', 'left');
        $this->db->where('mpi.purchase_id', $purchase_id);
        $this->db->order_by('mpi.id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    function getPurchaseItemById($id)
    {
        $this->db->select('mpi.*, m.name as medicine_name, mp.supplier_id');
        $this->db->from('medicine_purchase_items mpi');
        $this->db->join('medicine m', 'mpi.medicine_id = m.id', 'left');
        $this->db->join('medicine_purchases mp', 'mpi.purchase_id = mp.id', 'left');
        $this->db->where('mpi.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function updatePurchaseItem($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('medicine_purchase_items', $data);
    }

    // ========== BATCH FUNCTIONS ==========
    
    function insertBatch($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('medicine_batches', $data2);
        return $this->db->insert_id();
    }

    function getBatches()
    {
        $this->db->select('mb.*, m.name as medicine_name, m.generic, m.category, ms.name as supplier_name');
        $this->db->from('medicine_batches mb');
        $this->db->join('medicine m', 'mb.medicine_id = m.id', 'left');
        $this->db->join('medicine_suppliers ms', 'mb.supplier_id = ms.id', 'left');
        $this->db->where('mb.hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('mb.expiry_date', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    function getBatchesByMedicine($medicine_id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('medicine_id', $medicine_id);
        $this->db->where('current_stock >', 0);
        $this->db->order_by('expiry_date', 'asc');
        $query = $this->db->get('medicine_batches');
        return $query->result();
    }

    function getBatchById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('medicine_batches');
        return $query->row();
    }

    function updateBatch($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('medicine_batches', $data);
    }

    function deleteBatch($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('medicine_batches');
    }

    function getExpiringMedicines($days = 90)
    {
        $this->db->select('mb.*, m.name as medicine_name, m.generic, m.category, ms.name as supplier_name');
        $this->db->from('medicine_batches mb');
        $this->db->join('medicine m', 'mb.medicine_id = m.id', 'left');
        $this->db->join('medicine_suppliers ms', 'mb.supplier_id = ms.id', 'left');
        $this->db->where('mb.hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('mb.current_stock >', 0);
        $this->db->where('DATEDIFF(mb.expiry_date, CURDATE()) <=', $days);
        $this->db->where('DATEDIFF(mb.expiry_date, CURDATE()) >=', 0);
        $this->db->order_by('mb.expiry_date', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    function getExpiredMedicines()
    {
        $this->db->select('mb.*, m.name as medicine_name, m.generic, m.category, ms.name as supplier_name');
        $this->db->from('medicine_batches mb');
        $this->db->join('medicine m', 'mb.medicine_id = m.id', 'left');
        $this->db->join('medicine_suppliers ms', 'mb.supplier_id = ms.id', 'left');
        $this->db->where('mb.hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('mb.current_stock >', 0);
        $this->db->where('mb.expiry_date <', date('Y-m-d'));
        $this->db->order_by('mb.expiry_date', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    // ========== STOCK MANAGEMENT FUNCTIONS ==========
    
    function updateMedicineTotalStock($medicine_id)
    {
        // Use the new dynamic calculation method
        $current_quantity = $this->calculateCurrentQuantity($medicine_id);
        
        $this->db->where('id', $medicine_id);
        $this->db->update('medicine', array('quantity' => $current_quantity));
        
        return $current_quantity;
    }

    function reduceBatchStock($batch_id, $quantity)
    {
        $batch = $this->getBatchById($batch_id);
        if ($batch && $batch->current_stock >= $quantity) {
            $new_stock = $batch->current_stock - $quantity;
            $this->updateBatch($batch_id, array(
                'current_stock' => $new_stock,
                'quantity_sold' => $batch->quantity_sold + $quantity
            ));
            
            // Update medicine total stock
            $this->updateMedicineTotalStock($batch->medicine_id);
            
            // Log stock movement
            $this->logStockMovement($batch->medicine_id, $batch_id, 'sale', $quantity);
            
            return true;
        }
        return false;
    }

    function logStockMovement($medicine_id, $batch_id, $movement_type, $quantity, $reference_type = null, $reference_id = null, $notes = null)
    {
        $data = array(
            'medicine_id' => $medicine_id,
            'batch_id' => $batch_id,
            'movement_type' => $movement_type,
            'quantity' => $quantity,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'notes' => $notes,
            'performed_by' => $this->session->userdata('user_id'),
            'hospital_id' => $this->session->userdata('hospital_id')
        );
        
        $this->db->insert('medicine_stock_movements', $data);
    }

    function getStockMovements($medicine_id = null, $batch_id = null)
    {
        $this->db->select('msm.*, m.name as medicine_name, mb.batch_number');
        $this->db->from('medicine_stock_movements msm');
        $this->db->join('medicine m', 'msm.medicine_id = m.id', 'left');
        $this->db->join('medicine_batches mb', 'msm.batch_id = mb.id', 'left');
        $this->db->where('msm.hospital_id', $this->session->userdata('hospital_id'));
        
        if ($medicine_id) {
            $this->db->where('msm.medicine_id', $medicine_id);
        }
        
        if ($batch_id) {
            $this->db->where('msm.batch_id', $batch_id);
        }
        
        $this->db->order_by('msm.movement_date', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    // ========== ENHANCED MEDICINE FUNCTIONS ==========
    
    function getMedicineWithBatches($medicine_id)
    {
        $medicine = $this->getMedicineById($medicine_id);
        if ($medicine) {
            $medicine->batches = $this->getBatchesByMedicine($medicine_id);
        }
        return $medicine;
    }

    function getMedicineByStockAlertWithBatches()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('quantity <=', 10); // Low stock threshold
        $this->db->order_by('quantity', 'asc');
        $query = $this->db->get('medicine');
        $medicines = $query->result();
        
        foreach ($medicines as $medicine) {
            $medicine->batches = $this->getBatchesByMedicine($medicine->id);
        }
        
        return $medicines;
    }

    function getMedicineForSaleWithBatch($medicine_id, $required_quantity)
    {
        // Get available batches ordered by expiry date (FIFO)
        $this->db->where('medicine_id', $medicine_id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('current_stock >', 0);
        $this->db->where('expiry_date >', date('Y-m-d'));
        $this->db->order_by('expiry_date', 'asc');
        $query = $this->db->get('medicine_batches');
        
        return $query->result();
    }

    // ========== PAYMENT FUNCTIONS ==========
    
    function insertPurchasePayment($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('medicine_purchase_payments', $data2);
        return $this->db->insert_id();
    }

    function getPurchasePayments($purchase_id)
    {
        $this->db->where('purchase_id', $purchase_id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('payment_date', 'desc');
        $query = $this->db->get('medicine_purchase_payments');
        return $query->result();
    }

    function getTotalPaymentsByPurchase($purchase_id)
    {
        $this->db->select('SUM(amount) as total_paid');
        $this->db->where('purchase_id', $purchase_id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'cleared');
        $query = $this->db->get('medicine_purchase_payments');
        $result = $query->row();
        
        return ($result && $result->total_paid) ? $result->total_paid : 0;
    }

    // ========== REPORTING FUNCTIONS ==========
    
    function getPurchaseReport($start_date = null, $end_date = null, $supplier_id = null)
    {
        $this->db->select('mp.*, ms.name as supplier_name');
        $this->db->from('medicine_purchases mp');
        $this->db->join('medicine_suppliers ms', 'mp.supplier_id = ms.id', 'left');
        $this->db->where('mp.hospital_id', $this->session->userdata('hospital_id'));
        
        if ($start_date) {
            $this->db->where('mp.purchase_date >=', $start_date);
        }
        
        if ($end_date) {
            $this->db->where('mp.purchase_date <=', $end_date);
        }
        
        if ($supplier_id) {
            $this->db->where('mp.supplier_id', $supplier_id);
        }
        
        $this->db->order_by('mp.purchase_date', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    function getStockReport()
    {
        $this->db->select('m.*, SUM(mb.current_stock) as total_batch_stock, COUNT(mb.id) as total_batches');
        $this->db->from('medicine m');
        $this->db->join('medicine_batches mb', 'm.id = mb.medicine_id AND mb.current_stock > 0', 'left');
        $this->db->where('m.hospital_id', $this->session->userdata('hospital_id'));
        $this->db->group_by('m.id');
        $this->db->order_by('m.name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    function getSupplierReport($supplier_id = null)
    {
        $this->db->select('ms.*, COUNT(mp.id) as total_purchases, SUM(mp.net_amount) as total_amount, SUM(mp.balance_amount) as total_balance');
        $this->db->from('medicine_suppliers ms');
        $this->db->join('medicine_purchases mp', 'ms.id = mp.supplier_id', 'left');
        $this->db->where('ms.hospital_id', $this->session->userdata('hospital_id'));
        
        if ($supplier_id) {
            $this->db->where('ms.id', $supplier_id);
        }
        
        $this->db->group_by('ms.id');
        $this->db->order_by('ms.name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
}
