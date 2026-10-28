<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Referral_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // REFERRER CRUD
    // ==========================================

    function getReferrers($hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $this->db->where('hospital_id', $hospital_id);
        $this->db->order_by('id', 'desc');
        return $this->db->get('referrer')->result();
    }

    function getActiveReferrers($hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('status', 'Active');
        $this->db->order_by('name', 'asc');
        return $this->db->get('referrer')->result();
    }

    function getReferrerById($id, $hospital_id = null)
    {
        if (!empty($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }
        $this->db->where('id', $id);
        return $this->db->get('referrer')->row();
    }

    function insertReferrer($data)
    {
        $this->db->insert('referrer', $data);
        $id = $this->db->insert_id();
        // Automatically initialize wallet
        if ($id) {
            $this->getOrCreateWallet($id, $data['hospital_id']);
            $this->logAction('Referrer Created', 'REF-' . $id, null, json_encode($data), $data['hospital_id']);
        }
        return $id;
    }

    function updateReferrer($id, $data)
    {
        $old = $this->getReferrerById($id);
        $this->db->where('id', $id);
        $this->db->update('referrer', $data);
        $this->logAction('Referrer Updated', 'REF-' . $id, json_encode($old), json_encode($data), $old->hospital_id);
        return true;
    }

    function deleteReferrer($id, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $ref = $this->getReferrerById($id, $hospital_id);
        if ($ref) {
            // Check if there are existing commission ledger transactions
            $trans = $this->db->get_where('referral_commission_ledger', array('referrer_id' => $id))->num_rows();
            if ($trans > 0) {
                // Soft deactivate to preserve historical financial integrity
                $this->db->where('id', $id)->update('referrer', array('status' => 'Inactive'));
                $this->logAction('Referrer Deactivated (Historical Records Exist)', 'REF-' . $id, 'Active', 'Inactive', $hospital_id);
            } else {
                $this->db->where('id', $id)->delete('referrer');
                $this->db->where('referrer_id', $id)->delete('referral_wallet');
                $this->logAction('Referrer Deleted', 'REF-' . $id, json_encode($ref), null, $hospital_id);
            }
            return true;
        }
        return false;
    }

    // ==========================================
    // WALLET MANAGEMENT & TRANSACTIONS
    // ==========================================

    function getOrCreateWallet($referrer_id, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $wallet = $this->db->get_where('referral_wallet', array('referrer_id' => $referrer_id))->row();
        if (empty($wallet)) {
            $data = array(
                'referrer_id' => $referrer_id,
                'total_earned' => 0.00,
                'pending_commission' => 0.00,
                'available_balance' => 0.00,
                'pending_withdrawal' => 0.00,
                'total_withdrawn' => 0.00,
                'hospital_id' => $hospital_id,
                'updated_at' => time()
            );
            $this->db->insert('referral_wallet', $data);
            $wallet = $this->db->get_where('referral_wallet', array('referrer_id' => $referrer_id))->row();
        }
        return $wallet;
    }

    function getWallets($hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $this->db->select('referral_wallet.*, referrer.name as referrer_name, referrer.phone as referrer_phone, referrer.type as referrer_type, referrer.status as referrer_status');
        $this->db->from('referral_wallet');
        $this->db->join('referrer', 'referrer.id = referral_wallet.referrer_id', 'left');
        $this->db->where('referral_wallet.hospital_id', $hospital_id);
        $this->db->order_by('referrer.name', 'asc');
        return $this->db->get()->result();
    }

    function getWalletByReferrerId($referrer_id)
    {
        return $this->getOrCreateWallet($referrer_id);
    }

    function getWallet($referrer_id)
    {
        return $this->getOrCreateWallet($referrer_id);
    }

    function recordWalletTransaction($data)
    {
        $data['created_at'] = !empty($data['created_at']) ? $data['created_at'] : time();
        $data['created_by'] = !empty($data['created_by']) ? $data['created_by'] : $this->ion_auth->get_user_id();
        $this->db->insert('referral_wallet_transactions', $data);
        return $this->db->insert_id();
    }

    function getWalletTransactions($referrer_id, $from_date = null, $to_date = null)
    {
        $this->db->where('referrer_id', $referrer_id);
        if (!empty($from_date)) {
            $from_ts = is_numeric($from_date) ? intval($from_date) : strtotime($from_date . ' 00:00:00');
            $this->db->where('created_at >=', $from_ts);
        }
        if (!empty($to_date)) {
            $to_ts = is_numeric($to_date) ? intval($to_date) : strtotime($to_date . ' 23:59:59');
            $this->db->where('created_at <=', $to_ts);
        }
        $this->db->order_by('id', 'desc');
        return $this->db->get('referral_wallet_transactions')->result();
    }

    function recordControlledAdjustment($referrer_id, $type, $amount, $reason, $reference, $user_id, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $amount = floatval($amount);
        if ($amount <= 0) {
            return array('status' => 'error', 'message' => 'Adjustment amount must be positive.');
        }
        if (empty($reason)) {
            return array('status' => 'error', 'message' => 'Adjustment reason is mandatory.');
        }

        $wallet = $this->getOrCreateWallet($referrer_id, $hospital_id);
        $old_avail = floatval($wallet->available_balance);

        $this->db->trans_start();

        if ($type == 'credit') {
            $new_avail = $old_avail + $amount;
            $new_earned = floatval($wallet->total_earned) + $amount;
            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'available_balance' => $new_avail,
                'total_earned' => $new_earned,
                'updated_at' => time()
            ));

            $this->recordWalletTransaction(array(
                'referrer_id' => $referrer_id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $new_avail,
                'source' => 'adjustment',
                'reference_id' => !empty($reference) ? $reference : 'ADJ-' . time(),
                'description' => 'Controlled Credit Adjustment: ' . $reason,
                'hospital_id' => $hospital_id,
                'created_by' => $user_id
            ));

            $this->logAction('Wallet Adjustment (Credit)', $reference, 'Old: ' . $old_avail, 'New: ' . $new_avail . ' | Reason: ' . $reason, $hospital_id);
        } else {
            if ($old_avail < $amount) {
                return array('status' => 'error', 'message' => 'Cannot debit ' . $amount . '. Available balance is only ' . $old_avail);
            }
            $new_avail = $old_avail - $amount;
            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'available_balance' => $new_avail,
                'updated_at' => time()
            ));

            $this->recordWalletTransaction(array(
                'referrer_id' => $referrer_id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $new_avail,
                'source' => 'adjustment',
                'reference_id' => !empty($reference) ? $reference : 'ADJ-' . time(),
                'description' => 'Controlled Debit Adjustment: ' . $reason,
                'hospital_id' => $hospital_id,
                'created_by' => $user_id
            ));

            $this->logAction('Wallet Adjustment (Debit)', $reference, 'Old: ' . $old_avail, 'New: ' . $new_avail . ' | Reason: ' . $reason, $hospital_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array('status' => 'error', 'message' => 'Database error while processing adjustment.');
        }

        return array('status' => 'success', 'message' => 'Controlled adjustment of ' . $amount . ' processed successfully.');
    }

    // ==========================================
    // COMMISSION LIFECYCLE & LEDGER
    // ==========================================

    /**
     * Snapshot invoice items and record pending commissions
     */
    function recordInvoiceCommissions($payment_id, $items, $referrer_id, $doctor_id, $hospital_id)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        $payment = $this->db->get_where('payment', array('id' => $payment_id))->row();
        $patient_id = !empty($payment->patient) ? $payment->patient : 0;
        $total_ref_commission = 0;

        foreach ($items as $item) {
            $item_id = $item['item_id'];
            $item_name = $item['item_name'];
            $item_price = floatval($item['item_price']);
            $quantity = floatval($item['quantity']);
            $subtotal = isset($item['subtotal']) ? floatval($item['subtotal']) : ($item_price * $quantity);

            $d_rate = floatval($item['doctor_commission_rate']);
            $r_rate = (!empty($referrer_id)) ? floatval($item['referral_commission_rate']) : 0.00;

            // CORE BUSINESS RULE: Commission Base Amount = Final Net Invoice Amount After Discount
            if (isset($item['net_subtotal'])) {
                $item_net_base = max(0.00, floatval($item['net_subtotal']));
            } else {
                $p_gross = (!empty($payment) && !empty($payment->amount) && floatval($payment->amount) > 0) ? floatval($payment->amount) : 0;
                $p_disc = 0.00;
                if (!empty($payment->flat_discount) && floatval($payment->flat_discount) > 0) {
                    $p_disc = floatval($payment->flat_discount);
                } elseif (!empty($payment->discount) && floatval($payment->discount) > 0) {
                    $p_disc = floatval($payment->discount);
                }
                $net_ratio = ($p_gross > 0) ? max(0.0, ($p_gross - $p_disc) / $p_gross) : 1.0;
                $item_net_base = max(0.00, round($subtotal * $net_ratio, 2));
            }

            if (empty($doctor_id)) {
                $d_rate = 0.00;
                $d_amount = 0.00;
            } elseif (isset($item['doctor_commission_amount'])) {
                $d_amount = floatval($item['doctor_commission_amount']);
            } else {
                $d_amount = round(($item_net_base * $d_rate) / 100, 2);
            }

            if (empty($referrer_id)) {
                $r_rate = 0.00;
                $r_amount = 0.00;
            } elseif (isset($item['referral_commission_amount'])) {
                $r_amount = floatval($item['referral_commission_amount']);
            } else {
                $r_amount = round(($item_net_base * $r_rate) / 100, 2);
            }

            // 1. Snapshot into payment_items
            $item_data = array(
                'payment_id' => $payment_id,
                'item_id' => $item_id,
                'item_name' => $item_name,
                'item_price' => $item_price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'doctor_commission_rate' => $d_rate,
                'doctor_commission_amount' => $d_amount,
                'referral_commission_rate' => $r_rate,
                'referral_commission_amount' => $r_amount,
                'hospital_id' => $hospital_id,
                'created_at' => time()
            );
            $this->db->insert('payment_items', $item_data);
            $invoice_item_id = $this->db->insert_id();

            // 2. If referrer is selected and commission rate > 0, create pending ledger entry
            if (!empty($referrer_id) && $r_amount > 0) {
                // Idempotency check: don't create duplicate for this payment_id and item
                $existing = $this->db->get_where('referral_commission_ledger', array(
                    'invoice_id' => $payment_id,
                    'item_id' => $item_id
                ))->row();

                if (empty($existing)) {
                    $txn_id = 'REF-' . $payment_id . '-' . $item_id . '-' . time();
                    $note_text = 'Invoice item: ' . $item_name;
                    if (abs($subtotal - $item_net_base) > 0.009) {
                        $note_text .= ' (Gross: ' . number_format($subtotal, 2) . ', Comm Base: ' . number_format($item_net_base, 2) . ')';
                    }
                    $ledger_entry = array(
                        'transaction_id' => $txn_id,
                        'referrer_id' => $referrer_id,
                        'invoice_id' => $payment_id,
                        'invoice_item_id' => $invoice_item_id,
                        'patient_id' => $patient_id,
                        'item_id' => $item_id,
                        'commission_rate' => $r_rate,
                        'item_price' => $item_net_base, // Commission Base Amount after discount
                        'commission_amount' => $r_amount,
                        'earned_amount' => 0.00,
                        'payment_reference' => 'INV-' . $payment_id,
                        'status' => 'Pending',
                        'hospital_id' => $hospital_id,
                        'created_date' => time(),
                        'created_by' => $this->ion_auth->get_user_id(),
                        'notes' => $note_text
                    );
                    $this->db->insert('referral_commission_ledger', $ledger_entry);
                    $total_ref_commission += $r_amount;
                }
            }
        }

        // Increase wallet pending commission
        if (!empty($referrer_id) && $total_ref_commission > 0) {
            $wallet = $this->getOrCreateWallet($referrer_id, $hospital_id);
            $new_pending = floatval($wallet->pending_commission) + $total_ref_commission;
            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'pending_commission' => $new_pending,
                'updated_at' => time()
            ));
        }

        return true;
    }

    /**
     * Synchronize invoice items and commissions on invoice edit ($id != '')
     */
    function syncInvoiceCommissions($payment_id, $items, $referrer_id, $doctor_id, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        $payment = $this->db->get_where('payment', array('id' => $payment_id))->row();
        if (empty($payment)) {
            return false;
        }

        $patient_id = !empty($payment->patient) ? $payment->patient : 0;

        // 1. Fetch existing ledger entries for this invoice
        $existing_entries = $this->db->get_where('referral_commission_ledger', array('invoice_id' => $payment_id))->result();

        // 2. Safely reverse prior commissions from wallets to prevent double-crediting
        if (!empty($existing_entries)) {
            $reversed_by_referrer = array();
            foreach ($existing_entries as $entry) {
                if ($entry->status != 'Reversed' && $entry->status != 'Cancelled') {
                    $ref_id = $entry->referrer_id;
                    if (!isset($reversed_by_referrer[$ref_id])) {
                        $reversed_by_referrer[$ref_id] = array('earned' => 0.00, 'pending' => 0.00);
                    }
                    $e_amt = floatval($entry->earned_amount);
                    $p_amt = floatval($entry->commission_amount) - $e_amt;
                    $reversed_by_referrer[$ref_id]['earned'] += $e_amt;
                    if ($p_amt > 0) {
                        $reversed_by_referrer[$ref_id]['pending'] += $p_amt;
                    }
                }
            }

            foreach ($reversed_by_referrer as $r_id => $rev_data) {
                $wallet = $this->getOrCreateWallet($r_id, $hospital_id);
                $new_avail = max(0.0, floatval($wallet->available_balance) - $rev_data['earned']);
                $new_total_e = max(0.0, floatval($wallet->total_earned) - $rev_data['earned']);
                $new_pending = max(0.0, floatval($wallet->pending_commission) - $rev_data['pending']);

                $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                    'available_balance' => $new_avail,
                    'total_earned' => $new_total_e,
                    'pending_commission' => $new_pending,
                    'updated_at' => time()
                ));

                if ($rev_data['earned'] > 0) {
                    $this->recordWalletTransaction(array(
                        'referrer_id' => $r_id,
                        'type' => 'debit',
                        'amount' => $rev_data['earned'],
                        'balance_after' => $new_avail,
                        'source' => 'reversal',
                        'reference_id' => 'INV-' . $payment_id,
                        'description' => 'Referral commission adjusted/reversed due to invoice edit on Invoice #' . $payment_id,
                        'hospital_id' => $hospital_id
                    ));
                }
            }

            // Mark old entries as Reversed/Superceded
            $this->db->where('invoice_id', $payment_id);
            $this->db->update('referral_commission_ledger', array(
                'status' => 'Reversed',
                'notes' => 'Superceded by invoice edit on ' . date('d-m-Y H:i')
            ));
        }

        // 3. Delete existing payment_items for this invoice and insert new snapshot
        $this->db->where('payment_id', $payment_id);
        $this->db->delete('payment_items');

        $total_ref_commission = 0.00;

        foreach ($items as $item) {
            $item_id = $item['item_id'];
            $item_name = $item['item_name'];
            $item_price = floatval($item['item_price']);
            $quantity = floatval($item['quantity']);
            $subtotal = isset($item['subtotal']) ? floatval($item['subtotal']) : ($item_price * $quantity);
            $item_net_base = isset($item['net_subtotal']) ? max(0.00, floatval($item['net_subtotal'])) : $subtotal;

            $d_rate = (!empty($doctor_id)) ? floatval($item['doctor_commission_rate']) : 0.00;
            $d_amount = (!empty($doctor_id) && isset($item['doctor_commission_amount'])) ? floatval($item['doctor_commission_amount']) : round(($item_net_base * $d_rate) / 100, 2);

            $r_rate = (!empty($referrer_id)) ? floatval($item['referral_commission_rate']) : 0.00;
            $r_amount = (!empty($referrer_id) && isset($item['referral_commission_amount'])) ? floatval($item['referral_commission_amount']) : round(($item_net_base * $r_rate) / 100, 2);

            $item_data = array(
                'payment_id' => $payment_id,
                'item_id' => $item_id,
                'item_name' => $item_name,
                'item_price' => $item_price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'doctor_commission_rate' => $d_rate,
                'doctor_commission_amount' => $d_amount,
                'referral_commission_rate' => $r_rate,
                'referral_commission_amount' => $r_amount,
                'hospital_id' => $hospital_id,
                'created_at' => time()
            );
            $this->db->insert('payment_items', $item_data);
            $invoice_item_id = $this->db->insert_id();

            // 4. Create new pending ledger entries if referrer exists
            if (!empty($referrer_id) && $r_amount > 0) {
                $txn_id = 'REF-' . $payment_id . '-' . $item_id . '-' . time() . '-' . rand(100, 999);
                $note_text = 'Invoice item: ' . $item_name;
                if (abs($subtotal - $item_net_base) > 0.009) {
                    $note_text .= ' (Gross: ' . number_format($subtotal, 2) . ', Comm Base: ' . number_format($item_net_base, 2) . ')';
                }
                $ledger_entry = array(
                    'transaction_id' => $txn_id,
                    'referrer_id' => $referrer_id,
                    'invoice_id' => $payment_id,
                    'invoice_item_id' => $invoice_item_id,
                    'patient_id' => $patient_id,
                    'item_id' => $item_id,
                    'commission_rate' => $r_rate,
                    'item_price' => $item_net_base,
                    'commission_amount' => $r_amount,
                    'earned_amount' => 0.00,
                    'payment_reference' => 'INV-' . $payment_id,
                    'status' => 'Pending',
                    'hospital_id' => $hospital_id,
                    'created_date' => time(),
                    'created_by' => $this->ion_auth->get_user_id(),
                    'notes' => $note_text
                );
                $this->db->insert('referral_commission_ledger', $ledger_entry);
                $total_ref_commission += $r_amount;
            }
        }

        // 5. Update wallet pending commission for new referrer
        if (!empty($referrer_id) && $total_ref_commission > 0) {
            $wallet = $this->getOrCreateWallet($referrer_id, $hospital_id);
            $new_pending = floatval($wallet->pending_commission) + $total_ref_commission;
            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'pending_commission' => $new_pending,
                'updated_at' => time()
            ));

            // 6. Check if payment already has deposits, and earn commission proportionally
            $this->db->select_sum('deposited_amount');
            $this->db->where('payment_id', $payment_id);
            $dep_row = $this->db->get('patient_deposit')->row();
            $total_deposited = ($dep_row && !empty($dep_row->deposited_amount)) ? floatval($dep_row->deposited_amount) : floatval($payment->amount_received);

            if ($total_deposited > 0) {
                $gross_total = floatval($payment->gross_total);
                $this->processPaymentCommission($payment_id, $total_deposited, $gross_total, $hospital_id);
            }
        }

        return true;
    }


    /**
     * Process payment deposit and allocate collected money
     * Rule 3: When a patient makes a partial payment, allocate collected money to the hospital first
     * until the hospital's full share is paid. Only then credit subsequent collections to the referrer's
     * wallet, up to the referrer's remaining commission entitlement.
     * Rule 4 & 5: Support multiple payments, prevent duplicate credits, handle refunds/reductions safely.
     */
    function processPaymentCommission($payment_id, $new_deposit_amount, $gross_total, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        $payment = $this->db->get_where('payment', array('id' => $payment_id))->row();
        if (empty($payment) || empty($payment->referrer)) {
            return false;
        }

        $referrer_id = $payment->referrer;
        if (empty($hospital_id)) {
            $hospital_id = !empty($payment->hospital_id) ? $payment->hospital_id : null;
        }

        $gross_total = floatval($payment->gross_total);
        $referral_entitlement = isset($payment->referral_amount) ? floatval($payment->referral_amount) : 0.00;
        $hospital_share = isset($payment->hospital_amount) ? floatval($payment->hospital_amount) : 0.00;

        if (!isset($payment->hospital_amount) || $payment->hospital_amount === null) {
            $doctor_amount = isset($payment->doctor_amount) ? floatval($payment->doctor_amount) : 0.00;
            $hospital_share = max(0.00, round($gross_total - $doctor_amount - $referral_entitlement, 2));
        }

        // Calculate total deposited so far from patient_deposit
        $this->db->select_sum('deposited_amount');
        $this->db->where('payment_id', $payment_id);
        $dep_row = $this->db->get('patient_deposit')->row();
        $total_deposited = ($dep_row && $dep_row->deposited_amount !== null) ? floatval($dep_row->deposited_amount) : floatval($payment->amount_received);

        // If no records in patient_deposit yet, fallback to $new_deposit_amount
        if ($total_deposited <= 0 && !empty($new_deposit_amount) && floatval($new_deposit_amount) > 0) {
            $total_deposited = floatval($new_deposit_amount);
        }
        $total_deposited = max(0.00, $total_deposited);

        // Rule 3: Hospital paid first, referrer only gets collections after hospital share is paid
        if ($referral_entitlement <= 0 || $total_deposited <= $hospital_share) {
            $cumulative_target_earned = 0.00;
        } else {
            $surplus = round($total_deposited - $hospital_share, 2);
            $cumulative_target_earned = min($referral_entitlement, $surplus);
        }
        $cumulative_target_earned = max(0.00, $cumulative_target_earned);

        // Get all pending/earned ledger entries for this invoice
        $this->db->where('invoice_id', $payment_id);
        $this->db->where_in('status', array('Pending', 'Earned'));
        $ledger_entries = $this->db->get('referral_commission_ledger')->result();

        if (empty($ledger_entries)) {
            return false;
        }

        $already_earned_total = 0.00;
        $total_ledger_commission = 0.00;
        foreach ($ledger_entries as $entry) {
            $already_earned_total += floatval($entry->earned_amount);
            $total_ledger_commission += floatval($entry->commission_amount);
        }
        $already_earned_total = round($already_earned_total, 2);
        $total_ledger_commission = round($total_ledger_commission, 2);

        // Determine net change to credit or debit
        $net_diff = round($cumulative_target_earned - $already_earned_total, 2);

        // If no net change, return true without touching wallet to prevent duplicate entries
        if (abs($net_diff) < 0.005) {
            return true;
        }

        $wallet = $this->getOrCreateWallet($referrer_id, $hospital_id);

        $this->db->trans_start();

        // Distribute cumulative_target_earned across ledger entries
        $allocated_earned_so_far = 0.00;
        $num_entries = count($ledger_entries);

        for ($i = 0; $i < $num_entries; $i++) {
            $entry = $ledger_entries[$i];
            $comm_amt = floatval($entry->commission_amount);

            if ($total_ledger_commission > 0) {
                if ($i === $num_entries - 1) {
                    $entry_target_earned = max(0.00, round($cumulative_target_earned - $allocated_earned_so_far, 2));
                } else {
                    $entry_target_earned = round(($comm_amt / $total_ledger_commission) * $cumulative_target_earned, 2);
                    $allocated_earned_so_far += $entry_target_earned;
                }
            } else {
                $entry_target_earned = 0.00;
            }

            $new_status = ($entry_target_earned >= $comm_amt && $comm_amt > 0) ? 'Earned' : 'Pending';

            $this->db->where('id', $entry->id)->update('referral_commission_ledger', array(
                'earned_amount' => $entry_target_earned,
                'status' => $new_status,
                'payment_reference' => 'INV-' . $payment_id . ' (Collected: ৳' . number_format($total_deposited, 2) . ')'
            ));
        }

        if ($net_diff > 0) {
            // Credit to referrer wallet
            $credit_amount = $net_diff;
            $new_available = round(floatval($wallet->available_balance) + $credit_amount, 2);
            $new_total_earned = round(floatval($wallet->total_earned) + $credit_amount, 2);
            $new_pending = max(0.00, round(floatval($wallet->pending_commission) - $credit_amount, 2));

            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'available_balance' => $new_available,
                'total_earned' => $new_total_earned,
                'pending_commission' => $new_pending,
                'updated_at' => time()
            ));

            // Record credit in wallet ledger
            $this->recordWalletTransaction(array(
                'referrer_id' => $referrer_id,
                'type' => 'credit',
                'amount' => $credit_amount,
                'balance_after' => $new_available,
                'source' => 'commission',
                'reference_id' => 'INV-' . $payment_id,
                'description' => 'Referral commission earned from Invoice #' . $payment_id . ' (Collected: ৳' . number_format($total_deposited, 2) . ', Hospital Share: ৳' . number_format($hospital_share, 2) . ')',
                'hospital_id' => $hospital_id
            ));

            $this->logAction('Commission Earned', 'INV-' . $payment_id, null, 'Credited: ৳' . $credit_amount . ' (Total Earned for Inv: ৳' . $cumulative_target_earned . ')', $hospital_id);
        } elseif ($net_diff < 0) {
            // Debit / reversal from referrer wallet (e.g. deposit reduced or refunded)
            $debit_amount = abs($net_diff);
            $new_available = max(0.00, round(floatval($wallet->available_balance) - $debit_amount, 2));
            $new_total_earned = max(0.00, round(floatval($wallet->total_earned) - $debit_amount, 2));
            $new_pending = round(floatval($wallet->pending_commission) + $debit_amount, 2);

            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'available_balance' => $new_available,
                'total_earned' => $new_total_earned,
                'pending_commission' => $new_pending,
                'updated_at' => time()
            ));

            // Record reversal in wallet ledger
            $this->recordWalletTransaction(array(
                'referrer_id' => $referrer_id,
                'type' => 'debit',
                'amount' => $debit_amount,
                'balance_after' => $new_available,
                'source' => 'reversal',
                'reference_id' => 'INV-' . $payment_id,
                'description' => 'Referral commission reversed due to deposit refund/reduction on Invoice #' . $payment_id,
                'hospital_id' => $hospital_id
            ));

            $this->logAction('Commission Reversed (Refund)', 'INV-' . $payment_id, 'Reversed: ৳' . $debit_amount, 'Remaining Earned: ৳' . $cumulative_target_earned, $hospital_id);
        }

        $this->db->trans_complete();

        return ($this->db->trans_status() !== FALSE);
    }

    function recalculateCommissionAfterDepositChange($payment_id, $hospital_id = null)
    {
        return $this->processPaymentCommission($payment_id, null, null, $hospital_id);
    }

    /**
     * Invoice cancellation / reversal: adjust wallet & mark ledger cancelled
     */
    function reverseCommissionOnInvoiceCancellation($payment_id, $hospital_id = null, $reason = 'Invoice cancelled')
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        $payment = $this->db->get_where('payment', array('id' => $payment_id))->row();
        if (empty($payment) || empty($payment->referrer)) {
            return false;
        }

        $referrer_id = $payment->referrer;
        $ledger_entries = $this->db->get_where('referral_commission_ledger', array('invoice_id' => $payment_id))->result();

        if (empty($ledger_entries)) {
            return false;
        }

        $wallet = $this->getOrCreateWallet($referrer_id, $hospital_id);
        $total_earned_reversal = 0;
        $total_pending_reversal = 0;

        foreach ($ledger_entries as $entry) {
            if ($entry->status != 'Reversed' && $entry->status != 'Cancelled') {
                $earned = floatval($entry->earned_amount);
                $pending = floatval($entry->commission_amount) - $earned;

                $total_earned_reversal += $earned;
                if ($pending > 0) {
                    $total_pending_reversal += $pending;
                }

                $this->db->where('id', $entry->id)->update('referral_commission_ledger', array(
                    'status' => 'Reversed',
                    'notes' => $entry->notes . ' | Reversal: ' . $reason
                ));
            }
        }

        if ($total_earned_reversal > 0 || $total_pending_reversal > 0) {
            $new_available = floatval($wallet->available_balance) - $total_earned_reversal;
            $new_total_earned = max(0.0, floatval($wallet->total_earned) - $total_earned_reversal);
            $new_pending = max(0.0, floatval($wallet->pending_commission) - $total_pending_reversal);

            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'available_balance' => $new_available,
                'total_earned' => $new_total_earned,
                'pending_commission' => $new_pending,
                'updated_at' => time()
            ));

            if ($total_earned_reversal > 0) {
                $this->recordWalletTransaction(array(
                    'referrer_id' => $referrer_id,
                    'type' => 'debit',
                    'amount' => $total_earned_reversal,
                    'balance_after' => $new_available,
                    'source' => 'reversal',
                    'reference_id' => 'INV-' . $payment_id,
                    'description' => 'Commission reversal for Invoice #' . $payment_id . ' (' . $reason . ')',
                    'hospital_id' => $hospital_id
                ));
            }

            $this->logAction('Commission Reversed', 'INV-' . $payment_id, 'Earned: ' . $total_earned_reversal, 'Reversal: ' . $reason, $hospital_id);
        }

        return true;
    }

    // ==========================================
    // WITHDRAWAL MANAGEMENT
    // ==========================================

    function getWithdrawals($hospital_id = null, $status = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $this->db->select('referral_withdrawals.*, referrer.name as referrer_name, referrer.phone as referrer_phone, referrer.type as referrer_type');
        $this->db->from('referral_withdrawals');
        $this->db->join('referrer', 'referrer.id = referral_withdrawals.referrer_id', 'left');
        $this->db->where('referral_withdrawals.hospital_id', $hospital_id);
        if (!empty($status)) {
            $this->db->where('referral_withdrawals.status', $status);
        }
        $this->db->order_by('referral_withdrawals.id', 'desc');
        return $this->db->get()->result();
    }

    function getWithdrawalById($id)
    {
        $this->db->select('referral_withdrawals.*, referrer.name as referrer_name, referrer.phone as referrer_phone');
        $this->db->from('referral_withdrawals');
        $this->db->join('referrer', 'referrer.id = referral_withdrawals.referrer_id', 'left');
        $this->db->where('referral_withdrawals.id', $id);
        return $this->db->get()->row();
    }

    function requestWithdrawal($referrer_id, $amount, $payment_method, $account_number, $notes, $user_id, $hospital_id)
    {
        $amount = floatval($amount);
        if ($amount <= 0) {
            return array('status' => 'error', 'message' => 'Withdrawal amount must be greater than zero.');
        }

        $wallet = $this->getOrCreateWallet($referrer_id, $hospital_id);
        if ($amount > floatval($wallet->available_balance)) {
            return array('status' => 'error', 'message' => 'Insufficient available balance. Available: ' . $wallet->available_balance);
        }

        // Database transaction to ensure reservation integrity
        $this->db->trans_start();

        // 1. Reserve amount from available_balance -> pending_withdrawal
        $new_available = floatval($wallet->available_balance) - $amount;
        $new_pending_wd = floatval($wallet->pending_withdrawal) + $amount;

        $this->db->where('id', $wallet->id)->update('referral_wallet', array(
            'available_balance' => $new_available,
            'pending_withdrawal' => $new_pending_wd,
            'updated_at' => time()
        ));

        // 2. Insert withdrawal request
        $wd_id = 'WD-' . $hospital_id . '-' . time() . '-' . rand(100, 999);
        $data = array(
            'withdrawal_id' => $wd_id,
            'referrer_id' => $referrer_id,
            'amount' => $amount,
            'payment_method' => $payment_method,
            'account_number' => $account_number,
            'notes' => $notes,
            'status' => 'Pending',
            'hospital_id' => $hospital_id,
            'created_at' => time()
        );
        $this->db->insert('referral_withdrawals', $data);
        $inserted_id = $this->db->insert_id();

        $this->logAction('Withdrawal Requested', $wd_id, null, 'Amount: ' . $amount . ' to ' . $payment_method, $hospital_id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array('status' => 'error', 'message' => 'Failed to process withdrawal request.');
        }

        return array('status' => 'success', 'message' => 'Withdrawal request submitted successfully.', 'id' => $inserted_id);
    }

    function updateWithdrawalStatus($id, $new_status, $admin_notes = '', $payment_reference = '', $user_id = null)
    {
        $wd = $this->getWithdrawalById($id);
        if (empty($wd)) {
            return array('status' => 'error', 'message' => 'Withdrawal record not found.');
        }

        $wallet = $this->getOrCreateWallet($wd->referrer_id, $wd->hospital_id);
        $amount = floatval($wd->amount);

        $this->db->trans_start();

        if ($new_status == 'Rejected') {
            if ($wd->status == 'Paid') {
                return array('status' => 'error', 'message' => 'Cannot reject a paid withdrawal.');
            }
            if ($wd->status != 'Rejected') {
                // Return reserved amount from pending_withdrawal back to available_balance
                $new_available = floatval($wallet->available_balance) + $amount;
                $new_pending_wd = max(0.0, floatval($wallet->pending_withdrawal) - $amount);

                $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                    'available_balance' => $new_available,
                    'pending_withdrawal' => $new_pending_wd,
                    'updated_at' => time()
                ));

                $this->db->where('id', $id)->update('referral_withdrawals', array(
                    'status' => 'Rejected',
                    'admin_notes' => $admin_notes,
                    'processed_by' => $user_id
                ));

                $this->logAction('Withdrawal Rejected', $wd->withdrawal_id, 'Pending', 'Rejected (Refunded to balance)', $wd->hospital_id);
            }
        } elseif ($new_status == 'Paid') {
            if ($wd->status == 'Paid') {
                return array('status' => 'error', 'message' => 'Withdrawal is already marked as Paid.');
            }

            // Remove from pending_withdrawal, add to total_withdrawn
            $new_pending_wd = max(0.0, floatval($wallet->pending_withdrawal) - $amount);
            $new_total_wd = floatval($wallet->total_withdrawn) + $amount;

            $this->db->where('id', $wallet->id)->update('referral_wallet', array(
                'pending_withdrawal' => $new_pending_wd,
                'total_withdrawn' => $new_total_wd,
                'updated_at' => time()
            ));

            // Record wallet debit transaction
            $this->recordWalletTransaction(array(
                'referrer_id' => $wd->referrer_id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => floatval($wallet->available_balance),
                'source' => 'withdrawal',
                'reference_id' => $wd->withdrawal_id,
                'description' => 'Withdrawal paid via ' . $wd->payment_method . ' (' . $wd->account_number . '). Ref: ' . $payment_reference,
                'hospital_id' => $wd->hospital_id
            ));

            $this->db->where('id', $id)->update('referral_withdrawals', array(
                'status' => 'Paid',
                'payment_reference' => $payment_reference,
                'payment_date' => time(),
                'admin_notes' => $admin_notes,
                'processed_by' => $user_id
            ));

            $this->logAction('Withdrawal Paid', $wd->withdrawal_id, $wd->status, 'Paid. Ref: ' . $payment_reference, $wd->hospital_id);
        } else {
            // Pending / Approved / Processing
            $this->db->where('id', $id)->update('referral_withdrawals', array(
                'status' => $new_status,
                'admin_notes' => $admin_notes,
                'processed_by' => $user_id
            ));
            $this->logAction('Withdrawal Status Updated', $wd->withdrawal_id, $wd->status, $new_status, $wd->hospital_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array('status' => 'error', 'message' => 'Database error while updating withdrawal status.');
        }

        return array('status' => 'success', 'message' => 'Withdrawal status updated to ' . $new_status . '.');
    }

    // ==========================================
    // METRICS, STATEMENTS & REPORTS
    // ==========================================

    function getDashboardMetrics($hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        $total_referrers = $this->db->where('hospital_id', $hospital_id)->count_all_results('referrer');
        $active_referrers = $this->db->where(array('hospital_id' => $hospital_id, 'status' => 'Active'))->count_all_results('referrer');

        // Today's commission earned
        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $today_end = strtotime(date('Y-m-d 23:59:59'));
        $this->db->select_sum('amount');
        $this->db->where(array(
            'hospital_id' => $hospital_id,
            'source' => 'commission',
            'created_at >=' => $today_start,
            'created_at <=' => $today_end
        ));
        $today_earned = floatval($this->db->get('referral_wallet_transactions')->row()->amount);

        // This Month's commission earned
        $month_start = strtotime(date('Y-m-01 00:00:00'));
        $month_end = strtotime(date('Y-m-t 23:59:59'));
        $this->db->select_sum('amount');
        $this->db->where(array(
            'hospital_id' => $hospital_id,
            'source' => 'commission',
            'created_at >=' => $month_start,
            'created_at <=' => $month_end
        ));
        $month_earned = floatval($this->db->get('referral_wallet_transactions')->row()->amount);

        // Aggregate wallet balances
        $this->db->select_sum('available_balance');
        $this->db->select_sum('pending_commission');
        $this->db->select_sum('pending_withdrawal');
        $this->db->select_sum('total_withdrawn');
        $this->db->where('hospital_id', $hospital_id);
        $w_row = $this->db->get('referral_wallet')->row();

        return array(
            'total_referrers' => $total_referrers,
            'active_referrers' => $active_referrers,
            'today_earned' => $today_earned,
            'today_commission' => $today_earned,
            'month_earned' => $month_earned,
            'month_commission' => $month_earned,
            'total_available' => floatval($w_row->available_balance),
            'total_wallet_balance' => floatval($w_row->available_balance),
            'total_pending_commission' => floatval($w_row->pending_commission),
            'pending_commission' => floatval($w_row->pending_commission),
            'total_pending_withdrawal' => floatval($w_row->pending_withdrawal),
            'pending_withdrawals' => floatval($w_row->pending_withdrawal),
            'total_withdrawn' => floatval($w_row->total_withdrawn)
        );
    }

    function getReferrerStatement($referrer_id, $from_date = null, $to_date = null)
    {
        $wallet = $this->getOrCreateWallet($referrer_id);

        $this->db->where('referrer_id', $referrer_id);
        if (!empty($from_date)) {
            $from_ts = is_numeric($from_date) ? intval($from_date) : strtotime($from_date . ' 00:00:00');
            $this->db->where('created_at >=', $from_ts);
        }
        if (!empty($to_date)) {
            $to_ts = is_numeric($to_date) ? intval($to_date) : strtotime($to_date . ' 23:59:59');
            $this->db->where('created_at <=', $to_ts);
        }
        $this->db->order_by('id', 'asc');
        $transactions = $this->db->get('referral_wallet_transactions')->result();

        // Calculate opening balance prior to from_date
        $opening_balance = 0.00;
        if (!empty($from_date)) {
            $from_ts = is_numeric($from_date) ? intval($from_date) : strtotime($from_date . ' 00:00:00');
            $this->db->select('balance_after');
            $this->db->where('referrer_id', $referrer_id);
            $this->db->where('created_at <', $from_ts);
            $this->db->order_by('id', 'desc');
            $this->db->limit(1);
            $prev = $this->db->get('referral_wallet_transactions')->row();
            if ($prev) {
                $opening_balance = floatval($prev->balance_after);
            }
        }

        $period_earnings = 0.00;
        $period_reversals = 0.00;
        $period_withdrawals = 0.00;

        foreach ($transactions as $tx) {
            if ($tx->source == 'commission') {
                $period_earnings += floatval($tx->amount);
            } elseif ($tx->source == 'reversal') {
                $period_reversals += floatval($tx->amount);
            } elseif ($tx->source == 'withdrawal') {
                $period_withdrawals += floatval($tx->amount);
            }
        }

        $closing_balance = (!empty($transactions)) ? floatval(end($transactions)->balance_after) : (empty($from_date) ? floatval($wallet->available_balance) : $opening_balance);

        return array(
            'referrer' => $this->getReferrerById($referrer_id),
            'wallet' => $wallet,
            'opening_balance' => $opening_balance,
            'period_earnings' => $period_earnings,
            'period_reversals' => $period_reversals,
            'period_withdrawals' => $period_withdrawals,
            'earnings' => $period_earnings,
            'reversals' => $period_reversals,
            'withdrawals' => $period_withdrawals,
            'closing_balance' => $closing_balance,
            'transactions' => $transactions
        );
    }

    function getCommissionTransactions($filters = array(), $limit = 100, $start = 0)
    {
        $hospital_id = !empty($filters['hospital_id']) ? $filters['hospital_id'] : $this->session->userdata('hospital_id');

        $this->db->select('referral_commission_ledger.*, referrer.name as referrer_name, referrer.phone as referrer_phone, patient.name as patient_name, payment_category.category as item_name');
        $this->db->from('referral_commission_ledger');
        $this->db->join('referrer', 'referrer.id = referral_commission_ledger.referrer_id', 'left');
        $this->db->join('patient', 'patient.id = referral_commission_ledger.patient_id', 'left');
        $this->db->join('payment_category', 'payment_category.id = referral_commission_ledger.item_id', 'left');
        $this->db->where('referral_commission_ledger.hospital_id', $hospital_id);

        if (!empty($filters['referrer_id'])) {
            $this->db->where('referral_commission_ledger.referrer_id', $filters['referrer_id']);
        }
        if (!empty($filters['invoice_id'])) {
            $this->db->where('referral_commission_ledger.invoice_id', $filters['invoice_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('referral_commission_ledger.status', $filters['status']);
        }
        if (!empty($filters['from_date'])) {
            $from_ts = is_numeric($filters['from_date']) ? intval($filters['from_date']) : strtotime($filters['from_date'] . ' 00:00:00');
            $this->db->where('referral_commission_ledger.created_date >=', $from_ts);
        }
        if (!empty($filters['to_date'])) {
            $to_ts = is_numeric($filters['to_date']) ? intval($filters['to_date']) : strtotime($filters['to_date'] . ' 23:59:59');
            $this->db->where('referral_commission_ledger.created_date <=', $to_ts);
        }

        $this->db->order_by('referral_commission_ledger.id', 'desc');
        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }
        return $this->db->get()->result();
    }

    function getReferralReportData($filters = array())
    {
        $hospital_id = !empty($filters['hospital_id']) ? $filters['hospital_id'] : $this->session->userdata('hospital_id');

        $this->db->select('
            payment.id as invoice_id,
            payment.date as invoice_date,
            payment.patient_name,
            payment.doctor_name,
            payment.amount as gross_amount,
            payment.flat_discount,
            payment.discount,
            payment.gross_total,
            payment.amount_received,
            payment.status as payment_status,
            referrer.name as referrer_name,
            referrer.phone as referrer_phone,
            payment_items.item_name,
            payment_items.item_price,
            payment_items.quantity,
            payment_items.subtotal,
            payment_items.doctor_commission_rate,
            payment_items.doctor_commission_amount,
            payment_items.referral_commission_rate,
            payment_items.referral_commission_amount,
            payment_items.referral_commission_rate as commission_rate,
            payment_items.referral_commission_amount as commission_amount,
            referral_commission_ledger.item_price as commission_base,
            referral_commission_ledger.earned_amount,
            referral_commission_ledger.status as commission_status,
            referral_commission_ledger.status as status,
            payment.date as created_date
        ');
        $this->db->from('payment_items');
        $this->db->join('payment', 'payment.id = payment_items.payment_id', 'inner');
        $this->db->join('referrer', 'referrer.id = payment.referrer', 'left');
        $this->db->join('referral_commission_ledger', 'referral_commission_ledger.invoice_item_id = payment_items.id', 'left');
        $this->db->where('payment.hospital_id', $hospital_id);

        if (!empty($filters['referrer_id'])) {
            $this->db->where('payment.referrer', $filters['referrer_id']);
        }
        if (!empty($filters['doctor_id'])) {
            $this->db->where('payment.doctor', $filters['doctor_id']);
        }
        if (!empty($filters['patient_id'])) {
            $this->db->where('payment.patient', $filters['patient_id']);
        }
        if (!empty($filters['from_date'])) {
            $from_ts = is_numeric($filters['from_date']) ? intval($filters['from_date']) : strtotime($filters['from_date'] . ' 00:00:00');
            $this->db->where('payment.date >=', $from_ts);
        }
        if (!empty($filters['to_date'])) {
            $to_ts = is_numeric($filters['to_date']) ? intval($filters['to_date']) : strtotime($filters['to_date'] . ' 23:59:59');
            $this->db->where('payment.date <=', $to_ts);
        }
        if (!empty($filters['status'])) {
            $this->db->where('referral_commission_ledger.status', $filters['status']);
        }

        $this->db->order_by('payment.id', 'desc');
        return $this->db->get()->result();
    }

    // ==========================================
    // AUDIT LOG
    // ==========================================

    function logAction($action, $reference = null, $old_value = null, $new_value = null, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }
        $data = array(
            'user_id' => $this->ion_auth->get_user_id() ? $this->ion_auth->get_user_id() : 0,
            'action' => $action,
            'reference' => $reference,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'ip_address' => $this->input->ip_address(),
            'hospital_id' => $hospital_id,
            'created_at' => time()
        );
        $this->db->insert('referral_audit_log', $data);
    }
}
