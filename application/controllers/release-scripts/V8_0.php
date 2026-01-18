<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_0 extends CI_Controller
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
        $this->add_subscription_flag();
        $this->fix_category_in_contact_grid_saved_filers();
        $this->fix_category_in_company_grid_saved_filers();
        $this->change_categories_controller('contact_categories', 'contact_company_categories');
        $this->change_categories_controller('/export/contact_categories/', '/export/contact_company_categories/');
        $this->update_contact_company_categories();
        $this->add_clients_attachments_folder();
        $this->add_client_to_allowed_file_uploads();
        $this->add_contract_to_allowed_file_uploads();
        $this->fix_corrupted_invoice_templates();
        $this->add_extensions('xlt|xltx');
        $this->add_extensions('docm|xlsm');
        $this->add_extensions('xltm|pptm');
        $this->update_permissions();
        $this->update_license_files();
        $this->add_contract_permissions_to_all_user_groups();
        $this->fix_custom_fields_data();
        $this->fix_expenses_case_ids();
        // custom script for A4LCRM to set Opt-out = Yes for all contacts that do not have a country value
        //$this->update_contact_custom_field();
        // clear country 103 on A4LCRM for contacts that imported from excel
        //$this->clear_contact_country_value();
        // clear email value for contact that contains "@email.com" 
        //$this->clear_contact_email_value();
        $this->write_log($this->log_path, 'done from migration script');
    }
    
    public function add_subscription_flag(){
        $this->write_log($this->log_path, 'update subscription status');
        $this->load->model('instance_data');
        $instance_data_array = $this->instance_data->get_values();
        $cloud_installation_type = $instance_data_array['installationType'] == "on-cloud";
        if ($cloud_installation_type) { //cloud installation
            $instance_client_type = isset($instance_data_array['clientType']) ? $instance_data_array['clientType'] : NULL;
            $subscription_status = $instance_client_type == 'customer' ? 'active' : '';
            $this->write_log($this->log_path, 'add status: ' . $subscription_status);
            $this->instance_data->insert_on_duplicate_key_update(array('keyName' => 'subscription', 'keyValue' => $subscription_status), array('keyName'));
        }
    }
    
    public function fix_category_in_company_grid_saved_filers(){
        $this->write_log($this->log_path, 'start fixing category column in company grid saved filters table');
        $this->load->model('grid_saved_filter', 'grid_saved_filterfactory');
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $fields_arr = [
            // old => new
            'companies.company_category_id' => 'companies.company_sub_category_id'
        ];
//        this array is for testing to reproduce the old saved filter values        
//        $fields_arr = [
//            // new => old
//            'companies.company_sub_category_id' => 'companies.company_category_id'
//        ];
        $sql = "SELECT id, filterName, formData FROM grid_saved_filters WHERE model = 'Company'";
        $query_execution = $this->db->query($sql);
        $saved_filters = $query_execution->result_array();
        foreach ($saved_filters as $key => $saved_filter) {
            $form_data = [];
            $this->write_log($this->log_path, "fix filters in filter # '{$saved_filter['id']}'");
            $form_data = unserialize($saved_filter['formData']);
            if(!empty($form_data) && isset($form_data['gridFilters'])){
                $json = json_decode($form_data['gridFilters']);
                foreach($json->filters as $k => $filter){
                    if(isset($filter->filters[0]->field)){
                        foreach($fields_arr as $old_field => $new_field){
                            if($filter->filters[0]->field == $old_field){
                                $json->filters[$k]->filters[0]->field = $new_field;
                                // update saved filter
                                $new_form_data = serialize(['gridFilters' => json_encode($json, JSON_UNESCAPED_UNICODE)]);
                                if (strpos($saved_filter['filterName'], "(Sub-category)") === FALSE) {
                                    if ($this->db->query("UPDATE grid_saved_filters set formData = '{$new_form_data}', filterName = '{$saved_filter['filterName']} (Sub-category)' WHERE id = '{$saved_filter['id']}'")) {
                                        $this->write_log($this->log_path, 'Done - Updating filters and filter name of id  = '.$saved_filter['id']);
                                    } else {
                                        $this->write_log($this->log_path, 'Error - Failed to update the saved filter id = '.$saved_filter['id']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'end renaming columns');
    }

    public function fix_category_in_contact_grid_saved_filers(){
        $this->write_log($this->log_path, 'start fixing category column in contact grid saved filters table');
        $this->load->model('grid_saved_filter', 'grid_saved_filterfactory');
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $fields_arr = [
            // old => new
            'contacts.contact_category_id' => 'contacts.contact_sub_category_id'
        ];
//        this array is for testing to reproduce the old saved filter values        
//        $fields_arr = [
//            // new => old
//            'contacts.contact_sub_category_id' => 'contacts.contact_category_id'
//        ];
        $sql = "SELECT id, filterName, formData FROM grid_saved_filters WHERE model = 'Contact'";
        $query_execution = $this->db->query($sql);
        $saved_filters = $query_execution->result_array();
        foreach ($saved_filters as $key => $saved_filter) {
            $form_data = [];
            $this->write_log($this->log_path, "fix filters in filter # '{$saved_filter['id']}'");
            $form_data = unserialize($saved_filter['formData']);
            if(!empty($form_data) && isset($form_data['gridFilters'])){
                $json = json_decode($form_data['gridFilters']);
                foreach($json->filters as $k => $filter){
                    if(isset($filter->filters[0]->field)){
                        foreach($fields_arr as $old_field => $new_field){
                            if($filter->filters[0]->field == $old_field){
                                $json->filters[$k]->filters[0]->field = $new_field;
                            }
                        }
                    }
                }
                // update saved filter
                $new_form_data = serialize(['gridFilters' => json_encode($json, JSON_UNESCAPED_UNICODE)]);
                if (strpos($new_form_data, "contacts.contact_sub_category_id") !== FALSE) {
                    if ($this->db->query("UPDATE grid_saved_filters set formData = '{$new_form_data}', filterName = '{$saved_filter['filterName']} (Sub-category)' WHERE id = '{$saved_filter['id']}'")) {
                        $this->write_log($this->log_path, 'Done - Updating filters and filter name of id  = '.$saved_filter['id']);
                    } else {
                        $this->write_log($this->log_path, 'Error - Failed to update the saved filter id = '.$saved_filter['id']);
                    }
                }else{
                    if ($this->db->query("UPDATE grid_saved_filters set formData = '{$new_form_data}' WHERE id = '{$saved_filter['id']}'")) {
                        $this->write_log($this->log_path, 'Done - Updating filters of id  = '.$saved_filter['id']);
                    } else {
                        $this->write_log($this->log_path, 'Error - Failed to update the saved filter id = '.$saved_filter['id']);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'end renaming columns');
    }

    /**
     * Replace the category controller by the new name
     * in the user group permissions table
     */
    private function change_categories_controller($old_url, $new_url){
        $this->write_log($this->log_path, "START Replacing the URL {$old_url} by {$new_url}");
        $this->load->model('user_group_permission');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $user_groups = $this->user_group->load_all();

        foreach($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module == 'core'){
                    foreach($group_permission as $k => $permission){
                        if(strpos($permission, $old_url) !== false){
                            $group_permission[$k] = str_replace($old_url, $new_url, $permission);
                            $group_permissions[$module] = $group_permission;
                            $this->user_group_permission->set_permission_data($user_group['id'], $group_permissions);
                        }
                    }
                }
            }
        }

        $this->write_log($this->log_path, "done Replacing the category controller by the new name");
    }

    private function add_extensions($extension)
    {
        $this->write_log($this->log_path, 'Changes on allowed files upload started', 'info');
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';

        if(!file_exists($allowed_file_uploads_path)){
            $this->write_log($this->log_path, 'target file not found (' . $allowed_file_uploads_path . ')', 'error');
            return;
        }

        $target_keys = [
            'company', 'contact', 'case', 'doc', 'BI', 'BI-PY', 'EXP', 'INV', 'QOT', 'INV-PY', 'task', 'caseContainer', 'client', 'contract'
        ];
        $handle = fopen($allowed_file_uploads_path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                foreach($target_keys as $target_key){
                    $str_pos = strpos($line, '$config[\'' . $target_key . '\']');
                    if($str_pos !== false){
                        $this->write_log($this->log_path, 'replacing config variable: ' . '$config[\'' . $target_key . '\']', 'info');
                        $new_line = substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")+2);
                        file_put_contents($allowed_file_uploads_path, str_replace($new_line, substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")) . "|" . $extension ."';", file_get_contents($allowed_file_uploads_path)));
                    }
                }
            }
            fclose($handle);
        } else {
            $this->write_log($this->log_path, 'error opening the file (' . $allowed_file_uploads_path . ')', 'error');
        }

        $this->write_log($this->log_path, 'Changes on allowed files upload is done', 'info');
    }

    public function update_contact_company_categories(){
        $this->write_log($this->log_path, 'start updating contact / company categories', 'info');
        $sql = "SELECT id, name FROM contact_company_sub_categories";
        $query_execution = $this->db->query($sql);
        $old_categories = $query_execution->result_array();
        foreach ($old_categories as $key => $old_category) {
            $this->write_log($this->log_path, "update old category '{$old_category['name']}'");
            if(in_array($old_category['name'], ['موكل محتمل', 'عميل محتمل/ Possible Client', 'Business Leads', 'App4Legal-Hot-Lead', 'MFJ-Lead', 'Immigration Leads', 'Potential', 'Real Estate Leads', 'Yetkili | Lead', 'عميل محتمل', 'زبون محتمل', 'Potential Client / عميل محتمل', 'Potential Client', 'Lead- عميل محتمل', 'Lead', 'Atlassian-Lead', 'App4Legal-New-Lead', 'App4Legal-Cold-Lead', 'App4Daycare-Lead', 'App4Clinic-Lead'])){
                // Lead
                $new_category_id = 4;
            }elseif(in_array($old_category['name'], ['موكل/  Client', 'موكل', 'Auditor of Client', 'Business Clients', 'SME Client', 'Real Estate Clients', 'MFJ-Client', 'Immigration Clients', 'عميل/ Client', 'عميل', 'زبون', 'Πελάτης', 'SID-Client',  'Müvekkil | Client',  'Individual Client',  'Clients',  'Cliente',  'Client- عميل',  'Client\'s Rep',  'client\'s relative',  'Client موكل',  'Client type',  'Client Employee',  'Client Auditor',  'Client 2',  'Client / موكل',  'Client',  'Atlassian-Customer',  'App4Legal-Customer',  'App4Daycare-Customer',  'App4Clinic-Customer', 'عميل مهم', 'عميل خارجي', 'Πελάτης/Συνεργάτης'])){
                // Client
                $new_category_id = 1;
            }elseif(in_array($old_category['name'], ['مكتب محاماة خارجي',  'مستشار خارجي',  'External Lawyer',  'External Law Firms',  'External Law Firm',  'External Experts',  'External Counsel',  'External Consultant',  'External Advisor- محامي خارجي',  'External Advisor / مستشار خارجي',  'External Advisor',  'Consultant'])){
                // External Advisor
                $new_category_id = 2;
            }elseif(in_array($old_category['name'], ['عميل داخلي', 'Colleague',  'Internal staff', 'SKP Staff',  'Internal Advisor- محامي الشركة',  'Internal', 'زميل بالمكتب', 'زميل', 'زملاء المكتب'])){
                // Internal
                $new_category_id = 3;
            }elseif(in_array($old_category['name'], ['شريك/ Partner',  'شريك', 'SKP Partner', 'Ortak | Partner',  'Potential Partner',  'Partner- شريك',  'Partner شريك',  'Partner / شريك',  'Partner',  'Atl-Partner',  'App4Legal-Partner',  'App4Daycare-Partner',  'App4Clinic-Partner'])){
                // Partner
                $new_category_id = 5;
            }elseif(in_array($old_category['name'], ['Prospects',  'Prospective Clients',  'Prospect',  'Possible Clients',  'Possible Client'])){
                // Prospect
                $new_category_id = 6;
            }elseif(in_array($old_category['name'], ['مورد', 'translator', 'Distributor', 'Service Provider', 'ProviderIT',  'Vendor / Contractor / Freelancer',  'Vendor / Contractor / Company',  'Vendor',  'Tedarikçi | Provider',  'Suppliers',  'Supplier مورد',  'Supplier / مزود خدمة',  'Supplier',  'Provider- مزود خدمة',  'Provider / مزود خدمة',  'Provider',  'Proveedor', 'ممون', 'مزود خدمة'])){
                // Supplier
                $new_category_id = 7;
            }else{
                // Other
                $new_category_id = 8;
            }
            if ($this->db->query("UPDATE contacts set contact_category_id = '{$new_category_id}' where contact_sub_category_id = '{$old_category['id']}'")) {
                $this->write_log($this->log_path, 'Done - Updating contacts having old category id  = '.$old_category['id']);
            } else {
                $this->write_log($this->log_path, 'Error - Failed to update contacts having old category id  = '.$old_category['id']);
            }
            if ($this->db->query("UPDATE companies set company_category_id = '{$new_category_id}' where company_sub_category_id = '{$old_category['id']}'")) {
                $this->write_log($this->log_path, 'Done - Updating companies having old category id  = '.$old_category['id']);
            } else {
                $this->write_log($this->log_path, 'Error - Failed to update companies having old category id  = '.$old_category['id']);
            }
        }
    }

    public function add_clients_attachments_folder(){
        $this->write_log($this->log_path, 'start creating clients folder', 'info');
        if(!file_exists(FCPATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'clients') && mkdir(FCPATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'clients') && copy(FCPATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'companies' . DIRECTORY_SEPARATOR . ".gitignore", FCPATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'clients' . DIRECTORY_SEPARATOR . ".gitignore")){
            $this->write_log($this->log_path, 'Done - clients folder created successfully');
        }else{
            $this->write_log($this->log_path, 'Info - folder already exists');
        }
    }

    /**
     * add clients allowed file types to confing/allowed_file_uploads.php
     */
    public function add_client_to_allowed_file_uploads()
    {
        $this->write_log($this->log_path, 'update_allowed_file_uploads started', 'info');
        $file = getcwd() . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "allowed_file_uploads.php";
        $str = "\$config['client'] = 'doc|docx|xls|xlsx|pps|ppt|pptx|pdf|tif|tiff|jpg|png|gif|jpeg|bmp|html|htm|txt|msg|eml|vcf|zip|rar|mpg|mp3|mp4|flv|mov|wav|3gp|avi|pages|dwg|dwf|rtf|ogg|wmv|aiff|aif|m4a|m4v|au|xlt|xltx';";
        $option = 'client';

        if (file_exists($file)) {
            if (!$this->config_option_already_exists($option, $file)) {
                $data = PHP_EOL . $str . PHP_EOL;

                if (!file_put_contents($file, $data, FILE_APPEND | LOCK_EX)) {
                    $this->write_log($this->log_path, '    couldn\'t modify ' . $file);
                }
            }
        } else {
            $this->write_log($this->log_path, $file . ' doesn\'t exists.', 'info');
        }

        $this->write_log($this->log_path, 'update_allowed_file_uploads is done', 'info');
    }

    private function config_option_already_exists($option, $file)
    {
        $this->write_log($this->log_path, 'config_option_already_exists started', 'info');
        $lines = file($file, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $key => $line) {
            if (strpos($line, $option) > 0) {
                return true;
            }
        }

        $this->write_log($this->log_path, 'config_option_already_exists is done', 'info');

        return false;
    }
    public function fix_corrupted_invoice_templates()
    {
        $this->write_log($this->log_path, 'started fixing corrupted invoice template');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => $template){
            $settings = unserialize($template['settings']);
            if(isset($settings['body']['general']['line_items'])){
                $line_items = array();
                foreach($settings['body']['general']['line_items'] as $key => $line_item){
                    if($line_item == min($settings['body']['general']['line_items']) && !isset($min)){
                        $line_items[$key] = '1';
                        $min = true;
                    }elseif($line_item == max($settings['body']['general']['line_items']) && !isset($max)){
                        $line_items[$key] = '3';
                        $max = true;
                    }elseif(!isset($mid)){
                        $line_items[$key] = '2';
                        $mid = true;
                    }
                }
                $settings['body']['general']['line_items'] = $line_items;
                $template['settings'] = serialize($settings);
            }
            $template['settings'] = addslashes($template['settings']);
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
            unset($min, $max, $mid);
        }
        unset($template);
        $this->write_log($this->log_path, 'fixed corrupted invoice templates');

    }

    public function update_permissions()
    {
        $this->write_log($this->log_path, 'fix_permissions: started');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $modules = array(
            'case' => array(
                'permission_module' => 'core',
                'controller' => 'cases',
                'list_document_permission' => '/cases/show_matter_in_customer_portal/',
                'list_document_permission_update' => '/cases/save_show_hide_customer_portal/'
            )
        );
        $user_groups = $this->user_group->load_all(array('select' => array('id, name')));
        foreach ($user_groups as $group) {
            $this->write_log($this->log_path, "fix_permissions: group {$group['id']} ");
            $user_group_permissions = $this->user_group_permission->get_permissions($group['id'], false);
            if (isset($user_group_permissions['core'])) {
                $this->write_log($this->log_path, "fix_permissions: core module");
                foreach ($modules as $module_key => $module_properties) {
                    if (in_array($module_properties['list_document_permission'], $user_group_permissions['core'])) {
                        $this->write_log($this->log_path, "fix_permissions: doc permission is exists");
                        $user_group_permissions['core'][] = "/{$module_properties['controller']}/list_file_versions/";
                        if (isset($module_properties['list_document_permission_update'])) {
                            $action_key = array_search($module_properties['list_document_permission'], $user_group_permissions['core']);
                            $user_group_permissions['core'][$action_key] = $module_properties['list_document_permission_update'];
                        }
                    }
                }
                $this->user_group_permission->set_permission_data($group['id'], $user_group_permissions);
            }
        }
        $this->write_log($this->log_path, 'fix_permissions: done');
    }



    public function clear_contact_country_value(){
        $this->write_log($this->log_path, 'start removing wrong country from contacts');
        if ($this->db->query("UPDATE contacts set country_id = NULL WHERE country_id= '103'")) {
            $this->write_log($this->log_path, 'Done - removing wrong country from contacts');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to remove wrong country from contacts');
        }
    }

    public function clear_contact_email_value(){
        $this->write_log($this->log_path, 'start removing wrong emails from contacts');
        if ($this->db->query("UPDATE contacts set email = NULL WHERE email LIKE '%@email.com'")) {
            $this->write_log($this->log_path, 'Done - removing wrong emails from contacts');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to remove wrong emails from contacts');
        }
    }

    public function update_contact_custom_field(){
        $this->write_log($this->log_path, 'add custom field opt-out for contacts in A4LCRM');
        $sql = "SELECT contacts.id"
                . " FROM contacts"
                . " WHERE contacts.country_id ='103'";
        $query_execution = $this->db->query($sql);
        $contacts = $query_execution->result_array();
        $this->load->model('custom_field_value');
        foreach ($contacts as $key => $contact) {
            if(!$this->custom_field_value->fetch(['custom_field_id' => '25', 'recordId' => $contact['id']])){
                $this->write_log($this->log_path, 'contact #' . $contact['id']);
                $this->custom_field_value->set_field('custom_field_id', '25');
                $this->custom_field_value->set_field('recordId', $contact['id']);
                $this->custom_field_value->set_field('text_value', 'Yes');
                $this->custom_field_value->insert();
                $this->custom_field_value->reset_fields();
            }
        }
    }

    private function update_license_files()
    {
        $this->write_log($this->log_path, 'get core license content');
        $fibonacciStr = file_get_contents(COREPATH. 'config/license.php');
        $this->write_log($this->log_path, 'decode license string');
        $b64_license = $this->license_decode($fibonacciStr);
        if ($b64_license) {
            $this->write_log($this->log_path, 'unserialize decoded string');
            @eval(@unserialize($b64_license));
            if (isset($config)) {
                $this->write_log($this->log_path, 'load installation type');
                $this->load->model('instance_data');
                $installation_type = $this->instance_data->get_value_by_key('installationType');
                if ($installation_type['keyValue'] === 'on-cloud') {
                    $this->write_log($this->log_path, 'update cloud plan');
                    $config['App4Legal']['plan'] = 'cloud-business';
                    $config['App4Legal']['plan_excluded_features'] = 'In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement';
                    $this->write_log($this->log_path, 'disable ldap and azure features for cloud business plan');
                    $this->load->model('system_preference');
                    $this->system_preference->set_value_by_key('AzureDirectory', 'AllowFeatureAzureAd', 0);
                    $this->system_preference->set_value_by_key('AzureDirectory', 'AllowFeatureAzureAdLogoutEnable', 0);
                    $this->system_preference->set_value_by_key('ActiveDirectory', 'adEnabled', 0);
                }else{
                    $this->write_log($this->log_path, 'update self-hosted plan');
                    $config['App4Legal']['plan'] = 'self-enterprise';
                }
                $this->write_log($this->log_path, 'encode license');
                $encoded = $this->license_encode($config['App4Legal'], 'App4Legal');
                $this->write_log($this->log_path, 'write license file');
                @file_put_contents(COREPATH . 'config/license.php', $encoded);
                $this->write_log($this->log_path, 'update license is done');
            }
        }
    }

    private function license_encode($licenseVars, $product)
    {
        $license = "";
        if (!empty($licenseVars)) {
            foreach ($licenseVars as $licenseVar => $licenseVal) {
                $license .= " \$config['{$product}']['{$licenseVar}'] = '{$licenseVal}';";
            }
            $slicense = serialize($license);
            $str = base64_encode($slicense);
            if (empty($str)) {
                return '';
            }
            $lastPos = strlen($str) - 1;
            $newStr = '';
            if ($lastPos == 0) {
                $newStr = $str . $str;
            } elseif ($lastPos == 1) {
                $newStr = $str[1] . $str[0] . $str[0] . $str[1] . $str[1];
            } elseif ($lastPos == 2) {
                $newStr = $str[1] . $str[0] . $str[2] . $str[0] . $str[1] . $str[0] . $str[2];
            } elseif ($lastPos == 3) {
                $newStr = $str[1] . $str[0] . $str[2] . $str[0] . $str[1] . $str[0] . $str[2] . $str[2] . $str[3];
            } elseif ($lastPos == 4) {
                $newStr = $str[2] . $str[0] . $str[1] . $str[3] . $str[1] . $str[0] . $str[2] . $str[2] . $str[3] . $str[4];
            }
            if ($lastPos < 5) {
                die($newStr);
            }
            $newStr = $str[2] . $str[0] . $str[1] . $str[3] . $str[1] . $str[0] . $str[2] . $str[2] . $str[3] . $str[4];
            $lastStopPos = 5;
            $n = 5;
            $Un2 = 2;
            $Un1 = 3;
            $Un = $Un1 + $Un2;
            do {
                $Un2 = $Un1;
                $Un1 = $Un;
                while ($Un > $lastStopPos) {
                    $newStr .= $str[$lastStopPos];
                    $lastStopPos++;
                }
                $newStr .= $str[rand(1, $lastPos)];
            } while (($Un = $Un + $Un2) <= $lastPos);
            while ($lastStopPos <= $lastPos) {
                $newStr .= $str[$lastStopPos];
                $lastStopPos++;
            }
            $a4l_encoded = base64_encode('INFOSYSTA-LICENSE-KEYWORD');
            return $newStr.$a4l_encoded;
        }
    }

    private function license_decode($str)
    {
        $a4l_encoded = base64_encode('INFOSYSTA-LICENSE-KEYWORD');
        $key_length = strlen($a4l_encoded);
        if (empty($str) || (!empty($str) && substr($str, -$key_length) !== $a4l_encoded)) {
            return false;
        } else {
            $str = substr($str, 0, -$key_length);
        }
        $length = strlen($str);
        $newStr = '';
        $fibonacciPositions = array();
        $i = 0;
        $Un2 = -1;
        $Un1 = 1;
        while ($i < $length) {
            $Un = $Un1 + $Un2;
            $Un2 = $Un1;
            $Un1 = $Un;
            $fibonacciPositions[] = $Un + $i;
            if (!in_array($i, $fibonacciPositions)) {
                $newStr .= $str[$i];
            }
            $i++;
        }
        return base64_decode($newStr);
    }


    /**
     * add contract module allowed file types to confing/allowed_file_uploads.php
     */
    public function add_contract_to_allowed_file_uploads()
    {
        $this->write_log($this->log_path, 'update_allowed_file_uploads for contract started', 'info');
        $file = getcwd() . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "allowed_file_uploads.php";
        $str = "\$config['contract'] = 'doc|docx|xls|xlsx|pps|ppt|pptx|pdf|tif|tiff|jpg|png|gif|jpeg|bmp|html|htm|txt|msg|eml|vcf|zip|rar|mpg|mp3|mp4|flv|mov|wav|3gp|avi|pages|dwg|dwf|rtf|ogg|wmv|aiff|aif|m4a|m4v|au|xlt|xltx';";
        $option = 'contract';

        if (file_exists($file)) {
            if (!$this->config_option_already_exists($option, $file)) {
                $data = PHP_EOL . $str . PHP_EOL;

                if (!file_put_contents($file, $data, FILE_APPEND | LOCK_EX)) {
                    $this->write_log($this->log_path, '    couldn\'t modify ' . $file);
                }
            }
        } else {
            $this->write_log($this->log_path, $file . ' doesn\'t exists.', 'info');
        }

        $this->write_log($this->log_path, 'update_allowed_file_uploads for contract is done', 'info');
    }

    private function add_contract_permissions_to_all_user_groups(){
        $this->write_log($this->log_path, "START adding contract module to all user groups");
        $this->load->model('user_group_permission');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $user_groups = $this->user_group->load_all();
        foreach($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            $group_permissions['contract'][0] = '/';
            $this->user_group_permission->set_permission_data($user_group['id'], $group_permissions);
        }

        $this->write_log($this->log_path, "done adding contract module to all user groups");
    }

    public function fix_custom_fields_data()
    {
        $this->write_log($this->log_path, 'Custom fields fix data migration started');
        $query = 'DELETE
                    FROM custom_field_values
                    WHERE custom_field_values.id IN 
                    (select sub_cfv.id FROM ( select cfv_1.id
                                              FROM custom_field_values AS cfv_1
                                              WHERE cfv_1.id NOT IN 
                                              (SELECT MAX(cfv.id) as id
                                                 FROM custom_field_values cfv
                                                          INNER JOIN custom_fields cf on cf.id = cfv.custom_field_id
                                                 GROUP BY cfv.custom_field_id, cfv.recordId, cfv.date_value, cfv.text_value, cfv.time_value, cf.type
                                              )
                                            ) sub_cfv
                    )';
        $this->db->query($query);
        $this->write_log($this->log_path, 'Custom fields fix data migration ended');
    }
    
    public function fix_expenses_case_ids()
    {
        $this->write_log($this->log_path, 'fixing expenses case ids data migration started');
        if($this->db->dbdriver == 'mysqli'){
            $query = 'SELECT id, case_id FROM voucher_headers WHERE voucherType = "EXP" AND case_id != ""';
            $expenses = $this->db->query($query)->result_array();
            foreach($expenses as $expense){
                $this->db->query('UPDATE voucher_headers SET case_id = "' . str_pad($expense['case_id'], 8, "0", STR_PAD_LEFT) . '" WHERE id = "' . $expense['id'] . '"');
            }
        }
        $this->write_log($this->log_path, 'fixing expenses case ids data ended');

    }
}
