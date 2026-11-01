<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Forward legacy view to the comprehensive Phase 4 Doctor Commission Summary
if (!isset($summary)) {
    redirect('finance/doctorsCommissionSummary');
} else {
    $this->load->view('finance/doctor_commission_summary', $data);
}
