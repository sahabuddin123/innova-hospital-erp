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

    function getWithdrawalById($id, $hospital_id = null)
    {
        $this->db->select('referral_withdrawals.*, referrer.name as referrer_name, referrer.phone as referrer_phone, referrer.type as referrer_type, referrer.address as referrer_address, referrer.email as referrer_email');
        $this->db->from('referral_withdrawals');
        $this->db->join('referrer', 'referrer.id = referral_withdrawals.referrer_id', 'left');
        $this->db->where('referral_withdrawals.id', $id);
        if (!empty($hospital_id)) {
            $this->db->where('referral_withdrawals.hospital_id', $hospital_id);
        }
        return $this->db->get()->row();
    }

    function getWithdrawalByCode($withdrawal_id, $hospital_id = null)
    {
        $this->db->select('referral_withdrawals.*, referrer.name as referrer_name, referrer.phone as referrer_phone, referrer.type as referrer_type, referrer.address as referrer_address, referrer.email as referrer_email');
        $this->db->from('referral_withdrawals');
        $this->db->join('referrer', 'referrer.id = referral_withdrawals.referrer_id', 'left');
        $this->db->where('referral_withdrawals.withdrawal_id', (string)$withdrawal_id);
        if (!empty($hospital_id)) {
            $this->db->where('referral_withdrawals.hospital_id', $hospital_id);
        }
        return $this->db->get()->row();
    }

    function getWithdrawalByAnyIdentifier($identifier, $hospital_id = null)
    {
        if (empty($identifier)) {
            return null;
        }
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        // 1. Try by numeric primary key
        if (is_numeric($identifier)) {
            $res = $this->getWithdrawalById(intval($identifier), $hospital_id);
            if (!empty($res)) {
                return $res;
            }
        }

        // 2. Try by withdrawal_id string
        $res = $this->getWithdrawalByCode((string)$identifier, $hospital_id);
        if (!empty($res)) {
            return $res;
        }

        // 3. Try looking up through referral_wallet_transactions
        $txn = null;
        if (is_numeric($identifier)) {
            $txn = $this->db->get_where('referral_wallet_transactions', array('id' => intval($identifier)))->row();
        }
        if (empty($txn)) {
            $txn = $this->db->get_where('referral_wallet_transactions', array('reference_id' => (string)$identifier))->row();
        }
        if (!empty($txn) && !empty($txn->reference_id)) {
            return $this->getWithdrawalByCode($txn->reference_id, $hospital_id);
        }

        return null;
    }

    function getWithdrawalTransactionRecord($withdrawal_id, $referrer_id = null)
    {
        $this->db->where('source', 'withdrawal');
        $this->db->where('reference_id', (string)$withdrawal_id);
        if (!empty($referrer_id)) {
            $this->db->where('referrer_id', $referrer_id);
        }
        $this->db->order_by('id', 'desc');
        return $this->db->get('referral_wallet_transactions')->row();
    }

    function getProcessorStaffName($user_id)
    {
        if (empty($user_id)) {
            return 'Hospital Admin';
        }
        $staff = $this->db->get_where('accountant', array('ion_user_id' => $user_id))->row();
        if (empty($staff) || empty($staff->name)) {
            $staff = $this->db->get_where('receptionist', array('ion_user_id' => $user_id))->row();
        }
        if (empty($staff) || empty($staff->name)) {
            $staff = $this->db->get_where('laboratorist', array('ion_user_id' => $user_id))->row();
        }
        if (empty($staff) || empty($staff->name)) {
            $staff = $this->db->get_where('pharmacist', array('ion_user_id' => $user_id))->row();
        }
        if (!empty($staff) && !empty($staff->name)) {
            return trim($staff->name);
        }

        $user = $this->db->get_where('users', array('id' => $user_id))->row();
        if (!empty($user)) {
            $full_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if (!empty($full_name)) {
                return $full_name;
            }
            if (!empty($user->username)) {
                return trim($user->username);
            }
        }
        return 'Hospital Admin';
    }

    function getWithdrawalSlipData($identifier, $hospital_id = null)
    {
        if (empty($hospital_id)) {
            $hospital_id = $this->session->userdata('hospital_id');
        }

        $withdrawal = $this->getWithdrawalByAnyIdentifier($identifier, $hospital_id);
        if (empty($withdrawal)) {
            return null;
        }

        $referrer = $this->getReferrerById($withdrawal->referrer_id, $withdrawal->hospital_id);
        $wallet = $this->getOrCreateWallet($withdrawal->referrer_id, $withdrawal->hospital_id);

        // Find corresponding wallet debit transaction
        $wallet_txn = $this->getWithdrawalTransactionRecord($withdrawal->withdrawal_id, $withdrawal->referrer_id);

        $withdrawal_amount = floatval($withdrawal->amount);
        if (!empty($wallet_txn)) {
            $remaining_balance = floatval($wallet_txn->balance_after);
            $previous_balance = round($remaining_balance + $withdrawal_amount, 2);
            $transaction_date = !empty($wallet_txn->created_at) ? $wallet_txn->created_at : (!empty($withdrawal->payment_date) ? $withdrawal->payment_date : $withdrawal->created_at);
        } else {
            $remaining_balance = floatval($wallet->available_balance);
            $previous_balance = round($remaining_balance + $withdrawal_amount, 2);
            $transaction_date = !empty($withdrawal->payment_date) ? $withdrawal->payment_date : $withdrawal->created_at;
        }

        // Processed / Authorized staff
        $processor_name = $this->getProcessorStaffName($withdrawal->processed_by);

        // Hospital settings
        $settings = $this->db->get_where('settings', array('hospital_id' => $withdrawal->hospital_id))->row();
        if (empty($settings)) {
            $this->load->model('settings/settings_model');
            $settings = $this->settings_model->getSettings();
        }

        return array(
            'withdrawal' => $withdrawal,
            'referrer' => $referrer,
            'wallet' => $wallet,
            'wallet_txn' => $wallet_txn,
            'previous_balance' => $previous_balance,
            'withdrawal_amount' => $withdrawal_amount,
            'remaining_balance' => $remaining_balance,
            'transaction_date' => $transaction_date,
            'processor_name' => $processor_name,
            'settings' => $settings
        );
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

                // If an expense was created for this withdrawal, remove it
                $this->removeWithdrawalExpense($wd);

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

            // Auto link or create expense entry
            $this->linkWithdrawalToExpense($id, $user_id, $payment_reference);

            $this->logAction('Withdrawal Paid', $wd->withdrawal_id, $wd->status, 'Paid. Ref: ' . $payment_reference, $wd->hospital_id);
        } else {
            // Pending / Approved / Processing
            $this->db->where('id', $id)->update('referral_withdrawals', array(
                'status' => $new_status,
                'admin_notes' => $admin_notes,
                'processed_by' => $user_id
            ));
            if ($new_status == 'Approved') {
                // Auto create expense on Approved
                $this->linkWithdrawalToExpense($id, $user_id, $payment_reference);
            }

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

    /**
     * Automatically links or generates an expense voucher under 'Ref Commission'
     * whenever a referral withdrawal is approved or marked paid.
     */
    public function linkWithdrawalToExpense($id, $user_id = null, $payment_reference = '')
    {
        $wd = $this->getWithdrawalById($id);
        if (empty($wd)) {
            return null;
        }

        // 1. Ensure columns exist safely in both tables
        if (!$this->db->field_exists('expense_id', 'referral_withdrawals')) {
            $this->db->query("ALTER TABLE referral_withdrawals ADD COLUMN expense_id INT NULL DEFAULT NULL AFTER admin_notes");
        }
        if (!$this->db->field_exists('pay_to', 'expense')) {
            $this->db->query("ALTER TABLE expense ADD COLUMN pay_to VARCHAR(255) NULL AFTER note");
        }
        if (!$this->db->field_exists('payer_name', 'expense')) {
            $this->db->query("ALTER TABLE expense ADD COLUMN payer_name VARCHAR(255) NULL AFTER pay_to");
        }
        if (!$this->db->field_exists('bill_date', 'expense')) {
            $this->db->query("ALTER TABLE expense ADD COLUMN bill_date VARCHAR(100) NULL AFTER payer_name");
        }

        // 2. Check if already linked
        if (!empty($wd->expense_id)) {
            $existing = $this->db->get_where('expense', array('id' => $wd->expense_id))->row();
            if (!empty($existing)) {
                if (!empty($payment_reference) && strpos($existing->note, 'Ref:') === false) {
                    $updated_note = trim($existing->note . ' Ref: ' . $payment_reference);
                    $this->db->where('id', $existing->id)->update('expense', array('note' => $updated_note));
                }
                return $existing->id;
            }
        }

        // 3. Prevent duplicate: Check if an expense already exists with this withdrawal_id in note
        $existing_by_note = $this->db->where('hospital_id', $wd->hospital_id)
            ->like('note', $wd->withdrawal_id)
            ->get('expense')
            ->row();
        if (!empty($existing_by_note)) {
            $this->db->where('id', $wd->id)->update('referral_withdrawals', array('expense_id' => $existing_by_note->id));
            return $existing_by_note->id;
        }

        // 4. Find category name (User confirmed: 'Ref Commission')
        $cat_row = $this->db->where('hospital_id', $wd->hospital_id)
            ->group_start()
                ->where('category', 'Ref Commission')
                ->or_like('category', 'Ref Commission')
                ->or_like('category', 'Referral Commission')
            ->group_end()
            ->get('expense_category')
            ->row();

        if (!empty($cat_row)) {
            $category_name = $cat_row->category;
        } else {
            // Check global / any hospital
            $cat_any = $this->db->like('category', 'Ref Commission')->get('expense_category')->row();
            if (!empty($cat_any)) {
                $category_name = $cat_any->category;
            } else {
                $category_name = 'Ref Commission';
                // Insert category for hospital so it appears in dropdowns
                $this->db->insert('expense_category', array(
                    'category' => 'Ref Commission',
                    'description' => 'Referral Commission Payout',
                    'hospital_id' => $wd->hospital_id
                ));
            }
        }

        // 5. Referrer info
        $referrer = $this->db->get_where('referrer', array('id' => $wd->referrer_id))->row();
        $ref_name = !empty($referrer->name) ? $referrer->name : (!empty($wd->referrer_name) ? $wd->referrer_name : 'Referrer #' . $wd->referrer_id);

        // 6. Payer name (disburser/approver)
        $payer_name = 'Hospital Administration';
        $acting_user_id = !empty($user_id) ? $user_id : $this->session->userdata('user_id');
        if (!empty($acting_user_id)) {
            $staff_name = $this->getProcessorStaffName($acting_user_id);
            if (!empty($staff_name) && $staff_name != 'Hospital Admin') {
                $payer_name = $staff_name;
            } else {
                $u = $this->db->get_where('users', array('id' => $acting_user_id))->row();
                if (!empty($u->username)) {
                    $payer_name = $u->username;
                }
            }
        }

        // 7. Compose note
        $note = $ref_name;
        if (!empty($wd->account_number)) {
            $note .= ' ' . $wd->account_number;
        }
        if (!empty($wd->payment_method)) {
            $note .= ' (' . $wd->payment_method . ')';
        }
        if (!empty($payment_reference)) {
            $note .= ' Ref: ' . $payment_reference;
        } elseif (!empty($wd->payment_reference)) {
            $note .= ' Ref: ' . $wd->payment_reference;
        }
        $note .= ' [' . $wd->withdrawal_id . ']';

        $time_now = time();
        $expense_data = array(
            'category' => $category_name,
            'date' => $time_now,
            'datestring' => date('d/m/y', $time_now),
            'bill_date' => date('Y-m-d', $time_now),
            'amount' => floatval($wd->amount),
            'pay_to' => $ref_name,
            'payer_name' => $payer_name,
            'note' => $note,
            'user' => !empty($acting_user_id) ? $acting_user_id : 1,
            'hospital_id' => $wd->hospital_id
        );

        $this->db->insert('expense', $expense_data);
        $expense_id = $this->db->insert_id();

        if (!empty($expense_id)) {
            $this->db->where('id', $wd->id)->update('referral_withdrawals', array('expense_id' => $expense_id));
        }

        return $expense_id;
    }

    /**
     * Removes auto-generated expense voucher if a withdrawal request is rejected.
     */
    public function removeWithdrawalExpense($wd)
    {
        if (is_numeric($wd)) {
            $wd = $this->getWithdrawalById($wd);
        }
        if (empty($wd)) {
            return;
        }

        if (!empty($wd->expense_id)) {
            $this->db->where('id', $wd->expense_id)->delete('expense');
            $this->db->where('id', $wd->id)->update('referral_withdrawals', array('expense_id' => null));
        } else {
            if (!empty($wd->withdrawal_id)) {
                $this->db->where('hospital_id', $wd->hospital_id)
                    ->like('note', $wd->withdrawal_id)
                    ->delete('expense');
            }
        }
    }

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

    // ==========================================
    // PHASE 5: REFERRAL REPORTING & RECONCILIATION
    // ==========================================


    public function resolveHospitalId($hospital_id = null)
    {
        if (!empty($hospital_id)) return $hospital_id;
        if ($this->session && $this->session->userdata('hospital_id')) {
            return $this->session->userdata('hospital_id');
        }
        return 98;
    }

    /**
     * 1. REFERRAL COMMISSION SUMMARY
     */
    public function getReferralCommissionSummaryReport($start_time, $end_time, $referrer_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        // Fetch referrers
        $this->db->reset_query();
        $this->db->select("r.*, rw.total_earned, rw.pending_commission, rw.available_balance, rw.total_withdrawn");
        $this->db->from('referrer r');
        $this->db->join('referral_wallet rw', 'rw.referrer_id = r.id', 'left');
        $this->db->where("(r.hospital_id = '$hospital_id' OR r.hospital_id IS NULL)", NULL, FALSE);
        if (!empty($referrer_id)) {
            $this->db->where('r.id', $referrer_id);
        }
        $this->db->order_by('r.name', 'ASC');
        $referrers = $this->db->get()->result();

        // Invoices with referrer in period
        $this->db->reset_query();
        $this->db->select("p.id as invoice_id, p.referrer, p.amount as gross_amount,
                           COALESCE(p.flat_discount, p.discount, 0) as discount_amount,
                           p.gross_total, p.hospital_amount as hospital_share,
                           p.referral_amount as referral_entitlement,
                           COALESCE(p.amount_received, 0) as amount_received,
                           p.status");
        $this->db->from('payment p');
        $this->db->where("(p.hospital_id = '$hospital_id' OR p.hospital_id IS NULL)", NULL, FALSE);
        $this->db->where("p.referrer IS NOT NULL AND p.referrer != '' AND p.referrer != '0'", NULL, FALSE);
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);
        if (!empty($referrer_id)) {
            $this->db->where('p.referrer', $referrer_id);
        }
        $invoices = $this->db->get()->result();

        // Wallet transactions in period
        $this->db->reset_query();
        $this->db->select("rwt.*");
        $this->db->from('referral_wallet_transactions rwt');
        $this->db->where('rwt.hospital_id', $hospital_id);
        $this->db->where('rwt.created_at >=', $start_time);
        $this->db->where('rwt.created_at <=', $end_time);
        if (!empty($referrer_id)) {
            $this->db->where('rwt.referrer_id', $referrer_id);
        }
        $transactions = $this->db->get()->result();

        // Group transactions by referrer
        $tx_by_ref = [];
        $tot_credited_all = 0.00;
        $tot_withdrawn_all = 0.00;
        $tot_adj_all = 0.00;
        $tot_rev_all = 0.00;

        foreach ($transactions as $tx) {
            $amt = floatval($tx->amount);
            $r_id = $tx->referrer_id;
            if ($tx->type === 'credit' && $tx->source === 'commission') {
                $tx_by_ref[$r_id]['credited'] = ($tx_by_ref[$r_id]['credited'] ?? 0.00) + $amt;
                $tot_credited_all += $amt;
            } elseif ($tx->source === 'withdrawal') {
                $tx_by_ref[$r_id]['withdrawn'] = ($tx_by_ref[$r_id]['withdrawn'] ?? 0.00) + $amt;
                $tot_withdrawn_all += $amt;
            } elseif ($tx->source === 'adjustment') {
                $tx_by_ref[$r_id]['adjustment'] = ($tx_by_ref[$r_id]['adjustment'] ?? 0.00) + $amt;
                $tot_adj_all += $amt;
            } elseif ($tx->source === 'reversal') {
                $tx_by_ref[$r_id]['reversal'] = ($tx_by_ref[$r_id]['reversal'] ?? 0.00) + $amt;
                $tot_rev_all += $amt;
            }
        }

        // Group invoices by referrer
        $inv_by_ref = [];
        foreach ($invoices as $inv) {
            $inv_by_ref[$inv->referrer][] = $inv;
        }

        $records = [];
        $tot_gen_all = 0.00;
        $tot_elig_all = 0.00;
        $tot_avail_all = 0.00;
        $tot_pending_all = 0.00;
        $tot_gross_all = 0.00;
        $tot_net_all = 0.00;
        $tot_coll_all = 0.00;

        foreach ($referrers as $r) {
            $r_id = $r->id;
            $ref_invs = $inv_by_ref[$r_id] ?? [];

            $gross_billed = 0.00;
            $net_payable = 0.00;
            $collected = 0.00;
            $gen_comm = 0.00;
            $elig_comm = 0.00;

            foreach ($ref_invs as $inv) {
                $gross_billed += floatval($inv->gross_amount);
                $net_payable += floatval($inv->gross_total);
                $inv_coll = floatval($inv->amount_received);
                $collected += $inv_coll;

                $r_ent = floatval($inv->referral_entitlement);
                $h_share = floatval($inv->hospital_share);
                $gen_comm += $r_ent;

                // Hospital Share First recovery rule:
                $surplus = max(0.00, $inv_coll - $h_share);
                $elig_comm += min($r_ent, $surplus);
            }

            $credited = $tx_by_ref[$r_id]['credited'] ?? 0.00;
            $withdrawn = $tx_by_ref[$r_id]['withdrawn'] ?? 0.00;
            $avail_bal = floatval($r->available_balance);
            $pending_comm = max(0.00, round($gen_comm - $credited, 2));

            $tot_gen_all += $gen_comm;
            $tot_elig_all += $elig_comm;
            $tot_avail_all += $avail_bal;
            $tot_pending_all += $pending_comm;
            $tot_gross_all += $gross_billed;
            $tot_net_all += $net_payable;
            $tot_coll_all += $collected;

            $records[] = [
                'referrer_id' => $r_id,
                'referrer_name' => $r->name,
                'phone' => $r->phone,
                'type' => $r->type,
                'status' => $r->status,
                'total_invoices' => count($ref_invs),
                'gross_billing' => $gross_billed,
                'net_billing' => $net_payable,
                'total_collected' => $collected,
                'generated_commission' => $gen_comm,
                'eligible_commission' => $elig_comm,
                'credited_commission' => $credited,
                'withdrawn_commission' => $withdrawn,
                'available_balance' => $avail_bal,
                'pending_commission' => $pending_comm
            ];
        }

        return [
            'records' => $records,
            'summary' => [
                'total_generated_commission' => $tot_gen_all,
                'total_eligible_commission' => $tot_elig_all,
                'total_commission_credited' => $tot_credited_all,
                'total_commission_withdrawn' => $tot_withdrawn_all,
                'available_wallet_balance' => $tot_avail_all,
                'pending_commission' => $tot_pending_all,
                'commission_adjustments' => $tot_adj_all,
                'commission_reversed' => $tot_rev_all,
                'total_invoices' => count($invoices),
                'total_gross_billing' => $tot_gross_all,
                'total_net_billing' => $tot_net_all,
                'total_collected' => $tot_coll_all
            ]
        ];
    }

    /**
     * 2. REFERRER-WISE COMMISSION REPORT
     */
    public function getReferrerWiseCommissionReport($start_time, $end_time, $referrer_id = null, $status = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("r.*, rw.total_earned, rw.pending_commission, rw.available_balance, rw.total_withdrawn");
        $this->db->from('referrer r');
        $this->db->join('referral_wallet rw', 'rw.referrer_id = r.id', 'left');
        $this->db->where("(r.hospital_id = '$hospital_id' OR r.hospital_id IS NULL)", NULL, FALSE);
        if (!empty($referrer_id)) {
            $this->db->where('r.id', $referrer_id);
        }
        if (!empty($status) && strtolower($status) !== 'all') {
            $this->db->where('r.status', $status);
        }
        $this->db->order_by('r.name', 'ASC');
        $referrers = $this->db->get()->result();

        // Invoices with patient details
        $this->db->reset_query();
        $this->db->select("p.id as invoice_id, p.referrer, p.patient, p.amount as gross_amount,
                           COALESCE(p.flat_discount, p.discount, 0) as discount_amount,
                           p.gross_total, p.hospital_amount as hospital_share,
                           p.referral_amount as referral_entitlement,
                           COALESCE(p.amount_received, 0) as amount_received");
        $this->db->from('payment p');
        $this->db->where("(p.hospital_id = '$hospital_id' OR p.hospital_id IS NULL)", NULL, FALSE);
        $this->db->where("p.referrer IS NOT NULL AND p.referrer != '' AND p.referrer != '0'", NULL, FALSE);
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);
        $invoices = $this->db->get()->result();

        // Wallet transactions
        $this->db->reset_query();
        $this->db->select("rwt.*");
        $this->db->from('referral_wallet_transactions rwt');
        $this->db->where('rwt.hospital_id', $hospital_id);
        $this->db->where('rwt.created_at >=', $start_time);
        $this->db->where('rwt.created_at <=', $end_time);
        $transactions = $this->db->get()->result();

        $tx_by_ref = [];
        foreach ($transactions as $tx) {
            $amt = floatval($tx->amount);
            $r_id = $tx->referrer_id;
            if ($tx->type === 'credit' && $tx->source === 'commission') {
                $tx_by_ref[$r_id]['credited'] = ($tx_by_ref[$r_id]['credited'] ?? 0.00) + $amt;
            } elseif ($tx->source === 'withdrawal') {
                $tx_by_ref[$r_id]['withdrawn'] = ($tx_by_ref[$r_id]['withdrawn'] ?? 0.00) + $amt;
            }
        }

        $inv_by_ref = [];
        foreach ($invoices as $inv) {
            $inv_by_ref[$inv->referrer][] = $inv;
        }

        $records = [];
        $total_gross = 0.00;
        $total_disc = 0.00;
        $total_net = 0.00;
        $total_gen = 0.00;
        $total_elig = 0.00;
        $total_cred = 0.00;
        $total_wth = 0.00;
        $total_avail = 0.00;

        foreach ($referrers as $r) {
            $r_id = $r->id;
            $ref_invs = $inv_by_ref[$r_id] ?? [];

            $patients = [];
            $gross = 0.00;
            $disc = 0.00;
            $net = 0.00;
            $gen = 0.00;
            $elig = 0.00;

            foreach ($ref_invs as $inv) {
                if (!empty($inv->patient)) {
                    $patients[$inv->patient] = true;
                }
                $gross += floatval($inv->gross_amount);
                $disc += floatval($inv->discount_amount);
                $net += floatval($inv->gross_total);
                $coll = floatval($inv->amount_received);

                $r_ent = floatval($inv->referral_entitlement);
                $h_share = floatval($inv->hospital_share);
                $gen += $r_ent;

                $surplus = max(0.00, $coll - $h_share);
                $elig += min($r_ent, $surplus);
            }

            $credited = $tx_by_ref[$r_id]['credited'] ?? 0.00;
            $withdrawn = $tx_by_ref[$r_id]['withdrawn'] ?? 0.00;
            $avail = floatval($r->available_balance);
            $pending = max(0.00, round($gen - $credited, 2));

            $total_gross += $gross;
            $total_disc += $disc;
            $total_net += $net;
            $total_gen += $gen;
            $total_elig += $elig;
            $total_cred += $credited;
            $total_wth += $withdrawn;
            $total_avail += $avail;

            $records[] = [
                'referrer_id' => $r_id,
                'referrer_name' => $r->name,
                'phone' => $r->phone,
                'type' => $r->type,
                'status' => $r->status,
                'total_patients' => count($patients),
                'total_invoices' => count($ref_invs),
                'gross_billing' => $gross,
                'total_discount' => $disc,
                'net_billing' => $net,
                'generated_commission' => $gen,
                'eligible_commission' => $elig,
                'credited_commission' => $credited,
                'withdrawn_commission' => $withdrawn,
                'available_balance' => $avail,
                'pending_commission' => $pending
            ];
        }

        return [
            'records' => $records,
            'summary' => [
                'referrers_count' => count($records),
                'total_gross_billing' => $total_gross,
                'total_discount' => $total_disc,
                'total_net_billing' => $total_net,
                'total_generated' => $total_gen,
                'total_eligible' => $total_elig,
                'total_credited' => $total_cred,
                'total_withdrawn' => $total_wth,
                'total_available' => $total_avail
            ]
        ];
    }

    /**
     * 3. INVOICE-WISE REFERRAL COMMISSION REPORT
     */
    public function getInvoiceWiseReferralCommissionReport($start_time, $end_time, $referrer_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("p.*, r.name as ref_name, r.phone as ref_phone, r.type as ref_type");
        $this->db->from('payment p');
        $this->db->join('referrer r', 'r.id = p.referrer', 'inner');
        $this->db->where("(p.hospital_id = '$hospital_id' OR p.hospital_id IS NULL)", NULL, FALSE);
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);
        if (!empty($referrer_id)) {
            $this->db->where('p.referrer', $referrer_id);
        }
        $this->db->order_by('p.id', 'DESC');
        $invoices = $this->db->get()->result();

        // Get credited amounts per invoice from referral_wallet_transactions
        $this->db->reset_query();
        $this->db->select("reference_id, SUM(amount) as total_credited");
        $this->db->from('referral_wallet_transactions');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('type', 'credit');
        $this->db->where('source', 'commission');
        $this->db->group_by('reference_id');
        $credits = $this->db->get()->result();

        $credited_by_ref_id = [];
        foreach ($credits as $c) {
            $credited_by_ref_id[$c->reference_id] = floatval($c->total_credited);
        }

        $records = [];
        $tot_gross = 0.00;
        $tot_disc = 0.00;
        $tot_net = 0.00;
        $tot_hosp = 0.00;
        $tot_ent = 0.00;
        $tot_coll = 0.00;
        $tot_elig = 0.00;
        $tot_cred = 0.00;

        foreach ($invoices as $inv) {
            $gross = floatval($inv->amount);
            $disc = !empty($inv->flat_discount) ? floatval($inv->flat_discount) : floatval($inv->discount);
            $net = floatval($inv->gross_total);
            $h_share = floatval($inv->hospital_amount);
            $r_ent = floatval($inv->referral_amount);
            $coll = floatval($inv->amount_received);

            // Hospital Share First Allocation:
            $h_recovered = min($h_share, $coll);
            $h_remaining = max(0.00, $h_share - $coll);
            $surplus = max(0.00, $coll - $h_share);
            $elig_comm = min($r_ent, $surplus);

            // Credited so far from wallet tx
            $ref_key = 'INV-' . $inv->id;
            $credited = $credited_by_ref_id[$ref_key] ?? 0.00;
            $rem_comm = max(0.00, round($r_ent - $credited, 2));

            // Status label
            if ($coll >= $net && $net > 0) {
                $status_desc = 'Hospital Recovered & Fully Eligible';
                $badge_class = 'success';
            } elseif ($surplus > 0) {
                $status_desc = 'Hospital Recovered & Partially Eligible';
                $badge_class = 'info';
            } elseif ($coll > 0) {
                $status_desc = 'Hospital Share Incomplete (৳' . number_format($h_remaining, 2) . ' rem)';
                $badge_class = 'warning';
            } else {
                $status_desc = 'Unpaid (Pending Collection)';
                $badge_class = 'secondary';
            }

            $tot_gross += $gross;
            $tot_disc += $disc;
            $tot_net += $net;
            $tot_hosp += $h_share;
            $tot_ent += $r_ent;
            $tot_coll += $coll;
            $tot_elig += $elig_comm;
            $tot_cred += $credited;

            $records[] = [
                'invoice_id' => $inv->id,
                'invoice_date' => $inv->date,
                'patient_name' => !empty($inv->patient_name) ? $inv->patient_name : 'Patient #' . $inv->patient,
                'referrer_name' => $inv->ref_name,
                'referrer_id' => $inv->referrer,
                'gross_amount' => $gross,
                'discount' => $disc,
                'net_payable' => $net,
                'hospital_share' => $h_share,
                'hospital_recovered' => $h_recovered,
                'hospital_remaining' => $h_remaining,
                'referral_entitlement' => $r_ent,
                'amount_collected' => $coll,
                'eligible_commission' => $elig_comm,
                'credited_commission' => $credited,
                'remaining_commission' => $rem_comm,
                'allocation_status' => $status_desc,
                'badge_class' => $badge_class
            ];
        }

        return [
            'records' => $records,
            'summary' => [
                'total_invoices' => count($records),
                'total_gross' => $tot_gross,
                'total_discount' => $tot_disc,
                'total_net' => $tot_net,
                'total_hospital_share' => $tot_hosp,
                'total_referral_entitlement' => $tot_ent,
                'total_collected' => $tot_coll,
                'total_eligible_commission' => $tot_elig,
                'total_credited_commission' => $tot_cred
            ]
        ];
    }

    /**
     * 4. TEST-WISE REFERRAL COMMISSION REPORT
     */
    public function getTestWiseReferralCommissionReport($start_time, $end_time, $referrer_id = null, $category_id = null, $status = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("pi.*, p.id as invoice_id, p.date as invoice_date, p.referrer, r.name as referrer_name,
                           p.gross_total as invoice_net, p.amount_received as invoice_collected,
                           p.hospital_amount as hospital_share, p.referral_amount as referral_entitlement,
                           pc.category as test_category_name");
        $this->db->from('payment_items pi');
        $this->db->join('payment p', 'p.id = pi.payment_id', 'inner');
        $this->db->join('referrer r', 'r.id = p.referrer', 'inner');
        $this->db->join('payment_category pc', 'pc.id = pi.item_id', 'left');
        $this->db->where("(p.hospital_id = '$hospital_id' OR p.hospital_id IS NULL)", NULL, FALSE);
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);

        if (!empty($referrer_id)) {
            $this->db->where('p.referrer', $referrer_id);
        }
        if (!empty($category_id)) {
            $this->db->where('pc.id', $category_id);
        }

        $this->db->order_by('pi.id', 'DESC');
        $items = $this->db->get()->result();

        $records = [];
        $tot_price = 0.00;
        $tot_gen = 0.00;
        $tot_elig = 0.00;

        foreach ($items as $it) {
            $price = floatval($it->subtotal);
            $rate = floatval($it->referral_commission_rate);
            $gen = floatval($it->referral_commission_amount);

            $inv_net = floatval($it->invoice_net);
            $inv_coll = floatval($it->invoice_collected);
            $h_share = floatval($it->hospital_share);
            $r_ent = floatval($it->referral_entitlement);

            // Pro-rated eligibility for this test based on hospital recovery
            $surplus = max(0.00, $inv_coll - $h_share);
            $inv_ratio = ($r_ent > 0) ? min(1.0, max(0.0, $surplus / $r_ent)) : 0.0;
            $elig = round($gen * $inv_ratio, 2);
            $pending = max(0.00, round($gen - $elig, 2));

            if ($inv_ratio >= 0.999) {
                $item_status = 'Fully Eligible';
            } elseif ($inv_ratio > 0.001) {
                $item_status = 'Partially Eligible';
            } else {
                $item_status = 'Pending Collection';
            }

            if (!empty($status) && strtolower($status) !== 'all' && strtolower($status) !== strtolower($item_status)) {
                continue;
            }

            $tot_price += $price;
            $tot_gen += $gen;
            $tot_elig += $elig;

            $records[] = [
                'item_id' => $it->item_id,
                'item_name' => $it->item_name,
                'test_category' => !empty($it->test_category_name) ? $it->test_category_name : 'Diagnostic',
                'referrer_name' => $it->referrer_name,
                'referrer_id' => $it->referrer,
                'invoice_id' => $it->invoice_id,
                'invoice_date' => $it->invoice_date,
                'test_price' => floatval($it->item_price),
                'quantity' => floatval($it->quantity),
                'subtotal' => $price,
                'commission_rate' => $rate,
                'generated_commission' => $gen,
                'eligible_commission' => $elig,
                'credited_commission' => 0.00,
                'pending_commission' => $pending,
                'commission_status' => $item_status
            ];
        }

        return [
            'records' => $records,
            'summary' => [
                'total_tests_billed' => count($records),
                'total_test_amount' => $tot_price,
                'total_generated_commission' => $tot_gen,
                'total_eligible_commission' => $tot_elig
            ]
        ];
    }

    /**
     * 5. REFERRAL COMMISSION PAYABLE REPORT
     */
    public function getReferralCommissionPayableReport($start_time, $end_time, $referrer_id = null, $status = null, $hospital_id = null)
    {
        $inv_data = $this->getInvoiceWiseReferralCommissionReport($start_time, $end_time, $referrer_id, $hospital_id);
        $records = [];
        $total_payable_sum = 0.00;
        $total_elig_sum = 0.00;
        $total_ent_sum = 0.00;

        foreach ($inv_data['records'] as $r) {
            $ent = $r['referral_entitlement'];
            $elig = $r['eligible_commission'];
            $cred = $r['credited_commission'];
            $rem_payable = max(0.00, round($elig - $cred, 2));

            if ($cred >= $elig && $elig > 0) {
                $p_status = 'Fully Credited';
            } elseif ($cred > 0) {
                $p_status = 'Partially Credited';
            } elseif ($elig > 0) {
                $p_status = 'Eligible (Uncredited)';
            } else {
                $p_status = 'Pending Collection';
            }

            if (!empty($status) && strtolower($status) !== 'all' && strtolower($status) !== strtolower($p_status)) {
                continue;
            }

            $total_payable_sum += $rem_payable;
            $total_elig_sum += $elig;
            $total_ent_sum += $ent;

            $records[] = [
                'referrer_name' => $r['referrer_name'],
                'referrer_id' => $r['referrer_id'],
                'invoice_id' => $r['invoice_id'],
                'invoice_date' => $r['invoice_date'],
                'net_invoice_amount' => $r['net_payable'],
                'hospital_share' => $r['hospital_share'],
                'referral_entitlement' => $ent,
                'eligible_commission' => $elig,
                'already_credited' => $cred,
                'remaining_payable' => $rem_payable,
                'status' => $p_status
            ];
        }

        return [
            'records' => $records,
            'summary' => [
                'total_qualifying_invoices' => count($records),
                'total_referral_entitlement' => $total_ent_sum,
                'total_eligible_commission' => $total_elig_sum,
                'total_uncredited_payable' => $total_payable_sum
            ]
        ];
    }

    /**
     * 6. REFERRER WALLET REPORT (RECONCILED)
     */
    public function getReferrerWalletAuditReport($hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("r.*, rw.total_earned, rw.pending_commission, rw.available_balance, rw.pending_withdrawal, rw.total_withdrawn");
        $this->db->from('referrer r');
        $this->db->join('referral_wallet rw', 'rw.referrer_id = r.id', 'left');
        $this->db->where("(r.hospital_id = '$hospital_id' OR r.hospital_id IS NULL)", NULL, FALSE);
        $referrers = $this->db->get()->result();

        // Get ledger transaction sums per referrer
        $this->db->reset_query();
        $this->db->select("referrer_id,
                           SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_credits,
                           SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_debits,
                           SUM(CASE WHEN source = 'withdrawal' THEN amount ELSE 0 END) as ledger_withdrawn,
                           SUM(CASE WHEN source = 'adjustment' THEN amount ELSE 0 END) as ledger_adjustments,
                           SUM(CASE WHEN source = 'reversal' THEN amount ELSE 0 END) as ledger_reversals");
        $this->db->from('referral_wallet_transactions');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->group_by('referrer_id');
        $ledger_sums = $this->db->get()->result();

        $sums_by_ref = [];
        foreach ($ledger_sums as $ls) {
            $sums_by_ref[$ls->referrer_id] = $ls;
        }

        $records = [];
        $total_wallet_avail = 0.00;
        $total_calculated_avail = 0.00;
        $total_variance = 0.00;
        $total_withdrawn_all = 0.00;

        foreach ($referrers as $r) {
            $r_id = $r->id;
            $ls = $sums_by_ref[$r_id] ?? null;

            $credits = $ls ? floatval($ls->total_credits) : 0.00;
            $debits = $ls ? floatval($ls->total_debits) : 0.00;
            $withdrawn = $ls ? floatval($ls->ledger_withdrawn) : floatval($r->total_withdrawn);
            $adj = $ls ? floatval($ls->ledger_adjustments) : 0.00;
            $rev = $ls ? floatval($ls->ledger_reversals) : 0.00;

            $wallet_bal = floatval($r->available_balance);
            $calc_bal = round($credits - $debits, 2);
            $variance = round($wallet_bal - $calc_bal, 2);

            $total_wallet_avail += $wallet_bal;
            $total_calculated_avail += $calc_bal;
            $total_variance += abs($variance);
            $total_withdrawn_all += $withdrawn;

            $records[] = [
                'referrer_id' => $r_id,
                'referrer_name' => $r->name,
                'phone' => $r->phone,
                'type' => $r->type,
                'status' => $r->status,
                'opening_balance' => 0.00,
                'total_credits' => $credits,
                'total_debits' => $debits,
                'total_withdrawn' => $withdrawn,
                'adjustments' => $adj,
                'reversals' => $rev,
                'available_balance' => $wallet_bal,
                'calculated_balance' => $calc_bal,
                'variance' => $variance,
                'is_reconciled' => (abs($variance) < 0.01)
            ];
        }

        return [
            'records' => $records,
            'summary' => [
                'total_referrers' => count($records),
                'total_wallet_balance' => $total_wallet_avail,
                'total_calculated_balance' => $total_calculated_avail,
                'total_reconciliation_variance' => $total_variance,
                'total_withdrawn' => $total_withdrawn_all
            ]
        ];
    }

    /**
     * 7. REFERRAL COMMISSION LEDGER
     */
    public function getReferralCommissionLedgerReport($start_time, $end_time, $referrer_id = null, $source = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("rwt.*, r.name as referrer_name");
        $this->db->from('referral_wallet_transactions rwt');
        $this->db->join('referrer r', 'r.id = rwt.referrer_id', 'inner');
        $this->db->where('rwt.hospital_id', $hospital_id);
        $this->db->where('rwt.created_at >=', $start_time);
        $this->db->where('rwt.created_at <=', $end_time);

        if (!empty($referrer_id)) {
            $this->db->where('rwt.referrer_id', $referrer_id);
        }
        if (!empty($source) && strtolower($source) !== 'all') {
            $this->db->where('rwt.source', $source);
        }

        $this->db->order_by('rwt.id', 'ASC');
        $txs = $this->db->get()->result();

        return [
            'records' => $txs,
            'summary' => [
                'total_entries' => count($txs)
            ]
        ];
    }

    /**
     * 8. REFERRAL WITHDRAWAL REPORT
     */
    public function getReferralWithdrawalReport($start_time, $end_time, $referrer_id = null, $payment_method = null, $status = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("rw.*, r.name as referrer_name, r.phone as referrer_phone, u.username as processed_by_user");
        $this->db->from('referral_withdrawals rw');
        $this->db->join('referrer r', 'r.id = rw.referrer_id', 'inner');
        $this->db->join('users u', 'u.id = rw.processed_by', 'left');
        $this->db->where('rw.hospital_id', $hospital_id);
        $this->db->where('rw.created_at >=', $start_time);
        $this->db->where('rw.created_at <=', $end_time);

        if (!empty($referrer_id)) {
            $this->db->where('rw.referrer_id', $referrer_id);
        }
        if (!empty($payment_method) && strtolower($payment_method) !== 'all') {
            $this->db->where('rw.payment_method', $payment_method);
        }
        if (!empty($status) && strtolower($status) !== 'all') {
            $this->db->where('rw.status', $status);
        }

        $this->db->order_by('rw.id', 'DESC');
        $withdrawals = $this->db->get()->result();

        $tot_withdrawn = 0.00;
        $by_method = [];

        foreach ($withdrawals as $w) {
            $amt = floatval($w->amount);
            $tot_withdrawn += $amt;
            $by_method[$w->payment_method] = ($by_method[$w->payment_method] ?? 0.00) + $amt;
        }

        return [
            'records' => $withdrawals,
            'summary' => [
                'total_withdrawal_amount' => $tot_withdrawn,
                'number_of_withdrawals' => count($withdrawals),
                'by_payment_method' => $by_method
            ]
        ];
    }

    /**
     * 9. REFERRAL ADJUSTMENT & REVERSAL REPORT
     */
    public function getReferralAdjustmentReport($start_time, $end_time, $referrer_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("rwt.*, r.name as referrer_name, u.username as processor_name");
        $this->db->from('referral_wallet_transactions rwt');
        $this->db->join('referrer r', 'r.id = rwt.referrer_id', 'inner');
        $this->db->join('users u', 'u.id = rwt.created_by', 'left');
        $this->db->where('rwt.hospital_id', $hospital_id);
        $this->db->where_in('rwt.source', ['adjustment', 'reversal', 'withdrawal_refund']);
        $this->db->where('rwt.created_at >=', $start_time);
        $this->db->where('rwt.created_at <=', $end_time);

        if (!empty($referrer_id)) {
            $this->db->where('rwt.referrer_id', $referrer_id);
        }

        $this->db->order_by('rwt.id', 'DESC');
        $adjustments = $this->db->get()->result();

        $tot_adj = 0.00;
        foreach ($adjustments as $a) {
            $tot_adj += floatval($a->amount);
        }

        return [
            'records' => $adjustments,
            'summary' => [
                'total_adjustments_count' => count($adjustments),
                'total_adjusted_amount' => $tot_adj
            ]
        ];
    }

    /**
     * 10. REFERRAL DISCOUNT RECONCILIATION REPORT
     * Audits discount absorption rule: discount reduces referrer entitlement first.
     */
    public function getReferralDiscountReconciliationReport($start_time, $end_time, $referrer_id = null, $hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $this->db->reset_query();
        $this->db->select("p.*, r.name as referrer_name");
        $this->db->from('payment p');
        $this->db->join('referrer r', 'r.id = p.referrer', 'left');
        $this->db->where("(p.hospital_id = '$hospital_id' OR p.hospital_id IS NULL)", NULL, FALSE);
        $this->db->where('p.date >=', $start_time);
        $this->db->where('p.date <=', $end_time);
        $this->db->group_start();
        $this->db->where('p.flat_discount >', 0);
        $this->db->or_where('p.discount >', 0);
        $this->db->group_end();

        if (!empty($referrer_id)) {
            $this->db->where('p.referrer', $referrer_id);
        }

        $this->db->order_by('p.id', 'DESC');
        $discount_invoices = $this->db->get()->result();

        $records = [];
        $tot_gross = 0.00;
        $tot_disc = 0.00;
        $tot_net = 0.00;
        $tot_hosp = 0.00;
        $tot_ref_ent = 0.00;

        foreach ($discount_invoices as $inv) {
            $gross = floatval($inv->amount);
            $disc = !empty($inv->flat_discount) ? floatval($inv->flat_discount) : floatval($inv->discount);
            $net = floatval($inv->gross_total);
            $h_share = floatval($inv->hospital_amount);
            $r_ent = floatval($inv->referral_amount);

            $has_referrer = !empty($inv->referrer) && !empty($inv->referrer_name);
            $gross_ref_comm = $has_referrer ? ($r_ent + $disc) : 0.00;
            $disc_absorbed_by_ref = $has_referrer ? min($disc, $gross_ref_comm) : 0.00;

            if ($has_referrer) {
                if ($disc_absorbed_by_ref >= $disc) {
                    $absorption_status = 'Fully Absorbed by Referrer';
                    $badge = 'success';
                } else {
                    $absorption_status = 'Partially Absorbed by Referrer';
                    $badge = 'warning';
                }
            } else {
                $absorption_status = 'Hospital Borne (No Referrer)';
                $badge = 'secondary';
            }

            $tot_gross += $gross;
            $tot_disc += $disc;
            $tot_net += $net;
            $tot_hosp += $h_share;
            $tot_ref_ent += $r_ent;

            $records[] = [
                'invoice_id' => $inv->id,
                'invoice_date' => $inv->date,
                'patient_name' => !empty($inv->patient_name) ? $inv->patient_name : 'Patient #' . $inv->patient,
                'referrer_name' => $has_referrer ? $inv->referrer_name : 'No Referrer Selected',
                'has_referrer' => $has_referrer,
                'gross_amount' => $gross,
                'discount_applied' => $disc,
                'net_payable' => $net,
                'hospital_share' => $h_share,
                'gross_ref_commission' => $gross_ref_comm,
                'discount_deducted_from_ref' => $disc_absorbed_by_ref,
                'final_referral_entitlement' => $r_ent,
                'absorption_status' => $absorption_status,
                'badge_class' => $badge
            ];
        }

        return [
            'records' => $records,
            'summary' => [
                'discount_invoices_count' => count($records),
                'total_gross' => $tot_gross,
                'total_discount' => $tot_disc,
                'total_net' => $tot_net,
                'total_hospital_share' => $tot_hosp,
                'total_referral_entitlement' => $tot_ref_ent
            ]
        ];
    }

    /**
     * 12. REFERRAL COMMISSION RECONCILIATION DASHBOARD
     * Automated discrepancy detection engine
     */
    public function getReferralReconciliationDashboardData($hospital_id = null)
    {
        $hospital_id = $this->resolveHospitalId($hospital_id);

        $wallet_audit = $this->getReferrerWalletAuditReport($hospital_id);
        $discrepancies = [];

        // 1. Check for wallet balance mismatches vs transaction ledger
        foreach ($wallet_audit['records'] as $w) {
            if (!$w['is_reconciled']) {
                $discrepancies[] = [
                    'severity' => 'HIGH',
                    'type' => 'Wallet Balance Mismatch',
                    'reference' => 'Referrer #' . $w['referrer_id'] . ' (' . $w['referrer_name'] . ')',
                    'description' => "Wallet available balance (৳" . number_format($w['available_balance'], 2) . ") does not match ledger transaction sum (৳" . number_format($w['calculated_balance'], 2) . "). Variance: ৳" . number_format($w['variance'], 2) . ".",
                    'action_needed' => 'Inspect manual adjustments or raw wallet balance updates without corresponding transaction ledger records.'
                ];
            }
        }

        // 2. Check for duplicate wallet credits for the same invoice
        $this->db->reset_query();
        $this->db->select("reference_id, referrer_id, count(*) as credit_count, sum(amount) as credited_sum");
        $this->db->from('referral_wallet_transactions');
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('type', 'credit');
        $this->db->where('source', 'commission');
        $this->db->like('reference_id', 'INV-');
        $this->db->group_by(['reference_id', 'referrer_id']);
        $this->db->having('credit_count >', 1);
        $dup_credits = $this->db->get()->result();

        foreach ($dup_credits as $dc) {
            $inv_id = str_replace('INV-', '', $dc->reference_id);
            $pay = $this->db->get_where('payment', ['id' => $inv_id])->row();
            $max_ent = $pay ? floatval($pay->referral_amount) : 0.00;
            if (floatval($dc->credited_sum) > $max_ent + 0.01) {
                $discrepancies[] = [
                    'severity' => 'CRITICAL',
                    'type' => 'Over-Credit Above Entitlement',
                    'reference' => $dc->reference_id,
                    'description' => "Invoice has {$dc->credit_count} wallet credits totaling ৳" . number_format($dc->credited_sum, 2) . ", which exceeds the invoice referral entitlement of ৳" . number_format($max_ent, 2) . ".",
                    'action_needed' => 'Audit partial deposit credit duplication.'
                ];
            }
        }

        // Aggregate system totals
        $this->db->reset_query();
        $this->db->select_sum('referral_amount');
        $this->db->where('hospital_id', $hospital_id);
        $total_entitlement = floatval($this->db->get('payment')->row()->referral_amount);

        $this->db->reset_query();
        $this->db->select("SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) as tot_cred,
                           SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) as tot_deb,
                           SUM(CASE WHEN source='withdrawal' THEN amount ELSE 0 END) as tot_wth,
                           SUM(CASE WHEN source='adjustment' THEN amount ELSE 0 END) as tot_adj,
                           SUM(CASE WHEN source='reversal' THEN amount ELSE 0 END) as tot_rev");
        $this->db->from('referral_wallet_transactions');
        $this->db->where('hospital_id', $hospital_id);
        $tx_totals = $this->db->get()->row();

        return [
            'discrepancies' => $discrepancies,
            'summary' => [
                'total_referral_entitlement' => $total_entitlement,
                'total_wallet_credit' => floatval($tx_totals->tot_cred),
                'total_wallet_debit' => floatval($tx_totals->tot_deb),
                'total_withdrawals' => floatval($tx_totals->tot_wth),
                'total_adjustments' => floatval($tx_totals->tot_adj),
                'total_reversals' => floatval($tx_totals->tot_rev),
                'total_available_wallet_balance' => $wallet_audit['summary']['total_wallet_balance'],
                'discrepancy_count' => count($discrepancies)
            ]
        ];
    }

}
