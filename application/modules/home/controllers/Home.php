<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends MX_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('finance/finance_model');
        $this->load->model('appointment/appointment_model');
        $this->load->model('notice/notice_model');
        $this->load->model('home_model');
        $this->load->model('patient/patient_model');
        $this->load->model('settings/settings_model');
    }

    public function index()
    {
        $data = array();

        if (!$this->ion_auth->in_group(array('superadmin'))) {

            $date = date('d-m-Y');
            $clock_in = date('h:i A');
            $clock_out = "";
            $late = "";
            $halfday = "";
            $work_from = "";
            $details = array();
            $month = date('F', strtotime($date));
            $year = date('Y', strtotime($date));
            $day = explode('-', $date);

            $result = $this->db->get_where('attendance', array('staff' => $this->ion_auth->user()->row()->id, 'month' => $month, 'year' => $year))->row();
            if (!empty($result->details)) {
                $details = explode('#', $result->details);

                $detail = explode('_', $details[$day[0] - 1] ?? '');
            }

            $finalDetail = ($clock_in != '' ? $clock_in : 'NONE') . '_' . ($clock_out != '' ? $clock_out : 'NONE') . '_' . ($late == 'late' ? $late : 'NONE') . '_' . ($halfday == 'halfday' ? $halfday : 'NONE') . '_' . ($work_from == '' ? 'office' : $work_from);

            $details[$day[0] - 1] = $finalDetail;

            $detail = implode('#', $details);
            if (!empty($result->log)) {
                $logs = explode("_", $result->log);
                $checkAdded = "";

                if ($clock_in != '') {
                    $checkAdded = $logs[$day[0] - 1] ?? '';
                    $logs[$day[0] - 1] = 'yes';
                }

                $log = implode('_', $logs);
            } else {
                $log = '';
                $checkAdded = '';
            }


            $data = array(
                'log' => $log,
                'details' => $detail
            );
            if (!empty($result->id)) {
                if ($checkAdded != 'yes') {
                    $this->db->where('id', $result->id);
                    $this->db->update('attendance', $data);
                }
            }

            $data['sum'] = $this->home_model->getSum('gross_total', 'payment');
            $data['payments'] = $this->finance_model->getPayment();
            $data['notices'] = $this->notice_model->getNotice();
            $data['this_month'] = $this->finance_model->getThisMonth();
            $data['expenses'] = $this->finance_model->getExpense();

            if ($this->ion_auth->in_group(array('Doctor'))) {
                redirect('doctor/details');
            } else {
                $data['appointments'] = $this->appointment_model->getAppointment();
            }

            if ($this->ion_auth->in_group(array('Accountant', 'Receptionist'))) {
                redirect('finance/addPaymentView');
            }

            if ($this->ion_auth->in_group(array('Pharmacist'))) {
                redirect('finance/pharmacy/home');
            }

            if ($this->ion_auth->in_group(array('Patient'))) {
                redirect('patient/medicalHistory');
            }
            if ($this->ion_auth->in_group(array('Laboratorist'))) {
                redirect('lab');
            }


            if (!$this->ion_auth->in_group(array('Patient', 'Pharmacist', 'Accountant', 'Receptionist', 'Doctor'))) {
                $data['this_month']['payment'] = $this->finance_model->thisMonthPayment();
                $data['this_month']['expense'] = $this->finance_model->thisMonthExpense();
                $data['this_month']['appointment'] = $this->finance_model->thisMonthAppointment();

                $data['this_day']['payment'] = $this->finance_model->thisDayPayment();
                $data['this_day']['expense'] = $this->finance_model->thisDayExpense();
                $data['this_day']['appointment'] = $this->finance_model->thisDayAppointment();

                $data['this_year']['payment'] = $this->finance_model->thisYearPayment();
                $data['this_year']['expense'] = $this->finance_model->thisYearExpense();
                $data['this_year']['appointment'] = $this->finance_model->thisYearAppointment();

                $data['this_month']['appointment'] = $this->finance_model->thisMonthAppointment();
                $data['this_month']['appointment_treated'] = $this->finance_model->thisMonthAppointmentTreated();
                $data['this_month']['appointment_cancelled'] = $this->finance_model->thisMonthAppointmentCancelled();

                $data['this_year']['payment_per_month'] = $this->finance_model->getPaymentPerMonthThisYear();

                $data['this_year']['expense_per_month'] = $this->finance_model->getExpensePerMonthThisYear();

                $data['this_day']['deposit'] = $this->home_model->getTotalDepositToday();
                $data['this_month']['deposit'] = $this->home_model->getTotalDepositThisMonth();
                $data['this_year']['deposit'] = $this->home_model->getTotalDepositThisYear();

                $data['this_day']['due'] = ($data['this_day']['payment'] ?? 0) - ($data['this_day']['deposit'] ?? 0);
                $data['this_month']['due'] = ($data['this_month']['payment'] ?? 0) - ($data['this_month']['deposit'] ?? 0);
                $data['this_year']['due'] = ($data['this_year']['payment'] ?? 0) - ($data['this_year']['deposit'] ?? 0);

                $last_month_due = $this->home_model->getTotalDueLastMonth();
                $data['last_month_due'] = $last_month_due;



                if ($last_month_due > 0) {
                    $change_due = (($data['this_month']['due'] ?? 0) - $last_month_due);
                    $percentage_change_due = ($change_due / $last_month_due) * 100;
                } elseif (($data['this_month']['due'] ?? 0) > 0) {
                    $percentage_change_due = 100;
                } else {
                    $percentage_change_due = 0;
                }



                $data['percentage_change_bill'] = $this->home_model->billPercentageChangedFromLastMonth();
                $data['percentage_change_deposit'] = $this->home_model->depositPercentageChangedFromLastMonth();
                $data['percentage_change_due'] = number_format($percentage_change_due, 2, '.', ',');
                $data['percentage_change_expense'] = $this->home_model->expensePercentageChangedFromLastMonth();




                // Now stats starts
                $doctorsOnDuty = $this->home_model->countDoctorsOnDuty();
                $nursesOnDuty = $this->home_model->countNursesOnDuty();
                $pharmacistsOnDuty = $this->home_model->countPharmacistsOnDuty();
                $laboratoristsOnDuty = $this->home_model->countLaboratoristsOnDuty();
                $receptionistsOnDuty = $this->home_model->countReceptionistsOnDuty();
                $accountantsOnDuty = $this->home_model->countAccountantsOnDuty();
                $patientAdmitted = $this->home_model->countPatientsAdmitted();

                $data['doctorsOnDuty'] = $doctorsOnDuty;
                $data['nursesOnDuty'] = $nursesOnDuty;
                $data['pharmacistsOnDuty'] = $pharmacistsOnDuty;
                $data['laboratoristsOnDuty'] = $laboratoristsOnDuty;
                $data['receptionistsOnDuty'] = $receptionistsOnDuty;
                $data['accountantsOnDuty'] = $accountantsOnDuty;
                $data['patientAdmitted'] = $patientAdmitted;

                // Now stats ends



                // today stats starts

                $registeredToday = $this->home_model->countPatientsRegisteredToday();
                $admittedToday = $this->home_model->countPatientsAdmittedToday();
                $dischargedToday = $this->home_model->countPatientsDischargedToday();

                $data['registeredToday'] = $registeredToday;
                $data['admittedToday'] = $admittedToday;
                $data['dischargedToday'] = $dischargedToday;

                // today stats ends

                // this month stats starts

                $registeredThisMonth = $this->home_model->countPatientsRegisteredThisMonth();
                $admittedThisMonth = $this->home_model->countPatientsAdmittedThisMonth();
                $dischargedThisMonth = $this->home_model->countPatientsDischargedThisMonth();

                $data['registeredThisMonth'] = $registeredThisMonth;
                $data['admittedThisMonth'] = $admittedThisMonth;
                $data['dischargedThisMonth'] = $dischargedThisMonth;

                // this month stats ends


                // this year stats starts
                $registeredThisYear = $this->home_model->countPatientsRegisteredThisYear();
                $admittedThisYear = $this->home_model->countPatientsAdmittedThisYear();
                $dischargedThisYear = $this->home_model->countPatientsDischargedThisYear();
                $data['registeredThisYear'] = $registeredThisYear;
                $data['admittedThisYear'] = $admittedThisYear;
                $data['dischargedThisYear'] = $dischargedThisYear;
                // this year stats ends




                // bed category wise occupancy number. 
                // Table names = bed, bed_category and alloted_bed
                // final loop should be on categories becaue we need to show the total bed number and ocuupency and availibilty of each category

                $bedCategories = $this->home_model->getBedCategories();
                $beds = $this->home_model->getBeds();
                $allotedBeds = $this->home_model->getAllotedBeds();


                $bedCategoryWiseOccupancy = array();
                foreach ($bedCategories as $bedCategory) {
                    $bedCategoryWiseOccupancy[$bedCategory->category] = array(
                        'total' => 0,
                        'occupied' => 0,
                        'available' => 0
                    );
                }

                foreach ($beds as $bed) {
                    $bedCategoryWiseOccupancy[$bed->category]['total']++;
                }

                foreach ($allotedBeds as $allotedBed) {
                    $bedCategoryWiseOccupancy[$allotedBed->category]['occupied']++;
                }

                foreach ($bedCategoryWiseOccupancy as $key => $value) {
                    $bedCategoryWiseOccupancy[$key]['available'] = $bedCategoryWiseOccupancy[$key]['total'] - $bedCategoryWiseOccupancy[$key]['occupied'];
                }

                $data['bedCategoryWiseOccupancy'] = $bedCategoryWiseOccupancy;


                // bed category wise occupancy number ends


                // Top Diagnoses and Treatments starts

                $diagnoses = $this->home_model->getDiagnosis();

                foreach ($diagnoses as $diagnosis) {
                    $diagnoses_number[$diagnosis->id] = $this->home_model->getCaseNumberByDiagnosisLastThirtyDays($diagnosis->id);
                }

                $treatments = $this->home_model->getTreatment();

                foreach ($treatments as $treatment) {
                    $treatments_number[$treatment->id] = $this->home_model->getCaseNumberByTreatmentLastThirtyDeays($treatment->id);
                }

                if (!empty($diagnoses_number)) {
                    arsort($diagnoses_number);
                    $diagnoses_number = array_slice($diagnoses_number, 0, 10, true);
                    $data['topDiagnoses'] = $diagnoses_number;
                }

                if (!empty($treatments_number)) {
                    arsort($treatments_number);
                    $treatments_number = array_slice($treatments_number, 0, 10, true);
                    $data['topTreatments'] = $treatments_number;
                }
                // Top Diagnoses and Treatments ends






                // diseas with outbrerak potential starts
                $data['diseasesWithOutbreakPotential'] = $this->home_model->getDiseasesWithOutbreakPotential() ?? [];
                $diseases = $this->home_model->getDiseasesWithOutbreakPotential() ?? [];

                $data['cases'] = []; // Initialize the array for storing case ratios

                if (is_array($diseases) && !empty($diseases)) {
                    foreach ($diseases as $disease) {
                        $expected_patients = $disease->maximum_expected_number_of_patient_in_a_week ?? 0;
                        $number_of_cases = $this->home_model->getCasesNumberForOutbreakDisease($disease->id) ?? 0;

                        // Ensure that $expected_patients is not zero to avoid division by zero
                        if ($expected_patients > 0) {
                            $ratio = ($number_of_cases / $expected_patients);
                        } else {
                            $ratio = 0; // If $expected_patients is zero, set ratio to 0
                        }

                        $data['cases'][$disease->id] = $ratio;
                    }
                }

                if (is_array($data['cases']) && !empty($data['cases'])) {
                    arsort($data['cases']); // Sort cases by ratio in descending order
                    $data['cases'] = array_slice($data['cases'], 0, 10, true); // Get the top 10 ratios
                }


                // diseas with outbrerak potential ends


                // Top Revenue Generating Services starts
                $payment_categories = $this->home_model->getPaymentCategories();
                $payments = $this->home_model->getPaymentLastThirtyDays();

                $category_total = array();
                foreach ($payment_categories as $cat_name) {
                    foreach ($payments as $payment) {
                        $categories_in_payment = explode(',', $payment->category_name);
                        foreach ($categories_in_payment as $key => $category_in_payment) {
                            $category_id = explode('*', $category_in_payment);
                            if ($category_id[0] == $cat_name->id) {
                                $category_total[$cat_name->id] = ($category_total[$cat_name->id] ?? 0) + ($category_id[1] ?? 0) * ($category_id[3] ?? 0);
                            }
                        }
                    }
                }

                arsort($category_total);
                // get 10 first 10 elemts of the array
                $category_total = array_slice($category_total, 0, 10, true);

                $data['topRevenueGeneratingServices'] = $category_total;

                // Top Revenue Generating Services ends

                // Low Stock of Key Medications starts
                $data['lowStockMedicines'] = $this->home_model->getMedicineByStockAlert();
                // Low Stock of Key Medications ends

                // Diagnostic, Invoices, Referral & Staff Analytics for active menu modules
                $hospital_id = $this->session->userdata('hospital_id');
                if (!empty($hospital_id)) {
                    $startOfDay = strtotime("today midnight");
                    $endOfDay = strtotime("tomorrow midnight") - 1;
                    $startOfMonth = strtotime("first day of this month midnight");
                    $endOfMonth = strtotime("first day of next month midnight") - 1;
                    $startOfYear = strtotime("first day of january this year midnight");
                    $endOfYear = strtotime("last day of december this year midnight") + 86399;

                    // Invoices
                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $data['totalInvoices'] = $this->db->count_all_results('payment');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('date >=', $startOfDay);
                    $this->db->where('date <=', $endOfDay);
                    $data['invoicesToday'] = $this->db->count_all_results('payment');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('date >=', $startOfMonth);
                    $this->db->where('date <=', $endOfMonth);
                    $data['invoicesThisMonth'] = $this->db->count_all_results('payment');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('date >=', $startOfYear);
                    $this->db->where('date <=', $endOfYear);
                    $data['invoicesThisYear'] = $this->db->count_all_results('payment');

                    // Lab Tests / Reports
                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $data['totalLabReports'] = $this->db->count_all_results('lab');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('date >=', $startOfDay);
                    $this->db->where('date <=', $endOfDay);
                    $data['labTestsToday'] = $this->db->count_all_results('lab');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('date >=', $startOfMonth);
                    $this->db->where('date <=', $endOfMonth);
                    $data['labTestsThisMonth'] = $this->db->count_all_results('lab');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('date >=', $startOfYear);
                    $this->db->where('date <=', $endOfYear);
                    $data['labTestsThisYear'] = $this->db->count_all_results('lab');

                    // Lab Status
                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->group_start();
                    $this->db->where('test_status !=', 'done');
                    $this->db->or_where('test_status IS NULL', null, false);
                    $this->db->or_where('test_status', '');
                    $this->db->group_end();
                    $data['labPendingCount'] = $this->db->count_all_results('lab');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('test_status', 'done');
                    $data['labDoneCount'] = $this->db->count_all_results('lab');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('delivery_status', 'delivered');
                    $data['labDeliveredCount'] = $this->db->count_all_results('lab');

                    // Referrals
                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $data['totalReferrers'] = $this->db->count_all_results('referrer');

                    $this->db->reset_query();
                    $this->db->select_sum('commission_amount');
                    $this->db->where('hospital_id', $hospital_id);
                    $this->db->where('created_date >=', $startOfMonth);
                    $ref_comm_res = $this->db->get('referral_commission_ledger')->row();
                    $data['referralCommissionThisMonth'] = $ref_comm_res->commission_amount ?? 0;

                    $this->db->reset_query();
                    $this->db->select_sum('commission_amount');
                    $this->db->where('hospital_id', $hospital_id);
                    $ref_comm_total_res = $this->db->get('referral_commission_ledger')->row();
                    $data['referralCommissionTotal'] = $ref_comm_total_res->commission_amount ?? 0;

                    $data['topReferrers'] = $this->db->query("
                        SELECT r.id, r.name, COUNT(l.id) as total_cases, COALESCE(SUM(l.commission_amount), 0) as total_comm
                        FROM referrer r
                        LEFT JOIN referral_commission_ledger l ON r.id = l.referrer_id
                        WHERE r.hospital_id = " . (int)$hospital_id . "
                        GROUP BY r.id
                        ORDER BY total_cases DESC, total_comm DESC
                        LIMIT 5
                    ")->result();

                    // Inventory & Departments & Staff
                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $data['totalInventoryItems'] = $this->db->count_all_results('inventory_items');

                    $this->db->reset_query();
                    $this->db->where('hospital_id', $hospital_id);
                    $data['totalDepartments'] = $this->db->count_all_results('department');

                    $total_staff = 0;
                    foreach (['nurse', 'laboratorist', 'receptionist', 'accountant', 'pharmacist'] as $staff_table) {
                        $this->db->reset_query();
                        $this->db->where('hospital_id', $hospital_id);
                        $total_staff += $this->db->count_all_results($staff_table);
                    }
                    $data['totalStaff'] = $total_staff;
                }

                $data['settings'] = $this->settings_model->getSettings();
                $data['timezones'] = $this->gmtTime();
                $this->load->view('dashboard'); // just the header file
                $this->load->view('home', $data);
                $this->load->view('footer', $data);
            }
        } else {
            $data['hospitals'] = $this->hospital_model->getHospital();
            $data['this_month']['payment'] = $this->hospital_model->thisMonthlyDepositCount();
            $data['this_yearly']['payment'] = $this->hospital_model->thisYearlyDepositCount();
            $data['this_year']['payment_per_month'] = $this->hospital_model->getPaymentPerMonthThisYear();
            $data['this_monthly']['payment'] = $this->hospital_model->thisMonthlyDeposit();
            $data['this_year']['payment'] = $this->hospital_model->thisYearlyDeposit();
            $data['this_day']['payment'] = $this->hospital_model->thisDayMonthlyPayment();
            $data['this_day']['payment_yearly'] = $this->hospital_model->thisDayYearlyPayment();
            $data['this_year_payment']['payment'] = $this->hospital_model->thisYearYearlyPayment();
            $data['this_month_payment']['payment'] = $this->hospital_model->thisYearMonthlyPayment();
            $data['hospitals'] = $this->hospital_model->getHospital();
            $data['settings'] = $this->settings_model->getSettings();
            $this->load->view('dashboard'); // just the header file
            $this->load->view('home', $data);
            $this->load->view('footer');
        }
    }


    public function permission()
    {
        $this->load->view('permission');
    }

    function updateSidebarState()
    {
        $userId = $this->ion_auth->get_user_id(); // Get the current user ID
        $sidebarValue = $this->input->post('sidebar'); // Get the sidebar value from the POST data
        $data = array(
            'sidebar' => $sidebarValue
        );
        $this->home_model->updateUser($userId, $data);
    }

    function updateDarkMode()
    {
        $darkModeValue = $this->input->post('darkMode'); // Get the dark mode value from the POST data
        $this->session->set_userdata('darkMode', $darkModeValue);
    }



    function businessChart()
    {
        $this->load->view('business_chart');
    }

    function modernDashboard()    
    {
        $this->load->view('modern_dashboard');
    }

    function gmtTime() {
        $timezones = array(
            'Pacific/Midway' => "(GMT-11:00) Midway Island",
            'US/Samoa' => "(GMT-11:00) Samoa",
            'US/Hawaii' => "(GMT-10:00) Hawaii",
            'US/Alaska' => "(GMT-09:00) Alaska",
            'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)",
            'America/Tijuana' => "(GMT-08:00) Tijuana",
            'US/Arizona' => "(GMT-07:00) Arizona",
            'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)",
            'America/Chihuahua' => "(GMT-07:00) Chihuahua",
            'America/Mazatlan' => "(GMT-07:00) Mazatlan",
            'America/Mexico_City' => "(GMT-06:00) Mexico City",
            'America/Monterrey' => "(GMT-06:00) Monterrey",
            'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
            'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)",
            'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)",
            'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
            'America/Bogota' => "(GMT-05:00) Bogota",
            'America/Lima' => "(GMT-05:00) Lima",
            'America/Caracas' => "(GMT-04:30) Caracas",
            'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
            'America/La_Paz' => "(GMT-04:00) La Paz",
            'America/Santiago' => "(GMT-04:00) Santiago",
            'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
            'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
            'Greenland' => "(GMT-03:00) Greenland",
            'Atlantic/Stanley' => "(GMT-02:00) Stanley",
            'Atlantic/Azores' => "(GMT-01:00) Azores",
            'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
            'Africa/Casablanca' => "(GMT) Casablanca",
            'Europe/Dublin' => "(GMT) Dublin",
            'Europe/Lisbon' => "(GMT) Lisbon",
            'Europe/London' => "(GMT) London",
            'Africa/Monrovia' => "(GMT) Monrovia",
            'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
            'Europe/Belgrade' => "(GMT+01:00) Belgrade",
            'Europe/Berlin' => "(GMT+01:00) Berlin",
            'Europe/Bratislava' => "(GMT+01:00) Bratislava",
            'Europe/Brussels' => "(GMT+01:00) Brussels",
            'Europe/Budapest' => "(GMT+01:00) Budapest",
            'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
            'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
            'Europe/Madrid' => "(GMT+01:00) Madrid",
            'Europe/Paris' => "(GMT+01:00) Paris",
            'Europe/Prague' => "(GMT+01:00) Prague",
            'Europe/Rome' => "(GMT+01:00) Rome",
            'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
            'Europe/Skopje' => "(GMT+01:00) Skopje",
            'Europe/Stockholm' => "(GMT+01:00) Stockholm",
            'Europe/Vienna' => "(GMT+01:00) Vienna",
            'Europe/Warsaw' => "(GMT+01:00) Warsaw",
            'Europe/Zagreb' => "(GMT+01:00) Zagreb",
            'Europe/Athens' => "(GMT+02:00) Athens",
            'Europe/Bucharest' => "(GMT+02:00) Bucharest",
            'Africa/Cairo' => "(GMT+02:00) Cairo",
            'Africa/Harare' => "(GMT+02:00) Harare",
            'Europe/Helsinki' => "(GMT+02:00) Helsinki",
            'Europe/Istanbul' => "(GMT+02:00) Istanbul",
            'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
            'Europe/Kiev' => "(GMT+02:00) Kyiv",
            'Europe/Minsk' => "(GMT+02:00) Minsk",
            'Europe/Riga' => "(GMT+02:00) Riga",
            'Europe/Sofia' => "(GMT+02:00) Sofia",
            'Europe/Tallinn' => "(GMT+02:00) Tallinn",
            'Europe/Vilnius' => "(GMT+02:00) Vilnius",
            'Asia/Baghdad' => "(GMT+03:00) Baghdad",
            'Asia/Kuwait' => "(GMT+03:00) Kuwait",
            'Africa/Nairobi' => "(GMT+03:00) Nairobi",
            'Asia/Riyadh' => "(GMT+03:00) Riyadh",
            'Europe/Moscow' => "(GMT+03:00) Moscow",
            'Asia/Tehran' => "(GMT+03:30) Tehran",
            'Asia/Baku' => "(GMT+04:00) Baku",
            'Europe/Volgograd' => "(GMT+04:00) Volgograd",
            'Asia/Muscat' => "(GMT+04:00) Muscat",
            'Asia/Tbilisi' => "(GMT+04:00) Tbilisi",
            'Asia/Yerevan' => "(GMT+04:00) Yerevan",
            'Asia/Kabul' => "(GMT+04:30) Kabul",
            'Asia/Karachi' => "(GMT+05:00) Karachi",
            'Asia/Tashkent' => "(GMT+05:00) Tashkent",
            'Asia/Kolkata' => "(GMT+05:30) Kolkata",
            'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
            'Asia/Yekaterinburg' => "(GMT+06:00) Ekaterinburg",
            'Asia/Almaty' => "(GMT+06:00) Almaty",
            'Asia/Dhaka' => "(GMT+06:00) Dhaka",
            'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk",
            'Asia/Bangkok' => "(GMT+07:00) Bangkok",
            'Asia/Jakarta' => "(GMT+07:00) Jakarta",
            'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk",
            'Asia/Chongqing' => "(GMT+08:00) Chongqing",
            'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
            'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
            'Australia/Perth' => "(GMT+08:00) Perth",
            'Asia/Singapore' => "(GMT+08:00) Singapore",
            'Asia/Taipei' => "(GMT+08:00) Taipei",
            'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
            'Asia/Urumqi' => "(GMT+08:00) Urumqi",
            'Asia/Irkutsk' => "(GMT+09:00) Irkutsk",
            'Asia/Seoul' => "(GMT+09:00) Seoul",
            'Asia/Tokyo' => "(GMT+09:00) Tokyo",
            'Australia/Adelaide' => "(GMT+09:30) Adelaide",
            'Australia/Darwin' => "(GMT+09:30) Darwin",
            'Asia/Yakutsk' => "(GMT+10:00) Yakutsk",
            'Australia/Brisbane' => "(GMT+10:00) Brisbane",
            'Australia/Canberra' => "(GMT+10:00) Canberra",
            'Pacific/Guam' => "(GMT+10:00) Guam",
            'Australia/Hobart' => "(GMT+10:00) Hobart",
            'Australia/Melbourne' => "(GMT+10:00) Melbourne",
            'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
            'Australia/Sydney' => "(GMT+10:00) Sydney",
            'Asia/Vladivostok' => "(GMT+11:00) Vladivostok",
            'Asia/Magadan' => "(GMT+12:00) Magadan",
            'Pacific/Auckland' => "(GMT+12:00) Auckland",
            'Pacific/Fiji' => "(GMT+12:00) Fiji",
        );
        return $timezones;
    }

    public function updateTimezone()
    {
        // Set JSON header
        header('Content-Type: application/json');
        
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(array('success' => false, 'message' => 'Not authorized'));
            return;
        }

        $timezone = $this->input->post('timezone');
        
        if (empty($timezone)) {
            echo json_encode(array('success' => false, 'message' => 'Timezone is required'));
            return;
        }

        // Validate timezone - simplified list
        $valid_timezones = array(
            'UTC', 'GMT', 'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles',
            'Europe/London', 'Europe/Paris', 'Europe/Berlin', 'Europe/Rome', 'Europe/Madrid',
            'Asia/Tokyo', 'Asia/Shanghai', 'Asia/Kolkata', 'Asia/Dubai', 'Asia/Karachi',
            'Australia/Sydney', 'Australia/Melbourne', 'Pacific/Auckland'
        );

        if (!in_array($timezone, $valid_timezones)) {
            echo json_encode(array('success' => false, 'message' => 'Invalid timezone'));
            return;
        }

        $hospital_id = $this->session->userdata('hospital_id');
        
        if (!$hospital_id) {
            echo json_encode(array('success' => false, 'message' => 'Hospital ID not found'));
            return;
        }

        // Update timezone in settings
        $this->db->where('hospital_id', $hospital_id);
        $result = $this->db->update('settings', array('timezone' => $timezone));

        if ($result) {
            // Update session timezone
            $this->session->set_userdata('timezone', $timezone);
            
            echo json_encode(array('success' => true, 'message' => 'Timezone updated successfully'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to update timezone'));
        }
    }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
