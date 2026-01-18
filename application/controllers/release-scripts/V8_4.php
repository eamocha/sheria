<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_4 extends CI_Controller
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
        $this->move_contacts_emails_to_new_table();
        $this->rename_matter_notes_attachments();
        $this->copy_task_statuses_values();
        $this->copy_task_locations_values();
        $this->fix_system_filters();
        $this->grant_users_to_access_new_permissions();
        $this->update_idp_clientid();
        $this->refresh_api_token_default_value();
        $this->replace_new_logo();
        $this->modify_htaccess();
        $this->write_log($this->log_path, 'End migration script');
    }
    
    private function replace_new_logo()
    {
        $this->write_log($this->log_path, 'start copying default logo');
        $source = "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR. "customer-portal-login-logo.png";
        $destination = "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "main_default_theme" . DIRECTORY_SEPARATOR . "customer-portal-login-logo.png";
        $this->write_log($this->log_path, 'copy ' . $source . ' to ' . $destination);
        if(file_exists($source) && file_exists($destination)){
            if(copy($source , $destination)){
                $this->write_log($this->log_path, 'copied successfully');
            }else{
               $this->write_log($this->log_path, 'failed to copy');
            }
        }else{
            $this->write_log($this->log_path, 'source of destination is not found');
        }
    }
    
    public function fix_system_filters(){
        $this->write_log($this->log_path, 'Fixing System Filters for Companies & Contacts');
        $query = "SELECT * FROM grid_saved_filters WHERE user_id = 0000000001 AND ( model = 'Company' OR model = 'Contact')";
        $filters = $this->db->query($query);
        $filters_array = $filters->result_array();
        $this->load->model('grid_saved_column');
        foreach($filters_array as $filter){
            if(!($this->grid_saved_column->fetch(array('grid_saved_filter_id' => $filter['id'])))){
                $this->grid_saved_column->set_field('model', $filter['model']);
                $this->grid_saved_column->set_field('user_id', 0000000001);
                $this->grid_saved_column->set_field('grid_details', 'a:3:{s:8:"pageSize";s:2:"20";s:4:"sort";s:0:"";s:16:"selected_columns";N;}');
                $this->grid_saved_column->set_field('grid_saved_filter_id', $filter['id']);
                if($this->grid_saved_column->insert()){
                    $this->write_log($this->log_path, 'Done - System Filter Added Successfully to the grid_saved_columns Table');
                } else {
                    $this->write_log($this->log_path, 'Error - Failed to Add the System Filter');
                }
            }
            $this->grid_saved_column->reset_fields();
        }
        $this->write_log($this->log_path, 'Finished fixing System Filter');
    }

    public function move_contacts_emails_to_new_table()
    {
        $this->write_log($this->log_path, 'move_contacts_emails_to_new_table started', 'info');

        $this->load->model('contact', 'contactfactory');
        $this->contact = $this->contactfactory->get_instance();

        $contacts = $this->contact->load_all(['where' => "contacts.email IS NOT NULL AND contacts.email <> ''"]);

        $this->load->model('contact_emails');

        foreach ($contacts as $contact) {
            $emails = explode(';', $contact['email']);

            if (!empty($emails)) {
                foreach ($emails as $email) {
                    $this->contact_emails->reset_fields();

                    $this->contact_emails->set_field('contact_id', $contact['id']);
                    $this->contact_emails->set_field('email', trim($email));

                    $this->contact_emails->insert();
                }
            } elseif (!empty($contact['email'])) {
                $this->contact_emails->reset_fields();

                $this->contact_emails->set_field('contact_id', $contact['id']);
                $this->contact_emails->set_field('email', trim($contact['email']));

                $this->contact_emails->insert();
            }
        }

        $this->remove_contact_email_field();

        $this->write_log($this->log_path, 'move_contacts_emails_to_new_table is done', 'info');
    }

    private function remove_contact_email_field()
    {
        $this->write_log($this->log_path, 'remove_contact_email_field started', 'info');

        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("DECLARE @constraint_name as NVARCHAR(255);
            DECLARE @constraint_cursor as CURSOR;
            DECLARE @columns_name TABLE (name varchar(1000));
            DECLARE @table_name as NVARCHAR(255);
            SET @table_name = 'contacts';
            INSERT INTO @columns_name VALUES ('email');
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
            DEALLOCATE @constraint_cursor;");
        }
        
        $this->db->query("ALTER TABLE contacts DROP COLUMN email");

        $this->write_log($this->log_path, 'remove_contact_email_field is done', 'info');
    }

    public function rename_matter_notes_attachments(){
        $this->write_log($this->log_path, 'Renaming Matter_Notes_Attachments folders to Matter Notes Attachments');
        $query = "SELECT * FROM documents_management_system WHERE name LIKE '%Matter_Notes_Attachments%' ";
        $docs = $this->db->query($query);
        $docs_array = array_column($docs->result_array(), 'id');
        $docs_query = implode(",", $docs_array);
        if(!empty($docs_query)){
            if ($this->db->query("UPDATE documents_management_system SET name = 'Matter Notes Attachments' WHERE id IN ( $docs_query )")) {
                $this->write_log($this->log_path, 'Done - Matter notes attachments folders renamed successfully');
            } else {
                $this->write_log($this->log_path, 'Error - Failed to rename Matter notes attachments folders');
            }
        }
        $this->write_log($this->log_path, 'Finished renaming folders');
    }

    /**
     * Copy the Task statuses values to advisor_task_statuses table
     */
    public function copy_task_statuses_values()
    {
        $this->write_log($this->log_path, 'copy_task_statuses_values started');

        $query = "SELECT * FROM task_statuses";

        $task_statuses = $this->db->query($query)->result_array();

        foreach ($task_statuses as $k => $v) {
            $name = '';
            $category = '';
            $isGlobal = '';

            foreach ($v as $field_name => $field_value) {
                if (isset(${$field_name})) {
                    ${$field_name} = $field_value;
                }
            }

            $name = $this->db->escape($name);
            $category = $this->db->escape($category);
            $isGlobal = $this->db->escape($isGlobal);

            $insert_query = "INSERT INTO advisor_task_statuses(name, category, isGlobal) values ($name, $category, $isGlobal)";

            $this->db->query($insert_query);
        }

        $this->write_log($this->log_path, 'copy_task_statuses_values done');
    }

    /**
     * Copy the task locations values to advisor_task_locations table
     */
    public function copy_task_locations_values()
    {
        $this->write_log($this->log_path, 'copy_task_locations_values started');

        $query = "SELECT * FROM task_locations";

        $task_statuses = $this->db->query($query)->result_array();

        foreach ($task_statuses as $k => $v) {
            $name = '';

            foreach ($v as $field_name => $field_value) {
                if (isset(${$field_name})) {
                    ${$field_name} = $field_value;
                }
            }

            $name = $this->db->escape($name);

            $insert_query = "INSERT INTO advisor_task_locations(name) values ($name)";

            $this->db->query($insert_query);
        }

        $this->write_log($this->log_path, 'copy_task_locations_values done');
    }
    
    public function grant_users_to_access_new_permissions(){
        $this->write_log($this->log_path, 'add new actions to user permissions');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'core'){
                    $new_permissions = $group_permissions;
                    if(in_array('/case_containers/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/case_containers/preview_document/', '/case_containers/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/cases/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/cases/preview_document/', '/cases/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/companies/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/companies/preview_document/', '/companies/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/contacts/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/contacts/preview_document/', '/contacts/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/docs/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/docs/preview_document/', '/docs/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/intellectual_properties/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/intellectual_properties/preview_document/', '/intellectual_properties/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/tasks/edit/', $group_permission)){
                        array_push($new_permissions['core'], '/tasks/add_comment/', '/tasks/edit_comment/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/tasks/delete/', $group_permission)){
                        array_push($new_permissions['core'], '/tasks/delete_comment/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/users/index/', $group_permission) || in_array('/users/', $group_permission)){
                        array_push($new_permissions['core'], '/advisors/users/');
                        array_push($new_permissions['core'], '/advisors/ban_unban/');
                        array_push($new_permissions['core'], '/advisors/flag_to_change_password/');
                        array_push($new_permissions['core'], '/advisors/user_activate_deactivate/');
                        array_push($new_permissions['core'], '/advisors/user_add/');
                        array_push($new_permissions['core'], '/advisors/user_edit/');
                        array_push($new_permissions['core'], '/export/advisor_users/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/cases/tasks/', $group_permission) || in_array('/cases/', $group_permission)){
                        array_push($new_permissions['core'], '/cases/advisor_tasks/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/tasks/view/', $group_permission) || in_array('/tasks/', $group_permission)){
                        array_push($new_permissions['core'], '/cases/advisor_task/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/cases/export_to_word/', $group_permission) || in_array('/cases/', $group_permission)){
                        array_push($new_permissions['core'], '/tasks/export_task_to_word/');
                        array_push($new_permissions['core'], '/tasks/export_task_to_word_for_clients/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/azure_ad/', $group_permission)){
                        array_push($new_permissions['core'], '/saml_sso/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/azure_ad/import_azure_users/', $group_permission)){
                        array_push($new_permissions['core'], '/saml_sso/import_users');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/customer_portal/user_import_from_ad/', $group_permission)){
                        array_push($new_permissions['core'], '/saml_sso/import_cp_users');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/cases/litigation_case/', $group_permission)){
                        array_push($new_permissions['core'], '/cases/list_hearings/', '/cases/non_verified_hearings/', '/cases/verified_hearings/', '/cases/judged_hearings/', '/cases/all_todays_hearings/', '/cases/my_todays_hearings/', '/cases/all_hearings_for_tomorrow/', '/cases/my_hearings_for_tomorrow/', '/cases/all_hearings_for_this_week/', '/cases/my_hearings_for_this_week/', '/cases/all_hearings_for_this_month/', '/cases/my_hearings_for_this_month/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/cases/hearings/', $group_permission)){
                        array_push($new_permissions['core'], '/cases/hearing_send_report_to_client/', '/cases/fill_hearing_summary/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    public function update_idp_clientid(){
        $this->write_log($this->log_path, 'update Idp data');
        $auth_source_path = substr(COREPATH, 0, -12) . "saml/config/authsources.php";
        require_once $auth_source_path;
        $entity = $config['default-sp']['entityID'] ?? '';
        $idp    = $config['default-sp']['idp'] ?? '';
        if( isset($entity) && strlen($entity)>4 ){
            $client_id = substr($entity, 4);
            $query = "UPDATE saml_configuration SET keyValue='{$client_id}' WHERE keyName='client_id'";
            $res = $this->db->query($query);
            $query2 = "DELETE FROM `system_preferences` WHERE `groupName`='AzureDirectory'";
            $res2 = $this->db->query($query2);
        }
        unset($config['default-sp']); 
        $arr =   array(
                    'app4legal-azure_ad' => 
                    array (
                        0 => 'saml:SP',
                        'entityID' => $entity,
                        'idp' => $idp,
                        'discoURL' => NULL,
                        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
                        'simplesaml.nameidattribute' => 'eduPersonTargetedID',
                    ),
                    'app4legal-onelogin' => 
                    array (
                        0 => 'saml:SP',
                        'entityID' => NULL,
                        'idp' => '',
                        'discoURL' => NULL,
                        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
                        'simplesaml.nameidattribute' => 'eduPersonTargetedID',
                    ),
                );
        $config = array_merge($config, $arr);
        file_put_contents($auth_source_path, '<?php $config = ' . var_export($config, true) . ';');
        $this->write_log($this->log_path, 'done IDP Update');
    }
    public function refresh_api_token_default_value(){
        $this->write_log($this->log_path, 'Change Refresh Api Token default value', 'info');
        $instance_file_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'instance.php';
        if(!file_exists($instance_file_path)){
            $this->write_log($this->log_path, 'target file not found (' . $instance_file_path . ')', 'error');
            return;
        }

        $file = fopen($instance_file_path, "r");
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $key = "api_key_validity_time";
                $str_pos = strpos($line, '$config[\'' . $key . '\']');
                if($str_pos !== false){
                    $this->write_log($this->log_path, 'replacing default value: ' . '$config[\'' . $key . '\']', 'info');
                    $sub_line = substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';") + 2 );
                    $substring_start = '$config[\'api_key_validity_time\'] = \'';
                    $substring_end = "';";
                    $current_val = $this->extract_current_value($sub_line, $substring_start, $substring_end);
                    $new_val = intval($current_val) * 60;
                    $new_api_key_validity_time = $substring_start . $new_val . $substring_end;
                    $replace = str_replace($sub_line, $new_api_key_validity_time, $line);
                    file_put_contents($instance_file_path, str_replace($line, $replace , file_get_contents($instance_file_path)));
                }
            }
            fclose($file);
        } else {
            $this->write_log($this->log_path, 'error opening the file (' . $instance_file_path . ')', 'error');
        }

        $this->write_log($this->log_path, 'Changes default value is done', 'info');
    }

    public function modify_htaccess()
    {
        $this->write_log($this->log_path, 'Start modifying root .htaccess file', 'info');

        $htaccess_file_path = FCPATH . '.htaccess';

        $file = file_get_contents($htaccess_file_path);
        $file = str_replace('</IfModule>', "\t". 'RewriteCond %{REQUEST_URI} /api/^(.*)$' . PHP_EOL . "\t" . 'RewriteRule /api/$1 [L,QSA]' . PHP_EOL . '</IfModule>', $file);
        
        file_put_contents($htaccess_file_path, $file);

        $this->write_log($this->log_path, 'Modifying root .htaccess file is done', 'info');
    }

    private function extract_current_value($content, $substring_start, $substring_end){
        $r = explode($substring_start, $content);
        if (isset($r[1])){
            $r = explode($substring_end, $r[1]);
            return $r[0];
        }
        return '';
    }
}
