<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ambulance extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('ambulance_model');
        $this->load->model('patient/patient_model');
        if (!$this->ion_auth->in_group(array('admin', 'Doctor', 'Nurse', 'Receptionist'))) {
            redirect('home/permission');
        }
    }

    // Ambulance Fleet Management
    public function index()
    {
        $data['ambulances'] = $this->ambulance_model->getAmbulance();
        $data['available_count'] = $this->ambulance_model->getAvailableAmbulanceCount();
        $data['on_call_count'] = $this->ambulance_model->getOnCallAmbulanceCount();
        $this->load->view('home/dashboard');
        $this->load->view('ambulance', $data);
        $this->load->view('home/footer');
    }

    public function addNewView()
    {
        $this->load->view('home/dashboard');
        $this->load->view('add_new');
        $this->load->view('home/footer');
    }

    public function addNew()
    {
        $id = $this->input->post('id');
        $vehicle_number = $this->input->post('vehicle_number');
        $driver_name = $this->input->post('driver_name');
        $driver_phone = $this->input->post('driver_phone');
        $ambulance_type = $this->input->post('ambulance_type');
        $status = $this->input->post('status');
        $equipment = $this->input->post('equipment');
        $capacity = $this->input->post('capacity');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('vehicle_number', 'Vehicle Number', 'required');
        $this->form_validation->set_rules('driver_name', 'Driver Name', 'required');
        $this->form_validation->set_rules('driver_phone', 'Driver Phone', 'required');
        $this->form_validation->set_rules('ambulance_type', 'Ambulance Type', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data = array();
            $data['setval'] = 'setval';
            $data['vehicle_number'] = $vehicle_number;
            $data['driver_name'] = $driver_name;
            $data['driver_phone'] = $driver_phone;
            $data['ambulance_type'] = $ambulance_type;
            $data['status'] = $status;
            $data['equipment'] = $equipment;
            $data['capacity'] = $capacity;
            $this->load->view('home/dashboard');
            $this->load->view('add_new', $data);
            $this->load->view('home/footer');
        } else {
            $data = array(
                'vehicle_number' => $vehicle_number,
                'driver_name' => $driver_name,
                'driver_phone' => $driver_phone,
                'ambulance_type' => $ambulance_type,
                'status' => $status,
                'equipment' => $equipment,
                'capacity' => $capacity,
            );

            if (empty($id)) {
                $this->ambulance_model->insertAmbulance($data);
                $this->session->set_flashdata('feedback', lang('added'));
            } else {
                $this->ambulance_model->updateAmbulance($id, $data);
                $this->session->set_flashdata('feedback', lang('updated'));
            }

            redirect('ambulance');
        }
    }

    function editAmbulance()
    {
        $data = array();
        $id = $this->input->get('id');
        $data['ambulance'] = $this->ambulance_model->getAmbulanceById($id);
        $this->load->view('home/dashboard');
        $this->load->view('edit_ambulance', $data);
        $this->load->view('home/footer');
    }

    function deleteAmbulance()
    {
        $id = $this->input->get('id');
        $this->ambulance_model->deleteAmbulance($id);
        $this->session->set_flashdata('feedback', lang('deleted'));
        redirect('ambulance');
    }

    // Ambulance Service Bookings
    public function bookings()
    {
        $data['bookings'] = $this->ambulance_model->getBookings();
        $data['pending_count'] = $this->ambulance_model->getBookingCountByStatus('Pending');
        $data['in_progress_count'] = $this->ambulance_model->getBookingCountByStatus('In Progress');
        $this->load->view('home/dashboard');
        $this->load->view('bookings', $data);
        $this->load->view('home/footer');
    }

    public function newBooking()
    {
        $data['ambulances'] = $this->ambulance_model->getAvailableAmbulances();
        $data['patients'] = $this->patient_model->getPatient();
        $this->load->view('home/dashboard');
        $this->load->view('new_booking', $data);
        $this->load->view('home/footer');
    }

    public function getPatientInfo()
    {
        $searchTerm = $this->input->get('term');
        $this->load->model('patient_model');
        
        // Check if hospital_id is set
        if (!$this->session->userdata('hospital_id')) {
            echo json_encode(array());
            return;
        }
        
        $data = $this->patient_model->getPatientInfo($searchTerm);
        echo json_encode($data);
    }

    public function viewBooking()
    {
        $id = $this->input->get('id');
        $data['booking'] = $this->ambulance_model->getBookingById($id);
        
        if (!$data['booking']) {
            $this->session->set_flashdata('feedback', 'Booking not found');
            redirect('ambulance/bookings');
        }
        
        $this->load->view('home/dashboard');
        $this->load->view('view_booking', $data);
        $this->load->view('home/footer');
    }

    public function editBooking()
    {
        $id = $this->input->get('id');
        $data['booking'] = $this->ambulance_model->getBookingById($id);
        
        if (!$data['booking']) {
            $this->session->set_flashdata('feedback', 'Booking not found');
            redirect('ambulance/bookings');
        }
        
        $data['ambulances'] = $this->ambulance_model->getAmbulance();
        $data['patients'] = $this->patient_model->getPatient();
        
        $this->load->view('home/dashboard');
        $this->load->view('edit_booking', $data);
        $this->load->view('home/footer');
    }

    public function updateBooking()
    {
        $id = $this->input->post('id');
        $patient_id = $this->input->post('patient_id');
        $pickup_address = $this->input->post('pickup_address');
        $destination_address = $this->input->post('destination_address');
        $ambulance_id = $this->input->post('ambulance_id');
        $booking_type = $this->input->post('booking_type');
        $priority = $this->input->post('priority');
        $pickup_time = $this->input->post('pickup_time');
        $notes = $this->input->post('notes');
        $status = $this->input->post('status');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('patient_id', 'Patient', 'required');
        $this->form_validation->set_rules('pickup_address', 'Pickup Address', 'required');
        $this->form_validation->set_rules('destination_address', 'Destination Address', 'required');
        $this->form_validation->set_rules('ambulance_id', 'Ambulance', 'required');
        $this->form_validation->set_rules('booking_type', 'Booking Type', 'required');
        $this->form_validation->set_rules('priority', 'Priority', 'required');
        $this->form_validation->set_rules('pickup_time', 'Pickup Time', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Get the original booking data for the view
            $booking = $this->ambulance_model->getBookingById($id);
            
            $data = array();
            $data['setval'] = 'setval';
            $data['booking'] = $booking; // Add booking object to data
            $data['id'] = $id;
            $data['patient_id'] = $patient_id;
            $data['pickup_address'] = $pickup_address;
            $data['destination_address'] = $destination_address;
            $data['ambulance_id'] = $ambulance_id;
            $data['booking_type'] = $booking_type;
            $data['priority'] = $priority;
            $data['pickup_time'] = $pickup_time;
            $data['notes'] = $notes;
            $data['status'] = $status;
            $data['ambulances'] = $this->ambulance_model->getAmbulance();
            $data['patients'] = $this->patient_model->getPatient();
            $this->load->view('home/dashboard');
            $this->load->view('edit_booking', $data);
            $this->load->view('home/footer');
        } else {
            // Get patient details from database
            $patient = $this->patient_model->getPatientById($patient_id);
            
            $data = array(
                'patient_id' => $patient_id,
                'patient_name' => $patient->name,
                'patient_phone' => $patient->phone,
                'patient_address' => $patient->address,
                'pickup_address' => $pickup_address,
                'destination_address' => $destination_address,
                'ambulance_id' => $ambulance_id,
                'booking_type' => $booking_type,
                'priority' => $priority,
                'pickup_time' => $pickup_time,
                'notes' => $notes,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $this->ambulance_model->updateBooking($id, $data);
            $this->session->set_flashdata('feedback', 'Booking updated successfully');
            redirect('ambulance/bookings');
        }
    }

    public function deleteBooking()
    {
        $id = $this->input->get('id');
        
        // Get booking details to update ambulance status
        $booking = $this->ambulance_model->getBookingById($id);
        
        if ($booking) {
            // Update ambulance status back to Available
            $this->ambulance_model->updateAmbulanceStatus($booking->ambulance_id, 'Available');
            
            // Delete the booking
            $this->ambulance_model->deleteBooking($id);
            $this->session->set_flashdata('feedback', 'Booking deleted successfully');
        } else {
            $this->session->set_flashdata('feedback', 'Booking not found');
        }
        
        redirect('ambulance/bookings');
    }

    public function addBooking()
    {
        $patient_id = $this->input->post('patient_id');
        $pickup_address = $this->input->post('pickup_address');
        $destination_address = $this->input->post('destination_address');
        $ambulance_id = $this->input->post('ambulance_id');
        $booking_type = $this->input->post('booking_type');
        $priority = $this->input->post('priority');
        $pickup_time = $this->input->post('pickup_time');
        $notes = $this->input->post('notes');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('patient_id', 'Patient', 'required');
        $this->form_validation->set_rules('pickup_address', 'Pickup Address', 'required');
        $this->form_validation->set_rules('destination_address', 'Destination Address', 'required');
        $this->form_validation->set_rules('ambulance_id', 'Ambulance', 'required');
        $this->form_validation->set_rules('booking_type', 'Booking Type', 'required');
        $this->form_validation->set_rules('priority', 'Priority', 'required');
        $this->form_validation->set_rules('pickup_time', 'Pickup Time', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data = array();
            $data['setval'] = 'setval';
            $data['patient_id'] = $patient_id;
            $data['pickup_address'] = $pickup_address;
            $data['destination_address'] = $destination_address;
            $data['ambulance_id'] = $ambulance_id;
            $data['booking_type'] = $booking_type;
            $data['priority'] = $priority;
            $data['pickup_time'] = $pickup_time;
            $data['notes'] = $notes;
            $data['ambulances'] = $this->ambulance_model->getAvailableAmbulances();
            $data['patients'] = $this->patient_model->getPatient();
            $this->load->view('home/dashboard');
            $this->load->view('new_booking', $data);
            $this->load->view('home/footer');
        } else {
            $booking_number = 'AMB' . date('Ymd') . rand(1000, 9999);
            
            // Get patient details from database
            $patient = $this->patient_model->getPatientById($patient_id);
            
            $data = array(
                'booking_number' => $booking_number,
                'patient_id' => $patient_id,
                'patient_name' => $patient->name,
                'patient_phone' => $patient->phone,
                'patient_address' => $patient->address,
                'pickup_address' => $pickup_address,
                'destination_address' => $destination_address,
                'ambulance_id' => $ambulance_id,
                'booking_type' => $booking_type,
                'priority' => $priority,
                'pickup_time' => $pickup_time,
                'notes' => $notes,
            );

            $booking_id = $this->ambulance_model->insertBooking($data);
            
            // Update ambulance status to 'On Call'
            $this->ambulance_model->updateAmbulanceStatus($ambulance_id, 'On Call');
            
            $this->session->set_flashdata('feedback', 'Booking created successfully. Booking Number: ' . $booking_number);
            redirect('ambulance/bookings');
        }
    }


    public function updateBookingStatus()
    {
        $id = $this->input->post('booking_id');
        $status = $this->input->post('status');
        $notes = $this->input->post('notes');
        
        $booking = $this->ambulance_model->getBookingById($id);
        
        $data = array(
            'status' => $status,
            'notes' => $notes
        );
        
        if ($status == 'In Progress') {
            $data['actual_pickup_time'] = date('Y-m-d H:i:s');
        } elseif ($status == 'Completed') {
            $data['actual_dropoff_time'] = date('Y-m-d H:i:s');
            // Update ambulance status back to Available
            $this->ambulance_model->updateAmbulanceStatus($booking->ambulance_id, 'Available');
        }
        
        $this->ambulance_model->updateBooking($id, $data);
        $this->session->set_flashdata('feedback', 'Booking status updated successfully');
        redirect('ambulance/viewBooking?id=' . $id);
    }

    // Payment Management
    public function payments()
    {
        $data['payments'] = $this->ambulance_model->getPayments();
        $data['bookings'] = $this->ambulance_model->getBookings();
        $this->load->view('home/dashboard');
        $this->load->view('payments', $data);
        $this->load->view('home/footer');
    }

    public function getBookingInfo()
    {
        $searchTerm = $this->input->get('term');
        
        // Check if hospital_id is set
        if (!$this->session->userdata('hospital_id')) {
            echo json_encode(array());
            return;
        }
        
        $this->db->select('id, booking_number, patient_name, total_fare');
        $this->db->from('ambulance_bookings');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        
        if (!empty($searchTerm)) {
            $s = $this->db->escape_like_str($searchTerm);
            $this->db->where("(booking_number LIKE '%" . $s . "%' OR patient_name LIKE '%" . $s . "%')", NULL, FALSE);
        }
        
        $this->db->order_by('id', 'desc');
        $this->db->limit(20);
        $query = $this->db->get();
        $bookings = $query->result();
        
        $data = array();
        foreach ($bookings as $booking) {
            $data[] = array(
                "id" => $booking->id, 
                "text" => $booking->booking_number . ' - ' . $booking->patient_name . ' ($' . number_format($booking->total_fare, 2) . ')'
            );
        }
        
        echo json_encode($data);
    }

    public function getBookingById()
    {
        $id = $this->input->get('id');
        
        if (empty($id)) {
            echo json_encode(array('success' => false, 'message' => 'Booking ID is required'));
            return;
        }
        
        $booking = $this->ambulance_model->getBookingById($id);
        
        if ($booking) {
            $data = array(
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'patient_name' => $booking->patient_name,
                'total_fare' => $booking->total_fare,
                'pickup_address' => $booking->pickup_address,
                'destination_address' => $booking->destination_address,
                'ambulance_id' => $booking->ambulance_id,
                'vehicle_number' => $booking->vehicle_number,
                'driver_name' => $booking->driver_name
            );
            
            echo json_encode(array('success' => true, 'data' => $data));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Booking not found'));
        }
    }

    public function addPayment()
    {
        $booking_id = $this->input->post('booking_id');
        $amount = $this->input->post('amount');
        $payment_method = $this->input->post('payment_method');
        $notes = $this->input->post('notes');
        
        $payment_number = 'PAY' . date('Ymd') . rand(1000, 9999);
        
        $data = array(
            'booking_id' => $booking_id,
            'payment_number' => $payment_number,
            'amount' => $amount,
            'payment_method' => $payment_method,
            'payment_status' => 'Completed',
            'payment_date' => date('Y-m-d H:i:s'),
            'notes' => $notes
        );
        
        $this->ambulance_model->insertPayment($data);
        
        // Update booking payment status
        $this->ambulance_model->updateBookingPaymentStatus($booking_id, 'Paid');
        
        $this->session->set_flashdata('feedback', 'Payment recorded successfully');
        redirect('ambulance/payments');
    }

    public function getPaymentById()
    {
        $id = $this->input->get('id');
        
        if (empty($id)) {
            echo json_encode(array('success' => false, 'message' => 'Payment ID is required'));
            return;
        }
        
        $payment = $this->ambulance_model->getPaymentById($id);
        
        if ($payment) {
            $data = array(
                'id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'booking_id' => $payment->booking_id,
                'booking_number' => $payment->booking_number,
                'patient_name' => $payment->patient_name,
                'total_fare' => $payment->total_fare,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'payment_date' => $payment->payment_date,
                'notes' => $payment->notes
            );
            
            echo json_encode(array('success' => true, 'data' => $data));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Payment not found'));
        }
    }

    public function updatePayment()
    {
        $payment_id = $this->input->post('payment_id');
        $booking_id = $this->input->post('booking_id');
        $amount = $this->input->post('amount');
        $payment_method = $this->input->post('payment_method');
        $payment_status = $this->input->post('payment_status');
        $notes = $this->input->post('notes');
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('payment_id', 'Payment ID', 'required');
        $this->form_validation->set_rules('booking_id', 'Booking', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');
        $this->form_validation->set_rules('payment_status', 'Payment Status', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('feedback', 'Please fill in all required fields');
            redirect('ambulance/payments');
        } else {
            $data = array(
                'booking_id' => $booking_id,
                'amount' => $amount,
                'payment_method' => $payment_method,
                'payment_status' => $payment_status,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            $this->ambulance_model->updatePayment($payment_id, $data);
            $this->session->set_flashdata('feedback', 'Payment updated successfully');
            redirect('ambulance/payments');
        }
    }

    public function deletePayment()
    {
        $id = $this->input->post('id');
        
        if (empty($id)) {
            echo json_encode(array('success' => false, 'message' => 'Payment ID is required'));
            return;
        }
        
        $result = $this->ambulance_model->deletePayment($id);
        
        if ($result) {
            echo json_encode(array('success' => true, 'message' => 'Payment deleted successfully'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to delete payment'));
        }
    }

    // Reports
    public function reports()
    {
        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status');
        
        
        $data['total_bookings'] = $this->ambulance_model->getTotalBookings();
        $data['total_revenue'] = $this->ambulance_model->getTotalRevenue();
        $data['monthly_bookings'] = $this->ambulance_model->getMonthlyBookings();
        $data['monthly_data'] = $this->ambulance_model->getMonthlyBookingsData();
        
        // Get filtered data - try model methods first, fallback to direct methods
        if (method_exists($this->ambulance_model, 'getBookingsByType')) {
            $data['bookings_by_type'] = $this->ambulance_model->getBookingsByType($date_from, $date_to, $status);
        } else {
            $data['bookings_by_type'] = $this->getBookingsByTypeDirect($date_from, $date_to, $status);
        }
        
        if (method_exists($this->ambulance_model, 'getDetailedReports')) {
            $data['detailed_reports'] = $this->ambulance_model->getDetailedReports($date_from, $date_to, $status);
        } else {
            $data['detailed_reports'] = $this->getDetailedReportsDirect($date_from, $date_to, $status);
        }
        
        if (method_exists($this->ambulance_model, 'getAverageResponseTime')) {
            $data['avg_response_time'] = $this->ambulance_model->getAverageResponseTime($date_from, $date_to, $status);
        } else {
            $data['avg_response_time'] = $this->getAverageResponseTimeDirect($date_from, $date_to, $status);
        }
        
        
        // Pass filter values back to view
        $data['filter_date_from'] = $date_from;
        $data['filter_date_to'] = $date_to;
        $data['filter_status'] = $status;
        
        $this->load->view('home/dashboard');
        $this->load->view('reports', $data);
        $this->load->view('home/footer');
    }

    // Direct query methods as fallback
    private function getBookingsByTypeDirect($date_from = null, $date_to = null, $status = null)
    {
        $this->db->select('booking_type, COUNT(*) as count');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        
        // Apply date filters
        if (!empty($date_from)) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }
        
        // Apply status filter
        if (!empty($status)) {
            $this->db->where('status', $status);
        }
        
        $this->db->group_by('booking_type');
        $query = $this->db->get('ambulance_bookings');
        
        
        return $query->result();
    }

    private function getDetailedReportsDirect($date_from = null, $date_to = null, $status = null)
    {
        $this->db->select('ab.*, a.vehicle_number, a.driver_name, p.name as patient_name');
        $this->db->from('ambulance_bookings ab');
        $this->db->join('ambulance a', 'a.id = ab.ambulance_id', 'left');
        $this->db->join('patient p', 'p.id = ab.patient_id', 'left');
        $this->db->where('ab.hospital_id', $this->session->userdata('hospital_id'));
        
        // Apply date filters
        if (!empty($date_from)) {
            $this->db->where('DATE(ab.created_at) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(ab.created_at) <=', $date_to);
        }
        
        // Apply status filter
        if (!empty($status)) {
            $this->db->where('ab.status', $status);
        }
        
        $this->db->order_by('ab.created_at', 'desc');
        $query = $this->db->get();
        
        
        return $query->result();
    }

    private function getAverageResponseTimeDirect($date_from = null, $date_to = null, $status = null)
    {
        $this->db->select('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_time');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('status', 'Completed');
        
        // Apply date filters
        if (!empty($date_from)) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }
        
        // Apply status filter (override default Completed status if specified)
        if (!empty($status)) {
            $this->db->where('status', $status);
        }
        
        $query = $this->db->get('ambulance_bookings');
        $result = $query->row();
        return $result->avg_time ? round($result->avg_time) : 0;
    }

    // Rates Management
    public function rates()
    {
        // Initialize rates if they don't exist for this hospital
        $this->ambulance_model->initializeRatesIfNeeded();
        
        $data['rates'] = $this->ambulance_model->getRates();
        $data['currency'] = $this->currency; // Pass system currency to view
        $this->load->view('home/dashboard');
        $this->load->view('rates', $data);
        $this->load->view('home/footer');
    }

    public function updateRates()
    {
        $rates = $this->input->post('rates');
        
        // Debug: Log received data
        log_message('debug', 'Rates form data received: ' . print_r($rates, true));
        log_message('debug', 'All POST data: ' . print_r($this->input->post(), true));
        
        if (empty($rates)) {
            $this->session->set_flashdata('feedback_error', 'No rate data received');
            redirect('ambulance/rates');
            return;
        }
        
        // Map form types to database ambulance_type values
        $type_mapping = array(
            'Emergency' => 'Basic',  // Map Emergency to Basic in database
            'Transfer' => 'Advanced', // Map Transfer to Advanced in database  
            'Discharge' => 'Critical Care' // Map Discharge to Critical Care in database
        );
        
        $updated_count = 0;
        foreach ($rates as $form_type => $rate_data) {
            if (isset($type_mapping[$form_type])) {
                $db_type = $type_mapping[$form_type];
                
                // Map form field names to database column names
                $db_data = array(
                    'base_fare' => $rate_data['base_rate'],
                    'per_km_rate' => $rate_data['per_km'],
                    'waiting_rate_per_hour' => $rate_data['waiting_rate'],
                    'night_charge_percentage' => isset($rate_data['night_charge']) ? $rate_data['night_charge'] : 0,
                    'weekend_charge_percentage' => isset($rate_data['weekend_charge']) ? $rate_data['weekend_charge'] : 0,
                    'holiday_charge_percentage' => isset($rate_data['holiday_charge']) ? $rate_data['holiday_charge'] : 0,
                    'minimum_charge' => $rate_data['minimum_charge']
                );
                
                log_message('debug', 'Updating ' . $db_type . ' with data: ' . print_r($db_data, true));
                
                $result = $this->ambulance_model->updateRate($db_type, $db_data);
                if ($result) {
                    $updated_count++;
                }
            }
        }
        
        if ($updated_count > 0) {
            $this->session->set_flashdata('feedback', "Rates updated successfully ($updated_count rates)");
        } else {
            $this->session->set_flashdata('feedback_error', 'No rates were updated');
        }
        redirect('ambulance/rates');
    }

    // Debug method to check database table
    public function debugRates()
    {
        $hospital_id = $this->session->userdata('hospital_id');
        
        // Check if table exists
        $tables = $this->db->list_tables();
        echo "<h3>Available tables:</h3>";
        echo "<pre>" . print_r($tables, true) . "</pre>";
        
        // Check table structure
        if (in_array('ambulance_rates', $tables)) {
            echo "<h3>ambulance_rates table structure:</h3>";
            $fields = $this->db->list_fields('ambulance_rates');
            echo "<pre>" . print_r($fields, true) . "</pre>";
            
            // Check existing data
            echo "<h3>Existing rates data for hospital_id $hospital_id:</h3>";
            $this->db->where('hospital_id', $hospital_id);
            $rates = $this->db->get('ambulance_rates')->result();
            echo "<pre>" . print_r($rates, true) . "</pre>";
        } else {
            echo "<h3>ambulance_rates table does not exist!</h3>";
        }
        
        // Check session data
        echo "<h3>Session data:</h3>";
        echo "<pre>" . print_r($this->session->userdata(), true) . "</pre>";
    }

    // AJAX Functions
    public function getAvailableAmbulances()
    {
        $ambulance_type = $this->input->get('type');
        $ambulances = $this->ambulance_model->getAvailableAmbulancesByType($ambulance_type);
        echo json_encode($ambulances);
    }

    public function calculateFare()
    {
        $ambulance_type = $this->input->get('type');
        $distance = $this->input->get('distance');
        $waiting_hours = $this->input->get('waiting_hours', 0);
        
        $fare = $this->ambulance_model->calculateFare($ambulance_type, $distance, $waiting_hours);
        echo json_encode(array('fare' => $fare));
    }

    public function exportReports()
    {
        $format = $this->input->get('format');
        
        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status');
        
        // Get data
        $data['bookings'] = $this->ambulance_model->getDetailedReports($date_from, $date_to, $status);
        $data['summary'] = array(
            'total_bookings' => $this->ambulance_model->getTotalBookings($date_from, $date_to, $status),
            'bookings_by_type' => $this->ambulance_model->getBookingsByType($date_from, $date_to, $status),
            'average_response_time' => $this->ambulance_model->getAverageResponseTime($date_from, $date_to, $status)
        );
        
        $filename = 'ambulance_reports_' . date('Y-m-d_H-i-s');
        
        switch ($format) {
            case 'pdf':
                $this->exportPDF($data, $filename);
                break;
                
            case 'excel':
                $this->exportExcel($data, $filename);
                break;
                
            case 'csv':
                $this->exportCSV($data, $filename);
                break;
                
            default:
                redirect('ambulance/reports');
        }
    }
    
    private function exportPDF($data, $filename)
    {
        // Create a simple text-based report and redirect to print
        $this->load->view('ambulance/export_pdf', $data);
    }
    
    private function exportExcel($data, $filename)
    {
        // Generate CSV format that Excel can open properly
        $output = fopen('php://output', 'w');
        
        // Set headers for Excel
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        // Add BOM for UTF-8 to ensure proper encoding in Excel
        echo "\xEF\xBB\xBF";
        
        // Write headers
        fputcsv($output, array(
            'ID',
            'Booking Number', 
            'Patient Name',
            'Ambulance Type',
            'Status',
            'Created Date',
            'Amount'
        ));
        
        // Write data rows
        foreach ($data['bookings'] as $booking) {
            fputcsv($output, array(
                $booking->id,
                $booking->booking_number,
                $booking->patient_name,
                $booking->ambulance_type,
                $booking->status,
                $booking->created_at,
                $booking->total_amount
            ));
        }
        
        fclose($output);
    }
    
    private function exportCSV($data, $filename)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Booking Number', 'Patient Name', 'Ambulance Type', 'Status', 'Created Date', 'Amount'));
        
        foreach ($data['bookings'] as $booking) {
            fputcsv($output, array(
                $booking->id,
                $booking->booking_number,
                $booking->patient_name,
                $booking->ambulance_type,
                $booking->status,
                $booking->created_at,
                $booking->total_amount
            ));
        }
        fclose($output);
    }

    // Dashboard
    public function dashboard()
    {
        $data['total_ambulances'] = $this->ambulance_model->getAmbulanceCount();
        $data['available_ambulances'] = $this->ambulance_model->getAvailableAmbulanceCount();
        $data['total_bookings'] = $this->ambulance_model->getTotalBookings();
        $data['pending_bookings'] = $this->ambulance_model->getBookingCountByStatus('Pending');
        $data['in_progress_bookings'] = $this->ambulance_model->getBookingCountByStatus('In Progress');
        $data['today_revenue'] = $this->ambulance_model->getTodayRevenue();
        $data['recent_bookings'] = $this->ambulance_model->getRecentBookings(5);
        
        $this->load->view('home/dashboard');
        $this->load->view('ambulance_dashboard', $data);
        $this->load->view('home/footer');
    }
}
