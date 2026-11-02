<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RBAC (Role-Based Access Control) Library
 * 
 * Standardizes granular role-based permissions across:
 * - 9 Report Sub-menus
 * - 10 Standard Permission Actions (VIEW, PRINT, PDF, EXCEL, CSV, etc.)
 * - Phase 11 Centralized Export Engine
 * - Sensitive Data Masking & Forensic Audit Trail
 * 
 * @package    Lifecare Hospital ERP
 * @subpackage Libraries
 */
class Rbac
{
    protected $ci;
    protected $user_groups_cache = [];
    protected $permissions_cache = [];

    /**
     * Master mapping: Report Key -> Permission Group
     */
    protected $report_group_map = [
        // =================================================================
        // 1. FINANCIAL DASHBOARD
        // =================================================================
        'financial_performance'            => 'financial_dashboard',
        'financial_report'                 => 'financial_dashboard',
        'collection_summary'               => 'financial_dashboard',
        'expense_vs_income'                => 'financial_dashboard',
        'pnl_dashboard'                    => 'financial_dashboard',
        'financial_dashboard'              => 'financial_dashboard',

        // =================================================================
        // 2. INCOME & COLLECTION REPORTS
        // =================================================================
        'daily_collection'                 => 'income_collection',
        'monthly_collection'               => 'income_collection',
        'due_collection'                   => 'income_collection',
        'partial_payment'                  => 'income_collection',
        'advance_payment'                  => 'income_collection',
        'payment_method'                   => 'income_collection',
        'cashier_collection'               => 'income_collection',
        'discount_report'                  => 'income_collection',
        'refund_report'                    => 'income_collection',
        'income_collection'                => 'income_collection',

        // =================================================================
        // 3. EXPENSE & ACCOUNTS REPORTS
        // =================================================================
        'daily_expense'                    => 'expense_accounts',
        'monthly_expense'                  => 'expense_accounts',
        'expense_ledger'                   => 'expense_accounts',
        'expense_category'                 => 'expense_accounts',
        'cash_book'                        => 'expense_accounts',
        'bank_book'                        => 'expense_accounts',
        'petty_cash'                       => 'expense_accounts',
        'vendor_payment'                   => 'expense_accounts',
        'salary_report'                    => 'expense_accounts',
        'utility_bills'                    => 'expense_accounts',
        'asset_purchase'                   => 'expense_accounts',
        'expense_approval'                 => 'expense_accounts',
        'acc_general_ledger'               => 'expense_accounts',
        'acc_pnl_statement'                => 'expense_accounts',
        'acc_audit_trail'                  => 'expense_accounts',
        'expense_accounts'                 => 'expense_accounts',

        // =================================================================
        // 4. DOCTOR COMMISSION REPORTS
        // =================================================================
        'doctor_commission_summary'        => 'doctor_commission',
        'doctor_wise_commission'           => 'doctor_commission',
        'invoice_wise_commission'          => 'doctor_commission',
        'test_wise_commission'             => 'doctor_commission',
        'commission_payable'               => 'doctor_commission',
        'commission_paid'                  => 'doctor_commission',
        'doctor_commission_ledger'         => 'doctor_commission',
        'doctor_commission_statement'      => 'doctor_commission',
        'commission_adjustment'            => 'doctor_commission',
        'commission_configuration'         => 'doctor_commission',
        'doctor_commission'                => 'doctor_commission',

        // =================================================================
        // 5. REFERRAL COMMISSION REPORTS
        // =================================================================
        'referral_summary'                 => 'referral_commission',
        'referrer_wise_commission'         => 'referral_commission',
        'invoice_wise_referral'            => 'referral_commission',
        'test_wise_referral'               => 'referral_commission',
        'referral_payable'                 => 'referral_commission',
        'referrer_wallet'                  => 'referral_commission',
        'referral_ledger'                  => 'referral_commission',
        'referral_withdrawal'              => 'referral_commission',
        'referrer_statement'               => 'referral_commission',
        'referral_adjustment'              => 'referral_commission',
        'referral_reconciliation'          => 'referral_commission',
        'referral_commission'              => 'referral_commission',

        // =================================================================
        // 6. PROFIT & LOSS REPORTS
        // =================================================================
        'daily_pnl'                        => 'profit_loss',
        'monthly_pnl'                      => 'profit_loss',
        'yearly_pnl'                       => 'profit_loss',
        'collection_based_pnl'             => 'profit_loss',
        'dept_profitability'               => 'profit_loss',
        'test_profitability'               => 'profit_loss',
        'expense_breakdown'                => 'profit_loss',
        'income_vs_expense'                => 'profit_loss',
        'pnl_comparison'                   => 'profit_loss',
        'pnl_reconciliation'               => 'profit_loss',
        'profit_loss'                      => 'profit_loss',

        // =================================================================
        // 7. DIAGNOSTIC PERFORMANCE REPORTS
        // =================================================================
        'diag_dashboard'                   => 'diagnostic_performance',
        'diag_daily'                       => 'diagnostic_performance',
        'diag_monthly'                     => 'diagnostic_performance',
        'diag_yearly'                      => 'diagnostic_performance',
        'diag_test'                        => 'diagnostic_performance',
        'diag_category'                    => 'diagnostic_performance',
        'diag_department'                  => 'diagnostic_performance',
        'diag_sample'                      => 'diagnostic_performance',
        'diag_pending'                     => 'diagnostic_performance',
        'diag_verification'                => 'diagnostic_performance',
        'diag_delivery'                    => 'diagnostic_performance',
        'diag_tat'                         => 'diagnostic_performance',
        'diag_cancelled'                   => 'diagnostic_performance',
        'diag_home_collection'             => 'diagnostic_performance',
        'diag_doctor'                      => 'diagnostic_performance',
        'diag_efficiency'                  => 'diagnostic_performance',
        'diag_test_item_selected'          => 'diagnostic_performance',
        'diag_test_items_summary'          => 'diagnostic_performance',
        'birth'                            => 'diagnostic_performance',
        'operation'                        => 'diagnostic_performance',
        'expire'                           => 'diagnostic_performance',
        'diagnostic_performance'           => 'diagnostic_performance',

        // =================================================================
        // 8. FINANCIAL AUDIT LOGS
        // =================================================================
        'fin_audit_dashboard'              => 'financial_audit_logs',
        'fin_audit_invoices'               => 'financial_audit_logs',
        'fin_audit_payments'               => 'financial_audit_logs',
        'fin_audit_expenses'               => 'financial_audit_logs',
        'fin_audit_doctor_commissions'     => 'financial_audit_logs',
        'fin_audit_referral_commissions'   => 'financial_audit_logs',
        'fin_audit_cash_bank'              => 'financial_audit_logs',
        'fin_audit_trail'                  => 'financial_audit_logs',
        'financial_audit_logs'             => 'financial_audit_logs',

        // =================================================================
        // 9. USER ACTIVITY REPORTS
        // =================================================================
        'activity_dashboard'               => 'user_activity_reports',
        'login_history'                    => 'user_activity_reports',
        'logout_history'                   => 'user_activity_reports',
        'failed_logins'                    => 'user_activity_reports',
        'user_activity'                    => 'user_activity_reports',
        'module_access'                    => 'user_activity_reports',
        'data_entry'                       => 'user_activity_reports',
        'session_report'                   => 'user_activity_reports',
        'print_export'                     => 'user_activity_reports',
        'suspicious_activity'              => 'user_activity_reports',
        'user_activity_reports'            => 'user_activity_reports',
    ];

