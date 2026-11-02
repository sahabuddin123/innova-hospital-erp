<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RBAC Model
 * 
 * Manages role-based permissions and forensic permission audit logging.
 * 
 * @package    Lifecare Hospital ERP
 * @subpackage Models
 */
class Rbac_model extends CI_Model
{
    protected $table = 'role_permissions';
    protected $audit_table = 'permission_audit_logs';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTablesExist();
    }

    /**
     * Idempotent table creation
     */
    public function ensureTablesExist()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `{$this->table}` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `group_id` MEDIUMINT(8) UNSIGNED NOT NULL,
                  `group_name` VARCHAR(50) NOT NULL,
                  `permission_group` VARCHAR(50) NOT NULL,
                  `report_key` VARCHAR(100) NOT NULL DEFAULT 'all',
                  `can_view` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_create` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_update` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_delete` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_print` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_export_pdf` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_export_excel` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_export_csv` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_view_details` TINYINT(1) NOT NULL DEFAULT 0,
                  `can_view_sensitive` TINYINT(1) NOT NULL DEFAULT 0,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uk_group_perm_key` (`group_id`, `permission_group`, `report_key`),
                  KEY `idx_group_name` (`group_name`),
                  KEY `idx_permission_group` (`permission_group`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        }

        if (!$this->db->table_exists($this->audit_table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `{$this->audit_table}` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `role_id` MEDIUMINT(8) UNSIGNED NOT NULL,
                  `role_name` VARCHAR(50) NOT NULL,
                  `permission_group` VARCHAR(50) NOT NULL,
                  `report_key` VARCHAR(100) NOT NULL DEFAULT 'all',
                  `action_changed` VARCHAR(50) NOT NULL,
                  `previous_value` VARCHAR(50) NULL,
                  `new_value` VARCHAR(50) NOT NULL,
                  `changed_by` INT UNSIGNED NOT NULL,
                  `changed_by_name` VARCHAR(100) NOT NULL,
                  `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `reason` VARCHAR(255) NULL,
                  `ip_address` VARCHAR(45) NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_role_id` (`role_id`),
                  KEY `idx_permission_group` (`permission_group`),
                  KEY `idx_changed_at` (`changed_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        }
    }

    /**
     * Get all active user groups (roles)
     */
    public function getAllGroups()
    {
        return $this->db->order_by('id', 'asc')->get('groups')->result();
    }

    /**
     * Get single group by ID
     */
    public function getGroupById($id)
    {
        return $this->db->where('id', $id)->get('groups')->row();
    }

    /**
     * Get permission records for a specific group ID
     */
    public function getPermissionsByGroup($group_id)
    {
        return $this->db->where('group_id', $group_id)
            ->get($this->table)
            ->result_array();
    }

    /**
     * Get permission records for multiple group IDs
     */
    public function getPermissionsByGroupIds(array $group_ids)
    {
        if (empty($group_ids)) {
            return [];
        }
        return $this->db->where_in('group_id', $group_ids)
            ->get($this->table)
            ->result_array();
    }

    /**
     * Get single permission record
     */
    public function getPermission($group_id, $permission_group, $report_key = 'all')
    {
        return $this->db->where('group_id', $group_id)
            ->where('permission_group', $permission_group)
            ->where('report_key', $report_key)
            ->get($this->table)
            ->row_array();
    }

    /**
     * Save / update role permissions in a database transaction with audit logging
     * 
     * @param int    $group_id           Group/Role ID
     * @param array  $permission_matrix  Array of permission rows [permission_group => [can_view => 1, ...]]
     * @param int    $changed_by_user_id User ID performing modification
     * @param string $reason             Reason for changes
     * @return bool
     */
    public function saveRolePermissions($group_id, array $permission_matrix, $changed_by_user_id, $reason = '')
    {
        $group = $this->getGroupById($group_id);
        if (!$group) {
            return false;
        }

        $user = $this->db->where('id', $changed_by_user_id)->get('users')->row();
        $user_name = $user ? ($user->username ?? $user->email) : 'System';
        $ip_address = $this->input->ip_address();

        $existing = [];
        $existing_rows = $this->getPermissionsByGroup($group_id);
        foreach ($existing_rows as $row) {
            $key = $row['permission_group'] . ':' . $row['report_key'];
            $existing[$key] = $row;
        }

        $actions = [
            'can_view',
            'can_create',
            'can_update',
            'can_delete',
            'can_print',
            'can_export_pdf',
            'can_export_excel',
            'can_export_csv',
            'can_view_details',
            'can_view_sensitive'
        ];

        $this->db->trans_start();

        foreach ($permission_matrix as $perm_group => $data) {
            $report_key = $data['report_key'] ?? 'all';
            $key = $perm_group . ':' . $report_key;
            $old_row = $existing[$key] ?? null;

            $record = [
                'group_id'         => $group_id,
                'group_name'       => $group->name,
                'permission_group' => $perm_group,
                'report_key'       => $report_key,
                'updated_at'       => date('Y-m-d H:i:s')
            ];

            foreach ($actions as $act) {
                $val = !empty($data[$act]) ? 1 : 0;
                $record[$act] = $val;

                // Check for audit diff
                $old_val = isset($old_row[$act]) ? (int)$old_row[$act] : 0;
                if ($old_val !== $val) {
                    $this->db->insert($this->audit_table, [
                        'role_id'          => $group_id,
                        'role_name'        => $group->name,
                        'permission_group' => $perm_group,
                        'report_key'       => $report_key,
                        'action_changed'   => $act,
                        'previous_value'   => (string)$old_val,
                        'new_value'        => (string)$val,
                        'changed_by'       => $changed_by_user_id,
                        'changed_by_name'  => $user_name,
                        'changed_at'       => date('Y-m-d H:i:s'),
                        'reason'           => !empty($reason) ? $reason : 'Permission matrix update via Role Management',
                        'ip_address'       => $ip_address
                    ]);
                }
            }

            if ($old_row) {
                $this->db->where('id', $old_row['id'])->update($this->table, $record);
            } else {
                $record['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert($this->table, $record);
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Get permission audit logs
     */
    public function getAuditLogs($group_id = null, $limit = 50, $offset = 0)
    {
        if ($group_id) {
            $this->db->where('role_id', $group_id);
        }
        return $this->db->order_by('changed_at', 'desc')
            ->limit($limit, $offset)
            ->get($this->audit_table)
            ->result_array();
    }

    /**
     * Count audit logs
     */
    public function countAuditLogs($group_id = null)
    {
        if ($group_id) {
            $this->db->where('role_id', $group_id);
        }
        return $this->db->count_all_results($this->audit_table);
    }
}
