<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_6_1 extends CI_Controller
{
    use MigrationLogTrait;

    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->grant_users_to_access_new_permissions();
        $this->update_time_types_attending_arabic_value();
        $this->change_legal_case_details_comment_type();
        $this->update_money_dashboard_widget_filter();
        $this->update_money_dashboard_widgets_types();
        $this->add_fields_to_invoice_templates();
        $this->modify_htaccess();
        $this->add_advisor_system_preferences();
        $this->write_log($this->log_path, 'End migration script');
    }
    
    public function modify_htaccess()
    {
        $this->write_log($this->log_path, 'Start modifying root .htaccess file', 'info');

        $htaccess_file_path = FCPATH . '.htaccess';

        $file = file_get_contents($htaccess_file_path);
        $file = str_replace('RewriteCond %{REQUEST_URI} /api/^(.*)$', 'AddHandler x-httpd-suphp72 .php .php3 .php4 .php5' . PHP_EOL . 'RewriteCond %{REQUEST_URI} /api/^(.*)$', $file);
        
        file_put_contents($htaccess_file_path, $file);

        $this->write_log($this->log_path, 'Modifying root .htaccess file is done', 'info');
    }
    
    public function grant_users_to_access_new_permissions()
    {
        $this->write_log($this->log_path, 'add new actions to user permissions');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group) {
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach ($group_permissions as $module => $group_permission) {
                if ($module === 'core') {
                    $new_permissions = $group_permissions;
                    if(in_array('/case_containers/create_slot_for_advanced_export/', $group_permission)){
                        array_push($new_permissions['core'], '/case_containers/edit_slot_for_advanced_export/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if (in_array('/vouchers/invoice_add/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/advice_fee_note_export_options/');
                        array_push($new_permissions['money'], '/vouchers/advice_fee_note_export_to_word/');
                        array_push($new_permissions['money'], '/vouchers/invoice_partners_settlements/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'contract') {
                    $new_permissions = $group_permissions;
                    if (in_array('/contracts/add/', $group_permission)) {
                        array_push($new_permissions['contract'], '/contracts/delete_email_comment/');
                        array_push($new_permissions['contract'], '/contracts/get_all_comments/');
                        array_push($new_permissions['contract'], '/contracts/get_all_core_and_cp_comments/');
                        array_push($new_permissions['contract'], '/contracts/get_all_email_comments/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }
    
    public function change_legal_case_details_comment_type()
    {
        $this->write_log($this->log_path, 'start change field type');
        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("
            DECLARE @constraint_name as NVARCHAR(255);
            DECLARE @constraint_cursor as CURSOR;
            DECLARE @columns_name TABLE (name varchar(1000));
            DECLARE @table_name as NVARCHAR(255);
            SET @table_name = 'legal_case_litigation_details';
            INSERT INTO @columns_name VALUES ('comments');

            SET @constraint_cursor = CURSOR FOR
            (SELECT fk.name AS constraint_name
            FROM sys.foreign_keys fk
                INNER JOIN sys.foreign_key_columns fkcol on fkcol.constraint_object_id = fk.object_id
                INNER JOIN sys.columns col on col.column_id = fkcol.parent_column_id and fk.parent_object_id = col.object_id
            WHERE fk.parent_object_id = OBJECT_ID(@table_name)
                AND col.name IN (SELECT name FROM @columns_name)
            UNION
            SELECT chk.name AS constraint_name
            FROM sys.check_constraints chk
                INNER JOIN sys.columns col on col.column_id = chk.parent_column_id  and chk.parent_object_id = col.object_id
            WHERE chk.parent_object_id = OBJECT_ID(@table_name)
                AND col.name IN (SELECT name FROM @columns_name)
            UNION
            SELECT dc.name AS constraint_name
            FROM sys.default_constraints dc
                INNER JOIN sys.columns col ON col.default_object_id = dc.object_id and dc.parent_object_id = col.object_id
            WHERE dc.parent_object_id = OBJECT_ID(@table_name)
                AND col.name IN (SELECT name FROM @columns_name));
            OPEN @constraint_cursor;
            FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
            WHILE @@FETCH_STATUS = 0
            BEGIN
            EXEC(N'alter table ' + @table_name + ' drop constraint  [' + @constraint_name + N']');
            FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
            END
            CLOSE @constraint_cursor;
            DEALLOCATE @constraint_cursor;

            alter table legal_case_litigation_details
            ALTER COLUMN comments nvarchar(MAX);

            alter table legal_case_litigation_details
            ADD CONSTRAINT df_comments DEFAULT NULL FOR comments; 
        ");
        }
        $this->write_log($this->log_path, 'done change field type');
    }

    public function update_time_types_attending_arabic_value()
    {
        $this->write_log($this->log_path, 'updating time types attending arabic value');
        $type_attending = $this->db->query("SELECT type FROM time_types_languages WHERE name = 'Attending'");
        $type_attending_id = $type_attending->result_array();
        if (isset($type_attending_id[0]['type'])){
            $this->write_log($this->log_path, 'updateing arabic value of time type name Attending');
            $this->db->query("UPDATE time_types_languages SET time_types_languages.name = 'حضور' WHERE time_types_languages.language_id = 2 AND time_types_languages.type = " . $type_attending_id[0]['type']);
        }
        $this->write_log($this->log_path, 'done from updating time types');
    }
    public function update_money_dashboard_widget_filter()
    {
        $this->write_log($this->log_path, 'update money dashboard widget filter started', 'info');
        $widgets = $this->db->query("SELECT id, filter FROM money_dashboard_widgets");
        $all_widgets= $widgets->result_array();
        foreach ($all_widgets as $widget) {
            $filter = unserialize($widget['filter']);
            if (!array_key_exists('operator', $filter)){
                $filter['operator'] = 'between';
            }
            if(!$filter['filter_type']){
                $filter['filter_type'] = 'date';
                $filter['date'] = 'custom';
                $filter['from_date'] = '2021-01-01';
                $filter['to_date'] = '2021-12-31';
                $filter['specific_date'] = '';
            }
            $filter = serialize($filter);
            if($filter !== unserialize($widget['filter'])){
                $this->db->query("UPDATE money_dashboard_widgets SET filter = '{$filter}' WHERE id = {$widget['id']}");
            }
        }
        $this->write_log($this->log_path, 'money dashboard widget filter updated successfully', 'info');
    }
    public function update_money_dashboard_widgets_types()
    {
        $this->write_log($this->log_path, 'update money dashboard widgets types started', 'info');
        $widgets_types = $this->db->query("SELECT id, name, settings FROM money_dashboard_widgets_types");
        $all_widgets_types= $widgets_types->result_array();
        foreach ($all_widgets_types as $widgets_type) {
            $filter = unserialize($widgets_type['settings']);
            if(!$filter['filter_type']){
                $filter['filter_type'] = 'date';
            }
            $filter = serialize($filter);
            $name = $widgets_type['name'];
            if($widgets_type['name'] === 'Transactions'){
                $name = 'transactions';
            } else if($widgets_type['name'] === 'Bills'){
                $name = 'bills';
            }
            $set_filter = $filter !== unserialize($widgets_type['settings']) ? "settings = '{$filter}'" : "";
            $set_name = $name !== $widgets_type['name'] ? "name = '{$name}'" : "";
            $comma = ($filter !== unserialize($widgets_type['settings']) && $name !== $widgets_type['name']) ? ", " : "";
            if($name !== $widgets_type['name'] || $filter !== unserialize($widgets_type['settings'])){
                $this->db->query("UPDATE money_dashboard_widgets_types SET ".$set_filter.$comma.$set_name." WHERE id = {$widgets_type['id']}");
            }
        }
        $this->write_log($this->log_path, 'money dashboard widgets types updated successfully', 'info');
    }
    public function add_fields_to_invoice_templates()
    {
        $this->write_log($this->log_path, 'Started adding Invoice Ref to invoice templates');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => $template){
            $settings = unserialize($template['settings']);
            if(isset($settings['header'])){
                if(!isset($settings['header']['show']['center-logo'])){
                    $settings['header']['show']['center-logo'] = false;
                }
                if(!isset($settings['header']['show']['logo-system-size'])){
                    $settings['header']['show']['logo-system-size'] = true;
                }
                if(!isset($settings['body']['show']['show-entity-currency'])){
                    $settings['body']['show']['show-entity-currency'] = false;
                }
                $template['settings'] = serialize($settings);
            }
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
        }
        $this->write_log($this->log_path, 'Logo values added Succeddfully');
    }

    public function add_advisor_system_preferences()
    {
        $this->write_log($this->log_path, 'Started aadd_advisor_system_preferences');

        $query = "SELECT * FROM system_preferences WHERE groupName = 'AdvisorConfig' AND keyName = 'AllowFeatureAdvisor'";
        $filters = $this->db->query($query);
        $filters_array = $filters->result_array();
        
        if (empty($filters_array)) {
            if ($this->db->dbdriver === 'sqlsrv') {
                $this->db->query("INSERT INTO system_preferences (groupName, keyName, keyValue) VALUES ('AdvisorConfig', 'AllowFeatureAdvisor', '0');");
            } else {
                $this->db->query("INSERT INTO `system_preferences` (`groupName`, `keyName`, `keyValue`) VALUES ('AdvisorConfig', 'AllowFeatureAdvisor', '0');");
            }
        }

        $this->write_log($this->log_path, 'Done add_advisor_system_preferences');
    }
}