    /**
     * Standard Actions Map to DB Columns
     */
    protected $action_column_map = [
        'VIEW'                 => 'can_view',
        'CREATE'               => 'can_create',
        'UPDATE'               => 'can_update',
        'DELETE'               => 'can_delete',
        'PRINT'                => 'can_print',
        'EXPORT_PDF'           => 'can_export_pdf',
        'EXPORT_EXCEL'         => 'can_export_excel',
        'EXPORT_CSV'           => 'can_export_csv',
        'VIEW_DETAILS'         => 'can_view_details',
        'VIEW_SENSITIVE_DATA'  => 'can_view_sensitive',
        // Lowercase aliases
        'view'                 => 'can_view',
        'create'               => 'can_create',
        'update'               => 'can_update',
        'delete'               => 'can_delete',
        'print'                => 'can_print',
        'pdf'                  => 'can_export_pdf',
        'excel'                => 'can_export_excel',
        'csv'                  => 'can_export_csv',
        'details'              => 'can_view_details',
        'sensitive'            => 'can_view_sensitive',
    ];

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->ci =& get_instance();
            $this->ci->load->model('rbac_model');
            if (!isset($this->ci->ion_auth)) {
                $this->ci->load->library('Ion_auth');
            }
        }
    }

    /**
     * Master Permission Check
     * 
     * @param string   $permission_group e.g. 'income_collection', 'diagnostic_performance'
     * @param string   $action           e.g. 'VIEW', 'PRINT', 'EXPORT_EXCEL', 'VIEW_SENSITIVE_DATA'
     * @param string   $report_key       Optional specific report key or 'all'
     * @param int|null $user_id          Optional user ID (defaults to current logged-in user)
     * @return bool
     */
    public function check($permission_group, $action, $report_key = 'all', $user_id = null)
    {
        if (!$this->ci || !isset($this->ci->ion_auth)) {
            return false;
        }

        if (!$this->ci->ion_auth->logged_in()) {
            return false;
        }

        $user_id = $user_id ?: $this->ci->ion_auth->get_user_id();
        if (!$user_id) {
            return false;
        }

        // 1. Super Admin always has full unrestricted access
        if ($this->ci->ion_auth->in_group('superadmin', $user_id)) {
            return true;
        }

        // 2. Resolve Column Name
        $col = $this->action_column_map[$action] ?? null;
        if (!$col) {
            $col = strtolower($action);
            if (!in_array($col, array_values($this->action_column_map))) {
                return false;
            }
        }

        // 3. Get User Groups
        $groups = $this->getUserGroups($user_id);
        if (empty($groups)) {
            return false;
        }

        // 4. Fetch Permissions for all groups of this user
        $group_ids = array_column($groups, 'id');
        $permissions = $this->getGroupsPermissions($group_ids);

        // Check if ANY group grants this action (Union/OR rule)
        foreach ($permissions as $perm) {
            if ($perm['permission_group'] === $permission_group) {
                // If specific report_key matches, or perm applies to 'all'
                if ($perm['report_key'] === 'all' || $perm['report_key'] === $report_key) {
                    if (!empty($perm[$col]) && (int)$perm[$col] === 1) {
                        return true;
                    }
                }
            }
        }

        // 5. Default Fallbacks for Admin (if no explicit record exists yet)
        if ($this->ci->ion_auth->in_group('admin', $user_id)) {
            return true;
        }

        return false;
    }

    /**
     * Check permission using a Report Key
     * 
     * @param string   $report_key Report Key or Group Name
     * @param string   $action     Action (VIEW, PRINT, EXPORT_PDF, EXPORT_EXCEL, EXPORT_CSV, etc.)
     * @param int|null $user_id    User ID
     * @return bool
     */
    public function hasPermission($report_key, $action, $user_id = null)
    {
        $perm_group = $this->resolvePermissionGroup($report_key);
        return $this->check($perm_group, $action, $report_key, $user_id);
    }

    /**
     * Check if user can view an entire permission group (for menu visibility)
     */
    public function canViewGroup($permission_group, $user_id = null)
    {
        return $this->check($permission_group, 'VIEW', 'all', $user_id);
    }

    /**
     * Check export permission for specific format
     * 
     * @param string $report_key Report Key
     * @param string $format     Format: 'print', 'pdf', 'excel', 'xlsx', 'csv'
     * @param int|null $user_id  User ID
     * @return bool
     */
    public function canExport($report_key, $format, $user_id = null)
    {
        $fmt = strtolower(trim($format));
        if ($fmt === 'xlsx') {
            $fmt = 'excel';
        }

        $format_action_map = [
            'print' => 'PRINT',
            'pdf'   => 'EXPORT_PDF',
            'excel' => 'EXPORT_EXCEL',
            'csv'   => 'EXPORT_CSV',
        ];

        $action = $format_action_map[$fmt] ?? null;
        if (!$action) {
            return false;
        }

        // Must have VIEW permission first, then the export action permission
        if (!$this->hasPermission($report_key, 'VIEW', $user_id)) {
            return false;
        }

        return $this->hasPermission($report_key, $action, $user_id);
    }

    /**
     * Get available export format flags for UI toolbar
     * 
     * @param string $report_key
     * @param int|null $user_id
     * @return array ['print' => bool, 'pdf' => bool, 'excel' => bool, 'csv' => bool]
     */
    public function getAvailableExportActions($report_key, $user_id = null)
    {
        return [
            'print' => $this->canExport($report_key, 'print', $user_id),
            'pdf'   => $this->canExport($report_key, 'pdf', $user_id),
            'excel' => $this->canExport($report_key, 'excel', $user_id),
            'csv'   => $this->canExport($report_key, 'csv', $user_id),
        ];
    }

    /**
     * Sensitive Data Protection: Masks restricted data fields when user lacks VIEW_SENSITIVE_DATA
     * 
     * @param string     $report_key Report Identifier
     * @param array      $dataset    Data rows array (by reference)
     * @param int|null   $user_id    User ID
     * @return void
     */
    public function sanitizeDataset($report_key, array &$dataset, $user_id = null)
    {
        if (empty($dataset)) {
            return;
        }

        // If user has sensitive data permission, leave data untouched
        if ($this->hasPermission($report_key, 'VIEW_SENSITIVE_DATA', $user_id)) {
            return;
        }

        // Sensitive field names to mask
        $sensitive_masks = [
            'patient_phone' => function ($val) {
                if (empty($val)) return $val;
                return substr($val, 0, 3) . '***' . substr($val, -2);
            },
            'phone' => function ($val) {
                if (empty($val)) return $val;
                return substr($val, 0, 3) . '***' . substr($val, -2);
            },
            'mobile' => function ($val) {
                if (empty($val)) return $val;
                return substr($val, 0, 3) . '***' . substr($val, -2);
            },
            'patient_email' => function ($val) {
                if (empty($val)) return $val;
                $parts = explode('@', $val);
                return substr($parts[0], 0, 1) . '***@***.' . (isset($parts[1]) ? substr(strrchr($parts[1], '.'), 1) : 'com');
            },
            'email' => function ($val) {
                if (empty($val)) return $val;
                $parts = explode('@', $val);
                return substr($parts[0], 0, 1) . '***@***.' . (isset($parts[1]) ? substr(strrchr($parts[1], '.'), 1) : 'com');
            },
            'before_value' => function ($val) {
                return '[RESTRICTED]';
            },
            'after_value' => function ($val) {
                return '[RESTRICTED]';
            },
            'ip_address' => function ($val) {
                return 'xxx.xxx.xxx.xxx';
            },
            'wallet_balance' => function ($val) {
                return '****';
            },
            'bank_account' => function ($val) {
                return '****';
            },
            'account_no' => function ($val) {
                return '****';
            }
        ];

        foreach ($dataset as &$row) {
            foreach ($sensitive_masks as $field => $mask_fn) {
                if (is_object($row) && isset($row->$field)) {
                    $row->$field = $mask_fn($row->$field);
                } elseif (is_array($row) && isset($row[$field])) {
                    $row[$field] = $mask_fn($row[$field]);
                }
            }
        }
    }

    /**
     * Resolve Permission Group from report key
     */
    public function resolvePermissionGroup($report_key)
    {
        $clean_key = strtolower(trim($report_key));

        if (isset($this->report_group_map[$clean_key])) {
            return $this->report_group_map[$clean_key];
        }

        // Fuzzy fallback based on keyword
        if (strpos($clean_key, 'collection') !== false || strpos($clean_key, 'due') !== false || strpos($clean_key, 'payment') !== false || strpos($clean_key, 'refund') !== false || strpos($clean_key, 'discount') !== false) {
            return 'income_collection';
        }
        if (strpos($clean_key, 'expense') !== false || strpos($clean_key, 'bank') !== false || strpos($clean_key, 'cash') !== false || strpos($clean_key, 'salary') !== false || strpos($clean_key, 'vendor') !== false) {
            return 'expense_accounts';
        }
        if (strpos($clean_key, 'doctor_comm') !== false || strpos($clean_key, 'doctor_wise') !== false) {
            return 'doctor_commission';
        }
        if (strpos($clean_key, 'referral') !== false || strpos($clean_key, 'referrer') !== false || strpos($clean_key, 'wallet') !== false) {
            return 'referral_commission';
        }
        if (strpos($clean_key, 'pnl') !== false || strpos($clean_key, 'profit') !== false) {
            return 'profit_loss';
        }
        if (strpos($clean_key, 'diag') !== false || strpos($clean_key, 'lab') !== false || strpos($clean_key, 'test') !== false || in_array($clean_key, ['birth', 'operation', 'expire'])) {
            return 'diagnostic_performance';
        }
        if (strpos($clean_key, 'fin_audit') !== false || strpos($clean_key, 'audit_trail') !== false) {
            return 'financial_audit_logs';
        }
        if (strpos($clean_key, 'activity') !== false || strpos($clean_key, 'login') !== false || strpos($clean_key, 'session') !== false || strpos($clean_key, 'module_access') !== false) {
            return 'user_activity_reports';
        }

        return 'financial_dashboard';
    }

    /**
     * Get user's assigned groups with memory caching
     */
    public function getUserGroups($user_id)
    {
        if (!isset($this->user_groups_cache[$user_id])) {
            $user_groups = $this->ci->ion_auth->get_users_groups($user_id)->result_array();
            $this->user_groups_cache[$user_id] = $user_groups;
        }
        return $this->user_groups_cache[$user_id];
    }

    /**
     * Get permissions for group IDs with memory caching
     */
    protected function getGroupsPermissions(array $group_ids)
    {
        $cache_key = implode(',', $group_ids);
        if (!isset($this->permissions_cache[$cache_key])) {
            $this->permissions_cache[$cache_key] = $this->ci->rbac_model->getPermissionsByGroupIds($group_ids);
        }
        return $this->permissions_cache[$cache_key];
    }

    /**
     * Clear memory cache
     */
    public function clearCache()
    {
        $this->user_groups_cache = [];
        $this->permissions_cache = [];
    }

    /**
     * Get master permission groups list
     */
    public function getPermissionGroups()
    {
        return [
            'financial_dashboard'    => [
                'name'        => 'Financial Dashboard',
                'description' => 'Executive financial overview, performance metrics, and high-level KPIs',
                'icon'        => 'fas fa-tachometer-alt',
                'badge'       => 'badge-primary'
            ],
            'income_collection'      => [
                'name'        => 'Income & Collection Reports',
                'description' => 'Itemized billing, patient collections, dues, partial/advance deposits, and refunds',
                'icon'        => 'fas fa-hand-holding-usd',
                'badge'       => 'badge-success'
            ],
            'expense_accounts'       => [
                'name'        => 'Expense & Accounts Reports',
                'description' => 'Operating expenses, vendor payments, cash/bank books, and payroll disbursements',
                'icon'        => 'fas fa-money-bill-wave',
                'badge'       => 'badge-danger'
            ],
            'doctor_commission'      => [
                'name'        => 'Doctor Commission Reports',
                'description' => 'Doctor-wise earnings, commission payable/paid, and commission statements',
                'icon'        => 'fas fa-user-md',
                'badge'       => 'badge-info'
            ],
            'referral_commission'    => [
                'name'        => 'Referral Commission Reports',
                'description' => 'Agent commissions, referrer wallet balances, withdrawals, and reconciliation',
                'icon'        => 'fas fa-users-cog',
                'badge'       => 'badge-warning'
            ],
            'profit_loss'            => [
                'name'        => 'Profit & Loss Reports',
                'description' => 'Collection vs accrual performance, department profitability, and income vs expense',
                'icon'        => 'fas fa-chart-pie',
                'badge'       => 'badge-secondary'
            ],
            'diagnostic_performance' => [
                'name'        => 'Diagnostic Performance Reports',
                'description' => 'Lab workflow, test volume, turnaround time, sample tracking, and clinical outcomes',
                'icon'        => 'fas fa-microscope',
                'badge'       => 'badge-info'
            ],
            'financial_audit_logs'   => [
                'name'        => 'Financial Audit Logs',
                'description' => 'Forensic immutable audit trail for billing, invoices, payments, and adjustments',
                'icon'        => 'fas fa-shield-alt',
                'badge'       => 'badge-dark'
            ],
            'user_activity_reports'  => [
                'name'        => 'User Activity Reports',
                'description' => 'Security telemetry, login/logout history, session logs, and suspicious monitoring',
                'icon'        => 'fas fa-user-shield',
                'badge'       => 'badge-danger'
            ]
        ];
    }
}

